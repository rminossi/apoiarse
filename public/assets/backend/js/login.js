$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    $('form[name="login"]').submit(function (event) {
        event.preventDefault();

        const form = $(this);
        const action = form.attr('action');
        const email = form.find('input[name="email"]').val();
        const password = form.find('input[name="password_check"]').val();
        $.post(action, {email: email, password: password}, function (response) {
            console.log(response)
            if(response.message) {
                ajaxMessage(response.message, 3)
            }
            if(response.redirect) {
                window.location.href = response.redirect;
            }
        }, 'json')
    })

    $('form[name="register"]').submit(function (event) {
        event.preventDefault();

        const form = $(this);
        const action = form.attr('action');
        const name = form.find('input[name="name"]').val();
        const email = form.find('input[name="email"]').val();
        const phone = form.find('input[name="phone"]').val();
        const cpf = form.find('input[name="cpf"]').val();
        const password = form.find('input[name="password"]').val();
        const password_confirmation = form.find('input[name="password_confirmation"]').val();
        $.post(action, {name: name, email: email, phone: phone, cpf: cpf, password: password, password_confirmation: password_confirmation}, function (response) {
            console.log(response)
            if(response.message) {
                ajaxMessage(response.message, 3)
            }
            if(response.redirect) {
                window.location.href = response.redirect;
            }
        }, 'json')
    })

    $('form[name="forgot_password"]').submit(function (event) {
        event.preventDefault();
        //desabilitar botão e mudar texto
        $(this).find('button').attr('disabled', 'disabled').text('Enviando...');

        const form = $(this);
        const action = form.attr('action');
        const email = form.find('input[name="email"]').val();
        $.post(action, {email: email}, function (response) {
            console.log(response)
            $(this).find('button').removeAttr('disabled').text('Email enviado');
            if(response.message) {
                ajaxMessage(response.message, 3)
            }
            if(response.redirect) {
                window.location.href = response.redirect;
            }
        }, 'json')
    })

    $('form[name="reset_password"]').submit(function (event) {
        event.preventDefault();
        const form = $(this);
        const action = form.attr('action');
        const token = form.find('input[name="token"]').val();
        const password = form.find('input[name="password"]').val();
        const password_confirmation = form.find('input[name="password_confirmation"]').val();
        $.post(action, {password: password, password_confirmation: password_confirmation, token: token}, function (response) {
            console.log(response)
            if(response.message) {
                ajaxMessage(response.message, 3)
            }
            if(response.redirect) {
                window.location.href = response.redirect;
            }
        }, 'json')
    })



    var ajaxResponseBaseTime = 3;
    function ajaxMessage(message, time) {
        var ajaxMessage = $(message);

        ajaxMessage.append("<div class='message_time'></div>");
        ajaxMessage.find(".message_time").animate({"width": "100%"}, time * 1000, function () {
            $(this).parents(".message").fadeOut(200);
        });

        $(".ajax_response").append(ajaxMessage);
    }
})
