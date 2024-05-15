/**
 * Resize function without multiple trigger
 * 
 * Usage:
 * $(window).smartresize(function(){  
 *     // code here
 * });
 */
(function($, sr) {
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function(func, threshold, execAsap) {
        var timeout;

        return function debounced() {
            var obj = this,
                args = arguments;

            function delayed() {
                if (!execAsap)
                    func.apply(obj, args);
                timeout = null;
            }

            if (timeout)
                clearTimeout(timeout);
            else if (execAsap)
                func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100);
        };
    };

    // smartresize 
    jQuery.fn[sr] = function(fn) { return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery, 'smartresize');
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var CURRENT_URL = window.location.href.split('#')[0].split('?')[0],
    $BODY = $('body'),
    $MENU_TOGGLE = $('#menu_toggle'),
    $SIDEBAR_MENU = $('#sidebar-menu'),
    $SIDEBAR_FOOTER = $('.sidebar-footer'),
    $LEFT_COL = $('.left_col'),
    $RIGHT_COL = $('.right_col'),
    $NAV_MENU = $('.nav_menu'),
    $FOOTER = $('footer');

// Sidebar
function init_sidebar() {
    debugger
    // TODO: This is some kind of easy fix, maybe we can improve this
    var setContentHeight = function() {
        // reset height
        $RIGHT_COL.css('min-height', $(window).height());

        var bodyHeight = $BODY.outerHeight(),
            footerHeight = $BODY.hasClass('footer_fixed') ? -10 : $FOOTER.height(),
            leftColHeight = $LEFT_COL.eq(1).height() + $SIDEBAR_FOOTER.height(),
            contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

        // normalize content
        contentHeight -= $NAV_MENU.height() + footerHeight;

        $RIGHT_COL.css('min-height', contentHeight);
    };

    var openUpMenu = function() {
        $SIDEBAR_MENU.find('li').removeClass('active active-sm');
        $SIDEBAR_MENU.find('li ul').slideUp();
    }

    $SIDEBAR_MENU.find('a').on('click', function(ev) {
        var $li = $(this).parent();

        if ($li.is('.active')) {
            $li.removeClass('active active-sm');
            $('ul:first', $li).slideUp(function() {
                setContentHeight();
            });
        } else {
            // prevent closing menu if we are on child menu
            if (!$li.parent().is('.child_menu')) {
                openUpMenu();
            } else {
                if ($BODY.is('nav-sm')) {
                    if (!$li.parent().is('child_menu')) {
                        openUpMenu();
                    }
                }
            }

            $li.addClass('active');

            $('ul:first', $li).slideDown(function() {
                setContentHeight();
            });
        }
    });

    // toggle small or large menu
    $MENU_TOGGLE.on('click', function() {
        if ($BODY.hasClass('nav-md')) {
            $SIDEBAR_MENU.find('li.active ul').hide();
            $SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
        } else {
            $SIDEBAR_MENU.find('li.active-sm ul').show();
            $SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
        }

        $BODY.toggleClass('nav-md nav-sm');

        setContentHeight();

        $('.dataTable').each(function() { $(this).dataTable().fnDraw(); });
    });

    // check active menu
    $SIDEBAR_MENU.find('a[href="' + CURRENT_URL + '"]').parent('li').addClass('current-page');

    $SIDEBAR_MENU.find('a').filter(function() {
        return this.href == CURRENT_URL;
    }).parent('li').addClass('current-page').parents('ul').slideDown(function() {
        setContentHeight();
    }).parent().addClass('active');

    // recompute content when resizing
    $(window).smartresize(function() {
        setContentHeight();
    });

    setContentHeight();

    // fixed sidebar
    if ($.fn.mCustomScrollbar) {
        $('.menu_fixed').mCustomScrollbar({
            autoHideScrollbar: true,
            theme: 'minimal',
            mouseWheel: { preventDefault: true }
        });
    }
}
// /Sidebar

// Panel toolbox
$(document).ready(function() {
    $('.collapse-link').on('click', function() {
        var $BOX_PANEL = $(this).closest('.x_panel'),
            $ICON = $(this).find('i'),
            $BOX_CONTENT = $BOX_PANEL.find('.x_content');

        // fix for some div with hardcoded fix class
        if ($BOX_PANEL.attr('style')) {
            $BOX_CONTENT.slideToggle(200, function() {
                $BOX_PANEL.removeAttr('style');
            });
        } else {
            $BOX_CONTENT.slideToggle(200);
            $BOX_PANEL.css('height', 'auto');
        }

        $ICON.toggleClass('fa-chevron-up fa-chevron-down');
    });

    $('.close-link').click(function() {
        var $BOX_PANEL = $(this).closest('.x_panel');

        $BOX_PANEL.remove();
    });
});
// /Panel toolbox

// Tooltip
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body'
    });
});
// /Tooltip

