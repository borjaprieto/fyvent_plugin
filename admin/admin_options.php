<?php

/**
 * Initialices settings fields.
 * These are settings for event options
 *
 * @since 1.0.0
 */
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

	add_settings_field(
		'fyv_speaker_info_page',
		__( 'Speaker Information Page', 'fyvent' ),
		'fyv_speaker_info_page_render',
		'pluginPage',
		'fyv_pluginPage_section'
	);

}

/**
 * Renders fyv_event_name field.
 *
 * @since 1.0.0
 */
function fyv_event_name_render(  ) {

	$options = get_option( 'fyv_settings' );
	$option = esc_html( $options ? $options['fyv_event_name'] : '' );
	?>
	<input type='text' name='fyv_settings[fyv_event_name]' value='<?php echo $option; ?>'>
	<?php

}

/**
 * Renders fyv_start_date field.
 *
 * @since 1.0.0
 */
function fyv_start_date_render(  ) {

	$options = get_option( 'fyv_settings' );
	$option = esc_html( $options ? $options['fyv_start_date'] : '' );
	?>
	<input type='date' name='fyv_settings[fyv_start_date]' value='<?php echo $option; ?>'>
	<?php

}

/**
 * Renders fyv_end_date field.
 *
 * @since 1.0.0
 */
function fyv_end_date_render(  ) {

	$options = get_option( 'fyv_settings' );
	$option = esc_html( $options ? $options['fyv_end_date'] : '' );
	?>
	<input type='date' name='fyv_settings[fyv_end_date]' value='<?php echo $option; ?>'>
	<?php

}

/**
 * Renders fyv_tracks field.
 *
 * @since 1.0.0
 */
function fyv_tracks_render(  ) {

	$options = get_option( 'fyv_settings' );
	$option = esc_html( $options ? $options['fyv_tracks'] : '' );
	?>
	<label for="fyv_settings[fyv_tracks]"><?php echo  __( 'If your event has different tracks, write them here separated by commas', 'fyvent' ); ?></label>
	<input type='text' name='fyv_settings[fyv_tracks]' value='<?php echo $option; ?>'>
	<?php

}

/**
 * Renders fyv_attendant_types field.
 *
 * @since 1.0.0
 */
function fyv_attendant_types_render(  ) {

	$options = get_option( 'fyv_settings' );
	$option = esc_html( $options ? $options['fyv_attendant_types'] : '' );
	?>
	<label for="fyv_settings[fyv_attendant_types]"><?php echo  __( 'If your event has different types of attendants, write them here separated by commas', 'fyvent' ); ?></label>
	<input type='text' name='fyv_settings[fyv_attendant_types]' value='<?php echo $option; ?>'>
	<?php

}

/**
 * Renders fyv_Session_types field.
 *
 * @since 1.0.0
 */
function fyv_session_types_render(  ) {

	$options = get_option( 'fyv_settings' );
	$option = esc_html( $options ? $options['fyv_session_types'] : '' );
	?>
	<label for="fyv_settings[fyv_session_types]"><?php echo  __( 'If your event has different types of sessions, write them here separated by commas', 'fyvent' ); ?></label>
	<input type='text' name='fyv_settings[fyv_session_types]' value='<?php echo $option; ?>'>
	<?php

}

/**
 * Renders fyv_presentation_download field.
 *
 * @since 1.0.0
 */
function fyv_presentation_download_render(  ) {

	$options = get_option( 'fyv_settings' );
	if( $options ){
		$option = array_key_exists( 'fyv_presentation_download', $options ) ? $options['fyv_presentation_download'] : '';
	} else {
		$option = '';
	}
	$checked =  esc_html( $option ? 'checked' : '' );
	?>
	<label for="fyv_settings[fyv_presentation_download]"><?php echo  __( 'Do you allow Presentations download from Session or Speaker pages?', 'fyvent' ); ?></label>
	<?php
	echo '<input type="checkbox" name="fyv_settings[fyv_presentation_download]" '.$checked.'>';

}

/**
 * Renders fyv_use_bootstrap field.
 *
 * @since 1.0.0
 */
