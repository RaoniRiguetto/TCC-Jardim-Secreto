<?php
require_once "include/conexao.php";
require_once "include/validacoes.php";
require_once "include/verifSessionFuncionario.php";

$validacao = true;

$mensagemErro = $_SESSION['mensagemErro'] ?? "";
$msgCadastrado = $_SESSION['msgCadastrado'] ?? "";
$oldInput = $_SESSION['oldInput'] ?? [];

unset($_SESSION['mensagemErro']);
unset($_SESSION['msgCadastrado']);
unset($_SESSION['oldInput']);

if (isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];

    if (!verifVazio($nome)){
        $validacao = false;
    }

    if($validacao){
        $sql = "INSERT INTO tbcategoria (nome) VALUES(?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "s", $nome);
        mysqli_stmt_execute($stmt);
        $_SESSION['msgCadastrado'] = "Cadastrado com sucesso!";
    }else{
        $_SESSION['mensagemErro'] = "O campo Nome da Categoria é obrigatório.";
        $_SESSION['oldInput'] = $_POST;
    }
    header("Location: cadCategoria.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Categoria - Jardim Secreto</title>
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
                        <h1>Cadastro de Categoria</h1>
                        <p>Defina uma nova categoria de quarto para o hotel</p>
                    </div>

                    <?php if (!empty($mensagemErro)){ ?>
                        <div class="card card-erro-flutuante">
                            <div class="card-content">
                                <?= htmlspecialchars($mensagemErro); ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (!empty($msgCadastrado)){ ?>
                        <div class="card card-sucesso-flutuante">
                            <div class="card-content">
                                <?= htmlspecialchars($msgCadastrado); ?>
                            </div>
                        </div>
                    <?php } ?>

                    <form class="cadastro-form" method="POST">

                        <div class="form-section">
                            <h2>Dados da Categoria</h2>
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="nome">Nome da Categoria *</label>
                                    <input type="text" name="nome" id="nome" placeholder="Ex: Standard, Luxo, Premium" required value="<?php echo htmlspecialchars($oldInput['nome'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" name="cancelar" class="btn-cancelar" onclick="window.location.href = 'painelFuncionario.php'">Cancelar</button>
                            <button type="submit" name="cadastrar" class="btn-cadastrar">Cadastrar Categoria</button>
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