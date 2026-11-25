<?php
require_once "include/conexao.php";
require_once "include/validacoes.php";
require_once "include/verifSessionFuncionario.php";

$validacao = true;

$mensagensErro = $_SESSION['mensagensErro'] ?? [];
$oldInput = $_SESSION['oldInput'] ?? [];

$idCategoria = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idCategoria <= 0 && !isset($_POST['cadastrar'])) {
    header("Location: listaCategoria.php");
    exit();
}

unset($_SESSION['mensagensErro']);
unset($_SESSION['oldInput']);

if (isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $status = intval($_POST['status']);
    $erros = [];

    if (!verifVazio($nome)){
        $validacao = false;
        $erros[] = "O campo Nome da Categoria é obrigatório.";
    }

    if($validacao){
        $sql = "UPDATE tbcategoria SET nome = ?, status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "sii", $nome, $status, $idCategoria);
        mysqli_stmt_execute($stmt);
        $_SESSION['msgExclusao'] = "Categoria alterada com sucesso!";
        header("Location: listaCategoria.php");
        exit();
    }else{
        $_SESSION['mensagensErro'] = $erros;
        $_SESSION['oldInput'] = $_POST;
        header("Location: altCategoria.php?id=" . $idCategoria);
        exit();
    }
}
$sql = "SELECT * FROM tbcategoria WHERE id = ?";
$stmtCat = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmtCat, "i", $idCategoria);
mysqli_stmt_execute($stmtCat);
$resultado = mysqli_stmt_get_result($stmtCat);
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
    <title>Alteração de Categoria - Jardim Secreto</title>
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
                        <h1>Alterar Categoria</h1>
                        <p>Modifique os dados da categoria selecionada</p>
                    </div>

                    <?php if (!empty($mensagensErro)){ ?>
                        <div class="card card-erro-flutuante">
                            <div class="card-content">
                                <?php foreach ($mensagensErro as $erro) { ?>
                                    <p><?= htmlspecialchars($erro); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <form class="cadastro-form" method="POST">

                        <div class="form-section">
                            <h2>Dados da Categoria</h2>
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="nome">Nome da Categoria *</label>
                                    <input type="text" name="nome" id="nome" placeholder="Ex: Standard, Luxo, Premium" required value="<?= htmlspecialchars($oldInput['nome'] ?? $linha['nome']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Status *</label>
                                    <select id="status" name="status" required>
                                        <?php $statusAtual = $oldInput['status'] ?? $linha['status']; ?>
                                        <option value="1" <?= ($statusAtual == 1) ? 'selected' : ''; ?>>Ativo</option>
                                        <option value="0" <?= ($statusAtual == 0) ? 'selected' : ''; ?>>Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" name="cancelar" class="btn-cancelar" onclick="window.location.href = 'listaCategoria.php'">Cancelar</button>
                            <button type="submit" name="cadastrar" class="btn-cadastrar">Salvar Alterações</button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="js/cards.js"></script>
</body>

</html>