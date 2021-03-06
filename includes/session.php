<?php

// Update the columns shown on the custom post type edit.php view - so we also have custom columns
add_filter( 'manage_session_posts_columns', 'fyvent_session_columns' );
// this fills in the columns that were created with each individual post's value
add_action( 'manage_session_posts_custom_column', 'fyvent_fill_session_columns', 10, 2 );
// this makes columns sortable
add_filter( 'manage_edit-session_sortable_columns', 'fyvent_session_sortable_columns' );
//hook to change columns width
add_action( 'admin_head', 'fyvent_session_column_width' );

/**
 * Specifies sortable columns in the admin table.
 *
 * @since 1.0.0
 *
 * @param array $columns Array of column names used in the admin table.
 * @return array Array of column names used in the admin table.
 */
function fyvent_session_sortable_columns( $columns ) {
	$columns['title'] = 'title';
	$columns['room'] = 'room';
	$columns['session_date'] = 'session_date';
	$columns['time'] = 'time';
	return $columns;
}


/**
 * Adapts column widths to lenght of fields
 *
 * Echoes some CSS to override the standard WordPress admin table column width.
 *
 * @since 1.0.0
 */
function fyvent_session_column_width() {
    echo '<style type="text/css">
        .column-title { text-align: left; overflow:hidden }
        .column-room { text-align: left; overflow:hidden }
        .column-session_date { text-align: left; overflow:hidden }
        .column-time { text-align: left; overflow:hidden }
        .column-type { text-align: left; overflow:hidden }
        .column-length { text-align: left; overflow:hidden }
        .column-speakers { text-align: left; overflow:hidden }
    	</style>';
}


/**
 * Specifies fields that will be shown in the admin table.
 *
 * @since 1.0.0
 *
 * @param array $columns Array of column names used in the admin table.
 * @return array Array of column names used in the admin table.
 */
function fyvent_session_columns( $columns ){
	return array(
				'cb' => '<input type="checkbox" />',
				'title' => esc_html__( 'Name', 'fyvent' ),
				'room' => esc_html__( 'Room', 'fyvent' ),
				'session_date' => esc_html__( 'Date', 'fyvent' ),
				'time' => esc_html__( 'Time', 'fyvent' ),
				'type' => esc_html__( 'Type', 'fyvent' ),
				'length' => esc_html__( 'Length', 'fyvent' ),
				'speakers' => esc_html__( 'Speakers', 'fyvent' ),
				);
}

/**
 * Specific code needed to show some of the fields in the sessions admin table.
 *
 * @since 1.0.0
 *
 * @param array $column Column that we are preparing to show.
 * @param int $post_id ID of the post (session) that we are showing.
 */
function fyvent_fill_session_columns( $column, $post_id ) {

	$post = get_post( $post_id );

	// Fill in the columns with meta box info associated with each post or formatted content
	switch ( $column ) {
		case 'session_date' :
			echo esc_html( date( get_option( 'date_format' ), strtotime( get_post_meta( $post_id , 'session_date' , true ) ) ) );
			break;
		case 'time' :
			echo esc_html( get_post_meta( $post_id , 'time' , true ) );
			break;
		case 'type' :
			echo esc_html( ucwords( get_post_meta( $post_id , 'type' , true ) ) );
			break;
		case 'length' :
			echo esc_html( get_post_meta( $post_id , 'length' , true ).__( ' min', 'fyvent' ) );
			break;
		case 'room':
			$room_id = fyvent_get_room_from_session( $post_id );
			if( $room_id ){
				$post = get_post( $room_id );
				$link = 'post.php?post='.$post->ID.'&action=edit';
				$room = $post->post_title;
				echo '<a href="'.esc_url( $link ).'">'.esc_html( $room ).'</a><br/>';
			} else {
				echo "---";
			}
			break;
		case 'speakers':
			$speakers = get_post_meta( $post_id, 'speakers', true	 );

			if( $speakers ){
				foreach( $speakers as $speaker ){
					$speaker_obj = get_user_by( 'id', $speaker);
					$link = '/wp-admin/user-edit.php?user_id='.$speaker;
					$speaker_name = $speaker_obj->first_name . ' ' . $speaker_obj->last_name;
					echo '<a href="'.esc_url( $link ).'">'.esc_html( $speaker_name ).'</a><br/>';
				}
			} else {
				echo "---";
			}
			break;
	}
}

