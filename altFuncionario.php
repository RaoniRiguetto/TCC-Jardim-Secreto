<?php
require_once "include/conexao.php";
require_once "include/validacoes.php";
require_once "include/verifSessionFuncionario.php";

$validacao = true;

$mensagensErro = $_SESSION['mensagensErro'] ?? [];

$idFuncionario = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idFuncionario <= 0 && !isset($_POST['cadastrar'])) {
    header("Location: listaFuncionario.php");
    exit();
}

unset($_SESSION['mensagensErro']);

if (isset($_POST['cadastrar'])) {
    $idFuncionarioPost = $_POST['idFuncionario'];
    $nome = $_POST['nome'];
    $cpf = preg_replace('/[^0-9]/is', '', $_POST['cpf']);
    $tell = preg_replace('/[^0-9]/is', '', $_POST['tell']);
    $dataNasc = $_POST['dataNasc'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $senhaConf = $_POST['senhaConf'];
    $dataAdmiss = $_POST['dataAdmiss'];
    $dataDemiss = $_POST['dataDemiss'];
    $cep = preg_replace('/[^0-9]/is', '', $_POST['cep']);
    $numero = $_POST['numero'];
    $endereco = $_POST['endereco'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $uf = $_POST['uf'];
    $status = $_POST['status'];


    $erros = [];

    if (!verifVazio($nome)){
        $validacao = false;
    }
    if(!verifVazio($cpf)){
        $validacao = false;
    }
    if(!verifVazio($tell)){
        $validacao = false;
    }
    if(!verifVazio($dataNasc)){
        $validacao = false;
    }
    if(!verifVazio($email)){
        $validacao = false;
    }
    if(!verifVazio($senha)){
        $validacao = false;
    }
    if(!verifVazio($senhaConf)){
        $validacao = false;
    }
    if(!verifVazio($dataAdmiss)){
        $validacao = false;
    }
    if(!verifVazio($cep)){
        $validacao = false;
    }
    if(!verifVazio($numero)){
        $validacao = false;
    }
    if(!verifVazio($endereco)){
        $validacao = false;
    }
    if(!verifVazio($bairro)){
        $validacao = false;
    }
    if(!verifVazio($cidade)){
        $validacao = false;
    }
    if(!verifVazio($uf)){
        $validacao = false;
    }
    if(!verifVazio($status)){
        $validacao = false;
    }

    if(!validarCPF($cpf)){
        $erros[] = "O CPF inserido é inválido.";
        $validacao = false;
    }
    if(!verifTelefone($tell)){
        $erros[] = "O Telefone precisa ter 10 ou 11 dígitos.";
        $validacao = false;
    }
    if(!verifDDD($tell)){
        $erros[] = "O DDD é invalido";
        $validacao = false;
    }
    if(!validarEmail($email)){
        $erros[] = "O formato do e-mail é inválido.";
        $validacao = false;
    }
    if(!validarSenha($senha)){
        $erros[] = "A senha deve ter no mínimo 8 caracteres.";
        $validacao = false;
    }
    if(!validarSenhasIguais($senha, $senhaConf)){
        $erros[] = "As senhas não coincidem.";
        $validacao = false;
    }
    if(!validarApenasNumeros($numero)){
        $erros[] = "O campo 'Número' deve conter apenas números.";
        $validacao = false;
    }
    if(!maiorDeIdade($dataNasc)){
        $erros[] = "O Funcionário deve ter mais de 18 anos.";
        $validacao = false;
    }
    if(!dataPassado($dataAdmiss)){
        $erros[] = "A data de admissão é inválida.";
        $validacao = false;
    }
    if($dataDemiss != ''){
        if(!dataFuturo($dataDemiss)){
            $erros[] = "A data de demissão é inválida.";
            $validacao = false;
        }
    }
    // se informar demissão, inativa o funcionário automaticamente
    if($dataDemiss != ''){
        $status = 0;
    }

    if($validacao){

        $sql = "SELECT id FROM tbusuario WHERE email = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($resultado) > 0) {
            $validacao = false;
            $erros[] = "Este E-mail já está cadastrado.";
        }

        $sql = "SELECT id FROM tbfuncionario WHERE email = ? AND id != ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "si", $email, $idFuncionarioPost);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($resultado) > 0) {
            $validacao = false;
            $erros[] = "Este E-mail já está cadastrado.";
        }

        $sql = "SELECT id FROM tbfuncionario WHERE cpf = ? AND id != ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "si", $cpf, $idFuncionarioPost);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($resultado) > 0) {
            $validacao = false;
            $erros[] = "Este CPF já está cadastrado.";
        }

    }

    if($validacao){

        $dataDemissValor = ($dataDemiss !== '') ? $dataDemiss : null;
        $sql = "UPDATE tbfuncionario SET nome = ?, cpf = ?, telefone = ?, dataNascimento = ?, email = ?, senha = ?, dataAdmissao = ?, dataDemissao = ?, status = ?, cep = ?, numero = ?, endereco = ?, bairro = ?, cidade = ?, uf = ? WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssissssssi", $nome, $cpf, $tell, $dataNasc, $email, $senha, $dataAdmiss, $dataDemissValor, $status, $cep, $numero, $endereco, $bairro, $cidade, $uf, $idFuncionarioPost);
        mysqli_stmt_execute($stmt);
        $_SESSION['msgExclusao'] = "Funcionário alterado com sucesso!";
        header("Location: listaFuncionario.php");
        exit();

    }else{

        if (empty($erros)) {
            $erros[] = "Dados inválidos ou campos obrigatórios não preenchidos.";
        }

        $_SESSION['mensagensErro'] = $erros;
        header("Location: altFuncionario.php?id=" . $idFuncionarioPost);
        exit();
        
    }
}

