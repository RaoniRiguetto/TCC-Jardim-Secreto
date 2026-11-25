<?php
require_once "include/conexao.php";
require_once "include/validacoes.php";
require_once "include/verifSessionFuncionario.php";

$validacao = true;

$mensagensErro = $_SESSION['mensagensErro'] ?? [];
$msgCadastrado = $_SESSION['msgCadastrado'] ?? "";
$oldInput = $_SESSION['oldInput'] ?? [];

unset($_SESSION['mensagensErro']);
unset($_SESSION['msgCadastrado']);
unset($_SESSION['oldInput']);

$sqlCategorias = "SELECT * FROM tbcategoria WHERE status = 1";
$resultadoCategorias = mysqli_query($conexao, $sqlCategorias);

$diretorioUpload = "uploads/";
$tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
$tamanhoMaximo = 10 * 1024 * 1024;

if (!file_exists($diretorioUpload)) {
    mkdir($diretorioUpload, 0777, true);
}

$caminhoImagem = null;

if (isset($_POST['cadastrar']) && isset($_FILES['foto'])) {
    $nome = $_POST['nome'];
    $numero = $_POST['numero'];
    $ocupacao = preg_replace('/[^0-9]/is', '', $_POST['ocupacao']);
    $area = $_POST['area'];
    $valorDiaria = formatarDecimal($_POST['valorDiaria']);
    $descricao = $_POST['descricao'];
    $categoria = $_POST['categoria'];

    $arquivo = $_FILES['foto'];
    $nomeArquivo = $arquivo['name'];
    $tipoArquivo = $arquivo['type'];
    $tamanhoArquivo = $arquivo['size'];
    $arquivoTmp = $arquivo['tmp_name'];

    $erros = [];

    if (!in_array($tipoArquivo, $tiposPermitidos)) {
        $erros[] = "Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.";
        $validacao = false;
    }
    if ($tamanhoArquivo > $tamanhoMaximo) {
        $erros[]  = "Arquivo muito grande. Tamanho máximo: " . $tamanhoMaximo / 1024 / 1024 . "MB.";
        $validacao = false;
    }

    // gera um nome único pra não sobrescrever outras fotos na pasta uploads
    $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
    $novoNome = uniqid('img_') . '_' . date('YmdHis') . '.' . $extensao;
    
    $caminhoDestino = $diretorioUpload . $novoNome;

    if (!verifVazio($nome)){
        $validacao = false;
    }
    if (!verifVazio($numero)){
        $validacao = false;
    }
    if (!verifVazio($ocupacao)){
        $validacao = false;
    }
    if (!verifVazio($area)){
        $validacao = false;
    }
    if (!verifVazio($valorDiaria)){
        $validacao = false;
    }
    if (!verifVazio($descricao)){
        $validacao = false;
    }
    if (!verifVazio($categoria)){
        $validacao = false;
    }

    if(!validarApenasNumeros($numero)){
        $erros[] = "O campo 'Número do Quarto' deve conter apenas números.";
        $validacao = false;
    }
    if(!verifInteiroPositivo($ocupacao, false)){
        $erros[] = "O campo 'Ocupação Máxima' deve ser um número inteiro positivo.";
        $validacao = false;
    }
    if(!verifInteiroPositivo($area, false)){
        $erros[] = "O campo 'Área' deve ser um número inteiro positivo.";
        $validacao = false;
    }
    if(!verifFloatPositivo($valorDiaria, false)){
        $erros[] = "O campo 'Valor da Diária' deve ser um número positivo.";
        $validacao = false;
    }

    if($validacao){
        if (move_uploaded_file($arquivoTmp, $caminhoDestino)) {

            $caminhoImagem = $caminhoDestino;
            $sql = "INSERT INTO tbquarto (nome, numero, ocupacao, area, valorDiaria, descricao, caminhoImg, fkCategoria)
            VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt, "ssiidssi", $nome, $numero, $ocupacao, $area, $valorDiaria, $descricao, $caminhoImagem, $categoria);
            mysqli_stmt_execute($stmt);
            $_SESSION['msgCadastrado'] = "Cadastrado com sucesso!";
            
        } else {
            $erros[] = "Erro ao processar o upload da imagem.";
            $_SESSION['mensagensErro'] = array_merge($mensagensErro, $erros);
        }
    }else{
        if (empty($erros)) {
            $erros[] = "Dados inválidos ou campos obrigatórios não preenchidos.";
        }
        $_SESSION['mensagensErro'] = $erros;
        $_SESSION['oldInput'] = $_POST;
    }
    header("Location: cadQuarto.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Quarto - Jardim Secreto</title>
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
                        <h1>Cadastro de Quarto</h1>
                        <p>Preencha os dados do novo quarto do hotel</p>
                    </div>

                    <?php if (!empty($mensagensErro)){ ?>
                        <div class="card card-erro-flutuante">
                            <div class="card-content">
                                <?php foreach ($mensagensErro as $erro){ ?>
                                    <p><?= htmlspecialchars($erro); ?></p>
                                <?php } ?>
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

                    <form class="cadastro-form" method="POST" enctype="multipart/form-data">
                        <div class="form-section">
                            <h2>Dados do Quarto</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nome">Nome do Quarto *</label>
                                    <input type="text" name="nome" id="nome" placeholder="Ex: Quarto Solteiro, Quarto Casal" required value="<?= htmlspecialchars($oldInput['nome'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="numero">Número do Quarto *</label>
                                    <input type="text" name="numero" id="numero" placeholder="Ex: 101, 205" oninput="formatarNumeros(this)" required minlength="3" value="<?= htmlspecialchars($oldInput['numero'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="ocupacao">Ocupação Máxima *</label>
                                    <input type="text" name="ocupacao" id="ocupacao" placeholder="Número de pessoas" oninput="formatarNumeros(this)" required min="1" value="<?= htmlspecialchars($oldInput['ocupacao'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="area">Área (m²) *</label>
                                    <input type="text" name="area" id="area" placeholder="Ex: 25, 35" oninput="formatarNumeros(this)" required value="<?= htmlspecialchars($oldInput['area'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="valorDiaria">Valor da Diária (R$) *</label>
                                    <input type="text" name="valorDiaria" id="valorDiaria" placeholder="Ex: 250,00" oninput="formatarValor(this)" required value="<?= htmlspecialchars($oldInput['valorDiaria'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="categoria">Categoria *</label>
                                    <select id="categoria" name="categoria" required>
                                        <option value="">Selecione</option>
                                        <?php while($linha = mysqli_fetch_array($resultadoCategorias)) { ?>
                                            <option value="<?= $linha['id'] ?>" <?php if(isset($oldInput['categoria']) && $oldInput['categoria'] == $linha['id']) echo 'selected'; ?>><?= $linha['nome'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group full-width">
                                    <label for="foto">Foto do Quarto *</label>
                                    <input type="file" name="foto" id="foto" accept="image/*" required>
                                    <small>Formatos: JPG, PNG, GIF. Máx: 10MB</small>
                                </div>
                                <div class="form-group full-width">
                                    <label for="descricao">Descrição *</label>
                                    <input type="text" name="descricao" id="descricao" placeholder="Descrição da categoria" required value="<?= htmlspecialchars($oldInput['descricao'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" name="cancelar" class="btn-cancelar" onclick="window.location.href = 'painelFuncionario.php'">Cancelar</button>
                            <button type="submit" name="cadastrar" class="btn-cadastrar">Cadastrar Quarto</button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="js/validacoes.js"></script>
    <script src="js/formatacoes.js"></script>
    <script src="js/cards.js"></script>
</body>

</html>