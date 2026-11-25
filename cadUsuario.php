<?php
require_once "include/conexao.php";
require_once "include/validacoes.php";
require_once "include/verifSessionFuncionario.php";

$validacao = true;

$mensagens_erro = $_SESSION['mensagens_erro'] ?? [];
$msgCadastrado = $_SESSION['msgCadastrado'] ?? "";
$oldInput = $_SESSION['oldInput'] ?? [];

unset($_SESSION['mensagens_erro']);
unset($_SESSION['msgCadastrado']);
unset($_SESSION['oldInput']);

if (isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $cpf = preg_replace('/[^0-9]/is', '', $_POST['cpf']);
    $tell = preg_replace('/[^0-9]/is', '', $_POST['tell']);
    $dataNasc = $_POST['dataNasc'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $senhaConf = $_POST['senhaConf'];
    $cep = preg_replace('/[^0-9]/is', '', $_POST['cep']);
    $numero = $_POST['numero'];
    $endereco = $_POST['endereco'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $uf = $_POST['uf'];

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
        $erros[] = "O Usuario deve ter mais de 18 anos.";
        $validacao = false;
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

        $sql = "SELECT id FROM tbfuncionario WHERE email = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($resultado) > 0) {
            $validacao = false;
            $erros[] = "Este E-mail já está cadastrado.";
        }

        $sql = "SELECT id FROM tbusuario WHERE cpf = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "s", $cpf);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($resultado) > 0) {
            $validacao = false;
            $erros[] = "Este CPF já está cadastrado.";
        }

    }

    if($validacao){
        $sql = "INSERT INTO tbusuario (nome, cpf, telefone, dataNascimento, email, senha, cep, numero, endereco, bairro, cidade, uf)
        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssssss", $nome, $cpf, $tell, $dataNasc, $email, $senha, $cep, $numero, $endereco, $bairro, $cidade, $uf);
        mysqli_stmt_execute($stmt);
        $_SESSION['msgCadastrado'] = "Cadastrado com sucesso!";
    }else{
        if (empty($erros)) {
            $erros[] = "Dados inválidos ou campos obrigatórios não preenchidos.";
        }
        $_SESSION['mensagens_erro'] = $erros;
        $_SESSION['oldInput'] = $_POST;
    }
    header("Location: cadUsuario.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário- Jardim Secreto</title>
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
                        <h1>Cadastro de Usuário</h1>
                        <p>Preencha os dados do novo usuário</p>
                    </div>

                    <?php if (!empty($mensagens_erro)){ ?>
                        <div class="card card-erro-flutuante">
                            <div class="card-content">
                                <?php foreach ($mensagens_erro as $erro){ ?>
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

                    <form class="cadastro-form" method="POST">

                        <div class="form-section">
                            <h2>Dados Pessoais</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nome">Nome Completo *</label>
                                    <input type="text" name="nome" id="nome" placeholder="Seu nome completo" required value="<?= htmlspecialchars($oldInput['nome'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="cpf">CPF *</label>
                                    <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" oninput="formatarCPF(this)" required minlength="14" maxlength="14" value="<?= htmlspecialchars($oldInput['cpf'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="tell">Telefone *</label>
                                    <input type="tel" name="tell" id="tell" placeholder="(11) 99999-9999" oninput="formatarTelefone(this)" required minlength="14" maxlength="15" value="<?= htmlspecialchars($oldInput['tell'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dataNasc">Data de Nascimento *</label>
                                    <input type="date" name="dataNasc" id="dataNasc" required value="<?= htmlspecialchars($oldInput['dataNasc'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Dados de Acesso</h2>
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="email">E-mail *</label>
                                    <input type="email" name="email" id="email" placeholder="seu@email.com" required value="<?= htmlspecialchars($oldInput['email'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="senha">Senha *</label>
                                    <input type="password" name="senha" id="senha" placeholder="Mínimo 8 caracteres" oninput="validarSenha(this)" required minlength="8">
                                    <small id="contador-senha">0/8 caracteres</small>
                                </div>
                                <div class="form-group">
                                    <label for="senhaConf">Confirmar Senha *</label>
                                    <input type="password" name="senhaConf" id="senhaConf" placeholder="Digite a senha novamente" required minlength="8">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Endereço</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="cep">CEP *</label>
                                    <input type="text" name="cep" id="cep" placeholder="00000-000" oninput="formatarCEP(this)" onblur="preencherEnderecoPorCEP(this)" required minlength="9" maxlength="9" value="<?= htmlspecialchars($oldInput['cep'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="numero">Numero *</label>
                                    <input type="text" name="numero" id="numero" placeholder="Número da residencia" oninput="formatarNumeros(this)" required value="<?= htmlspecialchars($oldInput['numero'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="endereco">Endereço *</label>
                                    <input type="text" name="endereco" id="endereco" placeholder="Endereço da residencia" required value="<?= htmlspecialchars($oldInput['endereco'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="bairro">Bairro *</label>
                                    <input type="text" name="bairro" id="bairro" placeholder="Bairro da residencia" required value="<?= htmlspecialchars($oldInput['bairro'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="cidade">Cidade *</label>
                                    <input type="text" name="cidade" id="cidade" placeholder="Sua cidade" required value="<?= htmlspecialchars($oldInput['cidade'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="uf">UF *</label>
                                    <select name="uf" id="uf" required>
                                        <option value="">Selecione</option>
                                        <option value="AC" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'AC') echo 'selected'; ?>>Acre</option>
                                        <option value="AL" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'AL') echo 'selected'; ?>>Alagoas</option>
                                        <option value="AP" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'AP') echo 'selected'; ?>>Amapá</option>
                                        <option value="AM" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'AM') echo 'selected'; ?>>Amazonas</option>
                                        <option value="BA" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'BA') echo 'selected'; ?>>Bahia</option>
                                        <option value="CE" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'CE') echo 'selected'; ?>>Ceará</option>
                                        <option value="DF" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'DF') echo 'selected'; ?>>Distrito Federal</option>
                                        <option value="ES" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'ES') echo 'selected'; ?>>Espírito Santo</option>
                                        <option value="GO" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'GO') echo 'selected'; ?>>Goiás</option>
                                        <option value="MA" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'MA') echo 'selected'; ?>>Maranhão</option>
                                        <option value="MT" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'MT') echo 'selected'; ?>>Mato Grosso</option>
                                        <option value="MS" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'MS') echo 'selected'; ?>>Mato Grosso do Sul</option>
                                        <option value="MG" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'MG') echo 'selected'; ?>>Minas Gerais</option>
                                        <option value="PA" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'PA') echo 'selected'; ?>>Pará</option>
                                        <option value="PB" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'PB') echo 'selected'; ?>>Paraíba</option>
                                        <option value="PR" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'PR') echo 'selected'; ?>>Paraná</option>
                                        <option value="PE" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'PE') echo 'selected'; ?>>Pernambuco</option>
                                        <option value="PI" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'PI') echo 'selected'; ?>>Piauí</option>
                                        <option value="RJ" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'RJ') echo 'selected'; ?>>Rio de Janeiro</option>
                                        <option value="RN" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'RN') echo 'selected'; ?>>Rio Grande do Norte</option>
                                        <option value="RS" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'RS') echo 'selected'; ?>>Rio Grande do Sul</option>
                                        <option value="RO" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'RO') echo 'selected'; ?>>Rondônia</option>
                                        <option value="RR" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'RR') echo 'selected'; ?>>Roraima</option>
                                        <option value="SC" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'SC') echo 'selected'; ?>>Santa Catarina</option>
                                        <option value="SP" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'SP') echo 'selected'; ?>>São Paulo</option>
                                        <option value="SE" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'SE') echo 'selected'; ?>>Sergipe</option>
                                        <option value="TO" <?php if(isset($oldInput['uf']) && $oldInput['uf'] == 'TO') echo 'selected'; ?>>Tocantins</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" name="cancelar" class="btn-cancelar" onclick="window.location.href = 'painelFuncionario.php'">Cancelar</button>
                            <button type="submit" name="cadastrar" class="btn-cadastrar">Cadastrar Usuário</button>
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