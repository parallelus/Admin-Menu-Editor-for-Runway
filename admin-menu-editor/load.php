<?php
/*
	Extension Name: Admin Menu Editor
	Extension URI: https://github.com/parallelus/Admin-Menu-Editor-for-Runway
	Version: 0.8.2
	Description: Manage your WordPress admin menus and create custom theme menus.
	Author: Parallelus
	Author URI: http://para.llel.us
*/

// Settings
$fields = array(
	'var' => array( 'menu_url', 'menu_name', 'page_name', 'menu_permissions', 'slug', 'function' ),
	'array' => array()
);

$default = array();

$settings = array(
	'name' 			=> __( 'Admin Menu', 'runway' ),
	'option_key' 	=> $shortname.'admin-menu-editor',
	'fields' 		=> $fields,
	'default'		=> $default,
	'parent_menu' 	=> 'framework-options',
	//'menu_permissions' => 5,
	'file' 			=> __FILE__,
	'js' 			=> array(
						FRAMEWORK_URL.'extensions/admin-menu-editor/js/menu-nav.custom.dev.js',
						FRAMEWORK_URL.'framework/js/jquery.tmpl.min.js',
						'jquery',
						'utils',
						'jquery-ui-core',
						'jquery-ui-widget',
						'jquery-ui-mouse',
						'jquery-ui-sortable',
						'jquery-ui-draggable',
						'jquery-ui-dialog',
						'jquery-ui-position',
					),
	'css' 			=> array(
						FRAMEWORK_URL.'framework/css/smoothness/jquery-ui-1.8.23.custom.css',
						FRAMEWORK_URL.'extensions/admin-menu-editor/css/style.css',
					)
);
global $admin_Dashboard_Admin, $admin_dashboard_settings, $extm;

// Required components
include $extm->extensions_dir.'admin-menu-editor/object.php';

$admin_dashboard_settings = new Admin_Dashboard_Settings_Object( $settings );
// Load admin components
if ( is_admin() ) {
	include $extm->extensions_dir.'admin-menu-editor/settings-object.php';
	$admin_Dashboard_Admin = new Admin_Dashboard_Admin_Object( $settings );
}

?>
