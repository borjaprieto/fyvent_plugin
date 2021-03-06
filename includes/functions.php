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
function fyvent_get_option( $section, $key, $default = false ) {
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
 * Allows the media uploader to work on specific pages
 *
 * @since  1.0.0
 *
 */
function fyvent_allow_speaker_uploads() {
	if ( is_admin() ) {
		return;
	}

	$path = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';

	$speaker_info_page = fyvent_get_option( 'fyvent_settings', 'fyvent_speaker_info_page', '/speaker-information/' );
	if ( !$path || ( $speaker_info_page != $path ) ) {
		return;
	}

	$speaker = get_role( 'speaker' );

	// This is the only cap needed to upload files.
	$speaker->add_cap( 'upload_files' );

}
add_action( 'init', 'fyvent_allow_speaker_uploads' );

/**
 * Displays only user-uploaded files to each user
 *
 * @param WP_Query $wp_query_obj
 *
 * @since 1.0.0
 */
function fyvent_restrict_media_library( $wp_query_obj ) {
	global $current_user, $pagenow;

	if ( !is_a( $current_user, 'WP_User' ) ) {
		return;
	}

	if ( ( 'admin-ajax.php' != $pagenow ) || ( 'query-attachments' != $_REQUEST['action'] ) ){
		return;
	}

	if ( !current_user_can( 'manage_media_library' ) ){
		$wp_query_obj->set( 'author', $current_user->ID );
	}
}
add_action( 'pre_get_posts', 'fyvent_restrict_media_library' );


/**
 * Handles uploading documents to an user profile
 *
 * @since 1.0.0
 *
 * @return string ID of the upload or false if we couldn't upload the avatar
 */
function fyvent_upload_media( $document ) {

	$doc = $_FILES[ $document ];
	if( !empty( $doc['name'] ) ){
		$wordpress_upload_dir = wp_upload_dir();

		$new_file_path = $wordpress_upload_dir['path'] . '/' . $doc['name'];
		$file_uploaded = $_FILES[ $document ]['tmp_name'];
		$new_file_mime = mime_content_type( $file_uploaded );
		$file = '/path/to/file.png';
		$filename = basename( $file_uploaded );

		$upload_file = wp_upload_bits( $filename, null, file_get_contents( $new_file_path ) );
		if( !$upload_file['error'] ){
			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_parent' => $parent_post_id,
				'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );
			if( !is_wp_error( $attachment_id ) ){
				require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
				wp_update_attachment_metadata( $attachment_id,  $attachment_data );
			}
		}

		while ( file_exists( $new_file_path ) ) {
			$i++;
			$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $doc['name'];
		}

		if ( move_uploaded_file( $doc['tmp_name'], $new_file_path ) ) {

			$upload_id = wp_insert_attachment(
				[
					'guid' => $new_file_path,
					'post_mime_type' => $new_file_mime,
					'post_title' => preg_replace( '/\.[^.]+$/', '', $doc['name'] ),
					'post_content' => '',
					'post_status' => 'inherit',
				], $new_file_path
			);

			if ( ! function_exists( 'wp_crop_image' ) ) {
    			include( ABSPATH . 'wp-admin/includes/image.php' );
			}
			// Generate and save the attachment metas into the database
			wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );

			return $upload_id;
		}
	}

	return false;

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
function fyvent_get_venue_from_room( $room_id ){
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
function fyvent_get_room_from_session( $session_id ){
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

/**
 * Gets all Sessions for a Speaker
 *
 * @param  string   $speaker_id  ID of the speaker whose sessions we are looking for
 *
 * @since 1.0.0
 *
 * @return Array of session ids or false if not found
 */
function fyvent_get_sessions_from_speaker( $speaker_id ){
	global $wpdb;
	$table = $wpdb->prefix.'postmeta';
	$sql = "SELECT * FROM $table WHERE meta_value LIKE '%".$speaker_id."%' AND meta_key='speakers';";
	$results = $wpdb->get_results( $sql, ARRAY_A );

	if( $results ){
		$sessions = array();
		foreach( $results as $result){
			$sessions[] = $result['post_id'];
		}
		return $sessions;
	} else {
		return false;
	}
}

/**
 * Removes Date filter in admin tables
 *
 * @param Array $months
 *
 * @since 1.0.0
 *
 * @return array
 */
function fyvent_remove_date_filter( ) {

	$screen = get_current_screen();

    if ( ( 'venue' == $screen->post_type ) ||
    	 ( 'room' == $screen->post_type ) ||
    	 ( 'session' == $screen->post_type )
       ){
        add_filter('months_dropdown_results', '__return_empty_array');
    }
}

/**
 * Callback function to show user metadata only to some roles
 *
 * @param CMB_Object $cmb Custom Metabox we are showing
 *
 * @since 1.0.0
 *
 * @return true if metabox should be showed, false if not
 */
function fyvent_show_meta_to_chosen_roles( $cmb ) {

	$roles = $cmb->prop( 'show_on_roles', array() );

	// Do not limit the box display unless the roles are defined.
	if ( empty( $roles ) ) {
		return true;
	}

	$user = get_user_by( 'id', get_current_user_id() );

	// No user found, return
	if ( empty( $user ) ) {
		return false;
	}

	$has_role = array_intersect( (array) $roles, $user->roles );

	// Will show the box if user has one of the defined roles.
	return !empty( $has_role );
}


/**
 * Shows info or error messages on admin area
 *
 * @param string $message Message to be shown
 * @param string $error Error to be shown
 *
 * @since 1.0.0
 */
function fyvent_show_admin_messages( $message, $error ) {
	if ( ! empty( $error ) ) {
		echo '<div class="notice notice-error">';
		echo '<p><strong>' . esc_html( $error ) . '</strong></p>';
		echo '</div>';
	}

	if ( ! empty( $message ) ) {
		echo '<div class="notice notice-success is-dismissible">';
		echo '<p><strong>' . esc_html( $message ) . '</strong></p>';
		echo '</div>';
	}
}

/**
 * Shows info or error messages on posts or pages
 *
 * @param string $message Message to be shown
 * @param string $error Error to be shown
 *
 * @since 1.0.0
 */
function fyvent_show_front_messages( $message, $error ) {
	if ( ! empty( $error ) ) {
		echo '<div '.fyvent_classes( 'fyv-message-error' ).'">';
		echo '<p><strong>' . esc_html( $error ) . '</strong></p>';
		echo '</div>';
	}

	if ( ! empty( $message ) ) {
		echo '<div '.fyvent_classes( 'fyv-message-info' ).'">';
		echo '<p><strong>' . esc_html( $message ) . '</strong></p>';
		echo '</div>';
	}
}

/**
 * Adds or updates an user metadata field
 *
 * @param string $key Field to be updated
 * @param string $data Value to update
 *
 * @since 1.0.0
 */
function fyvent_update_user_data( $key, $data ){
	$save_data = sanitize_text_field( $data );
	$id = get_current_user_id();
	$result = add_user_meta( $id, $key, $save_data, true );
	if( !$result ){
		update_user_meta( $id, $key, $save_data );
	}
}

/**
 * Adds speaker var to query vars array
 *
 * @param array $aVars array of query vars
 *
 * @return array array of query vars
 *
 * @since 1.0.0
 */
function add_query_vars_speaker($aVars) {
	$aVars[] = "speaker";
	return $aVars;
}
// hook add_query_vars_speaker function into query_vars
add_filter('query_vars', 'add_query_vars_speaker');

/**
 * Adds session var to query vars array
 *
 * @param array $aVars array of query vars
 *
 * @return array array of query vars
 *
 * @since 1.0.0
 */
function add_query_vars_session($aVars) {
	$aVars[] = "session_id";
	return $aVars;
}
// hook add_query_vars_session function into query_vars
add_filter('query_vars', 'add_query_vars_session');

/**
 * Adds venue var to query vars array
 *
 * @param array $aVars array of query vars
 *
 * @return array array of query vars
 *
 * @since 1.0.0
 */
function add_query_vars_venue($aVars) {
	$aVars[] = "venue_id";
	return $aVars;
}
// hook add_query_vars_venue function into query_vars
add_filter('query_vars', 'add_query_vars_venue');

/**
 * Renders a link to a presentation submitted by the speaker if that option is selected
 *
 * @param array $speaker_data metadata of the speaker
 *
 * @return string HTML code to show presentation link
 *
 * @since 1.0.0
 */
function fyvent_get_presentation( $speaker_data ){
	$options = get_option( 'fyvent_settings' );
	$output = '';
	if( array_key_exists( 'fyvent_presentation_download', $options ) ){
		if( array_key_exists( 'fyvent_speaker_presentation', $speaker_data ) ){
			$files = unserialize( $speaker_data['fyvent_speaker_presentation'][0] );
			$output .= '<ul class="'.fyvent_classes( 'presentation-list' ).'">';
			foreach( $files as $file ){
				$output .= '<li><a '.fyvent_classes( 'presentation' ).'href="'.$file.'">'.__( 'Download Presentation', 'fyvent' ).'</a></li>';
			}
			$output .= '</ul>';
		}
	}
	return wp_kses( $output, 'post' );
}

/**
 * Finds out if current theme uses Bootstrap
 *
 * @param array $speaker_data metadata of the speaker
 *
 * @return true if theme uses Bootstrap, false if not
 *
 * @since 1.0.0
 */
function fyvent_theme_uses_bootstrap(){
	$style = 'bootstrap';
	if( ( ! wp_style_is( $style, 'queue' ) ) && ( ! wp_style_is( $style, 'done' ) ) ) {
	   return true;
	} else {
		return false;
	}
}

/**
 * Renders CSS classes to personalize design
 *
 * @param string $class name of a CSS class that we are rendering
 *
 * @return string classes to show in the HTML code
 *
 * @since 1.0.0
 */
function fyvent_classes( $class ){

	//if the theme doesn't use bootstrap just output the class so the user can define it
	if( !fyvent_theme_uses_bootstrap() ){
		return 'class="'.$class.'"';
	}
	$output = 'class="';
	switch( $class ){
		case 'fyv-message-info':
			$output .= 'alert alert-primary"';
			break;
		case 'fyv-message-error':
			$output .= 'alert alert-danger"';
			break;
		case 'img':
			$output .= 'img-fluid"';
			break;
		case 'speaker-list':
			$output .= 'row py-2"';
			break;
		case 'speaker-one':
			$output .= 'row"';
			break;
		case 'speaker-photo':
			$output .= 'col-md-2 p-4"';
			break;
		case 'speaker-photo-one':
			$output .= 'col-md-2 p-4"';
			break;
		case 'speaker-info':
			$output .= 'col-md-10 pt-3"';
			break;
		case 'speaker-info-one':
			$output .= 'col-md-10"';
			break;
		case 'session-list':
			$output .= 'row py-2"';
			break;
		case 'session-one':
			$output .= 'row"';
			break;
		case 'session-image':
			$output .= 'col-md-3 p-4"';
			break;
		case 'session-image-one':
			$output .= 'col-md-4 p-1"';
			break;
		case 'session-info':
			$output .= 'col-md-9 pt-3"';
			break;
		case 'session-info-one':
			$output .= 'col-md-8"';
			break;
		case 'session-speaker-list':
			$output .= 'col-md-8"';
			break;
		case 'speaker-position':
			$output .= 'font-weight-bold"';
			break;
		case 'button':
			$output .= 'btn btn-primary m-4" ';
			break;
		default:
			$output .= $class.'"';
	}
	return esc_html( $output );
}

/**
 * Finds out if current user is a speaker
 *
 * @return true if user is a speaker, false if not
 *
 * @since 1.0.0
 */
function fyvent_is_user_speaker(){
	$user = wp_get_current_user();
	if( in_array( 'speaker', $user->roles, true ) ){
		return true;
	} else {
		return false;
	}
}

/**
 * Finds out if current user is an attendant
 *
 * @return true if user is an attendant, false if not
 *
 * @since 1.0.0
 */
function fyvent_is_user_attendant(){
	$user = wp_get_current_user();
	if( in_array( 'attendant', $user->roles, true ) ){
		return true;
	} else {
		return false;
	}
}

/**
 * Expands wp_kses allowed tags to include form tags
 *
 * @return array of allowed tags
 *
 * @since 1.0.0
 */
function fyvent_allowed_tags() {
	$allowed = wp_kses_allowed_html( 'post' );

	$allowed['input'] = array(
		'class' => array(),
		'id'    => array(),
		'name'  => array(),
		'value' => array(),
		'type'  => array(),
	);

	$allowed['select'] = array(
		'class'  => array(),
		'id'     => array(),
		'name'   => array(),
		'value'  => array(),
		'type'   => array(),
	);

	$allowed['option'] = array(
		'selected' => array(),
	);

	$allowed['style'] = array(
		'types' => array(),
	);

	return $allowed;
}