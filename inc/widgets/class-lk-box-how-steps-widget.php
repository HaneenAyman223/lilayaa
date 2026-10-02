<?php
/**
 * Lila Kora — Box How Steps Widget
 *
 * The "From canvas to silk scarf in four simple steps" section on the
 * Experience Box product page. Distinct from LK — How It Works (the
 * homepage's dark burgundy version) — this one sits on a plain ivory
 * background with a different heading split and a lighter 4-image
 * grid.
 *
 * Per client feedback (from her video walkthrough), the step images
 * default to plain SQUARE corners — the reference's original
 * `border-radius: 999px 999px 0 0` (rounded/arched top) has been
 * removed, not just zeroed out, so there's no leftover arch shape.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-box-how-steps-widget.php';
 *   $widgets_manager->register( new \LK_Box_How_Steps_Widget() );
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

class LK_Box_How_Steps_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-box-how-steps';
	}

	public function get_title() {
		return 'LK — Box How Steps';
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'box', 'steps', 'how it works', 'experience box' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'How It Works', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'From canvas to silk scarf', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — emphasized line', 'type' => Controls_Manager::TEXT, 'default' => 'in four simple steps.', 'label_block' => true, 'description' => 'Only the last word ("steps.") renders in italic, matching the reference.' ) );
		$this->add_control( 'description', array( 'label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'No art experience is needed. Create freely at home, send us your artwork and approve the refined design before it becomes your scarf.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Steps
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_steps',
			array( 'label' => 'Steps', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$repeater->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$repeater->add_control( 'title', array( 'label' => 'Title', 'type' => Controls_Manager::TEXT, 'default' => 'Step title', 'label_block' => true ) );
		$repeater->add_control( 'description', array( 'label' => 'Description', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Step description.', 'label_block' => true ) );
		$repeater->add_control( 'link_text', array( 'label' => 'Link text (optional)', 'type' => Controls_Manager::TEXT, 'label_block' => true, 'description' => 'Renders as a plain underlined text link below the description — not a button.' ) );
		$repeater->add_control( 'link_url', array( 'label' => 'Link URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'show_external' => true, 'label_block' => true, 'condition' => array( 'link_text!' => '' ) ) );

		$this->add_control(
			'steps',
			array(
				'label' => 'Steps', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'number' => '01', 'title' => 'Receive Your Experience Box', 'description' => 'Your box arrives with the materials and guidance you need to begin.', 'link_text' => 'See what\'s inside', 'link_url' => array( 'url' => '#inside' ) ),
					array( 'number' => '02', 'title' => 'Create Your Design', 'description' => 'Follow your intuition and create an original artwork through line, shape and colour.', 'link_text' => 'See designs of other women', 'link_url' => array( 'url' => '#gallery' ) ),
					array( 'number' => '03', 'title' => 'Send Us a Photo', 'description' => 'Within five working days, we digitize and refine your artwork, then share it with you for approval.', 'link_text' => 'See transformation: Canvas → Digital Artwork → Scarf', 'link_url' => array( 'url' => '#transformation' ) ),
					array( 'number' => '04', 'title' => 'Receive Your Scarf', 'description' => 'Once approved, your one-of-a-kind scarf is crafted and delivered within 20 working days.' ),
				),
				'title_field' => '{{{ number }}} — {{{ title }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#FFFEFD', 'selectors' => array( '{{WRAPPER}} .lk-boxhow' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-boxhow' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-boxhow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'heading_gap', array(
			'label' => 'Gap in heading row', 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', 'vw' ),
			'range' => array( 'px' => array( 'min' => 0, 'max' => 200 ), 'vw' => array( 'min' => 0, 'max' => 15 ) ), 'default' => array( 'unit' => 'vw', 'size' => 8 ),
			'selectors' => array( '{{WRAPPER}} .lk-boxhow-heading' => 'gap: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'heading_spacing', array( 'label' => 'Spacing below heading row', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 120 ) ), 'default' => array( 'unit' => 'px', 'size' => 65 ), 'selectors' => array( '{{WRAPPER}} .lk-boxhow-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'steps_gap', array( 'label' => 'Gap between steps', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 60 ) ), 'default' => array( 'unit' => 'px', 'size' => 26 ), 'selectors' => array( '{{WRAPPER}} .lk-boxhow-grid' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhow-heading h2' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_emphasis_color', array( 'label' => 'Emphasized word colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'selectors' => array( '{{WRAPPER}} .lk-boxhow-heading h2 em' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-boxhow-heading h2',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 48 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 32 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );

		$this->add_control( 'description_color', array( 'label' => 'Description colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhow-heading p.lk-boxhow-desc' => 'color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Steps
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_steps',
			array( 'label' => 'Steps', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control(
			'step_image_radius',
			array(
				'label'       => 'Image corner radius',
				'type'        => Controls_Manager::SLIDER,
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 999 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 0 ),
				'description' => 'Client-requested change: default is now square (0). The reference\'s original arched top (999px 999px 0 0) is available again here if ever wanted back.',
				'selectors'   => array( '{{WRAPPER}} .lk-boxhow-step img' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'step_number_color', array( 'label' => 'Number colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhow-step-number' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'step_number_typography', 'selector' => '{{WRAPPER}} .lk-boxhow-step-number', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 16.8 ) ) ) ) );

		$this->add_control( 'step_title_color', array( 'label' => 'Title colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhow-step h3' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'step_title_typography', 'selector' => '{{WRAPPER}} .lk-boxhow-step h3',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 33 ) ), 'font_weight' => array( 'default' => '500' ), 'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.1 ) ) ),
		) );

		$this->add_control( 'step_desc_color', array( 'label' => 'Description colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhow-step p' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'step_desc_typography', 'selector' => '{{WRAPPER}} .lk-boxhow-step p', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 13.8 ) ) ) ) );
		$this->add_control( 'step_link_color', array( 'label' => 'Link colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxhow-link' => 'color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'step_link_typography', 'selector' => '{{WRAPPER}} .lk-boxhow-link', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 12.5 ) ) ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="lk-boxhow">
			<div class="lk-boxhow-heading">
				<div>
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
					<h2><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h2>
				</div>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-boxhow-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>
			</div>

			<div class="lk-boxhow-grid">
				<?php foreach ( $s['steps'] as $index => $step ) :
					$image_html = Group_Control_Image_Size::get_attachment_image_html( $step, 'image' );
					?>
					<article class="lk-boxhow-step">
						<?php echo $image_html; ?>
						<span class="lk-boxhow-step-number"><?php echo esc_html( $step['number'] ); ?></span>
						<h3><?php echo esc_html( $step['title'] ); ?></h3>
						<p><?php echo esc_html( $step['description'] ); ?></p>
						<?php if ( ! empty( $step['link_text'] ) ) :
							$key = 'step_link_' . $index;
							$this->add_render_attribute( $key, 'class', 'lk-boxhow-link' );
							$this->add_link_attributes( $key, $step['link_url'] );
							?>
							<a <?php echo $this->get_render_attribute_string( $key ); ?>><?php echo esc_html( $step['link_text'] ); ?></a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<style>
			.lk-boxhow-heading { display: grid; grid-template-columns: 1.1fr 0.9fr; align-items: end; }
			.lk-boxhow-heading h2 { margin: 0; font-style: normal; } .lk-boxhow-heading h2 em { font-style: italic; }
			.lk-boxhow-heading p.lk-boxhow-desc { max-width: 570px; margin: 0 0 7px; }
			.lk-boxhow-grid { display: grid; grid-template-columns: repeat(4, 1fr); }
			.lk-boxhow-step img { width: 100%; aspect-ratio: 0.82; object-fit: cover; display: block; }
			.lk-boxhow-step-number { display: block; margin: 22px 0 7px; font-style: normal; }
			.lk-boxhow-step h3 { min-height: 58px; margin: 0 0 14px; }
			.lk-boxhow-step p { margin: 0; }
			.lk-boxhow-link { display: inline-block; margin-top: 12px; text-decoration: none; border-bottom: 1px solid currentColor; padding-bottom: 1px; }
			@media (max-width: 820px) {
				.lk-boxhow-heading { grid-template-columns: 1fr; align-items: start; gap: 30px !important; }
				.lk-boxhow-grid { grid-template-columns: 1fr 1fr; row-gap: 50px; }
			}
			@media (max-width: 540px) {
				.lk-boxhow-grid { grid-template-columns: 1fr; }
				.lk-boxhow-step h3 { min-height: auto; }
			}
		</style>
		<?php
	}
}
