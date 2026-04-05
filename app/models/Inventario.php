<?php
// Modelo de inventarios mensais
class Inventario extends ModeloBase
{
    public function buscarCicloPorCompetencia($competencia)
    {
        $stmt = $this->db->prepare(
            'SELECT im.*,
                    ua.nome AS usuario_abertura_nome,
                    uf.nome AS usuario_fechamento_nome
             FROM inventarios_mensais im
             LEFT JOIN usuarios ua ON ua.id = im.usuario_abertura_id
             LEFT JOIN usuarios uf ON uf.id = im.usuario_fechamento_id
             WHERE im.competencia = :competencia
             LIMIT 1'
        );
        $stmt->execute([':competencia' => $competencia]);
        return $stmt->fetch() ?: null;
    }

    public function listarCompetenciasRecentes($limite = 12)
    {
        $stmt = $this->db->prepare('SELECT competencia FROM inventarios_mensais ORDER BY competencia DESC LIMIT :limite');
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return array_map(static function ($item) {
            return $item['competencia'];
        }, $stmt->fetchAll());
    }

    public function abrirCiclo($competencia, $usuarioId, $observacoes = '')
    {
        $stmt = $this->db->prepare(
            'INSERT INTO inventarios_mensais
                (competencia, status, observacoes_abertura, usuario_abertura_id, data_abertura)
             VALUES
                (:competencia, \'aberto\', :observacoes_abertura, :usuario_abertura_id, NOW())'
        );
        $stmt->execute([
            ':competencia' => $competencia,
            ':observacoes_abertura' => $observacoes,
            ':usuario_abertura_id' => $usuarioId
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function listarPorCompetencia($competencia, $limite = 400)
    {
        $stmt = $this->db->prepare(
            'SELECT i.*, p.nome AS produto_nome, p.unidade_medida, u.nome AS usuario_nome
             FROM inventarios i
             INNER JOIN inventarios_mensais im ON im.id = i.inventario_mensal_id
             LEFT JOIN produtos p ON p.id = i.produto_id
             LEFT JOIN usuarios u ON u.id = i.usuario_id
             WHERE im.competencia = :competencia
             ORDER BY p.nome ASC
             LIMIT :limite'
        );
        $stmt->bindValue(':competencia', $competencia, PDO::PARAM_STR);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function resumoPorCompetencia($competencia)
    {
        $stmt = $this->db->prepare(
            'SELECT
                COUNT(i.id) AS total_contados,
                SUM(CASE WHEN i.diferenca <> 0 THEN 1 ELSE 0 END) AS total_divergencias,
                SUM(CASE WHEN i.diferenca > 0 THEN 1 ELSE 0 END) AS total_sobras,
                SUM(CASE WHEN i.diferenca < 0 THEN 1 ELSE 0 END) AS total_faltas
             FROM inventarios i
             INNER JOIN inventarios_mensais im ON im.id = i.inventario_mensal_id
             WHERE im.competencia = :competencia'
        );
        $stmt->execute([':competencia' => $competencia]);
        $linha = $stmt->fetch() ?: [];
        return [
            'total_contados' => (int)($linha['total_contados'] ?? 0),
            'total_divergencias' => (int)($linha['total_divergencias'] ?? 0),
            'total_sobras' => (int)($linha['total_sobras'] ?? 0),
            'total_faltas' => (int)($linha['total_faltas'] ?? 0)
        ];
    }

    public function contarProdutosAtivos()
    {
        $linha = $this->db->query('SELECT COUNT(*) AS total FROM produtos')->fetch();
        return (int)$linha['total'];
    }

    public function listarPendentesPorCompetencia($competencia, $limite = 15)
    {
        $stmt = $this->db->prepare(
            'SELECT p.id, p.nome, p.estoque_atual, p.unidade_medida
             FROM produtos p
             WHERE NOT EXISTS (
                SELECT 1
                FROM inventarios i
                INNER JOIN inventarios_mensais im ON im.id = i.inventario_mensal_id
                WHERE im.competencia = :competencia
                  AND i.produto_id = p.id
             )
             ORDER BY p.nome ASC
             LIMIT :limite'
        );
        $stmt->bindValue(':competencia', $competencia, PDO::PARAM_STR);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function salvarContagem($dados)
    {
        $stmtInserir = $this->db->prepare(
            'INSERT INTO inventarios
                (inventario_mensal_id, produto_id, quantidade_sistema, quantidade_real, diferenca, usuario_id, motivo_ajuste, ajuste_aplicado, data_inventario)
             VALUES
                (:inventario_mensal_id, :produto_id, :quantidade_sistema, :quantidade_real, :diferenca, :usuario_id, :motivo_ajuste, 0, NOW())
             ON DUPLICATE KEY UPDATE
                id = LAST_INSERT_ID(id),
                quantidade_sistema = VALUES(quantidade_sistema),
                quantidade_real = VALUES(quantidade_real),
                diferenca = VALUES(diferenca),
                usuario_id = VALUES(usuario_id),
                motivo_ajuste = VALUES(motivo_ajuste),
                ajuste_aplicado = 0,
                data_ajuste = NULL,
                data_inventario = NOW()'
        );
        $stmtInserir->execute([
            ':inventario_mensal_id' => $dados['inventario_mensal_id'],
            ':produto_id' => $dados['produto_id'],
            ':quantidade_sistema' => $dados['quantidade_sistema'],
            ':quantidade_real' => $dados['quantidade_real'],
            ':diferenca' => $dados['diferenca'],
            ':usuario_id' => $dados['usuario_id'],
            ':motivo_ajuste' => $dados['motivo_ajuste']
        ]);

        $id = (int)$this->db->lastInsertId();
        $atualizado = $stmtInserir->rowCount() !== 1;
        return [
            'id' => $id,
            'atualizado' => $atualizado
        ];
    }

    public function contarRegistrosDoCiclo($cicloId)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total FROM inventarios WHERE inventario_mensal_id = :ciclo_id');
        $stmt->execute([':ciclo_id' => $cicloId]);
        $linha = $stmt->fetch();
        return (int)$linha['total'];
    }

    public function listarItensParaAjuste($cicloId)
    {
        $stmt = $this->db->prepare(
            'SELECT i.*, p.nome AS produto_nome
             FROM inventarios i
             INNER JOIN produtos p ON p.id = i.produto_id
             WHERE i.inventario_mensal_id = :ciclo_id
               AND i.ajuste_aplicado = 0
               AND i.diferenca <> 0
             ORDER BY p.nome ASC'
        );
        $stmt->execute([':ciclo_id' => $cicloId]);
        return $stmt->fetchAll();
    }

    public function marcarCicloComoAjustado($cicloId)
    {
        $stmt = $this->db->prepare(
            'UPDATE inventarios
             SET ajuste_aplicado = 1,
                 data_ajuste = NOW()
             WHERE inventario_mensal_id = :ciclo_id
               AND ajuste_aplicado = 0'
        );
        $stmt->execute([':ciclo_id' => $cicloId]);
        return $stmt->rowCount();
    }

    public function fecharCiclo($cicloId, $usuarioId, $observacoes = '')
    {
        $stmt = $this->db->prepare(
            'UPDATE inventarios_mensais
             SET status = \'fechado\',
                 usuario_fechamento_id = :usuario_fechamento_id,
                 observacoes_fechamento = :observacoes_fechamento,
                 data_fechamento = NOW()
             WHERE id = :id
               AND status = \'aberto\''
        );
        $stmt->execute([
            ':usuario_fechamento_id' => $usuarioId,
            ':observacoes_fechamento' => $observacoes,
            ':id' => $cicloId
        ]);
        return $stmt->rowCount() > 0;
    }
}
