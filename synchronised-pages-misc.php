<?php

/**
 * Register the new status (Template) and the taxonomy Synchronised pages
 */
function synchronised_pages_add_stati() {
    
    // Register a custom post status for the templates: these are privately-published posts in a final form (not drafts)
    // The main advandage over the Private core status is you have a tab on the top of the post list, to avoid mixing
    // really private posts and template posts. The drawback of such a custom status is that, when you deactivate the
    // extension, the templates are no more in the post list, although you can access them through their post id.
	register_post_status( 'template', array(
		'label'        => __( 'Template', 'synchronised-pages' ),
		'private'      => true,
		'label_count'  => _n_noop( 'Template <span class="count">(%s)</span>', 'Templates <span class="count">(%s)</span>', 'synchronised-pages' ),
	) );
    
    // Labels of the taxonomy below
	$labels = array(
		'name'              => __( 'Genres', 'synchronised-pages' ),
		'singular_name'     => __( 'Genre', 'synchronised-pages' ),
		'search_items'      => __( 'Search Genres', 'synchronised-pages' ),
		'all_items'         => __( 'All Genres', 'synchronised-pages' ),
		'parent_item'       => __( 'Parent Genre', 'synchronised-pages' ),
		'parent_item_colon' => __( 'Parent Genre:', 'synchronised-pages' ),
		'edit_item'         => __( 'Edit Genre', 'synchronised-pages' ),
		'update_item'       => __( 'Update Genre', 'synchronised-pages' ),
		'add_new_item'      => __( 'Add New Genre', 'synchronised-pages' ),
		'new_item_name'     => __( 'New Genre Name', 'synchronised-pages' ),
		'menu_name'         => __( 'Genre', 'synchronised-pages' ),
	);
	
    // Register the taxonomy where are grouped the imports of synchronised pages
    register_taxonomy( 'synchronised_pages', null, array(
   		'label'        => __( 'Synchronised pages', 'synchronised-pages' ),
    //	'labels'       => $labels,
    	'description'  => __( 'Sets of synchronised pages from a template page', 'synchronised-pages' ),
    	'hierarchical' => true,
    	'public'       => false,
        'show_ui'      => true,
    ) );
    
    // Get post types where we want synchronised pages
    $post_types = get_option( 'synchronised-pages-setting-post-types', null );
    if( $post_types ) $post_types = array_keys( (array) $post_types );
    else if( $post_types === null ) $post_types = array( 'page' );
    else $post_types = array();
    
   	// Register taxonomy for each requested post type
    foreach( $post_types as $post_type ) {
        
        register_taxonomy_for_object_type( 'synchronised_pages', $post_type );
    }
}
add_action( 'init', 'synchronised_pages_add_stati' );


function synchronised_pages_add_admin() {
    
    register_setting( 'writing', 'synchronised-pages-setting-post-types', 'synchronised_pages_validate_settings_post_types' );
    
    register_setting( 'writing', 'synchronised-pages-setting-display-synchronised', 'boolval' );
    
    add_settings_section(
        'synchronised-pages',
        __('Synchronised Pages', 'synchronised-pages'),
        'synchronised_pages_settings',
        'writing'
    );
    
    add_settings_field(
		'synchronised-pages-post-types',
		__('Post Types', 'synchronised-pages'),
		'synchronised_pages_settings_post_types',
		'writing',
		'synchronised-pages'
	);
    
    add_settings_field(
		'synchronised-pages-display-synchronised',
		__('Synchronised status', 'synchronised-pages'),
		'synchronised_pages_settings_display_synchronised',
		'writing',
		'synchronised-pages'
	);
}
add_action( 'admin_init', 'synchronised_pages_add_admin' );

function synchronised_pages_settings( $arg ) {
    
    echo esc_html__('Selected post types will be given the possibility to be mass-generated. Relevant taxonomies will be created.', 'synchronised-pages');
}

function synchronised_pages_settings_post_types() {
    
    $post_types = get_post_types( array( 'public' => true ), 'objects' );
    $post_types_setting = get_option( 'synchronised-pages-setting-post-types', null );
    if( $post_types_setting ) $post_types_setting = array_keys( (array) $post_types_setting );
    else if( $post_types_setting === null ) $post_types_setting = array( 'page' );
    else $post_types_setting = array();
    
    $first = true;
    foreach( $post_types as $post_type ) {
        
        if( $post_type->name == 'attachment' ) continue; // This type is too complicated for now as it must be handled differently
        if( !$first ) echo '<br />';
        echo '<input name="synchronised-pages-setting-post-types['.$post_type->name.']" id="synchronised-pages-checkbox-'.$post_type->name.'" type="checkbox" class="code" value=""'.checked( in_array($post_type->name, $post_types_setting), true, false ).' /> <label for="synchronised-pages-checkbox-'.$post_type->name.'">'.esc_html($post_type->label).'</label>';
        $first = false;
    }
}

