<?php
/**
 * Lila Kora — Organizations Widget
 *
 * A simple, reusable "Organizations we worked with" section: an optional
 * eyebrow and heading above a row of partner/organization logos. Logos are
 * grayscale and slightly faded by default, and switch to full color on
 * hover — the common convention for this kind of trust strip. Each logo can
 * optionally link out to that organization's site.
 *
 * Not a marquee/auto-scroll carousel — this is a static row that wraps onto
 * further rows if there are more logos than fit one line, which suits a
 * short, fixed list of named organizations better than a moving strip.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-organizations-widget.php';
 *   $widgets_manager->register( new \LK_Organizations_Widget() );
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

class LK_Organizations_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-organizations';
	}

	public function get_title() {
		return 'LK — Organizations';
	}

	public function get_icon() {
		return 'eicon-logo-wall';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'organizations', 'logos', 'partners', 'worked with', 'clients' );
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

		$this->add_control( 'show_heading', array( 'label' => 'Show heading', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Trusted by', 'label_block' => true, 'condition' => array( 'show_heading' => 'yes' ) ) );
		$this->add_control( 'heading_text', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Organizations we worked with', 'label_block' => true, 'condition' => array( 'show_heading' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Logos
		 * =======================================================*/
		$this->start_controls_section( 'section_content_logos', array( 'label' => 'Logos', 'tab' => Controls_Manager::TAB_CONTENT ) );

		$logo = new Repeater();
		$logo->add_control( 'image', array( 'label' => 'Logo', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$logo->add_control( 'name', array( 'label' => 'Organization name (for alt text)', 'type' => Controls_Manager::TEXT, 'default' => 'Organization', 'label_block' => true ) );
		$logo->add_control( 'link', array( 'label' => 'Link (optional)', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '' ), 'show_external' => true ) );

		$this->add_control(
			'logos',
			array(
				'label'       => 'Logos',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $logo->get_controls(),
				'default'     => array(
					array( 'name' => 'Organization 1' ),
					array( 'name' => 'Organization 2' ),
					array( 'name' => 'Organization 3' ),
					array( 'name' => 'Organization 4' ),
				),
				'title_field' => '{{{ name }}}',
			)
		);
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'logos', 'default' => 'medium' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section( 'section_style_layout', array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->color( 'bg_color', 'Background', '.lk-orgs', '#FFFEFD', 'background-color' );
		$this->add_responsive_control( 'section_max_width', array( 'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ), 'description' => 'Set your outer Elementor container to full width with 0 padding.', 'selectors' => array( '{{WRAPPER}} .lk-orgs' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ) ) );
		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => 'Section padding',
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px' ),
				'default'        => array( 'top' => 80, 'right' => 65, 'bottom' => 80, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 60, 'right' => 32, 'bottom' => 60, 'left' => 32, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 50, 'right' => 24, 'bottom' => 50, 'left' => 24, 'unit' => 'px' ),
				'selectors'      => array( '{{WRAPPER}} .lk-orgs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'heading_spacing', array( 'label' => 'Space below heading', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 100 ) ), 'default' => array( 'unit' => 'px', 'size' => 45 ), 'condition' => array( 'show_heading' => 'yes' ), 'selectors' => array( '{{WRAPPER}} .lk-orgs-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'align', array( 'label' => 'Alignment', 'type' => Controls_Manager::CHOOSE, 'options' => array( 'left' => array( 'title' => 'Left', 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => 'Centre', 'icon' => 'eicon-text-align-center' ) ), 'default' => 'center', 'selectors_dictionary' => array( 'left' => 'flex-start', 'center' => 'center' ), 'selectors' => array( '{{WRAPPER}} .lk-orgs' => 'text-align: {{VALUE}};', '{{WRAPPER}} .lk-orgs-logos' => 'justify-content: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading
		 * =======================================================*/
		$this->start_controls_section( 'section_style_heading', array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_heading' => 'yes' ) ) );

		$this->color( 'eyebrow_color', 'Eyebrow colour', '.lk-orgs-eyebrow', '#FF715E' );
		$this->typo( 'eyebrow_typography', 'Eyebrow typography', '.lk-orgs-eyebrow', 'Montserrat', 10.6, '600', 0.19, true, 1.5 );
		$this->color( 'heading_color', 'Heading colour', '.lk-orgs-heading h2', '#57282D', 'color', true );
		$this->typo( 'heading_typography', 'Heading typography', '.lk-orgs-heading h2', 'Cormorant Garamond', 34, '400', null, false, 1.15, 28, 24 );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Logos
		 * =======================================================*/
		$this->start_controls_section( 'section_style_logos', array( 'label' => 'Logos', 'tab' => Controls_Manager::TAB_STYLE ) );

		$this->add_responsive_control( 'logo_height', array( 'label' => 'Logo height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 20, 'max' => 120 ) ), 'default' => array( 'unit' => 'px', 'size' => 46 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 34 ), 'selectors' => array( '{{WRAPPER}} .lk-orgs-logos img' => 'height: {{SIZE}}{{UNIT}}; width: auto;' ) ) );
		$this->add_responsive_control( 'logo_gap', array( 'label' => 'Gap between logos', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 160 ) ), 'default' => array( 'unit' => 'px', 'size' => 70 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 45 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 32 ), 'selectors' => array( '{{WRAPPER}} .lk-orgs-logos' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control(
			'logo_treatment',
			array(
				'label'   => 'Default look',
				'type'    => Controls_Manager::SELECT,
				'default' => 'grayscale',
				'options' => array( 'grayscale' => 'Grayscale, faded (colour on hover)', 'plain' => 'Full colour always' ),
			)
		);
		$this->add_control( 'logo_opacity', array( 'label' => 'Faded opacity', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0.2, 'max' => 1, 'step' => 0.05 ) ), 'default' => array( 'size' => 0.55 ), 'condition' => array( 'logo_treatment' => 'grayscale' ), 'selectors' => array( '{{WRAPPER}} .lk-orgs-logos img' => 'opacity: {{SIZE}};' ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$logos = (array) $s['logos'];
		?>
		<div class="lk-orgs<?php echo 'grayscale' === $s['logo_treatment'] ? ' lk-orgs-grayscale' : ''; ?>">
			<?php if ( 'yes' === $s['show_heading'] && ( ! empty( $s['eyebrow'] ) || ! empty( $s['heading_text'] ) ) ) : ?>
				<div class="lk-orgs-heading">
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-orgs-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $s['heading_text'] ) ) : ?><h2><?php echo esc_html( $s['heading_text'] ); ?></h2><?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $logos ) : ?>
				<div class="lk-orgs-logos">
					<?php foreach ( $logos as $logo ) :
						if ( empty( $logo['image']['url'] ) ) {
							continue;
						}
						$html     = Group_Control_Image_Size::get_attachment_image_html( array_merge( $logo, array( 'logos_size' => $s['logos_size'], 'logos_custom_dimension' => $s['logos_custom_dimension'] ) ), 'logos', 'image' );
						$has_link = ! empty( $logo['link']['url'] );
						?>
						<?php if ( $has_link ) : ?>
							<a class="lk-orgs-logo" href="<?php echo esc_url( $logo['link']['url'] ); ?>"<?php echo ! empty( $logo['link']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo $html; ?></a>
						<?php else : ?>
							<span class="lk-orgs-logo"><?php echo $html; ?></span>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<style>
			.lk-orgs { box-sizing: border-box; }
			.lk-orgs-heading h2 { margin: 0; font-style: normal; }
			.lk-orgs-logos { display: flex; flex-wrap: wrap; align-items: center; }
			.lk-orgs-logo { display: inline-flex; align-items: center; line-height: 0; }
			.lk-orgs-logo img { display: block; width: auto; object-fit: contain; filter: none; transition: opacity .3s ease, filter .3s ease; }
			.lk-orgs-grayscale .lk-orgs-logos img { filter: grayscale(100%); }
			.lk-orgs-grayscale .lk-orgs-logo:hover img { opacity: 1 !important; filter: grayscale(0%); }
		</style>
		<?php
	}
}
