<?php
/**
 * Lila Kora — Stat Counters Widget
 *
 * "By the Numbers" pattern: a heading followed by a row of animated
 * counters (0 → target value) that trigger once, when scrolled into
 * view. Pure vanilla JS via IntersectionObserver, event-delegated and
 * guarded against double-binding.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-stat-counters-widget.php';
 *   $widgets_manager->register( new \LK_Stat_Counters_Widget() );
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

class LK_Stat_Counters_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-stat-counters';
	}

	public function get_title() {
		return 'LK — Stat Counters';
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'counters', 'stats', 'numbers' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'show_heading', array( 'label' => 'Show heading', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$this->add_control( 'heading', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'By the Numbers', 'label_block' => true, 'condition' => array( 'show_heading' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Counters
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_counters',
			array( 'label' => 'Counters', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'title', array( 'label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Label', 'label_block' => true ) );
		$repeater->add_control( 'value', array( 'label' => 'Target number', 'type' => Controls_Manager::NUMBER, 'default' => 100 ) );
		$repeater->add_control( 'prefix', array( 'label' => 'Prefix', 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$repeater->add_control( 'suffix', array( 'label' => 'Suffix', 'type' => Controls_Manager::TEXT, 'default' => '+' ) );

		$this->add_control(
			'counters',
			array(
				'label' => 'Items', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'title' => 'Google Reviews', 'value' => 200, 'suffix' => '+' ),
					array( 'title' => 'Average Rating', 'value' => 5, 'suffix' => '.0*' ),
					array( 'title' => 'Scarves Created', 'value' => 350, 'suffix' => '+' ),
					array( 'title' => 'Women Reached', 'value' => 1000, 'suffix' => '+' ),
				),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->add_control(
			'counter_duration',
			array( 'label' => 'Animation duration (ms)', 'type' => Controls_Manager::NUMBER, 'default' => 2000, 'separator' => 'before' )
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#F8ECE9', 'selectors' => array( '{{WRAPPER}} .lk-counters' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-counters' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 80, 'right' => 65, 'bottom' => 80, 'left' => 65, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 50, 'right' => 24, 'bottom' => 50, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-counters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'columns',
			array(
				'label' => 'Columns', 'type' => Controls_Manager::SELECT, 'default' => '4', 'mobile_default' => '2',
				'options' => array( '2' => '2', '3' => '3', '4' => '4' ),
				'selectors' => array( '{{WRAPPER}} .lk-counters-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'condition' => array( 'show_heading' => 'yes' ), 'selectors' => array( '{{WRAPPER}} .lk-counters-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-counters-heading', 'condition' => array( 'show_heading' => 'yes' ),
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ), 'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.15 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ),
		) );

		$this->add_control( 'number_color', array( 'label' => 'Number colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-counter-number' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'number_typography', 'selector' => '{{WRAPPER}} .lk-counter-number',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 48 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 34 ) ), 'font_weight' => array( 'default' => '500' ) ),
		) );

		$this->add_control( 'label_color', array( 'label' => 'Label colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-counter-label' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'label_typography', 'selector' => '{{WRAPPER}} .lk-counter-label',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11.5 ) ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.06 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ),
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$widget_id = $this->get_id();
		?>
		<div class="lk-counters">
			<?php if ( 'yes' === $s['show_heading'] && ! empty( $s['heading'] ) ) : ?>
				<p class="lk-counters-heading"><?php echo esc_html( $s['heading'] ); ?></p>
			<?php endif; ?>

			<div class="lk-counters-grid" data-lk-counters data-duration="<?php echo esc_attr( $s['counter_duration'] ); ?>">
				<?php foreach ( $s['counters'] as $counter ) : ?>
					<div class="lk-counter">
						<div class="lk-counter-number">
							<span><?php echo esc_html( $counter['prefix'] ); ?></span><span class="lk-counter-value" data-target="<?php echo esc_attr( $counter['value'] ); ?>">0</span><span><?php echo esc_html( $counter['suffix'] ); ?></span>
						</div>
						<div class="lk-counter-label"><?php echo esc_html( $counter['title'] ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<style>
			.lk-counters-heading { text-align: center; margin: 0 0 40px; }
			.lk-counters-grid { display: grid; gap: 30px; }
			.lk-counter { text-align: center; }
			.lk-counter-number { line-height: 1; margin-bottom: 8px; }
			.lk-counter-label { }
		</style>

		<script>
			( function () {
				function animate( el, target, duration ) {
					var start = 0;
					var startTime = null;
					function step( timestamp ) {
						if ( ! startTime ) { startTime = timestamp; }
						var progress = Math.min( ( timestamp - startTime ) / duration, 1 );
						var value = Math.floor( progress * ( target - start ) + start );
						el.textContent = value.toLocaleString();
						if ( progress < 1 ) {
							window.requestAnimationFrame( step );
						} else {
							el.textContent = target.toLocaleString();
						}
					}
					window.requestAnimationFrame( step );
				}

				if ( ! window.__lkCountersObserver ) {
					window.__lkCountersObserver = new IntersectionObserver( function ( entries ) {
						entries.forEach( function ( entry ) {
							if ( ! entry.isIntersecting ) { return; }
							var grid = entry.target;
							var duration = parseInt( grid.getAttribute( 'data-duration' ), 10 ) || 2000;
							grid.querySelectorAll( '.lk-counter-value' ).forEach( function ( el ) {
								var target = parseFloat( el.getAttribute( 'data-target' ) ) || 0;
								animate( el, target, duration );
							} );
							window.__lkCountersObserver.unobserve( grid );
						} );
					}, { threshold: 0.3 } );
				}

				// Bind any grid not already observed — safe to re-run this
				// script block on a page with several Stat Counters widgets,
				// since each instance is only ever attached once.
				document.querySelectorAll( '[data-lk-counters]:not([data-lk-bound])' ).forEach( function ( grid ) {
					grid.setAttribute( 'data-lk-bound', 'true' );
					window.__lkCountersObserver.observe( grid );
				} );
			} )();
		</script>
		<?php
	}
}
