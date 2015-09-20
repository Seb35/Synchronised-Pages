<?php
/**
 * @package Synchronised Pages
 * @version 0.1.1
 * @license WFTPL 2.0
 */


/**
 * Register the new status (Template) and the taxonomy Synchronised pages
 *
 * @action init
 */
function synchronised_pages_add_stati() {
	
	// Register a custom post status for the templates: these are privately-published posts in a final form (not drafts)
	// The main advandage over the Private core status is you have a tab on the top of the post list, to avoid mixing
	// really private posts and template posts. The drawback of such a custom status is that, when you deactivate the
	// extension, the templates are no more in the post list, although you can access them through their post id.
	register_post_status( 'template', array(
		// translators: New post status
		'label'		=> __( 'Template', 'synchronised-pages' ),
		'private'	=> true,
		// translators: Label of the link to the specific post status
		'label_count'=> _n_noop( 'Template <span class="count">(%s)</span>', 'Templates <span class="count">(%s)</span>', 'synchronised-pages' ),
	) );
	
	// Labels of the taxonomy below
	//$labels = array(
	//	'name'			=> __( 'Genres', 'synchronised-pages' ),
	//	'singular_name'	 => __( 'Genre', 'synchronised-pages' ),
	//	'search_items'	=> __( 'Search Genres', 'synchronised-pages' ),
	//	'all_items'		 => __( 'All Genres', 'synchronised-pages' ),
	//	'parent_item'	 => __( 'Parent Genre', 'synchronised-pages' ),
	//	'parent_item_colon' => __( 'Parent Genre:', 'synchronised-pages' ),
	//	'edit_item'		 => __( 'Edit Genre', 'synchronised-pages' ),
	//	'update_item'	 => __( 'Update Genre', 'synchronised-pages' ),
	//	'add_new_item'	=> __( 'Add New Genre', 'synchronised-pages' ),
	//	'new_item_name'	 => __( 'New Genre Name', 'synchronised-pages' ),
	//	'menu_name'		 => __( 'Genre', 'synchronised-pages' ),
	//);
	
	// Register the taxonomy where are grouped the imports of synchronised pages
	register_taxonomy( 'synchronised_pages', null, array(
		// translators: New taxonomy
 		'label'		=> __( 'Synchronised pages', 'synchronised-pages' ),
	//	'labels'	 => $labels,
		// translators: Description of the taxonomy
		'description'=> __( 'Sets of synchronised pages from a template page', 'synchronised-pages' ),
		'hierarchical' => true,
		'public'	 => false,
		'show_ui'	=> true,
	) );
	
	// Get post types where we want synchronised pages
	$post_types = get_option( 'synchronised-pages-setting-post-types', null );
	if ( $post_types ) $post_types = array_keys( (array) $post_types );
	else if ( $post_types === null ) $post_types = array( 'page' );
	else $post_types = array();
	
 	// Register taxonomy for each requested post type
	foreach ( $post_types as $post_type ) {
		
		register_taxonomy_for_object_type( 'synchronised_pages', $post_type );
	}
}


/**
 * Append the status Template in the UI
 *
 * This part will be mostly cut when bug #12706 will be solved
 *
 * @action admin_footer-post.php
 * @action admin_footer-post-new.php
 */
