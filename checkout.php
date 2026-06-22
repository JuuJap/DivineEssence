<?php
require_once "proteger.php";
require_once "conexao.php";

$carrinho = $_SESSION["carrinho"] ?? [];

if (empty($carrinho)) {
    header("Location: carrinho.php");
    exit;
}

$ids = array_map("intval", array_keys($carrinho));
$sql = "SELECT * FROM produtos WHERE ativo = 1 AND id IN (" . implode(",", $ids) . ")";
$resultado = $conn->query($sql);

$itens = [];
$total = 0;

while ($produto = $resultado->fetch_assoc()) {
    $qtd = $carrinho[$produto["id"]];
    $produto["quantidade"] = $qtd;
    $produto["subtotal"] = $qtd * $produto["preco"];
    $total += $produto["subtotal"];
    $itens[] = $produto;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuarioId = $_SESSION["usuario_id"];
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $endereco = trim($_POST["endereco"]);
    $pagamento = trim($_POST["pagamento"]);

    if ($nome === "" || $email === "" || $endereco === "" || $pagamento === "") {
        die("Preencha todos os campos para finalizar o pedido.");
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            INSERT INTO pedidos 
            (usuario_id, nome_entrega, email_entrega, endereco, pagamento, total)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("issssd", $usuarioId, $nome, $email, $endereco, $pagamento, $total);
        $stmt->execute();

        $pedidoId = $conn->insert_id;

        $stmtItem = $conn->prepare("
            INSERT INTO itens_pedido 
            (pedido_id, produto_id, nome_produto, preco_unitario, quantidade, subtotal, imagem)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtEstoque = $conn->prepare("
            UPDATE produtos 
            SET estoque = estoque - ? 
            WHERE id = ? AND estoque >= ? AND ativo = 1
        ");

        foreach ($itens as $item) {
            $stmtEstoque->bind_param("iii", $item["quantidade"], $item["id"], $item["quantidade"]);
            $stmtEstoque->execute();

            if ($stmtEstoque->affected_rows === 0) {
                throw new Exception("Estoque insuficiente para o produto: " . $item["nome"]);
            }

            $stmtItem->bind_param(
                "iisdiis",
                $pedidoId,
                $item["id"],
                $item["nome"],
                $item["preco"],
                $item["quantidade"],
                $item["subtotal"],
                $item["imagem"]
            );

            $stmtItem->execute();
        }

        $conn->commit();

        unset($_SESSION["carrinho"]);

        header("Location: pedido_sucesso.php?id=" . $pedidoId);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        die("Erro ao finalizar pedido: " . $e->getMessage());
    }
}

$qtdCarrinho = array_sum($carrinho);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Divine Essence</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="ecommerce.css?v=9">

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <style>
        .pix-box {
            display: none;
            margin: 18px 0;
            padding: 22px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid rgba(229, 72, 104, 0.35);
            text-align: center;
        }

        .pix-box.ativo {
            display: block;
        }

        .pix-box h2 {
            margin-bottom: 10px;
            font-size: 24px;
        }

        .pix-box p {
            margin-bottom: 16px;
            font-size: 15px;
        }

        .pix-qrcode {
            width: 220px;
            max-width: 100%;
            border-radius: 16px;
            background: white;
            padding: 10px;
        }

        .pix-box small {
            display: block;
            margin-top: 12px;
            opacity: 0.8;
        }

.cartao-box {
    display: none;
    margin: 18px 0;
    padding: 22px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.12);
    border: 2px solid rgba(229, 72, 104, 0.35);
}

.cartao-box.ativo {
    display: block;
}

.cartao-box h2 {
    margin-bottom: 10px;
    font-size: 24px;
}

.cartao-box p {
    margin-bottom: 16px;
    font-size: 15px;
}

.cartao-linha {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.cartao-box small {
    display: block;
    margin-top: 12px;
    opacity: 0.8;
}

@media (max-width: 600px) {
    .cartao-linha {
        grid-template-columns: 1fr;
    }
}

.boleto-box {
    display: none;
    margin: 18px 0;
    padding: 22px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.12);
    border: 2px solid rgba(229, 72, 104, 0.35);
}

.boleto-box.ativo {
    display: block;
}

.boleto-box h2 {
    margin-bottom: 10px;
    font-size: 24px;
}

.boleto-box p {
    margin-bottom: 16px;
    font-size: 15px;
}

.boleto-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin: 16px 0;
}

