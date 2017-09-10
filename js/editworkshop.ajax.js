/**
 * File that processes our form data and submits it to WordPress via the WP REST API
 */
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

function generateJSON() {
    let formData = {
        "status":   "private",
        "title":    $( 'input[name=title]' ).val(),
        "content":  $( 'textarea[name=description]' ).val(),
        // "date":     $( 'input[name=date]' ).val(),
        "cmb2": {
            "workbook_metabox": {
                "workbook_presenter_1": $( 'input[name=presenter_1]' ).val(),
                "workbook_title_1":     $( 'input[name=title_1]' ).val(),
                "workbook_abstract_1":  $( 'input[name=abstract_1]' ).val(),
                "workbook_bio_1":       $( 'input[name=bio_1]' ).val(),
                "workbook_presenter_2": $( 'input[name=presenter_2]' ).val(),
                "workbook_title_2":     $( 'input[name=title_2]' ).val(),
                "workbook_abstract_2":  $( 'input[name=abstract_2]' ).val(),
                "workbook_bio_2":       $( 'input[name=bio_2]' ).val(),
            }
        },
        "attendee_list": false
    };

    createWorkshop( formData );
}

$( document ).on( 'submit', '#workshop-form', function( event ) {
    event.preventDefault();
    generateJSON();
});
