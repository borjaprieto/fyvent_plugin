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
        .column-location { text-align: left; overflow:hidden }
		.column-rooms { text-align: overflow:hidden }
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
				'location' => esc_html__( 'Location', 'fyvent' ),
				'rooms' =>esc_html__( 'Rooms', 'fyvent' ),
				);
}

/**
 * Specific code needed to show some of the fields in the venues admin table.
 *
 * @since 1.0.0
 *
 * @param array $column Column that we are preparing to show.
 * @param int $post_id ID of the post (venue) that we are showing.
 */
function fill_venue_columns( $column, $post_id ) {

	$post = get_post( $post_id );

	// Fill in the columns with meta box info associated with each post or formatted content
	switch ( $column ) {
	case 'location' :
		$coords = get_post_meta( $post_id , 'venue_location' , true );
		if( is_array( $coords ) ){
			echo fyv_getaddress( $coords['latitude'], $coords['longitude'] );
		} else {
			echo '---';
		}
		break;
	case 'rooms' :
		$show = array();
		$rooms = get_post_meta( $post_id , 'rooms' , true );
		if( $rooms ){
			foreach ( $rooms as $room ) {
				$post = get_post( $room );
				echo '<a href="post.php?post='.$post->ID.'&action=edit">'.$post->post_title.'</a><br/>';
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
			'name' => esc_html__( 'Location', 'fyvent' ),
			'desc' => esc_html__( 'Drag the marker to the location of your venue', 'fyvent' ),
			'id' => 'venue_location',
			'type' => 'pw_map',
		]
	);

	$cmb->add_field( array(
		'name'    => __( 'Rooms', 'fyvent' ),
		'desc'    => __( 'Drag posts from the left column to the right column to attach them to this page.<br />You may rearrange the order of the posts in the right column by dragging and dropping.', 'fyvent' ),
		'id'      => 'rooms',
		'type'    => 'custom_attached_posts',
		//'column'  => true, // Output in the admin post-listing as a custom column. https://github.com/CMB2/CMB2/wiki/Field-Parameters#column
		'options' => array(
			'show_thumbnails' => true, // Show thumbnails on the left
			'filter_boxes'    => true, // Show a text box for filtering the results
			'query_args'      => array(
				'posts_per_page' => 10,
				'post_type'      => 'room',
			), // override the get_posts args
		),
	) );

	$cmb->add_field( array(
	    'name' => esc_html__( 'Notes', 'fyvent' ),
	    'id' => 'notes',
	    'type' => 'textarea_small'
	) );


} // fyv_venue_metabox()

// Run the venue init on init.
add_action( 'init', 'fyv_venue_init' );

add_action( 'cmb2_admin_init', 'fyv_venue_metabox' );