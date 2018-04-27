<?php
/*
Plugin Name: 		Group Button Adder for BuddyPress
Plugin URI:  		https://github.com/jdmdigital/group-button-adder-buddypress
Description: 		WordPress plugin that adds a configurable link button to BuddyPress Group Headers
Version: 			1.0.0
Requires at least:	3.3
Tested up to: 		4.9.5
License: 			GPLv2 or later
License URI: 		http://www.gnu.org/licenses/gpl-2.0.html
Author: 			JDM Digital
Author URI: 		https://jdmdigital.co
*/

/* 	USAGE: 
	if(function_exists('show_field_in_header')){
		show_field_in_header();
	}
*/

// BASED ON: https://codex.buddypress.org/plugindev/how-to-edit-group-meta-tutorial/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function gbab_bp_group_meta_init() {
	function gbab_custom_field($meta_key='') {
		//get current group id and load meta_key value if passed. If not pass it blank
		return groups_get_groupmeta( bp_get_group_id(), $meta_key) ;
	}

	//code if using seperate files require( dirname( __FILE__ ) . '/buddypress-group-meta.php' );
	// This function is our custom field's form that is called in create a group and when editing group details
	function gbab_group_header_fields_markup() {
		global $bp, $wpdb;?>
		<div id="group-button-url-wrapper">
			<label for="group-button-url">Button Link URL <span style="font-weight: normal">(optional)</span></label>
			<input id="group-button-url" type="url" name="group-button-url" value="<?php echo gbab_custom_field('group-button-url'); ?>" placeholder="leave blank to disable" />
		</div>
		<div id="group-button-text-wrapper">
			<label for="group-button-text">Button Text <span style="font-weight: normal">(optional)</span></label>
			<input id="group-button-text" type="text" name="group-button-text" value="<?php echo gbab_custom_field('group-button-text'); ?>" placeholder="text for the button" />
		</div>
	<?php }

	// This saves the custom group meta
	// Where $plain_fields = array.. you may add additional fields, eg
	//  $plain_fields = array(
	//      'field-one',
	//      'field-two'
	//  );
	function gbab_group_header_fields_save( $group_id ) {
		global $bp, $wpdb;
		$plain_fields = array(
			'group-button-url',
			'group-button-text'
		);
		foreach( $plain_fields as $field ) {
			$key = $field;
			if ( isset( $_POST[$key] ) ) {
				$value = $_POST[$key];
				groups_update_groupmeta( $group_id, $field, $value );
			}
		}
	}
	add_filter( 'groups_custom_group_fields_editable', 'gbab_group_header_fields_markup' );
	add_action( 'groups_group_details_edited', 'gbab_group_header_fields_save' );
	add_action( 'groups_created_group',  'gbab_group_header_fields_save' );
 
	// Show the custom field in the group header
	function gbab_show_field_in_header( ) {
		if(custom_field('group-button-url') != ''){ 
			echo '<a id="justnform-group-button" href="'. gbab_custom_field('group-button-url') .'" class="button btn-fw btn-primary" role="button">'. gbab_custom_field('group-button-text') .'</a>';
		}
	}
	add_action('bp_group_header_meta' , 'show_field_in_header') ;

} // END group_meta_init
add_action( 'bp_include', 'gbab_bp_group_meta_init' );

/* If you have code that does not need BuddyPress to run, then add it here. */

?>
