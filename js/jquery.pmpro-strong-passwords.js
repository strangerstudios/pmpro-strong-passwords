/*
// TODO: Allow Weak filter
// TODO: detect width of password field and set progressbar width same
// TODO: set pmprosp-progressbar-status box shadow width to larger than progressbar
// TODO: Add password note as tooltip
// TODO: On screen resize adjust progressbar + box-shadow widths 
? Add additional checks for upper & lowercase, numbers and special characters and combine with strength check
// TODO: Filter in blacklist array
?: Mismatch styling?
?: Hide and autofill confirm password field?
?: Add toggle show password?
?: Generate Password + button?
?: Auto Generate PW and show?
*/


var pmprosp_password_blacklist = JSON.parse(pwsL10n.password_blacklist);

function checkPasswordStrength( 
	password_field_1,
    password_field_2,
    strength_result,
    submit_button,
    blacklistArray
	 ) {

    var password_field_1 = password_field_1.val();
    var password_field_2 = password_field_2.val();
 
    // Reset the form & meter
    submit_button.attr( 'disabled', true );
	strength_result.removeClass( 'short bad good strong' );
 
    // Get the password strength
    var strength = wp.passwordStrength.meter( password_field_1, blacklistArray, password_field_2 );
 
    // Add the strength meter results
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
            strength_result.addClass( 'strong' ).html( pwsL10n.strong );
            jQuery(".pmprosp-progressbar-status").css("width", 100 + "%");
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
     if ( password_field_1 === '' ) {
        strength_result.removeClass( 'short bad good strong' );
        jQuery(".pmprosp-progressbar-status").css("width", 0 + "%");
    }
 
    // The meter function returns a result even if password_field_2 is empty,
    // enable only the submit button if the password is strong and
    // both passwords are filled up
    if ( pwsL10n.allow_weak == 1 && '' !== password_field_2.trim() && 5 != strength ) {
        submit_button.removeAttr( 'disabled' );
    } else if ( 4 == strength && '' !== password_field_2.trim() ) {
        submit_button.removeAttr( 'disabled' );
    }
    return strength;
}
 
jQuery( document ).ready( function( $ ) {

    // Move the message and bar to just below the PMPro password 1 field.
    jQuery('#pmprosp-container').insertAfter('.pmpro_checkout-field-password');


    // Show strength progressbar depending on filter
    if ( pwsL10n.display_progressbar ) {
        
        jQuery('#pmprosp-container').append('<div class="pmprosp-progressbar"><span class="pmprosp-progressbar-status"></span></div>');

        function adjust_progressbar_width(){

            var pmpro_progressbar__width = Math.round( jQuery('.pmpro_form input[name=password]').outerWidth() );
            jQuery( '.pmprosp-progressbar' ).css( 'width', pmpro_progressbar__width + 'px' );
    
            var pmpro_progressbar__boxshadow_width = pmpro_progressbar__width + 20;
            jQuery( '.pmprosp-progressbar-status' ).css( 'box-shadow', pmpro_progressbar__boxshadow_width + 'px 0 0 ' + pmpro_progressbar__boxshadow_width + 'px ' + pwsL10n.progressbar_bg_color );
        }

        adjust_progressbar_width();

        // Set progressbar width to password field width
        function resize_progressbar() {
            adjust_progressbar_width();
        };

        // On window resize reset width when resize has finished (debounce)
        var doit;
        window.onresize = function(){
        clearTimeout(doit);
        doit = setTimeout(resize_progressbar, 100);
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

    // add disabled attribute to submit button on page load.
    jQuery('#pmpro_btn-submit').attr('disabled', true);
    
    // Binding to trigger checkPasswordStrength
    jQuery( 'body' ).on( 'keyup', 'input[name=password], input[name=password2]',
        function( event ) {
            checkPasswordStrength(
                jQuery('.pmpro_form input[name=password]'),         // First password field
                jQuery('.pmpro_form input[name=password2]'), // Second password field
                jQuery('.pmpro_form #pmprosp-password-strength'),           // Strength meter
                jQuery('.pmpro_form #pmpro_btn-submit'),           // Submit button
                pmprosp_password_blacklist        // Blacklisted words
            );
        }
    );
});
