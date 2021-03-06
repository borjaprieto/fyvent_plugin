<?php

/**
 * Adds speaker role.
 *
 * @since 1.0.0
 */
function fyvent_speaker_role() {

    //add the speaker role
    add_role(
        'speaker',
        'Speaker',
        array(
            'read'	=> true,
        )
    );

}
add_action('admin_init', 'fyvent_speaker_role');

/**
 * Drops unused contact fields and updates new ones
 *
 * @params  array  $contact Array of contact methods
 * @params  User_object $user User whose contact fields we are processing
 *
 * @return array Array of contact methods
 *
 * @since 1.0.0
 */
function fyvent_filter_default_contacts ( $contact, $user ) {

	$user_roles = $user->roles;
	if( in_array( 'speaker', $user_roles, true ) ) {

	    // first we suppress the legacy fields but we check that are empty in the user profile
	    foreach ( array ( 'aim', 'yim', 'jabber' ) as $method ) {
	        // we check if the current user has data in this old fields
			if ( isset ( $user->$method ) && ( trim( $user->$method ) ) ) continue;
	        unset( $contact[ $method ] );
	    }
	    $new_c = array ( 'phone' => 'Phone',
	                     'twitter'     => 'Twitter',
	                     'linkedin'     => 'LinkedIn',
	    );
    	return array_merge( $contact, $new_c );
	} else {
		return $contact;
	}

}
add_filter( 'user_contactmethods', 'fyvent_filter_default_contacts', 99, 2 );

/**
 * Hooks in and adds a metabox to add fields to the user profile pages
 *
 * @params string $user_id ID of the user whose profile we are showing
 *
 * @since 1.0.0
 *
 */
function fyvent_register_speaker_profile_metabox( $user_id ) {

	$prefix = 'fyvent_speaker_';

	$options = get_option( 'fyvent_settings', false );
	$option_privacy = $options ? $options['fyvent_privacy_page'] : '/privacy';
	$option = !empty( $option_privacy ) ? $option_privacy : '/privacy';


	/**
	 * Metabox for the user profile screen
	 */
	$cmb_user = new_cmb2_box( array(
		'id'               => $prefix.'edit',
		'title'            => esc_html__( 'Speaker Information', 'fyvent' ), // Doesn't output for user boxes
		'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
		'new_user_section' => 'add-existing-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
		'show_on_cb'	=> 'fyvent_show_meta_to_chosen_roles',
		'show_on_roles' => array( 'speaker', 'administrator' ),
	) );

	$cmb_user->add_field( array(
		'name'     => esc_html__( 'Extra Info', 'fyvent' ),
		'id'       => $prefix . 'extra_info',
		'type'     => 'title',
		'on_front' => false,
	) );

	$cmb_user->add_field( array(
		'name'    => esc_html__( 'Gender', 'fyvent' ),
		'type'    => 'select',
		'id'   => $prefix . 'gender',
    	'show_option_none' => false,
		'default'          => 'dnda',
		'options'          => array(
			'male' => esc_html__( 'Male', 'fyvent' ),
			'female'   => esc_html__( 'Female', 'fyvent' ),
			'other'   => esc_html__( 'Other', 'fyvent' ),
			'dnda' => esc_html__( 'I prefer not to say', 'fyvent' ),
		),
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Position', 'fyvent' ),
		'id'   => $prefix . 'position',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Organization', 'fyvent' ),
		'id'   => $prefix . 'organization',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'City', 'fyvent' ),
		'id'   => $prefix . 'city',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Country', 'fyvent' ),
		'id'   => $prefix . 'country',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => wp_kses( 'I agree with the <a href="'.$option.'">Privacy Policy</a>.' , fyvent_allowed_tags() ),
		'id'   => $prefix . 'gpdr',
		'type' => 'checkbox',
	) );

	if(  current_user_can( 'edit_users' ) ){
		$cmb_user->add_field( array(
			'name' => esc_html__( 'Attended', 'fyvent' ),
			'id'   => $prefix . 'attended',
			'type' => 'checkbox',
		) );
	}

	$cmb_user->add_field( array(
		'name' => esc_html__( 'Special Needs', 'fyvent' ),
		'description' => esc_html__( 'Do you have any dietary restriction or any other needs that we need to know about?', 'fyvent'),
		'id'   => $prefix . 'special_needs',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name'    => esc_html__( 'Photo', 'fyvent' ),
		'id'      => $prefix . 'photo',
		'type'    => 'file',
	) );

	$cmb_user->add_field( array(
	    'name' => esc_html__( 'Presentation', 'fyvent' ),
	    'desc' => '',
	    'id'   => $prefix . 'presentation',
	    'type' => 'file_list',
	    'text' => array(
	        'add_upload_files_text' => esc_html__( 'Add or Upload Files', 'fyvent' ),
	        'remove_image_text' => esc_html__( 'Remove File', 'fyvent' ),
	        'file_text' => esc_html__( 'File', 'fyvent' ),
	        'file_download_text' => esc_html__( 'Download', 'fyvent' ),
	        'remove_text' => esc_html__( 'Remove', 'fyvent' ),
	    ),
	) );

	$cmb_user->add_field( array(
	    'name' => esc_html__( 'Notes', 'fyvent' ),
	    'id' => $prefix . 'notes',
	    'type' => 'textarea_small'
	) );

}
add_action( 'cmb2_admin_init', 'fyvent_register_speaker_profile_metabox' );

