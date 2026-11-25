<?php
require_once "include/conexao.php";
require_once "include/validacoes.php";
require_once "include/verifSessionFuncionario.php";

$validacao = true;

$mensagensErro = $_SESSION['mensagensErro'] ?? [];
$oldInput = $_SESSION['oldInput'] ?? [];

$idQuarto = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idQuarto <= 0 && !isset($_POST['salvar'])) {
    header("Location: listaQuarto.php");
    exit();
}

unset($_SESSION['mensagensErro']);
unset($_SESSION['oldInput']);

$sqlCategorias = "SELECT * FROM tbcategoria WHERE status = 1";
$resultadoCategorias = mysqli_query($conexao, $sqlCategorias);

$statusOptions = ['disponivel', 'ocupado', 'inativo'];

$diretorioUpload = "uploads/";
$tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
$tamanhoMaximo = 10 * 1024 * 1024;

if (isset($_POST['salvar'])) {
    $idQuartoPost = $_POST['idQuarto'];
    $nome = $_POST['nome'];
    $numero = $_POST['numero'];
    $ocupacao = preg_replace('/[^0-9]/is', '', $_POST['ocupacao']);
    $area = $_POST['area'];
    $valorDiaria = formatarDecimal($_POST['valorDiaria']);
    $descricao = $_POST['descricao'];
    $categoria = $_POST['categoria'];
    $status = $_POST['status'];

    $erros = [];

    if (!verifVazio($nome) || !verifVazio($numero) || !verifVazio($ocupacao) || !verifVazio($area) || !verifVazio($valorDiaria) || !verifVazio($descricao) || !verifVazio($categoria) || !verifVazio($status)) {
        $erros[] = "Todos os campos com * são obrigatórios.";
        $validacao = false;
    }

    if (!validarApenasNumeros($numero)) {
        $erros[] = "O campo 'Número do Quarto' deve conter apenas números.";
        $validacao = false;
    }
    if (!verifInteiroPositivo($ocupacao, false)) {
        $erros[] = "O campo 'Ocupação Máxima' deve ser um número inteiro positivo.";
        $validacao = false;
    }
    if (!verifInteiroPositivo($area, false)) {
        $erros[] = "O campo 'Área' deve ser um número inteiro positivo.";
        $validacao = false;
    }
    if (!verifFloatPositivo($valorDiaria, false)) {
        $erros[] = "O campo 'Valor da Diária' deve ser um número positivo.";
        $validacao = false;
    }

    // se não mandar foto nova na edição, mantém a que já estava salva
    $caminhoImagem = $_POST['imagemAtual'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $arquivo = $_FILES['foto'];
        if (!in_array($arquivo['type'], $tiposPermitidos)) {
            $erros[] = "Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.";
            $validacao = false;
        }
        if ($arquivo['size'] > $tamanhoMaximo) {
            $erros[]  = "Arquivo muito grande. Tamanho máximo: 10MB.";
            $validacao = false;
        }

        if ($validacao) {
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $novoNome = uniqid('img_') . '_' . date('YmdHis') . '.' . $extensao;
            $caminhoDestino = $diretorioUpload . $novoNome;

            if (move_uploaded_file($arquivo['tmp_name'], $caminhoDestino)) {
                $caminhoImagem = $caminhoDestino;
            } else {
                $erros[] = "Erro ao processar o upload da nova imagem.";
                $validacao = false;
            }
        }
    }

    if ($validacao) {
        $sql = "UPDATE tbquarto SET 
                    nome = ?, numero = ?, ocupacao = ?, area = ?, valorDiaria = ?, 
                    descricao = ?, caminhoImg = ?, fkCategoria = ?, status = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ssiidssisi", $nome, $numero, $ocupacao, $area, $valorDiaria, $descricao, $caminhoImagem, $categoria, $status, $idQuartoPost);
        mysqli_stmt_execute($stmt);

        $_SESSION['msgExclusao'] = "Quarto alterado com sucesso!";
        header("Location: listaQuarto.php");
        exit();
    } else {
        $_SESSION['mensagensErro'] = $erros;
        $_SESSION['oldInput'] = $_POST;
        header("Location: altQuarto.php?id=" . $idQuartoPost);
        exit();
    }
}

