<style>
  .panel-body {
    background-color: #F2F5F7 !important;
  }

  .htmlAll {
    color: #73879C !important;
  }
.nameContas{
  font-size: 10px;
}
#dataReferencia{
  padding: 0;
  border-radius: 5px;
}
#dataConsulta{
  border-radius: 5px;
  text-align: center  ;
}
#tipolanc, .select2-selection--multiple{
  border-radius: 5px !important;
}
.select2-container{
  width: 100% !important;
}
#btnpesquisa{
  margin-top: 25px !important;
}
</style>
{debug}
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="images/favicon.ico" type="image/ico" />
  <title>Gentelella Alela! | </title>
  <!-- Bootstrap -->
  <link href="{$bootstrap}/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="{$bootstrap}/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <!-- NProgress -->
  <link href="{$bootstrap}/nprogress/nprogress.css" rel="stylesheet">
  <!-- iCheck -->
  <link href="{$bootstrap}/iCheck/skins/flat/green.css" rel="stylesheet">
  <!-- bootstrap-progressbar -->
  <link href="{$bootstrap}/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
  <!-- JQVMap -->
  <link href="{$bootstrap}/jqvmap/dist/jqvmap.min.css" rel="stylesheet" />
  <!-- bootstrap-daterangepicker -->
  <link href="{$bootstrap}/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
  <!-- Custom Theme Style -->
  <link href="css/custom.css" rel="stylesheet">
</head>

