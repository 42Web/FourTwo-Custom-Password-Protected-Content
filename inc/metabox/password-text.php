<?php
/**
 * Add Metabox
 */
 
function bodas_cppc_add_metabox() {
	
	global $_wp_post_type_features;
	add_meta_box( 'bodas_cppc_textmeta', 'Custom Password Protect Page Content', 'bodas_cppc_metabox_cb', null, 'normal', 'high' );

}
add_action( 'add_meta_boxes', 'bodas_cppc_add_metabox' );


/**
 * Metabox Callback
 */
 
function bodas_cppc_metabox_cb($post) {
	
	global $post;
	
	$values = get_post_custom($post->ID);
	
	if (isset($values['bodas_cppc_text_string'])) {
		$editor_content = $values['bodas_cppc_text_string'][0];
	} else {
		$editor_content = null;
	}
	
	$editor_settings = array(
		'media_buttons' => false,
		'textarea_name' => 'bodas_cppc_text_string'
	);
	
	echo '<p class="bodas_cppc_metabox_desc">' . __('If you would like to override the default WordPress password protected content, or the content specified in this plugins global options, simply fill out the editor below.', 'bodas_cppc') . '</p>';
	
	wp_editor(htmlspecialchars_decode($editor_content), 'bodas_cppc_text_string', $editor_settings);
	
	wp_nonce_field( 'bodas_cppc_meta_box_nonce', 'meta_box_nonce' );
	
}


function bodas_cppc_metabox_save($post_id) {
	
	// bail if we're doing an auto save
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

	// if our nonce isn't there, or we can't verify it, bail
	if(!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'bodas_cppc_meta_box_nonce')) return;

	// if our current user can't edit this post, bail
	if(!current_user_can('edit_post')) return;

	// make sure your data is set before trying to save it
	if( isset( $_POST['bodas_cppc_text_string'] ) )
		update_post_meta( $post_id, 'bodas_cppc_text_string', htmlspecialchars($_POST['bodas_cppc_text_string']) );
	
}
add_action('save_post', 'bodas_cppc_metabox_save');
add_action('plugins_loaded', 'bodas_cppc_metabox_save');