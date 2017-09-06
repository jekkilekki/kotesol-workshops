// Based on https://github.com/andreassolberg/jso/tree/version3
// Ref Mor10 @ LinkedIn https://www.lynda.com/WordPress-tutorials/Authenticate-app-users-through-JSO/609032/648701-4.html

var ROOTURL = "http://k2k.dev";
const RESTROOT = ROOTURL + '/wp-json';
const RESTROUTE = RESTROOT + '/wp/v2/workshops/';

var jso = new JSO({
  providerID: "Workshops", // OAuth Client name
  client_id: "ICfo4cUqwsbQZzSV6MSz7TMFcRFt1I1Klg9qs4QY", // OAuth Client I
  redirect_uri: "http://127.0.0.1:63506/htdocs/WorkshopApp/workshops.html", // OAuth Redirect URI
  authorization: ROOTURL + "/oauth/authorize"
});

// Catch the response after login:
jso.callback();

var token = localStorage.getItem( 'tokens-Workshops' );

// Trigger OAuth2 authentication sequence:
function oauthLogin() {
  jso.getToken();
}

// Logout all the things and wipe all memory of the session:
function oauthLogout() {
  jso.wipeTokens();
}

// Monitor the login button:
$( '#login' ).click( function() {
    $( '#login' ).toggle();
    $( '#logout' ).toggle();
    oauthLogin();
});

// Monitor the logout button:
$( '#logout' ).click( function() {
    $( '#logout' ).toggle();
    $( '#login' ).toggle();
    oauthLogout();
});

(function() {
  // // If we are on the home page, redirect to workshops.html:
  // if ( location.pathname == "/htdocs/WorkshopApp/" ) {
  //   // If we have a token, assume we're logged in:
  //   if ( token !== null ) {
  //     window.location.href = "./workshops.html";
  //   }
  // } else {
    // If we have a token, assume we're logged in:
    if ( token !== null ) {
      // Enable JSO jQuery wrapper:
      JSO.enablejQuery($);
    }
  //   else {
  //     // If we're not logged in, redirect to the login page:
  //     window.location.href = "./";
  //   }
  // }
})();
