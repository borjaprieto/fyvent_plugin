<?php

/**
 * Creates shortcode to show Event name
 *
 * @since 1.0.0
**/
function fyvent_event_name() {
	$options = get_option('fyv_settings');
	$event_name = $options['fyv_event_name'];
	return $event_name;
}
add_shortcode( 'event-name', 'fyvent_event_name' );

// Shows and process a register form for attendants
add_shortcode( 'attendant-register', 'fyv_register_attendant' );
// Shows and process a register form for speakers
add_shortcode( 'speaker-register', 'fyv_register_speaker' );
// Shows and process a login form
//add_shortcode( 'fyvent-login', 'fyv_login_form' );