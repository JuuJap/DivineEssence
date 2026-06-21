<?php
require_once "proteger.php";
require_once "conexao.php";

$qtdCarrinho = array_sum($_SESSION["carrinho"] ?? []);

$categoria = $_GET["categoria"] ?? "";
$busca = $_GET["busca"] ?? "";

$where = ["ativo = 1"];
$params = [];
$types = "";

if ($categoria !== "") {
    $where[] = "categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}

if ($busca !== "") {
    $where[] = "nome LIKE ?";
    $params[] = "%" . $busca . "%";
    $types .= "s";
}

$sql = "SELECT * FROM produtos";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY id ASC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$produtos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Divine Essence</title>

<link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
  rel="stylesheet"
>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="ecommerce.css?v=1">

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
  >
</head>

<body>

<header class="topo">
    <div class="logo">
        <a href="index.php">
            <img id="logoSite" src="img/LDE.png" alt="Divine Essence">
        </a>
    </div>

    <form class="barra-pesquisa" method="GET" action="index.php">
        <input type="text" name="busca" placeholder="Buscar produto" value="<?= htmlspecialchars($busca) ?>">
        <button type="submit" style="border:none;background:none;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>

    <a href="carrinho.php" class="carrinho">
        <i class="fa-solid fa-cart-shopping"></i>
        <span><?= $qtdCarrinho ?></span>
    </a>

    <div class="usuario-area">
        <span class="usuario-nome">
            Olá, <?= htmlspecialchars($_SESSION["usuario_nome"]) ?>
        </span>

<?php if(isset($_SESSION["usuario_tipo"]) && $_SESSION["usuario_tipo"] === "admin"): ?>
    <a href="admin_produtos.php" class="btn-entrar">Admin</a>
<?php endif; ?>

        <a href="meus_pedidos.php" class="btn-entrar">Pedidos</a>
        <a href="logout.php" class="btn-sair">Sair</a>
    </div>

    <div class="tema">
        <button id="btnTema" type="button">
            <i class="fa-solid fa-moon"></i>
        </button>
    </div>
</header>

<nav class="menu">
    <a href="index.php?categoria=Femininos">Femininos</a>
    <a href="index.php?categoria=Infantis">Infantis</a>
    <a href="index.php?categoria=Masculinos">Masculinos</a>
    <a href="carrinho.php">Carrinho</a>
</nav>

<section class="banner-carousel">
    <div id="bannerPrincipal" class="carousel slide" data-bs-ride="carousel">

        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerPrincipal" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#bannerPrincipal" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#bannerPrincipal" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#bannerPrincipal" data-bs-slide-to="3"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="banner banner1"></div>
            </div>

            <div class="carousel-item">
                <div class="banner banner2"></div>
            </div>

            <div class="carousel-item">
                <div class="banner banner3"></div>
            </div>

            <div class="carousel-item">
                <div class="banner banner4"></div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#bannerPrincipal" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#bannerPrincipal" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<section class="colecoes">
    <h2 class="titulo-colecoes">Coleções</h2>

    <div class="cards-colecoes">
        <a href="index.php?categoria=Masculinos" class="colecao-card">
            <img src="img/colecao1.png" alt="Coleção Lunar Men">
        </a>

        <a href="index.php?categoria=Femininos" class="colecao-card">
            <img src="img/colecao2.jpg" alt="Coleção Nature">
        </a>

        <a href="index.php?categoria=Infantis" class="colecao-card">
            <img src="img/colecao3.png" alt="Coleção Infantil">
        </a>
    </div>
</section>

<section class="produtos">
    <div class="topo-produtos">
        <h2>Produtos</h2>
        <a href="index.php">Ver todos</a>
    </div>

    <div class="grid-produtos">
        <?php while ($produto = $produtos->fetch_assoc()): ?>
            <div class="produto-card">
                <a href="produto.php?id=<?= $produto["id"] ?>" class="produto-link">
                    <div class="produto-img">
                        <img src="<?= htmlspecialchars($produto["imagem"]) ?>" alt="<?= htmlspecialchars($produto["nome"]) ?>">
                    </div>

                    <div class="avaliacao">
                        ★★★★★ <span>(<?= (int)$produto["avaliacao_qtd"] ?>)</span>
                    </div>

                    <h3><?= htmlspecialchars($produto["nome"]) ?></h3>

                    <p class="preco">
                        R$ <?= number_format($produto["preco"], 2, ",", ".") ?>
                    </p>
                </a>

                <form action="adicionar_carrinho.php" method="POST">
                    <input type="hidden" name="produto_id" value="<?= $produto["id"] ?>">
                    <input type="hidden" name="quantidade" value="1">
                    <button type="submit" name="acao" value="carrinho" class="btn-card-carrinho">
                        Adicionar ao carrinho
                    </button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<button id="btnTopo">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js?v=4"></script>

</body>
</html>