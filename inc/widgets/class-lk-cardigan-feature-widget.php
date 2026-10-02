<?php
/**
 * Lila Kora — Cardigan Feature Widget
 *
 * "One timeless base. Made personal at the cuffs." — a burgundy-soft
 * dark panel with copy + a numbered options list on one side, and an
 * overlapping two-image visual (main shot + a bottom-right detail
 * shot framed in a thick border) on the other.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-cardigan-feature-widget.php';
 *   $widgets_manager->register( new \LK_Cardigan_Feature_Widget() );
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

class LK_Cardigan_Feature_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-cardigan-feature';
	}

	public function get_title() {
		return 'LK — Cardigan Feature';
	}

	public function get_icon() {
		return 'eicon-product-images';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'cardigan', 'cuffs', 'feature' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'The Interchangeable Cardigan', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'One timeless base.', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — emphasized line', 'type' => Controls_Manager::TEXT, 'default' => 'Made personal at the cuffs.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Choose from our existing cuff designs, or create an original artwork and have it transformed into your own interchangeable cuffs. One cardigan, with more than one way to make it yours.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Options List
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_options',
			array( 'label' => 'Options List', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$repeater->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Option text', 'label_block' => true ) );

		$this->add_control(
			'options',
			array(
				'label' => 'Rows', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'number' => '01', 'text' => 'Choose an existing cuff design' ),
					array( 'number' => '02', 'text' => 'Create your own cuffs' ),
				),
				'title_field' => '{{{ number }}} — {{{ text }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Button
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_button',
			array( 'label' => 'Button', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'btn_text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Explore the Cardigan', 'label_block' => true ) );
		$this->add_control( 'btn_link', array( 'label' => 'Link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Images
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_images',
			array( 'label' => 'Images', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'main_image', array( 'label' => 'Main image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'main_image', 'default' => 'large' ) );
		$this->add_control( 'detail_image', array( 'label' => 'Detail image (bottom-right overlay)', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'detail_image', 'default' => 'medium' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#7A2C42', 'selectors' => array( '{{WRAPPER}} .lk-cardigan' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-cardigan' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-cardigan' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'columns_gap',
			array(
				'label' => 'Gap between text & visual', 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', 'vw' ),
				'range' => array( 'px' => array( 'min' => 0, 'max' => 200 ), 'vw' => array( 'min' => 0, 'max' => 15 ) ),
				'default' => array( 'unit' => 'vw', 'size' => 8 ),
				'description' => 'Reference: clamp(55px, 8vw, 120px).',
				'selectors' => array( '{{WRAPPER}} .lk-cardigan' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'copy_max_width',
			array( 'label' => 'Text column max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 800 ) ), 'default' => array( 'unit' => 'px', 'size' => 610 ), 'selectors' => array( '{{WRAPPER}} .lk-cardigan-copy' => 'max-width: {{SIZE}}{{UNIT}};' ) )
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'eyebrow_typography', 'selector' => '{{WRAPPER}} .lk-eyebrow',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ), 'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.19 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ),
		) );

		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_emphasis_color', array( 'label' => 'Emphasized line colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-heading em' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-cardigan-heading',
			'fields_options' => array(
				'font_family' => array( 'default' => 'Cormorant Garamond' ),
				'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 56 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 42 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 34 ) ),
				'font_weight' => array( 'default' => '400' ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 0.98 ) ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => -0.025 ) ),
			),
		) );

		$this->add_control( 'body_color', array( 'label' => 'Paragraph colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.72)', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-desc' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'body_typography', 'selector' => '{{WRAPPER}} .lk-cardigan-desc',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.75 ) ) ),
		) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Options List
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_options',
			array( 'label' => 'Options List', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'options_border_color', array( 'label' => 'Divider colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.25)', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-options' => 'border-color: {{VALUE}};', '{{WRAPPER}} .lk-cardigan-options > div' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'options_number_color', array( 'label' => 'Number colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-options span' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'options_number_typography', 'selector' => '{{WRAPPER}} .lk-cardigan-options span', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ) ) ) );
		$this->add_control( 'options_text_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-options strong' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'options_text_typography', 'selector' => '{{WRAPPER}} .lk-cardigan-options strong', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 20.8 ) ), 'font_weight' => array( 'default' => '400' ) ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Button
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_button',
			array( 'label' => 'Button', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'button_typography', 'selector' => '{{WRAPPER}} .lk-cardigan-btn',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ), 'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.12 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ),
		) );
		$this->start_controls_tabs( 'tabs_btn' );
		$this->start_controls_tab( 'tab_btn_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'btn_bg', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-btn' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-btn' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'btn_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-btn:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_color_hover', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-btn:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Visual
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_visual',
			array( 'label' => 'Visual', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_responsive_control(
			'visual_min_height',
			array(
				'label' => 'Height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 900 ) ),
				'default' => array( 'unit' => 'px', 'size' => 720 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 560 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 500 ),
				'description' => 'Reference: 720px desktop → 500px on the smallest screens.',
				'selectors' => array( '{{WRAPPER}} .lk-cardigan-visual' => 'min-height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'detail_border_color', array( 'label' => 'Detail image border colour', 'type' => Controls_Manager::COLOR, 'default' => '#7A2C42', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-cardigan-detail' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'detail_border_width', array( 'label' => 'Detail image border width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 30 ) ), 'default' => array( 'unit' => 'px', 'size' => 12 ), 'selectors' => array( '{{WRAPPER}} .lk-cardigan-detail' => 'border-width: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'btn', 'class', 'lk-cardigan-btn' );
		$this->add_link_attributes( 'btn', $s['btn_link'] );

		$main_image_html   = Group_Control_Image_Size::get_attachment_image_html( $s, 'main_image', 'main_image' );
		$detail_image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'detail_image', 'detail_image' );
		?>
		<div class="lk-cardigan">
			<div class="lk-cardigan-copy">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="lk-cardigan-heading"><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-cardigan-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

				<?php if ( ! empty( $s['options'] ) ) : ?>
					<div class="lk-cardigan-options">
						<?php foreach ( $s['options'] as $row ) : ?>
							<div><span><?php echo esc_html( $row['number'] ); ?></span><strong><?php echo esc_html( $row['text'] ); ?></strong></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $s['btn_text'] ) ) : ?><a <?php echo $this->get_render_attribute_string( 'btn' ); ?>><?php echo esc_html( $s['btn_text'] ); ?></a><?php endif; ?>
			</div>

			<div class="lk-cardigan-visual">
				<figure class="lk-cardigan-main"><?php echo $main_image_html; ?></figure>
				<figure class="lk-cardigan-detail"><?php echo $detail_image_html; ?></figure>
			</div>
		</div>

		<style>
			.lk-cardigan { display: grid; grid-template-columns: 0.85fr 1.15fr; align-items: center; color: white; }
			.lk-cardigan-copy { margin: 0; }
			.lk-cardigan-heading { margin: 0 0 28px; font-style: normal; } .lk-cardigan-heading em { font-style: italic; }
			.lk-cardigan-desc { margin: 0 0 34px; }
			.lk-cardigan-options { margin: 34px 0 36px; border-top: 1px solid; }
			.lk-cardigan-options > div { display: grid; grid-template-columns: 46px 1fr; align-items: center; padding: 18px 0; border-bottom: 1px solid; }
			.lk-cardigan-btn { display: inline-flex; align-items: center; justify-content: center; padding: 15px 30px; text-decoration: none; border: 1px solid transparent; border-radius: 0; white-space: nowrap; cursor: pointer; transition: background .3s ease, color .3s ease; }
			.lk-cardigan-visual { position: relative; }
			.lk-cardigan-visual figure { margin: 0; overflow: hidden; }
			.lk-cardigan-visual img { width: 100%; height: 100%; display: block; object-fit: cover; object-position: center top; }
			.lk-cardigan-main { position: absolute; inset: 0 20% 8% 0; }
			.lk-cardigan-detail { position: absolute; right: 0; bottom: 0; width: 43%; height: 54%; border-style: solid; }
			@media (max-width: 820px) {
				.lk-cardigan { grid-template-columns: 1fr; gap: 58px !important; }
			}
		</style>
		<?php
	}
}
