<?php

class Meow_MFRH_Engine {

    private $core = null;

	private $allowed_extensions = array( 
		// Images
		'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tif', 'tiff', 'webp', 'avif', 'svg',
		// Videos
		'mp4', 'm4v', 'mov', 'wmv', 'avi', 'mpg', 'ogv', '3gp', '3g2', '3gp2', '3gpp', '3gpp2', 'flv', 'mkv',
		// Text
		'pdf', 'txt', 'md', 'mdown', 'markdown', 'csv', 'rtf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'pages', 'numbers', 'key',
		// Audio
		'mp3', 'm4a', 'ogg', 'wav', 'wma', 'aac', 'flac', 'aiff', 'ape', 'mka', 'm3u', 'm3u8',
		// Archives
		'zip', 'rar', 'gz', 'tar', '7z',
	) ;

    function __construct( $core ) {
        $this->core = $core;
    }

	/**
	 *
	 * GENERATE A NEW FILENAME
	 *
	 */
	function replace_chars( $str ) {
		$special_chars = array();
		$special_chars = apply_filters( 'mfrh_replace_rules', $special_chars );
		if ( !empty( $special_chars ) ) {
			foreach ( $special_chars as $key => $value ) {
				$str = str_replace( $key, $value, $str );
            }
        }
		return $str;
	}

	/**
	 * Transform full width hyphens and other variety hyphens in half size into simple hyphen,
	 * and avoid consecutive hyphens and also at the beginning and end as well.
	 */
	function format_hyphens( $str ) {
		$hyphen = '-';
		$hyphens = [
			'﹣', '－', '−', '⁻', '₋',
			'‐', '‑', '‒', '–', '—',
			'―', '﹘', 'ー','ｰ',
		];
		$str = str_replace( $hyphens, $hyphen, $str );
		// remove at the beginning and end.
		$beginning = mb_substr( $str, 0, 1 );
		if ( $beginning === $hyphen ) {
			$str = mb_substr( $str, 1 );
		}
		$end = mb_substr( $str, -1 );
		if ( $end === $hyphen ) {
			$str = mb_strcut( $str, 0, mb_strlen( $str ) - 1 );
		}
		$str = preg_replace( '/-{2,}/u', '-', $str );
		$str = trim( $str, implode( '', $hyphens ) );
		return $str;
	}

	/**
	 * Computes the ideal filename based on a text
	 * @param array $media
	 * @param string $text
	 * @param string $manual_filename
	 * @return string|NULL If the resulting filename had no any valid characters, NULL is returned
	 */
	function new_filename( $text, $current_filename, $manual_filename = null, $media = null ) {
		// Gather the base values.
		if ( empty( $current_filename ) && !empty( $media ) ) {
			$current_filename = get_attached_file( $media['ID'] );
		}

		$pp = mfrh_pathinfo( $current_filename );
		$new_ext = empty( $pp['extension'] ) ? '' : $pp['extension'];
		$old_filename_no_ext = $pp['filename'];
		$text = empty( $text ) ? $old_filename_no_ext : $text;

		// Generate the new filename.

		if ( !empty( $manual_filename ) ) {
			// Forced filename (manual or undo, basically). Keep this extension in $new_ext.
			$manual_pp = mfrh_pathinfo( $manual_filename );
			$manual_filename = $manual_pp['filename'];
			$new_ext = empty( $manual_pp['extension'] ) ? $new_ext : $manual_pp['extension'];
			$new_filename = $manual_filename;
		}
		else {
			// Filename is generated from $text, without an extension.

			// Those are basically errors, when titles are generated from filename
			$text = str_replace( ".jpg", "", $text );
			$text = str_replace( ".jpeg", "", $text );
			$text = str_replace( ".png", "", $text );
			$text = str_replace( ".webp", "", $text );
			$text = str_replace( ".svg", "", $text );

			// Related to English
			$text = str_replace( "'s", "", $text );
			$text = str_replace( "n\'t", "nt", $text );
			$text = preg_replace( "/\'m/i", "-am", $text );

			// We probably do not want those neither
			$text = str_replace( "'", "-", $text );
			$text = preg_replace( "/\//s", "-", $text );
			$text = str_replace( ['.','…'], "", $text );
			$text = preg_replace( "/&amp;/s", "-", $text );

			$text = $this->replace_chars( $text );
			// Changed strolower to mb_strtolower... 
			if ( function_exists( 'mb_strtolower' ) ) {
				$text = mb_strtolower( $text );
			}
			else {
				$text = strtolower( $text );
			}
			$text = sanitize_file_name( $text );
			$new_filename = $this->format_hyphens( $text );
			$new_filename = trim( $new_filename, '-.' );
		}

		if ( empty( $manual_filename ) ) {
			$new_filename = $this->format_hyphens( $new_filename );
		}

		if ( !$manual_filename ) {
			$new_filename = apply_filters( 'mfrh_new_filename', $new_filename, $old_filename_no_ext, $media );
			$new_filename = sanitize_file_name( $new_filename );
		}

		// If the resulting filename had no any valid character, return NULL
		if ( empty( $new_filename ) ) {
			return null;
		}

		// We know have a new filename, let's add an extension.
		$new_filename = !empty( $new_ext ) ? ( $new_filename . '.' . $new_ext ) : $new_filename;

		return $new_filename;
	}

