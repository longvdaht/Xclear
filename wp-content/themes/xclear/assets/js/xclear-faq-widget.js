(function ($) {
    'use strict';

    function initFaqWidget($scope) {
        var wrapper    = $scope.find('.xclear-faq')[0];
        if (!wrapper) return;

        var items      = wrapper.querySelectorAll('.xclear-faq__item');
        var navLinks   = wrapper.querySelectorAll('.xclear-faq__nav-link');
        var categories = wrapper.querySelectorAll('.xclear-faq__category');

        /* ── Accordion toggle ────────────────────────────────────────────── */
        items.forEach(function (item) {
            item.addEventListener('click', function (e) {
                if (e.target.closest('.xclear-faq__answer')) return;

                var btn    = item.querySelector('.xclear-faq__question');
                var answer = item.querySelector('.xclear-faq__answer');
                if (!answer) return;

                var isActive = item.classList.contains('is-active');

                if (isActive) {
                    // Collapse: set explicit height first, then 0
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                    answer.offsetHeight; // force reflow
                    answer.style.maxHeight = '0px';
                    item.classList.remove('is-active');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                } else {
                    // Close other items
                    var allActiveItems = wrapper.querySelectorAll('.xclear-faq__item.is-active');
                    allActiveItems.forEach(function (activeItem) {
                        if (activeItem !== item) {
                            var activeAnswer = activeItem.querySelector('.xclear-faq__answer');
                            var activeBtn    = activeItem.querySelector('.xclear-faq__question');
                            if (activeAnswer) {
                                activeAnswer.style.maxHeight = activeAnswer.scrollHeight + 'px';
                                activeAnswer.offsetHeight; // force reflow
                                activeAnswer.style.maxHeight = '0px';
                            }
                            activeItem.classList.remove('is-active');
                            if (activeBtn) activeBtn.setAttribute('aria-expanded', 'false');
                        }
                    });

                    // Expand
                    item.classList.add('is-active');
                    if (btn) btn.setAttribute('aria-expanded', 'true');
                    answer.style.maxHeight = answer.scrollHeight + 'px';

                    // After transition ends, set to none so content can grow
                    answer.addEventListener('transitionend', function handler() {
                        answer.removeEventListener('transitionend', handler);
                        if (item.classList.contains('is-active')) {
                            answer.style.maxHeight = 'none';
                        }
                    });
                }
            });
        });

        /* ── Smooth scroll nav links ─────────────────────────────────────── */
        navLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var targetId = link.getAttribute('data-faq-target');
                var target   = document.getElementById(targetId);
                if (target) {
                    var offset = 80;
                    var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                }
            });
        });

        /* ── Scroll spy: highlight active nav link ───────────────────────── */
        if (navLinks.length > 0 && categories.length > 0) {
            var scrollSpyActive = true;

            function setActiveNavLink(catId) {
                navLinks.forEach(function (link) {
                    if (link.getAttribute('data-faq-target') === catId) {
                        link.classList.add('is-active');
                    } else {
                        link.classList.remove('is-active');
                    }
                });
            }

            var observer = new IntersectionObserver(function (entries) {
                if (!scrollSpyActive) return;
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        setActiveNavLink(entry.target.id);
                    }
                });
            }, {
                root: null,
                rootMargin: '-80px 0px -50% 0px',
                threshold: 0
            });

            categories.forEach(function (cat) {
                observer.observe(cat);
            });

            // First nav link active by default
            navLinks[0].classList.add('is-active');

            // Pause scroll spy during nav click scroll
            navLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    scrollSpyActive = false;
                    setActiveNavLink(link.getAttribute('data-faq-target'));
                    setTimeout(function () {
                        scrollSpyActive = true;
                    }, 1000);
                });
            });
        }

        /* ── Init: expand already-active items ───────────────────────────── */
        wrapper.querySelectorAll('.xclear-faq__item.is-active').forEach(function (item) {
            var answer = item.querySelector('.xclear-faq__answer');
            if (answer) {
                answer.style.maxHeight = 'none';
            }
        });
    }

    /* ── Register with Elementor frontend (works on both editor & frontend) */
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/xclear_faq_widget.default',
            initFaqWidget
        );
    });
})(jQuery);
