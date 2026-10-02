<?php
/**
 * Lila Kora — Box Gifting Widget
 *
 * "A gift she creates herself." — a two-column block (image with
 * caption + copy with an occasion-tag list and a button), topped off
 * by a full-width dark "corporate & branded gifting" sub-block that
 * spans both columns beneath it.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-box-gifting-widget.php';
 *   $widgets_manager->register( new \LK_Box_Gifting_Widget() );
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

class LK_Box_Gifting_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-box-gifting';
	}

	public function get_title() {
		return 'LK — Box Gifting';
	}

	public function get_icon() {
		return 'eicon-gift';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'box', 'gifting', 'corporate', 'experience box' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text & Image
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_text',
			array( 'label' => 'Text & Image', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Made for meaningful gifting', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'A gift she creates', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — emphasized line', 'type' => Controls_Manager::TEXT, 'default' => 'herself.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Give her more than something to unwrap. Give her time to create, a story to shape and a one-of-a-kind scarf that carries the memory long after.', 'label_block' => true ) );

		$this->add_control( 'image_divider', array( 'type' => Controls_Manager::DIVIDER ) );
		$this->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image', 'default' => 'large' ) );
		$this->add_control( 'image_caption', array( 'label' => 'Caption', 'type' => Controls_Manager::TEXT, 'default' => 'Made to be opened, created and remembered.', 'label_block' => true ) );
		$this->add_control( 'image_position', array( 'label' => 'Image position', 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => array( 'left' => 'Left', 'right' => 'Right' ), 'prefix_class' => 'lk-boxgift-img-' ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Occasions & Button
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_occasions',
			array( 'label' => 'Occasions & Button', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Occasion', 'label_block' => true ) );

		$this->add_control(
			'occasions',
			array(
				'label' => 'Occasions', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'text' => 'Birthdays' ), array( 'text' => 'Milestones' ), array( 'text' => 'Bridal gifting' ),
					array( 'text' => 'Mother and daughter' ), array( 'text' => 'Creative self-care' ), array( 'text' => 'Just because' ),
				),
				'title_field' => '{{{ text }}}',
			)
		);

		$this->add_control( 'btn_text', array( 'label' => 'Button — text', 'type' => Controls_Manager::TEXT, 'default' => 'Choose a Gift', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'btn_link', array( 'label' => 'Button — link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Corporate Sub-block
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_business',
			array( 'label' => 'Corporate Sub-block', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'business_eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Corporate and branded gifting', 'label_block' => true ) );
		$this->add_control( 'business_heading', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'A creative gift for clients, teams and communities.', 'label_block' => true ) );
		$this->add_control( 'business_description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'For larger gifting requirements, we can tailor selected details, materials and presentation around your occasion or brand.', 'label_block' => true ) );
		$this->add_control( 'business_link_text', array( 'label' => 'Link — text', 'type' => Controls_Manager::TEXT, 'default' => 'Request a gifting proposal', 'label_block' => true ) );
		$this->add_control( 'business_link_url', array( 'label' => 'Link — url', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#enquire' ), 'show_external' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#F8ECE9', 'selectors' => array( '{{WRAPPER}} .lk-boxgift' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-boxgift' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control( 'copy_padding', array(
			'label' => 'Text column padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px' ),
			'default' => array( 'top' => 100, 'right' => 65, 'bottom' => 100, 'left' => 65, 'unit' => 'px' ),
			'mobile_default' => array( 'top' => 80, 'right' => 24, 'bottom' => 80, 'left' => 24, 'unit' => 'px' ),
			'selectors' => array( '{{WRAPPER}} .lk-boxgift-copy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'image_min_height', array(
			'label' => 'Image column height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 900 ) ),
			'default' => array( 'unit' => 'px', 'size' => 760 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 560 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 480 ),
			'selectors' => array( '{{WRAPPER}} .lk-boxgift-image' => 'min-height: {{SIZE}}{{UNIT}};' ),
		) );

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
				'font_family'    => array( 'default' => 'Montserrat' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ),
				'font_weight'    => array( 'default' => '600' ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.19 ) ),
				'text_transform' => array( 'default' => 'uppercase' ),
			),
		) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-boxgift-heading',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 56 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 34 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );
		$this->add_control( 'desc_color', array( 'label' => 'Paragraph colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-desc' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'desc_typography', 'selector' => '{{WRAPPER}} .lk-boxgift-desc', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.75 ) ) ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Occasions & Button
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_occasions',
			array( 'label' => 'Occasions & Button', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'occasion_border_color', array( 'label' => 'Divider colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.16)', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-occasions' => 'border-color: {{VALUE}};', '{{WRAPPER}} .lk-boxgift-occasions span' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'occasion_mark_color', array( 'label' => 'Bullet mark colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-occasions span::before' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'occasion_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-occasions span' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'occasion_typography', 'selector' => '{{WRAPPER}} .lk-boxgift-occasions span', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16.8 ) ) ) ) );

		$this->add_control( 'heading_btn', array( 'label' => 'Button', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'btn_typography', 'selector' => '{{WRAPPER}} .lk-boxgift-btn', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ), 'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.12 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ) ) );
		$this->start_controls_tabs( 'tabs_btn' );
		$this->start_controls_tab( 'tab_btn_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'btn_bg', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-btn' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-btn' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'btn_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-btn:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_color_hover', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-btn:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Image & Caption
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_image',
			array( 'label' => 'Image & Caption', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'caption_bg', array( 'label' => 'Caption background', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.86)', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-image figcaption' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'caption_color', array( 'label' => 'Caption text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-image figcaption' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'caption_typography', 'selector' => '{{WRAPPER}} .lk-boxgift-image figcaption',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ), 'font_style' => array( 'default' => 'italic' ) ),
		) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Corporate Sub-block
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_business',
			array( 'label' => 'Corporate Sub-block', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'business_bg', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-business' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'business_padding', array(
			'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px' ),
			'default' => array( 'top' => 88, 'right' => 65, 'bottom' => 88, 'left' => 65, 'unit' => 'px' ),
			'mobile_default' => array( 'top' => 60, 'right' => 24, 'bottom' => 60, 'left' => 24, 'unit' => 'px' ),
			'selectors' => array( '{{WRAPPER}} .lk-boxgift-business' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );
		$this->add_control( 'business_eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-business .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'business_heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-business h3' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'business_heading_typography', 'selector' => '{{WRAPPER}} .lk-boxgift-business h3', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 44 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 30 ) ), 'font_style' => array( 'default' => 'italic' ), 'font_weight' => array( 'default' => '400' ) ) ) );
		$this->add_control( 'business_desc_color', array( 'label' => 'Paragraph colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.68)', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-business-copy p' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'business_desc_typography', 'selector' => '{{WRAPPER}} .lk-boxgift-business-copy p', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ) ) ) );
		$this->add_control( 'business_link_color', array( 'label' => 'Link colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxgift-business a' => 'color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'business_link_typography', 'selector' => '{{WRAPPER}} .lk-boxgift-business a', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 12.8 ) ) ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'btn', 'class', 'lk-boxgift-btn' );
		$this->add_link_attributes( 'btn', $s['btn_link'] );
		$this->add_render_attribute( 'business_link', 'class', 'lk-boxgift-business-link' );
		$this->add_link_attributes( 'business_link', $s['business_link_url'] );

		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		?>
		<div class="lk-boxgift">
			<figure class="lk-boxgift-image">
				<?php echo $image_html; ?>
				<?php if ( ! empty( $s['image_caption'] ) ) : ?><figcaption><?php echo esc_html( $s['image_caption'] ); ?></figcaption><?php endif; ?>
			</figure>

			<div class="lk-boxgift-copy">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="lk-boxgift-heading"><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-boxgift-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

				<?php if ( ! empty( $s['occasions'] ) ) : ?>
					<div class="lk-boxgift-occasions">
						<?php foreach ( $s['occasions'] as $item ) : ?><span><?php echo esc_html( $item['text'] ); ?></span><?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $s['btn_text'] ) ) : ?><a <?php echo $this->get_render_attribute_string( 'btn' ); ?>><?php echo esc_html( $s['btn_text'] ); ?></a><?php endif; ?>
			</div>

			<div class="lk-boxgift-business">
				<div>
					<?php if ( ! empty( $s['business_eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['business_eyebrow'] ); ?></p><?php endif; ?>
					<h3><?php echo esc_html( $s['business_heading'] ); ?></h3>
				</div>
				<div class="lk-boxgift-business-copy">
					<?php if ( ! empty( $s['business_description'] ) ) : ?><p><?php echo esc_html( $s['business_description'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $s['business_link_text'] ) ) : ?><a <?php echo $this->get_render_attribute_string( 'business_link' ); ?>><?php echo esc_html( $s['business_link_text'] ); ?> <span>↗︎</span></a><?php endif; ?>
				</div>
			</div>
		</div>

		<style>
			.lk-boxgift { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
			.lk-boxgift-img-right .lk-boxgift-image { order: 2; } .lk-boxgift-img-right .lk-boxgift-copy { order: 1; }
			.lk-boxgift-img-left .lk-boxgift-image { order: 1; } .lk-boxgift-img-left .lk-boxgift-copy { order: 2; }
			.lk-boxgift-image { position: relative; margin: 0; overflow: hidden; }
			.lk-boxgift-image img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-boxgift-image figcaption { position: absolute; left: 34px; bottom: 30px; padding: 12px 17px; }
			.lk-boxgift-copy { align-self: center; }
			.lk-boxgift-heading { margin: 0 0 20px; font-style: normal; } .lk-boxgift-heading em { font-style: italic; }
			.lk-boxgift-desc { margin: 0; max-width: 610px; }
			.lk-boxgift-occasions { display: grid; grid-template-columns: 1fr 1fr; max-width: 610px; margin: 38px 0 42px; border-top: 1px solid; }
			.lk-boxgift-occasions span { position: relative; padding: 15px 10px 15px 24px; border-bottom: 1px solid; font-style: normal; }
			.lk-boxgift-occasions span::before { content: "\2726"; position: absolute; left: 0; font-size: 11px; }
			.lk-boxgift-btn { display: inline-flex; align-items: center; justify-content: center; padding: 15px 30px; text-decoration: none; border: 1px solid transparent; border-radius: 0; white-space: nowrap; cursor: pointer; transition: background .3s ease, color .3s ease; }
			.lk-boxgift-business { grid-column: 1 / -1; display: grid; grid-template-columns: 1fr 1fr; gap: 7vw; align-items: center; color: #fff; }
			.lk-boxgift-business h3 { max-width: 680px; margin: 0; font-style: italic; line-height: 1.08; }
			.lk-boxgift-business-copy > p { max-width: 600px; margin: 0 0 16px; }
			.lk-boxgift-business a { text-decoration: none; border-bottom: 1px solid currentColor; padding-bottom: 2px; }
			@media (max-width: 820px) {
				.lk-boxgift { grid-template-columns: 1fr; }
				.lk-boxgift-copy { padding: 80px 24px !important; }
				.lk-boxgift-business { grid-template-columns: 1fr; gap: 30px; }
			}
			@media (max-width: 540px) {
				.lk-boxgift-occasions { grid-template-columns: 1fr; }
			}
		</style>
		<?php
	}
}
