<?php
/**
 * Template Name: Project Archive
 * Template for displaying the /projects/ archive page.
 * Shows all Project Categories as cards.
 */
get_header();

// Get all project categories
$project_categories = get_terms( array(
    'taxonomy'   => 'project_category',
    'hide_empty' => false,
) );
?>

<div class="projects-archive-wrapper container">

    <div class="projects-archive-header">
        <span class="projects-archive-badge"><?php echo esc_html__( 'OUR WORK', 'xclear' ); ?></span>
        <h1 class="projects-archive-title"><?php echo esc_html__( 'Projects', 'xclear' ); ?></h1>
        <p class="projects-archive-description">
            <?php echo esc_html__( 'Explore our portfolio of projects across different categories.', 'xclear' ); ?>
        </p>
    </div>

    <?php if ( ! empty( $project_categories ) && ! is_wp_error( $project_categories ) ) : ?>

        <div class="projects-categories-grid">
            <?php foreach ( $project_categories as $term ) :
                $category_details = xclear_get_project_category_details( $term );
                $term_link        = get_term_link( $term );

                // Get latest project in this category for the thumbnail
                $latest_projects = get_posts( array(
                    'post_type'      => 'project',
                    'posts_per_page' => 1,
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'project_category',
                            'field'    => 'term_id',
                            'terms'    => $term->term_id,
                        ),
                    ),
                ) );

                $thumb_url = '';
                if ( ! empty( $latest_projects ) ) {
                    $latest_post   = $latest_projects[0];
                    $custom_image  = get_post_meta( $latest_post->ID, '_project_image', true );
                    if ( ! empty( $custom_image ) ) {
                        $thumb_url = $custom_image;
                    } elseif ( has_post_thumbnail( $latest_post->ID ) ) {
                        $thumb_url = get_the_post_thumbnail_url( $latest_post->ID, 'large' );
                    }
                }
            ?>
            <article class="project-category-card">
                <a href="<?php echo esc_url( $term_link ); ?>" class="category-card-link">

                    <div class="category-card-image">
                        <?php if ( ! empty( $thumb_url ) ) : ?>
                            <img src="<?php echo esc_url( $thumb_url ); ?>"
                                 alt="<?php echo esc_attr( $category_details['name'] ); ?>" />
                        <?php else : ?>
                            <div class="category-card-image-placeholder">
                                <span><?php echo esc_html( substr( $category_details['name'], 0, 1 ) ); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="category-card-overlay"></div>

                        <?php if ( ! empty( $category_details['badge'] ) ) : ?>
                            <span class="category-card-badge"><?php echo esc_html( $category_details['badge'] ); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="category-card-content">
                        <h2 class="category-card-title"><?php echo esc_html( $category_details['name'] ); ?></h2>

                        <?php if ( ! empty( $category_details['short_description'] ) ) : ?>
                            <p class="category-card-desc">
                                <?php echo esc_html( wp_trim_words( $category_details['short_description'], 15 ) ); ?>
                            </p>
                        <?php elseif ( ! empty( $category_details['description'] ) ) : ?>
                            <p class="category-card-desc">
                                <?php echo esc_html( wp_trim_words( $category_details['description'], 15 ) ); ?>
                            </p>
                        <?php endif; ?>

                        <div class="category-card-meta">
                            <span class="category-project-count">
                                <?php
                                $count = $term->count;
                                printf(
                                    esc_html( _n( '%d Project', '%d Projects', $count, 'xclear' ) ),
                                    intval( $count )
                                );
                                ?>
                            </span>
                            <span class="category-card-arrow">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </span>
                        </div>
                    </div>

                </a>
            </article>
            <?php endforeach; ?>
        </div>

    <?php else : ?>
        <p class="no-categories-found"><?php echo esc_html__( 'No project categories found.', 'xclear' ); ?></p>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
