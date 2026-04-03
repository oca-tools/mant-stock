<?php
// Modelo para gestao das solicitacoes de titulares (LGPD)
class SolicitacaoLgpd extends ModeloBase
{
    public function listarRecentes($limite = 100)
    {
        $limite = max(1, (int)$limite);
        $sql = 'SELECT s.*,
                       ua.nome AS aberto_por_nome,
                       ur.nome AS responsavel_nome
                FROM solicitacoes_lgpd s
                LEFT JOIN usuarios ua ON ua.id = s.usuario_abertura_id
                LEFT JOIN usuarios ur ON ur.id = s.usuario_responsavel_id
                ORDER BY s.data_abertura DESC
                LIMIT ' . $limite;
        return $this->db->query($sql)->fetchAll();
    }

    public function criar(array $dados)
    {
        $protocolo = $this->gerarProtocolo();
        $stmt = $this->db->prepare(
            'INSERT INTO solicitacoes_lgpd
            (protocolo, titular_nome, titular_email, tipo_solicitacao, descricao, status, usuario_abertura_id, data_abertura, data_atualizacao)
            VALUES
            (:protocolo, :titular_nome, :titular_email, :tipo_solicitacao, :descricao, :status, :usuario_abertura_id, NOW(), NOW())'
        );
        $stmt->execute([
            ':protocolo' => $protocolo,
            ':titular_nome' => $dados['titular_nome'],
            ':titular_email' => $dados['titular_email'],
            ':tipo_solicitacao' => $dados['tipo_solicitacao'],
            ':descricao' => $dados['descricao'],
            ':status' => 'aberta',
            ':usuario_abertura_id' => $dados['usuario_abertura_id']
        ]);

        return [
            'id' => (int)$this->db->lastInsertId(),
            'protocolo' => $protocolo
        ];
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM solicitacoes_lgpd WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function atualizarStatus($id, $status, $resposta, $usuarioResponsavelId)
    {
        $status = (string)$status;
        $resposta = trim((string)$resposta);
        $encerrar = in_array($status, ['concluida', 'indeferida'], true);

        $stmt = $this->db->prepare(
            'UPDATE solicitacoes_lgpd
             SET status = :status,
                 resposta = :resposta,
                 usuario_responsavel_id = :usuario_responsavel_id,
                 data_atualizacao = NOW(),
                 data_conclusao = :data_conclusao
             WHERE id = :id'
        );

        return $stmt->execute([
            ':id' => $id,
            ':status' => $status,
            ':resposta' => $resposta !== '' ? $resposta : null,
            ':usuario_responsavel_id' => $usuarioResponsavelId,
            ':data_conclusao' => $encerrar ? date('Y-m-d H:i:s') : null
        ]);
    }

    private function gerarProtocolo()
    {
        $base = 'LGPD-' . date('Ymd') . '-';
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total FROM solicitacoes_lgpd WHERE DATE(data_abertura) = CURDATE()');
        $stmt->execute();
        $linha = $stmt->fetch();
        $sequencia = ((int)($linha['total'] ?? 0)) + 1;
        return $base . str_pad((string)$sequencia, 4, '0', STR_PAD_LEFT);
    }
}
