<?php

add_settings_section( 'synchronised-pages', 'Synchronised pages', 'synchronised_pages_settings', 'writing' );

function synchronised_pages_settings( $arg ) {
    // echo section intro text here
    echo 'ahahah';
	//echo '<p>id: ' . $arg['id'] . '</p>';             // id: eg_setting_section
	//echo '<p>title: ' . $arg['title'] . '</p>';       // title: Example settings section in reading
	//echo '<p>callback: ' . $arg['callback'] . '</p>'; // callback: eg_setting_section_callback_function
}


/*class Synchronised_pages {
    
    
    public $post_types = array();
    
    
}*/
