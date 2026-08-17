<?php

class Meow_MFRH_UI
{
	private $core = null;


	function __construct($core)
	{
		$this->core = $core;

		$show_in_library = $this->core->get_option('media_library_field', 'none') !== 'none';
		if( $show_in_library) {
			add_action('add_meta_boxes', array($this, 'add_rename_metabox'));

			add_filter('manage_media_columns', array($this, 'add_media_columns'));
			add_action('manage_media_custom_column', array($this, 'manage_media_custom_column'), 10, 2);
		}

		$dashboard_enabled = $this->core->get_option('dashboard', true);
		if( $dashboard_enabled ) {
			add_action('admin_menu', array($this, 'admin_menu'));
		}

		add_filter('media_send_to_editor', array($this, 'media_send_to_editor'), 20, 3);
		add_action('post-plupload-upload-ui', array($this, 'on_upload_method'));
	}

	function on_upload_method()
	{

		$on_upload = $this->core->get_option('on_upload_method', 'none');
		$fields = array(
			'Filename' => $this->core->get_option('sync_on_' . $on_upload . '_filename', false),
			'Alt' => $this->core->get_option( 'sync_on_' . $on_upload . '_alt', false ),
			'Title' => $this->core->get_option( 'sync_on_' . $on_upload . '_title', false ),
			'Description' => $this->core->get_option( 'sync_on_' . $on_upload . '_description', false ),
			'Caption' => $this->core->get_option( 'sync_on_' . $on_upload . '_caption', false ),
		);
		
		$enabledFields = array_filter($fields, function($value) {
			return $value !== false;
		});
		
		$enabledKeys = array_keys($enabledFields);
		$formattedString = '(' . implode(', ', $enabledKeys) . ')';

		if ( empty($enabledFields) ) {
			$formattedString = '(⚠️ ' . __('No on upload fields are enabled', 'media-file-renamer') . ')';
		} 
		
		$run_later = $this->core->get_option('on_upload_run_later', false) ? 'true' : 'false';
		$screen = get_current_screen();
		switch ($screen->id) {
			case 'media':
?>
				<div id="mfrh-on-upload-method" data-on-upload-method="<?php echo $on_upload; ?>" data-fields="<?php echo $formattedString; ?>" data-run-later="<?php echo $run_later; ?>"></div>
<?php
				break;
			default:
		}
	}

	function admin_menu()
	{
		add_media_page(
			'Media File Renamer',
			__('Renamer', 'media-file-renamer'),
			'read',
			'mfrh_dashboard',
			array($this, 'rename_media_files'),
			1
		);
	}

	function media_send_to_editor($html, $id, $attachment)
	{
		$output = array();
		$this->core->check_attachment(get_post($id, ARRAY_A), $output);
		return $html;
	}

	public function rename_media_files()
	{
		echo '<div id="mfrh-media-rename"></div>';
	}

	function add_rename_metabox()
	{
		add_meta_box(
			'mfrh_media',
			'Renamer',
			array($this, 'attachment_fields'),
			'attachment',
			'advanced',
			'high',
			null
		);
	}

	function attachment_fields($post)
	{
		if ($post) {
			echo '
				<div class="mfrh-renamer-field" data-id="' . $post->ID . '"></div>
				<div style="line-height: 15px; font-size: 12px; margin-top: 10px;">' . __('After an update, please reload this Edit Media page.', 'media-file-renamer') . '</div>
			';
		}
	}

	function add_media_columns($columns)
	{
		$columns['mfrh_column'] = __('Renamer', 'media-file-renamer');
		return $columns;
	}

	function manage_media_custom_column($column_name, $id)
	{
		if ($column_name === 'mfrh_column') {
			echo '<div class="mfrh-renamer-field" data-id="' . $id . '"></div>';
		}
	}
}
