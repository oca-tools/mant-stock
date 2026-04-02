-- Migracao para habilitar cadastro por convite e permitir varias contas por e-mail
-- Execute uma unica vez no banco existente.

START TRANSACTION;

-- Remove indice unico do e-mail em usuarios (nome do indice pode variar).
SET @idx_unico_email := (
    SELECT INDEX_NAME
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'usuarios'
      AND COLUMN_NAME = 'email'
      AND NON_UNIQUE = 0
    LIMIT 1
);
SET @sql_drop_idx := IF(
    @idx_unico_email IS NULL,
    'SELECT 1',
    CONCAT('ALTER TABLE usuarios DROP INDEX ', @idx_unico_email)
);
PREPARE stmt_drop_idx FROM @sql_drop_idx;
EXECUTE stmt_drop_idx;
DEALLOCATE PREPARE stmt_drop_idx;

-- Garante indice comum por e-mail para consultas de login.
SET @idx_email_existe := (
    SELECT COUNT(1)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'usuarios'
      AND INDEX_NAME = 'idx_usuarios_email'
);
SET @sql_create_idx := IF(
    @idx_email_existe > 0,
    'SELECT 1',
    'CREATE INDEX idx_usuarios_email ON usuarios (email)'
);
PREPARE stmt_create_idx FROM @sql_create_idx;
EXECUTE stmt_create_idx;
DEALLOCATE PREPARE stmt_create_idx;

-- Tabela de convites para cadastro por e-mail.
CREATE TABLE IF NOT EXISTS convites_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(120) NOT NULL,
    nome_sugerido VARCHAR(120) NULL,
    tipo_usuario ENUM('Administrador', 'Almoxarifado', 'Consulta') NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    status ENUM('pendente', 'aceito', 'expirado', 'cancelado') NOT NULL DEFAULT 'pendente',
    usuario_convite_id INT NOT NULL,
    usuario_criado_id INT NULL,
    expira_em DATETIME NOT NULL,
    criado_em DATETIME NOT NULL,
    usado_em DATETIME NULL,
    CONSTRAINT fk_convites_usuario_convite FOREIGN KEY (usuario_convite_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_convites_usuario_criado FOREIGN KEY (usuario_criado_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_convites_email (email),
    INDEX idx_convites_status (status),
    INDEX idx_convites_expira (expira_em)
) ENGINE=InnoDB;

COMMIT;
