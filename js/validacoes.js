function validarCPF(cpf) {

    cpf = cpf.replace(/\D/g, '');
    

    if (cpf.length !== 11) {
        return false;
    }


    if (/^(\d)\1{10}$/.test(cpf)) {
        return false;
    }

    for (let t = 9; t < 11; t++) {
        let d = 0;
        let c = 0;
        for (; c < t; c++) {
            d += parseInt(cpf[c]) * ((t + 1) - c);
        }
        d = ((10 * d) % 11) % 10;
        if (parseInt(cpf[c]) !== d) {
            return false;
        }
    }
    return true;
}

function verifVazio(variavel) {

    if (typeof variavel === 'undefined') {
        return false;
    }
    
    if (variavel === null) {
        return false;
    }

    if (typeof variavel === 'string') {
        return variavel.trim() !== '';
    }

    if (typeof variavel === 'number' || !isNaN(parseFloat(variavel))) {
        return true;
    }

    return variavel !== undefined;
}

function verifInteiroPositivo(valor, permiteZero = false) {

    if (isNaN(valor) || valor === '' || valor === null) {
        return false;
    }

    const numero = parseFloat(valor);

    if (!Number.isInteger(numero)) {
        return false;
    }
    
    if (permiteZero) {
        return numero >= 0;
    } else {
        return numero > 0;
    }
}

function verifFloatPositivo(valor, permiteZero = false) {

    if (isNaN(valor) || valor === '' || valor === null) {
        return false;
    }

    const numero = parseFloat(valor);

    if (permiteZero) {
        return numero >= 0;
    } else {
        return numero > 0;
    }
}

function verifTelefone(telefone) {

    telefone = telefone.replace(/\D/g, '');

    if (telefone.length < 10 || telefone.length > 11) {
        return false;
    } 

    return true;
}

function verifDDD(telefone) {

    telefone = telefone.replace(/\D/g, '');

    if (telefone.length < 2) {
        return false;
    }
    
    const ddd = telefone.substring(0, 2);

    const dddInt = parseInt(ddd, 10);

    return (dddInt >= 11 && dddInt <= 99);
}

function validarEmail(email) {

    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validarSenha(senha) {

    return senha.length >= 8;
}

function validarSenhasIguais(senha, senhaConf) {

    return senha === senhaConf;
}

async function verificarCEP(cep) {

    cep = cep.replace(/\D/g, '');
    
    if (cep.length !== 8) {
        return false;
    }
    
    try {

        const url = `https://viacep.com.br/ws/${cep}/json/`;
        const resposta = await fetch(url);
        
        if (!resposta.ok) {
            return false;
        }
        
        const dados = await resposta.json();
        
        return !dados.erro;
    } catch (erro) {
        console.error('Erro ao verificar CEP:', erro);
        return false;
    }
}

function validarData(data) {

    const regex = /^\d{4}-\d{2}-\d{2}$/;
    if (!regex.test(data)) {
        return false;
    }
    
    const dataObj = new Date(data);
    
    return dataObj instanceof Date && !isNaN(dataObj) && dataObj.toISOString().slice(0, 10) === data;
}

function maiorDeIdade(dataNascimento) {
    if (!validarData(dataNascimento)) {
        return false;
    }
    
    const nascimento = new Date(dataNascimento);
    const hoje = new Date();
    
    let idade = hoje.getFullYear() - nascimento.getFullYear();
    const mes = hoje.getMonth() - nascimento.getMonth();
    
    if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
        idade--;
    }
    
    return idade >= 18;
}

function dataFuturo(data) {
    if (!validarData(data)) {
        return false;
    }
    
    const dataObj = new Date(data);
    const hoje = new Date();

    dataObj.setHours(0, 0, 0, 0);
    hoje.setHours(0, 0, 0, 0);

    return dataObj.getTime() >= hoje.getTime();
}

function dataPassado(data) {
    if (!validarData(data)) {
        return false;
    }
    
    const dataObj = new Date(data);
    const hoje = new Date();
    
    dataObj.setHours(0, 0, 0, 0);
    hoje.setHours(0, 0, 0, 0);

    return dataObj.getTime() <= hoje.getTime();
}

function validarApenasNumeros(texto) {

    if (typeof texto !== 'string') {
        return false;
    }
    
    if (texto.length === 0) {
        return false;
    }

    return /^[0-9]+$/.test(texto);
}

