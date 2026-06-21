<?php
require_once "proteger.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$qtdCarrinho = array_sum($_SESSION["carrinho"] ?? []);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedido confirmado | Divine Essence</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="ecommerce.css?v=1">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >
</head>
<body>

<header class="topo">
    <div class="logo">
        <a href="index.php">
            <img id="logoSite" src="img/LDE.png" alt="Divine Essence">
        </a>
    </div>

    <a href="carrinho.php" class="carrinho">
        <i class="fa-solid fa-cart-shopping"></i>
        <span><?= $qtdCarrinho ?></span>
    </a>

    <div class="usuario-area">
        <span class="usuario-nome">
            Olá, <?= htmlspecialchars($_SESSION["usuario_nome"]) ?>
        </span>

        <a href="meus_pedidos.php" class="btn-entrar">Pedidos</a>
        <a href="logout.php" class="btn-sair">Sair</a>
    </div>

    <div class="tema">
        <button id="btnTema" type="button">
            <i class="fa-solid fa-moon"></i>
        </button>
    </div>
</header>

<nav class="menu">
    <a href="index.php">Início</a>
    <a href="carrinho.php">Carrinho</a>
    <a href="meus_pedidos.php">Meus pedidos</a>
</nav>

<main class="pagina-loja">
    <div class="box-loja sucesso">
        <h1>Pedido confirmado!</h1>

        <p>Seu pedido foi registrado com sucesso.</p>

        <a href="detalhe_pedido.php?id=<?= $id ?>" class="btn-loja">
            Ver pedido
        </a>

        <a href="index.php" class="btn-secundario">
            Voltar para a loja
        </a>
    </div>
</main>

<script src="script.js?v=4"></script>
</body>
</html>