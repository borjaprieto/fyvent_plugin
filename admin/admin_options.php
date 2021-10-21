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

	add_settings_field(
		'fyv_presentation_download',
		__( 'Allow Presentation Download', 'fyvent' ),
		'fyv_presentation_download_render',
		'pluginPage',
		'fyv_pluginPage_section',
		['type' => 'checkbox'],
	);

	add_settings_field(
		'fyv_use_bootstrap',
		__( 'Use Bootstrap', 'fyvent' ),
		'fyv_use_bootstrap_render',
		'pluginPage',
		'fyv_pluginPage_section',
		['type' => 'checkbox'],
	);

	add_settings_field(
		'fyv_session_privacy_page',
		__( 'Privacy Page', 'fyvent' ),
		'fyv_privacy_page_render',
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
	<label for="fyv_settings[fyv_tracks]"><?php echo  __( 'If your event has different tracks, write them here separated by commas', 'fyvent' ); ?></label>
	<input type='text' name='fyv_settings[fyv_tracks]' value='<?php echo $options['fyv_tracks']; ?>'>
	<?php

}

function fyv_attendant_types_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<label for="fyv_settings[fyv_attendant_types]"><?php echo  __( 'If your event has different types of attendants, write them here separated by commas', 'fyvent' ); ?></label>
	<input type='text' name='fyv_settings[fyv_attendant_types]' value='<?php echo $options['fyv_attendant_types']; ?>'>
	<?php

}

function fyv_session_types_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<label for="fyv_settings[fyv_session_types]"><?php echo  __( 'If your event has different types of sessions, write them here separated by commas', 'fyvent' ); ?></label>
	<input type='text' name='fyv_settings[fyv_session_types]' value='<?php echo $options['fyv_session_types']; ?>'>
	<?php

}

function fyv_presentation_download_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<label for="fyv_settings[fyv_presentation_download]"><?php echo  __( 'Do you allow Presentations download from Session or Speaker pages?', 'fyvent' ); ?></label>
	<?php
	$checked = array_key_exists( 'fyv_presentation_download', $options ) ? 'checked' : '';
	echo '<input type="checkbox" name="fyv_settings[fyv_presentation_download]" '.$checked.'>';?>
	<?php

}

function fyv_use_bootstrap_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<label for="fyv_settings[fyv_use_bootstrap]"><?php echo  __( 'Do you want to use Bootstrap if your Theme also uses it?', 'fyvent' ); ?></label>
	<?php
	$checked = array_key_exists( 'fyv_use_bootstrap', $options ) ? 'checked' : '';
	echo '<input type="checkbox" name="fyv_settings[fyv_use_bootstrap]" '.$checked.'>'; ?>
	<?php

}

function fyv_privacy_page_render(  ) {

	$options = get_option( 'fyv_settings' );
	?>
	<label for="fyv_settings[fyv_privacy_page]"><?php echo  __( 'Input here the slug of your privacy page (e.g. "/privacy")', 'fyvent' ); ?></label>
	<input type='text' name='fyv_settings[fyv_privacy_page]' value='<?php echo $options['fyv_privacy_page']; ?>'>
	<?php

}


function fyv_settings_section_callback(  ) {

//	echo __( 'This section description', 'fyvent' );

}

