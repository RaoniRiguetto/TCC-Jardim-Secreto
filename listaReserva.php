<?php
require_once "include/conexao.php";
require_once "include/verifSessionFuncionario.php";

$msgExclusao = $_SESSION['msgExclusao'] ?? '';
unset($_SESSION['msgExclusao']);

if (isset($_GET['id'])) {
    $idReserva = intval($_GET['id']);
    $sql = "UPDATE tbreserva SET status = 'cancelado' WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReserva);
    mysqli_stmt_execute($stmt);
    $_SESSION['msgExclusao'] = "Reserva cancelada com sucesso.";
    header("Location: listaReserva.php");
    exit();
}

$sqlCategoriasFiltro = "SELECT id, nome FROM tbcategoria ORDER BY nome";
$resultadoCategoriasFiltro = mysqli_query($conexao, $sqlCategoriasFiltro);

$whereConditions = [];
$params = [];
$types = "";

$pesquisaInput = trim($_POST['pesquisaInput'] ?? '');
$dataCheckin = $_POST['dataCheckin'] ?? '';
$statusFiltro = $_POST['statusFiltro'] ?? '';
$categoriaFiltro = $_POST['categoriaFiltro'] ?? '';

// junta apenas os filtros preenchidos pra montar o WHERE dinâmico
if ($pesquisaInput !== '') {
    $whereConditions[] = "u.nome LIKE ?";
    $params[] = "%" . $pesquisaInput . "%";
    $types .= "s";
}

if ($dataCheckin !== '') {
    $whereConditions[] = "r.dataChekIn = ?";
    $params[] = $dataCheckin;
    $types .= "s";
}

if ($statusFiltro !== '') {
    $whereConditions[] = "r.status = ?";
    $params[] = $statusFiltro;
    $types .= "s";
}

if ($categoriaFiltro !== '') {
    $whereConditions[] = "q.fkCategoria = ?";
    $params[] = intval($categoriaFiltro);
    $types .= "i";
}

$whereClause = "";
if (count($whereConditions) > 0) {
    $whereClause = "WHERE " . implode(' AND ', $whereConditions);
}

$sqlReservas = "SELECT r.id, r.dataChekIn, r.dataChekOut, r.status, u.nome as nomeUsuario, q.numero as numeroQuarto, c.nome as nomeCategoria
                FROM tbreserva r
                JOIN tbusuario u ON r.fkUsuario = u.id
                JOIN tbquarto q ON r.fkQuarto = q.id
                JOIN tbcategoria c ON q.fkCategoria = c.id
                $whereClause
                ORDER BY r.dataChekIn DESC, r.id DESC";
