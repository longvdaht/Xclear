(function ($) {
    'use strict';

    function initArticleWidget($scope) {
        var wrapper = $scope.find('.xclear-article')[0];
        if (!wrapper) return;

        var widgetId = wrapper.getAttribute('data-widget-id');
        var config   = window['xclearArticleConfig_' + widgetId];
        if (!config) return;

        // ── State ────────────────────────────────────────────────────────── //
        var state = {
            category : '',
            search   : '',
            page     : 1,
            loading  : false,
        };

        // ── Elements ─────────────────────────────────────────────────────── //
        var $heading    = $(wrapper).find('.xclear-article__heading');
        var $posts      = $(wrapper).find('.xclear-article__posts');
        var $spinner    = $(wrapper).find('.xclear-article__loading');
        var $empty      = $(wrapper).find('.xclear-article__empty');
        var $loadMore   = $(wrapper).find('.xclear-article__load-more');
        var $catLinks   = $(wrapper).find('.xclear-article__cat-link');
        var $searchInput = $(wrapper).find('.xclear-article__search-input');
        var $searchClear = $(wrapper).find('.xclear-article__search-clear');
        var $searchBtn  = $(wrapper).find('.xclear-article__search-btn');
        var debounceTimer = null;

        // ── Render a single card ─────────────────────────────────────────── //
        function renderCard(post) {
            var thumb = post.thumbnail
                ? '<div class="xclear-article__card-thumb"><img src="' + post.thumbnail + '" alt="' + escHtml(post.title) + '" loading="lazy" /></div>'
                : '<div class="xclear-article__card-thumb"></div>';

            return '<a href="' + post.permalink + '" class="xclear-article__card">' +
                thumb +
                '<div class="xclear-article__card-body">' +
                '<h3 class="xclear-article__card-title">' + post.title + '</h3>' +
                '<p class="xclear-article__card-excerpt">' + escHtml(post.excerpt) + '</p>' +
                '</div>' +
                '</a>';
        }

        function escHtml(str) {
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str));
            return d.innerHTML;
        }

        // ── Update heading ───────────────────────────────────────────────── //
        function updateHeading(total) {
            if (state.search !== '') {
                $heading.text(total + ' ' + config.searchResults + ' "' + state.search + '"');
            } else {
                $heading.text(config.allLabel);
            }
        }

        // ── Fetch posts via AJAX ─────────────────────────────────────────── //
        function fetchPosts(append) {
            if (state.loading) return;
            state.loading = true;

            $spinner.removeAttr('hidden');
            $empty.attr('hidden', '');

            if (!append) {
                $posts.html('');
            }

            $.ajax({
                url    : config.ajaxUrl,
                method : 'POST',
                data   : {
                    action         : 'xclear_article_query',
                    nonce          : config.nonce,
                    post_type      : config.postType,
                    taxonomy       : config.taxonomy,
                    category       : state.category,
                    search         : state.search,
                    page           : state.page,
                    posts_per_page : config.postsPerPage,
                },
                success: function (res) {
                    if (!res.success) return;

                    var data = res.data;
                    updateHeading(data.total);

                    if (data.posts.length === 0 && !append) {
                        $empty.removeAttr('hidden');
                    } else {
                        data.posts.forEach(function (post) {
                            $posts.append(renderCard(post));
                        });
                    }

                    // Show/hide load more
                    if (data.current_page < data.max_pages) {
                        $loadMore.removeAttr('hidden');
                    } else {
                        $loadMore.attr('hidden', '');
                    }
                },
                error: function () {
                    $empty.removeAttr('hidden');
                },
                complete: function () {
                    $spinner.attr('hidden', '');
                    state.loading = false;
                },
            });
        }

        // ── Event: Category click ────────────────────────────────────────── //
        $catLinks.on('click', function () {
            var $this = $(this);
            $catLinks.removeClass('is-active');
            $this.addClass('is-active');
            state.category = $this.data('category');
            state.page     = 1;
            fetchPosts(false);
        });

        // ── Event: Search input (debounced) ──────────────────────────────── //
        $searchInput.on('input', function () {
            var val = $(this).val().trim();
            $searchClear.attr('hidden', val === '' ? '' : null);
            if (val === '') {
                $searchClear.attr('hidden', '');
            } else {
                $searchClear.removeAttr('hidden');
            }

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                state.search = val;
                state.page   = 1;
                fetchPosts(false);
            }, 450);
        });

        // ── Event: Search button click ────────────────────────────────────── //
        $searchBtn.on('click', function () {
            state.search = $searchInput.val().trim();
            state.page   = 1;
            fetchPosts(false);
        });

        // ── Event: Search Enter key ───────────────────────────────────────── //
        $searchInput.on('keydown', function (e) {
            if (e.key === 'Enter') {
                clearTimeout(debounceTimer);
                state.search = $(this).val().trim();
                state.page   = 1;
                fetchPosts(false);
            }
        });

        // ── Event: Clear search ──────────────────────────────────────────── //
        $searchClear.on('click', function () {
            $searchInput.val('');
            $searchClear.attr('hidden', '');
            state.search = '';
            state.page   = 1;
            fetchPosts(false);
        });

        // ── Event: Load More ─────────────────────────────────────────────── //
        $loadMore.on('click', function () {
            state.page++;
            fetchPosts(true);
        });

        // ── Initial load ─────────────────────────────────────────────────── //
        fetchPosts(false);
    }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/xclear_article_widget.default',
            initArticleWidget
        );
    });
})(jQuery);
