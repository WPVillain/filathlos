<?php
/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB2 directory)
 *
 * Be sure to replace all instances of 'filathlos_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category YourThemeOrPlugin
 * @package  Demo_CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

add_action( 'cmb2_init', 'filathlos_add_carousel_metabox' );
function filathlos_add_carousel_metabox() {

	$prefix = '_filathloscarousel_';

	$cmb = new_cmb2_box( array(
		'id'           => $prefix . 'carousel_fields',
		'title'        => __( 'Carousel Fields', 'filathlos' ),
		'object_types' => array( 'slider' ),
		'context'      => 'normal',
		'priority'     => 'high',
	) );


	$cmb->add_field( array(
		'name' => __( 'Carousel Sub Header', 'filathlos' ),
		'id' => $prefix . 'carousel_sub_header',
		'type' => 'text',
		'default' => 'sub header',
		'desc' => __( 'Carousel Sub Header', 'filathlos' ),
	) );

	$cmb->add_field( array(
		'name' => __( 'Call to Action Button', 'filathlos' ),
		'id' => $prefix . 'carousel_cta',
		'type' => 'text',
		'default' => 'cta',
		'desc' => __( 'Call to Action Button', 'filathlos' ),
	) );

	$cmb->add_field( array(
		'name' => __( 'The Call To Action url or link', 'filathlos' ),
		'id' => $prefix . 'carousel_cta_url',
		'type' => 'text',
		'default' => 'https:/imagewize.com',
		'desc' => __( 'The Call To Action url or link', 'filathlos' ),
	) );

}