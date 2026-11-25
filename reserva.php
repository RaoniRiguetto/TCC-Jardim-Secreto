<?php
require_once "include/conexao.php";
require_once "include/validacoes.php";
session_start();

$validacao = true;
$mensagemLogin = $_SESSION['mensagemLogin'] ?? "";
$mensagensErro = $_SESSION['mensagensErro'] ?? [];
$oldInput = $_SESSION['oldInput'] ?? [];

if (empty($oldInput['categoria']) && isset($_GET['categoria'])) {
    $oldInput['categoria'] = intval($_GET['categoria']);
}

unset($_SESSION['mensagemLogin'], $_SESSION['mensagensErro'], $_SESSION['oldInput']);

$sqlCategorias = "SELECT * FROM tbcategoria WHERE status = 1 ORDER BY nome";
$resultadoCategorias = mysqli_query($conexao, $sqlCategorias);

$numQuartos = 0;
if (isset($_POST['buscar'])) {

    if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "usuario") {
        $mensagem = "Faça login como usuário para continuar.";
        header("Location: login.php?mensagem=" . urlencode($mensagem));
        exit();
    }

    $dataCheckIn = $_POST['checkin'];
    $dataCheckOut = $_POST['checkout'];
    $categoria = $_POST['categoria'];
    $qteAdultos = $_POST['qteAdultos'];
    $qteCriancas = $_POST['qteCriancas'];
    $qteOcupantes = $qteAdultos + $qteCriancas;

    $erros = [];

    if (!verifVazio($dataCheckIn)){
        $erros[] = "O campo 'Check-in' é obrigatório.";
        $validacao = false;
    }
    if (!verifVazio($dataCheckOut)){
        $erros[] = "O campo 'Check-out' é obrigatório.";
        $validacao = false;
    }
    if (!verifVazio($categoria)){
        $erros[] = "O campo 'Categoria' é obrigatório.";
        $validacao = false;
    }
    if (!verifVazio($qteAdultos)){
        $erros[] = "O campo 'Adultos' é obrigatório.";
        $validacao = false;
    }
    if ($qteCriancas === '' || $qteCriancas === null){
        $erros[] = "O campo 'Crianças' é obrigatório.";
        $validacao = false;
    }

    if ($validacao) {
        if(!dataFuturo($dataCheckIn, true)){
            $erros[] = "A data de 'Check-in' não pode ser no passado.";
            $validacao = false;
        }
        if ($dataCheckOut <= $dataCheckIn) {
            $erros[] = "A data de 'Check-out' deve ser posterior à data de 'Check-in'.";
            $validacao = false;
        }
    }
    

    if($validacao){

        $qteOcupantesInt = intval($qteOcupantes);
        $categoriaInt = intval($categoria);

        // filtra quartos que comportam os hóspedes e não têm reserva batendo com as datas escolhidas
        $sql = "SELECT q.*, c.nome AS nomeCategoria
            FROM tbquarto q
            JOIN tbcategoria c ON q.fkCategoria = c.id
            WHERE q.ocupacao >= ?
            AND q.fkCategoria = ?
            AND q.status != 'inativo'
            AND q.id NOT IN (
                SELECT fkQuarto FROM tbreserva
                WHERE dataChekIn < ? AND dataChekOut > ? AND status != 'cancelado'
            )";

        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "iiss", $qteOcupantesInt, $categoriaInt, $dataCheckOut, $dataCheckIn);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $numQuartos = mysqli_num_rows($resultado);
        $_SESSION['dadosBusca'] = $_POST;

    }else{

        $_SESSION['mensagensErro'] = $erros;
        $_SESSION['oldInput'] = $_POST;
        header("Location: reserva.php");
        exit();

    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/reservas.css">
</head>

<body>

    <?php include_once "include/header.php" ?>

    <main class="main-content">
        <div class="reservation-container">
            <div class="reservation-card">

                <div class="reservation-header">
                    <h1>Faça sua Reserva</h1>
                    <p>Preencha os dados abaixo para garantir sua estadia conosco</p>
                </div>

                <?php if (!empty($mensagemLogin)){ ?>
                    <div class="card card-erro-flutuante">
                        <div class="card-content">
                            <?= htmlspecialchars($mensagemLogin); ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!empty($mensagensErro)){ ?>
                        <div class="card card-erro-flutuante">
                            <div class="card-content">
                                <?php foreach ($mensagensErro as $erro){ ?>
                                    <p><?= htmlspecialchars($erro); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                <form class="reservation-form" method="POST">
                    <div class="form-section">
                        <div class="card card-sucesso">
                            <div class="card-content">
                                <p class="rooms-info">Para reservar mais de um quarto é necessario realizar outra reserva.</p>
                            </div>
                        </div>
                        <h2>Informações da Reserva</h2>

                            <div class="form-row">

                                <div class="form-group">
                                    <label for="checkin">Check-in *</label>
                                    <input type="date" id="checkin" name="checkin" required  min="<?= date("Y-m-d"); ?>" value="<?= htmlspecialchars($oldInput['checkin'] ?? '') ?>">
                                </div>

                                <div class="form-group">
                                    <label for="checkout">Check-out *</label>
                                    <input type="date" id="checkout" name="checkout" required min="<?= date("Y-m-d"); ?>" value="<?= htmlspecialchars($oldInput['checkout'] ?? '') ?>">
                                </div>

                            </div>

                            <div class="form-row">

                                <div class="form-group">
                                    <label for="category">Categoria *</label>
                                    <select id="category" name="categoria" required >
                                        <option value="">Selecione</option>
                                        <?php while($cat = mysqli_fetch_assoc($resultadoCategorias)){ ?>
                                            <option value="<?= $cat['id'] ?>" <?= (isset($oldInput['categoria']) && $oldInput['categoria'] == $cat['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['nome']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="qteAdultos">Adultos *</label>
                                    <select id="qteAdultos" name="qteAdultos" required>
                                        <option value="">Selecione</option>
                                        <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <option value="<?= $i ?>" <?= (isset($oldInput['qteAdultos']) && $oldInput['qteAdultos'] == $i) ? 'selected' : '' ?>>
                                                <?= $i ?> Adulto<?= $i > 1 ? 's' : '' ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="qteCriancas">Crianças</label>
                                    <select id="qteCriancas" name="qteCriancas" required>
                                        <?php for ($i = 0; $i <= 10; $i++): ?>
                                            <option value="<?= $i ?>" <?= (isset($oldInput['qteCriancas']) && $oldInput['qteCriancas'] == $i) ? 'selected' : '' ?>>
                                                <?= $i ?> Criança<?= $i != 1 ? 's' : '' ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                            </div>

                        <button type="submit" class="btn-submit" name="buscar">Buscar Quartos</button>
                    </div>

                </form>

                <?php if (isset($_POST['buscar']) && isset($resultado)){ ?>
                    <form class="reservation-form" method="POST" action="pagamento.php">
                        <div class="rooms-section">
                            <div class="form-section">

                                <div class="search-header">
                                    <h1>Quartos Disponíveis</h1>
                                </div>

                                <?php if ($numQuartos > 0){ ?>
                                    <div class="card card-sucesso">
                                        <div class="card-content">
                                            <p class="rooms-info">Encontramos <?= $numQuartos ?> quarto(s) disponível(is) para suas datas e critérios. Selecione um para continuar.</p>
                                        </div>
                                    </div>
                                    <div class="rooms-container">
                                        <?php while($quarto = mysqli_fetch_assoc($resultado)){ ?>
                                            <label class="room-option">
                                                <input type="radio" name="idQuarto" value="<?= $quarto['id'] ?>" required>
                                                <div class="room-option-content">
                                                    <div class="room-option-header">
                                                        <h3><?= htmlspecialchars($quarto['nome']) ?></h3>
                                                        <span class="room-option-category"><?= htmlspecialchars($quarto['nomeCategoria']) ?></span>
                                                    </div>
                                                    <div class="room-option-details">
                                                        <span><i class="bi bi-people-fill"></i> Até <?= $quarto['ocupacao'] ?> hóspedes</span>
                                                        <span class="room-option-price">R$ <?= number_format($quarto['valorDiaria'], 2, ',', '.') ?> / noite</span>
                                                    </div>
                                                </div>
                                            </label>
                                        <?php } ?>
                                    </div>
                                    <button type="submit" class="btn-submit" name="pagamento">Continuar para Pagamento</button>
                                <?php }else{ ?>
                                    <div class="card card-erro">
                                        <div class="card-content">
                                            <p class="rooms-info">Nenhum quarto encontrado com os critérios selecionados. Por favor, tente outras datas ou categorias.</p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                <?php } ?>

            </div>
        </div>
    </main>

    <?php include_once "include/footer.php" ?>
    <script src="js/cards.js"></script>
</body>

</html>