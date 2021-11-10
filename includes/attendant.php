<?php

/**
 * Adds attendant role.
 *
 * @since 1.0.0
 */
function fyv_attendant_role() {

    //add the attendant role
    add_role(
        'attendant',
        'Attendant',
        array(
            'read'         => true,
        )
    );

}
add_action('admin_init', 'fyv_attendant_role');

/**
 * Hooks in and adds a metabox to add fields to the user profile pages
 *
 * @params string $user_id ID of the user whose profile we are showing
 *
 * @since 1.0.0
 *
 */
function fyv_register_attendant_profile_metabox( $user_id ) {

	$prefix = 'fyv_attendant_';

	/**
	 * Metabox for the user profile screen
	 */
	$cmb_user = new_cmb2_box( array(
		'id'               => $prefix . 'edit',
		'title'            => __( 'Attendant Information', 'fyvent' ), // Doesn't output for user boxes
		'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
		'new_user_section' => 'add-existing-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
		'show_on_cb'	=> 'fyv_show_meta_to_chosen_roles',
		'show_on_roles' => array( 'attendant' ),
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

	//Shows some fields only if the user accesing the profile has admin permissions
	if(  current_user_can( 'edit_users' ) ){

		$options = get_option('fyv_settings');
		if ( $options ){
			if( $options['fyv_attendant_types'] != "" ){
				$attendant_types = array_map( 'trim', explode( ',', $options['fyv_attendant_types'] ) );
				$cmb_user->add_field(
					[
					    'name'             => esc_html__( 'Type of attendant', 'fyvent' ),
					    'desc'             => esc_html__( 'Select the type of attendant', 'fyvent' ),
					    'id'               => 'type',
					    'type'             => 'select',
					    'show_option_none' => false,
					    'options'          => $attendant_types,
					]
				);
			}
			$cmb_user->add_field( array(
				'name' => __( 'Attended', 'fyvent' ),
				'id'   => $prefix . 'attended',
				'type' => 'checkbox',
			) );
			$cmb_user->add_field( array(
				'name' => __( 'Paid', 'fyvent' ),
				'id'   => $prefix . 'paid',
				'type' => 'checkbox',
			) );
		}
	}


}
add_action( 'cmb2_admin_init', 'fyv_register_attendant_profile_metabox' );

/**
 * Registers the user as attendant if they have filled the register form
 *
 * @since 1.0.0
 *
 */
function fyv_register_attendant(){

	//If user is logged in, go to Home, they don't need to register
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
				$message = get_option( 'fyv_attendant_registration_user_created', 'Your user has been created.' );
				fyv_show_front_messages( $message, '' );
				$registered = true;
				$user = new WP_User( $user_id );
				$user->set_role( 'attendant' );
				fyv_update_user_data( 'first_name', $_POST['firstname'] );
				fyv_update_user_data( 'last_name', $_POST['lastname'] );
				fyv_update_user_data( 'gender', $_POST['gender'] );
				fyv_update_user_data( 'position', $_POST['position'] );
				fyv_update_user_data( 'organization', $_POST['organization'] );
				fyv_update_user_data( 'city', $_POST['city'] );
				fyv_update_user_data( 'country', $_POST['country'] );
				$gpdr= true;
				add_user_meta( $user_id, 'fyv_attendant_gpdr', $gpdr );

			} else {
				$error = $user_id->get_error_messages();
				if( is_array( $error ) ){
					foreach( $error as $error_msg){
						fyv_show_messages( '', $error_msg );
					}
				} else {
					fyv_show_messages( '', $error );
				}
			}
		} else {
			$error = get_option( 'fyv_attendant_user_exists', 'The username or email is already in use.' );
			fyv_show_front_messages( '', $error );
		}
	}
	//if registering was succesful show a message to tell the user, or else show the register form
	if ( $registered ) {
		echo '<div style="margin: auto;">';
		echo '<h3>' . get_option( 'fyv_attendant_registered', 'You have been registered.' ) . '</h3>';
		echo '<p style="margin-top:12px;"><a href="/login/">';
		echo '<button '.fyv_classes( 'button' ).'>'. __( 'Log In', 'fyvent' ) . '</button></a>';
		echo '</p></div>';

	} else {
		echo '<div>';
		$options = get_option( 'fyv_settings' );
		echo '<h3>Register for '.$options['fyv_event_name'];
		echo '</h3></div>';
		echo '<div>';
		fyv_attendant_register_form();
		echo '</div>';
	}

}

/**
 * Renders the attendant register form
 *
 * @since 1.0.0
 *
 */
function fyv_attendant_register_form(){

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
				<label for="firstname">' . esc_html( __( 'First Name', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="firstname" id="firstname" value="" />
            </div>
            <div class="form-group" >
				<label for="lastname">' . esc_html( __( 'Last Name', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="lastname" id="lastname" value="" />
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
			<div class="form-group" >
				<label for="position">' . esc_html( __( 'Position', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="position" id="position" value="" />
            </div>
            <div class="form-group" >
				<label for="organization">' . esc_html( __( 'Organization', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="organization" id="organization" value="" />
            </div>
            <div class="form-group" >
				<label for="city">' . esc_html( __( 'City', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="city" id="city" value="" />
            </div>
            <div class="form-group" >
				<label for="country">' . esc_html( __( 'Country', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="country" id="country" value="" />
            </div>
	        <div class="form-check" >
				<input  class="form-check-input" type="checkbox" id="check-terms" required>
				<label  class="form-check-label" for="check-terms">';
				$options = get_option( 'fyv_settings', 'fyv_privacy_page' );
				$option = $options ? $options['fyv_privacy_page'] : '/privacy';
				$form .= get_option( 'fyv_attendant_privacy_agreement', 'I agree with the <a href="'.$option.'">Privacy Policy</a>.' ) .
				'</label>
			</div>
			<div  class="form-group" >
				<button '.fyv_classes( 'button' ).' type="submit" name="submit" id="submit" >' . esc_attr( __( 'Register', 'fyvent' ) ) . '</button>
			</div>
		</form>';

	echo $form;
}