/**
 * Registers the user as speaker if they have filled the speaker form
 *
 * @since 1.0.0
 *
 */
function fyvent_register_speaker(){

	//If user is logged in, go to Home
	if ( is_user_logged_in() ) {
		echo '<script type="text/javascript">
			window.location = "'.get_home_url().'"
			</script>';
	}

	$registered = false;

	if ( isset( $_POST['submit'] ) ) {

		$username = sanitize_text_field( $_POST['username'] );
		$email = sanitize_email( $_POST['useremail'] );
		$password = $_POST['password'];
		$user_id = username_exists( $username );

		$message = '';
		$error = '';

		// checks that a user with this username or email doesn't exists already
		if ( ! $user_id and email_exists( $email ) == false ) {
			// create the new user
			$user_id = wp_create_user( $username, $password, $email );
			if ( ! is_wp_error( $user_id ) ) {
				$message = esc_html__( 'User created', 'fyvent' );
				$registered = true;
				$user = new WP_User( $user_id );
				$user->set_role( 'speaker' );

				$firstname = sanitize_text_field( $_POST['firstname'] );
				update_user_meta( $user_id, 'first_name', $firstname );
				$lastname = sanitize_text_field( $_POST['lastname'] );
				update_user_meta( $user_id, 'last_name', $lastname );
				$phone = sanitize_text_field( $_POST['phone'] );
				update_user_meta( $user_id, 'phone', $phone );
				$gender = sanitize_text_field( $_POST['gender'] );
				add_user_meta( $user_id, 'fyvent_speaker_gender', $gender );
				$gpdr= true;
				add_user_meta( $user_id, 'fyvent_speaker_gpdr', $gpdr );

			} else {
				$error = $user_id->get_error_messages();
			}
			fyvent_show_admin_messages( $message, $error );
		} else {
			$error = esc_html__( 'Username or Email already used', 'fyvent' );
		}
	}

	if ( $registered ) {
		echo '<div style="margin: auto;">';
		echo '<h3>' . esc_html__( 'You are registered now', 'fyvent' ) . '</h3>';
		echo '<p><a href="/login/">';
		echo '<button '.fyvent_classes( 'button' ).'>' . esc_html__( 'Log In', 'fyvent' ) . '</button></a>';
		echo '</p></div>';

	} else {
		echo '<div>';
		fyvent_speaker_register_form();
		echo '</div>';
	}

}

/**
 * Renders the speaker register form
 *
 * @since 1.0.0
 *
 */
