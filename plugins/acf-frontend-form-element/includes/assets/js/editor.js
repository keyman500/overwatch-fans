
  
jQuery('body').on('click', '.sub-fields-open', function() {
    var type = jQuery(this).data('type');
    var modal = jQuery('<div id="modal_'+type+'" class="modal edit-modal show" data-clear="1"><div class="modal-content" style="width:100%;margin:25px auto;text-align:center"><div class="modal-inner"><div class="content-container"><button class="sub-fields-close" type="button" data-type="attribute"><span class="close-modal-text">Close</span></button></div></div></div></div>');

    $parent_section = jQuery(this).parents('.elementor-control-fields_selection');
    jQuery('#elementor-panel-content-wrapper').prepend(modal);


    $subfields_section = $parent_section.siblings('.elementor-control-'+type+'_fields');
    $subfields_section.css('display','block');

    jQuery('#modal_'+type+'').find('.content-container').prepend($subfields_section);
} );

jQuery('body').on('click','.sub-fields-close', function() {
    var type = jQuery(this).data('type');
    removeModal(type);
});


function removeModal(type){
    var $modal = jQuery('#modal_'+type); 
    $subfields_section = $modal.find('.elementor-control-'+type+'_fields');
    $subfields_section.css('display','none');

    $parent_section.after($subfields_section);
    $modal.remove();
}

        
 

 
  