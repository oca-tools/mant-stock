<?php
// Controller de inventarios
class InventariosController extends ControllerBase
{
    public function index()
    {
        $model = new Inventario();
        $inventarios = $model->listar();
        $this->render('inventarios/index', ['inventarios' => $inventarios]);
    }

    public function criar()
    {
        $produtoModel = new Produto();
        $produtos = $produtoModel->listar(200, 0, '');
        $this->render('inventarios/criar', ['produtos' => $produtos, 'erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $produtoId = (int)($_POST['produto_id'] ?? 0);
        $quantidadeReal = (float)($_POST['quantidade_real'] ?? 0);
        if ($produtoId <= 0) {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(200, 0, '');
            $this->render('inventarios/criar', ['produtos' => $produtos, 'erro' => 'Informe produto valido.']);
            return;
        }
        if ($quantidadeReal < 0) {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(200, 0, '');
            $this->render('inventarios/criar', ['produtos' => $produtos, 'erro' => 'Quantidade real invalida.']);
            return;
        }

        $produtoModel = new Produto();
        $produto = $produtoModel->buscarPorId($produtoId);
        if (!$produto) {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(200, 0, '');
            $this->render('inventarios/criar', ['produtos' => $produtos, 'erro' => 'Produto nao encontrado.']);
            return;
        }

        $quantidadeSistema = (float)$produto['estoque_atual'];
        $diferenca = $quantidadeReal - $quantidadeSistema;
        $motivoAjuste = trim($_POST['motivo_ajuste'] ?? '');
        if ($diferenca != 0 && $motivoAjuste === '') {
            $produtos = $produtoModel->listar(200, 0, '');
            $this->render('inventarios/criar', ['produtos' => $produtos, 'erro' => 'Informe o motivo do ajuste.']);
            return;
        }

        $model = new Inventario();
        $dadosInventario = [
            'produto_id' => $produtoId,
            'quantidade_sistema' => $quantidadeSistema,
            'quantidade_real' => $quantidadeReal,
            'diferenca' => $diferenca,
            'usuario_id' => $_SESSION['usuario']['id'],
            'motivo_ajuste' => $motivoAjuste
        ];
        $id = $model->criar($dadosInventario);

        if ($diferenca != 0) {
            $produtoModel->atualizarEstoque($produtoId, $diferenca);
            $movModel = new Movimentacao();
            $movModel->criar([
                'produto_id' => $produtoId,
                'tipo_movimentacao' => 'ajuste',
                'quantidade' => abs($diferenca),
                'usuario_id' => $_SESSION['usuario']['id'],
                'origem' => 'Inventario',
                'destino' => 'Ajuste',
                'observacoes' => $motivoAjuste
            ]);
        }

        LogService::registrar($_SESSION['usuario']['id'], 'movimentacao', 'Inventario registrado', 'inventarios', $id, null, $dadosInventario);
        redirect(url('inventarios'));
    }
}
