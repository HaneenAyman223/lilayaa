<?php
/**
 * Lila Kora — Experience Grid Widget
 *
 * Reproduces the "How would you like to create?" section: a centred
 * heading, then a repeater-driven grid of numbered cards (Public
 * Workshops / Experience Box / Private Events / Brand & Corporate),
 * each with its own eyebrow, title, description, link and accent colour.
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
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class LK_Experience_Grid_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-experience-grid';
	}

	public function get_title() {
		return 'LK — Experience Grid';
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'grid', 'cards', 'experiences' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Section Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array(
				'label' => 'Section Heading',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => 'Eyebrow',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Create your own Lila Kora',
				'label_block' => true,
			)
		);
		$this->add_control(
			'heading_main',
			array(
				'label'       => 'Heading',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'How would you like to',
				'label_block' => true,
			)
		);
		$this->add_control(
			'heading_emphasis',
			array(
				'label'       => 'Heading — emphasized word(s)',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'create?',
				'label_block' => true,
			)
		);
		$this->add_control(
			'description',
			array(
				'label'       => 'Description',
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => 'For those who want to take part in the story, begin with a blank canvas and choose the setting that feels right.',
				'rows'        => 3,
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Cards Repeater
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_cards',
			array(
				'label' => 'Cards',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

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
			'eyebrow',
			array(
				'label'       => 'Eyebrow',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'For yourself',
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'title',
			array(
				'label'       => 'Title',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Public Workshops',
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'description',
			array(
				'label'       => 'Description',
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => 'Create in a guided, social setting in Dubai. No experience required.',
				'rows'        => 3,
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'link_text',
			array(
				'label'       => 'Link text',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'View workshops',
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'link_url',
			array(
				'label'         => 'Link URL',
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => './public-workshops.html' ),
				'show_external' => true,
				'label_block'   => true,
			)
		);
		$repeater->add_control(
			'accent_color',
			array(
				'label'   => 'Number colour',
				'type'    => Controls_Manager::COLOR,
				'default' => '#8F8584',
			)
		);

		$this->add_control(
			'cards',
			array(
				'label'       => 'Cards',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'number'      => '01',
						'eyebrow'     => 'For yourself',
						'title'       => 'Public Workshops',
						'description' => 'Create in a guided, social setting in Dubai. No experience required.',
						'link_text'   => 'View workshops',
						'link_url'    => array( 'url' => './public-workshops.html' ),
						'accent_color'=> '#8F8584',
					),
					array(
						'number'      => '02',
						'eyebrow'     => 'At home',
						'title'       => 'Experience Box',
						'description' => 'Create at your own pace, then have your artwork transformed into your scarf.',
						'link_text'   => 'Explore the box',
						'link_url'    => array( 'url' => './experience-box.html' ),
						'accent_color'=> '#8F8584',
					),
					array(
						'number'      => '03',
						'eyebrow'     => 'For your people',
						'title'       => 'Private Events',
						'description' => 'A meaningful creative experience for birthdays, bridal showers and intimate gatherings.',
						'link_text'   => 'Plan a private event',
						'link_url'    => array( 'url' => './private-workshops.html' ),
						'accent_color'=> '#8F8584',
					),
					array(
						'number'      => '04',
						'eyebrow'     => 'For your brand or team',
						'title'       => 'Brand & Corporate',
						'description' => 'A distinctive activation for client communities, teams and corporate gifting.',
						'link_text'   => 'Explore business experiences',
						'link_url'    => array( 'url' => '#enquire' ),
						'accent_color'=> '#8F8584',
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Section Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_heading',
			array(
				'label' => 'Section Heading',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_align',
			array(
				'label'   => 'Alignment',
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'   => array( 'title' => 'Left', 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => 'Center', 'icon' => 'eicon-text-align-center' ),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .lk-grid-heading' => 'text-align: {{VALUE}}; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'heading_max_width',
			array(
				'label'     => 'Max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 300, 'max' => 900 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 620 ),
				'selectors' => array(
					'{{WRAPPER}} .lk-grid-heading' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => 'Eyebrow colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF715E',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-grid-heading .lk-eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .lk-grid-heading .lk-eyebrow',
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
				'selectors' => array( '{{WRAPPER}} .lk-grid-heading h2' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'heading_emphasis_color',
			array(
				'label'       => 'Emphasized word colour',
				'type'        => Controls_Manager::COLOR,
				'default'     => '#57282D',
				'description' => 'The reference keeps this the same ink colour — only italic changes.',
				'selectors'   => array( '{{WRAPPER}} .lk-grid-heading h2 em' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .lk-grid-heading h2',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array(
						'default' => array( 'unit' => 'px', 'size' => 62 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 46 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 34 ),
					),
					'font_weight'    => array( 'default' => '400' ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => -0.025 ) ),
				),
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => 'Description colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8F8584',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-grid-heading p.lk-desc' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .lk-grid-heading p.lk-desc',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Montserrat' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
				),
			)
		);

		$this->add_responsive_control(
			'heading_bottom_spacing',
			array(
				'label'     => 'Spacing below section heading',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 56 ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .lk-grid-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Grid Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_grid',
			array(
				'label' => 'Grid Layout',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'section_max_width',
			array(
				'label'     => 'Max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'The reference\'s generic .section wrapper caps every section at 1440px and centres it. Set your outer Elementor container to full-width / 0 padding and let this control the actual width.',
				'selectors' => array(
					'{{WRAPPER}} .lk-experience-grid-wrap' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'section_padding',
			array(
				'label'      => 'Section padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'vw' ),
				'default'    => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 32, 'bottom' => 90, 'left' => 32, 'unit' => 'px' ),
				'mobile_default'  => array( 'top' => 90, 'right' => 20, 'bottom' => 90, 'left' => 20, 'unit' => 'px' ),
				'description' => 'Reference: clamp(90px, 10vw, 155px) 4.5vw. The desktop default here (120px / 65px) approximates that clamp at a typical 1440px width.',
				'selectors'  => array(
					'{{WRAPPER}} .lk-experience-grid-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'   => 'Columns',
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors' => array(
					'{{WRAPPER}} .lk-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'     => 'Gap',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 0 ),
				'selectors' => array(
					'{{WRAPPER}} .lk-grid' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Cards
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_cards',
			array(
				'label' => 'Cards',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_card_style' );

		$this->start_controls_tab( 'tab_card_normal', array( 'label' => 'Normal' ) );
		$this->add_control(
			'card_bg',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFDFB',
				'selectors' => array( '{{WRAPPER}} .lk-card' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .lk-card',
				'fields_options' => array(
					'width'  => array( 'default' => array( 'top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1, 'unit' => 'px', 'isLinked' => true ) ),
					'color'  => array( 'default' => 'rgba(105,33,55,0.16)' ),
					'border' => array( 'default' => 'solid' ),
				),
			)
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_card_hover', array( 'label' => 'Hover' ) );
		$this->add_control(
			'card_bg_hover',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-card:hover' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_title_color_hover',
			array(
				'label'     => 'Title colour on hover',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array( '{{WRAPPER}} .lk-card:hover h3' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'card_text_color_hover',
			array(
				'label'       => 'Eyebrow & link colour on hover',
				'type'        => Controls_Manager::COLOR,
				'default'     => '#FFC5BA',
				'description' => 'The reference keeps the title plain white on hover and reserves this accent colour for the eyebrow and the link only.',
				'selectors'   => array(
					'{{WRAPPER}} .lk-card:hover .lk-eyebrow'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .lk-card:hover p.lk-card-desc'=> 'color: rgba(255,255,255,0.76);',
					'{{WRAPPER}} .lk-card:hover .lk-card-link' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'card_translate_hover',
			array(
				'label'     => 'Lift on hover (px)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 20 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 5 ),
				'selectors' => array(
					'{{WRAPPER}} .lk-card:hover' => 'transform: translateY(-{{SIZE}}{{UNIT}});',
				),
			)
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .lk-card',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'     => 'Corner radius',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 0 ),
				'selectors' => array( '{{WRAPPER}} .lk-card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => 'Padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 40, 'right' => 34, 'bottom' => 40, 'left' => 34, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'transition_duration',
			array(
				'label'     => 'Transition speed (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 100, 'max' => 800, 'step' => 50 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 300 ),
				'selectors' => array(
					'{{WRAPPER}} .lk-card' => 'transition: all {{SIZE}}ms ease;',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Card Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_card_text',
			array(
				'label' => 'Card Text',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_number_note',
			array(
				'label'     => 'Number colour',
				'type'      => Controls_Manager::HEADING,
				'description' => 'Set per-card in the Content tab (each card has its own "Accent colour" field).',
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'card_number_typography',
				'selector'  => '{{WRAPPER}} .lk-card-number',
				'separator' => 'before',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 15 ) ),
					'font_weight' => array( 'default' => '600' ),
				),
			)
		);

		$this->add_control(
			'card_eyebrow_color',
			array(
				'label'     => 'Card eyebrow colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8F8584',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-card .lk-eyebrow' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_eyebrow_typography',
				'selector' => '{{WRAPPER}} .lk-card .lk-eyebrow',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 17.6 ) ),
					'font_style'  => array( 'default' => 'normal' ),
				),
			)
		);

		$this->add_control(
			'card_title_color',
			array(
				'label'     => 'Card title colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#57282D',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-card h3' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_title_typography',
				'selector' => '{{WRAPPER}} .lk-card h3',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 33 ) ),
					'font_weight' => array( 'default' => '500' ),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1 ) ),
				),
			)
		);

		$this->add_control(
			'card_desc_color',
			array(
				'label'     => 'Card description colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8F8584',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-card p.lk-card-desc' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_desc_typography',
				'selector' => '{{WRAPPER}} .lk-card p.lk-card-desc',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Montserrat' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
					'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.6 ) ),
				),
			)
		);

		$this->add_control(
			'card_link_color',
			array(
				'label'     => 'Card link colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-card-link' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_link_typography',
				'selector' => '{{WRAPPER}} .lk-card-link',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 10.5 ) ),
					'font_weight'    => array( 'default' => '600' ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.1 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);
		$this->add_control(
			'card_link_border_note',
			array(
				'type'        => Controls_Manager::HEADING,
				'label'       => 'Note',
				'description' => 'The reference draws a top border above this link, coloured the same as the link text, with the link stretched full-width. That is baked into the base CSS.',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="lk-experience-grid-wrap">
			<div class="lk-grid-heading">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?>
					<p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>
				<h2>
					<?php echo esc_html( $s['heading_main'] ); ?> <em><?php echo esc_html( $s['heading_emphasis'] ); ?></em>
				</h2>
				<?php if ( ! empty( $s['description'] ) ) : ?>
					<p class="lk-desc"><?php echo esc_html( $s['description'] ); ?></p>
				<?php endif; ?>
			</div>

			<div class="lk-grid">
				<?php foreach ( $s['cards'] as $index => $card ) :
					$link_key = 'link_' . $index;
					$this->add_render_attribute( $link_key, 'class', 'lk-card-link' );
					if ( ! empty( $card['link_url']['url'] ) ) {
						$this->add_link_attributes( $link_key, $card['link_url'] );
					} else {
						$this->add_render_attribute( $link_key, 'href', '#' );
					}
					$accent = ! empty( $card['accent_color'] ) ? $card['accent_color'] : '#E8674C';
					?>
					<article class="lk-card" style="--lk-card-accent: <?php echo esc_attr( $accent ); ?>;">
						<span class="lk-card-number"><?php echo esc_html( $card['number'] ); ?></span>
						<?php if ( ! empty( $card['eyebrow'] ) ) : ?>
							<p class="lk-eyebrow"><?php echo esc_html( $card['eyebrow'] ); ?></p>
						<?php endif; ?>
						<h3><?php echo esc_html( $card['title'] ); ?></h3>
						<?php if ( ! empty( $card['description'] ) ) : ?>
							<p class="lk-card-desc"><?php echo esc_html( $card['description'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $card['link_text'] ) ) : ?>
							<a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
								<?php echo esc_html( $card['link_text'] ); ?> <span>↗</span>
							</a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
