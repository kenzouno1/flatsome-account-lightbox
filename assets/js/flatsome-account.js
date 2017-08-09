jQuery(document).ready(function($) {
    $('#login-form-popup').find('#username, #password,#email, #reg_email, #reg_password').prop('required', true);
    $('#login-form-popup form').parsley({
        trigger: 'change',
        errorClass: 'form-error',
        successClass: "has-success",
        errorsWrapper: '<div class="error-msg"></div>',
        errorTemplate: '<span></span>',
    });
    $('#login-form-popup .login').submit(function(event) {
        event.preventDefault();
        var form = $(this);
        var formData = $(this).serialize() + '&action=fs_login';
        $.ajax({
                url: flatsomeVars.ajaxurl,
                type: 'POST',
                data: formData,
                dataType: 'json'
            })
            .done(function(response) {
                if (response.loggedin == true) {
                    var html = '<p class="sucess-msg"><span>' + response.message + '</span></p>';
                    form.find('.error-msg').remove();
                    form.find('#password').closest('.form-row').after(html);
                    location.reload();
                } else {
                    var html = '<p class="error-msg"><span>' + response.message + '</span></p>';
                    form.find('.error-msg').remove();
                    form.find('#password').closest('.form-row').after(html);
                    form.find('#password, #username').addClass('form-error');
                }
            });
    });
    $('#login-form-popup .register').submit(function(event) {
        event.preventDefault();
        var form = $(this);
        var formData = $(this).serialize() + '&action=fs_register';
        $.ajax({
                url: flatsomeVars.ajaxurl,
                type: 'POST',
                data: formData,
                dataType: 'json'
            }).done(function(response) {
                if (response.loggedin == true) {
                	var html = '<p class="sucess-msg"><span>' + response.message + '</span></p>';
                    form.find('.error-msg').remove();
                    form.find('#reg_password').closest('.form-row').after(html);
                    location.reload();
                } else {
                    var html = '<p class="error-msg"><span>' + response.message + '</span></p>';
                    form.find('.error-msg').remove();
                    form.find('#reg_password').closest('.form-row').after(html);
                }
            });
    });
});