<?php

/**
 * General Options for Fyvent
 *
 * @since 1.0.0
**/

$messages = [];
$warnings = [];
$errors = [];

// Process submission of account information
$updated = fyv_update_options();
$message = $updated['message'];
$error = $updated['error'];

fyv_options_header();
fyv_show_admin_messages( $message, $error );
fyv_options_form();



function fyv_options_form(){
	?>
	<form action="<?php echo htmlentities( $_SERVER['REQUEST_URI'] ); ?>" method="post">
		<div class="form-group">
			<label for="eventname"><?php echo esc_html( __( 'Event Name', 'fyvent' ) ); ?></label>
			<input type="text" name="eventname" id="eventname" class="form-control" value="<?php echo get_option('eventname'); ?>" />
		</div>
		<button type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary btn-block" ><?php echo esc_attr( __( 'Save', 'fyvent' ) ); ?></button>
	</form>
	<?php
}

function fyv_options_header(){
	echo '<h1>Fyvent Options</h1>';
}

function fyv_update_options() {

	$returned = [
			'message' => '',
			'error' => '',
	];

	if( isset( $_POST['eventname'] ) ){
		$eventname = $_POST['eventname'];
		update_option( 'eventname', $eventname, true );
		$returned['message'] = "Options updated";
	} else {
		$eventname = '';
	}

	return $returned;
}

