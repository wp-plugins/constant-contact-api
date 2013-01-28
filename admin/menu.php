<?php // $Id$

// register admin menu action
add_action('admin_menu', 'constant_contact_admin_menu');

// Create admin menu for the plugin
function constant_contact_admin_menu()
{
	// create new top-level menu
	add_menu_page('Constant Contact API', 'Constant Contact', 'manage_options', 'constant-contact-api', 'constant_contact_settings', plugins_url('images/constant-contact-admin-icon.png', __FILE__));

	// Only show these if settings are configured properly
	$cc = constant_contact_create_object();

	if(get_option('cc_configured')) {

		// Registration - Moved 2.0
		add_submenu_page( 'constant-contact-api', 'Registration &amp; Profile', 'Registration &amp; Profile', 'manage_options', 'constant-contact-registration', 'constant_contact_registration_settings');

		// Activities
		add_submenu_page( 'constant-contact-api', 'Activities', 'Activities', 'manage_options', 'constant-contact-activities', 'constant_contact_activities');

		// Events - Added 2.1
		add_submenu_page( 'constant-contact-api', 'Contacts', 'Contacts', 'manage_options', 'constant-contact-contacts', 'constant_contact_contacts');

		// Events - Added 2.1
		add_submenu_page( 'constant-contact-api', 'Events', 'Events', 'manage_options', 'constant-contact-events', 'constant_contact_events');

		// Import
		add_submenu_page( 'constant-contact-api', 'Import', 'Import', 'manage_options', 'constant-contact-import', 'constant_contact_import');

		// Contact Lists
		add_submenu_page( 'constant-contact-api', 'Lists', 'Lists', 'manage_options', 'constant-contact-lists', 'constant_contact_lists');

		// Campaigns - Added 2.0
		add_submenu_page( 'constant-contact-api', 'Campaigns', 'Campaigns', 'manage_options', 'constant-contact-campaigns', 'constant_contact_campaigns'); // Added 1.2

		// Stats - Added 2.3
		add_submenu_page( 'constant-contact-api', __('Analytics', 'constantstats'), __('Analytics', 'constantstats'), 'manage_options', 'constant-analytics', 'constant_contact_analytics');

	}

}
