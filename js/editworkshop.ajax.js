/**
 * File that processes our form data and submits it to WordPress via the WP REST API
 */
const FILENAME = location.href.split( '/' ).slice( -1 );

function createWorkshop( formData ) {
    jso.ajax({
        dataType: 'json',
        url: RESTROUTE,
        method: 'POST',
        data: formData
    })

    .done( function( object ) {
        // If successful, return to the list of Workshops
        window.location.href = "/workshops.html";
    })

    .fail( function( object ) {
        console.error( "REST error. Nothing returned for AJAX." );
    })

    .always( function() {

    })
}

function createDateTime( date, time ) {
    let unixTime = '1504364400';

    return unixTime;
}

function generateJSON( newWorkshop ) {
    let formData;
    // if creating a NEW Workshop, use this form data
    if ( newWorkshop ) {
        formData = {
            "status":   "private",
            "title":    $( 'input[name=title]' ).val(),
            "content":  $( 'textarea[name=description]' ).val(),
            // "date":     $( 'input[name=date]' ).val(),
            "cmb2": {
                "workshop_metabox": {
                    "workshop_location":    $( 'input[name=location]' ).val(),
                    "workshop_datetime":    createDateTime( $( 'input[name=date]' ).val(), $( 'input[name=time]' ) ),
                    "workshop_presenter_1": $( 'input[name=presenter_1]' ).val(),
                    "workshop_title_1":     $( 'input[name=title_1]' ).val(),
                    "workshop_abstract_1":  $( 'input[name=abstract_1]' ).val(),
                    "workshop_bio_1":       $( 'input[name=bio_1]' ).val(),
                    "workshop_presenter_2": $( 'input[name=presenter_2]' ).val(),
                    "workshop_title_2":     $( 'input[name=title_2]' ).val(),
                    "workshop_abstract_2":  $( 'input[name=abstract_2]' ).val(),
                    "workshop_bio_2":       $( 'input[name=bio_2]' ).val(),
                }
            },
            "attendee_list": false // No Attendee List when first creating the Workshop
        };
    } else {
        formData = {
            "cmb2": {
                "workshop_attendee_metabox": {
                    "workshop_attendee_group": {
                        "attendee_last_name":   $( 'input[name=last_name]' ).val(),
                        "attendee_first_name":  $( 'input[name=first_name]' ).val(),
                        "attendee_email":       $( 'input[name=att_email]' ).val(),
                        "attendee_membership":  $( 'input[name=att_membership]' ).val()
                    }
                }
            },
            "attendee_list": true // We now have an Attendee List
        };
    }

    createWorkshop( formData );
}

function monitorFormSubmit( newWorkshop ) {
    $( document ).on( 'submit', '#workshop-form', function( event ) {
        event.preventDefault();
        generateJSON( newWorkshop );
    });
}

if ( FILENAME[0] === 'new_workshop.html' ) {
    var newWorkshop = true;
    monitorFormSubmit( newWorkshop );
}
