<?php // $Id$

// Create admin menu for the plugin
function constant_contact_admin_menu()
{
	// create new top-level menu
	add_menu_page('Constant Contact API', 'Constant Contact', 'administrator', 'constant-contact-api', 'constant_contact_api', plugins_url('constant-contact-api/admin/images/constant-contact-admin-icon.png'));
	
	// Fix the menu link to be on one line
	wp_enqueue_style('constant-contact-api-admin', plugins_url('constant-contact-api/admin/constant-contact-admin-css.css'), false, false, 'all');
	
	// Add blank item, so it looks nice
	add_submenu_page( 'constant-contact-api', '', '', 'administrator', 'constant-contact-api');
	
	// Activities
	add_submenu_page( 'constant-contact-api', 'Settings', 'Settings', 'administrator', 'constant-contact-settings', 'constant_contact_settings');
	
	// Only show these if settings are configured properly
	$cc = constant_contact_create_object();
	
	if(is_object($cc)) {
	
		// Activities
		add_submenu_page( 'constant-contact-api', 'Activities', 'Activities', 'administrator', 'constant-contact-activities', 'constant_contact_activities');
		
		// Import
		add_submenu_page( 'constant-contact-api', 'Import', 'Import', 'administrator', 'constant-contact-import', 'constant_contact_import');
		
		// Export
		add_submenu_page( 'constant-contact-api', 'Export', 'Export', 'administrator', 'constant-contact-export', 'constant_contact_export');
		
		// Contact Lists
		add_submenu_page( 'constant-contact-api', 'Lists', 'Lists', 'administrator', 'constant-contact-lists', 'constant_contact_lists');
	
	}
		
	add_action( 'admin_init', 'constant_contact_register_settings' );
}

?>