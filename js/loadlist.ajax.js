/**
 * Script for loading the Task List.
 *
 * Constant RESTROUTE and variable token inherited from oauth.js
 */

function getAttendeeList( object ) {
    let list;

    if ( ! object.attendee_list ) {
        list = '<a class="attendee-list-button" href="add_attendees.html?workshop=' + object.id + '">Attendee List</a>';
    } else {
        list = '<a class="attendee-list-button" href="attendees.html?workshop=' + object.id + '">Attendee List</a>';
    }

    return list;
}

function getPresenter( object, num ) {
    let presenter;

    if ( num == 1 ) {
        if ( ! object.cmb2.workshop_metabox.workshop_presenter_1 ) {
            presenter = "";
        } else {
            presenter = '<p class="presenter-name">' + object.cmb2.workshop_metabox.workshop_presenter_1 + ': <span class="presentation-title">' + object.cmb2.workshop_metabox.workshop_title_1 + '</span></p>';
        }
    } else {
        if ( ! object.cmb2.workshop_metabox.workshop_presenter_2 ) {
            presenter = "";
        } else {
            presenter = '<p class="presenter-name">' + object.cmb2.workshop_metabox.workshop_presenter_2 + ': <span class="presentation-title">' + object.cmb2.workshop_metabox.workshop_title_2 + '</span></p>';
        }
    }

    return presenter;
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

function createWorkshopList( object ) {
    console.info( object );
    $( '.workshop-list' ).empty().append( '<ul></ul>' );

    for( let i = 0; i < object.length; i++ ) {
        let navListItem =
            '<li class="workshop">' +
                '<a href="single.html?workshop=' + object[i].id + '">' +
                    '<h1 class="workshop-title">' + object[i].title.rendered + '</h1>' +
                    getDate( object[i] ) +
                    getPresenter( object[i], 1 ) +
                    getPresenter( object[i], 2 ) +
                    getAttendeeList( object[i] ) +
                '</a>' +
            '</li>';
        $( '.workshop-list ul' ).append( navListItem );
    }
}

function getWorkshopList() {
    $( ".workshop-list" ).append( '<div class="loader"><img src="images/spinner.svg" class="ajax-loader"></div>' );

    jso.ajax({
        dataType: 'json',
        url: RESTROUTE
    })

    .done( function( object ) {
        createWorkshopList( object );
    })

    .fail( function( object ) {
        console.error( "REST error. Nothing returned for AJAX." );
    })

    .always( function() {
        $( '.loader' ).remove();
    })
}

if ( token !== null ) {
    getWorkshopList();
} else {
    window.location.href = "/";
}
