<?php
/*
Plugin Name: Media File Renamer: Rename for better SEO (AI-Powered)
Plugin URI: https://meowapps.com
Description: Rename filenames and media metadata for SEO and tidyness. Using AI, manually, in bulk, or in so many other ways!
Version: 6.2.8
Author: Jordy Meow
Author URI: https://meowapps.com
Text Domain: media-file-renamer
Domain Path: /languages
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( !defined( 'MFRH_VERSION' ) ) {
  define( 'MFRH_VERSION', '6.2.8' );
  define( 'MFRH_PREFIX', 'mfrh' );
  define( 'MFRH_DOMAIN', 'media-file-renamer' );
  define( 'MFRH_ENTRY', __FILE__ );
  define( 'MFRH_PATH', dirname( __FILE__ ) );
  define( 'MFRH_URL', plugin_dir_url( __FILE__ ) );
  define( 'MFRH_ITEM_ID', 2188 );
}

require_once( 'classes/init.php');

?>