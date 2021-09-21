<?php

function fyv_attendant_role() {

    //add the attendant role
    add_role(
        'attendant',
        'Attendant',
        array(
            'read'         => true,
        )
    );

}
add_action('admin_init', 'fyv_attendant_role');

/**
 * Hook in and add a metabox to add fields to the user profile pages
 */
function fyv_register_attendant_profile_metabox( $user_id ) {

	$prefix = 'fyv_attendant_';

	/**
	 * Metabox for the user profile screen
	 */
	$cmb_user = new_cmb2_box( array(
		'id'               => $prefix . 'edit',
		'title'            => __( 'Attendant Information', 'fyvent' ), // Doesn't output for user boxes
		'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
		'new_user_section' => 'add-existing-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
		'show_on_cb'	=> 'fyv_show_meta_to_chosen_roles',
		'show_on_roles' => array( 'attendant' ),
	) );

	$cmb_user->add_field( array(
		'name'     => __( 'Extra Info', 'fyvent' ),
		'id'       => $prefix . 'extra_info',
		'type'     => 'title',
		'on_front' => false,
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Gender', 'fyvent' ),
		'type'    => 'select',
		'id'   => $prefix . 'gender',
    	'show_option_none' => false,
		'default'          => 'dnda',
		'options'          => array(
			'male' => __( 'Male', 'fyvent' ),
			'female'   => __( 'Female', 'fyvent' ),
			'other'   => __( 'Other', 'fyvent' ),
			'dnda' => __( 'I prefer not to say', 'fyvent' ),
		),
	) );

	$cmb_user->add_field( array(
		'name' => __( 'Position', 'fyvent' ),
		'id'   => $prefix . 'position',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => __( 'Organization', 'fyvent' ),
		'id'   => $prefix . 'organization',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => __( 'City', 'fyvent' ),
		'id'   => $prefix . 'city',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => __( 'Country', 'fyvent' ),
		'id'   => $prefix . 'country',
		'type' => 'text',
	) );

	$cmb_user->add_field( array(
		'name' => __( 'I have read and agree with the <a href="privacy">privacy rules</a> for this event', 'fyvent' ),
		'id'   => $prefix . 'gpdr',
		'type' => 'checkbox',
	) );

	if(  current_user_can( 'edit_users' ) ){

		$options = get_option('fyv_settings');
		if( $options['fyv_attendant_types'] != "" ){
			$attendant_types = array_map( 'trim', explode( ',', $options['fyv_attendant_types'] ) );
			$cmb_user->add_field(
				[
				    'name'             => esc_html__( 'Type of attendant', 'fyvent' ),
				    'desc'             => esc_html__( 'Select the type of attendant', 'fyvent' ),
				    'id'               => 'type',
				    'type'             => 'select',
				    'show_option_none' => false,
				    'options'          => $attendant_types,
				]
			);
		}
		$cmb_user->add_field( array(
			'name' => __( 'Attended', 'fyvent' ),
			'id'   => $prefix . 'attended',
			'type' => 'checkbox',
		) );
		$cmb_user->add_field( array(
			'name' => __( 'Paid', 'fyvent' ),
			'id'   => $prefix . 'paid',
			'type' => 'checkbox',
		) );
	}


}
add_action( 'cmb2_admin_init', 'fyv_register_attendant_profile_metabox' );