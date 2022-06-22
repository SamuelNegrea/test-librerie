<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '2.5.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_load_textdomain', [ true ], '2.0', 'hello_elementor_load_textdomain' );
		if ( apply_filters( 'hello_elementor_load_textdomain', $hook_result ) ) {
			load_theme_textdomain( 'hello-elementor', get_template_directory() . '/languages' );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_menus', [ true ], '2.0', 'hello_elementor_register_menus' );
		if ( apply_filters( 'hello_elementor_register_menus', $hook_result ) ) {
			register_nav_menus( [ 'menu-1' => __( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => __( 'Footer', 'hello-elementor' ) ] );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_theme_support', [ true ], '2.0', 'hello_elementor_add_theme_support' );
		if ( apply_filters( 'hello_elementor_add_theme_support', $hook_result ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'classic-editor.css' );

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support( 'align-wide' );

			/*
			 * WooCommerce.
			 */
			$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_woocommerce_support', [ true ], '2.0', 'hello_elementor_add_woocommerce_support' );
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', $hook_result ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$enqueue_basic_style = apply_filters_deprecated( 'elementor_hello_theme_enqueue_style', [ true ], '2.0', 'hello_elementor_enqueue_style' );
		$min_suffix          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', $enqueue_basic_style ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_elementor_locations', [ true ], '2.0', 'hello_elementor_register_elementor_locations' );
		if ( apply_filters( 'hello_elementor_register_elementor_locations', $hook_result ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

/**
 * If Elementor is installed and active, we can load the Elementor-specific Settings & Features
*/

// Allow active/inactive via the Experiments
require get_template_directory() . '/includes/elementor-functions.php';

/**
 * Include customizer registration functions
*/
function hello_register_customizer_functions() {
	if ( hello_header_footer_experiment_active() && is_customize_preview() ) {
		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_register_customizer_functions' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * Wrapper function to deal with backwards compatibility.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		} else {
			do_action( 'wp_body_open' );
		}
	}
}

function add_custom_scripts() {
	if ( is_page('esempio') )
	{

		wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/assets/js/chiamata_ajax.js', array("jquery"));
		wp_localize_script('custom-js', 'ajax_var', array(
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax-nonce')
		));
    	wp_register_style('custom-css0', get_stylesheet_directory_uri() . '/assets/css/page-esempio-mystyle.css');

    	wp_enqueue_style('custom-css0');
	}
	if ( is_page('template') )
	{

    	wp_register_style('custom-css1', get_stylesheet_directory_uri() . '/assets/css/page-template-mystyle.css');

    	wp_enqueue_style('custom-css1');
	}
	if ( is_singular('prova') )
	{

    	wp_register_style('custom-css2', get_stylesheet_directory_uri() . '/assets/css/page-template-cpt-mystyle.css');

    	wp_enqueue_style('custom-css2');
	}
	if ( is_page('archive-template') )
	{

    	wp_register_style('custom-css3', get_stylesheet_directory_uri() . '/assets/css/page-archive-template-mystyle.css');

    	wp_enqueue_style('custom-css3');
	}

	if ( is_page('test-librerie') ){
		//TODO caricare le librerie
		wp_register_style( 'sweetalertcss', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.4.17/dist/sweetalert2.min.css');
		wp_enqueue_style('sweetalertcss');
		wp_enqueue_script( 'sweetalertJS', '//cdn.jsdelivr.net/npm/sweetalert2@11', array ( 'jquery' ), 1.1, true);
		wp_enqueue_script( 'sweetalertFunction', get_template_directory_uri() . '/includes/librerie/sweetalert2.js', array ( 'jquery' ), 1.1, true);

		wp_register_style( 'aosCSS', 'https://unpkg.com/aos@2.3.1/dist/aos.css');
		wp_enqueue_style('aosCSS');
		wp_enqueue_script( 'aosJS', 'https://unpkg.com/aos@2.3.1/dist/aos.js', array ( 'jquery' ), 1.1, true);
		wp_enqueue_script( 'aosFunction', get_template_directory_uri() . '/includes/librerie/aos.js', array ( 'jquery' ), 1.1, true);
		wp_enqueue_script( 'MasonryJS', 'https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js', array ( 'jquery' ), 1.1, true);
		wp_enqueue_script( 'MasonryFunction', get_template_directory_uri() . '/includes/librerie/Masonry.js', array ( 'jquery' ), 1.1, true);
		
	}
	
}

add_action('wp_enqueue_scripts', 'add_custom_scripts');


add_action('rest_api_init', 'myFunction');

function myFunction(){
	register_rest_route( 'proviamo', '/esempio', array(
        'methods' => 'GET',
        'callback' => 'get_fullname',

	) );

	register_rest_route( 'secondaprova', '/esempio', array(
        'methods' => 'GET',
        'callback' => 'get_all_user_data',

	) );

	register_rest_route( 'terzaprova', '/provalogin', array(
        'methods' => 'GET',
        'callback' => 'wp_nonce_field',

	) );
}


function get_fullname($request){
	
	return $request['nome'] . ' ' . $request['cognome'];
}

function get_all_user_data($request){
	$page = get_page_by_path('esempio');

	$dati = array("editor"=>get_field('editor', $page->ID),"immagini"=>get_field('immagini', $page->ID),"testo"=>get_field('testo', $page->ID),
	"cellulare"=>get_field('cellulare', $page->ID),"email"=>get_field('email', $page->ID),"password"=>get_field('password', $page->ID),"checkbox"=>get_field('checkbox', $page->ID),
	"radiobutton"=>get_field('radiobutton', $page->ID),"true"=>get_field('true', $page->ID),"false"=>get_field('false', $page->ID),"link"=>get_field('link', $page->ID),
	"mappa"=>get_field('mappa', $page->ID),"messaggio"=>get_field('messaggio', $page->ID),"utente"=>get_field('utente', $page->ID),"ora"=>get_field('ora', $page->ID) );

	return $dati;
}


/*function wp_nonce_field( $action = -1, $name = '_wpnonce', $referer = true, $echo = true ) {
    $name        = esc_attr( $name );
    $nonce_field = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . wp_create_nonce( $action ) . '" />';
 
    if ( $referer ) {
        $nonce_field .= wp_referer_field( false );
    }
 
    if ( $echo ) {
        echo $nonce_field;
    }
 
    return $nonce_field;
}*/

add_action('wp_ajax_nopriv_chiamata_con_nonce', 'chiamata_con_nonce_function_non_loggato');    
add_action('wp_ajax_chiamata_con_nonce', 'chiamata_con_nonce_function');

function chiamata_con_nonce_function() {
	if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
		die ( 'Busted!');
	}
	$result = "chiamata con nonce eseguita";
	echo $result;

	die();
}

function chiamata_con_nonce_function_non_loggato() {
	if (  wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
		die ( 'Busted!');
	}

	die();
}