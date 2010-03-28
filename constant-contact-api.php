<?php
/*
Plugin Name: Constant Contact API
Plugin URI: http://integrationservic.es/constant-contact/wordpress-plugin.php
Description: Integrates the <a href="http://bit.ly/cctrial" target="_blank">Constant Contact API</a> into your wordpress blog.
Author: James Benson
Version: 1.0.10
Author URI: http://justphp.co.uk/
*/

	// load our config file, this sets the default path and default PHP constants
	require_once dirname(__FILE__) . '/config.php';
	require_once CC_FILE_PATH . 'functions.php';
	require_once CC_FILE_PATH . 'user.php';
	require_once CC_FILE_PATH . 'constant-contact-api-widget.php';


	// load admin only files
	if(is_admin()):
		require_once CC_FILE_PATH . 'admin/install.php';
		require_once CC_FILE_PATH . 'admin/menu.php';
		require_once CC_FILE_PATH . 'admin/options.php';
		require_once CC_FILE_PATH . 'admin/import.php';
		require_once CC_FILE_PATH . 'admin/export.php';
		require_once CC_FILE_PATH . 'admin/lists.php';
		require_once CC_FILE_PATH . 'admin/activities.php';

		// register admin menu action
		add_action('admin_menu', 'constant_contact_admin_menu');

		// register user delete action
		add_action('delete_user', 'constant_contact_delete_user');

		// register the install / uninstall hooks
		register_activation_hook( __FILE__, 'constant_contact_activate' );
		register_deactivation_hook( __FILE__, 'constant_contact_deactivate' );
	endif;

	// register our widgets and post handlers
	add_action('widgets_init', 'constant_contact_load_widgets');
	add_action('init', 'constant_contact_submit_widget');


	// register user update action
	add_action('profile_update', 'constant_contact_profile_update');

	// register show user update form action
	add_action('show_user_profile', 'constant_contact_show_user_profile');

	// register user registration action
	add_action('register_post', 'constant_contact_register_post', 10, 3);

	// register show user register form action
	add_action('register_form', 'constant_contact_register_form');
?>