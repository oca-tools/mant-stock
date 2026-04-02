<?php
// Configuracoes gerais do sistema
$lerAmbiente = static function (string $chave, $padrao = null) {
    $valor = getenv($chave);
    if ($valor === false || $valor === null || $valor === '') {
        return $padrao;
    }
    return $valor;
};

$toBool = static function ($valor, bool $padrao = false): bool {
    if (is_bool($valor)) {
        return $valor;
    }
    if ($valor === null || $valor === '') {
        return $padrao;
    }
    return filter_var($valor, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $padrao;
};

$toInt = static function ($valor, int $padrao): int {
    if (is_numeric($valor)) {
        return (int)$valor;
    }
    return $padrao;
};

return [
    'app' => [
        'nome' => (string)$lerAmbiente('APP_NOME', 'Controle de Estoque - Manutencao'),
        'url_base' => (string)$lerAmbiente('APP_URL_BASE', '/'),
        'url_publica' => (string)$lerAmbiente('APP_URL_PUBLICA', 'http://localhost'),
        'forcar_https' => $toBool($lerAmbiente('APP_FORCAR_HTTPS', false), false),
        'upload_max_mb' => $toInt($lerAmbiente('APP_UPLOAD_MAX_MB', 2), 2)
    ],
    'db' => [
        'host' => (string)$lerAmbiente('DB_HOST', 'localhost'),
        'banco' => (string)$lerAmbiente('DB_BANCO', 'estoque_manutencao'),
        'usuario' => (string)$lerAmbiente('DB_USUARIO', 'root'),
        'senha' => (string)$lerAmbiente('DB_SENHA', ''),
        'charset' => (string)$lerAmbiente('DB_CHARSET', 'utf8mb4')
    ],
    'sessao' => [
        'nome' => (string)$lerAmbiente('SESSAO_NOME', 'sessao_estoque'),
        'expiracao' => $toInt($lerAmbiente('SESSAO_EXPIRACAO', 7200), 7200)
    ],
    'mail' => [
        'remetente_email' => (string)$lerAmbiente('MAIL_REMETENTE_EMAIL', 'nao-responda@oca-mantstock.local'),
        'remetente_nome' => (string)$lerAmbiente('MAIL_REMETENTE_NOME', 'OCA MantStock'),
        'modo_teste' => $toBool($lerAmbiente('MAIL_MODO_TESTE', false), false)
    ],
    'listas' => [
        'unidades_medida' => ['un', 'cx', 'kg', 'g', 'l', 'ml', 'm', 'm2', 'm3', 'kit', 'pca']
    ]
];
