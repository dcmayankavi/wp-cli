(function( $ ){
	
	$( '#wp-cli-cmd-input' ).keypress( function (e) {
		var key = e.which;
		if( key == 13 ) {

			var input_field 		= $(this),
				wrapper 			= input_field.closest('.wp-cli-console'),
				input_cmd 			= input_field.val().trim(),
				output_container 	= input_field.prev('.wp-cli-console-output'),
				spinner 			= input_field.next('.wp-cli-input-spinner');

			if( '' != input_cmd ) {

				input_field.val('');
				if( 'clear' == input_cmd ) {
					output_container.html('');
				} else {
					wrapper.addClass('wp-cmd-exicuting')
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
							wrapper.removeClass('wp-cmd-exicuting')
						},
						error: function (responseData, textStatus, errorThrown) {
							console.log(responseData);
						}
					});
					
				}
				input_field.focus();
			}
		}
	} ); 
})(jQuery);
