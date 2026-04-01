<?php
// Controller de saidas de estoque
class SaidasController extends ControllerBase
{
    public function index()
    {
        $model = new Saida();
        $saidas = $model->listar();
        $this->render('saidas/index', ['saidas' => $saidas]);
    }

    public function criar()
    {
        $produtoModel = new Produto();
        $produtos = $produtoModel->listar(200, 0, '');
        $this->render('saidas/criar', ['produtos' => $produtos, 'erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $produtoId = (int)($_POST['produto_id'] ?? 0);
        $quantidade = (float)($_POST['quantidade'] ?? 0);
        if ($produtoId <= 0 || $quantidade <= 0) {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(200, 0, '');
            $this->render('saidas/criar', ['produtos' => $produtos, 'erro' => 'Informe produto e quantidade validos.']);
            return;
        }

        $produtoModel = new Produto();
        $produto = $produtoModel->buscarPorId($produtoId);
        if (!$produto || $produto['estoque_atual'] < $quantidade) {
            $produtos = $produtoModel->listar(200, 0, '');
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

        LogService::registrar($_SESSION['usuario']['id'], 'movimentacao', 'Saída registrada pelo usuário ' . $_SESSION['usuario']['nome'], 'saidas', $saidaId, null, $dadosSaida);

        redirect(url('saidas'));
    }
}
