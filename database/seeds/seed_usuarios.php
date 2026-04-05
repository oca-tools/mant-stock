<?php
// Seed de usuario administrador com senha forte.
require __DIR__ . '/../../app/services/Conexao.php';

$nome = getenv('SEED_ADMIN_NOME') ?: 'Administrador';
$email = getenv('SEED_ADMIN_EMAIL') ?: 'admin@resort.local';
$senhaInformada = getenv('SEED_ADMIN_SENHA');
$senhaGerada = false;

if (!$senhaInformada) {
    $senhaInformada = bin2hex(random_bytes(6)) . 'Aa!';
    $senhaGerada = true;
}

$senhaHash = password_hash($senhaInformada, PASSWORD_BCRYPT);
$db = Conexao::obter();

$stmt = $db->prepare(
    'INSERT INTO usuarios (nome, email, senha_hash, tipo_usuario, ativo, created_at)
     SELECT :nome, :email, :senha_hash, :tipo_usuario, :ativo, NOW()
     WHERE NOT EXISTS (
         SELECT 1 FROM usuarios WHERE email = :email_check AND tipo_usuario = :tipo_usuario_check
     )'
);
$stmt->execute([
    ':nome' => $nome,
    ':email' => $email,
    ':senha_hash' => $senhaHash,
    ':tipo_usuario' => 'Administrador',
    ':ativo' => 1,
    ':email_check' => $email,
    ':tipo_usuario_check' => 'Administrador'
]);

if ($stmt->rowCount() > 0) {
    echo "Usuario administrador criado com sucesso.\n";
    echo 'Email: ' . $email . "\n";
    if ($senhaGerada) {
        echo "Senha temporaria gerada automaticamente: " . $senhaInformada . "\n";
        echo "Altere a senha no primeiro login.\n";
    } else {
        echo "Senha definida por variavel de ambiente.\n";
    }
} else {
    echo "Seed ignorado: ja existe usuario administrador para este e-mail.\n";
}
