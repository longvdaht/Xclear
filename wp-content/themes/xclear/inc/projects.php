<?php
if (! defined("ABSPATH")) {
    exit;
}

// Register Custom Post Type "project"
function xclear_register_project_cpt() {
    $labels = array(
        'name'               => _x( 'Projects', 'post type general name', 'xclear' ),
        'singular_name'      => _x( 'Project', 'post type singular name', 'xclear' ),
        'menu_name'          => _x( 'Projects', 'admin menu', 'xclear' ),
        'name_admin_bar'     => _x( 'Project', 'add new on admin bar', 'xclear' ),
        'add_new'            => _x( 'Add New', 'project', 'xclear' ),
        'add_new_item'       => __( 'Add New Project', 'xclear' ),
        'new_item'           => __( 'New Project', 'xclear' ),
        'edit_item'          => __( 'Edit Project', 'xclear' ),
        'view_item'          => __( 'View Project', 'xclear' ),
        'all_items'          => __( 'All Projects', 'xclear' ),
        'search_items'       => __( 'Search Projects', 'xclear' ),
        'parent_item_colon'  => __( 'Parent Projects:', 'xclear' ),
        'not_found'          => __( 'No projects found.', 'xclear' ),
        'not_found_in_trash' => __( 'No projects found in Trash.', 'xclear' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'projects' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-portfolio',
        'show_in_rest'       => true, // Enable block editor (Gutenberg)
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' )
    );

    register_post_type( 'project', $args );
}
add_action( 'init', 'xclear_register_project_cpt' );

// Add Meta Box for Project Details
function xclear_add_project_meta_boxes() {
    add_meta_box(
        'xclear_project_details',
        __( 'Project Details', 'xclear' ),
        'xclear_project_details_callback',
        'project',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'xclear_add_project_meta_boxes' );

// Enqueue WordPress media uploader scripts in admin
function xclear_admin_enqueue_projects_scripts( $hook ) {
    global $post;
    if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
        if ( $post && 'project' === $post->post_type ) {
            wp_enqueue_media();
        }
    }
}
add_action( 'admin_enqueue_scripts', 'xclear_admin_enqueue_projects_scripts' );

// Print JS in footer of edit page
function xclear_admin_projects_footer_scripts() {
    global $post;
    if ( ! $post || 'project' !== $post->post_type ) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($){
        var mediaUploader;
        $('#xclear_upload_image_btn').click(function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media({
                title: '<?php echo esc_html__("Choose Project Image", "xclear"); ?>',
                button: {
                    text: '<?php echo esc_html__("Choose Image", "xclear"); ?>'
                },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#project_image').val(attachment.url);
                $('#project_image_preview').attr('src', attachment.url).show();
                $('#xclear_remove_image_btn').show();
            });
            mediaUploader.open();
        });

        $('#xclear_remove_image_btn').click(function(e) {
            e.preventDefault();
            $('#project_image').val('');
            $('#project_image_preview').attr('src', '').hide();
            $(this).hide();
        });
    });
    </script>
    <?php
}
add_action( 'admin_print_footer_scripts', 'xclear_admin_projects_footer_scripts' );

