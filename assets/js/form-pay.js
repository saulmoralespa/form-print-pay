jQuery(document).ready(function(){
    jQuery('#form-print-pay').submit(function (e) {
        jQuery('input[name=url_form]').val(window.location);
        e.preventDefault();
        jQuery.ajax({
            data : jQuery(this).serialize() + '&action=fpp_form_print_pay&form_print=pay',
            type: 'post',
            url: fpp_form_print_pay.ajaxurl,
            beforeSend : function(){
                jQuery('div.overlay-form-print-pay').show();
                jQuery('div.overlay-form-print-pay div.overlay-content-form-print-pay').append("<p><strong>"+fpp_form_print_pay.loading+"</strong></p>");
            },
            success: function(r) {
                var obj = JSON.parse(r);
                if (obj.status == true){
                    jQuery('div.overlay-form-print-pay div.overlay-content-form-print-pay p strong').html('');
                    jQuery('div.overlay-form-print-pay div.overlay-content-form-print-pay p strong').html(fpp_form_print_pay.message_paypal);
                    window.location.replace(obj.url);
                }
            },
            error: function(x, s, e) {
                jQuery('div.overlay-form-print-pay div.overlay-content-form-print-pay').html(x.responseText + s.status + e.error);
            }
        });
    });

    if( jQuery('form#checkstatuspaypal').length )
    {
            jQuery.ajax({
                data : jQuery('form#checkstatuspaypal').serialize() + '&action=fpp_form_print_pay',
                type: 'post',
                url: fpp_form_print_pay.ajaxurl,
                beforeSend : function(){
                    jQuery('div.overlay-form-print-pay').show();
                    jQuery('div.overlay-form-print-pay div.overlay-content-form-print-pay').append("<p><strong>Revisando estado de la transacción...</strong></p>");
                },
                success: function(r) {
                    var obj = JSON.parse(r);
                    if (obj.status !== false && obj.status === 'pending'){
                        jQuery('div.overlay-form-print-pay').hide();
                        jQuery('div.messagetransaction p strong').html('El estado de la transacción es pendiente. Transacción id: ('+obj.transactionid+'). Le notificaremos el cambio de estado a ' +obj.email+'');

                    }else if(obj.status === 'completed' && obj.pdf && ob.email !== null) {
                        jQuery('div.overlay-form-print-pay').hide();
                        jQuery('div.messagetransaction p strong').html('El estado de la transacción es completado. Transacción id: ('+obj.transactionid+'). El documento impreso a sido enviado a ' +obj.email+'');
                    }else if (obj.status === 'completed' && ob.email === null){
                        jQuery('div.messagetransaction p strong').html('El estado de la transacción es completado. Transacción id: ('+obj.transactionid+'). El documento impreso no ha sido enviado a ' +obj.email+'');
                    }else if(obj.status === 'error'){
                        jQuery('div.overlay-form-print-pay').hide();
                        jQuery('div.messagetransaction p strong').html('Error generado: '+obj.message+' ');
                    }else{
                        jQuery('div.overlay-form-print-pay').hide();
                        jQuery('div.messagetransaction p strong').html('Errores tecnicos, requiera ayuda un desarrollador web info@saulmoralespa.com: '+obj.message+' ');
                    }
                },
                error: function(x, s, e) {
                    jQuery('div.overlay-form-print-pay div.overlay-content-form-print-pay').html(x.responseText + s.status + e.error);
                }
            });
    }

    jQuery('form#form-print-pay select').click(function(e){


        if(jQuery(this).children().size() > 1){
            return;
        }

        var data = jQuery(this).data('select-print');
        var array = data.split(',');

        for (option of  array){
            jQuery(this).append('<option value="'+option+'">'+jsUcfirst(option)+'</option>');
        }


    });

    function jsUcfirst(string)
    {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

});