/**
 * Updates the attendant info and meta data
 *
 * @since 1.0.0
 *
 */
function fyv_update_attendant(){

/**	if( !fyv_is_user_attendant() ){
		echo '<script type="text/javascript">window.location = "'.get_home_url().'"</script>';
	} else {
*/
		if ( isset( $_POST['submit'] ) ) {

			$message = '';
			$error = '';

			$display_name = sanitize_text_field( $_POST['display_name'] );
			$email = sanitize_text_field( $_POST['useremail'] );
			$password = $_POST['password'];
			$last_name = sanitize_text_field( $_POST['lastname'] );
			$first_name = sanitize_text_field( $_POST['firstname'] );

			$update_data = [
				'ID' => get_current_user_id(),
				'user_pass' => $password,
				'user_email' => $email,
				'last_name' => $last_name,
				'first_name' => $first_name,
				'display_name' => $display_name,
			];

			$user_data = wp_update_user( $update_data );

			if ( ! is_wp_error( $user_data ) ) {
				fyv_update_user_data( 'fyv_attendant_gender', $_POST['gender'] );
				fyv_update_user_data( 'fyv_attendant_position', $_POST['position'] );
				fyv_update_user_data( 'fyv_attendant_organization', $_POST['organization'] );
				fyv_update_user_data( 'fyv_attendant_city', $_POST['city'] );
				fyv_update_user_data( 'fyv_attendant_country', $_POST['country'] );
				$message = __('Your information has been updated', 'fyvent' );
				fyv_show_front_messages( $message, '' );
			} else {
				$error = $user_data->get_error_messages();
				if( is_array( $error ) ){
					foreach( $error as $error_msg){
						fyv_show_front_messages( '', $error_msg );
					}
				} else {
					fyv_show_front_messages( '', $error );
				}
			}

		} else {
			fyv_attendant_update_form();
		}
//	}
}

/**
 * Renders the attendant update form
 *
 * @since 1.0.0
 *
 */
function fyv_attendant_update_form(){

	$user = get_userdata( get_current_user_id() );

	$form = '
    	<form action="' . htmlentities( $_SERVER['REQUEST_URI'] ) . '" method="post">
			<div class="form-group" >
				<label for="username">' . esc_html( __( 'Username', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="text" name="username" id="username" value="'.$user->user_login.'" disabled />
            </div>
        	<div class="form-group" >
				<label for="useremail">' . esc_html( __( 'Email Address', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="email" name="useremail" id="useremail" value="'.$user->user_email.'" required />
            </div>
            <div class="form-group" >
                <label for="password">' . esc_html( __( 'Password', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input class="form-control" type="password" name="password" id="password" value="'.$user->user_pass.'"  required />
            </div>
            <div class="form-group" >
				<label for="firstname">' . esc_html( __( 'First Name', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="firstname" id="firstname" value="'.$user->first_name.'" />
            </div>
            <div class="form-group" >
				<label for="lastname">' . esc_html( __( 'Last Name', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="lastname" id="lastname" value="'.$user->last_name.'" />
            </div>
            <div class="form-group" >';
            $selected = "";
            $option = get_user_meta( $user->id, 'fyv_attendant_gender', true );
            $form.='<label for="gender">' . esc_html( __( 'Gender', 'fyvent' ) ) . '</label>
            <select class="form-control" name="gender" id="gender">';
            	$selected = ( $option == 'male' )? 'selected' : '';
	            $form .= '<option value="male" '.$selected.'>'.__( 'Male', 'fyvent' ).'</option>';
				$selected = ( $option == 'female' )? 'selected' : '';
	            $form .= '<option value="female" '.$selected.'>'.__( 'Female', 'fyvent' ).'</option>';
	            $selected = ( $option == 'other' )? 'selected' : '';
	            $form .= '<option value="other" '.$selected.'>'.__( 'Other', 'fyvent' ).'</option>';
	            $selected = ( $option == 'dnda' )? 'selected' : '';
	            $form .= '<option value="dnda" '.$selected.'>'.__( 'I prefer not to say', 'fyvent' ).'</option>
			</select>
			</div>
			<div class="form-group" >
				<label for="position">' . esc_html( __( 'Position', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="position" id="position" value="'.get_user_meta( $user->id, 'fyv_attendant_position', true).'" />
            </div>
            <div class="form-group" >
				<label for="organization">' . esc_html( __( 'Organization', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="organization" id="organization" value="'.get_user_meta( $user->id, 'fyv_attendant_organization', true).'" />
            </div>
            <div class="form-group" >
				<label for="city">' . esc_html( __( 'City', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="city" id="city" value="'.get_user_meta( $user->id, 'fyv_attendant_city', true).'" />
            </div>
            <div class="form-group" >
				<label for="country">' . esc_html( __( 'Country', 'fyvent' ) ) . '</label>
                <input class="form-control" type="text" name="country" id="country" value="'.get_user_meta( $user->id, 'fyv_attendant_country', true).'" />
            </div>
			<button '.fyv_classes( 'button' ).' type="submit" name="submit" id="submit" >' . esc_attr( __( 'Update', 'fyvent' ) ) . '</button>
		</form>';

	echo $form;
}