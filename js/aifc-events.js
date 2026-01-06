// Créez le fichier js/aifc-events.js avec ce contenu :
// Slider automatique
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le slider
    const sliders = document.querySelectorAll('.aifc-event-slider');
    
    sliders.forEach(slider => {
        const autoplay = slider.getAttribute('data-autoplay') || 5000;
        const effect = slider.getAttribute('data-effect') || 'slide';
        
        new Swiper(slider, {
            loop: true,
            effect: effect,
            speed: 600,
            autoplay: {
                delay: parseInt(autoplay),
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                    spaceBetween: 10
                },
                768: {
                    slidesPerView: 1,
                    spaceBetween: 20
                },
                1024: {
                    slidesPerView: 1,
                    spaceBetween: 30
                }
            }
        });
    });
    
    // Countdown timer
    const countdownElements = document.querySelectorAll('.aifc-countdown-timer');
    
    countdownElements.forEach(timer => {
        const eventDate = new Date(timer.getAttribute('data-date')).getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = eventDate - now;
            
            if (distance < 0) {
                timer.innerHTML = '<div class="aifc-countdown-item"><span class="aifc-countdown-number">Événement en cours!</span></div>';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            const daysEl = timer.querySelector('[data-days]');
            const hoursEl = timer.querySelector('[data-hours]');
            const minutesEl = timer.querySelector('[data-minutes]');
            const secondsEl = timer.querySelector('[data-seconds]');
            
            if (daysEl) daysEl.textContent = days.toString().padStart(2, '0');
            if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
            if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
            if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
    
    // Smooth scroll pour les ancres CTA
    document.querySelectorAll('.aifc-cta-btn[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
