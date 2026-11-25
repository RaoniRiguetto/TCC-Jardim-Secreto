<?php
    require_once "include/conexao.php";
    require_once "include/validacoes.php";
    require_once "include/verifSessionUsuario.php";

    $quarto = null;
    $valorTotal = 0;
    $qteDiarias = 0;
    $opcoesParcelamento = '';
    $dadosBusca = $_SESSION['dadosBusca'] ?? [];

    if (isset($_POST['pagamento']) && isset($_POST['idQuarto']) && !empty($dadosBusca)) {
        $idUsuario = $_SESSION['id'];
        $idQuarto = intval($_POST['idQuarto']);

        $dataCheckIn = $dadosBusca['checkin'];
        $dataCheckOut = $dadosBusca['checkout'];
        $qteAdultos = intval($dadosBusca['qteAdultos']);
        $qteCriancas = intval($dadosBusca['qteCriancas']);

        $qteDiarias = calcularDiasEstadia($dataCheckIn, $dataCheckOut);

        $sql = "SELECT * FROM tbquarto WHERE id = ?";
        $stmtQuarto = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmtQuarto, "i", $idQuarto);
        mysqli_stmt_execute($stmtQuarto);
        $resultado = mysqli_stmt_get_result($stmtQuarto);
        $quarto = mysqli_fetch_assoc($resultado);

        if (!$quarto) {
            header("Location: reserva.php");
            exit();
        }

        $valorTotal = $quarto['valorDiaria'] * $qteDiarias;
        for ($i = 1; $i <= 12; $i++) {
            $valorParcela = $valorTotal / $i;
            $valorFormatado = 'R$ ' . number_format($valorParcela, 2, ',', '.');
            $opcoesParcelamento .= "<option value='{$i}'>{$i}x de {$valorFormatado}</option>";
        }
        $_SESSION['dadosBusca']['idQuarto'] = $idQuarto;

    } elseif (isset($_POST['finalizar']) && !empty($dadosBusca['idQuarto'])) {

        $pagamento = $_POST['formaPagamento'] ?? 'credito';
        $parcelas = ($pagamento === 'credito') ? max(1, intval($_POST['parcelas'] ?? 1)) : 1;
        $dataCheckIn = $dadosBusca['checkin'];
        $dataCheckOut = $dadosBusca['checkout'];
        $qteAdultos = intval($dadosBusca['qteAdultos']);
        $qteCriancas = intval($dadosBusca['qteCriancas']);
        $idQuarto = intval($dadosBusca['idQuarto']);
        $idUsuario = intval($_SESSION['id']);

        $sqlQuarto = "SELECT valorDiaria FROM tbquarto WHERE id = ?";
        $stmtQuarto = mysqli_prepare($conexao, $sqlQuarto);
        mysqli_stmt_bind_param($stmtQuarto, "i", $idQuarto);
        mysqli_stmt_execute($stmtQuarto);
        $resQuarto = mysqli_stmt_get_result($stmtQuarto);
        $quartoDados = mysqli_fetch_assoc($resQuarto);
        if (!$quartoDados) {
            header("Location: reserva.php");
            exit();
        }

        $qteDiarias = calcularDiasEstadia($dataCheckIn, $dataCheckOut);
        // recalcula no servidor para evitar alteração no valor enviado pelo formulário
        $valorTotal = $quartoDados['valorDiaria'] * $qteDiarias;
        if ($pagamento === 'pix') {
            $valorTotal = round($valorTotal * 0.95, 2);
        }

        $sql = "INSERT INTO tbreserva (dataChekIn, dataChekOut, qteAdultos, qteCriancas, valor, qteParcelas, formaPagamento, fkQuarto, fkUsuario)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ssiidisii", $dataCheckIn, $dataCheckOut, $qteAdultos, $qteCriancas, $valorTotal, $parcelas, $pagamento, $idQuarto, $idUsuario);
        mysqli_stmt_execute($stmt);
        unset($_SESSION['dadosBusca']);
        $_SESSION['msgReserva'] = "Reserva realizada com sucesso!";
        header("Location: painelUsuario.php");
        exit();

    } else {
        header("Location: reserva.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento - Jardim Secreto</title>
    <link rel="shortcut icon" href="img/icons/logo.png" type="image/x-icon">

    <link rel="stylesheet" href="css/var.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/pagamento.css">
</head>
<body>
    <main class="main-content">
        <div class="payment-container">
            <div class="payment-layout">
                <div class="reservation-summary">
                    <h2>Resumo da Reserva</h2>
                    <div class="summary-card">
                        <div class="summary-item">
                            <strong>Quarto:</strong>
                            <span><?= htmlspecialchars($quarto['nome'] ?? 'N/A') ?></span>
                        </div>
                        <div class="summary-item">
                            <strong>Check-in:</strong>
                            <span><?= htmlspecialchars($dataCheckIn ?? 'N/A') ?></span>
                        </div>
                        <div class="summary-item">
                            <strong>Check-out:</strong>
                            <span><?= htmlspecialchars($dataCheckOut ?? 'N/A') ?></span>
                        </div>
                        <div class="summary-item">
                            <strong>Hóspedes:</strong>
                            <span><?= htmlspecialchars($qteAdultos ?? 0) ?> Adulto(s) <?php if(isset($qteCriancas) && $qteCriancas > 0){ echo htmlspecialchars($qteCriancas) . " Criança(s)"; } ?></span>
                        </div>
                        <div class="summary-item">
                            <strong>Diarias:</strong>
                            <span><?= htmlspecialchars($qteDiarias) ?> noite(s)</span>
                        </div>
                        <div class="price-breakdown">
                            <div class="price-item total">
                                <strong>Total</strong>
                                <strong id="valor-total-exibido" data-base-total="<?= htmlspecialchars($valorTotal) ?>">R$ <?= number_format($valorTotal, 2, ',', '.') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="payment-form-section">
                    <div class="payment-card">
                        <div class="payment-header">
                            <h1>Finalizar Pagamento</h1>
                            <p>Complete seu pagamento para confirmar a reserva</p>
                        </div>

                        <form class="payment-form" method="POST">
                            <input type="hidden" name="valorTotal" value="<?= htmlspecialchars($valorTotal) ?>">

                            <div class="form-section">
                                <h2> Método de Pagamento</h2>
                                <div class="payment-methods">
                                    <div class="payment-option">
                                        <input type="radio" id="payment-credit" name="formaPagamento" value="credito" required checked>
                                        <label for="payment-credit">
                                            <strong>Cartão de Crédito</strong>
                                            <small>Até 12x sem juros</small>
                                        </label>
                                    </div>
                                    
                                    <div class="payment-option">
                                        <input type="radio" id="payment-debit" name="formaPagamento" value="debito">
                                        <label for="payment-debit">
                                            <strong>Cartão de Débito</strong>
                                            <small>Pagamento à vista</small>
                                        </label>
                                    </div>
                                    
                                    <div class="payment-option">
                                        <input type="radio" id="payment-pix" name="formaPagamento" value="pix">
                                        <label for="payment-pix">
                                            <strong>PIX</strong>
                                            <small>5% de desconto</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h2> Dados do Cartão</h2>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="card-name">Nome no Cartão *</label>
                                        <input type="text" id="card-name" placeholder="Como está no cartão" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="card-number">Número do Cartão *</label>
                                        <input type="text" id="card-number" placeholder="0000 0000 0000 0000" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="card-expiry">Validade *</label>
                                        <input type="text" id="card-expiry" placeholder="MM/AA" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="card-cvv">CVV *</label>
                                        <input type="text" id="card-cvv" placeholder="000" required>
                                    </div>
                                    <div class="form-group full-width">
                                        <label for="card-installments">Parcelas *</label>
                                        <select id="card-installments" required name="parcelas">
                                            <?= $opcoesParcelamento ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section buttons-section">
                                <div class="buttons-grid">
                                    <button type="submit" class="btn-confirm" name="finalizar">Confirmar Pagamento</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
    document.addEventListener('DOMContentLoaded', function() {

    const paymentCredit = document.getElementById('payment-credit');
    const paymentDebit = document.getElementById('payment-debit');
    const paymentPix = document.getElementById('payment-pix');
    
    const todasAsSecoes = document.querySelectorAll('.form-section');
    const secaoCartao = todasAsSecoes[1];
    
    const cardName = document.getElementById('card-name');
    const cardNumber = document.getElementById('card-number');
    const cardExpiry = document.getElementById('card-expiry');
    const cardCvv = document.getElementById('card-cvv');
    const cardInstallments = document.getElementById('card-installments');
    const installmentsGroup = document.querySelector('.form-group.full-width');
    
    // alterna campos do cartão e mostra o desconto do pix na tela
    function mostrarOuEsconderCampos() {
        if (!secaoCartao) return;

        const valorTotalEl = document.getElementById('valor-total-exibido');
        const baseTotal = valorTotalEl ? parseFloat(valorTotalEl.dataset.baseTotal) : 0;

        if (paymentPix.checked) {

            secaoCartao.style.display = 'none';
            cardName.required = false;
            cardNumber.required = false;
            cardExpiry.required = false;
            cardCvv.required = false;
            cardInstallments.required = false;
            if (valorTotalEl) {
                const comDesconto = baseTotal * 0.95;
                valorTotalEl.textContent = 'R$ ' + comDesconto.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' (5% OFF)';
            }
            
        } else if (paymentDebit.checked) {

            secaoCartao.style.display = 'block';
            if (installmentsGroup) installmentsGroup.style.display = 'none';
            
            cardName.required = true;
            cardNumber.required = true;
            cardExpiry.required = true;
            cardCvv.required = true;
            cardInstallments.required = false;
            if (valorTotalEl) {
                valorTotalEl.textContent = 'R$ ' + baseTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            
        } else if (paymentCredit.checked) {

            secaoCartao.style.display = 'block';
            if (installmentsGroup) installmentsGroup.style.display = 'block';
            
            cardName.required = true;
            cardNumber.required = true;
            cardExpiry.required = true;
            cardCvv.required = true;
            cardInstallments.required = true;
            if (valorTotalEl) {
                valorTotalEl.textContent = 'R$ ' + baseTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }
    }
    
    if (paymentCredit && paymentDebit && paymentPix && secaoCartao) {
        paymentCredit.addEventListener('change', mostrarOuEsconderCampos);
        paymentDebit.addEventListener('change', mostrarOuEsconderCampos);
        paymentPix.addEventListener('change', mostrarOuEsconderCampos);
        
        mostrarOuEsconderCampos();
    }

    if (cardNumber) {
        cardNumber.addEventListener('input', function(e) {
            let valor = e.target.value.replace(/\D/g, '');
            valor = valor.substring(0, 16);
            
            let formatado = '';
            for (let i = 0; i < valor.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formatado += ' ';
                }
                formatado += valor[i];
            }
            
            e.target.value = formatado;
        });
    }
    
    if (cardExpiry) {
        cardExpiry.addEventListener('input', function(e) {
            let valor = e.target.value.replace(/\D/g, '');
            valor = valor.substring(0, 4);
            
            if (valor.length >= 2) {
                let mes = valor.substring(0, 2);
                let ano = valor.substring(2, 4);
                
                if (parseInt(mes) > 12) mes = '12';
                if (parseInt(mes) < 1) mes = '01';
                
                e.target.value = mes + (ano ? '/' + ano : '');
            } else {
                e.target.value = valor;
            }
        });
        
        cardExpiry.addEventListener('blur', function(e) {
            let valor = e.target.value;
            if (valor.length === 5) {
                let [mes, ano] = valor.split('/');
                let hoje = new Date();
                let anoAtual = hoje.getFullYear() % 100;
                let mesAtual = hoje.getMonth() + 1;
                
                if (parseInt(ano) < anoAtual || 
                    (parseInt(ano) === anoAtual && parseInt(mes) < mesAtual)) {
                    alert('Atenção: Este cartão pode estar expirado. Verifique a data.');
                }
            }
        });
    }
    
    if (cardCvv) {
        cardCvv.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });
    }
    
    if (cardName) {
        cardName.addEventListener('input', function(e) {
            
            e.target.value = e.target.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
        });
    }
    
    const formPagamento = document.querySelector('.payment-form');
    if (formPagamento) {
        formPagamento.addEventListener('submit', function(e) {
            
            if (paymentPix && paymentPix.checked) {
                return true; 
            }
            
            
            let problemas = [];
            
            if (cardName && cardName.value.trim().length < 3) {
                problemas.push('• Nome no cartão muito curto');
                cardName.focus();
            }
            
            if (cardNumber && cardNumber.value.replace(/\D/g, '').length < 13) {
                problemas.push('• Número do cartão inválido (mínimo 13 dígitos)');
                if (!cardName.value) cardNumber.focus();
            }
            
            if (cardExpiry && cardExpiry.value.length !== 5) {
                problemas.push('• Data de validade incompleta (use MM/AA)');
                if (!cardNumber.value) cardExpiry.focus();
            }
            
            if (cardCvv && cardCvv.value.length < 3) {
                problemas.push('• CVV inválido (mínimo 3 dígitos)');
                if (!cardExpiry.value) cardCvv.focus();
            }
            
            if (paymentCredit && paymentCredit.checked && cardInstallments && !cardInstallments.value) {
                problemas.push('• Selecione o número de parcelas');
            }
            
            
            if (problemas.length > 0) {
                e.preventDefault();
                alert('Por favor, corrija:\n\n' + problemas.join('\n'));
                return false;
            }
            
            return true;
        });
    }
});
    </script>
</body>
</html>