<?php
// Modelo de entradas
class Entrada extends ModeloBase
{
    public function listar($limite = 50)
    {
        $stmt = $this->db->prepare('SELECT e.*, p.nome AS produto_nome, u.nome AS usuario_nome FROM entradas e LEFT JOIN produtos p ON p.id = e.produto_id LEFT JOIN usuarios u ON u.id = e.usuario_id ORDER BY e.data_entrada DESC LIMIT :limite');
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare('INSERT INTO entradas (produto_id, quantidade, fornecedor, nota_fiscal, usuario_id, data_entrada, observacoes) VALUES (:produto_id, :quantidade, :fornecedor, :nota_fiscal, :usuario_id, NOW(), :observacoes)');
        $stmt->execute([
            ':produto_id' => $dados['produto_id'],
            ':quantidade' => $dados['quantidade'],
            ':fornecedor' => $dados['fornecedor'],
            ':nota_fiscal' => $dados['nota_fiscal'],
            ':usuario_id' => $dados['usuario_id'],
            ':observacoes' => $dados['observacoes']
        ]);
        return $this->db->lastInsertId();
    }
}
