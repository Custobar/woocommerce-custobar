(function ($) {
	$(
		function () {
			var $actions = $( "#fields-action" ).children().remove(),
			$submit      = $( "p.submit" ),
			$container   = $( "#custobar-settings" ),
			$fields      = $container.find( "textarea" );

			$submit.append( $actions );

			$( ".submit .button-lock, .submit .button-restore" ).tipTip(
				{
					attribute: "data-tip",
					fadeIn: 50,
					fadeOut: 50,
					delay: 200,
					defaultPosition: "top",
				}
			);

			$submit.on(
				"click",
				".button-lock",
				function (event) {
					event.preventDefault();

					var $icon = $( this ).find( ".dashicons" );

					if ($icon.hasClass( "dashicons-lock" )) {
						$fields.attr( "readonly", false );
						$icon
						.removeClass( "dashicons-lock" )
						.addClass( "dashicons-unlock" );
					} else {
						$fields.attr( "readonly", true );
						$icon
						.removeClass( "dashicons-unlock" )
						.addClass( "dashicons-lock" );
					}
				}
			);

			$submit.on(
				"click",
				".button-restore",
				function (event) {
					event.preventDefault();

					for (var fieldKey in Custobar.fields_map) {
						$( "#" + fieldKey ).val( Custobar.fields_map[fieldKey] );
					}

					$submit.find( ".woocommerce-save-button" ).click();
				}
			);
		}
	);

	// API connection test
	$( "#custobar-api-connection-test" ).on(
		"click",
		function (e) {
			e.preventDefault();

			data = {
				action: "custobar_api_test",
				_ajax_nonce: Custobar.nonce,
			};
			$.post(
				ajaxurl,
				data,
				function (response) {
					$( "#custobar-api-connection-test-wrap" ).append(
						"<p>" + response.data.message + "</p>"
					);
				}
			);

			console.log( "testing api..." );
		}
	);
})( jQuery );