function synchronised_pages_append_status() {
	
	global $post;
	
	// Retrieve the registered post types to be synchronised
	$post_types = get_option( 'synchronised-pages-setting-post-types', null );
	if ( $post_types ) $post_types = array_keys( (array) $post_types );
	else if ( $post_types_setting === null ) $post_types_setting = array( 'page' );
	else $post_types = array();
	
	// The post type is made available for only registered post types, but also, as an exception,
	// for existing Templates even after the post type becomes unregistered (NB: it should be tested
	// if saving does work, possibly the 'template' status is automatically changed to 'publish' by WordPress in this case)
	if ( ! in_array( $post->post_type, $post_types ) && $post->post_status != 'template' ) return;
	
	// Prepare variable JavaScript+HTML snippets
	$template_label = esc_js( esc_html( __('Template', 'synchronised-pages') ) );
	// translators: A button to save
	$update_template_label = esc_js( esc_html( __('Update the template', 'synchronised-pages') ) );
	// translators: A button to save
	$publish_template_label = esc_js( esc_html( __('Publish the template', 'synchronised-pages') ) );
	$complete = '';
	$label = '';
	if ( $post->post_status == 'template' ) {
		$complete = ' selected="selected"';
		$label = ' <span id="post-status-display">'.$template_label.'</span>';
	}
	
	// Add this script since WordPress doesn’t automatically add the status in the user interface and no hook is available (see bug #12706)
	echo '
	<script>
	jQuery(document).ready(function($){
		
		// Add the status in the stati available for the user
		$(\'select#post_status\').append(\'<option value="template"'.$complete.'>'.$template_label.'</option>\');
		$(\'.misc-pub-post-status label\').append(\''.$label.'\');
		
		// Save some original values
		var synchronised_pages_original_publish = $(\'#publish\').attr(\'name\'),
		    synchronised_pages_original_publish_val = $(\'#publish\').val(),
		    synchronised_pages_original_status = $(\'option:selected\', $(\'#post_status\')).val();
		
		var synchronised_pages_manage_status = function() {
			
			if( $(\'option:selected\', $(\'#post_status\')).val() == \'template\' ) {
				
				// Hide the \'Save draft\' button and change the \'Publish\' button to save
				$(\'#save-post\').hide();
				$(\'#publish\').attr(\'name\', \'save\');
				$(\'#publish\').val(synchronised_pages_original_status==\'template\'?\''.$publish_template_label.'\':\''.$update_template_label.'\');
				
			} else {
				
				// Restore the original \'Publish\' button, either save or publish depending on the current state – WP manages the \'Save\' button
				if( synchronised_pages_original_publish ) {
					$(\'#publish\').attr(\'name\', synchronised_pages_original_publish);
					$(\'#publish\').val(synchronised_pages_original_publish_val);
				}
			}
		};
		synchronised_pages_manage_status();
		$(\'#post-status-select\').find(\'.save-post-status\').click( synchronised_pages_manage_status );
		$(\'#post-status-select\').find(\'.cancel-post-status\').click( synchronised_pages_manage_status );
	});
	</script>
	';
}


/**
 * Remove the possibility to create a tag in the taxonomy synchronised_pages in the user interface
 *
 * @action admin_print_scripts-edit-tags.php
 */
function synchronised_pages_remove_tag_creation() {
	
	echo '<style type="text/css">
	.taxonomy-synchronised_pages #col-left {
		display: none;
	}
	.taxonomy-synchronised_pages #col-right {
		width: 100%;
	}
</style>
';
}


/**
 * Display the state Template or Synchronised in the posts list
 *
 * @filter display_post_states
 */
function synchronised_pages_display_state( $states ) {
	
	global $post;
	
	// Display nothing in the 'Template' posts list
	if ( get_query_var( 'post_status' ) == 'template' ) return $states;
	
	// Display the word 'Template' for such posts only in the 'All' posts list
	if ( $post->post_status == 'template' ) {
		return $states + array( esc_html( __('Template', 'synchronised-pages') ) );
	}
	
	// Display the word 'Synchronised' in every posts list when a post has at least one term in the synchronised_pages taxonomy
	if ( has_term( '', 'synchronised_pages', $post->ID ) && get_option( 'synchronised-pages-setting-display-synchronised', true ) ) {
		// translators: A label in the posts list
		return $states + array( esc_html( __('Synchronised', 'synchronised-pages') ) );
	}
	
	return $states;
}


/**
 * Register the tool in the Tools menu
 *
 * @action admin_menu
 */
function synchronised_pages_register_tool_submenu() {
	add_management_page( esc_html( __('Synchronised Pages', 'synchronised-pages') ), esc_html( __('Synchronised Pages', 'synchronised-pages') ), 'manage_options', 'synchronised-pages', 'synchronised_pages_tool_page' );
	//add_submenu_page( 'tools.php', esc_html( __('Synchronised Pages', 'synchronised-pages') ), esc_html( __('Synchronised Pages', 'synchronised-pages') ), 'manage_options', 'edit-tags.php?taxonomy=synchronised_pages&post_type=any' );
}


/**
 * Load l10n
 *
 * @action plugins_loaded
 */
function synchronised_pages_load_plugin_textdomain() {
	load_plugin_textdomain( 'synchronised-pages', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

