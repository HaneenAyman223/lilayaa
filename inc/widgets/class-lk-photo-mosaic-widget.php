<?php
/**
 * Lila Kora — Photo Mosaic Widget
 *
 * The "Previous workshops" band on the Public Workshops page: a burgundy-soft
 * section with an eyebrow, a large serif heading whose second line is italic,
 * a short paragraph aligned to the bottom-right of the heading, and a five-image
 * mosaic:
 *
 *   ┌────────────┬────────┬─────────┐
 *   │            │   2    │    4    │
 *   │     1      ├────────┼─────────┤
 *   │  (tall)    │   3    │    5    │
 *   └────────────┴────────┴─────────┘   columns 1.15 : 0.85 : 0.9
 *
 * The mosaic is a repeater: the first five images fill the pattern above; on
 * tablet it becomes 2 columns, on phone a single column. Any images after the
 * fifth simply continue below in extra rows.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-photo-mosaic-widget.php';
 *   $widgets_manager->register( new \LK_Photo_Mosaic_Widget() );
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

class LK_Photo_Mosaic_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-photo-mosaic';
	}

	public function get_title() {
		return 'LK — Photo Mosaic';
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'gallery', 'mosaic', 'photos', 'previous workshops', 'moments' );
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

		$this->add_control( 'anchor_id', array( 'label' => 'Anchor ID (optional)', 'type' => Controls_Manager::TEXT, 'default' => '', 'description' => 'Lets a "#your-id" link scroll here.' ) );
		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Previous workshops', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'A glimpse around', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic second line', 'type' => Controls_Manager::TEXT, 'default' => 'the table.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph (right of the heading)', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Every workshop looks different because every woman brings something of her own to the canvas.', 'label_block' => true, 'separator' => 'before' ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Images
		 * =======================================================*/
		$this->start_controls_section( 'section_content_images', array( 'label' => 'Mosaic Images', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control(
			'images_note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<div style="line-height:1.6;font-size:12px;color:#a4afb7;">Image <strong>1</strong> is the tall one on the left. Images 2 and 3 stack in the middle column; 4 and 5 stack on the right.</div>',
			)
		);

		$item = new Repeater();
		$item->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$item->add_control(
			'focus',
			array(
				'label'     => 'Focus (which part stays in view)',
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => array( 'center center' => 'Centre', 'center top' => 'Top', 'center bottom' => 'Bottom', 'left center' => 'Left', 'right center' => 'Right' ),
				'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}} img' => 'object-position: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'images',
			array(
				'label'       => 'Images',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $item->get_controls(),
				'default'     => array(
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
					array( 'image' => array( 'url' => Utils::get_placeholder_image_src() ) ),
				),
				'title_field' => 'Image',
			)
		);
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'thumb', 'default' => 'large', 'label' => 'Image size' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'bg_color', 'Background', '.lk-mosaic', '#7A2C42', 'background-color' );
		$this->add_responsive_control( 'section_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-mosaic' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => 'Section padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 145, 'right' => 65, 'bottom' => 145, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 100, 'right' => 32, 'bottom' => 100, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-mosaic' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'heading_space', array( 'label' => 'Space below heading block', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 140 ) ), 'default' => array( 'unit' => 'px', 'size' => 48 ), 'selectors' => array( '{{WRAPPER}} .lk-mosaic-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'heading_column_gap', array( 'label' => 'Gap between heading and paragraph (vw)', 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 20, 'step' => 0.5 ) ), 'default' => array( 'unit' => 'px', 'size' => 8 ), 'selectors' => array( '{{WRAPPER}} .lk-mosaic-heading' => 'column-gap: {{SIZE}}vw;' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading block
		 * =======================================================*/
		$this->start_controls_section( 'section_style_heading', array( 'label' => 'Heading Block', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-mosaic-eyebrow', '#FFC5BA' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-mosaic-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-mosaic-heading h2', '#FFFFFF', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic line colour', '.lk-mosaic-heading h2 em', '#FFFFFF' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-mosaic-heading h2', 'Cormorant Garamond', 72, '400', -0.025, false, 0.98, 54, 38 );
		$this->color( 'desc_color', 'Paragraph colour', '.lk-mosaic-desc', 'rgba(255,255,255,0.7)', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-mosaic-desc', 'Montserrat', 16, '', null, false, 1.65 );
		$this->add_responsive_control( 'desc_max_width', array( 'label' => 'Paragraph max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 250, 'max' => 800 ) ), 'default' => array( 'unit' => 'px', 'size' => 470 ), 'selectors' => array( '{{WRAPPER}} .lk-mosaic-desc' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Mosaic
		 * =======================================================*/
		$this->start_controls_section( 'section_style_grid', array( 'label' => 'Mosaic', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->add_responsive_control(
			'row_height',
			array(
				'label'          => 'Row height',
				'type'           => Controls_Manager::SLIDER,
				'range'          => array( 'px' => array( 'min' => 140, 'max' => 600 ) ),
				'default'        => array( 'unit' => 'px', 'size' => 290 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 280 ),
				'mobile_default' => array( 'unit' => 'px', 'size' => 330 ),
				'description'    => 'Desktop shows 2 rows; tablet 3 rows; phone 5 rows (one image each).',
				'selectors'      => array( '{{WRAPPER}} .lk-mosaic-grid' => '--lk-mosaic-row: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'grid_gap', array( 'label' => 'Gap between images', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 10 ), 'selectors' => array( '{{WRAPPER}} .lk-mosaic-grid' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'image_radius', array( 'label' => 'Corner radius', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-mosaic-grid figure' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$items  = (array) $s['images'];
		$anchor = ! empty( $s['anchor_id'] ) ? sanitize_html_class( $s['anchor_id'] ) : '';
		?>
		<section class="lk-mosaic"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
			<div class="lk-mosaic-heading">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-mosaic-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2><?php echo esc_html( $s['heading_line1'] ); ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-mosaic-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>
			</div>

			<?php if ( $items ) : ?>
				<div class="lk-mosaic-grid">
					<?php foreach ( $items as $item ) :
						if ( empty( $item['image']['url'] ) ) {
							continue;
						}
						$html = Group_Control_Image_Size::get_attachment_image_html(
							array(
								'thumb_size'             => isset( $s['thumb_size'] ) ? $s['thumb_size'] : 'large',
								'thumb_custom_dimension' => isset( $s['thumb_custom_dimension'] ) ? $s['thumb_custom_dimension'] : array(),
								'image'                  => $item['image'],
							),
							'thumb',
							'image'
						);
						?>
						<figure class="elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>"><?php echo $html; ?></figure>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>

		<style>
			.lk-mosaic { box-sizing: border-box; scroll-margin-top: 120px; }
			.lk-mosaic-heading { display: grid; grid-template-columns: 1fr 0.8fr; gap: 20px 8vw; align-items: end; }
			.lk-mosaic-heading .lk-mosaic-eyebrow { grid-column: 1 / -1; margin: 0; }
			.lk-mosaic-heading h2 { margin: 0; font-style: normal; }
			.lk-mosaic-heading h2 em { font-style: italic; }
			.lk-mosaic-desc { margin: 0 0 8px; }
			.lk-mosaic-grid { --lk-mosaic-row: 290px; display: grid; grid-template-columns: 1.15fr 0.85fr 0.9fr; grid-template-rows: var(--lk-mosaic-row) var(--lk-mosaic-row); grid-auto-rows: var(--lk-mosaic-row); gap: 10px; }
			.lk-mosaic-grid figure { position: relative; margin: 0; overflow: hidden; }
			.lk-mosaic-grid figure img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-mosaic-grid figure:first-child { grid-row: 1 / 3; }
			.lk-mosaic-grid figure:nth-child(4) { grid-column: 3; grid-row: 1; }
			.lk-mosaic-grid figure:nth-child(5) { grid-column: 3; grid-row: 2; }
			@media (max-width: 820px) {
				.lk-mosaic-heading { grid-template-columns: 1fr; }
				.lk-mosaic-heading .lk-mosaic-eyebrow { grid-column: auto; }
				.lk-mosaic-grid { grid-template-columns: 1fr 1fr; grid-template-rows: repeat(3, var(--lk-mosaic-row)); }
				.lk-mosaic-grid figure:first-child { grid-row: 1 / 3; }
				.lk-mosaic-grid figure:nth-child(4) { grid-column: 1; grid-row: 3; }
				.lk-mosaic-grid figure:nth-child(5) { grid-column: 2; grid-row: 3; }
			}
			@media (max-width: 540px) {
				.lk-mosaic-grid { grid-template-columns: 1fr; grid-template-rows: repeat(5, var(--lk-mosaic-row)); }
				.lk-mosaic-grid figure:first-child, .lk-mosaic-grid figure:nth-child(4), .lk-mosaic-grid figure:nth-child(5) { grid-column: auto; grid-row: auto; }
			}
		</style>
		<?php
	}
}
