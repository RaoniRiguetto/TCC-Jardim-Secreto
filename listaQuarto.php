<?php
require_once "include/conexao.php";
require_once "include/verifSessionFuncionario.php";

$msgExclusao = $_SESSION['msgExclusao'] ?? '';
unset($_SESSION['msgExclusao']);

if (isset($_GET['id'])) {
    $idQuarto = intval($_GET['id']);
    $sql = "UPDATE tbquarto SET status = 'inativo' WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idQuarto);
    mysqli_stmt_execute($stmt);
    $_SESSION['msgExclusao'] = "Quarto inativado com sucesso.";
    header("Location: listaQuarto.php");
    exit();
}

$sqlCategoriasFiltro = "SELECT id, nome FROM tbcategoria ORDER BY nome";
$resultadoCategoriasFiltro = mysqli_query($conexao, $sqlCategoriasFiltro);

$pesquisaInput = trim($_POST['pesquisaInput'] ?? '');
$categoriaFiltro = $_POST['categoriaFiltro'] ?? '';
$statusFiltro = $_POST['statusFiltro'] ?? '';

$whereConditions = [];
$params = [];
$types = "";

// monta os filtros só com os campos que foram preenchidos
if ($pesquisaInput !== '') {
    $whereConditions[] = "q.nome LIKE ?";
    $params[] = "%" . $pesquisaInput . "%";
    $types .= "s";
}
if ($categoriaFiltro !== '') {
    $whereConditions[] = "q.fkCategoria = ?";
    $params[] = intval($categoriaFiltro);
    $types .= "i";
}
if ($statusFiltro !== '') {
    $whereConditions[] = "q.status = ?";
    $params[] = $statusFiltro;
    $types .= "s";
}

$whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";

$sqlQuartos = "SELECT q.id, q.nome, q.numero, q.valorDiaria, q.status, c.nome as nomeCategoria
               FROM tbquarto q
               JOIN tbcategoria c ON q.fkCategoria = c.id
               $whereClause
               ORDER BY q.status, q.numero ASC";

$stmtQuartos = mysqli_prepare($conexao, $sqlQuartos);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmtQuartos, $types, ...$params);
}
mysqli_stmt_execute($stmtQuartos);
$resultadoQuartos = mysqli_stmt_get_result($stmtQuartos);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Quartos - Jardim Secreto</title>
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
                    <h1>Lista de Quartos</h1>
                    <p>Gerencie os quartos cadastrados no hotel</p>
                </div>
                <div class="header-right">
                    <span class="current-date" id="currentDate"></span>
                </div>
            </header>

            <div class="listagem-container">

                <div class="listagem-actions">
                    <form method="POST" class="listagem-search">
                        <div class="search-group">
                            <input type="text" placeholder="Buscar por nome do quarto..." name="pesquisaInput" value="<?= htmlspecialchars($pesquisaInput) ?>">
                            <select name="categoriaFiltro">
                                <option value="">Todas as Categorias</option>
                                <?php while ($cat = mysqli_fetch_assoc($resultadoCategoriasFiltro)) : ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($categoriaFiltro == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nome']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <select name="statusFiltro">
                                <option value="">Todos os Status</option>
                                <option value="disponivel" <?= ($statusFiltro == 'disponivel') ? 'selected' : '' ?>>Disponível</option>
                                <option value="ocupado" <?= ($statusFiltro == 'ocupado') ? 'selected' : '' ?>>Ocupado</option>
                                <option value="inativo" <?= ($statusFiltro == 'inativo') ? 'selected' : '' ?>>Inativo</option>
                            </select>
                            <button type="submit" class="btn-search" name="pesquisaBtn"><i class="bi bi-search"></i> Filtrar</button>
                            <a href="listaQuarto.php" class="btn-clear-filter">Limpar Filtros</a>
                        </div>
                    </form>
                    <button class="btn-novo" onclick="window.location.href='cadQuarto.php'"><i class="bi bi-plus-circle"></i> Novo Quarto</button>
                </div>

                <div class="listagem-table-container">

                    <?php if (!empty($msgExclusao)) { ?>
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
                                <th>Número</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Diária</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php if (mysqli_num_rows($resultadoQuartos) > 0) { ?>
                                <?php while ($linha = mysqli_fetch_assoc($resultadoQuartos)) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($linha['id']); ?></td>
                                        <td><?= htmlspecialchars($linha['numero']); ?></td>
                                        <td><?= htmlspecialchars($linha['nome']); ?></td>
                                        <td><?= htmlspecialchars($linha['nomeCategoria']); ?></td>
                                        <td>R$ <?= htmlspecialchars(number_format($linha['valorDiaria'], 2, ',', '.')); ?></td>
                                        <td>
                                            <span class="status-badge 
                                            <?php
                                            switch ($linha['status']) {
                                                case 'disponivel':
                                                    echo "status-ativo";
                                                    break;
                                                case 'ocupado':
                                                    echo "status-hospedado";
                                                    break;
                                                case 'inativo':
                                                    echo "status-inativo";
                                                    break;
                                            }
                                            ?>
                                        ">
                                            <?php
                                            switch ($linha['status']) {
                                                case 'disponivel':
                                                    echo "Disponível";
                                                    break;
                                                case 'ocupado':
                                                    echo "Ocupado";
                                                    break;
                                                case 'inativo':
                                                    echo "Inativo";
                                                    break;
                                            }
                                            ?>
                                            </span>
                                        </td>

                                        <td class="listagem-actions-cell">
                                            <button class="btn-acao btn-editar" title="Editar" onclick="window.location.href = 'altQuarto.php?id=<?= $linha['id']; ?>'"><i class="bi bi-pencil"></i></button>
                                            <button class="btn-acao btn-visualizar" title="Visualizar" onclick="window.location.href = 'visuQuarto.php?id=<?= $linha['id']; ?>'"><i class="bi bi-eye"></i></button>
                                            <?php if ($linha['status'] != 'inativo') { ?><button class="btn-acao btn-excluir" title="Inativar" onclick="if(confirm('Tem certeza que deseja inativar este quarto?')) { window.location.href = 'listaQuarto.php?id=<?= $linha['id']; ?>' }"><i class="bi bi-trash"></i></button><?php } ?>
                                        </td>

                                    </tr>
                                <?php } ?>

                            <?php } else { ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: var(--size-8);">
                                        <p style="color: var(--text-light);">Nenhum quarto encontrado.</p>
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
