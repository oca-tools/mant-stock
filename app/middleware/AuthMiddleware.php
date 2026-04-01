<?php
// Middleware de autenticacao
class AuthMiddleware
{
    public static function verificar()
    {
        $config = require __DIR__ . '/../config/config.php';
        $expiracao = $config['sessao']['expiracao'];
        if (!empty($_SESSION['ultimo_acesso']) && (time() - $_SESSION['ultimo_acesso']) > $expiracao) {
            session_destroy();
            redirect(url('login'));
        }
        $_SESSION['ultimo_acesso'] = time();

        if (empty($_SESSION['usuario'])) {
            redirect(url('login'));
        }
    }

    public static function verificarAdmin()
    {
        self::verificar();
        if (($_SESSION['usuario']['tipo_usuario'] ?? '') !== 'Administrador') {
            http_response_code(403);
            echo 'Acesso negado.';
            exit;
        }
    }

    public static function verificarTipos($tiposPermitidos)
    {
        self::verificar();
        $tipo = $_SESSION['usuario']['tipo_usuario'] ?? '';
        if (!in_array($tipo, $tiposPermitidos, true)) {
            http_response_code(403);
            echo 'Acesso negado.';
            exit;
        }
    }
}