function fyvent_speaker_register_form(){

	$form = '
    	<form action="' . htmlentities( $_SERVER['REQUEST_URI'] ) . '" method="post">
			<div class="form-group" >
				<label for="username">' . esc_html__( 'Username', 'fyvent' ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="username" id="username" value="" required />
            </div>
        	<div class="form-group" >
				<label for="useremail">' . esc_html__( 'Email Address', 'fyvent' ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="email" name="useremail" id="useremail" value="" required />
            </div>
            <div class="form-group" >
                <label for="password">' . esc_html__( 'Password', 'fyvent' ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="password" name="password" id="password" value=""  required />
            </div>
            <div class="form-group" >
				<label for="firstname">' . esc_html__( 'First Name', 'fyvent' ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="firstname" id="firstname" value="" required />
            </div>
            <div class="form-group" >
				<label for="lastname">' . esc_html__( 'Last Name', 'fyvent' ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="lastname" id="lastname" value="" required />
            </div>
            <div class="form-group" >
				<label for="phone">' . esc_html__( 'Phone', 'fyvent' ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="phone" id="phone" value="" required />
            </div>
            <div class="form-group" >
	            <label for="gender">' . esc_html__( 'Gender', 'fyvent' ) . '</label>
	            <select class="form-control" name="gender" id="gender">
		            <option value="male">'.esc_html__( 'Male', 'fyvent' ).'</option>
		            <option value="female">'.esc_html__( 'Female', 'fyvent' ).'</option>
		            <option value="other">'.esc_html__( 'Other', 'fyvent' ).'</option>
		            <option value="dnda" selected="selected">'.esc_html__( 'I prefer not to say', 'fyvent' ).'</option>
				</select>
			</div>
	        <div class="form-check" >
				<input class="form-check-input"  type="checkbox" id="check-terms" required>
				<label  class="form-check-label" for="check-terms">';
				$options = get_option( 'fyvent_settings', false );
				$option_privacy = $options ? $options['fyvent_privacy_page'] : '/privacy';
				$option = !empty( $option_privacy ) ? $option_privacy : '/privacy';
				$form .= wp_kses( get_option( 'fyvent_attendant_privacy_agreement', 'I agree with the <a href="'.$option.'">Privacy Policy</a>.' ), 'post' ).
				'</label>
			</div>

			<button '.fyvent_classes( 'button' ).' type="submit" name="submit" id="submit" >' . esc_attr__( 'Register', 'fyvent' ) . '</button>

		</form>';

	echo wp_kses( $form, fyvent_allowed_tags() );
}

/**
 * Renders session information from shortcode
 *
 * @since 1.0.0
 */
function fyvent_show_speaker_shortcode( $atts = [] ){

	// normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

	global $wp_query;
	if( $wp_query->query_vars['speaker'] ){
		$atts['id'] = $wp_query->query_vars['speaker'];
	}

	//if there is an id we show the speaker corresponding to that id. If not, list all speakers
    if( $atts['id'] ){
	    $user = get_user_by( 'id', $atts['id'] );
		if( in_array( 'speaker', $user->roles, true ) ){
	    	$id = $atts['id'];
	    	$speaker_info = get_userdata( $id );
	    	$speaker_data = get_user_meta( $id );
	    	?>
	    	<div <?php  echo wp_kses( fyvent_classes( 'speaker-one' ), 'post' ); ?> >
				<div <?php  echo wp_kses( fyvent_classes( 'speaker-photo' ), 'post' ); ?> >
					<?php
					if( $speaker_data['fyvent_speaker_photo'][0] ){
						echo wp_kses( '<img src="'.$speaker_data['fyvent_speaker_photo'][0].'" alt="speaker photo" width="250px" '.fyvent_classes( 'img' ).'/>', 'post' );
					}
					?>
				</div>
				<div <?php  echo wp_kses(  fyvent_classes( 'speaker-info' ), 'post'  ); ?> >
					<h4><?php echo '<a '.wp_kses( fyvent_classes( 'speaker-name' ), 'post'  ).' href="/speakers?speaker='.$speaker_info->ID.'">'.ucwords( $speaker_info->first_name.' '.$speaker_info->last_name ).'</a>'; ?></h4>
					<p <?php  echo wp_kses(  fyvent_classes( 'speaker-position' ), 'post'  ); ?> >
						<?php
							if( $speaker_data['fyvent_speaker_position'][0] ){
								echo wp_kses(  $speaker_data['fyvent_speaker_position'][0], 'post'  );
								$position = true;
							}
							if( $speaker_data['fyvent_speaker_organization'][0] ){
								$txt = $position ? ', ': '';
								echo wp_kses(  $txt.$speaker_data['fyvent_speaker_organization'][0], 'post'  );
							}
						?>
					</p>
				<div>
				<p>
					<?php echo wp_kses( $speaker_data['description'][0] , 'post' ); ?>
				</p>
				<p>
					<h5><?php echo esc_html__( 'Sessions: ', 'fyvent' ); ?></h5>
					<p>
						<?php
						$sessions = fyvent_get_sessions_from_speaker( $id );
						foreach( $sessions as $session ){
							$post = get_post( $session );
							echo wp_kses( '<p '.fyvent_classes( 'speaker-session' ).'><a href="/sessions/?session_id='.$post->ID.'">'.$post->post_title.'</a></p>', 'post'  );
						}
						?>
					</p>
				</p>
				<p>
					<?php fyvent_get_presentation( $speaker_data ); ?>
				</p>
				</div>
			</div>
			<?php
		}
    } else {
    	// list all speakers
    	$args = array(
		    'role'    => 'speaker',
		    'orderby' => 'user_nicename',
		    'order'   => 'ASC'
		);
		$users = get_users( $args );

		foreach ( $users as $user ) {
			$speaker_info = get_userdata( $user->ID );
	    	$speaker_data = get_user_meta( $user->ID );
			fyvent_list_speakers( $speaker_data, $speaker_info );
		}
    }
}

