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
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $rateLimit = new RateLimitLoginService();
        $status = $rateLimit->obterStatus($ip, $email);

        if (!empty($status['bloqueado'])) {
            $minutos = (int)ceil(((int)$status['segundos_restantes']) / 60);
            $mensagem = 'Muitas tentativas de login. Aguarde ' . max($minutos, 1) . ' minuto(s) e tente novamente.';
            $this->render('auth/login', ['erro' => $mensagem]);
            return;
        }

        if ($email === '' || $senha === '') {
            $this->render('auth/login', ['erro' => 'Informe email e senha.']);
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->autenticarPorEmailSenha($email, $senha);

        if (!$usuario) {
            $rateLimit->registrarFalha($ip, $email);
            $this->render('auth/login', ['erro' => 'Credenciais invalidas.']);
            return;
        }

        $rateLimit->registrarSucesso($ip, $email);
        $rateLimit->limparExpirados();

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

        encerrar_sessao_atual();
        redirect(url('login'));
    }
}
