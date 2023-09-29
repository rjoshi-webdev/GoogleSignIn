=== Sign in with Google ===
Tags: Google sign in, sso, oauth, authentication, sign-in, single sign-on, log in
Requires at least: 5.5
Tested up to: 6.3.1
Requires PHP: 7.4
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Minimal plugin that allows WordPress users to log in using Google.

== Description ==

Plugin to let your users signin to WordPress applications using their Google accounts.

### Initial Setup

1. Create a project from [Google Developers Console](https://console.developers.google.com/apis/dashboard) if none exists.


2. Go to **Credentials** tab, then create credential for OAuth client.
    * Application type will be **Web Application**
    * Add `YOUR_DOMAIN/wp-login.php` in **Authorized redirect URIs**


3. This will give you **Client ID** and **Secret key**.


4. Input these values either in `WP Admin > Settings > WP Google SignIn`, or in `wp-config.php` using the following code snippet:

```
define( 'WP_GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID' );
define( 'WP_GOOGLE_SECRET', 'YOUR_SECRET_KEY' );
```

### Browser support
[These browsers are supported](https://developers.google.com/identity/gsi/web/guides/supported-browsers). Note, for example, that One Tap SignIn is not supported in Safari.

### How to enable automatic user registration

You can enable user registration either by
- Enabling *Settings > WP Google SignIn > Enable Google SignIn Registration*


OR


- Adding
```
define( 'WP_GOOGLE_USER_REGISTRATION', 'true' );
```
in wp-config.php file.

**Note:** If the checkbox is ON then, it will register valid Google users even when WordPress default setting is OFF.

### Restrict user registration to one or more domain(s)

By default, when you enable user registration via constant `WP_GOOGLE_USER_REGISTRATION` or enable *Settings > WP Google SignIn > Enable Google SignIn Registration*, it will create a user for any Google signin (including gmail.com users). If you are planning to use this plugin on a private, internal site, then you may like to restrict user registration to users under a single Google Suite organization. This configuration variable does that.

Add your domain name, without any schema prefix and `www,` as the value of `WP_google_signin_WHITELIST_DOMAINS` constant or in the settings `Settings > WP Google SignIn > Whitelisted Domains`. You can whitelist multiple domains. Please separate domains with commas. See the below example to know how to do it via constants:
```
define( 'WP_GOOGLE_WHITELIST_DOMAINS', 'example.com,sample.com' );
```

**Note:** If a user already exists, they **will be allowed to Rj Google SignIn** regardless of whether their domain is whitelisted or not. Whitelisting will only prevent users from **registering** with email addresses from non-whitelisted domains.

#### wp-config.php parameters list

* `WP_GOOGLE_CLIENT_ID` (string): Google client ID of your application.


* `WP_GOOGLE_SECRET` (string): Secret key of your application


* `WP_GOOGLE_USER_REGISTRATION` (boolean) (optional): Set `true` If you want to enable new user registration. By default, user registration defers to `Settings > General Settings > Membership` if constant is not set.


* `WP_GOOGLE_WHITELIST_DOMAINS` (string) (optional): Domain names, if you want to restrict signin with your custom domain. By default, it will allow all domains. You can whitelist multiple domains.

== Screenshots ==

1. SignIn screen with Google option added.
2. Plugin settings screen.
3. Settings within Google Developer Console.

== Changelog ==

= 1.1.1 =
* Compatible with PHP 8.1
* Compatible with WordPress latest version 6.3.1
