<?php
/**
 * Lila Kora — commerce hooks. These must live somewhere that loads on EVERY
 * request (functions.php, or require this file from it) — NOT inside a widget
 * file, because widget files only load when Elementor renders a page, which is
 * too late for add-to-cart handling and absent entirely during WP-Cron.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ---------------------------------------------------------------------------
 * Gift Card codes — automatic, using WooCommerce's own free Coupon system
 * ---------------------------------------------------------------------------
 * When a Gift Card purchase (SKU "lk-gift-card") is paid for, this generates
 * one single-use coupon per unit bought, worth the exact value purchased
 * (AED 390 / 690 / 890), and shows it on the order-received page and in the
 * customer's confirmation email. No paid plugin, no manual step by Liliya.
 *
 * Matched by SKU rather than product ID, since the ID differs per install —
 * update LK_GIFT_CARD_SKU if the real product doesn't use "lk-gift-card".
 *
 * BALANCE CARRIES OVER: a gift card is money someone paid for, so spending
 * less than its value in one order should not forfeit the rest. If a
 * gift-card coupon is applied to an order whose subtotal is less than the
 * coupon's face value, a new coupon is issued for the remainder and shown
 * the same way. "Custom value" gift cards never reach this at all — the
 * widget always sends that option to WhatsApp, since there's no fixed
 * amount to turn into a coupon.
 */
define( 'LK_GIFT_CARD_SKU', 'lk-gift-card' );

/**
 * Creates one single-use "fixed cart" coupon worth $amount and returns its
 * code. Loops on a collision with an existing code (astronomically rare at
 * 8 random characters, but cheap to guard against regardless).
 */
if ( ! function_exists( 'lk_generate_gift_card_coupon' ) ) {
	function lk_generate_gift_card_coupon( $amount, $order_id ) {
		do {
			$code = 'GIFT-' . strtoupper( wp_generate_password( 8, false, false ) );
		} while ( wc_get_coupon_id_by_code( $code ) );

		$coupon = new WC_Coupon();
		$coupon->set_code( $code );
		$coupon->set_discount_type( 'fixed_cart' );
		$coupon->set_amount( $amount );
		$coupon->set_individual_use( false );
		$coupon->set_usage_limit( 1 );
		$coupon->set_date_expires( strtotime( '+1 year' ) );
		$coupon->update_meta_data( '_lk_is_gift_card', 'yes' );
		$coupon->update_meta_data( '_lk_face_value', $amount );
		$coupon->update_meta_data( '_lk_source_order_id', $order_id );
		$coupon->save();

		return $code;
	}
}

/**
 * Reads the numeric AED amount off a gift-card line item's "Value"
 * variation attribute (e.g. "AED 390" -> 390). Returns 0 if it can't find
 * one — the caller treats that as "don't auto-generate," so an unexpected
 * product setup fails safely instead of creating a wrongly-priced coupon.
 */
if ( ! function_exists( 'lk_gift_card_item_amount' ) ) {
	function lk_gift_card_item_amount( $item ) {
		foreach ( $item->get_meta_data() as $meta ) {
			$data = $meta->get_data();
			if ( false !== stripos( $data['key'], 'value' ) && preg_match( '/\d+(\.\d+)?/', (string) $data['value'], $m ) ) {
				return (float) $m[0];
			}
		}
		return 0.0;
	}
}

// Generate codes once payment is actually captured (not just "processing"),
// since a code has real cash value. Idempotent: skips any line item that
// already has a code stored, so a gateway re-firing this hook can't double-issue.
add_action( 'woocommerce_payment_complete', function ( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	foreach ( $order->get_items() as $item ) {
		if ( $item->get_meta( '_lk_gift_card_codes' ) ) {
			continue; // already generated for this item
		}
		$product = $item->get_product();
		if ( ! $product || LK_GIFT_CARD_SKU !== $product->get_sku() ) {
			continue;
		}
		$amount = lk_gift_card_item_amount( $item );
		if ( $amount <= 0 ) {
			continue; // couldn't determine a fixed value — leave ungenerated rather than guess
		}
		$codes = array();
		for ( $i = 0, $qty = $item->get_quantity(); $i < $qty; $i++ ) {
			$codes[] = lk_generate_gift_card_coupon( $amount, $order_id );
		}
		$item->add_meta_data( '_lk_gift_card_codes', implode( ', ', $codes ), true );
		$item->add_meta_data( '_lk_gift_card_amount', $amount, true );
		$item->save();
	}

	// Balance carryover: for every gift-card coupon actually used on THIS
	// order, if the order's subtotal was less than the coupon's face value,
	// the difference is still owed to the customer — issue a new coupon for it.
	$remainders = array();
	foreach ( $order->get_coupon_codes() as $code ) {
		$coupon = new WC_Coupon( $code );
		if ( 'yes' !== $coupon->get_meta( '_lk_is_gift_card' ) ) {
			continue;
		}
		$face_value = (float) $coupon->get_meta( '_lk_face_value' );
		$subtotal   = (float) $order->get_subtotal();
		if ( $face_value > $subtotal ) {
			$remainder = round( $face_value - $subtotal, 2 );
			$remainders[] = array( 'code' => lk_generate_gift_card_coupon( $remainder, $order_id ), 'amount' => $remainder );
		}
	}
	if ( $remainders ) {
		$order->update_meta_data( '_lk_gift_card_remainders', wp_json_encode( $remainders ) );
		$order->save();
	}
}, 20 );

