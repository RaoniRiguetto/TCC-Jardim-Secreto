<?php
require_once "include/conexao.php";
require_once "include/verifSessionFuncionario.php";

$idUsuario = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idUsuario <= 0) {
    header("Location: listaUsuario.php");
    exit();
}

$sql = "SELECT * FROM tbusuario WHERE id = ?";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $idUsuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$linha = mysqli_fetch_assoc($resultado);
if (!$linha) {
    header("Location: listaUsuario.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualização de Usuário - Jardim Secreto</title>
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
                        <h1>Visualizar Usuário</h1>
                        <p>Visualize os dados do usuário selecionado</p>
                    </div>

                    <form class="cadastro-form" method="POST">

                        <div class="form-section">
                            <h2>Dados Pessoais</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nome">Nome Completo *</label>
                                    <input type="text" name="nome" id="nome" placeholder="Seu nome completo" required value="<?= htmlspecialchars($linha['nome'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="cpf">CPF *</label>
                                    <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" oninput="formatarCPF(this)" required minlength="14" maxlength="14" value="<?= htmlspecialchars($linha['cpf'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="tell">Telefone *</label>
                                    <input type="tel" name="tell" id="tell" placeholder="(11) 99999-9999" oninput="formatarTelefone(this)" required minlength="14" maxlength="15" value="<?= htmlspecialchars($linha['telefone'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="dataNasc">Data de Nascimento *</label>
                                    <input type="date" name="dataNasc" id="dataNasc" required value="<?= htmlspecialchars($linha['dataNascimento'] ?? ''); ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Dados de Acesso</h2>
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="email">E-mail *</label>
                                    <input type="email" name="email" id="email" placeholder="seu@email.com" required value="<?= htmlspecialchars($linha['email'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="senha">Senha *</label>
                                    <input type="text" name="senha" id="senha" placeholder="Mínimo 8 caracteres" oninput="validarSenha(this)" required minlength="8" value="<?= htmlspecialchars($linha['senha'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="senhaConf">Confirmar Senha *</label>
                                    <input type="text" name="senhaConf" id="senhaConf" placeholder="Digite a senha novamente" required minlength="8" value="<?= htmlspecialchars($linha['senha'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Status *</label>
                                    <input type="text" value="<?= ($linha['status'] == 1) ? 'Ativo' : 'Inativo'; ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Endereço</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="cep">CEP *</label>
                                    <input type="text" name="cep" id="cep" placeholder="00000-000" oninput="formatarCEP(this)" onblur="preencherEnderecoPorCEP(this)" required minlength="9" maxlength="9" value="<?= htmlspecialchars($linha['cep'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="numero">Numero *</label>
                                    <input type="text" name="numero" id="numero" placeholder="Número da residencia" oninput="formatarNumeros(this)" required value="<?= htmlspecialchars($linha['numero'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="endereco">Endereço *</label>
                                    <input type="text" name="endereco" id="endereco" placeholder="Endereço da residencia" required value="<?= htmlspecialchars($linha['endereco'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="bairro">Bairro *</label>
                                    <input type="text" name="bairro" id="bairro" placeholder="Bairro da residencia" required value="<?= htmlspecialchars($linha['bairro'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="cidade">Cidade *</label>
                                    <input type="text" name="cidade" id="cidade" placeholder="Sua cidade" required value="<?= htmlspecialchars($linha['cidade'] ?? ''); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="uf">UF *</label>
                                    <select name="uf" id="uf" required disabled>
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
                            <button type="button" name="cancelar" class="btn-cadastrar" onclick="window.location.href = 'listaUsuario.php'">Voltar</button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>