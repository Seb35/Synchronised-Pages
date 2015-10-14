<?php
/**
 * @package Synchronised Pages
 * @version 0.0.1
 */
/*
Plugin Name: Synchronised Pages
Plugin URI: http://wordpress.org/plugins/synchronised-pages/
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: Sébastien Beyou
Version: 0.1
Author URI: https://www.seb35.fr
*/

/**
 * Register the new stati (Template, Synchronised) and the taxonomies
 */
function synchronised_pages_add_stati() {
	register_post_status( 'template', array(
		'label'        => __( 'Template', 'post', 'synchronised-pages' ),
		'private'      => true,
		'label_count'  => _n_noop( 'Template <span class="count">(%s)</span>', 'Templates <span class="count">(%s)</span>', 'synchronised-pages' ),
	) );
	    
	register_post_status( 'synchronised', array(
		'label'        => __( 'Synchronised', 'synchronised-pages' ),
		'public'       => true,
		'label_count'  => _n_noop( 'Synchronised <span class="count">(%s)</span>', 'Synchronised <span class="count">(%s)</span>', 'synchronised-pages' ),
	) );
	
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
	
	register_taxonomy( 'synchronised_pages', 'page', array(
		'label'        => __( 'Synchronised pages', 'synchronised-pages' ),
		//'labels'       => $labels,
		'description'  => __( 'Sets of synchronised pages from a template page', 'synchronised-pages' ),
		'hierarchical' => true,
		'public'       => true,
	) );
}
add_action( 'init', 'synchronised_pages_add_stati' );


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
     if( $post->post_type == 'page' ) {
          if( $post->post_status == 'template' ) {
              $complete_template = ' selected="selected"';
              $label = ' <span id="post-status-display">'.__( 'Template', 'synchronised-pages' ).'</span>';
          }
          elseif( $post->post_status == 'synchronised' ) {
              $complete_synchronised = ' selected="selected"';
              $label = ' <span id="post-status-display">'.__( 'Synchronised', 'synchronised-pages' ).'</span>';
          }
          echo '
          <script>
          jQuery(document).ready(function($){
               
               // Add the status in the stati available for the user
               $(\'select#post_status\').append(\'<option value="template"'.$complete_template.'>'.__( 'Template', 'synchronised-pages' ).'</option>\');
               $(\'select#post_status\').append(\'<option value="synchronised"'.$complete_synchronised.'>'.__( 'Synchronised', 'synchronised-pages' ).'</option>\');
               $(\'.misc-pub-post-status label\').append(\''.$label.'\');
               
               // Save some original values – intentionally global variables
               synchronised_pages_original_publish = $(\'#publish\').attr(\'name\');
               synchronised_pages_original_publish_val = $(\'#publish\').val();
               synchronised_pages_original_status = $(\'option:selected\', $(\'#post_status\')).val();
               synchronised_pages_original_visibility = false;
               
               var synchronised_pages_manage_status = function() {
                   
                   if( $(\'option:selected\', $(\'#post_status\')).val() == \'template\' ) {
                   
                       // Hide the \'Save draft\' button and change the \'Publish\' button to save
                       $(\'#save-post\').hide();
                       $(\'#publish\').attr(\'name\', \'save\');
                       $(\'#publish\').val(synchronised_pages_original_status==\'template\'?\''.__('Update the template', 'synchronised-pages').'\':\''.__('Publish the template', 'synchronised-pages').'\');
                       
                       // Don’t let the choice about the visibility
                       if( !synchronised_pages_original_visibility ) {
                           synchronised_pages_original_visibility = $(\'post-visibility-select\').find(\'input:radio:checked\').val();
                           $(\'#visibility-radio-public\').prop(\'checked\', true);
                       }
                       $(\'#post-visibility-display\').html( postL10n[ \'private\' ] );
                       $(\'.misc-pub-visibility a.edit-visibility\').hide();
                   } else {
                       
                       // Restore the original \'Publish\' button, either save or publish depending on the current state – WP manages the \'Save\' button
                       if( synchronised_pages_original_publish ) {
                           $(\'#publish\').attr(\'name\', synchronised_pages_original_publish);
                           $(\'#publish\').val(synchronised_pages_original_publish_val);
                       }
                       
                       // Restore the original visibility
                       if( synchronised_pages_original_visibility ) {
                           $(\'#visibility-radio-\'+synchronised_pages_original_visibility).prop(\'checked\', true);
                       }
                       synchronised_pages_original_visibility = false;
                       $(\'#post-visibility-display\').html( postL10n[ $(\'#post-visibility-select\').find(\'input:radio:checked\').val() + ($(\'#sticky\').prop(\'checked\')?\'Sticky\':\'\') ] );
                       
                       // Restore the choice about the visibility
                       $(\'.misc-pub-visibility a.edit-visibility\').show();
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
    if( $arg != 'template' ){
        if( $post->post_status == 'template' ) {
            return array(__('Template', 'synchronised-pages'));
        }
    }
    if( $arg != 'synchronised' ) {
        if( $post->post_status == 'synchronised' ) {
            return array(__('Synchronised', 'synchronised-pages'));
        }
    }
    return $states;
}
add_filter( 'display_post_states', 'synchronised_pages_display_state' );


/**
 * Register the tool in the Tools menu
 */
function synchronised_pages_register_tool_submenu() {
	add_management_page( __('Synchronised Pages', 'synchronised-pages'), __('Synchronised Pages', 'synchronised-pages'), 'manage_options', 'synchronised-pages', 'synchronised_pages_tool_page' );
}
add_action( 'admin_menu', 'synchronised_pages_register_tool_submenu' );

function synchronised_pages_tool_page() {
	
    if( isset($_POST['csvfile']) ) {
        $tag_name = $_POST['tag-name'];
        $csvfile = $_POST['csvfile'];
        $post_template = $_POST['post_template'];
        $tag_name = $_POST['tag_name'];
        synchronised_pages_process_csv_file( $post_type, $csvfile, $post_template, $tag_name );
    }
    
    $tax = get_taxonomy( 'synchronised_pages' );
	
	echo '<div class="wrap">';
	echo '<h1>'.esc_html( $tax->labels->name ).'</h1>';
	echo '<br class="clear" />';
	echo '<div id="col-container">';
	
	echo '<div id="col-right">';
	echo '<div class="col-wrap">';
	echo 'aa';
	echo '</div>';
	echo '</div>';
	
	echo '<div id="col-left">';
	echo '<div class="col-wrap">';
	echo '<div class="form-wrap">';
	echo '<form id="addtag" method="post" action="edit-tags.php" class="validate">';
	echo '<div class="form-field form-required term-name-wrap">';
	echo '<label for="tag-name">'.__( 'Name of the import', 'synchronised-pages' ).'</label>';
	echo '<input name="tag-name" id="tag-name" type="text" value="" size="40" aria-required="true" /><br />';
	echo '<p>'.__('Internal name of the import').'</p>';
	echo '</div>';
	echo '<div class="form-field term-slug-wrap">';
	echo '<label>'.__( 'Template', 'synchronised-pages' ).'</label>';
	echo '<select name="post_type"><option value="post">Post</option><option value="page" selected="selected">Page</option></select>';
	echo '<select name="post_template"><option>Modèle de page</option></select>';
	echo '<p>'.__('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.').'</p>';
	echo '</div>';
	echo '<div class="form-field term-slug-wrap">';
	echo '<label>'.__( 'Database file', 'synchronised-pages' ).'</label>';
	echo '<input type="file" name="csvfile" />';
	echo '<p>'.__('This file must be in CSV format. The column names will be the variable names in the template.').'</p>';
	echo '</div>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	
	echo '</div>';
	echo '</div>';
}


/**
 * Process the CSV file
 */
function synchronised_pages_process_csv_file( $post_type, $csvfile, $post_template, $tag_name ) {
    
    if (($handle = fopen($filename, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        for ($c=0; $c < $num; $c++) {
            echo $data[$c] . "<br />\n";
        }
    }
    fclose($handle);
}
}


/**
 * Create all synchronised pages
 */
function synchronised_pages_create_synchronised_pages() {
    
}


/**
 * Create one synchronised page
 */
function synchronised_pages_create_one_synchronised_page() {
    
    $data = array(
		'post_content' => $this->post_format(trim($row['soustitre']."\n".$row['chapo']."\n".$row['texte'])),
		'post_type' => 'post',
		'post_title' => $row['titre'],
		'post_excerpt' => $row['descriptif'],
		'post_author' => ( $row['id_auteur'] && isset($this->users[$row['id_auteur']]) ? $this->users[$row['id_auteur']] : '1' ),
		'post_date' => $row[19], // there are two 'dates' columns, in spip_articles and spip_urls, and they could be different
		'post_date_gmt' => $row[19],
		'post_category' => $ncat,
		'post_status' => 'publish',
        'post_name' => $row['url']
	);
    $ID = wp_insert_post($data);
	if( is_wp_error( $ID ) ) {
		echo $ID->get_error_message();
		return;
	}
}


/**
 * Load l10n
 */
function synchronised_pages_load_plugin_textdomain() {
    load_plugin_textdomain( 'synchronised-pages', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'synchronised_pages_load_plugin_textdomain' );

