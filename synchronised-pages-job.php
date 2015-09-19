<?php

/**
 * Process the CSV file
 */
function synchronised_pages_process_csv_file( $filename ) {
    
    $handle = fopen( $filename, 'r' );
    if( $handle === false ) return array( null, null, 'unknown file' ); // Exception
    
    // Read the first line
    $data = fgetcsv( $handle, 0, ',' );
    if( $data === false ) {
        fclose( $handle );
        return array( null, null, 'error reading column names' ); // Exception
    }
    $columns = $data;
    $nb_columns = count($data);
    foreach( $columns as $column ) {
        if( preg_match( '/%/', $column ) )
            return array( null, null, '% in column names' );; // Exception - variable-defining character
    }
    
    // Verify - read each line
    $data = array();
    $count = 0;
    while( ($row = fgetcsv( $handle, 0, ',' ) ) !== false ) {
        
        if( count($row) != $nb_columns ) {
            fclose( $handle );
            return array( null, null, 'not consistent number of columns accross lines' ); // Exception - there is not everywhere the same number of columns
        }
        $data[$count] = $row;
        $count++;
    }
    fclose( $handle );
    
    return array( $columns, $data, null );
}


/**
 * Create all synchronised pages
 */
function synchronised_pages_create_synchronised_pages( $filename, $template_id, $import_name ) {
    
    // First, open the template
    $template = get_post( $template_id );
    var_dump($template);
    if( $template->post_status != 'template' ) {
        echo 'erreur: pas un template<br />';
        return; // Exception about status
    }
    echo 'après lecture du template<br />';
    
    // Second, read CSV file
    list( $columns, $data, $error ) = synchronised_pages_process_csv_file( $filename );
    if( $error ) {
        return; // Exception during CSV reading
    }
    for( $i=0; $i<count($columns); $i++ ) {
        $columns[$i] = '/%' . $columns[$i] . '%/';
    }
    echo 'après lecture du CSV<br />';
    echo 'columns=';var_dump($columns);echo '<br />';
    echo 'data=';var_dump($data);echo '<br />';
    
    // Third, manage the categories, tags, and other taxonomies
    $template_terms = array();
    $taxonomies = get_taxonomies( array( 'objecttype' => array($template->post_type), 'public' => true ) );
    foreach( $taxonomies as $taxonomy ) {
        
        if( is_object_in_taxonomy( $template->post_type, $taxonomy->name ) ) {
            
            $template_terms[$taxonomy->name] = wp_get_object_terms( $template->ID, $taxonomy, array( 'fields' => 'names' ) );
        }
    }
    echo 'template_terms=';var_dump($template_terms);echo '<br />';
    
    // Forth, create the terms for the template and the import
    if( ! ($template_term_id = intval( get_term_by( 'slug', 'page'.$template->ID, 'synchronised_pages' )->term_id ) ) )
        $template_term_id = wp_insert_term( $template->post_title, 'synchronised_pages', array( 'slug' => 'page'.$template->ID ) )['term_id'];
    $import_term_id = intval( wp_insert_term( $import_name, 'synchronised_pages', array( 'parent' => $template_term_id /*, 'description' => ''*/ ) )['term_id'] );
    
    // Third, check consistency between the template and the column names
    //$template_text = apply_filters( 'the_content', $template->post_content );
    // nop for now
    
    // Operation - read each line
    for( $i=0; $i<count($data); $i++ ) {
        
        synchronised_pages_create_one_synchronised_page( $template, $columns, $data[$i], $template_terms, $import_term_id );
    }
}


/**
 * Create one synchronised page
 */
function synchronised_pages_create_one_synchronised_page( $template, $columns, $data, $template_terms, $import_term_id ) {
    
    // Debug
    echo "NEW POST<br />";
    
    // Search if there is already a post with this title
    $title = preg_replace( $columns, $data, preg_replace( '/^.*?\|/', '', $template->post_title ) );
    $args = array(
        'post_type'        => $template->post_type,
        'post_status'      => 'publish',
        'title'            => $title,
    );
    $post = get_posts( $args );
    if( count($post) > 1 ) {
        return; // Exception
    }
    $ID = null;
    if( count($post) == 1 ) $ID = $post[0]->ID;
    
    // Debug
    if( $ID ) echo "* update post $ID $title ({$template->post_title})<br />";
    else echo "* create post $title ({$template->post_title})<br />";
    
    // Manage the categories, post_tags, and other taxonomies
    $categories = array();
    $tags = array();
    $terms = array( 'synchronised_pages' => array($import_term_id) );
    if( $ID ) {
        $terms['synchronised_pages'] = array_merge( $terms['synchronised_pages'], wp_get_object_terms( $ID, 'synchronised_pages', array( 'fields' => 'ids' ) ) );
    }
    foreach( $template_terms as $taxonomy => $terms ) {
        
        $new_terms = array();
        
        foreach( $terms as $term_name ) {
            
            $term_name = preg_replace( $columns, $data, $term_name );
            if( !$term_name ) continue;
            $term_id = get_term_by( 'name', $term_name, $taxonomy );
            if( !$term_id ) {
                $term_id = wp_insert_term( $term_name, $taxonomy );
            }
            else $term_id = $term_id->term_id;
            
            if( $taxonomy == 'post_tag' ) $new_terms[] = $term_name;
            else $new_terms[] = $term_id;
        }
        
        if( $taxonomy == 'post_tag' ) $tags = $new_terms;
        else if( $taxonomy == 'category' ) $categories = $new_terms;
        else $terms[$taxonomy] = $new_terms;
    }
    
    // Insert or update the post
    $data = array(
        'ID' => $ID,
		'post_content' => preg_replace( $columns, $data, apply_filters( 'the_content', $template->post_content ) ),
		'post_type' => $template->post_type,
		'post_title' => $title,
		'post_excerpt' => preg_replace( $columns, $data, $template->post_excerpt ),
		'post_author' => preg_replace( $columns, $data, $template->post_author ),
		'post_status' => 'publish',
        'post_category' => $categories,
        'tags_input' => $tags,
        'tax_input' => $terms,
	);
    
    $post_ID = wp_insert_post( $data );
    
    if( is_wp_error( $post_ID ) ) {
		echo $post_ID->get_error_message();
		return;
	}
}