// Progressbar
$(document).ready(function() {
    if ($(".progress .progress-bar")[0]) {
        $('.progress .progress-bar').progressbar();
    }
});
// /Progressbar

// Switchery
$(document).ready(function() {
    if ($(".js-switch")[0]) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            var switchery = new Switchery(html, {
                color: '#26B99A'
            });
        });
    }
});
// /Switchery

// iCheck
$(document).ready(function() {
    if ($("input.flat")[0]) {
        $(document).ready(function() {
            $('input.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        });
    }
});
// /iCheck

//hover and retain popover when on popover content
var originalLeave = $.fn.popover.Constructor.prototype.leave;
$.fn.popover.Constructor.prototype.leave = function(obj) {
    var self = obj instanceof this.constructor ?
        obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type);
    var container, timeout;

    originalLeave.call(this, obj);

    if (obj.currentTarget) {
        container = $(obj.currentTarget).siblings('.popover');
        timeout = self.timeout;
        container.one('mouseenter', function() {
            //We entered the actual popover – call off the dogs
            clearTimeout(timeout);
            //Let's monitor popover content instead
            container.one('mouseleave', function() {
                $.fn.popover.Constructor.prototype.leave.call(self, self);
            });
        });
    }
};

$('body').popover({
    selector: '[data-popover]',
    trigger: 'click hover',
    delay: {
        show: 50,
        hide: 400
    }
});


function gd(year, month, day) {
    return new Date(year, month - 1, day).getTime();
}

function init_flot_chart(data) {
    if (typeof($.plot) === 'undefined') { return; }
    console.log('init_flot_chart');

    var chart_plot_01_settings = {
        series: {
            lines: {
                show: false,
                fill: true
            },
            splines: {
                show: true,
                tension: 0.3,
                lineWidth: 1,
                fill: 0.5
            },
            points: {
                radius: 3,
                show: true,
            },
            shadowSize: 5
        },
        grid: {
            verticalLines: true,
            hoverable: true,
            clickable: true,
            tickColor: "#d5d5d5",
            borderWidth: 1,
            color: '#fff'
        },
        colors: ["rgba(249, 102, 102, 0.34)", "rgba(3, 88, 106, 0.38)"],
        xaxis: {
            tickColor: "rgba(51, 51, 51, 0.06)",
            mode: "time",
            tickSize: [1, "day"],
            tickLength: 0.9,
            axisLabel: "Date",
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 1,
            axisLabelFontFamily: 'Verdana, Arial',
            axisLabelPadding: 1
        },
        yaxis: {
            ticks: 20,
            tickColor: "rgba(51, 51, 51, 0.06)",
        },
        tooltip: true,
    }

    if (data === null || data == '') {
        console.log('Dados nulos, não é possível inicializar os gráficos init_flot_chart.');
        if ($("#chart_plot_01").length) {
            var dataAtual = new Date();
            var anoAtual = dataAtual.getFullYear();
            var mesAtual = dataAtual.getMonth() + 1;
        
            array_debito_chart = [
                [gd(anoAtual, mesAtual, 1), 10],
                [gd(anoAtual, mesAtual, 2), 10],
                [gd(anoAtual, mesAtual, 3), 10],
                [gd(anoAtual, mesAtual, 4), 10],
                [gd(anoAtual, mesAtual, 5), 10],
                [gd(anoAtual, mesAtual, 6), 10],
                [gd(anoAtual, mesAtual, 7), 10],
                [gd(anoAtual, mesAtual, 8), 10],
                [gd(anoAtual, mesAtual, 9), 10],
                [gd(anoAtual, mesAtual, 10), 10],
                [gd(anoAtual, mesAtual, 11), 10],
                [gd(anoAtual, mesAtual, 12), 10],
                [gd(anoAtual, mesAtual, 13), 10],
                [gd(anoAtual, mesAtual, 14), 10],
                [gd(anoAtual, mesAtual, 15), 10],
                [gd(anoAtual, mesAtual, 16), 10],
                [gd(anoAtual, mesAtual, 17), 10],
                [gd(anoAtual, mesAtual, 18), 10]
            ];
        
            array_credito_chart = [
                [gd(anoAtual, mesAtual, 1), 10],
                [gd(anoAtual, mesAtual, 2), 10],
                [gd(anoAtual, mesAtual, 3), 10],
                [gd(anoAtual, mesAtual, 4), 10],
                [gd(anoAtual, mesAtual, 5), 10],
                [gd(anoAtual, mesAtual, 6), 10],
                [gd(anoAtual, mesAtual, 7), 10],
                [gd(anoAtual, mesAtual, 8), 10],
                [gd(anoAtual, mesAtual, 9), 10],
                [gd(anoAtual, mesAtual, 10), 10],
                [gd(anoAtual, mesAtual, 11), 10],
                [gd(anoAtual, mesAtual, 12), 10],
                [gd(anoAtual, mesAtual, 13), 10],
                [gd(anoAtual, mesAtual, 14), 10],
                [gd(anoAtual, mesAtual, 15), 10],
                [gd(anoAtual, mesAtual, 16), 10],
                [gd(anoAtual, mesAtual, 17), 10],
                [gd(anoAtual, mesAtual, 18), 10],
            ];
            $.plot($("#chart_plot_01"), [array_credito_chart, array_debito_chart], chart_plot_01_settings);
        }
        return
    }

    if ($("#chart_plot_01").length) {
        var array_debito_chart = [];
        var array_credito_chart = [];

        //ARRAY CREDITO
        var array_credito = data.array_credito;

        // Iterar sobre as strings de resposta
        array_credito.forEach(function(str) {

            // Use expressões regulares para extrair o ano, mês, dia e valor de cada string
            var matches = str.match(/\((\d+),\s*(\d+),\s*(\d+)\),\s*([\d.]+)/);
            
            // Se houver correspondência
            if (matches) {
                // Extrair os componentes da data e o valor
                var ano = matches[1];
                var mes = matches[2];
                var dia = matches[3];
                var valor = parseFloat(matches[4]);
                
                // Adicionar os dados formatados ao array
                array_credito_chart.push([gd(ano, mes, dia), valor]);
            }
        });
        //FIM ARRAY CREDITO

        // ARRAY DEBITO
        var array_debito = data.array_debito;
        // Iterar sobre as strings de resposta
        array_debito.forEach(function(str) {
            
            // Use expressões regulares para extrair o ano, mês, dia e valor de cada string
            var matches = str.match(/\((\d+),\s*(\d+),\s*(\d+)\),\s*([\d.]+)/);
            
            // Se houver correspondência
            if (matches) {
                // Extrair os componentes da data e o valor
                var ano = matches[1];
                var mes = matches[2];
                var dia = matches[3];
                var valor = parseFloat(matches[4]);
                
                // Adicionar os dados formatados ao array
                array_debito_chart.push([gd(ano, mes, dia), valor]);
            }
        });
        //FIM ARRAY DEBITO

        $.plot($("#chart_plot_01"), [array_credito_chart, array_debito_chart], chart_plot_01_settings);

        var totalDiaCreditoElement = document.getElementById('total_dia_credito');
        if (totalDiaCreditoElement) {
            var valorFormatado = parseFloat(data.total_dia_credito).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            totalDiaCreditoElement.innerText = valorFormatado;
        }

        var totalDiaDebitoElement = document.getElementById('total_dia_debito');
        if (totalDiaDebitoElement) {
            var valorFormatado = parseFloat(data.total_dia_debito).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            totalDiaDebitoElement.innerText = valorFormatado;
        }
    }
                

};



function montaLetra() {
    var i;
    var l;

    f = document.dashboard;

    //f.dataIniDay.valueee
    f.letra.value = f.dataIni.value + "|" + f.dataFim.value + "|0|";

    // data referencia
    for (i = 0; i < f.dataReferencia.length; i++) {
        if (f.dataReferencia[i].selected) {
            f.letra.value = f.letra.value + f.dataReferencia[i].value;
        }
    }

    // situacao lancamento
    myCheckbox = document.dashboard.elements["sitlanc[]"];

    l = 0;
    for (var i = 0; i < sitlanc.options.length; i++) {
        if (sitlanc[i].selected == true) { l++; }
    }
    f.letra.value = f.letra.value + "|" + l;
    for (var i = 0; i < sitlanc.options.length; i++) {
        if (sitlanc[i].selected == true) {
            f.letra.value = f.letra.value + "|" + sitlanc[i].value;
        }
    }

    // filial
    myCheckbox = document.dashboard.elements["filial[]"];

    l = 0;
    for (var i = 0; i < filial.options.length; i++) {
        if (filial[i].selected == true) { l++; }
    }
    f.letra.value = f.letra.value + "|" + l;
    for (var i = 0; i < filial.options.length; i++) {
        if (filial[i].selected == true) {
            f.letra.value = f.letra.value + "|" + filial[i].value;
        }
    }

    // tipo lancamento
    myCheckbox = document.dashboard.elements["tipolanc[]"];

    l = 0;
    for (var i = 0; i < tipolanc.options.length; i++) {
        if (tipolanc[i].selected == true) { l++; }
    }
    f.letra.value = f.letra.value + "|" + l;
    for (var i = 0; i < tipolanc.options.length; i++) {
        if (tipolanc[i].selected == true) {
            f.letra.value = f.letra.value + "|" + tipolanc[i].value;
        }
    }

    f.letra.value += "|0|0|0|0|0"; 

    return f.letra.value;

}

function init_chart_doughnut(data) {

    if (typeof(Chart) === 'undefined') { return; }
    console.log('init_chart_doughnut');

    if (data === null || data == '') {
        //variavel padrao caso nao consiga a consulta
        var chart_doughnut_settings = {
            type: 'doughnut',
            tooltipFillColor: "rgba(51, 51, 51, 0.55)",
            data: {
                labels: [
                    "Dados não encontrados",
                ],
                datasets: [{
                    data: [100],
                    backgroundColor: [
                        "#B22222",
                    ]
                }]
            },
            options: {
                legend: true,
                responsive: false
            }
        }

        $('.canvasDoughnut').each(function() {

            var chart_element = $(this);
            var chart_doughnut = new Chart(chart_element, chart_doughnut_settings);

        });
        return;
    }

    var chart_doughnut_settings = {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.datasets.data,
                backgroundColor: data.datasets.backgroundColor,
            }]
        },
        options: {
            legend: true,
            responsive: false,
            fullWidth: true,
            onClick: function(event, legendItem, chart) {
                console.log(legendItem)
                console.log(chart)
            },
        }
    };

    //plota o grafico
    $('.canvasDoughnut').each(function() {

        var chart_element = $(this);
        var chart_doughnut = new Chart(chart_element, chart_doughnut_settings);

    });

    //plota a tabela
    debugger
    let table = document.querySelector('.tile_info');
    let bancos = data.labels;
    
    // Verifica se a tabela já possui linhas e limpa antes de plotar novos dados
    if (table.rows.length > 0) {
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }
    }
    
    bancos.forEach(function(banco, index) {
        var row = table.insertRow(-1);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        
        cell1.innerHTML = '<p class="nameContas"><i class="fa fa-square" style="color:'+ data.datasets.backgroundColor[index] +'"></i>' + banco + '</p>';
        cell2.innerHTML = data.dataTabela[index] + '%';
    });
}


