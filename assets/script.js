(function( $ ){

	var WP_CLI = {

		/**
		 * Initializes the services logic.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		cmdStack: Array(),
		cmdTravel: 0,

		init: function() {

			this.console_open();
			this.console_close();
			this.console_resize();
		},

		console_open: function() {

			var self = this;
			$(document).on( 'click', '#wp-admin-bar-wp_cli_link', function(e){

				e.preventDefault();
				$('.wp-cli-console').fadeIn(100);
				$('.wp-cli-console').find('.wp-cli-cmd-input').focus();
			});

			$( '#wp-cli-cmd-input' ).keydown( function (e) {
		
				var key = e.which,
					input_field = $(this);
				
				if( key == 13 ) { // Enter Key

					var wrapper 			= input_field.closest('.wp-cli-console'),
						input_cmd 			= input_field.val().trim(),
						output_container 	= input_field.prev('.wp-cli-console-output'),
						spinner 			= input_field.next('.wp-cli-input-spinner');

					if( '' != input_cmd ) {

						self.cmdStack.push( input_cmd );
						self.cmdTravel = self.cmdStack.length;

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
									'action': 'get_wp_cli_result',
									'cmd': input_cmd
								},
								type: 'POST',
								dataType: 'json',
								success: function( responseData, textStatus, errorThrown) {
									
									output_container.append('<span class="wp-cli-command">$ ' + input_cmd + '</span>' );
									if( responseData.status ) {
										output_container.append( responseData.result );
										setTimeout(function(){
											output_container.scrollTop(output_container[0].scrollHeight);
										}, 200);
									} else {
										output_container.append( '<pre>Error: Command not found.</pre>' );
										setTimeout(function(){
											output_container.scrollTop(output_container[0].scrollHeight);
										}, 200);
									}
									wrapper.removeClass('wp-cmd-exicuting');
									input_field.removeAttr("disabled");
									input_field.focus();
								},
								error: function (responseData, textStatus, errorThrown) {
									console.log(responseData);
								}
							});
							
						}
						input_field.focus();
					}
				} else if ( key == 38 && self.cmdTravel > 0 ) { // Up Arrow Key
					self.cmdTravel--;
					input_field.val( self.cmdStack[self.cmdTravel] );
				} else if ( key == 40 && self.cmdStack.length > self.cmdTravel ) { // Down Arrow Key
					self.cmdTravel++;
					input_field.val( self.cmdStack[self.cmdTravel] );
				}
			} );
		},

		console_close: function() {

			$(document).on( 'click', '.wp-cli-console-close', function(e){

				e.preventDefault();
				$(this).closest('.wp-cli-console').fadeOut(100);
			});
		},
		
		console_resize: function() {

			var dragging = false;
			var oldPageY = 0;
			$( '#wp-cli-drag-bar' ).mousedown(function(e){
				e.preventDefault();
				oldPageY = e.pageY;
				dragging = true;
				var main = $('.wp-cli-console');
				var ghostbar = $('<div>',
						{
							id: 'wp-cli-ghost-bar',
						 	css: {
								width: main.outerWidth(),
								top: main.offset().top,
								left: main.offset().left
							}
						}).appendTo('body');
				
				$(document).mousemove(function(e){
					ghostbar.css( "top", e.pageY+2 );
				});
			});

			$(document).mouseup(function(e){

				if (dragging)  {

					var currentPageY  = e.pageY,
						consoleOutput = $( '.wp-cli-console-output' ),
						totalHeight   = oldPageY - currentPageY,
						oldHeight     = consoleOutput.outerHeight();
						newHeight     = oldHeight + totalHeight;

					consoleOutput.css( 'height', newHeight + 'px' );
					$( '#wp-cli-ghost-bar' ).remove();
					$(document).unbind( 'mousemove' );
					dragging = false;
				}
			});
		},
	};

	$( function() {
		
		WP_CLI.init();
	});

})(jQuery);
