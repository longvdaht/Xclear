<?php
if (! defined('ABSPATH')) {
    exit;
}

function xclearRegisterElementorCategory($elements_manager)
{
    $elements_manager->add_category('xclear-widgets', [
        'title' => esc_html__('Xclear widgets', 'xclear'),
        'icon'  => 'eicon-custom',
    ]);

    // Move category to the top (before 'basic')
    $prop = (new ReflectionClass($elements_manager))->getProperty('categories');
    $prop->setAccessible(true); // NOSONAR
    $cats = $prop->getValue($elements_manager);

    $entry = ['xclear-widgets' => $cats['xclear-widgets']];
    unset($cats['xclear-widgets']);

    $pos  = array_search('basic', array_keys($cats));
    $cats = array_slice($cats, 0, $pos, true) + $entry + array_slice($cats, $pos, null, true);

    $prop->setValue($elements_manager, $cats);
}
add_action('elementor/elements/categories_registered', 'xclearRegisterElementorCategory', 999);

function xclearRegisterElementorWidgets($widgets_manager)
{
    $widgets = [
        'xclear-hero-banner-widget.php'        => 'XclearHeroBannerWidget',
        'xclear-applications-cards-widget.php' => 'XclearApplicationsCardsWidget',
        'xclear-image-with-text-widget.php'    => 'XclearImageWithTextWidget',
        'xclear-categories-cards-widget.php'   => 'XclearCategoriesCardsWidget',
        'xclear-about-highlights-widget.php'   => 'XclearAboutHighlightsWidget',
        'xclear-uvc-guide-widget.php'          => 'XclearUvcGuideWidget',
        'xclear-uvc-how-it-works-widget.php'  => 'XclearUvcHowItWorksWidget',
        'xclear-uvc-installation-widget.php'  => 'XclearUvcInstallationWidget',
        'xclear-industry-cards-widget.php'    => 'XclearIndustryCardsWidget',
        'xclear-lamp-replacement-widget.php'  => 'XclearLampReplacementWidget',
        'xclear-document-download-widget.php' => 'XclearDocumentDownloadWidget',
        'xclear-faq-widget.php'               => 'XclearFaqWidget',
        'xclear-article-widget.php'           => 'XclearArticleWidget',
        'xclear-video-gallery-widget.php'     => 'XclearVideoGalleryWidget',
        'xclear-section-header-widget.php'     => 'XclearSectionHeaderWidget',
        'xclear-product-listing-widget.php'    => 'XclearProductListingWidget',
        'xclear-legal-widget.php'              => 'XclearLegalWidget',
        'xclear-list-category-widget.php'      => 'XclearListCategoryWidget',
        'xclear-product-category-widget.php'   => 'XclearProductCategoryWidget',
        'xclear-language-switcher-widget.php'  => 'XclearLanguageSwitcherWidget',
        'xclear-nav-dropdown-widget.php'       => 'XclearNavDropdownWidget',
    ];

    foreach ($widgets as $file => $class) {
        $path = get_stylesheet_directory() . '/inc/widgets/' . $file;

        if (file_exists($path)) {
            require_once $path; // NOSONAR

            if (class_exists($class)) {
                $widgets_manager->register(new $class());
            }
        }
    }
}
add_action('elementor/widgets/register', 'xclearRegisterElementorWidgets');
