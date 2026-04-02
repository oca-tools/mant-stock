-- Script SQL completo do sistema de estoque da manutencao
CREATE DATABASE IF NOT EXISTS estoque_manutencao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE estoque_manutencao;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('Administrador', 'Almoxarifado', 'Consulta') NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    INDEX idx_usuarios_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    categoria_id INT NULL,
    codigo_interno VARCHAR(80) NULL,
    unidade_medida VARCHAR(20) NULL,
    estoque_atual DECIMAL(12,2) NOT NULL DEFAULT 0,
    estoque_minimo DECIMAL(12,2) NOT NULL DEFAULT 0,
    localizacao VARCHAR(120) NULL,
    observacoes TEXT NULL,
    imagem VARCHAR(200) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_produtos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    tipo_movimentacao ENUM('entrada','saida','descarte','ajuste') NOT NULL,
    quantidade DECIMAL(12,2) NOT NULL,
    usuario_id INT NOT NULL,
    origem VARCHAR(150) NULL,
    destino VARCHAR(150) NULL,
    observacoes VARCHAR(255) NULL,
    data_movimentacao DATETIME NOT NULL,
    CONSTRAINT fk_mov_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_mov_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS entradas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    quantidade DECIMAL(12,2) NOT NULL,
    fornecedor VARCHAR(150) NULL,
    nota_fiscal VARCHAR(80) NULL,
    usuario_id INT NOT NULL,
    data_entrada DATETIME NOT NULL,
    observacoes VARCHAR(255) NULL,
    CONSTRAINT fk_ent_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_ent_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS saidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    quantidade DECIMAL(12,2) NOT NULL,
    setor VARCHAR(120) NULL,
    local_utilizacao VARCHAR(120) NULL,
    tecnico_responsavel VARCHAR(120) NULL,
    usuario_id INT NOT NULL,
    data_saida DATETIME NOT NULL,
    observacoes VARCHAR(255) NULL,
    CONSTRAINT fk_sai_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_sai_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS descartes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    quantidade DECIMAL(12,2) NOT NULL,
    motivo_descarte VARCHAR(150) NULL,
    item_recebido_troca VARCHAR(150) NULL,
    usuario_id INT NOT NULL,
    data_descarte DATETIME NOT NULL,
    observacoes VARCHAR(255) NULL,
    CONSTRAINT fk_des_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_des_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ferramentas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    descricao VARCHAR(255) NULL,
    status ENUM('Disponivel','Emprestada','Em manutencao') NOT NULL DEFAULT 'Disponivel',
    usuario_cadastro_id INT NULL,
    CONSTRAINT fk_ferramentas_usuario_cadastro FOREIGN KEY (usuario_cadastro_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS emprestimos_ferramentas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ferramenta_id INT NOT NULL,
    usuario_responsavel VARCHAR(120) NOT NULL,
    usuario_executor_id INT NULL,
    usuario_devolucao_id INT NULL,
    data_retirada DATETIME NOT NULL,
    data_devolucao DATETIME NULL,
    status VARCHAR(30) NOT NULL,
    CONSTRAINT fk_emp_ferramenta FOREIGN KEY (ferramenta_id) REFERENCES ferramentas(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_emp_usuario_executor FOREIGN KEY (usuario_executor_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_emp_usuario_devolucao FOREIGN KEY (usuario_devolucao_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

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

CREATE TABLE IF NOT EXISTS inventarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inventario_mensal_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade_sistema DECIMAL(12,2) NOT NULL,
    quantidade_real DECIMAL(12,2) NOT NULL,
    diferenca DECIMAL(12,2) NOT NULL,
    usuario_id INT NOT NULL,
    motivo_ajuste VARCHAR(180) NULL,
    ajuste_aplicado TINYINT(1) NOT NULL DEFAULT 0,
    data_ajuste DATETIME NULL,
    data_inventario DATETIME NOT NULL,
    UNIQUE KEY uq_inventario_mes_produto (inventario_mensal_id, produto_id),
    CONSTRAINT fk_inv_mensal FOREIGN KEY (inventario_mensal_id) REFERENCES inventarios_mensais(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_inv_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_inv_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    acao VARCHAR(60) NOT NULL,
    entidade VARCHAR(80) NULL,
    entidade_id INT NULL,
    descricao VARCHAR(255) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    dados_antes JSON NULL,
    dados_depois JSON NULL,
    data_log DATETIME NOT NULL,
    CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

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

-- Seeds basicos de categorias
INSERT INTO categorias (nome, descricao) VALUES
('Eletrica', 'Materiais eletricos'),
('Hidraulica', 'Materiais hidraulicos'),
('Piscina', 'Itens para piscina'),
('Ar-condicionado', 'Manutencao de ar-condicionado'),
('Pintura', 'Tintas e acessorios'),
('Ferramentas', 'Ferramentas em geral'),
('EPIs', 'Equipamentos de protecao individual'),
('Marcenaria', 'Materiais de marcenaria');
-- Usuario administrador: use o seed em database/seeds/seed_usuarios.php
