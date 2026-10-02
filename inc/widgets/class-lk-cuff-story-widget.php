<?php
/**
 * Lila Kora — Cuff Story Widget
 *
 * The "One base, many stories" dark burgundy band on the Interchangeable
 * Cardigan page: eyebrow, a two-line heading with the second line fully
 * italic, a paragraph, and a numbered list (number + text only, no titles —
 * unlike LK — What To Expect) between hairlines. An image with an italic
 * caption sits on the right.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-cuff-story-widget.php';
 *   $widgets_manager->register( new \LK_Cuff_Story_Widget() );
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

class LK_Cuff_Story_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-cuff-story';
	}

	public function get_title() {
		return 'LK — Cuff Story';
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'cardigan', 'cuffs', 'story', 'numbered list' );
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
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section( 'section_content_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'anchor_id', array( 'label' => 'Anchor ID (optional)', 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'One base, many stories', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Change the detail.', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic second line', 'type' => Controls_Manager::TEXT, 'default' => 'Change the expression.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => 'The cuffs are designed as the expressive element of the piece. Keep one cardigan and change the artwork as your style, season or story changes.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Numbered list
		 * =======================================================*/
		$this->start_controls_section( 'section_content_list', array( 'label' => 'Numbered List', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$list = new Repeater();
		$list->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$list->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'List item.', 'label_block' => true ) );
		$this->add_control(
			'items',
			array(
				'label'       => 'Items',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $list->get_controls(),
				'default'     => array(
					array( 'number' => '01', 'text' => 'Choose an existing Lila Kora cuff design.' ),
					array( 'number' => '02', 'text' => 'Or create original artwork for your own cuff set.' ),
					array( 'number' => '03', 'text' => 'Confirm sizing and production details with us.' ),
				),
				'title_field' => '{{{ number }}} — {{{ text }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Image
		 * =======================================================*/
		$this->start_controls_section( 'section_content_image', array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image', 'default' => 'large' ) );
		$this->add_control( 'image_caption', array( 'label' => 'Caption', 'type' => Controls_Manager::TEXT, 'default' => 'A considered detail, made personal.', 'label_block' => true ) );
		$this->add_control( 'image_position', array( 'label' => 'Image side', 'type' => Controls_Manager::SELECT, 'default' => 'right', 'options' => array( 'right' => 'Right', 'left' => 'Left' ), 'prefix_class' => 'lk-cuffstory-img-' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'bg_color', 'Background', '.lk-cuffstory', '#692137', 'background-color' );
		$this->add_responsive_control( 'section_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-cuffstory' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => 'Section padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 32, 'bottom' => 90, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 80, 'right' => 24, 'bottom' => 80, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-cuffstory' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'columns_gap', array( 'label' => 'Gap between columns', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 160 ) ), 'default' => array( 'unit' => 'px', 'size' => 90 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 50 ), 'selectors' => array( '{{WRAPPER}} .lk-cuffstory-layout' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section( 'section_style_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-cuffstory-eyebrow', '#FFC5BA' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-cuffstory-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-cuffstory-copy h2', '#FFFFFF', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic line colour', '.lk-cuffstory-copy h2 em', '#FFFFFF' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-cuffstory-copy h2', 'Cormorant Garamond', 58, '400', -0.025, false, 0.98, 44, 36 );
		$this->color( 'desc_color', 'Paragraph colour', '.lk-cuffstory-desc', 'rgba(255,255,255,0.72)', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-cuffstory-desc', 'Montserrat', 15, '', null, false, 1.65 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Numbered list
		 * =======================================================*/
		$this->start_controls_section( 'section_style_list', array( 'label' => 'Numbered List', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'list_line', 'Line colour', '.lk-cuffstory-list, {{WRAPPER}} .lk-cuffstory-item', 'rgba(255,255,255,0.22)', 'border-color' );
		$this->add_responsive_control( 'item_padding', array( 'label' => 'Row padding (top & bottom)', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 4, 'max' => 50 ) ), 'default' => array( 'unit' => 'px', 'size' => 15 ), 'selectors' => array( '{{WRAPPER}} .lk-cuffstory-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'number_column', array( 'label' => 'Number column width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 20, 'max' => 80 ) ), 'default' => array( 'unit' => 'px', 'size' => 32 ), 'selectors' => array( '{{WRAPPER}} .lk-cuffstory-item' => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr;' ) ) );
		$this->color( 'number_color', 'Number colour', '.lk-cuffstory-item span', '#FF715E', 'color', true );
		$this->typo( 'number_typography', 'Number typography', '.lk-cuffstory-item span', 'Cormorant Garamond', 14 );
		$this->color( 'item_text_color', 'Text colour', '.lk-cuffstory-item p', 'rgba(255,255,255,0.85)', 'color', true );
		$this->typo( 'item_text_typography', 'Text typography', '.lk-cuffstory-item p', 'Montserrat', 15, '', null, false, 1.5 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Image
		 * =======================================================*/
		$this->start_controls_section( 'section_style_image', array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->add_responsive_control(
			'image_size',
			array(
				'label'          => 'Image size (square)',
				'type'           => Controls_Manager::SLIDER,
				'range'          => array( 'px' => array( 'min' => 200, 'max' => 800 ) ),
				'default'        => array( 'unit' => 'px', 'size' => 540 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 420 ),
				'mobile_default' => array( 'unit' => 'px', 'size' => 300 ),
				'selectors'      => array( '{{WRAPPER}} .lk-cuffstory-image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			'image_focus',
			array(
				'label'     => 'Image focus',
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => array( 'center center' => 'Centre', 'center top' => 'Top', 'center bottom' => 'Bottom', 'left center' => 'Left', 'right center' => 'Right' ),
				'selectors' => array( '{{WRAPPER}} .lk-cuffstory-image img' => 'object-position: {{VALUE}};' ),
			)
		);
		$this->add_control( 'image_radius', array( 'label' => 'Corner radius', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-cuffstory-image' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );
		$this->color( 'caption_color', 'Caption colour', '.lk-cuffstory-image figcaption', 'rgba(255,255,255,0.85)', 'color', true );
		$this->typo( 'caption_typography', 'Caption typography', '.lk-cuffstory-image figcaption', 'Cormorant Garamond', 14, '', null, false, null, null, null, true );

		$this->end_controls_section();
	}

	protected function render() {
		$s          = $this->get_settings_for_display();
		$items      = (array) $s['items'];
		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		$anchor     = ! empty( $s['anchor_id'] ) ? sanitize_html_class( $s['anchor_id'] ) : '';
		?>
		<section class="lk-cuffstory"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
			<div class="lk-cuffstory-layout">
				<div class="lk-cuffstory-copy">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-cuffstory-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
					<h2><?php echo esc_html( $s['heading_line1'] ); ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?></h2>
					<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-cuffstory-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

					<?php if ( $items ) : ?>
						<div class="lk-cuffstory-list">
							<?php foreach ( $items as $item ) : ?>
								<div class="lk-cuffstory-item"><span><?php echo esc_html( $item['number'] ); ?></span><p><?php echo esc_html( $item['text'] ); ?></p></div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<figure class="lk-cuffstory-image">
					<?php echo $image_html; ?>
					<?php if ( ! empty( $s['image_caption'] ) ) : ?><figcaption><?php echo esc_html( $s['image_caption'] ); ?></figcaption><?php endif; ?>
				</figure>
			</div>
		</section>

		<style>
			.lk-cuffstory { box-sizing: border-box; scroll-margin-top: 120px; }
			.lk-cuffstory-layout { display: grid; grid-template-columns: 1fr auto; align-items: start; }
			.lk-cuffstory-img-left .lk-cuffstory-layout { grid-template-columns: auto 1fr; }
			.lk-cuffstory-img-left .lk-cuffstory-image { order: 1; } .lk-cuffstory-img-left .lk-cuffstory-copy { order: 2; }
			.lk-cuffstory-copy h2 { margin: 0 0 28px; font-style: normal; }
			.lk-cuffstory-copy h2 em { font-style: italic; }
			.lk-cuffstory-desc { margin: 0 0 34px; max-width: 420px; }
			.lk-cuffstory-list { border-top: 1px solid; }
			.lk-cuffstory-item { display: grid; grid-template-columns: 32px 1fr; gap: 8px; padding: 15px 0; border-bottom: 1px solid; }
			.lk-cuffstory-item span { font-style: normal; }
			.lk-cuffstory-item p { margin: 0; }
			.lk-cuffstory-image { position: relative; width: 540px; height: 540px; margin: 0; overflow: hidden; flex-shrink: 0; }
			.lk-cuffstory-image img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-cuffstory-image figcaption { margin-top: 14px; font-style: italic; text-align: right; }
			@media (max-width: 820px) {
				.lk-cuffstory-layout, .lk-cuffstory-img-left .lk-cuffstory-layout { grid-template-columns: 1fr; }
				.lk-cuffstory-img-left .lk-cuffstory-image, .lk-cuffstory-img-left .lk-cuffstory-copy { order: 0; }
				.lk-cuffstory-image { width: 100%; height: auto; aspect-ratio: 1; }
			}
		</style>
		<?php
	}
}
