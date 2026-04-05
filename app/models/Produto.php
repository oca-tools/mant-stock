<?php
// Modelo de produtos
class Produto extends ModeloBase
{
    public function listar($limite = 20, $offset = 0, $busca = '', $ordem = 'nome_asc')
    {
        $sql = 'SELECT p.*, c.nome AS categoria_nome FROM produtos p LEFT JOIN categorias c ON c.id = p.categoria_id';
        $params = [];
        if ($busca !== '') {
            $sql .= ' WHERE p.nome LIKE :busca OR p.codigo_interno LIKE :busca';
            $params[':busca'] = '%' . $busca . '%';
        }
        $ordemSql = 'p.nome ASC';
        if ($ordem === 'nome_desc') {
            $ordemSql = 'p.nome DESC';
        }
        $sql .= ' ORDER BY ' . $ordemSql . ' LIMIT :limite OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        foreach ($params as $chave => $valor) {
            $stmt->bindValue($chave, $valor, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function listarParaExportacao($busca = '', $ordem = 'nome_asc')
    {
        $sql = 'SELECT p.*, c.nome AS categoria_nome FROM produtos p LEFT JOIN categorias c ON c.id = p.categoria_id';
        $params = [];
        if ($busca !== '') {
            $sql .= ' WHERE p.nome LIKE :busca OR p.codigo_interno LIKE :busca';
            $params[':busca'] = '%' . $busca . '%';
        }
        $ordemSql = ($ordem === 'nome_desc') ? 'p.nome DESC' : 'p.nome ASC';
        $sql .= ' ORDER BY ' . $ordemSql;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $chave => $valor) {
            $stmt->bindValue($chave, $valor, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contar($busca = '')
    {
        $sql = 'SELECT COUNT(*) AS total FROM produtos';
        $params = [];
        if ($busca !== '') {
            $sql .= ' WHERE nome LIKE :busca OR codigo_interno LIKE :busca';
            $params[':busca'] = '%' . $busca . '%';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $linha = $stmt->fetch();
        return (int)$linha['total'];
    }

    public function contarEstoqueBaixo()
    {
        $linha = $this->db->query('SELECT COUNT(*) AS total FROM produtos WHERE estoque_atual <= estoque_minimo')->fetch();
        return (int)$linha['total'];
    }

    public function listarCriticos($categoriaId = null)
    {
        $sql = 'SELECT p.*, c.nome AS categoria_nome FROM produtos p LEFT JOIN categorias c ON c.id = p.categoria_id WHERE p.estoque_atual <= p.estoque_minimo';
        $params = [];
        if (!empty($categoriaId)) {
            $sql .= ' AND p.categoria_id = :categoria_id';
            $params[':categoria_id'] = $categoriaId;
        }
        $sql .= ' ORDER BY p.nome';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM produtos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function buscarPorIdParaAtualizacao($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM produtos WHERE id = :id LIMIT 1 FOR UPDATE');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function criar($dados)
    {
        $stmt = $this->db->prepare('INSERT INTO produtos (nome, categoria_id, codigo_interno, unidade_medida, estoque_atual, estoque_minimo, localizacao, observacoes, imagem, created_at, updated_at) VALUES (:nome, :categoria_id, :codigo_interno, :unidade_medida, :estoque_atual, :estoque_minimo, :localizacao, :observacoes, :imagem, NOW(), NOW())');
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':categoria_id' => $dados['categoria_id'],
            ':codigo_interno' => $dados['codigo_interno'],
            ':unidade_medida' => $dados['unidade_medida'],
            ':estoque_atual' => $dados['estoque_atual'],
            ':estoque_minimo' => $dados['estoque_minimo'],
            ':localizacao' => $dados['localizacao'],
            ':observacoes' => $dados['observacoes'],
            ':imagem' => $dados['imagem']
        ]);
        return $this->db->lastInsertId();
    }

    public function atualizar($id, $dados)
    {
        $stmt = $this->db->prepare('UPDATE produtos SET nome = :nome, categoria_id = :categoria_id, codigo_interno = :codigo_interno, unidade_medida = :unidade_medida, estoque_atual = :estoque_atual, estoque_minimo = :estoque_minimo, localizacao = :localizacao, observacoes = :observacoes, imagem = :imagem, updated_at = NOW() WHERE id = :id');
        return $stmt->execute([
            ':nome' => $dados['nome'],
            ':categoria_id' => $dados['categoria_id'],
            ':codigo_interno' => $dados['codigo_interno'],
            ':unidade_medida' => $dados['unidade_medida'],
            ':estoque_atual' => $dados['estoque_atual'],
            ':estoque_minimo' => $dados['estoque_minimo'],
            ':localizacao' => $dados['localizacao'],
            ':observacoes' => $dados['observacoes'],
            ':imagem' => $dados['imagem'],
            ':id' => $id
        ]);
    }

    public function excluir($id)
    {
        $stmt = $this->db->prepare('DELETE FROM produtos WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function atualizarEstoque($id, $quantidade)
    {
        return $this->incrementarEstoque($id, $quantidade);
    }

    public function incrementarEstoque($id, $quantidade)
    {
        $stmt = $this->db->prepare('UPDATE produtos SET estoque_atual = estoque_atual + :quantidade, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            ':quantidade' => $quantidade,
            ':id' => $id
        ]);
        return $stmt->rowCount() > 0;
    }

    public function debitarEstoqueSemNegativo($id, $quantidade)
    {
        $stmt = $this->db->prepare(
            'UPDATE produtos
             SET estoque_atual = estoque_atual - :quantidade, updated_at = NOW()
             WHERE id = :id
               AND estoque_atual >= :quantidade'
        );
        $stmt->execute([
            ':quantidade' => $quantidade,
            ':id' => $id
        ]);
        return $stmt->rowCount() > 0;
    }
}