<body>

  <form id="dashboard" data-parsley-validate class="form-horizontal form-label-left" NAME="dashboard"
    ACTION="{$SCRIPT_NAME}" METHOD="post" enctype="multipart/form-data">
    <input name=mod type=hidden value="fin">
    <input name=form type=hidden value="fin_dashboard">
    <input name=submenu type=hidden value={$subMenu}>
    <input name=letra type=hidden value={$letra}>
    <input name=opcao type=hidden value="{$opcao}">
    <input name=dataIni type=hidden value={$dataIni}>
    <input name=dataFim type=hidden value={$dataFim}>

    <!-- page content -->
    <div class="right_col htmlAll" role="main">

      <div class="row">
        <!-- start accordion -->
        <div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
          <div class="panel">
            <a class="panel-heading collapsed" role="tab" id="headingTwo" data-toggle="collapse"
              data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
              <h4 class="panel-title">Parâmetros</h4>
            </a>
            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
              <div class="panel-body">
                <!-- PARAMETROS -->
                <div class="row col-md-12 col-sm-12 col-xs-12">
                  <div class="form-group col-md-1 col-sm-2 col-xs-2">
                    <label>Data Refer&ecirc;ncia</label>
                    <select class="form-control" name=dataReferencia id="dataReferencia">
                      {html_options values=$datas_ids selected=$datas_id output=$datas_names}
                    </select>
                  </div>

                  <div class="form-group col-md-2 col-sm-2 col-xs-2">
                    <label for="dataConsulta">Per&iacute;odo</label>
                    <i class="glyphicon glyphicon-calendar fa fa-calendar" style="position: absolute; top: 10%; transform: translateY(-20%);"></i>
                    <div style="position: relative;">
                      <input type="text" name="dataConsulta" id="dataConsulta" class="form-control" value="{$dataIni} - {$dataFim}">
                    </div>
                  </div>

                  <div class="form-group col-md-2 col-sm-2 col-xs-2">
                    <label for="tipolanc">Tipo Lan&ccedil;amento</label>
                    <div class="input-group-prepend">
                      <select class="select2_multiple form-control" multiple="multiple" id="tipolanc" name="tipolanc">
                        {html_options values=$tipoLanc_ids selected=$tipoLanc_id output=$tipoLanc_names}
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-2 col-sm-2 col-xs-2">
                    <label for="sitlanc">Situa&ccedil;&atilde;o Lan&ccedil;amento</label>
                    <div class="input-group-prepend">
                      <select class="select2_multiple form-control" multiple="multiple" id="sitlanc" name="sitlanc">
                        {html_options values=$situacaoLanc_ids selected=$situacaoLanc_id output=$situacaoLanc_names}
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-4 col-sm-4 col-xs-4">
                    <label for="filial">Filial</label>
                    <div class="input-group-prepend">
                      <select class="select2_multiple form-control" multiple="multiple" id="filial" name="filial">
                        {html_options values=$filial_ids selected=$filial_id output=$filial_names}
                      </select>
                    </div>
                    <!-- END PARAMETROS -->
                  </div>

                  <div class="form-group col-md-1 col-sm-1 col-xs-1">
                    <button type="button" id="btnpesquisa" class="btn btn-primary pull-right" onClick="javascript:submitPesquisar('');">
                      <span>Pesquisar</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <!-- end of accordion -->
        </div><!-- <div class="row"> -->


        <!-- top tiles -->
        <div class="row col-md-12 col-sm-12" style="display: inline-block;">
          <div class="tile_count">
            <div class="col-md-3 col-sm-4  tile_stats_count">
              <span class="count_top"><i class="glyphicon glyphicon-usd"></i> Total débito Dia</span>
              <div class="count" id="total_dia_debito">0,00</div>
              <span class="count_bottom"><i class="green">4% </i> From last Week</span>
            </div>
            <div class="col-md-3 col-sm-4  tile_stats_count">
              <span class="count_top"><i class="glyphicon glyphicon-usd"></i> Total crédito Dia</span>
              <div class="count" id="total_dia_credito">0,00</div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>3% </i> From last Week</span>
            </div>
            <div class="col-md-3 col-sm-4  tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Débito Periodo</span>
              <div class="count green">0,00</div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span>
            </div>
            <div class="col-md-3 col-sm-4  tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i>Total Crédito Periodo</span>
              <div class="count">0,00</div>
              <span class="count_bottom"><i class="red"><i class="fa fa-sort-desc"></i>12% </i> From last Week</span>
            </div>
            {* <div class="col-md-2 col-sm-4  tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Collections</span>
              <div class="count">2,315</div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span>
            </div>
            <div class="col-md-2 col-sm-4  tile_stats_count">
              <span class="count_top"><i class="fa fa-user"></i> Total Connections</span>
              <div class="count">7,325</div>
              <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span>
            </div> *}
          </div>
        </div>


        <!-- /top tiles -->
        <div class="row">
          <div class="col-md-12 col-sm-12 ">
            <div class="dashboard_graph">
              <div class="row x_title">
                <div class="col-md-6">
                  <h3>Gráfico financeiro <small>crédito - débito</small></h3>
                </div>
              </div>
              <div class="col-md-12 col-sm-12" style="padding: 0 !important;">
                <div id="chart_plot_01" class="demo-placeholder"></div>
              </div>
              {* <div class="col-md-3 col-sm-3  bg-white">
              <div class="x_title">
                <h2>Top Campaign Performance</h2>
                <div class="clearfix"></div>
              </div>
              <div class="col-md-12 col-sm-12 ">
                <div>
                  <p>Facebook Campaign</p>
                  <div class="">
                    <div class="progress progress_sm" style="width: 76%;">
                      <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="80"></div>
                    </div>
                  </div>
                </div>
                <div>
                  <p>Twitter Campaign</p>
                  <div class="">
                    <div class="progress progress_sm" style="width: 76%;">
                      <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="60"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-sm-12 ">
                <div>
                  <p>Conventional Media</p>
                  <div class="">
                    <div class="progress progress_sm" style="width: 76%;">
                      <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="40"></div>
                    </div>
                  </div>
                </div>
                <div>
                  <p>Bill boards</p>
                  <div class="">
                    <div class="progress progress_sm" style="width: 76%;">
                      <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="50"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div> *}
              <div class="clearfix"></div>
            </div>
          </div>
        </div>
        <br />
        <div class="row">
            <div class="col-md-12 col-sm-12  widget_tally_box">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Resumo Centro de Custo</h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">


                    <div id="graph_bar" style="width:100%; height:200px;"></div>

                    <div class=" bg-white progress_summary">

                    </div>
                  </div>
                </div>
              </div>
        </div>


        <div class="row">

          <div class="col-md-6 col-sm-6">
            <div class="x_panel tile fixed_height_350 overflow_hidden">
              <div class="x_title">
                <h2>Saldos Bancários</h2>
                {* <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i
                        class="fa fa-wrench"></i></a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <a class="dropdown-item" href="#">Settings 1</a>
                      <a class="dropdown-item" href="#">Settings 2</a>
                    </div>
                  </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a>
                  </li>
                </ul> *}
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <table class="" style="width:100%">
                  <tr>
                    <th style="width:37%;">
                      <p></p>
                    </th>
                    <th>
                      <div class="col-lg-7 col-md-7 col-sm-7 ">
                        <p class="">Contas</p>
                      </div>
                      <div class="col-lg-5 col-md-5 col-sm-5 ">
                        <p class="pull-right">Percentual</p>
                      </div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <canvas class="canvasDoughnut" height="180" width="180" style="margin: 15px 10px 10px 0"></canvas>
                    </td>
                    <td>
                      <table class="tile_info">
                        <tr>
                          <td>
                            <p><i class="fa fa-square blue"></i>Dados não localizados </p>
                          </td>
                          <td>100%</td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>


          <div class="col-md-6 col-sm-6 ">
            <div class="x_panel tile fixed_height_320">
              <div class="x_title">
                <h2>Quick Settings</h2>
                {* <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                  </li>
                  <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i
                        class="fa fa-wrench"></i></a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <a class="dropdown-item" href="#">Settings 1</a>
                      <a class="dropdown-item" href="#">Settings 2</a>
                    </div>
                  </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a>
                  </li>
                </ul> *}
                <div class="clearfix"></div>
              </div>
              <div class="x_content">
                <div class="dashboard-widget-content">
                  <div class="sidebar-widget">
                    <h4>Progresso Débito</h4>
                    <canvas width="150" height="80" id="chart_gauge_01" class=""
                      style="width: 160px; height: 100px;"></canvas>
                    <div class="goal-wrapper">
                      <span id="gauge-text" class="gauge-value pull-left">0</span>
                      <span class="gauge-value pull-left">%</span>
                      <span id="goal-text" class="goal-value pull-right">100%</span>
                    </div>
                  </div>


                  <div class="sidebar-widget">
                    <h4>Progresso Crédito</h4>
                    <canvas width="150" height="80" id="chart_gauge_02" class=""
                      style="width: 160px; height: 100px;"></canvas>
                    <div class="goal-wrapper">
                      <span id="gauge-text2" class="gauge-value pull-left">0</span>
                      <span class="gauge-value pull-left">%</span>
                      <span id="goal-text" class="goal-value pull-right">100%</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- /page content -->

      </div>
    </div>
  </form>
