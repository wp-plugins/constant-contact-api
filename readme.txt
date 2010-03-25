=== Constant Contact for Wordpress ===
Contributors: jamesbenson, katzwebdesign
Donate link: http://integrationservic.es/donate.php
Tags: mail, email, newsletter, Constant Contact, plugin, sidebar, widget, mailing list, API
Requires at least: 2.9
Tested up to: 2.9.2
Stable tag: 1.0.9

This plugin integrates the Constant Contact API into your wordpress blog.

== Description ==

__This plugin requires a <a href="http://bit.ly/cctrial" title="Sign up for Constant Contact" rel="nofollow">Constant Contact account</a>.__

The Constant Contact Wordpress plugin integrates features from the Constant Contact REST API into your wordpress blog, it's the only plugin you need if you use the constantcontact.com service and wordpress.

You can place a signup checkbox or list selection on your register page or use the signup widget anywhere in your website sidebar or PHP templates.

Below are the main features provided in the plugin:

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

If you use the plugin and find it useful please make sure to come back and vote so other users know it works and we get better placement in the wordpress.org search pages.


== Installation ==

To install the plugin follow the steps below:

1. Upload `constant-contact-api` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Click the new main menu item called "Constant Contact".
4. You'll need to enter your username and password on the settings page then save the page to see your contact lists.
5. Now Configure the "Register Page Settings" to get the checkbox or list selection displayed on the user register page.
6. Alternatively configure the signup widget in the admin area, if widgets are not supported in your theme you'll have to place the code below somewhere into one of your PHP theme files:

`<?php $sbw = new constant_contact_api_widget(); $sbw->widget(); ?>`

Optionally, If you want to change the default plugin options you can you edit the config.php file before installing, this is not required because all the options are editable via the wp admin interface but doing so means you can install the plugin with your preferred settings or provide the plugin to clients using a default set of options.


== Changelog ==

= 0.0.7 =
* Problem with files in last release

= 0.0.6 =
* No code changes have been made in this release

= 0.0.5 =
* Fixed a bug relating to chunked http encoding in class.cc.php

== Upgrade Notice ==

= 0.0.7 =
This version fixes a bug introduced in version 0.0.5.

= 0.0.6 =
This version simply updates the readme.txt file so the project description page is more useful.

= 0.0.5 =
This version fixes a major bug and all users should upgrade immediately.


== License ==

Good news, this plugin is free for everyone!
It's [licensed under the GPL](http://www.gnu.org/licenses/gpl-3.0.txt "View the GPL License").