/**
 * Renders a speaker's information in a list of speakers
 *
 * @params  Array $speaker_data Array of speaker metadata
 * @params  Array $speaker_info Array of speaker info
 *
 * @since 1.0.0
 *
 */
function fyvent_list_speakers( $speaker_data, $speaker_info ){

	$output = '<div '.fyvent_classes( 'speaker-list' ).' >';
	$output .= '<div '.fyvent_classes( 'speaker-photo' ).' >';
	if( $speaker_data['fyvent_speaker_photo'][0] ){
		$output .= '<img src="'.$speaker_data['fyvent_speaker_photo'][0].'" alt="speaker photo" width="150px" '.fyvent_classes( 'img' ).'/>';
	} else {
		$output .= '<img src="'.plugin_dir_url( __FILE__ ) . '../assets/speaker-filler.png'.'" alt="speaker photo filler" width="150px" '.fyvent_classes( 'img' ).'/>';
	}
	$output .= '</div>';
	$output .= '<div '.fyvent_classes( 'speaker-info' ).' >';
	$output .= '<h4 '.fyvent_classes( 'speaker-name' ).' ><a href="/speakers?speaker='.$speaker_info->ID.'">'.ucwords( $speaker_info->first_name.' '.$speaker_info->last_name ).'</a>';
	$output .= '</h4>';
	$output .= '<p '.fyvent_classes( 'speaker-position' ).' >';
	if( $speaker_data['fyvent_speaker_position'][0] ){
		$output .= $speaker_data['fyvent_speaker_position'][0];
		$position = true;
	}
	if( $speaker_data['fyvent_speaker_organization'][0] ){
		$txt = $position ? ', ': '';
		$output .= $txt.$speaker_data['fyvent_speaker_organization'][0];
	}

	$output .= '</p>';
	$output .= fyvent_get_presentation( $speaker_data );
	$output .= '</div></div>';

	echo wp_kses( $output, fyvent_allowed_tags() );

}

/**
 * Processes a form where a speaker can update their information
 *
 * @since 1.0.0
 *
 */
