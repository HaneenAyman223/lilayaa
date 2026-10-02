<?php
/**
 * Lila Kora — Instagram Feed Widget
 *
 * Pulls real posts from the Instagram API (graph.instagram.com — the
 * current "Instagram API with Instagram Login" path, which needs a
 * Business or Creator account but NOT a linked Facebook Page).
 *
 * ─────────────────────────────────────────────────────────────────
 * ONE-TIME SETUP (do this before the widget will show anything):
 *
 * 1. Convert the Instagram account to a Business or Creator account
 *    (Instagram app → Settings → Account type).
 * 2. Go to developers.facebook.com → create an app → add the
 *    "Instagram" product (Instagram API with Instagram Login).
 * 3. Under that product, generate a token for the account — this
 *    walks you through Instagram login and gives you a
 *    SHORT-LIVED access token.
 * 4. Exchange it for a LONG-LIVED token (valid 60 days) by visiting:
 *    https://graph.instagram.com/access_token?grant_type=ig_exchange_token&client_secret=YOUR_APP_SECRET&access_token=YOUR_SHORT_LIVED_TOKEN
 * 5. Note the "user_id" returned alongside your token — that is
 *    your Instagram User ID.
 * 6. Paste BOTH into this widget's Content → Instagram Account tab
 *    once, then save the page. The widget bootstraps them into a WP
 *    option on first render and self-refreshes the token every ~50
 *    days via WP-Cron from then on — you should not need to touch
 *    it again unless the token is fully revoked.
 *
 * If nothing appears, check the note rendered in its place (visible
 * to logged-in editors) for the specific API error.
 * ─────────────────────────────────────────────────────────────────
 *
 * Self-contained single file. To wire it in, add to
 * class-lk-widgets-loader.php's register_widgets():
 *
 *   require_once __DIR__ . '/widgets/class-lk-instagram-feed-widget.php';
 *   $widgets_manager->register( new \LK_Instagram_Feed_Widget() );
 *
 * @package Astra_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Cron callback: refresh the long-lived token before it expires.
 * Registered unconditionally on every load (WP needs the hook
 * defined to know what to run when the scheduled event fires) — the
 * event itself is only scheduled once, from the widget's render().
 */
add_action( 'lk_ig_refresh_token_event', function () {
	$token = get_option( 'lk_instagram_access_token' );
	if ( empty( $token ) ) {
		return;
	}
	$url      = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . rawurlencode( $token );
	$response = wp_remote_get( $url, array( 'timeout' => 15 ) );
	if ( is_wp_error( $response ) ) {
		return;
	}
	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! empty( $body['access_token'] ) ) {
		update_option( 'lk_instagram_access_token', $body['access_token'], false );
		update_option( 'lk_instagram_token_refreshed_at', time(), false );
	}
} );

class LK_Instagram_Feed_Widget extends Widget_Base {

	public function get_name() {
		return 'lk-instagram-feed';
	}

	public function get_title() {
		return 'LK — Instagram Feed';
	}

	public function get_icon() {
		return 'eicon-instagram-icon';
	}

	public function get_categories() {
		return array( 'lila-kora' );
	}

	public function get_keywords() {
		return array( 'lila kora', 'instagram', 'social', 'feed' );
	}

