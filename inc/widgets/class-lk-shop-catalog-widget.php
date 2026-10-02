<?php
/**
 * Lila Kora — Shop Catalog Widget
 *
 * The Shop page's intro + catalog grid:
 *
 *   Eyebrow / "Choose a finished piece. / Or create your own." — a lead
 *   paragraph and a row of anchor links to each catalog entry beside it.
 *
 *   Then a catalog grid of cards, each a clickable image, eyebrow, 2-line
 *   heading with an italic tail, a short paragraph, and EITHER a row of tag
 *   spans (e.g. "Pure silk · Four designs · Limited collection") OR a
 *   label+value price line (e.g. "From / AED 390") — never both — then a
 *   button. Any one card can also be the "wide" featured card (spans two
 *   grid columns) and/or use the light-on-dark styling of the Gift Card
 *   entry in the reference.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-shop-catalog-widget.php';
 *   $widgets_manager->register( new \LK_Shop_Catalog_Widget() );
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

class LK_Shop_Catalog_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-shop-catalog';
	}

	public function get_title() {
		return 'LK — Shop Catalog';
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'shop', 'catalog', 'products', 'grid' );
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

	private function color( $id, $label, $selector, $default, $prop = 'color', $separator = false, $important = false ) {
		$args = array(
			'label'     => $label,
			'type'      => Controls_Manager::COLOR,
			'default'   => $default,
			'selectors' => array( '{{WRAPPER}} ' . $selector => $prop . ': {{VALUE}}' . ( $important ? ' !important' : '' ) . ';' ),
		);
		if ( $separator ) {
			$args['separator'] = 'before';
		}
		$this->add_control( $id, $args );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Intro
		 * =======================================================*/
		$this->start_controls_section( 'section_content_intro', array( 'label' => 'Intro', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Shop Lila Kora', 'label_block' => true ) );
		$this->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Choose a finished piece.', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic second line', 'type' => Controls_Manager::TEXT, 'default' => 'Or create your own.', 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Discover pure silk scarves, creative pieces and gifts designed to hold more than one meaning.', 'label_block' => true ) );

		$nav = new Repeater();
		$nav->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Category', 'label_block' => true ) );
		$nav->add_control( 'link', array( 'label' => 'Link (usually #anchor-id of a card below)', 'type' => Controls_Manager::TEXT, 'default' => '#' ) );
		$this->add_control(
			'nav_links',
			array(
				'label'       => 'Category links',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $nav->get_controls(),
				'default'     => array(
					array( 'text' => 'Ready-to-Wear', 'link' => '#ready-to-wear' ),
					array( 'text' => 'Create Your Own', 'link' => '#create' ),
					array( 'text' => 'Cardigan', 'link' => '#wear' ),
					array( 'text' => 'Gift Card', 'link' => '#gift' ),
				),
				'title_field' => '{{{ text }}}',
				'separator'   => 'before',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Cards
		 * =======================================================*/
		$this->start_controls_section( 'section_content_cards', array( 'label' => 'Catalog Cards', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$card = new Repeater();
		$card->add_control( 'anchor_id', array( 'label' => 'Anchor ID', 'type' => Controls_Manager::TEXT, 'default' => 'card', 'description' => 'Lets a category link above jump straight to this card.' ) );
		$card->add_control( 'wide', array( 'label' => 'Wide (spans two columns)', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => '' ) );
		$card->add_control( 'style', array( 'label' => 'Card style', 'type' => Controls_Manager::SELECT, 'default' => 'normal', 'options' => array( 'normal' => 'Normal (dark text on light card)', 'light' => 'Light (light text on dark card — e.g. Gift Card)' ) ) );
		$card->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$card->add_control( 'link', array( 'label' => 'Card / button link', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ) ) );
		$card->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Eyebrow', 'label_block' => true ) );
		$card->add_control( 'heading_line1', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Product', 'label_block' => true ) );
		$card->add_control( 'heading_emphasis', array( 'label' => 'Heading — italic second line', 'type' => Controls_Manager::TEXT, 'default' => 'Name', 'label_block' => true ) );
		$card->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'Short description.', 'label_block' => true ) );
		$card->add_control( 'meta_type', array( 'label' => 'Below the paragraph, show', 'type' => Controls_Manager::SELECT, 'default' => 'tags', 'options' => array( 'tags' => 'Tags (e.g. Pure silk · Four designs)', 'price' => 'Price line (label + value)', 'none' => 'Nothing' ) ) );
		$card->add_control( 'tag_1', array( 'label' => 'Tag 1', 'type' => Controls_Manager::TEXT, 'condition' => array( 'meta_type' => 'tags' ) ) );
		$card->add_control( 'tag_2', array( 'label' => 'Tag 2', 'type' => Controls_Manager::TEXT, 'condition' => array( 'meta_type' => 'tags' ) ) );
		$card->add_control( 'tag_3', array( 'label' => 'Tag 3', 'type' => Controls_Manager::TEXT, 'condition' => array( 'meta_type' => 'tags' ) ) );
		$card->add_control( 'price_label', array( 'label' => 'Price — label', 'type' => Controls_Manager::TEXT, 'default' => 'From', 'condition' => array( 'meta_type' => 'price' ) ) );
		$card->add_control( 'price_value', array( 'label' => 'Price — value', 'type' => Controls_Manager::TEXT, 'default' => 'AED 0', 'condition' => array( 'meta_type' => 'price' ) ) );
		$card->add_control( 'button_text', array( 'label' => 'Button text', 'type' => Controls_Manager::TEXT, 'default' => 'View', 'label_block' => true, 'separator' => 'before' ) );

		$this->add_control(
			'cards',
			array(
				'label'       => 'Cards',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $card->get_controls(),
				'default'     => array(
					array( 'anchor_id' => 'ready-to-wear', 'wide' => 'yes', 'style' => 'normal', 'link' => array( 'url' => './silk-scarf.html' ), 'eyebrow' => 'The Inception Collection', 'heading_line1' => 'Ready-to-Wear', 'heading_emphasis' => 'Silk Scarves', 'description' => 'Four original designs in 100% pure silk, finished with hand-rolled edges.', 'meta_type' => 'tags', 'tag_1' => 'Pure silk', 'tag_2' => 'Four designs', 'tag_3' => 'Limited collection', 'button_text' => 'View the Scarves' ),
					array( 'anchor_id' => 'create', 'wide' => '', 'style' => 'normal', 'link' => array( 'url' => './experience-box.html' ), 'eyebrow' => 'Create at home', 'heading_line1' => 'Canvas-to-Scarf', 'heading_emphasis' => 'Experience Box', 'description' => 'Everything needed to create an original artwork and receive it as a scarf.', 'meta_type' => 'price', 'price_label' => 'From', 'price_value' => 'AED 390', 'button_text' => 'Choose Your Box' ),
					array( 'anchor_id' => 'wear', 'wide' => '', 'style' => 'normal', 'link' => array( 'url' => './cardigan.html' ), 'eyebrow' => 'Made to order', 'heading_line1' => 'The Interchangeable', 'heading_emphasis' => 'Cardigan', 'description' => 'One timeless base with cuffs chosen from the collection or created from your artwork.', 'meta_type' => 'price', 'price_label' => 'Availability', 'price_value' => 'By enquiry', 'button_text' => 'Explore the Cardigan' ),
					array( 'anchor_id' => 'gift', 'wide' => '', 'style' => 'light', 'link' => array( 'url' => './gift-card.html' ), 'eyebrow' => 'A gift with choice', 'heading_line1' => 'Lila Kora', 'heading_emphasis' => 'Gift Card', 'description' => 'Let her choose a scarf, an Experience Box or a workshop that feels like hers.', 'meta_type' => 'price', 'price_label' => 'Value', 'price_value' => 'You choose', 'button_text' => 'Choose a Gift Card' ),
				),
				'title_field' => '{{{ heading_line1 }}} {{{ heading_emphasis }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

$this->add_control( 'heading_intro_section', array( 'label' => 'Intro Section', 'type' => Controls_Manager::HEADING ) );
		$this->color( 'intro_bg_color', 'Background', '.lk-shopcat-intro-section', '#F8ECE9', 'background-color' );
		$this->add_responsive_control( 'intro_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-shopcat-intro' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'intro_padding',
			array(
				'label'          => 'Padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 110, 'right' => 65, 'bottom' => 70, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 85, 'right' => 32, 'bottom' => 55, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 65, 'right' => 24, 'bottom' => 50, 'left' => 24, 'unit' => 'px' ),
				'description'    => 'The bottom side is deliberately smaller than the top — matches the reference, which sits directly above the catalog grid.',
				'selectors'      => array( '{{WRAPPER}} .lk-shopcat-intro' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_control( 'heading_catalog_section', array( 'label' => 'Catalog Section', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->color( 'catalog_bg_color', 'Background', '.lk-shopcat-catalog-section', '#FFFEFD', 'background-color' );
		$this->add_responsive_control( 'catalog_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'selectors' => array( '{{WRAPPER}} .lk-shopcat-grid' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control( 'catalog_padding', array( 'label' => 'Padding (frames the card grid)', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 60 ) ), 'default' => array( 'unit' => 'px', 'size' => 14 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 8 ), 'selectors' => array( '{{WRAPPER}} .lk-shopcat-catalog-section' => 'padding: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'grid_gap', array( 'label' => 'Gap between cards', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 14 ), 'selectors' => array( '{{WRAPPER}} .lk-shopcat-grid' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Intro
		 * =======================================================*/
		$this->start_controls_section( 'section_style_intro', array( 'label' => 'Intro', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-shopcat-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-shopcat-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-shopcat-title h1', '#57282D', 'color', true );
		$this->color( 'heading_emphasis_color', 'Italic line colour', '.lk-shopcat-title h1 em', '#57282D' );
$this->typo( 'heading_typography', 'Heading typography', '.lk-shopcat-title h1', 'Cormorant Garamond', 56, '400', -0.025, false, 0.98, 42, 34 );
		$this->add_responsive_control( 'heading_max_width', array( 'label' => 'Heading max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 1000 ) ), 'default' => array( 'unit' => 'px', 'size' => 850 ), 'selectors' => array( '{{WRAPPER}} .lk-shopcat-title h1' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->color( 'desc_color', 'Paragraph colour', '.lk-shopcat-lead > p', '#8F8584', 'color', true );
		$this->typo( 'desc_typography', 'Paragraph typography', '.lk-shopcat-lead > p', 'Montserrat', 16, '', null, false, 1.65 );
$this->color( 'navlink_color', 'Category link colour', '.lk-shopcat-nav a', '#692137' );
		$this->color( 'navlink_border', 'Category link border', '.lk-shopcat-nav a', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->typo( 'navlink_typography', 'Category link typography', '.lk-shopcat-nav a', 'Montserrat', 10.6, '600', 0.08, true );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Cards (normal)
		 * =======================================================*/
		$this->start_controls_section( 'section_style_cards', array( 'label' => 'Cards — Normal', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'card_bg', 'Background', '.lk-shopcat-card:not(.is-light)', '#F8ECE9', 'background-color' );
		$this->add_responsive_control( 'card_image_height', array( 'label' => 'Image height (normal cards)', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 150, 'max' => 700 ) ), 'default' => array( 'unit' => 'px', 'size' => 540 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 460 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 430 ), 'selectors' => array( '{{WRAPPER}} .lk-shopcat-card' => '--lk-shopcat-image-h: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'wide_image_height', array( 'label' => 'Image height (wide card)', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 150, 'max' => 900 ) ), 'default' => array( 'unit' => 'px', 'size' => 680 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 600 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 430 ), 'description' => 'At the phone breakpoint the wide card matches the normal cards\' height above.', 'selectors' => array( '{{WRAPPER}} .lk-shopcat-card.is-wide' => '--lk-shopcat-wide-h: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'card_padding', array( 'label' => 'Info padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px' ), 'default' => array( 'top' => 36, 'right' => 36, 'bottom' => 36, 'left' => 36, 'unit' => 'px' ), 'mobile_default' => array( 'top' => 26, 'right' => 24, 'bottom' => 26, 'left' => 24, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .lk-shopcat-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );

		$this->color( 'card_eyebrow_color', 'Eyebrow colour', '.lk-shopcat-card:not(.is-light) .lk-shopcat-card-eyebrow', '#FF715E' );
		$this->color( 'card_heading_color', 'Heading colour', '.lk-shopcat-card:not(.is-light) h2', '#692137', 'color', true );
		$this->typo( 'card_heading_typography', 'Heading typography (normal cards)', '.lk-shopcat-card:not(.is-wide) h2', 'Cormorant Garamond', 46, '400', null, false, 0.98, 38, 32 );
		$this->typo( 'wide_heading_typography', 'Heading typography (wide card)', '.lk-shopcat-card.is-wide h2', 'Cormorant Garamond', 58, '400', null, false, 0.98, 46, 36 );
		$this->color( 'card_desc_color', 'Paragraph colour', '.lk-shopcat-card:not(.is-light) .lk-shopcat-card-desc', '#8F8584' );
		$this->typo( 'card_desc_typography', 'Paragraph typography', '.lk-shopcat-card-desc', 'Montserrat', 14.5, '', null, false, 1.6 );
		$this->color( 'card_tag_color', 'Tags colour', '.lk-shopcat-card:not(.is-light) .lk-shopcat-tags span', '#692137' );
$this->color( 'card_tag_border', 'Tags border', '.lk-shopcat-card:not(.is-light) .lk-shopcat-tags span', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->typo( 'card_tag_typography', 'Tags typography', '.lk-shopcat-tags span', 'Montserrat', 10.6, '', null, true );
$this->color( 'card_price_border', 'Price line border', '.lk-shopcat-card:not(.is-light) .lk-shopcat-price', 'rgba(105,33,55,0.16)', 'border-color' );
		$this->color( 'card_price_label_color', 'Price label colour', '.lk-shopcat-card:not(.is-light) .lk-shopcat-price span', '#8F8584' );
		$this->color( 'card_price_value_color', 'Price value colour', '.lk-shopcat-card:not(.is-light) .lk-shopcat-price strong', '#692137' );
		$this->typo( 'card_price_typography', 'Price value typography', '.lk-shopcat-price strong', 'Cormorant Garamond', 22, '500' );

		$this->add_control( 'heading_card_button', array( 'label' => 'Button (normal cards)', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->start_controls_tabs( 'tabs_card_btn' );
		$this->start_controls_tab( 'tab_card_btn_normal', array( 'label' => 'Normal' ) );
$this->color( 'card_btn_bg', 'Background', '.lk-shopcat-card:not(.is-light) .lk-shopcat-btn', '#692137', 'background-color', false, true );
		$this->color( 'card_btn_color', 'Text colour', '.lk-shopcat-card:not(.is-light) .lk-shopcat-btn', '#FFFFFF', 'color', false, true );
		$this->color( 'card_btn_border', 'Border colour', '.lk-shopcat-card:not(.is-light) .lk-shopcat-btn', '#692137', 'border-color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_card_btn_hover', array( 'label' => 'Hover' ) );
		$this->color( 'card_btn_bg_hover', 'Background', '.lk-shopcat-card:not(.is-light) .lk-shopcat-btn:hover', 'rgba(0,0,0,0)', 'background-color', false, true );
		$this->color( 'card_btn_color_hover', 'Text colour', '.lk-shopcat-card:not(.is-light) .lk-shopcat-btn:hover', '#692137', 'color', false, true );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->typo( 'card_btn_typography', 'Button typography', '.lk-shopcat-btn', 'Montserrat', 10.9, '600', 0.14, true );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Cards (light / gift variant)
		 * =======================================================*/
		$this->start_controls_section( 'section_style_cards_light', array( 'label' => 'Cards — Light (e.g. Gift Card)', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'light_card_bg', 'Background', '.lk-shopcat-card.is-light', '#692137', 'background-color' );
		$this->color( 'light_eyebrow_color', 'Eyebrow colour', '.lk-shopcat-card.is-light .lk-shopcat-card-eyebrow', '#FFC5BA' );
		$this->color( 'light_heading_color', 'Heading colour', '.lk-shopcat-card.is-light h2', '#FFFFFF', 'color', true );
		$this->color( 'light_desc_color', 'Paragraph colour', '.lk-shopcat-card.is-light .lk-shopcat-card-desc', 'rgba(255,255,255,0.75)' );
$this->color( 'light_price_border', 'Price line border', '.lk-shopcat-card.is-light .lk-shopcat-price', 'rgba(255,255,255,0.23)', 'border-color' );
		$this->color( 'light_price_label_color', 'Price label colour', '.lk-shopcat-card.is-light .lk-shopcat-price span', 'rgba(255,255,255,0.66)' );
		$this->color( 'light_price_value_color', 'Price value colour', '.lk-shopcat-card.is-light .lk-shopcat-price strong', '#FFC5BA' );
		$this->color( 'light_tag_color', 'Tags colour', '.lk-shopcat-card.is-light .lk-shopcat-tags span', '#FFC5BA' );

		$this->add_control( 'heading_light_btn', array( 'label' => 'Button', 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->start_controls_tabs( 'tabs_light_btn' );
		$this->start_controls_tab( 'tab_light_btn_normal', array( 'label' => 'Normal' ) );
$this->color( 'light_btn_bg', 'Background', '.lk-shopcat-card.is-light .lk-shopcat-btn', '#FFC5BA', 'background-color', false, true );
		$this->color( 'light_btn_color', 'Text colour', '.lk-shopcat-card.is-light .lk-shopcat-btn', '#692137', 'color', false, true );
		$this->color( 'light_btn_border', 'Border colour', '.lk-shopcat-card.is-light .lk-shopcat-btn', '#FFC5BA', 'border-color', false, true );
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_light_btn_hover', array( 'label' => 'Hover' ) );
		$this->color( 'light_btn_bg_hover', 'Background', '.lk-shopcat-card.is-light .lk-shopcat-btn:hover', 'rgba(0,0,0,0)', 'background-color', false, true );
		$this->color( 'light_btn_color_hover', 'Text colour', '.lk-shopcat-card.is-light .lk-shopcat-btn:hover', '#FFC5BA', 'color', false, true );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="lk-shopcat-intro-section">
			<div class="lk-shopcat-intro">
				<div class="lk-shopcat-title">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-shopcat-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
					<h1><?php echo esc_html( $s['heading_line1'] ); ?><?php if ( ! empty( $s['heading_emphasis'] ) ) : ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em><?php endif; ?></h1>
				</div>
				<div class="lk-shopcat-lead">
					<?php if ( ! empty( $s['description'] ) ) : ?><p><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $s['nav_links'] ) ) : ?>
						<nav class="lk-shopcat-nav" aria-label="Shop categories">
							<?php foreach ( $s['nav_links'] as $link ) : ?><a href="<?php echo esc_attr( $link['link'] ); ?>"><?php echo esc_html( $link['text'] ); ?></a><?php endforeach; ?>
						</nav>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<?php if ( ! empty( $s['cards'] ) ) : ?>
			<section class="lk-shopcat-catalog-section" id="products">
				<div class="lk-shopcat-grid">
					<?php foreach ( $s['cards'] as $c ) :
						$image_html = Group_Control_Image_Size::get_attachment_image_html( $c, 'image', 'image' );
						$anchor     = ! empty( $c['anchor_id'] ) ? sanitize_html_class( $c['anchor_id'] ) : '';
						$classes    = array( 'lk-shopcat-card' );
						if ( 'yes' === $c['wide'] ) {
							$classes[] = 'is-wide';
						}
						if ( 'light' === $c['style'] ) {
							$classes[] = 'is-light';
						}
						$link_url = ! empty( $c['link']['url'] ) ? $c['link']['url'] : '#';
						?>
						<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $anchor ? ' id="' . esc_attr( $anchor ) . '"' : ''; ?>>
							<a class="lk-shopcat-image" href="<?php echo esc_url( $link_url ); ?>"><?php echo $image_html; ?></a>
							<div class="lk-shopcat-info">
								<?php if ( ! empty( $c['eyebrow'] ) ) : ?><p class="lk-shopcat-card-eyebrow"><?php echo esc_html( $c['eyebrow'] ); ?></p><?php endif; ?>
								<h2><?php echo esc_html( $c['heading_line1'] ); ?><?php if ( ! empty( $c['heading_emphasis'] ) ) : ?><br><em><?php echo esc_html( $c['heading_emphasis'] ); ?></em><?php endif; ?></h2>
								<?php if ( ! empty( $c['description'] ) ) : ?><p class="lk-shopcat-card-desc"><?php echo esc_html( $c['description'] ); ?></p><?php endif; ?>

								<?php if ( 'tags' === $c['meta_type'] && ( ! empty( $c['tag_1'] ) || ! empty( $c['tag_2'] ) || ! empty( $c['tag_3'] ) ) ) : ?>
									<div class="lk-shopcat-tags">
										<?php foreach ( array( $c['tag_1'], $c['tag_2'], $c['tag_3'] ) as $tag ) : if ( '' !== $tag ) : ?><span><?php echo esc_html( $tag ); ?></span><?php endif; endforeach; ?>
									</div>
								<?php elseif ( 'price' === $c['meta_type'] && ( ! empty( $c['price_label'] ) || ! empty( $c['price_value'] ) ) ) : ?>
									<div class="lk-shopcat-price"><span><?php echo esc_html( $c['price_label'] ); ?></span><strong><?php echo esc_html( $c['price_value'] ); ?></strong></div>
								<?php endif; ?>

								<?php if ( ! empty( $c['button_text'] ) ) : ?><a class="lk-shopcat-btn" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $c['button_text'] ); ?></a><?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<style>
			.lk-shopcat { box-sizing: border-box; }
			.lk-shopcat-intro { display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 20px 7vw; align-items: end; }
			.lk-shopcat-title h1 { margin: 0; font-style: normal; }
			.lk-shopcat-title h1 em { font-style: italic; }
			.lk-shopcat-lead > p { margin: 0 0 16px; }
			.lk-shopcat-nav { display: flex; flex-wrap: wrap; gap: 10px 22px; }
			.lk-shopcat-nav a { padding: 10px 14px; text-decoration: none; border: 1px solid; text-transform: uppercase; transition: color .25s ease, background .25s ease, border-color .25s ease; }
			.lk-shopcat-nav a:hover { color: #FFFFFF !important; background: #692137; border-color: #692137 !important; }
			.lk-shopcat-grid { display: grid; grid-template-columns: 1fr 1fr; }
			.lk-shopcat-card { display: grid; grid-template-rows: var(--lk-shopcat-image-h, 540px) auto; background: #F8ECE9; }
			.lk-shopcat-card.is-light { background: #7A2C42; }
			.lk-shopcat-card.is-wide { grid-column: 1 / -1; grid-template-columns: 1.15fr 0.85fr; grid-template-rows: var(--lk-shopcat-wide-h, 680px); }
			.lk-shopcat-image { position: relative; display: block; margin: 0; overflow: hidden; }
			.lk-shopcat-image img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .5s ease; }
			.lk-shopcat-card:hover .lk-shopcat-image img { transform: scale(1.03); }
			.lk-shopcat-card:not(.is-wide) .lk-shopcat-image { height: var(--lk-shopcat-image-h, 340px); }
			.lk-shopcat-info { box-sizing: border-box; display: flex; flex-direction: column; align-items: flex-start; justify-content: center; }
			.lk-shopcat-info h2 { margin: 0 0 12px; font-style: normal; }
			.lk-shopcat-info h2 em { font-style: italic; }
			.lk-shopcat-card-desc { margin: 0 0 18px; }
			.lk-shopcat-tags { display: flex; flex-wrap: wrap; gap: 8px; margin: 18px 0 30px; }
			.lk-shopcat-tags span { padding: 8px 11px; border: 1px solid; text-transform: uppercase; }
			.lk-shopcat-price { width: 100%; box-sizing: border-box; display: flex; align-items: baseline; justify-content: space-between; gap: 20px; margin: 22px 0 30px; padding: 16px 0; border-top: 1px solid; border-bottom: 1px solid; }
			.lk-shopcat-price span { letter-spacing: .1em; text-transform: uppercase; }
			.lk-shopcat-btn { min-height: 48px; box-sizing: border-box; display: inline-flex; align-items: center; justify-content: center; margin-top: auto; padding: 12px 24px; text-decoration: none; border: 1px solid; border-radius: 0; cursor: pointer; align-self: flex-start; transition: color .25s ease, background .25s ease, transform .25s ease; }
			.lk-shopcat-btn:hover { transform: translateY(-2px); }
			@media (max-width: 820px) {
				.lk-shopcat-intro { grid-template-columns: 1fr; }
				.lk-shopcat-grid { grid-template-columns: 1fr; }
				.lk-shopcat-card, .lk-shopcat-card.is-wide { grid-template-columns: 1fr; grid-template-rows: var(--lk-shopcat-image-h, 540px) auto; }
			}
		</style>
		<?php
	}
}
