-- ============================================================
-- Banco de dados do projeto Divine Essence
-- Projeto acadêmico de e-commerce de perfumes
-- ============================================================

CREATE DATABASE IF NOT EXISTS divine_essence
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE divine_essence;

-- ============================================================
-- Tabela de usuários
-- ============================================================

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo VARCHAR(20) NOT NULL DEFAULT 'cliente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Tabela de produtos
-- ============================================================

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    preco_antigo DECIMAL(10,2) DEFAULT NULL,
    imagem VARCHAR(255) NOT NULL,
    categoria VARCHAR(80) NOT NULL,
    estoque INT DEFAULT 0,
    avaliacao_qtd INT DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Tabela de pedidos
-- ============================================================

CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nome_entrega VARCHAR(150) NOT NULL,
    email_entrega VARCHAR(150) NOT NULL,
    endereco TEXT NOT NULL,
    pagamento VARCHAR(50) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Confirmado',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- ============================================================
-- Tabela de itens dos pedidos
-- ============================================================

CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    nome_produto VARCHAR(150) NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    quantidade INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- ============================================================
-- Produtos iniciais
-- A loja trabalha apenas com perfumes adultos femininos e masculinos.
-- O UPDATE abaixo remove da vitrine produtos antigos fora do catálogo atual.
-- ============================================================

UPDATE produtos
SET ativo = 0
WHERE categoria NOT IN ('Femininos', 'Masculinos')
   OR id BETWEEN 4 AND 8;

INSERT INTO produtos 
(nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo)
SELECT
    'Orquídea Silvestre',
    'Perfume feminino adulto com fragrância floral sofisticada, inspirado na delicadeza das orquídeas e no frescor da natureza.',
    149.90,
    189.90,
    'img/perfume2.png',
    'Femininos',
    20,
    170,
    1
WHERE NOT EXISTS (
    SELECT 1 FROM produtos WHERE nome = 'Orquídea Silvestre'
);

INSERT INTO produtos 
(nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo)
SELECT
    'Strawberry Pavlova',
    'Perfume feminino adulto doce, marcante e delicado, com notas inspiradas em frutas vermelhas e baunilha.',
    60.99,
    79.99,
    'img/perfume1.png',
    'Femininos',
    30,
    265,
    1
WHERE NOT EXISTS (
    SELECT 1 FROM produtos WHERE nome = 'Strawberry Pavlova'
);

INSERT INTO produtos 
(nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo)
SELECT
    'Lunar Energy',
    'Perfume masculino adulto intenso, misterioso e marcante, inspirado no brilho da noite.',
    170.80,
    210.00,
    'img/perfume3.png',
    'Masculinos',
    15,
    132,
    1
WHERE NOT EXISTS (
    SELECT 1 FROM produtos WHERE nome = 'Lunar Energy'
);

-- ============================================================
-- COMO TRANSFORMAR UM USUÁRIO EM ADMINISTRADOR
-- 
-- 1. Cadastre o usuário pelo site.
-- 2. Troque o e-mail abaixo pelo e-mail cadastrado.
-- 3. Execute o comando no phpMyAdmin.
-- ============================================================

-- UPDATE usuarios
-- SET tipo = 'admin'
-- WHERE email = 'SEU_EMAIL_AQUI';