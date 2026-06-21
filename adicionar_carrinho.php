<?php
require_once "proteger.php";

$produtoId = isset($_POST["produto_id"]) ? intval($_POST["produto_id"]) : 0;
$quantidade = isset($_POST["quantidade"]) ? intval($_POST["quantidade"]) : 1;
$acao = $_POST["acao"] ?? "carrinho";

if ($produtoId <= 0) {
    header("Location: index.php");
    exit;
}

if ($quantidade <= 0) {
    $quantidade = 1;
}

if (!isset($_SESSION["carrinho"])) {
    $_SESSION["carrinho"] = [];
}

if (isset($_SESSION["carrinho"][$produtoId])) {
    $_SESSION["carrinho"][$produtoId] += $quantidade;
} else {
    $_SESSION["carrinho"][$produtoId] = $quantidade;
}

if ($acao === "comprar") {
    header("Location: checkout.php");
} else {
    header("Location: carrinho.php");
}

exit;