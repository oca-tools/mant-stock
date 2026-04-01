<?php
// Controller de usuarios
class UsuariosController extends ControllerBase
{
    public function index()
    {
        $model = new Usuario();
        $usuarios = $model->listar();
        $this->render('usuarios/index', ['usuarios' => $usuarios]);
    }

    public function criar()
    {
        $this->render('usuarios/criar', ['erro' => null]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipo = $_POST['tipo_usuario'] ?? '';
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        if ($nome === '' || $email === '' || $senha === '' || $tipo === '') {
            $this->render('usuarios/criar', ['erro' => 'Preencha todos os campos obrigatorios.']);
            return;
        }

        $model = new Usuario();
        if ($model->buscarPorEmail($email)) {
            $this->render('usuarios/criar', ['erro' => 'Email ja cadastrado.']);
            return;
        }

        $dados = [
            'nome' => $nome,
            'email' => $email,
            'senha_hash' => password_hash($senha, PASSWORD_BCRYPT),
            'tipo_usuario' => $tipo,
            'ativo' => $ativo
        ];
        $id = $model->criar($dados);
        LogService::registrar($_SESSION['usuario']['id'], 'criacao', 'Usuario criado', 'usuarios', $id, null, $dados);
        redirect(url('usuarios'));
    }

    public function editar($id)
    {
        $model = new Usuario();
        $usuario = $model->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            echo 'Usuario nao encontrado.';
            return;
        }
        $this->render('usuarios/editar', ['usuario' => $usuario, 'erro' => null]);
    }

    public function atualizar($id)
    {
        $this->exigirCsrf();
        $model = new Usuario();
        $usuario = $model->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            echo 'Usuario nao encontrado.';
            return;
        }

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $tipo = $_POST['tipo_usuario'] ?? '';
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        if ($nome === '' || $email === '' || $tipo === '') {
            $this->render('usuarios/editar', ['usuario' => $usuario, 'erro' => 'Preencha todos os campos obrigatorios.']);
            return;
        }

        $senhaNova = $_POST['senha'] ?? '';
        $dados = [
            'nome' => $nome,
            'email' => $email,
            'tipo_usuario' => $tipo,
            'ativo' => $ativo
        ];

        if ($senhaNova !== '') {
            $dados['senha_hash'] = password_hash($senhaNova, PASSWORD_BCRYPT);
        }

        $model->atualizar($id, $dados);
        LogService::registrar($_SESSION['usuario']['id'], 'edicao', 'Usuario atualizado', 'usuarios', $id, $usuario, $dados);
        redirect(url('usuarios'));
    }

    public function desativar($id)
    {
        $this->exigirCsrf();
        $model = new Usuario();
        $usuario = $model->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            echo 'Usuario nao encontrado.';
            return;
        }

        $model->atualizar($id, ['ativo' => 0]);
        LogService::registrar($_SESSION['usuario']['id'], 'edicao', 'Usuario desativado', 'usuarios', $id, $usuario, ['ativo' => 0]);
        redirect(url('usuarios'));
    }
}
