(function($) {

  $('#custobar-api-connection-test').on('click', function( e ) {

    e.preventDefault()

    data = {
       action: 'custobar_api_test'
     }
     $.post( ajaxurl, data, function( response ) {
       response = JSON.parse( response )

       console.log( response )

       if ( response.status == 'success' ) {

       } else {

       }
     });


    console.log('testing api...')
  })

})( jQuery );
