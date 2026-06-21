<?php
require_once "proteger.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($id > 0 && isset($_SESSION["carrinho"][$id])) {
    unset($_SESSION["carrinho"][$id]);
}

header("Location: carrinho.php");
exit;