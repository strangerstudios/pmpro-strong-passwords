function checkPasswordStrength( 
	$password_field_1,
    $password_field_2,
    $strengthResult,
    $submitButton,
    blacklistArray
	 ) {

    var password_field_1 = $password_field_1.val();
    var password_field_2 = $password_field_2.val();
 
    // Reset the form & meter
    $submitButton.attr( 'disabled', 'disabled' );
	$strengthResult.removeClass( 'short bad good strong' );
 
    // Get the password strength
    var strength = wp.passwordStrength.meter( password_field_1, blacklistArray, password_field_2 );
 
    // Add the strength meter results
    switch ( strength ) {
        case 2:
            $strengthResult.addClass( 'bad' ).html( pwsL10n.bad );
            break;

        case 3:
            $strengthResult.addClass( 'good' ).html( pwsL10n.good );
            break;
 
        case 4:
            $strengthResult.addClass( 'strong' ).html( pwsL10n.strong );
            break;
 
        case 5:
            $strengthResult.addClass( 'short' ).html( pwsL10n.mismatch );
            break;
 
        default:
            $strengthResult.addClass( 'short' ).html( pwsL10n.short );
 
    }
 
    // The meter function returns a result even if password_field_2 is empty,
    // enable only the submit button if the password is strong and
    // both passwords are filled up
    if ( 4 === strength && '' !== password_field_2.trim() ) {
        $submitButton.removeAttr( 'disabled' );
    }
 
    return strength;
}
 
jQuery( document ).ready( function( $ ) {
    // Binding to trigger checkPasswordStrength
    jQuery( 'body' ).on( 'keyup', 'input[name=password], input[name=password2]',
        function( event ) {
            checkPasswordStrength(
                jQuery('input[name=password]'),         // First password field
                jQuery('input[name=password2]'), // Second password field
                jQuery('#password-strength'),           // Strength meter
                $('input[type=submit]'),           // Submit button
                ['black', 'listed', 'word']        // Blacklisted words
            );
        }
    );
});