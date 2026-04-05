<?php
// Controller de busca global
class BuscaController extends ControllerBase
{
    public function index()
    {
        $termo = trim((string)($_GET['q'] ?? ''));
        $termo = substr($termo, 0, 80);
        $produtos = [];
        $movimentacoes = [];

        if ($termo !== '') {
            $db = Conexao::obter();
            $prefixo = $termo . '%';
            $stmt = $db->prepare('SELECT p.*, c.nome AS categoria_nome FROM produtos p LEFT JOIN categorias c ON c.id = p.categoria_id WHERE p.codigo_interno = :codigo_exato OR p.nome LIKE :prefixo OR p.codigo_interno LIKE :prefixo ORDER BY p.nome LIMIT 20');
            $stmt->execute([':codigo_exato' => $termo, ':prefixo' => $prefixo]);
            $produtos = $stmt->fetchAll();

            $stmt = $db->prepare('SELECT m.*, p.nome AS produto_nome FROM movimentacoes m LEFT JOIN produtos p ON p.id = m.produto_id WHERE p.nome LIKE :prefixo_produto OR m.tipo_movimentacao LIKE :prefixo_tipo ORDER BY m.data_movimentacao DESC LIMIT 20');
            $stmt->execute([':prefixo_produto' => $prefixo, ':prefixo_tipo' => $prefixo]);
            $movimentacoes = $stmt->fetchAll();
        }

        $this->render('busca/index', [
            'termo' => $termo,
            'produtos' => $produtos,
            'movimentacoes' => $movimentacoes
        ]);
    }
}