$sql = "SELECT * FROM tbfuncionario WHERE id = ?";
$stmtFunc = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmtFunc, "i", $idFuncionario);
mysqli_stmt_execute($stmtFunc);
$resultado = mysqli_stmt_get_result($stmtFunc);
$linha = mysqli_fetch_assoc($resultado);
if (!$linha) {
    header("Location: listaFuncionario.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alteração de Funcionários - Jardim Secreto</title>
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
                        <h1>Alterar Funcionário</h1>
                        <p>Modifique os dados do funcionário selecionado</p>
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

                    <form class="cadastro-form" method="POST">
                        <input type="hidden" name="idFuncionario" value="<?= htmlspecialchars($linha['id'] ?? ''); ?>">
                        <div class="form-section">
                            <h2>Dados Pessoais</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nome">Nome Completo *</label>
                                    <input type="text" name="nome" id="nome" placeholder="Seu nome completo" required value="<?= htmlspecialchars($linha['nome'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="cpf">CPF *</label>
                                    <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" oninput="formatarCPF(this)" required minlength="14" maxlength="14" value="<?= htmlspecialchars($linha['cpf'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="tell">Telefone *</label>
                                    <input type="tel" name="tell" id="tell" placeholder="(11) 99999-9999" oninput="formatarTelefone(this)" required minlength="14" maxlength="15" value="<?= htmlspecialchars($linha['telefone'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dataNasc">Data de Nascimento *</label>
                                    <input type="date" name="dataNasc" id="dataNasc" required value="<?= htmlspecialchars($linha['dataNascimento'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Dados de Acesso</h2>
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="email">E-mail *</label>
                                    <input type="email" name="email" id="email" placeholder="seu@email.com" required value="<?= htmlspecialchars($linha['email'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="senha">Senha *</label>
                                    <input type="text" name="senha" id="senha" placeholder="Mínimo 8 caracteres" oninput="validarSenha(this)" required minlength="8" value="<?= htmlspecialchars($linha['senha'] ?? ''); ?>">
                                    <small id="contador-senha">0/8 caracteres</small>
                                </div>
                                <div class="form-group">
                                    <label for="senhaConf">Confirmar Senha *</label>
                                    <input type="text" name="senhaConf" id="senhaConf" placeholder="Digite a senha novamente" required minlength="8" value="<?= htmlspecialchars($linha['senha'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Status *</label>
                                    <select id="status" name="status" required>
                                        <?php $statusAtual = $linha['status']; ?>
                                        <option value="1" <?= ($statusAtual == 1) ? 'selected' : ''; ?>>Ativo</option>
                                        <option value="0" <?= ($statusAtual == 0) ? 'selected' : ''; ?>>Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Dados Trabalhistas</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="dataAdmiss">Data de Admissão *</label>
                                    <input type="date" name="dataAdmiss" id="dataAdmiss" required max="<?= date("Y-m-d"); ?>" value="<?= htmlspecialchars($linha['dataAdmissao'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dataDemiss">Data de Demissão </label>
                                    <input type="date" name="dataDemiss" id="dataDemiss" min="<?= date("Y-m-d"); ?>" value="<?= htmlspecialchars($linha['dataDemissao'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Endereço</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="cep">CEP *</label>
                                    <input type="text" name="cep" id="cep" placeholder="00000-000" oninput="formatarCEP(this)" onblur="preencherEnderecoPorCEP(this)" required minlength="9" maxlength="9" value="<?= htmlspecialchars($linha['cep'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="numero">Numero*</label>
                                    <input type="text" name="numero" id="numero" placeholder="Número da residencia" oninput="formatarNumeros(this)" required value="<?= htmlspecialchars($linha['numero'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="endereco">Endereço *</label>
                                    <input type="text" name="endereco" id="endereco" placeholder="Endereço da residencia" required value="<?= htmlspecialchars($linha['endereco'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="bairro">Bairro *</label>
                                    <input type="text" name="bairro" id="bairro" placeholder="Bairro da residencia" required value="<?= htmlspecialchars($linha['bairro'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="cidade">Cidade *</label>
                                    <input type="text" name="cidade" id="cidade" placeholder="Sua cidade" required value="<?= htmlspecialchars($linha['cidade'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="uf">UF *</label>
                                    <select name="uf" id="uf" required>
                                        <option value="">Selecione</option>
                                        <option value="AC" <?php if(isset($linha['uf']) && $linha['uf'] == 'AC') echo 'selected'; ?>>Acre</option>
                                        <option value="AL" <?php if(isset($linha['uf']) && $linha['uf'] == 'AL') echo 'selected'; ?>>Alagoas</option>
                                        <option value="AP" <?php if(isset($linha['uf']) && $linha['uf'] == 'AP') echo 'selected'; ?>>Amapá</option>
                                        <option value="AM" <?php if(isset($linha['uf']) && $linha['uf'] == 'AM') echo 'selected'; ?>>Amazonas</option>
                                        <option value="BA" <?php if(isset($linha['uf']) && $linha['uf'] == 'BA') echo 'selected'; ?>>Bahia</option>
                                        <option value="CE" <?php if(isset($linha['uf']) && $linha['uf'] == 'CE') echo 'selected'; ?>>Ceará</option>
                                        <option value="DF" <?php if(isset($linha['uf']) && $linha['uf'] == 'DF') echo 'selected'; ?>>Distrito Federal</option>
                                        <option value="ES" <?php if(isset($linha['uf']) && $linha['uf'] == 'ES') echo 'selected'; ?>>Espírito Santo</option>
                                        <option value="GO" <?php if(isset($linha['uf']) && $linha['uf'] == 'GO') echo 'selected'; ?>>Goiás</option>
                                        <option value="MA" <?php if(isset($linha['uf']) && $linha['uf'] == 'MA') echo 'selected'; ?>>Maranhão</option>
                                        <option value="MT" <?php if(isset($linha['uf']) && $linha['uf'] == 'MT') echo 'selected'; ?>>Mato Grosso</option>
                                        <option value="MS" <?php if(isset($linha['uf']) && $linha['uf'] == 'MS') echo 'selected'; ?>>Mato Grosso do Sul</option>
                                        <option value="MG" <?php if(isset($linha['uf']) && $linha['uf'] == 'MG') echo 'selected'; ?>>Minas Gerais</option>
                                        <option value="PA" <?php if(isset($linha['uf']) && $linha['uf'] == 'PA') echo 'selected'; ?>>Pará</option>
                                        <option value="PB" <?php if(isset($linha['uf']) && $linha['uf'] == 'PB') echo 'selected'; ?>>Paraíba</option>
                                        <option value="PR" <?php if(isset($linha['uf']) && $linha['uf'] == 'PR') echo 'selected'; ?>>Paraná</option>
                                        <option value="PE" <?php if(isset($linha['uf']) && $linha['uf'] == 'PE') echo 'selected'; ?>>Pernambuco</option>
                                        <option value="PI" <?php if(isset($linha['uf']) && $linha['uf'] == 'PI') echo 'selected'; ?>>Piauí</option>
                                        <option value="RJ" <?php if(isset($linha['uf']) && $linha['uf'] == 'RJ') echo 'selected'; ?>>Rio de Janeiro</option>
                                        <option value="RN" <?php if(isset($linha['uf']) && $linha['uf'] == 'RN') echo 'selected'; ?>>Rio Grande do Norte</option>
                                        <option value="RS" <?php if(isset($linha['uf']) && $linha['uf'] == 'RS') echo 'selected'; ?>>Rio Grande do Sul</option>
                                        <option value="RO" <?php if(isset($linha['uf']) && $linha['uf'] == 'RO') echo 'selected'; ?>>Rondônia</option>
                                        <option value="RR" <?php if(isset($linha['uf']) && $linha['uf'] == 'RR') echo 'selected'; ?>>Roraima</option>
                                        <option value="SC" <?php if(isset($linha['uf']) && $linha['uf'] == 'SC') echo 'selected'; ?>>Santa Catarina</option>
                                        <option value="SP" <?php if(isset($linha['uf']) && $linha['uf'] == 'SP') echo 'selected'; ?>>São Paulo</option>
                                        <option value="SE" <?php if(isset($linha['uf']) && $linha['uf'] == 'SE') echo 'selected'; ?>>Sergipe</option>
                                        <option value="TO" <?php if(isset($linha['uf']) && $linha['uf'] == 'TO') echo 'selected'; ?>>Tocantins</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" name="cancelar" class="btn-cancelar" onclick="window.location.href = 'listaFuncionario.php'">Cancelar</button>
                            <button type="submit" name="cadastrar" class="btn-cadastrar">Salvar Alterações</button>
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