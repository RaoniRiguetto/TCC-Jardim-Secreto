<?php
require_once "include/conexao.php";
require_once "include/verifSessionUsuario.php";

$idUsuario = intval($_SESSION['id']);
$msgReserva = $_SESSION['msgReserva'] ?? '';
unset($_SESSION['msgReserva']);

if (isset($_GET['cancelar'])) {
    $idCancelar = intval($_GET['cancelar']);
    if ($idCancelar > 0) {
        // garante que o cliente só cancele uma reserva dele mesmo e ainda não iniciada
        $sqlCancelar = "UPDATE tbreserva SET status = 'cancelado' WHERE id = ? AND fkUsuario = ? AND status = 'reservado'";
        $stmtCancelar = mysqli_prepare($conexao, $sqlCancelar);
        mysqli_stmt_bind_param($stmtCancelar, "ii", $idCancelar, $idUsuario);
        mysqli_stmt_execute($stmtCancelar);
        if (mysqli_stmt_affected_rows($stmtCancelar) > 0) {
            $_SESSION['msgReserva'] = "Reserva cancelada com sucesso.";
        }
    }
    header("Location: painelUsuario.php");
    exit();
}

$sqlReservas = "SELECT r.*, q.nome as nomeQuarto FROM tbreserva r JOIN tbquarto q ON r.fkQuarto = q.id WHERE r.fkUsuario = ? ORDER BY r.horaReserva DESC";
$stmt = mysqli_prepare($conexao, $sqlReservas);
mysqli_stmt_bind_param($stmt, "i", $idUsuario);
mysqli_stmt_execute($stmt);
$resultadoReservas = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Painel - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/painelUsuario.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="dashboard-container">

        <?php include_once('include/sidebarUsuario.php'); ?>

        <main class="main-content">

            <header class="header">
                <div class="header-left">
                    <h1>Meu Painel</h1>
                    <p>Bem-vindo de volta, <?= htmlspecialchars($_SESSION['nome']) ?>!</p>
                </div>
                <div class="header-actions">
                    <a href="reserva.php" class="btn-new-reservation">Nova Reserva</a>
                </div>
            </header>

            <?php if (!empty($msgReserva)) : ?>
                <div class="card card-sucesso-flutuante">
                    <div class="card-content">
                        <?= htmlspecialchars($msgReserva); ?>
                    </div>
                </div>
            <?php endif; ?>

            <section class="reservations-section">
                <h2>Minhas Reservas</h2>

                <?php if (mysqli_num_rows($resultadoReservas) > 0) : ?>
                    <?php while ($reserva = mysqli_fetch_assoc($resultadoReservas)) : ?>
                        <div class="next-reservation-card status-<?= htmlspecialchars($reserva['status']) ?>">
                            <div class="reservation-info">
                                <div class="reservation-main">
                                    <h3><?= htmlspecialchars($reserva['nomeQuarto']) ?></h3>

                                    <div class="reservation-dates">
                                        <div class="date-item">
                                            <strong>Check-in:</strong>
                                            <span><?= htmlspecialchars(date('d/m/Y', strtotime($reserva['dataChekIn']))) ?></span>
                                        </div>
                                        <div class="date-item">
                                            <strong>Check-out:</strong>
                                            <span><?= htmlspecialchars(date('d/m/Y', strtotime($reserva['dataChekOut']))) ?></span>
                                        </div>
                                    </div>

                                    <div class="reservation-guests">
                                        <strong>Hóspedes:</strong>
                                        <span><?= htmlspecialchars($reserva['qteAdultos']) ?> Adulto(s)<?php if ($reserva['qteCriancas'] > 0) {
                                                                                                            echo " e " . htmlspecialchars($reserva['qteCriancas']) . " Criança(s)";
                                                                                                        } ?></span>
                                    </div>
                                </div>

                                <div class="status <?= htmlspecialchars($reserva['status']) ?>">
                                    <span><?= htmlspecialchars(ucfirst($reserva['status'])) ?></span>
                                </div>
                            </div>

                            <div class="reservation-actions" style="gap: 0.5rem;">
                                <?php if ($reserva['status'] === 'reservado') : ?>
                                    <button class="btn-action" style="color: var(--error); border-color: rgba(220, 53, 69, 0.3);" onclick="if(confirm('Tem certeza que deseja cancelar esta reserva?')) { window.location.href='painelUsuario.php?cancelar=<?= $reserva['id'] ?>'; }">Cancelar Reserva</button>
                                <?php endif; ?>
                                <button class="btn-action" onclick="window.location.href='visuReservaUsuario.php?id=<?= $reserva['id'] ?>'">Ver Detalhes</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="no-reservations-card">
                        <p>Você ainda não possui nenhuma reserva.</p>
                        <a href="reserva.php" class="btn-new-reservation">Fazer minha primeira reserva</a>
                    </div>
                <?php endif; ?>

            </section>
        </main>
    </div>
    <script src="js/cards.js"></script>
</body>
</html>