.boleto-info div {
    padding: 14px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.16);
}

.boleto-info span {
    display: block;
    font-size: 13px;
    opacity: 0.8;
    margin-bottom: 5px;
}

.boleto-info strong {
    font-size: 18px;
}

.linha-digitavel-area {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
}

.linha-digitavel-area input {
    flex: 1;
    font-size: 14px;
}

.linha-digitavel-area button,
.btn-imprimir-boleto {
    border: none;
    border-radius: 12px;
    padding: 12px 16px;
    background: #4d4d69;
    color: white;
    font-weight: bold;
    cursor: pointer;
}

.codigo-barras-fake {
    width: 100%;
    height: 80px;
    margin: 18px 0;
    border-radius: 8px;
    background: repeating-linear-gradient(
        90deg,
        #000 0px,
        #000 3px,
        #fff 3px,
        #fff 6px,
        #000 6px,
        #000 8px,
        #fff 8px,
        #fff 14px
    );
}

.btn-imprimir-boleto {
    width: 100%;
    margin-top: 8px;
}

.boleto-box small {
    display: block;
    margin-top: 12px;
    opacity: 0.8;
    text-align: center;
}

@media (max-width: 600px) {
    .boleto-info {
        grid-template-columns: 1fr;
    }

    .linha-digitavel-area {
        flex-direction: column;
    }
}
    </style>
</head>
<body>

<header class="topo">
    <div class="logo">
        <a href="index.php">
            <img id="logoSite" src="img/LDE.png" alt="Divine Essence">
        </a>
    </div>

    <form class="barra-pesquisa" method="GET" action="index.php">
        <input type="text" name="busca" placeholder="Buscar produto">
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
    <a href="index.php?categoria=Masculinos">Masculinos</a>
    <a href="index.php?categoria=Kits">Kits</a>
    <a href="carrinho.php">Carrinho</a>
</nav>

<main class="pagina-loja">
    <div class="grid-checkout">
        <form method="POST" class="box-loja">
            <h1>Finalizar compra</h1>

            <label>Nome completo</label>
            <input 
                type="text" 
                name="nome" 
                value="<?= htmlspecialchars($_SESSION["usuario_nome"]) ?>" 
                required
            >

            <label>E-mail</label>
            <input 
                type="email" 
                name="email" 
                value="<?= htmlspecialchars($_SESSION["usuario_email"] ?? "") ?>" 
                required
            >

            <label>Endereço</label>
            <textarea name="endereco" required></textarea>

            <label>Pagamento</label>
<select name="pagamento" id="pagamentoSelect" required>
    <option value="">Selecione</option>
    <option value="Pix">Pix</option>
    <option value="Cartão de crédito">Cartão de crédito</option>
    <option value="Boleto">Boleto</option>
</select>

            <div class="pix-box" id="pixBox">
                <h2>Pagamento via Pix</h2>
                <p>Escaneie o QR Code abaixo com o aplicativo do seu banco para concluir o pagamento.</p>

                <a href="img/pix.jpg" target="_blank" rel="noopener noreferrer" class="pix-link">
                    <img src="img/pix.jpg" alt="QR Code para pagamento via Pix" class="pix-qrcode">
                </a>

                <small>Toque na imagem para abrir o QR Code em tamanho maior.</small>
            </div>

            <div class="cartao-box" id="cartaoBox">
    <h2>Pagamento com cartão</h2>
    <p>Preencha os dados do cartão para simular a finalização da compra.</p>

    <label>Número do cartão</label>
    <input 
        type="text" 
        id="numeroCartao" 
        placeholder="0000 0000 0000 0000" 
        maxlength="19"
        inputmode="numeric"
        autocomplete="off"
    >

    <label>Nome impresso no cartão</label>
    <input 
        type="text" 
        id="nomeCartao" 
        placeholder="NOME DO TITULAR"
        autocomplete="off"
    >

    <div class="cartao-linha">
        <div>
            <label>Validade</label>
            <input 
                type="text" 
                id="validadeCartao" 
                placeholder="MM/AA" 
                maxlength="5"
                inputmode="numeric"
                autocomplete="off"
            >
        </div>

        <div>
            <label>CVV</label>
            <input 
                type="text" 
                id="cvvCartao" 
                placeholder="000" 
                maxlength="4"
                inputmode="numeric"
                autocomplete="off"
            >
        </div>
    </div>

    <small>Esses dados são apenas ilustrativos e não serão salvos.</small>
