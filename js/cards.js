    // fecha automaticamente os avisos flutuantes depois de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
        const errorMessages = document.querySelectorAll('.card-erro-flutuante');
    
        errorMessages.forEach((errorMessage) => {
            setTimeout(() => {
                errorMessage.classList.add('hidden');
            
                setTimeout(() => {
                    if (errorMessage.parentNode) {
                        errorMessage.remove();
                    }
                }, 800);
            }, 5000);
        });
    });

document.addEventListener('DOMContentLoaded', function() {
        const sucessMessages = document.querySelectorAll('.card-sucesso-flutuante');
    
        sucessMessages.forEach((sucessMessage) => {
            setTimeout(() => {
                sucessMessage.classList.add('hidden');
            
                setTimeout(() => {
                    if (sucessMessage.parentNode) {
                        sucessMessage.remove();
                    }
                }, 800);
            }, 5000);
        });
    });