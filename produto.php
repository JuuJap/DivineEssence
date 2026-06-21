<?php
require_once "proteger.php";
require_once "conexao.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Produto não encontrado.");
}

$produto = $resultado->fetch_assoc();
$qtdCarrinho = array_sum($_SESSION["carrinho"] ?? []);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($produto["nome"]) ?> | Divine Essence</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="ecommerce.css?v=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<header class="topo">
    <div class="logo">
        <a href="index.php"><img id="logoSite" src="img/LDE.png" alt="Divine Essence"></a>
    </div>

    <div class="barra-pesquisa">
        <input type="text" placeholder="Buscar produto">
        <i class="fa-solid fa-magnifying-glass"></i>
    </div>

    <a href="carrinho.php" class="carrinho">
        <i class="fa-solid fa-cart-shopping"></i>
        <span><?= $qtdCarrinho ?></span>
    </a>

    <div class="usuario-area">
        <span class="usuario-nome">Olá, <?= htmlspecialchars($_SESSION["usuario_nome"]) ?></span>
        <a href="meus_pedidos.php" class="btn-entrar">Pedidos</a>
        <a href="logout.php" class="btn-sair">Sair</a>
    </div>

    <div class="tema">
        <button id="btnTema"><i class="fa-solid fa-moon"></i></button>
    </div>
</header>

<nav class="menu">
    <a href="index.php">Início</a>
    <a href="index.php?categoria=Femininos">Femininos</a>
    <a href="index.php?categoria=Infantis">Infantis</a>
    <a href="index.php?categoria=Masculinos">Masculinos</a>
</nav>

<main class="pagina-loja">
    <section class="produto-detalhe">
        <div class="produto-foto">
            <img src="<?= htmlspecialchars($produto["imagem"]) ?>" alt="<?= htmlspecialchars($produto["nome"]) ?>">
        </div>

        <div class="produto-info">
            <p class="categoria-produto"><?= htmlspecialchars($produto["categoria"]) ?></p>

            <h1><?= htmlspecialchars($produto["nome"]) ?></h1>

            <div class="avaliacao">
                ★★★★★ <span>(<?= (int)$produto["avaliacao_qtd"] ?>)</span>
            </div>

            <p><?= htmlspecialchars($produto["descricao"]) ?></p>

            <div class="precos-produto">
                <strong>R$ <?= number_format($produto["preco"], 2, ",", ".") ?></strong>

                <?php if (!empty($produto["preco_antigo"])): ?>
                    <span>R$ <?= number_format($produto["preco_antigo"], 2, ",", ".") ?></span>
                <?php endif; ?>
            </div>

            <p>ou 6x de R$ <?= number_format($produto["preco"] / 6, 2, ",", ".") ?> sem juros</p>
            <p>Estoque: <?= (int)$produto["estoque"] ?> unidades</p>

            <form action="adicionar_carrinho.php" method="POST" class="form-compra">
                <input type="hidden" name="produto_id" value="<?= $produto["id"] ?>">

                <label>Quantidade</label>
                <input type="number" name="quantidade" value="1" min="1" max="<?= (int)$produto["estoque"] ?>">

                <button type="submit" name="acao" value="comprar" class="btn-loja">Comprar agora</button>
                <button type="submit" name="acao" value="carrinho" class="btn-secundario">Adicionar ao carrinho</button>
            </form>
        </div>
    </section>
</main>

<script src="script.js?v=4"></script>
</body>
</html>