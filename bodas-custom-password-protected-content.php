<?php
/*
Plugin Name: 	BODA's Custom Password Protected Content
Plugin URI: 	https://github.com/BODA82/bodas-custom-password-protected-content
Description: 	This plugin changes the default password protected text on either a global, or page-by-page basis.
Version: 		1.0
Author: 		Christopher Spires
Author URI: 	http://cspir.es
License:		GPL2
License URI: 	https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:	boda_wp
*/

/**
 * General Plugin Settings
 */

define('BODAS_CPPC_VERSION', '1.0');

/** 
 * Add Metabox Settings
 */
include('inc/metabox/password-text.php');

/**
 * Add Plugin Settings Page
 */
 
function bodas_cppc_admin_add_page() {
	
	global $bodas_cppc_settings_page;
	
	$bodas_cppc_settings_page = add_options_page('BODA\'s Custom Password Protected Content', 'Custom Password Protected Content', 'manage_options', 'bodas_cppc', 'bodas_cppc_admin_page');
	
	add_action('admin_enqueue_scripts', 'bodas_cppc_admin_scripts');

}
add_action('admin_menu', 'bodas_cppc_admin_add_page');


/**
 * Plugin Styles/Scripts
 */

function bodas_cppc_admin_scripts($hook_suffix) {
	
	global $bodas_cppc_settings_page;
	
	if ($bodas_cppc_settings_page == $hook_suffix || 'post.php' == $hook_suffix)
		wp_enqueue_style('bodas_cppc_admin_styles', plugins_url('inc/css/styles.css', __FILE__), null, BODAS_CPPC_VERSION, 'all');
		wp_enqueue_script('bodas_cppc_admin_scripts', plugins_url('inc/js/functions.js', __FILE__), array('jquery'), BODAS_CPPC_VERSION, true);
		
} 
 


/**
 * Build Plugin Settings Page
 */

function bodas_cppc_admin_page() {
?>
	
	<div class="wrap">
		<div class="bodas_cppc_inner">
			<h1><?php _e('BODA\'s Custom Password Protected Content', 'boda_wp'); ?></h1>
			<form action="options.php" method="post">
				<?php settings_fields('bodas_cppc_options'); ?>
				<?php do_settings_sections('bodas_cppc'); ?>
				<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'boda_wp'); ?>" />
			</form>
		</div>
	</div>

<?php	
}


/**
 * Define Plugin Settings
 */

add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init(){

	register_setting( 'bodas_cppc_options', 'bodas_cppc_options', 'bodas_cppc_options_validate' );
	add_settings_section('bodas_cppc_main', 'Main Settings', 'bodas_cppc_section_text', 'bodas_cppc');
	add_settings_field('bodas_cppc_text_string', 'Global Password Protected Text', 'bodas_cppc_setting_string', 'bodas_cppc', 'bodas_cppc_main');

}

/**
 * Section Callback
 */

function bodas_cppc_section_text() {
	
	echo '<p>' . __('The options below are for setting a global password protected page message. If you do not enter a custom message, the default WordPress message will display. Alternatively, you can also specify an individual pages message when editing that page by using the WP Custom Password Protected Text metabox.', 'boda_wp') . '</p>';

}

/**
 * Field Callback
 */
 
function bodas_cppc_setting_string() {
	
	$options = get_option('bodas_cppc_options');
	
	if (isset($options['text_string'])) {
		$editor_content = $options['text_string'];
	} else {
		$editor_content = null;
	}
	
	$editor_settings = array(
		'media_buttons' => false,
		'textarea_name' => 'bodas_cppc_options[text_string]'
	);
	
	wp_editor(htmlspecialchars_decode($editor_content), 'bodas_cppc_text_string', $editor_settings);

}


/**
 * Content Validation
 */

function bodas_cppc_options_validate($input) {
	
	$html['text_string'] = htmlspecialchars($input['text_string']);
	
	return $html;

}

/**
 * Modified Password Form
 */
 
function bodas_cppc_form() {
	
	global $post;
	
	// Default Content
	$default_content = __("To view this protected post, enter the password below:", "boda_wp");
	
	// Global Content
	$options = get_option('bodas_cppc_options');
  	$global_content = htmlspecialchars_decode($options['text_string']);
  	
  	// Page Content
  	$post_custom = get_post_custom($post->ID);
  	if (isset($post_custom['bodas_cppc_text_string'])) {
		$page_content = htmlspecialchars_decode($post_custom['bodas_cppc_text_string'][0]);
	} else {
		$page_content = null;
	}
  	
  	// Set $form_content
  	if (!is_null($page_content)) {
	  	$form_content = $page_content;
  	} elseif (!empty($global_content)) {
	  	$form_content = $global_content;
  	} else {
	  	$form_content = $default_content;
  	}
  	
  	
	$output = '
	<form class="post-password-form" action="' . get_option('siteurl') . '/wp-login.php?action=postpass" method="post">
		<p>' . $form_content . '</p>
		<p>
			<label for="pwbox-' . $post->ID . '">Password:
				<input id="pwbox-' . $post->ID . '" type="password" size="20" name="post_password" />
			</label>
			<input type="submit" value="' . __("Submit", "boda_wp") . '" name="Submit" />
		</p>
	</form>';
	return $output;
	
}  

add_filter('the_password_form','bodas_cppc_form');