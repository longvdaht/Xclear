<?php
get_header();

// Get current term
$current_term = get_queried_object();
$category_details = xclear_get_project_category_details($current_term);
global $wp_query;
$projects_count = intval($wp_query->found_posts);
?>

<div class="project-category-wrapper container">
    <div class="project-category-top">
        <?php if (!empty($category_details['badge'])) : ?>
            <span class="project-category-badge"><?php echo esc_html($category_details['badge']); ?></span>
        <?php else: ?>
            <span class="project-category-badge"><?php echo esc_html__('PROJECTS', 'xclear'); ?></span>
        <?php endif; ?>
        
        <h1 class="project-category-title"><?php echo esc_html($category_details['title']); ?></h1>
        
        <div class="project-category-description">
            <?php 
            if (!empty($category_details['description'])) {
                echo wpautop(wp_kses_post($category_details['description'])); 
            } else if (!empty($category_details['short_description'])) {
                echo wpautop(wp_kses_post($category_details['short_description']));
            }
            ?>
        </div>
    </div>

    <?php if (have_posts()) : ?>
        <div class="projects-container">
            <?php 
            $project_count = 0;
            $has_grid = false;
            
            while (have_posts()) : the_post(); 
                $project_details = xclear_get_project_details(get_the_ID());
                $display_year = !empty($project_details['year']) ? $project_details['year'] : get_the_date('Y');
                $card_excerpt = !empty($project_details['short_description']) 
                    ? $project_details['short_description'] 
                    : get_the_excerpt();
                $card_excerpt = wp_trim_words($card_excerpt, 20);
                
                if ($project_count === 0 && !is_paged()) {
                    ?>
                    <article class="project-card-featured">
                        <div class="featured-content">
                            <span class="project-label"><?php echo esc_html__('FEATURED', 'xclear'); ?></span>
                            <div class="project-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </div>
                            
                            <div class="project-info-list">
                                <div class="info-item"><span class="info-label"><?php echo esc_html__('Year:', 'xclear'); ?></span> <span class="info-value"><?php echo esc_html($display_year); ?></span></div>
                                <?php if (!empty($project_details['client'])) : ?>
                                    <div class="info-item"><span class="info-label"><?php echo esc_html__('Client:', 'xclear'); ?></span> <span class="info-value"><?php echo esc_html($project_details['client']); ?></span></div>
                                <?php endif; ?>
                                <?php if (!empty($project_details['application'])) : ?>
                                    <div class="info-item">
                                        <span class="info-label"><?php echo esc_html__('Application:', 'xclear'); ?></span> <span class="info-value"><?php echo esc_html($project_details['application']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="project-excerpt">
                                <p><?php echo esc_html($card_excerpt); ?></p>
                            </div>

                            <div class="project-action">
                                <a href="<?php the_permalink(); ?>" class="btn btn-small">
                                    <?php echo esc_html__('Project Details', 'xclear'); ?>
                                </a>
                            </div>
                        </div>
                        <?php if (!empty($project_details['image'])) : ?>
                            <div class="featured-image">
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url($project_details['image']); ?>" alt="<?php the_title_attribute(); ?>" />
                                </a>
                            </div>
                        <?php elseif (has_post_thumbnail()) : ?>
                            <div class="featured-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </article>
                    <?php
                } else {
                    // Grid Projects
                    if (($project_count === 1 && !is_paged()) || ($project_count === 0 && is_paged())) {
                        echo '<div class="projects-grid">';
                        $has_grid = true;
                    }
                    ?>
                    <article class="project-card-grid">
                        <?php if (!empty($project_details['image'])) : ?>
                            <div class="grid-image">
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url($project_details['image']); ?>" alt="<?php the_title_attribute(); ?>" />
                                </a>
                            </div>
                        <?php elseif (has_post_thumbnail()) : ?>
                            <div class="grid-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="grid-content">
                            <h2 class="project-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="project-info-list">
                                <div class="info-item"><span class="info-label"><?php echo esc_html__('Year:', 'xclear'); ?></span> <span class="info-value"><?php echo esc_html($display_year); ?></span></div>
                                <?php if (!empty($project_details['client'])) : ?>
                                    <div class="info-item"><span class="info-label"><?php echo esc_html__('Client:', 'xclear'); ?></span> <span class="info-value"><?php echo esc_html($project_details['client']); ?></span></div>
                                <?php endif; ?>
                                <?php if (!empty($project_details['application'])) : ?>
                                    <div class="info-item"><span class="info-label"><?php echo esc_html__('Application:', 'xclear'); ?></span> <span class="info-value"><?php echo esc_html($project_details['application']); ?></span></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="project-excerpt">
                                <p><?php echo esc_html($card_excerpt); ?></p>
                            </div>
                            
                            <div class="project-action">
                                <a href="<?php the_permalink(); ?>" class="btn btn-small">
                                    <?php echo esc_html__('Project Details', 'xclear'); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php
                }
                $project_count++;
            endwhile; 
            
            if ($has_grid) {
                echo '</div>'; // Close projects-grid
            }
            ?>
        </div>
        
        <div class="pagination-wrapper">
            <?php the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __( '&laquo; Prev', 'xclear' ),
                'next_text' => __( 'Next &raquo;', 'xclear' ),
            )); ?>
        </div>
        
    <?php else : ?>
        <p class="no-projects-found"><?php echo esc_html__('No projects found in this category.', 'xclear'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();
