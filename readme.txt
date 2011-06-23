=== Constant Contact for Wordpress ===
Contributors: katzwebdesign, jamesbenson
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zackkatz%40gmail%2ecom&item_name=Constant%20Contact%20API%20Plugin&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: mail, email, newsletter, Constant Contact, plugin, sidebar, widget, mailing list, API, email marketing, newsletters, form, forms, event, events, event marketing
Requires at least: 2.9
Tested up to: 3.1.2
Stable tag: trunk

Integrate Constant Contact into your website with this full-featured plugin.

== Description ==

> __This plugin requires a <a href="http://www.constantcontact.com/index.jsp?pn=katzwebservices&cc=api" title="Sign up for a free Constant Contact trial" rel="nofollow">Constant Contact account</a>.__ <br />*Don't have an account?* Constant Contact offers a <a href="http://www.constantcontact.com/email-marketing/index.jsp?pn=katzwebservices&cc=api" rel="nofollow">free 60 day trial</a>, so sign up and give this plugin a whirl!

<h4>Fully integrate Constant Contact with your WordPress website.</h4>

The Constant Contact for Wordpress plugin is the best email marketing plugin for WordPress: integrate your website seamlessly with your Constant Contact account. 

You can place a signup checkbox or list selection on your register page or use the signup widget anywhere in your website sidebar or PHP templates.

<h3>Now featuring Event Marketing!</h3>
Version 2.1 of the plugin introduces <a href="http://conta.cc/hB5lnC" title="Learn more about Constant Contact Event Marketing" rel="nofollow">Constant Contact Event Marketing</a> functionality by allowing you to track events, registration, and registrants using the plugin. Simply navigate to Constant Contact > Events. Manage your events from inside WordPress!

<h3>Version 2.0</h3>
Version 2.0 of the Constant Contact API plugin brings great new capabilities, improved interface, and expanded instructions.  New capabilities include Campaign details (email sending statistics) and caching of data to dramatically improve load speed. The plugin has been majorly reconstructed to improve the way the API is accessed and reduce the number of calls to the API. Each administration screen has improved interfaces to make the plugin look and feel more like a native extension of WordPress. Great care has been taken to conform to WordPress standards of design.

<strong>The new Constant Contact Form Designer (CCFD)</strong> gives the Constant Contact API a form generation tool. The Form Designer allows users to generate unlimited number of unique forms and gives a wide variety of options that can be configured, including what fields to show in the signup form. There and tons of design options, including custom background images, border width, colors, fonts and much more.

<h4>Plugin features:</h4>
* Add signup checkbox and list selection to your register page and update profile page
* Add / edit / delete contact lists without visiting constantcontact.com
* Includes a powerful form designer
* View your events registration details and get updated with a dashboard widget
* Show contact list selection on register page with ability to exclude certain lists
* Automatically subscribe your user to one or more contact lists on the register page
* Customize the register page signup box (and list selection) title and description
* Add / edit / delete users from your constant contact account
* Add a signup widget to your sidebar or anywhere in your template
* Add extra fields to your signup form
* Uses the REST API

