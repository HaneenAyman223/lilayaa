<?php
/**
 * Lila Kora — Box Inside Widget
 *
 * "Inside the Experience Box" section: eyebrow, heading, paragraph,
 * a numbered inclusions list (repeater), and an image with an
 * italic right-aligned caption.
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-box-inside-widget.php';
 *   $widgets_manager->register( new \LK_Box_Inside_Widget() );
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

class LK_Box_Inside_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-box-inside';
	}

	public function get_title() {
		return 'LK — Box Inside';
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'box', 'inside', 'inclusions', 'experience box' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => 'Inside the Experience Box', 'label_block' => true ) );
		$this->add_control( 'heading_main', array( 'label' => 'Heading — first line', 'type' => Controls_Manager::TEXT, 'default' => 'Everything she needs', 'label_block' => true ) );
		$this->add_control( 'heading_emphasis', array( 'label' => 'Heading — emphasized line', 'type' => Controls_Manager::TEXT, 'default' => 'to begin creating.', 'label_block' => true, 'description' => 'Only the last word ("creating.") renders in italic, matching the reference.' ) );
		$this->add_control( 'description', array( 'label' => 'Paragraph', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => 'The box is thoughtfully prepared so she can open it and begin in her own time. A simple guide takes her through the creative method step by step.', 'label_block' => true ) );

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Inclusions
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_inclusions',
			array( 'label' => 'Inclusions', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$repeater = new Repeater();
		$repeater->add_control( 'number', array( 'label' => 'Number', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$repeater->add_control( 'text', array( 'label' => 'Text', 'type' => Controls_Manager::TEXT, 'default' => 'Item text', 'label_block' => true ) );

		$this->add_control(
			'inclusions',
			array(
				'label' => 'Items', 'type' => Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'number' => '01', 'text' => 'Canvas and pencil for the original design' ),
					array( 'number' => '02', 'text' => 'Paints, brushes and palette' ),
					array( 'number' => '03', 'text' => 'Step-by-step creative guide' ),
					array( 'number' => '04', 'text' => 'Professional artwork digitization and refinement' ),
					array( 'number' => '05', 'text' => 'Your selected premium satin or pure silk scarf' ),
					array( 'number' => '06', 'text' => 'Premium packaging for the finished piece' ),
				),
				'title_field' => '{{{ number }}} — {{{ text }}}',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Image / Video
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_image',
			array( 'label' => 'Image / Video', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control(
			'visual_type',
			array(
				'label'   => 'Visual',
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'image' => array( 'title' => 'Image', 'icon' => 'eicon-image' ),
					'video' => array( 'title' => 'Video', 'icon' => 'eicon-youtube' ),
				),
				'default' => 'video',
			)
		);
		$this->add_control( 'image', array( 'label' => 'Image', 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ), 'condition' => array( 'visual_type' => 'image' ) ) );
		$this->add_group_control( Group_Control_Image_Size::get_type(), array( 'name' => 'image', 'default' => 'large', 'condition' => array( 'visual_type' => 'image' ) ) );
		$this->add_control(
			'video_url',
			array(
				'label'       => 'Video URL',
				'type'        => Controls_Manager::TEXT,
				'default'     => 'https://youtube.com/shorts/uoAI3mGwuRs?si=2DinwajYRCKazWAB',
				'label_block' => true,
				'placeholder' => 'https://www.youtube.com/watch?v=... or a Shorts / youtu.be link',
				'description' => 'Accepts a normal YouTube link, a youtu.be short link, or a YouTube Shorts link — all are converted automatically.',
				'condition'   => array( 'visual_type' => 'video' ),
			)
		);
		$this->add_control(
			'video_shape',
			array(
				'label'       => 'Video shape',
				'type'        => Controls_Manager::SELECT,
				'default'     => 'portrait',
				'options'     => array(
					'fill'      => 'Fill the frame (matches a landscape photo\'s box — will pillarbox a Shorts/portrait video)',
					'portrait'  => 'Portrait (9:16 — Shorts, Reels, TikTok-style clips)',
					'landscape' => 'Landscape (16:9 — a normal horizontal YouTube upload)',
				),
				'description' => 'A Shorts link is portrait video — "Fill the frame" would force it into the same wide box a photo uses, adding black bars on the sides. "Portrait" sizes the box to match instead.',
				'condition'   => array( 'visual_type' => 'video' ),
			)
		);
		$this->add_control(
			'video_poster',
			array(
				'label'       => 'Custom poster image (optional)',
				'type'        => Controls_Manager::MEDIA,
				'condition'   => array( 'visual_type' => 'video' ),
				'description' => 'Shown before the video is played, with a clean play button on top — no YouTube "Shorts" badge, channel name or title cluttering it. Leave empty to auto-use YouTube\'s own thumbnail frame (still clean — just not one you chose).',
			)
		);
		$this->add_control( 'image_caption', array( 'label' => 'Caption', 'type' => Controls_Manager::TEXT, 'default' => 'Open the box. Set an intention. Let your hands lead.', 'label_block' => true, 'condition' => array( 'visual_type' => 'image' ) ) );
		$this->add_control( 'image_position', array( 'label' => 'Image position', 'type' => Controls_Manager::SELECT, 'default' => 'right', 'options' => array( 'right' => 'Right', 'left' => 'Left' ), 'prefix_class' => 'lk-boxinside-img-' ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#FFFEFD', 'selectors' => array( '{{WRAPPER}} .lk-boxinside' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-boxinside' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 120, 'right' => 65, 'bottom' => 120, 'left' => 65, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 90, 'right' => 24, 'bottom' => 90, 'left' => 24, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-boxinside' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control( 'columns_gap', array( 'label' => 'Gap', 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', 'vw' ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 200 ), 'vw' => array( 'min' => 0, 'max' => 15 ) ), 'default' => array( 'unit' => 'vw', 'size' => 8 ), 'selectors' => array( '{{WRAPPER}} .lk-boxinside' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'copy_max_width', array( 'label' => 'Text column max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 300, 'max' => 800 ) ), 'default' => array( 'unit' => 'px', 'size' => 650 ), 'selectors' => array( '{{WRAPPER}} .lk-boxinside-copy' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxinside-heading' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-boxinside-heading',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 56 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 34 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );
		$this->add_control( 'desc_color', array( 'label' => 'Paragraph colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxinside-desc' => 'color: {{VALUE}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Inclusions
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_inclusions',
			array( 'label' => 'Inclusions', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'inclusions_border_color', array( 'label' => 'Divider colour', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.16)', 'selectors' => array( '{{WRAPPER}} .lk-boxinside-inclusions' => 'border-color: {{VALUE}};', '{{WRAPPER}} .lk-boxinside-inclusions > div' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'number_color', array( 'label' => 'Number colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxinside-inclusions span' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'number_typography', 'selector' => '{{WRAPPER}} .lk-boxinside-inclusions span', 'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ) ) ) );
		$this->add_control( 'item_color', array( 'label' => 'Text colour', 'type' => Controls_Manager::COLOR, 'default' => '#281D21', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxinside-inclusions p' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'item_typography', 'selector' => '{{WRAPPER}} .lk-boxinside-inclusions p', 'fields_options' => array( 'font_family' => array( 'default' => 'Montserrat' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 13.4 ) ) ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Image
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_image',
			array( 'label' => 'Image', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_responsive_control( 'image_height', array(
			'label' => 'Height', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 200, 'max' => 900 ) ),
			'default' => array( 'unit' => 'px', 'size' => 690 ), 'tablet_default' => array( 'unit' => 'px', 'size' => 540 ), 'mobile_default' => array( 'unit' => 'px', 'size' => 440 ),
			'description' => 'Matches the reference: 690px desktop → 540px tablet → 440px mobile. Applies to the video too, when that\'s selected.',
			'selectors' => array( '{{WRAPPER}} .lk-boxinside-visual' => 'height: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_control( 'image_radius', array( 'label' => 'Corner radius', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'default' => array( 'unit' => 'px', 'size' => 0 ), 'selectors' => array( '{{WRAPPER}} .lk-boxinside-visual' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'caption_color', array( 'label' => 'Caption colour', 'type' => Controls_Manager::COLOR, 'default' => '#8F8584', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-boxinside-image figcaption' => 'color: {{VALUE}};' ) ) );

		$this->add_control( 'heading_play_button', array( 'label' => 'Play Button', 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => array( 'visual_type' => 'video' ) ) );
		$this->add_control( 'play_button_bg', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => 'rgba(105,33,55,0.88)', 'condition' => array( 'visual_type' => 'video' ), 'selectors' => array( '{{WRAPPER}} .lk-boxinside-play' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'play_button_color', array( 'label' => 'Icon colour', 'type' => Controls_Manager::COLOR, 'default' => '#FFFFFF', 'condition' => array( 'visual_type' => 'video' ), 'selectors' => array( '{{WRAPPER}} .lk-boxinside-play' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'play_button_size', array( 'label' => 'Size', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 40, 'max' => 140 ) ), 'default' => array( 'unit' => 'px', 'size' => 74 ), 'condition' => array( 'visual_type' => 'video' ), 'selectors' => array( '{{WRAPPER}} .lk-boxinside-play' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();
	}

	/**
	 * Extracts a YouTube video ID from a normal watch URL, a youtu.be
	 * short link, or a Shorts link. Returns '' if nothing recognizable
	 * is found (render() then just shows nothing rather than a broken
	 * embed).
	 */
	private function get_youtube_id( $url ) {
		$url = trim( (string) $url );
		if ( '' === $url ) {
			return '';
		}
		if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m ) ) {
			return $m[1];
		}
		return '';
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$is_video  = 'video' === $s['visual_type'];
		$youtube_id = $is_video ? $this->get_youtube_id( $s['video_url'] ) : '';
		$image_html = Group_Control_Image_Size::get_attachment_image_html( $s, 'image', 'image' );
		?>
		<div class="lk-boxinside">
			<div class="lk-boxinside-copy">
				<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
				<h2 class="lk-boxinside-heading"><?php echo esc_html( $s['heading_main'] ); ?><br><em><?php echo esc_html( $s['heading_emphasis'] ); ?></em></h2>
				<?php if ( ! empty( $s['description'] ) ) : ?><p class="lk-boxinside-desc"><?php echo esc_html( $s['description'] ); ?></p><?php endif; ?>

				<?php if ( ! empty( $s['inclusions'] ) ) : ?>
					<div class="lk-boxinside-inclusions">
						<?php foreach ( $s['inclusions'] as $item ) : ?>
							<div><span><?php echo esc_html( $item['number'] ); ?></span><p><?php echo esc_html( $item['text'] ); ?></p></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<figure class="lk-boxinside-image">
				<div class="lk-boxinside-visual<?php echo $is_video ? ' lk-boxinside-shape-' . esc_attr( $s['video_shape'] ) : ''; ?>">
					<?php if ( $is_video && $youtube_id ) :
						$poster_src = ! empty( $s['video_poster']['url'] )
							? $s['video_poster']['url']
							: "https://img.youtube.com/vi/{$youtube_id}/hqdefault.jpg";
						?>
						<div class="lk-boxinside-video" data-lk-youtube-facade data-youtube-id="<?php echo esc_attr( $youtube_id ); ?>">
							<button type="button" class="lk-boxinside-poster" aria-label="Play video">
								<img src="<?php echo esc_url( $poster_src ); ?>" alt="" loading="lazy">
								<span class="lk-boxinside-play" aria-hidden="true">
									<svg viewBox="0 0 24 24" width="38%" height="38%" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
								</span>
							</button>
						</div>
					<?php elseif ( $is_video ) : ?>
						<?php if ( current_user_can( 'edit_posts' ) ) : ?>
							<div class="lk-boxinside-video-notice">No video URL set, or the link wasn't recognized as YouTube — only visible to editors.</div>
						<?php endif; ?>
					<?php else : ?>
						<?php echo $image_html; ?>
					<?php endif; ?>
				</div>
				<?php if ( ! $is_video && ! empty( $s['image_caption'] ) ) : ?><figcaption><?php echo esc_html( $s['image_caption'] ); ?></figcaption><?php endif; ?>
			</figure>
		</div>

		<style>
			.lk-boxinside { display: grid; grid-template-columns: 0.95fr 1.05fr; align-items: center; }
			.lk-boxinside-img-right .lk-boxinside-copy { order: 1; } .lk-boxinside-img-right .lk-boxinside-image { order: 2; }
			.lk-boxinside-img-left .lk-boxinside-copy { order: 2; } .lk-boxinside-img-left .lk-boxinside-image { order: 1; }
			.lk-boxinside-heading { margin: 0 0 20px; font-style: normal; } .lk-boxinside-heading em { font-style: italic; }
			.lk-boxinside-desc { margin: 0; }
			.lk-boxinside-inclusions { margin-top: 34px; border-top: 1px solid; }
			.lk-boxinside-inclusions > div { display: grid; grid-template-columns: 45px 1fr; gap: 12px; padding: 15px 0; border-bottom: 1px solid; }
			.lk-boxinside-inclusions span { font-style: normal; }
			.lk-boxinside-inclusions p { margin: 0; }
			.lk-boxinside-image { margin: 0; }
			.lk-boxinside-visual { position: relative; overflow: hidden; }
			.lk-boxinside-shape-portrait { height: auto !important; max-height: 720px; max-width: 405px; aspect-ratio: 9 / 16; margin: 0 auto; }
			.lk-boxinside-shape-landscape { height: auto !important; aspect-ratio: 16 / 9; }
			.lk-boxinside-visual img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-boxinside-image figcaption { padding-top: 13px; text-align: right; font-style: italic; }
			.lk-boxinside-video { position: absolute; inset: 0; background: #000; }
			.lk-boxinside-video iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; display: block; }
			.lk-boxinside-poster { position: absolute; inset: 0; width: 100%; height: 100%; padding: 0; margin: 0; border: 0; background: transparent; cursor: pointer; -webkit-appearance: none; appearance: none; }
			.lk-boxinside-poster img { width: 100%; height: 100%; object-fit: cover; display: block; }
			.lk-boxinside-play { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 6px 24px rgba(0,0,0,0.35); transition: transform .25s ease; }
			.lk-boxinside-poster:hover .lk-boxinside-play { transform: translate(-50%, -50%) scale(1.08); }
			.lk-boxinside-video-notice { padding: 24px; background: rgba(255,113,94,0.12); border: 1px dashed #FF715E; font-family: 'Montserrat', Arial, sans-serif; font-size: 13px; color: #57282D; }
			@media (max-width: 820px) {
				.lk-boxinside { grid-template-columns: 1fr !important; }
				.lk-boxinside-img-right .lk-boxinside-copy, .lk-boxinside-img-left .lk-boxinside-copy { order: 2; }
				.lk-boxinside-img-right .lk-boxinside-image, .lk-boxinside-img-left .lk-boxinside-image { order: 1; }
			}
		</style>

		<script>
			( function () {
				if ( window.__lkYoutubeFacadeBound ) { return; }
				window.__lkYoutubeFacadeBound = true;
				document.addEventListener( 'click', function ( event ) {
					var button = event.target.closest( '[data-lk-youtube-facade] .lk-boxinside-poster' );
					if ( ! button ) { return; }
					var wrap = button.closest( '[data-lk-youtube-facade]' );
					var id = wrap.getAttribute( 'data-youtube-id' );
					var iframe = document.createElement( 'iframe' );
					iframe.src = 'https://www.youtube.com/embed/' + id + '?autoplay=1&playsinline=1';
					iframe.setAttribute( 'title', 'Video' );
					iframe.setAttribute( 'allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share' );
					iframe.setAttribute( 'allowfullscreen', '' );
					iframe.style.position = 'absolute';
					iframe.style.inset = '0';
					iframe.style.width = '100%';
					iframe.style.height = '100%';
					iframe.style.border = '0';
					wrap.innerHTML = '';
					wrap.appendChild( iframe );
				} );
			} )();
		</script>
		<?php
	}
}
