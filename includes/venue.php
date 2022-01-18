<?php

// Update the columns shown on the custom post type edit.php view - so we also have custom columns
add_filter( 'manage_venue_posts_columns', 'venue_columns' );
// this fills in the columns that were created with each individual post's value
add_action( 'manage_venue_posts_custom_column', 'fill_venue_columns', 10, 2 );
// this makes columns sortable
add_filter( 'manage_edit-venue_sortable_columns', 'venue_sortable_columns');
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
		$address = get_post_meta( $post_id , 'venue_location' , true );
		if( !empty( $address ) && is_string( $address ) ){
			echo esc_html( $address );
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
				$link = esc_url( 'post.php?post='.$post->ID.'&action=edit' );
				$room_title = esc_html( $post->post_title );
				echo '<a href="'.$link.'">'.$room_title.'</a><br/>';
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
		'menu_position' => 15,
		'supports' => [ 'title', 'editor', 'thumbnail' ],
		'has_archive' => true,
		'map_meta_cap' => true,
		'capability_type'    => 'post',
		'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="black" d="M501.62 92.11L267.24 2.04a31.958 31.958 0 0 0-22.47 0L10.38 92.11A16.001 16.001 0 0 0 0 107.09V144c0 8.84 7.16 16 16 16h480c8.84 0 16-7.16 16-16v-36.91c0-6.67-4.14-12.64-10.38-14.98zM64 192v160H48c-8.84 0-16 7.16-16 16v48h448v-48c0-8.84-7.16-16-16-16h-16V192h-64v160h-96V192h-64v160h-96V192H64zm432 256H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h480c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"/></svg>'),
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
			'desc' => esc_html__( 'Input the address of the venue', 'fyvent' ),
			'id' => 'venue_location',
			'type' => 'text',
		]
	);

	$cmb->add_field( array(
		'name'    => esc_html__( 'Rooms', 'fyvent' ),
		'desc'    => esc_html__( 'Drag posts from the left column to the right column to attach them to this page.<br />You may rearrange the order of the posts in the right column by dragging and dropping.', 'fyvent' ),
		'id'      => 'rooms',
		'type'    => 'custom_attached_posts',
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


/**
 * Shows the info of a venue.
 *
 * @since 1.0.0
 */
function fyv_show_venue( $atts = [] ){

	// normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER );

	global $wp_query;
	if( $wp_query->query_vars['venue'] ){
		$atts['id'] = $wp_query->query_vars['venue'];
	}

	//if there is an id we show the speaker corresponding to that id. If not, list all speakers
    if( $atts['id'] && ( get_post_type( $atts['id'] ) == 'venue' )  ){
    	$id = $atts['id'];
    	$venue = get_post( $id, ARRAY_A );
		?>
		<div class="venue-info">
			<div <?php echo esc_html( fyv_classes( 'venue-image-one' ) ); ?> >
				<?php echo get_the_post_thumbnail( $id, 'large', array( 'class' => 'aligncenter' ) ); ?>
			</div>
			<div <?php echo esc_html( fyv_classes( 'venue-info-one' ) ); ?> >
				<h2><?php echo get_the_title( $id ); ?></h2>
				<h4><?php echo get_post_meta( $id, 'venue_location', true ); ?></h4>
				<div class="venue-description"><?php echo get_the_content( null, false, $id ); ?></div>
				<?php
				$rooms = get_post_meta( $id, 'rooms', false );
				if( $rooms ){
					echo '<strong>'.esc_html__( 'Rooms: ', 'fyvent' );
					foreach( $rooms[0] as $room ){
						echo '<a href="'.get_permalink( $room ).'">'.get_the_title( $room ).'</a>&nbsp;';
					}
				}
			echo '</div>';
		echo '</div>';
    } else {
    	$loop = new WP_Query( array( 'post_type' => 'venue', 'paged' => $paged ) );
	    if( $loop->have_posts() ){
	        while ( $loop->have_posts() ) : $loop->the_post();
	        ?>
    		<div <?php echo esc_html( fyv_classes( 'venue-list' ) ); ?> >
				<h4><?php
					echo '<a href="/venues/?venue_id='.get_the_id().'">'.get_the_title( ).'</a>';
					?></h4>
				<p <?php echo esc_html( fyv_classes( 'venue-content' ) ); ?> ><?php echo get_the_content(); ?></p>
			</div>
			<?php endwhile;
			if (  $loop->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( esc_html__( '<span class="meta-nav">&larr;</span> Previous', 'fyvent' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( esc_html__( 'Next <span class="meta-nav">&rarr;</span>', 'fyvent' ) ); ?></div>
				</div>
			<?php endif;
	    } else {
	    	echo esc_html__( 'No venues found', 'fyvent' );
	    }
		wp_reset_postdata();
    }
}