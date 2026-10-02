<?php
/**
 * Lila Kora — Testimonials Grid Widget
 *
 * "The experience speaks for itself" pattern: a heading, a 3-column
 * grid of testimonials (star rating + quote + name), and an optional
 * link out to the full review platform.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-testimonials-widget.php';
 *   $widgets_manager->register( new \LK_Testimonials_Widget() );
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

class LK_Testimonials_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-testimonials';
	}

	public function get_title() {
		return 'LK — Testimonials Grid';
	}

	public function get_icon() {
		return 'eicon-testimonial';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'testimonials', 'reviews' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'heading', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'The experience speaks for itself', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Testimonials
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_items',
			array( 'label' => 'Testimonials', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'rating', array( 'label' => 'Rating (1–5)', 'type' => Controls_Manager::NUMBER, 'default' => 5, 'min' => 1, 'max' => 5 ) );
		$repeater->add_control( 'quote', array( 'label' => 'Quote', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Quote text.', 'label_block' => true ) );
		$repeater->add_control( 'name', array( 'label' => 'Name', 'type' => Controls_Manager::TEXT, 'default' => 'Name', 'label_block' => true ) );

		$this->add_control(
			'items',
			array(
				'label' => 'Items', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'rating' => 5, 'quote' => 'Absolutely magical. I turned my own painting into a scarf I now wear every week — I have never owned anything so personal.', 'name' => 'Sarah M.' ),
					array( 'rating' => 5, 'quote' => 'The most beautiful bridal shower afternoon. Every detail was thoughtful, elegant, and completely unforgettable.', 'name' => 'Fatima K.' ),
					array( 'rating' => 5, 'quote' => 'We booked Lila Kora for a corporate event and the execution was flawless. Beyond professional, beyond beautiful.', 'name' => 'James R.' ),
				),
				'title_field' => '{{{ name }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Link
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_link',
			array( 'label' => 'Bottom Link', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'show_link', array( 'label' => 'Show', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$this->add_control( 'link_text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Read all reviews on Google', 'label_block' => true, 'condition' => array( 'show_link' => 'yes' ) ) );
		$this->add_control( 'link_url', array( 'label' => 'URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => 'https://google.com', 'is_external' => true ), 'show_external' => true, 'label_block' => true, 'condition' => array( 'show_link' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-testimonials' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 100, 'right' => 65, 'bottom' => 100, 'left' => 65, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 70, 'right' => 24, 'bottom' => 70, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-testimonials' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'grid_gap', array( 'label' => 'Card gap', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 80 ) ), 'default' => array( 'unit' => 'px', 'size' => 40 ), 'selectors' => array( '{{WRAPPER}} .lk-testimonials-grid' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'heading_color', array( 'label' => 'Colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'selectors' => array( '{{WRAPPER}} .lk-testimonials-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-testimonials-heading',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 44 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 30 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );
		$this->add_responsive_control( 'heading_spacing', array( 'label' => 'Spacing below', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 100 ) ), 'default' => array( 'unit' => 'px', 'size' => 55 ), 'selectors' => array( '{{WRAPPER}} .lk-testimonials-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Cards
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_cards',
			array( 'label' => 'Cards', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'star_color', array( 'label' => 'Star colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-stars' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'star_size', array( 'label' => 'Star size', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 8, 'max' => 32 ) ), 'default' => array( 'unit' => 'px', 'size' => 15 ), 'selectors' => array( '{{WRAPPER}} .lk-stars' => 'font-size: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'star_empty_color', array( 'label' => 'Empty star colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.18)', 'selectors' => array( '{{WRAPPER}} .lk-stars .lk-star-empty' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'quote_color', array( 'label' => 'Quote colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-testimonial-quote' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'quote_typography', 'selector' => '{{WRAPPER}} .lk-testimonial-quote',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 19 ) ), 'font_style' => array( 'default' => 'italic' ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.5 ) ) ),
		) );
		$this->add_control( 'name_color', array( 'label' => 'Name colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-testimonial-name' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'name_typography', 'selector' => '{{WRAPPER}} .lk-testimonial-name',
			'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 12.5 ) ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.04 ) ) ),
		) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Bottom Link
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_link',
			array( 'label' => 'Bottom Link', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_link' => 'yes' ) )
		);

		$this->add_control( 'link_color', array( 'label' => 'Colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-testimonials-link' => 'color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'link_typography', 'selector' => '{{WRAPPER}} .lk-testimonials-link',
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

		if ( 'yes' === $s['show_link'] ) {
			$this->add_render_attribute( 'link', 'class', 'lk-testimonials-link' );
			$this->add_link_attributes( 'link', $s['link_url'] );
		}
		?>
		<div class="lk-testimonials">
			<h2 class="lk-testimonials-heading"><?php echo esc_html( $s['heading'] ); ?></h2>

			<div class="lk-testimonials-grid">
				<?php foreach ( $s['items'] as $item ) :
					$rating = max( 1, min( 5, (int) $item['rating'] ) );
					?>
					<article class="lk-testimonial">
						<div class="lk-stars" aria-label="<?php echo esc_attr( $rating . ' out of 5 stars' ); ?>">
							<?php for ( $i = 0; $i < 5; $i++ ) : ?>
								<span class="<?php echo $i < $rating ? 'lk-star-filled' : 'lk-star-empty'; ?>">★</span>
							<?php endfor; ?>
						</div>
						<p class="lk-testimonial-quote">&ldquo;<?php echo esc_html( $item['quote'] ); ?>&rdquo;</p>
						<p class="lk-testimonial-name"><?php echo esc_html( $item['name'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>

			<?php if ( 'yes' === $s['show_link'] && ! empty( $s['link_text'] ) ) : ?>
				<a <?php echo $this->get_render_attribute_string( 'link' ); ?>><?php echo esc_html( $s['link_text'] ); ?> <span>↗︎</span></a>
			<?php endif; ?>
		</div>

		<style>
			.lk-testimonials-heading { text-align: center; margin: 0; font-style: normal; }
			.lk-testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); }
			.lk-testimonial { text-align: left; }
			.lk-stars { display: flex; gap: 2px; margin-bottom: 18px; line-height: 1; }
			.lk-testimonial-quote { margin: 0 0 16px; }
			.lk-testimonial-name { margin: 0; }
			.lk-testimonials-link { display: flex; align-items: center; gap: 8px; width: max-content; margin: 45px auto 0; text-decoration: none; border-bottom: 1px solid currentColor; padding-bottom: 2px; }
			@media (max-width: 820px) {
				.lk-testimonials-grid { grid-template-columns: 1fr; gap: 45px !important; }
			}
		</style>
		<?php
	}
}
