<?php
/**
 * Lila Kora — Box Product Configurator Widget
 *
 * The interactive "Canvas-to-Scarf Experience Box" product section:
 * a sticky product image, live price, a Fabric picker and a Size
 * picker (sizes — and their prices — depend on which fabric is
 * selected), an order button that builds a WhatsApp message from the
 * current selection, and a running summary line.
 *
 * Each fabric's sizes/prices are entered as one "Label | Price" per
 * line (same trick used for the header's nav-dropdown links) rather
 * than a nested repeater, which Elementor doesn't support.
 *
 * Self-contained single file, including its own interaction JS
 * (vanilla, event-delegated, safe with multiple instances). To wire
 * it in, add to class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-box-product-widget.php';
 *   $widgets_manager->register( new \LK_Box_Product_Widget() );
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
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class LK_Box_Product_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-box-product';
	}

	public function get_title() {
		return 'LK — Box Product Configurator';
	}

	public function get_icon() {
		return 'eicon-product-add-to-cart';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'box', 'configurator', 'product', 'fabric', 'size' );
	}

	/**
	 * Parses a "Label | Price" (one per line) textarea into
	 * [['label'=>...,'price'=>...], ...]. Bad lines are skipped
	 * rather than fataling.
	 */
	private function parse_sizes( $raw, $option_name = '', $wc = null, &$missing = array() ) {
		$sizes = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
			$line = trim( $line );
			if ( '' === $line || false === strpos( $line, '|' ) ) {
				continue;
			}
			$parts = array_map( 'trim', explode( '|', $line, 3 ) );
			$label = $parts[0];
			$price = isset( $parts[1] ) ? $parts[1] : '';
			if ( '' === $label ) {
				continue;
			}

			// WooCommerce mode: find the real variation for "option + size".
			// Its price replaces the typed one (single source of truth), and
			// its add-to-cart URL is what the button uses. No match = this
			// size quietly falls back to WhatsApp and is reported to editors.
			$buy = '';
			if ( $wc ) {
				$hit = lk_wc_buy_url( $wc['product_id'], array( $option_name, $label ), $wc['dest'] );
				if ( $hit ) {
					$buy   = $hit['url'];
					$price = $hit['price'];
				} else {
					$missing[] = trim( $option_name . ' / ' . $label );
				}
			}

			$sizes[] = array( 'label' => $label, 'price' => $price, 'buy' => $buy );
		}
		return $sizes;
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Create at home', 'label_block' => true ) );
		$this->add_control( 'heading', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Canvas-to-Scarf Experience Box', 'label_block' => true, 'description' => 'A line break is inserted automatically before the last word if there\'s room — simplest is to just write it as one line.' ) );
		$this->add_control( 'description', array( 'label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Create your original artwork at home, send it to us and receive a scarf made from your own design. Every option includes the complete creative box, professional artwork refinement, your approval before production and the selected scarf.', 'label_block' => true ) );
		$this->add_control( 'delivery_note', array( 'label' => 'Delivery note (small text below the order button)', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'Experience Box delivery within the UAE usually takes 2 to 3 business days. International delivery may take up to 2 weeks, depending on the destination.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Details Accordion
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_accordion',
			array( 'label' => 'Details Accordion', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'show_accordion', array( 'label' => 'Show accordion', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );

		$accordion_repeater = new Repeater();
		$accordion_repeater->add_control( 'label', array( 'label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Row label', 'label_block' => true ) );
		$accordion_repeater->add_control( 'content', array( 'label' => 'Content', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Row content.', 'label_block' => true ) );
		$accordion_repeater->add_control( 'open_by_default', array( 'label' => 'Open by default', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => '' ) );

		$this->add_control(
			'accordion_rows',
			array(
				'label' => 'Rows', 'type' => Controls_Manager::REPEATER, 'fields' => $accordion_repeater->get_controls(),
				'default' => array(
					array( 'label' => 'Material & Care', 'content' => 'Dry clean only. Store folded or gently rolled, away from direct sunlight.' ),
					array( 'label' => 'Shipping & Delivery', 'content' => 'Ready-to-wear pieces ship within the UAE in 2 to 3 business days. International delivery may take up to 2 weeks depending on destination.' ),
					array( 'label' => 'Presentation', 'content' => 'Arrives in a Lila Kora gift box, ready to give or keep.' ),
				),
				'title_field' => '{{{ label }}}',
				'condition'   => array( 'show_accordion' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Fabrics & Sizes
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_fabrics',
			array( 'label' => 'Fabrics & Sizes', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'name', array( 'label' => 'Fabric name', 'type' => Controls_Manager::TEXT, 'default' => 'Fabric name', 'label_block' => true ) );
		$repeater->add_control( 'description', array( 'label' => 'Fabric description', 'type' => Controls_Manager::TEXT, 'default' => 'Short description.', 'label_block' => true ) );
		$repeater->add_control( 'sizes', array( 'label' => 'Sizes & prices', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => "70 x 70 cm | 390\n90 x 90 cm | 490", 'description' => 'One per line, as: Size label | Price. In WooCommerce mode the price is read live from the matching variation, so the typed price is only a fallback (number only, no currency).', 'label_block' => true ) );

		$this->add_control(
			'fabrics',
			array(
				'label' => 'Fabrics', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'name' => 'Premium Satin', 'description' => 'Soft, fluid and easy to style. Our most accessible option.', 'sizes' => "70 x 70 cm | 390\n90 x 90 cm | 490" ),
					array( 'name' => '100% Pure Silk', 'description' => 'Natural silk with a refined drape and hand-rolled edges.', 'sizes' => "65 x 65 cm | 690\n85 x 85 cm | 890" ),
				),
				'title_field' => '{{{ name }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Order & WhatsApp
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_order',
			array( 'label' => 'Order & Checkout', 'tab' => Controls_Manager::TAB_CONTENT )
		);

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
				'description' => 'Needs lk-commerce-hooks.php loaded from functions.php. Any option/size that has no matching WooCommerce variation falls back to WhatsApp — editors see which ones underneath the button.',
			)
		);
		$this->add_control(
			'wc_product_id',
			array(
				'label'       => 'WooCommerce product ID (variable product)',
				'type'        => Controls_Manager::NUMBER,
				'condition'   => array( 'checkout_mode' => 'woocommerce' ),
				'description' => 'Products → hover the product → ID. The fabric names and size labels below must match that product\'s attribute values (spacing and capitals don\'t matter).',
			)
		);
		$this->add_control(
			'wc_destination',
			array(
				'label'     => 'After adding, send the customer to',
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cart',
				'options'   => array( 'cart' => 'Cart', 'checkout' => 'Checkout' ),
				'condition' => array( 'checkout_mode' => 'woocommerce' ),
			)
		);

		$this->add_control( 'button_text', array( 'label' => 'Button text (add to cart)', 'type' => Controls_Manager::TEXT, 'default' => 'Add to Cart', 'label_block' => true ) );
		$this->add_control( 'button_text_enquiry', array( 'label' => 'Button text when falling back to WhatsApp', 'type' => Controls_Manager::TEXT, 'default' => 'Enquire on WhatsApp', 'label_block' => true ) );
		$this->add_control( 'currency', array( 'label' => 'Currency label', 'type' => Controls_Manager::TEXT, 'default' => 'AED' ) );
		$this->add_control( 'whatsapp_number', array( 'label' => 'WhatsApp number (with country code, no + or spaces)', 'type' => Controls_Manager::TEXT, 'default' => '971589610166' ) );
		$this->add_control(
			'message_template',
			array(
				'label'       => 'WhatsApp message template',
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => 'Hello Lila Kora, I would like the {fabric} {size} Canvas-to-Scarf Experience Box.',
				'description' => 'Use {fabric} and {size} — they\'re replaced with the current selection.',
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Image
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_image',
			array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image', 'default' => 'large' ) );
		$this->add_control( 'image_caption', array( 'label' => 'Caption', 'type' => Controls_Manager::TEXT, 'default' => 'Everything needed to create, beautifully prepared in one box.', 'label_block' => true ) );
		$this->add_control( 'image_position', array( 'label' => 'Image position', 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => array( 'left' => 'Left', 'right' => 'Right' ), 'prefix_class' => 'lk-boxprod-img-' ) );
		$this->add_control( 'image_sticky', array( 'label' => 'Image sticks while scrolling', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => 'yes' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#FFFEFD', 'selectors' => array( '{{WRAPPER}} .lk-boxprod' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-boxprod' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 80, 'right' => 24, 'bottom' => 80, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-boxprod' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'columns_gap', array( 'label' => 'Gap', 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', 'vw' ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 200 ), 'vw' => array( 'min' => 0, 'max' => 15 ) ), 'default' => array( 'unit' => 'vw', 'size' => 6 ), 'selectors' => array( '{{WRAPPER}} .lk-boxprod' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-boxprod-heading',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 60 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 38 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );
		$this->add_control( 'price_color', array( 'label' => 'Price colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-price' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'price_typography', 'selector' => '{{WRAPPER}} .lk-boxprod-price', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 36 ) ) ) ) );
		$this->add_control( 'desc_color', array( 'label' => 'Description colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-desc' => 'color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Choices
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_choices',
			array( 'label' => 'Fabric & Size Buttons', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'legend_color', array( 'label' => 'Group label colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-legend' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'legend_typography', 'selector' => '{{WRAPPER}} .lk-boxprod-legend',
			'fields_options' => array(
				'font_family'    => array( 'default' => 'Montserrat' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 10.6 ) ),
				'font_weight'    => array( 'default' => '600' ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.2 ) ),
				'text_transform' => array( 'default' => 'uppercase' ),
			),
		) );

		$this->add_control( 'heading_choice_state', array( 'label' => 'Button Colours', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->start_controls_tabs( 'tabs_choice' );
		$this->start_controls_tab( 'tab_choice_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'choice_border', array( 'label' => 'Border colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.16)', 'selectors' => array( '{{WRAPPER}} .lk-choice' => 'border-color: {{VALUE}} !important;' ) ) );
		$this->add_control( 'choice_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#281D21', 'selectors' => array( '{{WRAPPER}} .lk-choice' => 'color: {{VALUE}} !important;' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_choice_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'choice_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'description' => 'Explicitly locked to transparent — otherwise the theme\'s own default button-hover colour (often blue) shows through on a real <button> element.', 'selectors' => array( '{{WRAPPER}} .lk-choice:not(.is-selected):hover' => 'background-color: {{VALUE}} !important;' ) ) );
		$this->add_control( 'choice_border_hover', array( 'label' => 'Border colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'description' => 'Only applies to a button that isn\'t already selected.', 'selectors' => array( '{{WRAPPER}} .lk-choice:not(.is-selected):hover' => 'border-color: {{VALUE}} !important;' ) ) );
		$this->add_control( 'choice_color_hover', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#281D21', 'selectors' => array( '{{WRAPPER}} .lk-choice:not(.is-selected):hover' => 'color: {{VALUE}} !important;' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_choice_selected', array( 'label' => 'Selected' ) );
		$this->add_control( 'choice_bg_selected', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-choice.is-selected' => 'background-color: {{VALUE}} !important; border-color: {{VALUE}} !important;' ) ) );
		$this->add_control( 'choice_color_selected', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-choice.is-selected' => 'color: {{VALUE}} !important;' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'heading_choice_padding', array( 'label' => 'Button Padding', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_responsive_control( 'choice_padding', array(
			'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px' ),
			'default' => array( 'top' => 15, 'right' => 21, 'bottom' => 15, 'left' => 21, 'unit' => 'px' ),
			'selectors' => array( '{{WRAPPER}} .lk-choice' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_control( 'heading_fabric_text', array( 'label' => 'Fabric Button Text', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'fabric_name_typography', 'label' => 'Fabric name', 'selector' => '{{WRAPPER}} .lk-choice-fabric strong',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ), 'font_weight' => array( 'default' => '500' ) ),
		) );
		$this->add_control( 'fabric_desc_color', array( 'label' => 'Fabric description colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'selectors' => array( '{{WRAPPER}} .lk-choice-fabric span' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'fabric_desc_typography', 'label' => 'Fabric description', 'selector' => '{{WRAPPER}} .lk-choice-fabric span',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11.7 ) ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.65 ) ) ),
		) );
		$this->add_control( 'fabric_desc_color_selected', array( 'label' => 'Fabric description colour (selected)', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.7)', 'selectors' => array( '{{WRAPPER}} .lk-choice-fabric.is-selected span' => 'color: {{VALUE}};' ) ) );

		$this->add_control( 'heading_size_text', array( 'label' => 'Size Button Text', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'size_typography', 'selector' => '{{WRAPPER}} .lk-choice-size',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 13 ) ) ),
		) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Order Button & Summary
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_order',
			array( 'label' => 'Order Button & Summary', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'order_btn_typography', 'selector' => '{{WRAPPER}} .lk-boxprod-order',
			'fields_options' => array(
				'font_family'    => array( 'default' => 'Montserrat' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 10.9 ) ),
				'font_weight'    => array( 'default' => '600' ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.17 ) ),
				'text_transform' => array( 'default' => 'uppercase' ),
			),
		) );
		$this->start_controls_tabs( 'tabs_order_btn' );
		$this->start_controls_tab( 'tab_order_btn_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'order_btn_bg', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-order' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_control( 'order_btn_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-order' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_order_btn_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'order_btn_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-order:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'order_btn_color_hover', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-order:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'summary_color', array( 'label' => 'Summary text colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-summary' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'summary_typography', 'selector' => '{{WRAPPER}} .lk-boxprod-summary', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 12.2 ) ) ) ) );

		$this->add_control( 'delivery_color', array( 'label' => 'Delivery note colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-delivery' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'delivery_typography', 'selector' => '{{WRAPPER}} .lk-boxprod-delivery', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11.5 ) ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.7 ) ) ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Image
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_image',
			array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'image_radius', array( 'label' => 'Corner radius', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-boxprod-image img' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'caption_color', array( 'label' => 'Caption colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxprod-image figcaption' => 'color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Details Accordion
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_accordion',
			array( 'label' => 'Details Accordion', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_accordion' => 'yes' ) )
		);

		$this->add_responsive_control( 'accordion_spacing', array( 'label' => 'Spacing above', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 80 ) ), 'default' => array( 'unit' => 'px', 'size' => 34 ), 'selectors' => array( '{{WRAPPER}} .lk-details-accordion' => 'margin-top: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'accordion_border_color', array( 'label' => 'Divider colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.16)', 'selectors' => array( '{{WRAPPER}} .lk-details-accordion' => 'border-color: {{VALUE}};', '{{WRAPPER}} .lk-details-item' => 'border-color: {{VALUE}};' ) ) );

		$this->add_control( 'accordion_label_color', array( 'label' => 'Label colour', 'type' => Controls_Manager::COLOR, 'default' => '#281D21', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-details-item button' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'accordion_label_typography', 'selector' => '{{WRAPPER}} .lk-details-item button span:first-child',
			'fields_options' => array(
				'font_family'    => array( 'default' => 'Montserrat' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 13.5 ) ),
				'font_weight'    => array( 'default' => '600' ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.04 ) ),
			),
		) );
		$this->add_control( 'accordion_icon_color', array( 'label' => '"+" icon colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-details-plus' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'accordion_content_color', array( 'label' => 'Content colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-details-content' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'accordion_content_typography', 'selector' => '{{WRAPPER}} .lk-details-content', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 13.5 ) ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.7 ) ) ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$currency = $s['currency'];
		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		$widget_id = $this->get_id();
		$hooks_ok  = function_exists( 'lk_wc_buy_url' );
		$mode      = ( 'woocommerce' === $s['checkout_mode'] && $hooks_ok && ! empty( $s['wc_product_id'] ) ) ? 'woocommerce' : 'whatsapp';
		$wc        = ( 'woocommerce' === $mode ) ? array( 'product_id' => absint( $s['wc_product_id'] ), 'dest' => $s['wc_destination'] ) : null;
		$missing   = array();
		?>
		<div class="lk-boxprod" data-lk-box-product data-currency="<?php echo esc_attr( $currency ); ?>" data-template="<?php echo esc_attr( $s['message_template'] ); ?>" data-whatsapp="<?php echo esc_attr( $s['whatsapp_number'] ); ?>">
			<figure class="lk-boxprod-image<?php echo 'yes' === $s['image_sticky'] ? ' lk-boxprod-sticky' : ''; ?>">
				<?php echo $image_html; ?>
				<?php if ( ! empty( $s['image_caption'] ) ) : ?><figcaption><?php echo esc_html( $s['image_caption'] ); ?></figcaption><?php endif; ?>
			</figure>

			<div class="lk-boxprod-details">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="lk-boxprod-heading"><?php echo esc_html( $s['heading'] ); ?></h2>
				<p class="lk-boxprod-price" data-lk-price></p>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-boxprod-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

				<fieldset class="lk-boxprod-group">
					<legend class="lk-boxprod-legend">Fabric</legend>
					<div class="lk-boxprod-fabrics" data-lk-fabrics>
						<?php foreach ( $s['fabrics'] as $index => $fabric ) :
							$sizes = $this->parse_sizes( $fabric['sizes'], $fabric['name'], $wc, $missing );
							?>
							<button type="button" class="lk-choice lk-choice-fabric<?php echo 0 === $index ? ' is-selected' : ''; ?>" data-fabric-name="<?php echo esc_attr( $fabric['name'] ); ?>" data-sizes="<?php echo esc_attr( wp_json_encode( $sizes ) ); ?>" aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>">
								<strong><?php echo esc_html( $fabric['name'] ); ?></strong>
								<span><?php echo esc_html( $fabric['description'] ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>
				</fieldset>

				<fieldset class="lk-boxprod-group">
					<legend class="lk-boxprod-legend">Size</legend>
					<div class="lk-boxprod-sizes" data-lk-sizes></div>
				</fieldset>

				<a class="lk-boxprod-order" data-lk-order data-label-cart="<?php echo esc_attr( $s['button_text'] ); ?>" data-label-enquiry="<?php echo esc_attr( $s['button_text_enquiry'] ); ?>" href="#" target="_blank" rel="noopener"><?php echo esc_html( 'woocommerce' === $mode ? $s['button_text'] : $s['button_text_enquiry'] ); ?></a>
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<?php if ( 'woocommerce' === $s['checkout_mode'] && ! $hooks_ok ) : ?>
						<p class="lk-boxprod-notice">Cart mode is on, but lk-commerce-hooks.php isn't loaded — add its require_once line to functions.php. Falling back to WhatsApp. (Only visible to editors.)</p>
					<?php elseif ( 'woocommerce' === $s['checkout_mode'] && empty( $s['wc_product_id'] ) ) : ?>
						<p class="lk-boxprod-notice">Cart mode is on, but no WooCommerce product ID is set yet — falling back to WhatsApp. (Only visible to editors.)</p>
					<?php elseif ( ! empty( $missing ) ) : ?>
						<p class="lk-boxprod-notice">No matching WooCommerce variation for: <?php echo esc_html( implode( ', ', $missing ) ); ?>. Those sizes fall back to WhatsApp. Check the names match the product's attribute values. (Only visible to editors.)</p>
					<?php endif; ?>
				<?php endif; ?>
				<p class="lk-boxprod-summary" data-lk-summary></p>
				<?php if ( ! empty( $s['delivery_note'] ) ) : ?><p class="lk-boxprod-delivery"><?php echo esc_html( $s['delivery_note'] ); ?></p><?php endif; ?>

				<?php if ( 'yes' === $s['show_accordion'] && ! empty( $s['accordion_rows'] ) ) :
					$widget_id = $this->get_id();
					?>
					<div class="lk-details-accordion">
						<?php foreach ( $s['accordion_rows'] as $index => $row ) :
							$is_open = 'yes' === $row['open_by_default'];
							?>
							<article class="lk-details-item">
								<button type="button" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="lk-details-content-<?php echo esc_attr( $widget_id . '-' . $index ); ?>">
									<span><?php echo esc_html( $row['label'] ); ?></span>
									<span class="lk-details-plus" aria-hidden="true">+</span>
								</button>
								<div class="lk-details-content" id="lk-details-content-<?php echo esc_attr( $widget_id . '-' . $index ); ?>" <?php echo $is_open ? '' : 'hidden'; ?>>
									<p><?php echo esc_html( $row['content'] ); ?></p>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<style>
			.lk-boxprod { display: grid; grid-template-columns: 1.02fr 0.98fr; align-items: start; }
			.lk-boxprod-img-right .lk-boxprod-image { order: 2; } .lk-boxprod-img-right .lk-boxprod-details { order: 1; }
			.lk-boxprod-img-left .lk-boxprod-image { order: 1; } .lk-boxprod-img-left .lk-boxprod-details { order: 2; }
			.lk-boxprod-image { margin: 0; }
			.lk-boxprod-sticky { position: sticky; top: 110px; }
			.lk-boxprod-image img { width: 100%; aspect-ratio: 1; object-fit: cover; display: block; }
			.lk-boxprod-image figcaption { padding-top: 13px; font-style: italic; }
			.lk-boxprod-heading { margin: 0 0 20px; font-style: normal; }
			.lk-boxprod-price { margin: 0 0 24px; line-height: 1; }
			.lk-boxprod-desc { margin: 0 0 35px; max-width: 650px; }
			.lk-boxprod-group { margin: 0 0 29px; padding: 0; border: 0; }
			.lk-boxprod-legend { display: block; width: 100%; margin-bottom: 13px; }
			.lk-boxprod-fabrics { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
			.lk-boxprod-sizes { display: flex; gap: 10px; flex-wrap: wrap; }
			.lk-choice { background: transparent; border: 1px solid; cursor: pointer; text-align: left; font-family: 'Montserrat', Arial, sans-serif; -webkit-appearance: none; appearance: none; box-shadow: none; transition: color .25s ease, background .25s ease, border-color .25s ease; }
			.lk-choice-fabric { min-height: 120px; }
			.lk-choice-fabric strong, .lk-choice-fabric span { display: block; }
			.lk-choice-fabric strong { margin-bottom: 9px; font-family: 'Cormorant Garamond', serif; }
			.lk-choice-size { min-width: 150px; text-align: center; }
			.lk-boxprod-order { width: 100%; min-height: 58px; display: flex; align-items: center; justify-content: center; margin-top: 38px; text-decoration: none; border: 1px solid transparent; border-radius: 0; cursor: pointer; transition: color .25s ease, background .25s ease; }
			.lk-boxprod-summary { margin: 20px 0 4px; }
			.lk-boxprod-notice { margin: 14px 0 0; padding: 12px 16px; background: rgba(255,113,94,0.12); border: 1px dashed #FF715E; font-family: 'Montserrat', Arial, sans-serif; font-size: 12px; line-height: 1.6; color: #57282D; }
			.lk-boxprod-delivery { margin: 0; }
			.lk-details-accordion { border-top: 1px solid; }
			.lk-details-item { border-bottom: 1px solid; }
			.lk-details-item button { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 17px 0; background: transparent; border: 0; cursor: pointer; text-align: left; font-family: 'Montserrat', Arial, sans-serif; text-transform: uppercase; }
			.lk-details-plus { flex-shrink: 0; font-size: 18px; font-weight: 300; transition: transform .3s ease; }
			.lk-details-item button[aria-expanded="true"] .lk-details-plus { transform: rotate(45deg); }
			.lk-details-content { padding: 0 0 20px; }
			.lk-details-content p { margin: 0; }
			.lk-details-content[hidden] { display: none; }
			@media (max-width: 820px) {
				.lk-boxprod { grid-template-columns: 1fr !important; }
				.lk-boxprod-sticky { position: relative; top: auto; }
			}
			@media (max-width: 540px) {
				.lk-boxprod-fabrics { grid-template-columns: 1fr; }
				.lk-choice-fabric { min-height: auto; }
				.lk-choice-size { flex: 1; min-width: 130px; }
			}
		</style>

		<script>
			( function () {
				function initBoxProduct( root ) {
					var currency   = root.getAttribute( 'data-currency' ) || '';
					var template   = root.getAttribute( 'data-template' ) || '';
					var whatsapp   = root.getAttribute( 'data-whatsapp' ) || '';
					var fabricWrap = root.querySelector( '[data-lk-fabrics]' );
					var sizeWrap   = root.querySelector( '[data-lk-sizes]' );
					var priceEl    = root.querySelector( '[data-lk-price]' );
					var summaryEl  = root.querySelector( '[data-lk-summary]' );
					var orderEl    = root.querySelector( '[data-lk-order]' );

					function currentFabricButton() {
						return fabricWrap.querySelector( '.is-selected' );
					}

					function renderSizes() {
						var fabricBtn = currentFabricButton();
						if ( ! fabricBtn ) { return; }
						var sizes = [];
						try { sizes = JSON.parse( fabricBtn.getAttribute( 'data-sizes' ) || '[]' ); } catch ( e ) { sizes = []; }
						sizeWrap.innerHTML = '';
						sizes.forEach( function ( size, index ) {
							var btn = document.createElement( 'button' );
							btn.type = 'button';
							btn.className = 'lk-choice lk-choice-size' + ( 0 === index ? ' is-selected' : '' );
							btn.setAttribute( 'aria-pressed', 0 === index ? 'true' : 'false' );
							btn.setAttribute( 'data-price', size.price );
							btn.setAttribute( 'data-buy', size.buy || '' );
							btn.textContent = size.label;
							sizeWrap.appendChild( btn );
						} );
						update();
					}

					function update() {
						var fabricBtn = currentFabricButton();
						var sizeBtn   = sizeWrap.querySelector( '.is-selected' );
						if ( ! fabricBtn || ! sizeBtn ) { return; }
						var fabricName = fabricBtn.getAttribute( 'data-fabric-name' );
						var sizeLabel  = sizeBtn.textContent;
						var price      = sizeBtn.getAttribute( 'data-price' );

						if ( priceEl ) { priceEl.textContent = currency + ' ' + price; }
						if ( summaryEl ) { summaryEl.textContent = fabricName + ' · ' + sizeLabel + ' · ' + currency + ' ' + price; }
						if ( orderEl ) {
							var buyUrl = sizeBtn.getAttribute( 'data-buy' ) || '';
							if ( buyUrl ) {
								orderEl.href = buyUrl;
								orderEl.removeAttribute( 'target' );
								orderEl.removeAttribute( 'rel' );
								orderEl.textContent = orderEl.getAttribute( 'data-label-cart' );
							} else {
								var message = template.replace( '{fabric}', fabricName ).replace( '{size}', sizeLabel );
								orderEl.href = 'https://wa.me/' + whatsapp + '?text=' + encodeURIComponent( message );
								orderEl.setAttribute( 'target', '_blank' );
								orderEl.setAttribute( 'rel', 'noopener' );
								orderEl.textContent = orderEl.getAttribute( 'data-label-enquiry' );
							}
						}
					}

					fabricWrap.addEventListener( 'click', function ( event ) {
						var btn = event.target.closest( '.lk-choice-fabric' );
						if ( ! btn ) { return; }
						fabricWrap.querySelectorAll( '.lk-choice-fabric' ).forEach( function ( b ) {
							var active = b === btn;
							b.classList.toggle( 'is-selected', active );
							b.setAttribute( 'aria-pressed', String( active ) );
						} );
						renderSizes();
					} );

					sizeWrap.addEventListener( 'click', function ( event ) {
						var btn = event.target.closest( '.lk-choice-size' );
						if ( ! btn ) { return; }
						sizeWrap.querySelectorAll( '.lk-choice-size' ).forEach( function ( b ) {
							var active = b === btn;
							b.classList.toggle( 'is-selected', active );
							b.setAttribute( 'aria-pressed', String( active ) );
						} );
						update();
					} );

					renderSizes();
				}

				document.querySelectorAll( '[data-lk-box-product]:not([data-lk-bound])' ).forEach( function ( root ) {
					root.setAttribute( 'data-lk-bound', 'true' );

					// Details accordion — shared markup/behaviour with the
					// standalone LK — Details Accordion widget, so both work
					// together correctly if both appear on one page.
					if ( ! window.__lkDetailsAccordionBound ) {
						window.__lkDetailsAccordionBound = true;
						document.addEventListener( 'click', function ( event ) {
							var button = event.target.closest( '.lk-details-item button' );
							if ( ! button ) { return; }
							var expanded = button.getAttribute( 'aria-expanded' ) === 'true';
							var content = document.getElementById( button.getAttribute( 'aria-controls' ) );
							button.setAttribute( 'aria-expanded', String( ! expanded ) );
							if ( content ) { content.hidden = expanded; }
						} );
					}

					initBoxProduct( root );
				} );
			} )();
		</script>
		<?php
	}
}