function fyvent_speaker_information_form(){

	//if user is not a speaker they shouldn't be here
	if( !fyvent_is_user_speaker() ){
		echo '<script type="text/javascript">window.location = "'.get_home_url().'"</script>';
	} else {

		$user = wp_get_current_user();
		if ( isset( $_POST['submit'] ) ) {

			$message = '';
			$error = '';

			$email = sanitize_email( $_POST['useremail'] );
			$last_name = sanitize_text_field( $_POST['lastname'] );
			$first_name = sanitize_text_field( $_POST['firstname'] );

			$update_data = [
				'ID' => $user->id,
				'user_email' => $email,
				'last_name' => $last_name,
				'first_name' => $first_name,
			];
			$user_data = wp_update_user( $update_data );

			if ( ! is_wp_error( $user_data ) ) {

				$firstname = sanitize_text_field( $_POST['firstname'] );
				update_user_meta( $user->id, 'first_name', $firstname );
				$lastname = sanitize_text_field( $_POST['lastname'] );
				update_user_meta( $user->id, 'last_name', $lastname );
				$phone = sanitize_text_field( $_POST['phone'] );
				update_user_meta( $user->id, 'phone', $phone );
				$gender = sanitize_text_field( $_POST['gender'] );
				update_user_meta( $user->id, 'fyvent_speaker_gender', $gender );
				$position = sanitize_text_field( $_POST['position'] );
				update_user_meta( $user->id, 'fyvent_speaker_position', $position );
				$organization = sanitize_text_field( $_POST['organization'] );
				update_user_meta( $user->id, 'fyvent_speaker_organization', $organization );
				$city = sanitize_text_field( $_POST['city'] );
				update_user_meta( $user->id, 'fyvent_speaker_city', $city );
				$country = sanitize_text_field( $_POST['country'] );
				update_user_meta( $user->id, 'fyvent_speaker_country', $country );
				$special_needs = sanitize_text_field( $_POST['special_needs'] );
				update_user_meta( $user->id, 'fyvent_speaker_special_needs', $special_needs );
				$photo_id = fyvent_upload_media( 'photo' );
				if( $photo_id ){
					update_user_meta( $user->id, 'fyvent_speaker_photo_id', $photo_id );
					$photo = wp_get_attachment_url( $photo_id );
					update_user_meta( $user->id, 'fyvent_speaker_photo', $photo );
				}
				$presentation_id = fyvent_upload_media( 'presentation' );
				if( $presentation_id ){
					update_user_meta( $user->id, 'fyvent_speaker_presentation_id', $presentation_id );
					$presentation = wp_get_attachment_url( $presentation_id );
					update_user_meta( $user->id, 'fyvent_speaker_presentation', $presentation );
				}
				$message = esc_html__('Your information has been updated', 'fyvent' );
				fyvent_show_front_messages( $message, '' );
				fyvent_show_speaker_info_form();
			} else {
				$error = $user_data->get_error_messages();
				if( is_array( $error ) ){
					foreach( $error as $error_msg){
						fyvent_show_front_messages( '', $error_msg );
					}
				} else {
					fyvent_show_front_messages( '', $error );
				}
			}
		} else {
			fyvent_show_speaker_info_form();
		}
	}

}

/**
 * Renders the speaker information form
 *
 * @since 1.0.0
 *
 */
