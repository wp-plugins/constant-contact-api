=== Constant Contact API ===
Contributors: jamesbenson
Donate link: http://integrationservic.es/donate.php
Tags: mail, email, newsletter, Constant Contact, API, REST
Requires at least: 2.9
Tested up to: 2.9.2
Stable tag: 1.0.0

This plugin integrates the Constant Contact API into your wordpress blog.

== Description ==

The Constant Contact Wordpress plugin integrates features from the Constant Contact REST API into your wordpress blog, it's the only plugin you need if you use the constantcontact.com service and wordpress.

    * Add signup checkbox and list selection to your register page and update profile page
    * Add / edit / delete contact lists without visiting constantcontact.com
    * Show contact list selection on register page with ability to exclude certain lists
    * Automatically subscribe your user to one or more contact lists on the register page
    * Customize the register page signup box (and list selection) title and description
    * Add / edit / delete users from your constant contact account
    * Add a signup widget to your sidebar or anywhere in your template
    * Add extra fields to your signup form
    * Uses the REST API
	
To obtain support please use the (http://wordpress.org/forums/ "wordpress forums").


== Installation ==

To install the plugin follow the steps below:

1. Upload `constant-contact-api` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Click the new main menu item called "Constant Contact".
4. You'll need to enter your username and password on the settings page then save the page to see your contact lists.
5. Now Configure the "Register Page Settings" to get the checkbox or list selection displayed on the user register page.
6. Alternatively configure the signup widget and display a dynamic sidebar (enable on the admin widgets page) or place the code below directly into your template and it will output the signup form wherever you like:

`<?php
	$sbw = new constant_contact_api_widget();
	$sbw->widget();
?>`

You can include widget arguments if you need to, the code below shows how to use all arguments:

`<?php
	$sbw = new constant_contact_api_widget();
	$args = array(
		'title' => 'My Signup Widget', 
		'description' => 'My Signup Widget Description', 
		'before_title' => '<h4>', 
		'after_title' => '</h4>',
		'before_widget' => '', 
		'after_widget' => '',
	);
	$sbw->widget($args);
?>`


== About ==
The plugin is provided by (http://justphp.co.uk/ "James Benson").
I also provide a (http://integrationservic.es/constant-contact/drupal-module.php "drupal module") and (http://integrationservic.es/constant-contact/php-developers-code.php "php developers code") for Constant Contact.

