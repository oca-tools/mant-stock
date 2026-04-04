<?php
// Controller de autenticacao
class AuthController extends ControllerBase
{
    public function formLogin()
    {
        // Evita exibir a tela de login para quem ja esta autenticado.
        if (!empty($_SESSION['usuario']['id'])) {
            redirect(url('dashboard'));
        }

        $this->render('auth/login', ['erro' => null]);
    }

    public function login()
    {
        // Se a sessao ja estiver ativa, mantem o usuario no painel.
        if (!empty($_SESSION['usuario']['id'])) {
            redirect(url('dashboard'));
        }

        $this->exigirCsrf();
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === '') {
            $this->render('auth/login', ['erro' => 'Informe email e senha.']);
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->autenticarPorEmailSenha($email, $senha);

        if (!$usuario) {
            $this->render('auth/login', ['erro' => 'Credenciais invalidas.']);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'tipo_usuario' => $usuario['tipo_usuario'],
            'lgpd_aceite_at' => $usuario['lgpd_aceite_at'] ?? null,
            'lgpd_aceite_versao' => $usuario['lgpd_aceite_versao'] ?? null
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

        // Limpa dados da sessao e invalida o cookie para evitar sessao "fantasma".
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'], $params['httponly']);
        }
        session_destroy();
        redirect(url('login'));
    }
}
