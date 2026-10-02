<?php
/**
 * Lila Kora — Site Header Widget
 *
 * The sticky top bar: brand mark + name, a centred nav repeater, a
 * text link + button on the right, and — below the configurable
 * breakpoint — a hamburger that reveals a full-width mobile menu.
 *
 * Note: this duplicates the site's global navigation as an Elementor
 * widget, which only makes sense if you're placing it once, at the
 * very top of a template Elementor controls for the whole site (e.g.
 * an Elementor Pro Theme Builder header, or the top of every page).
 * If you're only ever building one-off Elementor pages under Astra's
 * normal header, Astra's own Header Builder (Customizer → Header)
 * is the simpler place to manage logo + menu instead.
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
use Elementor\Group_Control_Box_Shadow;

class LK_Header_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-header';
	}

	public function get_title() {
		return 'LK — Site Header';
	}

	public function get_icon() {
		return 'eicon-header';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'header', 'navigation', 'menu' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Brand
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_brand',
			array(
				'label' => 'Brand',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'logo_type',
			array(
				'label'   => 'Logo type',
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'text'  => array( 'title' => 'Mark + Text', 'icon' => 'eicon-t-letter' ),
					'image' => array( 'title' => 'Image', 'icon' => 'eicon-image' ),
				),
				'default' => 'text',
			)
		);

		$this->add_control(
			'brand_mark_text',
			array(
				'label'       => 'Mark (initials in the box)',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'LK',
				'condition'   => array( 'logo_type' => 'text' ),
			)
		);
		$this->add_control(
			'brand_name_text',
			array(
				'label'       => 'Brand name',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'LILA KORA',
				'label_block' => true,
				'condition'   => array( 'logo_type' => 'text' ),
			)
		);
		$this->add_control(
			'logo_image',
			array(
				'label'     => 'Logo image',
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'logo_type' => 'image' ),
			)
		);
		$this->add_responsive_control(
			'logo_image_width',
			array(
				'label'     => 'Image width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 40, 'max' => 400 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 140 ),
				'condition' => array( 'logo_type' => 'image' ),
				'selectors' => array( '{{WRAPPER}} .lk-brand img' => 'width: {{SIZE}}{{UNIT}}; height: auto;' ),
			)
		);

		$this->add_control(
			'brand_link',
			array(
				'label'         => 'Logo links to',
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => home_url( '/' ) ),
				'show_external' => false,
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Navigation
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_nav',
			array(
				'label' => 'Navigation',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'type',
			array(
				'label'   => 'Type',
				'type'    => Controls_Manager::SELECT,
				'default' => 'link',
				'options' => array(
					'link'     => 'Link',
					'dropdown' => 'Dropdown',
				),
			)
		);
		$repeater->add_control(
			'label',
			array(
				'label'       => 'Label',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Menu Item',
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'link',
			array(
				'label'         => 'Link',
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => '#' ),
				'show_external' => true,
				'label_block'   => true,
				'condition'     => array( 'type' => 'link' ),
			)
		);
		$repeater->add_control(
			'dropdown_items',
			array(
				'label'       => 'Dropdown links',
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'default'     => "Public Workshops | ./public-workshops.html\nPrivate Workshops | ./private-workshops.html",
				'description' => 'One per line, as: Label | URL',
				'condition'   => array( 'type' => 'dropdown' ),
			)
		);

		$this->add_control(
			'nav_items',
			array(
				'label'       => 'Menu items',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'type' => 'link', 'label' => 'Home', 'link' => array( 'url' => home_url( '/' ) ) ),
					array( 'type' => 'dropdown', 'label' => 'Shop', 'dropdown_items' => "Shop All | ./shop.html\nCanvas-to-Scarf Experience Box | ./experience-box.html\nReady-to-Wear Silk Scarves | ./scarves.html\nInterchangeable Cardigan | ./cardigan.html\nGift Cards | ./gift-cards.html" ),
					array( 'type' => 'dropdown', 'label' => 'Experiences', 'dropdown_items' => "Public Workshops | ./public-workshops.html\nPrivate Events | ./private-workshops.html\nGallery | ./gallery.html" ),
					array( 'type' => 'link', 'label' => 'For Business', 'link' => array( 'url' => '#enquire' ) ),
					array( 'type' => 'link', 'label' => 'Our Story', 'link' => array( 'url' => './our-story.html' ) ),
					array( 'type' => 'link', 'label' => 'Contact', 'link' => array( 'url' => '#contact' ) ),
				),
				'title_field' => '{{{ label }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Actions
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_actions',
			array(
				'label' => 'Header Actions',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'shop_text',
			array(
				'label'       => 'Text link — label',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Shop',
				'label_block' => true,
			)
		);
		$this->add_control(
			'shop_link',
			array(
				'label'         => 'Text link — url',
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => '#shop' ),
				'show_external' => true,
			)
		);

		$this->add_control(
			'button_divider',
			array( 'type' => Controls_Manager::DIVIDER )
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => 'Button — label',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Enquire',
				'label_block' => true,
			)
		);
		$this->add_control(
			'button_link',
			array(
				'label'         => 'Button — url',
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => '#enquire' ),
				'show_external' => true,
			)
		);
		$this->add_control(
			'mobile_button_text',
			array(
				'label'       => 'Button label inside mobile menu',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Plan an Experience',
				'label_block' => true,
				'description' => 'The reference site swaps in a longer label once the button moves inside the open mobile menu.',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Behaviour
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_behaviour',
			array(
				'label' => 'Behaviour',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'sticky',
			array(
				'label'     => 'Sticky header',
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => 'Sticky',
				'label_off' => 'Static',
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'mobile_breakpoint',
			array(
				'label'       => 'Collapse to hamburger below (px)',
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1120,
				'description' => 'Matches the reference breakpoint. The full nav shows above this width; a hamburger menu replaces it below.',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Cart
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_cart',
			array( 'label' => 'Cart', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'show_cart', array( 'label' => 'Show cart icon', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$this->add_control(
			'cart_url',
			array(
				'label'       => 'Cart link (optional)',
				'type'        => Controls_Manager::URL,
				'placeholder' => 'Leave empty to use the WooCommerce cart page',
				'condition'   => array( 'show_cart' => 'yes' ),
			)
		);
		$this->add_control( 'cart_label', array( 'label' => 'Accessible label', 'type' => Controls_Manager::TEXT, 'default' => 'Cart', 'condition' => array( 'show_cart' => 'yes' ) ) );
		$this->add_control( 'cart_hide_zero', array( 'label' => 'Hide the count badge when the cart is empty', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => 'yes', 'condition' => array( 'show_cart' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Cart
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_cart',
			array( 'label' => 'Cart', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_cart' => 'yes' ) )
		);

		$this->add_control( 'cart_color', array( 'label' => 'Icon colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'selectors' => array( '{{WRAPPER}} .lk-cart-link' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'cart_color_hover', array( 'label' => 'Icon colour on hover', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-cart-link:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'cart_icon_size', array( 'label' => 'Icon size', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 14, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 22 ), 'selectors' => array( '{{WRAPPER}} .lk-cart-link svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'cart_badge_bg', array( 'label' => 'Badge background', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-cart-count' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'cart_badge_color', array( 'label' => 'Badge text colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => array( '{{WRAPPER}} .lk-cart-count' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'cart_badge_typography', 'selector' => '{{WRAPPER}} .lk-cart-count', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 10 ) ), 'font_weight' => array( 'default' => '600' ) ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Bar
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_bar',
			array(
				'label' => 'Bar',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bar_bg',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,253,251,0.94)',
				'selectors' => array( '{{WRAPPER}} .lk-header' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'bar_max_width',
			array(
				'label'     => 'Max width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'The reference caps the header bar at 1440px and centres it, same as every other section. Set your outer Elementor container to full-width / 0 padding and let this control the actual width.',
				'selectors' => array(
					'{{WRAPPER}} .lk-header' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);
		$this->add_control(
			'bar_border_color',
			array(
				'label'     => 'Bottom border colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(105,33,55,0.16)',
				'selectors' => array( '{{WRAPPER}} .lk-header' => 'border-bottom-color: {{VALUE}};' ),
			)
		);
		$this->add_responsive_control(
			'bar_min_height',
			array(
				'label'     => 'Min height',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 50, 'max' => 140 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 82 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 70 ),
				'selectors' => array( '{{WRAPPER}} .lk-header' => 'min-height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'bar_padding',
			array(
				'label'      => 'Horizontal padding',
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'vw' => array( 'min' => 0, 'max' => 12 ), 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'size_units' => array( 'vw', 'px' ),
				'default'    => array( 'unit' => 'vw', 'size' => 4.5 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 20 ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-header' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Brand
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_brand',
			array(
				'label' => 'Brand',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'brand_color',
			array(
				'label'     => 'Colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-brand' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_responsive_control(
			'mark_size',
			array(
				'label'     => 'Mark box size',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 20, 'max' => 60 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 36 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 32 ),
				'condition' => array( 'logo_type' => 'text' ),
				'selectors' => array(
					'{{WRAPPER}} .lk-brand-mark' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'mark_typography',
				'selector'  => '{{WRAPPER}} .lk-brand-mark',
				'condition' => array( 'logo_type' => 'text' ),
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 18.4 ) ),
					'font_style'  => array( 'default' => 'italic' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'name_typography',
				'selector'  => '{{WRAPPER}} .lk-brand-name',
				'separator' => 'before',
				'condition' => array( 'logo_type' => 'text' ),
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Cormorant Garamond' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 21.6 ) ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.08 ) ),
				),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Navigation (desktop)
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_nav',
			array(
				'label' => 'Navigation (Desktop)',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'nav_color',
			array(
				'label'     => 'Colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#57282D',
				'selectors' => array(
					'{{WRAPPER}} .lk-nav a, {{WRAPPER}} .lk-shop-link, {{WRAPPER}} .lk-nav-dropdown > summary' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'nav_underline_color',
			array(
				'label'     => 'Underline colour (on hover)',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array(
					'{{WRAPPER}} .lk-nav a::after, {{WRAPPER}} .lk-shop-link::after' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_typography',
				'selector' => '{{WRAPPER}} .lk-nav a, {{WRAPPER}} .lk-shop-link, {{WRAPPER}} .lk-nav-dropdown > summary',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 11.2 ) ),
					'font_weight'    => array( 'default' => '500' ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.11 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);
		$this->add_responsive_control(
			'nav_gap',
			array(
				'label'     => 'Gap between links',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 8, 'max' => 60 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 32 ),
				'selectors' => array( '{{WRAPPER}} .lk-nav' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'actions_gap',
			array(
				'label'     => 'Gap in header actions',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 6, 'max' => 40 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 20 ),
				'selectors' => array( '{{WRAPPER}} .lk-header-actions' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Nav Dropdown
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_dropdown',
			array(
				'label' => 'Nav Dropdown',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'dropdown_icon_color',
			array(
				'label'     => 'Toggle icon colour (+ / −)',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF715E',
				'selectors' => array( '{{WRAPPER}} .lk-nav-dropdown summary::after' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'dropdown_bg',
			array(
				'label'     => 'Panel background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFEFD',
				'selectors' => array( '{{WRAPPER}} .lk-nav-dropdown-menu' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'dropdown_border_color',
			array(
				'label'     => 'Panel border & link divider colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(105,33,55,0.16)',
				'selectors' => array(
					'{{WRAPPER}} .lk-nav-dropdown-menu'    => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .lk-nav-dropdown-menu a + a' => 'border-top-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'dropdown_link_color',
			array(
				'label'     => 'Link colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#57282D',
				'selectors' => array( '{{WRAPPER}} .lk-nav-dropdown-menu a' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'dropdown_shadow',
				'selector' => '{{WRAPPER}} .lk-nav-dropdown-menu',
			)
		);
		$this->add_control(
			'dropdown_min_width',
			array(
				'label'     => 'Panel min width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 120, 'max' => 400 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 215 ),
				'selectors' => array( '{{WRAPPER}} .lk-nav-dropdown-menu' => 'min-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Button
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => 'Button',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .lk-header-btn',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 10.9 ) ),
					'font_weight'    => array( 'default' => '600' ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.12 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);
		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => 'Padding',
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array( 'top' => 10, 'right' => 19, 'bottom' => 10, 'left' => 19, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-header-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_button' );
		$this->start_controls_tab( 'tab_button_normal', array( 'label' => 'Normal' ) );
		$this->add_control(
			'button_bg',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-header-btn' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_color',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array( '{{WRAPPER}} .lk-header-btn' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'tab_button_hover', array( 'label' => 'Hover' ) );
		$this->add_control(
			'button_bg_hover',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array( '{{WRAPPER}} .lk-header-btn:hover' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'button_color_hover',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-header-btn:hover' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Mobile Menu
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_mobile',
			array(
				'label' => 'Mobile Menu',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hamburger_color',
			array(
				'label'     => 'Hamburger colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-menu-toggle span' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'mobile_menu_bg',
			array(
				'label'     => 'Menu background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFDFB',
				'separator' => 'before',
				'selectors' => array( '{{WRAPPER}} .lk-mobile-menu' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'mobile_menu_border',
			array(
				'label'     => 'Menu link divider colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(105,33,55,0.16)',
				'selectors' => array( '{{WRAPPER}} .lk-mobile-menu a' => 'border-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'mobile_menu_color',
			array(
				'label'     => 'Menu link colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#57282D',
				'selectors' => array( '{{WRAPPER}} .lk-mobile-menu a:not(.lk-header-btn)' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'mobile_menu_typography',
				'selector' => '{{WRAPPER}} .lk-mobile-menu a:not(.lk-header-btn)',
				'fields_options' => array(
					'font_family' => array( 'default' => 'Cormorant Garamond' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 21.6 ) ),
				),
			)
		);
		$this->add_control(
			'mobile_menu_label_color',
			array(
				'label'       => 'Section label colour (e.g. "Workshops")',
				'type'        => Controls_Manager::COLOR,
				'default'     => '#FF715E',
				'separator'   => 'before',
				'selectors'   => array( '{{WRAPPER}} .lk-mobile-menu-label' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'mobile_menu_shadow',
				'selector' => '{{WRAPPER}} .lk-mobile-menu',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$header_classes = array( 'lk-header' );
		if ( 'yes' === $s['sticky'] ) {
			$header_classes[] = 'lk-header-sticky';
		}

		$breakpoint = ! empty( $s['mobile_breakpoint'] ) ? (int) $s['mobile_breakpoint'] : 1120;

		$this->add_render_attribute( 'brand', 'class', 'lk-brand' );
		$this->add_link_attributes( 'brand', $s['brand_link'] );

		$this->add_render_attribute( 'shop_link', 'class', 'lk-shop-link' );
		$this->add_link_attributes( 'shop_link', $s['shop_link'] );

		$this->add_render_attribute( 'button', 'class', array( 'lk-header-btn', 'lk-header-btn-desktop' ) );
		$this->add_link_attributes( 'button', $s['button_link'] );

		$this->add_render_attribute( 'mobile_button', 'class', array( 'lk-header-btn', 'lk-mobile-button' ) );
		$this->add_link_attributes( 'mobile_button', $s['button_link'] );
		?>
		<style>
			.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-nav,
			.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-header-btn-desktop { display: none; }
			.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-menu-toggle { display: grid; }
			.lk-cart-link { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; text-decoration: none; transition: color .25s ease; }
			.lk-cart-link svg { display: block; }
			.lk-cart-count { position: absolute; top: 3px; right: 0; min-width: 17px; height: 17px; padding: 0 4px; box-sizing: border-box; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; line-height: 1; }
			.lk-cart-hide-zero .lk-cart-count[data-count="0"] { display: none; }
			@media (min-width: <?php echo esc_attr( $breakpoint ); ?>px) {
				.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-nav,
				.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-header-btn-desktop,
				.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-shop-link { display: inline-flex; }
				.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-nav { display: flex; }
				.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-menu-toggle,
				.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-mobile-menu { display: none !important; }
			}
			@media (max-width: <?php echo esc_attr( $breakpoint - 1 ); ?>px) {
				.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-shop-link { display: none; }
				/* With the nav column empty below the breakpoint, force an
				   explicit two-column spread (brand / actions) rather than
				   relying on the now-invisible middle "1fr" track to hold
				   its space — keeps the hamburger pinned to the far right
				   instead of collapsing in next to the logo. */
				.elementor-element-<?php echo esc_attr( $this->get_id() ); ?> .lk-header {
					grid-template-columns: auto auto !important;
					justify-content: space-between !important;
				}
			}
		</style>

		<header class="<?php echo esc_attr( implode( ' ', $header_classes ) ); ?>" data-lk-header>
			<a <?php echo $this->get_render_attribute_string( 'brand' ); ?>>
				<?php if ( 'image' === $s['logo_type'] && ! empty( $s['logo_image']['url'] ) ) : ?>
					<img src="<?php echo esc_url( $s['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php else : ?>
					<span class="lk-brand-mark"><?php echo esc_html( $s['brand_mark_text'] ); ?></span>
					<span class="lk-brand-name"><?php echo esc_html( $s['brand_name_text'] ); ?></span>
				<?php endif; ?>
			</a>

			<nav class="lk-nav" aria-label="Primary navigation">
				<?php foreach ( $s['nav_items'] as $index => $item ) :
					if ( 'dropdown' === $item['type'] ) :
						$sub_links = $this->parse_dropdown_links( $item['dropdown_items'] );
						?>
						<details class="lk-nav-dropdown">
							<summary><?php echo esc_html( $item['label'] ); ?></summary>
							<div class="lk-nav-dropdown-menu">
								<?php foreach ( $sub_links as $sub ) : ?>
									<a href="<?php echo esc_url( $sub['url'] ); ?>"><?php echo esc_html( $sub['label'] ); ?></a>
								<?php endforeach; ?>
							</div>
						</details>
						<?php
					else :
						$key = 'nav_' . $index;
						$this->add_render_attribute( $key, 'href', ! empty( $item['link']['url'] ) ? $item['link']['url'] : '#' );
						if ( ! empty( $item['link']['is_external'] ) ) {
							$this->add_render_attribute( $key, 'target', '_blank' );
						}
						?>
						<a <?php echo $this->get_render_attribute_string( $key ); ?>><?php echo esc_html( $item['label'] ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</nav>

			<div class="lk-header-actions">
				<a <?php echo $this->get_render_attribute_string( 'shop_link' ); ?>><?php echo esc_html( $s['shop_text'] ); ?></a>

				<?php if ( 'yes' === $s['show_cart'] ) :
					$cart_url   = ! empty( $s['cart_url']['url'] ) ? $s['cart_url']['url'] : ( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#' );
					$cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
					?>
					<a class="lk-cart-link<?php echo 'yes' === $s['cart_hide_zero'] ? ' lk-cart-hide-zero' : ''; ?>" href="<?php echo esc_url( $cart_url ); ?>" aria-label="<?php echo esc_attr( $s['cart_label'] ); ?>">
						<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1.2 11.2a1.5 1.5 0 0 1-1.5 1.3H7.7a1.5 1.5 0 0 1-1.5-1.3L5 8z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/></svg>
						<span class="lk-cart-count" data-count="<?php echo esc_attr( $cart_count ); ?>"><?php echo esc_html( $cart_count ); ?></span>
					</a>
				<?php endif; ?>

				<button class="lk-menu-toggle" type="button" aria-expanded="false" aria-controls="lk-mobile-menu-<?php echo esc_attr( $this->get_id() ); ?>" aria-label="Open menu">
					<span></span><span></span>
				</button>

				<a <?php echo $this->get_render_attribute_string( 'button' ); ?>><?php echo esc_html( $s['button_text'] ); ?></a>
			</div>

			<nav class="lk-mobile-menu" id="lk-mobile-menu-<?php echo esc_attr( $this->get_id() ); ?>" aria-label="Mobile navigation" hidden>
				<?php foreach ( $s['nav_items'] as $index => $item ) :
					if ( 'dropdown' === $item['type'] ) :
						$sub_links = $this->parse_dropdown_links( $item['dropdown_items'] );
						?>
						<span class="lk-mobile-menu-label"><?php echo esc_html( $item['label'] ); ?></span>
						<?php foreach ( $sub_links as $sub ) : ?>
							<a href="<?php echo esc_url( $sub['url'] ); ?>"><?php echo esc_html( $sub['label'] ); ?></a>
						<?php endforeach; ?>
						<?php
					else :
						$key = 'mnav_' . $index;
						$this->add_render_attribute( $key, 'href', ! empty( $item['link']['url'] ) ? $item['link']['url'] : '#' );
						?>
						<a <?php echo $this->get_render_attribute_string( $key ); ?>><?php echo esc_html( $item['label'] ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
				<a href="<?php echo esc_url( ! empty( $s['shop_link']['url'] ) ? $s['shop_link']['url'] : '#' ); ?>"><?php echo esc_html( $s['shop_text'] ); ?></a>
				<a <?php echo $this->get_render_attribute_string( 'mobile_button' ); ?>><?php echo esc_html( $s['mobile_button_text'] ); ?></a>
			</nav>
		</header>
		<?php
	}

	/**
	 * Parses the repeater's "Label | URL" textarea (one per line) into
	 * an array of ['label' => ..., 'url' => ...]. Lines with no "|"
	 * are skipped rather than fataling on a typo.
	 *
	 * @param string $raw
	 * @return array
	 */
	private function parse_dropdown_links( $raw ) {
		$links = array();
		$lines = preg_split( '/\r\n|\r|\n/', (string) $raw );
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( '' === $line || false === strpos( $line, '|' ) ) {
				continue;
			}
			list( $label, $url ) = array_map( 'trim', explode( '|', $line, 2 ) );
			if ( '' === $label ) {
				continue;
			}
			$links[] = array(
				'label' => $label,
				'url'   => '' !== $url ? $url : '#',
			);
		}
		return $links;
	}
}
