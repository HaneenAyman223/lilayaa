<?php
/**
 * Lila Kora — Gift Card Product Widget
 *
 * The Gift Card product page: a full-bleed photo on the left with a dark
 * gradient and a floating "LK / A GIFT TO CREATE OR WEAR / Lila Kora Gift
 * Card" overlay card at its bottom, and on the right a sticky panel —
 * breadcrumb, eyebrow, heading, the selected value shown in large serif
 * type, a description, a 2×2 grid of value buttons (three fixed amounts
 * plus "Custom value"), an order button, a summary line and a three-item
 * assurance strip.
 *
 * CHECKOUT: the three fixed amounts (390 / 690 / 890 by default) each map to
 * a WooCommerce variation of a single "Value" attribute, using the same
 * lk_wc_buy_url() helper as every other product widget (needs
 * lk-commerce-hooks.php loaded from functions.php). "Custom value" has no
 * fixed price — like the original reference design, it always opens a
 * WhatsApp enquiry instead, so she can name her own amount.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-gift-card-product-widget.php';
 *   $widgets_manager->register( new \LK_Gift_Card_Product_Widget() );
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;

class LK_Gift_Card_Product_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-gift-card-product';
	}

	public function get_title() {
		return 'LK — Gift Card Product';
	}

	public function get_icon() {
		return 'eicon-gift';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'gift card', 'product', 'checkout', 'value' );
	}

	private function typo( $name, $label, $selector, $family, $size, $weight = '', $spacing = null, $upper = false, $line = null, $tablet = null, $mobile = null, $italic = false ) {
		$size_opt = array( 'default' => array( 'unit' => 'px', 'size' => $size ) );
		if ( null !== $tablet ) {
			$size_opt['tablet_default'] = array( 'unit' => 'px', 'size' => $tablet );
		}
		if ( null !== $mobile ) {
			$size_opt['mobile_default'] = array( 'unit' => 'px', 'size' => $mobile );
		}
		$fields = array( 'font_family' => array( 'default' => $family ), 'font_size' => $size_opt );
		if ( '' !== $weight ) {
			$fields['font_weight'] = array( 'default' => $weight );
		}
		if ( null !== $spacing ) {
			$fields['letter_spacing'] = array( 'default' => array( 'unit' => 'em', 'size' => $spacing ) );
		}
		if ( $upper ) {
			$fields['text_transform'] = array( 'default' => 'uppercase' );
		}
		if ( $italic ) {
			$fields['font_style'] = array( 'default' => 'italic' );
		}
		if ( null !== $line ) {
			$fields['line_height'] = array( 'default' => array( 'unit' => 'em', 'size' => $line ) );
		}
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => $name, 'label' => $label, 'selector' => '{{WRAPPER}} ' . $selector, 'fields_options' => $fields )
		);
	}

	private function color( $id, $label, $selector, $default, $prop = 'color', $separator = false, $important = false ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'default'   => $default,
			'selectors' => array( '{{WRAPPER}} ' . $selector => $prop . ': {{VALUE}}' . ( $important ? ' !important' : '' ) . ';' ),
		);
		if ( $separator ) {
			$args['separator'] = 'before';
		}
		$this->add_control( $id, $args );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section( 'section_content_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'show_breadcrumb', array( 'label' => 'Show breadcrumb', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$this->add_control( 'breadcrumb_shop_text', array( 'label' => 'Breadcrumb — shop label', 'type' => Controls_Manager::TEXT, 'default' => 'Shop', 'condition' => array( 'show_breadcrumb' => 'yes' ) ) );
		$this->add_control( 'breadcrumb_shop_link', array( 'label' => 'Breadcrumb — shop link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => './shop.html' ), 'condition' => array( 'show_breadcrumb' => 'yes' ) ) );
		$this->add_control( 'breadcrumb_current', array( 'label' => 'Breadcrumb — current page', 'type' => Controls_Manager::TEXT, 'default' => 'Gift Card', 'condition' => array( 'show_breadcrumb' => 'yes' ) ) );

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'A gift with choice', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Lila Kora', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic word', 'type' => Controls_Manager::TEXT, 'default' => 'Gift Card', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => 'Let her choose what feels most like her. The gift card can be used towards a ready-to-wear scarf, Canvas-to-Scarf Experience Box or public workshop.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Visual
		 * =======================================================*/
		$this->start_controls_section( 'section_content_visual', array( 'label' => 'Visual', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image', 'default' => 'large' ) );
		$this->add_control( 'image_position', array( 'label' => 'Visual side', 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => array( 'left' => 'Left', 'right' => 'Right' ), 'prefix_class' => 'lk-giftprod-img-' ) );

		$this->add_control( 'show_overlay', array( 'label' => 'Show overlay card', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes', 'separator' => 'before' ) );
		$this->add_control( 'overlay_mark', array( 'label' => 'Overlay — monogram', 'type' => Controls_Manager::TEXT, 'default' => 'LK', 'condition' => array( 'show_overlay' => 'yes' ) ) );
		$this->add_control( 'overlay_label', array( 'label' => 'Overlay — small label', 'type' => Controls_Manager::TEXT, 'default' => 'A gift to create or wear', 'label_block' => true, 'condition' => array( 'show_overlay' => 'yes' ) ) );
		$this->add_control( 'overlay_title', array( 'label' => 'Overlay — title', 'type' => Controls_Manager::TEXT, 'default' => 'Lila Kora Gift Card', 'label_block' => true, 'condition' => array( 'show_overlay' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Value options & checkout
		 * =======================================================*/
		$this->start_controls_section( 'section_content_values', array( 'label' => 'Value Options & Checkout', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'legend', array( 'label' => 'Group label', 'type' => Controls_Manager::TEXT, 'default' => 'Gift card value' ) );

		$this->add_control(
			'checkout_mode',
			array(
				'label'       => 'Order button does',
				'type'        => Controls_Manager::SELECT,
				'default'     => 'woocommerce',
				'options'     => array(
					'woocommerce' => 'Add to cart (WooCommerce checkout)',
					'whatsapp'    => 'Send a WhatsApp enquiry',
				),
				'description' => 'Needs lk-commerce-hooks.php loaded from functions.php. "Custom value" always uses WhatsApp regardless of this setting — it has no fixed price to check out with.',
			)
		);
		$this->add_control( 'wc_product_id', array( 'label' => 'WooCommerce product ID (variable product)', 'type' => Controls_Manager::NUMBER, 'condition' => array( 'checkout_mode' => 'woocommerce' ), 'description' => 'The fixed values below must match that product\'s "Value" attribute values exactly (e.g. "AED 390").' ) );
		$this->add_control( 'wc_destination', array( 'label' => 'After adding, send the customer to', 'type' => Controls_Manager::SELECT, 'default' => 'cart', 'options' => array( 'cart' => 'Cart', 'checkout' => 'Checkout' ), 'condition' => array( 'checkout_mode' => 'woocommerce' ) ) );

		$values = new Repeater();
		$values->add_control( 'label', array( 'label' => 'Button text', 'type' => Controls_Manager::TEXT, 'default' => 'AED 0', 'label_block' => true ) );
		$values->add_control( 'is_custom', array( 'label' => 'This is the "Custom value" option (no fixed price — always WhatsApp)', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => '' ) );
		$this->add_control(
			'values',
			array(
				'label'       => 'Values',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $values->get_controls(),
				'default'     => array(
					array( 'label' => 'AED 390', 'is_custom' => '' ),
					array( 'label' => 'AED 690', 'is_custom' => '' ),
					array( 'label' => 'AED 890', 'is_custom' => '' ),
				),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->add_control( 'button_text', array( 'label' => 'Button text (add to cart)', 'type' => Controls_Manager::TEXT, 'default' => 'Arrange the Gift Card', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'button_text_enquiry', array( 'label' => 'Button text when falling back to WhatsApp', 'type' => Controls_Manager::TEXT, 'default' => 'Arrange the Gift Card', 'label_block' => true ) );
		$this->add_control( 'whatsapp_number', array( 'label' => 'WhatsApp number (with country code, no + or spaces)', 'type' => Controls_Manager::TEXT, 'default' => '971589610166' ) );
		$this->add_control( 'message_template', array( 'label' => 'WhatsApp message template', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Hello Lila Kora, I would like to arrange a Lila Kora gift card with this value: {value}.', 'description' => 'Use {value} — replaced with the selected button text.' ) );
		$this->add_control( 'fine_print', array( 'label' => 'Small print under the button', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'The gift card code is emailed automatically to the recipient as soon as payment is confirmed.', 'label_block' => true ) );

		$this->add_control( 'show_recipient', array( 'label' => 'Ask for recipient details', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => 'yes', 'separator' => 'before', 'description' => 'Adds name / email / message fields. The code is emailed to that address after payment (needs lk-commerce-hooks.php).' ) );
		$this->add_control( 'recipient_title', array( 'label' => 'Recipient section title', 'type' => Controls_Manager::TEXT, 'default' => 'Who is it for?', 'label_block' => true, 'condition' => array( 'show_recipient' => 'yes' ) ) );
		$this->add_control( 'recipient_note', array( 'label' => 'Recipient section note', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'We will email the gift card code straight to them. Leave the email empty to receive the code yourself.', 'label_block' => true, 'condition' => array( 'show_recipient' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Assurance strip
		 * =======================================================*/
		$this->start_controls_section( 'section_content_assurance', array( 'label' => 'Assurance Strip', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'show_assurance', array( 'label' => 'Show strip', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$assurance = new Repeater();
		$assurance->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Fact', 'label_block' => true ) );
		$this->add_control(
			'assurance',
			array(
				'label'       => 'Items',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $assurance->get_controls(),
				'default'     => array(
					array( 'text' => 'Personal message included' ),
					array( 'text' => 'Flexible choice' ),
					array( 'text' => 'Simple to arrange' ),
				),
				'title_field' => '{{{ text }}}',
				'condition'   => array( 'show_assurance' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->add_responsive_control( 'page_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-giftprod' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'page_padding',
			array(
				'label'          => 'Padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 45, 'right' => 65, 'bottom' => 105, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 32, 'right' => 32, 'bottom' => 80, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 24, 'right' => 24, 'bottom' => 70, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-giftprod' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'columns_gap', array( 'label' => 'Gap between visual and panel', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 160 ) ), 'default' => array( 'unit' => 'px', 'size' => 75 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 45 ), 'selectors' => array( '{{WRAPPER}} .lk-giftprod' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'panel_sticky', array( 'label' => 'Keep the panel visible while scrolling (desktop)', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => 'yes', 'prefix_class' => 'lk-giftprod-sticky-' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Breadcrumb
		 * =======================================================*/
		$this->start_controls_section( 'section_style_breadcrumb', array( 'label' => 'Breadcrumb', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_breadcrumb' => 'yes' ) ) );

		$this->color( 'crumb_link_color', 'Link colour', '.lk-giftprod-crumb a', '#692137' );
		$this->color( 'crumb_current_color', 'Current page colour', '.lk-giftprod-crumb span:last-child', '#8F8584' );
		$this->typo( 'crumb_typography', 'Typography', '.lk-giftprod-crumb', 'Montserrat', 13 );
		$this->color( 'crumb_border', 'Bottom border', '.lk-giftprod-crumb-wrap', 'rgba(105,33,55,0.16)', 'border-color' );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Visual
		 * =======================================================*/
		$this->start_controls_section( 'section_style_visual', array( 'label' => 'Visual', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->add_responsive_control( 'visual_height', array( 'label' => 'Min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 1000 ) ), 'default' => array( 'unit' => 'px', 'size' => 760 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 620 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 520 ), 'selectors' => array( '{{WRAPPER}} .lk-giftprod-visual' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'image_focus', array( 'label' => 'Image focus', 'type' => Controls_Manager::SELECT, 'default' => 'center center', 'options' => array( 'center center' => 'Centre', 'center top' => 'Top', 'center bottom' => 'Bottom', 'left center' => 'Left', 'right center' => 'Right' ), 'selectors' => array( '{{WRAPPER}} .lk-giftprod-visual img' => 'object-position: {{VALUE}};' ) ) );
		$this->add_control(
			'gradient_color',
			array(
				'label'     => 'Gradient tint',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(57,9,25,0.6)',
				'selectors' => array( '{{WRAPPER}} .lk-giftprod-visual::after' => 'background-image: linear-gradient(to top, {{VALUE}}, transparent 55%);' ),
			)
		);

		$this->add_control( 'heading_overlay', array( 'label' => 'Overlay Card', 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => array( 'show_overlay' => 'yes' ) ) );
		$this->color( 'overlay_bg', 'Background', '.lk-giftprod-overlay', 'rgba(255,254,253,0.94)', 'background-color' );
		$this->color( 'overlay_color', 'Text colour', '.lk-giftprod-overlay', '#692137', 'color' );
		$this->color( 'overlay_mark_border', 'Monogram border', '.lk-giftprod-overlay > span', '#692137', 'border-color' );
		$this->typo( 'overlay_mark_typography', 'Monogram typography', '.lk-giftprod-overlay > span', 'Cormorant Garamond', 20, '', null, false, null, null, null, true );
		$this->typo( 'overlay_label_typography', 'Small label typography', '.lk-giftprod-overlay small', 'Montserrat', 10.4, '', 0.13, true );
		$this->typo( 'overlay_title_typography', 'Title typography', '.lk-giftprod-overlay strong', 'Cormorant Garamond', 24.8, '500' );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section( 'section_style_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-giftprod-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-giftprod-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-giftprod-heading', '#57282D', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic word colour', '.lk-giftprod-heading em', '#57282D' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-giftprod-heading', 'Cormorant Garamond', 60, '400', -0.025, false, 0.98, 46, 36 );
		$this->color( 'status_color', 'Selected value colour', '.lk-giftprod-status', '#692137', 'color', true );
		$this->typo( 'status_typography', 'Selected value typography', '.lk-giftprod-status', 'Cormorant Garamond', 26.4 );
		$this->color( 'desc_color', 'Paragraph colour', '.lk-giftprod-desc', '#8F8584', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-giftprod-desc', 'Montserrat', 16, '', null, false, 1.8 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Value buttons
		 * =======================================================*/
		$this->start_controls_section( 'section_style_values', array( 'label' => 'Value Buttons', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'legend_color', 'Group label colour', '.lk-giftprod-legend', '#692137', 'color', true );
		$this->typo( 'legend_typography', 'Group label typography', '.lk-giftprod-legend', 'Montserrat', 12, '600', 0.12, true );
		$this->color( 'legend_border', 'Divider above label', '.lk-giftprod-values-group', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->add_responsive_control( 'value_min_height', array( 'label' => 'Button min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 40, 'max' => 100 ) ), 'default' => array( 'unit' => 'px', 'size' => 58 ), 'selectors' => array( '{{WRAPPER}} .lk-giftprod-value' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->typo( 'value_typography', 'Button typography', '.lk-giftprod-value', 'Montserrat', 13.1 );

		$this->start_controls_tabs( 'tabs_value' );
		$this->start_controls_tab( 'tab_value_normal', array( 'label' => 'Normal' ) );
		$this->color( 'value_bg', 'Background', '.lk-giftprod-value', 'transparent', 'background-color', false, true );
		$this->color( 'value_color', 'Text colour', '.lk-giftprod-value', '#281D21', 'color', false, true );
		$this->color( 'value_border', 'Border colour', '.lk-giftprod-value', 'rgba(105,33,55,0.16)', 'border-color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_value_hover', array( 'label' => 'Hover' ) );
		$this->color( 'value_border_hover', 'Border colour', '.lk-giftprod-value:not(.is-selected):hover', '#692137', 'border-color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_value_selected', array( 'label' => 'Selected' ) );
		$this->color( 'value_bg_selected', 'Background', '.lk-giftprod-value.is-selected', '#692137', 'background-color', false, true );
		$this->color( 'value_color_selected', 'Text colour', '.lk-giftprod-value.is-selected', '#FFFFFF', 'color', false, true );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Order button & summary
		 * =======================================================*/
		$this->start_controls_section( 'section_style_order', array( 'label' => 'Order Button & Summary', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->typo( 'btn_typography', 'Button typography', '.lk-giftprod-order', 'Montserrat', 10.9, '600', 0.17, true );
		$this->start_controls_tabs( 'tabs_btn' );
		$this->start_controls_tab( 'tab_btn_normal', array( 'label' => 'Normal' ) );
		$this->color( 'btn_bg', 'Background', '.lk-giftprod-order', '#692137', 'background-color', false, true );
		$this->color( 'btn_color', 'Text colour', '.lk-giftprod-order', '#FFFFFF', 'color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_hover', array( 'label' => 'Hover' ) );
		$this->color( 'btn_bg_hover', 'Background', '.lk-giftprod-order:hover', 'rgba(0,0,0,0)', 'background-color', false, true );
		$this->color( 'btn_color_hover', 'Text colour', '.lk-giftprod-order:hover', '#692137', 'color', false, true );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->color( 'summary_color', 'Summary colour', '.lk-giftprod-summary', '#8F8584', 'color', true );
		$this->typo( 'summary_typography', 'Summary typography', '.lk-giftprod-summary', 'Montserrat', 12.8 );
		$this->color( 'fine_color', 'Small print colour', '.lk-giftprod-fine', '#8F8584', 'color', true );
		$this->typo( 'fine_typography', 'Small print typography', '.lk-giftprod-fine', 'Montserrat', 11.5, '', null, false, 1.5 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Assurance strip
		 * =======================================================*/
		$this->start_controls_section( 'section_style_assurance', array( 'label' => 'Assurance Strip', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_assurance' => 'yes' ) ) );

		$this->color( 'assurance_border', 'Divider colour', '.lk-giftprod-assurance, {{WRAPPER}} .lk-giftprod-assurance span', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->color( 'assurance_color', 'Text colour', '.lk-giftprod-assurance span', '#8F8584', 'color', true );
		$this->typo( 'assurance_typography', 'Typography', '.lk-giftprod-assurance span', 'Montserrat', 11.2, '', null, false, 1.45 );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		$values     = (array) $s['values'];
		$default_i  = 0;
		foreach ( $values as $i => $v ) {
			if ( 'yes' !== $v['is_custom'] ) {
				$default_i = $i;
				break;
			}
		}

		$hooks_ok = function_exists( 'lk_wc_buy_url' ) && function_exists( 'wc_get_product' );
		$wc_on    = ( 'woocommerce' === $s['checkout_mode'] && $hooks_ok && ! empty( $s['wc_product_id'] ) );
		$missing  = array();
		$js_values = array();
		foreach ( $values as $v ) {
			$buy   = '';
			$price = '';
			if ( $wc_on && 'yes' !== $v['is_custom'] ) {
				$hit = lk_wc_buy_url( $s['wc_product_id'], array( $v['label'] ), $s['wc_destination'] );
				if ( $hit ) {
					$buy = $hit['url'];
				} else {
					$missing[] = $v['label'];
				}
			}
			$js_values[] = array( 'label' => $v['label'], 'custom' => ( 'yes' === $v['is_custom'] ), 'buy' => $buy );
		}
		?>
		<div class="lk-giftprod" data-lk-giftcard
			data-whatsapp="<?php echo esc_attr( $s['whatsapp_number'] ); ?>"
			data-template="<?php echo esc_attr( $s['message_template'] ); ?>"
			data-label-cart="<?php echo esc_attr( $s['button_text'] ); ?>"
			data-label-enquiry="<?php echo esc_attr( $s['button_text_enquiry'] ); ?>"
			data-values="<?php echo esc_attr( wp_json_encode( $js_values ) ); ?>">

			<figure class="lk-giftprod-visual">
				<?php echo $image_html; ?>
				<?php if ( 'yes' === $s['show_overlay'] ) : ?>
					<div class="lk-giftprod-overlay">
						<span><?php echo esc_html( $s['overlay_mark'] ); ?></span>
						<small><?php echo esc_html( $s['overlay_label'] ); ?></small>
						<strong><?php echo esc_html( $s['overlay_title'] ); ?></strong>
					</div>
				<?php endif; ?>
			</figure>

			<div class="lk-giftprod-panel">
				<?php if ( 'yes' === $s['show_breadcrumb'] ) : ?>
					<nav class="lk-giftprod-crumb-wrap">
						<div class="lk-giftprod-crumb">
							<a href="<?php echo esc_url( ! empty( $s['breadcrumb_shop_link']['url'] ) ? $s['breadcrumb_shop_link']['url'] : '#' ); ?>"><?php echo esc_html( $s['breadcrumb_shop_text'] ); ?></a>
							<span aria-hidden="true">/</span>
							<span><?php echo esc_html( $s['breadcrumb_current'] ); ?></span>
						</div>
					</nav>
				<?php endif; ?>

				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-giftprod-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h1 class="lk-giftprod-heading"><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h1>
				<p class="lk-giftprod-status" data-lk-status><?php echo esc_html( $values ? $values[ $default_i ]['label'] : '' ); ?></p>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-giftprod-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

				<?php if ( $values ) : ?>
					<fieldset class="lk-giftprod-values-group">
						<legend class="lk-giftprod-legend"><?php echo esc_html( $s['legend'] ); ?></legend>
						<div class="lk-giftprod-values">
							<?php foreach ( $values as $i => $v ) : ?>
								<button type="button" class="lk-giftprod-value<?php echo $i === $default_i ? ' is-selected' : ''; ?>" data-index="<?php echo esc_attr( $i ); ?>" aria-pressed="<?php echo $i === $default_i ? 'true' : 'false'; ?>"><?php echo esc_html( $v['label'] ); ?></button>
							<?php endforeach; ?>
						</div>
					</fieldset>
				<?php endif; ?>

				<?php if ( 'yes' === $s['show_recipient'] ) : ?>
					<fieldset class="lk-giftprod-recipient" data-lk-recipient>
						<legend class="lk-giftprod-legend"><?php echo esc_html( $s['recipient_title'] ); ?></legend>
						<?php if ( ! empty( $s['recipient_note'] ) ) : ?><p class="lk-giftprod-recipient-note"><?php echo esc_html( $s['recipient_note'] ); ?></p><?php endif; ?>
						<input type="text" data-lk-gc="name" placeholder="Recipient's name" autocomplete="off" maxlength="80">
						<input type="email" data-lk-gc="email" placeholder="Recipient's email" autocomplete="off" maxlength="120">
						<input type="text" data-lk-gc="from" placeholder="Your name (shown in the email)" autocomplete="name" maxlength="80">
						<textarea data-lk-gc="msg" rows="3" placeholder="Personal message (optional)" maxlength="400"></textarea>
						<p class="lk-giftprod-recipient-error" data-lk-gc-error role="alert" hidden>Please enter a valid email address for the recipient.</p>
					</fieldset>
				<?php endif; ?>

				<a class="lk-giftprod-order" data-lk-order href="#" target="_blank" rel="noopener"><?php echo esc_html( $s['button_text'] ); ?></a>
				<p class="lk-giftprod-summary" data-lk-summary></p>
				<?php if ( ! empty( $s['fine_print'] ) ) : ?><p class="lk-giftprod-fine"><?php echo esc_html( $s['fine_print'] ); ?></p><?php endif; ?>

				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<?php if ( 'woocommerce' === $s['checkout_mode'] && ! $hooks_ok ) : ?>
						<p class="lk-giftprod-notice">Cart mode is on, but lk-commerce-hooks.php isn't loaded — add its require_once line to functions.php. Falling back to WhatsApp. (Only visible to editors.)</p>
					<?php elseif ( 'woocommerce' === $s['checkout_mode'] && empty( $s['wc_product_id'] ) ) : ?>
						<p class="lk-giftprod-notice">Cart mode is on, but no WooCommerce product ID is set yet — falling back to WhatsApp. (Only visible to editors.)</p>
					<?php elseif ( ! empty( $missing ) ) : ?>
						<p class="lk-giftprod-notice">No matching WooCommerce variation for: <?php echo esc_html( implode( ', ', $missing ) ); ?>. Those values fall back to WhatsApp. Check the names match the product's "Value" attribute. (Only visible to editors.)</p>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( 'yes' === $s['show_assurance'] && ! empty( $s['assurance'] ) ) : ?>
					<div class="lk-giftprod-assurance">
						<?php foreach ( $s['assurance'] as $item ) : ?><span><?php echo esc_html( $item['text'] ); ?></span><?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<style>
			.lk-giftprod { display: grid; grid-template-columns: 1.12fr 0.88fr; align-items: start; box-sizing: border-box; }
			.lk-giftprod-img-right .lk-giftprod-visual { order: 2; } .lk-giftprod-img-right .lk-giftprod-panel { order: 1; }
			.lk-giftprod-sticky-yes .lk-giftprod-panel { position: sticky; top: 125px; }
			.lk-giftprod-panel { padding-top: 24px; }
			.lk-giftprod-visual { position: relative; margin: 0; overflow: hidden; }
			.lk-giftprod-visual img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-giftprod-visual::after { content: ""; position: absolute; inset: 0; pointer-events: none; }
			.lk-giftprod-overlay { position: absolute; z-index: 1; left: 8%; right: 8%; bottom: 7%; display: grid; grid-template-columns: auto 1fr; gap: 3px 15px; align-items: center; padding: 25px; box-sizing: border-box; }
			.lk-giftprod-overlay > span { grid-row: 1 / span 2; width: 54px; height: 54px; display: grid; place-items: center; border: 1px solid; box-sizing: border-box; }
			.lk-giftprod-overlay small, .lk-giftprod-overlay strong { display: block; }
			.lk-giftprod-crumb-wrap { margin: 0 0 30px; padding-bottom: 18px; border-bottom: 1px solid; }
			.lk-giftprod-crumb { display: flex; align-items: center; gap: 8px; }
			.lk-giftprod-crumb a { text-decoration: none; }
			.lk-giftprod-crumb span[aria-hidden] { opacity: .5; }
			.lk-giftprod-heading { margin: 0 0 20px; font-style: normal; }
			.lk-giftprod-heading em { font-style: italic; }
			.lk-giftprod-status { margin: 0 0 22px; font-style: normal; }
			.lk-giftprod-desc { margin: 0; max-width: 560px; }
			.lk-giftprod-values-group { margin: 32px 0 0; padding: 18px 0 0; border: 0; border-top: 1px solid; }
			.lk-giftprod-legend { display: block; width: 100%; margin: 0 0 13px; }
			.lk-giftprod-values { display: grid; grid-template-columns: 1fr 1fr; gap: 9px; }
			.lk-giftprod-value { box-sizing: border-box; padding: 13px 16px; text-align: left; background: transparent; border: 1px solid; cursor: pointer; -webkit-appearance: none; appearance: none; box-shadow: none; transition: color .2s ease, background .2s ease, border-color .2s ease; }
			.lk-giftprod-recipient { margin: 28px 0 0; padding: 18px 0 0; border: 0; border-top: 1px solid rgba(105,33,55,0.16); display: grid; gap: 9px; }
			.lk-giftprod-recipient-note { margin: 0 0 4px; font-family: 'Montserrat', Arial, sans-serif; font-size: 13px; line-height: 1.6; color: #8F8584; }
			.lk-giftprod-recipient input, .lk-giftprod-recipient textarea { width: 100%; box-sizing: border-box; padding: 13px 16px; font-family: 'Montserrat', Arial, sans-serif; font-size: 13px; color: #281D21; background: transparent; border: 1px solid rgba(105,33,55,0.16); border-radius: 0; box-shadow: none; }
			.lk-giftprod-recipient input:focus, .lk-giftprod-recipient textarea:focus { outline: 0; border-color: #692137; }
			.lk-giftprod-recipient-error { margin: 0; font-family: 'Montserrat', Arial, sans-serif; font-size: 12px; color: #B3261E; }
			.lk-giftprod-order { display: flex; width: 100%; box-sizing: border-box; align-items: center; justify-content: center; min-height: 58px; margin-top: 30px; text-decoration: none; border: 0; border-radius: 0; cursor: pointer; transition: color .25s ease, background .25s ease; }
			.lk-giftprod-summary { margin: 13px 0 0; text-align: center; }
			.lk-giftprod-fine { margin: 8px 0 0; text-align: center; }
			.lk-giftprod-notice { margin: 16px 0 0; padding: 12px 16px; background: rgba(255,113,94,0.12); border: 1px dashed #FF715E; font-family: 'Montserrat', Arial, sans-serif; font-size: 12px; line-height: 1.6; color: #57282D; }
			.lk-giftprod-assurance { display: grid; grid-template-columns: repeat(3, 1fr); margin-top: 30px; border-top: 1px solid; }
			.lk-giftprod-assurance span { padding: 16px 10px 0; text-align: center; }
			.lk-giftprod-assurance span + span { border-left: 1px solid; }
			@media (max-width: 1120px) {
				.lk-giftprod { grid-template-columns: 1fr 1fr; gap: 45px; }
			}
			@media (max-width: 820px) {
				.lk-giftprod { grid-template-columns: 1fr !important; }
				.lk-giftprod-img-right .lk-giftprod-visual, .lk-giftprod-img-right .lk-giftprod-panel { order: 0; }
				.lk-giftprod-sticky-yes .lk-giftprod-panel { position: relative; top: auto; }
			}
			@media (max-width: 540px) {
				.lk-giftprod-values { grid-template-columns: 1fr; }
				.lk-giftprod-assurance { grid-template-columns: 1fr; }
				.lk-giftprod-assurance span { padding: 13px 0; text-align: left; }
				.lk-giftprod-assurance span + span { border-left: 0; border-top: 1px solid; }
			}
		</style>

		<script>
			( function () {
				if ( window.__lkGiftCardBound ) { return; }
				window.__lkGiftCardBound = true;
				document.addEventListener( 'click', function ( event ) {
					var btn = event.target.closest( '.lk-giftprod-value' );
					if ( ! btn ) { return; }
					var root = btn.closest( '[data-lk-giftcard]' );
					var values = [];
					try { values = JSON.parse( root.getAttribute( 'data-values' ) || '[]' ); } catch ( e ) {}
					var index = parseInt( btn.getAttribute( 'data-index' ), 10 ) || 0;
					var chosen = values[ index ];
					if ( ! chosen ) { return; }

					root.querySelectorAll( '.lk-giftprod-value' ).forEach( function ( b ) {
						var active = b === btn;
						b.classList.toggle( 'is-selected', active );
						b.setAttribute( 'aria-pressed', String( active ) );
					} );

					var statusEl  = root.querySelector( '[data-lk-status]' );
					if ( statusEl ) { statusEl.textContent = chosen.label; }
					var summaryEl = root.querySelector( '[data-lk-summary]' );
					if ( summaryEl ) { summaryEl.textContent = 'Gift card value: ' + chosen.label; }

					var orderEl = root.querySelector( '[data-lk-order]' );
					if ( ! orderEl ) { return; }
					if ( ! chosen.custom && chosen.buy ) {
						orderEl.href = chosen.buy;
						orderEl.removeAttribute( 'target' );
						orderEl.removeAttribute( 'rel' );
						orderEl.textContent = root.getAttribute( 'data-label-cart' );
					} else {
						var msg = root.getAttribute( 'data-template' ).replace( '{value}', chosen.label );
						orderEl.href = 'https://wa.me/' + root.getAttribute( 'data-whatsapp' ) + '?text=' + encodeURIComponent( msg );
						orderEl.setAttribute( 'target', '_blank' );
						orderEl.setAttribute( 'rel', 'noopener' );
						orderEl.textContent = root.getAttribute( 'data-label-enquiry' );
					}
				} );

				document.addEventListener( 'click', function ( event ) {
					var order = event.target.closest( '[data-lk-order]' );
					if ( ! order || order.getAttribute( 'target' ) === '_blank' ) { return; } // WhatsApp mode: leave alone
					var root = order.closest( '[data-lk-giftcard]' );
					var box = root && root.querySelector( '[data-lk-recipient]' );
					if ( ! box ) { return; }
					event.preventDefault();
					var get = function ( k ) { var el = box.querySelector( '[data-lk-gc="' + k + '"]' ); return el ? el.value.trim() : ''; };
					var email = get( 'email' );
					var err = box.querySelector( '[data-lk-gc-error]' );
					if ( email && ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( email ) ) {
						if ( err ) { err.hidden = false; }
						box.querySelector( '[data-lk-gc="email"]' ).focus();
						return;
					}
					if ( err ) { err.hidden = true; }
					var url = order.getAttribute( 'href' );
					if ( ! url || url === '#' ) { return; }
					if ( email ) {
						url += '&lk_gc_email=' + encodeURIComponent( email ) +
							'&lk_gc_name=' + encodeURIComponent( get( 'name' ) ) +
							'&lk_gc_from=' + encodeURIComponent( get( 'from' ) ) +
							'&lk_gc_msg=' + encodeURIComponent( get( 'msg' ) );
					}
					window.location.href = url;
				} );

				document.querySelectorAll( '[data-lk-giftcard]:not([data-lk-bound])' ).forEach( function ( root ) {
					root.setAttribute( 'data-lk-bound', 'true' );
					var selected = root.querySelector( '.lk-giftprod-value.is-selected' );
					if ( selected ) { selected.click(); }
				} );
			} )();
		</script>
		<?php
	}
}
