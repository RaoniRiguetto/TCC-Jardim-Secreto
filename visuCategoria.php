<?php
require_once "include/conexao.php";
require_once "include/validacoes.php";
require_once "include/verifSessionFuncionario.php";

$idCategoria = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idCategoria <= 0) {
    header("Location: listaCategoria.php");
    exit();
}

$sql = "SELECT * FROM tbcategoria WHERE id = ?";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $idCategoria);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$linha = mysqli_fetch_assoc($resultado);
if (!$linha) {
    header("Location: listaCategoria.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualização de Categoria - Jardim Secreto</title>
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
                        <h1>Visualizar Categoria</h1>
                        <p>Visualize os dados da categoria selecionada</p>
                    </div>

                    <?php if (!empty($mensagemErro)){ ?>
                        <div class="card card-erro-flutuante">
                            <div class="card-content">
                                <?= htmlspecialchars($mensagemErro); ?>
                            </div>
                        </div>
                    <?php } ?>

                    <form class="cadastro-form" method="POST">

                        <div class="form-section">
                            <h2>Dados da Categoria</h2>
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="nome">Nome da Categoria *</label>
                                    <input type="text" name="nome" id="nome" placeholder="Ex: Standard, Luxo, Premium" required value="<?php echo htmlspecialchars($linha['nome'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Status *</label>
                                    <input type="text" value="<?= ($linha['status'] == 1) ? 'Ativo' : 'Inativo'; ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" name="cancelar" class="btn-cadastrar" onclick="window.location.href = 'listaCategoria.php'">Voltar</button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="js/validacoes.js"></script>
    <script src="js/cards.js"></script>
</body>

</html>