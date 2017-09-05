$( '#qr-scanner' ).html5_qrcode( function( data ) {
    // do something when code is read
    $( '.user_name' ).val( 'Aaron' );
    $( '.error-msg' ).html( "" );
  },
  function( error ) {
    // show read errors
    $( '.error-msg' ).html( "Error accessing camera." );
  },
  function( videoError ) {
    // the video stream could not be opened
    $( '.error-msg' ).html( "Error with video." );
  }
);
