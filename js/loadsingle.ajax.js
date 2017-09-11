// http://127.0.0.1:5000/single.html?workshop=5067

var urlParams = new URLSearchParams( window.location.search );
const CURRENTID = urlParams.get( 'workshop' );
console.info( 'Task ID: ', CURRENTID );

function getAttendeeList( object ) {
    let attendeeList = object.attendee_list;

    if ( attendeeList ) { // We already have a list of attendees
        var createdList =
            '<div class="attendee-list full-list">Full Attendee List</div>';
        return createdList;
    } else { // There is no list of attendees
        var newList =
            '<div class="attendee-list new-list">New Attendee List</div>';
        return newList;
    }
}

function getDate( object ) {
    // let date;
    // Options to pass to the toLocaleDateString() method of JS Date to make a more human readable string
    let dateOptions = {
        weekday:    "short",
        year:       "numeric",
        month:      "short",
        day:        "numeric",
        hour:       "2-digit",
        minute:     "2-digit"
    };

    var workshopDate = new Date( object.date );
    var date = '<span class="workshop-date"><time datetime="' + object.date + '">' + workshopDate.toLocaleDateString( "en-us", dateOptions ) + '</time></span>';

    var modifiedDate = new Date( object.modified );
    var modified = '';
    // Set modified date only if workshopDate and modifiedDate are different:
    if ( object.date != object.modified ) {
        modified = '<span class="workshop-date modified-date"><time datetime="' + object.modified + '">' + modifiedDate.toLocaleDateString( "en-us", dateOptions ) + '</time></span>';
    }

    return date + modified;
}

function createWorkshop( object ) {

    $( '.single-workshop' ).empty().append( '<article class="workshop"></article>' );

    var workshop =
        '<h2 class="workshop-title">' + object.title.rendered + '</h2>' +
        '<div class="workshop-meta">' + getDate( object ) + '</div>' +
        '<div class="workshop-description">' + object.content.rendered + '</div>';

    var presenterOne =
        '<div class="presenter_1">' +
        '<h2>' + object.cmb2.workshop_metabox.workshop_title_1 + '</h2>' +
        '<p class="workshop-meta">' + object.cmb2.workshop_metabox.workshop_presenter_1 + '</p>' +
        '<div class="presentation-abstract">' + object.cmb2.workshop_metabox.workshop_abstract_1 + '</div>' +
        '<div class="presenter-bio">' +
        '<h3>About ' + object.cmb2.workshop_metabox.workshop_presenter_1 + '</h3>' +
        object.cmb2.workshop_metabox.workshop_bio_1 +
        '</div>'
        '</div><!-- .presenter_1 -->';

    var presenterTwo =
        '<div class="presenter_2">' +
        '<h2>' + object.cmb2.workshop_metabox.workshop_title_2 + '</h2>' +
        '<p class="workshop-meta">' + object.cmb2.workshop_metabox.workshop_presenter_2 + '</p>' +
        '<div class="presentation-abstract">' + object.cmb2.workshop_metabox.workshop_abstract_2 + '</div>' +
        '<div class="presenter-bio">' +
        '<h3>About ' + object.cmb2.workshop_metabox.workshop_presenter_2 + '</h3>' +
        object.cmb2.workshop_metabox.workshop_bio_2 +
        '</div>'
        '</div><!-- .presenter_2 -->';

    $( '.single-workshop article' ).append( workshop );
    $( '.single-workshop article' ).append( presenterOne );
    $( '.single-workshop article' ).append( presenterTwo );

    var attendeeList = getAttendeeList( object );
    if ( attendeeList !== null ) {
        $( '.main-area' ).append( '<section class="attendee-section centered"></section>' );
        $( '.attendee-section' ).append( attendeeList );
    }
}

function getWorkshop( workshopRoute ) {
    $( ".workshop-list" ).append( '<div class="loader"><img src="images/spinner.svg" class="ajax-loader"></div>' );

    jso.ajax({
        dataType: 'json',
        url: workshopRoute
    })

    .done( function( object ) {
        createWorkshop( object );
        //console.info( object );
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
    getWorkshop( workshopRoute );
} else {
    window.location.href = "/workshops.html";
}
