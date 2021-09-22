<?php

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
 * Hook in and add a metabox to add fields to the user profile pages
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

	if(  current_user_can( 'edit_users' ) ){

		$options = get_option('fyv_settings');
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
add_action( 'cmb2_admin_init', 'fyv_register_attendant_profile_metabox' );

function fyv_register_attendant(){

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
				$user->set_role( 'attendant' );

				$firstname = sanitize_text_field( $_POST['firstname'] );
				update_user_meta( $user_id, 'first_name', $firstname );
				$lastname = sanitize_text_field( $_POST['lastname'] );
				update_user_meta( $user_id, 'last_name', $lastname );
				$gender = $_POST['gender'];
				add_user_meta( $user_id, 'fyv_attendant_gender', $gender );
				$position = sanitize_text_field( $_POST['position'] );
				add_user_meta( $user_id, 'fyv_attendant_position', $position );
				$organization = sanitize_text_field( $_POST['organization'] );
				add_user_meta( $user_id, 'fyv_attendant_organization', $organization);
				$city = sanitize_text_field( $_POST['city'] );
				add_user_meta( $user_id, 'fyv_attendant_city', $city );
				$country = sanitize_text_field( $_POST['country'] );
				add_user_meta( $user_id, 'fyv_attendant_country', $country );
				$gpdr= true;
				add_user_meta( $user_id, 'fyv_attendant_gpdr', $gpdr );

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
		echo '<h3">' . __( 'You are registered now', 'fyvent' ) . '</h3>';
		echo '<p><a href="/login/">';
		echo '<button>' . __( 'Log In', 'fyvent' ) . '</button></a>';
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

function fyv_attendant_register_form(){

	$form = '
    	<form action="' . htmlentities( $_SERVER['REQUEST_URI'] ) . '" method="post">
			<div>
				<label for="username">' . esc_html( __( 'Username', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input type="text" name="username" id="username" value="" />
            </div>
        	<div>
				<label for="useremail">' . esc_html( __( 'Email Address', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input type="email" name="useremail" id="useremail" value="" />
            </div>
            <div>
                <label for="password">' . esc_html( __( 'Password', 'fyvent' ) ) . '<span style="color:red;">*</span></label>
                <input type="password" name="password" id="password" value=""  />
            </div>
            <div>
				<label for="firstname">' . esc_html( __( 'First Name', 'fyvent' ) ) . '</label>
                <input type="text" name="firstname" id="firstname" value="" />
            </div>
            <div>
				<label for="lastname">' . esc_html( __( 'Last Name', 'fyvent' ) ) . '</label>
                <input type="text" name="lastname" id="lastname" value="" />
            </div>
            <div>
            <label for="gender">' . esc_html( __( 'Gender', 'fyvent' ) ) . '</label>
            <select name="gender" id="gender">
	            <option value="male">'.__( 'Male', 'fyvent' ).'</option>
	            <option value="female">'.__( 'Female', 'fyvent' ).'</option>
	            <option value="other">'.__( 'Other', 'fyvent' ).'</option>
	            <option value="dnda">'.__( 'I prefer not to say', 'fyvent' ).'</option>
			</select>
			</div>
			<div>
				<label for="position">' . esc_html( __( 'Position', 'fyvent' ) ) . '</label>
                <input type="text" name="position" id="position" value="" />
            </div>
            <div>
				<label for="organization">' . esc_html( __( 'Organization', 'fyvent' ) ) . '</label>
                <input type="text" name="organization" id="organization" value="" />
            </div>
            <div>
				<label for="city">' . esc_html( __( 'City', 'fyvent' ) ) . '</label>
                <input type="text" name="city" id="city" value="" />
            </div>
            <div>
				<label for="country">' . esc_html( __( 'Country', 'fyvent' ) ) . '</label>
                <input type="text" name="country" id="country" value="" />
            </div>
	        <div>
				<input type="checkbox" id="check-terms" required>
				<label for="check-terms">' .
				__( 'I agree with the the <a href="/privacy">Privacy Policy</a>.', 'fyvent' ) .
				'</label>
			</div>

			<button type="submit" name="submit" id="submit" >' . esc_attr( __( 'Register', 'fyvent' ) ) . '</button>
		</form>';

	echo $form;
}