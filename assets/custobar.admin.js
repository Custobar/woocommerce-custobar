(function($) {

  $('#custobar-api-connection-test').on('click', function( e ) {

    e.preventDefault()

    data = {
       action: 'custobar_api_test'
     }
     $.post( ajaxurl, data, function( response ) {
       response = JSON.parse( response )
       $('#custobar-api-connection-test-wrap').append('<p>' + response.message + '</p>')
     });


    console.log('testing api...')
  })

})( jQuery );
