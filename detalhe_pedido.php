<?php
require_once "proteger.php";
require_once "conexao.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$usuarioId = $_SESSION["usuario_id"];

$stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuarioId);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();

if (!$pedido) {
    die("Pedido não encontrado.");
}

$stmtItens = $conn->prepare("SELECT * FROM itens_pedido WHERE pedido_id = ?");
$stmtItens->bind_param("i", $id);
$stmtItens->execute();
$itens = $stmtItens->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedido #<?= $id ?> | Divine Essence</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="ecommerce.css?v=1">
</head>
<body>

<main class="pagina-loja">
    <div class="box-loja">
        <h1>Pedido #<?= $pedido["id"] ?></h1>

        <p>Status: <?= htmlspecialchars($pedido["status"]) ?></p>
        <p>Pagamento: <?= htmlspecialchars($pedido["pagamento"]) ?></p>
        <p>Total: R$ <?= number_format($pedido["total"], 2, ",", ".") ?></p>

        <h2>Itens</h2>

        <?php while ($item = $itens->fetch_assoc()): ?>
            <div class="item-carrinho">
                <img src="<?= htmlspecialchars($item["imagem"]) ?>" alt="<?= htmlspecialchars($item["nome_produto"]) ?>">

                <div>
                    <h3><?= htmlspecialchars($item["nome_produto"]) ?></h3>
                    <p>Quantidade: <?= $item["quantidade"] ?></p>
                    <p>Subtotal: R$ <?= number_format($item["subtotal"], 2, ",", ".") ?></p>
                </div>
            </div>
        <?php endwhile; ?>

        <a href="meus_pedidos.php" class="btn-secundario">Voltar</a>
    </div>
</main>

</body>
</html>