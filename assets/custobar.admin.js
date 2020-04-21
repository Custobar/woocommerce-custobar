(function($) {

  // Export run
  $('button.custobar-export').on('click', function( e ) {

    e.preventDefault()

    data = {
       action: 'custobar_export'
     }
     $.post( ajaxurl, data, function( response ) {
       response = JSON.parse( response )
       $('#custobar-export-wrap table').append('<tr><td colspan="4">' + response.message + '</td></tr>');
     });

  })

  // API connection test
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
