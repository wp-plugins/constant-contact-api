<?php
/*
Plugin Name: Constant Contact API: Form Designer (Alpha)
Plugin URI: http://integrationservic.es/constant-contact/wordpress-plugin.php
Description: Create fancy-lookin' forms for the Constant Contact API plugin that have tons of neat configuration options.
Author: Katz Web Services, Inc.
Version: 2.4.1
Author URI: http://www.katzwebservices.com
*/


// register admin menu action
add_action('admin_menu', 'constant_contact_form_designer_admin_menu', 30);
function constant_contact_form_designer_admin_menu() {
	if(get_option('cc_configured')) {
		add_submenu_page( 'constant-contact-api', 'Constant Contact Form Designer', 'Form Designer', 'manage_options', 'constant-contact-forms', 'constant_contact_design_forms');
	}
}

add_action('plugins_loaded', 'ccfg_init');

function ccfg_init() {
	global $pagenow;

	define('CC_FORM_GEN_PATH', plugin_dir_url(__FILE__));
	add_action('init', 'check_ccfg_compatibility');
	add_action('widgets_init', 'constant_contact_form_load_widget');
	add_action('wp_print_scripts', 'constant_contact_widget_scripts');

	if(!($pagenow === 'admin.php' && $_GET['page'] === 'constant-contact-forms')) { return; }

	add_action('admin_notices', 'ccfg_compatibility_warning');
	add_action('admin_print_scripts', 'constant_contact_admin_widget_scripts');
	add_action('admin_head', 'constant_contact_add_help' );

	add_action('admin_print_scripts-constant-contact_page_constant-contact-forms', 'cc_form_scripts');
	add_action('admin_print_scripts-constant-contact_page_constant-contact-forms', 'cc_form_script_swap');
	add_action('admin_print_styles-constant-contact_page_constant-contact-forms', 'cc_form_style');
}

// Gotta check, ya know?
function check_ccfg_compatibility() {
	global $cc;

	if(!defined('CC_FILE_PATH') || !function_exists('constant_contact_create_object')) {
		return null;
	}

	constant_contact_create_object();

	if(empty($cc)) {
		return 0;
	} else {
		return true;
	}
}

function ccfg_compatibility_warning() {
	global $wp_version;

	// If the plugins' not activated
	if(check_ccfg_compatibility() === null && floor($wp_version >= 3)) { ?>
	<div class="error"><p><strong><?php _e('The Constant Contact API plugin must be enabled to use the Form Generator.', 'constant-contact-api' ); ?></strong></p></div>
	<?php
		return false;
	}

	// Not set up right
	elseif(check_ccfg_compatibility() === 0) { ?>
		<div class="error"><p><strong><?php _e('The Constant Contact API plugin must be configured properly before using the Form Generator.', 'constant-contact-api' ); ?></strong></p></div>
	<?
		return false;
	}

	// Less than WP 3.0
	elseif(floor($wp_version) < 3) {?>
	<div class="error"><p><strong><?php _e('The Constant Contact Form Generator plugin requires WordPress 3.0 or greater. Sorry, folks!', 'constant-contact-api' ); ?></strong></p></div>
	<?php
		return false;
	}
}

function constant_contact_add_help() {
	$message = '
	<h3>Help is at the WordPress Forums</h3>
	<p>The best place to get support for this plugin is on the <a href="http://wordpress.org/tags/constant-contact-api">WordPress Constant Contact Plugin Forum</a>. Leave a message there with the problem you\'re having, and you\'ll get support eventually.</p>
	<h4>A show of support = more enthusiastic support</h4>
	<p>You can also request help from the plugin developers, <a href="mailto:info@katzwebservices.com">Zack Katz</a> and <a href="mailto:james@justphp.co.uk">James Benson</a>. Emails preceded by a PayPal donation (use the same email addresses) receive <strong><em>20,000% more attention</em></strong>!</p>
	';
	$message .= '<style type="text/css">#wpbody #screen-meta { z-index:999999!important; }</style>';

	add_contextual_help( 'constant-contact_page_constant-contact-settings', $message );
	add_contextual_help( 'constant-contact_page_constant-contact-activities', $message );
	add_contextual_help( 'constant-contact_page_constant-contact-lists', $message );
	add_contextual_help( 'constant-contact_page_constant-contact-import', $message );
	add_contextual_help( 'constant-contact_page_constant-contact-export', $message );
	add_contextual_help( 'constant-contact_page_constant-contact-campaigns', $message );
	add_contextual_help( 'constant-contact_page_constant-contact-forms', $message );

	return;
}

