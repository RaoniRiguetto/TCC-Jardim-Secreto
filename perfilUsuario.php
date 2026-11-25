<?php
require_once "include/conexao.php";
require_once "include/verifSessionUsuario.php";
require_once "include/validacoes.php";

$idUsuario = $_SESSION['id'];

$mensagensErro = $_SESSION['mensagens_erro'] ?? [];
$msgSucesso = $_SESSION['msg_sucesso'] ?? '';
$oldInput = $_SESSION['oldInput'] ?? [];

unset($_SESSION['mensagens_erro'], $_SESSION['msg_sucesso'], $_SESSION['oldInput']);

if (isset($_POST['salvar_dados'])) {
    $nome = $_POST['nome'];
    $cpf = preg_replace('/[^0-9]/is', '', $_POST['cpf']);
    $tell = preg_replace('/[^0-9]/is', '', $_POST['tell']);
    $dataNasc = $_POST['dataNasc'];
    $email = $_POST['email'];
    $cep = preg_replace('/[^0-9]/is', '', $_POST['cep']);
    $numero = $_POST['numero'];
    $endereco = $_POST['endereco'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $uf = $_POST['uf'];

    $erros = [];
    $validacao = true;

    if (!verifVazio($nome) || !verifVazio($cpf) || !verifVazio($tell) || !verifVazio($dataNasc) || !verifVazio($email) || !verifVazio($cep) || !verifVazio($numero) || !verifVazio($endereco) || !verifVazio($bairro) || !verifVazio($cidade) || !verifVazio($uf)) {
        $erros[] = "Todos os campos com * são obrigatórios.";
        $validacao = false;
    }

    if (!validarCPF($cpf)) { $erros[] = "O CPF inserido é inválido."; $validacao = false; }
    if (!verifTelefone($tell)) { $erros[] = "O Telefone precisa ter 10 ou 11 dígitos."; $validacao = false; }
    if (!validarEmail($email)) { $erros[] = "O formato do e-mail é inválido."; $validacao = false; }
    if (!maiorDeIdade($dataNasc)) { $erros[] = "Você deve ter mais de 18 anos."; $validacao = false; }

    if ($validacao) {
        $sqlCheck = "SELECT id FROM tbusuario WHERE (email = ? OR cpf = ?) AND id != ?";
        $stmtCheck = mysqli_prepare($conexao, $sqlCheck);
        mysqli_stmt_bind_param($stmtCheck, "ssi", $email, $cpf, $idUsuario);
        mysqli_stmt_execute($stmtCheck);
        $resultadoCheck = mysqli_stmt_get_result($stmtCheck);
        if (mysqli_num_rows($resultadoCheck) > 0) {
            $erros[] = "O e-mail ou CPF informado já está em uso por outro usuário.";
            $validacao = false;
        }
    }

    if ($validacao) {
        $sql = "UPDATE tbusuario SET nome = ?, cpf = ?, telefone = ?, dataNascimento = ?, email = ?, cep = ?, numero = ?, endereco = ?, bairro = ?, cidade = ?, uf = ? WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssssi", $nome, $cpf, $tell, $dataNasc, $email, $cep, $numero, $endereco, $bairro, $cidade, $uf, $idUsuario);
        mysqli_stmt_execute($stmt);

        $_SESSION['nome'] = $nome;
        $_SESSION['msg_sucesso'] = "Seus dados foram atualizados com sucesso!";
    } else {
        $_SESSION['mensagens_erro'] = $erros;
        $_SESSION['oldInput'] = $_POST;
    }

    header("Location: perfilUsuario.php");
    exit();
}

if (isset($_POST['salvar_senha'])) {
    $senhaAtual = $_POST['senha_atual'];
    $novaSenha = $_POST['nova_senha'];
    $novaSenhaConf = $_POST['nova_senha_conf'];

    $erros = [];
    $validacao = true;

    $sqlSenha = "SELECT senha FROM tbusuario WHERE id = ?";
    $stmtSenha = mysqli_prepare($conexao, $sqlSenha);
    mysqli_stmt_bind_param($stmtSenha, "i", $idUsuario);
    mysqli_stmt_execute($stmtSenha);
    $resultadoSenha = mysqli_stmt_get_result($stmtSenha);
    $usuarioSenha = mysqli_fetch_assoc($resultadoSenha);

    if ($usuarioSenha['senha'] !== $senhaAtual) {
        $erros[] = "A senha atual está incorreta.";
        $validacao = false;
    }

    if (!validarSenha($novaSenha)) { $erros[] = "A nova senha deve ter no mínimo 8 caracteres."; $validacao = false; }
    if (!validarSenhasIguais($novaSenha, $novaSenhaConf)) { $erros[] = "As novas senhas não coincidem."; $validacao = false; }

    if ($validacao) {
        $sql = "UPDATE tbusuario SET senha = ? WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "si", $novaSenha, $idUsuario);
        mysqli_stmt_execute($stmt);
        $_SESSION['msg_sucesso'] = "Sua senha foi alterada com sucesso!";
    } else {
        $_SESSION['mensagens_erro'] = $erros;
    }

    header("Location: perfilUsuario.php");
    exit();
}

