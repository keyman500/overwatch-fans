jQuery(function (e) {

	var dynamicValueFields = e('.acfef-payment-total .payment-amount[data-dynamic_value!=""]');
    e.each( dynamicValueFields, function( key, value ){
        var totalElement = jQuery(value);
        var fieldSources = totalElement.data('default');
		var defaultValue = totalElement.data('dynamic_value');

		if( Number.isInteger(defaultValue) ){
			totalElement.html(eval(defaultValue));
		}
		if( defaultValue.length > 0 ){
            var inputParts = [];
            var inputValue = defaultValue;
			var fieldStructure = defaultValue;
			if( fieldSources.length > 0 ){
				jQuery.each( fieldSources, function( index, fieldName ){
					var sourceInput = acfef_get_field_element(fieldName, true);   
					inputValue = inputValue.replace( '[' + fieldName + ']', sourceInput.val() );

					var valueHolder = inputValue.split(sourceInput.val());
					inputParts.push(valueHolder[0]);
					defaultValue = valueHolder[1]; 
				
				});
			}
            totalElement.html(eval(inputValue));
			
			if( fieldSources.length > 0 ){

				jQuery.each( fieldSources, function( index, fieldName ){
					var sourceInput = acfef_get_field_element(fieldName, false);    
					jQuery('body').on('input', sourceInput, function(){
						var returnValue = defaultValue = fieldStructure;
						jQuery.each( fieldSources, function(ind, part){
								sourceInput = acfef_get_field_element(part, true); 
								returnValue = returnValue.replace( '[' + part + ']', sourceInput.val() );
								var valueHolder = defaultValue.split('[' + part + ']');
								inputParts.push(valueHolder[0]);
								defaultValue = valueHolder[1]; 
							
						});
					
						totalElement.html(eval(returnValue));
					});            
				});
			}
            function acfef_get_field_element(fieldName, chosen){
                var sourceField = totalElement.parents('form').siblings('.pay-to-post').find('div[data-name=' + fieldName + ']');
                var sourceInput = sourceField.find('input');
                if(sourceField.data('type') == 'radio'){
					if( chosen ){
						sourceInput = sourceField.find('label.selected').find('input');
					}else{
	                    sourceInput = sourceField.find('label').find('input');
					}
				}
				if(sourceField.data('type') == 'select'){
					if( chosen ){
						sourceInput = sourceField.find('option:selected');
					}else{
	                    sourceInput = sourceField.find('option');
					}
				}    
                return sourceInput;
            }
        } 
    });




	function requestHandler(settings, card, form) {
		data = {
			action: 'acfef_new_payment',
			acfef_nonce: acfef_vars.cc_nonce,
			payment_card: card,
			payment_data: settings,
		};
		jQuery.post(acfef_vars.ajax_url, data, function(result) {
			if (!result.success) {
				addCreditCardError(result.data, form);
			}else{		
				addCreditCardSuccess(result.data, form);
			}
		});
	}
	
	function addCreditCardError( message, form ){
		form.find('.card-wrapper').append('<div class="acf-notice acf-error-message -error"><p>' + message + '</p></div>');
		form.find('.acf-spinner').css( 'display', 'none' );
		form.find("button[type=submit]").attr('disabled',false).css('display', 'block');
	}
	function addCreditCardSuccess( message, form ){
		var widget = e('.elementor-element-' + form.data('widget'));
		widget.find('.acf-form-submit').prepend('<div class="acf-notice acf-success-message payment-success"><p>' + message + '</p></div>').find('.acfef-submit-button').attr('disabled',false).removeClass('acf-hidden');
		form.addClass('acf-hidden');
	}


	e(".cc-purchase").submit(function (event) {
		event.preventDefault();
		var form = e(this);
		var settings = e('.elementor-element-' + form.data('widget')).data('settings');

		settings['amount'] = form.find('.payment-amount').html();
		form.find("button[type=submit]").attr('disabled',true).css('display', 'none');
		form.find('.acf-spinner').css( 'display', 'inline-block' );
		form.find('.acf-error-message').remove();
		
		if(settings.payment_processor == 'stripe'){
			if(acfef_vars.stripe_spk == ''){
				addCreditCardError('Could not connect to Stripe. Please check your API keys.', form);
			}
			if(typeof Stripe != 'undefined'){
				Stripe.setPublishableKey(acfef_vars.stripe_spk);
				if(form.find("input[name=stripeToken]").length == 0){
					Stripe.card.createToken(form, function(event, result){
						if (result.error) {
							// Inform the customer that there was an error.
							addCreditCardError(result.error.message, form);
						}else{					
							requestHandler(settings, result.id, form);
						}
					});
				}
			} 
		}
		if(settings.payment_processor == 'paypal'){

			card = {
				number: form.find("input.number").val(),
				name: form.find("input.name").val(),
				expiry: form.find("input.exp").val(),
				cvv: form.find("input.cvc").val(),
			}

			var cardValid = true;
			e.each( card, function( key, value ){
				if(value == ''){
					addCreditCardError('The ' + key + ' field is required', form);
					cardValid = false;
					return false;
				}	
			});

			if(cardValid){
				requestHandler(settings, card, form);
			}	
		}
	});
});