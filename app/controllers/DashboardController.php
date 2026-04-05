<?php
// Controller do dashboard
class DashboardController extends ControllerBase
{
    public function index()
    {
        $db = Conexao::obter();
        $totalProdutos = (int)$db->query('SELECT COUNT(*) AS total FROM produtos')->fetch()['total'];
        $estoqueBaixo = $db->query('SELECT p.*, c.nome AS categoria_nome FROM produtos p LEFT JOIN categorias c ON c.id = p.categoria_id WHERE p.estoque_atual <= p.estoque_minimo ORDER BY p.nome LIMIT 10')->fetchAll();

        $movModel = new Movimentacao();
        $movimentacoes = $movModel->listarRecentes(8);

        $maisUtilizados = $db->query('SELECT p.nome, SUM(m.quantidade) AS total_saida FROM movimentacoes m LEFT JOIN produtos p ON p.id = m.produto_id WHERE m.tipo_movimentacao = \'saida\' GROUP BY p.nome ORDER BY total_saida DESC LIMIT 5')->fetchAll();

        $maisDescartados = $db->query('SELECT p.nome, SUM(m.quantidade) AS total_descarte FROM movimentacoes m LEFT JOIN produtos p ON p.id = m.produto_id WHERE m.tipo_movimentacao = \'descarte\' GROUP BY p.nome ORDER BY total_descarte DESC LIMIT 5')->fetchAll();

        $this->render('dashboard/index', [
            'totalProdutos' => $totalProdutos,
            'estoqueBaixo' => $estoqueBaixo,
            'movimentacoes' => $movimentacoes,
            'maisUtilizados' => $maisUtilizados,
            'maisDescartados' => $maisDescartados
        ]);
    }
}