$stmtReservas = mysqli_prepare($conexao, $sqlReservas);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmtReservas, $types, ...$params);
}
mysqli_stmt_execute($stmtReservas);
$resultadoReservas = mysqli_stmt_get_result($stmtReservas);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Reservas - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/painelFuncionario.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/listagem.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="dashboard-container">
        
        <?php include_once('include/sidebarFuncionario.php'); ?>

        <main class="main-content">

            <header class="header">
                <div class="header-left">
                    <h1>Lista de Reservas</h1>
                    <p>Gerencie as reservas cadastradas no sistema</p>
                </div>
                <div class="header-right">
                    <span class="current-date" id="currentDate"></span>
                </div>
            </header>

            <div class="listagem-container">

                <div class="listagem-actions">
                    <form method="POST" class="listagem-search">
                        <div class="search-group">
                            <input type="text" placeholder="Buscar por nome do cliente..." name="pesquisaInput" value="<?= htmlspecialchars($pesquisaInput) ?>">
                            <select name="categoriaFiltro">
                                <option value="">Todas as Categorias</option>
                                <?php while ($cat = mysqli_fetch_assoc($resultadoCategoriasFiltro)) : ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($categoriaFiltro == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nome']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <input type="date" name="dataCheckin" value="<?= htmlspecialchars($dataCheckin) ?>">
                            <select name="statusFiltro">
                                <option value="">Todos os Status</option>
                                <option value="reservado" <?= ($statusFiltro == 'reservado') ? 'selected' : '' ?>>Reservado</option>
                                <option value="hospedado" <?= ($statusFiltro == 'hospedado') ? 'selected' : '' ?>>Hospedado</option>
                                <option value="concluido" <?= ($statusFiltro == 'concluido') ? 'selected' : '' ?>>Concluído</option>
                                <option value="cancelado" <?= ($statusFiltro == 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                            <button type="submit" class="btn-search" name="pesquisaBtn"><i class="bi bi-search"></i> Filtrar</button>
                            <a href="listaReserva.php" class="btn-clear-filter">Limpar Filtros</a>
                        </div>
                    </form>
                </div>

                <div class="listagem-table-container">

                    <?php if (!empty($msgExclusao)){ ?>
                        <div class="card card-sucesso-flutuante">
                            <div class="card-content">
                                <?= htmlspecialchars($msgExclusao); ?>
                            </div>
                        </div>
                    <?php } ?>
                    
                    <table class="listagem-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Quarto</th>
                                <th>Categoria</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php if(mysqli_num_rows($resultadoReservas) > 0){ ?>
                                <?php while($linha = mysqli_fetch_assoc($resultadoReservas)){ ?>
                                <tr>

                                    <td><?php echo htmlspecialchars($linha['id']); ?></td>
                                    <td><?php echo htmlspecialchars($linha['nomeUsuario']); ?></td>
                                    <td><?php echo htmlspecialchars($linha['numeroQuarto']); ?></td>
                                    <td><?php echo htmlspecialchars($linha['nomeCategoria']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($linha['dataChekIn']))); ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($linha['dataChekOut']))); ?></td>

                                    <td>
                                        <span class="status-badge 
                                            <?php 
                                                switch ($linha['status']) {
                                                    case 'reservado':
                                                        echo "status-ativo";
                                                        break;

                                                    case 'cancelado':
                                                        echo "status-inativo";
                                                        break;

                                                    case 'hospedado':
                                                        echo "status-hospedado";
                                                        break;

                                                    case 'concluido':
                                                        echo "status-concluido";
                                                        break;
                                                }
                                            ?>
                                        ">

                                            <?php 
                                                switch ($linha['status']) {
                                                    case 'reservado':
                                                        echo "Reservado";
                                                        break;

                                                    case 'cancelado':
                                                        echo "Cancelado";
                                                        break;

                                                    case 'hospedado':
                                                        echo "Hospedado";
                                                        break;

                                                    case 'concluido':
                                                        echo "Concluído";
                                                        break;

                                                }
                                            ?>
                                        </span>
                                    </td>

                                    <td class="listagem-actions-cell">
                                        <button class="btn-acao btn-editar" title="Editar" onclick="window.location.href = 'altReserva.php?id=<?php echo $linha['id']; ?>'"><i class="bi bi-pencil"></i></button>
                                        <button class="btn-acao btn-visualizar" title="Visualizar" onclick="window.location.href = 'visuReserva.php?id=<?= $linha['id']; ?>'"><i class="bi bi-eye"></i></button>
                                        <?php if ($linha['status'] != 'cancelado'){?><button class="btn-acao btn-excluir" title="Cancelar Reserva" onclick="if(confirm('Tem certeza que deseja cancelar esta reserva?')) { window.location.href = 'listaReserva.php?id=<?php echo $linha['id']; ?>' }"><i class="bi bi-trash"></i></button><?php } ?>
                                    </td>

                                </tr>
                                <?php } ?>

                            <?php }else{ ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: var(--size-8);">
                                        <p style="color: var(--text-light);">Nenhuma reserva encontrada com os filtros aplicados.</p>
                                    </td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>

                </div>

            </div>
        </main>
    </div>
    <script src="js/dataMenu.js"></script>
    <script src="js/cards.js"></script>
</body>
</html>
