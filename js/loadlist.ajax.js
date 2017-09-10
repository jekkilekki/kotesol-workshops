/**
 * Script for loading the Task List.
 *
 * Constant RESTROUTE and variable token inherited from oauth.js
 */

function getAttendeeList( object ) {
    let list;

    if ( ! object.attendee_list ) {
        list = "";
    } else {
        list = '<a class="attendee-list-button" href="attendees.html?list=' + object.id + '">Attendee List</a>';
    }

    return list;
}

function getPresenter( object, num ) {
    let presenter;

    if ( num == 1 ) {
        if ( ! object.cmb2.workbook_metabox.workbook_presenter_1 ) {
            presenter = "";
        } else {
            presenter = '<p class="presenter-name">' + object.cmb2.workbook_metabox.workbook_presenter_1 + ': <span class="presentation-title">' + object.cmb2.workbook_metabox.workbook_title_1 + '</span></p>';
        }
    } else {
        if ( ! object.cmb2.workbook_metabox.workbook_presenter_2 ) {
            presenter = "";
        } else {
            presenter = '<p class="presenter-name">' + object.cmb2.workbook_metabox.workbook_presenter_2 + ': <span class="presentation-title">' + object.cmb2.workbook_metabox.workbook_title_2 + '</span></p>';
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

    if ( ! object.modified ) {
        // Create a JS Date object to convert the date to a human readable string
        let workshopDate = new Date( object.date );
        date = '<span class="workshop-date"><time datetime="' + object.date + '">' + workshopDate.toLocaleDateString( "en-us", dateOptions ) + '</time></span>';
    } else {
        // Create a JS Date object to convert the date to a human readable string
        let workshopModified = new Date( object.modified );
        date = '<span class="workshop-date modified-date"><time datetime="' + object.modified + '">' + workshopModified.toLocaleDateString( "en-us", dateOptions ) + '</time></span>';
    }

    return date;
}

function createWorkshopList( object ) {
    console.info( object );
    $( '.workshop-list' ).empty().append( '<ul></ul>' );

    for( let i = 0; i < object.length; i++ ) {
        let navListItem =
            '<li class="workshop">' +
                '<a href="single.html?task=' + object[i].id + '">' +
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
