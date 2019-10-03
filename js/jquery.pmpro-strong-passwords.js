/**
 * PMPro Strong Passwords
 */

// Create an array from blacklist JSON
var pmprosp_password_blacklist = JSON.parse(pwsL10n.password_blacklist);

// Check strength of password using WordPress password strength meter
function checkPasswordStrength(
	password_field_1,
    password_field_2,
    strength_result,
    blacklistArray
	 ) {

    // get values from password field object
    var password_field_2_value = password_field_2.val();
    var password_field_1_value = password_field_1.val();

    // Reset the form & meter
	strength_result.removeClass( 'short bad good strong' );

    // Get the password strength
    var strength = wp.passwordStrength.meter( password_field_1_value, blacklistArray, password_field_2_value );
    var pass_ok = false;

   if ( password_field_1_value ) {

        var reg = /^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\/\]\[\\\^\$\.\|\?\*\+\(\){}\-_\`\'\",<>:&%#@!~;=€£¥]).*$/;

        //Check if password is 8 characters long with special characters, uppercase and letters.
        if ( jQuery('#username').val() != password_field_1_value && reg.test( password_field_1_value ) ) {
            pass_ok = true;
        }
    }

        switch ( strength ) {
            case 2:
                strength_result.addClass( 'bad' ).html( pwsL10n.bad );
                jQuery(".pmprosp-progressbar-status").css("width", 50 + "%");
                break;

            case 3:
                strength_result.addClass( 'good' ).html( pwsL10n.good );
                jQuery(".pmprosp-progressbar-status").css("width", 70 + "%");
                break;

            case 4:
                if ( pass_ok ) {
                    strength_result.addClass( 'strong' ).html( pwsL10n.strong );
                    jQuery(".pmprosp-progressbar-status").css("width", 100 + "%");
                } else {
                    strength_result.addClass( 'good' ).html( pwsL10n.good );
                    jQuery(".pmprosp-progressbar-status").css("width", 70 + "%");
                }
                break;

            case 5:
                strength_result.addClass( 'short' ).html( pwsL10n.mismatch );
                jQuery(".pmprosp-progressbar-status").css("width", 20 + "%");
                break;

            default:
                strength_result.addClass( 'short' ).html( pwsL10n.short );
                jQuery(".pmprosp-progressbar-status").css("width", 20 + "%");
        }

     // hide the password strength.
     if ( password_field_1_value === '' ) {
        strength_result.removeClass( 'short bad good strong' );
        jQuery(".pmprosp-progressbar-status").css("width", 0 + "%");
    }

    return strength;
}

jQuery( document ).ready( function( $ ) {

    // Move the stong password container to just below the PMPro password 1 field.
    jQuery('#pmprosp-container').insertAfter('.pmpro_checkout-field-password');


    // Show strength progressbar depending on filter
    if ( pwsL10n.display_progressbar ) {

        // Add progressbar element to page
        jQuery('#pmprosp-container').append('<div class="pmprosp-progressbar"><span class="pmprosp-progressbar-status"></span></div>');

        // Set progressbar width to match password field width
        function adjust_progressbar_width(){

            // Get width of password input field and set progressbar width
            var pmpro_progressbar__width = Math.round( jQuery('.pmpro_form input[name=password]').outerWidth() );
            jQuery( '.pmprosp-progressbar' ).css( 'width', pmpro_progressbar__width + 'px' );

            // box-shadow width must be greater than progressbar width
            var pmpro_progressbar__boxshadow_width = pmpro_progressbar__width + 20;
            jQuery( '.pmprosp-progressbar-status' ).css( 'box-shadow', pmpro_progressbar__boxshadow_width + 'px 0 0 ' + pmpro_progressbar__boxshadow_width + 'px ' + pwsL10n.progressbar_bg_color );
        }
        adjust_progressbar_width();

        // On window resize reset width when resize has finished (debounce)
        var do_debounce;
        window.onresize = function(){
        clearTimeout(do_debounce);
        do_debounce = setTimeout(adjust_progressbar_width, 100);
        };
    }

    // Show password tooltip depending on filter
    if ( pwsL10n.display_password_tooltip ) {
        jQuery('.pmpro_checkout-field-password label').append('<span class="pmprosp-tooltip__password" data-tooltip-location="right" data-tooltip="' + pwsL10n.password_tooltip + '">?</span>');
    }

    // Show password strength pill depending on filter
    if ( pwsL10n.display_password_strength ) {
        jQuery('.pmpro_checkout-field-password label').append('<span id="pmprosp-password-strength"></span>');
    }

    // create objects from password input fields
    var password_field_1 = jQuery('.pmpro_form input[name=password]');
    var password_field_2 = jQuery('.pmpro_form input[name=password2]');

    // Check if confirm password field is available otherwise use password field
    if ( undefined == password_field_2 || 1 != jQuery('.pmpro_form input[name=password2]').length || jQuery(password_field_2).is(":hidden") ) {
        var password_field_2 = password_field_1;
    }

    // If passwords were passed to the checkout form from another page check strength on load
    setTimeout(function () {
        checkPasswordStrength(password_field_1, // First password field
        password_field_2, // Second password field
        jQuery('.pmpro_form #pmprosp-password-strength'), // Strength meter
        pmprosp_password_blacklist // Blacklisted words
        );
      }, 500);

    // Binding to trigger checkPasswordStrength
    jQuery( 'body' ).on( 'keyup', 'input[name=password], input[name=password2]',
        function( event ) {
            checkPasswordStrength(
                password_field_1,         // First password field
                password_field_2,         // Second password field
                jQuery('.pmpro_form #pmprosp-password-strength'),           // Strength meter
                pmprosp_password_blacklist        // Blacklisted words
            );
        }
    );
});
