<?php
include_once("verifSessionFuncionario.php");

$nome = $_SESSION['nome'];
$email = $_SESSION['email'];
$current_page = basename($_SERVER['PHP_SELF']);

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <a href="painelFuncionario.php" class="nav-item <?php echo $current_page == 'painelFuncionario.php' ? 'active' : ''; ?>">
                    <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="dropdown <?php echo in_array($current_page, ['cadFuncionario.php', 'cadQuarto.php', 'cadCategoria.php', 'cadUsuario.php']) ? 'active' : ''; ?>">
                <div class="dropdown-header">
                    <span class="nav-icon"><i class="bi bi-person-plus"></i></span>
                    <span>Cadastros</span>
                    <span class="dropdown-arrow"><i class="bi bi-caret-down"></i></span>
                </div>
                <ul class="dropdown-menu">

                    <li>
                        <a href="cadUsuario.php" class="nav-item <?php echo $current_page == 'cadUsuario.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="bi bi-person"></i></span>
                            <span>Usuários</span>
                        </a>
                    </li>
                    <li>
                        <a href="cadFuncionario.php" class="nav-item <?php echo $current_page == 'cadFuncionario.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="bi bi-person-gear"></i></span>
                            <span>Funcionários</span>
                        </a>
                    </li>
                    <li>
                        <a href="cadQuarto.php" class="nav-item <?php echo $current_page == 'cadQuarto.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fa-solid fa-bed"></i></span>
                            <span>Quartos</span>
                        </a>
                    </li>
                    <li>
                        <a href="cadCategoria.php" class="nav-item <?php echo $current_page == 'cadCategoria.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="bi bi-card-list"></i></span>
                            <span>Categorias</span>
                        </a>
                    </li>

                </ul>
            </li>

            <li class="dropdown <?php echo in_array($current_page, ['listaReserva.php', 'listaUsuario.php', 'listaFuncionario.php', 'listaQuarto.php', 'listaCategoria.php']) ? 'active' : ''; ?>">
                <div class="dropdown-header">
                    <span class="nav-icon"><i class="bi bi-list-ul"></i></span>
                    <span>Listagens</span>
                    <span class="dropdown-arrow"><i class="bi bi-caret-down"></i></span>
                </div>
                <ul class="dropdown-menu">

                    <li>
                        <a href="listaReserva.php" class="nav-item <?php echo $current_page == 'listaReserva.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="bi bi-calendar"></i></span>
                            <span>Reservas</span>
                        </a>
                    </li>
                    <li>
                        <a href="listaUsuario.php" class="nav-item <?php echo $current_page == 'listaUsuario.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="bi bi-person"></i></span>
                            <span>Usuários</span>
                        </a>
                    </li>
                    <li>
                        <a href="listaFuncionario.php" class="nav-item <?php echo $current_page == 'listaFuncionario.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="bi bi-person-gear"></i></span>
                            <span>Funcionários</span>
                        </a>
                    </li>
                    <li>
                        <a href="listaQuarto.php" class="nav-item <?php echo $current_page == 'listaQuarto.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="fa-solid fa-bed"></i></span>
                            <span>Quartos</span>
                        </a>
                    </li>
                    <li>
                        <a href="listaCategoria.php" class="nav-item <?php echo $current_page == 'listaCategoria.php' ? 'active' : ''; ?>">
                            <span class="nav-icon"><i class="bi bi-card-list"></i></span>
                            <span>Categorias</span>
                        </a>
                    </li>

                </ul>
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