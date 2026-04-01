<?php
// Controller de ferramentas
class FerramentasController extends ControllerBase
{
    public function index()
    {
        $model = new Ferramenta();
        $ferramentas = $model->listar();
        $this->render('ferramentas/index', ['ferramentas' => $ferramentas]);
    }

    public function criar()
    {
        $this->render('ferramentas/criar', ['erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $nome = trim($_POST['nome'] ?? '');
        if ($nome === '') {
            $this->render('ferramentas/criar', ['erro' => 'Nome da ferramenta e obrigatorio.']);
            return;
        }

        $model = new Ferramenta();
        $dados = [
            'nome' => $nome,
            'descricao' => trim($_POST['descricao'] ?? ''),
            'status' => 'Disponivel'
        ];
        $id = $model->criar($dados);
        LogService::registrar($_SESSION['usuario']['id'], 'criacao', 'Ferramenta criada', 'ferramentas', $id, null, $dados);
        redirect(url('ferramentas'));
    }
}
