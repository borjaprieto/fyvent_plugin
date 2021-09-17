<?php

// Update the columns shown on the custom post type edit.php view - so we also have custom columns
add_filter( 'manage_vendor_posts_columns', 'vendor_columns' );
// this fills in the columns that were created with each individual post's value
add_action( 'manage_vendor_posts_custom_column', 'fill_vendor_columns', 10, 2 );
// this makes columns sortable
add_filter( 'manage_edit-vendor_sortable_columns', 'vendor_sortable_columns');
//change proposals query to sort by votes
//add_action( 'pre_get_posts', 'vendors_orderby' );
//hook to change columns width
//add_action('admin_head', 'vendor_column_width');

/**
 * Specifies sortable columns in the admin table.
 *
 * @since 1.0.0
 *
 * @param array $columns Array of column names used in the admin table.
 * @return array Array of column names used in the admin table.
 */
function vendor_sortable_columns( $columns ) {
	$columns['company'] = 'company';
	$columns['lastname'] = 'lastname';
	return $columns;
}


/**
 * Adapts column widths to lenght of fields
 *
 * Echoes some CSS to override the standard WordPress admin table column width.
 *
 * @since 1.0.0
 */
function vendor_column_width() {
    echo '<style type="text/css">
        .column-firstname { text-align: left; overflow:hidden }
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
function vendor_columns($columns){
	return array(
				'cb' => '<input type="checkbox" />',
				'firstname' => esc_html__( 'First Name', 'fyvent' ),
				'lastname' => esc_html__( 'Last Name', 'fyvent' ),
				'company' =>esc_html__( 'Company', 'fyvent' ),
				'position' =>esc_html__( 'Position', 'fyvent' ),
				'providing' =>esc_html__( 'Providing', 'fyvent' ),
				'phone' =>esc_html__( 'Phone', 'fyvent' ),
				'email' =>esc_html__( 'Email', 'fyvent' ),
				);
}

/**
 * Specific code needed to show some of the fields in the vendors admin table.
 *
 * @since 1.0.0
 *
 * @param array $column Column that we are preparing to show.
 * @param int $post_id ID of the post (vendor) that we are showing.
 */
function fill_vendor_columns( $column, $post_id ) {

	$post = get_post( $post_id );

	// Fill in the columns with meta box info associated with each post or formatted content
	switch ( $column ) {
	case 'firstname' :
		$firstname = get_post_meta( $post_id , 'vendor_firstname' , true );
		echo $firstname;
		break;
	case 'lastname' :
		$lastname = get_post_meta( $post_id , 'vendor_lastname' , true );
		echo $lastname;
		break;
	case 'company' :
		$company = get_post_meta( $post_id , 'vendor_company' , true );
		echo $company;
		break;
	case 'position' :
		$position = get_post_meta( $post_id , 'vendor_position' , true );
		echo $position;
		break;
	case 'providing' :
		$providing = get_post_meta( $post_id , 'vendor_providing' , true );
		echo $providing;
		break;
	case 'phone' :
		$phone = get_post_meta( $post_id , 'vendor_phone' , true );
		echo $phone;
		break;
	case 'email' :
		$email = get_post_meta( $post_id , 'vendor_email' , true );
		echo $email;
		break;
	}
}

/**
 * Registers Custom Post Type and taxonomies on init.
 *
 * @since 1.0.0
 */
function fyv_vendor_init() {

	$labels = [
		'name' => _x( 'Vendors', 'post type general name', 'fyvent' ),
		'singular_name' => _x( 'Vendor', 'post type singular name', 'fyvent' ),
		'add_new' => _x( 'Add New', 'Vendor', 'fyvent' ),
		'add_new_item' => esc_html__( 'Add New vendor', 'fyvent' ),
		'edit_item' => esc_html__( 'Edit vendor', 'fyvent' ),
		'new_item' => esc_html__( 'New vendor', 'fyvent' ),
		'all_items' => esc_html__( 'All vendors', 'fyvent' ),
		'view_item' => esc_html__( 'View vendor', 'fyvent' ),
		'search_items' => esc_html__( 'Search vendors', 'fyvent' ),
		'not_found' => esc_html__( 'No vendor found', 'fyvent' ),
		'not_found_in_trash' => esc_html__( 'No vendor found in the Trash', 'fyvent' ),
		'parent_item_colon' => '',
		'menu_name' => esc_html__( 'Vendors', 'fyvent' ),
	];

	$args = [
		'labels' => $labels,
		'description' => esc_html__( 'Displays vendors', 'fyvent' ),
		'public' => true,
		'menu_position' => 7,
		'supports' => [ 'thumbnail' ],
		'has_archive' => true,
		'map_meta_cap' => true,
	//	'capability_type' => [ 'vendor', 'vendors' ],
		'capability_type'    => 'post',
		'menu_icon' => 'dashicons-building',
	];

	register_post_type( 'vendor', $args );

}  //fyv_vendor_init()

/**
 * Defines the metabox and field configurations.
 *
 * @since 1.0.0
 */
function fyv_vendor_metabox() {

	// Initiate the metabox
	$cmb = new_cmb2_box(
		[
			'id' => 'vendor_additional_info',
			'title' => esc_html__( 'Vendor Information', 'fyvent' ),
			'object_types' => [ 'vendor' ], // Post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'First Name', 'fyvent' ),
			'id' => 'vendor_firstname',
			'type' => 'text',
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Last Name', 'fyvent' ),
			'id' => 'vendor_lastname',
			'type' => 'text',
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Company', 'fyvent' ),
			'id' => 'vendor_company',
			'type' => 'text',
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Position', 'fyvent' ),
			'id' => 'vendor_position',
			'type' => 'text',
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Providing', 'fyvent' ),
			'id' => 'vendor_providing',
			'type' => 'text',
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Email', 'fyvent' ),
			'id' => 'vendor_email',
			'type' => 'text_email',
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Phone', 'fyvent' ),
			'id' => 'vendor_phone',
			'type' => 'text',
		]
	);

	$cmb->add_field( array(
	    'name' => esc_html__( 'Notes', 'fyvent' ),
	    'id' => 'notes',
	    'type' => 'textarea_small'
	) );


} // fyv_vendor_metabox()

// Run the vendor init on init.
add_action( 'init', 'fyv_vendor_init' );

add_action( 'cmb2_admin_init', 'fyv_vendor_metabox' );