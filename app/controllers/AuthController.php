<?php
// Controller de autenticacao
class AuthController extends ControllerBase
{
    public function formLogin()
    {
        $this->render('auth/login', ['erro' => null]);
    }

    public function login()
    {
        $this->exigirCsrf();
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === '') {
            $this->render('auth/login', ['erro' => 'Informe email e senha.']);
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarPorEmail($email);

        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            $this->render('auth/login', ['erro' => 'Credenciais invalidas.']);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'tipo_usuario' => $usuario['tipo_usuario']
        ];

        LogService::registrar($usuario['id'], 'login', 'Usuario efetuou login', 'usuarios', $usuario['id']);

        redirect(url('dashboard'));
    }

    public function logout()
    {
        $this->exigirCsrf();
        if (!empty($_SESSION['usuario']['id'])) {
            LogService::registrar($_SESSION['usuario']['id'], 'logout', 'Usuario efetuou logout', 'usuarios', $_SESSION['usuario']['id']);
        }
        session_destroy();
        redirect(url('login'));
    }
}
