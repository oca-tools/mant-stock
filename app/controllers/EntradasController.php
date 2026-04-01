<?php
// Controller de entradas de estoque
class EntradasController extends ControllerBase
{
    public function index()
    {
        $model = new Entrada();
        $entradas = $model->listar();
        $this->render('entradas/index', ['entradas' => $entradas]);
    }

    public function criar()
    {
        $produtoModel = new Produto();
        $produtos = $produtoModel->listar(200, 0, '');
        $this->render('entradas/criar', ['produtos' => $produtos, 'erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $produtoId = (int)($_POST['produto_id'] ?? 0);
        $quantidade = (float)($_POST['quantidade'] ?? 0);
        if ($produtoId <= 0 || $quantidade <= 0) {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(200, 0, '');
            $this->render('entradas/criar', ['produtos' => $produtos, 'erro' => 'Informe produto e quantidade validos.']);
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

        LogService::registrar($_SESSION['usuario']['id'], 'movimentacao', 'Entrada registrada', 'entradas', $entradaId, null, $dadosEntrada);

        redirect(url('entradas'));
    }
}
