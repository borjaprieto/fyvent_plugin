<?php

/**
 * Creates shortcode to show Event name
 *
 * @return  string  name of event
 *
 * @since  1.0.0
**/
function fyvent_event_name() {
	$options = get_option('fyvent_settings');
	$event_name = $options['fyvent_event_name'];
	return $event_name;
}
add_shortcode( 'fyvent-event-name', 'fyvent_event_name' );

// Shows and process a register form for attendants
add_shortcode( 'fyvent-attendant-register', 'fyvent_register_attendant' );

// Shows and process a register form for speakers
add_shortcode( 'fyvent-speaker-register', 'fyvent_register_speaker' );

// Shows sessions information
add_shortcode( 'fyvent-session', 'fyvent_show_session_shortcode' );

// Shows speakers information
add_shortcode( 'fyvent-speaker', 'fyvent_show_speaker_shortcode' );

// Shows and process a form for speakers to update their information
add_shortcode( 'fyvent-speaker-information', 'fyvent_speaker_information_form' );

// Shows a link to update information page for attendants
add_shortcode( 'fyvent-update-info', 'fyvent_update_attendant' );

// Shows a link to update information page for attendants
add_shortcode( 'fyvent-venue', 'fyvent_show_venue' );