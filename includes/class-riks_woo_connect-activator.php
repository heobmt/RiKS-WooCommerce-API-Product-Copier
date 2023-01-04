<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.riksiot.com
 * @since      1.0.0
 *
 * @package    Riks_woo_connect
 * @subpackage Riks_woo_connect/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Riks_woo_connect
 * @subpackage Riks_woo_connect/includes
 * @author     Ocean Vu <hadolanh@yahoo.com>
 */
class Riks_woo_connect_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        global $wpdb;
    	//global $jal_db_version;
    
    	$table_name = $wpdb->prefix.'riks_wac';
    	
    	$charset_collate = $wpdb->get_charset_collate();
    
    	$sql = "CREATE TABLE $table_name (
    		id mediumint(9) NOT NULL AUTO_INCREMENT,
    		name tinytext NOT NULL,
    		scode tinytext NOT NULL,
    		api_key text NOT NULL,
    		api_url text NOT NULL,
    		PRIMARY KEY  (id)
    	) $charset_collate;";
    
    	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    	dbDelta( $sql );
    
    	//add_option( 'jal_db_version', $jal_db_version );
	}
}