function init_gauge_vel(data) {
    debugger
    if (typeof(Gauge) === 'undefined') { return; }
    console.log('init_gauge [' + $('.gauge-chart').length + ']');
    console.log('init_gauge');

    if (data === null || data == '') {
        console.log('Dados nulos, não é possível inicializar os gráficos init_gauge.');
        var chart_gauge_settings = {
            lines: 12,
            angle: 0,
            lineWidth: 0.4,
            pointer: {
                length: 0.75,
                strokeWidth: 0.042,
                color: '#1D212A'
            },
            limitMax: 'false',
            colorStart: '#1ABC9C',
            colorStop: '#1ABC9C',
            strokeColor: '#F0F3F3',
            generateGradient: true
        };
        var chart_gauge_settings2 = {
            lines: 12,
            angle: 0,
            lineWidth: 0.4,
            pointer: {
                length: 0.75,
                strokeWidth: 0.042,
                color: '#1D212A'
            },
            limitMax: 'false',
            colorStart: '#1ABC9C',
            colorStop: '#1ABC9C',
            strokeColor: '#F0F3F3',
            generateGradient: true
        };
        if ($('#chart_gauge_01').length) {
            var chart_gauge_01_elem = document.getElementById('chart_gauge_01');
            var chart_gauge_01 = new Gauge(chart_gauge_01_elem).setOptions(chart_gauge_settings);
        }
        if ($('#gauge-text').length) {
            chart_gauge_01.maxValue = 6000;
            chart_gauge_01.animationSpeed = 32;
            chart_gauge_01.set(0);
            chart_gauge_01.setTextField(document.getElementById("gauge-text"));
        }
        if ($('#chart_gauge_02').length) {
            var chart_gauge_02_elem = document.getElementById('chart_gauge_02');
            var chart_gauge_02 = new Gauge(chart_gauge_02_elem).setOptions(chart_gauge_settings2);
        }
        if ($('#gauge-text2').length) {
            chart_gauge_02.maxValue = 9000;
            chart_gauge_02.animationSpeed = 32;
            chart_gauge_02.set(0);
            chart_gauge_02.setTextField(document.getElementById("gauge-text2"));
        }
        return;
    }

    var chart_gauge_settings = {
        lines: 12,
        angle: 0,
        lineWidth: 0.4,
        pointer: {
            length: 0.75,
            strokeWidth: 0.042,
            color: '#1D212A'
        },
        limitMax: 'false',
        colorStart: '#f6a1a1',
        colorStop: '#f7f7f7',
        strokeColor: '#ea7272',
        generateGradient: true
    };
    var chart_gauge_settings2 = {
        lines: 12,
        angle: 0,
        lineWidth: 0.4,
        pointer: {
            length: 0.75,
            strokeWidth: 0.042,
            color: '#1D212A'
        },
        limitMax: 'false',
        colorStart: '#92989d',
        colorStop: '#f7f7f7',
        strokeColor: '#34495E',
        generateGradient: true
    };
    if ($('#chart_gauge_01').length) {
        var chart_gauge_01_elem = document.getElementById('chart_gauge_01');
        var chart_gauge_01 = new Gauge(chart_gauge_01_elem).setOptions(chart_gauge_settings);
    }
    if ($('#gauge-text').length) {
        chart_gauge_01.maxValue = 100;
        chart_gauge_01.animationSpeed = 32;
        chart_gauge_01.set(data.percentual_credito_baixado);
        chart_gauge_01.setTextField(document.getElementById("gauge-text"));
    }
    if ($('#chart_gauge_02').length) {
        var chart_gauge_02_elem = document.getElementById('chart_gauge_02');
        var chart_gauge_02 = new Gauge(chart_gauge_02_elem).setOptions(chart_gauge_settings2);
    }
    if ($('#gauge-text2').length) {
        chart_gauge_02.maxValue = 100;
        chart_gauge_02.animationSpeed = 32;
        chart_gauge_02.set(data.percentual_debito_baixado);
        chart_gauge_02.setTextField(document.getElementById("gauge-text2"));
    }
};