    function rename_file( $old, $new, $case_issue = false ) {
		// Some plugins can create custom thumbnail folders instead in the same folder, so make sure
		// the thumbnail folders are available.
		wp_mkdir_p( dirname($new) );

		// If there is a case issue, that means the system doesn't make the difference between AA.jpg and aa.jpg even though WordPress does.
		// In that case it is important to rename the file to a temporary filename in between like: AA.jpg ➡️ TMP.jpg ➡️ aa.jpg.
		if ( $case_issue ) {
			if ( !rename( $old, $old . md5( $old ) ) ) {
				$this->core->log( "🚫 The file couldn't be renamed (case issue) from $old to " . $old . md5( $old ) . "." );
				return false;
			}
			if ( !rename( $old . md5( $old ), $new ) ) {
				$this->core->log( "🚫 The file couldn't be renamed (case issue) from " . $old . md5( $old ) . " to $new." );
				return false;
			}
		}
		else if ( ( !rename( $old, $new ) ) ) {
			$this->core->log( "🚫 The file couldn't be renamed from $old to $new." );
			return false;
		}
		
		$this->core->log( "⚙️ PHP rename() function: " . basename( $old ) . " ➡️ " . basename( $new ) );	
		return true;
	}

	function rename( $media, $manual_filename = null, $undo = false, $method = null ) {
		$id = null;
		$post = null;
		$output = array();

		//MeowKit_MFRH_Helpers::timer_start("Rename");

		// Randomly throw an exception
		// if ( rand( 0, 4 ) == 1 ) {
		// 	throw new Exception( 'Random Exception' );
		// }

		// This filter permits developers to allow or not the renaming of certain files.
		$allowed = apply_filters( 'mfrh_allow_rename', true, $media, $manual_filename );
		if ( !$allowed ) {
			$this->core->log( "🚫 The renaming of this file is not allowed." );
			return $post;
		}

		// Check the arguments
		if ( is_numeric( $media ) ) {
			$id = $media;
			$post = get_post( $media, ARRAY_A );
		}
		else if ( is_array( $media ) ) {
			$id = $media['ID'];
			$post = $media;
		}
		else if ( is_object( $media ) ) {
			$id = $media->ID;
			$post = (array) $media;
		}
		else {
			die( 'Media File Renamer: rename() requires the ID or the array for the media.' );
		}

		//$force_rename = apply_filters( 'mfrh_force_rename', false );
		$force_rename = $this->core->get_option( 'force_rename', false );

		// Check attachment
		$need_rename = $this->core->check_attachment( $post, $output, $manual_filename, $force_rename, false );
		
		if ( $force_rename &&  is_null( $output['proposed_filename'] ) ) {
			$this->core->log( "🚫 Force Rename is enabled, but the proposed filename is null. Generating a new one without force rename." );
			$force_rename = false;
			$need_rename = $this->core->check_attachment( $post, $output, $manual_filename, $force_rename, false );
		}

		if ( !$need_rename ) {
			delete_post_meta( $id, '_require_file_renaming' );
			$this->core->log( "🚫 The file doesn't require renaming." );
			return $post;
		}

		// Prepare the variables
		$orig_attachment_url = null;
		$old_filepath = $output['current_filepath'];
		if( PHP_OS_FAMILY == 'Windows' ) {
			$old_filepath = str_replace( '\\', '/', $old_filepath );
		}

		$case_issue = $output['case_issue'];
		$new_filepath = $output['desired_filepath'];
		if( PHP_OS_FAMILY == 'Windows' ) {
			$new_filepath = str_replace( '\\', '/', $new_filepath );
		}

		$new_filename = $output['proposed_filename'];
		$manual = ( $output['manual'] || !empty( $manual_filename ) ) && !$undo;
		$path_parts = mfrh_pathinfo( $old_filepath );
		$directory = isset( $path_parts['dirname'] ) ? $path_parts['dirname'] : null; // Directory where the files are, under 'uploads', such as '2011/01'
		if( PHP_OS_FAMILY == 'Windows' ) {
			$directory = str_replace( '\\', '/', $directory );
		}

		$old_filename = $path_parts['basename']; // 'whatever.jpeg'
		// Get old extension and new extension
		$old_ext = isset( $path_parts['extension'] ) ? $path_parts['extension'] : null;
		$new_ext = $old_ext;
		if ( $manual_filename ) {
			$pp = mfrh_pathinfo( $manual_filename );

			if( array_key_exists( 'extension', $pp ) ){
				$new_ext = $pp['extension'];
			}

			$allowed_extensions = apply_filters( 'mfrh_allowed_extensions', $this->allowed_extensions ) ;

			if ( !in_array( $new_ext, $allowed_extensions ) ) {
				$this->core->log( "🚫 The extension $new_ext is not allowed." );
				die( 'Media File Renamer: The extension ' . $new_ext . ' is not allowed.' );
			}
		}

		if( !preg_match( '/\.\w+$/', $new_filename ) ) {
			$this->core->log( "⚡Used .$old_ext to prevent from a no-extension file." );
			$new_ext = $old_ext;

			$new_filename .= '.' . $old_ext;
			$new_filepath .= '.' . $old_ext;
		}

		$this->core->log( "🏁 Rename Media: " . $old_filename );
        $this->core->log( "🎯 New file will be: " . $new_filename );

		$noext_old_filename = $this->core->str_replace( '.' . $old_ext, '', $old_filename ); // Old filename without extension

		$noext_new_filename = $this->core->str_replace( '.' . $old_ext, '', $new_filename ); 
		$noext_new_filename = $this->core->str_replace( '.' . $new_ext, '', $new_filename ); 

		// Check for issues with the files
		if ( !$force_rename && !file_exists( $old_filepath ) ) {
			$this->core->log( "The original file ($old_filepath) cannot be found." );
			return [ 'warning' => '⚠️ The original file cannot be found.' ];
		}

		// Get the attachment meta
		$meta = wp_get_attachment_metadata( $id );
		if ( !$meta ) {
			$this->core->log( "The attachment metadata cannot be found." );
			return [ 'warning' => '⚠️ The attachment metadata cannot be found.' ];
		}

		// Get the information about the original image
		// (which means the current file is a rescaled version of it)
		$is_scaled_image = isset( $meta['original_image'] ) && !empty( $meta['original_image'] );
		$original_is_ideal = $is_scaled_image ? $new_filename === $meta['original_image'] : false;

		if ( !$original_is_ideal && !$case_issue && !$force_rename && file_exists( $new_filepath ) ) {
			$this->core->log( "The new file already exists ($new_filepath). It is not a case issue. Renaming cancelled." );
			return [ 'warning' => '⚠️ The new filename already exists. Renaming cancelled.' ];
		}

		// Keep the original filename (that's for the "Undo" feature)
		$original_filename = get_post_meta( $id, '_original_filename', true );
		if ( empty( $original_filename ) )
			add_post_meta( $id, '_original_filename', $old_filename, true );

		// Support for the original image if it was "-rescaled".
		// We should rename the -rescaled image first, as it could cause an issue
		// if renamed after the main file. In fact, the original file might have already
		// the best filename and evidently, the "-rescaled" one not.
		if ( $is_scaled_image ) {
			$meta_old_filename = $meta['original_image'];
			$meta_old_filepath = trailingslashit( $directory ) . $meta_old_filename;
			// In case of the undo, since we do not have the actual real original filename for that un-scaled image,
			// we make sure the -scaled part of the original filename is not used (that could bring some confusion otherwise).
			$meta_new_filename = preg_replace( '/\-scaled$/', '', $noext_new_filename ) . '-mfrh-original.' . $new_ext;
			$meta_new_filepath = trailingslashit( $directory ) . $meta_new_filename;
			if ( !$this->rename_file( $meta_old_filepath, $meta_new_filepath, $case_issue ) && !$force_rename ) {
				$this->core->log( "🚫 File $meta_old_filepath ➡️ $meta_new_filepath" );
				return $post;
			}
			// Manual Rename also uses the new extension (if it was not stripped to avoid user mistake)
			if ( $force_rename && !empty( $new_ext ) ) {
				$meta_new_filename = $this->core->str_replace( $old_ext, $new_ext, $meta_new_filename );
			}
			$this->core->log( "✅ File $old_filepath ➡️ $new_filepath" );
			do_action( 'mfrh_path_renamed', $post, $old_filepath, $new_filepath );
			$meta['original_image'] = $meta_new_filename;
		}

		// Rename the main media file.
		if ( !$this->rename_file( $old_filepath, $new_filepath, $case_issue ) && !$force_rename ) {
			$this->core->log( "🚫 File $old_filepath ➡️ $new_filepath" );
			return $post;
		}
		$this->core->log( "✅ File $old_filepath ➡️ $new_filepath" );
		do_action( 'mfrh_path_renamed', $post, $old_filepath, $new_filepath );

		

		if ( $meta ) {
			if ( isset( $meta['file'] ) && !empty( $meta['file'] ) )
				$meta['file'] = $this->core->str_replace( $noext_old_filename, $noext_new_filename, $meta['file'] );
			if ( isset( $meta['url'] ) && !empty( $meta['url'] ) && strlen( $meta['url'] ) > 4 )
				$meta['url'] = $this->core->str_replace( $noext_old_filename, $noext_new_filename, $meta['url'] );
			else
				$meta['url'] = $noext_new_filename . '.' . $old_ext;
		}

		// Better to check like this rather than with wp_attachment_is_image
		// PDFs also have thumbnails now, since WP 4.7
		$has_thumbnails = isset( $meta['sizes'] );

		// Loop through the different sizes in the case of an image, and rename them.
		$orig_image_urls = array();
		if ( $has_thumbnails ) {

			// In the case of a -scaled image, we need to update the next_old_filename.
			// next_old_filename is based on the filename of the main file, but since
			// it contains '-scaled' but not its thumbnails, we need to modify it here.
			// $noext_new_filename is to support this in case of undo.
			if ( $is_scaled_image ) {
				$noext_new_filename = preg_replace( '/\-scaled$/', '', $noext_new_filename );
				$noext_old_filename = preg_replace( '/\-scaled$/', '', $noext_old_filename );
			}

			$handled_sizes = array(); // This is used to recognized which sizes has been already handled (in the case of the same file used in many different size names, to avoid double processing) 
			$orig_image_urls = array();
			$orig_image_data = wp_get_attachment_image_src( $id, 'full' );
			$orig_image_urls['full'] = $orig_image_data[0];
			foreach ( $meta['sizes'] as $size => $meta_size ) {
				if ( !isset($meta['sizes'][$size]['file'] ) || in_array( $size, $handled_sizes ) ) {
					continue;
				}
				$meta_old_filename = $meta['sizes'][$size]['file'];
				$meta_old_filepath = trailingslashit( $directory ) . $meta_old_filename;

				$meta_new_filename = $this->core->str_replace( $old_ext, $new_ext, $meta_old_filename );
				$meta_new_filename = $this->core->str_replace( $noext_old_filename, $noext_new_filename, $meta_new_filename );

				$meta_new_filepath = trailingslashit( $directory ) . $meta_new_filename;
				$orig_image_data = wp_get_attachment_image_src( $id, $size );
				$orig_image_urls[$size] = $orig_image_data[0];

				// Double check files exist before trying to rename.
				if ( $force_rename || ( file_exists( $meta_old_filepath ) && 
						( ( !file_exists( $meta_new_filepath ) ) || is_writable( $meta_new_filepath ) ) ) ) {
					// Get the actual extension of the thumbnail file (important for PDF thumbnails which are .jpg)
					$meta_old_ext = pathinfo( $meta_old_filename, PATHINFO_EXTENSION );
					$meta_new_ext = pathinfo( $meta_new_filename, PATHINFO_EXTENSION );

					// WP Retina 2x is detected, let's rename those files as well
					if ( function_exists( 'wr2x_get_retina' ) ) {
						$wr2x_old_filepath = $this->core->str_replace( '.' . $meta_old_ext, '@2x.' . $meta_old_ext, $meta_old_filepath );
						$wr2x_new_filepath = $this->core->str_replace( '.' . $meta_new_ext, '@2x.' . $meta_new_ext, $meta_new_filepath );
						if ( file_exists( $wr2x_old_filepath )
							&& ( ( !file_exists( $wr2x_new_filepath ) ) || is_writable( $wr2x_new_filepath ) ) ) {

							// Rename retina file
							if ( !$this->rename_file( $wr2x_old_filepath, $wr2x_new_filepath, $case_issue ) && !$force_rename ) {
								$this->core->log( "🚫 Retina $wr2x_old_filepath ➡️ $wr2x_new_filepath" );
								return $post;
							}
							$this->core->log( "✅ Retina $wr2x_old_filepath ➡️ $wr2x_new_filepath" );
							do_action( 'mfrh_path_renamed', $post, $wr2x_old_filepath, $wr2x_new_filepath );
						}
					}

					// Rename meta file
					if ( !$this->rename_file( $meta_old_filepath, $meta_new_filepath, $case_issue ) && !$force_rename ) {
						$this->core->log( "🚫 File $meta_old_filepath ➡️ $meta_new_filepath" );
						return $post;
					}
					$meta['sizes'][$size]['file'] = $meta_new_filename;

					// Detect if another size has exactly the same filename
					foreach ( $meta['sizes'] as $s => $m ) {
						if ( !isset( $meta['sizes'][$s]['file'] ) )
							continue;
						if ( $meta['sizes'][$s]['file'] ==  $meta_old_filename ) {
							$this->core->log( "✅ Updated $s based on $size, as they use the same file (probably same size)." );
							$meta['sizes'][$s]['file'] = $meta_new_filename;
							array_push( $handled_sizes, $s );
						}
					}

					// Success, call other plugins
					array_push( $handled_sizes, $size );
					$this->core->log( "✅ File $meta_old_filepath ➡️ $meta_new_filepath" );
					do_action( 'mfrh_path_renamed', $post, $meta_old_filepath, $meta_new_filepath );

				}
			}
		}
		else {
			$orig_attachment_url = wp_get_attachment_url( $id );
		}

		// Update Renamer Meta
		delete_post_meta( $id, '_require_file_renaming' ); // This media doesn't require renaming anymore
		delete_post_meta( $id, '_mfrh_keep_filename' ); // Clear any "keep filename" decision since file was renamed
		// Clear cached rename data (computed during Re-analyze Library)
		delete_post_meta( $id, '_mfrh_proposed_filename' );
		delete_post_meta( $id, '_mfrh_used_method' );
		delete_post_meta( $id, '_mfrh_ideal_filename' );
		delete_post_meta( $id, '_mfrh_proposed_filename_exists' );

		if ( $manual && $this->core->get_option( 'autolock_manual', true ) ) {
			// If it was renamed manually (including undo), lock the file
			add_post_meta( $id, '_manual_file_renaming', true, true );
		}
		else if ( !$manual_filename && $this->core->get_option( 'autolock_auto', false ) ) {
			// If the user wants the media to be locked after an automatic rename
			add_post_meta( $id, '_manual_file_renaming', true, true );
		}

		// Update DB: Media and Metadata
		$upload_dir = wp_upload_dir();
		$upload_dir['basedir'] = str_replace( '\\', '/', $upload_dir['basedir'] );
		$new_filepath = str_replace( $upload_dir['basedir'] . '/', '', $new_filepath );

        update_attached_file( $id, $new_filepath );
		if ( $meta ) {
			wp_update_attachment_metadata( $id, $meta );
		}

		// Rename the main media file in WebP if it exists.
		$this->rename_alternative_image_formats( $old_filepath, $old_ext, $new_filepath,
			$new_ext, $case_issue, $force_rename, $post );

		clean_post_cache( $id ); // TODO: Would be good to know what this WP function actually does exactly (might be useless, but hopefully it does clear the cache)

		// Rename slug/permalink
		if ( $this->core->get_option( "rename_slug" ) ) {
			$oldslug = $post['post_name'];
			$info = mfrh_pathinfo( $new_filepath );
			$newslug = preg_replace( '/\\.[^.\\s]{3,4}$/', '', $info['basename'] );
			$post['post_name'] = $newslug;
			if ( wp_update_post( $post ) )
				$this->core->log( "🚀 Slug $oldslug ➡️ $newslug" );
		}

		// Post actions
		//MeowKit_MFRH_Helpers::timer_start("Post Actions");

		$used_method = $method == "auto" ? $this->core->last_used_method : $method;

		$this->core->add_to_media_history( $id, [
			'method' => $used_method,
			'metadata' => 'filename',
			'original' => $old_filename,
			'new' => $new_filename,
			'date' => date( 'Y-m-d H:i:s' ),
		] );

		// If the guid is not par of the sizes ( which happens for PDFs for example where sizes are only thumbnails and not the full file), we should update it as well, otherwise there will be broken links. 
		$guid_found_in_sizes = false;
		foreach( $orig_image_urls as $size => $url ) {
			if( $post['guid'] === $url ) {
				$guid_found_in_sizes = true;
				break;
			}
		}

		if ( !$guid_found_in_sizes )  {
			$this->core->call_post_actions( $id, $post, $meta, false, [], $post['guid'] );
			do_action( 'mfrh_media_renamed', $post, $old_filepath, $new_filepath, false, $method );
		}

		$this->core->call_post_actions( $id, $post, $meta, $has_thumbnails, $orig_image_urls, $orig_attachment_url );
		do_action( 'mfrh_media_renamed', $post, $old_filepath, $new_filepath, $undo, $method );

		//MeowKit_MFRH_Helpers::timer_log_elapsed("Post Actions");
		//MeowKit_MFRH_Helpers::timer_log_elapsed("Rename");
		//error_log("===");

		return $post;
	}