function constant_contact_form_load_widget() {
	$compat = check_ccfg_compatibility();
	if(empty($compat)) { return; }

	// Instead of forcing paragraphs, we're adding a filter that can be removed
	// and modified.
	add_filter('cc_widget_description', 'wpautop');

	require_once('widget-form-designer.php');
	register_widget( 'constant_contact_form_widget' );
}

function constant_contact_admin_widget_scripts() {
	global $pagenow;
	if($pagenow == 'widgets.php' && !empty($compat)) {
		wp_enqueue_script( 'admin-cc-widget', plugin_dir_url(__FILE__).'js/admin-cc-widget.js' );
	}
}

function is_widget_active_in_sidebar($name) {
	foreach($GLOBALS['_wp_sidebars_widgets'] as $key => $widgetarea) {
		if($key != 'wp_inactive_widgets') {
			if(is_array($widgetarea) && !empty($widgetarea)) {
				$length = strlen($name);
				foreach($widgetarea as $widget) {
					if(substr($widget, 0, $length) == $name) { return true; }
				}
			}
		}
	}
	return false;
}


function constant_contact_widget_scripts() {
	if ( is_widget_active_in_sidebar('constant_contact_form_widget') ) {
		wp_enqueue_script( 'jquery-form', false, 'jquery');
		wp_enqueue_script( 'cc-widget', plugin_dir_url(__FILE__).'js/cc-widget.js' );
	}
}

function constant_contact_retrieve_form($formid, $force_update=false, $unique_id = '') {
	global $cc_signup_count;
	$cc_signup_count++;
	$formid = (int)$formid;

	$success = constant_contact_get_transient($unique_id);
	$success = !empty($success);

	// If it is an array and we are not forcing an update, return the data
	if(!$success && !$force_update && $form = constant_contact_get_transient("cc_form_$formid")) {
		return $form;
	}

	$forms = get_option('cc_form_design');

	if($forms && is_array($forms) && isset($forms[$formid])) {
		$list = $forms[$formid];
		// Just the items we need, please.
		unset($list['_wp_http_referer'], $list['update-cc-form-nonce'], $list['meta-box-order-nonce'], $list['closedpostboxesnonce'], $list['save_form'], $list['page'], $list['form-name'], $list['action'], $list['form-style'], $list['presets'], $list['truncated_name']);
	} else {
		$list = array('formOnly'=>true);
	}

	$list['output'] = 'html';
	$list['time'] = time();
	$list['verify'] = sha1($unique_id.$list['time']);
	$list['echo'] = true;
	$list['path'] = CC_FORM_GEN_PATH;
	$list['cc_success'] = $success;
	$list['cc_signup_count'] = $cc_signup_count;
	$list['cc_request'] = empty($_REQUEST['uniqueformid']) ? array() : $_REQUEST;
	$list['uniqueformid'] = $unique_id;

	// Get the form from form.php
	$response = wp_remote_post( CC_FORM_GEN_PATH.'form.php', array(
	               'timeout' => 20,
	               'body' => $list
	            ));

	if( is_wp_error( $response ) ) {
		if(current_user_can('manage_options')) {
			echo '<!-- Constant Contact API Error: `wp_remote_post` failed with this error: '.$response->get_error_message().' -->';
		}
		return false;
	} else {
		$form = $response['body'];
		if(empty($_GET) && empty($_POST) && !$success) {
			// Save the array into the cc_form_id transient with a 30 day expiration
			constant_contact_set_transient("cc_form_$formid", $form, 60*60*24*30);
		}

		return $form;
	}
}

