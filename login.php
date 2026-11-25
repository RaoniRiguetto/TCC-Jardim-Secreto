<?php
require_once("include/conexao.php");
require_once("include/verifSessionDeslogado.php");

if (isset($_POST["entrar"]) && !empty($_POST["email"]) && !empty($_POST["senha"])){

    $email = $_POST["email"];
    $senha = $_POST["senha"];

    // confere primeiro na tabela de clientes e depois na de funcionários ativos
    $sqlUsuario = "SELECT * FROM tbusuario WHERE email = ? AND senha = ? AND status = 1";
    $stmtUsuario = mysqli_prepare($conexao, $sqlUsuario);
    mysqli_stmt_bind_param($stmtUsuario, "ss", $email, $senha);
    mysqli_stmt_execute($stmtUsuario);
    $resultadoUsuario = mysqli_stmt_get_result($stmtUsuario);
    $registrosUsuario = mysqli_num_rows($resultadoUsuario);

    $sqlFuncionario = "SELECT * FROM tbfuncionario WHERE email = ? AND senha = ? AND status = 1";
    $stmtFuncionario = mysqli_prepare($conexao, $sqlFuncionario);
    mysqli_stmt_bind_param($stmtFuncionario, "ss", $email, $senha);
    mysqli_stmt_execute($stmtFuncionario);
    $resultadoFuncionario = mysqli_stmt_get_result($stmtFuncionario);
    $registrosFuncionario = mysqli_num_rows($resultadoFuncionario);

    if ($registrosUsuario > 0) {
        $usuario = mysqli_fetch_array($resultadoUsuario);

        $_SESSION["id"] = $usuario["id"];
        $_SESSION["nome"] = $usuario["nome"];
        $_SESSION["email"] = $usuario["email"];
        $_SESSION["tipo"] = "usuario";
        header( "location: index.php");
        exit();
    }elseif($registrosFuncionario > 0){
        $usuario = mysqli_fetch_array($resultadoFuncionario);

        $_SESSION["id"] = $usuario["id"];
        $_SESSION["nome"] = $usuario["nome"];
        $_SESSION["email"] = $usuario["email"];
        $_SESSION["tipo"] = "funcionario";
        header( "location: index.php");
        exit();
    }else{
        $_SESSION['erro_login'] = "Usuário ou senha inválidos.";
        $_SESSION['email_digitado'] = $email;

        header("location: login.php");
        exit();
    }
}

$mensagem = $_SESSION['erro_login'] ?? $_GET['mensagem'] ?? '';
$email_salvo = $_SESSION['email_digitado'] ?? '';
$msgCadastrado = $_SESSION['msgCadastrado'] ?? '';

unset($_SESSION['erro_login']);
unset($_SESSION['email_digitado']);
unset($_SESSION['msgCadastrado']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-box">

            <div class="image-side">
                <div class="image-overlay">
                    <div class="logo">Jardim Secreto</div>
                    <p class="slogan">Descubra o refúgio perfeito para suas férias. Um oásis de tranquilidade e conforto no coração da natureza.</p>
                </div>
            </div>

            <div class="form-side">
                <div class="login-header">
                    <h1>Faça seu login</h1>
                    <p>Entre em sua conta para acessar o sistema de reservas</p>
                </div>

                <?php if (!empty($mensagem)){ ?>
                <div class="card card-erro-flutuante">
                    <div class="card-content">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                </div>
                <?php } ?>

                <?php if (!empty($msgCadastrado)){ ?>
                <div class="card card-sucesso-flutuante">
                    <div class="card-content">
                        <?php echo htmlspecialchars($msgCadastrado); ?>
                    </div>
                </div>
                <?php } ?>

                <form class="login-form" method="POST">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" placeholder="seu@email.com" value="<?php echo htmlspecialchars($email_salvo) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" placeholder="Sua senha" required>
                    </div>

                    <button type="submit" name="entrar" class="btn btn-primary">Entrar</button>

                    <div class="register-link">
                        Não tem uma conta? <a href="cadUsuarioLogin.php">Cadastre-se</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Jardim Secreto Hotel. Todos os direitos reservados.</p>
    </footer>
    <script src="js/cards.js"></script>
</body>

</html>