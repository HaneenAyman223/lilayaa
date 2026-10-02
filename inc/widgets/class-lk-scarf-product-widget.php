<?php
/**
 * Lila Kora — Scarf Product Widget
 *
 * Main image + thumbnails gallery.
 * Integrates the design/size configurator, live price, dynamic WhatsApp button,
 * and an accordion for shipping/delivery/materials details.
 * Features design-to-image linking and original LK burgundy styling.
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

class LK_Scarf_Product_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-scarf-product';
	}

	public function get_title() {
		return 'LK — Scarf Product';
	}

	public function get_icon() {
		return 'eicon-product-images';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section( 'section_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Ready-to-wear · Inception Collection', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Pure Silk', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading (Italic)', 'type' => Controls_Manager::TEXT, 'default' => 'Scarf', 'label_block' => true ) );
		$this->add_control( 'status', array( 'label' => 'Status / Availability', 'type' => Controls_Manager::TEXT, 'default' => 'Available by private order', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Choose from four original designs created by Lila Kora. Each scarf is crafted in 100% pure silk, finished with hand-rolled edges and presented in a Lila Kora gift box.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Gallery
		 * =======================================================*/
		$this->start_controls_section( 'section_gallery', array( 'label' => 'Gallery Images', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'gallery', array( 'label' => 'Add Images', 'type' => Controls_Manager::GALLERY, 'default' => array() ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'gallery', 'default' => 'large', 'label' => 'Image Size' ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Configurator Options
		 * =======================================================*/
		$this->start_controls_section( 'section_options', array( 'label' => 'Configurator Options', 'tab' => Controls_Manager::TAB_CONTENT ) );

		// Designs Repeater
		$this->add_control( 'heading_designs', array( 'label' => 'Designs', 'type' => Controls_Manager::HEADING ) );
		$design_repeater = new Repeater();
		$design_repeater->add_control( 'label', array( 'label' => 'Design Name', 'type' => Controls_Manager::TEXT, 'default' => 'Design 01', 'label_block' => true ) );
		$design_repeater->add_control( 'swatch', array( 'label' => 'Swatch CSS (Color/Gradient)', 'type' => Controls_Manager::TEXT, 'default' => 'linear-gradient(135deg, #7a203b, #ef7890)', 'label_block' => true, 'description' => 'Used only when no swatch image is set below.' ) );
		$design_repeater->add_control( 'swatch_image', array( 'label' => 'Swatch image (optional)', 'type' => Controls_Manager::MEDIA, 'description' => 'A real photo/scan of the design. Overrides the CSS gradient above when set.' ) );
		$design_repeater->add_control( 'image', array( 'label' => 'Link to Image', 'type' => Controls_Manager::MEDIA, 'description' => 'If added, clicking this design will change the main product image to this image.' ) );
		
		$this->add_control(
			'designs',
			array(
				'label' => 'Designs', 'type' => Controls_Manager::REPEATER, 'fields' => $design_repeater->get_controls(),
				'default' => array(
					array( 'label' => 'Design 01', 'swatch' => 'linear-gradient(135deg, #7a203b 0 30%, #ef7890 30% 55%, #f4c7a0 55% 75%, #58122b 75%)' ),
					array( 'label' => 'Design 02', 'swatch' => 'linear-gradient(135deg, #692137 0 35%, #d14a62 35% 60%, #f5d4cb 60% 78%, #8e334b 78%)' ),
					array( 'label' => 'Design 03', 'swatch' => 'linear-gradient(135deg, #3f1830 0 30%, #b35a71 30% 55%, #eed8b7 55% 75%, #8a1f39 75%)' ),
					array( 'label' => 'Design 04', 'swatch' => 'linear-gradient(135deg, #85233f 0 28%, #ff715e 28% 52%, #f8ece9 52% 76%, #4d1426 76%)' ),
				),
				'title_field' => '{{{ label }}}',
			)
		);

		// Sizes Repeater
		$this->add_control( 'heading_sizes', array( 'label' => 'Sizes & Prices', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$size_repeater = new Repeater();
		$size_repeater->add_control( 'label', array( 'label' => 'Size Name', 'type' => Controls_Manager::TEXT, 'default' => '65 x 65 cm', 'label_block' => true ) );
		$size_repeater->add_control( 'price', array( 'label' => 'Price (Value Only)', 'type' => Controls_Manager::NUMBER, 'default' => 690 ) );
		$this->add_control(
			'sizes',
			array(
				'label' => 'Sizes', 'type' => Controls_Manager::REPEATER, 'fields' => $size_repeater->get_controls(),
				'default' => array(
					array( 'label' => '65 x 65 cm', 'price' => 690 ),
					array( 'label' => '85 x 85 cm', 'price' => 890 ),
				),
				'title_field' => '{{{ label }}} - AED {{{ price }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Action & Accordion
		 * =======================================================*/
		$this->start_controls_section( 'section_accordion', array( 'label' => 'Order & Accordion', 'tab' => Controls_Manager::TAB_CONTENT ) );

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
				'description' => 'Needs lk-commerce-hooks.php loaded from functions.php. Any design/size combination with no matching WooCommerce variation falls back to WhatsApp — editors see which ones underneath the button.',
			)
		);
		$this->add_control(
			'wc_product_id',
			array(
				'label'       => 'WooCommerce product ID (variable product)',
				'type'        => Controls_Manager::NUMBER,
				'condition'   => array( 'checkout_mode' => 'woocommerce' ),
				'description' => 'Products → hover the product → ID. The design names and size labels above must match the product\'s "Design" and "Size" attribute values (spacing and capitals don\'t matter).',
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
		$this->add_control( 'button_text', array( 'label' => 'Button Text (add to cart)', 'type' => Controls_Manager::TEXT, 'default' => 'Add to Cart' ) );
		$this->add_control( 'button_text_enquiry', array( 'label' => 'Button Text when falling back to WhatsApp', 'type' => Controls_Manager::TEXT, 'default' => 'Request This Scarf' ) );
		$this->add_control( 'currency', array( 'label' => 'Currency', 'type' => Controls_Manager::TEXT, 'default' => 'AED' ) );
		$this->add_control( 'whatsapp_number', array( 'label' => 'WhatsApp Number', 'type' => Controls_Manager::TEXT, 'default' => '971589610166' ) );
		$this->add_control( 'message_template', array( 'label' => 'WhatsApp Template', 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => 'Hello Lila Kora, I would like to order the Inception pure silk scarf in {design}, size {size}. Please share the available design images and price.', 'description' => 'Use {design} and {size} tags. They will be dynamically replaced.' ) );

		$this->add_control( 'heading_accordion', array( 'label' => 'Accordion Details', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		
		$acc_repeater = new Repeater();
		$acc_repeater->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Material & Care', 'label_block' => true ) );
		$acc_repeater->add_control( 'content', array( 'label' => 'Content', 'type' => Controls_Manager::WYSIWYG, 'default' => '100% pure silk with hand-rolled edges. Dry clean only.' ) );
		
		$this->add_control(
			'accordion',
			array(
				'label' => 'Dropdown Items', 'type' => Controls_Manager::REPEATER, 'fields' => $acc_repeater->get_controls(),
				'default' => array(
					array( 'title' => 'Material & Care', 'content' => '<p>Crafted in 100% pure silk and finished with hand-rolled edges. We recommend professional dry cleaning only to maintain the vibrant colors and silk texture.</p>' ),
					array( 'title' => 'Shipping & Delivery', 'content' => '<p>Complimentary delivery within the UAE takes 2-3 business days. International shipping is available and calculated at checkout based on destination.</p>' ),
					array( 'title' => 'Presentation', 'content' => '<p>Every scarf arrives beautifully enclosed in our signature Lila Kora gift box, perfect for personal keeping or meaningful gifting.</p>' ),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text & General
		 * =======================================================*/
		$this->start_controls_section( 'section_style_text', array( 'label' => 'Typography & Colors', 'tab' => Controls_Manager::TAB_STYLE ) );
		
		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#FFFEFD', 'selectors' => array( '{{WRAPPER}} .lk-scarf-page' => 'background-color: {{VALUE}};' ) ) );

		$this->add_control( 'heading_typo_title', array( 'label' => 'Heading', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_control( 'color_title', array( 'label' => 'Heading Color', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-title' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typo_title', 'selector' => '{{WRAPPER}} .lk-scarf-title' ) );

		$this->add_control( 'heading_typo_eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_control( 'color_eyebrow', array( 'label' => 'Eyebrow Color', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typo_eyebrow', 'selector' => '{{WRAPPER}} .lk-eyebrow' ) );

		$this->add_control( 'heading_typo_status', array( 'label' => 'Status', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_control( 'color_status', array( 'label' => 'Status Color', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-status' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typo_status', 'selector' => '{{WRAPPER}} .lk-scarf-status' ) );

		$this->add_control( 'heading_typo_desc', array( 'label' => 'Description', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_control( 'color_desc', array( 'label' => 'Description Color', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'selectors' => array( '{{WRAPPER}} .lk-scarf-desc' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typo_desc', 'selector' => '{{WRAPPER}} .lk-scarf-desc' ) );

		$this->add_control( 'heading_typo_price', array( 'label' => 'Price & Summary', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_control( 'color_price', array( 'label' => 'Price Color', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-price' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typo_price', 'selector' => '{{WRAPPER}} .lk-scarf-price' ) );
		$this->add_control( 'color_summary', array( 'label' => 'Summary Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'selectors' => array( '{{WRAPPER}} .lk-scarf-summary' => 'color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Option Buttons
		 * =======================================================*/
		$this->start_controls_section( 'section_style_options', array( 'label' => 'Configurator Options', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->add_control( 'color_labels', array( 'label' => 'Group Labels (e.g. SIZE)', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-group legend' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typo_choices', 'label' => 'Option Button Typography', 'selector' => '{{WRAPPER}} .lk-scarf-choice' ) );

		$this->start_controls_tabs( 'tabs_options' );

		// Normal Options
		$this->start_controls_tab( 'tab_opt_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'opt_bg_normal', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'selectors' => array( '{{WRAPPER}} .lk-scarf-choice' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'opt_text_normal', array( 'label' => 'Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#281D21', 'selectors' => array( '{{WRAPPER}} .lk-scarf-choice' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'opt_border_normal', array( 'label' => 'Border Color', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.16)', 'selectors' => array( '{{WRAPPER}} .lk-scarf-choice' => 'border-color: {{VALUE}};' ) ) );
		$this->end_controls_tab();

		// Hover Options
		$this->start_controls_tab( 'tab_opt_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'opt_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'selectors' => array( '{{WRAPPER}} .lk-scarf-choice:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'opt_text_hover', array( 'label' => 'Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#281D21', 'selectors' => array( '{{WRAPPER}} .lk-scarf-choice:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'opt_border_hover', array( 'label' => 'Border Color', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-choice:hover' => 'border-color: {{VALUE}};' ) ) );
		$this->end_controls_tab();

		// Selected Options
		$this->start_controls_tab( 'tab_opt_selected', array( 'label' => 'Selected' ) );
		$this->add_control( 'opt_bg_selected', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-choice.is-selected' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'opt_text_selected', array( 'label' => 'Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-scarf-choice.is-selected' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'opt_border_selected', array( 'label' => 'Border Color', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-choice.is-selected' => 'border-color: {{VALUE}};' ) ) );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Action Button
		 * =======================================================*/
		$this->start_controls_section( 'section_style_button', array( 'label' => 'Action Button', 'tab' => Controls_Manager::TAB_STYLE ) );
		
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typo_btn', 'selector' => '{{WRAPPER}} .lk-scarf-btn' ) );

		$this->start_controls_tabs( 'tabs_btn' );

		// Normal Button
		$this->start_controls_tab( 'tab_btn_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'btn_bg_normal', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-btn' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_text_normal', array( 'label' => 'Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-scarf-btn' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_border_normal', array( 'label' => 'Border Color', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-btn' => 'border-color: {{VALUE}};' ) ) );
		$this->end_controls_tab();

		// Hover Button
		$this->start_controls_tab( 'tab_btn_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'btn_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'selectors' => array( '{{WRAPPER}} .lk-scarf-btn:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_text_hover', array( 'label' => 'Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-btn:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_border_hover', array( 'label' => 'Border Color', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-scarf-btn:hover' => 'border-color: {{VALUE}};' ) ) );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Accordion
		 * =======================================================*/
		$this->start_controls_section( 'section_style_accordion', array( 'label' => 'Accordion', 'tab' => Controls_Manager::TAB_STYLE ) );
		
		$this->add_control( 'acc_border', array( 'label' => 'Divider Color', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.16)', 'selectors' => array( '{{WRAPPER}} .lk-scarf-accordion, {{WRAPPER}} .lk-acc-item' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'acc_title_color', array( 'label' => 'Title Color', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-acc-title' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typo_acc_title', 'selector' => '{{WRAPPER}} .lk-acc-title' ) );
		$this->add_control( 'acc_icon_color', array( 'label' => 'Icon (+) Color', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-plus' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'acc_content_color', array( 'label' => 'Content Text Color', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'selectors' => array( '{{WRAPPER}} .lk-acc-content' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typo_acc_content', 'selector' => '{{WRAPPER}} .lk-acc-content' ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$id = $this->get_id();
		$currency = $s['currency'];

		$hooks_ok = function_exists( 'lk_wc_buy_url' );
		$wc_on    = ( 'woocommerce' === $s['checkout_mode'] && $hooks_ok && ! empty( $s['wc_product_id'] ) );
		$buy_map  = array();
		$missing  = array();
		if ( $wc_on ) {
			foreach ( $s['designs'] as $d ) {
				foreach ( $s['sizes'] as $sz ) {
					$hit = lk_wc_buy_url( $s['wc_product_id'], array( $d['label'], $sz['label'] ), $s['wc_destination'] );
					if ( $hit ) {
						$buy_map[ $d['label'] . '||' . $sz['label'] ] = array( 'url' => $hit['url'], 'price' => $hit['price'] );
					} else {
						$missing[] = $d['label'] . ' / ' . $sz['label'];
					}
				}
			}
		}
		?>
		<div class="lk-scarf-page">
			<!-- GALLERY -->
			<div class="lk-scarf-gallery" id="lk-gallery-<?php echo esc_attr( $id ); ?>">
				<?php if ( ! empty( $s['gallery'] ) ) : 
					$first_img = wp_get_attachment_image_url( $s['gallery'][0]['id'], $s['gallery_size'] );
				?>
					<figure class="lk-scarf-gallery-main">
						<img src="<?php echo esc_url( $first_img ); ?>" alt="">
					</figure>
					<div class="lk-scarf-gallery-thumbs">
						<?php foreach ( $s['gallery'] as $index => $img ) : 
							$thumb_url = wp_get_attachment_image_url( $img['id'], 'medium' );
							$full_url  = wp_get_attachment_image_url( $img['id'], $s['gallery_size'] );
						?>
							<div class="lk-scarf-thumb <?php echo $index === 0 ? 'is-active' : ''; ?>" data-full="<?php echo esc_url( $full_url ); ?>">
								<img src="<?php echo esc_url( $thumb_url ); ?>" alt="">
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- DETAILS & CONFIGURATOR -->
			<div class="lk-scarf-panel" id="lk-config-<?php echo esc_attr( $id ); ?>" data-template="<?php echo esc_attr( $s['message_template'] ); ?>" data-whatsapp="<?php echo esc_attr( $s['whatsapp_number'] ); ?>" data-currency="<?php echo esc_attr( $currency ); ?>" data-buy="<?php echo esc_attr( wp_json_encode( (object) $buy_map ) ); ?>">
				
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h1 class="lk-scarf-title"><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h1>
				<?php if ( ! empty( $s['status'] ) ) : ?><p class="lk-scarf-status"><?php echo esc_html( $s['status'] ); ?></p><?php endif; ?>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-scarf-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

				<!-- Options -->
				<fieldset class="lk-scarf-group">
					<legend>Design</legend>
					<div class="lk-scarf-choice-grid lk-scarf-designs">
						<?php foreach ( $s['designs'] as $i => $d ) : 
							$design_image_url = !empty($d['image']['url']) ? wp_get_attachment_image_url($d['image']['id'], $s['gallery_size']) : '';
						$swatch_image_url = !empty($d['swatch_image']['url']) ? wp_get_attachment_image_url($d['swatch_image']['id'], 'thumbnail') : '';
						?>
							<button type="button" class="lk-scarf-choice <?php echo $i === 0 ? 'is-selected' : ''; ?>" data-type="design" data-value="<?php echo esc_attr( $d['label'] ); ?>" data-image="<?php echo esc_attr($design_image_url); ?>">
								<i class="lk-swatch" style="<?php echo $swatch_image_url ? 'background-image: url(' . esc_url( $swatch_image_url ) . ');' : 'background: ' . esc_attr( $d['swatch'] ) . ';'; ?>"></i>
								<span><?php echo esc_html( $d['label'] ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>
				</fieldset>

				<fieldset class="lk-scarf-group">
					<legend>Size</legend>
					<div class="lk-scarf-choice-grid lk-scarf-sizes">
						<?php foreach ( $s['sizes'] as $i => $sz ) : ?>
							<button type="button" class="lk-scarf-choice <?php echo $i === 0 ? 'is-selected' : ''; ?>" data-type="size" data-label="<?php echo esc_attr( $sz['label'] ); ?>" data-price="<?php echo esc_attr( $sz['price'] ); ?>">
								<?php echo esc_html( $sz['label'] ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				</fieldset>

				<!-- Action -->
				<div class="lk-scarf-price-wrap">
					<p class="lk-scarf-price" id="lk-price-<?php echo esc_attr( $id ); ?>"></p>
				</div>
				<a class="lk-scarf-btn" id="lk-order-<?php echo esc_attr( $id ); ?>" data-label-cart="<?php echo esc_attr( $s['button_text'] ); ?>" data-label-enquiry="<?php echo esc_attr( $s['button_text_enquiry'] ); ?>" href="#" target="_blank" rel="noopener"><?php echo esc_html( $wc_on ? $s['button_text'] : $s['button_text_enquiry'] ); ?></a>
				<p class="lk-scarf-summary" id="lk-summary-<?php echo esc_attr( $id ); ?>"></p>
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<?php if ( 'woocommerce' === $s['checkout_mode'] && ! $hooks_ok ) : ?>
						<p class="lk-scarf-notice">Cart mode is on, but lk-commerce-hooks.php isn't loaded — add its require_once line to functions.php. Falling back to WhatsApp. (Only visible to editors.)</p>
					<?php elseif ( 'woocommerce' === $s['checkout_mode'] && empty( $s['wc_product_id'] ) ) : ?>
						<p class="lk-scarf-notice">Cart mode is on, but no WooCommerce product ID is set yet — falling back to WhatsApp. (Only visible to editors.)</p>
					<?php elseif ( ! empty( $missing ) ) : ?>
						<p class="lk-scarf-notice">No matching WooCommerce variation for: <?php echo esc_html( implode( ', ', $missing ) ); ?>. Those combinations fall back to WhatsApp. Check the names match the product's attribute values. (Only visible to editors.)</p>
					<?php endif; ?>
				<?php endif; ?>

				<!-- Accordion Dropdowns -->
				<?php if ( ! empty( $s['accordion'] ) ) : ?>
					<div class="lk-scarf-accordion">
						<?php foreach ( $s['accordion'] as $i => $acc ) : ?>
							<article class="lk-acc-item">
								<button type="button" class="lk-acc-toggle" aria-expanded="false">
									<span class="lk-acc-title"><?php echo esc_html( $acc['title'] ); ?></span>
									<span class="lk-plus">+</span>
								</button>
								<div class="lk-acc-content" hidden>
									<?php echo wpautop( $acc['content'] ); ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<style>
			.lk-scarf-page { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: clamp(45px, 6vw, 95px); max-width: var(--lk-page-width, 1440px); margin: 0 auto; padding: 45px 4.5vw 105px; align-items: start; }
			
			/* Gallery */
			.lk-scarf-gallery { display: flex; flex-direction: column; gap: 10px; position: sticky; top: 120px; }
			.lk-scarf-gallery-main { width: 100%; aspect-ratio: 1; margin: 0; background: #f6f0ec; overflow: hidden; }
			.lk-scarf-gallery-main img { width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.5s ease; }
			.lk-scarf-gallery-thumbs { display: flex; gap: 10px; overflow-x: auto; scrollbar-width: none; }
			.lk-scarf-gallery-thumbs::-webkit-scrollbar { display: none; }
			.lk-scarf-thumb { width: 90px; height: 90px; flex-shrink: 0; cursor: pointer; opacity: 0.5; border: 2px solid transparent; transition: all 0.3s ease; }
			.lk-scarf-thumb img { width: 100%; height: 100%; object-fit: cover; }
			.lk-scarf-thumb:hover, .lk-scarf-thumb.is-active { opacity: 1; border-color: #692137; }

			/* Text Panel */
			.lk-eyebrow { font-size: 0.66rem; font-weight: 600; letter-spacing: 0.19em; text-transform: uppercase; margin-bottom: 15px; line-height: 1.5; }
			.lk-scarf-title { margin: 0 0 15px; font-family: 'Cormorant Garamond', serif; font-weight: 400; font-size: clamp(3.7rem, 5.4vw, 6.4rem); line-height: 0.98; letter-spacing: -0.025em; }
			.lk-scarf-title em { font-style: italic; }
			.lk-scarf-status { font-family: 'Cormorant Garamond', serif; font-size: 1.65rem; margin-bottom: 22px; }
			.lk-scarf-desc { font-size: 1rem; line-height: 1.8; margin-bottom: 30px; }

			/* Configurator */
			.lk-scarf-group { margin: 32px 0 0; padding: 0; border: 0; }
			.lk-scarf-group legend { width: 100%; padding-top: 18px; margin-bottom: 12px; border-top: 1px solid rgba(105,33,55,0.16); font-size: 0.75rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; }
			.lk-scarf-choice-grid { display: grid; gap: 9px; }
			.lk-scarf-designs { grid-template-columns: 1fr 1fr; }
			.lk-scarf-sizes { display: flex; flex-wrap: wrap; }
			.lk-scarf-sizes .lk-scarf-choice { min-width: 150px; flex: 1; text-align: center; justify-content: center; }
			.lk-scarf-choice { display: inline-flex; align-items: center; min-height: 58px; padding: 13px 16px; border: 1px solid; font-family: 'Montserrat', sans-serif; font-size: 0.82rem; cursor: pointer; transition: all 0.2s ease; outline: none; }
			.lk-swatch { width: 26px; height: 26px; margin-right: 12px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3); flex-shrink: 0; background-size: cover; background-position: center; }

			/* Order Button */
			.lk-scarf-price-wrap { margin-top: 35px; }
			.lk-scarf-price { font-family: 'Cormorant Garamond', serif; font-size: 2.25rem; margin: 0; line-height: 1; }
			.lk-scarf-btn { display: flex; width: 100%; min-height: 58px; align-items: center; justify-content: center; margin-top: 15px; border: 1px solid; font-family: 'Montserrat', sans-serif; font-size: 0.68rem; font-weight: 600; letter-spacing: 0.17em; text-transform: uppercase; text-decoration: none; transition: all 0.3s ease; }
			.lk-scarf-notice { margin: 0 0 25px; padding: 12px 16px; background: rgba(255,113,94,0.12); border: 1px dashed #FF715E; font-family: 'Montserrat', sans-serif; font-size: 12px; line-height: 1.6; color: #57282D; }
			.lk-scarf-summary { text-align: center; font-size: 0.8rem; margin: 13px 0 35px; }

			/* Accordion */
			.lk-scarf-accordion { border-top: 1px solid; }
			.lk-acc-item { border-bottom: 1px solid; }
			.lk-acc-toggle { width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 22px 0; background: transparent !important; border: none; outline: none; cursor: pointer; text-align: left; transition: opacity 0.3s ease; }
			.lk-acc-toggle:hover, .lk-acc-toggle:focus, .lk-acc-toggle:active { background: transparent !important; opacity: 0.8; }
			.lk-acc-title { font-family: 'Cormorant Garamond', serif; font-size: 1.45rem; transition: color 0.3s ease; }
			.lk-plus { font-size: 1.5rem; font-weight: 300; transition: transform 0.3s ease, color 0.3s ease; }
			.lk-acc-toggle[aria-expanded="true"] .lk-plus { transform: rotate(45deg); }
			.lk-acc-content { padding: 0 20px 20px 0; font-size: 0.9rem; line-height: 1.7; }
			.lk-acc-content p:last-child { margin-bottom: 0; }

			@media (max-width: 820px) {
				.lk-scarf-page { grid-template-columns: 1fr; padding: 24px 24px 80px; }
				.lk-scarf-gallery { position: relative; top: auto; }
			}
			@media (max-width: 540px) {
				.lk-scarf-title { font-size: clamp(3.6rem, 16vw, 5.5rem); }
				.lk-scarf-designs { grid-template-columns: 1fr; }
				.lk-scarf-sizes .lk-scarf-choice { min-width: 100%; }
			}
		</style>

		<script>
			(function() {
				const id = '<?php echo esc_js( $id ); ?>';
				
				// Elements
				const gallery = document.getElementById('lk-gallery-' + id);
				const configPanel = document.getElementById('lk-config-' + id);
				
				let mainImg, thumbs;
				
				if (gallery) {
					mainImg = gallery.querySelector('.lk-scarf-gallery-main img');
					thumbs = gallery.querySelectorAll('.lk-scarf-thumb');
					
					// Thumbnail clicking updates main image
					thumbs.forEach(thumb => {
						thumb.addEventListener('click', function() {
							mainImg.src = this.dataset.full;
							thumbs.forEach(t => t.classList.remove('is-active'));
							this.classList.add('is-active');
						});
					});
				}

				// Configurator Logic
				if (configPanel) {
					const designBtns = configPanel.querySelectorAll('.lk-scarf-designs .lk-scarf-choice');
					const sizeBtns = configPanel.querySelectorAll('.lk-scarf-sizes .lk-scarf-choice');
					const priceEl = document.getElementById('lk-price-' + id);
					const summaryEl = document.getElementById('lk-summary-' + id);
					const orderBtn = document.getElementById('lk-order-' + id);
					
					const template = configPanel.dataset.template;
					const whatsapp = configPanel.dataset.whatsapp;
					const currency = configPanel.dataset.currency;
					let buyMap = {};
					try { buyMap = JSON.parse(configPanel.dataset.buy || '{}') || {}; } catch (e) { buyMap = {}; }

					function updateConfigText() {
						const activeDesign = configPanel.querySelector('.lk-scarf-designs .is-selected');
						const activeSize = configPanel.querySelector('.lk-scarf-sizes .is-selected');
						if (!activeDesign || !activeSize) return;

						const dLabel = activeDesign.dataset.value;
						const sLabel = activeSize.dataset.label;
						const sPrice = activeSize.dataset.price;
						const hit = buyMap[dLabel + '||' + sLabel];
						const finalPrice = hit ? hit.price : sPrice;

						if (priceEl) priceEl.textContent = `${currency} ${finalPrice}`;
						if (summaryEl) summaryEl.textContent = `${dLabel} · ${sLabel} · ${currency} ${finalPrice}`;

						if (orderBtn) {
							// Using requested URL format exactly
							if (hit) {
								orderBtn.href = hit.url;
								orderBtn.removeAttribute('target');
								orderBtn.removeAttribute('rel');
								orderBtn.textContent = orderBtn.dataset.labelCart;
							} else {
								const msg = template.replace(/{design}/g, dLabel).replace(/{size}/g, sLabel);
								orderBtn.href = `https://api.whatsapp.com/send/?phone=${whatsapp}&text=${encodeURIComponent(msg)}&type=phone_number&app_absent=0`;
								orderBtn.setAttribute('target', '_blank');
								orderBtn.setAttribute('rel', 'noopener');
								orderBtn.textContent = orderBtn.dataset.labelEnquiry;
							}
						}
					}

					// Design Buttons: Update selection, text, AND change main image
					designBtns.forEach(btn => {
						btn.addEventListener('click', function() {
							designBtns.forEach(b => b.classList.remove('is-selected'));
							this.classList.add('is-selected');
							updateConfigText();

							// Update Image if one is attached to this design
							const designImgUrl = this.dataset.image;
							if (designImgUrl && mainImg) {
								mainImg.src = designImgUrl;
								// Update active state on thumbnails if a matching thumb exists
								if (thumbs) {
									thumbs.forEach(t => {
										t.classList.remove('is-active');
										if (t.dataset.full === designImgUrl) {
											t.classList.add('is-active');
										}
									});
								}
							}
						});
					});

					// Size Buttons: Only update selection and text
					sizeBtns.forEach(btn => {
						btn.addEventListener('click', function() {
							sizeBtns.forEach(b => b.classList.remove('is-selected'));
							this.classList.add('is-selected');
							updateConfigText();
						});
					});

					updateConfigText(); // Init on load
				}

				// Accordion Logic - Explicitly preventing default form actions just in case
				const accToggles = document.querySelectorAll('#lk-config-' + id + ' .lk-acc-toggle');
				accToggles.forEach(toggle => {
					toggle.addEventListener('click', function(e) {
						e.preventDefault();
						const expanded = this.getAttribute('aria-expanded') === 'true';
						this.setAttribute('aria-expanded', !expanded);
						this.nextElementSibling.hidden = expanded;
					});
				});
			})();
		</script>
		<?php
	}
}