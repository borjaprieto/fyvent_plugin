<?php


function fyv_settings_init(  ) {

	register_setting( 'pluginPage', 'fyv_settings' );

	add_settings_section(
		'fyv_pluginPage_section',
		__( '', 'fyvent' ),
		'fyv_settings_section_callback',
		'pluginPage'
	);

	add_settings_field(
		'fyv_event_name',
		__( 'Event Name', 'fyvent' ),
		'fyv_event_name_render',
		'pluginPage',
		'fyv_pluginPage_section'
	);

	add_settings_field(
		'fyv_start_date',
		__( 'Start Date', 'fyvent' ),
		'fyv_start_date_render',
		'pluginPage',
		'fyv_pluginPage_section'
	);

	add_settings_field(
		'fyv_end_date',
		__( 'End Date', 'fyvent' ),
		'fyv_end_date_render',
		'pluginPage',
		'fyv_pluginPage_section'
	);

	add_settings_field(
		'fyv_tracks',
		__( 'Tracks', 'fyvent' ),
		'fyv_tracks_render',
		'pluginPage',
		'fyv_pluginPage_section'
	);

	add_settings_field(
		'fyv_attendant_types',
		__( 'Attendant types', 'fyvent' ),
		'fyv_attendant_types_render',
		'pluginPage',
		'fyv_pluginPage_section'
	);

	add_settings_field(
		'fyv_session_types',
		__( 'Session types', 'fyvent' ),
		'fyv_session_types_render',
		'pluginPage',
		'fyv_pluginPage_section'
	);


}


function fyv_event_name_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<input type='text' name='fyv_settings[fyv_event_name]' value='<?php echo $options['fyv_event_name']; ?>'>
	<?php

}


function fyv_start_date_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<input type='date' name='fyv_settings[fyv_start_date]' value='<?php echo $options['fyv_start_date']; ?>'>
	<?php

}


function fyv_end_date_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<input type='date' name='fyv_settings[fyv_end_date]' value='<?php echo $options['fyv_end_date']; ?>'>
	<?php

}


function fyv_tracks_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<label for="fyv_settings[fyv_tracks]"><?php echo  __( 'If your event has different tracks, write them here separated by commas', 'wpdecide' ); ?></label>
	<input type='text' name='fyv_settings[fyv_tracks]' value='<?php echo $options['fyv_tracks']; ?>'>
	<?php

}


function fyv_attendant_types_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<label for="fyv_settings[fyv_attendant_types]"><?php echo  __( 'If your event has different types of attendants, write them here separated by commas', 'wpdecide' ); ?></label>
	<input type='text' name='fyv_settings[fyv_attendant_types]' value='<?php echo $options['fyv_attendant_types']; ?>'>
	<?php

}


function fyv_session_types_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<label for="fyv_settings[fyv_session_types]"><?php echo  __( 'If your event has different types of sessions, write them here separated by commas', 'wpdecide' ); ?></label>
	<input type='text' name='fyv_settings[fyv_session_types]' value='<?php echo $options['fyv_session_types']; ?>'>
	<?php

}


function fyv_settings_section_callback(  ) {

//	echo __( 'This section description', 'fyvent' );

}


function fyv_options_page(  ) {

	?>
	<h1>Fyvent Options</h1>

	<form action='options.php' method='post'>
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}
