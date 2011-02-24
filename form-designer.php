<?php
/*
Plugin Name: Constant Contact API: Form Designer (Alpha)
Plugin URI: http://integrationservic.es/constant-contact/wordpress-plugin.php
Description: Powerfully integrates <a href="http://conta.cc/bRojlN" target="_blank">Constant Contact</a> into your WordPress website.
Author: Katz Web Services, Inc.
Version: 2.0
Author URI: http://www.katzwebservices.com
*/

// register admin menu action
add_action('admin_menu', 'constant_contact_form_designer_admin_menu');
function constant_contact_form_designer_admin_menu() {
	add_submenu_page( 'constant-contact-api', 'Constant Contact Form Designer', 'Form Designer', 'administrator', 'constant-contact-forms', 'constant_contact_design_forms');
}

add_action('plugins_loaded', 'ccfg_init');

function ccfg_init() {
	add_action('widgets_init', 'constant_contact_form_load_widget');
	add_action('init', 'check_ccfg_compatibility');
	add_action('admin_notices', 'ccfg_compatibility_warning');
	add_action('admin_print_scripts', 'constant_contact_admin_widget_scripts');
	add_action('wp_print_scripts', 'constant_contact_widget_scripts');	
	add_action('admin_head', 'constant_contact_add_help' );
	add_action('admin_print_scripts-constant-contact_page_constant-contact-forms', 'cc_form_scripts');
	add_action('admin_print_scripts-constant-contact_page_constant-contact-forms', 'cc_form_script_swap');
	add_action('admin_print_styles-constant-contact_page_constant-contact-forms', 'cc_form_style');
	
	define('CC_FORM_GEN_PATH', plugin_dir_url(__FILE__));
}

// Gotta check, ya know?
function check_ccfg_compatibility() {
	if(!defined('CC_FILE_PATH')) {
		return false;
	} else {
		return true; 
	}
}

