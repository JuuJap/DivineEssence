<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta</title>

    <link rel="stylesheet" href="style.css">
</head>

<body class="login-page">

    <div class="background"></div>

    <header class="header">
        <img id="logoSite" src="img/LDE-dark2.png" alt="Divine Essence" style="width: 100px; height: auto;">

        
        <div class="menu-toggle" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>

        
        <nav>
            <a href="#">Contato</a>
            <a href="entrar.php">Login</a>

            <button id="theme-toggle" onclick="toggleTheme()">
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

    <script src="script.js"></script>

</body>
</html>