document.addEventListener('DOMContentLoaded', function() {
    
    const campoNome = document.getElementById('nome');
    
    function aplicarEstiloNome(valido) {
        const containerNome = campoNome?.closest('.form-group');
        
        containerNome.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerNome.classList.add('campo-valido');
        } else {
            containerNome.classList.add('campo-invalido');
        }
    }
    
    function validarCampoNome() {
        const valido = verifVazio(campoNome.value);
        aplicarEstiloNome(valido);
        return valido;
    }
    
    if (campoNome) {
        campoNome.addEventListener('blur', validarCampoNome);
        campoNome.addEventListener('input', validarCampoNome);
    }

    const campoCPF = document.getElementById('cpf');
    
    function aplicarEstiloCPF(valido) {
        const containerCPF = campoCPF?.closest('.form-group');
        
        containerCPF.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerCPF.classList.add('campo-valido');
        } else {
            containerCPF.classList.add('campo-invalido');
        }
    }
    
    function validarCampoCPF() {
        const valido = verifVazio(campoCPF.value) && validarCPF(campoCPF.value);
        aplicarEstiloCPF(valido);
        return valido;
    }
    
    if (campoCPF) {
        campoCPF.addEventListener('blur', validarCampoCPF);
        campoCPF.addEventListener('input', validarCampoCPF);
    }

    const campoTell = document.getElementById('tell');
    
    function aplicarEstiloTell(valido) {
        const containerTell = campoTell?.closest('.form-group');
        
        containerTell.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerTell.classList.add('campo-valido');
        } else {
            containerTell.classList.add('campo-invalido');
        }
    }
    
    function validarCampoTell() {
        const valido = verifVazio(campoTell.value) && verifTelefone(campoTell.value) && verifDDD(campoTell.value);
        aplicarEstiloTell(valido);
        return valido;
    }
    
    if (campoTell) {
        campoTell.addEventListener('blur', validarCampoTell);
        campoTell.addEventListener('input', validarCampoTell);
    }

    const campoDataNasc = document.getElementById('dataNasc');
    
    function aplicarEstiloDataNasc(valido) {
        const containerDataNasc = campoDataNasc?.closest('.form-group');
        
        containerDataNasc.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerDataNasc.classList.add('campo-valido');
        } else {
            containerDataNasc.classList.add('campo-invalido');
        }
    }
    
    function validarCampoDataNasc() {
        const valido = verifVazio(campoDataNasc.value) && maiorDeIdade(campoDataNasc.value);
        aplicarEstiloDataNasc(valido);
        return valido;
    }
    
    if (campoDataNasc) {
        campoDataNasc.addEventListener('blur', validarCampoDataNasc);
        campoDataNasc.addEventListener('input', validarCampoDataNasc);
    }

    const campoEmail = document.getElementById('email');
    
    function aplicarEstiloEmail(valido) {
        const containerEmail = campoEmail?.closest('.form-group');
        
        containerEmail.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerEmail.classList.add('campo-valido');
        } else {
            containerEmail.classList.add('campo-invalido');
        }
    }
    
    function validarCampoEmail() {
        const valido = verifVazio(campoEmail.value) && validarEmail(campoEmail.value);
        aplicarEstiloEmail(valido);
        return valido;
    }
    
    if (campoEmail) {
        campoEmail.addEventListener('blur', validarCampoEmail);
        campoEmail.addEventListener('input', validarCampoEmail);
    }

    const campoSenha = document.getElementById('senha');
    
    function aplicarEstiloSenha(valido) {
        const containerSenha = campoSenha?.closest('.form-group');
        
        containerSenha.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerSenha.classList.add('campo-valido');
        } else {
            containerSenha.classList.add('campo-invalido');
        }
    }
    
    function validarCampoSenha() {
        const valido = verifVazio(campoSenha.value) && validarSenha(campoSenha.value);
        aplicarEstiloSenha(valido);
        return valido;
    }
    
    if (campoSenha) {
        campoSenha.addEventListener('blur', validarCampoSenha);
        campoSenha.addEventListener('input', validarCampoSenha);
    }

    const campoSenhaConf = document.getElementById('senhaConf');
    
    function aplicarEstiloSenhaConf(valido) {
        const containerSenhaConf = campoSenhaConf?.closest('.form-group');
        
        containerSenhaConf.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerSenhaConf.classList.add('campo-valido');
        } else {
            containerSenhaConf.classList.add('campo-invalido');
        }
    }
    
    function validarCampoSenhaConf() {
        const valido = verifVazio(campoSenha.value) && verifVazio(campoSenhaConf.value) && validarSenhasIguais(campoSenha.value, campoSenhaConf.value);
        aplicarEstiloSenhaConf(valido);
        return valido;
    }
    
    if (campoSenhaConf) {
        campoSenhaConf.addEventListener('blur', validarCampoSenhaConf);
        campoSenhaConf.addEventListener('input', validarCampoSenhaConf);
    }

    const campoCep = document.getElementById('cep');
    
    function aplicarEstiloCep(valido) {
        const containerCep = campoCep?.closest('.form-group');
        
        containerCep.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerCep.classList.add('campo-valido');
        } else {
            containerCep.classList.add('campo-invalido');
        }
    }
    
    function validarCampoCep() {
        const valido = verifVazio(campoCep.value) && verificarCEP(campoCep.value);
        aplicarEstiloCep(valido);
        return valido;
    }
    
    if (campoCep) {
        campoCep.addEventListener('blur', validarCampoCep);
        campoCep.addEventListener('input', validarCampoCep);
    }

    const campoNumero = document.getElementById('numero');
    
    function aplicarEstiloNumero(valido) {
        const containerNumero = campoNumero?.closest('.form-group');
        
        containerNumero.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerNumero.classList.add('campo-valido');
        } else {
            containerNumero.classList.add('campo-invalido');
        }
    }
    
    function validarCampoNumero() {
        const valido = verifVazio(campoNumero.value) && validarApenasNumeros(campoNumero.value);
        aplicarEstiloNumero(valido);
        return valido;
    }
    
    if (campoNumero) {
        campoNumero.addEventListener('blur', validarCampoNumero);
        campoNumero.addEventListener('input', validarCampoNumero);
    }

    const campoEndereco = document.getElementById('endereco');
    
    function aplicarEstiloEndereco(valido) {
        const containerEndereco = campoEndereco?.closest('.form-group');
        
        containerEndereco.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerEndereco.classList.add('campo-valido');
        } else {
            containerEndereco.classList.add('campo-invalido');
        }
    }
    
    function validarCampoEndereco() {
        const valido = verifVazio(campoEndereco.value);
        aplicarEstiloEndereco(valido);
        return valido;
    }
    
    if (campoEndereco) {
        campoEndereco.addEventListener('blur', validarCampoEndereco);
        campoEndereco.addEventListener('input', validarCampoEndereco);
    }

    const campoBairro = document.getElementById('bairro');
    
    function aplicarEstiloBairro(valido) {
        const containerBairro = campoBairro?.closest('.form-group');
        
        containerBairro.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerBairro.classList.add('campo-valido');
        } else {
            containerBairro.classList.add('campo-invalido');
        }
    }
    
    function validarCampoBairro() {
        const valido = verifVazio(campoBairro.value);
        aplicarEstiloBairro(valido);
        return valido;
    }
    
    if (campoBairro) {
        campoBairro.addEventListener('blur', validarCampoBairro);
        campoBairro.addEventListener('input', validarCampoBairro);
    }

    const campoCidade = document.getElementById('cidade');
    
    function aplicarEstiloCidade(valido) {
        const containerCidade = campoCidade?.closest('.form-group');
        
        containerCidade.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerCidade.classList.add('campo-valido');
        } else {
            containerCidade.classList.add('campo-invalido');
        }
    }
    
    function validarCampoCidade() {
        const valido = verifVazio(campoCidade.value);
        aplicarEstiloCidade(valido);
        return valido;
    }
    
    if (campoCidade) {
        campoCidade.addEventListener('blur', validarCampoCidade);
        campoCidade.addEventListener('input', validarCampoCidade);
    }

    const campoUF = document.getElementById('uf');
    
    function aplicarEstiloUF(valido) {
        const containerUF = campoUF?.closest('.form-group');
        
        containerUF.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerUF.classList.add('campo-valido');
        } else {
            containerUF.classList.add('campo-invalido');
        }
    }
    
    function validarCampoUF() {
        const valido = verifVazio(campoUF.value);
        aplicarEstiloUF(valido);
        return valido;
    }
    
    if (campoUF) {
        campoUF.addEventListener('blur', validarCampoUF);
        campoUF.addEventListener('input', validarCampoUF);
    }

    const campoDataAdmiss = document.getElementById('dataAdmiss');
    
    function aplicarEstiloDataAdmiss(valido) {
        const containerDataAdmiss = campoDataAdmiss?.closest('.form-group');
        
        containerDataAdmiss.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerDataAdmiss.classList.add('campo-valido');
        } else {
            containerDataAdmiss.classList.add('campo-invalido');
        }
    }
    
    function validarCampoDataAdmiss() {
        const valido = verifVazio(campoDataAdmiss.value) && dataPassado(campoDataAdmiss.value);
        aplicarEstiloDataAdmiss(valido);
        return valido;
    }
    
    if (campoDataAdmiss) {
        campoDataAdmiss.addEventListener('blur', validarCampoDataAdmiss);
        campoDataAdmiss.addEventListener('input', validarCampoDataAdmiss);
    }

    const campoOcupacao = document.getElementById('ocupacao');
    
    function aplicarEstiloOcupacao(valido) {
        const containerOcupacao = campoOcupacao?.closest('.form-group');
        
        containerOcupacao.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerOcupacao.classList.add('campo-valido');
        } else {
            containerOcupacao.classList.add('campo-invalido');
        }
    }
    
    function validarCampoOcupacao() {
        const valido = verifVazio(campoOcupacao.value) && verifInteiroPositivo(campoOcupacao.value);
        aplicarEstiloOcupacao(valido);
        return valido;
    }
    
    if (campoOcupacao) {
        campoOcupacao.addEventListener('blur', validarCampoOcupacao);
        campoOcupacao.addEventListener('input', validarCampoOcupacao);
    }

    const campoArea = document.getElementById('area');
    
    function aplicarEstiloArea(valido) {
        const containerArea = campoArea?.closest('.form-group');
        
        containerArea.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerArea.classList.add('campo-valido');
        } else {
            containerArea.classList.add('campo-invalido');
        }
    }
    
    function validarCampoArea() {
        const valido = verifVazio(campoArea.value) && verifInteiroPositivo(campoArea.value);
        aplicarEstiloArea(valido);
        return valido;
    }
    
    if (campoArea) {
        campoArea.addEventListener('blur', validarCampoArea);
        campoArea.addEventListener('input', validarCampoArea);
    }

    const campoDescricao = document.getElementById('descricao');
    
    function aplicarEstiloDescricao(valido) {
        const containerDescricao = campoDescricao?.closest('.form-group');
        
        containerDescricao.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerDescricao.classList.add('campo-valido');
        } else {
            containerDescricao.classList.add('campo-invalido');
        }
    }
    
    function validarCampoDescricao() {
        const valido = verifVazio(campoDescricao.value);
        aplicarEstiloDescricao(valido);
        return valido;
    }
    
    if (campoDescricao) {
        campoDescricao.addEventListener('blur', validarCampoDescricao);
        campoDescricao.addEventListener('input', validarCampoDescricao);
    }

    const campoComodidades = document.getElementById('comodidades');
    
    function aplicarEstiloComodidades(valido) {
        const containerComodidades = campoComodidades?.closest('.form-group');
        
        containerComodidades.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerComodidades.classList.add('campo-valido');
        } else {
            containerComodidades.classList.add('campo-invalido');
        }
    }
    
    function validarCampoComodidades() {
        const valido = verifVazio(campoComodidades.value);
        aplicarEstiloComodidades(valido);
        return valido;
    }
    
    if (campoComodidades) {
        campoComodidades.addEventListener('blur', validarCampoComodidades);
        campoComodidades.addEventListener('input', validarCampoComodidades);
    }

    const campoCategoria = document.getElementById('categoria');
    
    function aplicarEstiloCategoria(valido) {
        const containerCategoria = campoCategoria?.closest('.form-group');
        
        containerCategoria.classList.remove('campo-valido', 'campo-invalido');
        
        if (valido) {
            containerCategoria.classList.add('campo-valido');
        } else {
            containerCategoria.classList.add('campo-invalido');
        }
    }
    
    function validarCampoCategoria() {
        const valido = verifVazio(campoCategoria.value);
        aplicarEstiloCategoria(valido);
        return valido;
    }
    
    if (campoCategoria) {
        campoCategoria.addEventListener('blur', validarCampoCategoria);
        campoCategoria.addEventListener('input', validarCampoCategoria);
    }
});
