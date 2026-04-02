<?php
// Controller de itens criticos
class CriticosController extends ControllerBase
{
    public function index()
    {
        $categoriaId = (int)($_GET['categoria_id'] ?? 0);
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->listar();

        $produtoModel = new Produto();
        $produtos = $produtoModel->listarCriticos($categoriaId ?: null);

        $this->render('criticos/index', [
            'categorias' => $categorias,
            'produtos' => $produtos,
            'categoria_id' => $categoriaId
        ]);
    }
}
