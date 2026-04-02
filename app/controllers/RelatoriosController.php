<?php
// Controller de relatorios
class RelatoriosController extends ControllerBase
{
    public function index()
    {
        $inicio = $_GET['inicio'] ?? date('Y-m-01');
        $fim = $_GET['fim'] ?? date('Y-m-t');
        $tipo = $_GET['tipo'] ?? 'movimentacoes';

        $dados = $this->gerarRelatorio($tipo, $inicio, $fim);

        $this->render('relatorios/index', [
            'inicio' => $inicio,
            'fim' => $fim,
            'tipo' => $tipo,
            'dados' => $dados
        ]);
    }

    public function exportarExcel()
    {
        $inicio = $_GET['inicio'] ?? date('Y-m-01');
        $fim = $_GET['fim'] ?? date('Y-m-t');
        $tipo = $_GET['tipo'] ?? 'movimentacoes';
        $dados = $this->gerarRelatorio($tipo, $inicio, $fim);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio_' . $tipo . '.csv"');

        $saida = fopen('php://output', 'w');
        if (!empty($dados['cabecalho'])) {
            fputcsv($saida, $dados['cabecalho'], ';');
        }
        foreach ($dados['linhas'] as $linha) {
            fputcsv($saida, $linha, ';');
        }
        if (!empty($dados['totais'])) {
            fputcsv($saida, $dados['totais'], ';');
        }
        fclose($saida);
        exit;
    }

