<?php
require_once "proteger.php";
require_once "conexao.php";

$carrinho = $_SESSION["carrinho"] ?? [];
$qtdCarrinho = array_sum($carrinho);
$itens = [];
$total = 0;

if (!empty($carrinho)) {
    $ids = array_map("intval", array_keys($carrinho));
    $sql = "SELECT * FROM produtos WHERE id IN (" . implode(",", $ids) . ")";
    $resultado = $conn->query($sql);

    while ($produto = $resultado->fetch_assoc()) {
        $produto["quantidade"] = $carrinho[$produto["id"]];
        $produto["subtotal"] = $produto["quantidade"] * $produto["preco"];
        $total += $produto["subtotal"];
        $itens[] = $produto;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho | Divine Essence</title>
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
    <a href="index.php?categoria=Masculinos">Masculinos</a>
</nav>

<main class="pagina-loja">
    <h1 class="titulo-pagina">Seu carrinho</h1>

    <?php if (empty($itens)): ?>
        <div class="box-loja">
            <h2>Seu carrinho está vazio</h2>
            <p>Adicione algum perfume para continuar.</p>
            <a href="index.php" class="btn-loja">Voltar para a loja</a>
        </div>
    <?php else: ?>
        <div class="grid-carrinho">
            <section class="box-loja">
                <?php foreach ($itens as $item): ?>
                    <div class="item-carrinho">
                        <img src="<?= htmlspecialchars($item["imagem"]) ?>" alt="<?= htmlspecialchars($item["nome"]) ?>">

                        <div>
                            <h3><?= htmlspecialchars($item["nome"]) ?></h3>
                            <p><?= htmlspecialchars($item["categoria"]) ?></p>
                            <p class="preco">R$ <?= number_format($item["preco"], 2, ",", ".") ?></p>

                            <div class="qtd-controle">
                                <form action="atualizar_carrinho.php" method="POST">
                                    <input type="hidden" name="produto_id" value="<?= $item["id"] ?>">
                                    <input type="hidden" name="acao" value="menos">
                                    <button type="submit">−</button>
                                </form>

                                <strong><?= $item["quantidade"] ?></strong>

                                <form action="atualizar_carrinho.php" method="POST">
                                    <input type="hidden" name="produto_id" value="<?= $item["id"] ?>">
                                    <input type="hidden" name="acao" value="mais">
                                    <button type="submit">+</button>
                                </form>
                            </div>
                        </div>

                        <div class="subtotal">
                            <strong>R$ <?= number_format($item["subtotal"], 2, ",", ".") ?></strong>
                            <a href="remover_carrinho.php?id=<?= $item["id"] ?>">Remover</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <aside class="box-loja resumo-pedido">
                <h2>Resumo</h2>
                <p>Itens: <strong><?= $qtdCarrinho ?></strong></p>
                <p>Frete: <strong>Grátis</strong></p>
                <h3>Total: R$ <?= number_format($total, 2, ",", ".") ?></h3>

                <a href="checkout.php" class="btn-loja">Finalizar compra</a>
                <a href="index.php" class="btn-secundario">Continuar comprando</a>
            </aside>
        </div>
    <?php endif; ?>
</main>

<script src="script.js?v=4"></script>
</body>
</html>