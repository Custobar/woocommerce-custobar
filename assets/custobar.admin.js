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

	// Export run
	$( "button.custobar-export" ).on(
		"click",
		function (e) {
			e.preventDefault();

			var recordType    = $( this ).data( "record-type" );
			var previousCount = 0;

			var responseCell = $( "#custobar-export-wrap table tr.response td" );
			var message      = "Starting to export " + recordType + "s...";

			if ( ! responseCell.length) {
				$( "#custobar-export-wrap table" ).append(
					'<tr class="response"><td colspan="9">' + message + "</td></tr>"
				);
			} else {
				responseCell.html( message );
			}

			var _post = function () {
				var resetCheck           = $( 'input[name="reset-' + recordType + '"]' );
				var emailPermissionCheck = $(
					'input[name="can-email-' + recordType + '"]'
				);
				var smsPermissionCheck   = $(
					'input[name="can-sms-' + recordType + '"]'
				);

				data = {
					action: "custobar_export",
					recordType: recordType,
				};

				// Reset offset
				if (resetCheck.is( ":checked" )) {
					data["reset"] = 1;
					resetCheck.prop( "checked", false );
				}

				if (emailPermissionCheck.is( ":checked" )) {
					data["can_email"] = 1;
				}

				if (smsPermissionCheck.is( ":checked" )) {
					data["can_sms"] = 1;
				}

				$.post(
					ajaxurl,
					data,
					function (response) {
						response = response.data;

						var message = "";
						if (response.code == 200) {
							message += response.stats.synced + " " + recordType + "s";

							if (response.stats.variant_synced) {
								message +=
								" and " +
								response.stats.variant_synced +
								" variants";
							}
							message += " exported.";

							// update row
							var reportRow = $( "tr.sync-report-" + response.recordType );
							reportRow.find( "td" ).eq( 2 ).html( response.stats.synced );
							reportRow
							.find( "td" )
							.eq( 3 )
							.html( response.stats.synced_percent );
							reportRow.find( "td" ).eq( 4 ).html( response.stats.updated );
						}
						if (response.code == 220) {
							message +=
							"No more records were found. Total of " +
							response.stats.synced +
							" " +
							recordType +
							"s exported.";
						}
						if (response.code == 420) {
							message +=
							"Either WooCommerce is uninstalled or other configuration conditions were not met. " +
							"Check that you have a valid API key set for Custobar. Response code " +
							response.code +
							", no records were exported.";
						}
						if (response.code == 429) {
							message +=
							"Too many requests. Response code " +
							response.code +
							", no records were exported.";
						}
						if (response.code == 440) {
							message +=
							"No more records available to export. Response code " +
							response.code +
							", no records were exported.";
						}
						if (response.code == 444) {
							message +=
							"Error connecting to Custobar API: " + response.body;
						}

						$( "#custobar-export-wrap table tr.response td" ).html( message );

						// Post again
						if (
						response.count &&
						response.stats.synced < response.stats.total
						) {
							$( "#custobar-export-wrap table tr.response td" ).html(
								message + ".."
							);
							_post();
						}
					}
				);
			};
			_post();
		}
	);

	// API connection test
	$( "#custobar-api-connection-test" ).on(
		"click",
		function (e) {
			e.preventDefault();

			data = {
				action: "custobar_api_test",
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
