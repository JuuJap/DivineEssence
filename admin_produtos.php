<?php
require_once "proteger_admin.php";
require_once "conexao.php";

$mensagem = "";
$produtoEditar = null;

$pastaUpload = "img/produtos/";

if (!is_dir($pastaUpload)) {
    mkdir($pastaUpload, 0777, true);
}

function salvarImagem($campo, $pastaUpload) {
    if (!isset($_FILES[$campo]) || $_FILES[$campo]["error"] !== UPLOAD_ERR_OK) {
        return null;
    }

    $arquivo = $_FILES[$campo];
    $extensao = strtolower(pathinfo($arquivo["name"], PATHINFO_EXTENSION));

    $permitidas = ["jpg", "jpeg", "png", "webp"];

    if (!in_array($extensao, $permitidas)) {
        return null;
    }

    $novoNome = uniqid("produto_") . "." . $extensao;
    $destino = $pastaUpload . $novoNome;

    if (move_uploaded_file($arquivo["tmp_name"], $destino)) {
        return $destino;
    }

    return null;
}

/* =========================
   CARREGAR PRODUTO PARA EDITAR
========================= */
if (isset($_GET["editar"])) {
    $idEditar = intval($_GET["editar"]);

    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $idEditar);
    $stmt->execute();

    $produtoEditar = $stmt->get_result()->fetch_assoc();
}

/* =========================
   CADASTRAR PRODUTO
========================= */
if (isset($_POST["acao"]) && $_POST["acao"] === "cadastrar") {
    $nome = trim($_POST["nome"]);
    $descricao = trim($_POST["descricao"]);
    $preco = floatval(str_replace(",", ".", $_POST["preco"]));
    $precoAntigo = !empty($_POST["preco_antigo"]) ? floatval(str_replace(",", ".", $_POST["preco_antigo"])) : null;
    $categoria = trim($_POST["categoria"]);
    $estoque = intval($_POST["estoque"]);
    $avaliacaoQtd = intval($_POST["avaliacao_qtd"]);

    $imagem = salvarImagem("imagem", $pastaUpload);

    if (!$imagem) {
        $mensagem = "Erro: envie uma imagem válida em JPG, PNG ou WEBP.";
    } else {
        $sql = "INSERT INTO produtos 
        (nome, descricao, preco, preco_antigo, imagem, categoria, estoque, avaliacao_qtd, ativo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssddssii",
            $nome,
            $descricao,
            $preco,
            $precoAntigo,
            $imagem,
            $categoria,
            $estoque,
            $avaliacaoQtd
        );

        if ($stmt->execute()) {
            header("Location: admin_produtos.php?msg=cadastrado");
            exit;
        } else {
            $mensagem = "Erro ao cadastrar produto.";
        }
    }
}

/* =========================
   ATUALIZAR PRODUTO
========================= */
if (isset($_POST["acao"]) && $_POST["acao"] === "editar") {
    $id = intval($_POST["id"]);
    $nome = trim($_POST["nome"]);
    $descricao = trim($_POST["descricao"]);
    $preco = floatval(str_replace(",", ".", $_POST["preco"]));
    $precoAntigo = !empty($_POST["preco_antigo"]) ? floatval(str_replace(",", ".", $_POST["preco_antigo"])) : null;
    $categoria = trim($_POST["categoria"]);
    $estoque = intval($_POST["estoque"]);
    $avaliacaoQtd = intval($_POST["avaliacao_qtd"]);

    $imagemAtual = $_POST["imagem_atual"];
    $novaImagem = salvarImagem("imagem", $pastaUpload);

    $imagemFinal = $novaImagem ? $novaImagem : $imagemAtual;

    $sql = "UPDATE produtos SET
        nome = ?,
        descricao = ?,
        preco = ?,
        preco_antigo = ?,
        imagem = ?,
        categoria = ?,
        estoque = ?,
        avaliacao_qtd = ?
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssddssiii",
        $nome,
        $descricao,
        $preco,
        $precoAntigo,
        $imagemFinal,
        $categoria,
        $estoque,
        $avaliacaoQtd,
        $id
    );

    if ($stmt->execute()) {
        header("Location: admin_produtos.php?msg=editado");
        exit;
    } else {
        $mensagem = "Erro ao editar produto.";
    }
}

/* =========================
   REMOVER DA LOJA
========================= */
if (isset($_GET["remover"])) {
    $id = intval($_GET["remover"]);

    $stmt = $conn->prepare("UPDATE produtos SET ativo = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin_produtos.php?msg=removido");
    exit;
}

/* =========================
   REATIVAR PRODUTO
========================= */
if (isset($_GET["ativar"])) {
    $id = intval($_GET["ativar"]);

    $stmt = $conn->prepare("UPDATE produtos SET ativo = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin_produtos.php?msg=ativado");
    exit;
}

/* =========================
   MENSAGENS
========================= */
if (isset($_GET["msg"])) {
    if ($_GET["msg"] === "cadastrado") {
        $mensagem = "Produto cadastrado com sucesso!";
    }

    if ($_GET["msg"] === "editado") {
        $mensagem = "Produto editado com sucesso!";
    }

    if ($_GET["msg"] === "removido") {
        $mensagem = "Produto removido da loja.";
    }

    if ($_GET["msg"] === "ativado") {
        $mensagem = "Produto reativado na loja.";
    }
}

