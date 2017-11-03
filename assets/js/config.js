jQuery(document).ready(function(){
    var template = jQuery("#template tr:nth-child(4)").html();
    jQuery("#template").on("click", '[data-action="remove"]', function(){
        jQuery(this).parent().parent().remove();
        return false;
    });
    jQuery("#new-field").click(function(){
        jQuery("#template").append("<tr>"+template+"</tr>");
        jQuery("#template tr:last input").val('');
        jQuery("#template tr:last input").removeAttr('readonly');
    });

    jQuery('div.statusOrder-form select[name=statusorder]').on('change', function() {
        if ( this.value !== ''){
            var cofirm = window.confirm('¿ Seguro, del estado de la transacción en su cuenta de paypal es completado ?');
        }
        if (cofirm){
            jQuery.ajax({
                data : jQuery(this).parents('form').serialize() + '&action=fpp_form_print_pay',
                type: 'post',
                url: ajaxurl,
                beforeSend : function(){
                    jQuery('div.overlay-form-print-pay').show();
                },
                success: function(r) {
                    jQuery('div.overlay-form-print-pay').hide();
                    var obj = JSON.parse(r);
                    if(obj.status){
                        window.location.reload(true);
                    }else{
                        alert('Orden sin completar porque no se pudo enviar email');
                    }
                },
                error: function(x, s, e) {
                    console.log(x.responseText + s.status + e.error);
                }
            });
        }
    });

    jQuery.fn.swap = function(b){
        b = jQuery(b)[0];
        var a = this[0];
        var t = a.parentNode.insertBefore(document.createTextNode(''), a);
        b.parentNode.insertBefore(a, b);
        t.parentNode.insertBefore(b, t);
        t.parentNode.removeChild(t);
        return this;
    };

    jQuery('form#form-print-pay').submit(function(e) {
        e.preventDefault();
        jQuery.ajax({
            data : jQuery(this).serialize() + '&action=fpp_form_print_pay',
            type: 'post',
            url: ajaxurl,
            beforeSend : function(){
                jQuery('div.overlay-form-print-pay').show();
                console.log('Enviando datos');
            },
            success: function(r) {
                jQuery('div.overlay-form-print-pay').hide();
                console.log(r);
            },
            error: function(x, s, e) {
                console.log(x.responseText + s.status + e.error);
            }
        });
    });

    jQuery('button#save_text_form_pdf').click(function() {
        jQuery.ajax({
            data : jQuery('textarea[name=text_form_print_pdf]').serialize() + '&' + jQuery('input[name=idpost]').serialize() + '&action=fpp_form_print_pay',
            type: 'post',
            url: ajaxurl,
            beforeSend : function(){
                jQuery('div.overlay-form-print-pay').show();
                console.log('Enviando datos');
            },
            success: function(r) {
                jQuery('div.overlay-form-print-pay').hide();
                console.log(r);
            },
            error: function(x, s, e) {
                console.log(x.responseText + s.status + e.error);
            }
        });
    });

    jQuery('#form-print-paypal').submit(function (e) {
        e.preventDefault();
        jQuery.ajax({
            data : jQuery(this).serialize() + '&action=fpp_form_print_pay',
            type: 'post',
            url: ajaxurl,
            beforeSend : function(){
                jQuery('div.overlay-form-print-pay').show();
            },
            success: function(r) {
                jQuery('div.overlay-form-print-pay').hide();
                console.log(r);
            },
            error: function(x, s, e) {
                console.log(x.responseText + s.status + e.error);
            }
        });
    });

    jQuery(document).on('change','select[data-fom-print="data"]',function(){
        if( this.value === 'select' ){;
            var values = prompt("inserte los valores del select, seperados por coma", "mujer,hombre");

            var coma = values.replace(/\s/g, '');

            var text = coma.includes(",");

            if (values !== '' && text){
                jQuery(this).closest('tr').find('td:nth-child(4) input').val(values);
            }else if(text === false){
                alert('Valores deben estar separados por coma ejemplo: mujer,hombre,bruto');
            }
        }else{
            jQuery(this).closest('tr').find('td:nth-child(4) input').val('');
        }
    });


    jQuery('#submit_form_print').click(function (e) {
        e.preventDefault();

        $price = jQuery('input#price_form_print').val();
        $description = jQuery('input#description_form_print').val();

        if ($price === '' || $description === ''){
            alert('precio y descripción del producto requeridos');
            return;
        }

        jQuery.ajax({
            data : jQuery('[data-fom-print="data"]').serialize() + '&action=fpp_form_print_pay',
            type: 'post',
            url: ajaxurl,
            beforeSend : function(){
                jQuery('div.overlay-form-print-pay').show();
            },
            success: function(r) {
                jQuery('div.overlay-form-print-pay').hide();
                var obj = JSON.parse(r);
               jQuery('input[name=post_title]').focus();
               jQuery('input[name=post_title]').blur();
               jQuery('input[name=post_title]').val('[fpp_form_print_pay id="'+obj.id+'"]');

                if (jQuery('div#text_pdf_form_print').is(":visible"))
                {
                    jQuery( "button#save_text_form_pdf" ).trigger( "click" );
                }else{
                    jQuery('div#text_pdf_form_print').show();
                }

                var availableTags = obj.fielname;
                console.log(availableTags);
                autocomplete(availableTags);
            },
            error: function(x, s, e) {
                console.log(x.responseText + s.status + e.error);
            }
        });
    });
    jQuery('form#formemail-print-paypal').submit(function (e) {
        e.preventDefault();

        var email = jQuery('input[name=adminemail]').val();
        var count = (email.match(/@/g) || []).length;
        if (count === 0){
            alert('Debe ingresar al menos un email');
            jQuery('input[name=adminemail]').focus();
            return;
        }else if(count > 1 && email.includes(",") === false){
            alert('Los email debe estar separados por comas. Ejemplo: (adminbruto@domain.com,username@domain.com)');
            jQuery('input[name=adminemail]').focus();
            return;
        }else if(count > 3){
            alert('Con tres que coloques más que suficiente, no abuses del spam.');
            return;
        }

        jQuery.ajax({
            data : jQuery(this).serialize() + '&action=fpp_form_print_pay',
            type: 'post',
            url: ajaxurl,
            beforeSend : function(){
                jQuery('div.overlay-form-print-pay').show();
                console.log('Enviando datos');
            },
            success: function(r) {
                console.log(r);
                jQuery('div.overlay-form-print-pay').hide();
            },
            error: function(x, s, e) {
                console.log(x.responseText + s.status + e.error);
            }
        });
    });
    jQuery('input[name="logo-pdf-form-print"]').on('change',function(e){
            e.preventDefault();

        var _URL = window.URL || window.webkitURL;
        var file = jQuery(this).val();
        var valImg = false;
        var reg = /(.*?)\.(jpg|jpeg)$/;
        var valuefile;
        var fd = new FormData();
        var imgwidth = 0;
        var imgheight = 0;
        var maxwidth = 150;
        var maxheight = 100;

        if ((valuefile = this.files[0])){
            img = new Image();
            img.onload = function () {
                imgwidth = this.width;
                imgheight = this.height;
                if (imgwidth <= maxwidth && imgheight <= maxheight){
                    loadAjaxFile(true);
                }else{
                    loadAjaxFile(false);
                }
            };
            img.src = _URL.createObjectURL(valuefile);
        }

        function loadAjaxFile(bool){
            valImg = bool;
            if (valImg && file.match(reg)){

                fd.append('logo-pdf-form-print', valuefile);
                fd.append('action', 'fpp_form_print_pay');
                fd.append('value', file);

                jQuery.ajax({
                    data: fd,
                    type: 'post',
                    contentType: false,
                    processData: false,
                    url: ajaxurl,
                    beforeSend : function(){
                        jQuery('div.overlay-form-print-pay').show();
                        jQuery('div.overlay-form-print-pay div.message strong').html('Subiendo imágen...');
                    },

                    success: function(r) {
                        jQuery('div.overlay-form-print-pay div.message strong').html('');
                        jQuery('div.overlay-form-print-pay').hide();
                        var obj = JSON.parse(r);
                        if (obj.status === false){
                            alert(obj.message);
                            jQuery('div#config-pdf tbody#logo-pdf th').append('<strong style="color:red;"> '+obj.message+'</strong>');
                        }else{
                            window.location.reload(true);
                        }

                    },
                    error: function(x, s, e) {
                        console.log(x.responseText + s.status + e.error);
                    }
                });
            }else if (!file.match(reg)){
                jQuery('div#config-pdf tbody#logo-pdf th').append('<strong style="color:red;"> Formato de imágen debe ser jpg</strong>');
            }else if (!valImg){
                jQuery('div#config-pdf tbody#logo-pdf th').append('<strong style="color:red;"> La imagen debe tener un tamaño máximo de '+ maxwidth + ' y de alto ' + maxheight + '</strong>');
            }
        }


    });

    jQuery('form#forpdf-print-paypal').submit(function (e) {
        e.preventDefault();
        jQuery.ajax({
            data : jQuery(this).serialize() + '&action=fpp_form_print_pay',
            type: 'post',
            url: ajaxurl,
            beforeSend : function(){
                jQuery('div.overlay-form-print-pay').show();
            },

            success: function(r) {
                jQuery('div.overlay-form-print-pay').hide();
                console.log(r);

            },
            error: function(x, s, e) {
                console.log(x.responseText + s.status + e.error);
            }
        });

    });

    function autocomplete(tags){

        jQuery('textarea#text_form_print_pdf').autocomplete({
            source: function( request, response ) {
                // delegate back to autocomplete, but extract the last term
                response( jQuery.ui.autocomplete.filter(
                    tags, extractLast( request.term ) ) );
            },
            select: function( event, ui ) {
                var terms = split( this.value );
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push( ui.item.value );
                // add placeholder to get the comma-and-space at the end
                terms.push( "" );
                this.value = terms.join( " " );
                return false;
            }
        });
    }
    function split( val ) {
        var split = [/ /, /\r?\n/];
        return val.split( new RegExp('[' + split.join('') + ']', 'g') );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }
    if(typeof fpp_form_print_pay != 'undefined'){
        var tags = fpp_form_print_pay.field_name;
        autocomplete(tags);
    }
});