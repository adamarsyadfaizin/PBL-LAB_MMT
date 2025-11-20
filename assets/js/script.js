// Animasi fade-in untuk scrollTopBtn pakai JS murni
const fadeIn = (el) => {
    el.style.opacity = 0;
    el.style.display = "block";
    let last = +new Date();
    const tick = function() {
        el.style.opacity = +el.style.opacity + (new Date() - last) / 200;
        last = +new Date();

        if (+el.style.opacity < 1) {
            (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
        }
    };
    tick();
};

const fadeOut = (el) => {
    el.style.opacity = 1;
    const tick = function() {
        el.style.opacity = +el.style.opacity - 0.05;
        if (+el.style.opacity > 0) {
            (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
        } else {
            el.style.display = "none";
        }
    };
    tick();
};

document.addEventListener("DOMContentLoaded", function() {
    const scrollTopBtn = document.querySelector('#scrollTopBtn');

    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                if (scrollTopBtn.style.display !== "block") fadeIn(scrollTopBtn);
            } else {
                if (scrollTopBtn.style.display !== "none") fadeOut(scrollTopBtn);
            }
        });

        scrollTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