</div>

<div class="boleto-box" id="boletoBox">
    <h2>Pagamento via boleto</h2>

    <p>
        Seu boleto foi gerado para simulação. Pague até a data de vencimento usando a linha digitável abaixo.
    </p>

    <div class="boleto-info">
        <div>
            <span>Valor</span>
            <strong>R$ <?= number_format($total, 2, ",", ".") ?></strong>
        </div>

        <div>
            <span>Vencimento</span>
            <strong><?= date("d/m/Y", strtotime("+3 days")) ?></strong>
        </div>
    </div>

    <label>Linha digitável</label>

    <div class="linha-digitavel-area">
        <input 
            type="text" 
            id="linhaDigitavel" 
            value="34191.79001 01043.510047 91020.150008 8 00000000000000" 
            readonly
        >

        <button type="button" id="copiarBoleto">
            Copiar
        </button>
    </div>

    <div class="codigo-barras-fake"></div>

    <button type="button" class="btn-imprimir-boleto" onclick="window.print()">
        Imprimir boleto
    </button>

    <small>
        Boleto demonstrativo. Nenhuma cobrança real será gerada.
    </small>
</div>

            <button type="submit" class="btn-loja">Confirmar pedido</button>
        </form>

        <aside class="box-loja">
            <h2>Resumo do pedido</h2>

            <?php foreach ($itens as $item): ?>
                <div class="linha-resumo">
                    <span>
                        <?= htmlspecialchars($item["nome"]) ?> x<?= $item["quantidade"] ?>
                    </span>

                    <strong>
                        R$ <?= number_format($item["subtotal"], 2, ",", ".") ?>
                    </strong>
                </div>
            <?php endforeach; ?>

            <h2>Total: R$ <?= number_format($total, 2, ",", ".") ?></h2>
        </aside>
    </div>
</main>

<script src="script.js?v=4"></script>

<script>
    const pagamentoSelect = document.getElementById("pagamentoSelect");

    const pixBox = document.getElementById("pixBox");
    const cartaoBox = document.getElementById("cartaoBox");
    const boletoBox = document.getElementById("boletoBox");

    const numeroCartao = document.getElementById("numeroCartao");
    const nomeCartao = document.getElementById("nomeCartao");
    const validadeCartao = document.getElementById("validadeCartao");
    const cvvCartao = document.getElementById("cvvCartao");

    const camposCartao = [
        numeroCartao,
        nomeCartao,
        validadeCartao,
        cvvCartao
    ];

    function atualizarAreaPagamento() {
        const pagamento = pagamentoSelect.value;

        pixBox.classList.toggle("ativo", pagamento === "Pix");
        cartaoBox.classList.toggle("ativo", pagamento === "Cartão de crédito");
        boletoBox.classList.toggle("ativo", pagamento === "Boleto");

        camposCartao.forEach(campo => {
            campo.required = pagamento === "Cartão de crédito";
            campo.disabled = pagamento !== "Cartão de crédito";
        });
    }

    pagamentoSelect.addEventListener("change", atualizarAreaPagamento);
    atualizarAreaPagamento();

    numeroCartao.addEventListener("input", function () {
        let valor = this.value.replace(/\D/g, "");
        valor = valor.replace(/(\d{4})(?=\d)/g, "$1 ");
        this.value = valor;
    });

    validadeCartao.addEventListener("input", function () {
        let valor = this.value.replace(/\D/g, "");

        if (valor.length >= 3) {
            valor = valor.substring(0, 2) + "/" + valor.substring(2, 4);
        }

        this.value = valor;
    });

    cvvCartao.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, "");
    });

    nomeCartao.addEventListener("input", function () {
        this.value = this.value.toUpperCase();
    });

    const copiarBoleto = document.getElementById("copiarBoleto");
    const linhaDigitavel = document.getElementById("linhaDigitavel");

    copiarBoleto.addEventListener("click", function () {
        linhaDigitavel.select();
        linhaDigitavel.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(linhaDigitavel.value);

        copiarBoleto.textContent = "Copiado!";

        setTimeout(() => {
            copiarBoleto.textContent = "Copiar";
        }, 2000);
    });
</script>

</body>
</html>