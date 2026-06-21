<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Login</title>

    <!-- Conexão com o CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body class="login-page">

<?php if(isset($_GET['erro'])): ?>
<div id="toastErro" class="toast-erro">
    E-mail ou senha incorretos!
</div>
<?php endif; ?>

    <!-- Fundo borrado -->
    <div class="background"></div>

    <!-- Header -->
    <header class="header">
        <img id="logoSite" src="img/LDE-dark2.png" alt="Divine Essence" style="width: 100px; height: auto;">

        <!-- botao pra ficar show no mobas -->
        <div class="menu-toggle" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <nav>
            <a href="https://github.com/JuuJap/DivineEssence">Sobre</a>
            <a href="cadastro.php">Criar conta</a>

            <!-- Botão de tema -->
            <button id="theme-toggle" onclick="toggleTheme()">
                <span class="icon">🌙</span>
            </button>
        </nav>
    </header>

    <!-- Conteúdo principal -->
    <main class="main-content">
<div class="login-container">
    <h2>Entrar</h2>

    <form action="login_usuario.php" method="POST">

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
            Entrar
        </button>

    </form>

    <a href="cadastro.php" class="forgot-password">
        Criar conta
    </a>
</div>

    <!-- Script -->
<script src="script.js?v=4"></script>

<?php if(isset($_GET['erro'])): ?>
<script>
setTimeout(() => {
    const toast = document.getElementById("toastErro");
    if(toast){
        toast.classList.add("sumir");
    }
}, 3000);
</script>
<?php endif; ?>

</body>
</html>
