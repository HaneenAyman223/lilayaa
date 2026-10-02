<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Lila Kora custom Elementor widgets (Hero, Experience Grid, and more
 * to follow). Kept in /inc/ so this file stays clean as the widget
 * count grows.
 */
require_once get_stylesheet_directory() . '/inc/class-lk-widgets-loader.php';
require_once get_stylesheet_directory() . '/inc/lk-commerce-hooks.php';
require_once get_stylesheet_directory() . '/inc/lk-woocommerce-style-enqueue.php';

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );
add_action('wp_footer', function() {
    ?>
    <script>
    (function () {
        var svgArrow = '<span class="lk-arrow-icon" style="display:inline-block; vertical-align:middle; margin-left:6px; line-height:1; text-decoration:none !important;"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:block;"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg></span>';

        function cleanArrows() {
            // Find every link, button, or card containing the arrow
            document.querySelectorAll('a, button, div, span, p, h1, h2, h3, h4').forEach(function (el) {
                // If it has children, only check text nodes directly inside it
                Array.from(el.childNodes).forEach(function (child) {
                    if (child.nodeType === 3 && /[\u2197\u21D7\u279A\u2B08]/.test(child.nodeValue)) {
                        var span = document.createElement('span');
                        span.innerHTML = child.nodeValue.replace(/[\u2197\u21D7\u279A\u2B08][\uFE00-\uFE0F]?/g, svgArrow);
                        el.replaceChild(span, child);
                    }
                });
            });
        }

        cleanArrows();
        document.addEventListener('DOMContentLoaded', cleanArrows);
        window.addEventListener('load', cleanArrows);
        setInterval(cleanArrows, 400); // Guarantees replacement even if loaded dynamically
    })();
    </script>
    <?php
}, 99);