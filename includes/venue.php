<?php

// Update the columns shown on the custom post type edit.php view - so we also have custom columns
add_filter( 'manage_venue_posts_columns', 'venue_columns' );
// this fills in the columns that were created with each individual post's value
add_action( 'manage_venue_posts_custom_column', 'fill_venue_columns', 10, 2 );
// this makes columns sortable
add_filter( 'manage_edit-venue_sortable_columns', 'venue_sortable_columns');
//change proposals query to sort by votes
//add_action( 'pre_get_posts', 'venues_orderby' );
//hook to change columns width
add_action('admin_head', 'venue_column_width');

/**
 * Specifies sortable columns in the admin table.
 *
 * @since 1.0.0
 *
 * @param array $columns Array of column names used in the admin table.
 * @return array Array of column names used in the admin table.
 */
function venue_sortable_columns( $columns ) {
	$columns['title'] = 'title';
	return $columns;
}


/**
 * Adapts column widths to lenght of fields
 *
 * Echoes some CSS to override the standard WordPress admin table column width.
 *
 * @since 1.0.0
 */
function venue_column_width() {
    echo '<style type="text/css">
        .column-title { text-align: left; width:200px !important; overflow:hidden }
        .column-post_id { text-align: right !important; overflow:hidden }
        .column-location { text-align: left; overflow:hidden }
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
function venue_columns($columns){
	return array(
				'cb' => '<input type="checkbox" />',
				'title' => esc_html__( 'Name', 'fyvent' ),
				'post_id' =>esc_html__( 'Venue ID', 'fyvent' ),
				'location' => esc_html__( 'Location', 'fyvent' ),
				);
}

/**
 * Registers Custom Post Type and taxonomies on init.
 *
 * @since 1.0.0
 */
function fyv_venue_init() {

	$labels = [
		'name' => _x( 'Venues', 'post type general name', 'fyvent' ),
		'singular_name' => _x( 'Venue', 'post type singular name', 'fyvent' ),
		'add_new' => _x( 'Add New', 'Venue', 'fyvent' ),
		'add_new_item' => esc_html__( 'Add New Venue', 'fyvent' ),
		'edit_item' => esc_html__( 'Edit Venue', 'fyvent' ),
		'new_item' => esc_html__( 'New Venue', 'fyvent' ),
		'all_items' => esc_html__( 'All Venues', 'fyvent' ),
		'view_item' => esc_html__( 'View Venue', 'fyvent' ),
		'search_items' => esc_html__( 'Search Venues', 'fyvent' ),
		'not_found' => esc_html__( 'No venue found', 'fyvent' ),
		'not_found_in_trash' => esc_html__( 'No Venue found in the Trash', 'fyvent' ),
		'parent_item_colon' => '',
		'menu_name' => esc_html__( 'Venues', 'fyvent' ),
	];

	$args = [
		'labels' => $labels,
		'description' => esc_html__( 'Displays Venues', 'fyvent' ),
		'public' => true,
		'menu_position' => 7,
		'supports' => [ 'title', 'editor', 'thumbnail' ],
		'has_archive' => true,
		'map_meta_cap' => true,
	//	'capability_type' => [ 'venue', 'venues' ],
		'capability_type'    => 'post',
		'menu_icon' => 'dashicons-format-chat',
	];

	register_post_type( 'venue', $args );

}  //fyv_venue_init()

/**
 * Defines the metabox and field configurations.
 *
 * @since 1.0.0
 */
function fyv_venue_metabox() {

	// Initiate the metabox
	$cmb = new_cmb2_box(
		[
			'id' => 'venue_additional_info',
			'title' => esc_html__( 'Additional Information', 'fyvent' ),
			'object_types' => [ 'venue' ], // Post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Image', 'fyvent' ),
			'desc' => esc_html__( 'Add an image of the venue', 'fyvent' ),
			'id' => 'venue_image',
			'type' => 'file',
			'preview_size' => 'large', // Image size to use when previewing in the admin.
			// query_args are passed to wp.media's library query.
			'query_args' => [
					'type' => [
						'image/gif',
						'image/jpeg',
						'image/png',
					],
			],
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Location', 'fyvent' ),
			'desc' => esc_html__( 'Drag the marker to the location of your venue', 'fyvent' ),
			'id' => 'venue_location',
			'type' => 'pw_map',
		]
	);


} // fyv_venue_metabox()

// Run the venue init on init.
add_action( 'init', 'fyv_venue_init' );

add_action( 'cmb2_admin_init', 'fyv_venue_metabox' );