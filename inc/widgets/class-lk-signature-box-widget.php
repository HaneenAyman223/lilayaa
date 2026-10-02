<?php
/**
 * Lila Kora — Signature Box Widget
 *
 * The feature-split "signature product" block — eyebrow, two-line
 * heading, one or two paragraphs, a price line (label / price / small
 * note), a CTA button, and an image with an italic caption. Used on
 * the homepage for the Canvas-to-Scarf Experience Box, and reusable
 * for any similar card (e.g. a gift-card variant) just by editing
 * the text fields.
 *
 * Mobile note: the reference site's own CSS sizes this heading with
 * `clamp(3rem, 5vw, 6rem)`, which floors out at 48px on small screens
 * — too large once the section stacks to near-full viewport width on
 * mobile (this is exactly what the client flagged as "way too big").
 * Rather than copy that floor faithfully, the mobile default here is
 * set deliberately smaller (32px). Adjust further in Style → Heading
 * → the mobile (phone icon) tab if needed.
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class LK_Signature_Box_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-signature-box';
	}

	public function get_title() {
		return 'LK — Signature Box';
	}

	public function get_icon() {
		return 'eicon-single-page';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'box', 'product', 'feature', 'signature' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_text',
			array(
				'label' => 'Text',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => 'Eyebrow',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'The signature Canvas-to-Scarf Experience Box',
				'label_block' => true,
			)
		);
		$this->add_control(
			'heading_main',
			array(
				'label'       => 'Heading — first line',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Create a scarf',
				'label_block' => true,
			)
		);
		$this->add_control(
			'heading_emphasis',
			array(
				'label'       => 'Heading — emphasized line',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'that begins with you.',
				'label_block' => true,
			)
		);
		$this->add_control(
			'description_1',
			array(
				'label'       => 'Paragraph 1',
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => 'Everything you need to create your own scarf design at home, thoughtfully prepared in one box. Paint your artwork, send us a photo and approve the professionally refined design before it is crafted into your scarf.',
				'label_block' => true,
			)
		);
		$this->add_control(
			'description_2',
			array(
				'label'       => 'Paragraph 2 (optional)',
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => '',
				'label_block' => true,
				'description' => 'Leave empty to show just one paragraph, as on the homepage. Fill this in for variants like a gift-card card that use two.',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Price & Button
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_price',
			array(
				'label' => 'Price & Button',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'price_label',
			array(
				'label'       => 'Price line — label',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Canvas-to-Scarf Experience Box',
				'label_block' => true,
			)
		);
		$this->add_control(
			'price_value',
			array(
				'label'       => 'Price line — value',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'From AED 390',
				'label_block' => true,
			)
		);
		$this->add_control(
			'price_note',
			array(
				'label'       => 'Price line — small note (optional)',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Choose your fabric and scarf size on the product page.',
				'label_block' => true,
			)
		);

		$this->add_control(
			'button_divider',
			array( 'type' => Controls_Manager::DIVIDER )
		);
		$this->add_control(
			'button_text',
			array(
				'label'       => 'Button — text',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Explore the Experience Box',
				'label_block' => true,
			)
		);
		$this->add_control(
			'button_link',
			array(
				'label'         => 'Button — link',
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => './experience-box.html' ),
				'show_external' => true,
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Image
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_image',
			array(
				'label' => 'Image',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => 'Image',
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array( 'name' => 'image', 'default' => 'large' )
		);
		$this->add_control(
			'image_caption',
			array(
				'label'       => 'Image caption (optional)',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'From a blank canvas to a scarf unlike any other.',
				'label_block' => true,
			)
		);
		$this->add_control(
			'image_position',
			array(
				'label'        => 'Image position',
				'type'         => Controls_Manager::SELECT,
				'default'      => 'right',
				'options'      => array( 'right' => 'Right', 'left' => 'Left' ),
				'prefix_class' => 'lk-sigbox-img-',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array(
				'label' => 'Layout',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F8ECE9',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label'     => 'Max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'The reference\'s generic .section wrapper caps this at 1440px and centres it. Set your outer Elementor container to full-width / 0 padding and let this control the actual width.',
				'selectors' => array(
					'{{WRAPPER}} .lk-sigbox' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);
		$this->add_responsive_control(
			'columns_gap',
			array(
				'label'      => 'Gap between text & image',
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ), 'vw' => array( 'min' => 0, 'max' => 15 ) ),
				'default'    => array( 'unit' => 'vw', 'size' => 8 ),
				'description' => 'Reference: 8vw (≈115px at a 1440px width) — was hardcoded to a flat 70px before, which is why the gap looked off.',
				'selectors'  => array( '{{WRAPPER}} .lk-sigbox' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'      => 'Padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'vw' ),
				'default'    => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 32, 'bottom' => 90, 'left' => 32, 'unit' => 'px' ),
				'mobile_default'  => array( 'top' => 90, 'right' => 20, 'bottom' => 90, 'left' => 20, 'unit' => 'px' ),
				'description' => 'Reference: clamp(90px, 10vw, 155px) 4.5vw. The desktop default here (120px / 65px) approximates that clamp at a typical 1440px width.',
				'selectors'  => array(
					'{{WRAPPER}} .lk-sigbox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'copy_max_width',
			array(
				'label'     => 'Text column max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 300, 'max' => 700 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 590 ),
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-copy' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Eyebrow & Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_heading',
			array(
				'label' => 'Eyebrow & Heading',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => 'Eyebrow colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF715E',
				'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .lk-eyebrow',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ),
					'font_weight'    => array( 'default' => '600' ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.19 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => 'Heading colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#57282D',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-heading' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'heading_emphasis_color',
			array(
				'label'       => 'Emphasized line colour',
				'type'        => Controls_Manager::COLOR,
				'default'     => '#57282D',
				'description' => 'Same ink colour by default, matching the reference — only italic changes.',
				'selectors'   => array( '{{WRAPPER}} .lk-sigbox-heading em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .lk-sigbox-heading',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array(
						'default' => array( 'unit' => 'px', 'size' => 62 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 44 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 32 ),
					),
					'font_weight'    => array( 'default' => '400' ),
					'line_height'    => array( 'default' => array( 'unit' => 'em', 'size' => 0.98 ) ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => -0.025 ) ),
				),
			)
		);
		$this->add_control(
			'mobile_size_note',
			array(
				'type'        => Controls_Manager::HEADING,
				'label'       => 'Note',
				'description' => 'The mobile default above (32px) is deliberately smaller than the reference site\'s own clamp() floor of 48px — that floor is what caused the "way too big" mobile feedback. Adjust freely via the phone-icon tab on the control above.',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Body Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_body',
			array(
				'label' => 'Body Text',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'body_color',
			array(
				'label'     => 'Colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8F8584',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-copy p.lk-sigbox-desc' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'body_typography',
				'selector' => '{{WRAPPER}} .lk-sigbox-copy p.lk-sigbox-desc',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Montserrat' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.75 ) ),
				),
			)
		);
		$this->add_responsive_control(
			'body_spacing',
			array(
				'label'     => 'Spacing between paragraphs',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 16 ),
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-copy p.lk-sigbox-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Price Line
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_price',
			array(
				'label' => 'Price Line',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'price_border_color',
			array(
				'label'     => 'Divider colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(105,33,55,0.16)',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-price' => 'border-color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'price_margin',
			array(
				'label'      => 'Spacing above & below',
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 30 ),
				'description' => 'Matches the reference\'s margin: 30px 0 — this block was sitting flush against the paragraph and button before, with no breathing room.',
				'selectors'  => array( '{{WRAPPER}} .lk-sigbox-price' => 'margin: {{SIZE}}{{UNIT}} 0;' ),
			)
		);
		$this->add_responsive_control(
			'price_padding',
			array(
				'label'      => 'Inner padding (top & bottom)',
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 18 ),
				'description' => 'Matches the reference\'s padding: 18px 0, between the top/bottom divider lines.',
				'selectors'  => array( '{{WRAPPER}} .lk-sigbox-price' => 'padding: {{SIZE}}{{UNIT}} 0;' ),
			)
		);
		$this->add_control(
			'price_label_color',
			array(
				'label'     => 'Label colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#57282D',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-price span' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_label_typography',
				'selector' => '{{WRAPPER}} .lk-sigbox-price span',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 11.8 ) ),
					'font_weight'    => array( 'default' => '600' ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.08 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);
		$this->add_control(
			'price_value_color',
			array(
				'label'     => 'Value colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-price strong' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_value_typography',
				'selector' => '{{WRAPPER}} .lk-sigbox-price strong',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 27.2 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);
		$this->add_control(
			'price_note_color',
			array(
				'label'     => 'Small note colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8F8584',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-price small' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_note_typography',
				'selector' => '{{WRAPPER}} .lk-sigbox-price small',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Montserrat' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 11.5 ) ),
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Button
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => 'Button',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .lk-sigbox-btn',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ),
					'font_weight'    => array( 'default' => '600' ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.12 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);
		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => 'Padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array( 'top' => 15, 'right' => 30, 'bottom' => 15, 'left' => 30, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-sigbox-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_btn' );
		$this->start_controls_tab( 'tab_btn_normal', array( 'label' => 'Normal' ) );
		$this->add_control(
			'btn_bg',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-btn' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_color',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-btn' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_hover', array( 'label' => 'Hover' ) );
		$this->add_control(
			'btn_bg_hover',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-btn:hover' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_color_hover',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-btn:hover' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Image
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => 'Image',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'     => 'Height',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 200, 'max' => 900 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 710 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 560 ),
				'mobile_default'  => array( 'unit' => 'px', 'size' => 500 ),
				'description' => 'Matches the reference exactly: 710px desktop → 560px tablet → 500px mobile.',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-image img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;' ),
			)
		);
		$this->add_control(
			'image_radius',
			array(
				'label'     => 'Corner radius',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 0 ),
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-image img' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array( 'name' => 'image_shadow', 'selector' => '{{WRAPPER}} .lk-sigbox-image img' )
		);
		$this->add_control(
			'caption_color',
			array(
				'label'     => 'Caption colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8F8584',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-sigbox-image figcaption' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'caption_typography',
				'selector' => '{{WRAPPER}} .lk-sigbox-image figcaption',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
					'font_style'  => array( 'default' => 'italic' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'button', 'class', 'lk-sigbox-btn' );
		$this->add_link_attributes( 'button', $s['button_link'] );

		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		?>
		<div class="lk-sigbox">
			<div class="lk-sigbox-copy">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
					<p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>

				<h2 class="lk-sigbox-heading">
					<?php echo esc_html( $s['heading_main'] ); ?><br>
					<em><?php echo esc_html( $s['heading_emphasis'] ); ?></em>
				</h2>

				<?php if ( ! empty( $s['description_1'] ) ) : ?>
					<p class="lk-sigbox-desc"><?php echo esc_html( $s['description_1'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $s['description_2'] ) ) : ?>
					<p class="lk-sigbox-desc"><?php echo esc_html( $s['description_2'] ); ?></p>
				<?php endif; ?>

				<div class="lk-sigbox-price">
					<div>
						<span><?php echo esc_html( $s['price_label'] ); ?></span>
						<strong><?php echo esc_html( $s['price_value'] ); ?></strong>
					</div>
					<?php if ( ! empty( $s['price_note'] ) ) : ?>
						<small><?php echo esc_html( $s['price_note'] ); ?></small>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $s['button_text'] ) ) : ?>
					<a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
						<?php echo esc_html( $s['button_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>

			<figure class="lk-sigbox-image">
				<?php echo $image_html; ?>
				<?php if ( ! empty( $s['image_caption'] ) ) : ?>
					<figcaption><?php echo esc_html( $s['image_caption'] ); ?></figcaption>
				<?php endif; ?>
			</figure>
		</div>
		<?php
	}
}
