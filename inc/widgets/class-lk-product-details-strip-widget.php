<?php
/**
 * Lila Kora — Product Details Strip Widget
 *
 * A 4-column strip (collapsing to 2 on tablet, 1 on mobile) typically
 * used to show product details like Material, Finish, Care, etc.
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

class LK_Product_Details_Strip_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-product-details-strip';
	}

	public function get_title() {
		return 'LK — Product Details Strip';
	}

	public function get_icon() {
		return 'eicon-columns';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'product', 'details', 'strip', 'features' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Details
		 * =======================================================*/
		$this->start_controls_section(
			'section_content',
			array(
				'label' => 'Detail Items',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'label',
			array(
				'label'       => 'Label',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Material',
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'value',
			array(
				'label'       => 'Value',
				'type'        => Controls_Manager::TEXT,
				'default'     => '100% pure silk',
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
					array( 'label' => 'Material', 'value' => '100% pure silk' ),
					array( 'label' => 'Finish', 'value' => 'Hand-rolled edges' ),
					array( 'label' => 'Care', 'value' => 'Dry clean only' ),
					array( 'label' => 'Presentation', 'value' => 'Lila Kora gift box' ),
				),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array(
				'label' => 'Layout',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'selectors' => array( '{{WRAPPER}} .lk-product-details-strip' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'section_max_width',
			array(
				'label'     => 'Max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array(
					'{{WRAPPER}} .lk-product-details-strip' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => 'Divider Color',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(105, 33, 55, 0.15)',
				'selectors' => array(
					'{{WRAPPER}} .lk-product-details-strip' => '--lk-pds-divider: {{VALUE}};',
					'{{WRAPPER}} .lk-pds-item' => 'border-color: {{VALUE}};',
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
				'mobile_default' => '1',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors' => array(
					'{{WRAPPER}} .lk-product-details-strip' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array(
				'label' => 'Text',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control( 'heading_label', array( 'label' => 'Label', 'type' => Controls_Manager::HEADING ) );
		
		$this->add_control(
			'label_color',
			array(
				'label'     => 'Label Color',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8F8584',
				'selectors' => array( '{{WRAPPER}} .lk-pds-item span' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .lk-pds-item span',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 11.2 ) ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.1 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);

		$this->add_control( 'heading_value', array( 'label' => 'Value', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );

		$this->add_control(
			'value_color',
			array(
				'label'     => 'Value Color',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-pds-item strong' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'value_typography',
				'selector' => '{{WRAPPER}} .lk-pds-item strong',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="lk-product-details-strip">
			<?php foreach ( $s['items'] as $item ) : ?>
				<div class="lk-pds-item">
					<span><?php echo esc_html( $item['label'] ); ?></span>
					<strong><?php echo esc_html( $item['value'] ); ?></strong>
				</div>
			<?php endforeach; ?>
		</div>

		<style>
			.lk-product-details-strip {
				display: grid;
				width: 100%;
			}
			.lk-pds-item {
				min-height: 125px;
				display: flex;
				flex-direction: column;
				justify-content: center;
				padding: 20px 28px;
				border-right: 1px solid var(--lk-pds-divider, rgba(105,33,55,0.15));
			}
			.lk-pds-item:last-child {
				border-right: 0;
			}
			.lk-pds-item span {
				margin-bottom: 4px;
				line-height: 1.4;
			}
			.lk-pds-item strong {
				line-height: 1.2;
			}

			/* Responsive Borders based on assumed 4 > 2 > 1 column layout */
			@media (max-width: 820px) {
				.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-product-details-strip {
					grid-template-columns: repeat(2, 1fr) !important;
				}
				.lk-pds-item {
					border-right: 1px solid var(--lk-pds-divider, rgba(105,33,55,0.15));
				}
				.lk-pds-item:nth-child(even) {
					border-right: 0;
				}
				.lk-pds-item:nth-child(1),
				.lk-pds-item:nth-child(2) {
					border-bottom: 1px solid var(--lk-pds-divider, rgba(105,33,55,0.15));
				}
			}
			
			@media (max-width: 540px) {
				.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-product-details-strip {
					grid-template-columns: 1fr !important;
				}
				.lk-pds-item {
					border-right: 0 !important;
					border-bottom: 1px solid var(--lk-pds-divider, rgba(105,33,55,0.15));
				}
				.lk-pds-item:last-child {
					border-bottom: 0;
				}
			}
		</style>
		<?php
	}
}