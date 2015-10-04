<?php
/**
 * @package Synchronised Pages
 * @license WFTPL 2.0
 */


/**
 * Register the settings and admin forms
 *
 * @action admin_init
 */
function synchronised_pages_add_admin() {
	
	/**
	 * Settings
	 *
	 * The two settings are defined in the “Writing” section.
	 * The default values are explicitely defined each time the setting is read.
	 */
	
	// Post types where when we synchronised pages
	// Type: key-value array, but only the keys contain the information (the string defining a post type)
	// Default: array( 'page' )
	register_setting( 'writing', 'synchronised-pages-setting-post-types', 'synchronised_pages_validate_settings_post_types' );
	
	// Display or not the word “Synchronised” next to the post in the posts list for synchronised pages
	// Type: boolean
	// Default: true
	register_setting( 'writing', 'synchronised-pages-setting-display-synchronised', 'boolval' );
	
	
	/**
	 * Add the section “Synchronised Pages” in the “Writing” settings page, and add these two settings
	 */
	
	add_settings_section(
		'synchronised-pages',
		__('Synchronised Pages', 'synchronised-pages'),
		'synchronised_pages_settings',
		'writing'
	);
	
	add_settings_field(
		'synchronised-pages-post-types',
		// translators: Title of a setting
		__('Post Types', 'synchronised-pages'),
		'synchronised_pages_settings_post_types',
		'writing',
		'synchronised-pages'
	);
	
	add_settings_field(
		'synchronised-pages-display-synchronised',
		// translators: Title of a setting
		__('Synchronised Status', 'synchronised-pages'),
		'synchronised_pages_settings_display_synchronised',
		'writing',
		'synchronised-pages'
	);
}

function synchronised_pages_settings( $arg ) {
	
	// translators: Description of a section in the settings
	echo esc_html( __('Selected post types will be given the possibility to be mass-generated. Relevant taxonomies will be created.', 'synchronised-pages') );
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
	
	foreach ( $post_types_setting as $post_type ) {
		
		if ( ! in_array( $post_type, $post_types ) ) {
			
			add_settings_error( 'synchronised-pages-setting-post-types', 'invalid-post-types',
				// translators: An error message; the "%s → %s" is the location of the setting in error
				sprintf( esc_html( __('Some of the values you entered in %s → %s are not post types.', 'synchronised-pages') ),
					'<i>'.esc_html( __('Synchronised Pages', 'synchronised-pages') ).'</i>',
					'<i>'.esc_html( __('Post Types', 'synchronised-pages') ).'</i>'
				)
			);
			return get_option( 'synchronised-pages-setting-post-types', array( 'page' ) );
		}
		
		if ( $post_type == 'attachment' ) {
			
			add_settings_error( 'synchronised-pages-setting-post-types', 'unavailable-post-type',
				// translators: An error message; the "%s → %s" is the location of the setting in error
				sprintf( esc_html( __('For now, it is not possible to use the post type %s in %s → %s. Possibly in a future version of the plugin %s.', 'synchronised-pages') ),
					'<i>'.get_post_type_object('attachment')->label.'</i>',
					'<i>'.esc_html( __('Synchronised Pages', 'synchronised-pages') ).'</i>',
					'<i>'.esc_html( __('Post Types', 'synchronised-pages') ).'</i>',
					'<i>'.esc_html( __('Synchronised Pages', 'synchronised-pages') ).'</i>'
				)
			);
			return get_option( 'synchronised-pages-setting-post-types', array( 'page' ) );
		}
	}
	
	return $input;
}


/**
 * 
 */
function synchronised_pages_settings_display_synchronised() {
	
	$display_synchronised_setting = get_option( 'synchronised-pages-setting-display-synchronised', true );
	
	// translators: Description of a setting
	echo '<input name="synchronised-pages-setting-display-synchronised" id="synchronised-pages-setting-display-synchronised" type="checkbox" class="code" value="1"'.checked( $display_synchronised_setting, true, false ).' /> <label for="synchronised-pages-setting-display-synchronised">'.__('Display the status &#8220;Synchronised&#8221; in the posts list', 'synchronised-pages').'</label>';
}

