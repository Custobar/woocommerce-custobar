(function($) {

  // Export run
  $('button.custobar-export').on('click', function( e ) {

    e.preventDefault()

    var recordType = $(this).data('record-type')

    data = {
       action: 'custobar_export',
       recordType: recordType
     }
     $.post( ajaxurl, data, function( response ) {
       response = JSON.parse( response )

       var message = '';
       if( response.code == 200 ) {
         message += "Custobar data export successful. Code " + response.code + ", total of " + response.count + " records exported.";
       }
       if( response.code == 420 ) {
         message += "Either WooCommerce is uninstalled or other configuration conditions were not met. Check that you have a valid API key set for Custobar. Response code " + response.code + ", no records were exported.";
       }
       if( response.code == 440 ) {
         message += "No records available to export. Response code " + response.code + ", no records were exported.";
       }

       var responseRow = $( '#custobar-export-wrap table tr.response' );
       if( responseRow.length ) {
         responseRow.find('td').html( message )
       } else {
         $('#custobar-export-wrap table').append('<tr class="response"><td colspan="6">' + message + '</td></tr>');
       }

       console.log( response )

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