function fyvent_show_speaker_info_form(){

	$user = wp_get_current_user();
	$speaker_info = get_userdata( $user->ID );
	$speaker_data = get_user_meta( $user->ID );

	$form = '<form action="' . htmlentities( $_SERVER['REQUEST_URI'] ) . '" enctype="multipart/form-data" method="post">

        	<div class="form-group" >
				<label for="useremail">' . esc_html__( 'Email Address', 'fyvent' ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="email" name="useremail" id="useremail" value="'.$speaker_info->user_email.'" required />
            </div>
            <div class="form-group" >
				<label for="firstname">' . esc_html__( 'First Name', 'fyvent' ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="firstname" id="firstname" value="'.$speaker_info->first_name.'" required />
            </div>
            <div class="form-group" >
				<label for="lastname">' . esc_html__( 'Last Name', 'fyvent' ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="lastname" id="lastname" value="'.$speaker_info->last_name.'" required />
            </div>
            <div class="form-group" >
				<label for="phone">' . esc_html__( 'Phone', 'fyvent' ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="phone" id="phone" value="'.$speaker_info->phone.'" required />
            </div>
            <div class="form-group" >';
            $selected = "";
            $option = $speaker_data['fyvent_speaker_gender'][0];
            $form.='<label for="gender">' . esc_html__( 'Gender', 'fyvent' ) . '</label>
	            <select class="form-control" name="gender" id="gender">';
					$selected = ( $option == 'male' )? 'selected' : '';
		            $form .= '<option value="male" '.$selected.'>'.esc_html__( 'Male', 'fyvent' ).'</option>';
					$selected = ( $option == 'female' )? 'selected' : '';
		            $form .= '<option value="female" '.$selected.'>'.esc_html__( 'Female', 'fyvent' ).'</option>';
		            $selected = ( $option == 'other' )? 'selected' : '';
		            $form .= '<option value="other" '.$selected.'>'.esc_html__( 'Other', 'fyvent' ).'</option>';
		            $selected = ( $option == 'dnda' )? 'selected' : '';
		            $form .= '<option value="dnda" '.$selected.'>'.esc_html__( 'I prefer not to say', 'fyvent' ).'</option>
				</select>
			</div>
            <div class="form-group" >
				<label for="position">' . esc_html__( 'Position', 'fyvent' ) . '</label>
                <input class="form-control" type="text" name="position" id="position" value="'.esc_attr( $speaker_data['fyvent_speaker_position'][0] ).'"  />
            </div>
            <div class="form-group" >
				<label for="organization">' . esc_html__( 'Organization', 'fyvent' ) . '</label>
                <input class="form-control" type="text" name="organization" id="organization" value="'.esc_attr( $speaker_data['fyvent_speaker_organization'][0] ).'"  />
            </div>
            <div class="form-group" >
				<label for="city">' . esc_html__( 'City', 'fyvent' ) . '</label>
                <input class="form-control" type="text" name="city" id="city" value="'.esc_attr( $speaker_data['fyvent_speaker_city'][0] ).'"  />
            </div>
            <div class="form-group" >
				<label for="country">' . esc_html__( 'Country', 'fyvent' ) . '</label>
                <input class="form-control" type="text" name="country" id="country" value="'.esc_attr( $speaker_data['fyvent_speaker_country'][0] ).'" />
            </div>
            <div class="form-group" >
				<label for="special_needs">' . esc_html__( 'Special Needs', 'fyvent' ) . '</label>
                <input class="form-control" type="text" name="special_needs" id="special_needs" value="'.esc_attr( $speaker_data['fyvent_speaker_special_needs'][0] ).'" />
            	<br/><span id="SpecialNeedsHelp" class="form-text text-muted">'.esc_html__( 'Do you have any dietary restriction or any other needs that we need to know about?', 'fyvent').'</span>
            </div>
			<div class="form-group py-2">
				<div class="row">';
				if( !empty( $speaker_data['fyvent_speaker_photo'][0] ) ){
					$form .= '<div class="col-md-2">
						<img src="'.esc_attr( $speaker_data['fyvent_speaker_photo'][0] ).'" alt="speaker photo" />
					</div>';
				}
				$form .= '<div class="col-md-10">
				    <label for="photo">'.esc_html__( 'Upload your photo', 'fyvent' ).'</label>
				    <input type="file" class="form-control-file" id="photo" name="photo">
					</div>
				</div>
			</div>
			<div class="form-group py-2">
				<div class="row">';
				if( !empty( $speaker_data['fyvent_speaker_presentation'][0] ) ){
					$form .= '<div class="col-md-5">
						<a href="'.esc_attr( $speaker_data['fyvent_speaker_presentation'][0] ).'">'.basename($speaker_data['fyvent_speaker_presentation'][0]).'</a>
					</div>';
				}
				$form .= '<div class="col-md-5">
				    <label for="presentation">'.esc_html__( 'Upload your presentation', 'fyvent' ).'</label>
				    <input type="file" class="form-control-file" id="presentation" name="presentation">
					</div>
				</div>
			</div>
			<div class="row my-3">
				<button '.fyvent_classes( 'button' ).' type="submit" name="submit" id="submit" >' . esc_attr( __( 'Submit', 'fyvent' ) ) . '</button>
			</div>
		</form>';

	echo wp_kses( $form, fyvent_allowed_tags() );

}