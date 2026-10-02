<?php
/**
 * Lila Kora — Occasion Grid Widget
 *
 * "An occasion they will remember making." — the Private Events occasions section:
 * on the left an eyebrow, a two-line heading (second line italic) and a muted
 * paragraph; on the right a hairline-bordered grid of numbered cards (coral
 * number at the top, burgundy italic serif title and a muted sentence at the
 * bottom of each cell). The grid follows however many cards you add.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-occasion-grid-widget.php';
 *   $widgets_manager->register( new \LK_Occasion_Grid_Widget() );
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

class LK_Occasion_Grid_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-occasion-grid';
	}

	public function get_title() {
		return 'LK — Occasion Grid';
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'occasions', 'private events', 'grid', 'cards', 'birthday' );
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

	private function layout_controls( $root, $bg, $pad_top, $pad_bottom ) {
		$this->color( 'bg_color', 'Background', $root, $bg, 'background-color' );
		$this->add_responsive_control( 'section_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} ' . $root => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => 'Section padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => $pad_top, 'right' => 65, 'bottom' => $pad_bottom, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 32, 'bottom' => 90, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 80, 'right' => 24, 'bottom' => 80, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} ' . $root => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
	}

	protected function register_controls() {

		$this->start_controls_section( 'section_content_heading', array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Made for meaningful gatherings', 'label_block' => true ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'An occasion they will', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic second line', 'type' => Controls_Manager::TEXT, 'default' => 'remember making.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => "Choose a warm, guided art session on its own, or continue the experience by turning each guest's artwork into a scarf.", 'label_block' => true ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_content_items', array( 'label' => 'Cards', 'tab' => Controls_Manager::TAB_CONTENT ) );
		$item = new Repeater();
		$item->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$item->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Title', 'label_block' => true ) );
		$item->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'Description.', 'label_block' => true ) );
		$this->add_control(
			'items',
			array(
				'label'       => 'Cards',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $item->get_controls(),
				'default'     => array(
					array( 'number' => '01', 'title' => 'Birthdays', 'text' => 'A creative celebration centred on time together.' ),
					array( 'number' => '02', 'title' => 'Bridal Gatherings', 'text' => 'A personal alternative to the usual bridal activity.' ),
					array( 'number' => '03', 'title' => 'Baby Showers', 'text' => 'A gentle, memorable way to gather and create.' ),
					array( 'number' => '04', 'title' => 'Friends and Family', 'text' => 'A private experience for any moment worth marking.' ),
				),
				'title_field' => '{{{ number }}} — {{{ title }}}',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->layout_controls( '.lk-occ', '#FFFEFD', 140, 140 );
		$this->add_control( 'columns', array( 'label' => 'Card columns (desktop)', 'type' => Controls_Manager::SELECT, 'default' => '2', 'options' => array( '2' => '2', '3' => '3' ), 'description' => 'Phones always show 1.' ) );
		$this->add_responsive_control( 'columns_gap', array( 'label' => 'Gap between heading and cards', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 220 ) ), 'default' => array( 'unit' => 'px', 'size' => 115 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 50 ), 'selectors' => array( '{{WRAPPER}} .lk-occ' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_heading', array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-occ-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-occ-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-occ-heading', '#57282D', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic line colour', '.lk-occ-heading em', '#57282D' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-occ-heading', 'Cormorant Garamond', 76, '400', -0.025, false, 1.0, 54, 38 );
		$this->color( 'desc_color', 'Paragraph colour', '.lk-occ-desc', '#8F8584', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-occ-desc', 'Montserrat', 16, '', null, false, 1.8 );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_cards', array( 'label' => 'Cards', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->color( 'grid_line_color', 'Divider colour', '.lk-occ-grid, {{WRAPPER}} .lk-occ-card', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->add_responsive_control( 'card_min_height', array( 'label' => 'Card min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 120, 'max' => 500 ) ), 'default' => array( 'unit' => 'px', 'size' => 265 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 200 ), 'selectors' => array( '{{WRAPPER}} .lk-occ-card' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'card_padding', array( 'label' => 'Card padding', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 10, 'max' => 60 ) ), 'default' => array( 'unit' => 'px', 'size' => 25 ), 'selectors' => array( '{{WRAPPER}} .lk-occ-card' => 'padding: {{SIZE}}{{UNIT}};' ) ) );
		$this->color( 'number_color', 'Number colour', '.lk-occ-card > span', '#FF715E', 'color', true );
		$this->typo( 'number_typography', 'Number typography', '.lk-occ-card > span', 'Cormorant Garamond', 16 );
		$this->color( 'title_color', 'Title colour', '.lk-occ-card h3', '#692137', 'color', true );
		$this->typo( 'title_typography', 'Title typography', '.lk-occ-card h3', 'Cormorant Garamond', 28, '400', null, false, null, null, null, true );
		$this->color( 'text_color', 'Text colour', '.lk-occ-card p', '#8F8584', 'color', true );
		$this->typo( 'text_typography', 'Text typography', '.lk-occ-card p', 'Montserrat', 13.1, '', null, false, 1.6 );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = (array) $s['items'];
		$cols  = max( 2, min( 3, (int) $s['columns'] ) );
		?>
		<section class="lk-occ">
			<div class="lk-occ-intro">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-occ-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="lk-occ-heading"><?php echo esc_html( $s['heading_line1'] ); ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-occ-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>
			</div>
			<?php if ( $items ) : ?>
				<div class="lk-occ-grid" style="--lk-occ-cols: <?php echo esc_attr( $cols ); ?>;">
					<?php foreach ( $items as $item ) : ?>
						<article class="lk-occ-card">
							<span><?php echo esc_html( $item['number'] ); ?></span>
							<h3><?php echo esc_html( $item['title'] ); ?></h3>
							<p><?php echo esc_html( $item['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>

		<style>
			.lk-occ { display: grid; grid-template-columns: 0.8fr 1.2fr; align-items: start; box-sizing: border-box; }
			.lk-occ-heading { margin: 0 0 24px; font-style: normal; }
			.lk-occ-heading em { font-style: italic; }
			.lk-occ-eyebrow { margin: 0 0 22px; }
			.lk-occ-desc { margin: 0; max-width: 520px; }
			.lk-occ-grid { display: grid; grid-template-columns: repeat(var(--lk-occ-cols, 2), 1fr); border-top: 1px solid; border-left: 1px solid; }
			.lk-occ-card { box-sizing: border-box; display: flex; flex-direction: column; border-right: 1px solid; border-bottom: 1px solid; }
			.lk-occ-card > span { display: block; font-style: normal; }
			.lk-occ-card h3 { margin: auto 0 12px; padding-top: 54px; }
			.lk-occ-card p { margin: 0; }
			@media (max-width: 1120px) {
				.lk-occ { grid-template-columns: 1fr; }
			}
			@media (max-width: 540px) {
				.lk-occ-grid { grid-template-columns: 1fr; }
			}
		</style>
		<?php
	}
}
