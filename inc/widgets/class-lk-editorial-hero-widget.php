<?php
/**
 * Lila Kora — Editorial Hero Widget
 *
 * The split hero used on the Public Workshops page — and, with different
 * content, on Private Events and Contact: eyebrow, large serif heading with an
 * italic line, a serif lead paragraph, a sans paragraph, an optional row of
 * "facts" (label + value), a solid button plus either a second outlined button
 * or an underlined text link with an arrow, and a full-height image (with an
 * optional italic caption card).
 *
 * Defaults reproduce the Public Workshops hero: text column narrower than the
 * image (43 / 57), no facts row, no caption, underlined "See What to Expect ↘".
 *
 * The strip of short promises that sits directly under this hero on the
 * reference ("No art experience needed · All materials included · …") is the
 * existing LK — Proof Strip widget.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-editorial-hero-widget.php';
 *   $widgets_manager->register( new \LK_Editorial_Hero_Widget() );
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

class LK_Editorial_Hero_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-editorial-hero';
	}

	public function get_title() {
		return 'LK — Editorial Hero';
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'hero', 'banner', 'workshop', 'editorial', 'header' );
	}

	/* ---------------------------------------------------------------------
	 * Helpers to keep the long style panel readable
	 * -------------------------------------------------------------------*/

	private function typo( $name, $label, $selector, $family, $size, $weight = '', $spacing = null, $upper = false, $line = null, $tablet = null, $mobile = null, $italic = false ) {
		$size_opt = array( 'default' => array( 'unit' => 'px', 'size' => $size ) );
		if ( null !== $tablet ) {
			$size_opt['tablet_default'] = array( 'unit' => 'px', 'size' => $tablet );
		}
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
		if ( $italic ) {
			$fields['font_style'] = array( 'default' => 'italic' );
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

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section( 'section_content_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Public workshops in Dubai', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Design Your Own', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic line', 'type' => Controls_Manager::TEXT, 'default' => 'Scarf Workshop', 'label_block' => true ) );
		$this->add_control( 'heading_tag', array( 'label' => 'Heading tag', 'type' => Controls_Manager::SELECT, 'default' => 'h1', 'options' => array( 'h1' => 'H1 (use for the page\'s main hero)', 'h2' => 'H2' ), 'description' => 'A page should have exactly one H1.' ) );
		$this->add_control( 'lead', array( 'label' => 'Lead paragraph (serif, optional)', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Create your own scarf design on canvas through a guided, intuitive painting experience, with the option to transform it into a one-of-a-kind scarf.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => 'No art experience is needed. Come on your own or with someone you love. Everything required to create is prepared for you.', 'label_block' => true ) );
		$this->add_control( 'note', array( 'label' => 'Italic note (below buttons, optional)', 'type' => Controls_Manager::TEXT, 'default' => '', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Facts
		 * =======================================================*/
		$this->start_controls_section( 'section_content_facts', array( 'label' => 'Facts Row', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'show_facts', array( 'label' => 'Show facts row', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => '' ) );

		$facts = new Repeater();
		$facts->add_control( 'label', array( 'label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Label', 'label_block' => true ) );
		$facts->add_control( 'value', array( 'label' => 'Value', 'type' => Controls_Manager::TEXT, 'default' => 'Value', 'label_block' => true ) );

		$this->add_control(
			'facts',
			array(
				'label'       => 'Facts',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $facts->get_controls(),
				'default'     => array(
					array( 'label' => 'Workshop ticket', 'value' => 'AED 190' ),
					array( 'label' => 'Scarf transformation', 'value' => 'From AED 250' ),
				),
				'title_field' => '{{{ label }}} — {{{ value }}}',
				'condition'   => array( 'show_facts' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Buttons
		 * =======================================================*/
		$this->start_controls_section( 'section_content_buttons', array( 'label' => 'Buttons', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'btn_primary_text', array( 'label' => 'Primary button — text', 'type' => Controls_Manager::TEXT, 'default' => 'Choose a Workshop', 'label_block' => true ) );
		$this->add_control( 'btn_primary_link', array( 'label' => 'Primary button — link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#upcoming' ), 'show_external' => true ) );
		$this->add_control( 'btn_secondary_text', array( 'label' => 'Second action — text', 'type' => Controls_Manager::TEXT, 'default' => 'See What to Expect', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'btn_secondary_link', array( 'label' => 'Second action — link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#experience' ), 'show_external' => true ) );
		$this->add_control( 'btn_secondary_style', array( 'label' => 'Second action looks like', 'type' => Controls_Manager::SELECT, 'default' => 'link', 'options' => array( 'link' => 'Underlined text link with an arrow', 'outline' => 'Outlined button' ) ) );
		$this->add_control( 'btn_secondary_arrow', array( 'label' => 'Arrow', 'type' => Controls_Manager::TEXT, 'default' => '↘', 'description' => '↘ points down the page (to a section below), ↗ opens something outside. Leave empty for no arrow.', 'condition' => array( 'btn_secondary_style' => 'link' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Image
		 * =======================================================*/
		$this->start_controls_section( 'section_content_image', array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image', 'default' => 'large' ) );
		$this->add_control( 'image_caption', array( 'label' => 'Caption', 'type' => Controls_Manager::TEXT, 'default' => '', 'label_block' => true, 'description' => 'Leave empty for no caption card.' ) );
		$this->add_control( 'image_position', array( 'label' => 'Image side', 'type' => Controls_Manager::SELECT, 'default' => 'right', 'options' => array( 'right' => 'Right', 'left' => 'Left' ), 'prefix_class' => 'lk-edhero-img-' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'bg_color', 'Background', '.lk-edhero', '#F8ECE9', 'background-color' );
		$this->add_responsive_control(
			'hero_max_width',
			array(
				'label'     => 'Max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 1440 ),
				'selectors' => array( '{{WRAPPER}} .lk-edhero' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'hero_min_height',
			array(
				'label'       => 'Min height',
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 400, 'max' => 1100 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 650 ),
				'description' => 'Ignored once the layout stacks (tablet / phone).',
				'selectors'   => array( '{{WRAPPER}} .lk-edhero' => 'min-height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'text_share',
			array(
				'label'       => 'Text column width (%)',
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'range'       => array( '%' => array( 'min' => 30, 'max' => 70 ) ),
				'default'     => array( 'unit' => '%', 'size' => 43 ),
				'description' => '43 = the narrower-text / wider-image split of the Public Workshops hero. 50 = equal halves.',
				'selectors'   => array(
					'{{WRAPPER}}.lk-edhero-img-right .lk-edhero' => 'grid-template-columns: {{SIZE}}% 1fr;',
					'{{WRAPPER}}.lk-edhero-img-left .lk-edhero'  => 'grid-template-columns: 1fr {{SIZE}}%;',
				),
			)
		);
		$this->add_responsive_control(
			'copy_padding',
			array(
				'label'          => 'Text column padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 90, 'right' => 75, 'bottom' => 90, 'left' => 75, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 85, 'right' => 48, 'bottom' => 85, 'left' => 48, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 70, 'right' => 24, 'bottom' => 70, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-edhero-copy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section( 'section_style_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-edhero-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-edhero-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );

		$this->color( 'heading_color', 'Heading colour', '.lk-edhero-heading', '#57282D', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic line colour', '.lk-edhero-heading em', '#57282D' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-edhero-heading', 'Cormorant Garamond', 82, '400', -0.025, false, 0.98, 64, 40 );
		$this->add_responsive_control( 'heading_max_width', array( 'label' => 'Heading max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 1100 ) ), 'default' => array( 'unit' => 'px', 'size' => 690 ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-heading' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'heading_spacing', array( 'label' => 'Space below heading', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 80 ) ), 'default' => array( 'unit' => 'px', 'size' => 27 ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );

		$this->color( 'lead_color', 'Lead paragraph colour', '.lk-edhero-lead', '#8F8584', 'color', true );
		$this->typo( 'lead_typography', 'Lead paragraph typography', '.lk-edhero-lead', 'Cormorant Garamond', 20, '', null, false, 1.3 );

		$this->color( 'desc_color', 'Paragraph colour', '.lk-edhero-desc', '#8F8584', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-edhero-desc', 'Montserrat', 16, '', null, false, 1.65 );
		$this->add_responsive_control( 'para_spacing', array( 'label' => 'Space between paragraphs', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 60 ) ), 'default' => array( 'unit' => 'px', 'size' => 16 ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-lead, {{WRAPPER}} .lk-edhero-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'desc_max_width', array( 'label' => 'Paragraph max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 900 ) ), 'default' => array( 'unit' => 'px', 'size' => 620 ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-lead, {{WRAPPER}} .lk-edhero-desc' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );

		$this->color( 'note_color', 'Italic note colour', '.lk-edhero-note', '#692137', 'color', true );
		$this->typo( 'note_typography', 'Italic note typography', '.lk-edhero-note', 'Cormorant Garamond', 16.8, '', null, false, null, null, null, true );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Facts
		 * =======================================================*/
		$this->start_controls_section( 'section_style_facts', array( 'label' => 'Facts Row', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_facts' => 'yes' ) ) );

		$this->color( 'facts_line', 'Divider colour', '.lk-edhero-facts, {{WRAPPER}} .lk-edhero-facts > div', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->color( 'facts_label_color', 'Label colour', '.lk-edhero-facts span', '#8F8584', 'color', true );
		$this->typo( 'facts_label_typography', 'Label typography', '.lk-edhero-facts span', 'Montserrat', 10.4, '', 0.11, true );
		$this->color( 'facts_value_color', 'Value colour', '.lk-edhero-facts strong', '#692137', 'color', true );
		$this->typo( 'facts_value_typography', 'Value typography', '.lk-edhero-facts strong', 'Cormorant Garamond', 25.6, '500' );
		$this->add_responsive_control( 'facts_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 900 ) ), 'default' => array( 'unit' => 'px', 'size' => 590 ), 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-edhero-facts' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Buttons
		 * =======================================================*/
		$this->start_controls_section( 'section_style_buttons', array( 'label' => 'Buttons', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->typo( 'btn_typography', 'Button typography', '.lk-edhero-btn', 'Montserrat', 10.9, '600', 0.12, true );
		$this->add_responsive_control( 'btn_min_height', array( 'label' => 'Button min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 36, 'max' => 90 ) ), 'default' => array( 'unit' => 'px', 'size' => 48 ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-btn' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control(
			'btn_padding',
			array(
				'label'      => 'Button padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array( 'top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .lk-edhero-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'btn_gap', array( 'label' => 'Gap between buttons', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 26 ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-buttons' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->add_control( 'heading_btn_primary', array( 'label' => 'Primary Button', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->start_controls_tabs( 'tabs_btn_primary' );
		$this->start_controls_tab( 'tab_btn_primary_normal', array( 'label' => 'Normal' ) );
		$this->color( 'btn_primary_bg', 'Background', '.lk-edhero-btn-primary', '#692137', 'background-color', false, true );
		$this->color( 'btn_primary_color', 'Text colour', '.lk-edhero-btn-primary', '#FFFFFF', 'color', false, true );
		$this->color( 'btn_primary_border', 'Border colour', '.lk-edhero-btn-primary', '#692137', 'border-color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_primary_hover', array( 'label' => 'Hover' ) );
		$this->color( 'btn_primary_bg_hover', 'Background', '.lk-edhero-btn-primary:hover', 'rgba(0,0,0,0)', 'background-color', false, true );
		$this->color( 'btn_primary_color_hover', 'Text colour', '.lk-edhero-btn-primary:hover', '#692137', 'color', false, true );
		$this->color( 'btn_primary_border_hover', 'Border colour', '.lk-edhero-btn-primary:hover', '#692137', 'border-color', false, true );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'heading_link', array( 'label' => 'Second Action — Text Link', 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => array( 'btn_secondary_style' => 'link' ) ) );
		$this->add_control( 'link_color', array( 'label' => 'Colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'condition' => array( 'btn_secondary_style' => 'link' ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-link' => 'color: {{VALUE}} !important;' ) ) );
		$this->add_control( 'link_color_hover', array( 'label' => 'Colour on hover', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'condition' => array( 'btn_secondary_style' => 'link' ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-link:hover' => 'color: {{VALUE}} !important;' ) ) );
		$this->add_control( 'link_gap', array( 'label' => 'Space between text and arrow', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 30 ) ), 'default' => array( 'unit' => 'px', 'size' => 9 ), 'condition' => array( 'btn_secondary_style' => 'link' ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-link' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'link_underline_gap', array( 'label' => 'Space above underline', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 20 ) ), 'default' => array( 'unit' => 'px', 'size' => 4 ), 'condition' => array( 'btn_secondary_style' => 'link' ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-link' => 'padding-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->typo( 'link_typography', 'Typography', '.lk-edhero-link', 'Montserrat', 11.5, '600', 0.1, true );

		$this->add_control( 'heading_btn_secondary', array( 'label' => 'Second Action — Outlined Button', 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => array( 'btn_secondary_style' => 'outline' ) ) );
		$this->start_controls_tabs( 'tabs_btn_secondary', array( 'condition' => array( 'btn_secondary_style' => 'outline' ) ) );
		$this->start_controls_tab( 'tab_btn_secondary_normal', array( 'label' => 'Normal' ) );
		$this->color( 'btn_secondary_bg', 'Background', '.lk-edhero-btn-secondary', 'rgba(0,0,0,0)', 'background-color', false, true );
		$this->color( 'btn_secondary_color', 'Text colour', '.lk-edhero-btn-secondary', '#692137', 'color', false, true );
		$this->color( 'btn_secondary_border', 'Border colour', '.lk-edhero-btn-secondary', '#692137', 'border-color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_secondary_hover', array( 'label' => 'Hover' ) );
		$this->color( 'btn_secondary_bg_hover', 'Background', '.lk-edhero-btn-secondary:hover', '#692137', 'background-color', false, true );
		$this->color( 'btn_secondary_color_hover', 'Text colour', '.lk-edhero-btn-secondary:hover', '#FFFFFF', 'color', false, true );
		$this->color( 'btn_secondary_border_hover', 'Border colour', '.lk-edhero-btn-secondary:hover', '#692137', 'border-color', false, true );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Image
		 * =======================================================*/
		$this->start_controls_section( 'section_style_image', array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->add_responsive_control(
			'image_min_height',
			array(
				'label'          => 'Image min height',
				'type'           => Controls_Manager::SLIDER,
				'range'          => array( 'px' => array( 'min' => 300, 'max' => 1000 ) ),
				'default'        => array( 'unit' => 'px', 'size' => 650 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 570 ),
				'mobile_default' => array( 'unit' => 'px', 'size' => 470 ),
				'selectors'      => array( '{{WRAPPER}} .lk-edhero-image' => 'min-height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'image_focus',
			array(
				'label'     => 'Image focus',
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => array(
					'center center' => 'Centre',
					'center top'    => 'Top',
					'center bottom' => 'Bottom',
					'left center'   => 'Left',
					'right center'  => 'Right',
				),
				'description' => 'Which part of the photo stays in view when it is cropped.',
				'selectors' => array( '{{WRAPPER}} .lk-edhero-image img' => 'object-position: {{VALUE}};' ),
			)
		);
		$this->add_control( 'image_radius', array( 'label' => 'Corner radius', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-edhero-image' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );

		$this->add_control( 'heading_caption', array( 'label' => 'Caption Card (when a caption is set)', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->color( 'caption_bg', 'Background', '.lk-edhero-image figcaption', 'rgba(255,254,253,0.92)', 'background-color' );
		$this->color( 'caption_color', 'Text colour', '.lk-edhero-image figcaption', '#692137' );
		$this->typo( 'caption_typography', 'Caption typography', '.lk-edhero-image figcaption', 'Cormorant Garamond', 16, '', null, false, null, null, null, true );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$tag        = in_array( $s['heading_tag'], array( 'h1', 'h2' ), true ) ? $s['heading_tag'] : 'h1';
		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		$facts      = ( 'yes' === $s['show_facts'] && ! empty( $s['facts'] ) ) ? $s['facts'] : array();

		$has_primary   = ! empty( $s['btn_primary_text'] );
		$has_secondary = ! empty( $s['btn_secondary_text'] );

		if ( $has_primary ) {
			$this->add_render_attribute( 'btn_primary', 'class', array( 'lk-edhero-btn', 'lk-edhero-btn-primary' ) );
			$this->add_link_attributes( 'btn_primary', $s['btn_primary_link'] );
		}
		$is_link = ( 'link' === $s['btn_secondary_style'] );
		$arrow   = trim( (string) $s['btn_secondary_arrow'] );
		$move    = ( false !== strpos( $arrow, '↘' ) ) ? '3px, 3px' : ( ( false !== strpos( $arrow, '↗' ) ) ? '3px, -3px' : '3px, 0' );
		if ( $has_secondary ) {
			$this->add_render_attribute( 'btn_secondary', 'class', $is_link ? array( 'lk-edhero-link' ) : array( 'lk-edhero-btn', 'lk-edhero-btn-secondary' ) );
			if ( $is_link ) {
				$this->add_render_attribute( 'btn_secondary', 'style', '--lk-arrow-move: ' . $move . ';' );
			}
			$this->add_link_attributes( 'btn_secondary', $s['btn_secondary_link'] );
		}
		?>
		<section class="lk-edhero">
			<div class="lk-edhero-copy">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-edhero-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>

				<<?php echo esc_html( $tag ); ?> class="lk-edhero-heading"><?php echo esc_html( $s['heading_main'] ); ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?></<?php echo esc_html( $tag ); ?>>

				<?php if ( ! empty( $s['lead'] ) ) : ?><p class="lk-edhero-lead"><?php echo esc_html( $s['lead'] ); ?></p><?php endif; ?>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-edhero-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

				<?php if ( $facts ) : ?>
					<div class="lk-edhero-facts" style="--lk-facts: <?php echo esc_attr( count( $facts ) ); ?>;">
						<?php foreach ( $facts as $fact ) : ?>
							<div><span><?php echo esc_html( $fact['label'] ); ?></span><strong><?php echo esc_html( $fact['value'] ); ?></strong></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $has_primary || $has_secondary ) : ?>
					<div class="lk-edhero-buttons">
						<?php if ( $has_primary ) : ?><a <?php echo $this->get_render_attribute_string( 'btn_primary' ); ?>><?php echo esc_html( $s['btn_primary_text'] ); ?></a><?php endif; ?>
						<?php if ( $has_secondary ) : ?><a <?php echo $this->get_render_attribute_string( 'btn_secondary' ); ?>><?php echo esc_html( $s['btn_secondary_text'] ); ?><?php if ( $is_link && '' !== $arrow ) : ?><span class="lk-edhero-arrow" aria-hidden="true"><?php echo esc_html( $arrow . "\u{FE0E}" ); ?></span><?php endif; ?></a><?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $s['note'] ) ) : ?><p class="lk-edhero-note"><?php echo esc_html( $s['note'] ); ?></p><?php endif; ?>
			</div>

			<figure class="lk-edhero-image">
				<?php echo $image_html; ?>
				<?php if ( ! empty( $s['image_caption'] ) ) : ?><figcaption><?php echo esc_html( $s['image_caption'] ); ?></figcaption><?php endif; ?>
			</figure>
		</section>

		<style>
			.lk-edhero { display: grid; box-sizing: border-box; }
			.lk-edhero-img-right .lk-edhero { grid-template-columns: 43% 1fr; }
			.lk-edhero-img-left .lk-edhero { grid-template-columns: 1fr 43%; }
			.lk-edhero-img-right .lk-edhero-copy { order: 1; } .lk-edhero-img-right .lk-edhero-image { order: 2; }
			.lk-edhero-img-left .lk-edhero-copy { order: 2; } .lk-edhero-img-left .lk-edhero-image { order: 1; }
			.lk-edhero-copy { align-self: center; box-sizing: border-box; }
			.lk-edhero-eyebrow { margin: 0 0 22px; }
			.lk-edhero-heading { margin-top: 0; font-style: normal; }
			.lk-edhero-heading em { font-style: italic; }
			.lk-edhero-lead, .lk-edhero-desc { margin: 0 0 16px; }
			.lk-edhero-facts { display: grid; grid-template-columns: repeat(var(--lk-facts, 2), 1fr); margin: 32px 0 30px; border-top: 1px solid; border-bottom: 1px solid; }
			.lk-edhero-facts > div { padding: 18px 0; }
			.lk-edhero-facts > div + div { padding-left: 22px; border-left: 1px solid; }
			.lk-edhero-facts span, .lk-edhero-facts strong { display: block; }
			.lk-edhero-facts strong { margin-top: 4px; }
			.lk-edhero-buttons { display: flex; flex-wrap: wrap; align-items: center; }
			.lk-edhero-buttons { margin-top: 32px; }
			.lk-edhero-facts + .lk-edhero-buttons { margin-top: 0; }
			.lk-edhero-link { display: inline-flex; align-items: center; text-decoration: none; border-bottom: 1px solid currentColor; }
			.lk-edhero-arrow { display: inline-block; transition: transform .25s ease; }
			.lk-edhero-link:hover .lk-edhero-arrow { transform: translate(var(--lk-arrow-move, 3px, 0)); }
			.lk-edhero-btn { display: inline-flex; align-items: center; justify-content: center; box-sizing: border-box; text-decoration: none; border: 1px solid; border-radius: 0; cursor: pointer; transition: color .25s ease, background .25s ease, transform .25s ease; }
			.lk-edhero-btn:hover { transform: translateY(-2px); }
			.lk-edhero-note { margin: 27px 0 0; }
			.lk-edhero-image { position: relative; margin: 0; overflow: hidden; }
			.lk-edhero-image img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-edhero-image figcaption { position: absolute; right: 28px; bottom: 28px; max-width: 310px; padding: 15px 18px; }
			@media (max-width: 820px) {
				.lk-edhero { grid-template-columns: 1fr !important; min-height: 0 !important; }
				.lk-edhero-img-right .lk-edhero, .lk-edhero-img-left .lk-edhero { grid-template-columns: 1fr !important; }
				.lk-edhero-img-right .lk-edhero-image, .lk-edhero-img-left .lk-edhero-image { order: 1; }
				.lk-edhero-img-right .lk-edhero-copy, .lk-edhero-img-left .lk-edhero-copy { order: 2; }
			}
			@media (max-width: 540px) {
				.lk-edhero-facts { grid-template-columns: 1fr; }
				.lk-edhero-facts > div + div { padding-left: 0; border-left: 0; border-top: 1px solid; }
				.lk-edhero-btn { width: 100%; }
				.lk-edhero-image figcaption { right: 16px; bottom: 16px; left: 16px; max-width: none; }
			}
		</style>
		<?php
	}
}
