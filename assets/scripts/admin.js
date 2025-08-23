// Create Caching Containers
var $ibnBannerTitle;
var $ibnBackgroundColor;
var $ibnTextColor;
var $ibnSaveButton;
var $ibnPinPost;
var $ibnPinPostExpiration;
var $ibnExpirationSetupContainer;
var $ibnExpirationDateContainer;

// Cache Setup Defaults
var ibnSetup = {
    isSaveTriggered: false,
    isPinTriggered: false
};

// Document Ready Event Handler
jQuery( document ).ready( function(){
    // Options Page Setup
    if ( jQuery( "#ibn-save-settings" ).length > 0 ) {
        ibnOptionsPageSetup();
    } else if ( jQuery( "#ibn-expiration-setup-container" ).length > 0 ) {
        ibnPostPageSetup();
    }
} );

// Options Page Methods
function ibnOptionsPageSetup() {
    // Cache Containers
    $ibnBannerTitle = jQuery( "#ibn-banner-title" );
    $ibnBackgroundColor = jQuery( "#ibn-background-color" );
    $ibnTextColor = jQuery( "#ibn-text-color" );
    $ibnSaveButton = jQuery( "#ibn-save-settings" );

    // Set Banner Background Color Picker
    $ibnBackgroundColor.wpColorPicker().removeClass( "ibn-hidden-on-start" );

    // Set Banner Text Color Picker
    $ibnTextColor.wpColorPicker().removeClass( "ibn-hidden-on-start" );

    // Set Save Button
    $ibnSaveButton.on( "click", ibnSaveSettings );
}

function ibnSaveSettings() {
    let settings = {
        title: $ibnBannerTitle.val().trim(),
        background_color: $ibnBackgroundColor.val().trim(),
        text_color: $ibnTextColor.val().trim()
    };

    if ( 
        settings.title.length > 0 &&
        !ibnSetup.isSaveTriggered
    ) {
        // Lock the button
        ibnSetup.isSaveTriggered = true;

        // Change the Save button text to Loading
        $ibnSaveButton.html( ibnDefaults.loadingText );

        // Send the Save Settings Request
        jQuery.ajax( {
            url: ibnDefaults.ajax_url,
            type: "POST",
            data: {
                action: "ibn_save_settings",
                settings: settings,
                _wpnonce: ibnDefaults.nonce
            },
            success: function( response ) {
                if ( typeof response !== "undefined" ) {
                    // Revert Loading Button
                    $ibnSaveButton.html( ibnDefaults.saveText );
                    ibnSetup.isSaveTriggered = false;

                    // Parse the response
                    let result = JSON.parse( response );
                    if ( result != false ) {
                        alert( result );
                    }
                }
            },
            error: function( response ) {
                console.log( response );
            }
        } );
    } else if ( settings.title.length == 0 ) {
        alert( "Banner Title is required!" );
    }
}

// Post Page Methods
function ibnPostPageSetup() {
    // Cache Containers
    $ibnPinPost = jQuery( "#ibn-pin-post" );
    $ibnPinPostExpiration = jQuery( "#ibn-pin-post-expiration" );
    $ibnExpirationSetupContainer = jQuery( "#ibn-expiration-setup-container" );
    $ibnExpirationDateContainer = jQuery( "#ibn-expiration-day, #ibn-expiration-month, #ibn-expiration-year, #ibn-expiration-hour, #ibn-expiration-minute" );

    // Set Post Pin Controller
    $ibnPinPost.on( "change", ibnPinPost );

    // Set Expiration Date Opener
    $ibnPinPostExpiration.on( "change", ibnExpirationSetupContainerController );

    // Set Expiration Date Controller
    $ibnExpirationDateContainer.on( "change", ibnCheckExpirationDateOnChange );
}

function ibnExpirationSetupContainerController() {
    // Show / Hide Container
    $ibnExpirationSetupContainer.toggleClass( "hidden" );
}

function ibnPinPost() {
    if ( !ibnSetup.isPinTriggered ) {
        // Lock the Checkbox
        ibnSetup.isPinTriggered = true;

        // Disable the checkbox
        $ibnPinPost.attr( "disabled", "disabled" );

        // Get the Post ID
        let postID = jQuery( this ).data( "post-id" );

        // Get Pin Type
        let pinType = jQuery( this ).is( ":checked" );

        // Send the Pin Post Request
        jQuery.ajax( {
            url: ibnDefaults.ajax_url,
            type: "POST",
            data: {
                action: "ibn_pin_post",
                post_id: postID,
                pin_type: pinType,
                _wpnonce: ibnDefaults.pinNonce
            },
            success: function( response ) {
                if ( typeof response !== "undefined" ) {
                    // Unlock the Checkbox
                    ibnSetup.isPinTriggered = false;
                    $ibnPinPost.removeAttr( "disabled" );

                    // Parse the response
                    let result = JSON.parse( response );
                    if ( 
                        result != "pinned" && 
                        result != "unpinned" && 
                        result != false 
                    ) {
                        $ibnPinPost.prop( "checked", false );
                        alert( result );
                    } else if ( result == "pinned" ) {
                        ibnIsPinned = true;
                    } else if ( result == "unpinned" ) {
                        ibnIsPinned = false;
                    }
                }
            },
            error: function( response ) {
                console.log( response );
            }
        } );
    }
}

function ibnCheckExpirationDateOnChange() {
    let expirationDate = {
        year: jQuery( "#ibn-expiration-year" ).val().trim(),
        month: jQuery( "#ibn-expiration-month" ).val().trim(),
        day: jQuery( "#ibn-expiration-day" ).val().trim(),
        hour: jQuery( "#ibn-expiration-hour" ).val().trim(),
        minute: jQuery( "#ibn-expiration-minute" ).val().trim()
    };
    let dateObject = ibnConvertTime( expirationDate.year +"/"+ expirationDate.month +"/"+ expirationDate.day +" "+ expirationDate.hour +":"+ expirationDate.minute );

    if ( 
        dateObject.pickerTimeSerial < dateObject.serverTimeSerial &&
        ibnIsPinned
    ) {
        $ibnPinPost.prop( "checked", false ).trigger( "change" );
    } else if (
        dateObject.pickerTimeSerial > dateObject.serverTimeSerial &&
        ibnIsPinned
    ) {
        $ibnPinPost.prop( "checked", true ).trigger( "change" );
    }
}

function ibnConvertTime( date ) {
    // Get Local Client Time    
    let dateObject = new Date();
    let clientTimeSerial = dateObject.getTime();
    let clientOffset = dateObject.getTimezoneOffset() * 60000;

    // Calculate utcTime
    let utcTime = clientTimeSerial + clientOffset;

    // Calculate Server Time
    let serverTimeSerial = utcTime + ( 3600000 * ibnDefaults.gmtOffset );
    let serverDateObject = new Date( serverTimeSerial );

    // Append Local Seconds to the Picker
    date += ":"+ dateObject.getSeconds();

    // Calculate Picker Time
    let pickerTime = new Date( date );
    let pickerTimeSerial = pickerTime.getTime();

    return {
        clientTimeSerial: clientTimeSerial,
        serverTimeSerial: serverTimeSerial,
        pickerTimeSerial: pickerTimeSerial
    };
}