/**
 * Builds the HTML/plain-text block shown on the thank-you page and in the
 * customer email: newly purchased codes, plus any remainder from a
 * partially-used gift card on this same order. Returns '' if neither applies
 * (the normal case for an order with nothing gift-card-related in it).
 */
if ( ! function_exists( 'lk_gift_card_message_for_order' ) ) {
	function lk_gift_card_message_for_order( $order, $plain_text = false ) {
		$lines = array();

		foreach ( $order->get_items() as $item ) {
			$codes = $item->get_meta( '_lk_gift_card_codes' );
			if ( $codes ) {
				$amount = $item->get_meta( '_lk_gift_card_amount' );
				$lines[] = sprintf( 'Your AED %s Lila Kora gift card code: %s — enter it at checkout on a future order to redeem it.', esc_html( $amount ), esc_html( $codes ) );
			}
		}

		$remainders = $order->get_meta( '_lk_gift_card_remainders' );
		if ( $remainders ) {
			foreach ( json_decode( $remainders, true ) as $r ) {
				$lines[] = sprintf( 'Your gift card had AED %s remaining after this order — here is a new code for that balance: %s', esc_html( $r['amount'] ), esc_html( $r['code'] ) );
			}
		}

		if ( ! $lines ) {
			return '';
		}
		return $plain_text ? implode( "\n", $lines ) : implode( '<br>', $lines );
	}
}

add_action( 'woocommerce_thankyou', function ( $order_id ) {
	if ( ! $order_id ) {
		return;
	}
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}
	$message = lk_gift_card_message_for_order( $order );
	if ( $message ) {
		echo '<div class="woocommerce-message" style="border-top-color:#692137;">' . $message . '</div>';
	}
}, 20 );

add_action( 'woocommerce_email_before_order_table', function ( $order, $sent_to_admin, $plain_text, $email ) {
	if ( $sent_to_admin ) {
		return;
	}
	$message = lk_gift_card_message_for_order( $order, $plain_text );
	if ( ! $message ) {
		return;
	}
	if ( $plain_text ) {
		echo $message . "\n\n";
	} else {
		echo '<p style="padding:14px 18px;background:#F8ECE9;border-left:3px solid #692137;">' . $message . '</p>';
	}
}, 5 );

/**
 * ---------------------------------------------------------------------------
 * Variation lookup — used by LK — Box Product Configurator and LK — Scarf Product
 * ---------------------------------------------------------------------------
 * Builds a searchable index of a variable product's variations, then finds the
 * one whose attribute values match a set of labels (e.g. "Design 02" + "65 x 65 cm").
 * Matching is case/spacing-insensitive (both sides go through sanitize_title()),
 * and for global attributes it checks the term NAME as well as the slug — so as
 * long as the widget's labels match the WooCommerce attribute values, no IDs need
 * to be copied by hand.
 */
