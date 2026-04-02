<?php
// Funcoes auxiliares do sistema
function e($texto)
{
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function view($caminho, $dados = [])
{
    extract($dados);
    $arquivo = __DIR__ . '/../views/' . $caminho . '.php';
    if (!file_exists($arquivo)) {
        http_response_code(500);
        echo 'View nao encontrada: ' . e($caminho);
        exit;
    }
    require $arquivo;
}

function url($caminho = '')
{
    $config = require __DIR__ . '/../config/config.php';
    $base = rtrim($config['app']['url_base'], '/');
    $base = preg_replace('/^\xEF\xBB\xBF/', '', $base);
    $caminho = ltrim($caminho, '/');
    if ($caminho !== '' && strpos($caminho, $base) === 0) {
        return '/' . ltrim($caminho, '/');
    }
    return $base . '/' . $caminho;
}

function url_absoluta($caminho = '')
{
    $config = require __DIR__ . '/../config/config.php';
    $basePublica = trim((string)($config['app']['url_publica'] ?? ''));
    if ($basePublica === '') {
        $esquema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePublica = $esquema . '://' . $host;
    }
    return rtrim($basePublica, '/') . url($caminho);
}

function rota_atual()
{
    static $rota = null;
    if ($rota !== null) {
        return $rota;
    }

    $config = require __DIR__ . '/../config/config.php';
    $base = rtrim((string)($config['app']['url_base'] ?? ''), '/');
    $base = preg_replace('/^\xEF\xBB\xBF/', '', $base);

    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $uri = preg_replace('#^/index\.php#', '', (string)$uri);
    if ($base !== '' && strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }
    $rota = rtrim($uri, '/') ?: '/';
    return $rota;
}

function rota_ativa($caminho)
{
    $caminho = '/' . ltrim((string)$caminho, '/');
    $caminho = rtrim($caminho, '/') ?: '/';
    return rota_atual() === $caminho;
}

function rota_comeca_com($prefixos)
{
    $rota = rota_atual();
    foreach ((array)$prefixos as $prefixo) {
        $prefixo = '/' . ltrim((string)$prefixo, '/');
        $prefixo = rtrim($prefixo, '/') ?: '/';
        if ($prefixo === '/') {
            if ($rota === '/') {
                return true;
            }
            continue;
        }
        if (strpos($rota, $prefixo) === 0) {
            return true;
        }
    }
    return false;
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function validar_csrf()
{
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        echo 'Falha de seguranca (CSRF).';
        exit;
    }
}

function flash_set($chave, $mensagem, $tipo = 'success')
{
    $_SESSION['flash'][$chave] = ['mensagem' => $mensagem, 'tipo' => $tipo];
}

function flash_get($chave)
{
    if (!empty($_SESSION['flash'][$chave])) {
        $dado = $_SESSION['flash'][$chave];
        unset($_SESSION['flash'][$chave]);
        return $dado;
    }
    return null;
}

function estoque_baixo_count()
{
    if (empty($_SESSION['usuario'])) {
        return 0;
    }
    $db = Conexao::obter();
    $linha = $db->query('SELECT COUNT(*) AS total FROM produtos WHERE estoque_atual <= estoque_minimo')->fetch();
    return (int)$linha['total'];
}
