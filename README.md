# Divine Essence

Divine Essence é uma loja virtual fictícia de perfumes desenvolvida para fins acadêmicos, utilizando HTML, CSS, JavaScript, PHP e MySQL.

O projeto simula um e-commerce moderno, contendo sistema de autenticação de usuários, tema claro/escuro, catálogo de produtos e integração com banco de dados.

---

## Funcionalidades

### Sistema de Usuários

- Cadastro de usuários
- Login com autenticação
- Senhas criptografadas com `password_hash()`
- Sessões PHP
- Logout seguro
- Exibição do usuário logado

### Interface

- Tema Claro
- Tema Escuro
- Troca automática de logotipo conforme o tema
- Layout responsivo
- Menu adaptável para dispositivos móveis

### Loja Virtual

- Catálogo de perfumes
- Carrossel de banners
- Coleções destacadas
- Sistema de avaliação visual
- Barra de pesquisa
- Carrinho (estrutura preparada)

---

## Tecnologias Utilizadas

### Front-end

- HTML5
- CSS3
- JavaScript
- Bootstrap 5
- Font Awesome

### Back-end

- PHP

### Banco de Dados

- MySQL
- phpMyAdmin

### Ambiente de Desenvolvimento

- XAMPP

---

## Estrutura do Projeto

```text
DivineEssence/
│
├── img/
│
├── index.php
├── entrar.php
├── cadastro.php
│
├── conexao.php
├── cadastro_usuario.php
├── login_usuario.php
├── logout.php
├── proteger.php
│
├── script.js
├── style.css
│
└── README.md
```

---

## Configuração do Ambiente

### 1. Instalar o XAMPP

Baixe e instale o XAMPP:

https://www.apachefriends.org/

---

### 2. Mover o Projeto

Copie a pasta do projeto para:

```text
C:\xampp\htdocs\
```

Ficando:

```text
C:\xampp\htdocs\DivineEssence
```

---

### 3. Iniciar Serviços

Abra o XAMPP Control Panel e inicie:

- Apache
- MySQL

---

## Configuração do Banco de Dados

Abra:

```text
http://localhost/phpmyadmin
```

Clique em **SQL** e execute:

```sql
CREATE DATABASE divine_essence;

USE divine_essence;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Configuração da Conexão

Arquivo:

```text
conexao.php
```

```php
<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "divine_essence";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
```

---

## Executando o Projeto

Após iniciar Apache e MySQL:

```text
http://localhost/DivineEssence/
```

---

## Páginas Disponíveis

### Página Inicial

```text
http://localhost/DivineEssence/
```

ou

```text
http://localhost/DivineEssence/index.php
```

### Login

```text
http://localhost/DivineEssence/entrar.php
```

### Cadastro

```text
http://localhost/DivineEssence/cadastro.php
```

---

## Sistema de Segurança

O sistema utiliza:

### Criptografia de Senha

```php
password_hash()
```

### Validação

```php
password_verify()
```

### Sessões

```php
$_SESSION
```

### Proteção de Rotas

```php
require_once("proteger.php");
```

Impedindo que usuários não autenticados acessem páginas protegidas.

---

## Sistema de Temas

O projeto possui:

### Tema Claro

- Logo: `LDE2.png`

### Tema Escuro

- Logo: `LDE-dark2.png`

A preferência do usuário é salva utilizando:

```javascript
localStorage
```

---

## Autor

Julio Aparecido Barcelos Rodrigues

Projeto desenvolvido para fins acadêmicos com o objetivo de aplicar conceitos de:

- Desenvolvimento Web
- Front-end
- Back-end
- Banco de Dados
- Autenticação de Usuários
- Sessões PHP

---

## Status do Projeto

Concluído:

- Sistema de Login
- Sistema de Cadastro
- Banco de Dados
- Sessões PHP
- Tema Claro/Escuro
- Responsividade

Em desenvolvimento:

- Carrinho de Compras

Planejado:

- Painel Administrativo
- Integração Completa de Produtos
