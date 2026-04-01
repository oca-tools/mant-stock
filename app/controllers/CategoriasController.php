<?php
// Controller de categorias
class CategoriasController extends ControllerBase
{
    public function index()
    {
        $model = new Categoria();
        $categorias = $model->listar();
        $this->render('categorias/index', ['categorias' => $categorias]);
    }

    public function criar()
    {
        $this->render('categorias/criar', ['erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $nome = trim($_POST['nome'] ?? '');
        if ($nome === '') {
            $this->render('categorias/criar', ['erro' => 'Nome da categoria e obrigatorio.']);
            return;
        }

        $model = new Categoria();
        $dados = [
            'nome' => $nome,
            'descricao' => trim($_POST['descricao'] ?? '')
        ];
        $id = $model->criar($dados);
        LogService::registrar($_SESSION['usuario']['id'], 'criacao', 'Categoria criada', 'categorias', $id, null, $dados);
        redirect(url('categorias'));
    }

    public function editar($id)
    {
        $model = new Categoria();
        $categoria = $model->buscarPorId($id);
        if (!$categoria) {
            http_response_code(404);
            echo 'Categoria nao encontrada.';
            return;
        }
        $this->render('categorias/editar', ['categoria' => $categoria, 'erro' => null]);
    }

    public function atualizar($id)
    {
        $this->exigirCsrf();
        $nome = trim($_POST['nome'] ?? '');
        if ($nome === '') {
            $model = new Categoria();
            $categoria = $model->buscarPorId($id);
            $this->render('categorias/editar', ['categoria' => $categoria, 'erro' => 'Nome da categoria e obrigatorio.']);
            return;
        }

        $model = new Categoria();
        $antes = $model->buscarPorId($id);
        $dados = [
            'nome' => $nome,
            'descricao' => trim($_POST['descricao'] ?? '')
        ];
        $model->atualizar($id, $dados);
        LogService::registrar($_SESSION['usuario']['id'], 'edicao', 'Categoria atualizada', 'categorias', $id, $antes, $dados);
        redirect(url('categorias'));
    }

    public function excluir($id)
    {
        $this->exigirCsrf();
        $model = new Categoria();
        $antes = $model->buscarPorId($id);
        $model->excluir($id);
        LogService::registrar($_SESSION['usuario']['id'], 'exclusao', 'Categoria excluida', 'categorias', $id, $antes, null);
        redirect(url('categorias'));
    }
}
