<?php
/**
 * Lila Kora — Elementor Widgets Loader
 *
 * Drop this file's folder into your Astra child theme as /inc/,
 * then require it from functions.php (see snippet at the bottom
 * of this file for the exact line to add).
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

final class LK_Widgets_Loader {

	const VERSION = '1.0.0';

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// NOTE: a theme's functions.php is loaded *after* WordPress has
		// already fired 'plugins_loaded' (plugins load first, then
		// 'plugins_loaded' fires, then themes are loaded) — so hooking
		// this check onto 'plugins_loaded' from here would never run.
		// Elementor is already loaded (or not) by the time this file is
		// parsed, so we just check for it directly.
		$this->check_elementor();
	}

	/**
	 * Bail politely if Elementor isn't active — an Astra child theme
	 * shouldn't fatal a site that doesn't have it.
	 */
	public function check_elementor() {
		if ( ! did_action( 'elementor/loaded' ) && ! class_exists( '\Elementor\Plugin' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_elementor' ) );
			return;
		}

		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );

		// Front end: the standard, always-fires WordPress hook — not an
		// Elementor-specific one. This is what was silently failing
		// before: 'elementor/frontend/after_enqueue_styles' is not a
		// reliable place to hang front-end asset loading from, so the
		// Google Fonts stylesheet was never actually being printed.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// Editor panel (inside wp-admin, while editing with Elementor)
		// still needs its own hook so the canvas preview matches too.
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_styles' ) );
	}

	public function admin_notice_missing_elementor() {
		echo '<div class="notice notice-warning"><p>';
		echo 'Lila Kora widgets are installed but <strong>Elementor</strong> is not active, so they will not appear in the editor.';
		echo '</p></div>';
	}

	/**
	 * One shared stylesheet for every Lila Kora widget. Front end only
	 * needs this once, regardless of how many LK widgets are on the page.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'lk-widgets-fonts',
			'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400;1,500&family=Montserrat:wght@400;500;600&display=swap',
			array(),
			null
		);

		wp_enqueue_style(
			'lk-widgets',
			get_stylesheet_directory_uri() . '/assets/css/lk-elementor-widgets.css',
			array( 'lk-widgets-fonts' ),
			filemtime( get_stylesheet_directory() . '/assets/css/lk-elementor-widgets.css' )
		);

		wp_enqueue_script(
			'lk-header',
			get_stylesheet_directory_uri() . '/assets/js/lk-header.js',
			array(),
			filemtime( get_stylesheet_directory() . '/assets/js/lk-header.js' ),
			true
		);
	}

	/**
	 * A dedicated "Lila Kora" section at the top of the Elementor
	 * widget panel, so these don't get lost among Astra/Elementor's own.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'lila-kora',
			array(
				'title' => 'Lila Kora',
				'icon'  => 'eicon-flow',
			)
		);
	}

	public function register_widgets( $widgets_manager ) {
require_once __DIR__ . '/widgets/class-lk-hero-widget.php';
require_once __DIR__ . '/widgets/class-lk-experience-grid-widget.php';
require_once __DIR__ . '/widgets/class-lk-announcement-widget.php';
require_once __DIR__ . '/widgets/class-lk-header-widget.php';
require_once __DIR__ . '/widgets/class-lk-proof-strip-widget.php';
require_once __DIR__ . '/widgets/class-lk-commerce-strip-widget.php';
require_once __DIR__ . '/widgets/class-lk-signature-box-widget.php';
require_once __DIR__ . '/widgets/class-lk-transformation-strip-widget.php';
require_once __DIR__ . '/widgets/class-lk-how-it-works-widget.php';
require_once __DIR__ . '/widgets/class-lk-collection-feature-widget.php';
require_once __DIR__ . '/widgets/class-lk-cardigan-feature-widget.php';
require_once __DIR__ . '/widgets/class-lk-transformation-case-widget.php';
require_once __DIR__ . '/widgets/class-lk-shop-grid-widget.php';
require_once __DIR__ . '/widgets/class-lk-faq-widget.php';
require_once __DIR__ . '/widgets/class-lk-final-cta-widget.php';
require_once __DIR__ . '/widgets/class-lk-footer-widget.php';
require_once __DIR__ . '/widgets/class-lk-logo-carousel-widget.php';
require_once __DIR__ . '/widgets/class-lk-stat-counters-widget.php';
require_once __DIR__ . '/widgets/class-lk-testimonials-widget.php';
require_once __DIR__ . '/widgets/class-lk-product-grid-widget.php';
require_once __DIR__ . '/widgets/class-lk-instagram-feed-widget.php';
require_once __DIR__ . '/widgets/class-lk-box-page-hero-widget.php';
require_once __DIR__ . '/widgets/class-lk-box-how-steps-widget.php';
require_once __DIR__ . '/widgets/class-lk-box-product-widget.php';
require_once __DIR__ . '/widgets/class-lk-box-inside-widget.php';
require_once __DIR__ . '/widgets/class-lk-box-creative-method-widget.php';
require_once __DIR__ . '/widgets/class-lk-box-gifting-widget.php';
require_once __DIR__ . '/widgets/class-lk-scarf-product-widget.php';
		require_once __DIR__ . '/widgets/class-lk-product-details-strip-widget.php';
		require_once __DIR__ . '/widgets/class-lk-workshop-booking-widget.php';
		require_once __DIR__ . '/widgets/class-lk-editorial-hero-widget.php';
		require_once __DIR__ . '/widgets/class-lk-experience-flow-widget.php';
require_once __DIR__ . '/widgets/class-lk-scarf-addons-widget.php';
require_once __DIR__ . '/widgets/class-lk-photo-mosaic-widget.php';
require_once __DIR__ . '/widgets/class-lk-cardigan-product-widget.php';
require_once __DIR__ . '/widgets/class-lk-cuff-story-widget.php';
require_once __DIR__ . '/widgets/class-lk-shop-catalog-widget.php';
require_once __DIR__ . '/widgets/class-lk-shop-guidance-widget.php';
require_once __DIR__ . '/widgets/class-lk-gift-card-product-widget.php';
require_once __DIR__ . '/widgets/class-lk-numbered-link-grid-widget.php';
require_once __DIR__ . '/widgets/class-lk-organizations-widget.php';
require_once __DIR__ . '/widgets/class-lk-occasion-grid-widget.php';
require_once __DIR__ . '/widgets/class-lk-split-feature-widget.php';
require_once __DIR__ . '/widgets/class-lk-journey-cards-widget.php';
require_once __DIR__ . '/widgets/class-lk-enquiry-prompts-widget.php';
require_once __DIR__ . '/widgets/class-lk-credibility-stats-widget.php';
$widgets_manager->register( new \LK_Organizations_Widget() );
$widgets_manager->register( new \LK_Gift_Card_Product_Widget() );
$widgets_manager->register( new \LK_Numbered_Link_Grid_Widget() );
$widgets_manager->register( new \LK_Shop_Catalog_Widget() );
$widgets_manager->register( new \LK_Shop_Guidance_Widget() );
$widgets_manager->register( new \LK_Cardigan_Product_Widget() );
$widgets_manager->register( new \LK_Cuff_Story_Widget() );
$widgets_manager->register( new \LK_Photo_Mosaic_Widget() );
$widgets_manager->register( new \LK_Experience_Flow_Widget() );
$widgets_manager->register( new \LK_Scarf_Addons_Widget() );
$widgets_manager->register( new \LK_Editorial_Hero_Widget() );
$widgets_manager->register( new \LK_Workshop_Booking_Widget() );

		$widgets_manager->register( new \LK_Product_Details_Strip_Widget() );

$widgets_manager->register( new \LK_Scarf_Product_Widget() );

$widgets_manager->register( new \LK_Box_Product_Widget() );
$widgets_manager->register( new \LK_Box_Inside_Widget() );
$widgets_manager->register( new \LK_Box_Creative_Method_Widget() );
$widgets_manager->register( new \LK_Box_Gifting_Widget() );
$widgets_manager->register( new \LK_Box_Page_Hero_Widget() );
$widgets_manager->register( new \LK_Box_How_Steps_Widget() );

$widgets_manager->register( new \LK_Hero_Widget() );
$widgets_manager->register( new \LK_Experience_Grid_Widget() );
$widgets_manager->register( new \LK_Announcement_Widget() );
$widgets_manager->register( new \LK_Header_Widget() );
$widgets_manager->register( new \LK_Proof_Strip_Widget() );
$widgets_manager->register( new \LK_Commerce_Strip_Widget() );
$widgets_manager->register( new \LK_Signature_Box_Widget() );
$widgets_manager->register( new \LK_Transformation_Strip_Widget() );
$widgets_manager->register( new \LK_How_It_Works_Widget() );
$widgets_manager->register( new \LK_Collection_Feature_Widget() );
$widgets_manager->register( new \LK_Cardigan_Feature_Widget() );
$widgets_manager->register( new \LK_Transformation_Case_Widget() );
$widgets_manager->register( new \LK_Shop_Grid_Widget() );
$widgets_manager->register( new \LK_FAQ_Widget() );
$widgets_manager->register( new \LK_Final_CTA_Widget() );
$widgets_manager->register( new \LK_Footer_Widget() );
$widgets_manager->register( new \LK_Logo_Carousel_Widget() );
$widgets_manager->register( new \LK_Stat_Counters_Widget() );
$widgets_manager->register( new \LK_Testimonials_Widget() );
$widgets_manager->register( new \LK_Product_Grid_Widget() );
$widgets_manager->register( new \LK_Instagram_Feed_Widget() );
$widgets_manager->register( new \LK_Occasion_Grid_Widget() );
$widgets_manager->register( new \LK_Split_Feature_Widget() );
$widgets_manager->register( new \LK_Journey_Cards_Widget() );
$widgets_manager->register( new \LK_Enquiry_Prompts_Widget() );
$widgets_manager->register( new \LK_Credibility_Stats_Widget() );
	}
}

LK_Widgets_Loader::instance();

/**
 * ────────────────────────────────────────────────────────────────
 * Add this single line near the top of functions.php to wire it up:
 *
 *   require_once get_stylesheet_directory() . '/inc/class-lk-widgets-loader.php';
 *
 * Folder structure expected inside the child theme:
 *
 *   astra-child/
 *     functions.php
 *     inc/
 *       class-lk-widgets-loader.php   ← this file
 *       widgets/
 *         class-lk-hero-widget.php
 *         class-lk-experience-grid-widget.php
 *         class-lk-announcement-widget.php
 *         class-lk-header-widget.php
 *         class-lk-proof-strip-widget.php
 *         class-lk-commerce-strip-widget.php
 *         class-lk-signature-box-widget.php
 *         class-lk-transformation-strip-widget.php
 *     assets/
 *       css/
 *         lk-elementor-widgets.css
 *       js/
 *         lk-header.js
 * ────────────────────────────────────────────────────────────────
 */
