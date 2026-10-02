<?php
/**
 * Lila Kora — What To Expect Widget
 *
 * The dark burgundy "What to expect" section of the Public Workshops page:
 *
 *   ┌──────────────────────────────┬──────────────────────┐
 *   │ WHAT TO EXPECT               │                      │
 *   │ A few hours to slow down,    │  lead paragraph      │
 *   │ connect and *create.*        │  (bottom-aligned)    │
 *   ├────────────────┬─────────────┴──────────────────────┤
 *   │                │ ───────────────────────────────    │
 *   │     image      │ 01  Meet around the table          │
 *   │  (or two, or   │ ───────────────────────────────    │
 *   │   staggered)   │ 02  Create your design             │
 *   │                │ ───────────────────────────────    │
 *   │                │ 03  Choose what comes next         │
 *   └────────────────┴────────────────────────────────────┘
 *
 * Optionally a small "included" note sits under the rows (tinted box with a
 * coral edge). The section's Anchor ID defaults to "experience" so the hero's
 * "See What to Expect" link scrolls here.
 *
 * (File and class names are unchanged from the earlier 3-column version, so
 * an existing loader registration keeps working.)
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-experience-flow-widget.php';
 *   $widgets_manager->register( new \LK_Experience_Flow_Widget() );
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

class LK_Experience_Flow_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-experience-flow';
	}

	public function get_title() {
		return 'LK — What To Expect';
	}

	public function get_icon() {
		return 'eicon-image-before-after';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'what to expect', 'steps', 'experience', 'workshop', 'process' );
	}

	private function typo( $name, $label, $selector, $family, $size, $weight = '', $spacing = null, $upper = false, $line = null, $tablet = null, $mobile = null ) {
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
		if ( null !== $line ) {
			$fields['line_height'] = array( 'default' => array( 'unit' => 'em', 'size' => $line ) );
		}
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => $name, 'label' => $label, 'selector' => '{{WRAPPER}} ' . $selector, 'fields_options' => $fields )
		);
	}

	private function color( $id, $label, $selector, $default, $prop = 'color', $separator = false ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'default'   => $default,
			'selectors' => array( '{{WRAPPER}} ' . $selector => $prop . ': {{VALUE}};' ),
		);
		if ( $separator ) {
			$args['separator'] = 'before';
		}
		$this->add_control( $id, $args );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section( 'section_content_heading', array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'anchor_id', array( 'label' => 'Anchor ID', 'type' => Controls_Manager::TEXT, 'default' => 'experience', 'description' => 'What a "#experience" link scrolls to. Leave as is for the hero\'s "See What to Expect" link.' ) );
		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'What to expect', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'A few hours to slow down,', 'label_block' => true ) );
		$this->add_control( 'heading_line2', array( 'label' => 'Heading — second line', 'type' => Controls_Manager::TEXT, 'default' => 'connect and', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic word(s)', 'type' => Controls_Manager::TEXT, 'default' => 'create', 'label_block' => true ) );
		$this->add_control( 'heading_suffix', array( 'label' => 'Heading — after the italic (upright)', 'type' => Controls_Manager::TEXT, 'default' => '.', 'description' => 'E.g. the full stop, which stays upright.' ) );
		$this->add_control( 'lead', array( 'label' => 'Paragraph (right of the heading)', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'You are guided from the first line to the final colour, while still having the freedom to make the design entirely your own.', 'label_block' => true, 'separator' => 'before' ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Image
		 * =======================================================*/
		$this->start_controls_section( 'section_content_image', array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'image_layout', array( 'label' => 'Layout', 'type' => Controls_Manager::SELECT, 'default' => 'single', 'options' => array( 'single' => 'One image', 'duo' => 'Two images (staggered)' ) ) );
		$this->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image', 'default' => 'large' ) );
		$this->add_control( 'image_2', array( 'label' => 'Second image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ), 'condition' => array( 'image_layout' => 'duo' ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image_2', 'default' => 'large', 'condition' => array( 'image_layout' => 'duo' ) ) );
		$this->add_control( 'image_position', array( 'label' => 'Image side', 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => array( 'left' => 'Left', 'right' => 'Right' ), 'prefix_class' => 'lk-wte-img-' ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Steps
		 * =======================================================*/
		$this->start_controls_section( 'section_content_steps', array( 'label' => 'Numbered Rows', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$steps = new Repeater();
		$steps->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$steps->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Step title', 'label_block' => true ) );
		$steps->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Step description.', 'label_block' => true ) );

		$this->add_control(
			'steps',
			array(
				'label'       => 'Rows',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $steps->get_controls(),
				'default'     => array(
					array( 'number' => '01', 'title' => 'Meet around the table', 'text' => 'Settle in, meet the women beside you and take a moment away from the usual pace of the day.' ),
					array( 'number' => '02', 'title' => 'Create your design', 'text' => 'Follow a guided, intuitive process using free-flowing lines, softened intersections and colour. No art experience is required.' ),
					array( 'number' => '03', 'title' => 'Choose what comes next', 'text' => 'Take your canvas home or choose to have your artwork professionally refined and crafted into a scarf.' ),
				),
				'title_field' => '{{{ number }}} — {{{ title }}}',
			)
		);

		$this->add_control( 'show_included', array( 'label' => 'Show the "included" note under the rows', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => '', 'separator' => 'before' ) );
		$this->add_control( 'included_eyebrow', array( 'label' => 'Note — eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Included in your ticket', 'label_block' => true, 'condition' => array( 'show_included' => 'yes' ) ) );
		$this->add_control( 'included_text', array( 'label' => 'Note — text', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'All art materials, a beverage and pastry, and your finished canvas to take home.', 'label_block' => true, 'condition' => array( 'show_included' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'bg_color', 'Background', '.lk-wte', '#692137', 'background-color' );
		$this->add_responsive_control( 'section_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-wte' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => 'Section padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 95, 'right' => 65, 'bottom' => 95, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 75, 'right' => 32, 'bottom' => 75, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 65, 'right' => 24, 'bottom' => 65, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-wte' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'heading_space', array( 'label' => 'Space below heading block', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 140 ) ), 'default' => array( 'unit' => 'px', 'size' => 58 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 48 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 38 ), 'selectors' => array( '{{WRAPPER}} .lk-wte-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'layout_gap', array( 'label' => 'Gap between image and rows', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 160 ) ), 'default' => array( 'unit' => 'px', 'size' => 85 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 50 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 40 ), 'selectors' => array( '{{WRAPPER}} .lk-wte-layout' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'heading_column_gap', array( 'label' => 'Gap between heading and paragraph (vw)', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 20, 'step' => 0.5 ) ), 'size_units' => array( 'px' ), 'default' => array( 'unit' => 'px', 'size' => 8 ), 'selectors' => array( '{{WRAPPER}} .lk-wte-heading' => 'column-gap: {{SIZE}}vw;' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading block
		 * =======================================================*/
		$this->start_controls_section( 'section_style_heading', array( 'label' => 'Heading Block', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-wte-eyebrow', '#FFC5BA' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-wte-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-wte-heading h2', '#FFFFFF', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic word colour', '.lk-wte-heading h2 em', '#FFFFFF' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-wte-heading h2', 'Cormorant Garamond', 72, '400', -0.025, false, 0.98, 54, 38 );
		$this->add_responsive_control( 'heading_max_width', array( 'label' => 'Heading max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 1200 ) ), 'default' => array( 'unit' => 'px', 'size' => 860 ), 'selectors' => array( '{{WRAPPER}} .lk-wte-heading h2' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->color( 'lead_color', 'Paragraph colour', '.lk-wte-lead', 'rgba(255,255,255,0.72)', 'color', true );
		$this->typo( 'lead_typography', 'Paragraph typography', '.lk-wte-lead', 'Montserrat', 16, '', null, false, 1.65 );
		$this->add_responsive_control( 'lead_max_width', array( 'label' => 'Paragraph max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 250, 'max' => 800 ) ), 'default' => array( 'unit' => 'px', 'size' => 500 ), 'selectors' => array( '{{WRAPPER}} .lk-wte-lead' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Image
		 * =======================================================*/
		$this->start_controls_section( 'section_style_image', array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->add_responsive_control( 'image_min_height', array( 'label' => 'Image min height (one image)', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 250, 'max' => 900 ) ), 'default' => array( 'unit' => 'px', 'size' => 460 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 500 ), 'selectors' => array( '{{WRAPPER}} .lk-wte-image' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'duo_min_height', array( 'label' => 'Image min height (two images)', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 900 ) ), 'default' => array( 'unit' => 'px', 'size' => 630 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 560 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 430 ), 'condition' => array( 'image_layout' => 'duo' ), 'selectors' => array( '{{WRAPPER}} .lk-wte-duo' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'duo_offset', array( 'label' => 'Second image drop', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 200 ) ), 'default' => array( 'unit' => 'px', 'size' => 110 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 55 ), 'condition' => array( 'image_layout' => 'duo' ), 'selectors' => array( '{{WRAPPER}} .lk-wte-duo figure:last-child' => 'margin-top: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control(
			'image_focus',
			array(
				'label'     => 'Image focus',
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => array( 'center center' => 'Centre', 'center top' => 'Top', 'center bottom' => 'Bottom', 'left center' => 'Left', 'right center' => 'Right' ),
				'selectors' => array( '{{WRAPPER}} .lk-wte figure img' => 'object-position: {{VALUE}};' ),
			)
		);
		$this->add_control( 'image_radius', array( 'label' => 'Corner radius', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-wte figure' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Rows
		 * =======================================================*/
		$this->start_controls_section( 'section_style_rows', array( 'label' => 'Numbered Rows', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'rows_line', 'Line colour', '.lk-wte-steps, {{WRAPPER}} .lk-wte-step', 'rgba(255,255,255,0.22)', 'border-color' );
		$this->add_responsive_control( 'row_padding', array( 'label' => 'Row padding (top & bottom)', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 6, 'max' => 70 ) ), 'default' => array( 'unit' => 'px', 'size' => 21 ), 'selectors' => array( '{{WRAPPER}} .lk-wte-step' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'number_column', array( 'label' => 'Number column width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 24, 'max' => 120 ) ), 'default' => array( 'unit' => 'px', 'size' => 42 ), 'selectors' => array( '{{WRAPPER}} .lk-wte-step' => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr;' ) ) );
		$this->color( 'number_color', 'Number colour', '.lk-wte-step > span', '#FF715E', 'color', true );
		$this->typo( 'number_typography', 'Number typography', '.lk-wte-step > span', 'Cormorant Garamond', 16 );
		$this->color( 'title_color', 'Title colour', '.lk-wte-step h3', '#FFC5BA', 'color', true );
		$this->typo( 'title_typography', 'Title typography', '.lk-wte-step h3', 'Cormorant Garamond', 28.8, '500', null, false, 1.1, 26, 24 );
		$this->color( 'text_color', 'Text colour', '.lk-wte-step p', 'rgba(255,255,255,0.7)', 'color', true );
		$this->typo( 'text_typography', 'Text typography', '.lk-wte-step p', 'Montserrat', 15, '', null, false, 1.65 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Included note
		 * =======================================================*/
		$this->start_controls_section( 'section_style_included', array( 'label' => 'Included Note', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_included' => 'yes' ) ) );

		$this->color( 'inc_bg', 'Background', '.lk-wte-included', 'rgba(255,255,255,0.07)', 'background-color' );
		$this->color( 'inc_edge', 'Edge colour', '.lk-wte-included', '#FF715E', 'border-color' );
		$this->color( 'inc_eyebrow_color', 'Eyebrow colour', '.lk-wte-included .lk-wte-eyebrow', '#FF715E', 'color', true );
		$this->color( 'inc_text_color', 'Text colour', '.lk-wte-included p:not(.lk-wte-eyebrow)', 'rgba(255,255,255,0.78)' );
		$this->typo( 'inc_text_typography', 'Text typography', '.lk-wte-included p:not(.lk-wte-eyebrow)', 'Montserrat', 16, '', null, false, 1.65 );

		$this->end_controls_section();
	}

	protected function render() {
		$s           = $this->get_settings_for_display();
		$steps       = (array) $s['steps'];
		$duo         = ( 'duo' === $s['image_layout'] );
		$image_html  = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		$image2_html = $duo ? Group_Control_Image_Size::get_attachment_image_html( $s, 'image_2', 'image_2' ) : '';
		$anchor      = ! empty( $s['anchor_id'] ) ? sanitize_html_class( $s['anchor_id'] ) : '';
		?>
		<section class="lk-wte"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
			<div class="lk-wte-heading">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-wte-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2><?php echo esc_html( $s['heading_line1'] ); ?><?php if ( ! empty( $s['heading_line2'] ) ) : ?><br><?php echo esc_html( $s['heading_line2'] ); ?><?php endif; ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?> <em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?><?php echo esc_html( $s['heading_suffix'] ); ?></h2>
				<?php if ( ! empty( $s['lead'] ) ) : ?><p class="lk-wte-lead"><?php echo esc_html( $s['lead'] ); ?></p><?php endif; ?>
			</div>

			<div class="lk-wte-layout">
				<?php if ( $duo ) : ?>
					<div class="lk-wte-duo">
						<figure><?php echo $image_html; ?></figure>
						<figure><?php echo $image2_html; ?></figure>
					</div>
				<?php else : ?>
					<figure class="lk-wte-image"><?php echo $image_html; ?></figure>
				<?php endif; ?>

				<div class="lk-wte-side">
					<?php if ( $steps ) : ?>
						<div class="lk-wte-steps">
							<?php foreach ( $steps as $step ) : ?>
								<article class="lk-wte-step">
									<span><?php echo esc_html( $step['number'] ); ?></span>
									<div>
										<h3><?php echo esc_html( $step['title'] ); ?></h3>
										<p><?php echo esc_html( $step['text'] ); ?></p>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( 'yes' === $s['show_included'] && ( ! empty( $s['included_eyebrow'] ) || ! empty( $s['included_text'] ) ) ) : ?>
						<div class="lk-wte-included">
							<?php if ( ! empty( $s['included_eyebrow'] ) ) : ?><p class="lk-wte-eyebrow"><?php echo esc_html( $s['included_eyebrow'] ); ?></p><?php endif; ?>
							<?php if ( ! empty( $s['included_text'] ) ) : ?><p><?php echo esc_html( $s['included_text'] ); ?></p><?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<style>
			.lk-wte { box-sizing: border-box; scroll-margin-top: 120px; }
			.lk-wte-heading { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 20px 8vw; align-items: end; }
			.lk-wte-heading .lk-wte-eyebrow { grid-column: 1 / -1; margin: 0; }
			.lk-wte-heading h2 { margin: 0; font-style: normal; }
			.lk-wte-heading h2 em { font-style: italic; }
			.lk-wte-lead { margin: 0 0 8px; }
			.lk-wte-layout { display: grid; grid-template-columns: 0.88fr 1.12fr; gap: 85px; align-items: stretch; }
			.lk-wte-img-right .lk-wte-layout { grid-template-columns: 1.12fr 0.88fr; }
			.lk-wte-img-right .lk-wte-image, .lk-wte-img-right .lk-wte-duo { order: 2; }
			.lk-wte-img-right .lk-wte-side { order: 1; }
			.lk-wte figure { position: relative; margin: 0; overflow: hidden; }
			.lk-wte figure img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-wte-duo { display: grid; grid-template-columns: 1.25fr 0.75fr; gap: 10px; }
			.lk-wte-steps { border-top: 1px solid; }
			.lk-wte-step { display: grid; grid-template-columns: 42px 1fr; gap: 18px; padding: 21px 0; border-bottom: 1px solid; }
			.lk-wte-step > span { font-style: normal; }
			.lk-wte-step h3 { margin: 0 0 9px; }
			.lk-wte-step p { margin: 0; }
			.lk-wte-included { margin-top: 32px; padding: 24px; border-left: 2px solid; box-sizing: border-box; }
			.lk-wte-included .lk-wte-eyebrow { margin: 0 0 10px; }
			.lk-wte-included p { margin: 0; }
			@media (max-width: 820px) {
				.lk-wte-heading { grid-template-columns: 1fr; }
				.lk-wte-heading .lk-wte-eyebrow { grid-column: auto; }
				.lk-wte-layout, .lk-wte-img-right .lk-wte-layout { grid-template-columns: 1fr; }
				.lk-wte-img-right .lk-wte-image, .lk-wte-img-right .lk-wte-duo, .lk-wte-img-right .lk-wte-side { order: 0; }
			}
		</style>
		<?php
	}
}
