(function ($) {
    'use strict';

    var BREAKPOINT = 768;

    // Use matchMedia for reliable detection in DevTools emulation and Elementor editor
    function isMobile() {
        return window.matchMedia('(max-width: ' + BREAKPOINT + 'px)').matches;
    }

    // ── Language-switcher pill init (for pills rendered inside the drawer) ──
    function initLangPill($container) {
        var $pill = $container.find('.xclear-nav-drawer-lang .xclear-language-switcher--pill');
        if (! $pill.length) return;

        var $trigger  = $pill.find('.xclear-language-switcher__trigger');
        var $dropdown = $pill.find('.xclear-language-switcher__dropdown');
        var $drawerNav = $container.find('.xclear-nav-menu');

        function positionDropdown() {
            var rect = $trigger[0].getBoundingClientRect();
            $dropdown.css({
                top:       (rect.bottom + 8) + 'px',
                right:     (window.innerWidth - rect.right) + 'px',
                left:      'auto',
                bottom:    'auto',
                minWidth:  rect.width + 'px'
            });
        }

        function closeDropdown() {
            $pill.removeClass('is-open');
            $trigger.attr('aria-expanded', 'false');
        }

        $trigger.on('click.xclear-lang-drawer', function (e) {
            e.stopPropagation();
            var isOpen = $pill.hasClass('is-open');
            closeDropdown();
            if (! isOpen) {
                positionDropdown();
                $pill.addClass('is-open');
                $trigger.attr('aria-expanded', 'true');
            }
        });

        $(document).on('click.xclear-lang-drawer touchstart.xclear-lang-drawer', function (e) {
            if (! $(e.target).closest('.xclear-language-switcher--pill').length) {
                closeDropdown();
            }
        });

        // Close dropdown when drawer scrolls or any accordion item is tapped
        $drawerNav.on('scroll.xclear-lang-drawer', closeDropdown);
        $drawerNav.on('click.xclear-lang-drawer', '.xclear-nav-menu__trigger', closeDropdown);

        $(document).on('keydown.xclear-lang-drawer', function (e) {
            if (e.key === 'Escape') closeDropdown();
        });
    }

    var XclearNavMenu = function ($scope) {
        var $wrapper   = $scope.find('.xclear-nav-wrapper');
        if (! $wrapper.length) return;

        var $nav       = $wrapper.find('.xclear-nav-menu');
        var $hamburger = $wrapper.find('.xclear-nav-hamburger');
        var $backdrop  = $wrapper.find('.xclear-nav-backdrop');
        var $closeBtn  = $wrapper.find('.xclear-nav-drawer-close');
        var openOn     = $nav.data('open-on') || 'hover';
        var $items     = $nav.find('.xclear-nav-menu__item--dropdown');

        if (! $items.length && ! $hamburger.length) return;

        // Init any language-switcher pill rendered inside this widget's drawer
        initLangPill($wrapper);

        // ── Submenu positioning (desktop only) ──────────────────────────
        function positionSubmenu($item) {
            var $submenu = $item.children('.xclear-nav-menu__submenu');
            if (! $submenu.length) return;
            var rect = $item[0].getBoundingClientRect();
            $submenu.css({ position: 'fixed', top: (rect.bottom + 8) + 'px', left: rect.left + 'px', transform: 'none' });
        }

        // ── Open / close a single item ───────────────────────────────────
        function openItem($item) {
            $item.addClass('is-open');
            $item.children('.xclear-nav-menu__trigger').attr('aria-expanded', 'true');

            if (isMobile()) {
                // Mobile: CSS max-height handles accordion; clear any inline styles
                $item.children('.xclear-nav-menu__submenu').css({ opacity: '', visibility: '', pointerEvents: '' });
                $item.children('.xclear-nav-menu__trigger').find('.xclear-nav-menu__caret').css('transform', 'rotate(90deg)');
            } else {
                positionSubmenu($item);
                $item.children('.xclear-nav-menu__submenu').css({ opacity: '1', visibility: 'visible', pointerEvents: 'auto' });
                $item.children('.xclear-nav-menu__trigger').find('.xclear-nav-menu__caret').css('transform', 'rotate(-90deg)');
            }
        }

        function closeItem($item) {
            $item.removeClass('is-open');
            $item.children('.xclear-nav-menu__trigger').attr('aria-expanded', 'false');

            if (isMobile()) {
                $item.children('.xclear-nav-menu__submenu').css({ opacity: '', visibility: '', pointerEvents: '' });
                $item.children('.xclear-nav-menu__trigger').find('.xclear-nav-menu__caret').css('transform', 'rotate(0deg)');
            } else {
                $item.children('.xclear-nav-menu__submenu').css({ opacity: '0', visibility: 'hidden', pointerEvents: 'none' });
                $item.children('.xclear-nav-menu__trigger').find('.xclear-nav-menu__caret').css('transform', 'rotate(90deg)');
            }
        }

        function closeAll() {
            $items.each(function () { closeItem($(this)); });
        }

        // ── Hamburger / drawer ───────────────────────────────────────────
        function openDrawer() {
            $nav.addClass('is-mobile-open');
            $backdrop.addClass('is-active');
            $hamburger.addClass('is-active').attr('aria-expanded', 'true');
            $('body').addClass('xclear-drawer-open');
        }

        function closeDrawer() {
            $nav.removeClass('is-mobile-open');
            $backdrop.removeClass('is-active');
            $hamburger.removeClass('is-active').attr('aria-expanded', 'false');
            $('body').removeClass('xclear-drawer-open');
            closeAll();
        }

        $hamburger.on('click.xclear-nav', function () {
            if ($nav.hasClass('is-mobile-open')) { closeDrawer(); } else { openDrawer(); }
        });

        $backdrop.on('click.xclear-nav', closeDrawer);
        $closeBtn.on('click.xclear-nav', closeDrawer);

        // ── Dropdown interactions ────────────────────────────────────────
        if (openOn === 'hover') {
            $items.each(function () {
                var $item = $(this);
                var timer = null;

                function cancelClose() { if (timer) { clearTimeout(timer); timer = null; } }

                $item.on('mouseenter.xclear-nav', function () {
                    if (isMobile()) return;
                    cancelClose();
                    openItem($item);
                });

                $item.on('mouseleave.xclear-nav', function () {
                    if (isMobile()) return;
                    timer = setTimeout(function () { closeItem($item); }, 200);
                });

                $item.children('.xclear-nav-menu__submenu')
                    .on('mouseenter.xclear-nav', function () {
                        if (isMobile()) return;
                        cancelClose();
                    })
                    .on('mouseleave.xclear-nav', function () {
                        if (isMobile()) return;
                        timer = setTimeout(function () { closeItem($item); }, 200);
                    });
            });
        } else {
            // Click mode: desktop
            $items.each(function () {
                var $item    = $(this);
                var $trigger = $item.children('.xclear-nav-menu__trigger');

                $trigger.on('click.xclear-nav', function (e) {
                    if (isMobile()) return; // handled below
                    e.stopPropagation();
                    var wasOpen = $item.hasClass('is-open');
                    closeAll();
                    if (! wasOpen) openItem($item);
                });
            });

            $(document).on('click.xclear-nav', function (e) {
                if (! isMobile() && ! $(e.target).closest('.xclear-nav-menu').length) {
                    closeAll();
                }
            });
        }

        // Mobile: accordion toggle — trigger always opens/closes, never navigates
        $items.each(function () {
            var $item    = $(this);
            var $trigger = $item.children('.xclear-nav-menu__trigger');

            $trigger.on('click.xclear-nav-mobile', function (e) {
                if (! isMobile()) return;
                e.preventDefault();
                e.stopPropagation();
                var wasOpen = $item.hasClass('is-open');
                closeAll();
                if (! wasOpen) openItem($item);
            });
        });

        // ── Reposition on scroll / resize (desktop) ──────────────────────
        $(window).on('scroll.xclear-nav resize.xclear-nav', function () {
            if (isMobile()) return;
            $items.filter('.is-open').each(function () { positionSubmenu($(this)); });
        });

        // Close drawer when resizing to desktop
        $(window).on('resize.xclear-nav', function () {
            if (! isMobile() && $nav.hasClass('is-mobile-open')) {
                closeDrawer();
            }
        });

        $(document).on('keydown.xclear-nav', function (e) {
            if (e.key === 'Escape') {
                if ($nav.hasClass('is-mobile-open')) { closeDrawer(); } else { closeAll(); }
            }
        });
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/xclear_nav_dropdown_widget.default',
            XclearNavMenu
        );
    });
})(jQuery);
