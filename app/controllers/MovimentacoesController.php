<?php
// Controller de movimentacoes
class MovimentacoesController extends ControllerBase
{
    public function index()
    {
        $periodo = PeriodoService::periodoIndexado(
            $_GET['inicio'] ?? date('Y-m-01'),
            $_GET['fim'] ?? date('Y-m-t')
        );
        $limite = 20;
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $offset = ($pagina - 1) * $limite;

        $model = new Movimentacao();
        $totalRegistros = $model->contarPorPeriodo($periodo['inicio_data'], $periodo['fim_data']);
        $totalPaginas = max(1, (int)ceil($totalRegistros / $limite));
        if ($pagina > $totalPaginas) {
            $pagina = $totalPaginas;
            $offset = ($pagina - 1) * $limite;
        }

        $movimentacoes = $model->listarPorPeriodo($periodo['inicio_data'], $periodo['fim_data'], $limite, $offset);
        $this->render('movimentacoes/index', [
            'movimentacoes' => $movimentacoes,
            'inicio' => $periodo['inicio_data'],
            'fim' => $periodo['fim_data'],
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalRegistros' => $totalRegistros
        ]);
    }

    public function exportarExcel()
    {
        $periodo = PeriodoService::periodoIndexado(
            $_GET['inicio'] ?? date('Y-m-01'),
            $_GET['fim'] ?? date('Y-m-t')
        );

        $model = new Movimentacao();
        $movimentacoes = $model->listarPorPeriodo($periodo['inicio_data'], $periodo['fim_data'], null);

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
        $periodo = PeriodoService::periodoIndexado(
            $_GET['inicio'] ?? date('Y-m-01'),
            $_GET['fim'] ?? date('Y-m-t')
        );

        $model = new Movimentacao();
        $movimentacoes = $model->listarPorPeriodo($periodo['inicio_data'], $periodo['fim_data'], null);

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
            'Periodo: ' . $periodo['inicio_data'] . ' ate ' . $periodo['fim_data'],
            ['Data', 'Produto', 'Tipo', 'Quantidade', 'Usuario'],
            $linhas
        );
    }
}
