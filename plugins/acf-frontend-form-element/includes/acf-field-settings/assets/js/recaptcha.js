function acfef_recaptcha(){
    
    (function($){
        
        if(typeof acf === 'undefined')
            return;
        
        /**
         * Field: reCaptcha (render)
         */
        $.each(acf.getFields({type: 'acfef_recaptcha'}), function(i, field){
            
            field.render();
            
        });
    
    })(jQuery);
    
}

(function($){
    
    if(typeof acf === 'undefined')
        return;

    /**
     * Field: reCaptcha
     */
    var reCaptcha = acf.Field.extend({

        type: 'acfef_recaptcha',

        actions: {
            'validation_failure' : 'validationFailure'
        },

        $control: function(){
            return this.$('.acfef-field-recaptcha');
        },

        $input: function(){
            return this.$('input[type="hidden"]');
        },

        $selector: function(){
            return this.$control().find('> div');
        },

        selector: function(){
            return this.$selector()[0];
        },

        version: function(){
            return this.get('version');
        },

        render: function(){

            var field = this;

            if(this.version() === 'v2'){

                this.recaptcha = grecaptcha.render(field.selector(), {
                    'sitekey':  field.$control().data('site-key'),
                    'theme':    field.$control().data('theme'),
                    'size':     field.$control().data('size'),


                    'callback': function(response){

                        field.$input().val(response).change();
                        field.$input().closest('.acf-input').find('> .acf-notice.-error').hide();

                    },

                    'error-callback': function(){

                        field.$input().val('error').change();

                    },

                    'expired-callback': function(){

                        field.$input().val('expired').change();

                    }
                });

            }

            else if(this.version() === 'v3'){

                grecaptcha.ready(function(){
                    grecaptcha.execute(field.$control().data('site-key'), {action: 'homepage'}).then(function(response){

                        field.$input().val(response).change();
                        field.$input().closest('.acf-input').find('> .acf-notice.-error').hide();

                    });
                });

            }

        },

        validationFailure: function($form){

            if(this.version() === 'v2'){

                grecaptcha.reset(this.recaptcha);

            }

        }

    });

    acf.registerFieldType(reCaptcha);
})(jQuery);
