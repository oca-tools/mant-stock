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

        self::validarAceiteLgpd($config);
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

    private static function validarAceiteLgpd(array $config)
    {
        $configLgpd = $config['lgpd'] ?? [];
        if (empty($configLgpd['exigir_aceite'])) {
            return;
        }

        $rota = rota_atual();
        $rotasPermitidas = ['/lgpd/aceite', '/lgpd/politica', '/logout'];
        if (in_array($rota, $rotasPermitidas, true)) {
            return;
        }

        $usuarioId = (int)($_SESSION['usuario']['id'] ?? 0);
        if ($usuarioId <= 0) {
            return;
        }

        // Recarrega os dados de aceite em toda requisicao para manter consistencia.
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->buscarPorId($usuarioId);
        if ($usuario) {
            $_SESSION['usuario']['lgpd_aceite_at'] = $usuario['lgpd_aceite_at'] ?? null;
            $_SESSION['usuario']['lgpd_aceite_versao'] = $usuario['lgpd_aceite_versao'] ?? null;
        }

        $versaoAtual = (string)($configLgpd['versao_politica'] ?? '');
        $aceiteAt = $_SESSION['usuario']['lgpd_aceite_at'] ?? null;
        $aceiteVersao = (string)($_SESSION['usuario']['lgpd_aceite_versao'] ?? '');

        if ($aceiteAt && $aceiteVersao !== '' && $aceiteVersao === $versaoAtual) {
            return;
        }

        redirect(url('lgpd/aceite'));
    }
}