	function rename_alternative_image_formats( $old_filepath, $old_ext, $new_filepath, $new_ext, $case_issue = false, $force_rename = false, $post = null ) {
		$isWebP = ( $old_ext === 'webp' || $new_ext === 'webp' );
		$isAvif = ( $old_ext === 'avif' || $new_ext === 'avif' );
		$isPdf  = ( $old_ext === 'pdf' );
	
		if ( !$isWebP ) {
			$this->rename_alternative_image_format( '.webp', $old_filepath, $old_ext, $new_filepath, $new_ext, $case_issue, $force_rename, $post );
		}
		if ( !$isAvif ) {
			$this->rename_alternative_image_format( '.avif', $old_filepath, $old_ext, $new_filepath, $new_ext, $case_issue, $force_rename, $post );
		}
		if ( $isPdf ) {
			$this->rename_alternative_image_format( '.jpg', $old_filepath, $old_ext, $new_filepath, $new_ext, $case_issue, $force_rename, $post );
		}
	}

	function rename_alternative_image_format(
		$format_ext,
		$old_filepath,
		$old_ext,
		$new_filepath,
		$new_ext,
		$case_issue,
		$force_rename,
		$post = null
	) {
		$isPdf = $old_ext === 'pdf' && $format_ext === '.jpg';
		// Special handling for PDFs
		if ( $isPdf ) {
			$old_ext_for_replace = 'pdf';
			$new_ext_for_replace = 'pdf';
		} else {
			$old_ext_for_replace = $old_ext;
			$new_ext_for_replace = $new_ext;
		}
	
		// Construct file paths for renaming
		$alternatives = [
			[
				'old' => $this->core->str_replace( '.' . $old_ext_for_replace, $format_ext, $old_filepath ),
				'new' => $this->core->str_replace( '.' . $new_ext_for_replace, $format_ext, $new_filepath ),
			],
			[
				'old' => $this->core->str_replace( '.' . $old_ext_for_replace, '.' . $old_ext_for_replace . $format_ext, $old_filepath ),
				'new' => $this->core->str_replace( '.' . $new_ext_for_replace, '.' . $new_ext_for_replace . $format_ext, $new_filepath ),
			],
		];

		// Variables for PDF metadata update
		$attachment_id = null;
		$meta = null;
		$meta_updated = false;
	
		// Additional patterns for PDF thumbnails
		if ( $isPdf ) {

			// Retrieve the attachment ID from $post
			if ( $post ) {
				if ( is_numeric( $post ) ) {
					$attachment_id = $post;
				} elseif ( is_array( $post ) && isset( $post['ID'] ) ) {
					$attachment_id = $post['ID'];
				} elseif ( is_object( $post ) && isset( $post->ID ) ) {
					$attachment_id = $post->ID;
				}
			}
		
			// Get the attachment metadata
			if ( $attachment_id ) {
				$meta = wp_get_attachment_metadata( $attachment_id );
			}


			$noext_old_filename = pathinfo( $old_filepath, PATHINFO_FILENAME );
			$noext_new_filename = pathinfo( $new_filepath, PATHINFO_FILENAME );

			// The main PDF thumbnail (e.g., UserGuide-pdf.jpg)
			$main_pdf_thumbnail = $noext_old_filename . '-pdf' . $format_ext;

			// Find which size (if any) uses the main PDF thumbnail
			$main_thumbnail_size = null;
			if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
				foreach ( $meta['sizes'] as $size => $meta_size ) {
					if ( isset( $meta_size['file'] ) && $meta_size['file'] === $main_pdf_thumbnail ) {
						$main_thumbnail_size = $size;
						break;
					}
				}
			}

			// Thumbnails include '-pdf' in the filename
			$main_thumbnail_alternative = [
				'old' => $this->core->str_replace( $noext_old_filename . '.pdf', $noext_old_filename . '-pdf' . $format_ext, $old_filepath ),
				'new' => $this->core->str_replace( $noext_new_filename . '.pdf', $noext_new_filename . '-pdf' . $format_ext, $new_filepath ),
			];
			if ( $main_thumbnail_size ) {
				$main_thumbnail_alternative['size'] = $main_thumbnail_size;
			}
			$alternatives[] = $main_thumbnail_alternative;
	
			// Retrieve sizes from metadata for sized thumbnails (e.g., UserGuide-pdf-194x300.jpg)
			if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
				foreach ( $meta['sizes'] as $size => $meta_size ) {
					if ( isset( $meta_size['file'] ) ) {
						$meta_size_filename = $meta_size['file'];

						// Skip if this is the main PDF thumbnail (already handled above)
						if ( $meta_size_filename === $main_pdf_thumbnail ) {
							continue;
						}

						// Only process files that have size dimensions (e.g., UserGuide-pdf-100x100.jpg)
						$prefix = $noext_old_filename . '-pdf-';
						if ( strpos( $meta_size_filename, $prefix ) !== 0 ) {
							continue;
						}

						// Extract the size suffix (e.g., "100x100")
						$size_suffix = str_replace( [ $prefix, $format_ext ], '', $meta_size_filename );
	
						$alternatives[] = [
							'old' => $this->core->str_replace( $noext_old_filename . '.pdf', $noext_old_filename . '-pdf-' . $size_suffix . $format_ext, $old_filepath ),
							'new' => $this->core->str_replace( $noext_new_filename . '.pdf', $noext_new_filename . '-pdf-' . $size_suffix . $format_ext, $new_filepath ),
							'size' => $size, // Track which size this alternative belongs to
						];
					}
				}
			}
		}
	
		// Renaming logic remains the same
		foreach ( $alternatives as $alternative ) {
			$regex = '/' . str_replace( ".", "\.", $format_ext ) . '$/';
			$is_alternative = preg_match( $regex, $alternative['old'] );
			$old_file_ok = file_exists( $alternative['old'] );
			$new_file_ok = ( !file_exists( $alternative['new'] ) ) || is_writable( $alternative['new'] );
	
			if ( $old_file_ok && $new_file_ok ) {
				if ( !$this->rename_file( $alternative['old'], $alternative['new'], $case_issue ) && !$force_rename ) {
					$this->core->log( "🚫 PDF Thumbnail {$alternative['old']} ➡️ {$alternative['new']}" );
					return $post;
				}
				$this->core->log( "✅ PDF Thumbnail {$alternative['old']} ➡️ {$alternative['new']}" );
				do_action( 'mfrh_path_renamed', $post, $alternative['old'], $alternative['new'] );

				// Update metadata for PDF thumbnails
				if ( $isPdf && $meta && isset( $alternative['size'] ) ) {
					$size = $alternative['size'];
					if ( isset( $meta['sizes'][$size] ) ) {
						$meta['sizes'][$size]['file'] = basename( $alternative['new'] );
						$meta_updated = true;
					}
				}
			}
		}

		// Save updated metadata for PDF thumbnails
		if ( $meta_updated && $attachment_id && $meta ) {
			wp_update_attachment_metadata( $attachment_id, $meta );
			$this->core->log( "✅ Updated attachment metadata for PDF thumbnails (ID: $attachment_id)" );
		}
	}
}