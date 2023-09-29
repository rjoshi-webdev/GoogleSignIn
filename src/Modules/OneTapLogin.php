<?php
/**
 * One Tap SignIn Class.
 *
 * This class will be responsible for handling
 * Google's one tapsigninfor web functioning.
 *
 * @package RjoshiWebdev\GoogleSignIn\Modules
 * @since 1.0.16
 */

declare(strict_types=1);

namespace RjoshiWebdev\GoogleSignIn\Modules;

use Exception;
use RjoshiWebdev\GoogleSignIn\Utils\Authenticator;
use RjoshiWebdev\GoogleSignIn\Utils\GoogleClient;
use RjoshiWebdev\GoogleSignIn\Utils\Helper;
use RjoshiWebdev\GoogleSignIn\Interfaces\Module;
use RjoshiWebdev\GoogleSignIn\Utils\TokenVerifier;
use function RjoshiWebdev\GoogleSignIn\plugin;

/**
 * Class OneTapLogin
 *
 * @package RjoshiWebdev\GoogleSignIn\Modules
 */
class OneTapLogin implements Module {
	/**
	 * Settings Module.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Token verifier.
	 *
	 * @var TokenVerifier
	 */
	private $token_verifier;

	/**
	 * Google client instance.
	 *
	 * @var GoogleClient
	 */
	private $google_client;

	/**
	 * Authenticator service.
	 *
	 * @var Authenticator
	 */
	private $authenticator;

	/**
	 * OneTapLogin constructor.
	 *
	 * @param Settings      $settings Settings object.
	 * @param TokenVerifier $verifier Token verifier object.
	 * @param GoogleClient  $client   Google client instance.
	 * @param Authenticator $authenticator Authenticator service instance.
	 */
	public function __construct( Settings $settings, TokenVerifier $verifier, GoogleClient $client, Authenticator $authenticator ) {
		$this->settings       = $settings;
		$this->token_verifier = $verifier;
		$this->google_client  = $client;
		$this->authenticator  = $authenticator;
	}

	/**
	 * Module name.
	 *
	 * @return string
	 */
	public function name(): string {
		return 'one_tap_login';
	}

	/**
	 * Module Initialization activity.
	 *
	 * Everything will happen if and only if one tap is active in settings.
	 */
	public function init(): void {
		if ( $this->settings->one_tap_login ) {
			add_action( 'login_enqueue_scripts', [ $this, 'one_tap_scripts' ] );
			add_action( 'login_footer', [ $this, 'one_tap_prompt' ] );
			add_action( 'wp_ajax_nopriv_validate_id_token', [ $this, 'validate_token' ] );
			add_action( 'RjoshiWebdev.id_token_verified', [ $this, 'authenticate' ] );
			add_action(
				'init',
				function () {
					if ( ! is_user_logged_in() ) {
						$hook_prefix = ( 'sitewide' === $this->settings->one_tap_login_screen ) ? 'wp' : 'login';
						add_action( $hook_prefix . '_enqueue_scripts', [ $this, 'one_tap_scripts' ] );
						add_action( $hook_prefix . '_footer', [ $this, 'one_tap_prompt' ], 10000 );
					}
				}
			);
		}
	}

	/**
	 * Show one tap prompt markup.
	 *
	 * @return void
	 */
	public function one_tap_prompt(): void { ?>
		<div id="g_id_onload" data-client_id="<?php echo esc_attr( $this->settings->client_id ); ?>" data-login_uri="<?php echo esc_attr( wp_login_url() ); ?>" data-callback="LoginWithGoogleDataCallBack"></div>
		<?php
	}

	/**
	 * Enqueue one-tap related scripts.
	 *
	 * @return void
	 */
	public function one_tap_scripts(): void {
		$filename = ( defined( 'WP_SCRIPT_DEBUG' ) && true === WP_SCRIPT_DEBUG ) ? 'onetap.min.js' : 'onetap.js';

		wp_enqueue_script(
			'rj-google-signin-one-tap',
			'https://accounts.google.com/gsi/client',
			[],
			filemtime( trailingslashit( plugin()->path ) . 'assets/build/js/onetap.js' ),
			true
		);

		$data = [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'state'   => $this->google_client->state(),
			'homeurl' => get_option( 'home', '' ),
		];

		wp_register_script(
			'rj-google-signin-one-tap-js',
			trailingslashit( plugin()->url ) . 'assets/build/js/' . $filename,
			[
				'wp-i18n',
			],
			filemtime( trailingslashit( plugin()->path ) . 'assets/build/js/onetap.js' ),
			true
		);

		wp_add_inline_script(
			'rj-google-signin-one-tap-js',
			'var TempAccessOneTap=' . json_encode( $data ), //phpcs:disable WordPress.WP.AlternativeFunctions.json_encode_json_encode
			'before'
		);

		wp_enqueue_script( 'rj-google-signin-one-tap-js' );
	}

	/**
	 * Validate the ID token.
	 *
	 * @return void
	 * @throws Exception Credential verification failure exception.
	 */
	public function validate_token(): void {
		try {
			$token    = Helper::filter_input( INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$verified = $this->token_verifier->verify_token( $token );

			if ( ! $verified ) {
				throw new Exception( __( 'Cannot verify the credentials', 'rj-google-signin' ) );
			}

			/**
			 * Do something when token has been verified successfully.
			 *
			 * If we are here that means ID token has been verified.
			 *
			 * @since 1.0.16
			 */
			do_action( 'RjoshiWebdev.id_token_verified' );

			$redirect_to   = apply_filters( 'RjoshiWebdev.google_default_redirect', admin_url() );
			$state         = Helper::filter_input( INPUT_POST, 'state', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$decoded_state = $state ? (array) ( json_decode( base64_decode( $state ) ) ) : null;    // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

			if ( is_array( $decoded_state ) && ! empty( $decoded_state['provider'] ) && 'google' === $decoded_state['provider'] ) {
				$redirect_to = $decoded_state['redirect_to'] ?? $redirect_to;
			}

			wp_send_json_success(
				[
					'redirect' => $redirect_to,
				]
			);
			die;

		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	/**
	 * Authenticate the user in WordPress.
	 *
	 * @return void
	 * @throws Exception Authentication exception.
	 */
	public function authenticate(): void {
		$user = $this->token_verifier->current_user();

		if ( is_null( $user ) ) {
			throw new Exception( __( 'User not found to authenticate', 'rj-google-signin' ) );
		}

		$wp_user = $this->authenticator->authenticate( $user );
		$this->authenticator->set_auth_cookies( $wp_user );
	}
}
