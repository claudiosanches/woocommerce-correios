jQuery(document).ready(function($) {
    correios_adddays = $('.form-table:eq(0) tr:eq(7)');
    correios_login = $('.form-table:eq(1) tr:eq(1)');
    correios_password = $('.form-table:eq(1) tr:eq(2)');
    correios_esedex = $('.form-table:eq(1) tr:eq(7)');

    correios_adddays.hide();
    correios_login.hide();
    correios_password.hide();
    correios_esedex.hide();

    var correios_select = $('#woocommerce_correios_corporate_service');
    var correios_val = correios_select.val();
    var display_date = $('#woocommerce_correios_display_date');

    function addtionalDaysDisplay() {

        if ( display_date.is(':checked') ) {
            correios_adddays.show();
        } else {
            correios_adddays.hide();
        }
    }
    addtionalDaysDisplay();

    display_date.on('click', function() {
        addtionalDaysDisplay();
    });

    function correiosActive(correios) {
        if (correios == 'corporate') {
            correios_login.show();
            correios_password.show();
            correios_esedex.show();
        } else {
            correios_login.hide();
            correios_password.hide();
            correios_esedex.hide();
        }
    }
    correiosActive(correios_val);

    correios_select.change(function() {
        var service = $(this).val();
        correiosActive(service);
    });
});