	protected function register_controls() {

		/* =========================================================
		 * CONTENT — Instagram Account
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_account',
			array( 'label' => 'Instagram Account', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$has_stored_token = (bool) get_option( 'lk_instagram_access_token' );

		$this->add_control(
			'setup_note',
			array(
				'type'        => Controls_Manager::HEADING,
				'label'       => $has_stored_token ? 'Connected ✓' : 'Not yet connected',
				'description' => $has_stored_token
					? 'A token is already stored and auto-refreshing. You only need to fill in the fields below again if the connection is ever fully revoked.'
					: 'See the setup steps in this file\'s top comment. Paste your long-lived token and Instagram User ID below once — they\'re saved to a WP option and the token refreshes itself automatically after that.',
			)
		);
		$this->add_control(
			'access_token',
			array(
				'label'       => 'Long-lived access token',
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => 'Only needed once, to bootstrap the stored token. Leave blank after the first save — the widget then uses (and refreshes) the stored one automatically.',
			)
		);
		$this->add_control(
			'ig_user_id',
			array(
				'label'       => 'Instagram User ID',
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);
		$this->add_control(
			'post_count',
			array(
				'label'   => 'Number of posts',
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 2,
				'max'     => 20,
			)
		);
		$this->add_control(
			'cache_minutes',
			array(
				'label'       => 'Cache duration (minutes)',
				'type'        => Controls_Manager::NUMBER,
				'default'     => 60,
				'description' => 'How long fetched posts are cached before re-checking the API. Keep this reasonably high — Instagram rate-limits frequent requests.',
			)
		);

		$this->end_controls_section();

		/* =========================================================
		 * CONTENT — Heading
		 * =======================================================*/
		$this->start_controls_section(
			'section_content_heading',
			array( 'label' => 'Heading', 'tab' => Controls_Manager::TAB_CONTENT )
		);

		$this->add_control( 'eyebrow', array( 'label' => 'Eyebrow', 'type' => Controls_Manager::TEXT, 'default' => '@lila.kora_', 'label_block' => true ) );
		$this->add_control( 'heading', array( 'label' => 'Heading', 'type' => Controls_Manager::TEXT, 'default' => 'Follow our world', 'label_block' => true ) );
		$this->add_control( 'show_link', array( 'label' => 'Show "Follow" link', 'type' => Controls_Manager::SWITCHER, 'label_on' => 'Show', 'label_off' => 'Hide', 'default' => 'yes' ) );
		$this->add_control( 'link_text', array( 'label' => 'Link text', 'type' => Controls_Manager::TEXT, 'default' => 'Follow on Instagram', 'label_block' => true, 'condition' => array( 'show_link' => 'yes' ) ) );
		$this->add_control( 'link_url', array( 'label' => 'Link URL', 'type' => Controls_Manager::URL, 'default' => array( 'url' => 'https://instagram.com/lila.kora_', 'is_external' => true ), 'condition' => array( 'show_link' => 'yes' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Layout
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_layout',
			array( 'label' => 'Layout', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'bg_color', array( 'label' => 'Background', 'type' => Controls_Manager::COLOR, 'default' => '#F5EBEF', 'selectors' => array( '{{WRAPPER}} .lk-igfeed' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'section_max_width',
			array(
				'label' => 'Max width', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 900, 'max' => 1920 ) ), 'default' => array( 'unit' => 'px', 'size' => 1440 ),
				'description' => 'Caps and centres the section at 1440px. Set your outer Elementor container to full-width / 0 padding.',
				'selectors' => array( '{{WRAPPER}} .lk-igfeed' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ),
			)
		);
		$this->add_responsive_control(
			'section_padding',
			array(
				'label' => 'Padding', 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'vw' ),
				'default' => array( 'top' => 90, 'right' => 0, 'bottom' => 90, 'left' => 0, 'unit' => 'px' ),
				'mobile_default' => array( 'top' => 60, 'right' => 0, 'bottom' => 60, 'left' => 0, 'unit' => 'px' ),
				'selectors' => array( '{{WRAPPER}} .lk-igfeed' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'heading_padding_x', array( 'label' => 'Heading row side padding', 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', 'vw' ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 200 ), 'vw' => array( 'min' => 0, 'max' => 15 ) ), 'default' => array( 'unit' => 'vw', 'size' => 4.5 ), 'selectors' => array( '{{WRAPPER}} .lk-igfeed-heading' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control(
			'columns',
			array(
				'label' => 'Columns', 'type' => Controls_Manager::SELECT, 'default' => '6', 'tablet_default' => '4', 'mobile_default' => '3',
				'options' => array( '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
				'selectors' => array( '{{WRAPPER}} .lk-igfeed-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);' ),
			)
		);
		$this->add_control( 'grid_gap', array( 'label' => 'Gap', 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 20 ) ), 'default' => array( 'unit' => 'px', 'size' => 4 ), 'selectors' => array( '{{WRAPPER}} .lk-igfeed-grid' => 'gap: {{SIZE}}{{UNIT}};' ) ) );

		$this->end_controls_section();

		/* =========================================================
		 * STYLE — Text
		 * =======================================================*/
		$this->start_controls_section(
			'section_style_text',
			array( 'label' => 'Text', 'tab' => Controls_Manager::TAB_STYLE )
		);

		$this->add_control( 'eyebrow_color', array( 'label' => 'Eyebrow colour', 'type' => Controls_Manager::COLOR, 'default' => '#FF715E', 'selectors' => array( '{{WRAPPER}} .lk-eyebrow' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'heading_color', array( 'label' => 'Heading colour', 'type' => Controls_Manager::COLOR, 'default' => '#57282D', 'separator' => 'before', 'selectors' => array( '{{WRAPPER}} .lk-igfeed-heading h2' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'heading_typography', 'selector' => '{{WRAPPER}} .lk-igfeed-heading h2',
			'fields_options' => array( 'font_family' => array( 'default' => 'Cormorant Garamond' ), 'font_size' => array( 'default' => array( 'unit' => 'px', 'size' => 30 ) ), 'font_weight' => array( 'default' => '400' ) ),
		) );
		$this->add_control( 'link_color', array( 'label' => 'Follow-link colour', 'type' => Controls_Manager::COLOR, 'default' => '#692137', 'separator' => 'before', 'condition' => array( 'show_link' => 'yes' ), 'selectors' => array( '{{WRAPPER}} .lk-igfeed-link' => 'color: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'link_typography', 'selector' => '{{WRAPPER}} .lk-igfeed-link',
			'condition' => array( 'show_link' => 'yes' ),
			'fields_options' => array(
				'font_family' => array( 'default' => 'Montserrat' ),
				'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 12.8 ) ),
			),
		) );

		$this->end_controls_section();
	}

	/**
	 * Resolves the token/user-id, preferring wp-config.php constants
	 * (recommended — keeps the token out of post content entirely)
	 * over the widget's own stored option.
	 */
	private function get_credentials( $settings ) {
		// Bootstrap: if a token was pasted into the widget and none is
		// stored yet, save it once so future loads (and the refresh
		// cron) don't depend on the widget's own settings at all.
		if ( ! empty( $settings['access_token'] ) && ! get_option( 'lk_instagram_access_token' ) ) {
			update_option( 'lk_instagram_access_token', sanitize_text_field( $settings['access_token'] ), false );
			update_option( 'lk_instagram_token_refreshed_at', time(), false );
		}
		if ( ! empty( $settings['ig_user_id'] ) && ! get_option( 'lk_instagram_user_id' ) ) {
			update_option( 'lk_instagram_user_id', sanitize_text_field( $settings['ig_user_id'] ), false );
		}

		$token   = defined( 'LK_IG_ACCESS_TOKEN' ) ? LK_IG_ACCESS_TOKEN : get_option( 'lk_instagram_access_token' );
		$user_id = defined( 'LK_IG_USER_ID' ) ? LK_IG_USER_ID : get_option( 'lk_instagram_user_id' );

		// Make sure the refresh cron is scheduled (idempotent — WP
		// won't double-schedule if it's already there).
		if ( $token && ! wp_next_scheduled( 'lk_ig_refresh_token_event' ) ) {
			wp_schedule_event( time() + DAY_IN_SECONDS, 'daily', 'lk_ig_refresh_token_event' );
		}

		return array( 'token' => $token, 'user_id' => $user_id );
	}

	/**
	 * Fetches posts, cached in a transient. Returns
	 * ['posts' => array] on success or ['error' => string] on failure
	 * — never throws, so a bad API response can't break the page.
	 */
	private function fetch_posts( $token, $user_id, $count, $cache_minutes ) {
		if ( empty( $token ) || empty( $user_id ) ) {
			return array( 'error' => 'No access token / user ID configured yet — see the setup note in this widget\'s Content tab.' );
		}

		$cache_key = 'lk_ig_posts_' . md5( $user_id . '_' . $count );
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return array( 'posts' => $cached );
		}

		$url = add_query_arg(
			array(
				'fields'       => 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp',
				'access_token' => $token,
				'limit'        => (int) $count,
			),
			'https://graph.instagram.com/' . rawurlencode( $user_id ) . '/media'
		);

		$response = wp_remote_get( $url, array( 'timeout' => 12 ) );
		if ( is_wp_error( $response ) ) {
			return array( 'error' => $response->get_error_message() );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! empty( $body['error'] ) ) {
			return array( 'error' => isset( $body['error']['message'] ) ? $body['error']['message'] : 'Unknown Instagram API error.' );
		}
		if ( empty( $body['data'] ) ) {
			return array( 'error' => 'No posts returned by the API.' );
		}

		set_transient( $cache_key, $body['data'], max( 5, (int) $cache_minutes ) * MINUTE_IN_SECONDS );

		return array( 'posts' => $body['data'] );
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$creds  = $this->get_credentials( $s );
		$result = $this->fetch_posts( $creds['token'], $creds['user_id'], $s['post_count'], $s['cache_minutes'] );

		if ( 'yes' === $s['show_link'] ) {
			$this->add_render_attribute( 'link', 'class', 'lk-igfeed-link' );
			$this->add_link_attributes( 'link', $s['link_url'] );
		}
		?>
		<div class="lk-igfeed">
			<div class="lk-igfeed-heading">
				<div>
					<?php if ( ! empty( $s['eyebrow'] ) ) : ?><p class="lk-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p><?php endif; ?>
					<h2><?php echo esc_html( $s['heading'] ); ?></h2>
				</div>
				<?php if ( 'yes' === $s['show_link'] ) : ?>
					<a <?php echo $this->get_render_attribute_string( 'link' ); ?>><?php echo esc_html( $s['link_text'] ); ?></a>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $result['error'] ) ) : ?>
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<p class="lk-igfeed-notice">Instagram feed not showing: <?php echo esc_html( $result['error'] ); ?> (only visible to editors — see the setup steps in this widget's Content tab.)</p>
				<?php endif; ?>
			<?php else : ?>
				<div class="lk-igfeed-grid">
					<?php foreach ( $result['posts'] as $post ) :
						$is_video = isset( $post['media_type'] ) && 'VIDEO' === $post['media_type'];
						$image    = $is_video && ! empty( $post['thumbnail_url'] ) ? $post['thumbnail_url'] : $post['media_url'];
						$caption  = ! empty( $post['caption'] ) ? wp_trim_words( $post['caption'], 12 ) : '';
						?>
						<a class="lk-igfeed-item" href="<?php echo esc_url( $post['permalink'] ); ?>" target="_blank" rel="noopener" title="<?php echo esc_attr( $caption ); ?>">
							<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $caption ? $caption : 'Instagram post' ); ?>" loading="lazy">
							<?php if ( $is_video ) : ?><span class="lk-igfeed-video-icon">▶</span><?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<style>
			.lk-igfeed-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; flex-wrap: wrap; margin-bottom: 26px; }
			.lk-igfeed-heading h2 { margin: 0.2em 0 0; font-style: normal; }
			.lk-igfeed-link { text-decoration: none; border-bottom: 1px solid currentColor; padding-bottom: 2px; white-space: nowrap; }
			.lk-igfeed-notice { margin: 0 4.5vw; padding: 14px 18px; background: rgba(255,113,94,0.12); border: 1px dashed #FF715E; font-family: 'Montserrat', Arial, sans-serif; font-size: 13px; color: #57282D; }
			.lk-igfeed-grid { display: grid; }
			.lk-igfeed-item { position: relative; display: block; aspect-ratio: 1; overflow: hidden; }
			.lk-igfeed-item img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .5s ease; }
			.lk-igfeed-item:hover img { transform: scale(1.06); }
			.lk-igfeed-video-icon { position: absolute; top: 10px; right: 10px; color: #FFFFFF; font-size: 14px; text-shadow: 0 1px 4px rgba(0,0,0,0.5); }
		</style>
		<?php
	}
}
