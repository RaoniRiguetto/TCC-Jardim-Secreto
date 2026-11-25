document.addEventListener('DOMContentLoaded', function () {
    const spaceCards = document.querySelectorAll('.space-card');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');

    let currentIndex = 0;
    const totalSpaces = spaceCards.length;
    let autoPlayTimer = null;

    function showCard(index) {
        spaceCards.forEach(card => card.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        spaceCards[index].classList.add('active');
        dots[index].classList.add('active');

        currentIndex = index;
    }

    // reinicia o timer automático quando o usuário troca de slide manualmente
    function resetTimer() {
        if (autoPlayTimer) {
            clearInterval(autoPlayTimer);
        }
        autoPlayTimer = setInterval(() => {
            let nextIndex = currentIndex + 1;
            if (nextIndex >= totalSpaces) {
                nextIndex = 0;
            }
            showCard(nextIndex);
        }, 10000);
    }

    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showCard(index);
            resetTimer();
        });
    });

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            let nextIndex = currentIndex + 1;
            if (nextIndex >= totalSpaces) {
                nextIndex = 0;
            }
            showCard(nextIndex);
            resetTimer();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            let prevIndex = currentIndex - 1;
            if (prevIndex < 0) {
                prevIndex = totalSpaces - 1;
            }
            showCard(prevIndex);
            resetTimer();
        });
    }

    resetTimer();
});
