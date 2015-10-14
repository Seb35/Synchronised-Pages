<?php
/**
 * @package Synchronised Pages
 * @license WFTPL 2.0
 */
/*
Plugin Name: Synchronised Pages
Plugin URI: http://wordpress.org/plugins/synchronised-pages/
Description: This plugin generates a bunch of pages according to a template page with various informations extracted from a database. For instance you can mass-generate one page per event of a festival, with specific informations for each event.
Author: Sébastien Beyou
Version: 0.2.2
Author URI: https://www.seb35.fr
*/





/**
 * Add subpages by functional theme
 */
require_once "synchronised-pages-job.php";
require_once "synchronised-pages-misc.php";
require_once "synchronised-pages-tool.php";
require_once "synchronised-pages-settings.php";





/**
 * Register actions and filters
 */

/// Functions defined in synchronised-pages-misc.php

// Register the new status (Template) and the taxonomy Synchronised pages
add_action( 'init', 'synchronised_pages_add_stati' );

// Append the status Template in the UI
// TODO Add the same for Quick Edit
add_action( 'admin_footer-post.php', 'synchronised_pages_append_status' );
add_action( 'admin_footer-post-new.php', 'synchronised_pages_append_status' );

// Remove the possibility to create a tag in the taxonomy synchronised_pages in the user interface
add_action( 'admin_print_scripts-edit-tags.php', 'synchronised_pages_remove_tag_creation' );

// Remove the possibility to assign a tag of the taxonomy synchronised_pages in the edit interface
add_action( 'admin_print_scripts-post.php', 'synchronised_pages_remove_tag_assign' );
add_action( 'admin_print_scripts-post-new.php', 'synchronised_pages_remove_tag_assign' );

add_action( 'admin_footer-edit-tags.php', 'synchronised_pages_remove_term_link' );

// Remove the possibility to edit a term of the taxonomy synchronised_pages
add_filter( 'synchronised_pages_row_actions', 'synchronised_pages_remove_quick_term_edition', 10, 1 );

// Display the state Template or Synchronised in the posts list
add_filter( 'display_post_states', 'synchronised_pages_display_state' );

// Register the tool in the Tools menu
add_action( 'admin_menu', 'synchronised_pages_register_tool_submenu' );

// Load l10n
add_action( 'plugins_loaded', 'synchronised_pages_load_plugin_textdomain' );


/// Functions defined in synchronised-pages-settings.php

// Register the settings and admin forms
add_action( 'admin_init', 'synchronised_pages_add_admin' );


/// Functions defined in synchronised-pages-tool.php

// Catch the submit action after click on “Import”
add_action( 'admin_post_synchronised_pages_import', 'synchronised_pages_import' );

