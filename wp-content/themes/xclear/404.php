<?php
/**
 * 404 error page.
 */
get_header();
?>

<main id="main">
    <section class="error-404-wrapper container">
        <div class="error-404-visual" aria-hidden="true">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="90" fill="var(--bg-5)" />
                <path d="M55 108c0-24 20-44 45-44s45 20 45 44-20 30-45 30-45-6-45-30z" fill="var(--color-blue-300)" />
                <path d="M145 100l18-12v24l-18-12z" fill="var(--color-blue-300)" />
                <circle cx="80" cy="96" r="5" fill="var(--color-neutral-1)" />
                <path d="M45 60c10-14 26-14 34-2" stroke="var(--color-blue-500)" stroke-width="4" fill="none" stroke-linecap="round" />
                <path d="M120 148c-14 10-30 8-38-4" stroke="var(--color-green-500)" stroke-width="4" fill="none" stroke-linecap="round" />
            </svg>
        </div>

        <span class="error-404-badge"><?php echo esc_html__('ERROR 404', 'xclear'); ?></span>
        <h1 class="error-404-title"><?php echo esc_html__('This page went swimming off', 'xclear'); ?></h1>
        <p class="error-404-description">
            <?php echo esc_html__("The page you're looking for doesn't exist or has been moved. Head back to a page that does.", 'xclear'); ?>
        </p>

        <div class="error-404-actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn"><?php echo esc_html__('Back to homepage', 'xclear'); ?></a>
            <?php $products_link = get_post_type_archive_link('product'); ?>
            <?php if ($products_link) : ?>
                <a href="<?php echo esc_url($products_link); ?>" class="btn btn-outline"><?php echo esc_html__('Browse products', 'xclear'); ?></a>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
