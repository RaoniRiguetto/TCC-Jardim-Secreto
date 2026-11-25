<?php
require_once "include/conexao.php";
require_once "include/verifSessionFuncionario.php";

$idReserva = $_GET['id'];

$sqlReserva = "SELECT * FROM tbreserva WHERE id = ?";
$stmt = mysqli_prepare($conexao, $sqlReserva);
mysqli_stmt_bind_param($stmt, "i", $idReserva);
mysqli_stmt_execute($stmt);
$resultadoReserva = mysqli_stmt_get_result($stmt);
$linha = mysqli_fetch_assoc($resultadoReserva);

if (!$linha) {
    header("Location: listaReserva.php");
    exit();
}

$sqlUsuario = "SELECT nome FROM tbusuario WHERE id = ?";
$stmtUsuario = mysqli_prepare($conexao, $sqlUsuario);
mysqli_stmt_bind_param($stmtUsuario, "i", $linha['fkUsuario']);
mysqli_stmt_execute($stmtUsuario);
$resultadoUsuario = mysqli_stmt_get_result($stmtUsuario);
$usuario = mysqli_fetch_assoc($resultadoUsuario);

$sqlQuarto = "SELECT nome, numero FROM tbquarto WHERE id = ?";
$stmtQuarto = mysqli_prepare($conexao, $sqlQuarto);
mysqli_stmt_bind_param($stmtQuarto, "i", $linha['fkQuarto']);
mysqli_stmt_execute($stmtQuarto);
$resultadoQuarto = mysqli_stmt_get_result($stmtQuarto);
$quarto = mysqli_fetch_assoc($resultadoQuarto);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Reserva - Jardim Secreto</title>
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
                        <h1>Visualizar Reserva</h1>
                        <p>Dados da reserva para o cliente: <strong><?= htmlspecialchars($usuario['nome'] ?? 'Não encontrado'); ?></strong></p>
                    </div>

                    <form class="cadastro-form">
                        <div class="form-section">
                            <h2>Dados da Reserva</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="dataCheckIn">Data de Check-in</label>
                                    <input type="date" id="dataCheckIn" value="<?= htmlspecialchars($linha['dataChekIn']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="dataCheckOut">Data de Check-out</label>
                                    <input type="date" id="dataCheckOut" value="<?= htmlspecialchars($linha['dataChekOut']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="qteAdultos">Adultos</label>
                                    <input type="number" id="qteAdultos" value="<?= htmlspecialchars($linha['qteAdultos']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="qteCriancas">Crianças</label>
                                    <input type="number" id="qteCriancas" value="<?= htmlspecialchars($linha['qteCriancas']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="quarto">Quarto</label>
                                    <input type="text" id="quarto" value="<?= htmlspecialchars($quarto['numero'] . ' - ' . $quarto['nome']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <input type="text" id="status" value="<?= htmlspecialchars(ucfirst($linha['status'])); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="valor">Valor Total</label>
                                    <input type="text" id="valor" value="R$ <?= htmlspecialchars(number_format($linha['valor'], 2, ',', '.')); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="pagamento">Forma de Pagamento</label>
                                    <input type="text" id="pagamento" value="<?= htmlspecialchars(ucfirst($linha['formaPagamento'])); ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cadastrar" onclick="window.location.href = 'listaReserva.php'">Voltar</button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>