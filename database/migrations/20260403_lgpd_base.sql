-- Migracao base de adequacao LGPD
-- Compatibilidade com MySQL/MariaDB sem "ADD COLUMN IF NOT EXISTS" no ALTER TABLE.

START TRANSACTION;

SET @col_lgpd_aceite_at := (
    SELECT COUNT(1)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'usuarios'
      AND COLUMN_NAME = 'lgpd_aceite_at'
);
SET @sql_col_lgpd_aceite_at := IF(
    @col_lgpd_aceite_at > 0,
    'SELECT 1',
    'ALTER TABLE usuarios ADD COLUMN lgpd_aceite_at DATETIME NULL AFTER created_at'
);
PREPARE stmt_col_lgpd_aceite_at FROM @sql_col_lgpd_aceite_at;
EXECUTE stmt_col_lgpd_aceite_at;
DEALLOCATE PREPARE stmt_col_lgpd_aceite_at;

SET @col_lgpd_aceite_ip := (
    SELECT COUNT(1)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'usuarios'
      AND COLUMN_NAME = 'lgpd_aceite_ip'
);
SET @sql_col_lgpd_aceite_ip := IF(
    @col_lgpd_aceite_ip > 0,
    'SELECT 1',
    'ALTER TABLE usuarios ADD COLUMN lgpd_aceite_ip VARCHAR(45) NULL AFTER lgpd_aceite_at'
);
PREPARE stmt_col_lgpd_aceite_ip FROM @sql_col_lgpd_aceite_ip;
EXECUTE stmt_col_lgpd_aceite_ip;
DEALLOCATE PREPARE stmt_col_lgpd_aceite_ip;

SET @col_lgpd_aceite_versao := (
    SELECT COUNT(1)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'usuarios'
      AND COLUMN_NAME = 'lgpd_aceite_versao'
);
SET @sql_col_lgpd_aceite_versao := IF(
    @col_lgpd_aceite_versao > 0,
    'SELECT 1',
    'ALTER TABLE usuarios ADD COLUMN lgpd_aceite_versao VARCHAR(30) NULL AFTER lgpd_aceite_ip'
);
PREPARE stmt_col_lgpd_aceite_versao FROM @sql_col_lgpd_aceite_versao;
EXECUTE stmt_col_lgpd_aceite_versao;
DEALLOCATE PREPARE stmt_col_lgpd_aceite_versao;

CREATE TABLE IF NOT EXISTS solicitacoes_lgpd (
    id INT AUTO_INCREMENT PRIMARY KEY,
    protocolo VARCHAR(40) NOT NULL UNIQUE,
    titular_nome VARCHAR(120) NOT NULL,
    titular_email VARCHAR(120) NOT NULL,
    tipo_solicitacao ENUM('acesso','correcao','anonimizacao','eliminacao','portabilidade','oposicao','revogacao') NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('aberta','em_analise','concluida','indeferida') NOT NULL DEFAULT 'aberta',
    resposta TEXT NULL,
    usuario_abertura_id INT NOT NULL,
    usuario_responsavel_id INT NULL,
    data_abertura DATETIME NOT NULL,
    data_atualizacao DATETIME NOT NULL,
    data_conclusao DATETIME NULL,
    CONSTRAINT fk_sol_lgpd_abertura FOREIGN KEY (usuario_abertura_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_sol_lgpd_responsavel FOREIGN KEY (usuario_responsavel_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_sol_lgpd_email (titular_email),
    INDEX idx_sol_lgpd_status (status),
    INDEX idx_sol_lgpd_data_abertura (data_abertura)
) ENGINE=InnoDB;

COMMIT;
