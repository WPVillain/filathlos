<?php
/**
 * Plugin Name: Imagewize Custom Post Type Slider Plugin
 * Plugin URI: https://imagewize.com
 * Description: A simple slider plugin
 * Version: 1.0.0
 * Author: Jasper Frumau
 * Author URI: https://imagewize.com
 * Text Domain: imagine
 * Domain Path: Optional. Plugin's relative directory path to .mo files. Example: /locale/
 * Network: Optional. Whether the plugin can only be activated network wide. Example: true
 * License: GPL2+
 */


if ( ! function_exists('imagine_cpt_slider') ) {

// Register Custom Post Type
function imagine_cpt_slider() {

	$labels = array(
		'name'                => _x( 'Slides', 'Post Type General Name', 'imagine' ),
		'singular_name'       => _x( 'Slide', 'Post Type Singular Name', 'imagine' ),
		'menu_name'           => __( 'Slider', 'imagine' ),
		'name_admin_bar'      => __( 'Slider', 'imagine' ),
		'parent_item_colon'   => __( 'Parent Item:', 'imagine' ),
		'all_items'           => __( 'All Items', 'imagine' ),
		'add_new_item'        => __( 'Add New Item', 'imagine' ),
		'add_new'             => __( 'Add New', 'imagine' ),
		'new_item'            => __( 'New Item', 'imagine' ),
		'edit_item'           => __( 'Edit Item', 'imagine' ),
		'update_item'         => __( 'Update Item', 'imagine' ),
		'view_item'           => __( 'View Item', 'imagine' ),
		'search_items'        => __( 'Search Item', 'imagine' ),
		'not_found'           => __( 'Not found', 'imagine' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'imagine' ),
	);
	
	$args = array(
		'label'               => __( 'slider', 'imagine' ),
		'description'         => __( 'Custom Post Types Slider', 'imagine' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', ),
		'taxonomies'          => array( ),
		'hierarchical'        => false,
		'public'              => false, // not show frontend as post
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-slides',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'slider', $args );

}

// Hook into the 'init' action
add_action( 'init', 'imagine_cpt_slider', 0 );

}

/*
*
* Page Templater to add templates to site from plugin based on code by WP Xplorer
* Page Template inclusion http://www.wpexplorer.com/wordpress-page-templates-plugin/ 
*
*/

class PageTemplater {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class. 
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new PageTemplater();
		} 

		return self::$instance;

	} 

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		$this->templates = array();


		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'register_project_templates' )
			);

		} else {

			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);

		}

		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data', 
			array( $this, 'register_project_templates' ) 
		);


		// Add a filter to the template include to determine if the page has our 
		// template assigned and return it's path
		add_filter(
			'template_include', 
			array( $this, 'view_project_template') 
		);


		// Add your templates to this array.
		$this->templates = array(
			'goodtobebad-template.php' => 'It\'s Good to Be Bad',
		);
			
	} 

	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 */
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	} 

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		
		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		} 

		$file = plugin_dir_path( __FILE__ ). get_post_meta( 
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}

} 
add_action( 'plugins_loaded', array( 'PageTemplater', 'get_instance' ) );
