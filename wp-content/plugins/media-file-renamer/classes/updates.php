<?php

class Meow_MFRH_Updates {

	private $core = null;
	private $useless_types_conditions = "
		post_status NOT IN ('inherit', 'trash', 'auto-draft')
		AND post_type NOT IN ('attachment', 'shop_order', 'shop_order_refund', 'nav_menu_item', 'revision', 'auto-draft', 'wphb_minify_group', 'customize_changeset', 'oembed_cache', 'nf_sub', 'jp_img_sitemap')
		AND post_type NOT LIKE 'dlssus_%'
		AND post_type NOT LIKE 'ml-slide%'
		AND post_type NOT LIKE '%acf-%'
		AND post_type NOT LIKE '%edd_%'
	";

	public function __construct( $core ) {
    	$this->core = $core;
		$this->init_actions();
	}

	function init_actions() {
		add_action( 'mfrh_media_renamed', array( $this, 'action_update_media_file_references' ), 10, 3 );

		if ( $this->core->get_option( "update_posts", true ) )
			add_action( 'mfrh_url_renamed', array( $this, 'action_update_posts' ), 10, 4 );
		if ( $this->core->get_option( "update_excerpts", false ) )
			add_action( 'mfrh_url_renamed', array( $this, 'action_update_excerpts' ), 10, 4 );
		if ( $this->core->get_option( "update_postmeta", false ) )
			add_action( 'mfrh_url_renamed', array( $this, 'action_update_postmeta' ), 10, 4 );
		if ( $this->core->get_option( "update_database_options", false ) )
			add_action( 'mfrh_url_renamed', array( $this, 'action_update_database_options' ), 10, 4 );


		if ( $this->core->get_option( "rename_guid" ) )
			add_action( 'mfrh_media_renamed', array( $this, 'action_rename_guid' ), 10, 4 );
		
	}

