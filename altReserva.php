<?php
require_once "include/conexao.php";
require_once "include/validacoes.php";
require_once "include/verifSessionFuncionario.php";

$validacao = true;

$mensagensErro = $_SESSION['mensagensErro'] ?? [];
$oldInput = $_SESSION['oldInput'] ?? [];

$idReserva = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idReserva <= 0 && !isset($_POST['salvar'])) {
    header("Location: listaReserva.php");
    exit();
}

unset($_SESSION['mensagensErro']);
unset($_SESSION['oldInput']);

if (isset($_POST['salvar'])) {
    $idReservaPost = $_POST['idReserva'];
    $status = $_POST['status'];

    $erros = [];

    if (!verifVazio($status)) {
        $validacao = false;
        $erros[] = "O campo Status é obrigatório.";
    }

    if ($validacao) {
        $sql = "UPDATE tbreserva SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status, $idReservaPost);
        mysqli_stmt_execute($stmt);
        $_SESSION['msgExclusao'] = "Reserva alterada com sucesso!";
        header("Location: listaReserva.php");
        exit();
    } else {
        $_SESSION['mensagensErro'] = $erros;
        header("Location: altReserva.php?id=" . $idReservaPost);
        exit();
    }
}

$sqlReserva = "SELECT * FROM tbreserva WHERE id = ?";
$stmtRes = mysqli_prepare($conexao, $sqlReserva);
mysqli_stmt_bind_param($stmtRes, "i", $idReserva);
mysqli_stmt_execute($stmtRes);
$resultadoReserva = mysqli_stmt_get_result($stmtRes);
$linha = mysqli_fetch_assoc($resultadoReserva);

if (!$linha) {
    header("Location: listaReserva.php");
    exit();
}

$sqlUsuario = "SELECT nome FROM tbusuario WHERE id = ?";
$stmtUsr = mysqli_prepare($conexao, $sqlUsuario);
mysqli_stmt_bind_param($stmtUsr, "i", $linha['fkUsuario']);
mysqli_stmt_execute($stmtUsr);
$resultadoUsuario = mysqli_stmt_get_result($stmtUsr);
$usuario = mysqli_fetch_assoc($resultadoUsuario);

$sqlQuartos = "SELECT id, nome, numero FROM tbquarto WHERE status != 'inativo' ORDER BY numero";
$resultadoQuartos = mysqli_query($conexao, $sqlQuartos);

$statusOptions = ['reservado', 'hospedado', 'concluido', 'cancelado'];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alteração de Reserva - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/painelFuncionario.css">
    <link rel="stylesheet" href="css/cadastro.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>

<body>
    <div class="dashboard-container">

        <?php include_once('include/sidebarFuncionario.php'); ?>

        <main class="main-content">
            <div class="cadastro-container-form">
                <div class="cadastro-card">

                    <div class="cadastro-header">
                        <h1>Alterar Reserva</h1>
                        <p>Modifique os dados da reserva para o cliente: <strong><?= htmlspecialchars($usuario['nome'] ?? 'Não encontrado'); ?></strong></p>
                    </div>

                    <?php if (!empty($mensagensErro)) { ?>
                        <div class="card card-erro-flutuante">
                            <div class="card-content">
                                <?php foreach ($mensagensErro as $erro) { ?>
                                    <p><?= htmlspecialchars($erro); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <form class="cadastro-form" method="POST">
                        <input type="hidden" name="idReserva" value="<?= htmlspecialchars($linha['id']); ?>">
                        <div class="form-section">
                            <h2>Dados da Reserva</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="dataCheckIn">Data de Check-in *</label>
                                    <input type="date" name="dataCheckIn" id="dataCheckIn" required value="<?= htmlspecialchars($linha['dataChekIn']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="dataCheckOut">Data de Check-out *</label>
                                    <input type="date" name="dataCheckOut" id="dataCheckOut" required value="<?= htmlspecialchars($linha['dataChekOut']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="qteAdultos">Adultos *</label>
                                    <input type="number" name="qteAdultos" id="qteAdultos" min="1" required value="<?= htmlspecialchars($linha['qteAdultos']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="qteCriancas">Crianças *</label>
                                    <input type="number" name="qteCriancas" id="qteCriancas" min="0" required value="<?= htmlspecialchars($linha['qteCriancas']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="quarto">Quarto *</label>
                                    <select id="quarto" name="quarto" required disabled>
                                        <option value="">Selecione um quarto</option>
                                        <?php while ($quarto = mysqli_fetch_assoc($resultadoQuartos)) {
                                            $selectedQuarto = $linha['fkQuarto'];
                                            $isSelected = ($quarto['id'] == $selectedQuarto) ? 'selected' : '';
                                            echo "<option value='{$quarto['id']}' {$isSelected}>{$quarto['numero']} - " . htmlspecialchars($quarto['nome']) . "</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select id="status" name="status" required>
                                        <option value="">Selecione um status</option>
                                        <?php foreach ($statusOptions as $s) {
                                            $selectedStatus = $linha['status'];
                                            $isSelected = ($s == $selectedStatus) ? 'selected' : '';
                                            echo "<option value='{$s}' {$isSelected}>" . ucfirst($s) . "</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="valor">Valor Total</label>
                                    <input type="text" name="valor" id="valor" value="R$ <?= htmlspecialchars(number_format($linha['valor'], 2, ',', '.')); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="pagamento">Forma de Pagamento</label>
                                    <input type="text" name="pagamento" id="pagamento" value="<?= htmlspecialchars(ucfirst($linha['formaPagamento'])); ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" name="cancelar" class="btn-cancelar" onclick="window.location.href = 'listaReserva.php'">Cancelar</button>
                            <button type="submit" name="salvar" class="btn-cadastrar">Salvar Alterações</button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="js/cards.js"></script>
</body>

</html>
