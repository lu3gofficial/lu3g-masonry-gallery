<?php
/**
 * Plugin Name:       LU3G Masonry Gallery
 * Description:       Widget Elementor che trasforma un campo repeater JetEngine (foto + link) in una gallery masonry animata. Ogni foto porta al suo link, senza lightbox.
 * Version:           1.7.2
 * Plugin URI:        https://github.com/lu3gofficial/lu3g-masonry-gallery
 * Update URI:        https://github.com/lu3gofficial/lu3g-masonry-gallery
 * Author:            LU3G Agenzia Web
 * Text Domain:       lu3g-masonry-gallery
 * Requires PHP:      7.4
 * Requires at least: 6.0
 * Elementor tested up to: 3.30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LU3G_MG_VERSION', '1.7.2' );
define( 'LU3G_MG_FILE', __FILE__ );
define( 'LU3G_MG_PATH', plugin_dir_path( __FILE__ ) );
define( 'LU3G_MG_URL', plugin_dir_url( __FILE__ ) );
define( 'LU3G_MG_IMAGE_SIZE', 'lu3g_masonry' );

/**
 * Dimensione immagine dedicata: 1000px di larghezza, nessun ritaglio.
 * Vale per i nuovi upload; per le foto già caricate usare "Regenerate Thumbnails".
 */
add_action( 'after_setup_theme', function () {
	add_image_size( LU3G_MG_IMAGE_SIZE, 1000, 0, false );
} );

add_filter( 'image_size_names_choose', function ( $sizes ) {
	$sizes[ LU3G_MG_IMAGE_SIZE ] = 'LU3G Masonry (1000px)';
	return $sizes;
} );

/**
 * Asset (caricati solo quando il widget è presente nella pagina).
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'lu3g-masonry-gallery', LU3G_MG_URL . 'assets/css/masonry.css', array(), LU3G_MG_VERSION );
	wp_register_script( 'lu3g-masonry-gallery', LU3G_MG_URL . 'assets/js/masonry.js', array(), LU3G_MG_VERSION, true );
} );

/**
 * Registrazione widget Elementor.
 */
add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
	require_once LU3G_MG_PATH . 'includes/class-widget.php';
	$widgets_manager->register( new \LU3G_Masonry_Gallery_Widget() );
} );

/**
 * Avviso se Elementor non è attivo.
 */
add_action( 'admin_notices', function () {
	if ( did_action( 'elementor/loaded' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>LU3G Masonry Gallery</strong> richiede Elementor attivo.</p></div>';
} );

/**
 * Aggiornamenti automatici dalle release GitHub di https://github.com/lu3gofficial/lu3g-masonry-gallery (vedi includes/class-updater.php).
 * Fuori da ogni controllo su Elementor: il plugin si aggiorna anche dove Elementor è disattivato.
 */
require_once __DIR__ . '/includes/class-updater.php';
LU3G_Masonry_Gallery_Updater::init( __FILE__ );
