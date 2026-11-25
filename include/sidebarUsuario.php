<?php
include_once("verifSessionUsuario.php");

$nome = $_SESSION['nome'];
$email = $_SESSION['email'];
$current_page = basename($_SERVER['PHP_SELF']);

?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="info">
            <div class="details">
                <h3><?php echo htmlspecialchars($nome); ?></h3>
                <p><?php echo htmlspecialchars($email); ?></p>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="painelUsuario.php" class="nav-item <?php echo $current_page == 'painelUsuario.php' ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="perfilUsuario.php" class="nav-item <?php echo $current_page == 'perfilUsuario.php' ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="bi bi-person-circle"></i></span>
                    <span>Perfil</span>
                </a>
            </li>
            <li>
                <a href="index.php" class="nav-item">
                    <span class="nav-icon"><i class="bi bi-house-door"></i></span>
                    <span>Site Principal</span>
                </a>
            </li>
            <li>
                <a href="include/sair.php" class="nav-item logout" onclick="return confirm('Deseja fazer logout?')">
                    <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span>
                    <span>Sair</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<script src="js/dropdown.js"></script>