(function ($) {
    'use strict';

    var $lightbox    = null;
    var $player      = null;
    var $backdrop    = null;
    var $closeBtn    = null;
    var isLightboxInit = false;

    function initLightbox() {
        if (isLightboxInit) return;
        isLightboxInit = true;

        $lightbox  = $('#xclear-vg-lightbox');
        $player    = $lightbox.find('.xclear-vg-lightbox__player');
        $backdrop  = $lightbox.find('.xclear-vg-lightbox__backdrop');
        $closeBtn  = $lightbox.find('.xclear-vg-lightbox__close');

        // Close on backdrop click
        $backdrop.on('click', closeLightbox);
        $closeBtn.on('click', closeLightbox);

        // Close on Escape key
        $(document).on('keydown.xclearVg', function (e) {
            if (e.key === 'Escape') closeLightbox();
        });
    }

    function openLightbox(videoUrl, videoType, title) {
        if (!$lightbox) return;

        // Build player HTML
        var playerHtml = '';

        if (videoType === 'iframe') {
            playerHtml = '<iframe src="' + videoUrl + '" allowfullscreen allow="autoplay; encrypted-media" title="' + title + '"></iframe>';
        } else {
            // HTML5 video (direct MP4 / WebM)
            playerHtml = '<video src="' + videoUrl + '" controls autoplay></video>';
        }

        $player.html(playerHtml);
        $lightbox.removeAttr('hidden').addClass('is-visible').removeClass('is-closing');

        // Prevent body scroll
        $('body').css('overflow', 'hidden');

        $closeBtn.focus();
    }

    function closeLightbox() {
        if (!$lightbox || $lightbox.attr('hidden') !== undefined) return;

        $lightbox.addClass('is-closing').removeClass('is-visible');

        setTimeout(function () {
            $lightbox.attr('hidden', '').removeClass('is-closing');
            $player.html(''); // Stop video by removing iframe/video
            $('body').css('overflow', '');
        }, 200);
    }

    function initVideoGallery($scope) {
        // Ensure lightbox is initialized (runs once)
        initLightbox();

        var $cards = $scope.find('.xclear-vg__card');

        $cards.each(function () {
            var $card     = $(this);
            var videoUrl  = $card.data('video-url');
            var videoType = $card.data('video-type') || 'iframe';
            var title     = $card.data('video-title') || '';

            $card.on('click', function () {
                openLightbox(videoUrl, videoType, title);
            });

            // Also handle keyboard activation
            $card.on('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openLightbox(videoUrl, videoType, title);
                }
            });
        });
    }

    /* ── Register with Elementor frontend ────────────────────────────────── */
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/xclear_video_gallery_widget.default',
            initVideoGallery
        );
    });
})(jQuery);
