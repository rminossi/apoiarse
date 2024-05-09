$(function () {

    // MOBILE MENU
    $(".mobile_menu").click(function (event) {
        event.preventDefault();

        $(".dash_sidebar").animate({left: "0"}, 200).addClass("mobile_menu_open");
        $("body").addClass("mobile_body");

        $("html").on("click touchstart", ".mobile_body", function (e) {
            var menu = $(".dash_sidebar");
            if (menu.hasClass('mobile_menu_open') && !$(e.target).hasClass('mobile_menu')) {
                if (!$(e.target).hasClass('dash_sidebar') && !$(e.target).parents().hasClass('dash_sidebar')) {
                    menu.animate({left: '-260'}, 200).removeClass('mobile_menu_open');
                }
            }
        });
    });

    // SEARCH CONTENT
    $('.search_open').click(function (event) {
        event.preventDefault();

        $(".dash_content_search").animate({right: "0"}, 200).addClass("search_open");
        $("body").addClass("search");

        $("html").on("click touchstart", ".search", function (e) {
            var search = $(".dash_content_search");
            if (search.hasClass('search_open') && !$(e.target).hasClass('search_open')) {
                if (!$(e.target).hasClass('dash_content_search') && !$(e.target).parents().hasClass('dash_content_search')) {
                    search.animate({right: '-320px'}, 200).removeClass('search_open');
                }
            }
        });

        $("html").on("click", ".search_close", function (eventOther) {
            eventOther.preventDefault();
            $(".dash_content_search").animate({right: '-320px'}, 200).removeClass('search_open');
        });
    });

    // COLLAPSE COMPONENT
    $('.collapse').click(function (event) {
        event.preventDefault();

        var collapse = $(this).closest('.app_collapse');

        $(collapse).find('.app_collapse_header > span').toggleClass('icon-plus-circle').toggleClass('icon-minus-circle');
        $(collapse).find('.app_collapse_content').slideToggle(200, function () {
            if ($(this).hasClass('d-none')) {
                $(this).removeClass('d-none');
            }
        });
    });

    // COMPONENT TABS
    $(".nav_tabs_item_link").click(function (event) {
        event.preventDefault();

        var targetTab = $(this).attr('href');
        var componentNav = $(this).closest('.nav');

        $(componentNav).find('.nav_tabs_item .active').removeClass('active');

        $(this).addClass('active');

        $(componentNav).find('.nav_tabs_content').children().hide();

        $(componentNav).find(targetTab).show();
    });

    // AJAX RESPONSE
    var ajaxResponseBaseTime = 3;

    function ajaxMessage(message, time) {
        var ajaxMessage = $(message);

        ajaxMessage.append("<div class='message_time'></div>");
        ajaxMessage.find(".message_time").animate({"width": "100%"}, time * 1000, function () {
            $(this).parents(".message").fadeOut(200);
        });

        $(".ajax_response").append(ajaxMessage);
    }

    // AJAX RESPONSE MONITOR
    $(".ajax_response .message").each(function (e, m) {
        ajaxMessage(m, ajaxResponseBaseTime += 1);
    });

    // AJAX MESSAGE CLOSE ON CLICK
    $(".ajax_response").on("click", ".message", function (e) {
        $(this).effect("bounce").fadeOut(1);
    });

    // SELECT2
    $('.select2').select2({
        language: "pt-BR"
    });

    // DATATABLES
    $('#dataTable').DataTable({
        responsive: true,
        "pageLength": 25,
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            },
            "oAria": {
                "sSortAscending": ": Ordenar colunas de forma ascendente",
                "sSortDescending": ": Ordenar colunas de forma descendente"
            }
        },
    });

    // MASK
    var cellMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    cellOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(cellMaskBehavior.apply({}, arguments), options);
        }
    };

    $('.mask-cell').mask(cellMaskBehavior, cellOptions);
    $('.mask-phone').mask('(00) 0000-0000');
    $(".mask-date").mask('00/00/0000');
    $(".mask-datetime").mask('00/00/0000 00:00');
    $(".mask-month").mask('00/0000', {reverse: true});
    $(".mask-doc").mask('000.000.000-00', {reverse: true});
    $(".mask-cnpj").mask('00.000.000/0000-00', {reverse: true});
    $(".mask-zipcode").mask('00000-000', {reverse: true});
    $(".mask-money").mask('R$ 000.000.000.000.000,00', {reverse: true, placeholder: "R$ 0,00"});

    // SEARCH ZIPCODE
    $('.zip_code_search').blur(function () {

        function emptyForm() {
            $(".street").val("");
            $(".neighborhood").val("");
            $(".city").val("");
            $(".state").val("");
        }

        var zip_code = $(this).val().replace(/\D/g, '');
        var validate_zip_code = /^[0-9]{8}$/;

        if (zip_code != "" && validate_zip_code.test(zip_code)) {

            $(".street").val("");
            $(".neighborhood").val("");
            $(".city").val("");
            $(".state").val("");

            $.getJSON("https://viacep.com.br/ws/" + zip_code + "/json/?callback=?", function (data) {

                if (!("erro" in data)) {
                    $(".street").val(data.logradouro);
                    $(".neighborhood").val(data.bairro);
                    $(".city").val(data.localidade);
                    $(".state").val(data.uf);
                } else {
                    emptyForm();
                    alert("CEP não encontrado.");
                }
            });
        } else {
            emptyForm();
            alert("Formato de CEP inválido.");
        }
    });

});