	function action_update_database_options( $post, $orig_image_url, $new_image_url, $size ) {

		list( $orig_image_url, $new_image_url ) = apply_filters( 'mfrh_update_database_options_urls', array( $orig_image_url, $new_image_url ) );

		if( empty( $orig_image_url ) || empty( $new_image_url ) ) {
			$this->core->log( "⚠️ Missing original or new image URL for database options update. Skipping." );
			return;
		}

		global $wpdb;

		// Get all options that contain the original filename
		$query = $wpdb->prepare( "SELECT option_name, option_value FROM $wpdb->options 
			WHERE option_value LIKE '%s'", '%' . $orig_image_url . '%' );
		$option_entries = $wpdb->get_results( $query );
		
		if ( empty( $option_entries ) ) {
			$this->core->log( "⚠️ No options found containing the original image URL. Skipping." );
			return array();
		}

		$updated_options = array();
		
		// Process each option individually
		foreach ( $option_entries as $option ) {
			$original_value = $option->option_value;
			$new_value = $this->safe_replace_in_serialized_data( $original_value, $orig_image_url, $new_image_url );
			
			// Only update if the value actually changed
			if ( $original_value !== $new_value ) {
				// Execute update
				$query = $wpdb->prepare( "UPDATE $wpdb->options 
					SET option_value = %s
					WHERE option_name = %s AND option_value = %s", 
					$new_value, $option->option_name, $original_value );
				$wpdb->query( $query );

				// Reverse update for logging
				$query_revert = $wpdb->prepare( "UPDATE $wpdb->options 
					SET option_value = %s
					WHERE option_name = %s AND option_value = %s", 
					$original_value, $option->option_name, $new_value );
				$this->core->log_sql( $query, $query_revert );
				
				$updated_options[] = $option->option_name;
			}
		}

		if ( !empty( $updated_options ) ) {
			$this->core->log( "🚀 Rewrite options $orig_image_url ➡️ $new_image_url (" . implode( ', ', $updated_options ) . ")" );
			
			// Clear options cache for updated options
			foreach ( $updated_options as $option_name ) {
				wp_cache_delete( $option_name, 'options' );
			}
		}
	}

	// Mass update of all the meta with the new filenames
	function action_update_postmeta( $post, $orig_image_url, $new_image_url, $size ) {
		global $wpdb;

		// Apply a filter to let plugins/users modify the update urls ( original and new )
		list( $orig_image_url, $new_image_url ) = apply_filters( 'mfrh_update_postmeta_urls', array( $orig_image_url, $new_image_url ) );

		// Get all meta entries that contain the original filename
		// Excluding internal meta keys that are already handled by WordPress or the plugin
		$query = $wpdb->prepare( "SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta
			WHERE meta_value LIKE '%s'
			AND meta_key NOT IN ('_original_filename', '_wp_attached_file')", '%' . $orig_image_url . '%' );

		$meta_entries = $wpdb->get_results( $query );
		
		if ( empty( $meta_entries ) ) {
			return array();
		}

		$updated_ids = array();
		
		// Process each meta entry individually
		foreach ( $meta_entries as $meta ) {
			$original_value = $meta->meta_value;
			$new_value = $this->safe_replace_in_serialized_data( $original_value, $orig_image_url, $new_image_url );
			
			// Only update if the value actually changed
			if ( $original_value !== $new_value ) {
				// Execute update
				$query = $wpdb->prepare( "UPDATE $wpdb->postmeta 
					SET meta_value = %s
					WHERE post_id = %d AND meta_key = %s AND meta_value = %s", 
					$new_value, $meta->post_id, $meta->meta_key, $original_value );
				$wpdb->query( $query );

				// Reverse update for logging
				$query_revert = $wpdb->prepare( "UPDATE $wpdb->postmeta 
					SET meta_value = %s
					WHERE post_id = %d AND meta_key = %s AND meta_value = %s", 
					$original_value, $meta->post_id, $meta->meta_key, $new_value );
				$this->core->log_sql( $query, $query_revert );
				
				$updated_ids[] = $meta->post_id;
			}
		}

		if ( !empty( $updated_ids ) ) {
			$this->core->log( "🚀 Rewrite meta $orig_image_url ➡️ $new_image_url" );
			
			// Reset meta cache for updated posts
			$unique_ids = array_unique( $updated_ids );
			update_meta_cache( 'post', $unique_ids );
		}
	}

	// Mass update of all the articles with the new filenames
	function action_update_posts( $post, $orig_image_url, $new_image_url, $size ) {
		$ids = $this->bulk_rename_content( $orig_image_url, $new_image_url );
		$this->core->log( "🚀 Rewrite content $orig_image_url ➡️ $new_image_url" );

		// Reset post cache
		if ( !empty( $ids ) ) {
			array_walk( $ids, 'clean_post_cache' );
		}
  }

	// Mass update of all the articles with the new filenames
	function action_update_excerpts( $post, $orig_image_url, $new_image_url, $size ) {
		$ids = $this->bulk_rename_excerpts( $orig_image_url, $new_image_url );
		$this->core->log( "🚀 Rewrite excerpts $orig_image_url ➡️ $new_image_url" );

		// Reset post cache
		if ( !empty( $ids ) ) {
			array_walk( $ids, 'clean_post_cache' );
		}
  }

	function bulk_rename_content( $orig_image_url, $new_image_url ) {
		global $wpdb;

		// Apply a filter to let plugins/users modify the update urls ( original and new )
		list( $orig_image_url, $new_image_url ) = apply_filters( 'mfrh_update_content_urls', array( $orig_image_url, $new_image_url ) );	
		
		// Get the IDs that require an update
		$query = $wpdb->prepare( "SELECT ID FROM $wpdb->posts 
			WHERE post_content LIKE '%s'
			AND {$this->useless_types_conditions}", '%' . $orig_image_url . '%' );
		$ids = $wpdb->get_col( $query );
		if ( empty( $ids ) ) {
			return array();
		}

		// Prepare SQL (WHERE IN)
		$ids_to_update = array_map(function( $id ) { return "'" . esc_sql( $id ) . "'"; }, $ids );
		$ids_to_update = implode(',', $ids_to_update);


		// Execute updates
		$query = $wpdb->prepare( "UPDATE $wpdb->posts 
			SET post_content = REPLACE(post_content, '%s', '%s')
			WHERE ID IN (" . $ids_to_update . ")", $orig_image_url, $new_image_url );
		$wpdb->query( $query );

		// Reverse updates & log
		$query_revert = $wpdb->prepare( "UPDATE $wpdb->posts 
			SET post_content = REPLACE(post_content, '%s', '%s')
			WHERE ID IN (" . $ids_to_update . ")", $orig_image_url, $new_image_url );
		$this->core->log_sql( $query, $query_revert );

		return $ids;
	}

	function bulk_rename_excerpts( $orig_image_url, $new_image_url ) {
		global $wpdb;

		list( $orig_image_url, $new_image_url ) = apply_filters( 'mfrh_update_excerpts_urls', array( $orig_image_url, $new_image_url ) );
		
		// Get the IDs that require an update
		$query = $wpdb->prepare( "SELECT ID FROM $wpdb->posts 
			WHERE post_excerpt LIKE '%s'
			AND {$this->useless_types_conditions}", '%' . $orig_image_url . '%' );
		$ids = $wpdb->get_col( $query );
		if ( empty( $ids ) ) {
			return array();
		}

		// Prepare SQL (WHERE IN)
		$ids_to_update = array_map(function( $id ) { return "'" . esc_sql( $id ) . "'"; }, $ids );
		$ids_to_update = implode(',', $ids_to_update);

		// Execute updates
		$query = $wpdb->prepare( "UPDATE $wpdb->posts 
			SET post_excerpt = REPLACE(post_excerpt, '%s', '%s')
			WHERE ID IN (" . $ids_to_update . ")", $orig_image_url, $new_image_url );
		$wpdb->query( $query );

		// Reverse updates & log
		$query_revert = $wpdb->prepare( "UPDATE $wpdb->posts 
			SET post_excerpt = REPLACE(post_excerpt, '%s', '%s')
			WHERE ID IN (" . $ids_to_update . ")", $orig_image_url, $new_image_url );
		$this->core->log_sql( $query, $query_revert );

		return $ids;
	}

  // The GUID should never be updated but... this will if the option is checked.
	// [TigrouMeow] It the recent version of WordPress, the GUID is not part of the $post (even though it is in database)
	// Explanation: http://pods.io/2013/07/17/dont-use-the-guid-field-ever-ever-ever/
	function action_rename_guid( $post, $old_filepath, $new_filepath, $undo = false ) {
		$meta = wp_get_attachment_metadata( $post['ID'] );
		$old_guid = get_the_guid( $post['ID'] );
		if ( $meta )
			$new_filepath = wp_get_attachment_url( $post['ID'] );
		global $wpdb;
		$query = $wpdb->prepare( "UPDATE $wpdb->posts SET guid = '%s' WHERE ID = '%d'", $new_filepath,  $post['ID'] );
		$query_revert = $wpdb->prepare( "UPDATE $wpdb->posts SET guid = '%s' WHERE ID = '%d'", $old_guid,  $post['ID'] );
		$this->core->log_sql( $query, $query_revert );
		$wpdb->query( $query );
		clean_post_cache( $post['ID'] );
		$this->core->log( "🚀 GUID $old_guid ➡️ $new_filepath." );
  }

	/**
	 * Updates renamed file references of all the duplicated media entries
	 * @param array $post
	 * @param string $old_filepath
	 * @param string $new_filepath
	 */
	function action_update_media_file_references( $post, $old_filepath, $new_filepath ) {
		global $wpdb;

		// Source of sync on 'posts' table
		$id = $post['ID'];
		$src = $wpdb->get_row( "SELECT post_mime_type FROM {$wpdb->posts} WHERE ID = {$id}" );

		// Source of sync on 'postmeta' table
		$meta = array ( // Meta keys to sync
			'_wp_attached_file' => null,
			'_wp_attachment_metadata' => null
		);
		foreach ( array_keys( $meta ) as $i ) {
			$meta[$i] = $wpdb->get_var( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = {$id} AND meta_key = '{$i}'" );
		}

		// Sync posts sharing the same attachment file
		$dest = $this->core->get_posts_by_attached_file( $old_filepath, $id );
		foreach ( $dest as $item ) {
			if ( get_post_type( $item ) != 'attachment' ) continue;

			// Set it as manual-renamed to avoid being marked as an issue
			add_post_meta( $item, '_manual_file_renaming', true, true );

			// Sync on 'posts' table
			$wpdb->update( $wpdb->posts, array ( // Data
				'post_mime_type' => $src->post_mime_type
			), array ( // WHERE
				'ID' => $item
			) );

			// Sync on 'postmeta' table
			foreach ( $meta as $j => $jtem ) {
				$wpdb->update( $wpdb->postmeta, array ( // Data
					'meta_value' => $jtem
				), array ( // WHERE
					'post_id'  => $item, // AND
					'meta_key' => $j
				) );
			}
		}
	}


	#region Helpers for serialized data replacement

	private function safe_replace_in_serialized_data( $data, $search, $replace ) {
		// Check if the data is serialized
		if ( is_serialized( $data ) ) {
			try {
				// Unserialize the data
				$unserialized = unserialize( $data );
				
				// Recursively replace in the unserialized data
				$updated = $this->recursive_replace( $unserialized, $search, $replace );
				
				// Re-serialize and return
				return serialize( $updated );
			} catch ( Exception $e ) {
				// If unserialization fails, fall back to string replacement
				// This might break serialized data, but it's better than losing the update entirely
				return str_replace( $search, $replace, $data );
			}
		} else {
			// Not serialized, just do a simple string replacement
			return str_replace( $search, $replace, $data );
		}
	}

	private function recursive_replace( $data, $search, $replace ) {

		if ( empty( $search ) ) {
			return $data; // Avoid infinite loop if search is empty
		}

		if ( is_string( $data ) ) {
			return str_replace( $search, $replace, $data );
		} elseif ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$data[$key] = $this->recursive_replace( $value, $search, $replace );
			}
			return $data;
		} elseif ( is_object( $data ) ) {
			foreach ( $data as $key => $value ) {
				$data->$key = $this->recursive_replace( $value, $search, $replace );
			}
			return $data;
		} else {
			// For other data types (int, bool, null, etc.), return as-is
			return $data;
		}
	}

	#endregion
}