$sqlQuarto = "SELECT * FROM tbquarto WHERE id = ?";
$stmtQuarto = mysqli_prepare($conexao, $sqlQuarto);
mysqli_stmt_bind_param($stmtQuarto, "i", $idQuarto);
mysqli_stmt_execute($stmtQuarto);
$resultadoQuarto = mysqli_stmt_get_result($stmtQuarto);
$linha = mysqli_fetch_assoc($resultadoQuarto);

if (!$linha) {
    header("Location: listaQuarto.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alteração de Quarto - Jardim Secreto</title>
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
                        <h1>Alterar Quarto</h1>
                        <p>Modifique os dados do quarto selecionado</p>
                    </div>

                    <?php if (!empty($mensagensErro)) { ?>
                        <div class="card card-erro-flutuante">
                            <div class="card-content">
                                <?php foreach ($mensagensErro as $erro) { ?>
                                    <p><?= htmlspecialchars($erro); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <form class="cadastro-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="idQuarto" value="<?= htmlspecialchars($linha['id']); ?>">
                        <input type="hidden" name="imagemAtual" value="<?= htmlspecialchars($linha['caminhoImg']); ?>">

                        <div class="form-section">
                            <h2>Dados do Quarto</h2>
                            <div class="form-grid">
                                <div class="form-group"><label for="nome">Nome *</label><input type="text" name="nome" id="nome" required value="<?= htmlspecialchars($oldInput['nome'] ?? $linha['nome']); ?>"></div>
                                <div class="form-group"><label for="numero">Número *</label><input type="text" name="numero" id="numero" required value="<?= htmlspecialchars($oldInput['numero'] ?? $linha['numero']); ?>"></div>
                                <div class="form-group"><label for="ocupacao">Ocupação Máxima *</label><input type="number" name="ocupacao" id="ocupacao" min="1" required value="<?= htmlspecialchars($oldInput['ocupacao'] ?? $linha['ocupacao']); ?>"></div>
                                <div class="form-group"><label for="area">Área (m²) *</label><input type="number" name="area" id="area" min="1" required value="<?= htmlspecialchars($oldInput['area'] ?? $linha['area']); ?>"></div>
                                <div class="form-group"><label for="valorDiaria">Valor da Diária (R$) *</label><input type="text" name="valorDiaria" id="valorDiaria" oninput="formatarValor(this)" required value="<?= htmlspecialchars($oldInput['valorDiaria'] ?? number_format($linha['valorDiaria'], 2, ',', '.')); ?>"></div>
                                <div class="form-group"><label for="categoria">Categoria *</label>
                                    <select id="categoria" name="categoria" required>
                                        <?php mysqli_data_seek($resultadoCategorias, 0);
                                        while ($cat = mysqli_fetch_assoc($resultadoCategorias)) {
                                            $selected = (($oldInput['categoria'] ?? $linha['fkCategoria']) == $cat['id']) ? 'selected' : '';
                                            echo "<option value='{$cat['id']}' {$selected}>" . htmlspecialchars($cat['nome']) . "</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group"><label for="status">Status *</label>
                                    <select id="status" name="status" required>
                                        <?php foreach ($statusOptions as $s) {
                                            $selected = (($oldInput['status'] ?? $linha['status']) == $s) ? 'selected' : '';
                                            echo "<option value='{$s}' {$selected}>" . ucfirst($s) . "</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group full-width"><label for="descricao">Descrição *</label><input type="text" name="descricao" id="descricao" required value="<?= htmlspecialchars($oldInput['descricao'] ?? $linha['descricao']); ?>"></div>
                                <div class="form-group full-width"><label for="foto">Foto do Quarto</label><input type="file" name="foto" id="foto" accept="image/*"><small>Envie uma nova imagem para substituí-la. Caso contrário, a atual será mantida.</small></div>
                                <div class="form-group full-width"><label>Imagem Atual</label><img src="<?= htmlspecialchars($linha['caminhoImg']); ?>" alt="Imagem atual do quarto" style="max-width: 200px; border-radius: 8px; margin-top: 8px;"></div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" name="cancelar" class="btn-cancelar" onclick="window.location.href = 'listaQuarto.php'">Cancelar</button>
                            <button type="submit" name="salvar" class="btn-cadastrar">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="js/formatacoes.js"></script>
    <script src="js/cards.js"></script>
</body>

</html>