function cc_form_get_selected_id($allForms = array()) {

	if(isset($_REQUEST['form']) && (empty($_REQUEST['action']) || @$_REQUEST['action'] === 'edit' || @$_REQUEST['action'] === 'update')) {
		return $_REQUEST['form'];
	}

	if(empty($allForms)) {
		$cc_form_selected_id = isset($_REQUEST['cc-form-id']) ? (int)$_REQUEST['cc-form-id'] : -1;
	} else {
		$lastForm = end($allForms);
		$cc_form_selected_id = $lastForm['cc-form-id'];

		// Intstead of always showing new form, show last form possible.
		$_REQUEST['form'] = (int)$cc_form_selected_id;
		$_REQUEST['action'] = 'edit';
	}

	return $cc_form_selected_id;
}

function cc_form_process() {
	global $cc_form_selected_id;

	require_once( 'form-designer-functions.php' );

	// Allowed actions: add, update, delete
	$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'edit';

	if($action === 'edit') { return; }

	$cc_form_selected_id = cc_form_get_selected_id();

	switch ( $action ) {
		case 'delete_all':
			delete_option('cc_form_design');
			break;
		case 'delete':

			$cc_form_selected_id = isset($_REQUEST['form']) ? (int)$_REQUEST['form'] : $cc_form_selected_id;

			if ( $deleted_form = wp_get_cc_form( $cc_form_selected_id ) ) {

				$delete_cc_form = wp_delete_cc_form( $cc_form_selected_id );

				if ( is_wp_error($delete_cc_form) ) {
					$messages[] = '<div id="message" class="error"><p>' . $delete_cc_form->get_error_message() . '</p></div>';
				} else {
					$messages[] = '<div id="message" class="updated"><p>' . __('The form '.$deleted_form['form-name'].' has been successfully deleted.','constant-contact-api') . '</p></div>';
					// Select the next available menu
					$cc_form_selected_id = -1;
					$_cc_forms = wp_get_cc_forms( array('orderby' => 'name') );
					foreach( $_cc_forms as $index => $_cc_form ) {
						if ( $index == count( $_cc_forms ) - 1 ) {
							$cc_form_selected_id = $_cc_form['cc-form-id'];
							break;
						}
					}
				}
				$_REQUEST['deleted'] = 1;
			} else {
				$_REQUEST['deleted'] = 0;
				// Reset the selected menu
				$cc_form_selected_id = -1;
				unset( $_REQUEST['form'] );
				$messages[] = '<div id="message" class="error"><p>'.__('The form could not be deleted. The form may have already been deleted.', 'constant-contact-api').'</p></div>';
			}
			break;

		case 'update':
#			check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );
			// Add Form
			if ( -1 == $cc_form_selected_id ) {
				$new_form_title = trim( esc_html( $_REQUEST['form-name'] ) );
				if($new_form_title == 'Enter form name here') { $new_form_title = ''; }

					$cc_form_selected_id = wp_create_cc_form();

					if ( is_wp_error( $cc_form_selected_id ) ) {
						$messages[] = '<div id="message" class="error"><p>' . $cc_form_selected_id->get_error_message() . '</p></div>';
					} else {
						$messages[] = '<div id="message" class="updated"><p>' . sprintf( __('The <strong>%s</strong> form has been successfully created.','constant-contact-api'), $new_form_title ) . '</p></div>';
					}

			// update existing form
			} else {
				if(wp_get_cc_form($cc_form_selected_id)) {
					$request = wp_update_cc_form_object($cc_form_selected_id, $_REQUEST);
					if(!is_wp_error($request)) {
						$messages[] = '<div id="message" class="updated after-h2 fade"><p>' . sprintf( __('The <strong>%s</strong> form has been updated.','constant-contact-api'), $request['form-name'] ) . '</p></div>';
					} else {
						$messages[] = '<div id="message" class="error"><p>' . $cc_form_selected_id->get_error_message() . '</p></div>';
					}
				} else {

				}
			}
			break;
	}
	return $messages;

}


// register admin menu action



