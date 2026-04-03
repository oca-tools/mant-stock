<?php
// Rotina de minimizacao de dados para suporte operacional a LGPD.
// Uso: php scripts/rotina_lgpd_retencao.php

date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . '/../app/services/Conexao.php';

$config = require __DIR__ . '/../app/config/config.php';
$configLgpd = $config['lgpd'] ?? [];

$diasRetencao = max(1, (int)($configLgpd['retencao_logs_dias'] ?? 365));
$diasAnonimizacao = max(1, (int)($configLgpd['anonimizacao_logs_dias'] ?? 90));

$limiteAnonimizacao = date('Y-m-d H:i:s', strtotime('-' . $diasAnonimizacao . ' days'));
$limiteExclusao = date('Y-m-d H:i:s', strtotime('-' . $diasRetencao . ' days'));

$db = Conexao::obter();

// 1) Anonimiza IP e user-agent em logs antigos.
$logsExiste = (int)$db->query("SELECT COUNT(1) AS total FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'logs'")->fetch()['total'] > 0;
$anonimizados = 0;
$removidos = 0;
if ($logsExiste) {
    $stmtAnon = $db->prepare(
        'UPDATE logs
         SET ip = :ip_anonimo,
             user_agent = NULL
         WHERE data_log < :limite
           AND (ip <> :ip_anonimo OR user_agent IS NOT NULL)'
    );
    $stmtAnon->execute([
        ':ip_anonimo' => '0.0.0.0',
        ':limite' => $limiteAnonimizacao
    ]);
    $anonimizados = $stmtAnon->rowCount();

    // 2) Remove logs acima da janela de retencao.
    $stmtDel = $db->prepare('DELETE FROM logs WHERE data_log < :limite');
    $stmtDel->execute([':limite' => $limiteExclusao]);
    $removidos = $stmtDel->rowCount();
}

// 3) Marca convites pendentes expirados.
$convitesExiste = (int)$db->query("SELECT COUNT(1) AS total FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'convites_usuarios'")->fetch()['total'] > 0;
$convitesExpirados = 0;
if ($convitesExiste) {
    $stmtConvites = $db->prepare(
        "UPDATE convites_usuarios
         SET status = 'expirado'
         WHERE status = 'pendente'
           AND expira_em < NOW()"
    );
    $stmtConvites->execute();
    $convitesExpirados = $stmtConvites->rowCount();
}

echo "Rotina LGPD concluida com sucesso.\n";
echo "Limite anonimizacao: {$limiteAnonimizacao}\n";
echo "Limite exclusao: {$limiteExclusao}\n";
echo "Logs anonimizados: {$anonimizados}\n";
echo "Logs removidos: {$removidos}\n";
echo "Convites expirados atualizados: {$convitesExpirados}\n";
