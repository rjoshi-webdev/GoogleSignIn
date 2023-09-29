/**
 * JS for SignIn and Register page.
 *
 * @package rj-google-signin
 */

const wpGoogleSignIn = {

	/**
	 * Init method.
	 *
	 * @return void
	 */
	init() {
		document.addEventListener( 'DOMContentLoaded', this.onContentLoaded );
	},

	/**
	 * Callback function when content is load.
	 * To render the google signin button at after signin form.
	 *
	 * Set cookie if "Rj Google SignIn" button displayed to bypass page cache
	 * Do not set on wp signin or registration page.
	 *
	 * @return void
	 */
	onContentLoaded() {

		// Form either can be signin or register form.
		this.form = document.getElementById( 'loginform' ) || document.getElementById( 'registerform' );

		// Set cookie if "Rj Google SignIn" button displayed to bypass page cache
		// Do not set on wp signin or registration page.
		if ( document.querySelector( '.wp_google_signin' ) && null === this.form ) {
			document.cookie = 'vip-go-cb=1;wp-rj-google-signin=1;path=' + encodeURI(window.location.pathname) + ';';
		}

		if ( null === this.form ) {
			return;
		}

		this.GoogleSignInButton = this.form.querySelector( '.wp_google_signin' );
		this.GoogleSignInButton.classList.remove( 'hidden' );
		// HTML is cloned from existing HTML node.
		this.form.append( this.GoogleSignInButton );
	}

};

wpGoogleSignIn.init();
