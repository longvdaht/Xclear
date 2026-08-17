<?php

if ( class_exists( 'MeowPro_MFRH_Core' ) && class_exists( 'Meow_MFRH_Core' ) ) {
	function mfrh_admin_notices() {
		echo '<div class="error"><p>Thanks for installing the Pro version of Media File Renamer :) However, the free version is still enabled. Please disable or uninstall it.</p></div>';
	}
	add_action( 'admin_notices', 'mfrh_admin_notices' );
	return;
}

spl_autoload_register(function ( $class ) {
  $file = null;
  if ( strpos( $class, 'Meow_MFRH' ) !== false ) {
    $file = MFRH_PATH . '/classes/' . str_replace( 'meow_mfrh_', '', strtolower( $class ) ) . '.php';
  }
  else if ( strpos( $class, 'MeowKit_MFRH_' ) !== false ) {
    $file = MFRH_PATH . '/common/' . str_replace( 'meowkit_mfrh_', '', strtolower( $class ) ) . '.php';
  }
  else if ( strpos( $class, 'MeowKitPro_MFRH_' ) !== false ) {
    $file = MFRH_PATH . '/common/premium/' . str_replace( 'meowkitpro_mfrh_', '', strtolower( $class ) ) . '.php';
  }
  else if ( strpos( $class, 'MeowPro_MFRH' ) !== false ) {
    $file = MFRH_PATH . '/premium/' . str_replace( 'meowpro_mfrh_', '', strtolower( $class ) ) . '.php';
  }
  if ( $file && file_exists( $file ) ) {
    require( $file );
  }
});

// We should NOT remove this, as API is not a class (and so it will not be autoloaded).
require_once( MFRH_PATH . '/classes/api.php');

// In admin or Rest API request (REQUEST URI begins with '/wp-json/')
//if ( is_admin() || MeowKit_MFRH_Helpers::is_rest() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	global $mfrh_core;
	$mfrh_core = new Meow_MFRH_Core();
  global $mfrh_rest;
  $mfrh_rest = $mfrh_core->rest;
//}

?>