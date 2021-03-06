<?php

// Update the columns shown on the custom post type edit.php view - so we also have custom columns
add_filter( 'manage_room_posts_columns', 'fyvent_room_columns' );
// this fills in the columns that were created with each individual post's value
add_action( 'manage_room_posts_custom_column', 'fyvent_fill_room_columns', 10, 2 );
// this makes columns sortable
add_filter( 'manage_edit-room_sortable_columns', 'fyvent_room_sortable_columns');
//hook to change columns width
add_action('admin_head', 'fyvent_room_column_width');

/**
 * Specifies sortable columns in the admin table.
 *
 * @since 1.0.0
 *
 * @param array $columns Array of column names used in the admin table.
 * @return array Array of column names used in the admin table.
 */
function fyvent_room_sortable_columns( $columns ) {
	$columns['title'] = 'title';
	$columns['capacity'] = 'capacity';
	return $columns;
}


/**
 * Adapts column widths to lenght of fields
 *
 * Echoes some CSS to override the standard WordPress admin table column width.
 *
 * @since 1.0.0
 */
function fyvent_room_column_width() {
    echo '<style type="text/css">
        .column-title { text-align: left; overflow:hidden }
        .column-venue { text-align: left; overflow:hidden }
        .column-capacity { text-align: right !important; width:90px !important; overflow:hidden }
        .column-sessions { text-align: left; overflow:hidden }
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
function fyvent_room_columns( $columns ){
	return array(
				'cb' => '<input type="checkbox" />',
				'title' => esc_html__( 'Name', 'fyvent' ),
				'venue' => esc_html__( 'Venue', 'fyvent' ),
				'capacity' => esc_html__( 'Capacity', 'fyvent' ),
				'sessions' => esc_html__( 'Sessions', 'fyvent' ),
				);
}

/**
 * Specific code needed to show some of the fields in the rooms admin table.
 *
 * @since 1.0.0
 *
 * @param array $column Column that we are preparing to show.
 * @param int $post_id ID of the post (room) that we are showing.
 */
function fyvent_fill_room_columns( $column, $post_id ) {

	// Fill in the columns with meta box info associated with each post or formatted content
	switch ( $column ) {
		case 'venue':
			$venue_id = fyvent_get_venue_from_room( $post_id );
			if( $venue_id ){
				$post = get_post( $venue_id );
				$link = 'post.php?post='.$post->ID.'&action=edit';
				$venue = $post->post_title;
				echo '<a href="'.esc_url( $link ).'">'.esc_html( $venue ).'</a><br/>';
			} else {
				echo "---";
			}
			break;
		case 'capacity' :
			echo esc_html( get_post_meta( $post_id , 'capacity' , true ) );
			break;
		case 'sessions' :
			$sessions = get_post_meta( $post_id , 'sessions' , true );
			if( $sessions ){
				foreach ( $sessions as $session ) {
					$post = get_post( $session );
					$link = 'post.php?post='.$post->ID.'&action=edit';
					$session_title = $post->post_title;
					echo '<a href="'.esc_url( $link ).'">'.esc_html( $session_title ).'</a><br/>';
				}
			} else {
				echo '-';
			}
			break;
	}
}

/**
 * Registers Custom Post Type and taxonomies on init.
 *
 * @since 1.0.0
 */
function fyvent_room_init() {

	$labels = [
		'name' => _x( 'Rooms', 'post type general name', 'fyvent' ),
		'singular_name' => _x( 'Room', 'post type singular name', 'fyvent' ),
		'add_new' => _x( 'Add New', 'Room', 'fyvent' ),
		'add_new_item' => esc_html__( 'Add New room', 'fyvent' ),
		'edit_item' => esc_html__( 'Edit room', 'fyvent' ),
		'new_item' => esc_html__( 'New room', 'fyvent' ),
		'all_items' => esc_html__( 'All rooms', 'fyvent' ),
		'view_item' => esc_html__( 'View room', 'fyvent' ),
		'search_items' => esc_html__( 'Search rooms', 'fyvent' ),
		'not_found' => esc_html__( 'No room found', 'fyvent' ),
		'not_found_in_trash' => esc_html__( 'No room found in the Trash', 'fyvent' ),
		'parent_item_colon' => '',
		'menu_name' => esc_html__( 'Rooms', 'fyvent' ),
	];

	$args = [
		'labels' => $labels,
		'description' => esc_html__( 'Displays rooms', 'fyvent' ),
		'public' => true,
		'menu_position' => 15,
		'supports' => [ 'title', 'editor', 'thumbnail' ],
		'has_archive' => true,
		'map_meta_cap' => true,
		'capability_type'    => 'post',
		'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="black" d="M624 448h-80V113.45C544 86.19 522.47 64 496 64H384v64h96v384h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16zM312.24 1.01l-192 49.74C105.99 54.44 96 67.7 96 82.92V448H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h336V33.18c0-21.58-19.56-37.41-39.76-32.17zM264 288c-13.25 0-24-14.33-24-32s10.75-32 24-32 24 14.33 24 32-10.75 32-24 32z"/></svg>'),
	];

	register_post_type( 'room', $args );

}  //fyvent_room_init()

/**
 * Defines the metabox and field configurations.
 *
 * @since 1.0.0
 */
function fyvent_room_metabox() {

	// Initiate the metabox
	$cmb = new_cmb2_box(
		[
			'id' => 'room_additional_info',
			'title' => esc_html__( 'Additional Information', 'fyvent' ),
			'object_types' => [ 'room' ], // Post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Capacity', 'fyvent' ),
			'desc' => esc_html__( 'Type the maximum number of persons the room can fit.', 'fyvent' ),
			'id' => 'capacity',
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
		    'name'    => esc_html__( 'Video Infraestructure', 'fyvent' ),
		    'desc'    => esc_html__( 'Specify any video infraestrucutre in the room (projector, cameras, etc)', 'fyvent' ),
		    'id'      => 'videoinf',
		    'type'    => 'text',
		]
	);

	$cmb->add_field(
		[
		    'name'    => esc_html__( 'Audio Infraestructure', 'fyvent' ),
		    'desc'    => esc_html__( 'Specify any audio infraestrucutre in the room (microphones, mixer, etc)', 'fyvent' ),
		    'id'      => 'audioinf',
		    'type'    => 'text',
		]
	);

	$cmb->add_field(
		[
		    'name'    => esc_html__( 'Translation Infraestructure', 'fyvent' ),
		    'desc'    => esc_html__( 'Specify if the room has translation infraestructure like a cabin', 'fyvent' ),
		    'id'      => 'translationinf',
		    'type'    => 'text',
		]
	);

	$cmb->add_field( array(
		'name'    => esc_html__( 'Sessions', 'fyvent' ),
		'desc'    => esc_html__( 'Drag posts from the left column to the right column to attach them to this page.<br />You may rearrange the order of the posts in the right column by dragging and dropping.', 'fyvent' ),
		'id'      => 'sessions',
		'type'    => 'custom_attached_posts',
		'options' => array(
			'show_thumbnails' => true, // Show thumbnails on the left
			'filter_boxes'    => true, // Show a text box for filtering the results
			'query_args'      => array(
				'posts_per_page' => 20,
				'post_type'      => 'session',
			), // override the get_posts args
		),
	) );

	$cmb->add_field( array(
	    'name' => esc_html__( 'Notes', 'fyvent' ),
	    'id' => 'notes',
	    'type' => 'textarea_small'
	) );

} // fyvent_room_metabox()

// Run the room init on init.
add_action( 'init', 'fyvent_room_init' );

add_action( 'cmb2_admin_init', 'fyvent_room_metabox' );