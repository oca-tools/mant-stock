<?php
// Controller do estoque atual
class EstoqueController extends ControllerBase
{
    public function index()
    {
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $busca = trim($_GET['busca'] ?? '');
        $ordem = $_GET['ordem'] ?? 'az';
        $limite = 12;
        $offset = ($pagina - 1) * $limite;

        $produtoModel = new Produto();
        $ordemSql = ($ordem === 'za') ? 'nome_desc' : 'nome_asc';
        $produtos = $produtoModel->listar($limite, $offset, $busca, $ordemSql);
        $total = $produtoModel->contar($busca);
        $totalPaginas = (int)ceil($total / $limite);

        $this->render('estoque/index', [
            'produtos' => $produtos,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'busca' => $busca,
            'ordem' => $ordem
        ]);
    }
}
