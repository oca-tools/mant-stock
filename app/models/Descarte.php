<?php
// Modelo de descartes
class Descarte extends ModeloBase
{
    public function listar($limite = 50)
    {
        $stmt = $this->db->prepare('SELECT d.*, p.nome AS produto_nome, u.nome AS usuario_nome FROM descartes d LEFT JOIN produtos p ON p.id = d.produto_id LEFT JOIN usuarios u ON u.id = d.usuario_id ORDER BY d.data_descarte DESC LIMIT :limite');
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare('INSERT INTO descartes (produto_id, quantidade, motivo_descarte, item_recebido_troca, usuario_id, data_descarte, observacoes) VALUES (:produto_id, :quantidade, :motivo_descarte, :item_recebido_troca, :usuario_id, NOW(), :observacoes)');
        $stmt->execute([
            ':produto_id' => $dados['produto_id'],
            ':quantidade' => $dados['quantidade'],
            ':motivo_descarte' => $dados['motivo_descarte'],
            ':item_recebido_troca' => $dados['item_recebido_troca'],
            ':usuario_id' => $dados['usuario_id'],
            ':observacoes' => $dados['observacoes']
        ]);
        return $this->db->lastInsertId();
    }
}
