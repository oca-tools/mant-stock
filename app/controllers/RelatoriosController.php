<?php
// Controller de relatorios
class RelatoriosController extends ControllerBase
{
    public function index()
    {
        $periodo = PeriodoService::periodoIndexado(
            $_GET['inicio'] ?? date('Y-m-01'),
            $_GET['fim'] ?? date('Y-m-t')
        );
        $tipo = $_GET['tipo'] ?? 'movimentacoes';

        $dados = $this->gerarRelatorio($tipo, $periodo);

        $this->render('relatorios/index', [
            'inicio' => $periodo['inicio_data'],
            'fim' => $periodo['fim_data'],
            'tipo' => $tipo,
            'dados' => $dados
        ]);
    }

    public function exportarExcel()
    {
        $periodo = PeriodoService::periodoIndexado(
            $_GET['inicio'] ?? date('Y-m-01'),
            $_GET['fim'] ?? date('Y-m-t')
        );
        $tipo = $_GET['tipo'] ?? 'movimentacoes';
        $dados = $this->gerarRelatorio($tipo, $periodo);

        $linhas = $dados['linhas'];
        if (!empty($dados['totais'])) {
            $linhas[] = $dados['totais'];
        }

        $this->exportarCsv(
            'relatorio_' . preg_replace('/[^a-z0-9_]/i', '_', $tipo) . '.csv',
            $dados['cabecalho'],
            $linhas
        );
    }

