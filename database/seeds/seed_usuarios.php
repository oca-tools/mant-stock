<?php
// Seed de usuario administrador
require __DIR__ . '/../../app/services/Conexao.php';

$senha = password_hash('admin123', PASSWORD_BCRYPT);
$db = Conexao::obter();
$stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha_hash, tipo_usuario, ativo, created_at) VALUES (:nome, :email, :senha_hash, :tipo_usuario, :ativo, NOW())');
$stmt->execute([
    ':nome' => 'Administrador',
    ':email' => 'admin@resort.local',
    ':senha_hash' => $senha,
    ':tipo_usuario' => 'Administrador',
    ':ativo' => 1
]);

echo "Usuario administrador criado com sucesso.\n";
