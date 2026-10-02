<?php
/**
 * Lila Kora — Scarf Add-Ons Widget
 *
 * The light "Scarf add-ons after the workshop" block on the Public Workshops
 * page. A hairline runs across the top; underneath, on the left an eyebrow, a
 * serif heading ("Prefer to decide later?") and a paragraph, and on the right a
 * hairline price list (fabric · size → price):
 *
 *   ─────────────────────────────────────────────────────────
 *   SCARF ADD-ONS AFTER THE WORKSHOP  │ ──────────────────────
 *   Prefer to decide later?           │ Premium satin · 70 x 70 cm    AED 250
 *   You can book the workshop on ...  │ ──────────────────────
 *
 * The rows are text on purpose — scarves chosen after the session are added to
 * the AED 190 ticket rather than bought through the cart here. (The bundles that
 * ARE bought at checkout live in LK — Workshop Booking: 190 + 250 = 440 and
 * 190 + 550 = 740.)
 *
 * The Anchor ID lets other links ("Prefer to decide later? See the add-ons")
 * scroll straight here.
 *
 * (File and class names are unchanged from the earlier dark version, so an
 * existing loader registration keeps working.)
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-scarf-addons-widget.php';
 *   $widgets_manager->register( new \LK_Scarf_Addons_Widget() );
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

class LK_Scarf_Addons_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-scarf-addons';
	}

	public function get_title() {
		return 'LK — Scarf Add-Ons';
	}

	public function get_icon() {
		return 'eicon-price-list';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'scarf', 'add-on', 'price list', 'prices', 'later' );
	}

	private function typo( $name, $label, $selector, $family, $size, $weight = '', $spacing = null, $upper = false, $line = null, $tablet = null, $mobile = null ) {
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

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section( 'section_content_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'anchor_id', array( 'label' => 'Anchor ID (optional)', 'type' => Controls_Manager::TEXT, 'default' => 'scarf-addons', 'description' => 'Lets a "#scarf-addons" link scroll here, with a little room for the sticky header.' ) );
		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Scarf add-ons after the workshop', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'heading', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Prefer to decide later?', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => 'You can book the workshop on its own and select a scarf after you have created your canvas. The scarf price is added to your AED 190 workshop ticket.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Price list
		 * =======================================================*/
		$this->start_controls_section( 'section_content_prices', array( 'label' => 'Price List', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$rows = new Repeater();
		$rows->add_control( 'label', array( 'label' => 'Option', 'type' => Controls_Manager::TEXT, 'default' => 'Fabric · size', 'label_block' => true ) );
		$rows->add_control( 'price', array( 'label' => 'Price', 'type' => Controls_Manager::TEXT, 'default' => 'AED 0', 'description' => 'Type it exactly as it should show, e.g. AED 250.' ) );

		$this->add_control(
			'rows',
			array(
				'label'       => 'Options',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rows->get_controls(),
				'default'     => array(
					array( 'label' => 'Premium satin · 70 x 70 cm', 'price' => 'AED 250' ),
					array( 'label' => 'Premium satin · 90 x 90 cm', 'price' => 'AED 350' ),
					array( 'label' => 'Pure silk · 65 x 65 cm', 'price' => 'AED 550' ),
					array( 'label' => 'Pure silk · 85 x 85 cm', 'price' => 'AED 750' ),
				),
				'title_field' => '{{{ label }}} — {{{ price }}}',
			)
		);
		$this->add_control( 'note', array( 'label' => 'Small note under the list (optional)', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => '', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'bg_color', 'Background', '.lk-addons', '#FFFEFD', 'background-color' );
		$this->add_responsive_control( 'section_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-addons' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => 'Section padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 70, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 55, 'right' => 32, 'bottom' => 90, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 45, 'right' => 24, 'bottom' => 80, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-addons' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'show_top_line', array( 'label' => 'Hairline across the top', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes', 'prefix_class' => 'lk-addons-line-' ) );
		$this->color( 'top_line_color', 'Hairline colour', '.lk-addons-inner', 'rgba(105,33,55,0.16)', 'border-top-color' );
		$this->add_responsive_control( 'inner_padding_top', array( 'label' => 'Space under the hairline', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 120 ) ), 'default' => array( 'unit' => 'px', 'size' => 52 ), 'selectors' => array( '{{WRAPPER}} .lk-addons-inner' => 'padding-top: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'column_gap', array( 'label' => 'Gap between the two columns (vw)', 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 20, 'step' => 0.5 ) ), 'default' => array( 'unit' => 'px', 'size' => 8 ), 'selectors' => array( '{{WRAPPER}} .lk-addons-inner' => 'column-gap: {{SIZE}}vw;' ) ) );
		$this->add_control( 'swap_sides', array( 'label' => 'Put the price list on the left', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => '', 'prefix_class' => 'lk-addons-swap-' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section( 'section_style_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-addons-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-addons-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-addons-copy h3', '#692137', 'color', true );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-addons-copy h3', 'Cormorant Garamond', 47, '400', -0.025, false, 0.98, 40, 34 );
		$this->color( 'desc_color', 'Paragraph colour', '.lk-addons-desc', '#8F8584', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-addons-desc', 'Montserrat', 16, '', null, false, 1.65 );
		$this->add_responsive_control( 'desc_max_width', array( 'label' => 'Paragraph max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 250, 'max' => 800 ) ), 'default' => array( 'unit' => 'px', 'size' => 520 ), 'selectors' => array( '{{WRAPPER}} .lk-addons-desc' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Price list
		 * =======================================================*/
		$this->start_controls_section( 'section_style_prices', array( 'label' => 'Price List', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'list_line', 'Line colour', '.lk-addons-list, {{WRAPPER}} .lk-addons-row', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->add_responsive_control( 'row_padding', array( 'label' => 'Row padding (top & bottom)', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 4, 'max' => 50 ) ), 'default' => array( 'unit' => 'px', 'size' => 17 ), 'selectors' => array( '{{WRAPPER}} .lk-addons-row' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->color( 'label_color', 'Option colour', '.lk-addons-row span', '#8F8584', 'color', true );
		$this->typo( 'label_typography', 'Option typography', '.lk-addons-row span', 'Montserrat', 16 );
		$this->color( 'price_color', 'Price colour', '.lk-addons-row strong', '#692137', 'color', true );
		$this->typo( 'price_typography', 'Price typography', '.lk-addons-row strong', 'Cormorant Garamond', 20, '500' );
		$this->color( 'note_color', 'Note colour', '.lk-addons-note', '#8F8584', 'color', true );
		$this->typo( 'note_typography', 'Note typography', '.lk-addons-note', 'Montserrat', 12.5, '', null, false, 1.65 );

		$this->end_controls_section();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$rows   = (array) $s['rows'];
		$anchor = ! empty( $s['anchor_id'] ) ? sanitize_html_class( $s['anchor_id'] ) : '';
		?>
		<section class="lk-addons"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
			<div class="lk-addons-inner">
				<div class="lk-addons-copy">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-addons-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $s['heading'] ) ) : ?><h3><?php echo esc_html( $s['heading'] ); ?></h3><?php endif; ?>
					<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-addons-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>
				</div>

				<div class="lk-addons-list-wrap">
					<?php if ( $rows ) : ?>
						<div class="lk-addons-list">
							<?php foreach ( $rows as $row ) : ?>
								<div class="lk-addons-row"><span><?php echo esc_html( $row['label'] ); ?></span><strong><?php echo esc_html( $row['price'] ); ?></strong></div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $s['note'] ) ) : ?><p class="lk-addons-note"><?php echo esc_html( $s['note'] ); ?></p><?php endif; ?>
				</div>
			</div>
		</section>

		<style>
			.lk-addons { box-sizing: border-box; scroll-margin-top: 120px; }
			.lk-addons-inner { display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 35px 8vw; align-items: start; border-top: 1px solid transparent; }
			.lk-addons-line-yes .lk-addons-inner { border-top-style: solid; }
			.lk-addons-line- .lk-addons-inner { border-top-color: transparent !important; }
			.lk-addons-swap-yes .lk-addons-copy { order: 2; } .lk-addons-swap-yes .lk-addons-list-wrap { order: 1; }
			.lk-addons-eyebrow { margin: 0 0 22px; }
			.lk-addons-copy h3 { margin: 0 0 15px; font-style: normal; }
			.lk-addons-desc { margin: 0; }
			.lk-addons-list { border-top: 1px solid; }
			.lk-addons-row { display: flex; justify-content: space-between; gap: 22px; padding: 17px 0; border-bottom: 1px solid; }
			.lk-addons-row span { font-style: normal; }
			.lk-addons-row strong { white-space: nowrap; }
			.lk-addons-note { margin: 22px 0 0; }
			@media (max-width: 820px) {
				.lk-addons-inner { grid-template-columns: 1fr; }
				.lk-addons-swap-yes .lk-addons-copy, .lk-addons-swap-yes .lk-addons-list-wrap { order: 0; }
			}
		</style>
		<?php
	}
}
