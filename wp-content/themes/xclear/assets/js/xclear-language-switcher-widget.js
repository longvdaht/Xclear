(function ($) {
    'use strict';

    var XclearLanguageSwitcher = function ($scope) {
        var $widget = $scope.find('.xclear-language-switcher--pill');
        if (! $widget.length) return;

        var $trigger = $widget.find('.xclear-language-switcher__trigger');

        function closeDropdown() {
            $widget.removeClass('is-open');
            $trigger.attr('aria-expanded', 'false');
        }

        $trigger.on('click.xclear-lang', function (e) {
            e.stopPropagation();
            var isOpen = $widget.hasClass('is-open');
            closeDropdown();
            if (! isOpen) {
                $widget.addClass('is-open');
                $trigger.attr('aria-expanded', 'true');
            }
        });

        $(document).on('click.xclear-lang', function (e) {
            if (! $(e.target).closest('.xclear-language-switcher--pill').length) {
                closeDropdown();
            }
        });

        $(document).on('keydown.xclear-lang', function (e) {
            if (e.key === 'Escape') closeDropdown();
        });
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/xclear_language_switcher_widget.default',
            XclearLanguageSwitcher
        );
    });
})(jQuery);
