<?php
/**
 * Lila Kora — Box Page Hero Widget
 *
 * The hero on the "Canvas-to-Scarf Experience Box" product page:
 * eyebrow, heading, paragraph, two buttons, an italic note, and an
 * image with a caption overlay. Per client feedback (from her video
 * walkthrough), this deliberately has NO pricing block — that lives
 * only in the interactive fabric/size configurator further down the
 * page, not decoratively in the hero.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-box-page-hero-widget.php';
 *   $widgets_manager->register( new \LK_Box_Page_Hero_Widget() );
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class LK_Box_Page_Hero_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-box-page-hero';
	}

	public function get_title() {
		return 'LK — Box Page Hero';
	}

	public function get_icon() {
		return 'eicon-slider-full-screen';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'box', 'hero', 'experience box' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'The Canvas-to-Scarf Experience Box', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'A gift she doesn\'t just receive.', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — emphasized line', 'type' => Controls_Manager::TEXT, 'default' => 'A gift she creates.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'A complete creative experience that begins with a blank canvas and ends with a one-of-a-kind scarf made from her own artwork.', 'label_block' => true ) );
		$this->add_control( 'note', array( 'label' => 'Italic note (below buttons)', 'type' => Controls_Manager::TEXT, 'default' => 'No art experience needed. Guided from the first line.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Buttons
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_buttons',
			array( 'label' => 'Buttons', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'btn_primary_text', array( 'label' => 'Primary button — text', 'type' => Controls_Manager::TEXT, 'default' => 'Choose Your Box', 'label_block' => true ) );
		$this->add_control( 'btn_primary_link', array( 'label' => 'Primary button — link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#product' ), 'show_external' => true ) );
		$this->add_control( 'btn_secondary_text', array( 'label' => 'Secondary button — text', 'type' => Controls_Manager::TEXT, 'default' => 'See What\'s Inside', 'label_block' => true ) );
		$this->add_control( 'btn_secondary_link', array( 'label' => 'Secondary button — link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#inside' ), 'show_external' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Image
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_image',
			array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image', 'default' => 'large' ) );
		$this->add_control( 'image_caption', array( 'label' => 'Caption', 'type' => Controls_Manager::TEXT, 'default' => 'Designed to be created, remembered and worn.', 'label_block' => true ) );
		$this->add_control( 'image_position', array( 'label' => 'Image position', 'type' => Controls_Manager::SELECT, 'default' => 'right', 'options' => array( 'right' => 'Right', 'left' => 'Left' ), 'prefix_class' => 'lk-boxhero-img-' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#F5EBEF', 'selectors' => array( '{{WRAPPER}} .lk-boxhero' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-boxhero' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'min_height',
			array(
				'label' => 'Minimum height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 400, 'max' => 1000 ) ),
				'default' => array( 'unit' => 'px', 'size' => 830 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 650 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 0 ),
				'description' => 'Reference uses min(830px, calc(100vh - 118px)) — a plain px value here is a close approximation.',
				'selectors' => array( '{{WRAPPER}} .lk-boxhero' => 'min-height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'copy_padding', array(
			'label' => 'Text column padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
			'default' => array( 'top' => 85, 'right' => 75, 'bottom' => 85, 'left' => 75, 'unit' => 'px' ),
			'mobile_default' => array( 'top' => 65, 'right' => 24, 'bottom' => 60, 'left' => 24, 'unit' => 'px' ),
			'description' => 'Reference: 85px clamp(34px,5.2vw,92px) desktop → 65px 24px 60px 24px on mobile.',
			'selectors' => array( '{{WRAPPER}} .lk-boxhero-copy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'copy_max_width', array( 'label' => 'Text column inner max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 900 ) ), 'default' => array( 'unit' => 'px', 'size' => 720 ), 'selectors' => array( '{{WRAPPER}} .lk-boxhero-copy' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_emphasis_color', array( 'label' => 'Emphasized line colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-heading em' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-boxhero-heading',
			'fields_options' => array(
				'font_family' => array( 'default' => 'Cormorant Garamond' ),
				'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 72 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 52 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 38 ) ),
				'font_weight' => array( 'default' => '400' ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 0.98 ) ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => -0.025 ) ),
			),
		) );

		$this->add_control( 'body_color', array( 'label' => 'Paragraph colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-desc' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'body_typography', 'selector' => '{{WRAPPER}} .lk-boxhero-desc',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16.3 ) ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.9 ) ) ),
		) );

		$this->add_control( 'note_color', array( 'label' => 'Note colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-note' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'note_typography', 'selector' => '{{WRAPPER}} .lk-boxhero-note',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16.8 ) ), 'font_style' => array( 'default' => 'italic' ) ),
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
			'name' => 'button_typography', 'selector' => '{{WRAPPER}} .lk-boxhero-btn',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ), 'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.12 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ),
		) );
		$this->add_responsive_control( 'button_padding', array(
			'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px' ),
			'default' => array( 'top' => 15, 'right' => 30, 'bottom' => 15, 'left' => 30, 'unit' => 'px' ),
			'selectors' => array( '{{WRAPPER}} .lk-boxhero-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_control( 'heading_btn_primary', array( 'label' => 'Primary Button', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->start_controls_tabs( 'tabs_btn_primary' );
		$this->start_controls_tab( 'tab_btn_primary_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'btn_primary_bg', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-btn-primary' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_primary_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-btn-primary' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_primary_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'btn_primary_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-btn-primary:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_primary_color_hover', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-btn-primary:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'heading_btn_secondary', array( 'label' => 'Secondary Button', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->start_controls_tabs( 'tabs_btn_secondary' );
		$this->start_controls_tab( 'tab_btn_secondary_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'btn_secondary_border', array( 'label' => 'Border colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-btn-secondary' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_secondary_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-btn-secondary' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_secondary_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'btn_secondary_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-btn-secondary:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_secondary_color_hover', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-btn-secondary:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control( 'button_gap', array( 'label' => 'Gap between buttons', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 60 ) ), 'default' => array( 'unit' => 'px', 'size' => 16 ), 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-buttons' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Image & Caption
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_image',
			array( 'label' => 'Image & Caption', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_responsive_control( 'image_min_height', array(
			'label' => 'Image column height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 200, 'max' => 900 ) ),
			'default' => array( 'unit' => 'px', 'size' => 680 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 520 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 430 ),
			'description' => 'Matches the reference: 680px desktop → 520px tablet → 430px mobile.',
			'selectors' => array( '{{WRAPPER}} .lk-boxhero-image' => 'min-height: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_control( 'image_radius', array( 'label' => 'Image corner radius', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 60 ) ), 'default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-boxhero-image img' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );

		$this->add_control( 'caption_bg', array( 'label' => 'Caption background', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,254,253,0.9)', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-image figcaption' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'caption_color', array( 'label' => 'Caption text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxhero-image figcaption' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'caption_typography', 'selector' => '{{WRAPPER}} .lk-boxhero-image figcaption',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ), 'font_style' => array( 'default' => 'italic' ) ),
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'btn_primary', 'class', array( 'lk-boxhero-btn', 'lk-boxhero-btn-primary' ) );
		$this->add_link_attributes( 'btn_primary', $s['btn_primary_link'] );
		$this->add_render_attribute( 'btn_secondary', 'class', array( 'lk-boxhero-btn', 'lk-boxhero-btn-secondary' ) );
		$this->add_link_attributes( 'btn_secondary', $s['btn_secondary_link'] );

		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		?>
		<div class="lk-boxhero">
			<div class="lk-boxhero-copy">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h1 class="lk-boxhero-heading"><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h1>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-boxhero-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

				<div class="lk-boxhero-buttons">
					<?php if ( ! empty( $s['btn_primary_text'] ) ) : ?><a <?php echo $this->get_render_attribute_string( 'btn_primary' ); ?>><?php echo esc_html( $s['btn_primary_text'] ); ?></a><?php endif; ?>
					<?php if ( ! empty( $s['btn_secondary_text'] ) ) : ?><a <?php echo $this->get_render_attribute_string( 'btn_secondary' ); ?>><?php echo esc_html( $s['btn_secondary_text'] ); ?></a><?php endif; ?>
				</div>

				<?php if ( ! empty( $s['note'] ) ) : ?><p class="lk-boxhero-note"><?php echo esc_html( $s['note'] ); ?></p><?php endif; ?>
			</div>

			<figure class="lk-boxhero-image">
				<?php echo $image_html; ?>
				<?php if ( ! empty( $s['image_caption'] ) ) : ?><figcaption><?php echo esc_html( $s['image_caption'] ); ?></figcaption><?php endif; ?>
			</figure>
		</div>

		<style>
			.lk-boxhero { display: grid; grid-template-columns: 1fr 1fr; align-items: stretch; }
			.lk-boxhero-img-right .lk-boxhero-copy { order: 1; } .lk-boxhero-img-right .lk-boxhero-image { order: 2; }
			.lk-boxhero-img-left .lk-boxhero-copy { order: 2; } .lk-boxhero-img-left .lk-boxhero-image { order: 1; }
			.lk-boxhero-copy { margin: 0; align-self: center; }
			.lk-boxhero-heading { margin: 0 0 28px; font-style: normal; } .lk-boxhero-heading em { font-style: italic; }
			.lk-boxhero-desc { margin: 0; }
			.lk-boxhero-buttons { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; margin-top: 30px; }
			.lk-boxhero-btn { display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: 1px solid transparent; border-radius: 0; white-space: nowrap; cursor: pointer; transition: background .3s ease, color .3s ease; }
			.lk-boxhero-note { margin: 24px 0 0; font-style: italic; }
			.lk-boxhero-image { position: relative; margin: 0; overflow: hidden; }
			.lk-boxhero-image img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-boxhero-image figcaption { position: absolute; right: 26px; bottom: 25px; max-width: 265px; padding: 15px 18px; }
			@media (max-width: 820px) {
				.lk-boxhero { grid-template-columns: 1fr !important; }
				.lk-boxhero-img-right .lk-boxhero-copy, .lk-boxhero-img-left .lk-boxhero-copy { order: 2; }
				.lk-boxhero-img-right .lk-boxhero-image, .lk-boxhero-img-left .lk-boxhero-image { order: 1; }
			}
			@media (max-width: 540px) {
				.lk-boxhero-btn { width: 100%; }
			}
		</style>
		<?php
	}
}
