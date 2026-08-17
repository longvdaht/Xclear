<?php
get_header();
?>

<div class="single-post-template container">
    <div class="single-post-inner">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <h1 class="post-title"><?php the_title(); ?></h1>
            
            <div class="post-meta">
                <span class="post-author"><?php the_author(); ?></span>
                <span class="meta-separator"></span>
                <span class="post-date"><?php echo get_the_date('d F Y'); ?></span>
                <span class="meta-separator"></span>
                <span class="post-category"><?php the_category(', '); ?></span>
            </div>

            <div class="post-content">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
                <?php the_content(); ?>
            </div>

            <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();
            ?>
            <!-- Previous / Next Navigation -->
            <nav class="post-navigation">
                <div class="post-nav-item post-nav-prev">
                    <?php if (!empty($prev_post)) : ?>
                        <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>">
                            <span class="nav-label">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                <?php echo esc_html__('Previous Article', 'xclear'); ?>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="post-nav-item post-nav-next">
                    <?php if (!empty($next_post)) : ?>
                        <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>">
                            <span class="nav-label">
                                <?php echo esc_html__('Next Article', 'xclear'); ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endwhile; endif; ?>
    </div>
</div>

<?php
get_footer();
