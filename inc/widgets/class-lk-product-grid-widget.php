<?php
/**
 * Lila Kora — Product Grid Widget
 *
 * "The Signature Collection" pattern: a heading followed by a plain
 * N-column product grid (image, title, price, CTA button) — simpler
 * than LK — Shop Grid (no special gift-card tile), for showcasing a
 * straightforward product line.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-product-grid-widget.php';
 *   $widgets_manager->register( new \LK_Product_Grid_Widget() );
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
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class LK_Product_Grid_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-product-grid';
	}

	public function get_title() {
		return 'LK — Product Grid';
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'products', 'collection', 'grid' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'The Signature Collection', 'label_block' => true ) );
		$this->add_control( 'heading', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Hand-painted signature scarves', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Our signature line — original artwork, translated onto pure silk and finished by hand in Dubai. A design story for every woman who wears it.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Products
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_products',
			array( 'label' => 'Products', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$repeater->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Product name', 'label_block' => true ) );
		$repeater->add_control( 'price', array( 'label' => 'Price', 'type' => Controls_Manager::TEXT, 'default' => 'AED 550', 'label_block' => true ) );
		$repeater->add_control( 'button_text', array( 'label' => 'Button text', 'type' => Controls_Manager::TEXT, 'default' => 'Select Options', 'label_block' => true ) );
		$repeater->add_control( 'button_link', array( 'label' => 'Button link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true, 'label_block' => true ) );

		$this->add_control(
			'products',
			array(
				'label' => 'Items', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'title' => 'Midnight Bloom', 'price' => 'AED 550' ),
					array( 'title' => 'Desert Rose', 'price' => 'AED 550' ),
					array( 'title' => 'Coral Horizon', 'price' => 'AED 550' ),
					array( 'title' => 'Jasmine Garden', 'price' => 'AED 550' ),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Bottom Link
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_link',
			array( 'label' => 'Bottom Link', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'show_link', array( 'label' => 'Show', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$this->add_control( 'link_text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Explore the collection', 'label_block' => true, 'condition' => array( 'show_link' => 'yes' ) ) );
		$this->add_control( 'link_url', array( 'label' => 'URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true, 'label_block' => true, 'condition' => array( 'show_link' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-productgrid' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 100, 'right' => 65, 'bottom' => 100, 'left' => 65, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 70, 'right' => 24, 'bottom' => 70, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-productgrid' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'heading_max_width', array( 'label' => 'Heading max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 900 ) ), 'default' => array( 'unit' => 'px', 'size' => 640 ), 'selectors' => array( '{{WRAPPER}} .lk-productgrid-heading' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control( 'heading_spacing', array( 'label' => 'Spacing below heading', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 100 ) ), 'default' => array( 'unit' => 'px', 'size' => 55 ), 'selectors' => array( '{{WRAPPER}} .lk-productgrid-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control(
			'columns',
			array(
				'label' => 'Columns', 'type' => Controls_Manager::SELECT, 'default' => '4', 'tablet_default' => '2', 'mobile_default' => '2',
				'options' => array( '2' => '2', '3' => '3', '4' => '4' ),
				'selectors' => array( '{{WRAPPER}} .lk-productgrid-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);' ),
			)
		);
		$this->add_control( 'grid_gap', array( 'label' => 'Gap', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 60 ) ), 'default' => array( 'unit' => 'px', 'size' => 28 ), 'selectors' => array( '{{WRAPPER}} .lk-productgrid-grid' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-productgrid-heading h2' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-productgrid-heading h2',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 44 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 30 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );
		$this->add_control( 'desc_color', array( 'label' => 'Description colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-productgrid-heading p.lk-productgrid-desc' => 'color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Products
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_products',
			array( 'label' => 'Products', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'image_bg', array( 'label' => 'Image background', 'type' => Controls_Manager::COLOR, 'default' => '#F5EBEF', 'selectors' => array( '{{WRAPPER}} .lk-product-image' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'title_color', array( 'label' => 'Title colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-product h3' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .lk-product h3', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 21.6 ) ) ) ) );
		$this->add_control( 'price_color', array( 'label' => 'Price colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-product-price' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'price_typography', 'selector' => '{{WRAPPER}} .lk-product-price', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 13.6 ) ) ) ) );

		$this->add_control( 'heading_button', array( 'label' => 'Button', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'button_typography', 'selector' => '{{WRAPPER}} .lk-product-btn',
			'fields_options' => array(
				'font_family'    => array( 'default' => 'Montserrat' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 10.9 ) ),
				'font_weight'    => array( 'default' => '600' ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.08 ) ),
				'text_transform' => array( 'default' => 'uppercase' ),
			),
		) );
		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => 'Button padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array( 'top' => 13, 'right' => 20, 'bottom' => 13, 'left' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-product-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->start_controls_tabs( 'tabs_btn' );
		$this->start_controls_tab( 'tab_btn_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'btn_bg', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-product-btn' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-product-btn' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'btn_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'selectors' => array( '{{WRAPPER}} .lk-product-btn:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_color_hover', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-product-btn:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Bottom Link
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_link',
			array( 'label' => 'Bottom Link', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_link' => 'yes' ) )
		);

		$this->add_control( 'link_color', array( 'label' => 'Colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-productgrid-link' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'link_typography', 'selector' => '{{WRAPPER}} .lk-productgrid-link',
			'fields_options' => array(
				'font_family' => array( 'default' => 'Montserrat' ),
				'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 12.8 ) ),
			),
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		if ( 'yes' === $s['show_link'] ) {
			$this->add_render_attribute( 'link', 'class', 'lk-productgrid-link' );
			$this->add_link_attributes( 'link', $s['link_url'] );
		}
		?>
		<div class="lk-productgrid">
			<div class="lk-productgrid-heading">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2><?php echo esc_html( $s['heading'] ); ?></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-productgrid-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>
			</div>

			<div class="lk-productgrid-grid">
				<?php foreach ( $s['products'] as $index => $p ) :
					$image_html = Group_Control_Image_Size::get_attachment_image_html( $p, 'image' );
					$key = 'pbtn_' . $index;
					$this->add_render_attribute( $key, 'class', 'lk-product-btn' );
					$this->add_link_attributes( $key, $p['button_link'] );
					?>
					<article class="lk-product">
						<div class="lk-product-image"><?php echo $image_html; ?></div>
						<h3><?php echo esc_html( $p['title'] ); ?></h3>
						<div class="lk-product-price"><?php echo esc_html( $p['price'] ); ?></div>
						<a <?php echo $this->get_render_attribute_string( $key ); ?>><?php echo esc_html( $p['button_text'] ); ?></a>
					</article>
				<?php endforeach; ?>
			</div>

			<?php if ( 'yes' === $s['show_link'] && ! empty( $s['link_text'] ) ) : ?>
				<a <?php echo $this->get_render_attribute_string( 'link' ); ?>><?php echo esc_html( $s['link_text'] ); ?> →︎</a>
			<?php endif; ?>
		</div>

		<style>
			.lk-productgrid-heading { text-align: center; }
			.lk-productgrid-heading h2 { margin: 0.3em 0; font-style: normal; }
			.lk-productgrid-heading p.lk-productgrid-desc { margin: 0 auto; max-width: 560px; }
			.lk-productgrid-grid { display: grid; }
			.lk-product { text-align: center; }
			.lk-product-image { aspect-ratio: 1; overflow: hidden; margin-bottom: 18px; }
			.lk-product-image img { width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-product h3 { margin: 0 0 4px; font-weight: 400; }
			.lk-product-price { margin-bottom: 16px; }
			.lk-product-btn { display: inline-flex; align-items: center; justify-content: center; width: 100%; text-decoration: none; border: 1px solid transparent; border-radius: 0; cursor: pointer; transition: background .3s ease, color .3s ease; box-sizing: border-box; }
			.lk-productgrid-link { display: flex; align-items: center; gap: 6px; width: max-content; margin: 50px auto 0; text-decoration: none; }
			@media (max-width: 540px) {
				.lk-productgrid-grid { grid-template-columns: repeat(2, 1fr) !important; }
			}
		</style>
		<?php
	}
}