if(!function_exists('wp_dequeue_script')) {
	function wp_dequeue_script( $handle ) {
	    global $wp_scripts;
	    if ( !is_a($wp_scripts, 'WP_Scripts') )
	        $wp_scripts = new WP_Scripts();
 		$wp_scripts->dequeue( $handle );
 	}
}
if(!function_exists('wp_dequeue_style')) {
	function wp_dequeue_style( $handle ) {
	    global $wp_styles;
	    if ( !is_a($wp_styles, 'WP_Styles') )
	        $wp_styles = new WP_Styles();
 		$wp_styles->dequeue( $handle );
 	}
}

function cc_form_script_swap() {

    // Colorpicker
    wp_deregister_script( 'colorpicker' );
    wp_register_script( 'colorpicker', plugin_dir_url(__FILE__).'js/colorpicker.js' );

}



function cc_form_scripts() {

	wp_dequeue_script('jquery-ui-sortable');

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-sortable' );

	wp_enqueue_script( 'colorpicker' );

	// Nav Menu functions
	wp_enqueue_script( 'jquery-scrollfollow', plugin_dir_url(__FILE__).'js/jquery.scrollfollow.js', array('jquery', 'jquery-ui-core'));
//	wp_enqueue_script( 'form-menu', plugin_dir_url(__FILE__).'js/form-menu.dev.js' );
	wp_enqueue_script( 'cc-cookie', plugin_dir_url(__FILE__).'js/jquery.cookie.js' );
	wp_enqueue_script( 'cc-tinymce', plugin_dir_url(__FILE__).'tiny_mce/jquery.tinymce.js');

	// Otto is the man.
	wp_enqueue_script( 'cc-code', plugin_dir_url(__FILE__).'js/cc-code-dev.js');
	$params = array('path' => plugin_dir_url(__FILE__), 'rand' => mt_rand(0, 100000000), 'text' => cc_form_text());
	wp_localize_script('cc-code', 'ScriptParams', $params);

	// Metaboxes
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
}

function cc_form_text() {
	return apply_filters('constant_contact_form_design_custom_text', array(
		'reqlabel' => __('The %s field is required', 'constant-contact-api'),
	));
}


function cc_form_style() {
	wp_enqueue_style( 'nav-menu', plugin_dir_url(__FILE__).'css/nav-menu.css' );
	wp_enqueue_style( 'cc-style', plugin_dir_url(__FILE__).'css/style.css' );
	wp_enqueue_style( 'cc-colorpicker', plugin_dir_url(__FILE__).'css/colorpicker.css' );
}

function wp_cc_form_setup() {
	global $cc, $cc_form_selected_id;

	constant_contact_create_object();

	require_once( 'form-designer-meta-boxes.php' );
	require_once( 'form-designer-functions.php' );
	$form = wp_get_cc_form($cc_form_selected_id);
	#r($form);
	$getHolder = $_GET;
	add_meta_box( 'formlists_select', __( 'Default Newsletters','constant-contact-api' ), 'cc_form_meta_box_formlists_select' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'formfields_select', __( 'Form Fields','constant-contact-api' ), 'cc_form_meta_box_formfields_select' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'presetoptions', __( 'Design Presets','constant-contact-api' ), 'cc_form_meta_box_presetoptions' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'backgroundoptions', __('Background','constant-contact-api'), 'cc_form_meta_box_backgroundoptions' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'border', __('Border','constant-contact-api'), 'cc_form_meta_box_border' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'fontstyles', __('Text Styles & Settings','constant-contact-api'), 'cc_form_meta_box_fontstyles' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'formdesign', __('Padding & Align','constant-contact-api'), 'cc_form_meta_box_formdesign' , 'constant-contact-form', 'side', 'default', array($form));
	$_GET = $getHolder;
}

