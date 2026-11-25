<?php
// valida CPF pelos dois dígitos verificadores
function validarCPF($cpf) {

    if (strlen($cpf) != 11) {
        return false;
    }

    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

function verifVazio($variavel) {

    if (!isset($variavel)) {
        return false;
    }

    if (is_null($variavel)) {
        return false;
    }

    if (is_string($variavel)) {
        return trim($variavel) !== '';
    }

    if (is_numeric($variavel)) {
        return true;
    }
}

function validarApenasNumeros($texto) {

    if (!is_string($texto)) {
        return false;
    }

    if (strlen($texto) === 0) {
        return false;
    }

    return preg_match('/^[0-9]+$/', $texto) === 1;
}

function verifInteiroPositivo($valor, $permiteZero = false) {

    if (!is_numeric($valor)) {
        return false;
    }

    $numero = floatval($valor);

    if ($numero != intval($numero)) {
        return false;
    }
    
    if ($permiteZero) {
        return $numero >= 0;
    } else {
        return $numero > 0;
    }
}

function verifFloatPositivo($valor, $permiteZero = false) {

    if (!is_numeric($valor)) {
        return false;
    }
    
    $numero = floatval($valor);
    
    if ($permiteZero) {
        return $numero >= 0;
    } else {
        return $numero > 0;
    }
}

// converte formato brasileiro (1.250,50) para padrão numérico do MySQL (1250.50)
function formatarDecimal($valor) {

    $valor = preg_replace('/[^0-9,.-]/', '', $valor);
    
    if (empty($valor)) {
        return '0';
    }
    
    $valor = str_replace('.', '', $valor);
    

    $valor = str_replace(',', '.', $valor);
    

    $contagemPontos = substr_count($valor, '.');
    if ($contagemPontos > 1) {

        $partes = explode('.', $valor);
        $inteiro = array_shift($partes);
        $valor = $inteiro . '.' . implode('', $partes);
    }
    
    return $valor;
}

function verifTelefone($telefone) {

    if (strlen($telefone) < 10 || strlen($telefone) > 11) {
        return false;
    } 

    return true;
    }

function verifDDD($telefone) {

    $ddd = substr($telefone, 0, 2);
    
    $dddInt = intval($ddd);
    
    return ($dddInt >= 11 && $dddInt <= 99);
}

function validarEmail($email) {

    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validarSenha($senha) {

    return strlen($senha) >= 8;
}

function validarSenhasIguais($senha, $senhaConf) {

    return $senha === $senhaConf;
}

function validarData($data) {

    $dataObj = DateTime::createFromFormat('Y-m-d', $data);
    
    return $dataObj && $dataObj->format('Y-m-d') === $data;
}

function maiorDeIdade($dataNascimento) {
    if (!validarData($dataNascimento)) {
        return false;
    }
    
    $nascimento = new DateTime($dataNascimento);
    $hoje = new DateTime();
    $diferenca = date_diff($nascimento, $hoje);
    
    return $diferenca->y >= 18;
}

function dataFuturo($data, $permiteHoje = false) {
    if (!validarData($data)) {
        return false;
    }
    
    $dataObj = new DateTime($data);
    $hoje = new DateTime();

    $hoje->setTime(0, 0, 0);
    $dataObj->setTime(0, 0, 0);

    if($permiteHoje){
        return $dataObj >= $hoje;
    }else{
        return $dataObj > $hoje;
    }
}

function dataPassado($data, $permiteHoje = false) {
    if (!validarData($data)) {
        return false;
    }
    
    $dataObj = new DateTime($data);
    $hoje = new DateTime();
    
    $hoje->setTime(0, 0, 0);
    $dataObj->setTime(0, 0, 0);

    if($permiteHoje){
        return $dataObj <= $hoje;
    }else{
        return $dataObj < $hoje;
    }
}

// garante check-in a partir de hoje e check-out pelo menos um dia depois
function validarDatasReserva($dataCheckIn, $dataCheckOut) {

    $checkIn = DateTime::createFromFormat('Y-m-d', $dataCheckIn);
    $checkOut = DateTime::createFromFormat('Y-m-d', $dataCheckOut);
    
    if (!$checkIn || !$checkOut) {
        return false;
    }
    

    if ($checkIn >= $checkOut) {
        return false;
    }
    
    $hoje = new DateTime();
    $hoje->setTime(0, 0, 0);
    
    if ($checkIn < $hoje) {
        return false;
    }
    
    return true;
}

function calcularDiasEstadia($checkin, $checkout) {

    $dataInicio = new DateTime($checkin);
    $dataFim = new DateTime($checkout);
    
    $diferenca = $dataInicio->diff($dataFim);
    
    return $diferenca->days;
}