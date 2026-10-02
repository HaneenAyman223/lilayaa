<?php
/**
 * Lila Kora — Final CTA Widget
 *
 * The closing burgundy call-to-action: a decorative circle line
 * behind the text, eyebrow, two-line heading, paragraph, two centred
 * buttons, and a closing enquiry link.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-final-cta-widget.php';
 *   $widgets_manager->register( new \LK_Final_CTA_Widget() );
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class LK_Final_CTA_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-final-cta';
	}

	public function get_title() {
		return 'LK — Final CTA';
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'cta', 'enquire', 'closing' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Your way to wear Lila Kora', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Choose one that speaks to you.', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — emphasized line', 'type' => Controls_Manager::TEXT, 'default' => 'Or create one that could only be yours.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'Discover the collection or begin your own Canvas-to-Scarf story.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Buttons & Link
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_actions',
			array( 'label' => 'Buttons & Link', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'btn_primary_text', array( 'label' => 'Primary button — text', 'type' => Controls_Manager::TEXT, 'default' => 'Shop the Collection', 'label_block' => true ) );
		$this->add_control( 'btn_primary_link', array( 'label' => 'Primary button — link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true ) );
		$this->add_control( 'btn_secondary_text', array( 'label' => 'Secondary button — text', 'type' => Controls_Manager::TEXT, 'default' => 'Create Your Own', 'label_block' => true ) );
		$this->add_control( 'btn_secondary_link', array( 'label' => 'Secondary button — link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true ) );

		$this->add_control( 'link_divider', array( 'type' => Controls_Manager::DIVIDER ) );
		$this->add_control( 'show_link', array( 'label' => 'Show enquiry link', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$this->add_control( 'link_text', array( 'label' => 'Link text', 'type' => Controls_Manager::TEXT, 'default' => 'Enquire about private and business experiences', 'label_block' => true, 'condition' => array( 'show_link' => 'yes' ) ) );
		$this->add_control( 'link_url', array( 'label' => 'Link URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => 'mailto:hello@example.com' ), 'show_external' => true, 'label_block' => true, 'condition' => array( 'show_link' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Section
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_section',
			array( 'label' => 'Section', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-finalcta' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-finalcta' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 145, 'right' => 72, 'bottom' => 145, 'left' => 72, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 105, 'right' => 24, 'bottom' => 105, 'left' => 24, 'unit' => 'px' ),
				'description' => 'Reference: 145px 5vw desktop → 105px 24px on mobile.',
				'selectors' => array( '{{WRAPPER}} .lk-finalcta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'circle_color', array( 'label' => 'Decorative circle colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,197,186,0.22)', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-circle' => 'border-color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'selectors' => array( '{{WRAPPER}} .lk-finalcta .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_emphasis_color', array( 'label' => 'Emphasized line colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-heading em' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-finalcta-heading',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 56 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 34 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );
		$this->add_control( 'body_color', array( 'label' => 'Paragraph colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.68)', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-desc' => 'color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Buttons & Link
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_buttons',
			array( 'label' => 'Buttons & Link', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'button_typography', 'selector' => '{{WRAPPER}} .lk-finalcta-btn',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ), 'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.12 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ),
		) );
		$this->add_control( 'heading_btn_primary', array( 'label' => 'Primary Button (light)', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->start_controls_tabs( 'tabs_btn_primary' );
		$this->start_controls_tab( 'tab_btn_primary_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'btn_primary_bg', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-btn-primary' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_primary_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-btn-primary' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_primary_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'btn_primary_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'transparent', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-btn-primary:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_primary_color_hover', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-btn-primary:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'heading_btn_secondary', array( 'label' => 'Secondary Button (ghost)', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->start_controls_tabs( 'tabs_btn_secondary' );
		$this->start_controls_tab( 'tab_btn_secondary_normal', array( 'label' => 'Normal' ) );
		$this->add_control( 'btn_secondary_border', array( 'label' => 'Border colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(255,255,255,0.48)', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-btn-secondary' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_secondary_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-btn-secondary' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_btn_secondary_hover', array( 'label' => 'Hover' ) );
		$this->add_control( 'btn_secondary_bg_hover', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-btn-secondary:hover' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'btn_secondary_color_hover', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-finalcta-btn-secondary:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control( 'link_color', array( 'label' => 'Enquiry link colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'separator' => 'before', 'condition' => array( 'show_link' => 'yes' ), 'selectors' => array( '{{WRAPPER}} .lk-finalcta-link' => 'color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'link_typography', 'selector' => '{{WRAPPER}} .lk-finalcta-link',
			'condition' => array( 'show_link' => 'yes' ),
			'fields_options' => array(
				'font_family'    => array( 'default' => 'Montserrat' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 11.5 ) ),
				'font_weight'    => array( 'default' => '600' ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.1 ) ),
				'text_transform' => array( 'default' => 'uppercase' ),
			),
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'btn_primary', 'class', array( 'lk-finalcta-btn', 'lk-finalcta-btn-primary' ) );
		$this->add_link_attributes( 'btn_primary', $s['btn_primary_link'] );
		$this->add_render_attribute( 'btn_secondary', 'class', array( 'lk-finalcta-btn', 'lk-finalcta-btn-secondary' ) );
		$this->add_link_attributes( 'btn_secondary', $s['btn_secondary_link'] );
		if ( 'yes' === $s['show_link'] ) {
			$this->add_render_attribute( 'link', 'class', 'lk-finalcta-link' );
			$this->add_link_attributes( 'link', $s['link_url'] );
		}
		?>
		<section class="lk-finalcta">
			<div class="lk-finalcta-circle" aria-hidden="true"></div>
			<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
			<h2 class="lk-finalcta-heading"><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h2>
			<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-finalcta-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

			<div class="lk-finalcta-buttons">
				<?php if ( ! empty( $s['btn_primary_text'] ) ) : ?><a <?php echo $this->get_render_attribute_string( 'btn_primary' ); ?>><?php echo esc_html( $s['btn_primary_text'] ); ?></a><?php endif; ?>
				<?php if ( ! empty( $s['btn_secondary_text'] ) ) : ?><a <?php echo $this->get_render_attribute_string( 'btn_secondary' ); ?>><?php echo esc_html( $s['btn_secondary_text'] ); ?></a><?php endif; ?>
			</div>

			<?php if ( 'yes' === $s['show_link'] && ! empty( $s['link_text'] ) ) : ?>
				<a <?php echo $this->get_render_attribute_string( 'link' ); ?>><?php echo esc_html( $s['link_text'] ); ?> <span>↗︎</span></a>
			<?php endif; ?>
		</section>

		<style>
			.lk-finalcta { position: relative; overflow: hidden; text-align: center; color: white; }
			.lk-finalcta-circle { position: absolute; width: 700px; height: 700px; left: 50%; top: -535px; transform: translateX(-50%); border-style: solid; border-width: 1px; border-radius: 50%; pointer-events: none; }
			.lk-finalcta-heading { position: relative; margin: 0 auto 25px; font-style: normal; } .lk-finalcta-heading em { font-style: italic; }
			.lk-finalcta-desc { position: relative; margin: 0; }
			.lk-finalcta-buttons { position: relative; display: flex; flex-wrap: wrap; justify-content: center; gap: 16px; margin-top: 32px; }
			.lk-finalcta-btn { display: inline-flex; align-items: center; justify-content: center; padding: 15px 30px; text-decoration: none; border: 1px solid transparent; border-radius: 0; white-space: nowrap; cursor: pointer; transition: background .3s ease, color .3s ease; }
			.lk-finalcta-link { position: relative; display: inline-flex; align-items: center; gap: 8px; margin-top: 30px; text-decoration: none; border-bottom: 1px solid currentColor; padding-bottom: 2px; }
		</style>
		<?php
	}
}
