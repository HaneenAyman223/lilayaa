<?php
/**
 * Lila Kora — How It Works Widget
 *
 * The dark burgundy "process-section": a marker line (How It Works /
 * The Canvas-to-Scarf Process), a two-column intro (3-line heading +
 * italic subtitle/lead), a 4-step image grid, and a closing signoff
 * line ("Your artwork. Your colours. Your scarf.").
 *
 * Self-contained — this single file has no dependencies beyond the
 * shared lk-elementor-widgets.css already enqueued by the loader.
 *
 * To wire it in, add these two lines to class-lk-widgets-loader.php's
 * register_widgets() method, alongside the other widgets:
 *
 *   require_once __DIR__ . '/widgets/class-lk-how-it-works-widget.php';
 *   $widgets_manager->register( new \LK_How_It_Works_Widget() );
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

class LK_How_It_Works_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-how-it-works';
	}

	public function get_title() {
		return 'LK — How It Works';
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'process', 'steps', 'how it works' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Marker & Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array(
				'label' => 'Marker & Heading',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'marker_number',
			array(
				'label'       => 'Marker — big label',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'How It Works',
				'label_block' => true,
			)
		);
		$this->add_control(
			'marker_label',
			array(
				'label'       => 'Marker — small caption',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'The Canvas-to-Scarf Process',
				'label_block' => true,
			)
		);

		$this->add_control(
			'heading_divider',
			array( 'type' => Controls_Manager::DIVIDER )
		);

		$this->add_control(
			'heading_line1',
			array(
				'label'       => 'Heading — line 1',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'From a blank canvas',
				'label_block' => true,
			)
		);
		$this->add_control(
			'heading_line2',
			array(
				'label'       => 'Heading — line 2',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'to a scarf unlike',
				'label_block' => true,
			)
		);
		$this->add_control(
			'heading_emphasis',
			array(
				'label'       => 'Heading — emphasized line',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'any other.',
				'label_block' => true,
			)
		);

		$this->add_control(
			'content_divider',
			array( 'type' => Controls_Manager::DIVIDER )
		);

		$this->add_control(
			'subtitle_line1',
			array(
				'label'       => 'Subtitle — line 1',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'From canvas to silk scarf',
				'label_block' => true,
			)
		);
		$this->add_control(
			'subtitle_line2',
			array(
				'label'       => 'Subtitle — line 2',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'in four simple steps.',
				'label_block' => true,
			)
		);
		$this->add_control(
			'lead',
			array(
				'label'       => 'Lead paragraph',
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => 'No art experience is needed. There is no right or wrong way to begin. Just space to pause, create and turn what you feel into something you can wear.',
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Steps
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_steps',
			array(
				'label' => 'Steps',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label'   => 'Image',
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);
		$repeater->add_control(
			'number',
			array(
				'label'       => 'Number',
				'type'        => Controls_Manager::TEXT,
				'default'     => '01',
				'label_block' => false,
			)
		);
		$repeater->add_control(
			'title',
			array(
				'label'       => 'Title',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Step title',
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'description',
			array(
				'label'       => 'Description',
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => 'Step description.',
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'link_text',
			array(
				'label'       => 'Link text (optional)',
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Renders as a plain underlined text link below the description — not a button.',
			)
		);
		$repeater->add_control(
			'link_url',
			array(
				'label'         => 'Link URL',
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => '#' ),
				'show_external' => true,
				'label_block'   => true,
				'condition'     => array( 'link_text!' => '' ),
			)
		);

		$this->add_control(
			'steps',
			array(
				'label'       => 'Steps',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'number'      => '01',
						'title'       => 'Begin Your Experience',
						'description' => 'Create at home with the Experience Box or join one of our guided workshops. Everything you need to begin is prepared for you.',
						'link_text'   => 'Explore the Experience Box',
						'link_url'    => array( 'url' => './experience-box.html' ),
					),
					array(
						'number'      => '02',
						'title'       => 'Create Your Design',
						'description' => 'Paint freely. Follow your intuition through flowing lines, softened intersections and colour. No pressure and no art experience needed.',
						'link_text'   => 'See designs of other women',
						'link_url'    => array( 'url' => '#gallery' ),
					),
					array(
						'number'      => '03',
						'title'       => 'Send Us Your Artwork',
						'description' => 'Send us a photo of your finished canvas. Within five working days, we digitize and refine it, then share the design with you for approval.',
						'link_text'   => 'See transformation: Canvas → Digital Artwork → Scarf',
						'link_url'    => array( 'url' => '#transformation' ),
					),
					array(
						'number'      => '04',
						'title'       => 'Receive Your One-of-a-Kind Scarf',
						'description' => 'Once approved, your design is crafted into a premium silk scarf with hand-rolled edges and delivered to your home within 20 working days.',
					),
				),
				'title_field' => '{{{ number }}} — {{{ title }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Signoff
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_signoff',
			array(
				'label' => 'Signoff',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_signoff',
			array(
				'label'     => 'Show signoff line',
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => 'Show',
				'label_off' => 'Hide',
				'default'   => 'yes',
			)
		);
		$this->add_control(
			'signoff_text',
			array(
				'label'       => 'Signoff text',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Your artwork. Your colours. Your scarf.',
				'label_block' => true,
				'condition'   => array( 'show_signoff' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Section
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_section',
			array(
				'label' => 'Section',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-how' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label'     => 'Max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px, matching every other section on the site. Set your outer Elementor container to full-width / 0 padding and let this control the actual width.',
				'selectors' => array(
					'{{WRAPPER}} .lk-how' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'      => 'Padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'vw' ),
				'default'    => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'mobile_default'  => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'description' => 'Reference: clamp(90px, 10vw, 155px) 4.5vw desktop → 90px 24px at 820px and below.',
				'selectors'  => array(
					'{{WRAPPER}} .lk-how' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Marker
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_marker',
			array(
				'label' => 'Marker',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'marker_border_color',
			array(
				'label'     => 'Divider colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.3)',
				'selectors' => array( '{{WRAPPER}} .lk-how-marker' => 'border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'marker_number_color',
			array(
				'label'     => 'Big label colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'selectors' => array( '{{WRAPPER}} .lk-how-marker span' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'marker_number_typography',
				'selector' => '{{WRAPPER}} .lk-how-marker span',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array(
						'default' => array( 'unit' => 'px', 'size' => 88 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 64 ),
						'mobile_default'  => array( 'unit' => 'px', 'size' => 48 ),
					),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 0.82 ) ),
				),
			)
		);
		$this->add_control(
			'marker_label_color',
			array(
				'label'     => 'Small caption colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.7)',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-how-marker small' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'marker_label_typography',
				'selector' => '{{WRAPPER}} .lk-how-marker small',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 10.9 ) ),
					'font_weight'    => array( 'default' => '600' ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.15 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);
		$this->add_responsive_control(
			'marker_spacing',
			array(
				'label'     => 'Spacing below',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 54 ),
				'mobile_default' => array( 'unit' => 'px', 'size' => 42 ),
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-how-marker' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Intro (Heading / Subtitle / Lead)
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_intro',
			array(
				'label' => 'Heading, Subtitle & Lead',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'intro_gap',
			array(
				'label'      => 'Gap between heading & content',
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ), 'vw' => array( 'min' => 0, 'max' => 15 ) ),
				'default'    => array( 'unit' => 'vw', 'size' => 9 ),
				'selectors'  => array( '{{WRAPPER}} .lk-how-intro' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'intro_spacing',
			array(
				'label'     => 'Spacing below',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 70 ),
				'mobile_default' => array( 'unit' => 'px', 'size' => 58 ),
				'selectors' => array( '{{WRAPPER}} .lk-how-intro' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => 'Heading colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-how-heading' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'heading_emphasis_color',
			array(
				'label'     => 'Emphasized line colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'selectors' => array( '{{WRAPPER}} .lk-how-heading em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .lk-how-heading',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array(
						'default' => array( 'unit' => 'px', 'size' => 56 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 42 ),
						'mobile_default'  => array( 'unit' => 'px', 'size' => 34 ),
					),
					'font_weight'    => array( 'default' => '400' ),
					'line_height'    => array( 'default' => array( 'unit' => 'em', 'size' => 0.98 ) ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => -0.025 ) ),
				),
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => 'Subtitle colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-how-subtitle' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .lk-how-subtitle',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array(
						'default' => array( 'unit' => 'px', 'size' => 44 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 32 ),
					),
					'font_style'  => array( 'default' => 'italic' ),
					'font_weight' => array( 'default' => '400' ),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.08 ) ),
				),
			)
		);
		$this->add_responsive_control(
			'subtitle_spacing',
			array(
				'label'     => 'Spacing below',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 24 ),
				'selectors' => array( '{{WRAPPER}} .lk-how-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'lead_color',
			array(
				'label'     => 'Lead paragraph colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.72)',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-how-lead' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'lead_typography',
				'selector' => '{{WRAPPER}} .lk-how-lead',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 23.2 ) ),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.5 ) ),
				),
			)
		);
		$this->add_responsive_control(
			'lead_max_width',
			array(
				'label'     => 'Lead max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 300, 'max' => 900 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 700 ),
				'selectors' => array( '{{WRAPPER}} .lk-how-lead' => 'max-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Steps Grid
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_steps',
			array(
				'label' => 'Steps Grid',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'steps_columns',
			array(
				'label'   => 'Columns',
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
				'selectors' => array(
					'{{WRAPPER}} .lk-how-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				),
			)
		);
		$this->add_responsive_control(
			'steps_row_gap',
			array(
				'label'     => 'Row gap (when stacked to 2 or 1 columns)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 24 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 55 ),
				'selectors' => array(
					'{{WRAPPER}} .lk-how-grid' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'steps_gap',
			array(
				'label'     => 'Column gap',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 26 ),
				'selectors' => array( '{{WRAPPER}} .lk-how-grid' => 'column-gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'step_image_radius',
			array(
				'label'     => 'Image corner radius',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 0 ),
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-how-step img' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'step_number_color',
			array(
				'label'     => 'Number colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF715E',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-how-step-number' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'step_number_typography',
				'selector' => '{{WRAPPER}} .lk-how-step-number',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16.8 ) ),
				),
			)
		);

		$this->add_control(
			'step_title_color',
			array(
				'label'     => 'Title colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-how-step h3' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'step_title_typography',
				'selector' => '{{WRAPPER}} .lk-how-step h3',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array(
						'default' => array( 'unit' => 'px', 'size' => 33 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 26 ),
					),
					'font_weight' => array( 'default' => '500' ),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.1 ) ),
				),
			)
		);

		$this->add_control(
			'step_desc_color',
			array(
				'label'     => 'Description colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.68)',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-how-step p' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'step_desc_typography',
				'selector' => '{{WRAPPER}} .lk-how-step p',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Montserrat' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 13.8 ) ),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.6 ) ),
				),
			)
		);

		$this->add_control(
			'step_link_color',
			array(
				'label'     => 'Link colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-how-step-link' => 'color: {{VALUE}}; border-color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'step_link_typography',
				'selector' => '{{WRAPPER}} .lk-how-step-link',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Montserrat' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 12.5 ) ),
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Signoff
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_signoff',
			array(
				'label'     => 'Signoff',
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_signoff' => 'yes' ),
			)
		);

		$this->add_control(
			'signoff_color',
			array(
				'label'     => 'Colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'selectors' => array( '{{WRAPPER}} .lk-how-signoff' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'signoff_typography',
				'selector' => '{{WRAPPER}} .lk-how-signoff',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 28 ) ),
					'font_style'  => array( 'default' => 'italic' ),
				),
			)
		);
		$this->add_responsive_control(
			'signoff_spacing',
			array(
				'label'     => 'Spacing above',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 35 ),
				'selectors' => array( '{{WRAPPER}} .lk-how-signoff' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="lk-how">
			<div class="lk-how-marker">
				<span><?php echo esc_html( $s['marker_number'] ); ?></span>
				<small><?php echo esc_html( $s['marker_label'] ); ?></small>
			</div>

			<?php $has_heading = ! empty( $s['heading_line1'] ) || ! empty( $s['heading_line2'] ) || ! empty( $s['heading_emphasis'] ); ?>
			<div class="lk-how-intro<?php echo $has_heading ? '' : ' lk-how-no-title'; ?>">
				<?php if ( $has_heading ) : ?>
					<div class="lk-how-title">
						<h2 class="lk-how-heading">
							<?php echo esc_html( $s['heading_line1'] ); ?><br>
							<?php echo esc_html( $s['heading_line2'] ); ?><br>
							<em><?php echo esc_html( $s['heading_emphasis'] ); ?></em>
						</h2>
					</div>
				<?php endif; ?>
				<div class="lk-how-content">
					<h3 class="lk-how-subtitle">
						<?php echo esc_html( $s['subtitle_line1'] ); ?><br>
						<?php echo esc_html( $s['subtitle_line2'] ); ?>
					</h3>
					<?php if ( ! empty( $s['lead'] ) ) : ?>
						<p class="lk-how-lead"><?php echo esc_html( $s['lead'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="lk-how-grid">
				<?php foreach ( $s['steps'] as $index => $step ) :
					$image_html = Group_Control_Image_Size::get_attachment_image_html( $step, 'image' );
					?>
					<article class="lk-how-step">
						<?php echo $image_html; ?>
						<div>
							<span class="lk-how-step-number"><?php echo esc_html( $step['number'] ); ?></span>
							<h3><?php echo esc_html( $step['title'] ); ?></h3>
							<p><?php echo esc_html( $step['description'] ); ?></p>
							<?php if ( ! empty( $step['link_text'] ) ) :
								$key = 'step_link_' . $index;
								$this->add_render_attribute( $key, 'class', 'lk-how-step-link' );
								$this->add_link_attributes( $key, $step['link_url'] );
								?>
								<a <?php echo $this->get_render_attribute_string( $key ); ?>><?php echo esc_html( $step['link_text'] ); ?></a>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<?php if ( 'yes' === $s['show_signoff'] && ! empty( $s['signoff_text'] ) ) : ?>
				<p class="lk-how-signoff"><?php echo esc_html( $s['signoff_text'] ); ?></p>
			<?php endif; ?>
		</section>

		<style>
			.lk-how-marker { display: flex; align-items: flex-end; justify-content: space-between; gap: 30px; padding-bottom: 24px; border-bottom: 1px solid; }
			.lk-how-marker span, .lk-how-marker small { margin: 0; }
			.lk-how-intro { display: grid; grid-template-columns: 0.84fr 1.16fr; }
			.lk-how-intro.lk-how-no-title { grid-template-columns: 1fr; }
			.lk-how-content { padding-top: 4px; }
			.lk-how-heading, .lk-how-subtitle, .lk-how-lead { margin: 0; font-style: normal; }
			.lk-how-heading em, .lk-how-subtitle { font-style: italic; }
			.lk-how-grid { display: grid; }
			.lk-how-step img { width: 100%; aspect-ratio: 1; object-fit: cover; display: block; }
			.lk-how-step > div { padding-top: 24px; }
			.lk-how-step-number { display: block; margin-bottom: 8px; font-style: normal; }
			.lk-how-step h3 { margin: 0 0 14px; }
			.lk-how-step p { margin: 0; }
			.lk-how-step-link { display: inline-block; margin-top: 12px; text-decoration: none; border-bottom: 1px solid currentColor; padding-bottom: 1px; }
			.lk-how-signoff { margin: 0; font-style: italic; }
			@media (max-width: 820px) {
				.lk-how-intro { grid-template-columns: 1fr; gap: 58px; }
			}
			@media (max-width: 767px) {
				.lk-how-step h3 { min-height: 0 !important; }
			}
		</style>
		<?php
	}
}
