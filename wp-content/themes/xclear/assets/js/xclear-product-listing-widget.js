(function ($) {
    'use strict';

    var XclearProductListing = function ($scope) {
        var $widget = $scope.find('.xclear-product-listing');
        if (! $widget.length) return;

        // ---------------------------------------------------------------
        // State
        // ---------------------------------------------------------------
        var state = {
            category:     $widget.data('category') || '',
            sort:         $widget.data('sort')      || 'default',
            page:         parseInt($widget.data('page'), 10) || 1,
            perPage:      parseInt($widget.data('per-page'), 10) || 10,
            linkProducts: $widget.data('link-products') === 1 || $widget.data('link-products') === '1',
            ajaxUrl:      $widget.data('ajax-url'),
            nonce:        $widget.data('nonce'),
            loading:      false,
        };

        var $grid           = $widget.find('.xclear-product-listing__grid');
        var $pagination     = $widget.find('.xclear-product-listing__pagination');
        var $count          = $widget.find('.xclear-product-listing__count');
        var $itemsInfo      = $widget.find('.xclear-product-listing__items-info');
        var $sortWrapper    = $widget.find('.xclear-product-listing__sort-wrapper');
        var $sortTrigger    = $widget.find('.xclear-product-listing__sort-trigger');
        var $sortDropdown   = $widget.find('.xclear-product-listing__sort-dropdown');
        var $sortLabel      = $widget.find('.xclear-product-listing__sort-label');
        var $filterBtns     = $widget.find('.xclear-product-listing__filter-btn');
        var $perPageSelect  = $widget.find('.xclear-product-listing__per-page');

        // ---------------------------------------------------------------
        // Fetch products via AJAX
        // ---------------------------------------------------------------
        function fetchProducts(scrollToTop) {
            if (state.loading) return;
            state.loading = true;
            $widget.addClass('is-loading');

            $.ajax({
                url:  state.ajaxUrl,
                type: 'POST',
                data: {
                    action:        'xclear_get_products',
                    nonce:         state.nonce,
                    category:      state.category,
                    sort:          state.sort,
                    page:          state.page,
                    per_page:      state.perPage,
                    link_products: state.linkProducts ? '1' : '0',
                },
                success: function (response) {
                    if (! response.success) return;

                    var d = response.data;

                    $grid.html(d.grid_html);
                    $pagination.html(d.pagination_html);

                    // Update count text
                    var total     = parseInt(d.total, 10);
                    var countText = total === 1 ? '1 Product' : total.toLocaleString() + ' Products';
                    $count.text(countText);

                    // Update items info
                    if (total > 0) {
                        $itemsInfo.text(
                            'Items ' + d.start + ' to ' + d.end + ' of ' + total
                        );
                    } else {
                        $itemsInfo.text('');
                    }

                    bindPaginationClicks();

                    if (scrollToTop !== false) {
                        $('html, body').animate(
                            { scrollTop: $widget.offset().top - 80 },
                            280
                        );
                    }
                },
                error: function () {
                    $grid.html(
                        '<div class="xclear-product-listing__no-results">Something went wrong. Please try again.</div>'
                    );
                },
                complete: function () {
                    state.loading = false;
                    $widget.removeClass('is-loading');
                },
            });
        }

        // ---------------------------------------------------------------
        // Pagination
        // ---------------------------------------------------------------
        function bindPaginationClicks() {
            $pagination.find('.xclear-product-listing__page-btn')
                .off('click.xclear')
                .on('click.xclear', function () {
                    if ($(this).prop('disabled') || $(this).hasClass('active')) return;
                    state.page = parseInt($(this).data('page'), 10);
                    fetchProducts(true);
                });
        }

        bindPaginationClicks();

        // ---------------------------------------------------------------
        // Sort labels used to update the UI when switching category
        // ---------------------------------------------------------------
        var sortLabels = {
            'default': 'Default',
            'newest':  'Newest',
            'a-z':     'A → Z',
            'z-a':     'Z → A',
            'custom':  'Custom Order',
        };

        function applySortUI(newSort) {
            state.sort = newSort;
            $widget.find('.xclear-product-listing__sort-option')
                .removeClass('active')
                .attr('aria-selected', 'false')
                .filter('[data-sort="' + newSort + '"]')
                .addClass('active')
                .attr('aria-selected', 'true');
            $sortLabel.text(sortLabels[newSort] || 'Default');
        }

        // ---------------------------------------------------------------
        // Filter tabs
        // ---------------------------------------------------------------
        $filterBtns.on('click.xclear', function () {
            var $btn = $(this);
            if ($btn.hasClass('active')) return;

            $filterBtns.removeClass('active').attr('aria-selected', 'false');
            $btn.addClass('active').attr('aria-selected', 'true');

            state.category = $btn.data('category') || '';
            state.page     = 1;

            // Switch sort to the category's default sort
            var catDefaultSort = $btn.data('default-sort') || 'default';
            applySortUI(catDefaultSort);

            fetchProducts(false);
        });

        // ---------------------------------------------------------------
        // Sort dropdown
        // ---------------------------------------------------------------
        $sortTrigger.on('click.xclear', function (e) {
            e.stopPropagation();
            var isOpen = $sortWrapper.hasClass('is-open');
            closeSortDropdown();
            if (! isOpen) {
                $sortWrapper.addClass('is-open');
                $sortDropdown.addClass('is-open');
                $sortTrigger.attr('aria-expanded', 'true');
            }
        });

        $widget.find('.xclear-product-listing__sort-option').on('click.xclear', function () {
            var $opt    = $(this);
            var newSort = $opt.data('sort');
            if (newSort === state.sort) {
                closeSortDropdown();
                return;
            }

            $widget.find('.xclear-product-listing__sort-option')
                .removeClass('active')
                .attr('aria-selected', 'false');
            $opt.addClass('active').attr('aria-selected', 'true');

            $sortLabel.text($opt.text().trim());
            state.sort = newSort;
            state.page = 1;

            closeSortDropdown();
            fetchProducts(false);
        });

        function closeSortDropdown() {
            $sortWrapper.removeClass('is-open');
            $sortDropdown.removeClass('is-open');
            $sortTrigger.attr('aria-expanded', 'false');
        }

        // Close dropdown on outside click
        $(document).on('click.xclear-sort-' + $widget.attr('id') || 'xclear', function (e) {
            if (! $(e.target).closest('.xclear-product-listing__sort-wrapper').length) {
                closeSortDropdown();
            }
        });

        // Close on Escape
        $(document).on('keydown.xclear', function (e) {
            if (e.key === 'Escape') {
                closeSortDropdown();
            }
        });

        // ---------------------------------------------------------------
        // Per page selector
        // ---------------------------------------------------------------
        $perPageSelect.on('change.xclear', function () {
            state.perPage = parseInt($(this).val(), 10);
            state.page    = 1;
            fetchProducts(false);
        });
    };

    // Register with Elementor frontend
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/xclear_product_listing_widget.default',
            XclearProductListing
        );
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/xclear_product_category_widget.default',
            XclearProductListing
        );
    });

})(jQuery);
