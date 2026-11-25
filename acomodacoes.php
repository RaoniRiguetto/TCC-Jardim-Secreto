<?php
require_once "include/conexao.php";
session_start();

$pesquisa = $_SESSION['pesquisa'] ?? "";
$hospedesSelecionado = $_SESSION['filtro_hospedes'] ?? 1;
$categoriaSelecionada = $_SESSION['filtro_categoria'] ?? "";

unset($_SESSION['pesquisa'], $_SESSION['filtro_hospedes'], $_SESSION['filtro_categoria']);

$whereExtra = "";
$params = [];
$types = "";

if(isset($_POST['pesquisar'])){
    $hospedes = isset($_POST['hospedes']) && $_POST['hospedes'] !== '' ? intval($_POST['hospedes']) : 0;
    $categoria = isset($_POST['categoria']) && $_POST['categoria'] !== '' ? intval($_POST['categoria']) : 0;

    $_SESSION['filtro_hospedes'] = $hospedes > 0 ? $hospedes : 1;
    $_SESSION['filtro_categoria'] = $categoria > 0 ? (string)$categoria : "";
    $hospedesSelecionado = $_SESSION['filtro_hospedes'];
    $categoriaSelecionada = $_SESSION['filtro_categoria'];
}

if (intval($hospedesSelecionado) > 1 || isset($_POST['pesquisar'])) {
    if (intval($hospedesSelecionado) > 0) {
        $whereExtra .= " AND q.ocupacao >= ?";
        $params[] = intval($hospedesSelecionado);
        $types .= "i";
    }
}
if ($categoriaSelecionada !== "") {
    $whereExtra .= " AND q.fkCategoria = ?";
    $params[] = intval($categoriaSelecionada);
    $types .= "i";
}

// busca só quartos ativos aplicando categoria e capacidade quando selecionados
$sql = "SELECT q.*, c.nome AS nomeCategoria
        FROM tbquarto q
        JOIN tbcategoria c ON q.fkCategoria = c.id
        WHERE q.status != 'inativo' $whereExtra";
$stmtQuartos = mysqli_prepare($conexao, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmtQuartos, $types, ...$params);
}
mysqli_stmt_execute($stmtQuartos);
$resultado = mysqli_stmt_get_result($stmtQuartos);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acomodações - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/acomodacoes.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>

<body>

    <?php include_once "include/header.php" ?>

    <main class="main-content">

        <section class="hero-accommodations">
            <div class="hero-content">
                <h1>Nossas Acomodações</h1>
                <p>Conheça nossos quartos e suítes, cada um pensado para oferecer o máximo conforto</p>
            </div>
        </section>

        <section class="filters-bar">
            <div class="container">
                <form method="POST" class="filters-form">
                    <div class="filters-grid">
                        
                        <div class="filter-group">

                            <h3><i class="bi bi-tags"></i> Categoria</h3>
                            <div class="checkbox-group">

                                <?php
                                    $sqlCategoriasFiltro = "SELECT * FROM tbcategoria WHERE status = 1 ORDER BY nome";
                                    $resultadoCategoriasFiltro = mysqli_query($conexao, $sqlCategoriasFiltro);
                                ?>

                                <label class="checkbox-label">
                                    <input type="radio" name="categoria" value="" <?= ($categoriaSelecionada === "") ? 'checked' : '' ?>>
                                    <span class="checkbox-text">Todas</span>
                                </label>

                                <?php while($cat = mysqli_fetch_assoc($resultadoCategoriasFiltro)){ ?>
                                    <label class="checkbox-label">
                                        <input type="radio" name="categoria" value="<?= $cat['id'] ?>" <?= ($categoriaSelecionada == $cat['id']) ? 'checked' : '' ?>>
                                        <span class="checkbox-text"><?= htmlspecialchars($cat['nome']) ?></span>
                                    </label>
                                <?php } ?>

                            </div>
                        </div>

                        <div class="filter-group">
                            <h3><i class="bi bi-people"></i> Hóspedes</h3>

                            <div class="guests-input">
                                <input type="number" name="hospedes" class="guests-field" value="<?= htmlspecialchars($hospedesSelecionado) ?>" min="1" max="10">
                                <span class="guests-label">hóspede(s)</span>
                            </div>
                            
                        </div>

                        <div class="filter-action">
                            <button type="submit" class="btn-search" name="pesquisar"><i class="bi bi-search"></i>Buscar</button>
                        </div>

                    </div>
                </form>
            </div>
        </section>

        <section class="rooms-section">
            <div class="container">
                <div class="rooms-grid">

                    <?php while($linha = mysqli_fetch_array($resultado)) { ?>
                        <a href="quarto.php?id=<?= $linha['id']; ?>" class="room-card-link">
                            <div class="room-card">

                                <div class="room-image">

                                    <img src="<?= htmlspecialchars($linha['caminhoImg']); ?>" alt="Imagem do quarto">
                                    <div class="room-badge"><?= htmlspecialchars($linha['nomeCategoria']); ?></div>

                                </div>

                                <div class="room-content">
                                    <h3><?= $linha['nome']; ?></h3>
                                    <p>Até <?= $linha['ocupacao']; ?> Hospede(s)</p>
                                    <div class="room-price">
                                        <span class="price">R$ <?= $linha['valorDiaria']; ?></span>
                                        <span class="period">/noite</span>
                                    </div>
                                    <button class="btn-reserve">Reservar Agora</button>
                                </div>

                            </div>
                        </a>
                    <?php } ?>

                </div>
            </div>
        </section>
    </main>

    <?php include_once "include/footer.php" ?>

</body>

</html>