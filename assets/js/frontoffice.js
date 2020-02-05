jQuery(document).ready( function($){
    "use strict";

    var $body = $('body'),
        $add_to_cart_el = $('.mwrq-quote-button'),
        $widget = $(document).find('.widget_mwrq_list_quote'),
        ajax_loader    = ( typeof mwrq_frontend !== 'undefined' ) ? mwrq_frontend.block_loader : false,
        $remove_item = $('.motif-mwrq-item-remove'),
        $shipping_form = $('.shipping_address'),
        $billing_form = $('.woocommerce-billing-fields'),
        url = document.location.href,
        table =  $(document).find('#motif-mwrq-table-list');

    if( table.length > 0 ){
        $.post( url, function(data){
            if ( data != '' ){
                var c =$("<div></div>").html( data ),
                    table = c.find('#motif-mwrq-table-list');
                $('#motif-mwrq-table-list').html( table.html() );
                $(document).trigger('mwrq_table_reloaded');
            }
        });
    }

    var xhr = false;
	$(document).on( 'click' ,'.mwrq-quote-button', function(e){
        e.preventDefault();

        var $t = $(this),
            $t_wrap = $t.closest('.motif-mwrq-quote'),
            add_to_cart_info = 'ac',
            $cart_form = '';

        if( $('.grouped_form').length ) {
            var qtys = 0;
            $('.grouped_form input.qty').each(function () {
                qtys = Math.floor($(this).val() )+ qtys;
            });
            if ( qtys == 0 ) {
                alert(mwrq_frontend.select_quanitity);
                return;
            }
        }

        if( $t.closest( '.cart' ).length ){
            $cart_form = $t.closest( '.cart' );
        }else if( $t_wrap.siblings( '.cart' ).first().length ) {
            $cart_form = $t_wrap.siblings( '.cart' ).first();
        }else if( $('.composite_form').length ){
            $cart_form = $('.composite_form') ;
        }
        else {
            $cart_form = $('.cart:not(.in_loop)'); // not(in_loop) for color and label
        }

        if ( typeof $cart_form[0] !== 'undefined' && typeof $cart_form[0].checkValidity === 'function' && !$cart_form[0].checkValidity()) {
            // If the form is invalid, submit it. The form won't actually submit;
            // this will just cause the browser to display the native HTML5 error messages.
            $('<input type="submit">').hide().appendTo($cart_form).click().remove();
            return;
        }

        if ( $t.closest('ul.products').length > 0) {
            var $add_to_cart_el = '',
                $product_id_el = $t.closest('li.product').find('a.add_to_cart_button'),
                $product_id_el_val = $product_id_el.data( 'product_id' );
        }else{
            var $add_to_cart_el = $t.closest('.product').find('input[name="add-to-cart"]'),
                $product_id_el = $t.closest('.product').find('input[name="product_id"]'),
                $product_id_el_val = $product_id_el.length ? $product_id_el.val() : $add_to_cart_el.val();
        }

        var prod_id = ( typeof $product_id_el_val == 'undefined') ? $t.data('product_id') : $product_id_el_val;

        add_to_cart_info = $cart_form.serializefiles();

        add_to_cart_info.append('context','frontend');
        add_to_cart_info.append('action','motif_mwrq_action');
        add_to_cart_info.append('mwrq_action','add_item');
        add_to_cart_info.append('product_id',$t.data('product_id'));
        add_to_cart_info.append('wp_nonce',$t.data('wp_nonce'));
        add_to_cart_info.append('motif-add-to-cart',$t.data('product_id'));
        var quantity = $t_wrap.find('input.qty').val();

        if ( quantity > 0) {
            add_to_cart_info.append('quantity', quantity);
        }

        $(document).trigger( 'motif_mwrq_action_before' );

        xhr = $.ajax({
            type   : 'POST',
            url    : mwrq_frontend.ajaxurl.toString().replace( '%%endpoint%%', 'motif_mwrq_action' ),
            dataType: 'json',
            data   : add_to_cart_info,
            contentType: false,
            processData: false,
            beforeSend: function(){
                $t.after( ' <img src="'+ajax_loader+'" class="mwrq-loader" >' );
            },
            complete: function(){
                $t.next().remove();
            },

            success: function (response) {


                if( response.result == 'true' || response.result == 'exists'){

                    if( mwrq_frontend.go_to_the_list == 'yes' ){
                        window.location.href = response.rqa_url;
                    }else{

                        $('.motif_mwrq_add_item_product-response-' + prod_id).show().removeClass('hide').html( response.message );
                        $('.motif_mwrq_add_item_browse-list-' + prod_id).show().removeClass('hide');
                        $t.parent().hide().removeClass('show').addClass('addedd');
                        $('.add-to-quote-'+ prod_id).attr('data-variation', response.variations );

                        if( $widget.length ){
                            $widget.mwrq_refresh_widget();
                            $widget = $(document).find('.widget_mwrq_list_quote');
                        }

                        mwrq_refresh_number_items();
                    }

                    $(document).trigger( 'motif_mwrq_added_successfully', [response] );

                }else if( response.result == 'false' ){
                    $('.motif_mwrq_add_item_response-' + prod_id ).show().removeClass('hide').html( response.message );

                    $(document).trigger( 'motif_mwrq_error_while_adding' );
                }
                xhr = false;
            }
        });

    });

    $.fn.mwrq_refresh_widget = function () {
        $widget.each(function () {
            var $t = $(this),
                $wrapper_list = $t.find('.motif-mwrq-list-wrapper'),
                $list = $t.find('.motif-mwrq-list'),
                data_widget = $t.find('.motif-mwrq-list-widget-wrapper').data('instance');

            $.ajax({
                type      : 'POST',
                url       : mwrq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'motif_mwrq_action'),
                data      : data_widget + '&mwrq_action=refresh_quote_list&action=motif_mwrq_action&context=frontend',
                beforeSend: function () {
                    $list.css('opacity', 0.5);
                    if ($t.hasClass('widget_mwrq_list_quote')) {
                        $wrapper_list.prepend(' <img src="' + ajax_loader + '" class="mwrq-loader">');
                    }
                },
                complete  : function () {
                    if ($t.hasClass('widget_mwrq_list_quote')) {
                        $wrapper_list.next().remove();
                    }
                    $list.css('opacity', 1);
                },
                success   : function (response) {
                    if ($t.hasClass('widget_mwrq_mini_list_quote')) {
                        $t.find('.motif-mwrq-list-widget-wrapper').html(response.mini);
                    } else {
                        $t.find('.motif-mwrq-list-widget-wrapper').html(response.large);
                    }
                }
            });
        });
    };

        /*Remove an item from rqa list*/
    $(document).on('click', '.motif-mwrq-item-remove', function (e) {

        e.preventDefault();

        var $t = $(this),
            key = $t.data('remove-item'),
            wrapper = $t.parents('.mwrq-wrapper'),
            form = $('#motif-mwrq-form'),
            cf7 = wrapper.find('.wpcf7-form'),
            remove_info = '';

        remove_info = 'context=frontend&action=motif_mwrq_action&mwrq_action=remove_item&key=' + $t.data('remove-item') + '&wp_nonce=' + $t.data('wp_nonce') + '&product_id=' + $t.data('product_id');

        $.ajax({
            type      : 'POST',
            url       : mwrq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'motif_mwrq_action'),
            dataType  : 'json',
            data      : remove_info,
            beforeSend: function () {
                $t.find('.ajax-loading').css('visibility', 'visible');
            },
            complete  : function () {
                $t.siblings('.ajax-loading').css('visibility', 'hidden');
            },
            success: function (response) {
                if (response === 1) {
                    var $row_to_remove = $("[data-remove-item='" + key + "']").parents('.cart_item');

                    $row_to_remove.remove();

                    if ($('.cart_item').length === 0) {

                        if (cf7.length) {
                            cf7.remove();
                        }

                        $('#motif-mwrq-form, .motif-mwrq-mail-form-wrapper').remove();
                        $('#motif-mwrq-message').html(mwrq_frontend.no_product_in_list);
                    }
                    if ($widget.length) {
                        $widget.mwrq_refresh_widget();
                        $widget = $(document).find('.widget_mwrq_list_quote');
                    }

                    mwrq_refresh_number_items();

                    $(document).trigger( 'motif_mwrq_removed_successfully' );
                }
                else{
                    $(document).trigger( 'motif_mwrq_error_while_removing' );
                }
            }
        });
    });

    // quantity change update
    var request;
    $(document).on( 'click, change', '.product-quantity input', function(e){

        if( typeof request !== 'undefined' ){
            request.abort();
        }

        var $t = $(this),
            name = $t.attr('name');

        if( typeof name ==   'undefined'){
            var $input_quantity = $t.closest('.product-quantity').find('.input-text.qty'),
                name = $input_quantity.attr('name'),
                value = $input_quantity.val(),
                item_keys = name.match(/[^[\]]+(?=])/g);

            if( $t.hasClass('plus') ){
                value ++;
            }

            if( $t.hasClass('minus') ){
                value --;
            }
            var request_info = 'context=frontend&action=motif_mwrq_action&mwrq_action=update_item_quantity&quantity='+value+'&key='+item_keys[0];

        }else{
            var value = $t.val(),
                item_keys = name.match(/[^[\]]+(?=])/g),
                request_info = 'context=frontend&action=motif_mwrq_action&mwrq_action=update_item_quantity&quantity='+value+'&key='+item_keys[0];

        }
        request = $.ajax({
            type   : 'POST',
            url    : mwrq_frontend.ajaxurl.toString().replace( '%%endpoint%%', 'motif_mwrq_action' ),
            dataType: 'json',
            data   : request_info,
            success: function ( response ) {
                $.post( url, function(data){
                    if ( data != '' ){
                        var c =$("<div></div>").html( data ),
                            table = c.find('#motif-mwrq-list');
                        $('#motif-mwrq-list').html( table.html() );
                        $(document).trigger('mwrq_table_reloaded');
                    }
                });
            }
        });

    });

    $.fn.serializefiles = function() {
        var obj = $(this);
        var formData = new FormData();
        $.each($(obj).find("input[type='file']"), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });

        var params = $(obj).serializeArray();

        var quantity_in = false;
        $.each(params, function (i, val) {
            if( val.name == 'quantity' || val.name.indexOf("quantity")){
                quantity_in = true;
            }

            if( val.name != 'add-to-cart'){
                formData.append(val.name, val.value);
            }
        });

        if( quantity_in === false ){
            formData.append('quantity', 1 );
        }
        return formData;
    };

    function mwrq_refresh_number_items (){
        var $number_items = $(document).find('.mwrq_number_items');
        $number_items.each( function(){
            var $t = $(this),
                show_url= $t.data('show_url'),
                item_name = $t.data('item_name'),
                item_plural_name= $t.data('item_plural_name');

            $.ajax({
                type      : 'POST',
                url       : mwrq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'motif_mwrq_action'),
                data      : 'mwrq_action=refresh_number_items&action=motif_mwrq_action&context=frontend&item_name='+item_name+'&item_plural_name='+ item_plural_name + '&show_url=' + show_url,
                success   : function (response) {
                    $t.replaceWith( response );
                }
            });
        });
    };

    $('#motif-mwrq-list').on('change', '.qty', function() {
        var qty = $(this).val();
        if (qty <= 0) {
            $(this).val(1);
        }
    });

});

