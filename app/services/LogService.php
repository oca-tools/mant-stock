<?php
// Servico para registrar logs de auditoria
class LogService
{
    public static function registrar($usuarioId, $acao, $descricao, $entidade = null, $entidadeId = null, $antes = null, $depois = null)
    {
        $antesSanitizado = self::sanitizarDados($antes);
        $depoisSanitizado = self::sanitizarDados($depois);

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
            ':dados_antes' => $antesSanitizado !== null ? json_encode($antesSanitizado, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            ':dados_depois' => $depoisSanitizado !== null ? json_encode($depoisSanitizado, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null
        ]);
    }

    private static function sanitizarDados($dados)
    {
        if ($dados === null) {
            return null;
        }

        if (!is_array($dados)) {
            return $dados;
        }

        $resultado = [];
        foreach ($dados as $chave => $valor) {
            $chaveTexto = strtolower((string)$chave);
            if (preg_match('/senha|token|csrf|cookie|authorization|api_key|secret/', $chaveTexto)) {
                $resultado[$chave] = '[DADO_SENSIVEL_OCULTO]';
                continue;
            }

            if (is_array($valor)) {
                $resultado[$chave] = self::sanitizarDados($valor);
                continue;
            }

            $resultado[$chave] = $valor;
        }

        return $resultado;
    }
}
