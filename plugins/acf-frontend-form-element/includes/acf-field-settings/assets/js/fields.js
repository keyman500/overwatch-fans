acf.add_action('ready_field/type=relationship', function( $el ){	
	$el.find('.acf-button.button-primary').on('click', function(){
				
        // popup
        popup = acf.newPopup({
            title: $el.attr('title'),
            loading: true,
            width: '300px'
        });
        // ajax
        var ajaxData = {
            action:		'acfef/fields/relationhip/add_post',
            field_key:	field.get('key')
        };
        
        // get HTML
        $.ajax({
            url: acf.get('ajaxurl'),
            data: acf.prepareForAjax(ajaxData),
            type: 'post',
            dataType: 'html',
            success: showForm
        });
    });
    
});

jQuery('.post-slug-field input').on('input', function() {
    var c = this.selectionStart,
        r = /[`~!@#$%^&*()|+=?;:..’“'"<>,€£¥•،٫؟»«\s\{\}\[\]\\\/]+/gi,
        v = jQuery(this).val();
    if(r.test(v)) {
      jQuery(this).val(v.replace(r,'').toLowerCase());
      c--;
    }
    this.setSelectionRange(c, c);
  }); 