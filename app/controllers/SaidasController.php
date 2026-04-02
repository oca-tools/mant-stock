<?php
// Controller de saidas de estoque
class SaidasController extends ControllerBase
{
    public function index()
    {
        $limite = 20;
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $offset = ($pagina - 1) * $limite;

        $model = new Saida();
        $totalRegistros = $model->contar();
        $totalPaginas = max(1, (int)ceil($totalRegistros / $limite));
        if ($pagina > $totalPaginas) {
            $pagina = $totalPaginas;
            $offset = ($pagina - 1) * $limite;
        }

        $saidas = $model->listar($limite, $offset);
        $this->render('saidas/index', [
            'saidas' => $saidas,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalRegistros' => $totalRegistros
        ]);
    }

    public function criar()
    {
        $produtoModel = new Produto();
        $produtos = $produtoModel->listar(500, 0, '');
        $this->render('saidas/criar', ['produtos' => $produtos, 'erro' => null]);
    }

    public function comprovante($id)
    {
        $model = new Saida();
        $saida = $model->buscarDetalhadaPorId((int)$id);
        if (!$saida) {
            http_response_code(404);
            echo 'Saida nao encontrada.';
            return;
        }

        $auto = (int)($_GET['auto'] ?? 0) === 1;
        $this->render('saidas/comprovante', [
            'saida' => $saida,
            'auto' => $auto
        ]);
    }

    public function exportarExcel()
    {
        $model = new Saida();
        $saidas = $model->listar(null);

        $linhas = array_map(function ($s) {
            return [
                $s['data_saida'] ?? '',
                $s['produto_nome'] ?? '',
                $s['quantidade'] ?? 0,
                $s['setor'] ?? '',
                $s['local_utilizacao'] ?? '',
                $s['tecnico_responsavel'] ?? '',
                $s['usuario_nome'] ?? '',
                $s['observacoes'] ?? ''
            ];
        }, $saidas);

        $this->exportarCsv(
            'saidas.csv',
            ['Data', 'Produto', 'Quantidade', 'Setor', 'Local de Utilizacao', 'Solicitante', 'Usuario Emissor', 'Observacoes'],
            $linhas
        );
    }

    public function exportarPdf()
    {
        $model = new Saida();
        $saidas = $model->listar(null);

        $linhas = array_map(function ($s) {
            return [
                $s['data_saida'] ?? '',
                $s['produto_nome'] ?? '',
                $s['quantidade'] ?? 0,
                $s['local_utilizacao'] ?? '',
                $s['tecnico_responsavel'] ?? '',
                $s['usuario_nome'] ?? ''
            ];
        }, $saidas);

        $this->exportarPdfHtmlTabela(
            'Exportacao de Saidas',
            'Historico de saidas de estoque',
            ['Data', 'Produto', 'Quantidade', 'Local', 'Solicitante', 'Usuario Emissor'],
            $linhas
        );
    }

    public function armazenar()
    {
        $this->exigirCsrf();

        $produtoId = (int)($_POST['produto_id'] ?? 0);
        $quantidade = (float)($_POST['quantidade'] ?? 0);
        $senhaConfirmacao = $_POST['senha_confirmacao'] ?? '';

        if ($produtoId <= 0 || $quantidade <= 0) {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(500, 0, '');
            $this->render('saidas/criar', ['produtos' => $produtos, 'erro' => 'Informe produto e quantidade validos.']);
            return;
        }

        $erroSenha = null;
        if (!$this->validarSenhaOperacional($senhaConfirmacao, $erroSenha)) {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(500, 0, '');
            $this->render('saidas/criar', ['produtos' => $produtos, 'erro' => $erroSenha]);
            return;
        }

        $produtoModel = new Produto();
        $produto = $produtoModel->buscarPorId($produtoId);
        if (!$produto || $produto['estoque_atual'] < $quantidade) {
            $produtos = $produtoModel->listar(500, 0, '');
            $this->render('saidas/criar', ['produtos' => $produtos, 'erro' => 'Estoque insuficiente para a saida.']);
            return;
        }

        $saidaModel = new Saida();
        $dadosSaida = [
            'produto_id' => $produtoId,
            'quantidade' => $quantidade,
            'setor' => trim($_POST['setor'] ?? ''),
            'local_utilizacao' => trim($_POST['local_utilizacao'] ?? ''),
            'tecnico_responsavel' => trim($_POST['solicitante'] ?? ''),
            'usuario_id' => $_SESSION['usuario']['id'],
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ];
        $saidaId = $saidaModel->criar($dadosSaida);

        $produtoModel->atualizarEstoque($produtoId, -$quantidade);

        $movModel = new Movimentacao();
        $movModel->criar([
            'produto_id' => $produtoId,
            'tipo_movimentacao' => 'saida',
            'quantidade' => $quantidade,
            'usuario_id' => $_SESSION['usuario']['id'],
            'origem' => 'Almoxarifado',
            'destino' => trim($_POST['local_utilizacao'] ?? ''),
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ]);

        LogService::registrar(
            $_SESSION['usuario']['id'],
            'movimentacao',
            'Saida registrada com validacao de senha',
            'saidas',
            $saidaId,
            null,
            array_merge($dadosSaida, ['validacao_senha' => true])
        );

        redirect(url('saidas/comprovante/' . $saidaId . '?auto=1'));
    }
}
