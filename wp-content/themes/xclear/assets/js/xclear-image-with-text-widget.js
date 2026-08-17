(function ($) {
    var WidgetImageWithTextHandler = function ($scope, $) {
        var $accordionItems = $scope.find('.xclear-img-with-text__acc__item');

        if (!$accordionItems.length) {
            return;
        }

        // Hide all descriptions initially
        var $descriptions = $accordionItems.find('.xclear-img-with-text__acc__desc');
        $descriptions.hide();

        // Open the first item by default
        $accordionItems.first().addClass('active');
        $accordionItems.first().find('.xclear-img-with-text__acc__desc').show();

        $accordionItems.find('.xclear-img-with-text__acc__title').on('click', function () {
            var $item = $(this).closest('.xclear-img-with-text__acc__item');
            var $desc = $item.find('.xclear-img-with-text__acc__desc');

            if ($item.hasClass('active')) {
                // If already active, close it
                $item.removeClass('active');
                $desc.slideUp();
            } else {
                // Close other items
                $accordionItems.removeClass('active');
                $accordionItems.find('.xclear-img-with-text__acc__desc').slideUp();

                // Open clicked item
                $item.addClass('active');
                $desc.slideDown();
            }
        });
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/xclear_image_with_text_widget.default', WidgetImageWithTextHandler);
    });
})(jQuery);
