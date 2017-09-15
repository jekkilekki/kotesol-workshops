  /* =====================================
   * JWT.js file
   * ================================== */

  const RESTROOT = 'http://k2k.dev/wp-json'; // if working with a live site, it should be HTTPS - since Codepen.io uses HTTPS
  const $ENTRYTITLE = $( '.post-title' );
  const $LOGIN = $( '#login' );
  const $LOGOUT = $( '#logout' );
  const $HOMEURL = 'file:///Users/jekkilekki/vagrant-local/www/web/WorkshopApp/index.html';

  // check if we are already logged in
  var token = sessionStorage.getItem( 'workshopToken' );

  // Ajax that should save new info to the DB
  function runAjax( postID, newTitle ) {
    $.ajax({
        url: RESTROOT + '/wp/v2/posts/' + postID,
        method: 'POST',
        beforeSend: function( xhr ) {
          xhr.setRequestHeader( 'Authorization', 'Bearer ' + sessionStorage.getItem( 'workshopToken' ) );
        },
        data: {
          'title': newTitle
        }
      })
      .done( function( response ) {
        console.info( response );
        $( '#title-input' ).toggle();
        $ENTRYTITLE.text( newTitle );
        $ENTRYTITLE.toggle();
        $( '.navigation-list a[data-id="' + postID + '"]' ).text( newTitle );
        $( '.edit-title.edit-button' ).toggle();
        $( '.edit-title.save' ).toggle();
      });
  }

  // Add edit post functionality
  function editPost() {
    $ENTRYTITLE.after( '<button class="edit-button edit-title">Edit title</button><button class="edit-title save" style="display: none">Save title</button>' );

    $( '.edit-title.edit-button' ).click( function() {
      let $originalTitle = $ENTRYTITLE.text();
      $ENTRYTITLE.toggle();
      $ENTRYTITLE.after( '<input id="title-input" type="text">' );
      document.querySelector( '#title-input' ).value = $originalTitle;
      $(this).toggle();
      $( '.edit-title.save' ).toggle();
    });

    $( '.save' ).click( function() {
      let postID = document.querySelector( '.post' ).getAttribute( 'data-id' );
      let newTitle = document.querySelector( '#title-input' ).value;
      runAjax( postID, newTitle );
    });
  }

  // Function to get the JWT authorization token
  function getToken( username, password ) {
    $.ajax({
        url: RESTROOT + '/jwt-auth/v1/token',
        method: 'POST',
        data: {
          'username': username,
          'password': password
        }
      })
      .done( function( response ) {
        sessionStorage.setItem( 'workshopToken', response.token );
        $LOGIN.toggle();
        $LOGOUT.toggle();
        editPost();
      })
      .fail( function( response ) {
        console.error( "REST error" );
      })
  }

  // Function to clear the JWT authorization token
  function clearToken() {
    sessionStorage.removeItem( 'workshopToken' );
    $LOGOUT.toggle();
    $LOGIN.toggle();
    $( '.edit-title' ).remove();
  }

  // Default actions on page LOAD
  $( '.error' ).toggle();

  if ( token === null ) {
    $LOGIN.toggle();

    // Actions to perform on LOGIN
    $( '#login_button' ).click( function(e) {
      e.preventDefault();
      let username = document.querySelector( '#user_login' ).value;
      let password = document.querySelector( '#user_pass' ).value;
      getToken( username, password );
    });

  } else {
    $LOGOUT.toggle();
    if ( window.location == $HOMEURL ) {
        window.location.href = "./workshops.html";
    }

    editPost();
  }

  // Actions to perform on LOGOUT
  $( '#logout' ).click( clearToken );