// ---------------------------------------------------------------------------------------------------
// --------------------------form submisstion
// ---------------------------------------------------------------------------------------------------

jQuery(document).ready(function ($) {
    "use strict";

    $(document).on('submit', 'form#motif-mwrq-request-form', function (e) {
        e.preventDefault();

        var mwrq_default_form = $('form[name="motif-mwrq-request-form"]'),
            submit_button = $('.raq-send-request'),
            ajax_loader    = ( typeof mwrq_frontend !== 'undefined' ) ? mwrq_frontend.block_loader : false;

        var data = mwrq_default_form.mwrq_serialize_files();

        data.append('context', 'frontend');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: data,
            contentType: false,
            processData: false,
            url: mwrq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'mwrq_submit_default_form'),
            beforeSend: function () {
                submit_button.prop('disabled', true).after(' <img src="' + ajax_loader + '" class="mwrq-loader" >');
            },
            complete: function () {
                submit_button.prop('disabled', false).next().remove();
            },
            success: function (response) {
                if ('success' === response.result) {
                    window.location.href = response.redirect;
                }
                // if ('failure' === response.result) {

                //     // Remove notices from all sources
                //     $('.woocommerce-error, .woocommerce-message').remove();

                //     // Add new errors returned by this event
                //     if (response.messages) {
                //         mwrq_default_form.prepend('<div class="woocommerce-error woocommerce-message">' + response.messages + '</div>');
                //     } else {
                //         mwrq_default_form.prepend(response);
                //     }

                //     // Lose focus for all fields
                //     mwrq_default_form.find('.input-text, select, input:checkbox').trigger('validate').blur();
                //     scroll_to_notices();
                //     if (typeof grecaptcha != "undefined") {
                //         grecaptcha.reset( rqa_captcha );
                //     }
                // }
            }
        });

        return false;

    });

    $.fn.mwrq_serialize_files = function () {
        var obj = $(this);
        var formData = new FormData();
        $.each($(obj).find("input[type='file']"), function (i, tag) {
            $.each($(tag)[0].files, function (i, file) {
                formData.append(tag.name, file);
            });
        });

        var params = $(obj).serializeArray();

        $.each(params, function (i, val) {
            if (val.name) {
                formData.append(val.name, val.value);
            }
        });

        return formData;
    };


});