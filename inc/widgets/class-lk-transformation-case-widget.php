<?php
/**
 * Lila Kora — Transformation Case Widget
 *
 * "See the transformation, step by step." — a centred section
 * heading followed by a 3-card grid (Original Canvas / Refined
 * Artwork / Finished Scarf), each a square image with a hover zoom
 * and a number/title/description caption beneath. Collapses to a
 * horizontal scroll-snap row on mobile, matching the reference.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-transformation-case-widget.php';
 *   $widgets_manager->register( new \LK_Transformation_Case_Widget() );
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

class LK_Transformation_Case_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-transformation-case';
	}

	public function get_title() {
		return 'LK — Transformation Case';
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'transformation', 'case', 'before after' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array( 'label' => 'Section Heading', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Real Canvas-to-Scarf Transformations', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'See the transformation,', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — emphasized line', 'type' => Controls_Manager::TEXT, 'default' => 'step by step.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Every finished piece begins with an original canvas. We refine the artwork with you, then translate the approved design onto your scarf.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Cards
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_cards',
			array( 'label' => 'Cards', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$repeater->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$repeater->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Card title', 'label_block' => true ) );
		$repeater->add_control( 'description', array( 'label' => 'Description', 'type' => Controls_Manager::TEXT, 'default' => 'Card description.', 'label_block' => true ) );

		$this->add_control(
			'cards',
			array(
				'label' => 'Cards', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'number' => '01', 'title' => 'Original Canvas', 'description' => 'Created freely by the client.' ),
					array( 'number' => '02', 'title' => 'Refined Artwork', 'description' => 'Digitized and approved together.' ),
					array( 'number' => '03', 'title' => 'Finished Scarf', 'description' => 'The approved artwork, crafted to wear.' ),
				),
				'title_field' => '{{{ number }}} — {{{ title }}}',
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
		$this->add_control( 'link_text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'See more client transformations', 'label_block' => true, 'condition' => array( 'show_link' => 'yes' ) ) );
		$this->add_control( 'link_url', array( 'label' => 'URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => 'https://instagram.com/', 'is_external' => true ), 'show_external' => true, 'condition' => array( 'show_link' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Section & Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_section',
			array( 'label' => 'Section & Heading', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-transcase' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-transcase' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control( 'heading_max_width', array( 'label' => 'Heading block max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 1000 ) ), 'default' => array( 'unit' => 'px', 'size' => 780 ), 'selectors' => array( '{{WRAPPER}} .lk-transcase-heading' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control( 'heading_spacing', array( 'label' => 'Spacing below heading block', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 120 ) ), 'default' => array( 'unit' => 'px', 'size' => 70 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 45 ), 'selectors' => array( '{{WRAPPER}} .lk-transcase-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-transcase-heading .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'eyebrow_typography', 'selector' => '{{WRAPPER}} .lk-transcase-heading .lk-eyebrow', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11 ) ), 'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.19 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ) ) );

		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-transcase-heading h2' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_emphasis_color', array( 'label' => 'Emphasized word colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'selectors' => array( '{{WRAPPER}} .lk-transcase-heading h2 em' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-transcase-heading h2',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 44 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 32 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );

		$this->add_control( 'description_color', array( 'label' => 'Description colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-transcase-heading p.lk-transcase-desc' => 'color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Cards
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_cards',
			array( 'label' => 'Cards', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'card_gap', array( 'label' => 'Gap', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 60 ) ), 'default' => array( 'unit' => 'px', 'size' => 18 ), 'selectors' => array( '{{WRAPPER}} .lk-transcase-grid' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'card_bg', array( 'label' => 'Image frame background', 'type' => Controls_Manager::COLOR, 'default' => '#F5EBEF', 'selectors' => array( '{{WRAPPER}} .lk-transcase-card > div' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control(
			'image_fit',
			array(
				'label'       => 'Image fit',
				'type'        => Controls_Manager::SELECT,
				'default'     => 'cover',
				'options'     => array(
					'cover'   => 'Fill the space (crops if needed)',
					'contain' => 'Show the whole photo (may leave gaps)',
				),
				'description' => 'The frame background colour above only shows through when this is set to "Show the whole photo."',
				'selectors'   => array( '{{WRAPPER}} .lk-transcase-card img' => 'object-fit: {{VALUE}};' ),
			)
		);

		$this->add_control( 'card_number_color', array( 'label' => 'Number colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-transcase-card figcaption span' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'card_number_typography', 'selector' => '{{WRAPPER}} .lk-transcase-card figcaption span', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ) ) ) );

		$this->add_control( 'card_title_color', array( 'label' => 'Title colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-transcase-card figcaption strong' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'card_title_typography', 'selector' => '{{WRAPPER}} .lk-transcase-card figcaption strong', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 23.2 ) ), 'font_weight' => array( 'default' => '500' ) ) ) );

		$this->add_control( 'card_desc_color', array( 'label' => 'Description colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-transcase-card figcaption p' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'card_desc_typography', 'selector' => '{{WRAPPER}} .lk-transcase-card figcaption p', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 12.5 ) ) ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Bottom Link
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_link',
			array( 'label' => 'Bottom Link', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_link' => 'yes' ) )
		);

		$this->add_control( 'link_color', array( 'label' => 'Colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-transcase-link' => 'color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'link_typography', 'selector' => '{{WRAPPER}} .lk-transcase-link', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 11.5 ) ), 'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.1 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		if ( 'yes' === $s['show_link'] && ! empty( $s['link_text'] ) ) {
			$this->add_render_attribute( 'link', 'class', 'lk-transcase-link' );
			$this->add_link_attributes( 'link', $s['link_url'] );
		}
		?>
		<div class="lk-transcase">
			<div class="lk-transcase-heading">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-transcase-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>
			</div>

			<div class="lk-transcase-grid">
				<?php foreach ( $s['cards'] as $card ) :
					$image_html = Group_Control_Image_Size::get_attachment_image_html( $card, 'image' );
					?>
					<figure class="lk-transcase-card">
						<div><?php echo $image_html; ?></div>
						<figcaption>
							<span><?php echo esc_html( $card['number'] ); ?></span>
							<strong><?php echo esc_html( $card['title'] ); ?></strong>
							<p><?php echo esc_html( $card['description'] ); ?></p>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			</div>

			<?php if ( 'yes' === $s['show_link'] && ! empty( $s['link_text'] ) ) : ?>
				<a <?php echo $this->get_render_attribute_string( 'link' ); ?>><?php echo esc_html( $s['link_text'] ); ?> <span>↗︎</span></a>
			<?php endif; ?>
		</div>

		<style>
			.lk-transcase { overflow: hidden; }
			.lk-transcase-heading { text-align: center; }
			.lk-transcase-heading h2 { margin: 0.3em 0; font-style: normal; } .lk-transcase-heading h2 em { font-style: italic; }
			.lk-transcase-heading p.lk-transcase-desc { margin: 0 auto; max-width: 640px; }
			.lk-transcase-grid { display: grid; grid-template-columns: repeat(3, 1fr); }
			.lk-transcase-card { margin: 0; }
			.lk-transcase-card > div { aspect-ratio: 1; overflow: hidden; }
			.lk-transcase-card img { width: 100%; height: 100%; display: block; transition: transform .8s ease; }
			.lk-transcase-card:hover img { transform: scale(1.025); }
			.lk-transcase-card figcaption { display: grid; grid-template-columns: auto 1fr; gap: 2px 14px; padding: 20px 2px 0; }
			.lk-transcase-card figcaption span { grid-row: 1 / span 2; font-style: normal; }
			.lk-transcase-card figcaption strong { font-style: normal; }
			.lk-transcase-card figcaption p { margin: 0; }
			.lk-transcase-link { display: flex; align-items: center; gap: 8px; width: max-content; margin: 38px auto 0; text-decoration: none; border-bottom: 1px solid currentColor; padding-bottom: 2px; }
			@media (max-width: 820px) {
				.lk-transcase-grid {
					display: flex;
					gap: 14px;
					margin-right: -24px;
					padding-right: 24px;
					overflow-x: auto;
					scroll-snap-type: x mandatory;
				}
				.lk-transcase-card { min-width: 82vw; scroll-snap-align: start; }
			}
		</style>
		<?php
	}
}
