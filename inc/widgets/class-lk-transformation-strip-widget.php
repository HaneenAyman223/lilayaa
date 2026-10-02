<?php
/**
 * Lila Kora — Transformation Strip Widget
 *
 * A single wide image (the 5-step Canvas-to-Scarf visual: box,
 * painting, canvas, refined artwork, finished scarf) sitting in a
 * horizontally-scrollable strip, since it's always wider than the
 * viewport. Deliberately just one image, not a gallery/repeater —
 * matching the reference exactly.
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

class LK_Transformation_Strip_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-transformation-strip';
	}

	public function get_title() {
		return 'LK — Transformation Strip';
	}

	public function get_icon() {
		return 'eicon-image';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'transformation', 'strip', 'process' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT
		 * =======================================================*/
		$this->start_controls_section(
			'section_content',
			array(
				'label' => 'Image',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => 'Image',
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array( 'name' => 'image', 'default' => 'full' )
		);
		$this->add_control(
			'alt_text',
			array(
				'label'       => 'Alt text',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'The Canvas-to-Scarf process shown through five images: Experience Box, painting, original canvas, refined digital artwork and finished scarf',
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE
		 * =======================================================*/
		$this->start_controls_section(
			'section_style',
			array(
				'label' => 'Style',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'section_max_width',
			array(
				'label'     => 'Max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'The reference caps this at 1440px and centres it, same as every other section. Set your outer Elementor container to full-width / 0 padding and let this control the actual width.',
				'selectors' => array(
					'{{WRAPPER}} .lk-transformation-strip' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);
		$this->add_control(
			'bg_color',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F8ECE9',
				'selectors' => array( '{{WRAPPER}} .lk-transformation-strip' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'image_min_width',
			array(
				'label'       => 'Image min width',
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 400, 'max' => 1600 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 880 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 900 ),
				'description' => 'Forces horizontal scrolling below this width — matches the reference\'s own 880px (900px on tablet).',
				'selectors'   => array(
					'{{WRAPPER}} .lk-transformation-strip img' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'scrollbar_thumb_color',
			array(
				'label'     => 'Scrollbar thumb colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'selectors' => array(
					'{{WRAPPER}} .lk-transformation-strip-scroll' => 'scrollbar-color: {{VALUE}} var(--lk-ts-track, #F8ECE9);',
					'{{WRAPPER}} .lk-transformation-strip-scroll::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'scrollbar_track_color',
			array(
				'label'     => 'Scrollbar track colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F8ECE9',
				'selectors' => array(
					'{{WRAPPER}} .lk-transformation-strip-scroll' => '--lk-ts-track: {{VALUE}};',
					'{{WRAPPER}} .lk-transformation-strip-scroll::-webkit-scrollbar-track' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );

		// Swap in the custom alt text if one was provided (Elementor's
		// image helper pulls alt from the attachment by default).
		if ( ! empty( $s['alt_text'] ) ) {
			$image_html = preg_replace( '/alt="[^"]*"/', 'alt="' . esc_attr( $s['alt_text'] ) . '"', $image_html, 1 );
		}
		?>
		<section class="lk-transformation-strip" aria-label="The Canvas-to-Scarf transformation from Experience Box to finished scarf">
			<div class="lk-transformation-strip-scroll">
				<?php echo $image_html; ?>
			</div>
		</section>
		<?php
	}
}
