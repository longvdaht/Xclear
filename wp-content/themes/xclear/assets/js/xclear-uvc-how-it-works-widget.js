(function ($) {
    function initDragScroll(el) {
        let startX, startScrollLeft;
        let isDragging = false;

        el.addEventListener('mousedown', (e) => {
            isDragging    = true;
            startX        = e.pageX;
            startScrollLeft = el.scrollLeft;
            el.classList.add('is-dragging');
        });

        el.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            el.scrollLeft = startScrollLeft - (e.pageX - startX);
        });

        ['mouseup', 'mouseleave'].forEach((event) => {
            el.addEventListener(event, () => {
                isDragging = false;
                el.classList.remove('is-dragging');
            });
        });
    }

    function onWidgetReady($scope) {
        const stepsWrap = $scope.find('.xclear-uvc-how-it-works__steps-wrap')[0];
        if (stepsWrap) {
            initDragScroll(stepsWrap);
        }
    }

    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/xclear_uvc_how_it_works_widget.default',
            onWidgetReady
        );
    });
})(jQuery);
