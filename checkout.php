<?php
require_once "proteger.php";
require_once "conexao.php";

$carrinho = $_SESSION["carrinho"] ?? [];

if (empty($carrinho)) {
    header("Location: carrinho.php");
    exit;
}

$ids = array_map("intval", array_keys($carrinho));
$sql = "SELECT * FROM produtos WHERE ativo = 1 AND id IN (" . implode(",", $ids) . ")";
$resultado = $conn->query($sql);

$itens = [];
$total = 0;

while ($produto = $resultado->fetch_assoc()) {
    $qtd = $carrinho[$produto["id"]];
    $produto["quantidade"] = $qtd;
    $produto["subtotal"] = $qtd * $produto["preco"];
    $total += $produto["subtotal"];
    $itens[] = $produto;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuarioId = $_SESSION["usuario_id"];
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $endereco = trim($_POST["endereco"]);
    $pagamento = trim($_POST["pagamento"]);

    if ($nome === "" || $email === "" || $endereco === "" || $pagamento === "") {
        die("Preencha todos os campos para finalizar o pedido.");
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            INSERT INTO pedidos 
            (usuario_id, nome_entrega, email_entrega, endereco, pagamento, total)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("issssd", $usuarioId, $nome, $email, $endereco, $pagamento, $total);
        $stmt->execute();

        $pedidoId = $conn->insert_id;

        $stmtItem = $conn->prepare("
            INSERT INTO itens_pedido 
            (pedido_id, produto_id, nome_produto, preco_unitario, quantidade, subtotal, imagem)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtEstoque = $conn->prepare("
            UPDATE produtos 
            SET estoque = estoque - ? 
            WHERE id = ? AND estoque >= ? AND ativo = 1
        ");

        foreach ($itens as $item) {
            $stmtEstoque->bind_param("iii", $item["quantidade"], $item["id"], $item["quantidade"]);
            $stmtEstoque->execute();

            if ($stmtEstoque->affected_rows === 0) {
                throw new Exception("Estoque insuficiente para o produto: " . $item["nome"]);
            }

            $stmtItem->bind_param(
                "iisdiis",
                $pedidoId,
                $item["id"],
                $item["nome"],
                $item["preco"],
                $item["quantidade"],
                $item["subtotal"],
                $item["imagem"]
            );

            $stmtItem->execute();
        }

        $conn->commit();

        unset($_SESSION["carrinho"]);

        header("Location: pedido_sucesso.php?id=" . $pedidoId);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        die("Erro ao finalizar pedido: " . $e->getMessage());
    }
}

$qtdCarrinho = array_sum($carrinho);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Divine Essence</title>
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
    <a href="meus_pedidos.php">Meus pedidos</a>
</nav>

<main class="pagina-loja">
    <div class="grid-checkout">
        <form method="POST" class="box-loja">
            <h1>Finalizar compra</h1>

            <label>Nome completo</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($_SESSION["usuario_nome"]) ?>" required>

            <label>E-mail</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_SESSION["usuario_email"] ?? "") ?>" required>

            <label>Endereço</label>
            <textarea name="endereco" required></textarea>

            <label>Pagamento</label>
            <select name="pagamento" required>
                <option value="">Selecione</option>
                <option value="Pix">Pix</option>
                <option value="Cartão de crédito">Cartão de crédito</option>
                <option value="Boleto">Boleto</option>
            </select>

            <button type="submit" class="btn-loja">Confirmar pedido</button>
        </form>

        <aside class="box-loja">
            <h2>Resumo do pedido</h2>

            <?php foreach ($itens as $item): ?>
                <div class="linha-resumo">
                    <span><?= htmlspecialchars($item["nome"]) ?> x<?= $item["quantidade"] ?></span>
                    <strong>R$ <?= number_format($item["subtotal"], 2, ",", ".") ?></strong>
                </div>
            <?php endforeach; ?>

            <h2>Total: R$ <?= number_format($total, 2, ",", ".") ?></h2>
        </aside>
    </div>
</main>

<script src="script.js?v=4"></script>
</body>
</html>