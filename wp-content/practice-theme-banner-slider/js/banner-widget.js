( function () {
    function initBannerSliders() {
        document.querySelectorAll( '.banner-slider.swiper' ).forEach( function ( el ) {
            if ( el.swiper ) return;
            var config = {};
            try {
                config = JSON.parse( el.getAttribute( 'data-slider-config' ) || '{}' );
            } catch ( e ) {}
            new Swiper( el, config );
        } );
    }

    document.addEventListener( 'DOMContentLoaded', initBannerSliders );

    if ( window.elementorFrontend ) {
        window.elementorFrontend.hooks.addAction(
            'frontend/element_ready/banner_widget.default',
            initBannerSliders
        );
    }
} )();
