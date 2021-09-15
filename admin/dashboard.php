<?php

/**
 * Shows relevant statistics on WordPress dashboard
 *
 * @since 1.0.0
**/

$locale = get_user_locale();
setlocale( LC_NUMERIC, $locale );
$locale_num = localeconv();
$thousands_separator = $locale_num['thousands_sep'];
if( $thousands_separator == '' ){
	$thousands_separator = ' ';
}
$decimal_point = $locale_num['decimal_point'];

?>
<div style="text-align:center;margin-bottom:48px;font-size:2em;"><h1><?php echo esc_html__( 'Dashboard', 'fyvent' ); ?></h1></div>
<div style="text-align:center;margin-bottom:48px;border-top-style:solid;border-color:grey;margin:24px;">
	<p style="font-size:4em;margin:24px;">
		<?php
			echo esc_html__( 'Users: ', 'fyvent' );
			$result = count_users();
			echo '<strong>'.number_format( $result['total_users'], 0, $decimal_point, $thousands_separator ).'</strong>';
		?>
	</p>
	<p style="font-size:2em;">
		<?php
			echo esc_html__( 'Validated Users: ', 'fyvent' );
			global $wpdb;
		    $table_name = $wpdb->prefix . 'fyv_userinfo';
	    	$count_query = "select count(*) from $table_name WHERE validated=1";
	    	$result = $wpdb->get_var($count_query);
			echo '<strong>'.number_format( $result, 0, $decimal_point, $thousands_separator ).'</strong>';
		?>
	</p>
	<p style="font-size:2em;">
		<?php
		echo esc_html__( 'Male: ', 'fyvent' );
		global $wpdb;
	    $table_name = $wpdb->prefix . 'fyv_userinfo';
		$count_query = "select count(*) from $table_name WHERE sex='Male'";
		$result = $wpdb->get_var($count_query);
		echo '<strong>'.number_format( $result, 0, $decimal_point, $thousands_separator ).'</strong>';
		echo ' | '.esc_html__( 'Female: ', 'fyvent' );
			global $wpdb;
	    $table_name = $wpdb->prefix . 'fyv_userinfo';
		$count_query = "select count(*) from $table_name WHERE sex='Female'";
		$result = $wpdb->get_var($count_query);
		echo '<strong>'.number_format( $result, 0, $decimal_point, $thousands_separator ).'</strong>';
		?>
	</p>
</div>
<div style="text-align:center;margin-bottom:48px;border-top-style:solid;border-color:grey;margin:24px;">
	<p style="font-size:2em;">
		<?php
			echo esc_html__( 'Proposals: ', 'fyvent' );
			$result = wp_count_posts( 'proposal' );
			echo '<strong>'.number_format( $result->publish, 0, $decimal_point, $thousands_separator ).'</strong>';
		?>
	</p>
	<p style="font-size:2em;">
		<?php
			echo esc_html__( 'Debates: ', 'fyvent' );
			$result = wp_count_posts( 'debate' );
			echo '<strong>'.number_format( $result->publish, 0, $decimal_point, $thousands_separator ).'</strong>';
		?>
	</p>
	<p style="font-size:2em;">
		<?php
			echo esc_html__( 'Votes: ', 'fyvent' );
			global $wpdb;
		    $table_name = $wpdb->prefix . 'fyv_votes';
	    	$count_query = "select count(*) from $table_name";
	    	$result = $wpdb->get_var($count_query);
			echo '<strong>'.number_format( $result, 0, $decimal_point, $thousands_separator ).'</strong>';
		?>
	</p>
	<p style="font-size:2em;">
		<?php
			echo esc_html__( 'Comments: ', 'fyvent' );
			$result = wp_count_comments();
			echo '<strong>'.number_format( $result->approved, 0, $decimal_point, $thousands_separator ).'</strong>';
		?>
	</p>
</div>