    public function exportarPdf()
    {
        $inicio = $_GET['inicio'] ?? date('Y-m-01');
        $fim = $_GET['fim'] ?? date('Y-m-t');
        $tipo = $_GET['tipo'] ?? 'movimentacoes';
        $dados = $this->gerarRelatorio($tipo, $inicio, $fim);

        header('Content-Type: text/html; charset=utf-8');
        $titulo = 'Relatório: ' . $tipo;
        $periodo = 'Período: ' . $inicio . ' a ' . $fim;
        echo '<html><head><meta charset="utf-8"><title>Relatório</title>';
        echo '<style>body{font-family:Arial,sans-serif;margin:24px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;font-size:12px}th{background:#f2f2f2}</style>';
        echo '</head><body>';
        echo '<h2>' . e($titulo) . '</h2>';
        echo '<p>' . e($periodo) . '</p>';
        echo '<table><thead><tr>';
        foreach ($dados['cabecalho'] as $col) { echo '<th>' . e($col) . '</th>'; }
        echo '</tr></thead><tbody>';
        foreach ($dados['linhas'] as $linha) {
            echo '<tr>';
            foreach ($linha as $valor) { echo '<td>' . e($valor) . '</td>'; }
            echo '</tr>';
        }
        if (!empty($dados['totais'])) {
            echo '<tr style="font-weight:bold;background:#fafafa;">';
            foreach ($dados['totais'] as $valor) { echo '<td>' . e($valor) . '</td>'; }
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '<p style="margin-top:24px;">Assinatura do Responsável: _______________________________</p>';
        echo '<script>window.print();</script>';
        echo '</body></html>';
        exit;
    }

    private function gerarRelatorio($tipo, $inicio, $fim)
    {
        $db = Conexao::obter();
        switch ($tipo) {
            case 'consumo_produto':
                $stmt = $db->prepare('SELECT p.nome, SUM(m.quantidade) AS total_saida FROM movimentacoes m LEFT JOIN produtos p ON p.id = m.produto_id WHERE m.tipo_movimentacao = \'saida\' AND DATE(m.data_movimentacao) BETWEEN :inicio AND :fim GROUP BY p.nome ORDER BY total_saida DESC');
                $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Produto', 'Total de Saídas'],
                    'linhas' => array_map(function ($l) { return [$l['nome'], $l['total_saida']]; }, $linhas),
                    'totais' => ['Total', array_sum(array_column($linhas, 'total_saida'))]
                ];
            case 'consumo_categoria':
                $stmt = $db->prepare('SELECT c.nome AS categoria, SUM(m.quantidade) AS total_saida FROM movimentacoes m LEFT JOIN produtos p ON p.id = m.produto_id LEFT JOIN categorias c ON c.id = p.categoria_id WHERE m.tipo_movimentacao = \'saida\' AND DATE(m.data_movimentacao) BETWEEN :inicio AND :fim GROUP BY c.nome ORDER BY total_saida DESC');
                $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Categoria', 'Total de Saídas'],
                    'linhas' => array_map(function ($l) { return [$l['categoria'], $l['total_saida']]; }, $linhas),
                    'totais' => ['Total', array_sum(array_column($linhas, 'total_saida'))]
                ];
            case 'consumo_setor_mensal':
                $stmt = $db->prepare('SELECT DATE_FORMAT(s.data_saida, \'%Y-%m\') AS mes, s.setor, SUM(s.quantidade) AS total_saida FROM saidas s WHERE DATE(s.data_saida) BETWEEN :inicio AND :fim GROUP BY mes, s.setor ORDER BY mes DESC, total_saida DESC');
                $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Mês', 'Setor', 'Total de Saídas'],
                    'linhas' => array_map(function ($l) { return [$l['mes'], $l['setor'], $l['total_saida']]; }, $linhas),
                    'totais' => ['Total', '', array_sum(array_column($linhas, 'total_saida'))]
                ];
            case 'descartes':
                $stmt = $db->prepare('SELECT p.nome, SUM(m.quantidade) AS total_descarte FROM movimentacoes m LEFT JOIN produtos p ON p.id = m.produto_id WHERE m.tipo_movimentacao = \'descarte\' AND DATE(m.data_movimentacao) BETWEEN :inicio AND :fim GROUP BY p.nome ORDER BY total_descarte DESC');
                $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Produto', 'Total de Descartes'],
                    'linhas' => array_map(function ($l) { return [$l['nome'], $l['total_descarte']]; }, $linhas),
                    'totais' => ['Total', array_sum(array_column($linhas, 'total_descarte'))]
                ];
            case 'descartes_vs_saidas':
                $stmt = $db->prepare('SELECT p.nome,
                    SUM(CASE WHEN m.tipo_movimentacao = \'saida\' THEN m.quantidade ELSE 0 END) AS total_saidas,
                    SUM(CASE WHEN m.tipo_movimentacao = \'descarte\' THEN m.quantidade ELSE 0 END) AS total_descartes
                FROM movimentacoes m
                LEFT JOIN produtos p ON p.id = m.produto_id
                WHERE m.tipo_movimentacao IN (\'saida\', \'descarte\')
                  AND DATE(m.data_movimentacao) BETWEEN :inicio AND :fim
                GROUP BY p.nome
                ORDER BY p.nome');
                $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
                $linhas = $stmt->fetchAll();
                $linhasFormatadas = array_map(function ($l) {
                    $dif = (float)$l['total_saidas'] - (float)$l['total_descartes'];
                    return [$l['nome'], $l['total_saidas'], $l['total_descartes'], $dif];
                }, $linhas);
                return [
                    'cabecalho' => ['Produto', 'Total de Saidas', 'Total de Descartes', 'Diferenca'],
                    'linhas' => $linhasFormatadas,
                    'totais' => ['Total',
                        array_sum(array_column($linhas, 'total_saidas')),
                        array_sum(array_column($linhas, 'total_descartes')),
                        array_sum(array_map(function ($l) { return (float)$l['total_saidas'] - (float)$l['total_descartes']; }, $linhas))
                    ]
                ];
            case 'ajustes':
                $stmt = $db->prepare('SELECT m.data_movimentacao, p.nome, m.quantidade, u.nome AS usuario_nome, m.observacoes FROM movimentacoes m LEFT JOIN produtos p ON p.id = m.produto_id LEFT JOIN usuarios u ON u.id = m.usuario_id WHERE m.tipo_movimentacao = \'ajuste\' AND DATE(m.data_movimentacao) BETWEEN :inicio AND :fim ORDER BY m.data_movimentacao DESC');
                $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Data', 'Produto', 'Quantidade', 'Usuário', 'Motivo'],
                    'linhas' => array_map(function ($l) { return [$l['data_movimentacao'], $l['nome'], $l['quantidade'], $l['usuario_nome'], $l['observacoes']]; }, $linhas),
                    'totais' => ['Total', '', array_sum(array_column($linhas, 'quantidade')), '', '']
                ];
            case 'estoque_minimo':
                $linhas = $db->query('SELECT nome, estoque_atual, estoque_minimo FROM produtos WHERE estoque_atual <= estoque_minimo ORDER BY nome')->fetchAll();
                return [
                    'cabecalho' => ['Produto', 'Estoque Atual', 'Estoque Mínimo'],
                    'linhas' => array_map(function ($l) { return [$l['nome'], $l['estoque_atual'], $l['estoque_minimo']]; }, $linhas),
                    'totais' => ['Total', count($linhas), '']
                ];
            case 'estoque_atual':
                $linhas = $db->query('SELECT nome, estoque_atual, unidade_medida FROM produtos ORDER BY nome')->fetchAll();
                return [
                    'cabecalho' => ['Produto', 'Estoque Atual', 'Unidade'],
                    'linhas' => array_map(function ($l) { return [$l['nome'], $l['estoque_atual'], $l['unidade_medida']]; }, $linhas),
                    'totais' => ['Total', count($linhas), '']
                ];
            case 'movimentacoes':
            default:
                $stmt = $db->prepare('SELECT m.data_movimentacao, p.nome, m.tipo_movimentacao, m.quantidade, u.nome AS usuario_nome FROM movimentacoes m LEFT JOIN produtos p ON p.id = m.produto_id LEFT JOIN usuarios u ON u.id = m.usuario_id WHERE DATE(m.data_movimentacao) BETWEEN :inicio AND :fim ORDER BY m.data_movimentacao DESC');
                $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
                $linhas = $stmt->fetchAll();
                return [
                    'cabecalho' => ['Data', 'Produto', 'Tipo', 'Quantidade', 'Usuário'],
                    'linhas' => array_map(function ($l) { return [$l['data_movimentacao'], $l['nome'], $l['tipo_movimentacao'], $l['quantidade'], $l['usuario_nome']]; }, $linhas),
                    'totais' => ['Total', '', '', array_sum(array_column($linhas, 'quantidade')), '']
                ];
        }
    }
}
