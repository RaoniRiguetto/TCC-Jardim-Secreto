<?php
require_once "include/conexao.php";
require_once "include/verifSessionFuncionario.php";

$hoje = new DateTime();
$msgCheck = $_SESSION['msgCheck'] ?? "";
unset($_SESSION['msgCheck']);

if (isset($_GET['id'], $_GET['acao'], $_GET['idQuarto']) && $_GET['acao'] == 'checkin') {

    // ao fazer check-in ou check-out, atualiza a reserva e libera/ocupa o quarto junto
    $idRes = intval($_GET['id']);
    $idQrt = intval($_GET['idQuarto']);
    $stmt = mysqli_prepare($conexao, "UPDATE tbreserva SET status = 'hospedado' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $idRes);
    mysqli_stmt_execute($stmt);
    $stmtQ = mysqli_prepare($conexao, "UPDATE tbquarto SET status = 'ocupado' WHERE id = ?");
    mysqli_stmt_bind_param($stmtQ, "i", $idQrt);
    mysqli_stmt_execute($stmtQ);
    $_SESSION['msgCheck'] = "Check-in realizado com sucesso.";
    header("Location: painelFuncionario.php");
    exit();

}

if (isset($_GET['id'], $_GET['acao'], $_GET['idQuarto']) && $_GET['acao'] == 'checkout') {

    $idRes = intval($_GET['id']);
    $idQrt = intval($_GET['idQuarto']);
    $stmt = mysqli_prepare($conexao, "UPDATE tbreserva SET status = 'concluido' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $idRes);
    mysqli_stmt_execute($stmt);
    $stmtQ = mysqli_prepare($conexao, "UPDATE tbquarto SET status = 'disponivel' WHERE id = ?");
    mysqli_stmt_bind_param($stmtQ, "i", $idQrt);
    mysqli_stmt_execute($stmtQ);
    $_SESSION['msgCheck'] = "Check-out realizado com sucesso.";
    header("Location: painelFuncionario.php");
    exit();

}
$sqlCat = "SELECT * FROM tbcategoria  WHERE status != 0 ORDER BY id";
$resultadoCat = mysqli_query($conexao, $sqlCat);

$sqlResCheckin = "SELECT * FROM tbreserva WHERE status = 'reservado' AND dataChekIn = '" . $hoje->format('Y-m-d') . "' ORDER BY id";
$resultadoResCheckin = mysqli_query($conexao, $sqlResCheckin);

$sqlResCheckout = "SELECT * FROM tbreserva WHERE status = 'hospedado' AND dataChekOut = '" . $hoje->format('Y-m-d') . "' ORDER BY id";
$resultadoResCheckout = mysqli_query($conexao, $sqlResCheckout);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/painelFuncionario.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body> 
    <div class="dashboard-container">

        <?php include_once('include/sidebarFuncionario.php'); ?>

        <main class="main-content">

            <header class="header">
                <div class="header-left">
                    <h1>Painel Administrativo</h1>
                    <p>Bem-vindo, <?= $_SESSION['nome']; ?></p>
                </div>
                <div class="header-right">
                    <span class="current-date" id="currentDate"></span>
                </div>
            </header>

            <div class="grid">
                <?php if (!empty($msgCheck)){ ?>
                        <div class="card card-sucesso-flutuante">
                            <div class="card-content">
                                <?= htmlspecialchars($msgCheck); ?>
                            </div>
                        </div>
                    <?php } ?>
                <section class="card-painel">


                    <div class="card-header">
                        <h2>Check-ins de Hoje</h2>
                    </div>

                    <div class="reservas-list">
                    <?php while($linhaResCheckin = mysqli_fetch_array($resultadoResCheckin)){?>
                        <?php
                            $sqlUsuarioChekin = "SELECT * FROM tbusuario WHERE id = " . $linhaResCheckin['fkUsuario'];
                            $resultadoUsuarioChekin = mysqli_query($conexao, $sqlUsuarioChekin);
                            $linhaUsuarioCheckin = mysqli_fetch_array($resultadoUsuarioChekin);

                            $sqlQuartoChekin = "SELECT * FROM tbquarto WHERE id = " . $linhaResCheckin['fkQuarto'];
                            $resultadoQuartoChekin = mysqli_query($conexao, $sqlQuartoChekin);
                            $linhaQuartoCheckin = mysqli_fetch_array($resultadoQuartoChekin);
                        ?>
                        <div class="reserva-item">
                            <div class="reserva-info">
                                <h3><?= htmlspecialchars($linhaUsuarioCheckin['nome']); ?></h3>
                                <span><?= htmlspecialchars($linhaQuartoCheckin['nome']);?> Nº <?= htmlspecialchars($linhaQuartoCheckin['numero']);?></span>
                            </div>
                            <div class="reserva-status">
                                <a href="painelFuncionario.php?id=<?= $linhaResCheckin['id']; ?>&acao=checkin&idQuarto=<?= $linhaResCheckin['fkQuarto']; ?>"><span class="status confirmado">Fazer Check-in</span></a>
                            </div>
                        </div>
                    <?php } ?>
                    </div>

                </section>

                <section class="card-painel">

                    <div class="card-header">
                        <h2>Check-outs de Hoje</h2>
                    </div>

                    <div class="reservas-list">

                        <?php while($linhaResCheckout = mysqli_fetch_array($resultadoResCheckout)){?>
                        <?php
                            $sqlUsuarioCheckout = "SELECT * FROM tbusuario WHERE id = " . $linhaResCheckout['fkUsuario'];
                            $resultadoUsuarioCheckout = mysqli_query($conexao, $sqlUsuarioCheckout);
                            $linhaUsuarioCheckout = mysqli_fetch_array($resultadoUsuarioCheckout);

                            $sqlQuartoCheckout = "SELECT * FROM tbquarto WHERE id = " . $linhaResCheckout['fkQuarto'];
                            $resultadoQuartoCheckout = mysqli_query($conexao, $sqlQuartoCheckout);
                            $linhaQuartoCheckout = mysqli_fetch_array($resultadoQuartoCheckout);
                        ?>
                        <div class="reserva-item">
                            <div class="reserva-info">
                                <h3><?= htmlspecialchars($linhaUsuarioCheckout['nome']); ?></h3>
                                <span><?= htmlspecialchars($linhaQuartoCheckout['nome']);?> Nº <?= htmlspecialchars($linhaQuartoCheckout['numero']);?></span>
                            </div>
                            <div class="reserva-status">
                                <a href="painelFuncionario.php?id=<?= $linhaResCheckout['id']; ?>&acao=checkout&idQuarto=<?= $linhaResCheckout['fkQuarto']; ?>"><span class="status confirmado">Fazer Check-out</span></a>
                            </div>
                        </div>
                    <?php } ?>
                    </div>

                </section>

            </div>
            <section class="card-painel">

                    <div class="card-header">
                        <div class="occupancy-header">
                            <h2>Ocupação em Tempo Real</h2>
                            <?php

                                $sql = "SELECT * FROM tbquarto WHERE status != 'inativo'";
                                $resultado = mysqli_query($conexao, $sql);
                                $numQuartosTotal = mysqli_num_rows($resultado);

                                $sql = "SELECT * FROM tbquarto WHERE status = 'ocupado'";
                                $resultado = mysqli_query($conexao, $sql);
                                $numQuartosTotalOcupados = mysqli_num_rows($resultado);

                                $porcentOcupacao = 0;
                                if ($numQuartosTotal > 0) {
                                    $porcentOcupacao = ($numQuartosTotalOcupados / $numQuartosTotal) * 100;
                                }

                            ?>
                            <div class="occupancy-summary">
                                <span class="occupancy-total"><?= $numQuartosTotalOcupados . "/" . $numQuartosTotal ?></span>
                                <span class="occupancy-rate"><?= number_format($porcentOcupacao, 2) . "%"?></span>
                            </div>
                        </div>
                    </div>

                    <div class="occupancy-chart">
                        <?php

                            $sqlCat = "SELECT * FROM tbcategoria ORDER BY id";
                            $resultadoCat = mysqli_query($conexao, $sqlCat);

                            if (mysqli_num_rows($resultadoCat) == 0) {
                                echo "<p>Nenhuma categoria cadastrada.</p>";
                            } else {
                                while($linhaCat = mysqli_fetch_array($resultadoCat)) {
                                
                                $sqlQuartosCategoria = "SELECT * FROM tbquarto WHERE status != 'inativo' AND fkCategoria = " . $linhaCat['id'];
                                $resultadoQuartosCategoria = mysqli_query($conexao, $sqlQuartosCategoria);
                                $numQuartosTotalCategoria = mysqli_num_rows($resultadoQuartosCategoria);
                            
                                $sqlQuartosOcupadosCategoria = "SELECT * FROM tbquarto WHERE status = 'ocupado' AND fkCategoria = " . $linhaCat['id'];
                                $resultadoQuartosOcupadosCategoria = mysqli_query($conexao, $sqlQuartosOcupadosCategoria);
                                $numQuartosOcupadoCategoria = mysqli_num_rows($resultadoQuartosOcupadosCategoria);
                                
                                $porcentOcupacaoCategoria = 0;
                                if ($numQuartosTotalCategoria > 0) {
                                    $porcentOcupacaoCategoria = ($numQuartosOcupadoCategoria / $numQuartosTotalCategoria) * 100;
                                }
                        ?>
                        <div class="chart-bar">
                            <div class="bar-label"><?= $linhaCat['nome']; ?></div>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: <?= number_format($porcentOcupacaoCategoria, 2) . "%" ?>"></div>
                            </div>
                            <span class="bar-value"><?= $numQuartosOcupadoCategoria . "/" . $numQuartosTotalCategoria ?></span>
                        </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                    
                </section>
        </main>
    </div>

    <script src="js/dataMenu.js"></script>
    <script src="js/cards.js"></script>
</body>
</html>