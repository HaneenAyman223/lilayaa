<?php
/**
 * Lila Kora — Workshop Booking Widget
 *
 * The "Choose your workshop. Choose your package." section of the Public
 * Workshops page: a row of date cards (one per scheduled session), a package
 * picker, a live summary, and a button that puts the chosen workshop + package
 * in the WooCommerce cart.
 *
 * HOW IT MAPS TO WOOCOMMERCE
 *   • One VARIABLE product per session date (e.g. "Public Workshop — Saturday
 *     10 October 2026") with a single attribute "Package" whose values are the
 *     package names below.
 *   • Capacity is set once, on the PARENT product's Inventory tab ("Manage
 *     stock" ticked, quantity = seats). The variations leave "Manage stock"
 *     off, so all three packages draw from the same pool of seats — which is
 *     exactly how a workshop's capacity works.
 *   • The widget reads that stock live ("8 spots left", "Sold out") and reads
 *     each package's price from its variation, so what's displayed can never
 *     differ from what checkout charges.
 *
 * Requires lk-commerce-hooks.php (loaded from functions.php). Without it, or
 * if a session has no product ID yet, that session falls back to a WhatsApp
 * enquiry instead of breaking.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-workshop-booking-widget.php';
 *   $widgets_manager->register( new \LK_Workshop_Booking_Widget() );
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class LK_Workshop_Booking_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-workshop-booking';
	}

	public function get_title() {
		return 'LK — Workshop Booking';
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'workshop', 'booking', 'calendar', 'dates', 'tickets' );
	}

	/* ---------------------------------------------------------------------
	 * Small helpers so the (long) style panel stays readable
	 * -------------------------------------------------------------------*/

	private function typo( $name, $label, $selector, $family, $size, $weight = '', $spacing = null, $upper = false, $line = null, $mobile = null ) {
		$size_opt = array( 'default' => array( 'unit' => 'px', 'size' => $size ) );
		if ( null !== $mobile ) {
			$size_opt['mobile_default'] = array( 'unit' => 'px', 'size' => $mobile );
		}
		$fields = array(
			'font_family' => array( 'default' => $family ),
			'font_size'   => $size_opt,
		);
		if ( '' !== $weight ) {
			$fields['font_weight'] = array( 'default' => $weight );
		}
		if ( null !== $spacing ) {
			$fields['letter_spacing'] = array( 'default' => array( 'unit' => 'em', 'size' => $spacing ) );
		}
		if ( $upper ) {
			$fields['text_transform'] = array( 'default' => 'uppercase' );
		}
		if ( null !== $line ) {
			$fields['line_height'] = array( 'default' => array( 'unit' => 'em', 'size' => $line ) );
		}
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => $name,
				'label'          => $label,
				'selector'       => '{{WRAPPER}} ' . $selector,
				'fields_options' => $fields,
			)
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

	/** Normal / Hover / Selected colour tabs for a clickable card. */
	private function card_states( $id, $selector ) {
		$this->start_controls_tabs( 'tabs_' . $id );

		$this->start_controls_tab( 'tab_' . $id . '_normal', array( 'label' => 'Normal' ) );
		$this->color( $id . '_bg', 'Background', $selector, '#FFFEFD', 'background-color', false, true );
		$this->color( $id . '_border', 'Border colour', $selector, 'rgba(105,33,55,0.16)', 'border-color', false, true );
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_' . $id . '_hover', array( 'label' => 'Hover' ) );
		$this->color( $id . '_bg_hover', 'Background', $selector . ':not(.is-selected):not(:disabled):hover', '#FFFEFD', 'background-color', false, true );
		$this->color( $id . '_border_hover', 'Border colour', $selector . ':not(.is-selected):not(:disabled):hover', '#692137', 'border-color', false, true );
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_' . $id . '_selected', array( 'label' => 'Selected' ) );
		$this->color( $id . '_bg_selected', 'Background', $selector . '.is-selected', '#F8ECE9', 'background-color', false, true );
		$this->color( $id . '_border_selected', 'Border colour', $selector . '.is-selected', '#692137', 'border-color', false, true );
		$this->end_controls_tab();

		$this->end_controls_tabs();
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section( 'section_content_heading', array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Upcoming public workshops', 'label_block' => true ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Choose your date', 'label_block' => true ) );
		$this->add_control( 'heading_line2', array( 'label' => 'Heading — second line', 'type' => Controls_Manager::TEXT, 'default' => 'and', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic word(s)', 'type' => Controls_Manager::TEXT, 'default' => 'package.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Select a confirmed session, then decide whether you would like to leave with your canvas or continue your artwork into a scarf.', 'label_block' => true ) );

		$notes = new Repeater();
		$notes->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$notes->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Note', 'label_block' => true ) );
		$this->add_control(
			'notes',
			array(
				'label'       => 'Numbered notes (optional — the current design has none)',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $notes->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ number }}} — {{{ text }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Step labels
		 * =======================================================*/
		$this->start_controls_section( 'section_content_steps', array( 'label' => 'Step Labels', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'step1_eyebrow', array( 'label' => 'Step 1 — eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Upcoming workshops', 'label_block' => true ) );
		$this->add_control( 'step1_title', array( 'label' => 'Step 1 — title', 'type' => Controls_Manager::TEXT, 'default' => 'Select a scheduled session', 'label_block' => true ) );
		$this->add_control( 'step2_eyebrow', array( 'label' => 'Step 2 — eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Choose a package', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'step2_title', array( 'label' => 'Step 2 — title', 'type' => Controls_Manager::TEXT, 'default' => 'How would you like your artwork to continue?', 'label_block' => true ) );
		$this->add_control( 'summary_label', array( 'label' => 'Summary label', 'type' => Controls_Manager::TEXT, 'default' => 'Your selection', 'separator' => 'before' ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Sessions (dates)
		 * =======================================================*/
		$this->start_controls_section( 'section_content_sessions', array( 'label' => 'Workshop Dates', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control(
			'sessions_note',
			array(
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<div style="line-height:1.6;font-size:12px;color:#a4afb7;">Each date is <strong>one WooCommerce variable product</strong> (with a "Package" attribute). Paste that product\'s ID on the date\'s row — seats left and prices then come from WooCommerce automatically.</div>',
			)
		);

		$this->add_control( 'hide_past', array( 'label' => 'Hide dates that have passed', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => 'yes' ) );
		$this->add_control( 'show_spots', array( 'label' => 'Show "N spots left" on each date', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => '', 'description' => 'Off matches the current design. A sold-out date is always marked, and seats are always enforced by WooCommerce.' ) );

		$sessions = new Repeater();
		$sessions->add_control( 'date', array( 'label' => 'Date', 'type' => Controls_Manager::DATE_TIME, 'picker_options' => array( 'enableTime' => false, 'dateFormat' => 'Y-m-d' ), 'default' => gmdate( 'Y-m-d' ) ) );
		$sessions->add_control( 'time', array( 'label' => 'Time', 'type' => Controls_Manager::TEXT, 'default' => '2:00 PM to 4:30 PM', 'label_block' => true ) );
		$sessions->add_control( 'location', array( 'label' => 'Location', 'type' => Controls_Manager::TEXT, 'default' => 'Dubai Design District', 'label_block' => true ) );
		$sessions->add_control( 'product_id', array( 'label' => 'WooCommerce product ID', 'type' => Controls_Manager::NUMBER, 'description' => 'The variable product for this date.' ) );
		$sessions->add_control( 'spots_text', array( 'label' => 'Spots label (fallback)', 'type' => Controls_Manager::TEXT, 'placeholder' => 'e.g. 12 spots left', 'description' => 'Only used when the product isn\'t tracking stock in WooCommerce.', 'label_block' => true ) );

		$this->add_control(
			'sessions',
			array(
				'label'       => 'Dates',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $sessions->get_controls(),
				'default'     => array(
					array( 'date' => '2026-10-10', 'time' => '2:00 PM to 4:30 PM', 'location' => 'Dubai Design District' ),
					array( 'date' => '2026-10-24', 'time' => '2:00 PM to 4:30 PM', 'location' => 'Jumeirah' ),
					array( 'date' => '2026-11-08', 'time' => '2:00 PM to 4:30 PM', 'location' => 'Dubai Design District' ),
					array( 'date' => '2026-11-21', 'time' => '2:00 PM to 4:30 PM', 'location' => 'Jumeirah' ),
				),
				'title_field' => '{{{ date }}} — {{{ location }}}',
			)
		);

		$this->add_control( 'empty_text', array( 'label' => 'Message when no dates are open', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'New workshop dates will be announced soon. Message us on WhatsApp and we\'ll let you know first.', 'label_block' => true, 'separator' => 'before' ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Packages
		 * =======================================================*/
		$this->start_controls_section( 'section_content_packages', array( 'label' => 'Packages', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control(
			'packages_note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<div style="line-height:1.6;font-size:12px;color:#a4afb7;">The <strong>name</strong> must match a value of the product\'s "Package" attribute (capitals and spacing don\'t matter).</div>',
			)
		);

		$packages = new Repeater();
		$packages->add_control( 'name', array( 'label' => 'Package name', 'type' => Controls_Manager::TEXT, 'default' => 'Package', 'label_block' => true ) );
		$packages->add_control( 'description', array( 'label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => '', 'label_block' => true ) );
		$packages->add_control( 'fallback_price', array( 'label' => 'Fallback price', 'type' => Controls_Manager::NUMBER, 'description' => 'Shown only if WooCommerce can\'t supply the price (no product yet / WhatsApp mode).' ) );
		$packages->add_control( 'badge', array( 'label' => 'Badge (optional)', 'type' => Controls_Manager::TEXT, 'placeholder' => 'e.g. Save AED 50', 'description' => 'Small dark tag beside the price.', 'label_block' => true ) );
		$packages->add_control( 'note', array( 'label' => 'Note under the card (optional)', 'type' => Controls_Manager::TEXT, 'placeholder' => 'e.g. You can choose a scarf add-on later.', 'label_block' => true ) );
		$packages->add_control( 'note_link_text', array( 'label' => 'Note — link text', 'type' => Controls_Manager::TEXT, 'placeholder' => 'e.g. View scarf add-on prices', 'label_block' => true ) );
		$packages->add_control( 'note_link', array( 'label' => 'Note — link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#scarf-addons' ) ) );

		$this->add_control(
			'packages',
			array(
				'label'       => 'Packages',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $packages->get_controls(),
				'default'     => array(
					array( 'name' => 'Workshop Only', 'description' => 'Guided session, all art materials, beverage and pastry. Take your finished canvas home.', 'fallback_price' => 190, 'note' => 'You can choose a scarf add-on later.', 'note_link_text' => 'View scarf add-on prices', 'note_link' => array( 'url' => '#scarf-addons' ) ),
					array( 'name' => 'Workshop + Premium Satin Scarf', 'description' => 'Everything in the workshop, plus artwork refinement and a 70 x 70 cm premium satin scarf.', 'fallback_price' => 390, 'badge' => 'Save AED 50' ),
					array( 'name' => 'Workshop + Pure Silk Scarf', 'description' => 'Everything in the workshop, plus artwork refinement and a 65 x 65 cm 100% pure silk scarf.', 'fallback_price' => 690, 'badge' => 'Save AED 50' ),
				),
				'title_field' => '{{{ name }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Checkout
		 * =======================================================*/
		$this->start_controls_section( 'section_content_checkout', array( 'label' => 'Checkout', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control(
			'checkout_mode',
			array(
				'label'   => 'Book button does',
				'type'    => Controls_Manager::SELECT,
				'default' => 'woocommerce',
				'options' => array(
					'woocommerce' => 'Add to cart (WooCommerce checkout)',
					'whatsapp'    => 'Send a WhatsApp enquiry',
				),
			)
		);
		$this->add_control( 'wc_destination', array( 'label' => 'After adding, send the customer to', 'type' => Controls_Manager::SELECT, 'default' => 'checkout', 'options' => array( 'cart' => 'Cart', 'checkout' => 'Checkout' ), 'condition' => array( 'checkout_mode' => 'woocommerce' ) ) );
		$this->add_control( 'currency', array( 'label' => 'Currency label', 'type' => Controls_Manager::TEXT, 'default' => 'AED' ) );
		$this->add_control( 'button_text', array( 'label' => 'Button text', 'type' => Controls_Manager::TEXT, 'default' => 'Continue to Booking', 'label_block' => true ) );
		$this->add_control( 'button_text_enquiry', array( 'label' => 'Button text when falling back to WhatsApp', 'type' => Controls_Manager::TEXT, 'default' => 'Continue to Booking', 'label_block' => true ) );
		$this->add_control( 'soldout_label', array( 'label' => 'Sold-out label', 'type' => Controls_Manager::TEXT, 'default' => 'Sold out' ) );
		$this->add_control( 'fine_print', array( 'label' => 'Small print under the button', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'Your place is secured as soon as your payment is complete.', 'label_block' => true ) );
		$this->add_control( 'whatsapp_number', array( 'label' => 'WhatsApp number (fallback)', 'type' => Controls_Manager::TEXT, 'default' => '971589610166', 'separator' => 'before' ) );
		$this->add_control( 'message_template', array( 'label' => 'WhatsApp message (fallback)', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Hello Lila Kora, I would like to book the {package} package for the public Canvas-to-Scarf workshop on {date}, from {time}, at {location}.', 'description' => 'Tags: {package} {date} {time} {location}', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'bg_color', 'Section background', '.lk-wsb', '#FFFEFD', 'background-color' );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label'       => 'Max width',
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors'   => array( '{{WRAPPER}} .lk-wsb' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => 'Section padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 32, 'bottom' => 90, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-wsb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'          => 'Booking panel padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 58, 'right' => 58, 'bottom' => 58, 'left' => 58, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 32, 'right' => 22, 'bottom' => 32, 'left' => 22, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-wsb-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->color( 'panel_bg', 'Panel background', '.lk-wsb-panel', '#FFFFFF', 'background-color', true );
		$this->color( 'panel_border', 'Panel border colour', '.lk-wsb-panel', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'           => 'panel_shadow',
				'selector'       => '{{WRAPPER}} .lk-wsb-panel',
				'fields_options' => array(
					'box_shadow_type' => array( 'default' => 'yes' ),
					'box_shadow'      => array( 'default' => array( 'horizontal' => 0, 'vertical' => 24, 'blur' => 60, 'spread' => 0, 'color' => 'rgba(105,33,55,0.08)' ) ),
				),
			)
		);
		$this->add_responsive_control( 'heading_spacing', array( 'label' => 'Space below heading block', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 120 ) ), 'default' => array( 'unit' => 'px', 'size' => 58 ), 'selectors' => array( '{{WRAPPER}} .lk-wsb-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading block
		 * =======================================================*/
		$this->start_controls_section( 'section_style_heading', array( 'label' => 'Heading Block', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour (all eyebrows)', '.lk-wsb-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-wsb-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );

		$this->color( 'heading_color', 'Heading colour', '.lk-wsb-heading h2', '#57282D', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic word colour', '.lk-wsb-heading h2 em', '#57282D' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-wsb-heading h2', 'Cormorant Garamond', 56, '400', -0.025, false, 0.98, 34 );

		$this->color( 'desc_color', 'Paragraph colour', '.lk-wsb-heading > p.lk-wsb-desc', '#8F8584', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-wsb-heading > p.lk-wsb-desc', 'Montserrat', 16 );

		$this->color( 'note_border_color', 'Note divider colour', '.lk-wsb-note', 'rgba(105,33,55,0.16)', 'border-color', true );
		$this->color( 'note_number_color', 'Note number colour', '.lk-wsb-note span', '#FF715E' );
		$this->typo( 'note_number_typography', 'Note number typography', '.lk-wsb-note span', 'Cormorant Garamond', 16 );
		$this->color( 'note_text_color', 'Note text colour', '.lk-wsb-note p', '#8F8584' );
		$this->typo( 'note_text_typography', 'Note text typography', '.lk-wsb-note p', 'Montserrat', 12.8 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Steps
		 * =======================================================*/
		$this->start_controls_section( 'section_style_steps', array( 'label' => 'Step Headings', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'step_divider', 'Divider between steps', '.lk-wsb-step + .lk-wsb-step', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->color( 'step_number_color', 'Step number colour', '.lk-wsb-step-head > span', '#FF715E', 'color', true );
		$this->typo( 'step_number_typography', 'Step number typography', '.lk-wsb-step-head > span', 'Cormorant Garamond', 17.6 );
		$this->color( 'step_title_color', 'Step title colour', '.lk-wsb-step-head h3', '#692137', 'color', true );
		$this->typo( 'step_title_typography', 'Step title typography', '.lk-wsb-step-head h3', 'Cormorant Garamond', 34, '400', null, false, 1.08, 26 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Date cards
		 * =======================================================*/
		$this->start_controls_section( 'section_style_dates', array( 'label' => 'Date Cards', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->card_states( 'date', '.lk-wsb-date' );
		$this->add_control( 'date_soldout_opacity', array( 'label' => 'Sold-out card opacity', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0.2, 'max' => 1, 'step' => 0.05 ) ), 'default' => array( 'size' => 0.5 ), 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-wsb-date.is-soldout' => 'opacity: {{SIZE}};' ) ) );
		$this->add_responsive_control( 'date_min_height', array( 'label' => 'Card min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 160, 'max' => 400 ) ), 'default' => array( 'unit' => 'px', 'size' => 240 ), 'selectors' => array( '{{WRAPPER}} .lk-wsb-date' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'date_gap', array( 'label' => 'Gap between cards', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 10 ), 'selectors' => array( '{{WRAPPER}} .lk-wsb-dates' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->color( 'date_day_color', 'Weekday colour', '.lk-wsb-date-day', '#692137', 'color', true );
		$this->typo( 'date_day_typography', 'Weekday typography', '.lk-wsb-date-day', 'Montserrat', 9.9, '600', 0.16, true );
		$this->color( 'date_num_color', 'Day number colour', '.lk-wsb-date > strong', '#692137', 'color', true );
		$this->typo( 'date_num_typography', 'Day number typography', '.lk-wsb-date > strong', 'Cormorant Garamond', 48, '400', null, false, 1 );
		$this->color( 'date_month_color', 'Month colour', '.lk-wsb-date-month', '#692137', 'color', true );
		$this->typo( 'date_month_typography', 'Month typography', '.lk-wsb-date-month', 'Montserrat', 10.7, '', 0.1, true );
		$this->color( 'date_meta_color', 'Time & location colour', '.lk-wsb-date-time, {{WRAPPER}} .lk-wsb-date-location', '#8F8584', 'color', true );
		$this->typo( 'date_meta_typography', 'Time & location typography', '.lk-wsb-date-time, {{WRAPPER}} .lk-wsb-date-location', 'Montserrat', 11, '', null, false, 1.35 );
		$this->color( 'date_meta_line', 'Time & location divider', '.lk-wsb-date-time, {{WRAPPER}} .lk-wsb-date-location', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->color( 'date_spots_color', '"Spots left" colour', '.lk-wsb-date small', '#692137', 'color', true );
		$this->typo( 'date_spots_typography', '"Spots left" typography', '.lk-wsb-date small', 'Montserrat', 10.9 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Package cards
		 * =======================================================*/
		$this->start_controls_section( 'section_style_packages', array( 'label' => 'Package Cards', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->card_states( 'pkg', '.lk-wsb-package' );
		$this->add_responsive_control( 'pkg_min_height', array( 'label' => 'Card min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 120, 'max' => 400 ) ), 'default' => array( 'unit' => 'px', 'size' => 230 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 0 ), 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-wsb-package' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'pkg_gap', array( 'label' => 'Gap between cards', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 10 ), 'selectors' => array( '{{WRAPPER}} .lk-wsb-packages' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control(
			'pkg_mark_color',
			array(
				'label'     => 'Radio mark colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .lk-wsb-mark' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .lk-wsb-package.is-selected .lk-wsb-mark' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->color( 'pkg_name_color', 'Name colour', '.lk-wsb-package-copy strong', '#692137', 'color', true );
		$this->typo( 'pkg_name_typography', 'Name typography', '.lk-wsb-package-copy strong', 'Cormorant Garamond', 19.5, '500' );
		$this->color( 'pkg_desc_color', 'Description colour', '.lk-wsb-package-copy small', '#8F8584', 'color', true );
		$this->typo( 'pkg_desc_typography', 'Description typography', '.lk-wsb-package-copy small', 'Montserrat', 11.5, '', null, false, 1.55 );
		$this->color( 'pkg_price_color', 'Price colour', '.lk-wsb-package-price', '#692137', 'color', true );
		$this->typo( 'pkg_price_typography', 'Price typography', '.lk-wsb-package-price', 'Cormorant Garamond', 24, '500' );
		$this->color( 'pkg_badge_bg', 'Badge background', '.lk-wsb-badge', '#692137', 'background-color', true );
		$this->color( 'pkg_badge_color', 'Badge text colour', '.lk-wsb-badge', '#FFFFFF' );
		$this->typo( 'pkg_badge_typography', 'Badge typography', '.lk-wsb-badge', 'Montserrat', 9.9, '600', 0.05, true );
		$this->color( 'pkg_note_color', 'Note under card — colour', '.lk-wsb-later', '#8F8584', 'color', true );
		$this->typo( 'pkg_note_typography', 'Note under card — typography', '.lk-wsb-later', 'Montserrat', 11.5, '', null, false, 1.5 );
		$this->color( 'pkg_note_link_color', 'Note under card — link colour', '.lk-wsb-later a', '#692137' );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Summary & button
		 * =======================================================*/
		$this->start_controls_section( 'section_style_summary', array( 'label' => 'Summary & Button', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'summary_border', 'Summary border colour', '.lk-wsb-summary', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->color( 'summary_label_color', 'Label colour', '.lk-wsb-summary small', '#8F8584', 'color', true );
		$this->typo( 'summary_label_typography', 'Label typography', '.lk-wsb-summary small', 'Montserrat', 10, '600', 0.12, true );
		$this->color( 'summary_sel_color', 'Selection colour', '.lk-wsb-summary p', '#692137', 'color', true );
		$this->typo( 'summary_sel_typography', 'Selection typography', '.lk-wsb-summary p', 'Cormorant Garamond', 17.6 );
		$this->color( 'summary_venue_color', 'Time & place colour', '.lk-wsb-summary div > span', '#8F8584', 'color', true );
		$this->typo( 'summary_venue_typography', 'Time & place typography', '.lk-wsb-summary div > span', 'Montserrat', 11.5 );
		$this->color( 'summary_total_color', 'Total colour', '.lk-wsb-summary > strong', '#692137', 'color', true );
		$this->typo( 'summary_total_typography', 'Total typography', '.lk-wsb-summary > strong', 'Cormorant Garamond', 32, '500' );

		$this->add_control( 'heading_button', array( 'label' => 'Book Button', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->typo( 'btn_typography', 'Button typography', '.lk-wsb-order', 'Montserrat', 10.9, '600', 0.17, true );
		$this->add_responsive_control( 'btn_min_height', array( 'label' => 'Button min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 40, 'max' => 100 ) ), 'default' => array( 'unit' => 'px', 'size' => 58 ), 'selectors' => array( '{{WRAPPER}} .lk-wsb-order' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->start_controls_tabs( 'tabs_btn' );
		$this->start_controls_tab( 'tab_btn_normal', array( 'label' => 'Normal' ) );
		$this->color( 'btn_bg', 'Background', '.lk-wsb-order', '#692137', 'background-color', false, true );
		$this->color( 'btn_color', 'Text colour', '.lk-wsb-order', '#FFFFFF', 'color', false, true );
		$this->color( 'btn_border', 'Border colour', '.lk-wsb-order', '#692137', 'border-color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_hover', array( 'label' => 'Hover' ) );
		$this->color( 'btn_bg_hover', 'Background', '.lk-wsb-order:not(.is-disabled):hover', 'rgba(0,0,0,0)', 'background-color', false, true );
		$this->color( 'btn_color_hover', 'Text colour', '.lk-wsb-order:not(.is-disabled):hover', '#692137', 'color', false, true );
		$this->color( 'btn_border_hover', 'Border colour', '.lk-wsb-order:not(.is-disabled):hover', '#692137', 'border-color', false, true );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->color( 'fine_color', 'Small print colour', '.lk-wsb-fine', '#8F8584', 'color', true );
		$this->typo( 'fine_typography', 'Small print typography', '.lk-wsb-fine', 'Montserrat', 11.2 );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$hooks_ok = function_exists( 'lk_wc_buy_url' ) && function_exists( 'wc_get_product' );
		$wc_on    = ( 'woocommerce' === $s['checkout_mode'] && $hooks_ok );
		$today    = current_time( 'Y-m-d' );
		$editor   = current_user_can( 'edit_posts' );

		$packages = array();
		foreach ( (array) $s['packages'] as $pkg ) {
			$packages[] = array(
				'name'     => (string) $pkg['name'],
				'desc'     => (string) $pkg['description'],
				'fallback' => ( '' !== (string) $pkg['fallback_price'] ) ? (string) $pkg['fallback_price'] : '',
				'badge'    => isset( $pkg['badge'] ) ? (string) $pkg['badge'] : '',
				'note'     => isset( $pkg['note'] ) ? (string) $pkg['note'] : '',
				'note_link_text' => isset( $pkg['note_link_text'] ) ? (string) $pkg['note_link_text'] : '',
				'note_link'      => ( isset( $pkg['note_link']['url'] ) ) ? (string) $pkg['note_link']['url'] : '',
			);
		}

		$sessions = array();
		$notices  = array();

		foreach ( (array) $s['sessions'] as $row ) {
			$dt = date_create_immutable( (string) $row['date'], new DateTimeZone( 'UTC' ) );
			if ( ! $dt ) {
				$notices[] = 'A date row has an invalid date and was skipped.';
				continue;
			}
			if ( 'yes' === $s['hide_past'] && $dt->format( 'Y-m-d' ) < $today ) {
				continue;
			}

			$pid     = absint( $row['product_id'] );
			$product = ( $wc_on && $pid ) ? wc_get_product( $pid ) : null;
			$sold    = false;
			$spots   = '';

			if ( $product ) {
				if ( ! $product->is_in_stock() ) {
					$sold = true;
				} elseif ( $product->managing_stock() && null !== $product->get_stock_quantity() ) {
					$qty   = (int) $product->get_stock_quantity();
					$spots = ( 1 === $qty ) ? '1 spot left' : $qty . ' spots left';
				}
			}
			if ( 'yes' !== $s['show_spots'] ) {
				$spots = '';
			} elseif ( '' === $spots && ! $sold ) {
				$spots = (string) $row['spots_text'];
			}

			$buy    = array();
			$prices = array();
			foreach ( $packages as $pkg ) {
				$hit = ( $wc_on && $pid ) ? lk_wc_buy_url( $pid, array( $pkg['name'] ), $s['wc_destination'] ) : null;
				if ( $wc_on && $pid && ! $hit ) {
					$notices[] = $dt->format( 'j M Y' ) . ' / ' . $pkg['name'];
				}
				$buy[]    = $hit ? $hit['url'] : '';
				$prices[] = $hit ? $hit['price'] : '';
			}
			if ( $wc_on && ! $pid ) {
				$notices[] = $dt->format( 'j M Y' ) . ' has no WooCommerce product ID yet';
			}

			$sessions[] = array(
				'day'      => $dt->format( 'D' ),
				'num'      => $dt->format( 'j' ),
				'month'    => $dt->format( 'M Y' ),
				'label'    => $dt->format( 'l, j F Y' ),
				'time'     => (string) $row['time'],
				'location' => (string) $row['location'],
				'sold'     => $sold,
				'spots'    => $sold ? $s['soldout_label'] : $spots,
				'buy'      => $buy,
				'price'    => $prices,
			);
		}

		// First bookable session is pre-selected.
		$sel_session = 0;
		foreach ( $sessions as $i => $sess ) {
			if ( ! $sess['sold'] ) {
				$sel_session = $i;
				break;
			}
		}
		$sel_package = 0;
		$currency    = $s['currency'];

		$js_sessions = array();
		foreach ( $sessions as $sess ) {
			$js_sessions[] = array(
				'label'    => $sess['label'],
				'time'     => $sess['time'],
				'location' => $sess['location'],
				'sold'     => $sess['sold'],
				'buy'      => $sess['buy'],
				'price'    => $sess['price'],
			);
		}
		$js_packages = array();
		foreach ( $packages as $pkg ) {
			$js_packages[] = array( 'name' => $pkg['name'], 'fallback' => $pkg['fallback'] );
		}

		$current      = $sessions ? $sessions[ $sel_session ] : null;
		$initial_price = static function ( $sess, $pkg_index, $packages ) {
			if ( $sess && '' !== $sess['price'][ $pkg_index ] ) {
				return $sess['price'][ $pkg_index ];
			}
			return $packages[ $pkg_index ]['fallback'];
		};
		?>
		<section class="lk-wsb" data-lk-wsb
			data-sessions="<?php echo esc_attr( wp_json_encode( $js_sessions ) ); ?>"
			data-packages="<?php echo esc_attr( wp_json_encode( $js_packages ) ); ?>"
			data-currency="<?php echo esc_attr( $currency ); ?>"
			data-whatsapp="<?php echo esc_attr( $s['whatsapp_number'] ); ?>"
			data-template="<?php echo esc_attr( $s['message_template'] ); ?>">

			<div class="lk-wsb-heading">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-wsb-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2><?php echo esc_html( $s['heading_line1'] ); ?><br><?php echo esc_html( $s['heading_line2'] ); ?> <em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-wsb-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>
				<?php foreach ( (array) $s['notes'] as $note ) : ?>
					<div class="lk-wsb-note"><span><?php echo esc_html( $note['number'] ); ?></span><p><?php echo esc_html( $note['text'] ); ?></p></div>
				<?php endforeach; ?>
			</div>

			<div class="lk-wsb-panel">
				<div class="lk-wsb-step">
					<div class="lk-wsb-step-head"><span>01</span><div><p class="lk-wsb-eyebrow"><?php echo esc_html( $s['step1_eyebrow'] ); ?></p><h3><?php echo esc_html( $s['step1_title'] ); ?></h3></div></div>

					<?php if ( $sessions ) : ?>
						<div class="lk-wsb-dates" role="group" aria-label="<?php echo esc_attr( $s['step1_title'] ); ?>">
							<?php foreach ( $sessions as $i => $sess ) : ?>
								<button type="button" class="lk-wsb-date<?php echo $i === $sel_session ? ' is-selected' : ''; ?><?php echo $sess['sold'] ? ' is-soldout' : ''; ?>" data-session="<?php echo esc_attr( $i ); ?>" aria-pressed="<?php echo $i === $sel_session ? 'true' : 'false'; ?>"<?php echo $sess['sold'] ? ' disabled' : ''; ?>>
									<span class="lk-wsb-date-day"><?php echo esc_html( $sess['day'] ); ?></span>
									<strong><?php echo esc_html( $sess['num'] ); ?></strong>
									<span class="lk-wsb-date-month"><?php echo esc_html( $sess['month'] ); ?></span>
									<span class="lk-wsb-date-time"><?php echo esc_html( $sess['time'] ); ?></span>
									<span class="lk-wsb-date-location"><?php echo esc_html( $sess['location'] ); ?></span>
									<?php if ( '' !== $sess['spots'] ) : ?><small><?php echo esc_html( $sess['spots'] ); ?></small><?php endif; ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p class="lk-wsb-empty"><?php echo esc_html( $s['empty_text'] ); ?></p>
					<?php endif; ?>
				</div>

				<div class="lk-wsb-step">
					<div class="lk-wsb-step-head"><span>02</span><div><p class="lk-wsb-eyebrow"><?php echo esc_html( $s['step2_eyebrow'] ); ?></p><h3><?php echo esc_html( $s['step2_title'] ); ?></h3></div></div>
					<div class="lk-wsb-packages">
						<?php foreach ( $packages as $p => $pkg ) :
							$price = $initial_price( $current, $p, $packages );
							?>
							<div class="lk-wsb-package-wrap">
								<button type="button" class="lk-wsb-package<?php echo 0 === $p ? ' is-selected' : ''; ?>" data-package="<?php echo esc_attr( $p ); ?>" aria-pressed="<?php echo 0 === $p ? 'true' : 'false'; ?>">
									<span class="lk-wsb-mark"></span>
									<span class="lk-wsb-package-copy"><strong><?php echo esc_html( $pkg['name'] ); ?></strong><?php if ( '' !== $pkg['desc'] ) : ?><small><?php echo esc_html( $pkg['desc'] ); ?></small><?php endif; ?></span>
									<span class="lk-wsb-package-foot">
										<?php if ( '' !== $pkg['badge'] ) : ?><span class="lk-wsb-badge"><?php echo esc_html( $pkg['badge'] ); ?></span><?php endif; ?>
										<b class="lk-wsb-package-price" data-price-for="<?php echo esc_attr( $p ); ?>"><?php echo '' !== $price ? esc_html( $currency . ' ' . $price ) : ''; ?></b>
									</span>
								</button>
								<?php if ( '' !== $pkg['note'] || '' !== $pkg['note_link_text'] ) : ?>
									<p class="lk-wsb-later"><?php echo esc_html( $pkg['note'] ); ?><?php if ( '' !== $pkg['note_link_text'] && '' !== $pkg['note_link'] ) : ?> <a href="<?php echo esc_url( $pkg['note_link'] ); ?>"><?php echo esc_html( $pkg['note_link_text'] ); ?></a><?php endif; ?></p>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="lk-wsb-summary">
					<div>
						<small><?php echo esc_html( $s['summary_label'] ); ?></small>
						<p data-lk-selection></p>
						<span data-lk-venue></span>
					</div>
					<strong data-lk-total></strong>
				</div>

				<a class="lk-wsb-order" data-lk-order data-label-cart="<?php echo esc_attr( $s['button_text'] ); ?>" data-label-enquiry="<?php echo esc_attr( $s['button_text_enquiry'] ); ?>" href="#" target="_blank" rel="noopener"><?php echo esc_html( $s['button_text'] ); ?></a>

				<?php if ( ! empty( $s['fine_print'] ) ) : ?><p class="lk-wsb-fine"><?php echo esc_html( $s['fine_print'] ); ?></p><?php endif; ?>

				<?php if ( $editor ) : ?>
					<?php if ( 'woocommerce' === $s['checkout_mode'] && ! $hooks_ok ) : ?>
						<p class="lk-wsb-notice">Cart mode is on, but lk-commerce-hooks.php isn't loaded — add its require_once line to functions.php. Falling back to WhatsApp. (Only visible to editors.)</p>
					<?php elseif ( $notices ) : ?>
						<p class="lk-wsb-notice">Set-up check: <?php echo esc_html( implode( '; ', array_unique( $notices ) ) ); ?>. Those combinations fall back to WhatsApp — the product's "Package" values must match the package names above. (Only visible to editors.)</p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</section>

		<style>
			.lk-wsb button { -webkit-appearance: none; appearance: none; box-shadow: none; font-family: inherit; }
			.lk-wsb-heading { display: grid; grid-template-columns: 1fr 0.78fr; gap: 20px 8vw; align-items: end; max-width: 1080px; text-align: left; }
			.lk-wsb-heading .lk-wsb-eyebrow { grid-column: 1 / -1; margin: 0; }
			.lk-wsb-heading h2 { margin: 0; font-style: normal; }
			.lk-wsb-heading h2 em { font-style: italic; }
			.lk-wsb-heading > p.lk-wsb-desc { max-width: 540px; margin: 0 0 8px; }
			.lk-wsb-note { display: inline-grid; grid-template-columns: 38px 1fr; gap: 12px; width: min(48%, 390px); padding: 15px 18px; box-sizing: border-box; border-top: 1px solid; text-align: left; vertical-align: top; }
			.lk-wsb-note:last-child { border-bottom: 1px solid; }
			.lk-wsb-note span { font-style: normal; }
			.lk-wsb-note p { margin: 0; }
			.lk-wsb-panel { border: 1px solid; box-sizing: border-box; }
			.lk-wsb-step + .lk-wsb-step { margin-top: 46px; padding-top: 42px; border-top: 1px solid; }
			.lk-wsb-step-head { display: grid; grid-template-columns: 45px 1fr; gap: 16px; align-items: start; margin-bottom: 25px; }
			.lk-wsb-step-head .lk-wsb-eyebrow { margin: 0 0 8px; }
			.lk-wsb-step-head h3 { margin: 0; }
			.lk-wsb-dates { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-left: 61px; }
			.lk-wsb-date { display: flex; flex-direction: column; align-items: center; padding: 20px 12px 16px; border: 1px solid; cursor: pointer; text-align: center; transition: border-color .25s ease, background .25s ease, transform .25s ease; }
			.lk-wsb-date:not(.is-selected):not(:disabled):hover { transform: translateY(-3px); }
			.lk-wsb-date.is-soldout { cursor: not-allowed; }
			.lk-wsb-date > strong { margin: 5px 0 0; font-style: normal; }
			.lk-wsb-date-month { margin-bottom: 16px; }
			.lk-wsb-date-time, .lk-wsb-date-location { width: 100%; padding: 7px 0; border-top: 1px solid; box-sizing: border-box; }
			.lk-wsb-date small { margin-top: auto; padding-top: 8px; }
			.lk-wsb-empty { margin: 0 0 0 61px; }
			.lk-wsb-packages { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 61px; }
			.lk-wsb-package { display: grid; grid-template-columns: 20px 1fr; gap: 16px; align-items: start; width: 100%; padding: 24px 20px; border: 1px solid; cursor: pointer; text-align: left; transition: border-color .25s ease, background .25s ease, transform .25s ease; }
			.lk-wsb-package:not(.is-selected):hover { transform: translateY(-3px); }
			.lk-wsb-mark { width: 16px; height: 16px; border: 1px solid; border-radius: 50%; box-sizing: border-box; background: transparent; box-shadow: inset 0 0 0 4px #FFFEFD; }
			.lk-wsb-package-copy strong, .lk-wsb-package-copy small { display: block; }
			.lk-wsb-package-copy small { max-width: 520px; margin-top: 5px; }
			.lk-wsb-package-wrap { min-width: 0; }
			.lk-wsb-package-foot { grid-column: 2; align-self: end; display: flex; justify-content: space-between; align-items: flex-end; gap: 8px; }
			.lk-wsb-package-price { white-space: nowrap; }
			.lk-wsb-badge { padding: 5px 7px; line-height: 1.2; }
			.lk-wsb-later { margin: 10px 8px 0; }
			.lk-wsb-later a { display: inline-block; text-decoration: none; border-bottom: 1px solid currentColor; font-weight: 600; white-space: nowrap; }
			.lk-wsb-summary { display: flex; justify-content: space-between; gap: 25px; align-items: end; margin-top: 38px; padding: 23px 0; border-top: 1px solid; border-bottom: 1px solid; }
			.lk-wsb-summary small { display: block; }
			.lk-wsb-summary p { margin: 4px 0 0; }
			.lk-wsb-summary div > span { display: block; margin-top: 4px; }
			.lk-wsb-summary > strong { white-space: nowrap; }
			.lk-wsb-order { width: 100%; box-sizing: border-box; display: flex; align-items: center; justify-content: center; margin-top: 38px; text-decoration: none; border: 1px solid; border-radius: 0; cursor: pointer; transition: color .25s ease, background .25s ease; }
			.lk-wsb-order.is-disabled { opacity: 0.45; cursor: not-allowed; pointer-events: none; }
			.lk-wsb-fine { margin: 13px 0 0; text-align: center; }
			.lk-wsb-notice { margin: 20px 0 0; padding: 12px 16px; background: rgba(255,113,94,0.12); border: 1px dashed #FF715E; font-family: 'Montserrat', Arial, sans-serif; font-size: 12px; line-height: 1.6; color: #57282D; }
			@media (max-width: 1120px) {
				.lk-wsb-dates { display: flex; margin-left: 0; padding-bottom: 10px; overflow-x: auto; scroll-snap-type: x mandatory; }
				.lk-wsb-date { min-width: 190px; scroll-snap-align: start; }
			}
			@media (max-width: 820px) {
				.lk-wsb-heading { grid-template-columns: 1fr; }
				.lk-wsb-heading .lk-wsb-eyebrow { grid-column: auto; }
				.lk-wsb-note { width: 100%; }
				.lk-wsb-note:last-child { border-bottom: 0; }
				.lk-wsb-packages { grid-template-columns: 1fr; margin-left: 0; }
			}
			@media (max-width: 540px) {
				.lk-wsb-empty { margin-left: 0; }
				.lk-wsb-step-head { grid-template-columns: 34px 1fr; }
				.lk-wsb-summary { align-items: flex-start; flex-direction: column; }
			}
		</style>

		<script>
			( function () {
				function init( root ) {
					var sessions = [], packages = [];
					try {
						sessions = JSON.parse( root.getAttribute( 'data-sessions' ) || '[]' );
						packages = JSON.parse( root.getAttribute( 'data-packages' ) || '[]' );
					} catch ( e ) {}

					var currency = root.getAttribute( 'data-currency' ) || '';
					var whatsapp = root.getAttribute( 'data-whatsapp' ) || '';
					var template = root.getAttribute( 'data-template' ) || '';
					var selEl    = root.querySelector( '[data-lk-selection]' );
					var venueEl  = root.querySelector( '[data-lk-venue]' );
					var totalEl  = root.querySelector( '[data-lk-total]' );
					var orderEl  = root.querySelector( '[data-lk-order]' );

					var si = 0, pi = 0;
					var chosen = root.querySelector( '.lk-wsb-date.is-selected' );
					if ( chosen ) { si = parseInt( chosen.getAttribute( 'data-session' ), 10 ) || 0; }

					function price( s, p ) {
						var live = s && s.price && s.price[ p ];
						return live ? live : ( packages[ p ] ? packages[ p ].fallback : '' );
					}

					function update() {
						var s = sessions[ si ], p = packages[ pi ];

						root.querySelectorAll( '.lk-wsb-date' ).forEach( function ( b ) {
							var on = parseInt( b.getAttribute( 'data-session' ), 10 ) === si;
							b.classList.toggle( 'is-selected', on );
							b.setAttribute( 'aria-pressed', String( on ) );
						} );
						root.querySelectorAll( '.lk-wsb-package' ).forEach( function ( b ) {
							var on = parseInt( b.getAttribute( 'data-package' ), 10 ) === pi;
							b.classList.toggle( 'is-selected', on );
							b.setAttribute( 'aria-pressed', String( on ) );
						} );
						// A package's price can differ per date, so refresh every card.
						root.querySelectorAll( '[data-price-for]' ).forEach( function ( el ) {
							var v = price( s, parseInt( el.getAttribute( 'data-price-for' ), 10 ) );
							el.textContent = v ? currency + ' ' + v : '';
						} );

						if ( ! s || ! p ) {
							if ( orderEl ) { orderEl.classList.add( 'is-disabled' ); orderEl.setAttribute( 'aria-disabled', 'true' ); orderEl.removeAttribute( 'target' ); orderEl.href = '#'; }
							return;
						}

						var v = price( s, pi );
						if ( selEl ) { selEl.textContent = p.name + ' · ' + s.label; }
						if ( venueEl ) { venueEl.textContent = s.time + ' · ' + s.location; }
						if ( totalEl ) { totalEl.textContent = v ? currency + ' ' + v : ''; }

						if ( ! orderEl ) { return; }
						var open = ! s.sold;
						orderEl.classList.toggle( 'is-disabled', ! open );
						if ( open ) { orderEl.removeAttribute( 'aria-disabled' ); } else { orderEl.setAttribute( 'aria-disabled', 'true' ); }

						var buy = s.buy && s.buy[ pi ];
						if ( buy ) {
							orderEl.href = buy;
							orderEl.removeAttribute( 'target' );
							orderEl.removeAttribute( 'rel' );
							orderEl.textContent = orderEl.getAttribute( 'data-label-cart' );
						} else {
							var msg = template.replace( '{package}', p.name ).replace( '{date}', s.label ).replace( '{time}', s.time ).replace( '{location}', s.location );
							orderEl.href = 'https://wa.me/' + whatsapp + '?text=' + encodeURIComponent( msg );
							orderEl.setAttribute( 'target', '_blank' );
							orderEl.setAttribute( 'rel', 'noopener' );
							orderEl.textContent = orderEl.getAttribute( 'data-label-enquiry' );
						}
					}

					root.addEventListener( 'click', function ( event ) {
						var d = event.target.closest( '.lk-wsb-date' );
						if ( d && ! d.disabled ) {
							si = parseInt( d.getAttribute( 'data-session' ), 10 ) || 0;
							update();
							return;
						}
						var k = event.target.closest( '.lk-wsb-package' );
						if ( k ) {
							pi = parseInt( k.getAttribute( 'data-package' ), 10 ) || 0;
							update();
							return;
						}
						var o = event.target.closest( '.lk-wsb-order' );
						if ( o && o.classList.contains( 'is-disabled' ) ) { event.preventDefault(); }
					} );

					update();
				}

				document.querySelectorAll( '[data-lk-wsb]:not([data-lk-bound])' ).forEach( function ( root ) {
					root.setAttribute( 'data-lk-bound', 'true' );
					init( root );
				} );
			} )();
		</script>
		<?php
	}
}
