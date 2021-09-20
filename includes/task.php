<?php

// Update the columns shown on the custom post type edit.php view - so we also have custom columns
add_filter( 'manage_task_posts_columns', 'task_columns' );
// this fills in the columns that were created with each individual post's value
add_action( 'manage_task_posts_custom_column', 'fill_task_columns', 10, 2 );
// this makes columns sortable
add_filter( 'manage_edit-task_sortable_columns', 'task_sortable_columns');
//change proposals query to sort by votes
//add_action( 'pre_get_posts', 'tasks_orderby' );
//hook to change columns width
//add_action('admin_head', 'task_column_width');


/**
 * Specifies sortable columns in the admin table.
 *
 * @since 1.0.0
 *
 * @param array $columns Array of column names used in the admin table.
 * @return array Array of column names used in the admin table.
 */
function task_sortable_columns( $columns ) {
	$columns['due_date'] = 'due_date';
	$columns['assigned_to'] = 'assigned_to';
	$columns['priority'] = 'priority';
	return $columns;
}


/**
 * Adapts column widths to lenght of fields
 *
 * Echoes some CSS to override the standard WordPress admin table column width.
 *
 * @since 1.0.0
 */
function task_column_width() {
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
function task_columns($columns){
	return array(
				'cb' => '<input type="checkbox" />',
				'title' => esc_html__( 'Task', 'fyvent' ),
				'due_date' => esc_html__( 'Due Date', 'fyvent' ),
				'assigned_to' =>esc_html__( 'Asigned To', 'fyvent' ),
				'done' =>esc_html__( 'Done', 'fyvent' ),
				'priority' =>esc_html__( 'Priority', 'fyvent' ),
				);
}

/**
 * Specific code needed to show some of the fields in the tasks admin table.
 *
 * @since 1.0.0
 *
 * @param array $column Column that we are preparing to show.
 * @param int $post_id ID of the post (task) that we are showing.
 */
function fill_task_columns( $column, $post_id ) {

	$post = get_post( $post_id );

	// Fill in the columns with meta box info associated with each post or formatted content
	switch ( $column ) {
	case 'due_date' :
		$due_date = get_post_meta( $post_id , 'task_due_date' , true );
		echo $due_date;
		break;
	case 'assigned_to' :
		$assigned_to = get_post_meta( $post_id , 'task_assigned_to' , true );
		echo $assigned_to;
		break;
	case 'done' :
		$done = get_post_meta( $post_id , 'task_done' , true );
		echo $done;
		break;
	case 'priority' :
		$priority = get_post_meta( $post_id , 'task_priority' , true );
		echo $priority;
		break;
	}
}

/**
 * Registers Custom Post Type and taxonomies on init.
 *
 * @since 1.0.0
 */
function fyv_task_init() {

	$labels = [
		'name' => _x( 'Tasks', 'post type general name', 'fyvent' ),
		'singular_name' => _x( 'Task', 'post type singular name', 'fyvent' ),
		'add_new' => _x( 'Add New', 'Task', 'fyvent' ),
		'add_new_item' => esc_html__( 'Add New task', 'fyvent' ),
		'edit_item' => esc_html__( 'Edit task', 'fyvent' ),
		'new_item' => esc_html__( 'New task', 'fyvent' ),
		'all_items' => esc_html__( 'All tasks', 'fyvent' ),
		'view_item' => esc_html__( 'View task', 'fyvent' ),
		'search_items' => esc_html__( 'Search tasks', 'fyvent' ),
		'not_found' => esc_html__( 'No task found', 'fyvent' ),
		'not_found_in_trash' => esc_html__( 'No task found in the Trash', 'fyvent' ),
		'parent_item_colon' => '',
		'menu_name' => esc_html__( 'Tasks', 'fyvent' ),
	];

	$args = [
		'labels' => $labels,
		'description' => esc_html__( 'Displays tasks', 'fyvent' ),
		'public' => true,
		'menu_position' => 11,
		'supports' => [ 'title', 'editor' ],
		'has_archive' => true,
		'map_meta_cap' => true,
		'capability_type' => [ 'task', 'tasks' ],
	//	'capability_type'    => 'post',
		'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="black" d="M139.61 35.5a12 12 0 0 0-17 0L58.93 98.81l-22.7-22.12a12 12 0 0 0-17 0L3.53 92.41a12 12 0 0 0 0 17l47.59 47.4a12.78 12.78 0 0 0 17.61 0l15.59-15.62L156.52 69a12.09 12.09 0 0 0 .09-17zm0 159.19a12 12 0 0 0-17 0l-63.68 63.72-22.7-22.1a12 12 0 0 0-17 0L3.53 252a12 12 0 0 0 0 17L51 316.5a12.77 12.77 0 0 0 17.6 0l15.7-15.69 72.2-72.22a12 12 0 0 0 .09-16.9zM64 368c-26.49 0-48.59 21.5-48.59 48S37.53 464 64 464a48 48 0 0 0 0-96zm432 16H208a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h288a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16zm0-320H208a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h288a16 16 0 0 0 16-16V80a16 16 0 0 0-16-16zm0 160H208a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h288a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16z"/></svg>'),
	];

	register_post_type( 'task', $args );

}  //fyv_task_init()

function fyv_add_admin_capabilities() {

	$roles = array( 'editor', 'administrator' );

    // Loop through each role and assign capabilities
    foreach( $roles as $the_role ) {
		$role = get_role( $the_role );

		$role->add_cap( 'read_task' );
		$role->add_cap( 'edit_task' );
		$role->add_cap( 'edit_tasks' );
		$role->add_cap( 'edit_other_tasks' );
		$role->add_cap( 'edit_published_tasks' );
		$role->add_cap( 'publish_tasks' );
		$role->add_cap( 'read_private_tasks' );
		$role->add_cap( 'delete_tasks' );
    }

}
add_action( 'init', 'fyv_add_admin_capabilities' );

/**
 * Defines the metabox and field configurations.
 *
 * @since 1.0.0
 */
function fyv_task_metabox() {

	// Initiate the metabox
	$cmb = new_cmb2_box(
		[
			'id' => 'task_additional_info',
			'title' => esc_html__( 'Task Information', 'fyvent' ),
			'object_types' => [ 'task' ], // Post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Due Date', 'fyvent' ),
			'id' => 'task_due_date',
			'type' => 'text_date',
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Done', 'fyvent' ),
			'id' => 'task_done',
		    'type' => 'checkbox',
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Priority', 'fyvent' ),
			'id' => 'task_priority',
			'type'             => 'select',
    		'show_option_none' => false,
			'default'          => 'medium',
			'options'          => array(
				'high' => __( 'High', 'fyvent' ),
				'medium'   => __( 'Medium', 'fyvent' ),
				'low'     => __( 'Low', 'fyvent' ),
				),
		]
	);

	$cmb->add_field(
		[
			'name' => esc_html__( 'Assigned To', 'fyvent' ),
			'id' => 'task_assigned_to',
			'type'	=> 'text',
		]
	);


	$cmb->add_field( array(
	    'name' => esc_html__( 'Notes', 'fyvent' ),
	    'id' => 'notes',
	    'type' => 'textarea_small'
	) );


} // fyv_task_metabox()

// Run the task init on init.
add_action( 'init', 'fyv_task_init' );

add_action( 'cmb2_admin_init', 'fyv_task_metabox' );