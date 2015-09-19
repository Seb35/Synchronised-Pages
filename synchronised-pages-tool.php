<?php

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
    	    'post_type'        => $post_type,
    	    'post_status'      => 'template',
    	    'orderby'          => 'date',
    	    'order'            => 'DESC',
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
	echo '<h1>'.esc_html( $tax->labels->name ).'</h1>';
	echo '<br class="clear" />';
	echo '<div id="col-container">';
	
	echo '<div id="col-right">';
	echo '<div class="col-wrap">';
	//echo '<form id="posts-filter" method="post">';
    //echo '<input type="hidden" name="taxonomy" value="synchronised_pages" />';
    //$wp_list_table->display();
    //echo '<br class="clear" /></form>';
    echo __('Manage previous imports for these post types:', 'synchronised-pages').' ';
    $first = true;
    foreach( $post_types as $post_type ) {
        if( !$first ) echo ', ';
        echo '<a href="'.get_admin_url().'edit-tags.php?taxonomy=synchronised_pages'.($post_type!='post'?'&post_type='.$post_type:'').'">'.get_post_type_object($post_type)->labels->singular_name.'</a>';
        $first = false;
    }
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
	echo '<label for="import-name">'.esc_html__( 'Name of the import', 'synchronised-pages' ).'</label>';
	echo '<input name="import_name" id="import-name" type="text" value="" size="40" aria-required="true" /><br />';
	echo '<p>'.esc_html__('Internal name of the import.', 'synchronised-pages').'</p>';
	echo '</div>';
	echo '<div class="form-field term-slug-wrap">';
	echo '<label for="'.(count($post_types)>1?'synchronised-pages-post_type':'synchronised_pages_post_template').'">'.esc_html__( 'Template', 'synchronised-pages' ).'</label>';
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
	echo '<p>'.esc_html__('All the synchronised pages will be created after the selected template.', 'synchronised-pages').'</p>';
	echo '</div>';
	echo '<div class="form-field term-slug-wrap">';
	echo '<label for="synchronised-pages-csvfile">'.esc_html__( 'Database file', 'synchronised-pages' ).'</label>';
	echo '<input type="file" name="csvfile" id="synchronised-pages-csvfile" />';
	echo '<p>'.esc_html__('This file must be in CSV format. The column names will be the variable names in the template and each synchronised pages will be created given the informations of one line.', 'synchronised-pages').'</p>';
	echo '</div>';
    //echo '<input type="hidden" name="page" value="synchronised-pages" />';
    echo '<input type="hidden" name="action" value="synchronised_pages_import" />';
    echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="'.__('Create the synchronised pages', 'synchronised-pages').'" /></p>';
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


function synchronised_pages_columns($theme_columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        //'header_icon' => '',
        'description' => __('Description'),
        'slug' => __('Slug'),
        'posts' => __('Posts')
        );
    return $new_columns;
}
//add_filter('manage_edit-synchronised_pages_columns', 'synchronised_pages_columns'); 


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
add_action( 'admin_post_synchronised_pages_import', 'synchronised_pages_import' );













