<?php

function fyv_login(){

	if ( is_user_logged_in() ) {
		//They're already logged in, so we bounce them back to the homepage.
		echo '<script type="text/javascript">
			window.location = "'.get_home_url().'"
			</script>';
	}

	// If the user has posted something, let's process it
	if ( $_POST ) {
		global $wpdb;

		//We shall SQL escape all inputs
		$username = $wpdb->escape( $_REQUEST['log'] );
		$password = $wpdb->escape( $_REQUEST['pwd'] );
		$remember = $wpdb->escape( $_REQUEST['remember'] );

		if ( $remember ) {
			$remember = 'true';
		} else {
			$remember = 'false';
		}

		$login_data = [];
		$login_data['user_login'] = $username;
		$login_data['user_password'] = $password;
		$login_data['remember'] = $remember;

		if ( wp_authenticate( $username, $password ) ) {
			$user = wp_signon( $login_data, is_ssl() );
			if ( is_wp_error( $user ) ) {
				$error = __( 'Invalid login details', 'fyvent' );
				fyv_show_front_messages( '', $user->get_error_message() );
			} else {

				wp_clear_auth_cookie();
            	do_action('wp_login', $user->ID);
            	wp_set_current_user($user->ID);
            	wp_set_auth_cookie($user->ID, true);
            	$redirect_to = get_home_url();
            	wp_safe_redirect($redirect_to);
//            	exit;
/*
				var_dump( $user_verify );
				wp_set_current_user( $user_verify->ID, $user_verify->user_login );
    			wp_set_auth_cookie( $user_verify->ID );
				do_action( 'wp_login', $user_verify->user_login, $user_verify );
				if ( ! is_user_logged_in() ) {
					echo "user is not logged";
				} else {
					echo "user is logged";
				}
*/
				//echo '<script type="text/javascript">window.location = "'.get_home_url().'"</script>';
			}
		} else {
			$error = __( 'Login or Password not valid', 'fyvent' );
			fyv_show_front_messages( '', $error );
		}
	} else {
	//	fyv_login_form();
	}
}

// Run before the headers and cookies are sent.
//add_action( 'after_setup_theme', 'fyv_login' );


function fyv_login_form(){
	$form = '

		<form name="loginform" id="loginform" action="'.site_url( '/login/' ).'" method="post">
			<div class="form-group">
				<label for="user_login">' . esc_html( __( 'Username or Email Address', 'fyvent' ) ) . '</label>
				<input type="text" name="log" id="user_login" class="form-control" value="" />
			</div>
			<div class="form-group">
				<label for="user_pass">' . esc_html( __( 'Password', 'fyvent' ) ) . '</label>
				<input type="user_pass" name="pwd" id="user_pass" class="form-control" value=""  />
			</div>
			<div class="form-group form-check">
				<input name="remember" type="checkbox" id="remember" value="forever" class="form-check-input" checked="checked" />
				<label class="form-check-label" >' . esc_html( __( 'Remember Me', 'fyvent' ) ) . '
				</label>
			</div>
			<input type="hidden" value="'.get_home_url().'" name="redirect_to">
			<button type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary btn-block" >' . esc_attr( __( 'Log In', 'fyvent' ) ) . '</button>
		</form>';

	echo $form;
}

add_action("login_form", "kill_wp_attempt_focus");
function kill_wp_attempt_focus() {
    global $error;
    $error = TRUE;
}