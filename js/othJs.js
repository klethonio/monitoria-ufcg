/* @author Klethônio Ferreira */
$(document).ready(function () {
    /////////////////////////////////////
    ////WINDOW DEL
    /////////////////////////////////////
    $('a[title*="Deletar"]').click(function (e) {
        e.preventDefault();
        var id = $(this).attr('href');
        href = $(this).attr('rel');

        $('a[name=excluir]').attr('href', href);

        var maskHeight = $(document).height();
        var maskWidth = $(window).width();

        $('#mask').css({'width': maskWidth, 'height': maskHeight});

        $('#mask').fadeIn(200);
        $('#mask').fadeTo("slow", 0.8);

        //Get the window height and width
        var winH = $(window).height();
        var winW = $(window).width();

        $(id).css('top', (winH - $(id).height()) / 2);
        $(id).css('left', (winW - $(id).width()) / 2);

        $(id).fadeIn(300);
    });
    /////////////////////////////////////
    ////WINDOW ATV
    /////////////////////////////////////
    $('a[title*="Ativar"]').click(function (e) {
        e.preventDefault();
        var id = $(this).attr('href');
        href = $(this).attr('rel');

        $('a[name=ativar]').attr('href', href);

        var maskHeight = $(document).height();
        var maskWidth = $(window).width();

        $('#mask').css({'width': maskWidth, 'height': maskHeight});

        $('#mask').fadeIn(200);
        $('#mask').fadeTo("slow", 0.8);

        //Get the window height and width
        var winH = $(window).height();
        var winW = $(window).width();

        $(id).css('top', (winH - $(id).height()) / 2);
        $(id).css('left', (winW - $(id).width()) / 2);

        $(id).fadeIn(300);
    });
    /////////////////////////////////////
    ////FEHCAR JANELAS
    /////////////////////////////////////
    $('[id*="window"] .close-window').click(function (e) {
        e.preventDefault();
        $('#mask').hide();
        $('[id*="window"]').hide();
    });
    $('#mask').click(function () {
        $(this).hide();
        $('[id*="window"]').hide();
        $('.loading').hide();
    });
    $(document).keyup(function (e) {
        if (e.keyCode == 27) {
            $('#mask').hide();
            $('[id*="window"]').hide();
            $('.loading').hide();
        }
    });
    /////////////////////////////////////
    ////URL FALSE MENU
    /////////////////////////////////////
    $('a[rel="false"]').click(function (e) {
        e.preventDefault();
    });
    /////////////////////////////////////
    ////CONTADOR DE SESSÃO
    /////////////////////////////////////
    $(function () {
        var countMin = 14,
                countSec = 59;
        var countDown = setInterval(function () {
            var minText = ('00').substring(0, (2 - countMin.toString().length)) + countMin,
                    secText = ('00').substring(0, (2 - countSec.toString().length)) + countSec;
            $('p.count-down').html('Sua sessão expira em: ' + minText + ':' + secText);
            if (countSec === 0) {
                countMin--;
                countSec = 59;
                if (minText == '00') {
                    $('p.count-down').html('Você deslogou! Recarrege a página para efetuar o login novamente.');
                    clearInterval(countDown);
                }
            }
            countSec--;
        }, 1000);
    });
    /////////////////////////////////////
    ////TABELA EXCEL
    /////////////////////////////////////
    $('.tab').first().show();
    $('#select-tabs li').first().css('background', '#333');
    $('#select-tabs li').click(function () {
        var ind = $(this).index();
        $('.tab').eq(ind).show().siblings('.tab').hide();
        $(this).css('background', '#333').siblings('#select-tabs li').css('background', '#999');
    });
    /////////////////////////////////////
    ////CARREGAR EXERCÍCIOS
    /////////////////////////////////////
    $('select[name="lists"]').change(function () {
        var list = $(this).val();
        $.ajax({
            type: 'POST',
            url: '../../../tpl/get-ajax.php',
            data: {list: list},
            dataType: 'json',
            encode: true,
            beforeSend: function () {
                $('select[name="exers"]').parent().hide();
                $('#loading').show();
            }
        }).done(function (numExers) {
            if (numExers) {
                var options = '<option value="all">Todos</option>';
                for (var i = 0; i < numExers.length; i++) {
                    options += '<option value="' + numExers[i] + '">Num ' + numExers[i] + '</option>';
                }
            } else {
                var options = '<option disabled value="">Nenhum exercício encontrado!</option>';
            }
            $('#loading').hide();
            $('select[name="exers"]').html(options).parent().show();
        });
    });
    /////////////////////////////////////
    ////CARREGAR INFORMAÇÃO ORDER
    /////////////////////////////////////
    $('a[href="#window-order"]').click(function (e) {
        e.preventDefault();
        var orderId = $(this).attr('rel'),
                tagId = $(this).attr('href'),
                //Get the window height and width
                winH = $(window).height(),
                winW = $(window).width();
        $.ajax({
            type: 'POST',
            url: '../../../tpl/get-ajax.php',
            data: {orderId: orderId},
            dataType: 'json',
            encode: true,
            beforeSend: function () {
                var maskHeight = $(document).height();
                var maskWidth = $(window).width();

                $('#mask').css({'width': maskWidth, 'height': maskHeight});

                $('#mask').fadeIn(200);
                $('#mask').fadeTo("slow", 0.8);

                $('.loading').css('top', (winH - $('.loading').height()) / 2);
                $('.loading').css('left', (winW - $('.loading').width()) / 2);
                $('.loading').fadeIn(300);
            }
        }).done(function (data) {
            if ($(".loading").is(":visible")) {
                $('.loading').hide();
                if (!data) {
                    $('#mask').hide();
                } else {
                    $('#infor-exer').html(data.num);
                    $('#infor-list').html(data.list_num);
                    $('#infor-end-date').html(data.end_date);
                    $('#infor-total-users').html(data.total_users);
                    $('#infor-total-sent').html(data.total_sent);
                    $('#infor-total-corrected').html(data.total_corrected);
                    if (data.media == 0) {
                        $('#infor-media').html('Nenhum corrigido');
                    } else {
                        $('#infor-media').html(data.media);
                    }
                    $(tagId).css('top', (winH - $(tagId).height()) / 2);
                    $(tagId).css('left', (winW - $(tagId).width()) / 2);
                    $('#mask').fadeIn(300);
                    $(tagId).fadeIn(300);
                }
            }
        });
    });
    /////////////////////////////////////
    ////CARREGAR INFORMAÇÃO LISTA
    /////////////////////////////////////
    $('a[href="#window-list"]').click(function (e) {
        e.preventDefault();
        var listId = $(this).attr('rel'),
                tagId = $(this).attr('href'),
                //Get the window height and width
                winH = $(window).height(),
                winW = $(window).width();
        $.ajax({
            type: 'POST',
            url: '../../../tpl/get-ajax.php',
            data: {listId: listId},
            dataType: 'json',
            encode: true,
            beforeSend: function () {
                var maskHeight = $(document).height();
                var maskWidth = $(window).width();

                $('#mask').css({'width': maskWidth, 'height': maskHeight});

                $('#mask').fadeIn(200);
                $('#mask').fadeTo("slow", 0.8);

                $('.loading').css('top', (winH - $('.loading').height()) / 2);
                $('.loading').css('left', (winW - $('.loading').width()) / 2);
                $('.loading').fadeIn(300);
            }
        }).done(function (data) {
            if ($(".loading").is(":visible")) {
                $('.loading').hide();
                if (!data) {
                    $('#mask').hide();
                } else {
                    $('#infor-list').html(data.list_num);
                    $('#infor-total-exers').html(data.total_exers);
                    $('#infor-ordered').html(data.ordered);
                    $('#infor-waited').html(data.waited);
                    $('#infor-total-sent').html(data.total_sent);
                    $('#infor-total-corrected').html(data.total_corrected);
                    if (data.media == 0) {
                        $('#infor-media').html('Nenhum corrigido');
                    } else {
                        $('#infor-media').html(data.media);
                    }
                    $(tagId).css('top', (winH - $(tagId).height()) / 2);
                    $(tagId).css('left', (winW - $(tagId).width()) / 2);
                    $('#mask').fadeIn(300);
                    $(tagId).fadeIn(300);
                }

            }
        });
    });
    /////////////////////////////////////
    ////CONTADOR CARACTERES
    /////////////////////////////////////
    $('.max-char').keyup(function (event) {
        var index = $('.max-char').index($(this)),
                target = $(".count-char"),
                max = $(this).attr('maxlength'),
                len = $(this).val().length,
                remain = max - len;
        if (len > max) {
            var val = $(this).val();
            $(this).val(val.substr(0, max));
            remain = 0;
        }
        target.eq(index).html('Restantes: ' + remain);
    });
    /////////////////////////////////////
    ////COPIA NOTAS PARA O TEXTAREA
    /////////////////////////////////////
    $('.p-notes').click(function (event) {
        $('.max-char').val($(this).children('.p-notes-text').val());
        $('input[name=grade]').focus();
    });
    /////////////////////////////////////
    ////COPIAR PROGRAMA
    /////////////////////////////////////
    $('a[href="#copiar"]').click(function (e) {
        e.preventDefault();
        var programContent = document.querySelector('#matlab-copy');
        var range = document.createRange();
        range.selectNode(programContent);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand("copy");
        window.getSelection().removeAllRanges();
        $('.msgCopy').show().delay(500).fadeOut('fast');
    });
    /////////////////////////////////////
    ////MIN-WIDTH
    /////////////////////////////////////
    var maxWidth = Math.max.apply(Math, $('pre.matlab span').map(function () {
        return $(this).width();
    }).get());
    $('#code').css('min-width', maxWidth+100);
});