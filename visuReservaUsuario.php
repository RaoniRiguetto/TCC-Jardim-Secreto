<?php
require_once "include/conexao.php";
require_once "include/verifSessionUsuario.php";

$idUsuario = intval($_SESSION['id']);
$idReserva = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idReserva <= 0) {
    header("Location: painelUsuario.php");
    exit();
}

$sqlReserva = "SELECT r.*, q.nome AS nomeQuarto, q.numero AS numeroQuarto
               FROM tbreserva r
               JOIN tbquarto q ON r.fkQuarto = q.id
               WHERE r.id = ? AND r.fkUsuario = ?";
$stmt = mysqli_prepare($conexao, $sqlReserva);
mysqli_stmt_bind_param($stmt, "ii", $idReserva, $idUsuario);
mysqli_stmt_execute($stmt);
$resultadoReserva = mysqli_stmt_get_result($stmt);
$linha = mysqli_fetch_assoc($resultadoReserva);

if (!$linha) {
    header("Location: painelUsuario.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Reserva - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/painelUsuario.css">
    <link rel="stylesheet" href="css/cadastro.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="dashboard-container">

        <?php include_once('include/sidebarUsuario.php'); ?>

        <main class="main-content">
            <div class="cadastro-container-form">
                <div class="cadastro-card">

                    <div class="cadastro-header">
                        <h1>Detalhes da Reserva</h1>
                        <p>Reserva #<?= htmlspecialchars($linha['id']); ?></p>
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
                                    <input type="text" id="quarto" value="<?= htmlspecialchars($linha['numeroQuarto'] . ' - ' . $linha['nomeQuarto']); ?>" disabled>
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
                                    <input type="text" id="pagamento" value="<?= htmlspecialchars(ucfirst($linha['formaPagamento'])); ?><?= ($linha['formaPagamento'] === 'credito' && $linha['qteParcelas'] > 1) ? ' (' . htmlspecialchars($linha['qteParcelas']) . 'x)' : ''; ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancelar" onclick="window.location.href = 'painelUsuario.php'">Voltar</button>
                            <?php if ($linha['status'] === 'reservado') : ?>
                                <button type="button" class="btn-cadastrar" style="background: var(--error);" onclick="if(confirm('Tem certeza que deseja cancelar esta reserva?')) { window.location.href = 'painelUsuario.php?cancelar=<?= $linha['id']; ?>'; }">Cancelar Reserva</button>
                            <?php endif; ?>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>
</body>
</html>
