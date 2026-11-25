<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Página Inicial - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>

    <?php include_once "include/header.php" ?>

    <main class="main-content">

        <section class="hero-index">
            <div class="hero-image">
                <img src="img/BannerAreaExterna.png" alt="Jardim Secreto Hotel">
            </div>
            <div class="hero-overlay">
                <div class="hero-content">
                    <h1>Bem-vindo ao lugar de paz e tranquilidade</h1>
                    <p>Descubra a harmonia entre conforto e natureza em cada detalhe da sua estadia</p>
                    <a href="acomodacoes.php" class="btn-primary">Explorar Acomodações</a>
                </div>
            </div>
        </section>

        <section class="about-section">
            <div class="container">
                <div class="about-card">
                    <div class="about-text">
                        <h2>Nossa Essência</h2>
                        <p>Criado em 2024 o Hotel Jardim Secreto visa o conforto e a experiência única de luxo, entrelaçado
                            com a natureza.  </p>

                        <h3>Um Encontro com a Natureza</h3>
                        <p>Localizado em meio a uma área preservada, nosso hotel oferece o equilíbrio perfeito entre o
                            conforto
                            moderno e a beleza natural. Aqui, cada amanhecer é uma celebração e cada pôr do sol, um
                            espetáculo único.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="spaces-section">
            <div class="container">
                <h2 class="section-title">Nossos Ambientes</h2>

                <div class="spaces-carousel">
                    <div class="carousel-container">

                        <div class="space-card active">
                            <div class="space-image">
                                <img src="img/Restaurante-Hotel.png" alt="Restaurante">
                            </div>
                            <div class="space-content">
                                <h3>Restaurante Natural</h3>
                                <p>Experiência gastronômica com ingredientes frescos da nossa horta orgânica, preparados
                                    por chefs
                                    especializados.</p>
                                <ul class="space-features">
                                    <li>Café da manhã buffet</li>
                                    <li>Almoço à la carte</li>
                                    <li>Jantar à luz de velas</li>
                                    <li>Capacidade: 80 pessoas</li>
                                </ul>
                            </div>
                        </div>

                        <div class="space-card">
                            <div class="space-image">
                                <img src="img/Piscina-Hotel.png" alt="Piscina">
                            </div>
                            <div class="space-content">
                                <h3>Área da Piscina</h3>
                                <p>Ambiente relaxante com piscina aquecida e área de descanso, perfeito para momentos de
                                    lazer e
                                    descontração.</p>
                                <ul class="space-features">
                                    <li>Piscina aquecida</li>
                                    <li>Área de descanso</li>
                                    <li>Bar molhado</li>
                                    <li>Horário estendido</li>
                                </ul>
                            </div>
                        </div>

                        <div class="space-card">
                            <div class="space-image">
                                <img src="img/Terraço-Hotel.png" alt="Terraço">
                            </div>
                            <div class="space-content">
                                <h3>Terraço Panorâmico</h3>
                                <p>Vistas deslumbrantes da natureza em um ambiente perfeito para relaxar.</p>
                                <ul class="space-features">
                                    <li>Vista privilegiada da natureza</li>
                                    <li>Área de lounge exclusiva</li>
                                    <li>Bar service completo</li>
                                    <li>Capacidade: 50 pessoas</li>
                                </ul>
                            </div>
                        </div>

                        <div class="space-card">
                            <div class="space-image">
                                <img src="img/Lobby-Hotel.png" alt="Lobby">
                            </div>
                            <div class="space-content">
                                <h3>Lobby Aconchegante</h3>
                                <p>Ambiente que recebe nossos hóspedes com o calor de um lar, onde cada detalhe foi
                                    pensado para seu
                                    conforto.</p>
                                <ul class="space-features">
                                    <li>Lareira aconchegante</li>
                                    <li>Biblioteca selecionada</li>
                                    <li>Recepção 24 horas</li>
                                    <li>Wi-Fi gratuito</li>
                                </ul>
                            </div>
                        </div>

                        <div class="space-card">
                            <div class="space-image">
                                <img src="img/Estacionamento-Hotel.png" alt="Estacionamento">
                            </div>
                            <div class="space-content">
                                <h3>Estacionamento Seguro</h3>
                                <p>Área exclusiva para hóspedes com segurança 24 horas e facilidades para sua
                                    comodidade.</p>
                                <ul class="space-features">
                                    <li>Vagas cobertas</li>
                                    <li>Segurança 24h</li>
                                    <li>Carregador elétrico</li>
                                    <li>Lavagem gratuita</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="carousel-controls">
                        <button class="carousel-prev"><i class="bi bi-caret-left"></i></button>
                        <div class="carousel-dots">
                            <span class="dot active" data-index="0"></span>
                            <span class="dot" data-index="1"></span>
                            <span class="dot" data-index="2"></span>
                            <span class="dot" data-index="3"></span>
                            <span class="dot" data-index="4"></span>
                        </div>
                        <button class="carousel-next"><i class="bi bi-caret-right"></i></button>
                    </div>

                </div>
            </div>
        </section>

        
    </main>

    <?php include_once "include/footer.php" ?>

    <script src="js/carrosel.js"></script>
</body>

</html>