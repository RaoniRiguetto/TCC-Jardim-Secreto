<?php
require_once "include/conexao.php";
require_once "include/verifSessionFuncionario.php";

$msgExclusao = $_SESSION['msgExclusao'] ?? '';
$pesquisaInput = trim($_POST['pesquisaInput'] ?? '');

unset($_SESSION['msgExclusao']);

if (isset($_GET['id'])) {
    $idFunc = intval($_GET['id']);
    $sql = "UPDATE tbfuncionario SET status = 0 WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idFunc);
    mysqli_stmt_execute($stmt);
    $_SESSION['msgExclusao'] = "Inativação realizada com sucesso.";
    header("Location: listaFuncionario.php");
    exit();
}

if ($pesquisaInput !== '') {
    $sqlFuncionarios = "SELECT * FROM tbfuncionario WHERE nome LIKE ? ORDER BY status DESC";
    $stmtFunc = mysqli_prepare($conexao, $sqlFuncionarios);
    $like = "%" . $pesquisaInput . "%";
    mysqli_stmt_bind_param($stmtFunc, "s", $like);
    mysqli_stmt_execute($stmtFunc);
    $resultadoFuncionarios = mysqli_stmt_get_result($stmtFunc);
} else {
    $sqlFuncionarios = "SELECT * FROM tbfuncionario ORDER BY status DESC";
    $resultadoFuncionarios = mysqli_query($conexao, $sqlFuncionarios);
}

function formatarTelefone($telefone){
    if (strlen($telefone) <= 10) {
        $telefone = preg_replace('/(\d{2})(\d{4})(\d{0,4})/', '($1) $2-$3', $telefone);
    } else {
        $telefone = preg_replace('/(\d{2})(\d{5})(\d{0,4})/', '($1) $2-$3', $telefone);
    }
    return $telefone;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Funcionários - Jardim Secreto</title>
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
                    <h1>Lista de Funcionários</h1>
                    <p>Gerencie os funcionários cadastrados no sistema</p>
                </div>
                <div class="header-right">
                    <span class="current-date" id="currentDate"></span>
                </div>
            </header>

            <div class="listagem-container">
                
                <div class="listagem-actions">
                    <form method="POST" class="listagem-search">
                        <input type="text" placeholder="Buscar funcionário..." name="pesquisaInput">
                        <button type="submit" class="btn-search" name="pesquisaBtn"><i class="bi bi-search"></i></button>
                    </form>
                    <button class="btn-novo" onclick="window.location.href='cadFuncionario.php'"><i class="bi bi-plus-circle"></i> Novo Funcionário</button>
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
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Telefone</th>
                                <th>Data Admissão</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php if(mysqli_num_rows($resultadoFuncionarios) > 0){ ?>
                                <?php while($linha = mysqli_fetch_assoc($resultadoFuncionarios)){ ?>
                                <tr>

                                    <td><?php echo htmlspecialchars($linha['id']); ?></td>
                                    <td><?php echo htmlspecialchars($linha['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($linha['email']); ?></td>
                                    <td><?php echo htmlspecialchars(formatarTelefone($linha['telefone'])); ?></td>
                                    <td><?php echo htmlspecialchars($linha['dataAdmissao']); ?></td>

                                    <td>
                                        <span class="status-badge 
                                            <?php 
                                                switch ($linha['status']) {
                                                    case '1':
                                                        echo "status-ativo";
                                                        break;

                                                    case '0':
                                                        echo "status-inativo";
                                                        break;

                                                }
                                            ?>
                                        ">

                                            <?php 
                                                switch ($linha['status']) {
                                                    case '1':
                                                        echo 'Ativo';
                                                        break;

                                                    case '0':
                                                        echo 'Inativo';
                                                        break;

                                                }
                                            ?>
                                        </span>
                                    </td>

                                    <td class="listagem-actions-cell">
                                        <button class="btn-acao btn-editar" title="Editar" onclick="window.location.href = 'altFuncionario.php?id=<?php echo $linha['id']; ?>'"><i class="bi bi-pencil"></i></button>
                                        <button class="btn-acao btn-visualizar" title="Visualizar" onclick="window.location.href = 'visuFuncionario.php?id=<?php echo $linha['id']; ?>'"><i class="bi bi-eye"></i></button>
                                        <?php if ($linha['status'] == '1'){?><button class="btn-acao btn-excluir" title="Desativar" onclick="if(confirm('Tem certeza que deseja desativar este funcionário?')) { window.location.href = 'listaFuncionario.php?id=<?php echo $linha['id']; ?>' }"><i class="bi bi-trash"></i></button><?php } ?>
                                    </td>

                                </tr>
                                <?php } ?>

                            <?php }else{ ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: var(--size-8);">
                                        <p style="color: var(--text-light);">Nenhum funcionário cadastrado.</p>
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