function constant_contact_design_forms() {

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.','constant-contact-api') );
	}
	$compat = check_ccfg_compatibility();
	if(empty($compat)) { return false; }

	require_once( 'form-designer-functions.php' );

	global $cc_form_selected_id;
	$cc_forms = array();


	// Container for any messages displayed to the user
	$messages = array();

	// Container that stores the name of the active menu
	$cc_form_selected_title = '';

	// Work with the actions and echo a message if there is one.
	$messages = cc_form_process();

	// Get all forms
	$cc_forms = wp_get_cc_forms();

	// The menu id of the current menu being edited
	$cc_form_selected_id = cc_form_get_selected_id($cc_forms);

	// If there's a menu, get its name.
	if ( ! $cc_form_selected_title && $_form = wp_get_cc_form( $cc_form_selected_id ) ) {
		$cc_form_selected_title = $_form['form-name'];
	}


?>
<?php

wp_cc_form_setup();

?>
<script>
jQuery(document).ready(function($) {
	$('#delete_all_forms').click(function() {
		return confirm('<?php _e('Delete all form Data? This cannot be undone.', 'constant-contact-api'); ?>');
	});
});
</script>
<div class="wrap">
<div>
	<a class="alignright" href="<?php echo wp_nonce_url( admin_url('admin.php?page=constant-contact-forms&action=delete_all&amp;form=all'), 'delete-all' ); ?>" id="delete_all_forms"><?php _e('Clear All Forms', 'constant-contact-api'); ?></a>
	<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>"><?php _e('Constant Contact Plugin', 'constant-contact-api'); ?> &gt;</a> <?php _e('Form Designer', 'constant-contact-api'); ?></h2>
	<?php

	constant_contact_add_gravity_forms_notice();

	if(isset($messages) && is_array($messages)) {
		foreach( $messages as $message ) :
			echo $message . "\n";
		endforeach;
	}
	$formURL = '';
	if($cc_form_selected_id != -1) {
		$formURL = '&form='.(int)$cc_form_selected_id;
	}
	?>
	<div class="hide-if-js">
		<div class="widefat form-table">
			<div class="wrap" style="width:60%; padding:10px 15px;">
				<h2><?php _e('This form creator requires Javascript.', 'constant-contact-api'); ?></h2>
				<p class="description"><?php _e('The form designer uses a lot of Javascript to put together the sweet looking forms that it does, so please <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&answer=12654" target="_blank">turn Javascript on in your browser</a> and let\'s make some forms together!', 'constant-contact-api'); ?></p>
			</div>
		</div>
	</div>
	<form id="cc-form-settings" action="<?php echo admin_url( 'admin.php?page=constant-contact-forms'.$formURL ); ?>" method="post" enctype="multipart/form-data" class="hide-if-no-js">
	<div id="nav-menus-frame">
	<div id="menu-settings-column" class="metabox-holder">

		<div id="settings">
<!--
			<input type="hidden" name="form" id="nav-menu-meta-object-id" value="<?php echo esc_attr( $cc_form_selected_id ); ?>" />
			<input type="hidden" name="action" value="add-form-item" />
