(function( $ ){

	var cmdStack = [];
	var cmdTravel = 0;
	
	$( '#wp-cli-cmd-input' ).keydown( function (e) {
		
		var key = e.which,
			input_field = $(this);
		
		if( key == 13 ) { // Enter Key

			var wrapper 			= input_field.closest('.wp-cli-console'),
				input_cmd 			= input_field.val().trim(),
				output_container 	= input_field.prev('.wp-cli-console-output'),
				spinner 			= input_field.next('.wp-cli-input-spinner');

			if( '' != input_cmd ) {

				cmdStack.push( input_cmd );
				cmdTravel = cmdStack.length;

				input_field.val('');
				if( 'clear' == input_cmd ) {
					output_container.html('');
				} else {
					wrapper.addClass('wp-cmd-exicuting');
					input_field.attr( "disabled", "true" );
					output_container.append('<br>');
					
					$.ajax({
						url: ajaxurl,
						data: {
							'action': 'get_cmd_result',
							'cmd': input_cmd
						},
						type: 'POST',
						dataType: 'json',
						success: function( responseData, textStatus, errorThrown) {
							// console.log(responseData);
							if( responseData.status ) {
								output_container.append( responseData.result );
								setTimeout(function(){
									output_container.scrollTop(output_container[0].scrollHeight);
								}, 200);
							}
							wrapper.removeClass('wp-cmd-exicuting');
							input_field.removeAttr("disabled");
						},
						error: function (responseData, textStatus, errorThrown) {
							console.log(responseData);
						}
					});
					
				}
				input_field.focus();
			}
		} else if ( key == 38 && cmdTravel > 0 ) { // Up Arrow Key
			cmdTravel--;
			input_field.val( cmdStack[cmdTravel] );
		} else if ( key == 40 && cmdStack.length > cmdTravel ) { // Down Arrow Key
			cmdTravel++;
			input_field.val( cmdStack[cmdTravel] );
		}
	} ); 
})(jQuery);
