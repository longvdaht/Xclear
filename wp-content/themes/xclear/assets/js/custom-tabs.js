(function ($) {
    'use strict';

    $(document).ready(function () {
        var $customTabs = $('.xclear-custom-tabs');
        if ($customTabs.length === 0) return;

        // Function to sanitize title to ID
        function sanitizeTitleToId(title) {
            return title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }

        $customTabs.each(function () {
            var $widget = $(this);
            var $tabs = $widget.find('.e-n-tab-title');
            
            // Map tab titles to IDs and add click listener
            $tabs.each(function () {
                var $tab = $(this);
                var titleText = $tab.find('.e-n-tab-title-text').text();
                var tabId = sanitizeTitleToId(titleText);
                
                // Add a data attribute for easier lookup
                $tab.attr('data-xclear-tab-id', tabId);

                // Add click event to update URL hash
                $tab.on('click', function() {
                    if (history.pushState) {
                        history.pushState(null, null, '#' + tabId);
                    } else {
                        window.location.hash = '#' + tabId;
                    }
                });
            });

            // Check if URL has a hash on load
            var currentHash = window.location.hash.substring(1);
            if (currentHash) {
                var $targetTab = $widget.find('.e-n-tab-title[data-xclear-tab-id="' + currentHash + '"]');
                if ($targetTab.length > 0) {
                    // Elementor might take a moment to initialize its JS, use a slight delay or trigger immediately
                    setTimeout(function() {
                        $targetTab.trigger('click');
                    }, 300);
                }
            }
        });
    });

})(jQuery);
