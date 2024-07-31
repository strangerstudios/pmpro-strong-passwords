/**
 * PMPro Strong Passwords
 */

jQuery(document).ready(function(){ 

    // Show strength progressbar depending on filter
    if ( pwsL10n.display_progressbar ) {
        // Add progressbar element to page
        jQuery('#pmprosp-container').append('<div class="pmprosp-progressbar"><span class="pmprosp-progressbar-status"></span></div>');
    }

    // Show password tooltip depending on filter
    if ( pwsL10n.display_password_tooltip ) {
        // Replace quotes with HTML entities
        var tooltip = pwsL10n.password_tooltip.replace(/"/g, '&quot;').replace(/'/g, '&#39;');

        // Add tooltip element to page
        jQuery('.pmpro_form_field-password label').append('<span class="pmprosp-tooltip__password" data-tooltip-location="right" data-tooltip="' + tooltip + '">?</span>');
    }

    // Show password strength pill depending on filter
    if (pwsL10n.display_password_strength) {
        // Use nth-child to target the password field label
        jQuery('.pmprosp-progressbar').after('<span id="pmprosp-password-strength"></span>');
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
            
            // Support Disallowed list for WP 5.5+
            if ( typeof( wp.passwordStrength.userInputDisallowedList ) !== 'undefined' ) { 
                strength = wp.passwordStrength.meter( pass1, wp.passwordStrength.userInputDisallowedList(), pass1 );
            } else { 
                strength = wp.passwordStrength.meter( pass1, wp.passwordStrength.userInputBlacklist(), pass1 );
            }
			
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

});