-- Migracao para inventario mensal com fechamento de ciclo
-- Execute uma unica vez no banco existente.

START TRANSACTION;

CREATE TABLE IF NOT EXISTS inventarios_mensais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    competencia CHAR(7) NOT NULL UNIQUE,
    status ENUM('aberto','fechado') NOT NULL DEFAULT 'aberto',
    observacoes_abertura VARCHAR(180) NULL,
    observacoes_fechamento VARCHAR(180) NULL,
    usuario_abertura_id INT NOT NULL,
    usuario_fechamento_id INT NULL,
    data_abertura DATETIME NOT NULL,
    data_fechamento DATETIME NULL,
    CONSTRAINT fk_inv_mensal_usuario_abertura FOREIGN KEY (usuario_abertura_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_inv_mensal_usuario_fechamento FOREIGN KEY (usuario_fechamento_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

ALTER TABLE inventarios
    ADD COLUMN IF NOT EXISTS inventario_mensal_id INT NULL AFTER id,
    ADD COLUMN IF NOT EXISTS ajuste_aplicado TINYINT(1) NOT NULL DEFAULT 0 AFTER motivo_ajuste,
    ADD COLUMN IF NOT EXISTS data_ajuste DATETIME NULL AFTER ajuste_aplicado;

-- Se houver mais de uma contagem do mesmo produto no mesmo mes, mantem apenas a mais recente.
DELETE antigo
FROM inventarios antigo
INNER JOIN inventarios novo
    ON antigo.produto_id = novo.produto_id
   AND DATE_FORMAT(antigo.data_inventario, '%Y-%m') = DATE_FORMAT(novo.data_inventario, '%Y-%m')
   AND antigo.id < novo.id;

-- Cria ciclos mensais fechados para os dados ja existentes.
INSERT INTO inventarios_mensais (
    competencia,
    status,
    observacoes_abertura,
    observacoes_fechamento,
    usuario_abertura_id,
    usuario_fechamento_id,
    data_abertura,
    data_fechamento
)
SELECT base.competencia,
       'fechado',
       'Ciclo migrado automaticamente',
       'Fechado automaticamente na migracao',
       base.usuario_abertura_id,
       base.usuario_fechamento_id,
       base.data_abertura,
       base.data_fechamento
FROM (
    SELECT DATE_FORMAT(i.data_inventario, '%Y-%m') AS competencia,
           MIN(i.usuario_id) AS usuario_abertura_id,
           MAX(i.usuario_id) AS usuario_fechamento_id,
           MIN(i.data_inventario) AS data_abertura,
           MAX(i.data_inventario) AS data_fechamento
    FROM inventarios i
    GROUP BY DATE_FORMAT(i.data_inventario, '%Y-%m')
) AS base
LEFT JOIN inventarios_mensais im
    ON im.competencia = base.competencia
WHERE im.id IS NULL;

-- Vincula os registros antigos ao ciclo mensal correspondente.
UPDATE inventarios i
INNER JOIN inventarios_mensais im
    ON im.competencia = DATE_FORMAT(i.data_inventario, '%Y-%m')
SET i.inventario_mensal_id = im.id,
    i.ajuste_aplicado = 1,
    i.data_ajuste = COALESCE(i.data_ajuste, i.data_inventario)
WHERE i.inventario_mensal_id IS NULL;

ALTER TABLE inventarios
    MODIFY COLUMN inventario_mensal_id INT NOT NULL,
    ADD UNIQUE KEY uq_inventario_mes_produto (inventario_mensal_id, produto_id),
    ADD CONSTRAINT fk_inv_mensal FOREIGN KEY (inventario_mensal_id) REFERENCES inventarios_mensais(id) ON UPDATE CASCADE ON DELETE RESTRICT;

COMMIT;
