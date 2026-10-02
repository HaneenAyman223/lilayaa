<?php
/**
 * Lila Kora — Journey Cards Widget
 *
 * "Simple to plan. Personal to experience." — a light blush section with a
 * centred eyebrow and two-line heading, then a row of step cards. Each card
 * has an ivory background, a coral top border, a coral number, a burgundy
 * serif title and a muted sentence. Four columns on desktop, two on tablet,
 * one on phones; the column count follows the cards you add (2-5).
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-journey-cards-widget.php';
 *   $widgets_manager->register( new \LK_Journey_Cards_Widget() );
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

class LK_Journey_Cards_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-journey-cards';
	}

	public function get_title() {
		return 'LK — Journey Cards';
	}

	public function get_icon() {
		return 'eicon-flow';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'journey', 'steps', 'process', 'cards', 'how it works' );
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
		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'How a private event comes together', 'label_block' => true ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Simple to plan.', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic second line', 'type' => Controls_Manager::TEXT, 'default' => 'Personal to experience.', 'label_block' => true ) );
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
					array( 'number' => '01', 'title' => 'Share Your Occasion', 'text' => 'Tell us the date, group size, setting and what you are celebrating.' ),
					array( 'number' => '02', 'title' => 'Receive Your Proposal', 'text' => 'We recommend the format, venue approach and scarf options.' ),
					array( 'number' => '03', 'title' => 'Create Together', 'text' => 'We bring the materials and guide your guests through the session.' ),
					array( 'number' => '04', 'title' => 'Continue the Story', 'text' => 'If selected, each canvas is transformed into a finished scarf.' ),
				),
				'title_field' => '{{{ number }}} — {{{ title }}}',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->layout_controls( '.lk-jrn', '#F8ECE9', 140, 140 );
		$this->add_responsive_control( 'heading_spacing', array( 'label' => 'Space below heading', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 140 ) ), 'default' => array( 'unit' => 'px', 'size' => 70 ), 'selectors' => array( '{{WRAPPER}} .lk-jrn-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'cards_gap', array( 'label' => 'Gap between cards', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 80 ) ), 'default' => array( 'unit' => 'px', 'size' => 22 ), 'selectors' => array( '{{WRAPPER}} .lk-jrn-grid' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_heading', array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-jrn-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-jrn-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-jrn-heading h2', '#57282D', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic line colour', '.lk-jrn-heading h2 em', '#57282D' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-jrn-heading h2', 'Cormorant Garamond', 76, '400', -0.025, false, 1.0, 54, 38 );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_cards', array( 'label' => 'Cards', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->color( 'card_bg', 'Card background', '.lk-jrn-card', '#FFFEFD', 'background-color' );
		$this->color( 'card_accent', 'Top border colour', '.lk-jrn-card', '#FF715E', 'border-top-color' );
		$this->add_responsive_control( 'card_padding', array( 'label' => 'Card padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px' ), 'default' => array( 'top' => 30, 'right' => 25, 'bottom' => 30, 'left' => 25, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .lk-jrn-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->color( 'number_color', 'Number colour', '.lk-jrn-card > span', '#FF715E', 'color', true );
		$this->typo( 'number_typography', 'Number typography', '.lk-jrn-card > span', 'Cormorant Garamond', 16 );
		$this->color( 'title_color', 'Title colour', '.lk-jrn-card h3', '#692137', 'color', true );
		$this->typo( 'title_typography', 'Title typography', '.lk-jrn-card h3', 'Cormorant Garamond', 25, '400', null, false, 1.12 );
		$this->add_responsive_control( 'title_top_space', array( 'label' => 'Space above title', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 120 ) ), 'default' => array( 'unit' => 'px', 'size' => 50 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 28 ), 'selectors' => array( '{{WRAPPER}} .lk-jrn-card h3' => 'margin-top: {{SIZE}}{{UNIT}};' ) ) );
		$this->color( 'text_color', 'Text colour', '.lk-jrn-card p', '#8F8584', 'color', true );
		$this->typo( 'text_typography', 'Text typography', '.lk-jrn-card p', 'Montserrat', 13.1, '', null, false, 1.6 );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = (array) $s['items'];
		$cols  = max( 2, min( 5, count( $items ) ) );
		?>
		<section class="lk-jrn">
			<div class="lk-jrn-heading">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-jrn-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2><?php echo esc_html( $s['heading_line1'] ); ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?></h2>
			</div>
			<?php if ( $items ) : ?>
				<div class="lk-jrn-grid" style="--lk-jrn-cols: <?php echo esc_attr( $cols ); ?>;">
					<?php foreach ( $items as $item ) : ?>
						<article class="lk-jrn-card">
							<span><?php echo esc_html( $item['number'] ); ?></span>
							<h3><?php echo esc_html( $item['title'] ); ?></h3>
							<p><?php echo esc_html( $item['text'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>

		<style>
			.lk-jrn { box-sizing: border-box; }
			.lk-jrn-heading { text-align: center; }
			.lk-jrn-eyebrow { margin: 0 0 22px; }
			.lk-jrn-heading h2 { margin: 0; font-style: normal; }
			.lk-jrn-heading h2 em { font-style: italic; }
			.lk-jrn-grid { display: grid; grid-template-columns: repeat(var(--lk-jrn-cols, 4), 1fr); }
			.lk-jrn-card { box-sizing: border-box; border-top: 3px solid; }
			.lk-jrn-card > span { display: block; font-style: normal; }
			.lk-jrn-card h3 { margin-bottom: 14px; }
			.lk-jrn-card p { margin: 0; }
			@media (max-width: 1120px) {
				.lk-jrn-grid { grid-template-columns: 1fr 1fr; }
			}
			@media (max-width: 540px) {
				.lk-jrn-grid { grid-template-columns: 1fr; }
			}
		</style>
		<?php
	}
}
