<?php
// Front controller do sistema
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_trans_sid', '0');
ini_set('expose_php', '0');

$seguro = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

$config = require __DIR__ . '/../app/config/config.php';
$host = preg_replace('/[^A-Za-z0-9\.\-:]/', '', (string)($_SERVER['HTTP_HOST'] ?? ''));
$hostSemPorta = explode(':', $host)[0] ?? $host;
$isLocal = in_array($hostSemPorta, ['localhost', '127.0.0.1'], true);
$usarCookieSeguro = $seguro || (!empty($config['app']['forcar_https']) && !$isLocal);
if ($usarCookieSeguro) {
    ini_set('session.cookie_secure', '1');
}
ini_set('session.gc_maxlifetime', (string)((int)$config['sessao']['expiracao']));

if (!empty($config['app']['forcar_https'])) {
    $seguroAtual = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    if (!$isLocal && !$seguroAtual) {
        $uriRequisicao = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $destino = 'https://' . $host . $uriRequisicao;
        header('Location: ' . $destino);
        exit;
    }
}
session_name($config['sessao']['nome']);
session_set_cookie_params([
    'lifetime' => $config['sessao']['expiracao'],
    'path' => '/',
    'httponly' => true,
    'samesite' => $config['sessao']['samesite'] ?? 'Lax',
    'secure' => $usarCookieSeguro
]);

date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . '/../app/helpers/funcoes.php';
session_start();
csrf_token();

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Permitted-Cross-Domain-Policies: none');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header('Content-Security-Policy: default-src \'self\'; img-src \'self\' data: https:; style-src \'self\' \'unsafe-inline\' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src \'self\' https://fonts.gstatic.com https://cdn.jsdelivr.net; script-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net; connect-src \'self\'; frame-ancestors \'self\'; base-uri \'self\'; form-action \'self\';');
if ($usarCookieSeguro && !$isLocal) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}
require __DIR__ . '/../app/services/Conexao.php';
require __DIR__ . '/../app/services/Router.php';
require __DIR__ . '/../app/middleware/AuthMiddleware.php';

// Autoload simples para models e controllers
spl_autoload_register(function ($classe) {
    $caminhos = [
        __DIR__ . '/../app/controllers/' . $classe . '.php',
        __DIR__ . '/../app/models/' . $classe . '.php',
        __DIR__ . '/../app/services/' . $classe . '.php'
    ];
    foreach ($caminhos as $arquivo) {
        if (file_exists($arquivo)) {
            require $arquivo;
            return;
        }
    }
});

$router = new Router();
$rotas = require __DIR__ . '/../routes/web.php';
foreach ($rotas as $rota) {
    $router->adicionar($rota['metodo'], $rota['caminho'], $rota['acao'], $rota['opcoes']);
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = preg_replace('#^/index\.php#', '', $uri);
$base = rtrim($config['app']['url_base'], '/');
if ($base !== '' && strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}
$uri = rtrim($uri, '/') ?: '/';

$router->executar($_SERVER['REQUEST_METHOD'], $uri);
