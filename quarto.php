<?php
require_once "include/conexao.php";
require_once "include/validacoes.php";

$idQuarto = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idQuarto <= 0) {
    header("Location: acomodacoes.php");
    exit();
}

$sql = "SELECT q.*, c.nome AS nomeCategoria
        FROM tbquarto q
        JOIN tbcategoria c ON q.fkCategoria = c.id
        WHERE q.id = ?";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $idQuarto);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$linha = mysqli_fetch_assoc($resultado);

if (!$linha) {
    header("Location: acomodacoes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($linha['nome']); ?> - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/quarto.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>

    <?php include_once "include/header.php" ?>

    <main class="main-content">
        <section class="room-details">
            <div class="container">

                <div class="room-info-grid">
                    <div class="room-image-container">
                        <img src="<?= htmlspecialchars($linha['caminhoImg']); ?>" alt="Imagem do quarto" class="main-room-image">
                    </div>
                    <div class="room-text-content">
                        <div class="room-header">
                            <div class="room-title-section">
                                <h1><?= htmlspecialchars($linha['nome']); ?></h1>

                                <div class="room-price-category">
                                    <span class="room-badge"><?= htmlspecialchars($linha['nomeCategoria']); ?></span>
                                    <div class="room-price">
                                        <span class="price">R$ <?= number_format($linha['valorDiaria'], 2, ',', '.'); ?></span>
                                        <span class="period">/noite</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="room-description">
                            <?= htmlspecialchars($linha['descricao']); ?>
                        </p>

                        <div class="room-meta-info">
                            <span><i class="bi bi-people-fill"></i> Até <?= htmlspecialchars($linha['ocupacao']); ?> hóspede(s)</span>
                            <span><i class="bi bi-arrows-fullscreen"></i> <?= htmlspecialchars($linha['area']); ?> m²</span>
                        </div>

                        <div class="room-actions">
                            <a href="reserva.php?categoria=<?= urlencode($linha['fkCategoria']); ?>" class="btn-check-availability" style="text-align: center; text-decoration: none; flex: 1;">
                                Reservar Agora
                            </a>
                            <a href="acomodacoes.php" class="btn-back-rooms">
                                Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once "include/footer.php" ?>

    <style>
        .room-info-grid {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }

        .room-image-container {
            flex: 1 1 55%;
            min-width: 350px;
        }

        .main-room-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .room-text-content {
            flex: 1 1 45%;
            padding: 2rem;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .room-meta-info {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-secondary);
            font-size: var(--font-sm);
            font-weight: 500;
        }

        .room-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .btn-back-rooms {
            padding: var(--size-3) var(--size-4);
            border-radius: var(--size-2);
            border: 1px solid var(--neutral-light);
            background: var(--neutral-extra-light);
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            margin-top: var(--size-2);
            transition: all 0.2s ease;
        }

        .btn-back-rooms:hover {
            background: var(--neutral-light);
        }

        @media (max-width: 768px) {
            .room-info-grid {
                flex-direction: column;
            }
            .room-image-container {
                min-width: 100%;
            }
        }
    </style>
</body>
</html>
