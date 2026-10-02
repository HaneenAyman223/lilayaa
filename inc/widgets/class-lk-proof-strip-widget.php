<?php
/**
 * Lila Kora — Proof Strip Widget
 *
 * The thin champagne strip of short, uppercase claims sitting right
 * under the hero ("No art experience needed" / "Guided from the
 * first line" / etc.), each column divided by a hairline border.
 * Collapses to a 2×2 grid on tablet, with borders repositioned so
 * the grid still reads as a clean box (matches the reference exactly:
 * the 2nd item loses its right border, and the first row gains a
 * bottom border instead).
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

class LK_Proof_Strip_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-proof-strip';
	}

	public function get_title() {
		return 'LK — Proof Strip';
	}

	public function get_icon() {
		return 'eicon-columns';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'proof', 'strip', 'stats', 'features' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT
		 * =======================================================*/
		$this->start_controls_section(
			'section_content',
			array(
				'label' => 'Items',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'text',
			array(
				'label'       => 'Text',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Item text',
				'label_block' => true,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => 'Items',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'text' => 'No art experience needed' ),
					array( 'text' => 'Everything included' ),
					array( 'text' => 'Your design approved first' ),
					array( 'text' => 'Crafted and delivered to you' ),
				),
				'title_field' => '{{{ text }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE
		 * =======================================================*/
		$this->start_controls_section(
			'section_style',
			array(
				'label' => 'Style',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'section_max_width',
			array(
				'label'     => 'Max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'The reference caps this at 1440px and centres it, same as every other section. Set your outer Elementor container to full-width / 0 padding and let this control the actual width.',
				'selectors' => array(
					'{{WRAPPER}} .lk-proof-strip' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);
		$this->add_control(
			'bg_color',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'selectors' => array( '{{WRAPPER}} .lk-proof-strip' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'divider_color',
			array(
				'label'     => 'Divider colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(105,33,55,0.13)',
				'selectors' => array( '{{WRAPPER}} .lk-proof-item' => 'border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'text_color',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-proof-item' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .lk-proof-item',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 10.4 ) ),
					'font_weight'    => array( 'default' => '600' ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.11 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'   => 'Columns',
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'tablet_default' => '2',
				'mobile_default' => '2',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors' => array(
					'{{WRAPPER}} .lk-proof-strip' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => 'Item padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array( 'top' => 22, 'right' => 18, 'bottom' => 22, 'left' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-proof-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'max_width_note',
			array(
				'type'        => Controls_Manager::HEADING,
				'label'       => 'Note',
				'separator'   => 'before',
				'description' => 'The reference caps this strip at the same 1440px page width as the rest of the site and centres it. Wrap this widget in a container with that max-width if your page layout doesn\'t already provide one.',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$count = count( $s['items'] );
		?>
		<div class="lk-proof-strip" aria-label="Highlights">
			<?php foreach ( $s['items'] as $index => $item ) : ?>
				<span class="lk-proof-item lk-proof-item-<?php echo esc_attr( $index + 1 ); ?>"><?php echo esc_html( $item['text'] ); ?></span>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
