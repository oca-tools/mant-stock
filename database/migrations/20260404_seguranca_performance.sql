-- Migracao de seguranca e performance (rate-limit de login + indices)
-- Compativel com MySQL/MariaDB sem "IF NOT EXISTS" em ALTER TABLE.

START TRANSACTION;

CREATE TABLE IF NOT EXISTS tentativas_login (
    chave CHAR(64) PRIMARY KEY,
    ip VARCHAR(45) NOT NULL,
    email VARCHAR(120) NOT NULL,
    tentativas INT NOT NULL DEFAULT 0,
    bloqueado_ate DATETIME NULL,
    ultimo_evento DATETIME NOT NULL,
    INDEX idx_tentativas_login_ultimo_evento (ultimo_evento),
    INDEX idx_tentativas_login_bloqueado (bloqueado_ate)
) ENGINE=InnoDB;

-- Indices de apoio para consultas de dashboard, relatorios e listagens.
SET @idx_prod_nome_codigo := (
    SELECT COUNT(1)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'produtos'
      AND INDEX_NAME = 'idx_produtos_nome_codigo'
);
SET @sql_idx_prod_nome_codigo := IF(
    @idx_prod_nome_codigo > 0,
    'SELECT 1',
    'CREATE INDEX idx_produtos_nome_codigo ON produtos (nome, codigo_interno)'
);
PREPARE stmt_idx_prod_nome_codigo FROM @sql_idx_prod_nome_codigo;
EXECUTE stmt_idx_prod_nome_codigo;
DEALLOCATE PREPARE stmt_idx_prod_nome_codigo;

SET @idx_mov_data_tipo_produto := (
    SELECT COUNT(1)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'movimentacoes'
      AND INDEX_NAME = 'idx_mov_data_tipo_produto'
);
SET @sql_idx_mov_data_tipo_produto := IF(
    @idx_mov_data_tipo_produto > 0,
    'SELECT 1',
    'CREATE INDEX idx_mov_data_tipo_produto ON movimentacoes (data_movimentacao, tipo_movimentacao, produto_id)'
);
PREPARE stmt_idx_mov_data_tipo_produto FROM @sql_idx_mov_data_tipo_produto;
EXECUTE stmt_idx_mov_data_tipo_produto;
DEALLOCATE PREPARE stmt_idx_mov_data_tipo_produto;

SET @idx_mov_produto_data := (
    SELECT COUNT(1)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'movimentacoes'
      AND INDEX_NAME = 'idx_mov_produto_data'
);
SET @sql_idx_mov_produto_data := IF(
    @idx_mov_produto_data > 0,
    'SELECT 1',
    'CREATE INDEX idx_mov_produto_data ON movimentacoes (produto_id, data_movimentacao)'
);
PREPARE stmt_idx_mov_produto_data FROM @sql_idx_mov_produto_data;
EXECUTE stmt_idx_mov_produto_data;
DEALLOCATE PREPARE stmt_idx_mov_produto_data;

SET @idx_ent_data_produto := (
    SELECT COUNT(1)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'entradas'
      AND INDEX_NAME = 'idx_entradas_data_produto'
);
SET @sql_idx_ent_data_produto := IF(
    @idx_ent_data_produto > 0,
    'SELECT 1',
    'CREATE INDEX idx_entradas_data_produto ON entradas (data_entrada, produto_id)'
);
PREPARE stmt_idx_ent_data_produto FROM @sql_idx_ent_data_produto;
EXECUTE stmt_idx_ent_data_produto;
DEALLOCATE PREPARE stmt_idx_ent_data_produto;

SET @idx_sai_data_produto := (
    SELECT COUNT(1)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'saidas'
      AND INDEX_NAME = 'idx_saidas_data_produto'
);
SET @sql_idx_sai_data_produto := IF(
    @idx_sai_data_produto > 0,
    'SELECT 1',
    'CREATE INDEX idx_saidas_data_produto ON saidas (data_saida, produto_id)'
);
PREPARE stmt_idx_sai_data_produto FROM @sql_idx_sai_data_produto;
EXECUTE stmt_idx_sai_data_produto;
DEALLOCATE PREPARE stmt_idx_sai_data_produto;

SET @idx_logs_data_acao := (
    SELECT COUNT(1)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'logs'
      AND INDEX_NAME = 'idx_logs_data_acao'
);
SET @sql_idx_logs_data_acao := IF(
    @idx_logs_data_acao > 0,
    'SELECT 1',
    'CREATE INDEX idx_logs_data_acao ON logs (data_log, acao)'
);
PREPARE stmt_idx_logs_data_acao FROM @sql_idx_logs_data_acao;
EXECUTE stmt_idx_logs_data_acao;
DEALLOCATE PREPARE stmt_idx_logs_data_acao;

COMMIT;
