<?php
// Controller de entradas de estoque
class EntradasController extends ControllerBase
{
    public function index()
    {
        $limite = 20;
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $offset = ($pagina - 1) * $limite;

        $model = new Entrada();
        $totalRegistros = $model->contar();
        $totalPaginas = max(1, (int)ceil($totalRegistros / $limite));
        if ($pagina > $totalPaginas) {
            $pagina = $totalPaginas;
            $offset = ($pagina - 1) * $limite;
        }

        $entradas = $model->listar($limite, $offset);
        $this->render('entradas/index', [
            'entradas' => $entradas,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalRegistros' => $totalRegistros
        ]);
    }

    public function criar()
    {
        $produtoModel = new Produto();
        $produtos = $produtoModel->listar(500, 0, '');
        $this->render('entradas/criar', ['produtos' => $produtos, 'erro' => null]);
    }

    public function exportarExcel()
    {
        $model = new Entrada();
        $entradas = $model->listar(null);

        $linhas = array_map(function ($e) {
            return [
                $e['data_entrada'] ?? '',
                $e['produto_nome'] ?? '',
                $e['quantidade'] ?? 0,
                $e['fornecedor'] ?? '',
                $e['nota_fiscal'] ?? '',
                $e['usuario_nome'] ?? '',
                $e['observacoes'] ?? ''
            ];
        }, $entradas);

        $this->exportarCsv(
            'entradas.csv',
            ['Data', 'Produto', 'Quantidade', 'Fornecedor', 'Nota Fiscal', 'Usuario Emissor', 'Observacoes'],
            $linhas
        );
    }

    public function exportarPdf()
    {
        $model = new Entrada();
        $entradas = $model->listar(null);

        $linhas = array_map(function ($e) {
            return [
                $e['data_entrada'] ?? '',
                $e['produto_nome'] ?? '',
                $e['quantidade'] ?? 0,
                $e['fornecedor'] ?? '',
                $e['usuario_nome'] ?? ''
            ];
        }, $entradas);

        $this->exportarPdfHtmlTabela(
            'Exportacao de Entradas',
            'Historico de entradas de estoque',
            ['Data', 'Produto', 'Quantidade', 'Fornecedor', 'Usuario Emissor'],
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
            $this->render('entradas/criar', ['produtos' => $produtos, 'erro' => 'Informe produto e quantidade validos.']);
            return;
        }

        $erroSenha = null;
        if (!$this->validarSenhaOperacional($senhaConfirmacao, $erroSenha)) {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(500, 0, '');
            $this->render('entradas/criar', ['produtos' => $produtos, 'erro' => $erroSenha]);
            return;
        }

        $entradaModel = new Entrada();
        $dadosEntrada = [
            'produto_id' => $produtoId,
            'quantidade' => $quantidade,
            'fornecedor' => trim($_POST['fornecedor'] ?? ''),
            'nota_fiscal' => trim($_POST['nota_fiscal'] ?? ''),
            'usuario_id' => $_SESSION['usuario']['id'],
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ];
        $entradaId = $entradaModel->criar($dadosEntrada);

        $produtoModel = new Produto();
        $produtoModel->atualizarEstoque($produtoId, $quantidade);

        $movModel = new Movimentacao();
        $movModel->criar([
            'produto_id' => $produtoId,
            'tipo_movimentacao' => 'entrada',
            'quantidade' => $quantidade,
            'usuario_id' => $_SESSION['usuario']['id'],
            'origem' => trim($_POST['fornecedor'] ?? ''),
            'destino' => 'Almoxarifado',
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ]);

        LogService::registrar(
            $_SESSION['usuario']['id'],
            'movimentacao',
            'Entrada registrada com validacao de senha',
            'entradas',
            $entradaId,
            null,
            array_merge($dadosEntrada, ['validacao_senha' => true])
        );

        redirect(url('entradas'));
    }
}
