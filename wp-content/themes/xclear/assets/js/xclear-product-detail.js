(function () {
    'use strict';

    var sliderDragged = false;

    // ── Image slider (gallery below featured image) ───────────────────────────
    function initSlider() {
        var sliders = document.querySelectorAll('.product-detail__slider');
        if (!sliders.length) return;

        sliders.forEach(function (slider) {
            var track     = slider.querySelector('.product-detail__slider-track');
            var trackWrap = slider.querySelector('.product-detail__slider-track-wrap');
            var prevBtn   = slider.querySelector('.product-detail__slider-prev');
            var nextBtn   = slider.querySelector('.product-detail__slider-next');
            var slides    = slider.querySelectorAll('.product-detail__slide');

            if (!track || !slides.length) return;

            var offset = 0;
            var total  = slides.length;

            function getConfig() {
                var w = window.innerWidth;
                if (slider.classList.contains('product-detail__slider--two-item')) {
                    if (w >= 1024) return { visible: 2, gap: 32 };
                    if (w >= 769)  return { visible: 2, gap: 32 };
                    return { visible: 1, gap: 24 };
                }
                if (w >= 1024) return { visible: 3, gap: 32 };
                if (w >= 769)  return { visible: 2, gap: 32 };
                return { visible: 1.2, gap: 24 };
            }

            function getSlideWidth(cfg) {
                if (!trackWrap) return 0;
                return (trackWrap.offsetWidth - cfg.gap * (cfg.visible - 1)) / cfg.visible;
            }

            function update() {
                var cfg = getConfig();
                var slideW = getSlideWidth(cfg);
                track.style.transform = 'translateX(-' + (offset * (slideW + cfg.gap)) + 'px)';
                if (prevBtn) prevBtn.disabled = offset <= 0;
                if (nextBtn) nextBtn.disabled = offset >= total - cfg.visible;
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function () {
                    if (offset > 0) { offset--; update(); }
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function () {
                    var cfg = getConfig();
                    if (offset < total - cfg.visible) { offset++; update(); }
                });
            }

            update();

            // ── Drag / swipe ─────────────────────────────────────────────────────
            var dragStartX = 0;
            var isDragging = false;
            var DRAG_THRESHOLD = 40;

            function onDragStart(x) {
                dragStartX = x;
                isDragging = true;
                track.style.transition = 'none';
            }

            function onDragEnd(x) {
                if (!isDragging) return;
                isDragging = false;
                track.style.transition = '';

                var diff = dragStartX - x;
                var cfg  = getConfig();

                if (diff > DRAG_THRESHOLD && offset < total - cfg.visible) {
                    offset++;
                    sliderDragged = true;
                } else if (diff < -DRAG_THRESHOLD && offset > 0) {
                    offset--;
                    sliderDragged = true;
                }
                update();
            }

            // Touch
            trackWrap.addEventListener('touchstart', function (e) {
                onDragStart(e.touches[0].clientX);
            }, { passive: true });

            trackWrap.addEventListener('touchend', function (e) {
                onDragEnd(e.changedTouches[0].clientX);
            });

            // Mouse
            trackWrap.addEventListener('mousedown', function (e) {
                onDragStart(e.clientX);
                e.preventDefault();
            });

            window.addEventListener('mouseup', function (e) {
                if (isDragging) onDragEnd(e.clientX);
            });

            trackWrap.style.cursor = 'grab';
            trackWrap.addEventListener('mousedown', function () {
                trackWrap.style.cursor = 'grabbing';
            });
            window.addEventListener('mouseup', function () {
                trackWrap.style.cursor = 'grab';
            });

            // ── Resize ───────────────────────────────────────────────────────────
            var resizeTimer;
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function () { offset = 0; update(); }, 100);
            });
        });
    }

    // ── Image lightbox ───────────────────────────────────────────────────────
    function initImageLightbox() {
        var lightboxes = document.querySelectorAll('.product-detail__image-lightbox');
        if (!lightboxes.length) {
            // Support legacy single ID if no class exists
            var lb = document.getElementById('product-image-lightbox');
            if (lb) lightboxes = [lb];
            else return;
        }

        lightboxes.forEach(function(lightbox) {
            var img      = lightbox.querySelector('.product-detail__image-lightbox-img');
            var closeBtn = lightbox.querySelector('.product-detail__image-lightbox-close');
            var prevBtn  = lightbox.querySelector('.product-detail__image-lightbox-prev');
            var nextBtn  = lightbox.querySelector('.product-detail__image-lightbox-next');
            
            // Find the slider that corresponds to this lightbox
            // Usually it's the preceding sibling or they are rendered as a pair.
            // Let's assume the slider is the element immediately before the lightbox, or nearby.
            var slider = lightbox.previousElementSibling;
            while (slider && !slider.classList.contains('product-detail__slider')) {
                slider = slider.previousElementSibling;
            }
            
            // If we couldn't find a preceding slider, just fallback to grabbing slides inside the parent container.
            var slides = slider ? slider.querySelectorAll('.product-detail__slide') : document.querySelectorAll('.product-detail__slide');
            
            if (!slides.length) return;

            var images = Array.prototype.map.call(slides, function (slide) {
                var i = slide.querySelector('img');
                return { src: i ? i.getAttribute('src') : '', alt: i ? (i.getAttribute('alt') || '') : '' };
            });

            var current = 0;
            var isOpen = false;

            function show(index) {
                current = Math.max(0, Math.min(index, images.length - 1));
                img.src = images[current].src;
                img.alt = images[current].alt;
                if (prevBtn) prevBtn.disabled = current <= 0;
                if (nextBtn) nextBtn.disabled = current >= images.length - 1;
            }

            function open(index) {
                show(index);
                lightbox.classList.add('open');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                isOpen = true;
            }

            function close() {
                lightbox.classList.remove('open');
                lightbox.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                img.src = '';
                isOpen = false;
            }

            slides.forEach(function (slide, i) {
                slide.addEventListener('click', function () {
                    if (sliderDragged) { sliderDragged = false; return; }
                    open(i);
                });
            });

            if (closeBtn) closeBtn.addEventListener('click', close);
            if (prevBtn)  prevBtn.addEventListener('click', function () { show(current - 1); });
            if (nextBtn)  nextBtn.addEventListener('click', function () { show(current + 1); });

            lightbox.addEventListener('click', function (e) {
                if (e.target === lightbox) close();
            });

            document.addEventListener('keydown', function (e) {
                if (!isOpen) return;
                if (e.key === 'Escape')      close();
                if (e.key === 'ArrowLeft')   show(current - 1);
                if (e.key === 'ArrowRight')  show(current + 1);
            });
        });
    }

    // ── Video lightbox ───────────────────────────────────────────────────────
    function initVideoLightbox() {
        var lightbox = document.getElementById('product-video-lightbox');
        if (!lightbox) return;

        var content  = lightbox.querySelector('.product-detail__lightbox-content');
        var closeBtn = lightbox.querySelector('.product-detail__lightbox-close');
        var cards    = document.querySelectorAll('.product-detail__video-card');

        function open(videoSrc) {
            var embed = '';
            if (videoSrc.startsWith('<')) {
                embed = videoSrc;
            } else {
                var yt = videoSrc.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/);
                var vm = videoSrc.match(/vimeo\.com\/(\d+)/);
                if (yt) {
                    embed = '<iframe src="https://www.youtube.com/embed/' + yt[1] + '?autoplay=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                } else if (vm) {
                    embed = '<iframe src="https://player.vimeo.com/video/' + vm[1] + '?autoplay=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                } else {
                    embed = '<video controls autoplay><source src="' + videoSrc + '"></video>';
                }
            }
            content.innerHTML = embed;
            lightbox.classList.add('open');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function close() {
            content.innerHTML = '';
            lightbox.classList.remove('open');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        cards.forEach(function (card) {
            card.addEventListener('click', function () {
                var src = card.getAttribute('data-video');
                if (src) open(src);
            });
        });

        if (closeBtn) closeBtn.addEventListener('click', close);

        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) close();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });
    }

    // ── Tab switcher ─────────────────────────────────────────────────────────
    function initTabs() {
        var tabBtns   = document.querySelectorAll('.product-detail__tab-btn');
        var tabPanels = document.querySelectorAll('.product-detail__tab-panel');

        if (!tabBtns.length) return;

        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetId = btn.getAttribute('data-tab');

                tabBtns.forEach(function (b) {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                tabPanels.forEach(function (p) {
                    p.classList.remove('active');
                    p.setAttribute('aria-hidden', 'true');
                });

                btn.classList.add('active');
                btn.setAttribute('aria-selected', 'true');

                var panel = document.getElementById('tab-panel-' + targetId);
                if (panel) {
                    panel.classList.add('active');
                    panel.setAttribute('aria-hidden', 'false');
                }
            });
        });
    }

    // ── Init ─────────────────────────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initSlider();
            initTabs();
            initVideoLightbox();
            initImageLightbox();
        });
    } else {
        initSlider();
        initTabs();
        initVideoLightbox();
        initImageLightbox();
    }
}());
