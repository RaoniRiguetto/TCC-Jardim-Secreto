function formatarTelefone(input) {

    let valor = input.value.replace(/\D/g, '');
    

    if (valor.length <= 10) {

        valor = valor.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else {

        valor = valor.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    }

    input.value = valor;
}

function formatarCEP(input) {
    let valor = input.value.replace(/\D/g, '');
    
    if (valor.length > 5) {
        valor = valor.replace(/(\d{5})(\d{0,3})/, '$1-$2');
    }
    
    input.value = valor;
}

function formatarNumeros(input) {

    input.value = input.value.replace(/\D/g, '');
}

function formatarCPF(input) {
    let valor = input.value.replace(/\D/g, '');

    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    
    input.value = valor;
}

function formatarMoeda(input) {

    let valor = input.value.replace(/\D/g, '');
    
    valor = (valor / 100).toFixed(2);
    valor = valor.replace('.', ',');
    valor = valor.replace(/(\d)(?=(\d{3})+,)/g, '$1.');
    
    input.value = valor ? 'R$ ' + valor : '';
}

function formatarValor(input) {
    let valor = input.value.replace(/\D/g, '');
    
    if (valor.length === 0) {
        input.value = '';
        return;
    }
    valor = (parseInt(valor, 10) / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    input.value = valor;
}

function validarSenha(input) {
    const senha = input.value;

    if (senha.length >= 8) {
        input.style.borderColor = 'green';
    } else {
        input.style.borderColor = 'red';
    }

    const contador = document.getElementById('contador-senha');
    if (contador) {
        contador.textContent = `${senha.length}/8 caracteres`;
        contador.style.color = senha.length >= 8 ? 'green' : 'red';
    }
}

// consulta a API do ViaCEP ao sair do campo CEP e preenche o endereço
async function preencherEnderecoPorCEP(cepInput) {
    const cep = cepInput.value.replace(/\D/g, '');
    
    if (cep.length !== 8) {
        return;
    }

    const enderecoInput = document.getElementById('endereco');
    const bairroInput = document.getElementById('bairro');
    const cidadeInput = document.getElementById('cidade');
    const estadoSelect = document.getElementById('uf');
    

    cepInput.style.borderColor = 'orange';
    
    try {

        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const dados = await response.json();
        
        if (dados.erro) {

            cepInput.style.borderColor = 'red';
            alert('CEP não encontrado! Verifique o número digitado.');
        } else {

            if (enderecoInput) enderecoInput.value = dados.logradouro || '';
            if (bairroInput) bairroInput.value = dados.bairro || '';
            if (cidadeInput) cidadeInput.value = dados.localidade || '';
            if (estadoSelect) estadoSelect.value = dados.uf || '';
            
            cepInput.style.borderColor = 'green';
        }
        
    } catch (error) {

        cepInput.style.borderColor = 'red';
        alert('Erro ao buscar CEP. Tente novamente.');
    }
}

// nas telas de edição, aplica a máscara nos valores que vêm sem formatação do banco
document.addEventListener('DOMContentLoaded', function() {
    const cpfInput = document.getElementById('cpf');
    if (cpfInput && cpfInput.value) formatarCPF(cpfInput);

    const tellInput = document.getElementById('tell');
    if (tellInput && tellInput.value) formatarTelefone(tellInput);

    const cepInput = document.getElementById('cep');
    if (cepInput && cepInput.value) formatarCEP(cepInput);
});
