<?php
/**
 * @package Synchronised Pages
 * @version 0.1.2
 * @license WFTPL 2.0
 */


/**
 * Displays the tool
 */
function synchronised_pages_tool_page() {
	
	/*if( isset($_FILE['csvfile']) || isset($_POST['import_name']) ) {
		$import_name = sanitize_text_field( strval( $_POST['import_name'] ) );
		$filename = strval($_FILE['csvfile']['tmp_name'] );
		$post_template = intval($_POST['post_template']);
		echo 'enter in processing<br />';
		synchronised_pages_create_synchronised_pages( $filename, $template_id, $import_name );
	}
	else echo 'not enter in processing<br />';*/
	
	$tax = get_taxonomy( 'synchronised_pages' );
	
	// Get post types
	$post_types = get_option( 'synchronised-pages-setting-post-types', null );
	if( $post_types ) $post_types = array_keys( (array) $post_types );
	else if( $post_types === null ) $post_types = array( 'page' );
	else $post_types = array();
	if( count( $post_types ) == 0 ) { echo 'nada post type.'; }
	
	// Get post templates
	$post_templates = array();
	$post_default_template = array();
	foreach( $post_types as $post_type ) {
		
		$args = array(
			'post_type'		=> $post_type,
			'post_status'	  => 'template',
			'orderby'		  => 'date',
			'order'			=> 'DESC',
		);  
		$posts_array = get_posts( $args );
		
		$post_templates[$post_type] = $posts_array;
	}
	/*
	require_once( ABSPATH . 'wp-admin/admin-header.php' );

if ( ! current_user_can( $tax->cap->edit_terms ) ) {
	wp_die(
		'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
		'<p>' . __( 'You are not allowed to edit this item.' ) . '</p>',
		403
	);
}
	$synchronised_pages_taxonomy_screen = WP_Screen::get('edit-tags');
	$synchronised_pages_taxonomy_screen->id = 'edit-synchronised_pages';
	$synchronised_pages_taxonomy_screen->post_type = 'page';
	$synchronised_pages_taxonomy_screen->taxonomy = 'synchronised_pages';
	
	//var_dump(get_current_screen());echo '<br />';
	//var_dump($synchronised_pages_taxonomy_screen);echo '<br />';
	//add_query_arg( 'taxonomy', 'synchronised_pages' );
	//add_query_arg( 'post_type', 'page' );
	$wp_list_table = _get_list_table('WP_Terms_List_Table', array( 'screen' => 'synchronised_pages' ) );
	//$pagenum = $wp_list_table->get_pagenum();
	$wp_list_table->prepare_items();
	//$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );*/
	
	echo '<div class="wrap">';
	echo '<h1>'.esc_html( $tax->labels->name );
	//echo '<br class="clear" />';
	// translators: Link
	echo ' <a href="'.get_admin_url().'edit-tags.php?taxonomy=synchronised_pages" class="page-title-action">'.esc_html( __( 'Manage imports', 'synchronised-pages' ) ).'</a>';
	echo '</h1>';
	echo '<br class="clear" />';
	echo '<div id="col-container">';
	
	echo '<div id="col-right">';
	echo '<div class="col-wrap">';
	//echo '<form id="posts-filter" method="post">';
	//echo '<input type="hidden" name="taxonomy" value="synchronised_pages" />';
	//$wp_list_table->display();
	//echo '<br class="clear" /></form>';
	//$links = '';
	//foreach ( $post_types as $post_type ) {
	//	if ( $links ) $links .= ', ';
	//	$links .= '<a href="'.get_admin_url().'edit-tags.php?taxonomy=synchronised_pages'.($post_type!='post'?'&post_type='.$post_type:'').'">'.get_post_type_object($post_type)->labels->singular_name.'</a>';
	//}
	// translators: Description of link(s); %s are the link(s) to post types
	//echo sprintf( esc_html( _n( 'Manage previous imports for this post type: %s.', 'Manage previous imports for these post types: %s.', count($post_types), 'synchronised-pages' ) ), $links);
	echo '';
	echo '';
	echo '';
	echo '';
	echo '';
	echo '';
	echo '';
	echo '';
	echo '</div>';
	echo '</div>';
	
	echo '<div id="col-left">';
	echo '<div class="col-wrap">';
	echo '<div class="form-wrap">';
	echo '<form id="synchronised-pages-form" enctype="multipart/form-data" method="post" action="'.get_admin_url().'admin-post.php" class="validate">';
	echo '<div class="form-field form-required term-name-wrap">';
	// translators: Title of a text field in a form
	echo '<label for="import-name">'.esc_html( __( 'Name of the import', 'synchronised-pages') ).'</label>';
	echo '<input name="import_name" id="import-name" type="text" value="" size="40" aria-required="true" /><br />';
	// translators: Description of the text field
	echo '<p>'.esc_html( __('Internal name of the import.', 'synchronised-pages') ).'</p>';
	echo '</div>';
	echo '<div class="form-field term-slug-wrap">';
	echo '<label for="'.(count($post_types)>1?'synchronised-pages-post_type':'synchronised_pages_post_template').'">'.esc_html( __( 'Template', 'synchronised-pages') ).'</label>';
	if( count($post_types) > 1 ) {
		echo '<select name="post_type" id="synchronised-pages-post_type" class="hide-if-no-js">';
		foreach( $post_types as $post_type ) {
			echo '<option value="'.$post_type.'">'.get_post_type_object($post_type)->labels->singular_name.'</option>';
		}
		echo '</select>';
	}
	echo '<select name="post_template" id="synchronised_pages_post_template">';
	foreach( $post_types as $post_type ) {
		if( count($post_types) > 1 ) echo '<option value="" disabled="disabled">'.get_post_type_object($post_type)->labels->singular_name.'</option>';
		foreach( $post_templates[$post_type] as $post ) {
			if( !isset($post_default_template[$post_type]) ) $post_default_template[$post_type] = $post->ID;
			echo '<option class="post-'.$post_type.'" value="'.$post->ID.'">'.$post->post_title.'</option>';
		}
	}
	echo '</select>';
	// translators: Description of an input in a form
	echo '<p>'.esc_html( __('All the synchronised pages will be created after the selected template.', 'synchronised-pages') ).'</p>';
	echo '</div>';
	echo '<div class="form-field term-slug-wrap">';
	// translators: Title of an upload field in a form
	echo '<label for="synchronised-pages-csvfile">'.esc_html( __( 'Database file', 'synchronised-pages') ).'</label>';
	echo '<input type="file" name="csvfile" id="synchronised-pages-csvfile" />';
	// translators: Description of the upload field
	echo '<p>'.esc_html( __('This file must be in CSV format. The column names will be the variable names in the template and each synchronised pages will be created given the informations of one line.', 'synchronised-pages') ).'</p>';
	echo '</div>';
	//echo '<input type="hidden" name="page" value="synchronised-pages" />';
	echo '<input type="hidden" name="action" value="synchronised_pages_import" />';
	// translators: A button to submit the form
	echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="'.esc_html( __('Create the synchronised pages', 'synchronised-pages') ).'" /></p>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	
	echo '</div>';
	echo '</div>';
	
	if( count($post_types) > 1 ) {
		
		$default_post = '';
		foreach( $post_default_template as $k => $v ) $default_post .= '\''.$k.'\':'.$v.',';
		
		echo '<script type="text/javascript">jQuery(document).ready( function($) {
			
			var default_post = {'.substr($default_post,0,-1).'};
			
			$(\'#synchronised_pages_post_template option\').hide();
			$(\'#synchronised_pages_post_template option.post-\'+$(\'#synchronised-pages-post_type\').val()).show();
			$(\'#synchronised_pages_post_template\').val(default_post[$(\'#synchronised-pages-post_type\').val()]);
			
			$(\'#synchronised-pages-post_type\').change( function() {
				
				$(\'#synchronised_pages_post_template option\').hide();
				$(\'#synchronised_pages_post_template option.post-\'+$(\'#synchronised-pages-post_type\').val()).show();
				$(\'#synchronised_pages_post_template\').val(default_post[$(\'#synchronised-pages-post_type\').val()]);
				
			});
			
		});</script>';
	}
}


function synchronised_pages_sortable_columns($theme_columns) {
	
	return array(
		'name' => 'name',
		'post_type' => 'post_type',
		'total' => 'count',
	);
}
add_filter('manage_edit-synchronised_pages_sortable_columns', 'synchronised_pages_sortable_columns'); 

function synchronised_pages_columns($theme_columns) {
	
	return array(
		'cb' => '<input id="cb-select-all-1" type="checkbox" />',
		'name' => __('Name'),
		'post_type' => __('Post Types', 'synchronised-pages'),
		'total' => __('Total'),
	);
}
add_filter('manage_edit-synchronised_pages_columns', 'synchronised_pages_columns'); 

// Add to admin_init function   
 
function synchronised_pages_rows( $junk, $column_name, $term_id ) {
	
	global $wpdb;
	
	// Get registered post types
	$post_types = get_option( 'synchronised-pages-setting-post-types', null );
	if( $post_types ) $post_types = array_keys( (array) $post_types );
	else if( $post_types === null ) $post_types = array( 'page' );
	else $post_types = array();
	
	$term = get_term( $term_id, 'synchronised_pages' );
	
	$post_type = null;
	$template = false;
	$capture = null;
	// If we are in a line of a post template
	if ( preg_match( '/^('.implode('|',$post_types).')/', $term->slug, $capture ) ) {
		$post_type = $capture[1];
		$template = true;
	}
	// If we are in a line of an import
	else {
		$parent_term = get_term( $term->parent, 'synchronised_pages' );
		if ( preg_match( '/^('.implode('|',$post_types).')/', $parent_term->slug, $capture ) ) {
			$post_type = $capture[1];
		}
		else {
			return; // Exception
		}
	}
	
	if ( $column_name == 'post_type' ) {
		
		// translators: Display the 'Templace' status of a page
		if( $template ) return sprintf( esc_html( __('%s (template)', 'synchronised-pages') ), get_post_type_object( $post_type )->labels->singular_name );
		return get_post_type_object( $post_type )->labels->name;
	}
	
	if ( $column_name == 'total' ) {
		
		$count_term = intval( $wpdb->get_col('SELECT COUNT(*) FROM '.$wpdb->term_relationships.' AS tr INNER JOIN '.$wpdb->term_taxonomy.' AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN '.$wpdb->posts.' AS p ON tr.object_id = p.ID WHERE tt.taxonomy = \'synchronised_pages\' AND tt.term_id = \''.intval($term_id).'\' AND p.post_type = \''.$post_type.'\'')[0] );
		
		if ( $template ) {
			
			$term_ids = get_term_children( $term_id, 'synchronised_pages' );
			$term_ids = '(\'' . implode( '\',\'', $term_ids ) . '\')';
			$count_terms = count( $wpdb->get_col('SELECT DISTINCT tr.object_id FROM '.$wpdb->term_relationships.' AS tr INNER JOIN '.$wpdb->term_taxonomy.' AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN '.$wpdb->posts.' AS p ON tr.object_id = p.ID WHERE tt.taxonomy = \'synchronised_pages\' AND tt.term_id IN '.$term_ids.' AND p.post_type = \''.$post_type.'\'') );
			
			// translators: Display two numbers
			return '<a href="'.get_admin_url().'edit.php?synchronised_pages='.$term->slug.($post_type!='post'?'&post_type='.$post_type:'').'">'.esc_html( sprintf( __('%s + %s', 'synchronised-pages'), number_format_i18n($count_term), number_format_i18n($count_terms) ) ).'</a>';
		}
		else {
			
			return '<a href="'.get_admin_url().'edit.php?synchronised_pages='.$term->slug.($post_type!='post'?'&post_type='.$post_type:'').'">'.number_format_i18n($count_term).'</a>';
		}
	}
}
add_filter( 'manage_synchronised_pages_custom_column', 'synchronised_pages_rows', 10, 3 );


/**
 * Catch the submit action after click on “Import”
 *
 * @action admin_post_synchronised_pages_import
 */
function synchronised_pages_import() {
	
	$import_name = sanitize_text_field( strval( $_POST['import_name'] ) );
	$filename = strval($_FILES['csvfile']['tmp_name'] );
	$post_template = intval($_POST['post_template']);
	echo 'enter in processing<br />';
	echo 'import_name='.$import_name.'<br />';
	echo 'filename='.$filename.'<br />';
	echo 'post_template='.$post_template.'<br />';
	var_dump($_FILES);echo '<br />';
	synchronised_pages_create_synchronised_pages( $filename, $post_template, $import_name );
	echo 'success import<br />';
}