function init_morris_charts(data) {
    if (typeof(Morris) === 'undefined') { return; }
    console.log('init_morris_charts');

    if (data === null || data == '') {
        console.log('Dados nulos, não é possível inicializar os gráficos init_morris_charts.');
        Morris.Bar({
            element: 'graph_bar',
            data: [
                { centroCusto: 'Dados não localizados', c: 500, d: 500 },
                { centroCusto: 'Dados não localizados', c: 500, d: 500 },
                { centroCusto: 'Dados não localizados', c: 500, d: 500 },
                { centroCusto: 'Dados não localizados', c: 500, d: 500 },
                { centroCusto: 'Dados não localizados', c: 500, d: 500 },
                { centroCusto: 'Dados não localizados', c: 500, d: 500 },
                { centroCusto: 'Dados não localizados', c: 500, d: 500 },
                { centroCusto: 'Dados não localizados', c: 500, d: 500 },
                { centroCusto: 'Dados não localizados', c: 500, d: 500 },
                { centroCusto: 'Dados não localizados', c: 500, d: 500 }
            ],
            xkey: 'centroCusto',
            ykeys: ['c', 'd'],
            labels: ['Total crédito', 'Total débito'],
            barRatio: 0.4,
            barColors: ['#ea7272', '#ea7272'],
            xLabelAngle: 20,
            hideHover: 'auto',
            resize: true,
            preUnits: 'R$',
            behaveLikeLine: true,
        });

        return
    }
    Morris.Bar({
        element: 'graph_bar',
        data: data,
        xkey: 'centroCusto',
        ykeys: ['c', 'd'],
        labels: ['Total crédito', 'Total débito'],
        fillOpacity: 0.6,
        barRatio: 0.1,
        barColors: ['#34495E', '#ea7272'],
        xLabelAngle: 20,
        hideHover: 'auto',
        resize: true,
        preUnits: 'R$',
        behaveLikeLine: true,
    });
};

function submitPesquisar(){
    //window.location.reload();
    debugger
    f = document.dashboard;
    montaLetra();
    f.submit();
}

// $(document).ready(function() {
//    init_flot_chart();
//    init_sidebar();
//    init_daterangepicker();
//    init_morris_charts();
//    init_chart_doughnut();
//    init_gauge();
// });