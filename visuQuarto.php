<?php
require_once "include/conexao.php";
require_once "include/verifSessionFuncionario.php";

$idQuarto = $_GET['id'];

$sql = "SELECT q.*, c.nome as nomeCategoria 
        FROM tbquarto q 
        JOIN tbcategoria c ON q.fkCategoria = c.id 
        WHERE q.id = ?";

$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $idQuarto);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$linha = mysqli_fetch_assoc($resultado);

if (!$linha) {
    header("Location: listaQuarto.php");
    exit();
}

$statusOptions = ['disponivel', 'ocupado', 'inativo'];

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualização de Quarto - Jardim Secreto</title>
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
                        <h1>Visualizar Quarto</h1>
                        <p>Visualize os dados do quarto selecionado</p>
                    </div>

                    <form class="cadastro-form">
                        <div class="form-section">
                            <h2>Dados do Quarto</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" id="nome" value="<?= htmlspecialchars($linha['nome']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="numero">Número</label>
                                    <input type="text" id="numero" value="<?= htmlspecialchars($linha['numero']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="ocupacao">Ocupação Máxima</label>
                                    <input type="text" id="ocupacao" value="<?= htmlspecialchars($linha['ocupacao']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="area">Área (m²)</label>
                                    <input type="text" id="area" value="<?= htmlspecialchars($linha['area']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="valorDiaria">Valor da Diária (R$)</label>
                                    <input type="text" id="valorDiaria" value="<?= htmlspecialchars(number_format($linha['valorDiaria'], 2, ',', '.')); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="categoria">Categoria</label>
                                    <input type="text" id="categoria" value="<?= htmlspecialchars($linha['nomeCategoria']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <input type="text" id="status" value="<?= ucfirst(htmlspecialchars($linha['status'])); ?>" disabled>
                                </div>
                                <div class="form-group full-width">
                                    <label for="descricao">Descrição</label>
                                    <input type="text" id="descricao" value="<?= htmlspecialchars($linha['descricao']); ?>" disabled>
                                </div>
                                <div class="form-group full-width">
                                    <label>Imagem Atual</label>
                                    <img src="<?= htmlspecialchars($linha['caminhoImg']); ?>" alt="Imagem do quarto" style="max-width: 200px; border-radius: 8px; margin-top: 8px;">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cadastrar" onclick="window.location.href = 'listaQuarto.php'">Voltar</button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
