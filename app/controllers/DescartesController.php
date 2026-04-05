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

        $dadosDescarte = [
            'produto_id' => $produtoId,
            'quantidade' => $quantidade,
            'motivo_descarte' => $motivo,
            'item_recebido_troca' => trim($_POST['item_recebido_troca'] ?? ''),
            'usuario_id' => $_SESSION['usuario']['id'],
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ];
        $dadosMovimentacao = [
            'produto_id' => $produtoId,
            'tipo_movimentacao' => 'descarte',
            'quantidade' => $quantidade,
            'usuario_id' => $_SESSION['usuario']['id'],
            'origem' => 'Uso',
            'destino' => 'Descarte',
            'observacoes' => trim($_POST['observacoes'] ?? '')
        ];

        try {
            $service = new OperacaoEstoqueService();
            $resultado = $service->registrarDescarte($dadosDescarte, $dadosMovimentacao);
            $descarteId = (int)$resultado['descarte_id'];
        } catch (Throwable $erro) {
            $produtoModel = new Produto();
            $produtos = $produtoModel->listar(200, 0, '');
            $this->render('descartes/criar', ['produtos' => $produtos, 'erro' => 'Nao foi possivel registrar o descarte. Tente novamente.']);
            return;
        }

        LogService::registrar($_SESSION['usuario']['id'], 'movimentacao', 'Descarte registrado', 'descartes', $descarteId, null, $dadosDescarte);

        redirect(url('descartes'));
    }
}
