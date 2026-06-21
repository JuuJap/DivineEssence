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
-- O INSERT abaixo evita duplicação caso o script seja executado novamente
-- ============================================================

INSERT INTO produtos 
(nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo)
SELECT
    'Orquídea Silvestre',
    'Fragrância floral sofisticada, inspirada na delicadeza das orquídeas e no frescor da natureza.',
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
    'Fragrância doce, marcante e delicada, com notas inspiradas em frutas vermelhas e baunilha.',
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
    'Perfume masculino intenso, misterioso e marcante, inspirado no brilho da noite.',
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

INSERT INTO produtos 
(nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo)
SELECT
    'Little Twin Stars',
    'Perfume infantil doce e encantador, perfeito para momentos especiais.',
    169.90,
    199.90,
    'img/perfume4.png',
    'Infantis',
    25,
    32,
    1
WHERE NOT EXISTS (
    SELECT 1 FROM produtos WHERE nome = 'Little Twin Stars'
);

INSERT INTO produtos 
(nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo)
SELECT
    'Little Blossom',
    'Perfume infantil floral, delicado e divertido, inspirado em sonhos e magia.',
    149.90,
    179.90,
    'img/perfume5.png',
    'Infantis',
    18,
    62,
    1
WHERE NOT EXISTS (
    SELECT 1 FROM produtos WHERE nome = 'Little Blossom'
);

INSERT INTO produtos 
(nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo)
SELECT
    'Essencial Kids',
    'Colônia infantil suave, alegre e perfeita para o uso diário.',
    159.90,
    189.90,
    'img/perfume6.png',
    'Infantis',
    22,
    82,
    1
WHERE NOT EXISTS (
    SELECT 1 FROM produtos WHERE nome = 'Essencial Kids'
);

INSERT INTO produtos 
(nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo)
SELECT
    'Orquídea Glow',
    'Minikit de beleza com inspiração floral, brilho delicado e toque sofisticado.',
    109.90,
    139.90,
    'img/perfume7.png',
    'Femininos',
    12,
    42,
    1
WHERE NOT EXISTS (
    SELECT 1 FROM produtos WHERE nome = 'Orquídea Glow'
);

INSERT INTO produtos 
(nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo)
SELECT
    'Lunar Glow',
    'Minikit de maquiagem com brilho lunar, elegante e moderno.',
    119.90,
    149.90,
    'img/perfume8.png',
    'Masculinos',
    10,
    22,
    1
WHERE NOT EXISTS (
    SELECT 1 FROM produtos WHERE nome = 'Lunar Glow'
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