function ccfg_compatibility_warning() {
	if(!check_ccfg_compatibility() && floor($GLOBALS['wp_version'] > 3)) { ?>
	<div class="error"><p><strong><?php _e('The Constant Contact API plugin must be enabled to use the Form Generator.', 'constant-contact-form-generator' ); ?></strong></p></div>
	<?php 
		return false;
	} elseif(floor($GLOBALS['wp_version']) < 3) {?>
	<div class="error"><p><strong><?php _e('The Constant Contact Form Generator plugin requires WordPress 3.0 or greater. Sorry, folks!', 'constant-contact-form-generator' ); ?></strong></p></div>
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
	if(!check_ccfg_compatibility()) { return; }

	// Instead of forcing paragraphs, we're adding a filter that can be removed
	// and modified.
	add_filter('cc_widget_description', 'wpautop');
	
	require_once('form-widget.php');
	register_widget( 'constant_contact_form_widget' );
}

function constant_contact_admin_widget_scripts() {
	global $pagenow;
	if($pagenow == 'widgets.php' && check_ccfg_compatibility()) {
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

function constant_contact_retrieve_form($formid, $force_update=false) {
	$formid = (int)$formid;
	
	// If it is an array and we are not forcing an update, return the data
	if (!$force_update && $form = get_transient("cc_form_$formid")) {
		return $form;
	}
	
	$lists = get_option('cc_form_design');
	
	if($lists && is_array($lists) && isset($lists[$formid])) {
		$list = $lists[$formid];
		// Just the items we need, please.
		unset($list['_wp_http_referer'], $list['update-cc-form-nonce'], $list['meta-box-order-nonce'], $list['closedpostboxesnonce'], $list['save_form'], $list['page'], $list['form-name'], $list['action'], $list['form-style']);
	} else {
		$list = array('formOnly'=>true);
	}
	
	$list['output'] = 'html';
	$list['echo'] = true;
	$list['path'] = CC_FORM_GEN_PATH;
	$list['cc_success'] = (isset($_REQUEST['cc_success']));
	$form_string = CC_FORM_GEN_PATH.'form.php?'.http_build_query($list);
	$response = wp_remote_get($form_string);
	
	if( is_wp_error( $response ) ) {
		return false;
	} else {
		$form = $response['body'];
		// Save the array into the cc_form_id transient with a 30 day expiration
		set_transient("cc_form_$formid", $form, 60*60*24*30);
		return $form;
	}
}

function cc_form_get_selected_id() {
	if(isset( $_REQUEST['form'] )) {
		$cc_form_selected_id = (int) $_REQUEST['form'];
	} else {
		if(isset( $_REQUEST['cc-form-id'] )) { 
			$cc_form_selected_id = (int) $_REQUEST['cc-form-id'];
		} else {
			$cc_form_selected_id = -1;
		}
	}
	return $cc_form_selected_id;
}

function cc_form_process() {
	require_once( 'form-designer-functions.php' );
	
	$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'edit';

	$cc_form_selected_id = cc_form_get_selected_id();
		
	switch ( $action ) {
		case 'delete':
			if ( $deleted_form = wp_get_cc_form( $cc_form_selected_id ) ) {
				
				$delete_cc_form = wp_delete_cc_form( $cc_form_selected_id );
	
				if ( is_wp_error($delete_cc_form) ) {
					$messages[] = '<div id="message" class="error"><p>' . $delete_cc_form->get_error_message() . '</p></div>';
				} else {
					$messages[] = '<div id="message" class="updated"><p>' . __('The form '.$deleted_form['form-name'].' has been successfully deleted.') . '</p></div>';
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
			} else {
				die();
				// Reset the selected menu
				$cc_form_selected_id = -1;
				unset( $_REQUEST['form'] );
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
						$messages[] = '<div id="message" class="updated"><p>' . sprintf( __('The <strong>%s</strong> form has been successfully created.'), $new_form_title ) . '</p></div>';
					}
	
			// update existing form
			} else {
	
				if(wp_get_cc_form($cc_form_selected_id)) {
					$request = wp_update_cc_form_object($cc_form_selected_id, $_REQUEST);
					if(!is_wp_error($request)) {
						$messages[] = '<div id="message" class="updated after-h2"><p>' . sprintf( __('The <strong>%s</strong> form has been updated.'), $request['form-name'] ) . '</p></div>';
					} else {
						$messages[] = '<div id="message" class="error"><p>' . $cc_form_selected_id->get_error_message() . '</p></div>';
					}
				} else {
				
				}
			}
			break;
	}
	if(isset($messages) && is_array($messages)) { foreach($messages as $message) { echo $message; } }
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
    
    // jQuery
    wp_deregister_script('jquery');
    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js' );
    
    // jQuery UI
	wp_deregister_script('jquery-ui-core');
	wp_register_script( 'jquery-ui-core', plugin_dir_url(__FILE__).'js/jquery-ui-1.8.5.custom.js' );
}



function cc_form_scripts() {

# 	wp_dequeue_script('jquery-color');
	wp_dequeue_script('jquery-ui-sortable');
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'colorpicker' );
	#wp_enqueue_script( 'cc-jquery-ui', plugin_dir_url(__FILE__).'js/jquery.ui.min.js' );
	// Nav Menu functions
//	wp_enqueue_script( 'form-menu', plugin_dir_url(__FILE__).'js/form-menu.dev.js' );
	wp_enqueue_script( 'cc-cookie', plugin_dir_url(__FILE__).'js/jquery.cookie.js' );
	wp_enqueue_script( 'cc-tinymce', plugin_dir_url(__FILE__).'tiny_mce/jquery.tinymce.js');
	wp_enqueue_script( 'jquery-scrollfollow', plugin_dir_url(__FILE__).'js/jquery.scrollfollow.js');
	
	// Otto is the man.
	// http://ottopress.com/2010/passing-parameters-from-php-to-javascripts-in-plugins/
	wp_enqueue_script( 'cc-code', plugin_dir_url(__FILE__).'js/cc-code.js');
	$params = array('path' => plugin_dir_url(__FILE__), 'rand' => mt_rand(0, 10000000), 'adminajax' => admin_url('admin-ajax.php'));
	wp_localize_script('cc-code', 'ScriptParams', $params);
	
	// Metaboxes
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
}


function cc_form_style() {
	wp_enqueue_style( 'nav-menu', plugin_dir_url(__FILE__).'css/nav-menu.css' );
	wp_enqueue_style( 'cc-style', plugin_dir_url(__FILE__).'css/style.css' );
	wp_enqueue_style( 'cc-colorpicker', plugin_dir_url(__FILE__).'css/colorpicker.css' );
}

function wp_cc_form_setup() {
	global $cc_form_selected_id;
	require_once( 'form-designer-meta-boxes.php' );
	require_once( 'form-designer-functions.php' );
	$form = wp_get_cc_form($cc_form_selected_id);
	#r($form);
	$getHolder = $_GET;
	add_meta_box( 'formfields_select', __( 'Form Fields' ), 'cc_form_meta_box_formfields_select' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'presetoptions', __( 'Design Presets' ), 'cc_form_meta_box_presetoptions' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'backgroundoptions', __('Background'), 'cc_form_meta_box_backgroundoptions' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'border', __('Border'), 'cc_form_meta_box_border' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'fontstyles', __('Text Styles & Settings'), 'cc_form_meta_box_fontstyles' , 'constant-contact-form', 'side', 'default', array($form));
	add_meta_box( 'formdesign', __('Padding & Align'), 'cc_form_meta_box_formdesign' , 'constant-contact-form', 'side', 'default', array($form));
	$_GET = $getHolder;
}

function constant_contact_design_forms() {
	
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	if(!check_ccfg_compatibility()) { return false; }
	require_once( 'form-designer-functions.php' );
	
	global $cc_form_selected_id;
	$cc_forms = array();

//	$cc_form_selected_id = '';


// Container for any messages displayed to the user
$messages = array();

// Container that stores the name of the active menu
$cc_form_selected_title = '';

// The menu id of the current menu being edited
$cc_form_selected_id = cc_form_get_selected_id();

// Allowed actions: add, update, delete
$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'edit';

	// Work with the actions
	cc_form_process();
	
	// Get all forms
	$cc_forms = wp_get_cc_forms();
	#r($cc_forms);

	// Get recently edited nav menu
	$recently_edited = (int) get_user_option( 'cc_form_recently_edited' );
	
	// If there was no recently edited menu, and $cc_form_selected_id is a nav menu, update recently edited menu.
	if ( !$recently_edited && is_cc_form( $cc_form_selected_id ) ) {
		$recently_edited = $cc_form_selected_id;
			
	// Else if $cc_form_selected_id is not a menu and not requesting that we create a new menu, but $recently_edited is a menu, grab that one.
	} elseif ( -1 == $cc_form_selected_id && ! isset( $_REQUEST['form'] ) && is_cc_form( $recently_edited ) ) {
		$cc_form_selected_id = $recently_edited;
	}
	
	// Update the user's setting
	if ( $cc_form_selected_id != $recently_edited && is_cc_form( $cc_form_selected_id ) )
		update_user_meta( $current_user->ID, 'cc_form_recently_edited', $cc_form_selected_id );
	
	// If there's a menu, get its name.
	if ( ! $cc_form_selected_title && $_form = wp_get_cc_form( $cc_form_selected_id ) ) {
		$cc_form_selected_title = $_form['form-name'];
	}
	
	// Generate truncated menu names
	$previous_names = array();
	foreach( (array) $cc_forms as $key => $_cc_form ) {
		$name = isset($_cc_form['form-name']) ? $_cc_form['form-name'] : 'Form '+$key;
		
		$_cc_form['truncated_name'] = trim( wp_html_excerpt( $name, 30 ) );
		if ( isset($_cc_form['form-name']) && $_cc_form['truncated_name'] != $_cc_form['form-name'])
			$_cc_form['truncated_name'] .= '&hellip;';
		
		if(!in_array(sanitize_user( $name ), $previous_names)) { 
			$previous_names[] = sanitize_user( $name );
		} else {
			$namekey = sanitize_user( $name );
			$previous_names[$namekey] = isset($previous_names[$namekey]) ? ($previous_names[$namekey] + 1) : 1;
			$_cc_form['truncated_name'] .= ' ('.$previous_names[$namekey].')';
		}
		
		$cc_forms[$key]['truncated_name'] = $_cc_form['truncated_name'];
	}

?>
<?php 
	
wp_cc_form_setup(); 

?>
<div class="wrap">
<div>
	<div id="icon-themes" class="icon32"></div>
	<h2><?php esc_html_e('Constant Contact - Form Design'); ?></h2>
	<?php
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
				<h2>This form creator requires Javascript.</h2>
				<p class="description">The form designer uses a lot of Javascript to put together the sweet looking forms that it does, so please <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&answer=12654" target="_blank">turn Javascript on in your browser</a> and let's make some forms together!</p>
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


			<div class="nav-tabs-wrapper">
			<div class="nav-tabs">
				<?php
				foreach( (array) $cc_forms as $_cc_form ) :
					if(!isset($_cc_form['cc-form-id'])) { continue; }
					if ($cc_form_selected_id == $_cc_form['cc-form-id'] ) : ?><span class="nav-tab nav-tab-active">
							<?php echo esc_html( $_cc_form['truncated_name'] ); ?>
						</span><?php else : ?><a href="<?php
							echo esc_url(add_query_arg(
								array(
									'action' => 'edit',
									'form' => $_cc_form['cc-form-id'],
								),
								admin_url( 'admin.php?page=constant-contact-forms' )
							));
						?>" class="nav-tab hide-if-no-js">
							<?php echo esc_html( $_cc_form['truncated_name'] ); ?>
						</a><?php endif;
				endforeach;
				if ( -1 == $cc_form_selected_id ) : ?><span class="nav-tab menu-add-new nav-tab-active">
					<?php printf( '<abbr title="%s">+</abbr>', esc_html__( 'Add form' ) ); ?>
				</span><?php else : ?><a href="<?php
					echo esc_url(add_query_arg(
						array(
							'action' => 'edit',
							'form' => -1,
						),
						admin_url( 'admin.php?page=constant-contact-forms' )
					));
				?>" class="nav-tab menu-add-new">
					<?php printf( '<abbr title="%s">+</abbr>', esc_html__( 'Add form' ) ); ?>
				</a><?php endif; ?>
			</div>
			</div>
			<div class="menu-edit">
				<div id="form-fields">
					<div id="nav-menu-header">
						<div id="submitpost" class="submitbox">
							<div class="major-publishing-actions">
								<label class="form-preview-label howto open-label" for="form-name">
									<span><?php _e('Form Name'); ?></span>
									<input name="form-name" id="form-name" type="text" class="menu-name regular-text menu-item-textbox input-with-default-title" title="<?php esc_attr_e('Enter form name here'); ?>" value="<?php echo esc_attr( $cc_form_selected_title  ); ?>" />
								</label>
								<div class="publishing-action">
									<input class="button-primary menu-save" name="save_form" type="submit" value="<?php ($cc_form_selected_id != 0 && empty($cc_form_selected_id)) ? esc_attr_e('Create Form') : esc_attr_e('Save Form'); ?>" />
								</div><!-- END .publishing-action -->

								<?php if ( $cc_form_selected_id != -1 ) :  ?>
								<div class="delete-action">
									<a class="submitdelete deletion menu-delete" href="<?php echo esc_url( wp_nonce_url( admin_url('admin.php?page=constant-contact-forms&action=delete&amp;form=' . $cc_form_selected_id), 'delete-cc_form-' . $cc_form_selected_id ) ); ?>"><?php _e('Delete Form'); ?></a>
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
						<input type="hidden" name="cc-form-id" id="cc-form-id" value="<?php echo esc_attr( $cc_form_selected_id ); ?>" />
					</div><!-- END #nav-menu-header -->
					<div id="post-body">
					
						<div id="post-body-content">
							<?php
								
								$form = wp_get_cc_form($cc_form_selected_id);

								cc_form_meta_box_formfields($form);
							?>
						</div><!-- /#post-body-content -->
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