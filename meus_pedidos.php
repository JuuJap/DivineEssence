<?php
require_once "proteger.php";
require_once "conexao.php";

$usuarioId = $_SESSION["usuario_id"];

$stmt = $conn->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY criado_em DESC");
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$pedidos = $stmt->get_result();
$qtdCarrinho = array_sum($_SESSION["carrinho"] ?? []);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus pedidos | Divine Essence</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="ecommerce.css?v=1">
</head>
<body>

<header class="topo">
    <div class="logo">
        <a href="index.php"><img id="logoSite" src="img/LDE.png" alt="Divine Essence"></a>
    </div>

    <a href="carrinho.php" class="carrinho">
        🛒
        <span><?= $qtdCarrinho ?></span>
    </a>

    <div class="usuario-area">
        <span class="usuario-nome">Olá, <?= htmlspecialchars($_SESSION["usuario_nome"]) ?></span>
        <a href="logout.php" class="btn-sair">Sair</a>
    </div>

    <div class="tema">
        <button id="btnTema">🌙</button>
    </div>
</header>

<nav class="menu">
    <a href="index.php">Início</a>
    <a href="carrinho.php">Carrinho</a>
</nav>

<main class="pagina-loja">
    <h1 class="titulo-pagina">Meus pedidos</h1>

    <div class="box-loja">
        <?php if ($pedidos->num_rows === 0): ?>
            <p>Você ainda não fez nenhum pedido.</p>
        <?php else: ?>
            <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                <div class="pedido-card">
                    <h3>Pedido #<?= $pedido["id"] ?></h3>
                    <p>Status: <?= htmlspecialchars($pedido["status"]) ?></p>
                    <p>Total: R$ <?= number_format($pedido["total"], 2, ",", ".") ?></p>
                    <p>Data: <?= date("d/m/Y H:i", strtotime($pedido["criado_em"])) ?></p>
                    <a href="detalhe_pedido.php?id=<?= $pedido["id"] ?>" class="btn-secundario">Ver detalhes</a>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</main>

<script src="script.js?v=4"></script>
</body>
</html>