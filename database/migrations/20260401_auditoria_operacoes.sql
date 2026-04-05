-- Migracao para reforco de auditoria operacional
-- Execute uma unica vez no banco existente.

START TRANSACTION;

SET @col_ferr_ucad := (
    SELECT COUNT(1) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ferramentas' AND COLUMN_NAME = 'usuario_cadastro_id'
);
SET @sql_col_ferr_ucad := IF(
    @col_ferr_ucad > 0,
    'SELECT 1',
    'ALTER TABLE ferramentas ADD COLUMN usuario_cadastro_id INT NULL AFTER status'
);
PREPARE stmt_col_ferr_ucad FROM @sql_col_ferr_ucad;
EXECUTE stmt_col_ferr_ucad;
DEALLOCATE PREPARE stmt_col_ferr_ucad;

SET @col_emp_exec := (
    SELECT COUNT(1) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'emprestimos_ferramentas' AND COLUMN_NAME = 'usuario_executor_id'
);
SET @sql_col_emp_exec := IF(
    @col_emp_exec > 0,
    'SELECT 1',
    'ALTER TABLE emprestimos_ferramentas ADD COLUMN usuario_executor_id INT NULL AFTER usuario_responsavel'
);
PREPARE stmt_col_emp_exec FROM @sql_col_emp_exec;
EXECUTE stmt_col_emp_exec;
DEALLOCATE PREPARE stmt_col_emp_exec;

SET @col_emp_dev := (
    SELECT COUNT(1) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'emprestimos_ferramentas' AND COLUMN_NAME = 'usuario_devolucao_id'
);
SET @sql_col_emp_dev := IF(
    @col_emp_dev > 0,
    'SELECT 1',
    'ALTER TABLE emprestimos_ferramentas ADD COLUMN usuario_devolucao_id INT NULL AFTER usuario_executor_id'
);
PREPARE stmt_col_emp_dev FROM @sql_col_emp_dev;
EXECUTE stmt_col_emp_dev;
DEALLOCATE PREPARE stmt_col_emp_dev;

-- Cria indices quando nao existirem.
SET @idx_ferr_ucad := (
    SELECT COUNT(1)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ferramentas'
      AND INDEX_NAME = 'idx_ferramentas_usuario_cadastro'
);
SET @sql_idx_ferr_ucad := IF(
    @idx_ferr_ucad > 0,
    'SELECT 1',
    'CREATE INDEX idx_ferramentas_usuario_cadastro ON ferramentas (usuario_cadastro_id)'
);
PREPARE stmt_idx_ferr_ucad FROM @sql_idx_ferr_ucad;
EXECUTE stmt_idx_ferr_ucad;
DEALLOCATE PREPARE stmt_idx_ferr_ucad;

SET @idx_emp_exec := (
    SELECT COUNT(1)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'emprestimos_ferramentas'
      AND INDEX_NAME = 'idx_emp_usuario_executor'
);
SET @sql_idx_emp_exec := IF(
    @idx_emp_exec > 0,
    'SELECT 1',
    'CREATE INDEX idx_emp_usuario_executor ON emprestimos_ferramentas (usuario_executor_id)'
);
PREPARE stmt_idx_emp_exec FROM @sql_idx_emp_exec;
EXECUTE stmt_idx_emp_exec;
DEALLOCATE PREPARE stmt_idx_emp_exec;

SET @idx_emp_dev := (
    SELECT COUNT(1)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'emprestimos_ferramentas'
      AND INDEX_NAME = 'idx_emp_usuario_devolucao'
);
SET @sql_idx_emp_dev := IF(
    @idx_emp_dev > 0,
    'SELECT 1',
    'CREATE INDEX idx_emp_usuario_devolucao ON emprestimos_ferramentas (usuario_devolucao_id)'
);
PREPARE stmt_idx_emp_dev FROM @sql_idx_emp_dev;
EXECUTE stmt_idx_emp_dev;
DEALLOCATE PREPARE stmt_idx_emp_dev;

-- FKs condicionais para evitar erro em bases que ja receberam a migracao.
SET @fk_ferr_ucad := (
    SELECT COUNT(1)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ferramentas'
      AND CONSTRAINT_NAME = 'fk_ferramentas_usuario_cadastro'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql_fk_ferr_ucad := IF(
    @fk_ferr_ucad > 0,
    'SELECT 1',
    'ALTER TABLE ferramentas ADD CONSTRAINT fk_ferramentas_usuario_cadastro FOREIGN KEY (usuario_cadastro_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE SET NULL'
);
PREPARE stmt_fk_ferr_ucad FROM @sql_fk_ferr_ucad;
EXECUTE stmt_fk_ferr_ucad;
DEALLOCATE PREPARE stmt_fk_ferr_ucad;

SET @fk_emp_exec := (
    SELECT COUNT(1)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'emprestimos_ferramentas'
      AND CONSTRAINT_NAME = 'fk_emp_usuario_executor'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql_fk_emp_exec := IF(
    @fk_emp_exec > 0,
    'SELECT 1',
    'ALTER TABLE emprestimos_ferramentas ADD CONSTRAINT fk_emp_usuario_executor FOREIGN KEY (usuario_executor_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE SET NULL'
);
PREPARE stmt_fk_emp_exec FROM @sql_fk_emp_exec;
EXECUTE stmt_fk_emp_exec;
DEALLOCATE PREPARE stmt_fk_emp_exec;

SET @fk_emp_dev := (
    SELECT COUNT(1)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'emprestimos_ferramentas'
      AND CONSTRAINT_NAME = 'fk_emp_usuario_devolucao'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql_fk_emp_dev := IF(
    @fk_emp_dev > 0,
    'SELECT 1',
    'ALTER TABLE emprestimos_ferramentas ADD CONSTRAINT fk_emp_usuario_devolucao FOREIGN KEY (usuario_devolucao_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE SET NULL'
);
PREPARE stmt_fk_emp_dev FROM @sql_fk_emp_dev;
EXECUTE stmt_fk_emp_dev;
DEALLOCATE PREPARE stmt_fk_emp_dev;

COMMIT;
