<?php
require_once "proteger.php";

$id = isset($_POST["produto_id"]) ? intval($_POST["produto_id"]) : 0;
$acao = $_POST["acao"] ?? "";

if ($id > 0 && isset($_SESSION["carrinho"][$id])) {
    if ($acao === "mais") {
        $_SESSION["carrinho"][$id]++;
    }

    if ($acao === "menos") {
        $_SESSION["carrinho"][$id]--;

        if ($_SESSION["carrinho"][$id] <= 0) {
            unset($_SESSION["carrinho"][$id]);
        }
    }
}

header("Location: carrinho.php");
exit;