</body>

</html>

<!-- jQuery -->
<script src="{$bootstrap}/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="{$bootstrap}/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!-- FastClick -->
<!-- <script src="{$bootstrap}/fastclick/lib/fastclick.js"></script> -->
<!-- NProgress -->
<!-- <script src="{$bootstrap}/nprogress/nprogress.js"></script> -->
<!-- Chart.js -->
<script src="{$bootstrap}/Chart.js/dist/Chart.min.js"></script>
<!-- gauge.js -->
<script src="{$bootstrap}/gauge.js/dist/gauge.min.js"></script>
<!-- bootstrap-progressbar -->
<script src="{$bootstrap}/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
<!-- iCheck -->
<script src="{$bootstrap}/iCheck/icheck.min.js"></script>
<!-- Skycons -->
<script src="{$bootstrap}/skycons/skycons.js"></script>
<!-- Flot -->
<script src="{$bootstrap}/Flot/jquery.flot.js"></script>
<script src="{$bootstrap}/Flot/jquery.flot.pie.js"></script>
<script src="{$bootstrap}/Flot/jquery.flot.time.js"></script>
<script src="{$bootstrap}/Flot/jquery.flot.stack.js"></script>
<script src="{$bootstrap}/Flot/jquery.flot.resize.js"></script>
<!-- Flot plugins -->
<script src="{$bootstrap}/flot.orderbars/js/jquery.flot.orderBars.js"></script>
<script src="{$bootstrap}/flot-spline/js/jquery.flot.spline.min.js"></script>
<script src="{$bootstrap}/flot.curvedlines/curvedLines.js"></script>
<!-- DateJS -->
<script src="{$bootstrap}/DateJS/build/date.js"></script>
<!-- JQVMap -->
<script src="{$bootstrap}/jqvmap/dist/jquery.vmap.js"></script>
<script src="{$bootstrap}/jqvmap/dist/maps/jquery.vmap.world.js"></script>
<script src="{$bootstrap}/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
<!-- bootstrap-daterangepicker -->
{* <script src="{$bootstrap}/moment/min/moment.min.js"></script>
<script src="{$bootstrap}/bootstrap-daterangepicker/daterangepicker.js"></script> *}

