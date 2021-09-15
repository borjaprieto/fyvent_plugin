<?php

require_once plugin_dir_path( __FILE__ ) . 'setup_db.php';

/**
 * Sets up the plugin (roles and admin user)
 *
 * @since 1.0.0
**/
function fyv_initial_setup(){
	fyv_set_up_roles();
	fyv_create_admin();
}

/**
 * On plugin activation, create an admin user with fyvent admin role
 *
 * @since 1.0.0
**/
function fyv_create_admin() {
    $username = 'decideadmin';
    $password = '#Democracy21';
    $email = 'name@example.com';

    if( username_exists( $username ) == null && email_exists( $email ) == false ) {
        $user_id = wp_create_user( $username, $password, $email );
        $user = get_user_by( 'id', $user_id );
        $user->remove_role( 'subscriber' );
        $user->add_role( 'fyv_admin' );
        $user->add_role( 'admin' );
    }
}

/**
 * Set up fyvent specific roles
 *
 * @since 1.0.0
**/
function fyv_set_up_roles() {

	remove_role( 'banned' );
	add_role(
		'banned', esc_html__( 'Banned', 'wpadmin' ), [
			'read' => true,
		]
	);

	remove_role( 'guest' );
	add_role(
		'guest', esc_html__( 'Guest', 'wpadmin' ), [
			'read' => true,
			'edit_posts' => true,
			'publish_posts' => true,
			'upload_files' => true,
			'create_debate' => true,
			'edit_debate' => true,
			'publish_debate' => true,
			'create_proposal' => true,
			'edit_proposal' => true,
			'publish_proposal' => true,
		]
	);

	remove_role( 'citizen' );
	add_role(
		'citizen', esc_html__( 'Citizen', 'fyvent' ), [
			'read' => true,
			'edit_posts' => true,
			'publish_posts' => true,
			'upload_files' => true,
			'create_debate' => true,
			'edit_debate' => true,
			'publish_debate' => true,
			'create_proposal' => true,
			'edit_proposal' => true,
			'publish_proposal' => true,
		]
	);

	remove_role( 'assessor' );
	add_role(
		'assessor', esc_html__( 'Assessor', 'fyvent' ), [
			'read' => true,
			'edit_others_posts' => true,
			'edit_published_posts' => true,
			'edit_posts' => true,
			'upload_files' => true,
		]
	);

	remove_role( 'moderator' );
	add_role(
		'moderator', esc_html__( 'Moderator', 'fyvent' ), [
			'read' => true,
			'create_post' => true,
			'edit_others_posts' => true,
			'edit_published_posts' => true,
			'edit_posts' => true,
			'publish_posts' => true,
			'upload_files' => true,
			'delete_posts' => true,
			'delete_published_posts' => true,
			'delete_others_posts' => true,
			'moderate_comments' => true,
			'edit_comment' => true,
			'list_users' => true,
			'edit_theme_options' => true,
		]
	);

	remove_role( 'manager' );
	add_role(
		'manager', esc_html__( 'Manager', 'fyvent' ), [
			'read' => true,
			'upload_files' => true,
			'moderate_comments' => true,
			'edit_comment' => true,
			'list_users' => true,
			'create_debate' => true,
			'create_proposal' => true,
			'edit_theme_options' => true,
			'edit_others_posts' => true,
			'edit_published_posts' => true,
			'edit_posts' => true,
			'publish_posts' => true,
		]
	);

	remove_role( 'fyv_admin' );
	add_role(
		'fyv_admin', esc_html__( 'Admin', 'fyvent' ), [
			'read' => true,
			'edit_others_posts' => true,
			'edit_published_posts' => true,
			'edit_posts' => true,
			'publish_posts' => true,
			'delete_posts' => true,
			'delete_others_posts' => true,
			'delete_published_posts' => true,
			'upload_files' => true,
			'edit_comment' => true,
			'moderate_comments' => true,
			'create_debate' => true,
			'create_proposal' => true,
			'list_users' => true,
			'edit_files' => true,
			'add_users' => true,
			'create_users' => true,
			'delete_users' => true,
			'promote_users' => true,
			'manage_categories' => true,
			'publish_pages' => true,
			'edit_pages' => true,
			'edit_others_pages' => true,
			'edit_published_pages' => true,
			'delete_pages' => true,
			'delete_others_pages' => true,
			'delete_published_pages' => true,
			'edit_theme_options' => true,
		]
	);

	// get the the role object
	$role_object = get_role( 'administrator' );
	// add $cap capability to this role object
	$role_object->add_cap( 'access_admin' );

}

