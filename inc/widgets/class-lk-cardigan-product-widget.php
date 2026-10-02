<?php
/**
 * Lila Kora — Cardigan Product Widget
 *
 * The Interchangeable Cardigan product page: a main image with two
 * thumbnails on the left, and on the right an eyebrow, heading, a serif
 * sub-line ("Availability and pricing by enquiry"), a paragraph, a
 * two-option "Choose your cuff direction" toggle, an Enquire button whose
 * WhatsApp message and small caption change with the toggle, and a
 * three-column facts strip underneath.
 *
 * This product is enquiry-only by design (no WooCommerce, no price) — that
 * was Liliya's explicit instruction, unlike the scarf and Experience Box.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-cardigan-product-widget.php';
 *   $widgets_manager->register( new \LK_Cardigan_Product_Widget() );
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

class LK_Cardigan_Product_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-cardigan-product';
	}

	public function get_title() {
		return 'LK — Cardigan Product';
	}

	public function get_icon() {
		return 'eicon-product-images';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'cardigan', 'cuffs', 'enquiry', 'product', 'made to order' );
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

		$this->add_control( 'breadcrumb_shop_text', array( 'label' => 'Breadcrumb — shop label', 'type' => Controls_Manager::TEXT, 'default' => 'Shop', 'label_block' => true ) );
		$this->add_control( 'breadcrumb_shop_link', array( 'label' => 'Breadcrumb — shop link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => './shop.html' ) ) );
		$this->add_control( 'breadcrumb_current', array( 'label' => 'Breadcrumb — current page', 'type' => Controls_Manager::TEXT, 'default' => 'Interchangeable Cardigan', 'label_block' => true ) );
		$this->add_control( 'show_breadcrumb', array( 'label' => 'Show breadcrumb', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Made to order', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'The Interchangeable', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic word', 'type' => Controls_Manager::TEXT, 'default' => 'Cardigan', 'label_block' => true ) );
		$this->add_control( 'subheading', array( 'label' => 'Sub-line', 'type' => Controls_Manager::TEXT, 'default' => 'Availability and pricing by enquiry', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => 'A timeless cardigan designed to change with you. Choose cuffs from the Lila Kora collection or begin with your own artwork and create a set that is entirely personal.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Gallery
		 * =======================================================*/
		$this->start_controls_section( 'section_content_gallery', array( 'label' => 'Gallery', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'main_image', array( 'label' => 'Main image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'main_image', 'default' => 'large' ) );

		$thumbs = new Repeater();
		$thumbs->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_control(
			'thumbnails',
			array(
				'label'       => 'Thumbnails (optional, click to swap the main image)',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $thumbs->get_controls(),
				'default'     => array(
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
				),
				'title_field' => 'Thumbnail',
			)
		);
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'thumbnails', 'default' => 'medium' ) );
		$this->add_control( 'image_position', array( 'label' => 'Gallery side', 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => array( 'left' => 'Left', 'right' => 'Right' ), 'prefix_class' => 'lk-cardprod-img-' ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Cuff direction & enquiry
		 * =======================================================*/
		$this->start_controls_section( 'section_content_cuffs', array( 'label' => 'Cuff Direction & Enquiry', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'toggle_label', array( 'label' => 'Toggle group label', 'type' => Controls_Manager::TEXT, 'default' => 'Choose your cuff direction', 'label_block' => true ) );
		$this->add_control( 'option_existing_text', array( 'label' => 'Option 1 — button text', 'type' => Controls_Manager::TEXT, 'default' => 'Existing Lila Kora design', 'label_block' => true ) );
		$this->add_control( 'option_existing_caption', array( 'label' => 'Option 1 — caption under button', 'type' => Controls_Manager::TEXT, 'default' => 'Existing Lila Kora cuff design', 'label_block' => true ) );
		$this->add_control( 'option_custom_text', array( 'label' => 'Option 2 — button text', 'type' => Controls_Manager::TEXT, 'default' => 'Create my own cuffs', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'option_custom_caption', array( 'label' => 'Option 2 — caption under button', 'type' => Controls_Manager::TEXT, 'default' => 'Original artwork, made into your own cuff set', 'label_block' => true ) );

		$this->add_control( 'button_text', array( 'label' => 'Button text', 'type' => Controls_Manager::TEXT, 'default' => 'Enquire About the Cardigan', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'whatsapp_number', array( 'label' => 'WhatsApp number (with country code, no + or spaces)', 'type' => Controls_Manager::TEXT, 'default' => '971589610166' ) );
		$this->add_control(
			'message_template',
			array(
				'label'       => 'WhatsApp message template',
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Hello Lila Kora, I would like to enquire about the Interchangeable Cardigan with this cuff direction: {cuff}.',
				'description' => 'Use {cuff} — replaced with the selected option\'s button text.',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Facts strip
		 * =======================================================*/
		$this->start_controls_section( 'section_content_facts', array( 'label' => 'Facts Strip', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'show_facts', array( 'label' => 'Show facts strip', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$facts = new Repeater();
		$facts->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Fact', 'label_block' => true ) );
		$this->add_control(
			'facts',
			array(
				'label'       => 'Facts',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $facts->get_controls(),
				'default'     => array(
					array( 'text' => 'Made to order' ),
					array( 'text' => 'Interchangeable cuffs' ),
					array( 'text' => 'Personal guidance included' ),
				),
				'title_field' => '{{{ text }}}',
				'condition'   => array( 'show_facts' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'bg_color', 'Background', '.lk-cardprod-page', '#FFFEFD', 'background-color' );
		$this->add_responsive_control( 'page_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-cardprod-page' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'page_padding',
			array(
				'label'          => 'Padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 45, 'right' => 65, 'bottom' => 105, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 32, 'right' => 32, 'bottom' => 80, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 24, 'right' => 24, 'bottom' => 70, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-cardprod-page' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'columns_gap', array( 'label' => 'Gap between columns', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 160 ) ), 'default' => array( 'unit' => 'px', 'size' => 95 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 50 ), 'selectors' => array( '{{WRAPPER}} .lk-cardprod-layout' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Breadcrumb
		 * =======================================================*/
		$this->start_controls_section( 'section_style_breadcrumb', array( 'label' => 'Breadcrumb', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_breadcrumb' => 'yes' ) ) );

		$this->color( 'crumb_link_color', 'Link colour', '.lk-cardprod-crumb a', '#8F8584' );
		$this->color( 'crumb_current_color', 'Current page colour', '.lk-cardprod-crumb span', '#57282D' );
		$this->typo( 'crumb_typography', 'Typography', '.lk-cardprod-crumb', 'Montserrat', 13 );
		$this->color( 'crumb_border', 'Bottom border', '.lk-cardprod-crumb', 'rgba(105,33,55,0.1)', 'border-color' );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Gallery
		 * =======================================================*/
		$this->start_controls_section( 'section_style_gallery', array( 'label' => 'Gallery', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->add_responsive_control( 'main_image_height', array( 'label' => 'Main image height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 250, 'max' => 900 ) ), 'default' => array( 'unit' => 'px', 'size' => 620 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 500 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 420 ), 'selectors' => array( '{{WRAPPER}} .lk-cardprod-main' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'thumb_gap', array( 'label' => 'Gap between thumbnails', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 12 ), 'selectors' => array( '{{WRAPPER}} .lk-cardprod-thumbs' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'thumb_height', array( 'label' => 'Thumbnail height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 80, 'max' => 400 ) ), 'default' => array( 'unit' => 'px', 'size' => 260 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 200 ), 'selectors' => array( '{{WRAPPER}} .lk-cardprod-thumbs figure' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'image_radius', array( 'label' => 'Corner radius', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-cardprod-gallery figure' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section( 'section_style_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-cardprod-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-cardprod-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-cardprod-heading', '#57282D', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic word colour', '.lk-cardprod-heading em', '#57282D' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-cardprod-heading', 'Cormorant Garamond', 68, '400', -0.025, false, 0.98, 50, 38 );
		$this->color( 'subheading_color', 'Sub-line colour', '.lk-cardprod-subheading', '#692137', 'color', true );
		$this->typo( 'subheading_typography', 'Sub-line typography', '.lk-cardprod-subheading', 'Cormorant Garamond', 22 );
		$this->color( 'desc_color', 'Paragraph colour', '.lk-cardprod-desc', '#8F8584', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-cardprod-desc', 'Montserrat', 16, '', null, false, 1.75 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Cuff toggle
		 * =======================================================*/
		$this->start_controls_section( 'section_style_toggle', array( 'label' => 'Cuff Direction Toggle', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'toggle_border_top', 'Divider above', '.lk-cardprod-cuffs', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->color( 'toggle_label_color', 'Group label colour', '.lk-cardprod-toggle-label', '#692137', 'color', true );
		$this->typo( 'toggle_label_typography', 'Group label typography', '.lk-cardprod-toggle-label', 'Montserrat', 10.6, '600', 0.14, true );
		$this->add_responsive_control( 'toggle_min_height', array( 'label' => 'Option min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 40, 'max' => 100 ) ), 'default' => array( 'unit' => 'px', 'size' => 58 ), 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-cardprod-option' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->typo( 'toggle_typography', 'Option typography', '.lk-cardprod-option', 'Montserrat', 13 );

		$this->start_controls_tabs( 'tabs_toggle' );
		$this->start_controls_tab( 'tab_toggle_normal', array( 'label' => 'Normal' ) );
		$this->color( 'toggle_bg', 'Background', '.lk-cardprod-option', 'transparent', 'background-color', false, true );
		$this->color( 'toggle_color', 'Text colour', '.lk-cardprod-option', '#281D21', 'color', false, true );
		$this->color( 'toggle_border', 'Border colour', '.lk-cardprod-option', 'rgba(105,33,55,0.16)', 'border-color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_toggle_hover', array( 'label' => 'Hover' ) );
		$this->color( 'toggle_border_hover', 'Border colour', '.lk-cardprod-option:not(.is-selected):hover', '#692137', 'border-color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_toggle_selected', array( 'label' => 'Selected' ) );
		$this->color( 'toggle_bg_selected', 'Background', '.lk-cardprod-option.is-selected', '#692137', 'background-color', false, true );
		$this->color( 'toggle_color_selected', 'Text colour', '.lk-cardprod-option.is-selected', '#FFFFFF', 'color', false, true );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->color( 'caption_color', 'Caption under button colour', '.lk-cardprod-caption', '#8F8584', 'color', true );
		$this->typo( 'caption_typography', 'Caption under button typography', '.lk-cardprod-caption', 'Montserrat', 12.5, '', null, false, null, null, null );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Button
		 * =======================================================*/
		$this->start_controls_section( 'section_style_button', array( 'label' => 'Enquire Button', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->typo( 'btn_typography', 'Typography', '.lk-cardprod-btn', 'Montserrat', 10.9, '600', 0.17, true );
		$this->add_responsive_control( 'btn_min_height', array( 'label' => 'Min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 40, 'max' => 100 ) ), 'default' => array( 'unit' => 'px', 'size' => 58 ), 'selectors' => array( '{{WRAPPER}} .lk-cardprod-btn' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->start_controls_tabs( 'tabs_btn' );
		$this->start_controls_tab( 'tab_btn_normal', array( 'label' => 'Normal' ) );
		$this->color( 'btn_bg', 'Background', '.lk-cardprod-btn', '#692137', 'background-color', false, true );
		$this->color( 'btn_color', 'Text colour', '.lk-cardprod-btn', '#FFFFFF', 'color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_hover', array( 'label' => 'Hover' ) );
		$this->color( 'btn_bg_hover', 'Background', '.lk-cardprod-btn:hover', 'rgba(105,33,55,0.88)', 'background-color', false, true );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Facts strip
		 * =======================================================*/
		$this->start_controls_section( 'section_style_facts', array( 'label' => 'Facts Strip', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_facts' => 'yes' ) ) );

		$this->color( 'facts_border', 'Divider colour', '.lk-cardprod-facts, {{WRAPPER}} .lk-cardprod-facts span', 'rgba(105,33,55,0.1)', 'border-color' );
		$this->color( 'facts_color', 'Text colour', '.lk-cardprod-facts span', '#8F8584', 'color', true );
		$this->typo( 'facts_typography', 'Typography', '.lk-cardprod-facts span', 'Montserrat', 13 );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$main_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'main_image', 'main_image' );
		$thumbs    = (array) $s['thumbnails'];

		$this->add_render_attribute( 'enquire', 'class', 'lk-cardprod-btn' );
		$this->add_render_attribute( 'enquire', 'target', '_blank' );
		$this->add_render_attribute( 'enquire', 'rel', 'noopener' );
		$this->add_render_attribute( 'enquire', 'href', '#' );
		$this->add_render_attribute( 'enquire', 'data-lk-cardigan-order', '' );

		if ( 'yes' === $s['show_breadcrumb'] ) {
			$this->add_render_attribute( 'crumb_link', 'href', ! empty( $s['breadcrumb_shop_link']['url'] ) ? $s['breadcrumb_shop_link']['url'] : '#' );
		}
		?>
		<div class="lk-cardprod-page" data-lk-cardigan
			data-whatsapp="<?php echo esc_attr( $s['whatsapp_number'] ); ?>"
			data-template="<?php echo esc_attr( $s['message_template'] ); ?>"
			data-existing-text="<?php echo esc_attr( $s['option_existing_text'] ); ?>"
			data-existing-caption="<?php echo esc_attr( $s['option_existing_caption'] ); ?>"
			data-custom-text="<?php echo esc_attr( $s['option_custom_text'] ); ?>"
			data-custom-caption="<?php echo esc_attr( $s['option_custom_caption'] ); ?>">

			<?php if ( 'yes' === $s['show_breadcrumb'] ) : ?>
				<nav class="lk-cardprod-crumb" aria-label="Breadcrumb">
					<a <?php echo $this->get_render_attribute_string( 'crumb_link' ); ?>><?php echo esc_html( $s['breadcrumb_shop_text'] ); ?></a>
					<span aria-hidden="true">/</span>
					<span><?php echo esc_html( $s['breadcrumb_current'] ); ?></span>
				</nav>
			<?php endif; ?>

			<div class="lk-cardprod-layout">
				<div class="lk-cardprod-gallery">
					<figure class="lk-cardprod-main"><?php echo $main_html; ?></figure>
					<?php if ( $thumbs ) : ?>
						<div class="lk-cardprod-thumbs">
							<?php foreach ( $thumbs as $t ) :
								if ( empty( $t['image']['url'] ) ) {
									continue;
								}
								$thumb_html = Group_Control_Image_Size::get_attachment_image_html( array_merge( $t, array( 'thumbnails_size' => $s['thumbnails_size'], 'thumbnails_custom_dimension' => $s['thumbnails_custom_dimension'] ) ), 'thumbnails', 'image' );
								?>
								<figure><?php echo $thumb_html; ?></figure>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="lk-cardprod-details">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-cardprod-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
					<h1 class="lk-cardprod-heading"><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h1>
					<?php if ( ! empty( $s['subheading'] ) ) : ?><p class="lk-cardprod-subheading"><?php echo esc_html( $s['subheading'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-cardprod-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

					<fieldset class="lk-cardprod-cuffs">
						<legend class="lk-cardprod-toggle-label"><?php echo esc_html( $s['toggle_label'] ); ?></legend>
						<div class="lk-cardprod-options" data-lk-cuff-options>
							<button type="button" class="lk-cardprod-option is-selected" data-cuff="existing" aria-pressed="true"><?php echo esc_html( $s['option_existing_text'] ); ?></button>
							<button type="button" class="lk-cardprod-option" data-cuff="custom" aria-pressed="false"><?php echo esc_html( $s['option_custom_text'] ); ?></button>
						</div>
					</fieldset>

					<a <?php echo $this->get_render_attribute_string( 'enquire' ); ?>><?php echo esc_html( $s['button_text'] ); ?></a>
					<p class="lk-cardprod-caption" data-lk-cuff-caption><?php echo esc_html( $s['option_existing_caption'] ); ?></p>

					<?php if ( 'yes' === $s['show_facts'] && ! empty( $s['facts'] ) ) : ?>
						<div class="lk-cardprod-facts">
							<?php foreach ( $s['facts'] as $fact ) : ?><span><?php echo esc_html( $fact['text'] ); ?></span><?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<style>
			.lk-cardprod-page { box-sizing: border-box; }
			.lk-cardprod-crumb { display: flex; align-items: center; gap: 8px; padding-bottom: 18px; margin-bottom: 45px; border-bottom: 1px solid; }
			.lk-cardprod-crumb a { text-decoration: none; }
			.lk-cardprod-crumb span[aria-hidden] { opacity: .5; }
			.lk-cardprod-layout { display: grid; grid-template-columns: 1fr 1fr; align-items: start; }
			.lk-cardprod-img-right .lk-cardprod-gallery { order: 2; } .lk-cardprod-img-right .lk-cardprod-details { order: 1; }
			.lk-cardprod-main { position: relative; margin: 0 0 14px; overflow: hidden; }
			.lk-cardprod-main img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-cardprod-thumbs { display: grid; grid-template-columns: 1fr 1fr; }
			.lk-cardprod-thumbs figure { position: relative; margin: 0; overflow: hidden; }
			.lk-cardprod-thumbs img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-cardprod-details { align-self: start; }
			.lk-cardprod-heading { margin: 0 0 22px; font-style: normal; }
			.lk-cardprod-heading em { font-style: italic; }
			.lk-cardprod-subheading { margin: 0 0 20px; font-style: normal; }
			.lk-cardprod-desc { margin: 0; max-width: 560px; }
			.lk-cardprod-cuffs { margin: 32px 0 0; padding: 24px 0 0; border: 0; border-top: 1px solid; }
			.lk-cardprod-toggle-label { display: block; width: 100%; margin: 0 0 14px; }
			.lk-cardprod-options { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
			.lk-cardprod-option { display: flex; align-items: center; justify-content: center; padding: 12px 16px; text-align: center; background: transparent; border: 1px solid; cursor: pointer; -webkit-appearance: none; appearance: none; box-shadow: none; transition: color .25s ease, background .25s ease, border-color .25s ease; }
			.lk-cardprod-btn { display: flex; width: 100%; min-height: 58px; align-items: center; justify-content: center; margin-top: 20px; box-sizing: border-box; text-decoration: none; border: 0; border-radius: 0; cursor: pointer; transition: background .25s ease; }
			.lk-cardprod-caption { margin: 14px 0 0; text-align: center; }
			.lk-cardprod-facts { display: grid; grid-template-columns: repeat(3, 1fr); margin-top: 30px; padding-top: 20px; border-top: 1px solid; }
			.lk-cardprod-facts span { padding: 0 14px; text-align: center; border-left: 1px solid; }
			.lk-cardprod-facts span:first-child { padding-left: 0; border-left: 0; }
			@media (max-width: 820px) {
				.lk-cardprod-layout { grid-template-columns: 1fr !important; }
				.lk-cardprod-img-right .lk-cardprod-gallery, .lk-cardprod-img-right .lk-cardprod-details { order: 0; }
			}
			@media (max-width: 540px) {
				.lk-cardprod-thumbs { grid-template-columns: 1fr 1fr; }
				.lk-cardprod-options { grid-template-columns: 1fr; }
				.lk-cardprod-facts { grid-template-columns: 1fr; }
				.lk-cardprod-facts span { border-left: 0; border-top: 1px solid; padding: 12px 0 0; margin-top: 12px; }
				.lk-cardprod-facts span:first-child { border-top: 0; padding-top: 0; margin-top: 0; }
			}
		</style>

		<script>
			( function () {
				if ( window.__lkCardiganBound ) { return; }
				window.__lkCardiganBound = true;
				document.addEventListener( 'click', function ( event ) {
					var btn = event.target.closest( '[data-lk-cuff-options] .lk-cardprod-option' );
					if ( btn ) {
						var root = btn.closest( '[data-lk-cardigan]' );
						root.querySelectorAll( '.lk-cardprod-option' ).forEach( function ( b ) {
							var active = b === btn;
							b.classList.toggle( 'is-selected', active );
							b.setAttribute( 'aria-pressed', String( active ) );
						} );
						var isCustom  = 'custom' === btn.getAttribute( 'data-cuff' );
						var cuffText  = isCustom ? root.getAttribute( 'data-custom-text' ) : root.getAttribute( 'data-existing-text' );
						var caption   = isCustom ? root.getAttribute( 'data-custom-caption' ) : root.getAttribute( 'data-existing-caption' );
						var captionEl = root.querySelector( '[data-lk-cuff-caption]' );
						if ( captionEl ) { captionEl.textContent = caption; }
						var orderEl = root.querySelector( '[data-lk-cardigan-order]' );
						if ( orderEl ) {
							var msg = root.getAttribute( 'data-template' ).replace( '{cuff}', cuffText );
							orderEl.href = 'https://wa.me/' + root.getAttribute( 'data-whatsapp' ) + '?text=' + encodeURIComponent( msg );
						}
						return;
					}
					var thumb = event.target.closest( '.lk-cardprod-thumbs figure' );
					if ( thumb ) {
						var gallery = thumb.closest( '.lk-cardprod-gallery' );
						var mainImg = gallery.querySelector( '.lk-cardprod-main img' );
						var thumbImg = thumb.querySelector( 'img' );
						if ( mainImg && thumbImg ) {
							var mainSrc = mainImg.getAttribute( 'src' );
							mainImg.setAttribute( 'src', thumbImg.getAttribute( 'src' ) );
							thumbImg.setAttribute( 'src', mainSrc );
						}
					}
				} );

				document.querySelectorAll( '[data-lk-cardigan]:not([data-lk-bound])' ).forEach( function ( root ) {
					root.setAttribute( 'data-lk-bound', 'true' );
					var orderEl = root.querySelector( '[data-lk-cardigan-order]' );
					if ( orderEl ) {
						var msg = root.getAttribute( 'data-template' ).replace( '{cuff}', root.getAttribute( 'data-existing-text' ) );
						orderEl.href = 'https://wa.me/' + root.getAttribute( 'data-whatsapp' ) + '?text=' + encodeURIComponent( msg );
					}
				} );
			} )();
		</script>
		<?php
	}
}
