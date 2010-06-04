=== Constant Contact for Wordpress ===
Contributors: jamesbenson, katzwebdesign
Donate link: http://integrationservic.es/donate.php
Tags: mail, email, newsletter, Constant Contact, plugin, sidebar, widget, mailing list, API, email marketing, newsletters, form, forms
Requires at least: 2.9
Tested up to: 2.9.2
Stable tag: trunk

Integrate Constant Contact into your website with this full-featured plugin.

== Description ==

<blockquote>
<strong>This plugin requires a <a href="http://bit.ly/tryconstantcontact" title="Sign up for a free Constant Contact trial" rel="nofollow">Constant Contact account</a>.</strong> Constant Contact offers a <em><a href="http://bit.ly/constant-contact-email" rel="nofollow">free 60 day trial</a></em>, so sign up and give this plugin a whirl.
</blockquote>

### Fully integrate your email marketing campaigns into your WordPress website ###

<h4>Take your website & newsletter to the next level</h4>

The Constant Contact for Wordpress plugin is the best email marketing plugin for WordPress: integrate your website seamlessly with your Constant Contact account. 

You can place a signup checkbox or list selection on your register page or use the signup widget anywhere in your website sidebar or PHP templates.

<h4>Plugin features:</h4>

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

If you use the plugin and find it useful please make sure to come back and vote so other users know it works.

== Installation ==

To install the plugin follow the steps below:

1. Upload `constant-contact-api` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Click the new main menu item called "Constant Contact".
4. You'll need to enter your username and password on the settings page then save the page to see your contact lists.
5. Now Configure the "Register Page Settings" to get the checkbox or list selection displayed on the user register page.
6. Alternatively configure the signup widget in the admin area, if widgets are not supported in your theme you'll have to place the code below somewhere into one of your PHP theme files:
<pre>
&lt;?php $sbw = new constant_contact_api_widget(); $sbw-&gt;widget(); ?&gt;
</pre>

Optionally, If you want to change the default plugin options you can you edit the config.php file before installing, this is not required because all the options are editable via the wp admin interface but doing so means you can install the plugin with your preferred settings or provide the plugin to clients using a default set of options.


== Changelog ==

= 1.1.2 =
* Minor bug fix, fixes `in_array(): Wrong datatype for second argument` error <a href="http://wordpress.org/support/topic/393359" rel="nofollow">reported here</a>.
* Added menu image for plugin, and forced plugin name to be on one line. Menu looks nicer now.
* If plugin is not configured, the other menu items (Activities, Import, Export, Lists) will not be displayed. Previously, they were displayed, but the pages were empty.

= 1.1.1 =
* Files updated: constant-contact-api-widget.php, readme.txt, /admin/options.php
* <em>Short story:</em> __Improved speed.__ <br /><em>Long story:</em> Fixes major potential bug - if you have noticed your site takes a long time to start loading, it may be because the plugin had been trying to access the Constant Contact API for the list values twice per page load. This structure has been totally revamped, and now the Constant Contact API is only accessed once upon changing settings. This release improves load time considerably by storing that information in the WordPress database. Added `cc_widget_lists_array` option to store Constant Contact lists, so that the API doesn't need to be called every page load. Now, API is only called when the plugin settings are saved.
* Wrapped the List Selection Title for the multi-select form element in a `label` tag, and removed line break.

= 1.1.0.1 = 
* Removed line break (`<br />`) before widget form to improve display of widget signup form
* Fixed widget description and title display issues by renaming variables from `$title` to `$widget_title` and `$description` to `$widget_description`.
* Converted some settings fields to `<textarea>` to make editing easier.

= 1.1 =
* Adds error messages if username & password aren't properly configured & working
* Replaced $_SESSION with $GLOBALS for servers with `register_globals` issues
* Improved widget error messages
	* Converted widget errors to list items (`<LI>`s), instead of items separated with `<BR />` for better standards compliance
	* Wrapped errors in `<LABEL>`s so that clicking an error will take users to the input
* Improved redirection upon widget submission; now properly redirects to the page the user was on, instead of the home page
* Added filters for more control over widget output:
	* apply_filters('constant_contact_form', $output); to widget output
	* apply_filters('constant_contact_form_success', $success);
	* apply_filters('constant_contact_form_description', $description);
	* apply_filters('constant_contact_form_errors', $errors);
	* apply_filters('constant_contact_form_submit', $submit_button);

= 1.0.10 =
* This release fixes a problem with 1and1 servers

= 1.0.7 =
* Problem with files in last release

= 1.0.6 =
* No code changes have been made in this release

= 1.0.5 =
* Fixed a bug relating to chunked http encoding in class.cc.php

== Upgrade Notice ==

= 1.1.2 =
* Minor bug fix, fixes `in_array(): Wrong datatype for second argument` error <a href="http://wordpress.org/support/topic/393359" rel="nofollow">reported here</a>.

= 1.1.1 =
* Fixes major potential bug - if you have noticed your site takes a long time to start loading, it may be because the plugin had been trying to access the Constant Contact API for the list values twice per page load. This structure has been totally revamped, and now the Constant Contact API is only accessed once upon changing settings. This release improves load time considerably by storing that information in the WordPress database. 
* Added `cc_widget_lists_array` option to store Constant Contact lists, so that the API doesn't need to be called every page load. Now, API is only called when the plugin settings are saved.

= 1.1.0.1 =
* If your widget description and titles were not displaying in the signup form, that is now fixed.

= 1.1 = 
* Fixes a potential `register_globals` issue
* Adds helpful filters for developers
* Improves widget error handling & error messages
* Improves 

= 1.0.10 =
This release fixes a problem with 1and1 servers

= 1.0.7 =
This version fixes a bug introduced in version 0.0.5.

= 1.0.6 =
This version simply updates the readme.txt file so the project description page is more useful.

= 1.0.5 =
This version fixes a major bug and all users should upgrade immediately.

== Frequently Asked Questions ==

= Do I need a Constant Contact account for this plugin? =
This plugin requires a [Constant Contact account](http://bit.ly/tryconstantcontact 'Sign up for Constant Contact').

Constant Contact is a great email marketing company -- their rates are determined by the number of contacts in your list, not how many emails you send. This means you can send unlimited emails per month for one fixed rate! [Give it a test run](http://bit.ly/constant-contact-signup 'Try out Constant Contact today').

= How do I use the new `apply_filters()` functionality? (Added 1.1) =
If you want to change some code in the widget, you can use the WordPress `add_filter()` function to achieve this.

You can add code to your theme's `functions.php` file that will modify the widget output. Here's an example:
<pre>
function my_example_function($widget) { 
	// The $widget variable is the output of the widget
	// This will replace 'this word' with 'that word' in the widget output.
	$widget = str_replace('this word', 'that word', $widget);
	// Make sure to return the $widget variable, or it won't work!
	return $widget;
}
add_filter('constant_contact_form', 'my_example_function');
</pre>

You can modify the widget output by hooking into any of the filters below in a similar manner.

* Entire form output: `constant_contact_form`
* Successful submission message: `constant_contact_form_success`
* Form description text: `constant_contact_form_description` (after it has been modified by `wpautop()`)
* Error message: `constant_contact_form_errors`
* Submit button: `constant_contact_form_submit` (includes entire `input` string)

= What is the plugin license? =
Good news, this plugin is free for everyone!

The plugin is [licensed under the GPL](http://www.gnu.org/licenses/gpl-3.0.txt "View the GPL License").