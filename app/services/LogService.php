<?php
// Servico para registrar logs de auditoria
class LogService
{
    public static function registrar($usuarioId, $acao, $descricao, $entidade = null, $entidadeId = null, $antes = null, $depois = null)
    {
        $db = Conexao::obter();
        $stmt = $db->prepare('INSERT INTO logs (usuario_id, acao, entidade, entidade_id, descricao, ip, user_agent, dados_antes, dados_depois, data_log) VALUES (:usuario_id, :acao, :entidade, :entidade_id, :descricao, :ip, :user_agent, :dados_antes, :dados_depois, NOW())');
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':acao' => $acao,
            ':entidade' => $entidade,
            ':entidade_id' => $entidadeId,
            ':descricao' => $descricao,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ':dados_antes' => $antes ? json_encode($antes) : null,
            ':dados_depois' => $depois ? json_encode($depois) : null
        ]);
    }
}
