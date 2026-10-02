<?php
/**
 * Lila Kora — Site Footer Widget
 *
 * Brand + tagline row, three link columns (repeaters), and a bottom
 * bar with copyright + a closing tagline.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-footer-widget.php';
 *   $widgets_manager->register( new \LK_Footer_Widget() );
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

class LK_Footer_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-footer';
	}

	public function get_title() {
		return 'LK — Site Footer';
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'footer' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Brand
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_brand',
			array( 'label' => 'Brand', 'tab' => Controls_Manager::TAB_CONTENT )
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
		$this->add_control( 'brand_mark_text', array( 'label' => 'Mark (initials)', 'type' => Controls_Manager::TEXT, 'default' => 'LK', 'condition' => array( 'logo_type' => 'text' ) ) );
		$this->add_control( 'brand_name_text', array( 'label' => 'Brand name', 'type' => Controls_Manager::TEXT, 'default' => 'LILA KORA', 'label_block' => true, 'condition' => array( 'logo_type' => 'text' ) ) );
		$this->add_control( 'logo_image', array( 'label' => 'Logo image', 'type' => Controls_Manager::MEDIA, 'condition' => array( 'logo_type' => 'image' ) ) );
		$this->add_responsive_control(
			'logo_image_width',
			array(
				'label'     => 'Image width',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 40, 'max' => 400 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 140 ),
				'condition' => array( 'logo_type' => 'image' ),
				'selectors' => array( '{{WRAPPER}} .lk-footer-brand-logo img' => 'width: {{SIZE}}{{UNIT}}; height: auto;' ),
			)
		);
		$this->add_control( 'brand_link', array( 'label' => 'Logo links to', 'type' => Controls_Manager::URL, 'default' => array( 'url' => home_url( '/' ) ) ) );
		$this->add_control( 'tagline', array( 'label' => 'Tagline', 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'Art and fashion brought together through creative experiences and scarves made to hold personal meaning.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Link Columns
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_columns',
			array( 'label' => 'Link Columns', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		foreach ( array( 1, 2, 3 ) as $n ) {
			$this->add_control( "col{$n}_title", array( 'label' => "Column {$n} — title", 'type' => Controls_Manager::TEXT, 'default' => 1 === $n ? 'Explore' : ( 2 === $n ? 'Experiences' : 'Connect' ), 'label_block' => true ) );

			$repeater = new Repeater();
			$repeater->add_control( 'label', array( 'label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Link', 'label_block' => true ) );
			$repeater->add_control( 'link', array( 'label' => 'URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true, 'label_block' => true ) );
			$repeater->add_control( 'is_text_only', array( 'label' => 'Plain text (not a link)', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'default' => '' ) );

			$defaults = array(
				1 => array(
					array( 'label' => 'Public Workshops', 'link' => array( 'url' => './public-workshops.html' ) ),
					array( 'label' => 'Private Workshops', 'link' => array( 'url' => './private-workshops.html' ) ),
					array( 'label' => 'Experience Box', 'link' => array( 'url' => './experience-box.html' ) ),
					array( 'label' => 'Our Story', 'link' => array( 'url' => './our-story.html' ) ),
				),
				2 => array(
					array( 'label' => 'Private Events', 'link' => array( 'url' => './private-workshops.html' ) ),
					array( 'label' => 'Brand Activations', 'link' => array( 'url' => '#enquire' ) ),
					array( 'label' => 'Corporate & Teams', 'link' => array( 'url' => '#enquire' ) ),
					array( 'label' => 'Corporate Gifting', 'link' => array( 'url' => '#enquire' ) ),
				),
				3 => array(
					array( 'label' => 'Email', 'link' => array( 'url' => 'mailto:hello@example.com' ) ),
					array( 'label' => 'WhatsApp', 'link' => array( 'url' => 'https://wa.me/', 'is_external' => true ) ),
					array( 'label' => 'Instagram', 'link' => array( 'url' => 'https://instagram.com/', 'is_external' => true ) ),
					array( 'label' => 'Dubai, UAE', 'link' => array( 'url' => '#' ), 'is_text_only' => 'yes' ),
				),
			);

			$this->add_control(
				"col{$n}_links",
				array(
					'label' => "Column {$n} — links", 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
					'default' => $defaults[ $n ], 'title_field' => '{{{ label }}}',
				)
			);
		}

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Bottom Bar
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_bottom',
			array( 'label' => 'Bottom Bar', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'copyright', array( 'label' => 'Copyright text', 'type' => Controls_Manager::TEXT, 'default' => '© 2026 Lila Kora. All rights reserved.', 'label_block' => true ) );
		$this->add_control( 'closing_tagline', array( 'label' => 'Closing tagline', 'type' => Controls_Manager::TEXT, 'default' => 'Wear your story.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#FFFEFD', 'selectors' => array( '{{WRAPPER}} .lk-footer' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the footer at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-footer' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 75, 'right' => 65, 'bottom' => 28, 'left' => 65, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 60, 'right' => 24, 'bottom' => 28, 'left' => 24, 'unit' => 'px' ),
				'description' => 'Reference: 75px 4.5vw 28px.',
				'selectors' => array( '{{WRAPPER}} .lk-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'divider_color', array( 'label' => 'Divider colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.16)', 'selectors' => array( '{{WRAPPER}} .lk-footer-brand' => 'border-color: {{VALUE}};', '{{WRAPPER}} .lk-footer-bottom' => 'border-color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Brand
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_brand',
			array( 'label' => 'Brand', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'brand_color', array( 'label' => 'Logo colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-footer-brand-logo' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'tagline_color', array( 'label' => 'Tagline colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-footer-brand > p' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'tagline_typography', 'selector' => '{{WRAPPER}} .lk-footer-brand > p', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 24 ) ) ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Link Columns
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_columns',
			array( 'label' => 'Link Columns', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'col_title_color', array( 'label' => 'Column title colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-footer-links h3' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'col_title_typography', 'selector' => '{{WRAPPER}} .lk-footer-links h3', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 10.6 ) ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.12 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ) ) );
		$this->add_control( 'col_link_color', array( 'label' => 'Link colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-footer-links a, {{WRAPPER}} .lk-footer-links span' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'col_link_color_hover', array( 'label' => 'Link colour on hover', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'selectors' => array( '{{WRAPPER}} .lk-footer-links a:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'col_link_typography', 'selector' => '{{WRAPPER}} .lk-footer-links a, {{WRAPPER}} .lk-footer-links span', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 12.8 ) ) ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Bottom Bar
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_bottom',
			array( 'label' => 'Bottom Bar', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bottom_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'selectors' => array( '{{WRAPPER}} .lk-footer-bottom' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'bottom_typography', 'selector' => '{{WRAPPER}} .lk-footer-bottom', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 10.4 ) ), 'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.08 ) ), 'text_transform' => array( 'default' => 'uppercase' ) ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$this->add_render_attribute( 'brand', 'class', 'lk-footer-brand-logo' );
		$this->add_link_attributes( 'brand', $s['brand_link'] );
		?>
		<footer class="lk-footer">
			<div class="lk-footer-brand">
				<a <?php echo $this->get_render_attribute_string( 'brand' ); ?>>
					<?php if ( 'image' === $s['logo_type'] && ! empty( $s['logo_image']['url'] ) ) : ?>
						<img src="<?php echo esc_url( $s['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php else : ?>
						<span class="lk-footer-mark"><?php echo esc_html( $s['brand_mark_text'] ); ?></span>
						<span class="lk-footer-name"><?php echo esc_html( $s['brand_name_text'] ); ?></span>
					<?php endif; ?>
				</a>
				<?php if ( ! empty( $s['tagline'] ) ) : ?><p><?php echo esc_html( $s['tagline'] ); ?></p><?php endif; ?>
			</div>

			<div class="lk-footer-links">
				<?php foreach ( array( 1, 2, 3 ) as $n ) : ?>
					<div>
						<h3><?php echo esc_html( $s[ "col{$n}_title" ] ); ?></h3>
						<?php foreach ( $s[ "col{$n}_links" ] as $index => $item ) :
							if ( 'yes' === $item['is_text_only'] ) : ?>
								<span><?php echo esc_html( $item['label'] ); ?></span>
							<?php else :
								$key = "col{$n}_link_{$index}";
								$this->add_render_attribute( $key, 'href', ! empty( $item['link']['url'] ) ? $item['link']['url'] : '#' );
								if ( ! empty( $item['link']['is_external'] ) ) { $this->add_render_attribute( $key, 'target', '_blank' ); $this->add_render_attribute( $key, 'rel', 'noopener' ); }
								?>
								<a <?php echo $this->get_render_attribute_string( $key ); ?>><?php echo esc_html( $item['label'] ); ?></a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="lk-footer-bottom">
				<span><?php echo esc_html( $s['copyright'] ); ?></span>
				<span><?php echo esc_html( $s['closing_tagline'] ); ?></span>
			</div>
		</footer>

		<style>
			.lk-footer-brand { display: grid; grid-template-columns: 0.7fr 1.3fr; align-items: end; gap: 40px; padding-bottom: 55px; border-bottom: 1px solid; }
			.lk-footer-brand-logo { display: inline-flex; align-items: center; gap: 12px; text-decoration: none; }
			.lk-footer-brand-logo img { display: block; }
			.lk-footer-mark { display: grid; place-items: center; width: 36px; height: 36px; border: 1px solid currentColor; font-family: 'Cormorant Garamond', serif; font-size: 18.4px; font-style: italic; flex-shrink: 0; }
			.lk-footer-name { font-family: 'Cormorant Garamond', serif; font-size: 21.6px; letter-spacing: 0.08em; }
			.lk-footer-brand > p { max-width: 590px; margin: 0; font-style: italic; line-height: 1.4; }
			.lk-footer-links { display: grid; grid-template-columns: repeat(3, 1fr); gap: 45px; padding: 55px 0; }
			.lk-footer-links h3 { margin: 0 0 18px; }
			.lk-footer-links a, .lk-footer-links span { display: block; width: fit-content; margin: 7px 0; text-decoration: none; transition: color .25s ease; }
			.lk-footer-bottom { display: flex; justify-content: space-between; padding-top: 22px; border-top: 1px solid; }
			@media (max-width: 767px) {
				.lk-footer-brand { grid-template-columns: 1fr; align-items: start; }
			}
			@media (max-width: 540px) {
				.lk-footer-links { grid-template-columns: 1fr 1fr; }
				.lk-footer-links > div:last-child { grid-column: 1 / -1; }
				.lk-footer-bottom { flex-direction: column; gap: 8px; }
			}
		</style>
		<?php
	}
}
