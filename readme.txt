=== Paid Memberships Pro - Require Strong Passwords ===
Contributors: strangerstudios, scottsousa
Tags: password, security, strong password
Requires at least: 5.4
Tested up to: 6.6
Requires PHP: 7.2
Stable tag: 0.5.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Force users to submit strong passwords on checkout.

== Description ==

Require members to use strong passwords on their initial checkout. This makes use of the default WordPress password strength calculation (the same functionality when changing your default WordPress password).

== Installation ==
1. Upload the ‘pmpro-strong-passwords’ directory to the ‘/wp-content/plugins/’ directory of your site.
2. Activate the plugin through the ‘Plugins’ menu in WordPress.

== Frequently Asked Questions ==
= I need help with this plugin =
Please post support topics to [https://www.paidmembershipspro.com](https://www.paidmembershipspro.com)

== Changelog ==

= 0.5.1 - 2024-09-11 =
* ENHANCEMENT: Now localizing the suggestions returned from the Zxcvbn library. #56 (@kimcoleman)
* ENHANCEMENT: Update the Zxcvbn library to latest release v1.3.1. #55 (@kimcoleman)
* ENHANCEMENT: Added better support for v3.1 and use built-in CSS classes where possible. #53 (@andrewlimaza)
* ENHANCEMENT: Adjusted Javascript to work with v3.1 new CSS classes. #53 (@ipokkel)
* BUG FIX: Fixes an issue when using the block or shortcode on a page that isn't set to the checkout page options in PMPro. #51 (@andrewlimaza)
* BUG FIX: Fixed incorrect variable passed to localized text string. #48 (@ipokkel)
* SECURITY: Improved escaping on text. #53 (@andrewlimaza)

= 0.5 - 2021-09-28 =
* ENHANCEMENT: Added new filter to allow less required characters for site's using the custom password checker `pmprosp_minimum_password_length`. #41 (@mircobabini)
* ENHANCEMENT: Update the Zxcvbn library to support PHP 8.0. #43 (@mircobabini)
* BUG FIX/ENHANCEMENT: Fixed JavaScript warning for WordPress sites on 5.5.0+, support the newer method `userInputDisallowedList`. #37 (@andrewlimaza)
* BUG FIX/ENHANCEMENT: Fixed issue for sites running PHP 7.2 or lower. New requirements for the Zxcvbn library requires PHP 7.2+, older PHP versions will run our custom checker. #35 (@andrewlimaza)

= 0.4 =
* BUG FIX: Fixed an issue where site's running PHP 5.6 would fatal error. This uses a custom password checker for sites on PHP 5.6. Recommended PHP version is 7.2+

= 0.3 =
* BUG FIX: Fixed bug where a warning was shown if the $post global was empty. (Thanks, Mirco Babini)
* ENHANCEMENT: Updated to use the same "Zxcvbn" library that core WordPress and PMPro use for checking password strength.

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
