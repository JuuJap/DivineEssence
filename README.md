# Divine Essence

**Divine Essence** é uma loja virtual fictícia de perfumes desenvolvida para fins acadêmicos.  
O projeto simula um e-commerce com cadastro de usuários, login, catálogo de perfumes, kit especial, carrinho de compras, checkout, histórico de pedidos, tema claro/escuro e painel administrativo.

---

## Autores

- [Julio Aparecido](https://github.com/JuuJap)
- [Julio Cesar](https://github.com/CesarNSR)
- [Lucca Cruz](https://github.com/Grey-90)
- [Andrew Henrique](https://github.com/AndrewKinynubis)

---

## Tecnologias utilizadas

- HTML5
- CSS3
- JavaScript
- PHP
- MySQL
- Bootstrap
- Font Awesome
- XAMPP
- phpMyAdmin

---

## Funcionalidades

### Usuário

- Cadastro de conta
- Login e logout
- Senhas criptografadas
- Sessão de usuário
- Visualização dos perfumes e do kit disponível
- Adição de produtos ao carrinho
- Finalização de compras
- Histórico de pedidos

### Loja

- Catálogo com os produtos principais da Divine Essence
- Página individual de produto
- Filtro por categoria
- Barra de pesquisa
- Carrinho de compras
- Checkout
- Tema claro e escuro
- Layout responsivo

### Administrador

- Acesso ao painel administrativo
- Cadastro de perfumes ou kits
- Upload de imagem do item
- Edição de perfumes e kits
- Remoção de produtos da loja
- Reativação de produtos removidos

---

## Produtos iniciais do banco

Ao importar o arquivo `database.sql`, o sistema cria somente os seguintes produtos:

```text
Strawberry Pavlova
Orquidea Silvestre
Lunar Energy
Kit Trio Divine Essence
```

Nenhum perfume infantil, maquiagem ou produto extra deve aparecer na loja inicial.

Caso apareçam produtos antigos, importe novamente o arquivo `database.sql` atualizado pelo phpMyAdmin.

---

## Estrutura principal

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
├── cadastro_usuario.php
├── login_usuario.php
├── logout.php
├── proteger.php
├── proteger_admin.php
├── conexao.php
├── script.js
├── style.css
├── ecommerce.css
├── database.sql
└── README.md
```

---

## Passo a passo para instalar no XAMPP

### 1. Copiar o projeto para o XAMPP

Copie a pasta **DivineEssence** para dentro de:

```text
C:\xampp\htdocs\
```

O caminho final deve ficar assim:

```text
C:\xampp\htdocs\DivineEssence
```

---

### 2. Abrir o XAMPP

Abra o **XAMPP Control Panel** como administrador e inicie:

```text
Apache
MySQL
```

Se o MySQL não iniciar por causa da porta 3306, feche outro MySQL aberto ou altere a porta antes de continuar.

---

### 3. Importar o banco pelo phpMyAdmin

Acesse no navegador:

```text
http://localhost/phpmyadmin
```

Depois vá em:

```text
Importar
```

Selecione o arquivo:

```text
database.sql
```

Clique em **Executar**.

> Atenção: o arquivo `database.sql` apaga e recria o banco `divine_essence`. Se já existir algum dado importante, exporte antes de importar.

---

### 4. Conferir a conexão com o banco

Abra o arquivo:

```text
conexao.php
```

A configuração padrão deve estar assim:

```php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "divine_essence";
```

No XAMPP padrão, o usuário é `root` e a senha fica vazia.  
Se você colocou senha no MySQL, altere a variável `$senha`.

---

### 5. Acessar o projeto no navegador

Depois de importar o banco e iniciar o Apache/MySQL, acesse:

```text
http://localhost/DivineEssence/
```

ou:

```text
http://localhost/DivineEssence/index.php
```

---

## Como criar uma conta

Acesse:

```text
http://localhost/DivineEssence/cadastro.php
```

Preencha nome, e-mail e senha.  
Após cadastrar, o usuário poderá acessar a loja e realizar compras.

---

## Como transformar uma conta em administrador

Primeiro, cadastre uma conta normalmente pelo site.

Depois, no phpMyAdmin, clique no banco:

```text
divine_essence
```

Vá na aba **SQL** e execute:

```sql
UPDATE usuarios
SET tipo = 'admin'
WHERE email = 'SEU_EMAIL_AQUI';
```

Troque `SEU_EMAIL_AQUI` pelo e-mail usado no cadastro.

Depois faça logout e login novamente.  
O botão **Admin** aparecerá no topo do site.

---

## Banco de dados usado no projeto

O arquivo `database.sql` cria o banco:

```text
divine_essence
```

E cria as tabelas:

```text
usuarios
produtos
pedidos
itens_pedido
```

As colunas principais usadas pelo PHP são:

```text
usuarios: id, nome, email, senha, tipo, criado_em
produtos: id, nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo, criado_em
pedidos: id, usuario_id, nome_entrega, email_entrega, endereco, pagamento, total, status, criado_em
itens_pedido: id, pedido_id, produto_id, nome_produto, preco_unitario, quantidade, subtotal, imagem
```

---

## Produtos cadastrados pelo `database.sql`

A tabela `produtos` é recriada com apenas 4 registros iniciais:

| Produto | Categoria |
| --- | --- |
| Strawberry Pavlova | Perfumes |
| Orquidea Silvestre | Perfumes |
| Lunar Energy | Perfumes |
| Kit Trio Divine Essence | Kits |

Se o site mostrar produtos como perfume infantil, maquiagem ou outros itens antigos, o banco importado não é o atualizado. Nesse caso, importe novamente o arquivo `database.sql`.

---

## Páginas principais

| Página | Função |
| --- | --- |
| `index.php` | Página inicial da loja |
| `produto.php` | Detalhes de um produto |
| `carrinho.php` | Carrinho de compras |
| `checkout.php` | Finalização da compra |
| `meus_pedidos.php` | Histórico de pedidos |
| `detalhe_pedido.php` | Detalhes de um pedido |
| `admin_produtos.php` | Painel administrativo |
| `entrar.php` | Login |
| `cadastro.php` | Cadastro de usuário |

---

## Painel administrativo

O painel administrativo permite que o usuário admin gerencie perfumes e kits pelo próprio site.

No painel é possível:

- adicionar perfume ou kit;
- selecionar o tipo do item;
- enviar imagem;
- editar informações;
- remover item da loja;
- reativar item removido.

Produtos removidos não são apagados do banco.  
Eles apenas recebem `ativo = 0` e deixam de aparecer na loja.

---

## Possíveis erros e soluções

### Erro: Unknown column 'id'

Esse erro acontece quando o banco foi criado com colunas diferentes das que o PHP usa, por exemplo `id_usuario` ou `id_produto`.

Para corrigir, importe novamente o arquivo:

```text
database.sql
```

Ele recria o banco com as colunas corretas:

```text
usuarios.id
produtos.id
pedidos.id
itens_pedido.id
```

---

### Produtos antigos ainda aparecem na loja

Isso acontece quando o banco antigo ainda está sendo usado ou quando o `database.sql` antigo foi importado.

Para corrigir:

1. Abra o phpMyAdmin.
2. Vá em **Importar**.
3. Selecione o arquivo `database.sql` atualizado.
4. Clique em **Executar**.
5. Atualize a página da loja no navegador.

Depois disso, devem aparecer somente:

```text
Strawberry Pavlova
Orquidea Silvestre
Lunar Energy
Kit Trio Divine Essence
```

---

### Erro: MySQL shutdown unexpectedly

Esse erro geralmente acontece quando outro MySQL está usando a porta 3306.

No Prompt de Comando, verifique:

```cmd
netstat -ano | findstr :3306
```

Se aparecer outro processo usando a porta, finalize o processo ou pare o serviço MySQL no Windows antes de iniciar o MySQL do XAMPP.

---

## Observações

- O banco de dados usado é `divine_essence`.
- O arquivo `database.sql` deve ser importado antes de usar o site.
- O `database.sql` atualizado apaga e recria o banco.
- A loja inicial deve exibir somente os 3 perfumes principais e o kit.
- As imagens principais ficam na pasta `img/`.
- O painel admin usa a pasta `img/produtos/` para imagens enviadas pelo formulário.