$sql = "SELECT * FROM tbusuario WHERE id = ?";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $idUsuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/painelUsuario.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/cadastro.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        .cadastro-container-form {
            flex-direction: column;
            gap: var(--size-6);
            align-items: center;
            padding-bottom: var(--size-6);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">

        <?php include_once('include/sidebarUsuario.php'); ?>

        <main class="main-content">

            <header class="header">
                <div class="header-left">
                    <h1>Meu Perfil</h1>
                    <p>Gerencie suas informações pessoais e de acesso</p>
                </div>
            </header>

            <div class="cadastro-container-form">

                <?php if (!empty($mensagensErro)){ ?>
                    <div class="card card-erro-flutuante">
                        <div class="card-content">
                            <?php foreach ($mensagensErro as $erro){ ?>
                                <p><?= htmlspecialchars($erro); ?></p>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!empty($msgSucesso)){ ?>
                    <div class="card card-sucesso-flutuante">
                        <div class="card-content">
                            <?= htmlspecialchars($msgSucesso); ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="cadastro-card">
                    <div class="cadastro-header">
                        <h2>Dados Pessoais e Endereço</h2>
                    </div>
                    <form class="cadastro-form" method="POST">
                        <div class="form-section">
                            <div class="form-grid">
                                <div class="form-group"><label for="nome">Nome Completo *</label><input type="text" name="nome" id="nome" required value="<?= htmlspecialchars($oldInput['nome'] ?? $usuario['nome']); ?>"></div>
                                <div class="form-group"><label for="cpf">CPF *</label><input type="text" name="cpf" id="cpf" oninput="formatarCPF(this)" required minlength="14" maxlength="14" value="<?= htmlspecialchars($oldInput['cpf'] ?? $usuario['cpf']); ?>"></div>
                                <div class="form-group"><label for="tell">Telefone *</label><input type="tel" name="tell" id="tell" oninput="formatarTelefone(this)" required minlength="14" maxlength="15" value="<?= htmlspecialchars($oldInput['tell'] ?? $usuario['telefone']); ?>"></div>
                                <div class="form-group"><label for="dataNasc">Data de Nascimento *</label><input type="date" name="dataNasc" id="dataNasc" required value="<?= htmlspecialchars($oldInput['dataNasc'] ?? $usuario['dataNascimento']); ?>"></div>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="form-grid">
                                <div class="form-group full-width"><label for="email">E-mail *</label><input type="email" name="email" id="email" required value="<?= htmlspecialchars($oldInput['email'] ?? $usuario['email']); ?>"></div>
                            </div>
                        </div>
                        <div class="form-section">
                            <h2>Endereço</h2>
                            <div class="form-grid">
                                <div class="form-group"><label for="cep">CEP *</label><input type="text" name="cep" id="cep" oninput="formatarCEP(this)" onblur="preencherEnderecoPorCEP(this)" required minlength="9" maxlength="9" value="<?= htmlspecialchars($oldInput['cep'] ?? $usuario['cep']); ?>"></div>
                                <div class="form-group"><label for="numero">Numero *</label><input type="text" name="numero" id="numero" oninput="formatarNumeros(this)" required value="<?= htmlspecialchars($oldInput['numero'] ?? $usuario['numero']); ?>"></div>
                                <div class="form-group"><label for="endereco">Endereço *</label><input type="text" name="endereco" id="endereco" required value="<?= htmlspecialchars($oldInput['endereco'] ?? $usuario['endereco']); ?>"></div>
                                <div class="form-group"><label for="bairro">Bairro *</label><input type="text" name="bairro" id="bairro" required value="<?= htmlspecialchars($oldInput['bairro'] ?? $usuario['bairro']); ?>"></div>
                                <div class="form-group"><label for="cidade">Cidade *</label><input type="text" name="cidade" id="cidade" required value="<?= htmlspecialchars($oldInput['cidade'] ?? $usuario['cidade']); ?>"></div>
                                <div class="form-group"><label for="uf">UF *</label>
                                    <select name="uf" id="uf" required>
                                        <?php $ufSelecionado = $oldInput['uf'] ?? $usuario['uf']; ?>
                                        <option value="">Selecione</option>
                                        <option value="AC" <?= ($ufSelecionado == 'AC') ? 'selected' : ''; ?>>Acre</option>
                                        <option value="AL" <?= ($ufSelecionado == 'AL') ? 'selected' : ''; ?>>Alagoas</option>
                                        <option value="AP" <?= ($ufSelecionado == 'AP') ? 'selected' : ''; ?>>Amapá</option>
                                        <option value="AM" <?= ($ufSelecionado == 'AM') ? 'selected' : ''; ?>>Amazonas</option>
                                        <option value="BA" <?= ($ufSelecionado == 'BA') ? 'selected' : ''; ?>>Bahia</option>
                                        <option value="CE" <?= ($ufSelecionado == 'CE') ? 'selected' : ''; ?>>Ceará</option>
                                        <option value="DF" <?= ($ufSelecionado == 'DF') ? 'selected' : ''; ?>>Distrito Federal</option>
                                        <option value="ES" <?= ($ufSelecionado == 'ES') ? 'selected' : ''; ?>>Espírito Santo</option>
                                        <option value="GO" <?= ($ufSelecionado == 'GO') ? 'selected' : ''; ?>>Goiás</option>
                                        <option value="MA" <?= ($ufSelecionado == 'MA') ? 'selected' : ''; ?>>Maranhão</option>
                                        <option value="MT" <?= ($ufSelecionado == 'MT') ? 'selected' : ''; ?>>Mato Grosso</option>
                                        <option value="MS" <?= ($ufSelecionado == 'MS') ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                                        <option value="MG" <?= ($ufSelecionado == 'MG') ? 'selected' : ''; ?>>Minas Gerais</option>
                                        <option value="PA" <?= ($ufSelecionado == 'PA') ? 'selected' : ''; ?>>Pará</option>
                                        <option value="PB" <?= ($ufSelecionado == 'PB') ? 'selected' : ''; ?>>Paraíba</option>
                                        <option value="PR" <?= ($ufSelecionado == 'PR') ? 'selected' : ''; ?>>Paraná</option>
                                        <option value="PE" <?= ($ufSelecionado == 'PE') ? 'selected' : ''; ?>>Pernambuco</option>
                                        <option value="PI" <?= ($ufSelecionado == 'PI') ? 'selected' : ''; ?>>Piauí</option>
                                        <option value="RJ" <?= ($ufSelecionado == 'RJ') ? 'selected' : ''; ?>>Rio de Janeiro</option>
                                        <option value="RN" <?= ($ufSelecionado == 'RN') ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                                        <option value="RS" <?= ($ufSelecionado == 'RS') ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                                        <option value="RO" <?= ($ufSelecionado == 'RO') ? 'selected' : ''; ?>>Rondônia</option>
                                        <option value="RR" <?= ($ufSelecionado == 'RR') ? 'selected' : ''; ?>>Roraima</option>
                                        <option value="SC" <?= ($ufSelecionado == 'SC') ? 'selected' : ''; ?>>Santa Catarina</option>
                                        <option value="SP" <?= ($ufSelecionado == 'SP') ? 'selected' : ''; ?>>São Paulo</option>
                                        <option value="SE" <?= ($ufSelecionado == 'SE') ? 'selected' : ''; ?>>Sergipe</option>
                                        <option value="TO" <?= ($ufSelecionado == 'TO') ? 'selected' : ''; ?>>Tocantins</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="salvar_dados" class="btn-cadastrar">Salvar Alterações</button>
                        </div>
                    </form>
                </div>

                <div class="cadastro-card">
                    <div class="cadastro-header">
                        <h2>Alterar Senha</h2>
                    </div>
                    <form class="cadastro-form" method="POST">
                        <div class="form-section">
                            <div class="form-grid">
                                <div class="form-group"><label for="senha-atual">Senha Atual *</label><input type="password" name="senha_atual" id="senha-atual" placeholder="Digite sua senha atual" required></div>
                                <div class="form-group"><label for="nova-senha">Nova Senha *</label><input type="password" name="nova_senha" id="nova-senha" placeholder="Mínimo 8 caracteres" required minlength="8"></div>
                                <div class="form-group"><label for="confirmar-senha">Confirmar Nova Senha *</label><input type="password" name="nova_senha_conf" id="confirmar-senha" placeholder="Confirme a nova senha" required minlength="8"></div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="salvar_senha" class="btn-cadastrar">Alterar Senha</button>
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