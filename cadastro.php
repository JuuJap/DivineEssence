<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta</title>

    <link rel="stylesheet" href="style.css">
</head>

<body class="login-page">

<?php if(isset($_GET['erro']) && $_GET['erro'] == 'email'): ?>
    <div class="toast-erro">
        Este e-mail já está cadastrado!
    </div>

    <script>
        window.addEventListener("load", () => {

            const toast = document.querySelector(".toast-erro");

            if(!toast) return;

            setTimeout(() => {

                toast.classList.add("sumir");

                setTimeout(() => {
                    toast.remove();
                }, 500);

            }, 3000);

        });
    </script>
<?php endif; ?>

<div class="background"></div>

<header class="header">

    <img
        id="logoSite"
        src="img/LDE-dark2.png"
        alt="Divine Essence"
        style="width: 100px; height: auto;"
    >

    <div class="menu-toggle" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <nav>
            <a href="https://github.com/JuuJap/DivineEssence">Sobre</a>
        <a href="entrar.php">Entrar</a>

        <button id="theme-toggle" type="button">
    <span class="icon">🌙</span>
</button>
    </nav>

</header>

<main>

    <div class="login-container">

        <h2>Criar Conta</h2>

        <form action="cadastro_usuario.php" method="POST">

            <input
                type="text"
                name="nome"
                placeholder="Nome de usuário"
                required
            >

            <input
                type="email"
                name="email"
                placeholder="E-mail"
                required
            >

            <input
                type="password"
                name="senha"
                placeholder="Senha"
                required
            >

            <button type="submit">
                Cadastrar
            </button>

        </form>

        <a href="entrar.php" class="forgot-password">
            Já tenho conta
        </a>

    </div>

</main>

<script src="script.js?v=5"></script>


</body>
</html>