// Meta Box Callback
function xclear_project_details_callback( $post ) {
    // Add nonce for security
    wp_nonce_field( 'xclear_save_project_details', 'xclear_project_details_nonce' );

    // Retrieve existing values
    $short_desc = get_post_meta( $post->ID, '_project_short_description', true );
    $year = get_post_meta( $post->ID, '_project_year', true );
    $client = get_post_meta( $post->ID, '_project_client', true );
    $application = get_post_meta( $post->ID, '_project_application', true );
    $project_image = get_post_meta( $post->ID, '_project_image', true );

    ?>
    <table class="form-table">
        <tr>
            <th><label for="project_short_description"><?php echo esc_html__( 'Short Description', 'xclear' ); ?></label></th>
            <td>
                <textarea name="project_short_description" id="project_short_description" rows="3" class="large-text"><?php echo esc_textarea( $short_desc ); ?></textarea>
            </td>
        </tr>
        <tr>
            <th><label for="project_year"><?php echo esc_html__( 'Year', 'xclear' ); ?></label></th>
            <td>
                <input type="text" name="project_year" id="project_year" value="<?php echo esc_attr( $year ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="project_client"><?php echo esc_html__( 'Client', 'xclear' ); ?></label></th>
            <td>
                <input type="text" name="project_client" id="project_client" value="<?php echo esc_attr( $client ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="project_application"><?php echo esc_html__( 'Application', 'xclear' ); ?></label></th>
            <td>
                <input type="text" name="project_application" id="project_application" value="<?php echo esc_attr( $application ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="project_image"><?php echo esc_html__( 'Project Image', 'xclear' ); ?></label></th>
            <td>
                <input type="hidden" name="project_image" id="project_image" value="<?php echo esc_attr( $project_image ); ?>" />
                <div style="margin-bottom: 10px;">
                    <img id="project_image_preview" src="<?php echo esc_attr( $project_image ); ?>" style="max-width: 200px; max-height: 200px; border: 1px solid #ccc; border-radius: 4px; display: <?php echo !empty($project_image) ? 'block' : 'none'; ?>;" />
                </div>
                <button type="button" class="button" id="xclear_upload_image_btn"><?php echo esc_html__( 'Select Image', 'xclear' ); ?></button>
                <button type="button" class="button button-link-delete" id="xclear_remove_image_btn" style="display: <?php echo !empty($project_image) ? 'inline-block' : 'none'; ?>;"><?php echo esc_html__( 'Remove Image', 'xclear' ); ?></button>
                <p class="description"><?php echo esc_html__( 'Upload or select an additional showcase image for this project.', 'xclear' ); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

// Save Meta Box Data
function xclear_save_project_details( $post_id ) {
    // Check if nonce is set
    if ( ! isset( $_POST['xclear_project_details_nonce'] ) ) {
        return;
    }

    // Verify nonce
    if ( ! wp_verify_nonce( $_POST['xclear_project_details_nonce'], 'xclear_save_project_details' ) ) {
        return;
    }

    // Check if auto-save
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check user permissions
    if ( isset( $_POST['post_type'] ) && 'project' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    // Sanitize and save fields
    if ( isset( $_POST['project_short_description'] ) ) {
        update_post_meta( $post_id, '_project_short_description', sanitize_textarea_field( $_POST['project_short_description'] ) );
    }
    if ( isset( $_POST['project_year'] ) ) {
        update_post_meta( $post_id, '_project_year', sanitize_text_field( $_POST['project_year'] ) );
    }
    if ( isset( $_POST['project_client'] ) ) {
        update_post_meta( $post_id, '_project_client', sanitize_text_field( $_POST['project_client'] ) );
    }
    if ( isset( $_POST['project_application'] ) ) {
        update_post_meta( $post_id, '_project_application', sanitize_text_field( $_POST['project_application'] ) );
    }
    if ( isset( $_POST['project_image'] ) ) {
        update_post_meta( $post_id, '_project_image', esc_url_raw( $_POST['project_image'] ) );
    }
}
add_action( 'save_post', 'xclear_save_project_details' );

/**
 * Helper function to retrieve all custom details for a project.
 *
 * @param int $post_id The post ID.
 * @return array Project details (title, description, short_description, year, client, application).
 */
function xclear_get_project_details( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    return array(
        'title'             => get_the_title( $post_id ),
        'description'       => get_post_field( 'post_content', $post_id ),
        'short_description' => get_post_meta( $post_id, '_project_short_description', true ),
        'year'              => get_post_meta( $post_id, '_project_year', true ),
        'client'            => get_post_meta( $post_id, '_project_client', true ),
        'application'       => get_post_meta( $post_id, '_project_application', true ),
        'image'             => get_post_meta( $post_id, '_project_image', true ),
    );
}

// Register Custom Taxonomy "project_category"
function xclear_register_project_taxonomy() {
    $labels = array(
        'name'              => _x( 'Project Categories', 'taxonomy general name', 'xclear' ),
        'singular_name'     => _x( 'Project Category', 'taxonomy singular name', 'xclear' ),
        'search_items'      => __( 'Search Project Categories', 'xclear' ),
        'all_items'         => __( 'All Project Categories', 'xclear' ),
        'parent_item'       => __( 'Parent Project Category', 'xclear' ),
        'parent_item_colon' => __( 'Parent Project Category:', 'xclear' ),
        'edit_item'         => __( 'Edit Project Category', 'xclear' ),
        'update_item'       => __( 'Update Project Category', 'xclear' ),
        'add_new_item'      => __( 'Add New Project Category', 'xclear' ),
        'new_item_name'     => __( 'New Project Category Name', 'xclear' ),
        'menu_name'         => __( 'Project Categories', 'xclear' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'project-category' ),
        'show_in_rest'      => true, // Enable REST API/Gutenberg support
    );

    register_taxonomy( 'project_category', array( 'project' ), $args );
}
add_action( 'init', 'xclear_register_project_taxonomy' );

// Add custom fields to Add Category screen
function xclear_add_project_category_fields( $taxonomy ) {
    ?>
    <div class="form-field term-group">
        <label for="category_badge"><?php echo esc_html__( 'Badge', 'xclear' ); ?></label>
        <input name="category_badge" id="category_badge" type="text" value="" size="40" />
        <p><?php echo esc_html__( 'Enter a badge text for this category (e.g., Hot, New, Featured).', 'xclear' ); ?></p>
    </div>
    <div class="form-field term-group">
        <label for="category_short_description"><?php echo esc_html__( 'Short Description', 'xclear' ); ?></label>
        <textarea name="category_short_description" id="category_short_description" rows="3" cols="40"></textarea>
        <p><?php echo esc_html__( 'Enter a short description for this category.', 'xclear' ); ?></p>
    </div>
    <?php
}
add_action( 'project_category_add_form_fields', 'xclear_add_project_category_fields', 10, 1 );

// Add custom fields to Edit Category screen
function xclear_edit_project_category_fields( $term, $taxonomy ) {
    $badge = get_term_meta( $term->term_id, '_category_badge', true );
    $short_desc = get_term_meta( $term->term_id, '_category_short_description', true );
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="category_badge"><?php echo esc_html__( 'Badge', 'xclear' ); ?></label></th>
        <td>
            <input name="category_badge" id="category_badge" type="text" value="<?php echo esc_attr( $badge ); ?>" size="40" />
            <p class="description"><?php echo esc_html__( 'Enter a badge text for this category (e.g., Hot, New, Featured).', 'xclear' ); ?></p>
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="category_short_description"><?php echo esc_html__( 'Short Description', 'xclear' ); ?></label></th>
        <td>
            <textarea name="category_short_description" id="category_short_description" rows="3" cols="40"><?php echo esc_textarea( $short_desc ); ?></textarea>
            <p class="description"><?php echo esc_html__( 'Enter a short description for this category.', 'xclear' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'project_category_edit_form_fields', 'xclear_edit_project_category_fields', 10, 2 );

// Save custom fields
function xclear_save_project_category_fields( $term_id ) {
    if ( isset( $_POST['category_badge'] ) ) {
        update_term_meta( $term_id, '_category_badge', sanitize_text_field( $_POST['category_badge'] ) );
    }
    if ( isset( $_POST['category_short_description'] ) ) {
        update_term_meta( $term_id, '_category_short_description', sanitize_textarea_field( $_POST['category_short_description'] ) );
    }
}
add_action( 'created_project_category', 'xclear_save_project_category_fields', 10, 1 );
add_action( 'edited_project_category', 'xclear_save_project_category_fields', 10, 1 );

/**
 * Helper function to retrieve all details for a project category.
 *
 * @param int|object $term The term ID or term object.
 * @return array Project category details (id, slug, name, title, description, short_description, badge).
 */
function xclear_get_project_category_details( $term ) {
    if ( is_numeric( $term ) ) {
        $term = get_term( $term, 'project_category' );
    }

    if ( ! $term || is_wp_error( $term ) ) {
        return array();
    }

    return array(
        'id'                => $term->term_id,
        'slug'              => $term->slug,
        'name'              => $term->name,
        'title'             => $term->name, // Title is term Name
        'description'       => $term->description, // Description is term Description
        'badge'             => get_term_meta( $term->term_id, '_category_badge', true ),
        'short_description' => get_term_meta( $term->term_id, '_category_short_description', true ),
    );
}

// Disable Elementor support for 'project' CPT automatically
function xclear_remove_elementor_support_for_project() {
    $cpt_support = get_option( 'elementor_cpt_support', [ 'page', 'post' ] );
    if ( ( $key = array_search( 'project', $cpt_support ) ) !== false ) {
        unset( $cpt_support[$key] );
        update_option( 'elementor_cpt_support', array_values($cpt_support) );
    }
}
add_action( 'init', 'xclear_remove_elementor_support_for_project' );
