jQuery(function($){

    $('.dcw-toggle-rule').on('change', function(){

        let checkbox = $(this);
        let ruleId = checkbox.data('rule-id');
        let enabled = checkbox.is(':checked') ? 1 : 0;

        $.post(ajaxurl, {
            action: 'dcw_toggle_rule',
            rule_id: ruleId,
            enabled: enabled,
            _ajax_nonce: dcw_ajax.nonce
        }).fail(function(){
            alert('Error saving');
            checkbox.prop('checked', !enabled); // откат
        });

    });


    function conditions() {
        let index = $('.dcw-condition-row').length;

        $('#dcw-add-condition').on('click', function(){

            let template = $('#dcw-condition-row-template').html();

            template = template.replace(/__name__/g, 'conditions[' + index + ']');

            $('.dcw-conditions').append(template);

            index++;
        });

        $(document).on('click', '.dcw-remove-condition', function(){
            $(this).closest('.dcw-condition-row').remove();
        });
    }

    conditions();

    function discounts() {
        let index = $('.dcw-discount-row').length;

        $('#dcw-add-discount').on('click', function(){

            let template = $('#dcw-discount-row-template').html();

            template = template.replace(/__name__/g, 'discounts[' + index + ']');

            $('.dcw-discounts').append(template);

            index++;
        });

        $(document).on('click', '.dcw-remove-discount', function(){
            $(this).closest('.dcw-discount-row').remove();
        });
    }

    discounts();

    function gifts(){

        let index = $('.dcw-gift-row').length;

        $('#dcw-add-gift').on('click', function(){

            let template = $('#dcw-gift-row-template').html();

            template = template.replace(/__name__/g, 'gifts[' + index + ']');

            $('.dcw-gifts').append(template);

            $(document.body).trigger('wc-enhanced-select-init');

            index++;
        });

        $(document).on('click', '.dcw-remove-gift', function(){
            $(this).closest('.dcw-gift-row').remove();
        });
    }

    gifts();


    function toggleDiscountTypeOptions(){
        $('#discount_type').change(function() {
            let selected_option = $(this).val();
            $('.discount_type_opts:not(.hidden)').addClass('hidden');

            $('.discount_type_opts.hidden.' + selected_option + '_opts').removeClass('hidden');
        });
    }

    toggleDiscountTypeOptions();


});