function synchronised_pages_validate_settings_post_types( $input ) {
    
    $post_types = get_post_types( array( 'public' => true ) );
    $post_types_setting = array_keys( (array) $input );
    
    foreach( $post_types_setting as $post_type ) {
        
        if( ! in_array( $post_type, $post_types ) ) {
            
            add_settings_error( 'synchronised-pages-setting-post-types', 'invalid-post-types', sprintf( esc_html__('Some of the values you entered in %s → %s are not post types.', 'synchronised-pages' ), '<i>'.__('Synchronised Pages', 'synchronised-pages').'</i>', '<i>'.__('Post Types', 'synchronised-pages').'</i>' ) );
            return get_option( 'synchronised-pages-setting-post-types', '' );
        }
        
        if( $post_type == 'attachment' ) {
            
            add_settings_error( 'synchronised-pages-setting-post-types', 'unavailable-post-type', sprintf( esc_html__('For now, it is not possible to use the post type %s in %s → %s. Possibly in a future version of the plugin %s.', 'synchronised-pages'), '<i>'.get_post_type_object('attachment')->label.'</i>', '<i>'.__('Synchronised Pages', 'synchronised-pages').'</i>', '<i>'.__('Post Types', 'synchronised-pages').'</i>', '<i>'.__('Synchronised Pages', 'synchronised-pages').'</i>' ) );
            return get_option( 'synchronised-pages-setting-post-types', '' );
        }
    }
    
    return $input;
}

function synchronised_pages_settings_display_synchronised() {
    
    $display_synchronised_setting = get_option( 'synchronised-pages-setting-display-synchronised', true );
    
    echo '<input name="synchronised-pages-setting-display-synchronised" id="synchronised-pages-setting-display-synchronised" type="checkbox" class="code" value="1"'.checked( $display_synchronised_setting, true, false ).' /> <label for="synchronised-pages-setting-display-synchronised">'.__('Display the status &#8220;Synchronised&#8221; in the post list', 'synchronised-pages').'</label>';
}


/**
 * Append the status Template in the UI
 *
 * This part will be mostly cut when #12706 will be solved
 */
function synchronised_pages_append_status() {
     global $post;
     $complete_template = '';
     $complete_synchronised = '';
     $label = '';
     
     $post_types = get_option( 'synchronised-pages-setting-post-types', null );
     if( $post_types ) $post_types = array_keys( (array) $post_types );
     else if( $post_types_setting === null ) $post_types_setting = array( 'page' );
     else $post_types = array();
     
     if( in_array( $post->post_type, $post_types ) || $post->post_status == 'template' ) {
          if( $post->post_status == 'template' ) {
              $complete_template = ' selected="selected"';
              $label = ' <span id="post-status-display">'.esc_js(esc_html__( 'Template', 'synchronised-pages' )).'</span>';
          }
          echo '
          <script>
          jQuery(document).ready(function($){
               
               // Add the status in the stati available for the user
               $(\'select#post_status\').append(\'<option value="template"'.$complete_template.'>'.esc_js(esc_html__( 'Template', 'synchronised-pages' )).'</option>\');
               $(\'.misc-pub-post-status label\').append(\''.$label.'\');
               
               // Save some original values – intentionally global variables
               synchronised_pages_original_publish = $(\'#publish\').attr(\'name\');
               synchronised_pages_original_publish_val = $(\'#publish\').val();
               synchronised_pages_original_status = $(\'option:selected\', $(\'#post_status\')).val();
               
               var synchronised_pages_manage_status = function() {
                   
                   if( $(\'option:selected\', $(\'#post_status\')).val() == \'template\' ) {
                   
                       // Hide the \'Save draft\' button and change the \'Publish\' button to save
                       $(\'#save-post\').hide();
                       $(\'#publish\').attr(\'name\', \'save\');
                       $(\'#publish\').val(synchronised_pages_original_status==\'template\'?\''.esc_js(esc_html__('Update the template', 'synchronised-pages')).'\':\''.esc_js(esc_html__('Publish the template', 'synchronised-pages')).'\');
                       
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
}
add_action( 'admin_footer-post.php', 'synchronised_pages_append_status' );
add_action( 'admin_footer-post-new.php', 'synchronised_pages_append_status' );


/**
 * Display the state Template or Synchronised in the summary
 */
function synchronised_pages_display_state( $states ) {
    
    global $post;
    $arg = get_query_var( 'post_status' );
    
    if( $arg != 'template' ) {
        if( $post->post_status == 'template' ) {
            return array( esc_html__('Template', 'synchronised-pages') );
        }
    }
    if( has_term( '', 'synchronised_pages', $post->ID ) && get_option( 'synchronised-pages-setting-display-synchronised', true ) ) {
        return array( esc_html__('Synchronised', 'synchronised-pages') );
    }
    return $states;
}
add_filter( 'display_post_states', 'synchronised_pages_display_state' );


/**
 * Register the tool in the Tools menu
 */
function synchronised_pages_register_tool_submenu() {
	add_management_page( esc_html__('Synchronised Pages', 'synchronised-pages'), esc_html__('Synchronised Pages', 'synchronised-pages'), 'manage_options', 'synchronised-pages', 'synchronised_pages_tool_page' );
    //add_submenu_page( 'tools.php', esc_html__('Synchronised Pages', 'synchronised-pages'), esc_html__('Synchronised Pages', 'synchronised-pages'), 'manage_options', 'edit-tags.php?taxonomy=synchronised_pages&post_type=any' );
}
add_action( 'admin_menu', 'synchronised_pages_register_tool_submenu' );


/**
 * Load l10n
 */
function synchronised_pages_load_plugin_textdomain() {
    load_plugin_textdomain( 'synchronised-pages', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'synchronised_pages_load_plugin_textdomain' );

