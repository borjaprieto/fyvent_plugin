<?php

// Update the columns shown on the custom post type edit.php view - so we also have custom columns
add_filter( 'manage_room_posts_columns', 'room_columns' );
// this fills in the columns that were created with each individual post's value
add_action( 'manage_room_posts_custom_column', 'fill_room_columns', 10, 2 );
// this makes columns sortable
add_filter( 'manage_edit-room_sortable_columns', 'room_sortable_columns');
//hook to change columns width
add_action('admin_head', 'room_column_width');

/**
 * Specifies sortable columns in the admin table.
 *
 * @since 1.0.0
 *
 * @param array $columns Array of column names used in the admin table.
 * @return array Array of column names used in the admin table.
 */
function room_sortable_columns( $columns ) {
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
function room_column_width() {
    echo '<style type="text/css">
        .column-title { text-align: left; overflow:hidden }
        .column-venue { text-align: left; overflow:hidden }
        .column-capacity { text-align: right !important; width:50px !important; overflow:hidden }
        .column-post_id { text-align: right !important; overflow:hidden }
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
function room_columns($columns){
	return array(
				'cb' => '<input type="checkbox" />',
				'title' => esc_html__( 'Name', 'fyvent' ),
				'venue' => esc_html__( 'Venue', 'fyvent' ),
				'capacity' => esc_html__( 'Capacity', 'fyvent' ),
				'post_id' =>esc_html__( 'room ID', 'fyvent' ),
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
function fill_room_columns( $column, $post_id ) {

	$post = get_post( $post_id );

	// Fill in the columns with meta box info associated with each post or formatted content
	switch ( $column ) {

		case 'post_id' :
			echo $post_id;
			break;
	}
}

/**
 * Registers Custom Post Type and taxonomies on init.
 *
 * @since 1.0.0
 */
function fyv_room_init() {

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
		'menu_position' => 14,
		'supports' => [ 'title', 'editor', 'thumbnail' ],
		'has_archive' => true,
		'map_meta_cap' => true,
		'capability_type'    => 'post',
		'menu_icon' => 'dashicons-format-chat',
	];

	register_post_type( 'room', $args );

}  //fyv_room_init()

/**
 * Defines the metabox and field configurations.
 *
 * @since 1.0.0
 */
function fyv_room_metabox() {

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


} // fyv_room_metabox()

// Run the room init on init.
add_action( 'init', 'fyv_room_init' );

add_action( 'cmb2_admin_init', 'fyv_room_metabox' );