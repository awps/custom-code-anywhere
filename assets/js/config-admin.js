;(function( $ ) {

	"use strict";

	function clearMessages(){

	}

	function showMessage(){

	}

	function clearForm(){
		$('textarea[name="code"]').val( '' );
		$('input[name="label"]').val( '' );
		$('input[name="position_in_html"][value="footer"]').prop( 'checked', true );
		$('input[name="priority"]').val( 10 );
		$('input[name="tci-id"]').val( '' );
		$('#tc-delete').addClass( 'tc-hidden' );
	}

	// ----------------------------------------------------------------------------
	// Submit form
	// ----------------------------------------------------------------------------
	$('#tci-form').on( 'submit', function( event ){
		event.preventDefault();

		var _form  = $(this),
		_form_data = _form.serialize(),
		_btn       = $('#submit'),
		_btn_spin  = $('#submit-spinner');
		
		var _code_area = $('textarea[name="code"]');

		if( $('textarea[name="code"]').val().trim().length < 1 ) {
			_code_area.css( 'border-color', 'red' );
			return false;
		}

		_code_area.css( 'border-color', '' );

		_btn.attr( 'disabled', 'disabled' );
		_btn_spin.addClass( 'is-active' );

		$.ajax({
			method: 'POST',
			url: ajaxurl,
			data: {
				'action': 'tracking_code_installer',
				'form': _form_data,
			},
			success: function( response ){
				
				console.log( response );

				var _msg = '';
					
				if( response != 0 ){
					response = JSON.parse( response );
					
					if( response.status ){
						if( 'success' == response.status ){
							var _to_replace = $('.tci-connect').find( '#tc-'+ response.id );

							// Is an update?
							if( _to_replace.size() > 0 ){
								_to_replace.replaceWith( response.html );
							}
							// Is new.
							else{
								$('.tci-panel-left').append( response.html );
							}

							clearForm();
						}

						_msg = response.message;
					}
				}
				else{
					_msg= 'Something went wrong! Try again.';
				}
				
				showMessage( _msg );
			},
			complete: function(){
				_btn.removeAttr( 'disabled' );
				_btn_spin.removeClass( 'is-active' );
			},
		});
	});

	// ----------------------------------------------------------------------------
	// Delete
	// ----------------------------------------------------------------------------
	$('#tc-delete').on( 'click', function( event ){
		event.preventDefault();

		var _t  = $(this),
		_id = $('[name="tci-id"]').val(),
		_nonce = $('[name="tci-security-token"]').val();
		
		clearForm();

		$('.tc-single-item.active').slideUp( 200, function(){
			var _item = $(this);

			$.ajax({
				method: 'POST',
				url: ajaxurl,
				data: {
					'action': 'tracking_code_installer',
					'is_delete': true,
					'id': _id,
					'nonce': _nonce
				},
				success: function( response ){
					console.log( response );
					
					if( response != 0 ){
						response = JSON.parse( response );
					}

					if( response.is_delete == 1 && response.status === 'success' ){
						_item.remove();
					}
					else{
						_item.slideDown();
					}
				},
				complete: function(){
				},
				error: function( response ) {
					console.log( response );
				}
			});
		} );

	});

	// ----------------------------------------------------------------------------
	// Load form
	// ----------------------------------------------------------------------------
	$( '.tci-panel-left' ).on( 'click', '.tc-single-item', function(){
		$( '.tci-panel-left' ).find( '.tc-single-item' ).not(this).removeClass( 'active' );

		var _t = $(this),
		_code = _t.find( '.tc-code' ).text(),
		_label = _t.find( '.tc-label' ).text(),
		_position_in_html = _t.find('.tc-position_in_html').text(),
		_priority = _t.find( '.tc-priority' ).text(),
		_id = _t.data( 'id' );

		_t.addClass( 'active' );

		$('textarea[name="code"]').val( _code );
		$('input[name="label"]').val( _label );
		$('input[name="position_in_html"][value="'+ _position_in_html +'"]').prop( 'checked', true );
		$('input[name="priority"]').val( _priority );
		$('input[name="tci-id"]').val( _id );
		$('#tc-delete').removeClass( 'tc-hidden' );

	} );

	// ----------------------------------------------------------------------------
	// Cancel
	// ----------------------------------------------------------------------------
	$( '#cancel-edit' ).on( 'click', function(){
		$( '.tci-panel-left' ).find( '.tc-single-item' ).removeClass( 'active' );
		clearForm();
	} );


})(jQuery);