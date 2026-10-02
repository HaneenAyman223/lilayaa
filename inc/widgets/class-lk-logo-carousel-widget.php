<?php
/**
 * Lila Kora — Logo Carousel Widget
 *
 * "The Brands That Trust Us" pattern: a heading, an infinitely
 * auto-scrolling row of partner/press logos (pure CSS marquee, pauses
 * on hover — no JS carousel library needed), and an optional review
 * line beneath.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-logo-carousel-widget.php';
 *   $widgets_manager->register( new \LK_Logo_Carousel_Widget() );
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

class LK_Logo_Carousel_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-logo-carousel';
	}

	public function get_title() {
		return 'LK — Logo Carousel';
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'logos', 'carousel', 'trusted by', 'partners' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Trusted By', 'label_block' => true ) );
		$this->add_control( 'heading', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'The brands and venues that welcome Lila Kora', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Logos
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_logos',
			array( 'label' => 'Logos', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'image', array( 'label' => 'Logo image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$repeater->add_control( 'name', array( 'label' => 'Name (alt text)', 'type' => Controls_Manager::TEXT, 'default' => 'Brand name', 'label_block' => true ) );

		$this->add_control(
			'logos',
			array(
				'label' => 'Logos', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'name' => 'Four Seasons' ),
					array( 'name' => 'Katch International' ),
					array( 'name' => 'Women Who Thrive' ),
					array( 'name' => 'Her Table' ),
					array( 'name' => 'Habii' ),
					array( 'name' => 'mrdv.' ),
				),
				'title_field' => '{{{ name }}}',
			)
		);
		$this->add_control(
			'speed',
			array(
				'label' => 'Scroll speed (seconds per loop)', 'type' => Controls_Manager::SLIDER,
				'range' => array( 'px' => array( 'min' => 10, 'max' => 90 ) ), 'default' => array( 'size' => 30 ),
				'description' => 'Lower = faster. The strip scrolls continuously and pauses on hover.',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Review Line
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_review',
			array( 'label' => 'Review Line', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'show_review_line', array( 'label' => 'Show', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$this->add_control( 'review_line', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => '5.0 ★ on Google · 200+ Reviews · Dubai\'s most personal silk scarf experience', 'label_block' => true, 'condition' => array( 'show_review_line' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#FFFEFD', 'selectors' => array( '{{WRAPPER}} .lk-logos' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-logos' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 100, 'right' => 65, 'bottom' => 100, 'left' => 65, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 70, 'right' => 24, 'bottom' => 70, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-logos' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
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
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-logos-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-logos-heading',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 40 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 28 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );
		$this->add_control( 'review_color', array( 'label' => 'Review line colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'condition' => array( 'show_review_line' => 'yes' ), 'selectors' => array( '{{WRAPPER}} .lk-logos-review' => 'color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Logos
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_logos',
			array( 'label' => 'Logos', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'logo_height', array( 'label' => 'Logo height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 20, 'max' => 120 ) ), 'default' => array( 'unit' => 'px', 'size' => 48 ), 'selectors' => array( '{{WRAPPER}} .lk-logos-track img' => 'height: {{SIZE}}{{UNIT}}; width: auto;' ) ) );
		$this->add_control( 'logo_gap', array( 'label' => 'Gap between logos', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 20, 'max' => 160 ) ), 'default' => array( 'unit' => 'px', 'size' => 72 ), 'selectors' => array( '{{WRAPPER}} .lk-logos-track' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'grayscale', array( 'label' => 'Greyscale (colour on hover)', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'On', 'label_off' => 'Off', 'default' => 'yes' ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$duration = ! empty( $s['speed']['size'] ) ? $s['speed']['size'] : 30;
		$grayscale_class = 'yes' === $s['grayscale'] ? ' lk-logos-grayscale' : '';
		?>
		<div class="lk-logos">
			<div class="lk-logos-heading-wrap">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="lk-logos-heading"><?php echo esc_html( $s['heading'] ); ?></h2>
			</div>

			<div class="lk-logos-viewport<?php echo esc_attr( $grayscale_class ); ?>">
				<div class="lk-logos-track" style="animation-duration: <?php echo esc_attr( $duration ); ?>s;">
					<?php foreach ( array( 1, 2 ) as $loop ) : // duplicate once for a seamless loop ?>
						<?php foreach ( $s['logos'] as $logo ) :
							$image_html = Group_Control_Image_Size::get_attachment_image_html( $logo, 'image' );
							if ( empty( $logo['image']['url'] ) || strpos( $logo['image']['url'], 'placeholder' ) !== false ) : ?>
								<span class="lk-logos-text"><?php echo esc_html( $logo['name'] ); ?></span>
							<?php else : ?>
								<?php echo $image_html; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( 'yes' === $s['show_review_line'] && ! empty( $s['review_line'] ) ) : ?>
				<p class="lk-logos-review"><?php echo esc_html( $s['review_line'] ); ?></p>
			<?php endif; ?>
		</div>

		<style>
			.lk-logos-heading-wrap { text-align: center; margin-bottom: 50px; }
			.lk-logos-heading { margin: 0.3em 0; font-style: normal; max-width: 700px; margin-left: auto; margin-right: auto; }
			.lk-logos-viewport { overflow: hidden; -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent); mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent); }
			.lk-logos-track { display: flex; align-items: center; width: max-content; animation-name: lk-logos-scroll; animation-timing-function: linear; animation-iteration-count: infinite; }
			.lk-logos-viewport:hover .lk-logos-track { animation-play-state: paused; }
			.lk-logos-track img { display: block; object-fit: contain; flex-shrink: 0; }
			.lk-logos-grayscale img { filter: grayscale(100%); opacity: 0.6; transition: filter .3s ease, opacity .3s ease; }
			.lk-logos-grayscale img:hover { filter: grayscale(0%); opacity: 1; }
			.lk-logos-text { flex-shrink: 0; font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 20px; color: #57282D; opacity: 0.55; white-space: nowrap; }
			.lk-logos-review { margin: 40px 0 0; text-align: center; font-family: 'Montserrat', Arial, sans-serif; font-size: 12.8px; letter-spacing: 0.04em; }
			@keyframes lk-logos-scroll {
				from { transform: translateX(0); }
				to { transform: translateX(-50%); }
			}
		</style>
		<?php
	}
}
