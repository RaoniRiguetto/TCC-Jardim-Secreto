<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<header class="header">
    <nav class="navbar">
        <div class="logo">Jardim Secreto</div>
        <ul class="nav-links">
            <li>
                <a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                    Início
                </a>
            </li>
            <li>
                <a href="reserva.php" class="nav-link <?php echo $current_page == 'reserva.php' ? 'active' : ''; ?>">
                    Reservas
                </a>
            </li>
            <li>
                <a href="acomodacoes.php" class="nav-link <?php echo $current_page == 'acomodacoes.php' ? 'active' : ''; ?>">
                    Acomodações
                </a>
            </li>
        </ul>

        <?php if(isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "funcionario"){ ?>
        <div class="profile-compact">
            <span class="profile-name"><?php echo htmlspecialchars($_SESSION['nome']); ?></span>
            <button class="btn-painel" onclick="window.location.href='painelFuncionario.php'">Painel</button>
        </div>

        <?php } elseif(isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "usuario"){ ?>
        <div class="profile-compact">
            <span class="profile-name"><?php echo htmlspecialchars($_SESSION['nome']); ?></span>
            <button class="btn-painel" onclick="window.location.href='painelUsuario.php'">Painel</button>
        </div>
        <?php } else{ ?>
            <a href="login.php" class="btn-login">Login</a>
        <?php } ?>
    </nav>
</header>