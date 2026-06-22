<?php
require_once "proteger_admin.php";
require_once "conexao.php";

$mensagem = "";
$produtoEditar = null;

$pastaUpload = "img/produtos/";

if (!is_dir($pastaUpload)) {
    mkdir($pastaUpload, 0777, true);
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

function validarTokenCSRF() {
    if (
        empty($_POST["csrf_token"]) ||
        empty($_SESSION["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        die("Ação inválida. Atualize a página e tente novamente.");
    }
}

function definirCategoriaProduto($tipoItem, $categoriaPerfume) {
    $tipoItem = trim($tipoItem);
    $categoriaPerfume = trim($categoriaPerfume);

    if ($tipoItem === "kit") {
        return "Kits";
    }

    if ($tipoItem === "perfume" && in_array($categoriaPerfume, ["Femininos", "Masculinos"], true)) {
        return $categoriaPerfume;
    }

    return "";
}

function salvarImagem($campo, $pastaUpload) {
    if (!isset($_FILES[$campo]) || $_FILES[$campo]["error"] !== UPLOAD_ERR_OK) {
        return null;
    }

    $arquivo = $_FILES[$campo];
    $extensao = strtolower(pathinfo($arquivo["name"], PATHINFO_EXTENSION));
    $tamanhoMaximo = 3 * 1024 * 1024;

    $permitidas = [
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "png" => "image/png",
        "webp" => "image/webp"
    ];

    if (!isset($permitidas[$extensao]) || $arquivo["size"] > $tamanhoMaximo) {
        return null;
    }

    $dadosImagem = getimagesize($arquivo["tmp_name"]);

    if ($dadosImagem === false || ($dadosImagem["mime"] ?? "") !== $permitidas[$extensao]) {
        return null;
    }

    $novoNome = uniqid("produto_", true) . "." . $extensao;
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
   CADASTRAR PRODUTO OU KIT
========================= */
if (isset($_POST["acao"]) && $_POST["acao"] === "cadastrar") {
    validarTokenCSRF();

    $nome = trim($_POST["nome"]);
    $descricao = trim($_POST["descricao"]);
    $preco = floatval(str_replace(",", ".", $_POST["preco"]));
    $precoAntigo = !empty($_POST["preco_antigo"]) ? floatval(str_replace(",", ".", $_POST["preco_antigo"])) : null;
    $tipoItem = $_POST["tipo_item"] ?? "perfume";
    $categoria = definirCategoriaProduto($tipoItem, $_POST["categoria"] ?? "");
    $estoque = intval($_POST["estoque"]);
    $avaliacaoQtd = intval($_POST["avaliacao_qtd"]);

    if ($categoria === "") {
        $mensagem = "Erro: selecione se o cadastro é de perfume ou kit.";
    } else {
        $imagem = salvarImagem("imagem", $pastaUpload);

        if (!$imagem) {
            $mensagem = "Erro: envie uma imagem válida em JPG, PNG ou WEBP com até 3 MB.";
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
                $mensagem = "Erro ao cadastrar item.";
            }
        }
    }
}

/* =========================
   ATUALIZAR PRODUTO OU KIT
========================= */
if (isset($_POST["acao"]) && $_POST["acao"] === "editar") {
    validarTokenCSRF();

    $id = intval($_POST["id"]);
    $nome = trim($_POST["nome"]);
    $descricao = trim($_POST["descricao"]);
    $preco = floatval(str_replace(",", ".", $_POST["preco"]));
    $precoAntigo = !empty($_POST["preco_antigo"]) ? floatval(str_replace(",", ".", $_POST["preco_antigo"])) : null;
    $tipoItem = $_POST["tipo_item"] ?? "perfume";
    $categoria = definirCategoriaProduto($tipoItem, $_POST["categoria"] ?? "");
    $estoque = intval($_POST["estoque"]);
    $avaliacaoQtd = intval($_POST["avaliacao_qtd"]);

    if ($categoria === "") {
        $mensagem = "Erro: selecione se o cadastro é de perfume ou kit.";
    } else {
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
            $mensagem = "Erro ao editar item.";
        }
    }
}

/* =========================
   REMOVER OU REATIVAR PRODUTO
========================= */
if (isset($_POST["acao"]) && in_array($_POST["acao"], ["remover", "ativar"], true)) {
    validarTokenCSRF();

    $id = intval($_POST["id"]);
    $ativo = $_POST["acao"] === "ativar" ? 1 : 0;

    $stmt = $conn->prepare("UPDATE produtos SET ativo = ? WHERE id = ?");
    $stmt->bind_param("ii", $ativo, $id);
    $stmt->execute();

    $msg = $ativo ? "ativado" : "removido";
    header("Location: admin_produtos.php?msg=" . $msg);
    exit;
}

/* =========================
   MENSAGENS
========================= */
if (isset($_GET["msg"])) {
    if ($_GET["msg"] === "cadastrado") {
        $mensagem = "Item cadastrado com sucesso!";
    }

    if ($_GET["msg"] === "editado") {
        $mensagem = "Item editado com sucesso!";
    }

    if ($_GET["msg"] === "removido") {
        $mensagem = "Item removido da loja.";
    }

    if ($_GET["msg"] === "ativado") {
        $mensagem = "Item reativado na loja.";
    }
}

$produtos = $conn->query("SELECT * FROM produtos ORDER BY ativo DESC, categoria = 'Kits', id DESC");
$qtdCarrinho = array_sum($_SESSION["carrinho"] ?? []);
$tipoSelecionado = ($produtoEditar && $produtoEditar["categoria"] === "Kits") ? "kit" : "perfume";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin Produtos | Divine Essence</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="ecommerce.css?v=8">

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
    <a href="admin_produtos.php">Produtos e Kits</a>
    <a href="meus_pedidos.php">Meus pedidos</a>
</nav>

<main class="pagina-loja">

    <h1 class="titulo-pagina">Painel de Produtos e Kits</h1>

    <?php if ($mensagem): ?>
        <div class="box-loja mensagem-admin">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <section class="box-loja admin-form-box">

        <?php if ($produtoEditar): ?>
            <h2>Editar item</h2>
        <?php else: ?>
            <h2>Adicionar item</h2>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form-admin-produto">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">

            <?php if ($produtoEditar): ?>
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="id" value="<?= $produtoEditar["id"] ?>">
                <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($produtoEditar["imagem"]) ?>">
            <?php else: ?>
                <input type="hidden" name="acao" value="cadastrar">
            <?php endif; ?>

            <div class="admin-grid-form">
                <div>
                    <label>Tipo de item</label>
                    <select name="tipo_item" id="tipoItem" required>
                        <option value="perfume" <?= $tipoSelecionado === "perfume" ? "selected" : "" ?>>Perfume</option>
                        <option value="kit" <?= $tipoSelecionado === "kit" ? "selected" : "" ?>>Kit</option>
                    </select>
                </div>

                <div id="grupoCategoriaPerfume">
                    <label>Categoria do perfume</label>
                    <select name="categoria" id="categoriaPerfume">
                        <option value="">Selecione</option>

                        <?php
                        $categorias = ["Femininos", "Masculinos"];
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
                    <label>Nome do item</label>
                    <input 
                        type="text" 
                        name="nome" 
                        value="<?= $produtoEditar ? htmlspecialchars($produtoEditar["nome"]) : "" ?>" 
                        required
                    >
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

            <label>Imagem do item</label>
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
                <?= $produtoEditar ? "Salvar alterações" : "Cadastrar item" ?>
            </button>

            <?php if ($produtoEditar): ?>
                <a href="admin_produtos.php" class="btn-secundario">
                    Cancelar edição
                </a>
            <?php endif; ?>

        </form>
    </section>

    <section class="box-loja">
        <h2>Itens cadastrados</h2>

        <?php while ($produto = $produtos->fetch_assoc()): ?>
            <div class="admin-produto-item">

                <img 
                    src="<?= htmlspecialchars($produto["imagem"]) ?>" 
                    alt="<?= htmlspecialchars($produto["nome"]) ?>"
                >

                <div>
                    <h3><?= htmlspecialchars($produto["nome"]) ?></h3>

                    <p>Tipo: <?= $produto["categoria"] === "Kits" ? "Kit" : "Perfume" ?></p>
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
                        <form method="POST" class="form-acao-admin" onsubmit="return confirm('Remover este item da loja?')">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">
                            <input type="hidden" name="acao" value="remover">
                            <input type="hidden" name="id" value="<?= $produto["id"] ?>">
                            <button type="submit" class="btn-secundario">
                                Remover
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="form-acao-admin">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">
                            <input type="hidden" name="acao" value="ativar">
                            <input type="hidden" name="id" value="<?= $produto["id"] ?>">
                            <button type="submit" class="btn-loja">
                                Reativar
                            </button>
                        </form>
                    <?php endif; ?>

                </div>

            </div>
        <?php endwhile; ?>

    </section>

</main>

<script src="script.js?v=4"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const tipoItem = document.getElementById("tipoItem");
    const grupoCategoria = document.getElementById("grupoCategoriaPerfume");
    const categoriaPerfume = document.getElementById("categoriaPerfume");

    function atualizarFormulario() {
        if (!tipoItem || !grupoCategoria || !categoriaPerfume) {
            return;
        }

        if (tipoItem.value === "kit") {
            grupoCategoria.style.display = "none";
            categoriaPerfume.removeAttribute("required");
        } else {
            grupoCategoria.style.display = "block";
            categoriaPerfume.setAttribute("required", "required");
        }
    }

    atualizarFormulario();
    tipoItem.addEventListener("change", atualizarFormulario);
});
</script>
</body>
</html>
