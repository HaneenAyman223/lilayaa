<?php
/**
 * Lila Kora — Shop Guidance Widget
 *
 * The "Not sure where to begin?" dark burgundy band at the bottom of the
 * Shop page: an eyebrow and 2-line heading (with one italic word) on the
 * left, and on the right a row of numbered options — each a number, a short
 * title, a sentence, and an underlined text link with an arrow.
 *
 * The reference has exactly 2 options side by side; this widget supports 2
 * to 4 via a repeater, laid out in that many columns.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-shop-guidance-widget.php';
 *   $widgets_manager->register( new \LK_Shop_Guidance_Widget() );
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

class LK_Shop_Guidance_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-shop-guidance';
	}

	public function get_title() {
		return 'LK — Shop Guidance';
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'shop', 'guidance', 'not sure where to begin' );
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

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Not sure where to begin?', 'label_block' => true ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Choose by the way', 'label_block' => true ) );
		$this->add_control( 'heading_line2', array( 'label' => 'Heading — second line', 'type' => Controls_Manager::TEXT, 'default' => 'you want her to', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic word', 'type' => Controls_Manager::TEXT, 'default' => 'feel.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Options
		 * =======================================================*/
		$this->start_controls_section( 'section_content_options', array( 'label' => 'Options', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$opt = new Repeater();
		$opt->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$opt->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Option title', 'label_block' => true ) );
		$opt->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'Option description.', 'label_block' => true ) );
		$opt->add_control( 'link_text', array( 'label' => 'Link text', 'type' => Controls_Manager::TEXT, 'default' => 'Learn more', 'label_block' => true ) );
		$opt->add_control( 'link_url', array( 'label' => 'Link URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true ) );
		$opt->add_control( 'arrow', array( 'label' => 'Arrow', 'type' => Controls_Manager::TEXT, 'default' => '↗', 'description' => '↗ for a different page, ↘ to point down this page. Leave empty for none.' ) );

		$this->add_control(
			'options',
			array(
				'label'       => 'Options',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $opt->get_controls(),
				'default'     => array(
					array( 'number' => '01', 'title' => 'Ready to wear', 'text' => 'Choose a finished silk scarf when you already know the design that suits her.', 'link_text' => 'Shop scarves', 'link_url' => array( 'url' => './silk-scarf.html' ), 'arrow' => '↗' ),
					array( 'number' => '02', 'title' => 'Made by her', 'text' => 'Choose the Experience Box when you want the act of creating to be part of the gift.', 'link_text' => 'Explore the box', 'link_url' => array( 'url' => './experience-box.html' ), 'arrow' => '↗' ),
				),
				'title_field' => '{{{ number }}} — {{{ title }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'bg_color', 'Background', '.lk-shopguide', '#692137', 'background-color' );
		$this->add_responsive_control( 'section_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-shopguide' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => 'Section padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 110, 'right' => 65, 'bottom' => 110, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 85, 'right' => 32, 'bottom' => 85, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 70, 'right' => 24, 'bottom' => 70, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-shopguide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'columns_gap', array( 'label' => 'Gap between heading and options', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 160 ) ), 'default' => array( 'unit' => 'px', 'size' => 80 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 45 ), 'selectors' => array( '{{WRAPPER}} .lk-shopguide-layout' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'options_gap', array( 'label' => 'Gap between options', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 100 ) ), 'default' => array( 'unit' => 'px', 'size' => 45 ), 'selectors' => array( '{{WRAPPER}} .lk-shopguide-options' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading
		 * =======================================================*/
		$this->start_controls_section( 'section_style_heading', array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-shopguide-eyebrow', '#FFC5BA' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-shopguide-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-shopguide-copy h2', '#FFFFFF', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic word colour', '.lk-shopguide-copy h2 em', '#FFFFFF' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-shopguide-copy h2', 'Cormorant Garamond', 50, '400', -0.025, false, 1.08, 40, 32 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Options
		 * =======================================================*/
		$this->start_controls_section( 'section_style_options', array( 'label' => 'Options', 'tab' => Controls_Manager::TAB_STYLE ) );

$this->color( 'grid_line_color', 'Grid divider colour', '.lk-shopguide-options, {{WRAPPER}} .lk-shopguide-option', 'rgba(255,255,255,0.22)', 'border-color' );
		$this->color( 'number_color', 'Number colour', '.lk-shopguide-option > span', '#FF715E', 'color', true );
		$this->typo( 'number_typography', 'Number typography', '.lk-shopguide-option > span', 'Cormorant Garamond', 17.6 );
		$this->color( 'title_color', 'Title colour', '.lk-shopguide-option h3', '#FFC5BA', 'color', true );
		$this->typo( 'title_typography', 'Title typography', '.lk-shopguide-option h3', 'Cormorant Garamond', 26, '500' );
		$this->color( 'text_color', 'Text colour', '.lk-shopguide-option p', 'rgba(255,255,255,0.72)', 'color', true );
		$this->typo( 'text_typography', 'Text typography', '.lk-shopguide-option p', 'Montserrat', 14.5, '', null, false, 1.6 );
		$this->color( 'link_color', 'Link colour', '.lk-shopguide-option a', '#FFC5BA', 'color', true );
		$this->typo( 'link_typography', 'Link typography', '.lk-shopguide-option a', 'Montserrat', 12.5, '600', 0.1, true );

		$this->end_controls_section();
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$options = (array) $s['options'];
		?>
		<section class="lk-shopguide">
			<div class="lk-shopguide-layout">
				<div class="lk-shopguide-copy">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-shopguide-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
					<h2><?php echo esc_html( $s['heading_line1'] ); ?><?php if ( ! empty( $s['heading_line2'] ) ) : ?><br><?php echo esc_html( $s['heading_line2'] ); ?><?php endif; ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?> <em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?></h2>
				</div>

				<?php if ( $options ) : ?>
					<div class="lk-shopguide-options" style="--lk-shopguide-cols: <?php echo esc_attr( count( $options ) ); ?>;">
						<?php foreach ( $options as $opt ) :
							$this->add_render_attribute( 'link_' . $opt['_id'], 'href', ! empty( $opt['link_url']['url'] ) ? $opt['link_url']['url'] : '#' );
							if ( ! empty( $opt['link_url']['is_external'] ) ) {
								$this->add_render_attribute( 'link_' . $opt['_id'], 'target', '_blank' );
								$this->add_render_attribute( 'link_' . $opt['_id'], 'rel', 'noopener' );
							}
							?>
							<div class="lk-shopguide-option">
								<span><?php echo esc_html( $opt['number'] ); ?></span>
								<h3><?php echo esc_html( $opt['title'] ); ?></h3>
								<p><?php echo esc_html( $opt['text'] ); ?></p>
								<?php if ( ! empty( $opt['link_text'] ) ) : ?>
									<a <?php echo $this->get_render_attribute_string( 'link_' . $opt['_id'] ); ?>><?php echo esc_html( $opt['link_text'] ); ?> <?php if ( ! empty( $opt['arrow'] ) ) : ?><span aria-hidden="true"><?php echo esc_html( $opt['arrow'] . "\u{FE0E}" ); ?></span><?php endif; ?></a>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>

		<style>
			.lk-shopguide { box-sizing: border-box; }
			.lk-shopguide-layout { display: grid; grid-template-columns: 0.8fr 1.2fr; gap: 8vw; align-items: start; }
			.lk-shopguide-copy h2 { margin: 0; font-style: normal; }
			.lk-shopguide-copy h2 em { font-style: italic; }
			.lk-shopguide-options { display: grid; grid-template-columns: repeat(var(--lk-shopguide-cols, 2), 1fr); border-top: 1px solid; border-left: 1px solid; }
			.lk-shopguide-option { box-sizing: border-box; min-height: 330px; display: flex; flex-direction: column; padding: 28px; border-right: 1px solid; border-bottom: 1px solid; }
			.lk-shopguide-option > span { display: block; font-style: normal; }
			.lk-shopguide-option h3 { margin: 55px 0 15px; }
			.lk-shopguide-option p { margin: 0; }
			.lk-shopguide-option a { margin-top: auto; text-decoration: none; }
			@media (max-width: 820px) {
				.lk-shopguide-layout { grid-template-columns: 1fr; }
			}
			@media (max-width: 540px) {
				.lk-shopguide-option { min-height: 280px; }
			}
		</style>
		<?php
	}
}