function fyv_use_bootstrap_render(  ) {

	$options = get_option( 'fyv_settings' );
	if( $options ){
		$option = array_key_exists( 'fyv_use_bootstrap', $options ) ? $options['fyv_use_bootstrap'] : '';
	} else {
		$option = '';
	}
	$checked =  esc_html( $option ? 'checked' : '' );
	?>
	<label for="fyv_settings[fyv_use_bootstrap]"><?php echo  __( 'Do you want to use Bootstrap if your Theme also uses it?', 'fyvent' ); ?></label>
	<?php
	echo '<input type="checkbox" name="fyv_settings[fyv_use_bootstrap]" '.$checked.'>';

}

/**
 * Renders fyv_privacy_page field.
 *
 * @since 1.0.0
 */
function fyv_privacy_page_render(  ) {

	$options = get_option( 'fyv_settings' );
	$option = esc_html( $options ? $options['fyv_privacy_page'] : '' );
	?>
	<label for="fyv_settings[fyv_privacy_page]"><?php echo  __( 'Input here the slug of your privacy page (e.g. "/privacy")', 'fyvent' ); ?></label>
	<input type='text' name='fyv_settings[fyv_privacy_page]' value='<?php echo $option; ?>'>
	<?php

}

/**
 * Renders fyv_speaker_info_page field.
 *
 * @since 1.0.0
 */
function fyv_speaker_info_page_render(  ) {

	$options = get_option( 'fyv_settings' );
	$option = esc_html( $options ? $options['fyv_speaker_info_page'] : '' );
	?>
	<label for="fyv_settings[fyv_speaker_info_page]"><?php echo  __( 'Input here the slug of the page where speakers submit their information (e.g. "/speaker-information")', 'fyvent' ); ?></label>
	<input type='text' name='fyv_settings[fyv_speaker_info_page]' value='<?php echo $option; ?>'>
	<?php

}

/**
 * Callback function for settings section.
 *
 * @since 1.0.0
 */
function fyv_settings_section_callback(  ) {

//we aren't doing anything at the moment

}

/**
 * Initialices messages section.
 * These allows user to customize Fyvent messages
 *
 * @since 1.0.0
 */
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

/**
 * Renders fyv_attendant_registration_user_created field.
 *
 * @since 1.0.0
 */
function fyv_attendant_registration_user_created_render(  ) {
	?>
	<label for="fyv_attendant_registration_user_created"><?php echo  esc_html__( 'Message for successful user creation', 'fyvent' ); ?></label>
	<input type='text'class="large-text"  name='fyv_attendant_registration_user_created' value='<?php echo esc_html( get_option( 'fyv_attendant_registration_user_created' ) ); ?>'>
	<?php
}

/**
 * Renders fyv_attendant_privacy_agreemen field.
 *
 * @since 1.0.0
 */