function fyv_messages_init(  ) {

	add_settings_section(
		'fyv_attendant_messages_section',
		__( 'Attendant Messages', 'fyvent' ),
		'fyv_settings_section_callback',
		'messagesPage'
	);

	add_settings_field(
		'fyv_attendant_registration_user_created',
		__( 'User created', 'fyvent' ),
		'fyv_attendant_registration_user_created_render',
		'messagesPage',
		'fyv_attendant_messages_section',
		['label_for' => 'fyv_attendant_registration_user_created'],
	);
	register_setting( 'messagesPage', 'fyv_attendant_registration_user_created', ['type' => 'string', 'default' => 'Your user has been created.'] );

	add_settings_field(
		'fyv_attendant_privacy_agreement',
		__( 'Privacy Agreement', 'fyvent' ),
		'fyv_attendant_privacy_agreement_render',
		'messagesPage',
		'fyv_attendant_messages_section',
		['label_for' => 'fyv_attendant_privacy_agreement'],
	);
	register_setting( 'messagesPage', 'fyv_attendant_privacy_agreement', ['type' => 'string', 'default' => 'I agree with the <a href="/privacy">Privacy Policy</a>.'] );

	add_settings_field(
		'fyv_attendant_registered',
		__( 'Attendant registered successfully', 'fyvent' ),
		'fyv_attendant_registered_render',
		'messagesPage',
		'fyv_attendant_messages_section',
		['label_for' => 'fyv_attendant_registered'],
	);
	register_setting( 'messagesPage', 'fyv_attendant_registered', ['type' => 'string', 'default' => 'You have been registered.'] );

	add_settings_field(
		'fyv_attendant_user_exists',
		__( 'User already exists', 'fyvent' ),
		'fyv_attendant_user_exists_render',
		'messagesPage',
		'fyv_attendant_messages_section',
		['label_for' => 'fyv_attendant_user_exists'],
	);
	register_setting( 'messagesPage', 'fyv_attendant_user_exists', ['type' => 'string', 'default' => 'The username or email is already in use.'] );

	add_settings_section(
		'fyv_speaker_messages_section',
		__( 'Speaker Messages', 'fyvent' ),
		'fyv_settings_section_callback',
		'messagesPage'
	);

	add_settings_field(
		'fyv_speaker_registration_user_created',
		__( 'User created', 'fyvent' ),
		'fyv_speaker_registration_user_created_render',
		'messagesPage',
		'fyv_speaker_messages_section',
		['label_for' => 'fyv_speaker_registration_user_created'],
	);
	register_setting( 'messagesPage', 'fyv_speaker_registration_user_created', ['type' => 'string', 'default' => 'Your user has been created.'] );

	add_settings_field(
		'fyv_speaker_privacy_agreement',
		__( 'Privacy Agreement', 'fyvent' ),
		'fyv_speaker_privacy_agreement_render',
		'messagesPage',
		'fyv_speaker_messages_section',
		['label_for' => 'fyv_speaker_privacy_agreement'],
	);
	register_setting( 'messagesPage', 'fyv_speaker_privacy_agreement', ['type' => 'string', 'default' => 'I agree with the <a href="/privacy">Privacy Policy</a>.'] );

	add_settings_field(
		'fyv_speaker_registered',
		__( 'Speaker registered successfully', 'fyvent' ),
		'fyv_speaker_registered_render',
		'messagesPage',
		'fyv_speaker_messages_section',
		['label_for' => 'fyv_speaker_registered'],
	);
	register_setting( 'messagesPage', 'fyv_speaker_registered', ['type' => 'string', 'default' => 'You have been registered.'] );

	add_settings_field(
		'fyv_speaker_user_exists',
		__( 'User already exists', 'fyvent' ),
		'fyv_speaker_user_exists_render',
		'messagesPage',
		'fyv_speaker_messages_section',
		['label_for' => 'fyv_speaker_user_exists'],
	);
	register_setting( 'messagesPage', 'fyv_speaker_user_exists', ['type' => 'string', 'default' => 'The username or email is already in use.'] );

	add_settings_field(
		'fyv_speaker_complete_info',
		__( 'Complete Speaker info', 'fyvent' ),
		'fyv_speaker_complete_info_render',
		'messagesPage',
		'fyv_speaker_messages_section',
		['label_for' => 'fyv_speaker_complete_info'],
	);
	register_setting( 'messagesPage', 'fyv_speaker_complete_info', ['type' => 'string', 'default' => 'Update Info'] );

	add_settings_field(
		'fyv_speaker_more_info',
		__( 'More info for Speaker', 'fyvent' ),
		'fyv_speaker_more_info_render',
		'messagesPage',
		'fyv_speaker_messages_section',
		['label_for' => 'fyv_speaker_more_info'],
	);
	register_setting( 'messagesPage', 'fyv_speaker_more_info', ['type' => 'string', 'default' => 'Please complete all your info.'] );

	add_settings_field(
		'fyv_speaker_private_fields',
		__( 'Speaker private fields', 'fyvent' ),
		'fyv_speaker_private_fields_render',
		'messagesPage',
		'fyv_speaker_messages_section',
		['label_for' => 'fyv_speaker_private_fields'],
	);
	register_setting( 'messagesPage', 'fyv_speaker_private_fields', ['type' => 'string', 'default' => 'All info will be private except stated otherwise.'] );

	add_settings_field(
		'fyv_speaker_public_field',
		__( 'Public field', 'fyvent' ),
		'fyv_speaker_public_field_render',
		'messagesPage',
		'fyv_speaker_messages_section',
		['label_for' => 'fyv_speaker_public_field'],
	);
	register_setting( 'messagesPage', 'fyv_speaker_public_field', [ 'type' => 'string', 'default' => 'This field will be visible by other people.' ] );

}

function fyv_attendant_registration_user_created_render(  ) {
	?>
	<label for="fyv_attendant_registration_user_created"><?php echo  __( 'Message for successful user creation', 'fyvent' ); ?></label>
	<input type='text'class="large-text"  name='fyv_attendant_registration_user_created' value='<?php echo get_option( 'fyv_attendant_registration_user_created' ); ?>'>
	<?php
}

