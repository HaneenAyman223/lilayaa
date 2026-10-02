<?php
/**
 * Lila Kora — Box Creative Method Widget
 *
 * "No rules. No pressure. Just expression." — a dark burgundy section:
 * a heading on the left (narrow column), and a bordered 2×2 grid of
 * numbered method steps on the right (no images, just number/title/
 * description with grid-line borders between cells).
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-box-creative-method-widget.php';
 *   $widgets_manager->register( new \LK_Box_Creative_Method_Widget() );
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

class LK_Box_Creative_Method_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-box-creative-method';
	}

	public function get_title() {
		return 'LK — Box Creative Method';
	}

	public function get_icon() {
		return 'eicon-numbered-list';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'box', 'creative method', 'experience box' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'The creative method', 'label_block' => true ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — line 1', 'type' => Controls_Manager::TEXT, 'default' => 'No rules.', 'label_block' => true ) );
		$this->add_control( 'heading_line2', array( 'label' => 'Heading — line 2', 'type' => Controls_Manager::TEXT, 'default' => 'No pressure.', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — emphasized line', 'type' => Controls_Manager::TEXT, 'default' => 'Just expression.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Steps
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_steps',
			array( 'label' => 'Steps', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$repeater->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Step title', 'label_block' => true ) );
		$repeater->add_control( 'description', array( 'label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Step description.', 'label_block' => true ) );

		$this->add_control(
			'steps',
			array(
				'label' => 'Steps', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'number' => '01', 'title' => 'Set Your Intention', 'description' => 'Choose the energy, feeling or story you want your scarf to carry.' ),
					array( 'number' => '02', 'title' => 'Draw Freely', 'description' => 'Let your hand move across the canvas through intuitive, free-flowing lines.' ),
					array( 'number' => '03', 'title' => 'Soften the Lines', 'description' => 'Round each intersection into an organic curve, bringing harmony to the composition.' ),
					array( 'number' => '04', 'title' => 'Bring It to Life', 'description' => 'Fill the spaces with colours that feel right to you and make the design entirely your own.' ),
				),
				'title_field' => '{{{ number }}} — {{{ title }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxmethod' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-boxmethod' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-boxmethod' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'columns_gap', array( 'label' => 'Gap', 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', 'vw' ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 200 ), 'vw' => array( 'min' => 0, 'max' => 15 ) ), 'default' => array( 'unit' => 'vw', 'size' => 9 ), 'selectors' => array( '{{WRAPPER}} .lk-boxmethod' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxmethod-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_emphasis_color', array( 'label' => 'Emphasized line colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-boxmethod-heading em' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-boxmethod-heading',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 56 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 34 ) ), 'font_weight' => array( 'default' => '400' ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.05 ) ) ),
		) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Steps
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_steps',
			array( 'label' => 'Steps', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'steps_border_color', array( 'label' => 'Grid line colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.22)', 'selectors' => array( '{{WRAPPER}} .lk-boxmethod-steps' => 'border-color: {{VALUE}};', '{{WRAPPER}} .lk-boxmethod-steps article' => 'border-color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'step_min_height', array( 'label' => 'Cell min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 100, 'max' => 400 ) ), 'default' => array( 'unit' => 'px', 'size' => 215 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-boxmethod-steps article' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );

		$this->add_control( 'step_number_color', array( 'label' => 'Number colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxmethod-steps article span' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'step_number_typography', 'selector' => '{{WRAPPER}} .lk-boxmethod-steps article span', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ) ) ) );

		$this->add_control( 'step_title_color', array( 'label' => 'Title colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxmethod-steps h3' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'step_title_typography', 'selector' => '{{WRAPPER}} .lk-boxmethod-steps h3', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 26.4 ) ) ) ) );

		$this->add_control( 'step_desc_color', array( 'label' => 'Description colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.67)', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxmethod-steps p' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'step_desc_typography', 'selector' => '{{WRAPPER}} .lk-boxmethod-steps p', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 13.8 ) ) ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="lk-boxmethod">
			<div class="lk-boxmethod-copy">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="lk-boxmethod-heading">
					<?php echo esc_html( $s['heading_line1'] ); ?><br>
					<?php echo esc_html( $s['heading_line2'] ); ?><br>
					<em><?php echo esc_html( $s['heading_emphasis'] ); ?></em>
				</h2>
			</div>

			<div class="lk-boxmethod-steps">
				<?php foreach ( $s['steps'] as $step ) : ?>
					<article>
						<span><?php echo esc_html( $step['number'] ); ?></span>
						<h3><?php echo esc_html( $step['title'] ); ?></h3>
						<p><?php echo esc_html( $step['description'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<style>
			.lk-boxmethod { display: grid; grid-template-columns: 0.78fr 1.22fr; color: #fff; }
			.lk-boxmethod-copy { margin: 0; }
			.lk-boxmethod-heading { margin: 0; font-style: normal; } .lk-boxmethod-heading em { font-style: italic; }
			.lk-boxmethod-steps { display: grid; grid-template-columns: 1fr 1fr; border-top: 1px solid; }
			.lk-boxmethod-steps article { padding: 25px 28px 28px 0; border-bottom: 1px solid; box-sizing: border-box; }
			.lk-boxmethod-steps article:nth-child(odd) { border-right: 1px solid; padding-right: 28px; }
			.lk-boxmethod-steps article:nth-child(even) { padding-left: 28px; }
			.lk-boxmethod-steps article span { display: block; font-style: normal; }
			.lk-boxmethod-steps h3 { margin: 20px 0 10px; font-style: normal; }
			.lk-boxmethod-steps p { margin: 0; }
			@media (max-width: 820px) {
				.lk-boxmethod { grid-template-columns: 1fr !important; }
				.lk-boxmethod-copy { margin-bottom: 10px; }
			}
			@media (max-width: 540px) {
				.lk-boxmethod-steps { grid-template-columns: 1fr; }
				.lk-boxmethod-steps article, .lk-boxmethod-steps article:nth-child(even) { padding: 25px 0; border-right: 0; }
			}
		</style>
		<?php
	}
}
