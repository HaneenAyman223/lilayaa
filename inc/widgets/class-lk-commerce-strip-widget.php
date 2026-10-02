<?php
/**
 * Lila Kora — Commerce Strip Widget
 *
 * New in this revision: a dark burgundy strip of quick links (not
 * plain stats like the Proof Strip) sitting right after the hero —
 * "Ready-to-Wear Scarves / Create Your Own / Workshops / Meaningful
 * Gifting" — each with a trailing arrow, inverting to champagne on
 * hover. Same 4-column-to-2×2 responsive collapse as Proof Strip.
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

class LK_Commerce_Strip_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-commerce-strip';
	}

	public function get_title() {
		return 'LK — Commerce Strip';
	}

	public function get_icon() {
		return 'eicon-columns';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'commerce', 'strip', 'links', 'quicklinks' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT
		 * =======================================================*/
		$this->start_controls_section(
			'section_content',
			array(
				'label' => 'Links',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'text',
			array(
				'label'       => 'Text',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Link text',
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'link',
			array(
				'label'         => 'Link',
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => '#' ),
				'show_external' => true,
				'label_block'   => true,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => 'Items',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'text' => 'Ready-to-Wear Scarves', 'link' => array( 'url' => '#collection' ) ),
					array( 'text' => 'Create Your Own', 'link' => array( 'url' => './experience-box.html' ) ),
					array( 'text' => 'Workshops', 'link' => array( 'url' => './public-workshops.html' ) ),
					array( 'text' => 'Meaningful Gifting', 'link' => array( 'url' => '#shop' ) ),
				),
				'title_field' => '{{{ text }}}',
			)
		);

		$this->add_control(
			'show_arrow',
			array(
				'label'     => 'Show trailing arrow',
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => 'Show',
				'label_off' => 'Hide',
				'default'   => 'yes',
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
					'{{WRAPPER}} .lk-commerce-strip' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);
		$this->add_control(
			'bg_color',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-commerce-strip' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'divider_color',
			array(
				'label'     => 'Divider colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.18)',
				'selectors' => array( '{{WRAPPER}} .lk-commerce-item' => 'border-color: {{VALUE}};' ),
			)
		);

		$this->start_controls_tabs( 'tabs_item' );
		$this->start_controls_tab( 'tab_item_normal', array( 'label' => 'Normal' ) );
		$this->add_control(
			'text_color',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'selectors' => array( '{{WRAPPER}} .lk-commerce-item' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_item_hover', array( 'label' => 'Hover' ) );
		$this->add_control(
			'text_color_hover',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-commerce-item:hover' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'bg_color_hover',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'selectors' => array( '{{WRAPPER}} .lk-commerce-item:hover' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'typography',
				'selector'  => '{{WRAPPER}} .lk-commerce-item',
				'separator' => 'before',
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
					'{{WRAPPER}} .lk-commerce-strip' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => 'Item padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array( 'top' => 23, 'right' => 18, 'bottom' => 23, 'left' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-commerce-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="lk-commerce-strip<?php echo 'yes' === $s['show_arrow'] ? ' lk-commerce-strip-arrow' : ''; ?>" aria-label="Lila Kora collections and experiences">
			<?php foreach ( $s['items'] as $index => $item ) :
				$key = 'item_' . $index;
				$this->add_render_attribute( $key, 'class', 'lk-commerce-item' );
				$this->add_link_attributes( $key, $item['link'] );
				?>
				<a <?php echo $this->get_render_attribute_string( $key ); ?>><?php echo esc_html( $item['text'] ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
