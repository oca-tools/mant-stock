<?php
// Front controller do sistema
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');

$seguro = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if ($seguro) {
    ini_set('session.cookie_secure', '1');
}

$config = require __DIR__ . '/../app/config/config.php';
if (!empty($config['app']['forcar_https'])) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isLocal = in_array($host, ['localhost', '127.0.0.1'], true);
    $seguroAtual = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    if (!$isLocal && !$seguroAtual) {
        $destino = 'https://' . $host . $_SERVER['REQUEST_URI'];
        header('Location: ' . $destino);
        exit;
    }
}
session_name($config['sessao']['nome']);
session_set_cookie_params([
    'lifetime' => $config['sessao']['expiracao'],
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => $seguro
]);

date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . '/../app/helpers/funcoes.php';
session_start();
csrf_token();

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer-when-downgrade');
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
