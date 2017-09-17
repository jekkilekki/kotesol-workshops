// http://127.0.0.1:5000/add_attendees.html?workshop=5067

var urlParams = new URLSearchParams( window.location.search );
const CURRENTID = urlParams.get( 'workshop' );
console.info( 'Workshop ID: ', CURRENTID );

function buildAttendeeList( object ) {
    // Change the links in the menu
    $( '.all-attendees-button a' ).attr( 'href', 'attendees.html?workshop=' + object.id );
    $( '.add-attendees-button a' ).attr( 'href', 'add_attendees.html?workshop=' + object.id );

    // Load the Workshop Meta Information
    $( '.workshop-meta' ).append( '<p class="workshop-title">' +
                                  '<a href="single.html?workshop=' + object.id + '">' +
                                  object.title.rendered +
                                  '</p>' );
    $( '.workshop-meta' ).append( getDate( object ) );

    $( '.attendee-list' ).empty().append( '<h2>Attendee List</h2>' );

    var attendees = object.attendees; // Array of attendees

    attendees.forEach( function( attendee ) {
        var attendee =  '<article class="attendee">' +
                        '<p class="attendee-name">' + attendee.display_name + '</p>' +
                        '<a href="mailto:' + attendee.user_email + '" class="attendee-email">' + attendee.user_email + '</a>' +
                        '<span class="attendee-membership">' + attendee.attendee_membership + '</span>' +
                        '</article>';

        $( '.attendee-list' ).append( attendee );
    });

}

function getDate( object ) {
    let date;
    // Options to pass to the toLocaleDateString() method of JS Date to make a more human readable string
    let dateOptions = {
        weekday:    "short",
        year:       "numeric",
        month:      "short",
        day:        "numeric",
        hour:       "2-digit",
        minute:     "2-digit"
    };

    // If no specific date is set in the Workshop, use the Post published or modified date:
    if ( object.cmb2.workshop_metabox.workshop_datetime == '' ) {
        if ( ! object.modified ) {
            // Create a JS Date object to convert the date to a human readable string
            let workshopDate = new Date( object.date );
            date = '<p class="workshop-meta workshop-date"><time datetime="' + object.date + '">' + workshopDate.toLocaleDateString( "en-us", dateOptions ) + '</time></p>';
        } else {
            // Create a JS Date object to convert the date to a human readable string
            let workshopModified = new Date( object.modified );
            date = '<p class="workshop-meta workshop-date modified-date"><time datetime="' + object.modified + '">' + workshopModified.toLocaleDateString( "en-us", dateOptions ) + '</time></p>';
        }
    } else {
        // Workshop date is set as a UNIX timestamp, so convert it to a human readable string
        let workshopUnix = new Date( object.cmb2.workshop_metabox.workshop_datetime * 1000 ); // multiply by 1000 for ms
        date = '<p class="workshop-meta workshop-date"><time datetime="' + workshopUnix + '">' + workshopUnix.toLocaleDateString( "en-us", dateOptions ) + '</time></p>';
    }

    return date;
}

function getAttendeeList( workshopRoute ) {
    $( ".attendee-list" ).append( '<div class="loader"><img src="images/spinner.svg" class="ajax-loader"></div>' );

    jso.ajax({
        dataType: 'json',
        url: workshopRoute
    })

    .done( function( object ) {
        buildAttendeeList( object );
        console.info( object );
    })

    .fail( function( object ) {
        console.error( "REST error. Nothing returned for AJAX." );
    })

    .always( function() {
        $( '.loader' ).remove();
    })
}

// Redirect back to Workshops list if there is no Workshop Post ID passed in the URL
if ( CURRENTID !== null ) {
    let workshopRoute = RESTROUTE + CURRENTID;
    console.info( 'workshopRoute: ', workshopRoute );
    getAttendeeList( workshopRoute );
} else {
    window.location.href = "/workshops.html";
}
