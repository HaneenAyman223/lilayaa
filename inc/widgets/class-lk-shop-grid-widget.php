<?php
/**
 * Lila Kora — Shop Grid Widget
 *
 * "Choose the scarf. Choose the experience." — a heading followed by
 * a 3-card grid: two ordinary product cards (image + title/price/
 * link) and one distinct dark "gift card" tile with the LK monogram
 * in a circle instead of a photo.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-shop-grid-widget.php';
 *   $widgets_manager->register( new \LK_Shop_Grid_Widget() );
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

class LK_Shop_Grid_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-shop-grid';
	}

	public function get_title() {
		return 'LK — Shop Grid';
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'shop', 'products', 'gift card' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Shop Lila Kora', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Choose the scarf.', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — emphasized word', 'type' => Controls_Manager::TEXT, 'default' => 'Choose the experience.', 'label_block' => true, 'description' => 'Only the last word ("experience.") renders in italic — see the render for the split.' ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Product Cards
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_products',
			array( 'label' => 'Product Cards', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$repeater->add_control( 'crop', array( 'label' => 'Image focal point', 'type' => Controls_Manager::SELECT, 'default' => 'center center', 'options' => array( 'center center' => 'Center', '52% 46%' => 'Slightly upper-left (52% 46%)' ) ) );
		$repeater->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Product name', 'label_block' => true ) );
		$repeater->add_control( 'subtitle', array( 'label' => 'Subtitle / price', 'type' => Controls_Manager::TEXT, 'default' => 'Product detail', 'label_block' => true ) );
		$repeater->add_control( 'link_text', array( 'label' => 'Link text', 'type' => Controls_Manager::TEXT, 'default' => 'Shop', 'label_block' => true ) );
		$repeater->add_control( 'link_url', array( 'label' => 'Link URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true, 'label_block' => true ) );

		$this->add_control(
			'products',
			array(
				'label' => 'Cards', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'title' => 'Inception Collection', 'subtitle' => 'Ready-to-wear pure silk', 'link_text' => 'Shop', 'crop' => 'center center' ),
					array( 'title' => 'Experience Box', 'subtitle' => 'From AED 390', 'link_text' => 'Create', 'crop' => '52% 46%' ),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Gift Card
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_gift',
			array( 'label' => 'Gift Card', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'show_gift_card', array( 'label' => 'Show gift card tile', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$this->add_control(
			'gift_visual_type',
			array(
				'label'     => 'Visual',
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'monogram' => array( 'title' => 'Monogram', 'icon' => 'eicon-circle' ),
					'image'    => array( 'title' => 'Image', 'icon' => 'eicon-image' ),
				),
				'default'   => 'monogram',
				'condition' => array( 'show_gift_card' => 'yes' ),
			)
		);
		$this->add_control( 'gift_mark', array( 'label' => 'Monogram', 'type' => Controls_Manager::TEXT, 'default' => 'LK', 'condition' => array( 'show_gift_card' => 'yes', 'gift_visual_type' => 'monogram' ) ) );
		$this->add_control( 'gift_tagline', array( 'label' => 'Tagline', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'A creative experience, chosen by her.', 'label_block' => true, 'condition' => array( 'show_gift_card' => 'yes', 'gift_visual_type' => 'monogram' ) ) );
		$this->add_control( 'gift_image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ), 'condition' => array( 'show_gift_card' => 'yes', 'gift_visual_type' => 'image' ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'gift_image', 'default' => 'large', 'condition' => array( 'show_gift_card' => 'yes', 'gift_visual_type' => 'image' ) ) );
		$this->add_control( 'gift_title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Gift the Experience', 'label_block' => true, 'condition' => array( 'show_gift_card' => 'yes' ) ) );
		$this->add_control( 'gift_subtitle', array( 'label' => 'Subtitle', 'type' => Controls_Manager::TEXT, 'default' => 'Gift cards available', 'label_block' => true, 'condition' => array( 'show_gift_card' => 'yes' ) ) );
		$this->add_control( 'gift_link_text', array( 'label' => 'Link text', 'type' => Controls_Manager::TEXT, 'default' => 'Choose a gift', 'label_block' => true, 'condition' => array( 'show_gift_card' => 'yes' ) ) );
		$this->add_control( 'gift_link_url', array( 'label' => 'Link URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => 'mailto:hello@example.com' ), 'show_external' => true, 'label_block' => true, 'condition' => array( 'show_gift_card' => 'yes' ) ) );

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
				'selectors' => array( '{{WRAPPER}} .lk-shop' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 120, 'right' => 65, 'bottom' => 170, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'description' => 'Reference has extra bottom padding on this section (170px) since it\'s the last one before the FAQ block starts fresh.',
				'selectors' => array( '{{WRAPPER}} .lk-shop' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'heading_max_width', array( 'label' => 'Heading max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 1000 ) ), 'default' => array( 'unit' => 'px', 'size' => 760 ), 'selectors' => array( '{{WRAPPER}} .lk-shop-heading' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'heading_spacing', array( 'label' => 'Spacing below heading', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 100 ) ), 'default' => array( 'unit' => 'px', 'size' => 58 ), 'selectors' => array( '{{WRAPPER}} .lk-shop-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'grid_gap', array( 'label' => 'Card gap', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 60 ) ), 'default' => array( 'unit' => 'px', 'size' => 18 ), 'selectors' => array( '{{WRAPPER}} .lk-shop-grid' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array( 'label' => 'Heading Text', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-shop-heading h2' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-shop-heading h2',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 56 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 34 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Cards
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_cards',
			array( 'label' => 'Product Cards', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'card_border_color', array( 'label' => 'Border colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.16)', 'selectors' => array( '{{WRAPPER}} .lk-shop-card' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'card_bg', array( 'label' => 'Card background', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-shop-card' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'image_bg', array( 'label' => 'Image frame background', 'type' => Controls_Manager::COLOR, 'default' => '#F5EBEF', 'selectors' => array( '{{WRAPPER}} .lk-shop-image' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'image_height',
			array(
				'label' => 'Image height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 200, 'max' => 700 ) ),
				'default' => array( 'unit' => 'px', 'size' => 450 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 500 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 410 ),
				'selectors' => array( '{{WRAPPER}} .lk-shop-image' => 'height: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control( 'card_title_color', array( 'label' => 'Title colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-shop-info h3' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'card_title_typography', 'selector' => '{{WRAPPER}} .lk-shop-info h3', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 25.6 ) ) ) ) );
		$this->add_control( 'card_subtitle_color', array( 'label' => 'Subtitle colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-shop-info p' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'card_subtitle_typography', 'selector' => '{{WRAPPER}} .lk-shop-info p', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 12.3 ) ) ) ) );
		$this->add_control( 'card_link_color', array( 'label' => 'Link colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-shop-info a' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'card_link_typography', 'selector' => '{{WRAPPER}} .lk-shop-info a', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 10.9 ) ), 'font_weight' => array( 'default' => '600' ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.1 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Gift Card
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_gift',
			array( 'label' => 'Gift Card', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_gift_card' => 'yes' ) )
		);

		$this->add_control( 'gift_bg', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-gift-mark' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'gift_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'selectors' => array( '{{WRAPPER}} .lk-gift-mark' => 'color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'gift_height',
			array(
				'label' => 'Height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 200, 'max' => 700 ) ),
				'default' => array( 'unit' => 'px', 'size' => 450 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 500 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 410 ),
				'separator' => 'before',
				'description' => 'Independent of the product image height above — matches the reference\'s own values (450px → 500px → 410px) but can be set separately.',
				'selectors' => array( '{{WRAPPER}} .lk-gift-visual' => 'height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'gift_mark_border_color', array( 'label' => 'Monogram border colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFC5BA', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-gift-mark > span' => 'border-color: {{VALUE}};' ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="lk-shop">
			<div class="lk-shop-heading">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h2>
			</div>

			<div class="lk-shop-grid">
				<?php foreach ( $s['products'] as $index => $p ) :
					$image_html = Group_Control_Image_Size::get_attachment_image_html( $p, 'image' );
					$link_key   = 'plink_' . $index;
					$this->add_render_attribute( $link_key, 'href', ! empty( $p['link_url']['url'] ) ? $p['link_url']['url'] : '#' );
					if ( ! empty( $p['link_url']['is_external'] ) ) { $this->add_render_attribute( $link_key, 'target', '_blank' ); $this->add_render_attribute( $link_key, 'rel', 'noopener' ); }
					?>
					<article class="lk-shop-card">
						<div class="lk-shop-image" style="--lk-shop-crop: <?php echo esc_attr( $p['crop'] ); ?>;"><?php echo $image_html; ?></div>
						<div class="lk-shop-info">
							<h3><?php echo esc_html( $p['title'] ); ?></h3>
							<p><?php echo esc_html( $p['subtitle'] ); ?></p>
							<a <?php echo $this->get_render_attribute_string( $link_key ); ?>><?php echo esc_html( $p['link_text'] ); ?> <span>↗︎</span></a>
						</div>
					</article>
				<?php endforeach; ?>

				<?php if ( 'yes' === $s['show_gift_card'] ) :
					$this->add_render_attribute( 'gift_link', 'href', ! empty( $s['gift_link_url']['url'] ) ? $s['gift_link_url']['url'] : '#' );
					$use_image = 'image' === $s['gift_visual_type'] && ! empty( $s['gift_image']['url'] );
					?>
					<article class="lk-shop-card lk-shop-gift-card">
						<?php if ( $use_image ) :
							$gift_image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'gift_image', 'gift_image' );
							?>
							<div class="lk-shop-image lk-gift-visual"><?php echo $gift_image_html; ?></div>
						<?php else : ?>
							<div class="lk-gift-mark lk-gift-visual">
								<span><?php echo esc_html( $s['gift_mark'] ); ?></span>
								<p><?php echo esc_html( $s['gift_tagline'] ); ?></p>
							</div>
						<?php endif; ?>
						<div class="lk-shop-info">
							<h3><?php echo esc_html( $s['gift_title'] ); ?></h3>
							<p><?php echo esc_html( $s['gift_subtitle'] ); ?></p>
							<a <?php echo $this->get_render_attribute_string( 'gift_link' ); ?>><?php echo esc_html( $s['gift_link_text'] ); ?> <span>↗︎</span></a>
						</div>
					</article>
				<?php endif; ?>
			</div>
		</div>

		<style>
			.lk-shop-heading h2 { margin: 0.3em 0; font-style: normal; } .lk-shop-heading h2 em { font-style: italic; }
			.lk-shop-grid { display: grid; grid-template-columns: repeat(3, 1fr); }
			.lk-shop-card { border-style: solid; border-width: 1px; }
			.lk-shop-image { overflow: hidden; }
			.lk-shop-image img { width: 100%; height: 100%; display: block; object-fit: cover; object-position: var(--lk-shop-crop, center); }
			.lk-gift-mark { display: grid; place-content: center; padding: 40px; text-align: center; }
			.lk-gift-mark > span { display: grid; place-items: center; width: 95px; height: 95px; margin: 0 auto 35px; border-style: solid; border-width: 1px; font-family: 'Cormorant Garamond', serif; font-size: 40px; font-style: italic; }
			.lk-gift-mark > p { max-width: 250px; margin: 0 auto; font-family: 'Cormorant Garamond', serif; font-size: 28.8px; font-style: italic; line-height: 1.2; }
			.lk-shop-info { display: grid; grid-template-columns: 1fr auto; gap: 4px 20px; align-items: center; padding: 22px; }
			.lk-shop-info h3 { margin: 0; }
			.lk-shop-info p { margin: 0; }
			.lk-shop-info a { grid-column: 2; grid-row: 1 / span 2; text-decoration: none; white-space: nowrap; }
			@media (max-width: 820px) {
				.lk-shop-grid { grid-template-columns: 1fr; }
			}
			@media (max-width: 540px) {
				.lk-shop-info { grid-template-columns: 1fr; }
				.lk-shop-info a { grid-column: 1; grid-row: auto; margin-top: 10px; }
			}
		</style>
		<?php
	}
}
