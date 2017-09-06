/**
 * Script for loading the Task List.
 *
 * Constant RESTROUTE and variable token inherited from oauth.js
 */

function createWorkshopList( object ) {
    console.info( object );
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

// if ( token !== null ) {
    getWorkshopList();
// } else {
//     window.location.href = "/htdocs/WorkshopApp/";
// }
