<?php
/**
 * Lila Kora — Numbered Link Grid Widget
 *
 * A light, centered-heading section with a bordered grid below it — hairlines
 * on every edge, like a table. Each cell is a coral number, a burgundy serif
 * title, a muted sentence, and an uppercase arrow link pinned to the bottom
 * of the cell. Used as "One gift. Three ways to make it hers." on the Gift
 * Card page (3 columns), but the column count follows however many items
 * you add, so it works anywhere a short numbered set of links is needed.
 *
 * Not the same as LK — Shop Guidance, which is the dark, unequal-columns
 * version of a similar idea — this one is light-on-white with a centered
 * heading above a full-width grid, not a heading beside it.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-numbered-link-grid-widget.php';
 *   $widgets_manager->register( new \LK_Numbered_Link_Grid_Widget() );
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

class LK_Numbered_Link_Grid_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-numbered-link-grid';
	}

	public function get_title() {
		return 'LK — Numbered Link Grid';
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'numbered', 'grid', 'ways', 'options', 'links' );
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
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section( 'section_content_heading', array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'She chooses how to use it', 'label_block' => true ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'One gift.', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic second line', 'type' => Controls_Manager::TEXT, 'default' => 'Three ways to make it hers.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Items
		 * =======================================================*/
		$this->start_controls_section( 'section_content_items', array( 'label' => 'Items', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$item = new Repeater();
		$item->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$item->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Title', 'label_block' => true ) );
		$item->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'Description.', 'label_block' => true ) );
		$item->add_control( 'link_text', array( 'label' => 'Link text', 'type' => Controls_Manager::TEXT, 'default' => 'Learn more', 'label_block' => true ) );
		$item->add_control( 'link_url', array( 'label' => 'Link URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true ) );
		$item->add_control( 'arrow', array( 'label' => 'Arrow', 'type' => Controls_Manager::TEXT, 'default' => '↗', 'description' => 'Leave empty for none.' ) );

		$this->add_control(
			'items',
			array(
				'label'       => 'Items',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $item->get_controls(),
				'default'     => array(
					array( 'number' => '01', 'title' => 'A finished scarf', 'text' => 'Choose from the ready-to-wear Inception Collection.', 'link_text' => 'View scarves', 'link_url' => array( 'url' => './silk-scarf.html' ), 'arrow' => '↗' ),
					array( 'number' => '02', 'title' => 'An Experience Box', 'text' => 'Create an original artwork at home and receive it as a scarf.', 'link_text' => 'Explore the box', 'link_url' => array( 'url' => './experience-box.html' ), 'arrow' => '↗' ),
					array( 'number' => '03', 'title' => 'A public workshop', 'text' => 'Join a guided creative session in Dubai.', 'link_text' => 'View workshops', 'link_url' => array( 'url' => './public-workshops.html' ), 'arrow' => '↗' ),
				),
				'title_field' => '{{{ number }}} — {{{ title }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'bg_color', 'Background', '.lk-nlg', '#FFFEFD', 'background-color' );
		$this->add_responsive_control( 'section_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-nlg' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => 'Section padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 32, 'bottom' => 90, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 80, 'right' => 24, 'bottom' => 80, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-nlg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'heading_spacing', array( 'label' => 'Space below heading', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 120 ) ), 'default' => array( 'unit' => 'px', 'size' => 58 ), 'selectors' => array( '{{WRAPPER}} .lk-nlg-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'columns', array( 'label' => 'Columns (desktop)', 'type' => Controls_Manager::SELECT, 'default' => '3', 'options' => array( '2' => '2', '3' => '3', '4' => '4' ), 'description' => 'Tablet always shows 2, phone always shows 1.' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading
		 * =======================================================*/
		$this->start_controls_section( 'section_style_heading', array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-nlg-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-nlg-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-nlg-heading h2', '#57282D', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic line colour', '.lk-nlg-heading h2 em', '#57282D' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-nlg-heading h2', 'Cormorant Garamond', 60, '400', -0.025, false, 1.08, 44, 34 );
		$this->add_responsive_control( 'heading_max_width', array( 'label' => 'Heading max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 1100 ) ), 'default' => array( 'unit' => 'px', 'size' => 820 ), 'selectors' => array( '{{WRAPPER}} .lk-nlg-heading h2' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Grid
		 * =======================================================*/
		$this->start_controls_section( 'section_style_grid', array( 'label' => 'Grid', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'grid_line_color', 'Grid divider colour', '.lk-nlg-grid, {{WRAPPER}} .lk-nlg-item', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->add_responsive_control( 'item_min_height', array( 'label' => 'Cell min height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 150, 'max' => 500 ) ), 'default' => array( 'unit' => 'px', 'size' => 320 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 260 ), 'selectors' => array( '{{WRAPPER}} .lk-nlg-item' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'item_padding', array( 'label' => 'Cell padding', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 10, 'max' => 60 ) ), 'default' => array( 'unit' => 'px', 'size' => 28 ), 'selectors' => array( '{{WRAPPER}} .lk-nlg-item' => 'padding: {{SIZE}}{{UNIT}};' ) ) );

		$this->color( 'number_color', 'Number colour', '.lk-nlg-item > span', '#FF715E', 'color', true );
		$this->typo( 'number_typography', 'Number typography', '.lk-nlg-item > span', 'Cormorant Garamond', 16 );
		$this->color( 'title_color', 'Title colour', '.lk-nlg-item h3', '#692137', 'color', true );
		$this->typo( 'title_typography', 'Title typography', '.lk-nlg-item h3', 'Cormorant Garamond', 32, '400' );
		$this->add_responsive_control( 'title_top_space', array( 'label' => 'Space above title', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 120 ) ), 'default' => array( 'unit' => 'px', 'size' => 55 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 28 ), 'selectors' => array( '{{WRAPPER}} .lk-nlg-item h3' => 'margin-top: {{SIZE}}{{UNIT}};' ) ) );
		$this->color( 'text_color', 'Text colour', '.lk-nlg-item p', '#8F8584', 'color', true );
		$this->typo( 'text_typography', 'Text typography', '.lk-nlg-item p', 'Montserrat', 14.5, '', null, false, 1.55 );
		$this->color( 'link_color', 'Link colour', '.lk-nlg-item a', '#692137', 'color', true );
		$this->typo( 'link_typography', 'Link typography', '.lk-nlg-item a', 'Montserrat', 12, '600', 0.1, true );

		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = (array) $s['items'];
		$cols  = max( 2, min( 4, (int) $s['columns'] ) );
		?>
		<section class="lk-nlg">
			<div class="lk-nlg-heading">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-nlg-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2><?php echo esc_html( $s['heading_line1'] ); ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?></h2>
			</div>

			<?php if ( $items ) : ?>
				<div class="lk-nlg-grid" style="--lk-nlg-cols: <?php echo esc_attr( $cols ); ?>;">
					<?php foreach ( $items as $item ) :
						$this->add_render_attribute( 'link_' . $item['_id'], 'href', ! empty( $item['link_url']['url'] ) ? $item['link_url']['url'] : '#' );
						if ( ! empty( $item['link_url']['is_external'] ) ) {
							$this->add_render_attribute( 'link_' . $item['_id'], 'target', '_blank' );
							$this->add_render_attribute( 'link_' . $item['_id'], 'rel', 'noopener' );
						}
						?>
						<article class="lk-nlg-item">
							<span><?php echo esc_html( $item['number'] ); ?></span>
							<h3><?php echo esc_html( $item['title'] ); ?></h3>
							<p><?php echo esc_html( $item['text'] ); ?></p>
							<?php if ( ! empty( $item['link_text'] ) ) : ?>
								<a <?php echo $this->get_render_attribute_string( 'link_' . $item['_id'] ); ?>><?php echo esc_html( $item['link_text'] ); ?> <?php if ( ! empty( $item['arrow'] ) ) : ?><span aria-hidden="true"><?php echo esc_html( $item['arrow'] . "\u{FE0E}" ); ?></span><?php endif; ?></a>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>

		<style>
			.lk-nlg { box-sizing: border-box; text-align: center; }
			.lk-nlg-heading h2 { margin: 0; font-style: normal; }
			.lk-nlg-heading h2 em { font-style: italic; }
			.lk-nlg-grid { display: grid; grid-template-columns: repeat(var(--lk-nlg-cols, 3), 1fr); text-align: left; border-top: 1px solid; border-left: 1px solid; }
			.lk-nlg-item { box-sizing: border-box; display: flex; flex-direction: column; border-right: 1px solid; border-bottom: 1px solid; }
			.lk-nlg-item > span { display: block; font-style: normal; }
			.lk-nlg-item h3 { margin-bottom: 14px; }
			.lk-nlg-item p { margin: 0; }
			.lk-nlg-item a { margin-top: auto; text-decoration: none; }
			@media (max-width: 1120px) {
				.lk-nlg-grid { grid-template-columns: 1fr 1fr; }
			}
			@media (max-width: 540px) {
				.lk-nlg-grid { grid-template-columns: 1fr; }
			}
		</style>
		<?php
	}
}
