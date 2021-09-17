<?php

// Update the columns shown on the custom post type edit.php view - so we also have custom columns
add_filter( 'manage_session_posts_columns', 'session_columns' );
// this fills in the columns that were created with each individual post's value
add_action( 'manage_session_posts_custom_column', 'fill_session_columns', 10, 2 );
// this makes columns sortable
add_filter( 'manage_edit-session_sortable_columns', 'session_sortable_columns');
//hook to change columns width
add_action('admin_head', 'session_column_width');

/**
 * Specifies sortable columns in the admin table.
 *
 * @since 1.0.0
 *
 * @param array $columns Array of column names used in the admin table.
 * @return array Array of column names used in the admin table.
 */
function session_sortable_columns( $columns ) {
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
function session_column_width() {
    echo '<style type="text/css">
        .column-title { text-align: left; overflow:hidden }
        .column-room { text-align: left; overflow:hidden }
        .column-session_date { text-align: left; overflow:hidden }
        .column-time { text-align: left; overflow:hidden }
        .column-type { text-align: left; overflow:hidden }
        .column-length { text-align: left; overflow:hidden }
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
function session_columns($columns){
	return array(
				'cb' => '<input type="checkbox" />',
				'title' => esc_html__( 'Name', 'fyvent' ),
				'room' => esc_html__( 'Room', 'fyvent' ),
				'session_date' => esc_html__( 'Date', 'fyvent' ),
				'time' => esc_html__( 'Time', 'fyvent' ),
				'type' => esc_html__( 'Type', 'fyvent' ),
				'length' => esc_html__( 'Length', 'fyvent' ),
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
function fill_session_columns( $column, $post_id ) {

	$post = get_post( $post_id );

	// Fill in the columns with meta box info associated with each post or formatted content
	switch ( $column ) {
		case 'session_date' :
			echo date( get_option( 'date_format' ), strtotime( get_post_meta( $post_id , 'session_date' , true ) ) );
			break;
		case 'time' :
			echo get_post_meta( $post_id , 'time' , true );
			break;
		case 'type' :
			echo ucwords( get_post_meta( $post_id , 'type' , true ) );
			break;
		case 'length' :
			echo get_post_meta( $post_id , 'length' , true ).__( ' min', 'fyvent' );
			break;
		case 'room':
			$room_id = fyv_get_room_from_session( $post_id );
			if( $room_id ){
				$post = get_post( $room_id );
				echo '<a href="post.php?post='.$post->ID.'&action=edit">'.$post->post_title.'</a><br/>';
			} else {
				echo "---";
			}
			break;
	}
}

/**
 * Registers Custom Post Type and taxonomies on init.
 *
 * @since 1.0.0
 */
function fyv_session_init() {

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
		'menu_position' => 14,
		'supports' => [ 'title', 'editor', 'thumbnail' ],
		'has_archive' => true,
		'map_meta_cap' => true,
		'capability_type'    => 'post',
		'menu_icon' => 'dashicons-format-chat',
	];

	register_post_type( 'session', $args );

}  //fyv_session_init()

/**
 * Defines the metabox and field configurations.
 *
 * @since 1.0.0
 */
function fyv_session_metabox() {

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
		    // 'timezone_meta_key' => 'wiki_test_timezone',
		    // 'date_format' => 'l jS \of F Y',
		]
	);

	$cmb->add_field(
		[
		    'name' => esc_html__( 'Time', 'fyvent' ),
		    'id' => 'time',
		    'type' => 'text_time',
		    // Override default time-picker attributes:
		    // 'attributes' => array(
		    //     'data-timepicker' => json_encode( array(
		    //         'timeOnlyTitle' => __( 'Choose your Time', 'cmb2' ),
		    //         'timeFormat' => 'HH:mm',
		    //         'stepMinute' => 1, // 1 minute increments instead of the default 5
		    //     ) ),
		    // ),
		    // 'time_format' => 'h:i:s A',
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


	$cmb->add_field(
		[
		    'name'             => esc_html__( 'Type', 'fyvent' ),
		    'desc'             => esc_html__( 'Select the type of this session', 'fyvent' ),
		    'id'               => 'type',
		    'type'             => 'select',
		    'show_option_none' => false,
		    'default'          => 'presentation',
		    'options'          => array(
		        'presentation' => __( 'Presentation', 'fyvent' ),
		        'roundtable'   => __( 'Roundtable', 'fyvent' ),
		        'workshop'     => __( 'Workshop', 'fyvent' ),
		        'keynote'     => __( 'Keynote', 'fyvent' ),
		    ),
		]
	);

	$cmb->add_field( array(
	    'name' => esc_html__( 'Notes', 'fyvent' ),
	    'id' => 'notes',
	    'type' => 'textarea_small'
	) );

} // fyv_session_metabox()

// Run the session init on init.
add_action( 'init', 'fyv_session_init' );

add_action( 'cmb2_admin_init', 'fyv_session_metabox' );