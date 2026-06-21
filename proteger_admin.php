<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: entrar.php");
    exit;
}

$usuarioId = $_SESSION["usuario_id"];

$sql = "SELECT tipo FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioId);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows !== 1) {
    header("Location: entrar.php");
    exit;
}

$usuario = $resultado->fetch_assoc();

if ($usuario["tipo"] !== "admin") {
    header("Location: index.php");
    exit;
}
?>