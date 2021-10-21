<?php

function fyv_speaker_role() {

    //add the speaker role
    add_role(
        'speaker',
        'Speaker',
        array(
            'read'	=> true,
        )
    );

}
add_action('admin_init', 'fyv_speaker_role');


function filtering_default_contacts ( $contact, $user ) {

	$user_roles = $user->roles;
	if( in_array( 'speaker', $user_roles, true ) ) {

	    // first we suppress the legacy fields but we check that are empty in the user profile
	    foreach ( array ( 'aim', 'yim', 'jabber' ) as $method ) {
	        // we check if the current user has data in this old fields
			if ( isset ( $user->$method ) && ( trim( $user->$method ) ) ) continue;
	        unset( $contact[ $method ] );
	    }

	    $new_c = array ( 'phone' => 'Phone', // We keep the old name of legacy field
	                     'twitter'     => 'Twitter',
	                     'linkedin'     => 'LinkedIn',
	    );

    	return array_merge( $contact, $new_c );
	} else {
		return $contact;
	}

}
add_filter( 'user_contactmethods', 'filtering_default_contacts', 99, 2 );

/**
 * Hook in and add a metabox to add fields to the user profile pages
 */
function fyv_register_speaker_profile_metabox( $user_id ) {

	$prefix = 'fyv_speaker_';

	/**
	 * Metabox for the user profile screen
	 */
	$cmb_user = new_cmb2_box( array(
		'id'               => $prefix . 'edit',
		'title'            => __( 'Speaker Information', 'fyvent' ), // Doesn't output for user boxes
		'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
		'new_user_section' => 'add-existing-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
		'show_on_cb'	=> 'fyv_show_meta_to_chosen_roles',
		'show_on_roles' => array( 'speaker', 'administrator' ),
	) );

	$cmb_user->add_field( array(
		'name'     => __( 'Extra Info', 'fyvent' ),
		'id'       => $prefix . 'extra_info',
		'type'     => 'title',
		'on_front' => false,
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Gender', 'fyvent' ),
		'type'    => 'select',
		'id'   => $prefix . 'gender',
    	'show_option_none' => false,
		'default'          => 'dnda',
		'options'          => array(
			'male' => __( 'Male', 'fyvent' ),
			'female'   => __( 'Female', 'fyvent' ),
			'other'   => __( 'Other', 'fyvent' ),
			'dnda' => __( 'I prefer not to say', 'fyvent' ),
		),
	) );

	$cmb_user->add_field( array(
		'name' => __( 'Position', 'fyvent' ),
		'id'   => $prefix . 'position',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => __( 'Organization', 'fyvent' ),
		'id'   => $prefix . 'organization',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => __( 'City', 'fyvent' ),
		'id'   => $prefix . 'city',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => __( 'Country', 'fyvent' ),
		'id'   => $prefix . 'country',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => __( 'I have read and agree with the <a href="privacy">privacy rules</a> for this event', 'fyvent' ),
		'id'   => $prefix . 'gpdr',
		'type' => 'checkbox',
	) );

	if(  current_user_can( 'edit_users' ) ){
		$cmb_user->add_field( array(
			'name' => __( 'Attended', 'fyvent' ),
			'id'   => $prefix . 'attended',
			'type' => 'checkbox',
		) );
	}

	$cmb_user->add_field( array(
		'name' => __( 'Special Needs', 'fyvent' ),
		'description' => __( 'Do you have any dietary restriction or any other needs that we need to know about?', 'fyvent'),
		'id'   => $prefix . 'special_needs',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Photo', 'fyvent' ),
		'id'      => $prefix . 'photo',
		'type'    => 'file',
	) );

	$cmb_user->add_field( array(
	    'name' => __( 'Presentation', 'fyvent' ),
	    'desc' => '',
	    'id'   => $prefix . 'presentation',
	    'type' => 'file_list',
	    'text' => array(
	        'add_upload_files_text' => __( 'Add or Upload Files', 'fyvent' ),
	        'remove_image_text' => __( 'Remove File', 'fyvent' ),
	        'file_text' => __( 'File', 'fyvent' ),
	        'file_download_text' => __( 'Download', 'fyvent' ),
	        'remove_text' => __( 'Remove', 'fyvent' ),
	    ),
	) );

	$cmb_user->add_field( array(
	    'name' => esc_html__( 'Notes', 'fyvent' ),
	    'id' => $prefix . 'notes',
	    'type' => 'textarea_small'
	) );

}
add_action( 'cmb2_admin_init', 'fyv_register_speaker_profile_metabox' );

