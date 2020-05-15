/**
 * PMPro Strong Passwords
 */

jQuery(document).ready(function(){ 

    // Move the stong password container to just below the PMPro password 1 field.
    jQuery('#pmprosp-container').insertAfter('.pmpro_checkout-field-password');

    // Show strength progressbar depending on filter
    if ( pwsL10n.display_progressbar ) {
        // Add progressbar element to page
        jQuery('#pmprosp-container').append('<div class="pmprosp-progressbar"><span class="pmprosp-progressbar-status"></span></div>');

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
    
    // Check if password is strong or not.
    if ( jQuery( '#password' ) ) {
		pmpro_check_password_strength( jQuery( '#password' ) );
		jQuery( '#password' ).bind( 'keyup paste', function() {
			pmpro_check_password_strength( jQuery( '#password' ) );
		});
    }

    // Check if confirm password is strong or not and delay check slightly.
    if ( jQuery( '#password2' ) ) {
        jQuery( '#password2' ).bind( 'keyup paste', function() {
		    setTimeout( function() { pmpro_check_second_password( jQuery('#password'), jQuery('#password2') ) }, 2000 );
		});
    }



    /**
     * Function to check if password is strong.
     */
    function pmpro_check_password_strength( pass_field ) {
		var pass1 = jQuery( pass_field ).val();		
		var indicator = jQuery( '.pmpro_form #pmprosp-password-strength' );		
		
		var strength;		
		if ( pass1 != '' ) {
			strength = wp.passwordStrength.meter( pass1, wp.passwordStrength.userInputBlacklist(), pass1 );
		} else {
			strength = -1;
		}

		indicator.removeClass( 'empty bad good strong short' );

		switch ( strength ) {
			case -1:
                indicator.addClass( 'empty' ).html( '&nbsp;' );
                jQuery(".pmprosp-progressbar-status").css("width", 0 + "%");
				break;
			case 2:
				indicator.addClass( 'bad' ).html( pwsL10n.bad );
				jQuery(".pmprosp-progressbar-status").css("width", 50 + "%");
				break;
			case 3:
                indicator.addClass( 'good' ).html( pwsL10n.good );
                jQuery(".pmprosp-progressbar-status").css("width", 70 + "%");
				break;
			case 4:
                indicator.addClass( 'strong' ).html( pwsL10n.strong );
                jQuery(".pmprosp-progressbar-status").css("width", 100 + "%");
				break;
			case 5:
                indicator.addClass( 'short' ).html( pwsL10n.mismatch );
                jQuery(".pmprosp-progressbar-status").css("width", 50 + "%");
				break;
			default:
                indicator.addClass( 'short' ).html( pwsL10n['short'] );
		}
    }

    /**
     * Check confirm password is matching.
     */
    function pmpro_check_second_password( pass_1, pass_2 ) {
        var indicator = jQuery( '.pmpro_form #pmprosp-password-strength' );		
        
        if ( pass_1.val() == pass_2.val() ) {
            pmpro_check_password_strength( pass_1 );
        } else {
            indicator.addClass( 'short' ).html( pwsL10n.mismatch );
        }
    }
     /**
      * Function to adjust progress bar
      */
     function adjust_progressbar_width(){

        // Get width of password input field and set progressbar width
        var pmpro_progressbar__width = Math.round( jQuery('.pmpro_form input[name=password]').outerWidth() );
        jQuery( '.pmprosp-progressbar' ).css( 'width', pmpro_progressbar__width + 'px' );

        // box-shadow width must be greater than progressbar width
        var pmpro_progressbar__boxshadow_width = pmpro_progressbar__width + 20;
        jQuery( '.pmprosp-progressbar-status' ).css( 'box-shadow', pmpro_progressbar__boxshadow_width + 'px 0 0 ' + pmpro_progressbar__boxshadow_width + 'px ' + pwsL10n.progressbar_bg_color );
    }

});