function lk_wc_variation_index( $product_id ) {
	static $cache = array();
	$product_id = absint( $product_id );

	if ( isset( $cache[ $product_id ] ) ) {
		return $cache[ $product_id ];
	}
	$cache[ $product_id ] = array();

	if ( ! $product_id || ! function_exists( 'wc_get_product' ) ) {
		return $cache[ $product_id ];
	}
	$product = wc_get_product( $product_id );
	if ( ! $product || ! $product->is_type( 'variable' ) ) {
		return $cache[ $product_id ];
	}

	foreach ( $product->get_children() as $variation_id ) {
		$variation = wc_get_product( $variation_id );
		if ( ! $variation || ! $variation->variation_is_visible() ) {
			continue; // Unpublished, no price, or hidden out-of-stock variation.
		}

		$attrs    = $variation->get_variation_attributes(); // keys already prefixed: attribute_pa_design, attribute_size…
		$norm     = array();
		$complete = true;

		foreach ( $attrs as $key => $value ) {
			if ( '' === $value ) {
				$complete = false; // "Any …" variations can't be added without extra input — skip them.
				break;
			}
			$norm[]   = sanitize_title( $value );
			$taxonomy = preg_replace( '/^attribute_/', '', $key );
			if ( taxonomy_exists( $taxonomy ) ) {
				$term = get_term_by( 'slug', $value, $taxonomy );
				if ( $term ) {
					$norm[] = sanitize_title( $term->name );
				}
			}
		}
		if ( ! $complete ) {
			continue;
		}

		$price = (float) wc_get_price_to_display( $variation );

		$cache[ $product_id ][] = array(
			'id'    => $variation_id,
			'price' => ( floor( $price ) == $price ) ? (string) (int) $price : number_format( $price, 2, '.', '' ),
			'norm'  => $norm,
			'attrs' => $attrs,
		);
	}

	return $cache[ $product_id ];
}

/**
 * Finds the variation matching every label given and returns its add-to-cart URL
 * and display price — or null if nothing matches.
 *
 * The URL carries the full set of attribute values as well as the variation ID:
 * WooCommerce's add-to-cart handler validates them for variable products, so
 * sending only variation_id is not reliable.
 */
function lk_wc_buy_url( $product_id, array $labels, $destination = 'cart' ) {
	$needles = array_filter( array_map( 'sanitize_title', $labels ) );
	if ( empty( $needles ) ) {
		return null;
	}

	foreach ( lk_wc_variation_index( $product_id ) as $v ) {
		if ( array_diff( $needles, $v['norm'] ) ) {
			continue;
		}
		$args = array_merge(
			array(
				'add-to-cart'  => absint( $product_id ),
				'variation_id' => $v['id'],
				'quantity'     => 1,
				'lk_buy'       => 'checkout' === $destination ? 'checkout' : 'cart',
			),
			$v['attrs']
		);
		return array(
			'url'          => trailingslashit( home_url() ) . '?' . http_build_query( $args, '', '&', PHP_QUERY_RFC3986 ),
			'price'        => $v['price'],
			'variation_id' => $v['id'],
		);
	}
	return null;
}

/**
 * After the Box Product Configurator's add-to-cart link runs, send the customer
 * straight to a clean Cart/Checkout URL — so refreshing the page can't add the
 * item a second time.
 */
add_filter( 'woocommerce_add_to_cart_redirect', function ( $url ) {
	if ( isset( $_REQUEST['lk_buy'] ) && function_exists( 'wc_get_cart_url' ) ) {
		return 'checkout' === sanitize_key( wp_unslash( $_REQUEST['lk_buy'] ) ) ? wc_get_checkout_url() : wc_get_cart_url();
	}
	return $url;
} );

/**
 * Keeps the header cart badge (LK — Site Header) in sync. WooCommerce swaps the
 * matching element in via its cart-fragments script, so the count stays correct
 * even on cached pages and after items are added or removed.
 */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$count = ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
	$fragments['span.lk-cart-count'] = '<span class="lk-cart-count" data-count="' . esc_attr( $count ) . '">' . esc_html( $count ) . '</span>';
	return $fragments;
} );

/**
 * Make sure the cart-fragments script is loaded on every front-end page (some
 * setups only load it on shop pages, which would leave the header badge stale).
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( function_exists( 'is_woocommerce' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
}, 30 );

/**
 * Instagram token refresh (used by LK — Instagram Feed). Registered here rather
 * than in the widget file so the daily cron event can actually find its callback.
 */
add_action( 'lk_ig_refresh_token_event', function () {
	$token = get_option( 'lk_instagram_access_token' );
	if ( empty( $token ) ) {
		return;
	}
	$response = wp_remote_get( 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . rawurlencode( $token ), array( 'timeout' => 15 ) );
	if ( is_wp_error( $response ) ) {
		return;
	}
	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! empty( $body['access_token'] ) ) {
		update_option( 'lk_instagram_access_token', $body['access_token'], false );
		update_option( 'lk_instagram_token_refreshed_at', time(), false );
	}
} );
