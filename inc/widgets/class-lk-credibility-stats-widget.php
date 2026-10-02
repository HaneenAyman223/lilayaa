<?php
/**
 * Lila Kora — Credibility Stats Widget
 *
 * The dark counter band: a full-width burgundy-soft strip split into columns
 * (three by default) by thin white dividers. Each cell has a large champagne
 * serif number, with a small uppercase label under it. Numbers count up from
 * zero the first time the strip scrolls into view (switchable, and skipped
 * automatically for visitors who prefer reduced motion).
 *
 * Not the same as LK — Stat Counters, which is the light blush "By the Numbers"
 * band with a heading. This one is the dark credibility strip with no heading.
 * On phones the cells stack into a single column with a divider between them.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-credibility-stats-widget.php';
 *   $widgets_manager->register( new \LK_Credibility_Stats_Widget() );
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

class LK_Credibility_Stats_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-credibility-stats';
	}

	public function get_title() {
		return 'LK — Credibility Stats';
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'stats', 'counter', 'numbers', 'credibility', 'proof' );
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

		$this->start_controls_section( 'section_content_stats', array( 'label' => 'Stats', 'tab' => Controls_Manager::TAB_CONTENT ) );
		$item = new Repeater();
		$item->add_control( 'value', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '100', 'description' => 'Digits only (a decimal point is fine) — it is what counts up. Put symbols in Prefix / Suffix.' ) );
		$item->add_control( 'prefix', array( 'label' => 'Prefix', 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$item->add_control( 'suffix', array( 'label' => 'Suffix', 'type' => Controls_Manager::TEXT, 'default' => '+' ) );
		$item->add_control( 'label', array( 'label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Label', 'label_block' => true ) );
		$this->add_control(
			'items',
			array(
				'label'       => 'Stats',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $item->get_controls(),
				'default'     => array(
					array( 'value' => '100', 'prefix' => '', 'suffix' => '+', 'label' => 'Add your first label' ),
					array( 'value' => '100', 'prefix' => '', 'suffix' => '+', 'label' => 'Add your second label' ),
					array( 'value' => '100', 'prefix' => '', 'suffix' => '%', 'label' => 'Add your third label' ),
				),
				'title_field' => '{{{ prefix }}}{{{ value }}}{{{ suffix }}} — {{{ label }}}',
			)
		);
		$this->add_control( 'animate', array( 'label' => 'Count up when scrolled into view', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => 'yes', 'separator' => 'before' ) );
		$this->add_control( 'duration', array( 'label' => 'Count-up duration (ms)', 'type' => Controls_Manager::NUMBER, 'default' => 1800, 'min' => 300, 'max' => 6000, 'condition' => array( 'animate' => 'yes' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->color( 'bg_color', 'Background', '.lk-cs', '#7A2C42', 'background-color' );
		$this->add_responsive_control( 'section_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-cs' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control( 'cell_min_height', array( 'label' => 'Cell min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 80, 'max' => 400 ) ), 'default' => array( 'unit' => 'px', 'size' => 190 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 150 ), 'selectors' => array( '{{WRAPPER}} .lk-cs-cell' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'cell_padding', array( 'label' => 'Cell padding', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 80 ) ), 'default' => array( 'unit' => 'px', 'size' => 28 ), 'selectors' => array( '{{WRAPPER}} .lk-cs-cell' => 'padding: {{SIZE}}{{UNIT}};' ) ) );
		$this->color( 'divider_color', 'Divider colour', '.lk-cs-cell', 'rgba(255,255,255,0.2)', 'border-color' );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->color( 'number_color', 'Number colour', '.lk-cs-num', '#FFC5BA' );
		$this->typo( 'number_typography', 'Number typography', '.lk-cs-num', 'Cormorant Garamond', 72, '400', null, false, 1.0, 56, 51 );
		$this->color( 'label_color', 'Label colour', '.lk-cs-label', 'rgba(255,255,255,0.7)', 'color', true );
		$this->typo( 'label_typography', 'Label typography', '.lk-cs-label', 'Montserrat', 10.4, '', 0.1, true );
		$this->add_responsive_control( 'label_gap', array( 'label' => 'Space between number and label', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 14 ), 'selectors' => array( '{{WRAPPER}} .lk-cs-label' => 'margin-top: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = (array) $s['items'];
		$count = max( 1, count( $items ) );
		$anim  = ( 'yes' === $s['animate'] );
		?>
		<section class="lk-cs" style="--lk-cs-cols: <?php echo esc_attr( $count ); ?>;" data-lk-cs<?php echo $anim ? ' data-animate="1" data-duration="' . esc_attr( max( 300, (int) $s['duration'] ) ) . '"' : ''; ?>>
			<?php foreach ( $items as $item ) :
				$value = preg_replace( '/[^0-9.]/', '', (string) $item['value'] );
				?>
				<div class="lk-cs-cell">
					<strong class="lk-cs-num" data-target="<?php echo esc_attr( $value ); ?>" data-prefix="<?php echo esc_attr( $item['prefix'] ); ?>" data-suffix="<?php echo esc_attr( $item['suffix'] ); ?>"><?php echo esc_html( $item['prefix'] . $value . $item['suffix'] ); ?></strong>
					<span class="lk-cs-label"><?php echo esc_html( $item['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</section>

		<style>
			.lk-cs { display: grid; grid-template-columns: repeat(var(--lk-cs-cols, 3), 1fr); box-sizing: border-box; }
			.lk-cs-cell { box-sizing: border-box; display: flex; flex-direction: column; justify-content: center; text-align: center; border-right: 1px solid; }
			.lk-cs-cell:last-child { border-right: 0; }
			.lk-cs-num { display: block; font-style: normal; }
			.lk-cs-label { display: block; }
			@media (max-width: 540px) {
				.lk-cs { grid-template-columns: 1fr; }
				.lk-cs-cell { border-right: 0; border-bottom: 1px solid; }
				.lk-cs-cell:last-child { border-bottom: 0; }
			}
		</style>

		<script>
			( function () {
				if ( ! window.lkCsInit ) {
					window.lkCsInit = function () {
						var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
						var fmt = function ( n, decimals ) {
							return n.toLocaleString( 'en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals } );
						};
						var run = function ( root ) {
							var duration = parseInt( root.getAttribute( 'data-duration' ), 10 ) || 1800;
							root.querySelectorAll( '.lk-cs-num' ).forEach( function ( el ) {
								var raw = el.getAttribute( 'data-target' ) || '0';
								var target = parseFloat( raw ) || 0;
								var decimals = ( raw.split( '.' )[1] || '' ).length;
								var pre = el.getAttribute( 'data-prefix' ) || '', suf = el.getAttribute( 'data-suffix' ) || '';
								var start = null;
								var step = function ( ts ) {
									if ( start === null ) { start = ts; }
									var p = Math.min( ( ts - start ) / duration, 1 );
									var eased = 1 - Math.pow( 1 - p, 3 );
									el.textContent = pre + fmt( target * eased, decimals ) + suf;
									if ( p < 1 ) { requestAnimationFrame( step ); }
								};
								requestAnimationFrame( step );
							} );
						};
						document.querySelectorAll( '[data-lk-cs][data-animate]:not([data-lk-cs-bound])' ).forEach( function ( root ) {
							root.setAttribute( 'data-lk-cs-bound', 'true' );
							if ( reduce || ! ( 'IntersectionObserver' in window ) ) { return; }
							root.querySelectorAll( '.lk-cs-num' ).forEach( function ( el ) {
								el.textContent = ( el.getAttribute( 'data-prefix' ) || '' ) + '0' + ( el.getAttribute( 'data-suffix' ) || '' );
							} );
							var io = new IntersectionObserver( function ( entries ) {
								entries.forEach( function ( e ) {
									if ( e.isIntersecting ) { io.disconnect(); run( root ); }
								} );
							}, { threshold: 0.35 } );
							io.observe( root );
						} );
					};
				}
				window.lkCsInit();
			} )();
		</script>
		<?php
	}
}