$produtos = $conn->query("SELECT * FROM produtos ORDER BY ativo DESC, id DESC");
$qtdCarrinho = array_sum($_SESSION["carrinho"] ?? []);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin Produtos | Divine Essence</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="ecommerce.css?v=7">

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

    <a href="carrinho.php" class="carrinho">
        <i class="fa-solid fa-cart-shopping"></i>
        <span><?= $qtdCarrinho ?></span>
    </a>

    <div class="usuario-area">
        <span class="usuario-nome">
            Admin: <?= htmlspecialchars($_SESSION["usuario_nome"]) ?>
        </span>

        <a href="index.php" class="btn-entrar">Loja</a>
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
    <a href="index.php">Início</a>
    <a href="admin_produtos.php">Produtos</a>
    <a href="meus_pedidos.php">Meus pedidos</a>
</nav>

<main class="pagina-loja">

    <h1 class="titulo-pagina">Painel de Produtos</h1>

    <?php if ($mensagem): ?>
        <div class="box-loja mensagem-admin">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <section class="box-loja admin-form-box">

        <?php if ($produtoEditar): ?>
            <h2>Editar produto</h2>
        <?php else: ?>
            <h2>Adicionar produto</h2>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form-admin-produto">

            <?php if ($produtoEditar): ?>
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="id" value="<?= $produtoEditar["id"] ?>">
                <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($produtoEditar["imagem"]) ?>">
            <?php else: ?>
                <input type="hidden" name="acao" value="cadastrar">
            <?php endif; ?>

            <div class="admin-grid-form">
                <div>
                    <label>Nome do produto</label>
                    <input 
                        type="text" 
                        name="nome" 
                        value="<?= $produtoEditar ? htmlspecialchars($produtoEditar["nome"]) : "" ?>" 
                        required
                    >
                </div>

                <div>
                    <label>Categoria</label>
                    <select name="categoria" required>
                        <option value="">Selecione</option>

                        <?php
                        $categorias = ["Femininos", "Masculinos", "Infantis"];
                        foreach ($categorias as $cat):
                            $selecionado = ($produtoEditar && $produtoEditar["categoria"] === $cat) ? "selected" : "";
                        ?>
                            <option value="<?= $cat ?>" <?= $selecionado ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label>Preço atual</label>
                    <input 
                        type="text" 
                        name="preco" 
                        placeholder="Ex: 149.90"
                        value="<?= $produtoEditar ? htmlspecialchars($produtoEditar["preco"]) : "" ?>"
                        required
                    >
                </div>

                <div>
                    <label>Preço antigo</label>
                    <input 
                        type="text" 
                        name="preco_antigo" 
                        placeholder="Ex: 189.90"
                        value="<?= $produtoEditar ? htmlspecialchars($produtoEditar["preco_antigo"]) : "" ?>"
                    >
                </div>

                <div>
                    <label>Estoque</label>
                    <input 
                        type="number" 
                        name="estoque" 
                        min="0"
                        value="<?= $produtoEditar ? htmlspecialchars($produtoEditar["estoque"]) : "" ?>"
                        required
                    >
                </div>

                <div>
                    <label>Avaliações</label>
                    <input 
                        type="number" 
                        name="avaliacao_qtd" 
                        min="0"
                        value="<?= $produtoEditar ? htmlspecialchars($produtoEditar["avaliacao_qtd"]) : "0" ?>"
                    >
                </div>
            </div>

            <label>Descrição</label>
            <textarea name="descricao" required><?= $produtoEditar ? htmlspecialchars($produtoEditar["descricao"]) : "" ?></textarea>

            <label>Imagem do produto</label>
            <input 
                type="file" 
                name="imagem" 
                accept="image/png, image/jpeg, image/webp"
                <?= $produtoEditar ? "" : "required" ?>
            >

            <?php if ($produtoEditar): ?>
                <div class="preview-admin">
                    <p>Imagem atual:</p>
                    <img src="<?= htmlspecialchars($produtoEditar["imagem"]) ?>" alt="Imagem atual">
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-loja">
                <?= $produtoEditar ? "Salvar alterações" : "Cadastrar produto" ?>
            </button>

            <?php if ($produtoEditar): ?>
                <a href="admin_produtos.php" class="btn-secundario">
                    Cancelar edição
                </a>
            <?php endif; ?>

        </form>
    </section>

    <section class="box-loja">
        <h2>Produtos cadastrados</h2>

        <?php while ($produto = $produtos->fetch_assoc()): ?>
            <div class="admin-produto-item">

                <img 
                    src="<?= htmlspecialchars($produto["imagem"]) ?>" 
                    alt="<?= htmlspecialchars($produto["nome"]) ?>"
                >

                <div>
                    <h3><?= htmlspecialchars($produto["nome"]) ?></h3>

                    <p>Categoria: <?= htmlspecialchars($produto["categoria"]) ?></p>

                    <p>
                        Preço: R$ <?= number_format($produto["preco"], 2, ",", ".") ?>
                    </p>

                    <p>Estoque: <?= (int)$produto["estoque"] ?></p>

                    <p>
                        Status:
                        <?php if ($produto["ativo"]): ?>
                            <strong class="status-ativo">Ativo</strong>
                        <?php else: ?>
                            <strong class="status-removido">Removido</strong>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="admin-acoes">

                    <a 
                        href="admin_produtos.php?editar=<?= $produto["id"] ?>" 
                        class="btn-secundario"
                    >
                        Editar
                    </a>

                    <?php if ($produto["ativo"]): ?>
                        <a 
                            href="admin_produtos.php?remover=<?= $produto["id"] ?>" 
                            class="btn-secundario"
                            onclick="return confirm('Remover este produto da loja?')"
                        >
                            Remover
                        </a>
                    <?php else: ?>
                        <a 
                            href="admin_produtos.php?ativar=<?= $produto["id"] ?>" 
                            class="btn-loja"
                        >
                            Reativar
                        </a>
                    <?php endif; ?>

                </div>

            </div>
        <?php endwhile; ?>

    </section>

</main>

<script src="script.js?v=4"></script>
</body>
</html>