/**
 * Registers Custom Post Type on init.
 *
 * @since 1.0.0
 */
function fyvent_session_init() {

	$labels = [
		'name' => _x( 'Sessions', 'post type general name', 'fyvent' ),
		'singular_name' => _x( 'Session', 'post type singular name', 'fyvent' ),
		'add_new' => _x( 'Add New', 'session', 'fyvent' ),
		'add_new_item' => esc_html__( 'Add New session', 'fyvent' ),
		'edit_item' => esc_html__( 'Edit session', 'fyvent' ),
		'new_item' => esc_html__( 'New session', 'fyvent' ),
		'all_items' => esc_html__( 'All sessions', 'fyvent' ),
		'view_item' => esc_html__( 'View session', 'fyvent' ),
		'search_items' => esc_html__( 'Search sessions', 'fyvent' ),
		'not_found' => esc_html__( 'No session found', 'fyvent' ),
		'not_found_in_trash' => esc_html__( 'No session found in the Trash', 'fyvent' ),
		'parent_item_colon' => '',
		'menu_name' => esc_html__( 'Sessions', 'fyvent' ),
	];

	$args = [
		'labels' => $labels,
		'description' => esc_html__( 'Displays sessions', 'fyvent' ),
		'public' => true,
		'menu_position' => 15,
		'supports' => [ 'title', 'editor', 'thumbnail' ],
		'has_archive' => true,
		'map_meta_cap' => true,
		'capability_type'    => 'post',
		'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="black" d="M208 352c-2.39 0-4.78.35-7.06 1.09C187.98 357.3 174.35 360 160 360c-14.35 0-27.98-2.7-40.95-6.91-2.28-.74-4.66-1.09-7.05-1.09C49.94 352-.33 402.48 0 464.62.14 490.88 21.73 512 48 512h224c26.27 0 47.86-21.12 48-47.38.33-62.14-49.94-112.62-112-112.62zm-48-32c53.02 0 96-42.98 96-96s-42.98-96-96-96-96 42.98-96 96 42.98 96 96 96zM592 0H208c-26.47 0-48 22.25-48 49.59V96c23.42 0 45.1 6.78 64 17.8V64h352v288h-64v-64H384v64h-76.24c19.1 16.69 33.12 38.73 39.69 64H592c26.47 0 48-22.25 48-49.59V49.59C640 22.25 618.47 0 592 0z"/></svg>'),
	];

	register_post_type( 'session', $args );

}  //fyvent_session_init()

/**
 * Defines the metabox and field configurations.
 *
 * @since 1.0.0
 */
