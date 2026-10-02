<?php
/**
 * Lila Kora — FAQ Widget
 *
 * "Questions, answered." — a two-column split: heading + contact
 * link on the left, an accordion of questions on the right. Ships
 * its own toggle script, event-delegated on document so it survives
 * Elementor re-rendering the widget in the editor and works even
 * with multiple FAQ widgets on one page.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-faq-widget.php';
 *   $widgets_manager->register( new \LK_FAQ_Widget() );
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

class LK_FAQ_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-faq';
	}

	public function get_title() {
		return 'LK — FAQ';
	}

	public function get_icon() {
		return 'eicon-help-o';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'faq', 'questions', 'accordion' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Before you begin', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Questions,', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — emphasized line', 'type' => Controls_Manager::TEXT, 'default' => 'answered.', 'label_block' => true ) );
		$this->add_control( 'subtext', array( 'label' => 'Small line above email', 'type' => Controls_Manager::TEXT, 'default' => 'Still wondering about something?', 'label_block' => true ) );
		$this->add_control( 'email', array( 'label' => 'Contact email', 'type' => Controls_Manager::TEXT, 'default' => 'hello@example.com', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Questions
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_items',
			array( 'label' => 'Questions', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'question', array( 'label' => 'Question', 'type' => Controls_Manager::TEXT, 'default' => 'Question text', 'label_block' => true ) );
		$repeater->add_control( 'answer', array( 'label' => 'Answer', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'Answer text.', 'label_block' => true ) );

		$this->add_control(
			'items',
			array(
				'label' => 'Items', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'question' => 'Can I buy a scarf without creating the artwork?', 'answer' => 'Yes. Our ready-to-wear collection features artist-designed pure silk scarves that can be purchased as finished pieces.' ),
					array( 'question' => 'Do I need to be good at art?', 'answer' => 'Not at all. The experience is designed for beginners, and you are guided through every stage from the first line to the final colour.' ),
					array( 'question' => 'Is the scarf included in the workshop ticket?', 'answer' => 'For public workshops, the ticket covers the guided session, art materials and listed refreshments. Scarf transformation is available separately unless the event page clearly states that it is included.' ),
					array( 'question' => 'What happens after I finish my artwork?', 'answer' => 'We digitize your artwork and share the prepared design for approval within five working days. Once approved, your scarf is crafted and delivered within 20 working days.' ),
					array( 'question' => 'Can you host a private or company event?', 'answer' => 'Yes. We host private gatherings, community experiences, brand activations and corporate events, subject to date, venue and group requirements.' ),
					array( 'question' => 'Which scarf materials are available?', 'answer' => 'Selected experiences offer premium satin or pure silk in different sizes. The exact material, size and inclusions are always confirmed before you book or approve a proposal.' ),
				),
				'title_field' => '{{{ question }}}',
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

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#FAF6F7', 'selectors' => array( '{{WRAPPER}} .lk-faq' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-faq' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'tablet_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-faq' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'columns_gap',
			array(
				'label' => 'Gap between columns', 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', 'vw' ),
				'range' => array( 'px' => array( 'min' => 0, 'max' => 200 ), 'vw' => array( 'min' => 0, 'max' => 15 ) ),
				'default' => array( 'unit' => 'vw', 'size' => 9 ),
				'selectors' => array( '{{WRAPPER}} .lk-faq' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Heading Column
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_heading',
			array( 'label' => 'Heading Column', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-faq-heading h2' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-faq-heading h2',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 56 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 34 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );
		$this->add_control( 'subtext_color', array( 'label' => 'Small line colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-faq-heading p.lk-faq-subtext' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'email_color', array( 'label' => 'Email link colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-faq-heading a' => 'color: {{VALUE}}; border-color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Accordion
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_accordion',
			array( 'label' => 'Accordion', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'accordion_border_color', array( 'label' => 'Divider colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.16)', 'selectors' => array( '{{WRAPPER}} .lk-accordion' => 'border-color: {{VALUE}};', '{{WRAPPER}} .lk-faq-item' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'question_color', array( 'label' => 'Question colour', 'type' => Controls_Manager::COLOR, 'default' => '#281D21', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-faq-item button' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'question_typography', 'selector' => '{{WRAPPER}} .lk-faq-item button span:first-child', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 24 ) ) ) ) );
		$this->add_control( 'plus_color', array( 'label' => '"+" icon colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-faq-plus' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'answer_color', array( 'label' => 'Answer colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-faq-answer' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'answer_typography', 'selector' => '{{WRAPPER}} .lk-faq-answer', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 14.1 ) ) ) ) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="lk-faq">
			<div class="lk-faq-heading">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h2>
				<?php if ( ! empty( $s['subtext'] ) ) : ?><p class="lk-faq-subtext"><?php echo esc_html( $s['subtext'] ); ?></p><?php endif; ?>
				<?php if ( ! empty( $s['email'] ) ) : ?><a href="mailto:<?php echo esc_attr( $s['email'] ); ?>"><?php echo esc_html( $s['email'] ); ?></a><?php endif; ?>
			</div>

			<div class="lk-accordion">
				<?php foreach ( $s['items'] as $index => $item ) : ?>
					<article class="lk-faq-item">
						<button type="button" aria-expanded="false" aria-controls="lk-faq-answer-<?php echo esc_attr( $this->get_id() . '-' . $index ); ?>">
							<span><?php echo esc_html( $item['question'] ); ?></span>
							<span class="lk-faq-plus">+</span>
						</button>
						<div class="lk-faq-answer" id="lk-faq-answer-<?php echo esc_attr( $this->get_id() . '-' . $index ); ?>" hidden>
							<p><?php echo esc_html( $item['answer'] ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>

		<style>
			.lk-faq { display: grid; grid-template-columns: 0.7fr 1.3fr; }
			.lk-faq-heading h2 { margin: 0 0 28px; font-style: normal; } .lk-faq-heading h2 em { font-style: italic; }
			.lk-faq-heading p.lk-faq-subtext { margin: 0 0 3px; }
			.lk-faq-heading a { text-decoration: none; border-bottom: 1px solid currentColor; font-size: 13.4px; }
			.lk-accordion { border-top: 1px solid; }
			.lk-faq-item { border-bottom: 1px solid; }
			.lk-faq-item button { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 24px 0; background: transparent; border: 0; cursor: pointer; text-align: left; }
			.lk-faq-plus { width: 28px; font-size: 24px; font-weight: 300; font-style: normal; transition: transform .3s ease; flex-shrink: 0; }
			.lk-faq-item button[aria-expanded="true"] .lk-faq-plus { transform: rotate(45deg); }
			.lk-faq-answer { padding: 0 46px 23px 0; }
			.lk-faq-answer p { margin: 0; }
			.lk-faq-answer[hidden] { display: none; }
			@media (max-width: 820px) {
				.lk-faq { grid-template-columns: 1fr; gap: 58px !important; }
			}
		</style>

		<script>
			( function () {
				if ( window.__lkFaqBound ) { return; }
				window.__lkFaqBound = true;
				document.addEventListener( 'click', function ( event ) {
					var button = event.target.closest( '.lk-faq-item button' );
					if ( ! button ) { return; }
					var expanded = button.getAttribute( 'aria-expanded' ) === 'true';
					var answer = button.nextElementSibling;
					button.setAttribute( 'aria-expanded', String( ! expanded ) );
					if ( answer ) { answer.hidden = expanded; }
				} );
			} )();
		</script>
		<?php
	}
}