function fyv_register_speaker(){

	//If user is logged in, go to Home
	if ( is_user_logged_in() ) {
		echo '<script type="text/javascript">
			window.location = "'.get_home_url().'"
			</script>';
	}

	$registered = false;

	if ( isset( $_POST['submit'] ) ) {

		$username = sanitize_text_field( $_POST['username'] );
		$email = sanitize_text_field( $_POST['useremail'] );
		$password = $_POST['password'];
		$user_id = username_exists( $username );

		$message = '';
		$error = '';

		// checks that a user with this username or email doesn't exists already
		if ( ! $user_id and email_exists( $email ) == false ) {
			// create the new user
			$user_id = wp_create_user( $username, $password, $email );
			if ( ! is_wp_error( $user_id ) ) {
				$message = __( 'User created', 'fyvent' );
				$registered = true;
				$user = new WP_User( $user_id );
				$user->set_role( 'speaker' );

				$firstname = sanitize_text_field( $_POST['firstname'] );
				update_user_meta( $user_id, 'first_name', $firstname );
				$lastname = sanitize_text_field( $_POST['lastname'] );
				update_user_meta( $user_id, 'last_name', $lastname );
				$phone = sanitize_text_field( $_POST['phone'] );
				update_user_meta( $user_id, 'phone', $phone );
				$gender = $_POST['gender'];
				add_user_meta( $user_id, 'fyv_speaker_gender', $gender );
				$position = sanitize_text_field( $_POST['position'] );
				$gpdr= true;
				add_user_meta( $user_id, 'fyv_speaker_gpdr', $gpdr );

			} else {
				$error = $user_id->get_error_messages();
			}
			fyv_show_admin_messages( $message, $error );
		} else {
			$error = __( 'Username or Email already used', 'fyvent' );
		}
	}

	if ( $registered ) {
		echo '<div style="margin: auto;">';
		echo '<h3>' . __( 'You are registered now', 'fyvent' ) . '</h3>';
		echo '<p><a href="/login/">';
		echo '<button>' . __( 'Log In', 'fyvent' ) . '</button></a>';
		echo '</p></div>';

	} else {
		echo '<div>';
		fyv_speaker_register_form();
		echo '</div>';
	}

}

function fyv_speaker_register_form(){

	$form = '
    	<form action="' . htmlentities( $_SERVER['REQUEST_URI'] ) . '" method="post">
			<div class="form-group" >
				<label for="username">' . esc_html( __( 'Username', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="username" id="username" value="" required />
            </div>
        	<div class="form-group" >
				<label for="useremail">' . esc_html( __( 'Email Address', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="email" name="useremail" id="useremail" value="" required />
            </div>
            <div class="form-group" >
                <label for="password">' . esc_html( __( 'Password', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="password" name="password" id="password" value=""  required />
            </div>
            <div class="form-group" >
				<label for="firstname">' . esc_html( __( 'First Name', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="firstname" id="firstname" value="" required />
            </div>
            <div class="form-group" >
				<label for="lastname">' . esc_html( __( 'Last Name', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="lastname" id="lastname" value="" required />
            </div>
            <div class="form-group" >
				<label for="phone">' . esc_html( __( 'Phone', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="phone" id="phone" value="" required />
            </div>
            <div class="form-group" >
	            <label for="gender">' . esc_html( __( 'Gender', 'fyvent' ) ) . '</label>
	            <select class="form-control" name="gender" id="gender">
		            <option value="male">'.__( 'Male', 'fyvent' ).'</option>
		            <option value="female">'.__( 'Female', 'fyvent' ).'</option>
		            <option value="other">'.__( 'Other', 'fyvent' ).'</option>
		            <option value="dnda" selected="selected">'.__( 'I prefer not to say', 'fyvent' ).'</option>
				</select>
			</div>
	        <div class="form-check" >
				<input class="form-check-input"  type="checkbox" id="check-terms" required>
				<label class="form-check-label" for="check-terms">' .
				get_option( 'fyv_attendant_privacy_agreement', 'I agree with the <a href="'.get_option( 'fyv_settings', 'fyv_privacy_page' ).'">Privacy Policy</a>.' ) .
				'</label>
			</div>

			<button '.fyv_classes( 'button' ).' type="submit" name="submit" id="submit" >' . esc_attr( __( 'Register', 'fyvent' ) ) . '</button>

		</form>';

	echo $form;
}

