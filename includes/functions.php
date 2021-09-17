<?php

/**
 * Wrapper function around cmb2_get_option
 *
 * @since  1.0.0
 *
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 *
 * @return mixed           Option value
 */
function fyv_get_option( $section, $key, $default = false ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( $section, $key, $default );
	}

	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( 'main-options', $default );

	$val = $default;

	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}

	return $val;
}


/**
 * Gets options for a dropdown control from an enum field
 *
 * @since 1.0.0
 *
 * @param string $table_name Name of the table
 * @param string $column_name Name of the enum field with the values that we are retrieving
 * @param string $default Default value for the field
 *
 * @return string HTML for the dropdown field
 */
function fyv_get_enum_dropdown( $table_name, $column_name, $default ) {     global $wpdb;

	$dropdown = '<select name="' . $column_name . '" value="' . $default . '" class="form-control" >';
	$row = $wpdb->get_row( "SHOW COLUMNS FROM $table_name LIKE '$column_name'", ARRAY_A );
	$enum_list = explode( ',', str_replace( "'", '', substr( $row['Type'], 5, ( strlen( $row['Type'] ) - 6 ) ) ) );
	foreach ( $enum_list as $value ) {
		$dropdown .= '<option value="' . $value . '"';
		if ( $value == $default ) {
			$dropdown .= 'selected ';
		}
		$dropdown .= '>' . esc_html__( $value, 'fyvent' ) . '</option>';
	}
	$dropdown .= '</select>';

	return $dropdown;
}


/**
 * Gets the front-end-post-form cmb instance
 *
 * @since 1.0.0
 *
 * @param string $form ID of the form whose metabox object we want to retrieve
 *
 * @return CMB2 object
 */
function fyv_frontend_cmb2_get( $form ) {
	// Use ID of metabox in yourprefix_frontend_form_register
	$metabox_id = $form;

	// Post/object ID is not applicable since we're using this form for submission
	$object_id = 'fake-object-id';

	// Get CMB2 metabox object
	return cmb2_get_metabox( $metabox_id, $object_id );
}

/**
 * Disable CMB2 styles on front end forms.
 *
 * @since 1.0.0
 *
 * @param boolean $enabled state of the CMB2 front end style
 *
 * @return bool Whether to enable (enqueue) styles.
 */
function fyv_disable_cmb2_front_end_styles( $enabled ) {

	if ( ! is_admin() ) {
		$enabled = false;
	}

	return $enabled;
}
add_filter( 'cmb2_enqueue_css', 'fyv_disable_cmb2_front_end_styles' );

/**
 * Handles uploading a file to a WordPress post
 *
 * @param  int   $post_id              Post ID to upload the photo to
 * @param  array $attachment_post_data Attachement post-data array
 *
 * @since 1.0.0
 *
 * @return string attachment post ID
 */
function fyv_frontend_form_photo_upload( $post_id, $attachment_post_data = [] ) {

	// Make sure the right files were submitted
	if (
		empty( $_FILES )
		|| ! isset( $_FILES['submitted_post_thumbnail'] )
		|| isset( $_FILES['submitted_post_thumbnail']['error'] ) && 0 !== $_FILES['submitted_post_thumbnail']['error']
	) {
		return;
	}

	// Filter out empty array values
	$files = array_filter( $_FILES['submitted_post_thumbnail'] );

	// Make sure files were submitted at all
	if ( empty( $files ) ) {
		return;
	}

	// Make sure to include the WordPress media uploader API if it's not (front-end)
	if ( ! function_exists( 'media_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
	}

	// Upload the file and send back the attachment post ID
	return media_handle_upload( 'submitted_post_thumbnail', $post_id, $attachment_post_data );
}


/**
 * Shows social clean links as icons
 *
 * @param  int   $post_id  ID of the post in which we are showing the links so they point to the right URL
 *
 * @since 1.0.0
 *
 * @return string HTML code for displaying the links
 */
function fyv_social_links( $post_id ) {

	$links = '<div class="social-links">';
	$links .= '<a class="icon-share icon-share-facebook" href="http://www.facebook.com/sharer.php?u=' . get_permalink( $post_id ) . '"><i class="fab fa-facebook"></i></a>';
	$links .= '<a class="icon-share icon-share-linkedin" href="https://www.linkedin.com/sharing/share-offsite/?url=' . get_permalink( $post_id ) . '"><i class="fab fa-linkedin"></i></a>';
	$links .= '<a class="icon-share icon-share-twitter" href="https://twitter.com/intent/tweet?url=' . get_permalink( $post_id ) . '&text=' . get_the_title( $post_id ) . '"><i class="fab fa-twitter"></i></a>';
	$links .= '<a class="icon-share icon-share-whatsapp" href="https://api.whatsapp.com/send?text=' . get_the_title( $post_id ) . '%20' . get_permalink( $post_id ) . '"><i class="fab fa-whatsapp"></i></a>';
	$links .= '<a class="icon-share icon-share-telegram" href="https://t.me/share/url?url=' . get_permalink( $post_id ) . '&text=' . get_the_title( $post_id ) . '"><i class="fab fa-telegram"></i></a>';
	$links .= '</div>';

	return $links;
}

/**
 * Gets an address from a pair of coordinates
 *
 * @param  float   $lat  Latitude
 * @param  float   $long  Longitude
 *
 * @since 1.0.0
 *
 * @return String with the address or false if not found
 */
function fyv_getaddress( $lat, $long ){
	if( empty( $lat ) || empty( $long ) ){
		return false;
	}
	$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($long).'&sensor=false';
	$json = @file_get_contents( $url );
	$data=json_decode( $json );
	$status = $data->status;
	if($status=="OK") {
		return $data->results[0]->formatted_address;
	} else {
		return false;
	}
}

/**
 * Gets the venue from the room ID
 *
 * @param  string   $room_id  ID of the room whose venue we are looking for
 *
 * @since 1.0.0
 *
 * @return String Post id or false if not found
 */
function fyv_get_venue_from_room( $room_id ){
	global $wpdb;
	$table = $wpdb->prefix.'postmeta';
	$sql = "SELECT * FROM $table WHERE meta_value LIKE '%".$room_id."%' AND meta_key='rooms';";
	$results = $wpdb->get_results( $sql );
	if ( $results ) {
		return $results[0]->post_id;
	} else {
		return false;
	}
}

/**
 * Gets the room from the session ID
 *
 * @param  string   $session_id  ID of the session whose room we are looking for
 *
 * @since 1.0.0
 *
 * @return String Post id or false if not found
 */
function fyv_get_room_from_session( $session_id ){
	global $wpdb;
	$table = $wpdb->prefix.'postmeta';
	$sql = "SELECT * FROM $table WHERE meta_value LIKE '%".$session_id."%' AND meta_key='sessions';";
	$results = $wpdb->get_results( $sql );
	if ( $results ) {
		return $results[0]->post_id;
	} else {
		return false;
	}
}
