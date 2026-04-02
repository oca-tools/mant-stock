<?php
// Controller de movimentacoes
class MovimentacoesController extends ControllerBase
{
    public function index()
    {
        $inicio = $_GET['inicio'] ?? date('Y-m-01');
        $fim = $_GET['fim'] ?? date('Y-m-t');
        $limite = 20;
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $offset = ($pagina - 1) * $limite;

        $model = new Movimentacao();
        $totalRegistros = $model->contarPorPeriodo($inicio, $fim);
        $totalPaginas = max(1, (int)ceil($totalRegistros / $limite));
        if ($pagina > $totalPaginas) {
            $pagina = $totalPaginas;
            $offset = ($pagina - 1) * $limite;
        }

        $movimentacoes = $model->listarPorPeriodo($inicio, $fim, $limite, $offset);
        $this->render('movimentacoes/index', [
            'movimentacoes' => $movimentacoes,
            'inicio' => $inicio,
            'fim' => $fim,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalRegistros' => $totalRegistros
        ]);
    }

    public function exportarExcel()
    {
        $inicio = $_GET['inicio'] ?? date('Y-m-01');
        $fim = $_GET['fim'] ?? date('Y-m-t');

        $model = new Movimentacao();
        $movimentacoes = $model->listarPorPeriodo($inicio, $fim, null);

        $linhas = array_map(function ($m) {
            return [
                $m['data_movimentacao'] ?? '',
                $m['produto_nome'] ?? '',
                $m['tipo_movimentacao'] ?? '',
                $m['quantidade'] ?? 0,
                $m['origem'] ?? '',
                $m['destino'] ?? '',
                $m['usuario_nome'] ?? '',
                $m['observacoes'] ?? ''
            ];
        }, $movimentacoes);

        $this->exportarCsv(
            'movimentacoes.csv',
            ['Data', 'Produto', 'Tipo', 'Quantidade', 'Origem', 'Destino', 'Usuario', 'Observacoes'],
            $linhas
        );
    }

    public function exportarPdf()
    {
        $inicio = $_GET['inicio'] ?? date('Y-m-01');
        $fim = $_GET['fim'] ?? date('Y-m-t');

        $model = new Movimentacao();
        $movimentacoes = $model->listarPorPeriodo($inicio, $fim, null);

        $linhas = array_map(function ($m) {
            return [
                $m['data_movimentacao'] ?? '',
                $m['produto_nome'] ?? '',
                $m['tipo_movimentacao'] ?? '',
                $m['quantidade'] ?? 0,
                $m['usuario_nome'] ?? ''
            ];
        }, $movimentacoes);

        $this->exportarPdfHtmlTabela(
            'Exportacao de Movimentacoes',
            'Periodo: ' . $inicio . ' ate ' . $fim,
            ['Data', 'Produto', 'Tipo', 'Quantidade', 'Usuario'],
            $linhas
        );
    }
}
