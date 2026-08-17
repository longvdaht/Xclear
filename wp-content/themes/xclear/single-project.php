<?php
get_header();

// Get project details using our helper function
$project = xclear_get_project_details(get_the_ID());

// Get category terms of the project
$terms = get_the_terms(get_the_ID(), 'project_category');
?>

<div class="project-detail-template container">
    <?php if (have_posts()) : while (have_posts()) : the_post();
        // Fallback year: use post publish year if custom year is empty
        $display_year = !empty($project['year']) ? $project['year'] : get_the_date('Y');
    ?>
        <h1 class="project-title"><?php the_title(); ?></h1>
        <div class="project-info">
            <?php if (!empty($display_year)) : ?>
                <div class="project-info-row">
                    <span class="project-info-label"><?php echo esc_html__('Year: ', 'xclear'); ?></span>
                    <span class="project-info-value"><?php echo esc_html($display_year); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($project['client'])) : ?>
                <div class="project-info-row">
                    <span class="project-info-label"><?php echo esc_html__('Client: ', 'xclear'); ?></span>
                    <span class="project-info-value"><?php echo esc_html($project['client']); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($project['application'])) : ?>
                <div class="project-info-row">
                    <span class="project-info-label"><?php echo esc_html__('Application: ', 'xclear'); ?></span>
                    <span class="project-info-value"><?php echo esc_html($project['application']); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="project-content">
            <?php the_content(); ?>
        </div>

        <?php
            $prev_post = get_previous_post();
            $next_post = get_next_post();
        ?>
        <!-- Previous / Next Navigation -->
        <nav class="project-navigation">
            <div class="project-nav-item project-nav-prev">
                <?php if (!empty($prev_post)) : ?>
                    <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>">
                        <span class="nav-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            <?php echo esc_html__('Previous Project', 'xclear'); ?>
                        </span>
                    </a>
                <?php endif; ?>
            </div>
            <div class="project-nav-item project-nav-next">
                <?php if (!empty($next_post)) : ?>
                    <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>">
                        <span class="nav-label">
                            <?php echo esc_html__('Next Project', 'xclear'); ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </span>
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    <?php endwhile; endif; ?>
</div>

<?php
get_footer();