function fyvent_session_metabox() {

	// Initiate the metabox
	$cmb = new_cmb2_box(
		[
			'id' => 'session_additional_info',
			'title' => esc_html__( 'Additional Information', 'fyvent' ),
			'object_types' => [ 'session' ], // Post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
		]
	);

	$cmb->add_field(
		[
		    'name' => esc_html__( 'Date', 'fyvent' ),
		    'id'   => 'session_date',
		    'type' => 'text_date',
		]
	);

	$cmb->add_field(
		[
		    'name' => esc_html__( 'Time', 'fyvent' ),
		    'id' => 'time',
		    'type' => 'text_time',
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Length', 'fyvent' ),
			'desc' => esc_html__( 'Type the length of the session in minutes.', 'fyvent' ),
			'id' => 'length',
			'type' => 'text',
				'attributes' => [
					'type' => 'number',
					'pattern' => '\d*',
				],
			'sanitization_cb' => 'absint',
			'escape_cb' => 'absint',
		]
	);

	$options = get_option('fyvent_settings');
	if ( $options ){
		if( $options['fyvent_session_types'] != "" ){
			$session_types = array_map( 'trim', explode( ',', $options['fyvent_session_types'] ) );
			$cmb->add_field(
				[
				    'name'             => esc_html__( 'Type', 'fyvent' ),
				    'desc'             => esc_html__( 'Select the type of this session', 'fyvent' ),
				    'id'               => 'type',
				    'type'             => 'select',
				    'show_option_none' => false,
				    'options'          => $session_types,
				]
			);
		}
	}

	$cmb->add_field( array(
		'name'    => esc_html__( 'Speakers', 'fyvent' ),
		'desc'    => esc_html__( 'Drag users from the left column to the right column to attach them to this page.<br />You may rearrange the order of the users in the right column by dragging and dropping.', 'yourtextdomain' ),
		'id'      => 'speakers',
		'type'    => 'custom_attached_posts',
		'options' => array(
			'show_thumbnails' => true, // Show thumbnails on the left
			'filter_boxes'    => true, // Show a text box for filtering the results
			'query_users'     => true, // Do users instead of posts/custom-post-types.
		),
	) );

	$cmb->add_field( array(
	    'name' => esc_html__( 'Notes', 'fyvent' ),
	    'id' => 'notes',
	    'type' => 'textarea_small'
	) );
} // fyvent_session_metabox()

// Run the session init on init.
add_action( 'init', 'fyvent_session_init' );
add_action( 'cmb2_admin_init', 'fyvent_session_metabox' );

/**
 * Renders session information from shortcode
 *
 * @since 1.0.0
 */