function fyv_attendant_privacy_agreement_render(  ) {
	?>
	<label for="fyv_attendant_privacy_agreement"><?php echo  esc_html__( 'Text to show in privacy agreement check', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_attendant_privacy_agreement' value='<?php echo esc_html( get_option( 'fyv_attendant_privacy_agreement' ) ); ?>'>
	<?php
}

/**
 * Renders fyv_attendant_registered field.
 *
 * @since 1.0.0
 */
function fyv_attendant_registered_render(  ) {
	?>
	<label for="fyv_attendant_registered"><?php echo  esc_html__( 'Message for successful user registration', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_attendant_registered' value='<?php echo esc_html( get_option( 'fyv_attendant_registered' ) ); ?>'>
	<?php
}

/**
 * Renders fyv_attendant_user_exists field.
 *
 * @since 1.0.0
 */
function fyv_attendant_user_exists_render(  ) {
	?>
	<label for="fyv_attendant_user_exists"><?php echo esc_html__( 'Error message for user already exists', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_attendant_user_exists' value='<?php echo esc_html( get_option( 'fyv_attendant_user_exists' ) ); ?>'>
	<?php
}

/**
 * Renders fyv_speaker_registration_user_created field.
 *
 * @since 1.0.0
 */
function fyv_speaker_registration_user_created_render(  ) {
	?>
	<label for="fyv_speaker_registration_user_created"><?php echo  esc_html__( 'Message for successful user creation', 'fyvent' ); ?></label>
	<input type='text'class="large-text"  name='fyv_speaker_registration_user_created' value='<?php echo esc_html( get_option( 'fyv_speaker_registration_user_created' ) ); ?>'>
	<?php
}

/**
 * Renders fyv_speaker_privacy_agreement field.
 *
 * @since 1.0.0
 */
function fyv_speaker_privacy_agreement_render(  ) {
	?>
	<label for="fyv_speaker_privacy_agreement"><?php echo  esc_html__( 'Text to show in privacy agreement check', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speakert_privacy_agreement' value='<?php echo esc_html( get_option( 'fyv_speaker_privacy_agreement' ) ); ?>'>
	<?php
}

/**
 * Renders fyv_speaker_registered field.
 *
 * @since 1.0.0
 */
function fyv_speaker_registered_render(  ) {
	?>
	<label for="fyv_speaker_registered"><?php echo  esc_html__( 'Message for successful user registration', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_registered' value='<?php echo esc_html( get_option( 'fyv_speaker_registered' ) ); ?>'>
	<?php
}

/**
 * Renders  fyv_speaker_user_exists field.
 *
 * @since 1.0.0
 */
function fyv_speaker_user_exists_render(  ) {
	?>
	<label for="fyv_speaker_user_exists"><?php echo  esc_html__( 'Error message for user already exists', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_user_exists' value='<?php echo esc_html( get_option( 'fyv_speaker_user_exists' ) ); ?>'>
	<?php
}

/**
 * Renders fyv_speaker_more_info field.
 *
 * @since 1.0.0
 */
function fyv_speaker_more_info_render(  ) {
	?>
	<label for="fyv_speaker_more_info"><?php echo  esc_html__( 'Prompt to ask Speaker to complete their info', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_more_info' value='<?php echo esc_html( get_option( 'fyv_speaker_more_info' ) ); ?>'>
	<?php
}

/**
 * Renders fyv_speaker_complete_info field.
 *
 * @since 1.0.0
 */
function fyv_speaker_complete_info_render(  ) {
	?>
	<label for="fyv_speaker_complete_info"><?php echo  esc_html__( 'Text for the button to ask the Speaker to complete their info', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_complete_info' value='<?php echo esc_html( get_option( 'fyv_speaker_complete_info' ) ); ?>'>
	<?php
}

/**
 * Renders fyv_speaker_private_fields field.
 *
 * @since 1.0.0
 */
function fyv_speaker_private_fields_render(  ) {
	?>
	<label for="fyv_speaker_private_fields"><?php echo  esc_html__( 'Message for speaker that their info is private', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_private_fields' value='<?php echo esc_html( get_option( 'fyv_speaker_private_fields' ) ); ?>'>
	<?php
}

/**
 * Renders fyv_speaker_public_field field.
 *
 * @since 1.0.0
 */
function fyv_speaker_public_field_render(  ) {
	?>
	<label for="fyv_speaker_public_field"><?php echo  esc_html__( 'Message to explain that a field will be visible for the public', 'fyvent' ); ?></label>
	<input type='text' class="large-text" name='fyv_speaker_public_field' value='<?php echo esc_html( get_option( 'fyv_speaker_public_field' ) ); ?>'>
	<?php
}

/**
 * Renders options page with tabs.
 *
 * @since 1.0.0
 */
function fyv_options_page(  ) {

	//Get the active tab from the $_GET param
	$default_tab = 'settings';
	$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

	?>
	<h1>Fyvent Options</h1>
	<nav class="nav-tab-wrapper">
		<a href="?page=fyv_options&tab=settings" class="nav-tab <?php if( $tab===null ):?>nav-tab-active<?php endif; ?>">
			<?php echo esc_html__( 'Event Settings', 'fyvent' ); ?>
		</a>
		<a href="?page=fyv_options&tab=messages" class="nav-tab <?php if( $tab==='messages' ):?>nav-tab-active<?php endif; ?>">
			<?php echo esc_html__( 'Messages', 'fyvent' ); ?>
		</a>
		<a href="?page=fyv_options&tab=more" class="nav-tab <?php if( $tab==='more' ):?>nav-tab-active<?php endif; ?>">
			<?php echo esc_html__( 'More Info', 'fyvent' ); ?>
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
			?>
			<div style="display: flex;align-items: center;">
				<div style="margin:5px;">
					<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/Fyvent-logo.png'; ?>" alt="Fyvent logo" />
				</div>
			</div>
			<div>
				<div style="margin-top:5px;margin-bottom:15px;padding:5px;border:solid;"><?php echo __( 'Please consider a donation to help future development of Fyvent plugin.', 'fyvent' ); ?>
					<form action="https://www.paypal.com/donate" method="post" target="_top" style="margin:10px;">
						<input type="hidden" name="hosted_button_id" value="<?php echo ( substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) != 'es_ES' ) ? 'WP77D4BSQB5L4': 'LEDLZQTPHU7RG'; ?>" />
						<button type="submit" class="button" name="submit" alt="PayPal" /><?php echo esc_html__( 'DONATE', 'fyvent' ); ?></button>
					</form>
				</div>

				<p><?php echo esc_html__( 'Fyvent lets you manage your event within WordPress, while giving you total freedom to design your event page.', 'fyvent' ); ?></p>
				<p><?php echo esc_html__( 'You can see a demo here: ', 'fyvent' ); ?><a href="https://fyvent.com/demo"><?php echo __( 'Fyvent demo', 'fyvent' ); ?></a></p>
				<p><?php echo esc_html__( 'More information, including the demo template, and help at ', 'fyvent' ); ?><a href="https://fyvent.com"><?php echo __( 'Fyvent home page', 'fyvent' ); ?></a></p>

				<h3><?php echo esc_html__( 'Quick Help', 'fyvent' ); ?></h3>
				<p>
					<?php echo esc_html__( 'Use the admin menu options to create and manage venues, rooms, sessions, speakers and attendants. ', 'fyvent' ); ?>
					<?php echo esc_html__( 'You can insert shortcodes to show the information on your pages or to show registration forms.', 'fyvent' ); ?>
				</p>
				<p>
					<?php echo esc_html__( 'Use this shortcode to show the name of the event:', 'fyvent' ); ?><br/>
					<strong><?php echo '[fyvent-event-name]'; ?></strong><br/>
				</p>
				<p>
					<?php echo esc_html__( 'Use this shortcode to show all speakers:', 'fyvent' ); ?><br/>
					<strong><?php echo '[fyvent-speaker]'; ?></strong><br/>
					<?php echo esc_html__( 'Or use the id of a speaker to show their information:', 'fyvent' ); ?><br/>
					<strong><?php echo '[fyvent-speaker id="10"]'; ?></strong><br/>
				</p>
				<p>
					<?php echo esc_html__( 'Use this shortcode to show all sessions:', 'fyvent' ); ?><br/>
					<strong><?php echo '[fyvent-session]'; ?></strong><br/>
					<?php echo esc_html__( 'Or use the id of a session to show its information:', 'fyvent' ); ?><br/>
					<strong><?php echo '[fyvent-session id="10"]'; ?></strong><br/>
				</p>
				<p>
					<?php echo esc_html__( 'Use this shortcode to let a speaker update their information:', 'fyvent' ); ?><br/>
					<strong><?php echo '[fyvent-speaker-information]'; ?></strong><br/>
					<?php echo esc_html__( 'Please be aware that the speaker needs to be logged in to see and fill the form.', 'fyvent' ); ?><br/>
				</p>
				<p>
					<?php echo esc_html__( 'Use this shortcode to create a registration form for speakers:', 'fyvent' ); ?><br/>
					<strong><?php echo '[fyvent-speaker-register]'; ?></strong><br/>
				</p>
				<p>
					<?php echo esc_html__( 'Use this shortcode to create a registration form for attendants:', 'fyvent' ); ?><br/>
					<strong><?php echo '[fyvent-attendant-register]'; ?></strong><br/>
				</p>
				<p>
					<?php echo esc_html__( 'Use this shortcode to create a form to let attendants update their information:', 'fyvent' ); ?><br/>
					<strong><?php echo '[fyvent-update-info]'; ?></strong><br/>
				</p>

			</div>
			<?php
			break;

		default:
			break;
	}
}