function fyv_attendant_privacy_agreement_render(  ) {
	?>
	<label for="fyv_attendant_privacy_agreement"><?php echo  __( 'Text to show in privacy agreement check', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_attendant_privacy_agreement' value='<?php echo get_option( 'fyv_attendant_privacy_agreement' ); ?>'>
	<?php
}

function fyv_attendant_registered_render(  ) {
	?>
	<label for="fyv_attendant_registered"><?php echo  __( 'Message for successful user registration', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_attendant_registered' value='<?php echo get_option( 'fyv_attendant_registered' ); ?>'>
	<?php
}

function fyv_attendant_user_exists_render(  ) {
	?>
	<label for="fyv_attendant_user_exists"><?php echo  __( 'Error message for user already exists', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_attendant_user_exists' value='<?php echo get_option( 'fyv_attendant_user_exists' ); ?>'>
	<?php
}

function fyv_speaker_registration_user_created_render(  ) {
	?>
	<label for="fyv_speaker_registration_user_created"><?php echo  __( 'Message for successful user creation', 'fyvent' ); ?></label>
	<input type='text'class="large-text"  name='fyv_speaker_registration_user_created' value='<?php echo get_option( 'fyv_speaker_registration_user_created' ); ?>'>
	<?php
}

function fyv_speaker_privacy_agreement_render(  ) {
	?>
	<label for="fyv_speaker_privacy_agreement"><?php echo  __( 'Text to show in privacy agreement check', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speakert_privacy_agreement' value='<?php echo get_option( 'fyv_speaker_privacy_agreement' ); ?>'>
	<?php
}

function fyv_speaker_registered_render(  ) {
	?>
	<label for="fyv_speaker_registered"><?php echo  __( 'Message for successful user registration', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_registered' value='<?php echo get_option( 'fyv_speaker_registered' ); ?>'>
	<?php
}

function fyv_speaker_user_exists_render(  ) {
	?>
	<label for="fyv_speaker_user_exists"><?php echo  __( 'Error message for user already exists', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_user_exists' value='<?php echo get_option( 'fyv_speaker_user_exists' ); ?>'>
	<?php
}

function fyv_speaker_more_info_render(  ) {
	?>
	<label for="fyv_speaker_more_info"><?php echo  __( 'Prompt to ask Speaker to complete their info', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_more_info' value='<?php echo get_option( 'fyv_speaker_more_info' ); ?>'>
	<?php
}

function fyv_speaker_complete_info_render(  ) {
	?>
	<label for="fyv_speaker_complete_info"><?php echo  __( 'Text for the button to ask the Speaker to complete their info', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_complete_info' value='<?php echo get_option( 'fyv_speaker_complete_info' ); ?>'>
	<?php
}

function fyv_speaker_private_fields_render(  ) {
	?>
	<label for="fyv_speaker_private_fields"><?php echo  __( 'Message for speaker that their info is private', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_private_fields' value='<?php echo get_option( 'fyv_speaker_private_fields' ); ?>'>
	<?php
}

function fyv_speaker_public_field_render(  ) {
	?>
	<label for="fyv_speaker_public_field"><?php echo  __( 'Message to explain that a field will be visible for the public', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_public_field' value='<?php echo get_option( 'fyv_speaker_public_field' ); ?>'>
	<?php
}

function fyv_options_page(  ) {

	//Get the active tab from the $_GET param
	$default_tab = 'settings';
	$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

	?>
	<h1>Fyvent Options</h1>
	<nav class="nav-tab-wrapper">
		<a href="?page=fyv_options&tab=settings" class="nav-tab <?php if( $tab===null ):?>nav-tab-active<?php endif; ?>">
			<?php echo __( 'Event Settings', 'fyvent' ); ?>
		</a>
		<a href="?page=fyv_options&tab=messages" class="nav-tab <?php if( $tab==='messages' ):?>nav-tab-active<?php endif; ?>">
			<?php echo __( 'Messages', 'fyvent' ); ?>
		</a>
		<a href="?page=fyv_options&tab=more" class="nav-tab <?php if( $tab==='more' ):?>nav-tab-active<?php endif; ?>">
			<?php echo __( 'More Info', 'fyvent' ); ?>
		</a>
	</nav>

    <div class="tab-content">
    <?php switch( $tab ) {
		case 'settings':
			echo "<form action='options.php' method='post'>";
				settings_fields( 'pluginPage' );
				do_settings_sections( 'pluginPage' );
				submit_button();
			echo '</form>';
			break;
		case 'messages':
			echo "<form action='options.php' method='post'>";
				settings_fields( 'messagesPage' );
				do_settings_sections( 'messagesPage' );
				submit_button();
			echo '</form>';
			break;
		case 'more':
			echo "Please consider a donation to help future development of Fyvent plugin";
			echo '<br/>';
			if( fyv_theme_uses_bootstrap() ){
				echo "bootstrap";
			} else {
				echo "other css";
			}
			break;

		default:
			break;
	}
}
