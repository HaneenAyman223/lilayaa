<?php
/**
 * Lila Kora — Announcement Bar Widget
 *
 * The thin burgundy strip above the header: a centred message plus
 * one optional link. On the reference site the message text hides
 * on very small screens and only the link remains, centred.
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class LK_Announcement_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-announcement';
	}

	public function get_title() {
		return 'LK — Announcement Bar';
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'announcement', 'bar', 'topbar' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			array(
				'label' => 'Content',
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'message',
			array(
				'label'       => 'Message',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Ready-to-wear silk scarves and creative experiences',
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_link',
			array(
				'label'     => 'Show link',
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => 'Show',
				'label_off' => 'Hide',
				'default'   => 'yes',
			)
		);
		$this->add_control(
			'link_text',
			array(
				'label'       => 'Link — text',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Discover Lila Kora',
				'label_block' => true,
				'condition'   => array( 'show_link' => 'yes' ),
			)
		);
		$this->add_control(
			'link_url',
			array(
				'label'         => 'Link — url',
				'type'          => Controls_Manager::URL,
				'default'       => array( 'url' => '#collection' ),
				'show_external' => true,
				'condition'     => array( 'show_link' => 'yes' ),
			)
		);

		$this->add_control(
			'hide_message_on_mobile',
			array(
				'label'        => 'Hide message text on small screens',
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Hide',
				'label_off'    => 'Keep',
				'default'      => 'yes',
				'description'  => 'Matches the reference: below 540px only the link stays, centred.',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * STYLE
		 * =======================================================*/
		$this->start_controls_section(
			'section_style',
			array(
				'label' => 'Style',
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'     => 'Background',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#692137',
				'selectors' => array( '{{WRAPPER}} .lk-announcement' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'text_color',
			array(
				'label'     => 'Text colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFC5BA',
				'selectors' => array( '{{WRAPPER}} .lk-announcement' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'link_color',
			array(
				'label'       => 'Link colour',
				'type'        => Controls_Manager::COLOR,
				'default'     => '#FFC5BA',
				'condition'   => array( 'show_link' => 'yes' ),
				'description' => 'Matches the message text colour by default, as in the reference — change this if you want the link to stand out on its own.',
				'selectors'   => array( '{{WRAPPER}} .lk-announcement a' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'link_underline_color',
			array(
				'label'     => 'Link underline colour',
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,197,186,0.55)',
				'condition' => array( 'show_link' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .lk-announcement a' => 'border-color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .lk-announcement, {{WRAPPER}} .lk-announcement a',
				'fields_options' => array(
					'font_family'    => array( 'default' => 'Montserrat' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 10.9 ) ),
					'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.12 ) ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'     => 'Gap between message & link',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 22 ),
				'selectors' => array( '{{WRAPPER}} .lk-announcement' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'min_height',
			array(
				'label'     => 'Min height',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 20, 'max' => 80 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 36 ),
				'selectors' => array( '{{WRAPPER}} .lk-announcement' => 'min-height: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => 'Horizontal padding',
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'vw' => array( 'min' => 0, 'max' => 12 ) ),
				'size_units' => array( 'vw', 'px' ),
				'default'    => array( 'unit' => 'vw', 'size' => 5 ),
				'selectors'  => array(
					'{{WRAPPER}} .lk-announcement' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$wrapper_classes = array( 'lk-announcement' );
		if ( 'yes' === $s['hide_message_on_mobile'] ) {
			$wrapper_classes[] = 'lk-announcement-hide-msg-mobile';
		}

		if ( 'yes' === $s['show_link'] && ! empty( $s['link_text'] ) ) {
			$this->add_render_attribute( 'link', 'href', ! empty( $s['link_url']['url'] ) ? $s['link_url']['url'] : '#' );
			if ( ! empty( $s['link_url']['is_external'] ) ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}
			if ( ! empty( $s['link_url']['nofollow'] ) ) {
				$this->add_render_attribute( 'link', 'rel', 'nofollow' );
			}
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
			<?php if ( ! empty( $s['message'] ) ) : ?>
				<span><?php echo esc_html( $s['message'] ); ?></span>
			<?php endif; ?>
			<?php if ( 'yes' === $s['show_link'] && ! empty( $s['link_text'] ) ) : ?>
				<a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
					<?php echo esc_html( $s['link_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