function fyv_show_speaker_shortcode( $atts = [], $content = null, $tag = '' ){

	// normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

	global $wp_query;
	if( $wp_query->query_vars['speaker'] ){
		$atts['id'] = $wp_query->query_vars['speaker'];
	}

    if( $atts['id'] ){
	    $user = get_user_by( 'id', $atts['id'] );
		if( in_array( 'speaker', $user->roles, true ) ){
	    	$id = $atts['id'];
	    	$speaker_info = get_userdata( $id );
	    	$speaker_data = get_user_meta( $id );
	    	?>
	    	<div <?php  echo fyv_classes( 'speaker-one' ); ?> >
				<div <?php  echo fyv_classes( 'speaker-photo' ); ?> >
					<?php
					if( $speaker_data['fyv_speaker_photo'][0] ){
						echo '<img src="'.$speaker_data['fyv_speaker_photo'][0].'" alt="speaker photo" width="250px" '.fyv_classes( 'img' ).'/>';
					}
					?>
				</div>
				<div <?php  echo fyv_classes( 'speaker-info' ); ?> >
					<h4><?php echo '<a '.fyv_classes( 'speaker-name' ).' href="/speakers?speaker='.$speaker_info->ID.'">'.ucwords( $speaker_info->first_name.' '.$speaker_info->last_name ).'</a>'; ?></h4>
					<p <?php  echo fyv_classes( 'speaker-position' ); ?> >
						<?php
							if( $speaker_data['fyv_speaker_position'][0] ){
								echo $speaker_data['fyv_speaker_position'][0];
								$position = true;
							}
							if( $speaker_data['fyv_speaker_organization'][0] ){
								$txt = $position ? ', ': '';
								echo $txt.$speaker_data['fyv_speaker_organization'][0];
							}
						?>
					</p>
				<div>
				<p>
					<?php echo $speaker_data['description'][0]; ?>
				</p>
				<p>
					<h5><?php echo __( 'Sessions: ', 'fyvent' ); ?></h5>
					<p>
						<?php
						$sessions = fyv_get_sessions_from_speaker( $speaker_id );
						foreach( $sessions as $session ){
							$post = get_post( $session );
							echo '<p '.fyv_classes( 'speaker-session' ).'><a href="/sessions/?session_id='.$post->ID.'">'.$post->post_title.'</a></p>';
						}
						?>
					</p>
				</p>
				<p>
					<?php fyv_get_presentation( $speaker_data ); ?>
				</p>
				</div>
			</div>
			<?php
		}
    } else {
    	$args = array(
		    'role'    => 'speaker',
		    'orderby' => 'user_nicename',
		    'order'   => 'ASC'
		);
		$users = get_users( $args );

		foreach ( $users as $user ) {
			$speaker_info = get_userdata( $user->ID );
	    	$speaker_data = get_user_meta( $user->ID );
			fyv_list_speakers( $speaker_data, $speaker_info );
		}
    }
}

function fyv_list_speakers( $speaker_data, $speaker_info ){

	$output = '<div '.fyv_classes( 'speaker-list' ).' >';
	$output .= '<div '.fyv_classes( 'speaker-photo' ).' >';
	if( $speaker_data['fyv_speaker_photo'][0] ){
		$output .= '<img src="'.$speaker_data['fyv_speaker_photo'][0].'" alt="speaker photo" width="150px" '.fyv_classes( 'img' ).'/>';
	} else {
		$output .= '<img src="'.plugin_dir_url( __FILE__ ) . '../assets/speaker-filler.png'.'" alt="speaker photo filler" width="150px" '.fyv_classes( 'img' ).'/>';
	}
	$output .= '</div>';
	$output .= '<div '.fyv_classes( 'speaker-info' ).' >';
	$output .= '<h4 '.fyv_classes( 'speaker-name' ).' ><a href="/speakers?speaker='.$speaker_info->ID.'">'.ucwords( $speaker_info->first_name.' '.$speaker_info->last_name ).'</a>';
	$output .= '</h4>';
	$output .= '<p '.fyv_classes( 'speaker-position' ).' >';
	if( $speaker_data['fyv_speaker_position'][0] ){
		$output .= $speaker_data['fyv_speaker_position'][0];
		$position = true;
	}
	if( $speaker_data['fyv_speaker_organization'][0] ){
		$txt = $position ? ', ': '';
		$output .= $txt.$speaker_data['fyv_speaker_organization'][0];
	}

	$output .= '</p>';
	$output .= fyv_get_presentation( $speaker_data );
	$output .= '</div></div>';

	echo $output;

}
