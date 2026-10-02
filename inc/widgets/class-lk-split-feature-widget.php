<?php
/**
 * Lila Kora — Split Feature Widget
 *
 * A flexible image + copy section: an image (with an optional italic caption)
 * on one side, and on the other an eyebrow, a two-line heading (second line
 * italic), a paragraph, an OPTIONAL numbered list, an OPTIONAL italic note and
 * an OPTIONAL underlined arrow link. Anything optional can simply be left empty.
 *
 * Used twice on the Private Events page, with different content:
 *   - "Everything needed to begin."          image left, numbered list + note
 *   - "Let the artwork continue beyond..."   image right, underlined link
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-split-feature-widget.php';
 *   $widgets_manager->register( new \LK_Split_Feature_Widget() );
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

class LK_Split_Feature_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-split-feature';
	}

	public function get_title() {
		return 'LK — Split Feature';
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'split', 'image', 'feature', 'included', 'scarf', 'list' );
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

		$this->start_controls_section( 'section_content_copy', array( 'label' => 'Copy', 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'What is included', 'label_block' => true ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Everything needed', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic second line', 'type' => Controls_Manager::TEXT, 'default' => 'to begin.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'default' => 'We guide complete beginners from the first mark to a finished canvas, with the materials and support already in place.', 'label_block' => true ) );
		$this->add_control( 'note', array( 'label' => 'Italic note (optional)', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'A tailored quotation is provided based on group size, venue and scarf choice.', 'label_block' => true ) );
		$this->add_control( 'link_text', array( 'label' => 'Text link — label (optional)', 'type' => Controls_Manager::TEXT, 'default' => '', 'label_block' => true, 'separator' => 'before' ) );
		$this->add_control( 'link_url', array( 'label' => 'Text link — URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '' ), 'show_external' => true, 'condition' => array( 'link_text!' => '' ) ) );
		$this->add_control( 'link_arrow', array( 'label' => 'Text link — arrow', 'type' => Controls_Manager::TEXT, 'default' => '↗', 'condition' => array( 'link_text!' => '' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_content_list', array( 'label' => 'Numbered List', 'tab' => Controls_Manager::TAB_CONTENT ) );
		$item = new Repeater();
		$item->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$item->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Item', 'label_block' => true ) );
		$this->add_control(
			'items',
			array(
				'label'       => 'List items (remove all for no list)',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $item->get_controls(),
				'default'     => array(
					array( 'number' => '01', 'text' => 'A guided creative session with Lila Kora' ),
					array( 'number' => '02', 'text' => 'Canvases, paints, brushes and all art materials' ),
					array( 'number' => '03', 'text' => 'Creative guidance throughout the experience' ),
					array( 'number' => '04', 'text' => 'Coordination around your group, venue and occasion' ),
				),
				'title_field' => '{{{ number }}} — {{{ text }}}',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'section_content_image', array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_CONTENT ) );
		$this->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image', 'default' => 'full' ) );
		$this->add_control( 'image_caption', array( 'label' => 'Caption (optional)', 'type' => Controls_Manager::TEXT, 'default' => '', 'label_block' => true, 'description' => 'Small italic line under the image, right-aligned.' ) );
		$this->add_control( 'image_position', array( 'label' => 'Image side', 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => array( 'left' => 'Left', 'right' => 'Right' ), 'prefix_class' => 'lk-split-img-' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->layout_controls( '.lk-split', '#FFFEFD', 140, 140 );
		$this->add_responsive_control( 'columns_gap', array( 'label' => 'Gap between image and copy', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 220 ) ), 'default' => array( 'unit' => 'px', 'size' => 115 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 50 ), 'selectors' => array( '{{WRAPPER}} .lk-split' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'image_height', array( 'label' => 'Image height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 1000 ) ), 'default' => array( 'unit' => 'px', 'size' => 660 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 520 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 420 ), 'selectors' => array( '{{WRAPPER}} .lk-split-media img' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'image_radius', array( 'label' => 'Image corner radius', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-split-media img' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );
		$this->color( 'caption_color', 'Caption colour', '.lk-split-caption', '#8F8584', 'color', true );
		$this->typo( 'caption_typography', 'Caption typography', '.lk-split-caption', 'Cormorant Garamond', 16, '', null, false, null, null, null, true );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_text', array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-split-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-split-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-split-heading', '#57282D', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic line colour', '.lk-split-heading em', '#57282D' );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-split-heading', 'Cormorant Garamond', 76, '400', -0.025, false, 1.0, 54, 38 );
		$this->color( 'desc_color', 'Paragraph colour', '.lk-split-desc', '#8F8584', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-split-desc', 'Montserrat', 16, '', null, false, 1.8 );
		$this->color( 'note_color', 'Italic note colour', '.lk-split-note', '#692137', 'color', true );
		$this->typo( 'note_typography', 'Italic note typography', '.lk-split-note', 'Cormorant Garamond', 16, '', null, false, null, null, null, true );
		$this->color( 'link_color', 'Text link colour', '.lk-split-link', '#692137', 'color', true );
		$this->typo( 'link_typography', 'Text link typography', '.lk-split-link', 'Montserrat', 11.5, '600', 0.1, true );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style_list', array( 'label' => 'Numbered List', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->color( 'list_line_color', 'Divider colour', '.lk-split-list, {{WRAPPER}} .lk-split-list li', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->color( 'list_number_color', 'Number colour', '.lk-split-list li > span', '#FF715E', 'color', true );
		$this->typo( 'list_number_typography', 'Number typography', '.lk-split-list li > span', 'Cormorant Garamond', 16 );
		$this->color( 'list_text_color', 'Text colour', '.lk-split-list li > p', '#8F8584', 'color', true );
		$this->typo( 'list_text_typography', 'Text typography', '.lk-split-list li > p', 'Montserrat', 15, '', null, false, 1.6 );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = array_filter( (array) $s['items'], function ( $i ) {
			return ! empty( $i['text'] );
		} );

		$image_html = '';
		if ( ! empty( $s['image']['url'] ) ) {
			$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		}
		if ( ! empty( $s['link_text'] ) ) {
			$this->add_render_attribute( 'link', 'href', ! empty( $s['link_url']['url'] ) ? $s['link_url']['url'] : '#' );
			if ( ! empty( $s['link_url']['is_external'] ) ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
				$this->add_render_attribute( 'link', 'rel', 'noopener' );
			}
		}
		?>
		<section class="lk-split">
			<figure class="lk-split-media">
				<?php echo $image_html; ?>
				<?php if ( ! empty( $s['image_caption'] ) ) : ?><figcaption class="lk-split-caption"><?php echo esc_html( $s['image_caption'] ); ?></figcaption><?php endif; ?>
			</figure>
			<div class="lk-split-copy">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-split-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="lk-split-heading"><?php echo esc_html( $s['heading_line1'] ); ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-split-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>
				<?php if ( $items ) : ?>
					<ul class="lk-split-list">
						<?php foreach ( $items as $item ) : ?>
							<li><span><?php echo esc_html( $item['number'] ); ?></span><p><?php echo esc_html( $item['text'] ); ?></p></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( ! empty( $s['note'] ) ) : ?><p class="lk-split-note"><?php echo esc_html( $s['note'] ); ?></p><?php endif; ?>
				<?php if ( ! empty( $s['link_text'] ) ) : ?>
					<a class="lk-split-link" <?php echo $this->get_render_attribute_string( 'link' ); ?>><?php echo esc_html( $s['link_text'] ); ?><?php if ( ! empty( $s['link_arrow'] ) ) : ?> <span aria-hidden="true"><?php echo esc_html( $s['link_arrow'] . "\u{FE0E}" ); ?></span><?php endif; ?></a>
				<?php endif; ?>
			</div>
		</section>

		<style>
			.lk-split { display: grid; grid-template-columns: 1.05fr 0.95fr; align-items: center; box-sizing: border-box; }
			.lk-split-img-right .lk-split { grid-template-columns: 0.95fr 1.05fr; }
			.lk-split-img-right .lk-split-media { order: 2; }
			.lk-split-img-right .lk-split-copy { order: 1; }
			.lk-split-media { margin: 0; }
			.lk-split-media img { display: block; width: 100%; object-fit: cover; }
			.lk-split-caption { padding-top: 13px; text-align: right; }
			.lk-split-heading { margin: 0 0 24px; font-style: normal; }
			.lk-split-heading em { font-style: italic; }
			.lk-split-eyebrow { margin: 0 0 22px; }
			.lk-split-desc { margin: 0; }
			.lk-split-list { list-style: none; margin: 34px 0 24px; padding: 0; border-top: 1px solid; }
			.lk-split-list li { display: grid; grid-template-columns: 45px 1fr; margin: 0; padding: 14px 0; border-bottom: 1px solid; }
			.lk-split-list li > span { font-style: normal; }
			.lk-split-list li > p { margin: 0; }
			.lk-split-note { margin: 24px 0 0; }
			.lk-split-list + .lk-split-note { margin-top: 0; }
			.lk-split-link { display: inline-flex; gap: 9px; align-items: center; margin-top: 28px; padding-bottom: 4px; text-decoration: none; border-bottom: 1px solid currentColor; }
			@media (max-width: 1120px) {
				.lk-split, .lk-split-img-right .lk-split { grid-template-columns: 1fr; }
				.lk-split-img-right .lk-split-media, .lk-split-img-right .lk-split-copy { order: 0; }
			}
		</style>
		<?php
	}
}
