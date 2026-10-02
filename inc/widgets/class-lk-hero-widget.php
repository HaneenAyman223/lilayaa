<?php
/**
 * Lila Kora — Hero Widget
 *
 * Reproduces the "Create a scarf that begins with you" hero: eyebrow,
 * two-line heading with an emphasized (italic) second line, intro copy,
 * two buttons, an underlined text-link, and a framed image with a
 * numbered caption overlay.
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class LK_Hero_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-hero';
	}

	public function get_title() {
		return 'LK — Hero';
	}

	public function get_icon() {
		return 'eicon-slider-full-screen';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'hero', 'banner', 'header' );
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
				'default'     => 'Silk scarves and creative experiences · Dubai',
				'label_block' => true,
			)
		);

		$this->add_control(
			'heading_main',
			array(
				'label'       => 'Heading — first line',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'A scarf with',
				'label_block' => true,
			)
		);

		$this->add_control(
			'heading_emphasis',
			array(
				'label'       => 'Heading — emphasized line',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'a story.',
				'label_block' => true,
				'description' => 'Rendered on its own line, in italic and the accent colour.',
			)
		);

		$this->add_control(
			'intro',
			array(
				'label'       => 'Intro paragraph',
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => 'Discover limited-edition silk scarves from the Lila Kora collection, or begin with a blank canvas and create a scarf that is entirely your own.',
				'rows'        => 4,
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Buttons
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_buttons',
			array(
				'label' => 'Buttons & Link',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'btn_primary_text',
			array(
				'label'       => 'Primary button — text',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Shop Ready-to-Wear',
				'label_block' => true,
			)
		);
		$this->add_control(
			'btn_primary_link',
			array(
				'label'         => 'Primary button — link',
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://your-site.com/collection',
				'default'       => array( 'url' => '#collection' ),
				'show_external' => true,
			)
		);

		$this->add_control(
			'btn_secondary_text',
			array(
				'label'       => 'Secondary button — text',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Create Your Own',
				'label_block' => true,
			)
		);
		$this->add_control(
			'btn_secondary_link',
			array(
				'label'         => 'Secondary button — link',
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://your-site.com/experiences',
				'default'       => array( 'url' => '#experiences' ),
				'show_external' => true,
			)
		);

		$this->add_control(
			'underlink_divider',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'show_underlink',
			array(
				'label'        => 'Show text link below buttons',
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'underlink_text',
			array(
				'label'       => 'Text link — label',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Book a Workshop',
				'label_block' => true,
				'condition'   => array( 'show_underlink' => 'yes' ),
			)
		);
		$this->add_control(
			'underlink_link',
			array(
				'label'         => 'Text link — url',
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => './public-workshops.html' ),
				'show_external' => true,
				'condition'     => array( 'show_underlink' => 'yes' ),
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
			'hero_image',
			array(
				'label'   => 'Image',
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'hero_image', // produces hero_image_size / hero_image_custom_dimension
				'default' => 'large',
			)
		);

		$this->add_control(
			'show_caption',
			array(
				'label'     => 'Show numbered caption',
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => 'Show',
				'label_off' => 'Hide',
				'default'   => 'yes',
			)
		);
		$this->add_control(
			'caption_number',
			array(
				'label'     => 'Caption — number',
				'type'      => Controls_Manager::TEXT,
				'default'   => '01',
				'condition' => array( 'show_caption' => 'yes' ),
			)
		);
		$this->add_control(
			'caption_text',
			array(
				'label'       => 'Caption — text',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Designed to be worn. Created to mean something.',
				'label_block' => true,
				'condition'   => array( 'show_caption' => 'yes' ),
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

		$this->add_responsive_control(
			'max_width',
			array(
				'label'     => 'Max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'The reference caps every section at 1440px and centres it — matches that. Set your outer Elementor container to full-width / 0 padding and let this control the actual width.',
				'selectors' => array(
					'{{WRAPPER}} .lk-hero' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'min_height',
			array(
				'label'       => 'Minimum height',
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 400, 'max' => 1000 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 850 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 700 ),
				'mobile_default'  => array( 'unit' => 'px', 'size' => 0 ),
				'description' => 'The reference uses min(850px, calc(100vh - 118px)) — a plain px value here is a close approximation; a true viewport-relative cap isn\'t achievable through a single slider control.',
				'selectors'   => array(
					'{{WRAPPER}} .lk-hero' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'text_column_width',
			array(
				'label'       => 'Text column width (%)',
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( '%' => array( 'min' => 30, 'max' => 70 ) ),
				'default'     => array( 'unit' => '%', 'size' => 48.5 ),
				'description' => 'Matches the reference\'s 48.5% / 51.5% split — the image column takes whatever\'s left, so the two always add up to 100%.',
				'selectors'   => array(
					'{{WRAPPER}} .lk-hero' => 'grid-template-columns: {{SIZE}}% calc(100% - {{SIZE}}%);',
				),
			)
		);

		$this->add_responsive_control(
			'columns_gap',
			array(
				'label'      => 'Gap between text & image',
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0 ),
				'description' => 'The reference has no gap — the split comes purely from the column percentages above, with the text column\'s own padding providing the visual breathing room.',
				'selectors'  => array(
					'{{WRAPPER}} .lk-hero' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'border_bottom_color',
			array(
				'label'     => 'Bottom border colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(105,33,55,0.16)',
				'selectors' => array(
					'{{WRAPPER}} .lk-hero' => 'border-bottom: 1px solid {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'image_position',
			array(
				'label'   => 'Image position',
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'right' => 'Right',
					'left'  => 'Left',
				),
				'prefix_class' => 'lk-hero-img-',
			)
		);

		$this->add_responsive_control(
			'content_max_width',
			array(
				'label'     => 'Text column inner max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 300, 'max' => 800 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 560 ),
				'selectors' => array(
					'{{WRAPPER}} .lk-hero-copy' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'copy_padding',
			array(
				'label'      => 'Text column padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'vw' ),
				'default'    => array( 'top' => 90, 'right' => 75, 'bottom' => 90, 'left' => 75, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 70, 'right' => 40, 'bottom' => 70, 'left' => 40, 'unit' => 'px' ),
				'mobile_default'  => array( 'top' => 65, 'right' => 24, 'bottom' => 50, 'left' => 24, 'unit' => 'px' ),
				'description' => 'Reference: 90px clamp(34px,5.2vw,92px) desktop → 65px 24px 50px 24px on mobile. The desktop default here (75px sides) approximates that clamp at a typical 1440px width.',
				'selectors'  => array(
					'{{WRAPPER}} .lk-hero-copy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Eyebrow
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_eyebrow',
			array(
				'label' => 'Eyebrow',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => 'Colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF715E',
				'selectors' => array(
					'{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};',
				),
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
					'line_height'    => array( 'default' => array( 'unit' => 'em', 'size' => 1.5 ) ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.19 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);

		$this->add_responsive_control(
			'eyebrow_spacing',
			array(
				'label'     => 'Spacing below',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 18 ),
				'selectors' => array(
					'{{WRAPPER}} .lk-eyebrow' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_heading',
			array(
				'label' => 'Heading',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => 'Colour — main line',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#57282D',
				'selectors' => array(
					'{{WRAPPER}} .lk-heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'heading_emphasis_color',
			array(
				'label'       => 'Colour — emphasized line',
				'type'        => Controls_Manager::COLOR,
				'default'     => '#57282D',
				'description' => 'The reference design keeps this the same ink colour as the main line — only italic changes. Override here if you ever want a distinct accent colour.',
				'selectors'   => array(
					'{{WRAPPER}} .lk-heading em' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .lk-heading',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array(
						'default' => array( 'unit' => 'px', 'size' => 84 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 58 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 42 ),
					),
					'font_weight'    => array( 'default' => '400' ),
					'line_height'    => array( 'default' => array( 'unit' => 'em', 'size' => 0.98 ) ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => -0.025 ) ),
				),
			)
		);

		$this->add_responsive_control(
			'heading_spacing',
			array(
				'label'     => 'Spacing below',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 24 ),
				'selectors' => array(
					'{{WRAPPER}} .lk-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Intro text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_intro',
			array(
				'label' => 'Intro Text',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'intro_color',
			array(
				'label'     => 'Colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8F8584',
				'selectors' => array(
					'{{WRAPPER}} .lk-intro' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'intro_typography',
				'selector' => '{{WRAPPER}} .lk-intro',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Montserrat' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.9 ) ),
				),
			)
		);

		$this->add_responsive_control(
			'intro_spacing',
			array(
				'label'     => 'Spacing below',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 36 ),
				'selectors' => array(
					'{{WRAPPER}} .lk-intro' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Buttons
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_buttons',
			array(
				'label' => 'Buttons',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .lk-btn',
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
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 15, 'right' => 30, 'bottom' => 15, 'left' => 30, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_radius',
			array(
				'label'      => 'Border radius',
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0 ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_btn_primary',
			array(
				'label'     => 'Primary Button',
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'tabs_btn_primary' );

		$this->start_controls_tab( 'tab_btn_primary_normal', array( 'label' => 'Normal' ) );
		$this->add_control(
			'btn_primary_bg',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-btn-primary' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_primary_color',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array( '{{WRAPPER}} .lk-btn-primary' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_btn_primary_hover', array( 'label' => 'Hover' ) );
		$this->add_control(
			'btn_primary_bg_hover',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array( '{{WRAPPER}} .lk-btn-primary:hover' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_primary_color_hover',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-btn-primary:hover' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'heading_btn_secondary',
			array(
				'label'     => 'Secondary Button',
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'tabs_btn_secondary' );

		$this->start_controls_tab( 'tab_btn_secondary_normal', array( 'label' => 'Normal' ) );
		$this->add_control(
			'btn_secondary_border',
			array(
				'label'     => 'Border colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-btn-secondary' => 'border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_secondary_color',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-btn-secondary' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_btn_secondary_hover', array( 'label' => 'Hover' ) );
		$this->add_control(
			'btn_secondary_bg_hover',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-btn-secondary:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'btn_secondary_color_hover',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array( '{{WRAPPER}} .lk-btn-secondary:hover' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_gap',
			array(
				'label'     => 'Gap between buttons',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 16 ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .lk-button-row' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text Link (underlink)
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_underlink',
			array(
				'label'     => 'Text Link',
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_underlink' => 'yes' ),
			)
		);

		$this->add_control(
			'underlink_color',
			array(
				'label'     => 'Colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-underlink' => 'color: {{VALUE}}; border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'underlink_color_hover',
			array(
				'label'       => 'Hover colour',
				'type'        => Controls_Manager::COLOR,
				'default'     => '#692137',
				'description' => 'The reference keeps the same colour on hover — only the arrow shifts. Change this if you want a hover colour shift too.',
				'selectors'   => array( '{{WRAPPER}} .lk-underlink:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'underlink_typography',
				'selector' => '{{WRAPPER}} .lk-underlink',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 11.5 ) ),
					'font_weight'    => array( 'default' => '600' ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.1 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);

		$this->add_responsive_control(
			'underlink_spacing_top',
			array(
				'label'     => 'Spacing above',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 22 ),
				'selectors' => array(
					'{{WRAPPER}} .lk-underlink' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Image & Caption
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => 'Image & Caption',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_min_height',
			array(
				'label'       => 'Image column height',
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 200, 'max' => 900 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 680 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 520 ),
				'mobile_default'  => array( 'unit' => 'px', 'size' => 430 ),
				'description' => 'Matches the reference exactly: 680px desktop → 520px tablet → 430px mobile. The image fills this box completely (cropped to fit), stretching to match whichever is taller: this value, or the text column\'s natural height.',
				'selectors'   => array(
					'{{WRAPPER}} .lk-hero-image' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_object_position',
			array(
				'label'       => 'Image focal point',
				'type'        => Controls_Manager::TEXT,
				'default'     => '54% center',
				'tablet_default' => '58% center',
				'description' => 'CSS object-position — matches the reference\'s 54% center desktop, 58% center on tablet/mobile, so the subject stays framed as the crop narrows.',
				'selectors'   => array(
					'{{WRAPPER}} .lk-hero-image img' => 'object-position: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hover_zoom',
			array(
				'label'     => 'Zoom image on hover',
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => 'On',
				'label_off' => 'Off',
				'default'   => 'yes',
			)
		);
		$this->add_control(
			'hover_zoom_scale',
			array(
				'label'     => 'Hover zoom scale',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 1, 'max' => 1.15, 'step' => 0.005 ) ),
				'default'   => array( 'size' => 1.025 ),
				'condition' => array( 'hover_zoom' => 'yes' ),
				'selectors' => array(
					'{{WRAPPER}} .lk-hero-image:hover img' => 'transform: scale({{SIZE}});',
				),
			)
		);

		$this->add_control(
			'image_radius',
			array(
				'label'      => 'Image corner radius',
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0 ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-hero-image img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .lk-hero-image img',
			)
		);

		$this->add_control(
			'heading_caption_style',
			array(
				'label'     => 'Caption Overlay',
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_caption' => 'yes' ),
			)
		);

		$this->add_control(
			'caption_number_color',
			array(
				'label'     => 'Number colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'condition' => array( 'show_caption' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .lk-caption-number' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'caption_text_color',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'condition' => array( 'show_caption' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .lk-caption-text' => 'color: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'btn_primary', 'class', array( 'lk-btn', 'lk-btn-primary' ) );
		$this->add_link_attributes( 'btn_primary', $s['btn_primary_link'] );

		$this->add_render_attribute( 'btn_secondary', 'class', array( 'lk-btn', 'lk-btn-secondary' ) );
		$this->add_link_attributes( 'btn_secondary', $s['btn_secondary_link'] );

		if ( 'yes' === $s['show_underlink'] ) {
			$this->add_render_attribute( 'underlink', 'class', 'lk-underlink' );
			$this->add_link_attributes( 'underlink', $s['underlink_link'] );
		}

		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'hero_image', 'hero_image' );
		?>
		<div class="lk-hero">
			<div class="lk-hero-copy">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
					<p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>

				<h1 class="lk-heading">
					<?php echo esc_html( $s['heading_main'] ); ?><br>
					<em><?php echo esc_html( $s['heading_emphasis'] ); ?></em>
				</h1>

				<?php if ( ! empty( $s['intro'] ) ) : ?>
					<p class="lk-intro"><?php echo esc_html( $s['intro'] ); ?></p>
				<?php endif; ?>

				<div class="lk-button-row">
					<?php if ( ! empty( $s['btn_primary_text'] ) ) : ?>
						<a <?php echo $this->get_render_attribute_string( 'btn_primary' ); ?>>
							<?php echo esc_html( $s['btn_primary_text'] ); ?>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $s['btn_secondary_text'] ) ) : ?>
						<a <?php echo $this->get_render_attribute_string( 'btn_secondary' ); ?>>
							<?php echo esc_html( $s['btn_secondary_text'] ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( 'yes' === $s['show_underlink'] && ! empty( $s['underlink_text'] ) ) : ?>
					<a <?php echo $this->get_render_attribute_string( 'underlink' ); ?>>
						<?php echo esc_html( $s['underlink_text'] ); ?> <span>↗</span>
					</a>
				<?php endif; ?>
			</div>

			<figure class="lk-hero-image">
				<?php echo $image_html; ?>
				<?php if ( 'yes' === $s['show_caption'] ) : ?>
					<figcaption class="lk-caption">
						<span class="lk-caption-number"><?php echo esc_html( $s['caption_number'] ); ?></span>
						<span class="lk-caption-text"><?php echo esc_html( $s['caption_text'] ); ?></span>
					</figcaption>
				<?php endif; ?>
			</figure>
		</div>
		<?php
	}
}
