<?php

if (!defined('ADMpath')){
    exit;
} 

$dir = (__DIR__);

include_once($dir."/../../../smarty/libs/Smarty.class.php");
include_once($dir."/../../class/fin/c_lancamento.php");
require_once($dir."/../../class/fin/c_saldo.php");

//Class p_dashboard
Class p_dashboard extends c_lancamento {

    private $m_submenu           = NULL;
    private $m_opcao             = NULL;
    private $m_letra             = NULL;
    private $m_parmPost          = NULL;
    private $m_parmGet           = NULL;
    private $m_par               = NULL;
    private $m_tipLanc           = NULL;
    private $m_sitLanc           = NULL;
    private $m_dataIni           = NULL;
    private $m_dataFim           = NULL;
    private $m_dataRef           = NULL;
    private $m_filial            = NULL;
    private $m_temp              = NULL;
    private $arrayDebito         = [];
    private $arrayCredito        = [];
    private $totalPeridoDebito   = NULL;
    private $totalPeriodoCredito = NULL;
    private $totalDiaDebito      = NULL;
    private $totalDiaCredito     = NULL;
    public  $smarty              = NULL;
    private $cores               = ["#BDC3C7","#22dd21","#100d0d","#26B99A","#f00b39","#F39C12","#831abc","#5b9418","#2ECC71", "#e622e6"];

    //---------------------------------------------------------------
    function __construct(){

        // Cria uma instancia variaveis de sessao
        session_start();
        c_user::from_array($_SESSION['user_array']);

        // Cria uma instancia do Smarty
        $this->smarty = new Smarty;

        //Assim obtém os dados passando pelo filtro contra INJECTION ( segurança PHP )
        $this->m_parmPost = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $this->m_parmGet = filter_input_array(INPUT_GET, FILTER_DEFAULT);  

        // caminhos absolutos para todos os diretorios do Smarty
        $this->smarty->template_dir = ADMraizFonte . "/template/fin";
        $this->smarty->compile_dir = ADMraizCliente . "/smarty/templates_c/";
        $this->smarty->config_dir = ADMraizCliente . "/smarty/configs/";
        $this->smarty->cache_dir = ADMraizCliente . "/smarty/cache/";

        // caminhos absolutos para todos os diretorios biblioteca e sistema
        $this->smarty->assign('pathJs',  ADMhttpBib.'/js');
        $this->smarty->assign('bootstrap', ADMbootstrap);
        $this->smarty->assign('raizCliente', $this->raizCliente);

        $this->smarty->assign('titulo', "Dashboard");
        $this->smarty->assign('colVis', "[ 0, 1, 2 ]"); 
        $this->smarty->assign('disableSort', "[ 2 ]"); 
        $this->smarty->assign('numLine', "25");

        // inicializa variaveis de controle
        // Variável 'submenu'
        if (isset($this->m_parmGet['submenu'])) {
            $this->m_submenu = $this->m_parmGet['submenu'];
        } elseif (isset($this->m_parmPost['submenu'])) {
            $this->m_submenu = $this->m_parmPost['submenu'];
        } else {
            $this->m_submenu = null;
        }

        // Variável 'opcao'
        if (isset($this->m_parmGet['opcao'])) {
            $this->m_opcao = $this->m_parmGet['opcao'];
        } elseif (isset($this->m_parmPost['opcao'])) {
            $this->m_opcao = $this->m_parmPost['opcao'];
        } else {
            $this->m_opcao = null;
        }

        // Variável 'letra'
        if (isset($this->m_parmGet['letra'])) {
            $this->m_letra = $this->m_parmGet['letra'];
        } elseif (isset($this->m_parmPost['letra'])) {
            $this->m_letra = $this->m_parmPost['letra'];
        } else {
            $this->m_letra = null;
        }

        $this->m_par = explode("|", $this->m_letra);

        ############# Variáveis populadas pela variável $this->m_par #############
        // Data referencia 
        if (isset($this->m_par[3]) and $this->m_par[3] !== "") {
            $this->m_dataRef = ltrim(rtrim($this->m_par[3], ','), ',');
        } else {
            $this->m_dataRef = 'vencimento';
        }


        // Tipo lancamento 
        if (isset($this->m_par[9]) and $this->m_par[9] !== "") {
            $this->m_tipLanc = ltrim(rtrim($this->m_par[9], ','), ',');
        } else{
            $this->m_tipLanc = 'R,P'; // recebimento
        }

        // Situacao lancamento 
        if (isset($this->m_par[5]) and $this->m_par[5] !== "") {
            $this->m_sitLanc = ltrim(rtrim($this->m_par[5], ','), ',');
        } else{
            $this->m_sitLanc = 'A'; // baixado
        }

        // filial 
        if (isset($this->m_par[7]) and $this->m_par[7] !== "") {
            $this->m_filial = ltrim(rtrim($this->m_par[7], ','), ',');
        } else{
            $this->m_filial = $this->m_empresacentrocusto; // baixado
        }

        // data inicial
        if(isset($this->m_par[0]) and $this->m_par[0] !== ""){
            $this->m_dataIni = $this->m_par[0];
        }else{
            $this->m_dataIni = date("01/m/Y");
        }

        // data final
        if(isset($this->m_par[1]) and $this->m_par[1] !== "") {
            $this->m_dataFim = $this->m_par[1];
        }else{
            $mes = date("m");
            $ano = date("Y");
            $data = date("d/m/Y", mktime(0, 0, 0, $mes+1, 0, $ano));
            $this->m_dataFim = $data;
        }

    }

    /**
     * <b> É responsavel para indicar para onde o sistema ira executar </b>
    * @name controle
    * @param VARCHAR submenu 
    * @return vazio
    */
    function controle(){
    switch ($this->m_submenu){
        // case 'chart_plot_01':
        //     //busca lancamentos
        //     $lanc = $this->select_lancamento_letra($this->m_letra);
        //     //verifica se existe, caso na existe retorna false
        //     if(!is_array($lanc)){
        //         echo 'false';
        //     }
        //     //processa e monta o array para envio
        //     $result = $this->processaLanc($lanc);

        //     //verifica se foi populado se não insere 0.00
        //     $this->totalDiaDebito = $this->totalDiaDebito !== null ? $this->totalDiaDebito : 1900.00;
        //     $this->totalDiaCredito = $this->totalDiaCredito !== null ? $this->totalDiaCredito : 1800.00;
        //     $this->totalPeridoDebito = $this->totalPeridoDebito !== null ? $this->totalPeridoDebito : 0.00;
        //     $this->totalPeridoCredito = $this->totalPeridoCredito !== null ? $this->totalPeridoCredito : 0.00;

        //     if($result){

        //         //dados grafico
        //         $dados_completos = array(
        //             "array_debito" => $this->arrayDebito,
        //             "array_credito" => $this->arrayCredito,
        //             "total_periodo_debito" => $this->totalPeridoDebito,
        //             "total_periodo_credito" => $this->totalPeridoCredito,
        //             "total_dia_debito" => $this->totalDiaDebito,
        //             "total_dia_credito" => $this->totalDiaCredito
        //         );


        //         //converte em json
        //         $json_dados_completos = json_encode($dados_completos);
        //         //envia json
        //         header('Content-Type: application/json');
        //         echo $json_dados_completos;
        //     }

        // break;
        // case 'chart_doughnut':
        //     $consulta = new c_banco();
        //     $sql = "SELECT conta as id, nomeinterno as descricao FROM fin_conta  where status ='A'";
        //     $consulta->exec_sql($sql);
        //     $consulta->close_connection();
        //     $contas = $consulta->resultado;
        //     $this->m_dataIni = "01/03/2024";
        //     $objConta = new c_saldo;
        //     //pesquisa e monta o array de saldos conta
        //     for($i = 0; $i < count($contas); $i++){
        //         //monta parametros para consulta
        //         $ltr = $this->m_dataIni . "|" . $contas[$i]['ID'];
        //         //consulta saldo
        //         $buscaSaldosBancarios =  $objConta->newSadoContaAtual($ltr);
        //         //popula o array apenas das contas que possui valor valido
        //         if($buscaSaldosBancarios[0]['SALDO'] !== null and $buscaSaldosBancarios[0]['SALDO'] !== "0.00" ){
                    
        //             $resultSaldos[$contas[$i]['DESCRICAO']] = array(
        //                 'saldo' => $buscaSaldosBancarios[0]['SALDO']
        //             );
        //             $totalSaldo += $buscaSaldosBancarios[0]['SALDO'];
        //         }
        //     }

        //     //sets variaveis
        //     $percentualTabela = array();
        //     $dataPercentual   = array();
        //     $backgroundColor  = array();
        //     $labels           = array();
        //     $percentual = null;
        //     $count = 0;

        //     foreach ($resultSaldos as $index => &$conta) {
        //         //calcula o percentual
        //         $percentual = round(($conta['saldo'] / $totalSaldo) * 100, 4);
        //         //popula percentual para tabela que podera ser menor que zero
        //         array_push($percentualTabela, $percentual);
        //         //popula a label ou seja o nome do banco
        //         array_push($labels, $index);
        //         //condicao para pode plotar no grafico, pois nao plota menor que zero
        //         if($percentual < 1){
        //             $percentual = 1;
        //         }
        //         //popula o percentual do banco
        //         array_push($dataPercentual, $percentual);
        //         //popula o background do banco com o array private pre definino no construtor
        //         array_push($backgroundColor, $this->cores[$count]);
        //         $count ++;
        //     }

        //     //monta array final
        //     $data_init_chart_doughnut = array(
        //         "labels" => $labels,
        //         "dataTabela" => $percentualTabela,
        //         "datasets" =>  array(
        //             "data" => $dataPercentual,
        //             "backgroundColor" => $backgroundColor
        //         )
        //     );

        //     //converte em json
        //     $json_dados_completos = json_encode($data_init_chart_doughnut);
        //     //envia json
        //     header('Content-Type: application/json');
        //     echo $json_dados_completos;
        // break;
        // case 'morris_charts':
        //     $searchCenters = $this->searchTotalReceiptPayment(null, $this->m_dataIni, $this->m_dataFim);

        //     if(is_array($searchCenters)){
        //         $dados_completos = array();
        //         foreach($searchCenters as $center){
        //             $dados_temp = array(
        //                 "centroCusto"=> $center['DESCRICAO'],
        //                 "c" => $center['RECEBIMENTO'],
        //                 "d" => $center['PAGAMENTO']
        //             );
        //             array_push($dados_completos, $dados_temp);
        //         }
        //     }else{
        //         $dados_completos = '404';
        //     }

        //     //converte em json
        //     $json_dados_completos = json_encode($dados_completos);
        //     //envia json
        //     header('Content-Type: application/json');
        //     echo $json_dados_completos;
        // break;
        // case 'init_gauge':
        //     $searchTotal = $this->searchTotalCD(null, $this->m_dataIni, $this->m_dataFim);

        //     if(is_array($searchTotal)){
        //         //CALCULA O TOTAL DE CREDITO BAIXADO
        //         $total_credito = $searchTotal[0]["RECEBIMENTO_ABERTO"] + $searchTotal[0]["RECEBIMENTO_BAIXADO"];
        //         if ($total_credito > 0) {
        //             $percentual_credito_baixado = round(($searchTotal[0]["RECEBIMENTO_BAIXADO"] / $total_credito) * 100, 2);
        //         } else {
        //             $percentual_credito_baixado = 0; // Evita divisão por zero
        //         }

        //         //CALCULA O TOTAL DE DEBITO BAIXADO
        //         $total_debito = $searchTotal[0]["PAGAMENTO_ABERTO"] + $searchTotal[0]["PAGAMENTO_BAIXADO"];
        //         if ($total_credito > 0) {
        //             $percentual_debito_baixado = round(($searchTotal[0]["PAGAMENTO_BAIXADO"] / $total_debito) * 100, 2);
        //         } else {
        //             $percentual_debito_baixado = 0; // Evita divisão por zero
        //         }

        //         $dados_completos = array(
        //             "percentual_credito_baixado"=> $percentual_credito_baixado,
        //             "percentual_debito_baixado" => $percentual_debito_baixado
        //         );

        //     }else{
        //         $dados_completos = '404';
        //     }

        //     //converte em json
        //     $json_dados_completos = json_encode($dados_completos);
        //     //envia json
        //     header('Content-Type: application/json');
        //     echo $json_dados_completos;
        // break;
        default:
            $this->mostraDashboard('');

    }

    } // fim controle

 /**
 * <b> Desenha form de cadastro ou alteração Genero. </b>
 * @param String $mensagem mensagem que ira apresentar na tela no caso de erro ou msg de aviso ao usuário
 * @param String $tipoMsg tipo da mensagem sucesso/alerta
 */
function mostraDashboard($mensagem=NULL){

    $this->smarty->assign('subMenu', $this->m_submenu);
    $this->smarty->assign('letra', $this->m_letra);
    $this->smarty->assign('mensagem', $mensagem);
    $this->smarty->assign('dataIni', $this->m_dataIni);
    $this->smarty->assign('dataFim', $this->m_dataFim);


    // lista de datas.
    $this->smarty->assign('datas_ids', array('nao','lancamento','emissao', 'vencimento', 'pagamento'));
    $this->smarty->assign('datas_names', array('N&atilde;o Considera','Lan&ccedil;amento','Emiss&atilde;o','Vencimento','Movimento'));
    if($this->m_par[3] == ""){
        $this->smarty->assign('datas_id', $this->m_dataRef);
    }else{
        $this->smarty->assign('datas_id', $this->m_par[3]);
    }

    // tipo lancamento
    $consulta = new c_banco();
    $sql = "select tipo as id, padrao as descricao from amb_ddm where (alias='FIN_MENU') and (campo='TipoLanc')";
    $consulta->exec_sql($sql);
    $consulta->close_connection();
    $result = $consulta->resultado;
    for ($i=0; $i < count($result); $i++){
        $tipoLanc_ids[$i] = $result[$i]['ID'];
        $tipoLanc_names[$i] = ucwords(strtolower($result[$i]['DESCRICAO']));
    }
    $this->smarty->assign('tipoLanc_ids', $tipoLanc_ids);
    $this->smarty->assign('tipoLanc_names', $tipoLanc_names);
    $this->m_temp = explode(",", $this->m_tipLanc);
    $this->m_tipLanc = $this->m_temp;
    $this->smarty->assign('tipoLanc_id', $this->m_temp);

    // situacao lancamento
    $consulta = new c_banco();
    $sql = "select tipo as id, padrao as descricao from amb_ddm where (alias='FIN_MENU') and (campo='SituacaoPgto')";
    $consulta->exec_sql($sql);
    $consulta->close_connection();
    $result = $consulta->resultado;
    for ($i=0; $i < count($result); $i++){
        $situacaoLanc_ids[$i] = $result[$i]['ID'];
        $situacaoLanc_names[$i] = ucwords(strtolower($result[$i]['DESCRICAO']));
    }
    $this->smarty->assign('situacaoLanc_ids', $situacaoLanc_ids);
    $this->smarty->assign('situacaoLanc_names', $situacaoLanc_names);
    $this->smarty->assign('situacaoLanc_id', $this->m_sitLanc);	


    // filial
    $consulta = new c_banco();
    $sql = "select centrocusto as id, descricao from fin_centro_custo where (ativo='S')";
    $consulta->exec_sql($sql);
    $consulta->close_connection();
    $result = $consulta->resultado;
    for ($i=0; $i < count($result); $i++){
        $filial_ids[$i] = $result[$i]['ID'];
        $filial_names[$i] = ucwords(strtolower($result[$i]['DESCRICAO']));
    }
    $this->smarty->assign('filial_ids', $filial_ids);
    $this->smarty->assign('filial_names', $filial_names);
    $this->smarty->assign('filial_id', $this->m_filial);


    
    //Monta letra para pesquisa 
    //(par[0] - data inicial | data final | pessoa | data referencia | qtd sl |situacao lancamento | qtd filial| filial | qtd tl | tipo lancamento | situacao documento | conta | genero pagamento)
    //"01/04/2024|30/04/2024|0|vencimento|2|A|N|0|2|P|R|0|0||0"
    if($this->m_letra == null){
        $this->m_letra  = $this->m_dataIni . "|";
        $this->m_letra .= $this->m_dataFim . "|0|"; 
        $this->m_letra .= $this->m_dataRef . "|";
        $this->m_letra .= "1|". $this->m_sitLanc .'|';
        $this->m_letra .= "1|".$this->m_filial  . '|0|0|0|0|0';
    }

    //////////////////////  PROCESSO FLOT_CHART  //////////////////////
    //busca lancamentos
    $lanc = $this->select_lancamento_letra($this->m_letra);
    //verifica se existe, caso na existe retorna false
    if(!is_array($lanc)){
        $data_flot_chart = 'false';
    }else{
        //processa e monta o array para envio
        $result = $this->processaLanc($lanc);

        //verifica se foi populado se não insere 0.00
        $this->totalDiaDebito = $this->totalDiaDebito !== null ? $this->totalDiaDebito : 1900.00;
        $this->totalDiaCredito = $this->totalDiaCredito !== null ? $this->totalDiaCredito : 1800.00;
        $this->totalPeridoDebito = $this->totalPeridoDebito !== null ? $this->totalPeridoDebito : 0.00;
        $this->totalPeridoCredito = $this->totalPeridoCredito !== null ? $this->totalPeridoCredito : 0.00;

        if($result){

            //dados grafico
            $dados_completos = array(
                "array_debito" => $this->arrayDebito,
                "array_credito" => $this->arrayCredito,
                "total_periodo_debito" => $this->totalPeridoDebito,
                "total_periodo_credito" => $this->totalPeridoCredito,
                "total_dia_debito" => $this->totalDiaDebito,
                "total_dia_credito" => $this->totalDiaCredito
            );

            $data_flot_chart = $dados_completos;
        }else{
            $data_flot_chart = 404;
        }
    }

    $this->smarty->assign('data_flot_chart', $data_flot_chart);

    //////////////////////  END FLOT_CHART  //////////////////////

    //////////////////////  PROCESSO MORRIS_CHARTS  //////////////////////
    $searchCenters = $this->searchTotalReceiptPayment(null, $this->m_dataIni, $this->m_dataFim);

    if(is_array($searchCenters)){
        $temp_morris_charts = array();
        foreach($searchCenters as $center){
            $data_temp = array(
                "centroCusto"=> $center['DESCRICAO'],
                "c" => $center['RECEBIMENTO'],
                "d" => $center['PAGAMENTO']
            );
            array_push($temp_morris_charts, $data_temp);
        }
    }else{
        $temp_morris_charts = 404;
    }

    $data_morris_charts = $temp_morris_charts;
    $this->smarty->assign('data_morris_charts', $data_morris_charts);
    ////////////////////// END MORRIS_CHARTS //////////////////////


    //////////////////////  PROCESSO INIT_GAUGE  //////////////////////
    $searchTotal = $this->searchTotalCD(null, $this->m_dataIni, $this->m_dataFim);
    if(is_array($searchTotal)){
        //CALCULA O TOTAL DE CREDITO BAIXADO
        $total_credito = $searchTotal[0]["RECEBIMENTO_ABERTO"] + $searchTotal[0]["RECEBIMENTO_BAIXADO"];
        if ($total_credito > 0) {
            $percentual_credito_baixado = round(($searchTotal[0]["RECEBIMENTO_BAIXADO"] / $total_credito) * 100, 2);
        } else {
            $percentual_credito_baixado = 0; // Evita divisão por zero
        }

        //CALCULA O TOTAL DE DEBITO BAIXADO
        $total_debito = $searchTotal[0]["PAGAMENTO_ABERTO"] + $searchTotal[0]["PAGAMENTO_BAIXADO"];
        if ($total_credito > 0) {
            $percentual_debito_baixado = round(($searchTotal[0]["PAGAMENTO_BAIXADO"] / $total_debito) * 100, 2);
        } else {
            $percentual_debito_baixado = 0; // Evita divisão por zero
        }

        $data_init_gauge = array(
            "percentual_credito_baixado"=> $percentual_credito_baixado,
            "percentual_debito_baixado" => $percentual_debito_baixado
        );

    }else{
        $data_init_gauge = 404;
    }
    $this->smarty->assign('data_init_gauge', $data_init_gauge);
    //////////////////////  END INIT_GAUGE  //////////////////////


    //////////////////////  PROCESSO CHART_DOUGHNUT  //////////////////////
    $consulta = new c_banco();
    $sql = "SELECT conta as id, nomeinterno as descricao FROM fin_conta  where status ='A'";
    $consulta->exec_sql($sql);
    $consulta->close_connection();
    $contas = $consulta->resultado;
    $this->m_dataIni = "01/03/2024";
    $objConta = new c_saldo;
    //pesquisa e monta o array de saldos conta
    for($i = 0; $i < count($contas); $i++){
        //monta parametros para consulta
        $ltr = $this->m_dataIni . "|" . $contas[$i]['ID'];
        //consulta saldo
        $buscaSaldosBancarios =  $objConta->newSadoContaAtual($ltr);
        //popula o array apenas das contas que possui valor valido
        if($buscaSaldosBancarios[0]['SALDO'] !== null and $buscaSaldosBancarios[0]['SALDO'] !== "0.00" ){
            
            $resultSaldos[$contas[$i]['DESCRICAO']] = array(
                'saldo' => $buscaSaldosBancarios[0]['SALDO']
            );
            $totalSaldo += $buscaSaldosBancarios[0]['SALDO'];
        }
    }

    //sets variaveis
    $percentualTabela = array();
    $dataPercentual   = array();
    $backgroundColor  = array();
    $labels           = array();
    $percentual = null;
    $count = 0;

    foreach ($resultSaldos as $index => &$conta) {
        //calcula o percentual
        $percentual = round(($conta['saldo'] / $totalSaldo) * 100, 4);
        //popula percentual para tabela que podera ser menor que zero
        array_push($percentualTabela, $percentual);
        //popula a label ou seja o nome do banco
        array_push($labels, $index);
        //condicao para pode plotar no grafico, pois nao plota menor que zero
        if($percentual < 1){
            $percentual = 1;
        }
        //popula o percentual do banco
        array_push($dataPercentual, $percentual);
        //popula o background do banco com o array private pre definino no construtor
        array_push($backgroundColor, $this->cores[$count]);
        $count ++;
    }

    //monta array final
    $data_init_chart_doughnut = array(
        "labels" => $labels,
        "dataTabela" => $percentualTabela,
        "datasets" =>  array(
            "data" => $dataPercentual,
            "backgroundColor" => $backgroundColor
        )
    );
    $this->smarty->assign('data_init_chart_doughnut', $data_init_chart_doughnut);
    ////////////////////// END CHART_DOUGHNUT //////////////////////
    
    $this->smarty->display('fin_dashboard.tpl');

}//fim

function processaLanc($lanc){
    $totaisPorDia = array();
    foreach ($lanc as $lancamento) {
        $dataLancamento = $lancamento['DATEORDER'];

        // Verifique se já existe uma entrada para esse dia no array de totais
        if (!isset($totaisPorDia[$dataLancamento])) {
            // Se não existir, inicialize os totais para o dia
            $totaisPorDia[$dataLancamento] = array(
                'pagamentos' => 0,
                'recebimentos' => 0
            );
        }

        // Verifique o tipo de lançamento
        if ($lancamento['TIPOLANCAMENTO'] == 'PAGAMENTO') {
            // Some o valor ao total de pagamentos para esse dia
            $totaisPorDia[$dataLancamento]['pagamentos'] += $lancamento['TOTAL'];
        } elseif ($lancamento['TIPOLANCAMENTO'] == 'RECEBIMENTO') {
            // Some o valor ao total de recebimentos para esse dia
            $totaisPorDia[$dataLancamento]['recebimentos'] += $lancamento['TOTAL'];
        }
    }

    $result = $this->montaArray($totaisPorDia);
    if($result){
        return true;
    }
}

function montaArray($totaisPorDia){
    foreach ($totaisPorDia as $data => $valores) {

        //INFOS DO GRAFICO
        $pagamentos = $valores['pagamentos'];
        $recebimentos = $valores['recebimentos'];
        //formata data
        $dataFormatada = date('Y, n, j', strtotime($data));
        // Adicionar a data e os valores de pagamentos
        $this->arrayDebito[] = "(" . $dataFormatada . ")," . $pagamentos;
        // Adicionar a data e os valores de recebimentos
        $this->arrayCredito[] = "(" . $dataFormatada . ")," . $recebimentos;
        //FIM INFOS GRAFICO

        //TOTAL PERIODO
        $this->totalPeridoDebito += $valores['pagamentos'];
        $this->totalPeridoCredito += $valores['recebimentos'];
        //FIM TOTAL PERIODO

        //TOTAL DO DIA
        if($data == date('Y-m-d')){
            $this->totalDiaDebito += $valores['pagamentos'];
            $this->totalDiaCredito += $valores['recebimentos'];
        }

    }
    return true;
}
//-------------------------------------------------------------
}
//	END OF THE CLASS
 /**
 * <b> Rotina principal - cria classe. </b>
 */
$dashboard = new p_dashboard();                    
$dashboard->controle();
?>
