=== Constant Contact API ===
Contributors: jamesbenson
Donate link: http://integrationservic.es/donate.php
Tags: mail, email, newsletter, Constant Contact, plugin, sidebar, widget, mailing list, API
Requires at least: 2.9
Tested up to: 2.9.2
Stable tag: 1.0.3

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
	
To obtain support please use this link to the [wordpress forums](http://wordpress.org/tags/constant-contact-api).


== Installation ==

To install the plugin follow the steps below:

1. Upload `constant-contact-api` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Click the new main menu item called "Constant Contact".
4. You'll need to enter your username and password on the settings page then save the page to see your contact lists.
5. Now Configure the "Register Page Settings" to get the checkbox or list selection displayed on the user register page.
6. Alternatively configure the signup widget in the admin area, if widgets are not supported in your theme you'll have to place the code below into your theme:


`<?php
	// place this anywhere in your theme to output a signup form
	$sbw = new constant_contact_api_widget();
	$sbw->widget();
?>`


== About ==
The plugin is provided by [James Benson](http://justphp.co.uk/).

I also provide a [drupal module](http://integrationservic.es/constant-contact/drupal-module.php) and [php developers code](http://integrationservic.es/constant-contact/php-developers-code.php) for Constant Contact.

