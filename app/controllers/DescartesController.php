<?php
// Controller de descartes
class DescartesController extends ControllerBase
{
    public function index()
    {
        $model = new Descarte();
        $descartes = $model->listar();
        $this->render('descartes/index', ['descartes' => $descartes]);
    }

    public function criar()
    {
        $produtoModel = new Produto();
        $produtos = $produtoModel->listar(200, 0, '');
        $this->render('descartes/criar', ['produtos' => $produtos, 'erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $produtoId = (int)($_POST['produto_id'] ?? 0);
        $quantidade = (float)($_POST['quantidade'] ?? 0);
        $motivo = trim($_POST['motivo_descarte'] ?? '');
        if ($produtoId <= 0 || $quantidade <= 0) {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(200, 0, '');
            $this->render('descartes/criar', ['produtos' => $produtos, 'erro' => 'Informe produto e quantidade validos.']);
            return;
        }
        if ($motivo === '') {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(200, 0, '');
            $this->render('descartes/criar', ['produtos' => $produtos, 'erro' => 'Motivo do descarte e obrigatorio.']);
            return;
        }

        $descarteModel = new Descarte();
        $dadosDescarte = [
            'produto_id' => $produtoId,
            'quantidade' => $quantidade,
            'motivo_descarte' => $motivo,
            'item_recebido_troca' => trim($_POST['item_recebido_troca'] ?? ''),
            'usuario_id' => $_SESSION['usuario']['id'],
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ];
        $descarteId = $descarteModel->criar($dadosDescarte);

        $movModel = new Movimentacao();
        $movModel->criar([
            'produto_id' => $produtoId,
            'tipo_movimentacao' => 'descarte',
            'quantidade' => $quantidade,
            'usuario_id' => $_SESSION['usuario']['id'],
            'origem' => 'Uso',
            'destino' => 'Descarte',
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ]);

        LogService::registrar($_SESSION['usuario']['id'], 'movimentacao', 'Descarte registrado', 'descartes', $descarteId, null, $dadosDescarte);

        redirect(url('descartes'));
    }
}
