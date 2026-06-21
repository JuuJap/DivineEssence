<?php

session_start();
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {

        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario["senha"])) {

$_SESSION["usuario_id"] = $usuario["id"];
$_SESSION["usuario_nome"] = $usuario["nome"];
$_SESSION["usuario_email"] = $usuario["email"];
$_SESSION["usuario_tipo"] = $usuario["tipo"];

            header("Location: index.php");
            exit();
        }
    }

    // Se chegou aqui, login falhou
    header("Location: entrar.php?erro=1");
    exit();
}
?>
