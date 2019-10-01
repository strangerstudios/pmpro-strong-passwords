=== PMPro Require Strong Passwords ===
Contributors: strangerstudios, scottsousa
Tags: password,security,strong password
Donate link: https://www.paidmembershipspro.com
Requires at least: 4.7
Tested up to: 5.2.3
Requires PHP: 5.6
Stable tag: 0.2.2
License: GPL 2.0
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

Force users to submit strong passwords on checkout.

== Description ==
Require members to use strong passwords on their initial checkout. This makes use of the default WordPress password strength calculation (the same functionality when changing your default WordPress password.)

Improve security on your WordPress membership site.

== Installation ==
1. Upload the ‘pmpro-strong-passwords’ directory to the ‘/wp-content/plugins/’ directory of your site.
2. Activate the plugin through the ‘Plugins’ menu in WordPress.

== Frequently Asked Questions ==
= I need help with this plugin =
Please post support topics to [https://www.paidmembershipspro.com](https://www.paidmembershipspro.com)

== Changelog ==
= 0.2.2 =
* Bug Fix: Remove warning for logged-in users. Skip logic for checking password.

= 0.2.1 =
* Bug Fix/Enhancement: Added in a hint under Confirm Password.
* Enhancement: Add PHP check for password strength that was removed in 0.2.
* Enhancement: Improved Javascript password checker. 

= 0.2 =
* Bug Fix/Enhancement: Adjust priority of \'pmpro_checkout_after_password\' filter to avoid conflicts with other Add Ons hooking in on this.
* Enhancement: Using WordPress built-in password strength meter.
* Enhancement: Additional filters added, please see https://www.paidmembershipspro.com/add-ons/require-strong-passwords/ for available filters.
* Enhancement: Implement Internationalization.
* Enhancement: Translation for German and German (Formal) - Thanks to 00travelgirl00
* Deprecated: Temporarily commented out functionality from initial release. To restore custom password checks, please add "add_filter( 'pmpro_checkout_after_password', 'pmprosp_pmpro_checkout_after_password', 1 );" to your PMPro Customizations Plugin.

= 0.1 =
* Initial commit

== Upgrade Notice ==
= 0.2.2 =
Please upgrade to version 0.2.2 for minor bug fixes.

= 0.2 =
Please upgrade to receive nifty new features and improved functionality with the default WordPress password strength calculator.