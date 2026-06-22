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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus pedidos | Divine Essence</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="ecommerce.css?v=9">

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

    <form class="barra-pesquisa" method="GET" action="index.php">
        <input type="text" name="busca" placeholder="Buscar produto">
        <button type="submit" style="border:none;background:none;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>

    <a href="carrinho.php" class="carrinho">
        <i class="fa-solid fa-cart-shopping"></i>
        <span><?= $qtdCarrinho ?></span>
    </a>

    <div class="usuario-area">
        <span class="usuario-nome">
            Olá, <?= htmlspecialchars($_SESSION["usuario_nome"]) ?>
        </span>

        <?php if(isset($_SESSION["usuario_tipo"]) && $_SESSION["usuario_tipo"] === "admin"): ?>
            <a href="admin_produtos.php" class="btn-entrar">Admin</a>
        <?php endif; ?>

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
    <a href="index.php?categoria=Femininos">Femininos</a>
    <a href="index.php?categoria=Masculinos">Masculinos</a>
    <a href="index.php?categoria=Kits">Kits</a>
    <a href="carrinho.php">Carrinho</a>
</nav>

<main class="pagina-loja">
    <h1 class="titulo-pagina">Meus pedidos</h1>

    <div class="box-loja">
        <?php if ($pedidos->num_rows === 0): ?>
            <p>Você ainda não fez nenhum pedido.</p>
            <a href="index.php" class="btn-loja">Voltar para a loja</a>
        <?php else: ?>

            <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                <div class="pedido-card">
                    <h3>Pedido #<?= $pedido["id"] ?></h3>

                    <p>
                        <strong>Status:</strong>
                        <?= htmlspecialchars($pedido["status"]) ?>
                    </p>

                    <p>
                        <strong>Total:</strong>
                        R$ <?= number_format($pedido["total"], 2, ",", ".") ?>
                    </p>

                    <p>
                        <strong>Data:</strong>
                        <?= date("d/m/Y H:i", strtotime($pedido["criado_em"])) ?>
                    </p>

                    <a href="detalhe_pedido.php?id=<?= $pedido["id"] ?>" class="btn-secundario">
                        Ver detalhes
                    </a>
                </div>
            <?php endwhile; ?>

        <?php endif; ?>
    </div>
</main>

<script src="script.js?v=4"></script>

</body>
</html>