(function () {
    function initSlider(element) {
        const swiper = new Swiper(element, {
            speed: 450,
            grabCursor: false,
            pagination: {
                el: element.querySelector('.swiper-pagination'),
                clickable: true,
            },
            on: {
                slideChange() {
                    this.pagination.render();
                    this.pagination.update();
                },
            },
        });
        return swiper;
    }

    function init() {
        document.querySelectorAll('.xclear-hero-slider').forEach(initSlider);
    }

    window.addEventListener('elementor/frontend/init', () => {
        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/xclear_hero_widget.default',
            ($scope) => {
                const slider = $scope[0].querySelector('.xclear-hero-slider');
                if (slider) {
                    initSlider(slider);
                }
            }
        );
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
