<?php
// Controller de movimentacoes
class MovimentacoesController extends ControllerBase
{
    public function index()
    {
        $inicio = $_GET['inicio'] ?? date('Y-m-01');
        $fim = $_GET['fim'] ?? date('Y-m-t');
        $model = new Movimentacao();
        $movimentacoes = $model->listarPorPeriodo($inicio, $fim);
        $this->render('movimentacoes/index', [
            'movimentacoes' => $movimentacoes,
            'inicio' => $inicio,
            'fim' => $fim
        ]);
    }
}
