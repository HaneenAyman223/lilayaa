<?php
/**
 * Lila Kora — Enquiry Prompts Widget
 *
 * "Tell us what you are celebrating." — the dark enquiry band: on the left a
 * champagne eyebrow, a two-line heading (second line italic) and a paragraph;
 * on the right a hairline-separated list of numbered prompts with a button
 * underneath. The button defaults to a WhatsApp enquiry — set the number and
 * message and the link is built for you — or switch it to a custom URL.
 * The section gets the anchor ID "enquire" by default, so buttons elsewhere
 * on the page can link to #enquire.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-enquiry-prompts-widget.php';
 *   $widgets_manager->register( new \LK_Enquiry_Prompts_Widget() );
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;

class LK_Enquiry_Prompts_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-enquiry-prompts';
	}

	public function get_title() {
		return 'LK — Enquiry Prompts';
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'enquiry', 'enquire', 'whatsapp', 'prompts', 'plan', 'cta' );
	}

	private function typo( $name, $label, $selector, $family, $size, $weight = '', $spacing = null, $upper = false, $line = null, $tablet = null, $mobile = null, $italic = false ) {
		$size_opt = array( 'default' => array( 'unit' => 'px', 'size' => $size ) );
		if ( null !== $tablet ) {
			$size_opt['tablet_default'] = array( 'unit' => 'px', 'size' => $tablet );
		}
		if ( null !== $mobile ) {
			$size_opt['mobile_default'] = array( 'unit' => 'px', 'size' => $mobile );
		}
		$fields = array( 'font_family' => array( 'default' => $family ), 'font_size' => $size_opt );
		if ( '' !== $weight ) {
			$fields['font_weight'] = array( 'default' => $weight );
		}
		if ( null !== $spacing ) {
			$fields['letter_spacing'] = array( 'default' => array( 'unit' => 'em', 'size' => $spacing ) );
		}
		if ( $upper ) {
			$fields['text_transform'] = array( 'default' => 'uppercase' );
		}
		if ( $italic ) {
			$fields['font_style'] = array( 'default' => 'italic' );
		}
		if ( null !== $line ) {
			$fields['line_height'] = array( 'default' => array( 'unit' => 'em', 'size' => $line ) );
		}
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => $name, 'label' => $label, 'selector' => '{{WRAPPER}} ' . $selector, 'fields_options' => $fields )
		);
	}

	private function color( $id, $label, $selector, $default, $prop = 'color', $separator = false ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'default'   => $default,
			'selectors' => array( '{{WRAPPER}} ' . $selector => $prop . ': {{VALUE}};' ),
		);
		if ( $separator ) {
			$args['separator'] = 'before';
		}
		$this->add_control( $id, $args );
	}

	private function layout_controls( $root, $bg, $pad_top, $pad_bottom ) {
		$this->color( 'bg_color', 'Background', $root, $bg, 'background-color' );
		$this->add_responsive_control( 'section_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} ' . $root => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => 'Section padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => $pad_top, 'right' => 65, 'bottom' => $pad_bottom, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 32, 'bottom' => 90, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 80, 'right' => 24, 'bottom' => 80, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} ' . $root => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
	}

	protected function register_controls() {

		$this->start_controls_section( 'section_content_heading', array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Plan your private event', 'label_block' => true ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Tell us what', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic second line', 'type' => Controls_Manager::TEXT, 'default' => 'you are celebrating.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Share the details you already know. We will help shape the rest and return with a tailored proposal.', 'label_block' => true ) );
		$this->add_control( 'section_id', array( 'label' => 'Anchor ID (optional)', 'type' => Controls_Manager::TEXT, 'default' => 'enquire', 'description' => 'So a button elsewhere on the page can link to #enquire.' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_content_prompts', array( 'label' => 'Prompts & Button', 'tab' => Controls_Manager::TAB_CONTENT ) );
		$item = new Repeater();
		$item->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$item->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Prompt', 'label_block' => true ) );
		$this->add_control(
			'items',
			array(
				'label'       => 'Prompts',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $item->get_controls(),
				'default'     => array(
					array( 'number' => '01', 'text' => 'Preferred date and location' ),
					array( 'number' => '02', 'text' => 'Occasion and estimated number of guests' ),
					array( 'number' => '03', 'text' => 'Art session only or scarf transformation' ),
				),
				'title_field' => '{{{ number }}} — {{{ text }}}',
			)
		);
		$this->add_control( 'button_text', array( 'label' => 'Button text', 'type' => Controls_Manager::TEXT, 'default' => 'Enquire on WhatsApp', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'button_mode', array( 'label' => 'Button opens', 'type' => Controls_Manager::SELECT, 'default' => 'whatsapp', 'options' => array( 'whatsapp' => 'A WhatsApp message', 'url' => 'A custom link' ) ) );
		$this->add_control( 'whatsapp_number', array( 'label' => 'WhatsApp number (with country code, no + or spaces)', 'type' => Controls_Manager::TEXT, 'default' => '971589610166', 'condition' => array( 'button_mode' => 'whatsapp' ) ) );
		$this->add_control( 'message', array( 'label' => 'WhatsApp message', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Hello Lila Kora, I would like to plan a private Canvas-to-Scarf event.', 'label_block' => true, 'condition' => array( 'button_mode' => 'whatsapp' ) ) );
		$this->add_control( 'button_url', array( 'label' => 'Link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '' ), 'show_external' => true, 'condition' => array( 'button_mode' => 'url' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->layout_controls( '.lk-enq', '#7A2C42', 140, 140 );
		$this->add_responsive_control( 'columns_gap', array( 'label' => 'Gap between columns', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 220 ) ), 'default' => array( 'unit' => 'px', 'size' => 130 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 50 ), 'selectors' => array( '{{WRAPPER}} .lk-enq' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-enq-eyebrow', '#FFC5BA' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-enq-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-enq-heading', '#FFFFFF', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic line colour', '.lk-enq-heading em', '#FFFFFF' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-enq-heading', 'Cormorant Garamond', 76, '400', -0.025, false, 1.0, 54, 38 );
		$this->color( 'desc_color', 'Paragraph colour', '.lk-enq-desc', 'rgba(255,255,255,0.7)', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-enq-desc', 'Montserrat', 16, '', null, false, 1.8 );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_prompts', array( 'label' => 'Prompts', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->color( 'list_line_color', 'Divider colour', '.lk-enq-list, {{WRAPPER}} .lk-enq-list li', 'rgba(255,255,255,0.23)', 'border-color' );
		$this->color( 'prompt_number_color', 'Number colour', '.lk-enq-list li > span', '#FF715E', 'color', true );
		$this->typo( 'prompt_number_typography', 'Number typography', '.lk-enq-list li > span', 'Cormorant Garamond', 16 );
		$this->color( 'prompt_text_color', 'Text colour', '.lk-enq-list li > p', '#FFFFFF', 'color', true );
		$this->typo( 'prompt_text_typography', 'Text typography', '.lk-enq-list li > p', 'Montserrat', 15, '', null, false, 1.6 );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_button', array( 'label' => 'Button', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->typo( 'btn_typography', 'Typography', '.lk-enq-button', 'Montserrat', 10.9, '600', 0.17, true );
		$this->color( 'btn_bg', 'Background', '.lk-enq-button', '#FFC5BA', 'background-color', true );
		$this->color( 'btn_color', 'Text colour', '.lk-enq-button', '#692137' );
		$this->color( 'btn_border', 'Border colour', '.lk-enq-button', '#FFC5BA', 'border-color' );
		$this->color( 'btn_bg_hover', 'Hover background', '.lk-enq-button:hover', 'transparent', 'background-color', true );
		$this->color( 'btn_color_hover', 'Hover text colour', '.lk-enq-button:hover', '#FFC5BA' );
		$this->add_responsive_control( 'btn_min_height', array( 'label' => 'Min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 36, 'max' => 90 ) ), 'default' => array( 'unit' => 'px', 'size' => 48 ), 'selectors' => array( '{{WRAPPER}} .lk-enq-button' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = (array) $s['items'];

		if ( 'url' === $s['button_mode'] ) {
			$this->add_render_attribute( 'btn', 'href', ! empty( $s['button_url']['url'] ) ? $s['button_url']['url'] : '#' );
			if ( ! empty( $s['button_url']['is_external'] ) ) {
				$this->add_render_attribute( 'btn', 'target', '_blank' );
				$this->add_render_attribute( 'btn', 'rel', 'noopener' );
			}
		} else {
			$number = preg_replace( '/\D+/', '', (string) $s['whatsapp_number'] );
			$this->add_render_attribute( 'btn', 'href', 'https://wa.me/' . $number . '?text=' . rawurlencode( (string) $s['message'] ) );
			$this->add_render_attribute( 'btn', 'target', '_blank' );
			$this->add_render_attribute( 'btn', 'rel', 'noopener' );
		}
		$anchor = ! empty( $s['section_id'] ) ? sanitize_html_class( $s['section_id'] ) : '';
		?>
		<section class="lk-enq"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
			<div class="lk-enq-intro">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-enq-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="lk-enq-heading"><?php echo esc_html( $s['heading_line1'] ); ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-enq-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>
			</div>
			<div class="lk-enq-prompts">
				<?php if ( $items ) : ?>
					<ul class="lk-enq-list">
						<?php foreach ( $items as $item ) : ?>
							<li><span><?php echo esc_html( $item['number'] ); ?></span><p><?php echo esc_html( $item['text'] ); ?></p></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( ! empty( $s['button_text'] ) ) : ?>
					<a class="lk-enq-button" <?php echo $this->get_render_attribute_string( 'btn' ); ?>><?php echo esc_html( $s['button_text'] ); ?></a>
				<?php endif; ?>
			</div>
		</section>

		<style>
			.lk-enq { display: grid; grid-template-columns: 1fr 1fr; align-items: start; box-sizing: border-box; }
			.lk-enq-eyebrow { margin: 0 0 22px; }
			.lk-enq-heading { margin: 0 0 24px; font-style: normal; }
			.lk-enq-heading em { font-style: italic; }
			.lk-enq-desc { margin: 0; max-width: 520px; }
			.lk-enq-list { list-style: none; margin: 0; padding: 0; border-top: 1px solid; }
			.lk-enq-list li { display: grid; grid-template-columns: 45px 1fr; margin: 0; padding: 14px 0; border-bottom: 1px solid; }
			.lk-enq-list li > span { font-style: normal; }
			.lk-enq-list li > p { margin: 0; }
			.lk-enq-button { display: inline-flex; align-items: center; justify-content: center; box-sizing: border-box; margin-top: 35px; padding: 12px 24px; text-decoration: none; border: 1px solid; border-radius: 0; transition: color .25s ease, background .25s ease, transform .25s ease; }
			.lk-enq-button:hover { transform: translateY(-2px); }
			@media (max-width: 1120px) {
				.lk-enq { grid-template-columns: 1fr; }
			}
		</style>
		<?php
	}
}