function fyvent_show_session_shortcode( $atts = [] ){

	// normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

	global $wp_query;
	if( $wp_query->query_vars['session_id'] ){
		$atts['id'] = $wp_query->query_vars['session_id'];
	}

	//if there is an id we show the session corresponding to that id. If not, list all sessions
    if( $atts['id'] && ( get_post_type( $atts['id'] ) == 'session' ) ){
    	$id = $atts['id'];
    	?>
    	<div <?php echo esc_html( fyvent_classes( 'session-one' ) ); ?> >
			<div <?php echo esc_html( fyvent_classes( 'session-image-one' ) ); ?> >
				<?php echo get_the_post_thumbnail( $id, 'large', array( 'class' => 'aligncenter' ) ); ?>
			</div>
			<div <?php echo esc_html( fyvent_classes( 'session-info-one' ) ); ?> >
				<h4><?php
					$session_type = fyvent_get_session_type_by_index( get_post_meta( $id , 'type' , true ) ) ;
					if( $session_type ){
						echo wp_kses(  $session_type.': <a href="/sessions/?session_id='.$id.'">'.get_the_title( $id ).'</a>', 'post' );
					} else {
						echo wp_kses(  '<a href="/sessions/?session_id='.$id.'">'.get_the_title( $id ).'</a>', 'post' );
					}
				?></h4>
				<div <?php echo esc_html( fyvent_classes( 'session-time-room' ) ); ?> >
					<p <?php echo esc_html( fyvent_classes( 'session-time' ) ); ?> >
						<?php echo esc_html( get_post_meta( $id, 'session_date', true ) ); ?>&nbsp;|&nbsp;<?php echo esc_html( get_post_meta( $id, 'time', true ) ); ?>
					</p>
					<p <?php echo esc_html( fyvent_classes( 'session-room' ) ); ?> >
						<?php
						$room_id = fyvent_get_room_from_session( $id );
						if( $room_id ){
							$post = get_post( $room_id );
							echo ' '.esc_html__( 'Room: ', 'fyvent' ).esc_html( $post->post_title );
						} ?>
					</p>
				</div>
				<p <?php echo esc_html( fyvent_classes( 'session-content' ) ); ?> ><?php echo get_the_content( null, false, $id ); ?></p>
				<div <?php echo esc_html( fyvent_classes( 'session-speakers' ) ); ?> >
					<h5><?php echo esc_html__( 'Speakers:', 'fyvent' ); ?></h5>
					<div <?php echo esc_html( fyvent_classes( 'session-speakers-list' ) ); ?> >
					<?php
					$speakers = get_post_meta( $id, 'speakers', false );
					if( $speakers ){
						foreach( $speakers[0] as $speaker ){
							$speaker_info = get_userdata( $speaker );
							$speaker_data = get_user_meta( $speaker );
					    	fyvent_list_speakers( $speaker_data, $speaker_info );
						}
					}
					?>
					</div>
				</div>
			</div>
		</div>
		<?php
    } else {
    	// list all sessions
    	$loop = new WP_Query( array( 'post_type' => 'session', 'paged' => $paged ) );
	    if( $loop->have_posts() ){
	        while ( $loop->have_posts() ) : $loop->the_post();
	        ?>
				<div <?php echo esc_html( fyvent_classes( 'session-list' ) ); ?> >
					<div <?php echo esc_html( fyvent_classes( 'session-image' ) ); ?> >
						<?php
						$thumb = get_the_post_thumbnail( $id , 'thumbnail', array( 'class' => 'alignleft' ) );
						if( !empty( $thumb ) ){
							echo esc_html( $thumb );
						} else {
							echo '<img src="'.plugin_dir_url( __FILE__ ) . '../assets/session-filler.png'.'" alt="session image filler" width="150px" '.esc_html( fyvent_classes( 'img' ) ).'/>';

						}
						?>
					</div>
					<div <?php echo esc_html( fyvent_classes( 'session-info' ) ); ?> >
						<h4><?php
							$session_type = fyvent_get_session_type_by_index( get_post_meta( get_the_id() , 'type' , true ) );
							if( $session_type ){
								echo wp_kses( ucwords( $session_type ).': <a href="/sessions/?session_id='.get_the_id().'">'.get_the_title( ).'</a>', 'post' );
							} else {
								echo wp_kses( '<a href="/sessions/?session_id='.get_the_id().'">'.get_the_title( ).'</a>', 'post' );
							}
						?></h4>
						<div>
							<p>
								<?php echo get_post_meta( get_the_id(), 'session_date', true ); ?>&nbsp;|&nbsp;<?php echo get_post_meta( get_the_id(), 'time', true ); ?>
							</p>
							<p>
								<?php
								$room_id = fyvent_get_room_from_session( get_the_id() );
								if( $room_id ){
									$post = get_post( $room_id );
									echo ' '.esc_html__( 'Room: ', 'fyvent' ).esc_html( $post->post_title );
								} ?>
							</p>
						</div>
						<p <?php echo esc_html( fyvent_classes( 'session-content' ) ); ?> ><?php echo get_the_content(); ?></p>
						<div <?php echo esc_html( fyvent_classes( 'session-speakers' ) ); ?> >
							<h5><?php echo esc_html__( 'Speakers:', 'fyvent' ); ?></h5>
							<div <?php echo esc_html( fyvent_classes( 'session-speakers-list' ) ); ?> >
								<?php
								$speakers = get_post_meta( get_the_id(), 'speakers', false );
								if( $speakers ){
									foreach( $speakers[0] as $speaker ){
										$speaker_info = get_userdata( $speaker);
										$speaker_data = get_user_meta( $speaker );
										fyvent_list_speakers( $speaker_data, $speaker_info );
									}
								}
								?>
							</div>
						</div>
					</div>
				</div>
			<?php endwhile;
			if (  $loop->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( esc_html__( '<span class="meta-nav">&larr;</span> Previous', 'fyvent' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( esc_html__( 'Next <span class="meta-nav">&rarr;</span>', 'fyvent' ) ); ?></div>
				</div>
			<?php endif;
	    } else {
	    	echo esc_html__( 'No sessions found', 'fyvent' );
	    }
		wp_reset_postdata();
	}

}

/**
 * Gets the name of a session type by its index
 *
 * @param string $index index of the type of session to get
 *
 * @return string type of session
 *
 * @since 1.0.0
 */
function fyvent_get_session_type_by_index( $index ){
	$options = get_option( 'fyvent_settings' );
	$types = explode( ',', $options['fyvent_session_types'] );
	return trim( $types[ $index ] );
}