    public function exportarPdf()
    {
        $periodo = PeriodoService::periodoIndexado(
            $_GET['inicio'] ?? date('Y-m-01'),
            $_GET['fim'] ?? date('Y-m-t')
        );
        $tipo = $_GET['tipo'] ?? 'movimentacoes';
        $dados = $this->gerarRelatorio($tipo, $periodo);

        header('Content-Type: text/html; charset=utf-8');
        $titulo = 'Relatorio: ' . $tipo;
        $periodoTexto = 'Periodo: ' . $periodo['inicio_data'] . ' a ' . $periodo['fim_data'];
        echo '<html><head><meta charset="utf-8"><title>Relatorio</title>';
        echo '<style>body{font-family:Arial,sans-serif;margin:24px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;font-size:12px}th{background:#f2f2f2}</style>';
        echo '</head><body>';
        echo '<h2>' . e($titulo) . '</h2>';
        echo '<p>' . e($periodoTexto) . '</p>';
        echo '<table><thead><tr>';
        foreach ($dados['cabecalho'] as $coluna) {
            echo '<th>' . e($coluna) . '</th>';
        }
        echo '</tr></thead><tbody>';
        foreach ($dados['linhas'] as $linha) {
            echo '<tr>';
            foreach ($linha as $valor) {
                echo '<td>' . e($valor) . '</td>';
            }
            echo '</tr>';
        }
        if (!empty($dados['totais'])) {
            echo '<tr style="font-weight:bold;background:#fafafa;">';
            foreach ($dados['totais'] as $valor) {
                echo '<td>' . e($valor) . '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '<script>window.print();</script>';
        echo '</body></html>';
        exit;
    }

    private function gerarRelatorio($tipo, array $periodo)
    {
        $db = Conexao::obter();
        $inicioDt = $periodo['inicio_dt'];
        $fimDt = $periodo['fim_dt'];

        switch ($tipo) {
            case 'consumo_produto':
                $stmt = $db->prepare(
                    'SELECT p.nome, SUM(m.quantidade) AS total_saida
                     FROM movimentacoes m
                     LEFT JOIN produtos p ON p.id = m.produto_id
                     WHERE m.tipo_movimentacao = :tipo
                       AND m.data_movimentacao >= :inicio_dt
                       AND m.data_movimentacao < :fim_dt
                     GROUP BY p.nome
                     ORDER BY total_saida DESC'
                );
                $stmt->execute([':tipo' => 'saida', ':inicio_dt' => $inicioDt, ':fim_dt' => $fimDt]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Produto', 'Total de Saidas'],
                    'linhas' => array_map(static function ($linha) {
                        return [$linha['nome'], $linha['total_saida']];
                    }, $linhas),
                    'totais' => ['Total', array_sum(array_column($linhas, 'total_saida'))]
                ];

            case 'consumo_categoria':
                $stmt = $db->prepare(
                    'SELECT c.nome AS categoria, SUM(m.quantidade) AS total_saida
                     FROM movimentacoes m
                     LEFT JOIN produtos p ON p.id = m.produto_id
                     LEFT JOIN categorias c ON c.id = p.categoria_id
                     WHERE m.tipo_movimentacao = :tipo
                       AND m.data_movimentacao >= :inicio_dt
                       AND m.data_movimentacao < :fim_dt
                     GROUP BY c.nome
                     ORDER BY total_saida DESC'
                );
                $stmt->execute([':tipo' => 'saida', ':inicio_dt' => $inicioDt, ':fim_dt' => $fimDt]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Categoria', 'Total de Saidas'],
                    'linhas' => array_map(static function ($linha) {
                        return [$linha['categoria'], $linha['total_saida']];
                    }, $linhas),
                    'totais' => ['Total', array_sum(array_column($linhas, 'total_saida'))]
                ];

            case 'consumo_setor_mensal':
                $stmt = $db->prepare(
                    'SELECT DATE_FORMAT(s.data_saida, \'%Y-%m\') AS mes, s.setor, SUM(s.quantidade) AS total_saida
                     FROM saidas s
                     WHERE s.data_saida >= :inicio_dt
                       AND s.data_saida < :fim_dt
                     GROUP BY mes, s.setor
                     ORDER BY mes DESC, total_saida DESC'
                );
                $stmt->execute([':inicio_dt' => $inicioDt, ':fim_dt' => $fimDt]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Mes', 'Setor', 'Total de Saidas'],
                    'linhas' => array_map(static function ($linha) {
                        return [$linha['mes'], $linha['setor'], $linha['total_saida']];
                    }, $linhas),
                    'totais' => ['Total', '', array_sum(array_column($linhas, 'total_saida'))]
                ];

            case 'descartes':
                $stmt = $db->prepare(
                    'SELECT p.nome, SUM(m.quantidade) AS total_descarte
                     FROM movimentacoes m
                     LEFT JOIN produtos p ON p.id = m.produto_id
                     WHERE m.tipo_movimentacao = :tipo
                       AND m.data_movimentacao >= :inicio_dt
                       AND m.data_movimentacao < :fim_dt
                     GROUP BY p.nome
                     ORDER BY total_descarte DESC'
                );
                $stmt->execute([':tipo' => 'descarte', ':inicio_dt' => $inicioDt, ':fim_dt' => $fimDt]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Produto', 'Total de Descartes'],
                    'linhas' => array_map(static function ($linha) {
                        return [$linha['nome'], $linha['total_descarte']];
                    }, $linhas),
                    'totais' => ['Total', array_sum(array_column($linhas, 'total_descarte'))]
                ];

            case 'descartes_vs_saidas':
                $stmt = $db->prepare(
                    'SELECT p.nome,
                            SUM(CASE WHEN m.tipo_movimentacao = \'saida\' THEN m.quantidade ELSE 0 END) AS total_saidas,
                            SUM(CASE WHEN m.tipo_movimentacao = \'descarte\' THEN m.quantidade ELSE 0 END) AS total_descartes
                     FROM movimentacoes m
                     LEFT JOIN produtos p ON p.id = m.produto_id
                     WHERE m.tipo_movimentacao IN (\'saida\', \'descarte\')
                       AND m.data_movimentacao >= :inicio_dt
                       AND m.data_movimentacao < :fim_dt
                     GROUP BY p.nome
                     ORDER BY p.nome'
                );
                $stmt->execute([':inicio_dt' => $inicioDt, ':fim_dt' => $fimDt]);
                $linhas = $stmt->fetchAll();
                $linhasFormatadas = array_map(static function ($linha) {
                    $diferenca = (float)$linha['total_saidas'] - (float)$linha['total_descartes'];
                    return [$linha['nome'], $linha['total_saidas'], $linha['total_descartes'], $diferenca];
                }, $linhas);
                return [
                    'cabecalho' => ['Produto', 'Total de Saidas', 'Total de Descartes', 'Diferenca'],
                    'linhas' => $linhasFormatadas,
                    'totais' => [
                        'Total',
                        array_sum(array_column($linhas, 'total_saidas')),
                        array_sum(array_column($linhas, 'total_descartes')),
                        array_sum(array_map(static function ($linha) {
                            return (float)$linha['total_saidas'] - (float)$linha['total_descartes'];
                        }, $linhas))
                    ]
                ];

            case 'ajustes':
                $stmt = $db->prepare(
                    'SELECT m.data_movimentacao, p.nome, m.quantidade, u.nome AS usuario_nome, m.observacoes
                     FROM movimentacoes m
                     LEFT JOIN produtos p ON p.id = m.produto_id
                     LEFT JOIN usuarios u ON u.id = m.usuario_id
                     WHERE m.tipo_movimentacao = :tipo
                       AND m.data_movimentacao >= :inicio_dt
                       AND m.data_movimentacao < :fim_dt
                     ORDER BY m.data_movimentacao DESC'
                );
                $stmt->execute([':tipo' => 'ajuste', ':inicio_dt' => $inicioDt, ':fim_dt' => $fimDt]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Data', 'Produto', 'Quantidade', 'Usuario', 'Motivo'],
                    'linhas' => array_map(static function ($linha) {
                        return [$linha['data_movimentacao'], $linha['nome'], $linha['quantidade'], $linha['usuario_nome'], $linha['observacoes']];
                    }, $linhas),
                    'totais' => ['Total', '', array_sum(array_column($linhas, 'quantidade')), '', '']
                ];

            case 'estoque_minimo':
                $linhas = $db->query(
                    'SELECT nome, estoque_atual, estoque_minimo
                     FROM produtos
                     WHERE estoque_atual <= estoque_minimo
                     ORDER BY nome'
                )->fetchAll();
                return [
                    'cabecalho' => ['Produto', 'Estoque Atual', 'Estoque Minimo'],
                    'linhas' => array_map(static function ($linha) {
                        return [$linha['nome'], $linha['estoque_atual'], $linha['estoque_minimo']];
                    }, $linhas),
                    'totais' => ['Total', count($linhas), '']
                ];

            case 'estoque_atual':
                $linhas = $db->query(
                    'SELECT nome, estoque_atual, unidade_medida
                     FROM produtos
                     ORDER BY nome'
                )->fetchAll();
                return [
                    'cabecalho' => ['Produto', 'Estoque Atual', 'Unidade'],
                    'linhas' => array_map(static function ($linha) {
                        return [$linha['nome'], $linha['estoque_atual'], $linha['unidade_medida']];
                    }, $linhas),
                    'totais' => ['Total', count($linhas), '']
                ];

            case 'movimentacoes':
            default:
                $stmt = $db->prepare(
                    'SELECT m.data_movimentacao, p.nome, m.tipo_movimentacao, m.quantidade, u.nome AS usuario_nome
                     FROM movimentacoes m
                     LEFT JOIN produtos p ON p.id = m.produto_id
                     LEFT JOIN usuarios u ON u.id = m.usuario_id
                     WHERE m.data_movimentacao >= :inicio_dt
                       AND m.data_movimentacao < :fim_dt
                     ORDER BY m.data_movimentacao DESC'
                );
                $stmt->execute([':inicio_dt' => $inicioDt, ':fim_dt' => $fimDt]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Data', 'Produto', 'Tipo', 'Quantidade', 'Usuario'],
                    'linhas' => array_map(static function ($linha) {
                        return [$linha['data_movimentacao'], $linha['nome'], $linha['tipo_movimentacao'], $linha['quantidade'], $linha['usuario_nome']];
                    }, $linhas),
                    'totais' => ['Total', '', '', array_sum(array_column($linhas, 'quantidade')), '']
                ];
        }
    }
}