<h4>Plugin Support</h4>
To obtain support please use this link to the [wordpress forums](http://wordpress.org/tags/constant-contact-api).

<h4>If you like the plugin...</h4>
If you use the plugin and find it useful please make sure to come back and vote so other users know it works.

== Installation ==

To install the plugin follow the steps below:

1. Upload `constant-contact-api` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Activate the Constant Contact API: Form Designer plugin (optional)
4. Click the new main menu item called "Constant Contact".
5. You'll need to enter your username and password on the settings page then save the page to see your contact lists.
6. Now Configure the "Register Page Settings" to get the checkbox or list selection displayed on the user register page.

### Using the new Form Designer
1. Install this plugin.
2. Activate the Constant Contact API: Form Designer plugin
3. Configure the settings on the form designer by updating the settings in the boxes on the left. 
4. Next to "Form Name" where it says "Enter form name here," enter your form name.
5. Once you have configured and named your form, click Save Form.
6. In the Appearance menu of the administration, click the Widgets link.
7. Drag the widget named "Constant Contact Form Designer" into the sidebar. 
8. Configure the settings shown, then click the "Save" button at the bottom of the widget form.
9. You will see the signup widget you created on your website!
10. To edit the form, return the the Form Designer page (from Step 3) and click on the form tab with the name of the form you would like to edit. Edit the form, then click Update Form. The form will show as updated on your website.

== Changelog ==

= 2.2 = 
* Added an Events widget and shortcode
* Updated the readme to have Form Designer shortcode instructions (see FAQ)

= 2.1.4 = 
* Converted the plugin to using the <a href="http://codex.wordpress.org/HTTP_API" rel="nofollow">WordPress HTTP API</a> and `wp_remote_request()`. This should fix issues some users have been having with setting up the plugin (such as <a href="http://wordpress.org/support/topic/565047" rel="nofollow">issue #565047</a>)
* Fixed issue where if the Constant Contact username & password settings were incorrect, then saved again (and still incorrect), there would be an error `Warning: Cannot modify header information - headers already sent by...`
* Improved error messages so you'll know whether Constant Contact is having an issue of if it's a settings configuration issue.

= 2.1.3 =
* Fixed issues with legacy widget not updating "Show First Name" and "Show Last Name" settings (<a href="http://wordpress.org/support/topic/548028" rel="nofollow">issue #548028</a>)
* Improved legacy widget to show "More info" content and reflect changes to "Show List Selection?" checkbox setting
* Fixed "Invalid Argument" Line 183 error (<a href="http://wordpress.org/support/topic/547609" rel="nofollow">issue #547609</a>)
* Fixed issue with forms not redirecting upon success (<a href="http://wordpress.org/support/topic/547609" rel="nofollow">issue #547609</a>)

= 2.1.2 = 

* Form Designer
	* Determined that issues with Form Designer not displaying are caused by hosting configuration issues. <strong>Contact your web host and request that they "whitelist your domain for ModSecurity."</strong> View the FAQ section for more information.
	* Improved error notices for Form Designer when hosting issues are detected.
	* Improved the form generator javascript
* Legacy (non-Form Designer) widget
	* Improved speed
	* Fixed issue with "Please select at least 1 list" when Show List Selection was not checked
	* Restored functionality: incomplete form submissions once again fill in submitted data
* WP Registration form
	* Added support for Multisite registration forms
	* Vastly improved registration form functionality, including formatting of description, labels, and more.
	* Fixed <a href="http://wordpress.org/support/topic/432029">bug #432029</a>; "subscribe users by default" now functions properly when using the single opt-in checkbox
	* Added multiple filters to modify registration form, including `constant_contact_register_form`; you can now modify entire output of plugin on the registration page by using `add_filter('constant_contact_register_form', 'your_function_to_modify');`. <a href="http://codex.wordpress.org/Function_Reference/add_filter" rel="nofollow">Learn more about `add_filter` on WordPress.org</a>.
* Events - restored Events functionality that got messed up in 2.1.1.

= 2.1.1 =
* Improved Events page layout by adding Event Status filters and updating styles
* Added Events dashboard widget showing active & draft events

= 2.1 =
* Events Marketing page now available in the administration (under Constant Contact > Events)
	* View event and registrant details
* Improves speed of administration by caching Activities, Campaigns, Lists, and Events

= 2.0.1 =
* <strong>Fixed major bug</strong> where username and password would be reset when saving settings on the plugin's Registration options page. (<a href="http://wordpress.org/support/topic/532274" rel="nofollow">issue #532274</a>)
* Restored options to show or hide first and last names in Legacy widget (<a href="http://wordpress.org/support/topic/532932" rel="nofollow">issue #532932</a>)
* Fixed multiple Legacy widget bugs
* Remedied bug where registration form description wasn't displaying (<a href="http://wordpress.org/support/topic/513878" rel="nofollow">issue #513878</a>)
* Improved blog registration form HTML
* Improved Admin Profile lists HTML

= 2.0 = 
* <strong>Major upgrade</strong> - make sure to back up your database. If you already have installed the plugin, this upgrade may not transfer your current settings.
* Went through each page of the admin and made the layout and code better, and reworded the administration to <strong>make more sense</strong>
* Fixed Import, Export, Activity
* Converted the widget settings to be in the widget, not on a page.
* <strong>New Form Designer</strong> - Create awesome forms with tons of configuration options. This is really cool. <em>Requires a decent browser for the admin. Internet Explorer older than 2009 won't work.</em> Please leave feedback with issues - this feature is in Alpha.
	* Drag and drop inputs with live-updating form preview
	* Create custom gradients or choose from patterns or URL-based backgrounds
	* So, so much more.
* Lists will now be updated (<a href="http://wordpress.org/support/topic/423429">bug #423429</a>)
* Added a sample import CSV file in the plugin folder, named `email-import-sample.txt`
* Improved load time of the plugin & widget

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

= 2.2 = 
* Added an Events widget and shortcode
* Updated the readme to have Form Designer shortcode instructions (see FAQ)

= 2.1.4 = 
* Converted the plugin to using the <a href="http://codex.wordpress.org/HTTP_API" rel="nofollow">WordPress HTTP API</a>. This should fix issues some users have been having with setting up the plugin (such as <a href="http://wordpress.org/support/topic/565047" rel="nofollow">issue #565047</a>)
* Fixed issue where if the Constant Contact username & password settings were incorrect, then saved again (and still incorrect), there would be an error `Warning: Cannot modify header information - headers already sent by...`

= 2.1.3 =
* Fixed issues with legacy widget not updating "Show First Name" and "Show Last Name" settings (<a href="http://wordpress.org/support/topic/548028" rel="nofollow">issue #548028</a>)
* Improved legacy widget to show "More info" content and reflect changes to "Show List Selection?" checkbox setting
* Fixed "Invalid Argument" Line 183 error (<a href="http://wordpress.org/support/topic/547609" rel="nofollow">issue #547609</a>)
* Fixed issue with forms not redirecting upon success (<a href="http://wordpress.org/support/topic/547609" rel="nofollow">issue #547609</a>)

= 2.1.2 = 

* Form Designer
	* Determined that issues with Form Designer not displaying are caused by hosting configuration issues. <strong>Contact your web host and request that they "whitelist your domain for ModSecurity."</strong> View the FAQ section for more information.
	* Improved error notices for Form Designer when hosting issues are detected.
	* Improved the form generator javascript
* Legacy (non-Form Designer) widget
	* Improved speed
	* Fixed issue with "Please select at least 1 list" when Show List Selection was not checked
	* Restored functionality: incomplete form submissions once again fill in submitted data
* WP Registration form
	* Added support for Multisite registration forms
	* Vastly improved registration form functionality, including formatting of description, labels, and more.
	* Fixed <a href="http://wordpress.org/support/topic/432029">bug #432029</a>; "subscribe users by default" now functions properly when using the single opt-in checkbox
	* Added multiple filters to modify registration form, including `constant_contact_register_form`; you can now modify entire output of plugin on the registration page by using `add_filter('constant_contact_register_form', 'your_function_to_modify');`. <a href="http://codex.wordpress.org/Function_Reference/add_filter" rel="nofollow">Learn more about `add_filter` on WordPress.org</a>.
* Events - restored Events functionality that got messed up in 2.1.1.

= 2.1.1 =
* Improved Events page layout by adding Event Status filters and updating styles
* Added Events dashboard widget showing active & draft events

= 2.1 =
* Events Marketing page now available in the administration (under Constant Contact > Events)
	* View event and registrant details
* Improves speed of administration by caching Activities, Campaigns, Lists, and Events

= 2.0.1 =
* <strong>Fixed major bug</strong> where username and password would be reset when saving settings on the plugin's Registration options page. (<a href="http://wordpress.org/support/topic/532274" rel="nofollow">issue #532274</a>)
* Restored options to show or hide first and last names in Legacy widget (<a href="http://wordpress.org/support/topic/532932" rel="nofollow">issue #532932</a>)
* Fixed multiple Legacy widget bugs
* Remedied bug where registration form description wasn't displaying (<a href="http://wordpress.org/support/topic/513878" rel="nofollow">issue #513878</a>)
* Improved blog registration form HTML
* Improved Admin Profile lists HTML

= 2.0 = 
* <strong>Major upgrade</strong> - make sure to back up your database. If you already have installed the plugin, this upgrade may not transfer your current settings.
* Went through each page of the admin and made the layout and code better, and reworded the administration to <strong>make more sense</strong>
* Fixed Import, Export, Activity
* Converted the widget settings to be in the widget, not on a page.
* <strong>New Form Designer</strong> - Create awesome forms with tons of configuration options. This is really cool. <em>Requires a decent browser for the admin. Internet Explorer older than 2009 won't work.</em> Please leave feedback with issues - this feature is in Alpha.
	* Comes with a new Form Designer widget
	* Drag and drop inputs with live-updating form preview
	* Create custom gradients or choose from patterns or URL-based backgrounds
	* So, so much more. Check out the screenshots for an example.
* Lists will now be updated (<a href="http://wordpress.org/support/topic/423429">bug #423429</a>)
* Added a sample import CSV file in the plugin folder, named `email-import-sample.txt`
* Improved load time of the plugin & widget

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

== Screenshots ==
1. The administration screen is the landing page for all the functionality of the plugin.
2. Form Designer - New to 2.0, this bad boy is a custom form designer built right into WordPress.
3. An example of a form you can create with the custom form designer.
4. Add users to your registration process
5. View campaign details on the Campaigns screen
6. Example events widget display on the default WordPress theme, twentyten
7. Event widget configuration

== Frequently Asked Questions ==

= Do I need a Constant Contact account for this plugin? =
This plugin requires a [Constant Contact account](http://www.constantcontact.com/index.jsp?pn=katzwebservices&cc=api 'Sign up for Constant Contact').

Constant Contact is a great email marketing company -- their rates are determined by the number of contacts in your list, not how many emails you send. This means you can send unlimited emails per month for one fixed rate! [Give it a test run](http://www.constantcontact.com/features/signup.jsp?pn=katzwebservices&cc=api 'Try out Constant Contact today').

= Is there shortcode support? =

### Form Shortcode ###

There is shortcode support for the Form Designer forms: `[constantcontactapi]` with the following options:

<pre>
'formid' => 0, // REQUIRED
'before' => null,
'after' => null,
'redirect_url' => false,
'lists' => array(),
'title' => '',
'exclude_lists' => array(),
'description' => '',
'show_list_selection' => false,
'list_selection_title' => 'Add me to these lists:',
'list_selection_format' => 'checkbox'
</pre>

So to add a form, you would add the following in your content: `[constantcontactapi formid="3"]`

### Event Shortcode ###

To show event details, you can use the `[ccevents]` shortcode with the following options:

<pre>
'id' => null, // Show a specific event; enter Event ID (found on the Events page) to use
'limit' => 3, // Number of events to show by default
'showdescription' => true, // Show event Description
'datetime' => true, // Show event Date & Time
'location' => false, // Show event Location
'map' => false,  // Show map link for Location (if Location is shown)
'calendar' => false, // Show "Add to Calendar" link
'directtoregistration' => false, // Link directly to registration page, rather than event homepage
'newwindow' => false, // Open event links in a new window
'style' => true // Use plugin styles. Disable if you want to use your own styles (CSS)
</pre>

<strong>Sample Event Shortcodes</strong>

* To show event details for 5 events using the default settings, you would use `[ccevents limit=3]`
* To show event details for a single event with the id of `abc123` and also show the location details and map link, you would use: `[ccevents id="abc123" location=true map=true]`
* To use your own CSS file, you would use `[ccevents style=false]`


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

To modify the Events widget output, start with the following code, again in your theme's `functions.php` file:

<pre>
add_filter('cc_event_output_single', 'cc_event_output_single', 1, 2);

function cc_event_output_single($output, $pieces = array('start'=> '','title'=>'','description'=>'','date'=>'','calendar'=>'','location' => '', 'end'=>'')) {
	// The pieces of each event are stored in the $pieces array
	// So you can modify them and cut and paste in what order you
	// want the pieces to display
	return $pieces['start'].'<dt>Description</dt>'.$pieces['description'].$pieces['date'].$pieces['end'];	
}
</pre>

<strong>Some example filters:</strong>

* Entire form output: `constant_contact_form`
* Successful submission message: `constant_contact_form_success`
* Form description text: `constant_contact_form_description` (after it has been modified by `wpautop()`)
* Error message: `constant_contact_form_errors`
* Submit button: `constant_contact_form_submit` (includes entire `input` string)

= How do I use the Form Designer? =

### Using the new Form Designer
1. Install this plugin.
2. Activate the Constant Contact API: Form Designer plugin
3. Configure the settings on the form designer by updating the settings in the boxes on the left. 
4. Next to "Form Name" where it says "Enter form name here," enter your form name.
5. Once you have configured and named your form, click Save Form.
6. In the Appearance menu of the administration, click the Widgets link.
7. Drag the widget named "Constant Contact Form Designer" into the sidebar. 
8. Configure the settings shown, then click the "Save" button at the bottom of the widget form.
9. You will see the signup widget you created on your website!
10. To edit the form, return the the Form Designer page (from Step 3) and click on the form tab with the name of the form you would like to edit. Edit the form, then click Update Form. The form will show as updated on your website.

= Form Designer isn't showing up or working = 
Form Designer needs to be activated separately from the main plugin (see "How do I use the Form Designer?" above). Once you activate it, if it's still not working, it's likely a server issue. 

The problem is that your web server may think that Form Designer is an unwelcome script. In order to fix this, you should <strong>contact your web host and request that they "whitelist your domain for ModSecurity."</strong>. 

<a href="http://www.hostgator.com" rel="nofollow">HostGator</a> reps said whitelisting your own domain is <strong>not an issue that affects website security</strong>.

= My gradients aren't working on the Form Designer! =
This plugin uses <a href="http://ozh.in/kw">Ozh's gradient script</a>. Please refer to that page.

= What is the plugin license? =
Good news, this plugin is free for everyone! The plugin is [licensed under the GPL](http://www.gnu.org/licenses/gpl-3.0.txt "View the GPL License").