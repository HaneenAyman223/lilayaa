<?php
/**
 * Lila Kora — Collection Feature Widget
 *
 * "Shop ready-to-wear silk scarves." — a product image with an
 * italic caption on one side, and eyebrow/heading/paragraph/details-
 * list/button-row copy on the other.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-collection-feature-widget.php';
 *   $widgets_manager->register( new \LK_Collection_Feature_Widget() );
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

class LK_Collection_Feature_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-collection-feature';
	}

	public function get_title() {
		return 'LK — Collection Feature';
	}

	public function get_icon() {
		return 'eicon-product-images';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'collection', 'scarves', 'shop' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control(
			'eyebrow',
			array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Ready to wear · The Inception Collection', 'label_block' => true )
		);
		$this->add_control(
			'heading_main',
			array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Shop ready-to-wear', 'label_block' => true )
		);
		$this->add_control(
			'heading_emphasis',
			array( 'label' => 'Heading — emphasized line', 'type' => Controls_Manager::TEXT, 'default' => 'silk scarves.', 'label_block' => true )
		);
		$this->add_control(
			'description',
			array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Choose from four original designs and select the one that suits you. Each scarf is crafted in pure silk and finished with hand-rolled edges.', 'label_block' => true )
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Details List
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_details',
			array( 'label' => 'Details List', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'label', array( 'label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Material', 'label_block' => true ) );
		$repeater->add_control( 'value', array( 'label' => 'Value', 'type' => Controls_Manager::TEXT, 'default' => '100% pure silk', 'label_block' => true ) );

		$this->add_control(
			'details',
			array(
				'label'       => 'Rows',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'label' => 'Material', 'value' => '100% pure silk' ),
					array( 'label' => 'Finish', 'value' => 'Hand-rolled edges' ),
					array( 'label' => 'Collection', 'value' => 'Four original designs' ),
				),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Buttons & Image
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_actions',
			array( 'label' => 'Buttons & Image', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'btn_text', array( 'label' => 'Button — text', 'type' => Controls_Manager::TEXT, 'default' => 'Shop Ready-to-Wear', 'label_block' => true ) );
		$this->add_control( 'btn_link', array( 'label' => 'Button — link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true ) );
		$this->add_control( 'underlink_text', array( 'label' => 'Text link — label', 'type' => Controls_Manager::TEXT, 'default' => 'View all ways to shop', 'label_block' => true ) );
		$this->add_control( 'underlink_link', array( 'label' => 'Text link — url', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#shop' ), 'show_external' => true ) );

		$this->add_control( 'image_divider', array( 'type' => Controls_Manager::DIVIDER ) );
		$this->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image', 'default' => 'large' ) );
		$this->add_control( 'image_caption', array( 'label' => 'Image caption', 'type' => Controls_Manager::TEXT, 'default' => 'Original artwork, translated onto silk.', 'label_block' => true ) );
		$this->add_control(
			'image_position',
			array( 'label' => 'Image position', 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => array( 'left' => 'Left', 'right' => 'Right' ), 'prefix_class' => 'lk-collfeat-img-' )
		);

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
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER,
				'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-collfeat' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'description' => 'Reference: clamp(90px, 10vw, 155px) 4.5vw desktop → 90px 24px at 820px and below.',
				'selectors' => array( '{{WRAPPER}} .lk-collfeat' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'columns_gap',
			array(
				'label' => 'Gap between image & text', 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', 'vw' ),
				'range' => array( 'px' => array( 'min' => 0, 'max' => 200 ), 'vw' => array( 'min' => 0, 'max' => 15 ) ),
				'default' => array( 'unit' => 'vw', 'size' => 8 ),
				'description' => 'Reference: clamp(55px, 8vw, 125px).',
				'selectors' => array( '{{WRAPPER}} .lk-collfeat' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'copy_max_width',
			array( 'label' => 'Text column max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 800 ) ), 'default' => array( 'unit' => 'px', 'size' => 630 ), 'selectors' => array( '{{WRAPPER}} .lk-collfeat-copy' => 'max-width: {{SIZE}}{{UNIT}};' ) )
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'eyebrow_typography', 'selector' => '{{WRAPPER}} .lk-eyebrow',
			'fields_options' => array(
				'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ),
				'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.19 ) ), 'text_transform' => array( 'default' => 'uppercase' ),
			),
		) );

		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_emphasis_color', array( 'label' => 'Emphasized line colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-heading em' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-collfeat-heading',
			'fields_options' => array(
				'font_family' => array( 'default' => 'Cormorant Garamond' ),
				'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 56 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 42 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 34 ) ),
				'font_weight' => array( 'default' => '400' ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 0.98 ) ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => -0.025 ) ),
			),
		) );

		$this->add_control( 'body_color', array( 'label' => 'Paragraph colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-copy p.lk-collfeat-desc' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'body_typography', 'selector' => '{{WRAPPER}} .lk-collfeat-copy p.lk-collfeat-desc',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.75 ) ) ),
		) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Details List
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_details',
			array( 'label' => 'Details List', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'details_border_color', array( 'label' => 'Divider colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.16)', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-details' => 'border-color: {{VALUE}};', '{{WRAPPER}} .lk-collfeat-details > div' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'details_label_color', array( 'label' => 'Label colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-details span' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'details_label_typography', 'selector' => '{{WRAPPER}} .lk-collfeat-details span',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 10.7 ) ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.1 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ),
		) );
		$this->add_control( 'details_value_color', array( 'label' => 'Value colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-details strong' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'details_value_typography', 'selector' => '{{WRAPPER}} .lk-collfeat-details strong',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 18.4 ) ), 'font_weight' => array( 'default' => '500' ) ),
		) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Buttons
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_buttons',
			array( 'label' => 'Buttons', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'button_typography', 'selector' => '{{WRAPPER}} .lk-collfeat-btn',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ), 'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.12 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ),
		) );
		$this->start_controls_tabs( 'tabs_btn' );
		$this->start_controls_tab( 'tab_btn_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'btn_bg', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-btn' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-btn' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'btn_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-btn:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_color_hover', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-btn:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'underlink_color', array( 'label' => 'Text link colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-underlink' => 'color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'underlink_typography', 'selector' => '{{WRAPPER}} .lk-collfeat-underlink',
			'fields_options' => array(
				'font_family'    => array( 'default' => 'Montserrat' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 11.5 ) ),
				'font_weight'    => array( 'default' => '600' ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.1 ) ),
				'text_transform' => array( 'default' => 'uppercase' ),
			),
		) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Image
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_image',
			array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'image_bg', array( 'label' => 'Frame background', 'type' => Controls_Manager::COLOR, 'default' => '#F8F3ED', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-image' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'image_height',
			array(
				'label' => 'Height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 200, 'max' => 900 ) ),
				'default' => array( 'unit' => 'px', 'size' => 720 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 560 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 420 ),
				'description' => 'Reference: 720px desktop → 560px at 820px and below.',
				'selectors' => array( '{{WRAPPER}} .lk-collfeat-image img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;' ),
			)
		);
		$this->add_control( 'image_radius', array( 'label' => 'Corner radius', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-collfeat-image img' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'caption_color', array( 'label' => 'Caption colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-collfeat-image figcaption' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'caption_typography', 'selector' => '{{WRAPPER}} .lk-collfeat-image figcaption',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ), 'font_style' => array( 'default' => 'italic' ) ),
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'btn', 'class', 'lk-collfeat-btn' );
		$this->add_link_attributes( 'btn', $s['btn_link'] );
		$this->add_render_attribute( 'underlink', 'class', 'lk-collfeat-underlink' );
		$this->add_link_attributes( 'underlink', $s['underlink_link'] );

		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		?>
		<div class="lk-collfeat">
			<figure class="lk-collfeat-image">
				<?php echo $image_html; ?>
				<?php if ( ! empty( $s['image_caption'] ) ) : ?>
					<figcaption><?php echo esc_html( $s['image_caption'] ); ?></figcaption>
				<?php endif; ?>
			</figure>

			<div class="lk-collfeat-copy">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="lk-collfeat-heading"><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-collfeat-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

				<?php if ( ! empty( $s['details'] ) ) : ?>
					<div class="lk-collfeat-details">
						<?php foreach ( $s['details'] as $row ) : ?>
							<div><span><?php echo esc_html( $row['label'] ); ?></span><strong><?php echo esc_html( $row['value'] ); ?></strong></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="lk-collfeat-buttons">
					<?php if ( ! empty( $s['btn_text'] ) ) : ?><a <?php echo $this->get_render_attribute_string( 'btn' ); ?>><?php echo esc_html( $s['btn_text'] ); ?></a><?php endif; ?>
					<?php if ( ! empty( $s['underlink_text'] ) ) : ?><a <?php echo $this->get_render_attribute_string( 'underlink' ); ?>><?php echo esc_html( $s['underlink_text'] ); ?> <span>↗︎</span></a><?php endif; ?>
				</div>
			</div>
		</div>

		<style>
			.lk-collfeat { display: grid; grid-template-columns: 1.05fr 0.95fr; align-items: center; }
			.lk-collfeat-img-left .lk-collfeat-image { order: 1; } .lk-collfeat-img-left .lk-collfeat-copy { order: 2; }
			.lk-collfeat-img-right .lk-collfeat-image { order: 2; } .lk-collfeat-img-right .lk-collfeat-copy { order: 1; }
			.lk-collfeat-image { margin: 0; overflow: hidden; }
			.lk-collfeat-image img { width: 100%; display: block; object-fit: cover; object-position: center; }
			.lk-collfeat-image figcaption { padding-top: 13px; text-align: right; font-style: italic; }
			.lk-collfeat-heading { margin: 0 0 20px; font-style: normal; } .lk-collfeat-heading em { font-style: italic; }
			.lk-collfeat-desc { margin: 0; }
			.lk-collfeat-details { margin: 34px 0 35px; border-top: 1px solid; }
			.lk-collfeat-details > div { display: flex; justify-content: space-between; gap: 25px; padding: 15px 0; border-bottom: 1px solid; }
			.lk-collfeat-details strong { text-align: right; }
			.lk-collfeat-buttons { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; }
			.lk-collfeat-btn { display: inline-flex; align-items: center; justify-content: center; padding: 15px 30px; text-decoration: none; border: 1px solid transparent; border-radius: 0; white-space: nowrap; cursor: pointer; transition: background .3s ease, color .3s ease; }
			.lk-collfeat-underlink { display: inline-flex; align-items: center; gap: 9px; text-decoration: none; border-bottom: 1px solid currentColor; padding-bottom: 4px; }
			.lk-collfeat-underlink span { transition: transform .25s ease; }
			.lk-collfeat-underlink:hover span { transform: translate(3px, -3px); }
			@media (max-width: 820px) {
				.lk-collfeat { grid-template-columns: 1fr; gap: 65px !important; }
				.lk-collfeat-img-left .lk-collfeat-image, .lk-collfeat-img-right .lk-collfeat-image { order: 1; }
				.lk-collfeat-img-left .lk-collfeat-copy, .lk-collfeat-img-right .lk-collfeat-copy { order: 2; }
			}
		</style>
		<?php
	}
}