-->
			<?php /* wp_nonce_field( 'add-menu_item', 'menu-settings-column-nonce' ); */ ?>
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<?php do_meta_boxes( 'constant-contact-form', 'side', null ); ?>
			</div>
		</div>

	</div><!-- /#menu-settings-column -->
	<div id="menu-management-liquid">
		<div id="menu-management">

			<div id="examplewrapper">

				<div class="grabber"></div>

				<a href="#" id="stopFollowingMe"></a>
			</div><!-- end ExampleWrapper -->

			<style type="text/css">
			.nav-tab a {text-decoration: none; width: 100%; height: 100%; display: block;}
			.nav-tab a:link,.nav-tab a:visited {color: #aaa;}
			.nav-tab a:hover, .nav-tab a:active { color: #d54e21; }
			.nav-tab-active a { color: #464646;}
			</style>
			<div class="nav-tabs-wrapper">
			<div class="nav-tabs">
				<?php

				foreach( (array) $cc_forms as $_cc_form ) {
					if(!isset($_cc_form['cc-form-id'])) { continue; }
					?>
					<a href="<?php
							echo esc_url(add_query_arg(
								array(
									'action' => 'edit',
									'form' => $_cc_form['cc-form-id'],
								),
								admin_url( 'admin.php?page=constant-contact-forms' )
							));
						?>" class="hide-if-no-js nav-tab<?php if ($cc_form_selected_id == $_cc_form['cc-form-id'] ) { echo ' nav-tab-active'; } ?>">
							<?php echo !empty($_cc_form['truncated_name']) ? esc_html( $_cc_form['truncated_name'] ) : sprintf(__('Form #%d', 'constant-contact-api'), ($_cc_form['cc-form-id'] + 1)); ?>
					</a>
					<?php
				}
				if ( -1 == $cc_form_selected_id ) : ?><span class="nav-tab menu-add-new nav-tab-active">
					<?php printf( '<abbr title="%s">+</abbr>', esc_html__( 'Add form','constant-contact-api' ) ); ?>
				</span><?php else : ?><a href="<?php
					echo esc_url(add_query_arg(
						array(
							'action' => 'edit',
							'form' => -1,
						),
						admin_url( 'admin.php?page=constant-contact-forms' )
					));
				?>" class="nav-tab menu-add-new">
					<?php printf( '<abbr title="%s">+</abbr>', esc_html__( 'Add form','constant-contact-api' ) ); ?>
				</a><?php endif; ?>
			</div>
			</div>
			<div class="menu-edit" style="border:none;">
				<div id="form-fields">
					<div id="nav-menu-header">
						<div id="submitpost" class="submitbox">
							<div class="major-publishing-actions">
								<label class="form-preview-label howto open-label" for="form-name">
									<span><?php _e('Form Name'); ?></span>
									<input name="form-name" id="form-name" type="text" class="menu-name regular-text menu-item-textbox input-with-default-title" title="<?php echo esc_attr_e('Enter form name here', 'constant-contact-api'); ?>" value="<?php echo esc_attr( $cc_form_selected_title  ); ?>" />
								</label>
								<div class="publishing-action">
									<input class="button-primary menu-save" name="save_form" type="submit" value="<?php ($cc_form_selected_id != 0 && empty($cc_form_selected_id)) ? esc_attr_e('Create Form', 'constant-contact-api') : esc_attr_e('Save Form', 'constant-contact-api'); ?>" />
								</div><!-- END .publishing-action -->

								<?php if ( $cc_form_selected_id != -1 ) :  ?>
								<div class="delete-action">
									<a class="submitdelete deletion menu-delete" href="<?php echo esc_url( wp_nonce_url( admin_url('admin.php?page=constant-contact-forms&action=delete&amp;form=' . $cc_form_selected_id), 'delete-cc_form-' . $cc_form_selected_id ) ); ?>"  onclick="return confirm('<?php _e('Are you sure you want to delete this form?', 'constant-contact-api'); ?>');"><?php _e('Delete Form', 'constant-contact-api'); ?></a>
									<span>In a post or page, use <code>[constantcontactapi formid="<?php echo (int)$cc_form_selected_id; ?>"]</code> <a href="http://wordpress.org/extend/plugins/constant-contact-api/faq/" target="_blank">Learn More.</a></span>
								</div><!-- END .delete-action -->
								<?php  endif; ?>
							</div><!-- END .major-publishing-actions -->
						</div><!-- END #submitpost .submitbox -->
						<?php
						wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
						wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
						wp_nonce_field( 'update-cc-form', 'update-cc-form-nonce' );
						?>
						<input type="hidden" name="action" value="update" />
						<input type="hidden" name="cc-form-id" id="cc-form-id" value="<?php echo (int)$cc_form_selected_id; ?>" />
					</div><!-- END #nav-menu-header -->
					<div id="post-body">
						<div id="post-body-content">
							<?php

								$form = wp_get_cc_form($cc_form_selected_id);

								cc_form_meta_box_formfields($form);
							?>
						</div><!-- /#post-body-content -->
						<div class="clear"></div>
					</div><!-- /#post-body -->
				</div><!-- /#update-nav-menu -->
			</div><!-- /.menu-edit -->
		</div><!-- /#menu-management -->
	</div><!-- /#menu-management-liquid -->
	</div><!-- /#nav-menus-frame -->
	</form><!-- /#tha-form -->
</div><!-- /.wrap-->
<?php

} // End design forms

?>