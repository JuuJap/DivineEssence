# Divine Essence

**Divine Essence** é uma loja virtual fictícia de perfumes desenvolvida para fins acadêmicos.
O projeto simula um e-commerce com cadastro de usuários, login, catálogo de produtos, carrinho de compras, checkout, histórico de pedidos, tema claro/escuro e painel administrativo.

---

## Autores

- [Julio Aparecido](https://github.com/JuuJap)
- [Julio Cesar](https://github.com/CesarNSR)
- [Lucca Cruz](https://github.com/Grey-90)
- [Andrew Henrique](https://github.com/AndrewKinynubis)

---

## Tecnologias Utilizadas

* HTML5
* CSS3
* JavaScript
* PHP
* MySQL
* Bootstrap
* Font Awesome
* XAMPP
* phpMyAdmin

---

## Funcionalidades

### Usuário

* Cadastro de conta
* Login e logout
* Senhas criptografadas
* Sessão de usuário
* Visualização de produtos
* Adição de produtos ao carrinho
* Finalização de compras
* Histórico de pedidos

### Loja

* Catálogo de perfumes
* Página individual de produto
* Filtro por categoria
* Barra de pesquisa
* Carrinho de compras
* Checkout
* Tema claro e escuro
* Layout responsivo

### Administrador

* Acesso a painel administrativo
* Cadastro de novos produtos
* Upload de imagem do produto
* Edição de produtos
* Remoção de produtos da loja
* Reativação de produtos removidos

---

## Estrutura Principal

```text
DivineEssence/
│
├── img/
├── index.php
├── produto.php
├── carrinho.php
├── checkout.php
├── meus_pedidos.php
├── detalhe_pedido.php
├── admin_produtos.php
├── entrar.php
├── cadastro.php
├── conexao.php
├── script.js
├── style.css
├── ecommerce.css
├── database.sql
└── README.md
```

---

## Como Instalar

### 1. Baixar o projeto

Baixe o projeto pelo GitHub ou clone o repositório:

```bash
git clone URL_DO_REPOSITORIO
```

---

### 2. Colocar no XAMPP

Copie a pasta do projeto para:

```text
C:\xampp\htdocs\
```

A pasta deve ficar assim:

```text
C:\xampp\htdocs\DivineEssence
```

---

### 3. Iniciar o XAMPP

Abra o XAMPP e inicie:

```text
Apache
MySQL
```

---

### 4. Importar o banco de dados

Abra o phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Depois importe o arquivo:

```text
database.sql
```

Esse arquivo cria o banco `divine_essence`, as tabelas necessárias e os produtos iniciais.

---

### 5. Conferir a conexão

O arquivo `conexao.php` deve estar configurado assim:

```php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "divine_essence";
```

Caso seu MySQL tenha senha, altere a variável `$senha`.

---

## Como Executar

Depois de configurar o banco e iniciar o XAMPP, acesse:

```text
http://localhost/DivineEssence/
```

ou:

```text
http://localhost/DivineEssence/index.php
```

---

## Como Criar um Administrador

Primeiro, cadastre um usuário normalmente pelo site:

```text
http://localhost/DivineEssence/cadastro.php
```

Depois, no phpMyAdmin, execute:

```sql
USE divine_essence;

UPDATE usuarios
SET tipo = 'admin'
WHERE email = 'SEU_EMAIL_AQUI';
```

Após isso, faça logout e login novamente.
O botão **Admin** aparecerá no site.

---

## Páginas Principais

| Página               | Função                 |
| -------------------- | ---------------------- |
| `index.php`          | Página inicial da loja |
| `produto.php`        | Detalhes de um produto |
| `carrinho.php`       | Carrinho de compras    |
| `checkout.php`       | Finalização da compra  |
| `meus_pedidos.php`   | Histórico de pedidos   |
| `admin_produtos.php` | Painel administrativo  |
| `entrar.php`         | Login                  |
| `cadastro.php`       | Cadastro de usuário    |

---

## Painel Administrativo

O painel administrativo permite que o usuário admin gerencie os produtos pelo próprio site.

No painel é possível:

* adicionar produto;
* enviar imagem;
* editar informações;
* remover produto da loja;
* reativar produto removido.

Produtos removidos não são apagados do banco.
Eles apenas deixam de aparecer na loja.

---

## Observações

* O banco de dados usado é `divine_essence`.
* O arquivo `database.sql` deve ser importado antes de usar o site.
* As imagens principais ficam na pasta `img/`.
* Produtos cadastrados pelo painel podem ser salvos em `img/produtos/`.
* Para acessar áreas protegidas, o usuário precisa estar logado.
* Para acessar o painel administrativo, o usuário precisa ter `tipo = 'admin'`.

---

## Status do Projeto

Projeto concluído para fins acadêmicos, com funcionalidades principais de um e-commerce:

* login;
* cadastro;
* catálogo;
* carrinho;
* checkout;
* pedidos;
* painel administrativo;
* tema claro/escuro.
