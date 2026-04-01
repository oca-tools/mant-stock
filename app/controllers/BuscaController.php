<?php
// Controller de busca global
class BuscaController extends ControllerBase
{
    public function index()
    {
        $termo = trim($_GET['q'] ?? '');
        $produtos = [];
        $movimentacoes = [];

        if ($termo !== '') {
            $db = Conexao::obter();
            $stmt = $db->prepare('SELECT p.*, c.nome AS categoria_nome FROM produtos p LEFT JOIN categorias c ON c.id = p.categoria_id WHERE p.nome LIKE :q1 OR p.codigo_interno LIKE :q2 ORDER BY p.nome LIMIT 20');
            $stmt->execute([':q1' => '%' . $termo . '%', ':q2' => '%' . $termo . '%']);
            $produtos = $stmt->fetchAll();

            $stmt = $db->prepare('SELECT m.*, p.nome AS produto_nome FROM movimentacoes m LEFT JOIN produtos p ON p.id = m.produto_id WHERE p.nome LIKE :q1 OR m.tipo_movimentacao LIKE :q2 ORDER BY m.data_movimentacao DESC LIMIT 20');
            $stmt->execute([':q1' => '%' . $termo . '%', ':q2' => '%' . $termo . '%']);
            $movimentacoes = $stmt->fetchAll();
        }

        $this->render('busca/index', [
            'termo' => $termo,
            'produtos' => $produtos,
            'movimentacoes' => $movimentacoes
        ]);
    }
}
