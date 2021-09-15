<?php

/**
 * Create shortcode to show sites name
 *
 * @since 1.0.0
**/
function fyvent_site_name() {
	$site_name = carbon_get_theme_option( 'fyvent_site_name', false );
	if ( $site_name ) {
		return $site_name;
	} else {
		return esc_html__( 'The Greatest Event', 'fyvent' );
	}
}
add_shortcode( 'site-name', 'fyvent_site_name' );