<!-- morris.js -->
<script src="{$bootstrap}/raphael/raphael.min.js"></script>
<script src="{$bootstrap}/morris.js/morris.min.js"></script>

<!-- Custom Theme Scripts -->
<script src="{$pathJs}/fin/fin_dashboard_custom.js"></script>

<!-- Select2 -->
<script src="{$bootstrap}/select2-master/dist/js/select2.full.min.js"></script>

<script>
  $(document).ready(function() {
    $("#tipolanc.select2_multiple").select2({
      placeholder: "Tipo lançamento",
      allowClear: true,
      width: '100%',
    });
    $("#sitlanc.select2_multiple").select2({
      placeholder: "Escolha a Situação Documento",
      allowClear: true,
      with: '100%',
    });
    $("#filial.select2_multiple").select2({
      placeholder: "Escolha a filial",
      allowClear: true,
      with: '100%',
    });
  });
</script>
<!-- bootstrap-daterangepicker -->
<script src="js/moment/moment.min.js"></script>
<script src="js/datepicker/daterangepicker.js"></script>
<!-- daterangepicker -->
<script type="text/javascript">
  $('input[name="dataConsulta"]').daterangepicker({
      startDate: "{$dataIni}",
      endDate: "{$dataFim}",
      format: 'DD/MM/YYYY',
      ranges: {
        'Hoje': [moment(), moment()],
        'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
        'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
        'Este Mes': [moment().startOf('month'), moment().endOf('month')],
        'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      locale: {
        applyLabel: 'Confirma',
        cancelLabel: 'Limpa',
        fromLabel: 'Início',
        toLabel: 'Fim',
        customRangeLabel: 'Calendário',
        daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
        monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro',
          'Outubro', 'Novembro', 'Dezembro'
        ],
        firstDay: 1
      }

    },
    //funcao para recuperar o valor digitado        
    function(start, end, label) {
      f = document.dashboard;
      f.dataIni.value = start.format('DD/MM/YYYY');
      f.dataFim.value = end.format('DD/MM/YYYY');
      montaLetra();
    });
</script>

{assign var='data_flot_chart_json' value=$data_flot_chart|json_encode}
<script type="text/javascript">
    var flot_chart = {$data_flot_chart_json};
    init_flot_chart(flot_chart); // Chamada da função após atribuição da variável
</script>

{assign var='data_morris_charts_json' value=$data_morris_charts|json_encode}
<script type="text/javascript">
    var morris_charts = {$data_morris_charts_json};
    init_morris_charts(morris_charts); // Chamada da função após atribuição da variável
</script>

{assign var='data_init_gauge_json' value=$data_init_gauge|json_encode}
<script type="text/javascript">
    var init_gauge = {$data_init_gauge_json};
    init_gauge_vel(init_gauge); // Chamada da função após atribuição da variável
</script>

{assign var='data_chart_doughnut_json' value=$data_init_chart_doughnut|json_encode}
<script type="text/javascript">
    var chart_doughnut = {$data_chart_doughnut_json};
    init_chart_doughnut(chart_doughnut); // Chamada da função após atribuição da variável
</script>

