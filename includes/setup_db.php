<?php
// setup the database for the wp_decide app
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

/**
 * Create fyvent tables on new installs or updates
 * This functions checks if those tables already exist before creating them
 *
 * @since 1.0.0
**/
function fyv_setupDB() {
	global $wpdb;

	$db_version = get_option( 'fyv_db_version', 0 );
	$versions = version_compare( $db_version, fyvent_VERSION );

	// create tables on new installs or updates
	if ( empty( $db_version ) || version_compare( $db_version, fyvent_VERSION, "<") ) {

		update_option( 'fyv_db_version', fyvent_VERSION );

		$sqlQuery = 'CREATE TABLE ' . $wpdb->prefix . "fyv_votes (
		  id int(11) unsigned NOT NULL AUTO_INCREMENT,
		  id_post int(11) unsigned NOT NULL,
		  id_question int(11) unsigned NOT NULL,
		  id_comment int(11) unsigned NOT NULL,
		  id_user int(11) unsigned NOT NULL,
		  vote_yes tinyint(1) DEFAULT '0',
		  vote_no tinyint(1) DEFAULT '0',
		  vote_blank tinyint(1) DEFAULT '0',
		  timestamp timestamp NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY  (id),
		  KEY id_post (id_post),
		  KEY id_user (id_user),
		  KEY id_comment (id_comment)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;";

		dbDelta( $sqlQuery );

		$sqlQuery = 'CREATE TABLE ' . $wpdb->prefix . "fyv_userinfo (
		  id int(11) unsigned NOT NULL,
		  date_birth date DEFAULT NULL,
		  validated tinyint(1) DEFAULT '0',
		  id_number char(16) DEFAULT '',
		  postal_code char(16) DEFAULT NULL,
		  phone char(16) DEFAULT NULL,
		  sex enum('M','F','O','N') DEFAULT 'N',
		  avatar varchar(256) DEFAULT NULL,
		  show_content tinyint(4) DEFAULT '0',
		  first_name varchar(24) DEFAULT NULL,
		  last_name varchar(24) DEFAULT NULL,
		  role varchar(128) DEFAULT NULL,
		  PRIMARY KEY  (id) )
		  ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		dbDelta( $sqlQuery );

		$sqlQuery = 'CREATE TABLE ' . $wpdb->prefix . "fyv_citizens (
		  id int(11) unsigned NOT NULL,
		  date_birth date DEFAULT NULL,
		  id_number char(16) DEFAULT '',
		  postal_code char(16) DEFAULT NULL,
		  last_name varchar(24) DEFAULT NULL,
		  PRIMARY KEY  (id) );";

		dbDelta( $sqlQuery );

	}

}
add_action( 'init', 'fyv_setupDB', 0 );
