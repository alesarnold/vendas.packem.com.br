<?php 

    require("common.php"); 
//    require("../orcamentos/common.php"); 

    if(empty($_SESSION['user'])) { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    }

	if ($_SESSION['user']['nivel'] == '6' || $_SESSION['user']['nivel'] == '4') {
        header("Location: qualidade.php"); 
        die("Redirecting to qualidade.php"); 
	}


	$query_niveis = "SELECT nivel FROM niveis"; 
	try { 
		$stmt_niveis = $db->prepare($query_niveis); 
		$stmt_niveis->execute(); 
	}
	catch(PDOException $ex) { 
		die("Failed to run query: " . $ex->getMessage()); 
	} 
	$niveis = $stmt_niveis->fetchAll(); 
	$_SESSION['nivel'] = $niveis;

    if ($_SESSION['user']['nivel'] == '3')
    { 
        header("Location: orcamento.php?acao=novo"); 
	}

/*
	redirect("pedidos.php");
	die("Redirecionando para lista de orçamentos...");
*/

    require("cabecalho.php"); 

?>



        <div class="">
          <div class="page-title">
            <div class="title_left">
              <h3>Sistema de controle de orçamentos</h3>
            </div>
          </div>
          <div class="clearfix"></div>

          <div class="row">


            <!-- bar charts group -->
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Quantidade de orçamentos<small><font color="#f05223">Novos</font> <i class="fa fa-times"></i> <font color="#34495E">Aprovados</font> <i class="fa fa-times"></i> <font color="#ACADAC">Com pedidos</font></small></h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content1">
                  <div id="graph_bar_group" style="width:100%; height:280px;"></div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <!-- /bar charts group -->


            <!-- bar charts group -->
            <div class="col-md-8 col-sm-8 col-xs-12">
              <div class="x_panel">
				<form method="GET" id="data_seg">
				<div class="x_title">
                  <h2>Por segmentos</h2>
				  <div class="text-right"><input type="month" name="mes" onchange="document.getElementById('data_seg').submit()" class="form-control text-center" style="width:200px;height:30px;font-size:9pt;float:right;" value="<?php echo $_GET["mes"];?>"></div>
                  <div class="clearfix"></div>
                </div>
				</form>
                <div class="x_content1">
                  <div id="graph_bar_seg" class="col-md-12 col-sm-12 col-xs-12" style="height:280px;"></div>
				  
                </div>
              </div>
            </div>
            <!-- /bar charts group -->

            <!-- bar charts group -->
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2 style="width:100%;">Por fornecedoras</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content2">
<?php
/*
                  <div id="graph_bar_forn" style="width:100%; height:280px;"></div>
*/
?>
					<div id="graph_donut" class="col-md-12 col-sm-12 col-xs-12" style="height:280px;"></div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <!-- /bar charts group -->


            <!-- bar charts group -->
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Por modelos</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content1">
                  <div id="pormodelos" style="width:100%; height:420px;"></div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <!-- /bar charts group -->

<?php
/*
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Cidades atendidas<small>Principais cidades atendidas pela Bonsucesso Têxtil</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content1">
                  <div id="mapa_cidades" class="col-md-12 col-sm-12 col-xs-12" style="height:480px;"><iframe src="mapa/mapa.php" frameborder="0" style="border:0; display:block; width:100%; height:100%;" allowfullscreen></iframe></div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
*/
?>

		</div>
		</div>



<?php

function NovosMes($novo_ano,$novo_mes) {

	global $conn;

	$sql_novo .= "SELECT DISTINCT(`pedido`) AS `pedido`,`data` FROM `pedidos` WHERE `data` BETWEEN '".$novo_ano."-".$novo_mes."-01 00:00:00' ";
	if ($novo_mes == "12") {
		$novo_mes_mais = "01";
		$novo_ano = $novo_ano + 1;
	} else {
		$novo_mes_mais = sprintf('%02d', $novo_mes+1);
	}
	$sql_novo .= "and '".$novo_ano."-".$novo_mes_mais."-01 00:00:00'";

//	echo $sql_novo;

	$query_novos = mysqli_query($conn,$sql_novo);
	echo mysqli_num_rows($query_novos);
};

function AprovMes($aprov_ano,$aprov_mes) {

	global $conn;

	$sql_aprov .= "SELECT DISTINCT(`id_ped`) AS `pedido`,`data` FROM `pedidos_aprova` WHERE `data` BETWEEN '".$aprov_ano."-".$aprov_mes."-01 00:00:00' ";
	if ($aprov_mes == "12") {
		$aprov_mes_mais = "01";
		$aprov_ano = $aprov_ano + 1;
	} else {
		$aprov_mes_mais = sprintf('%02d', $aprov_mes+1);
	}
	$sql_aprov .= "and '".$aprov_ano."-".$aprov_mes_mais."-01 00:00:00'";

//	echo $sql_aprov;

	$query_aprov = mysqli_query($conn,$sql_aprov);
	echo mysqli_num_rows($query_aprov);
};

function PedMes($aprov_ano,$aprov_mes) {

	global $conn;

	$sql_ped .= "SELECT COUNT(DISTINCT(pedido)) as orc_aprov
		FROM qualidade
		WHERE data_entrega BETWEEN '".$aprov_ano."-".$aprov_mes."-01' ";
	if ($aprov_mes == "12") {
		$aprov_mes_mais = "01";
		$aprov_ano = $aprov_ano + 1;
	} else {
		$aprov_mes_mais = sprintf('%02d', $aprov_mes+1);
	}
	$sql_ped .= "and '".$aprov_ano."-".$aprov_mes_mais."-01'
		GROUP BY DATE_FORMAT(data_entrega, '%Y-%m')";

	// echo $sql_ped;

	$query_ped = mysqli_query($conn,$sql_ped);
	//echo mysqli_num_rows($query_aprov);
	$row_ped = mysqli_fetch_array($query_ped);
	echo number_format($row_ped["orc_aprov"],0,'','');
};


function QtdeSeg($id_seg) {

	global $conn;

	$data = explode("-",$_GET["mes"]);
	$ano = $data[0];
	$mes = $data[1];
	//echo "ANO: ".$ano." - MES: ".$mes;
	if($_GET["mes"]!="") {
		$query_seg = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos WHERE `segmento_cliente` LIKE '".$id_seg."' AND `status` NOT LIKE '0' AND YEAR(`data`) = $ano AND MONTH(`data`) = $mes";
	} else {
		$query_seg = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos WHERE `segmento_cliente` LIKE '".$id_seg."' AND `status` NOT LIKE '0'";
	}
    $sql_seg = mysqli_fetch_array(mysqli_query($conn,$query_seg));
    $total_seg = $sql_seg['num'];
    echo $total_seg;
};


function QtdeForn($id_forn) {

	global $conn;

	$query_total = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos WHERE `status` NOT LIKE '0'";
    $sql_total = mysqli_fetch_array(mysqli_query($conn,$query_total));
    $total = $sql_total['num'];



	$query_forn = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos WHERE `fornecedora` LIKE '".$id_forn."' AND `status` NOT LIKE '0'";
    $sql_forn = mysqli_fetch_array(mysqli_query($conn,$query_forn));
    $total_forn = $sql_forn['num'];
    $percent = $total_forn*100/$total;
    echo number_format($percent,1);
};



function QtdeModel($id_model) {

	global $conn;

	$query_forn = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos WHERE `corpo` LIKE '".$id_model."' AND `status` NOT LIKE '0'";
    $sql_forn = mysqli_fetch_array(mysqli_query($conn,$query_forn));
    $total_forn = $sql_forn['num'];

	return $total_forn;
};

?>

<!-- moris js -->
<script src="js/moris/raphael-min.js"></script>
<script src="js/moris/morris.min.js"></script>


<script>
$(document).ready(function() {

        Morris.Donut({
          element: 'graph_donut',
          data: [
            {label: 'Matriz', value: <?php QtdeForn('valor_sp'); ?>} <?php /* ,
            {label: 'Agro', value: <?php QtdeForn('valor_mg'); ?>},
            {label: 'Nordeste', value: <?php QtdeForn('valor_ba'); ?>},
            {label: 'BonPar', value: <?php QtdeForn('valor_bonpar'); ?>} */ ?>
          ],
          colors: ['#f05223', '#34495E', '#ACADAC', '#3498DB'],
          // colors: ['#26B99A', '#34495E', '#ACADAC', '#3498DB'],
          formatter: function (y) {
            return y + "%";
          },
          resize: true
        });


    var day_data = [
<?php
/*
		{month: 'Jul/2015', novos: <?php NovosMes('2015','07'); ?>, aprov: <?php AprovMes('2015','07'); ?>},
		{month: 'Ago/2015', novos: <?php NovosMes('2015','08'); ?>, aprov: <?php AprovMes('2015','08'); ?>},
		{month: 'Set/2015', novos: <?php NovosMes('2015','09'); ?>, aprov: <?php AprovMes('2015','09'); ?>},
		{month: 'Out/2015', novos: <?php NovosMes('2015','10'); ?>, aprov: <?php AprovMes('2015','10'); ?>},
		{month: 'Nov/2015', novos: <?php NovosMes('2015','11'); ?>, aprov: <?php AprovMes('2015','11'); ?>},
		{month: 'Dez/2015', novos: <?php NovosMes('2015','12'); ?>, aprov: <?php AprovMes('2015','12'); ?>},
*/
?>

		{month: 'Jan/2022', novos: <?php NovosMes('2022','01'); ?>, aprov: <?php AprovMes('2022','01'); ?>, ped: <?php PedMes('2022','01'); ?>},
		{month: 'Fev/2022', novos: <?php NovosMes('2022','02'); ?>, aprov: <?php AprovMes('2022','02'); ?>, ped: <?php PedMes('2022','02'); ?>},
		{month: 'Mar/2022', novos: <?php NovosMes('2022','03'); ?>, aprov: <?php AprovMes('2022','03'); ?>, ped: <?php PedMes('2022','03'); ?>},
		{month: 'Abr/2022', novos: <?php NovosMes('2022','04'); ?>, aprov: <?php AprovMes('2022','04'); ?>, ped: <?php PedMes('2022','04'); ?>},
		{month: 'Mai/2022', novos: <?php NovosMes('2022','05'); ?>, aprov: <?php AprovMes('2022','05'); ?>, ped: <?php PedMes('2022','05'); ?>},
		{month: 'Jun/2022', novos: <?php NovosMes('2022','06'); ?>, aprov: <?php AprovMes('2022','06'); ?>, ped: <?php PedMes('2022','06'); ?>},

		{month: 'Jul/2022', novos: <?php NovosMes('2022','07'); ?>, aprov: <?php AprovMes('2022','07'); ?>, ped: <?php PedMes('2022','07'); ?>},
		{month: 'Ago/2022', novos: <?php NovosMes('2022','08'); ?>, aprov: <?php AprovMes('2022','08'); ?>, ped: <?php PedMes('2022','08'); ?>},
		{month: 'Set/2022', novos: <?php NovosMes('2022','09'); ?>, aprov: <?php AprovMes('2022','09'); ?>, ped: <?php PedMes('2022','09'); ?>},
		{month: 'Out/2022', novos: <?php NovosMes('2022','10'); ?>, aprov: <?php AprovMes('2022','10'); ?>, ped: <?php PedMes('2022','10'); ?>},
		{month: 'Nov/2022', novos: <?php NovosMes('2022','11'); ?>, aprov: <?php AprovMes('2022','11'); ?>, ped: <?php PedMes('2022','11'); ?>},
		{month: 'Dez/2022', novos: <?php NovosMes('2022','12'); ?>, aprov: <?php AprovMes('2022','12'); ?>, ped: <?php PedMes('2022','12'); ?>}


    ];
    Morris.Bar({
        element: 'graph_bar_group',
        data: day_data,
        xkey: 'month',
        barColors: ['#f05223', '#34495E', '#ACADAC', '#3498DB'],
        // barColors: ['#26B99A', '#34495E', '#ACADAC', '#3498DB'],
        ykeys: ['novos', 'aprov', 'ped'],
        labels: ['Novos', 'Aprovados', 'Com pedido'],
        hideHover: 'auto',
        xLabelAngle: 45
    });



    var pormodelos = [
<?php
if(QtdeModel('qowa') != 0) { echo "		{modelo: 'Plano', modelqtde: ".QtdeModel('qowa')."},\n"; }
if(QtdeModel('cowa') != 0) { echo "		{modelo: 'Tubular', modelqtde: ".QtdeModel('cowa')."},\n"; }
if(QtdeModel('qowac') != 0) { echo "		{modelo: 'Painel U', modelqtde: ".QtdeModel('qowac')."},\n"; }
if(QtdeModel('qowacf') != 0) { echo "		{modelo: 'Painel U com forro', modelqtde: ".QtdeModel('qowacf')."},\n"; }
if(QtdeModel('qowad4') != 0) { echo "		{modelo: 'Travado com costuras nos cantos', modelqtde: ".QtdeModel('qowad4')."},\n"; }
if(QtdeModel('qowad8') != 0) { echo "		{modelo: 'Travado em gomos', modelqtde: ".QtdeModel('qowad8')."},\n"; }
if(QtdeModel('qowa2') != 0) { echo "		{modelo: 'Plano duplo', modelqtde: ".QtdeModel('qowa2')."},\n"; }
if(QtdeModel('cowa2') != 0) { echo "		{modelo: 'Tubular duplo', modelqtde: ".QtdeModel('cowa2')."},\n"; }
if(QtdeModel('qowaf') != 0) { echo "		{modelo: 'Plano com forro', modelqtde: ".QtdeModel('qowaf')."},\n"; }
if(QtdeModel('qowadlf') != 0) { echo "		{modelo: 'Plano com forro travado', modelqtde: ".QtdeModel('qowadlf')."},\n"; }
if(QtdeModel('cowaf') != 0) { echo "		{modelo: 'Tubular com forro', modelqtde: ".QtdeModel('cowaf')."},\n"; }
if(QtdeModel('qowao') != 0) { echo "		{modelo: 'Plano condutivo', modelqtde: ".QtdeModel('qowao')."},\n"; }
if(QtdeModel('qowafi') != 0) { echo "		{modelo: 'Plano com forro VCI', modelqtde: ".QtdeModel('qowafi')."},\n"; }
if(QtdeModel('cowafi') != 0) { echo "		{modelo: 'Tubular com forro VCI', modelqtde: ".QtdeModel('cowafi')."},\n"; }
if(QtdeModel('qowam') != 0) { echo "		{modelo: 'Plano antimicrobiano', modelqtde: ".QtdeModel('qowam')."},\n"; }
if(QtdeModel('qowaa') != 0) { echo "		{modelo: 'Plano arejado', modelqtde: ".QtdeModel('qowaa')."},\n"; }
if(QtdeModel('qowat') != 0) { echo "		{modelo: 'Plano térmico', modelqtde: ".QtdeModel('qowat')."},\n"; }
if(QtdeModel('qhe') != 0) { echo "		{modelo: 'Plano com fechamento especial', modelqtde: ".QtdeModel('qhe')."},\n"; }
if(QtdeModel('qhe_ref') != 0) { echo "		{modelo: 'Plano QHE reforçado com fita', modelqtde: ".QtdeModel('qhe_ref')."},\n"; }
if(QtdeModel('rof') != 0) { echo "		{modelo: 'Porta ensacado simples', modelqtde: ".QtdeModel('rof')."},\n"; }
if(QtdeModel('qms') != 0) { echo "		{modelo: 'Plano com duas alças', modelqtde: ".QtdeModel('qms')."},\n"; }
if(QtdeModel('cms') != 0) { echo "		{modelo: 'Tubular com duas alças', modelqtde: ".QtdeModel('cms')."},\n"; }
if(QtdeModel('outros') != 0) { echo "		{modelo: 'Outro', modelqtde: ".QtdeModel('outros')."}\n"; }
?>
    ];
    Morris.Bar({
        element: 'pormodelos',
        data: pormodelos,
        xkey: 'modelo',
        barColors: ['#ACADAC'],
        ykeys: ['modelqtde'],
        labels: ['Qtde'],
        hideHover: 'auto',
        xLabelAngle: 70
    });


    var seg_data = [
		{segmento: 'Alimentício', segs: <?php QtdeSeg('1'); ?>},
		{segmento: 'Café', segs: <?php QtdeSeg('2'); ?>},
		{segmento: 'Carga perigosa', segs: <?php QtdeSeg('3'); ?>},
		{segmento: 'Exportação', segs: <?php QtdeSeg('4'); ?>},
		{segmento: 'Fertilizante', segs: <?php QtdeSeg('5'); ?>},
		{segmento: 'Grãos / sementes', segs: <?php QtdeSeg('6'); ?>},
		{segmento: 'Minérios', segs: <?php QtdeSeg('7'); ?>},
		{segmento: 'Petroquímico', segs: <?php QtdeSeg('8'); ?>},
		{segmento: 'Químico', segs: <?php QtdeSeg('9'); ?>},
		{segmento: 'Usinas', segs: <?php QtdeSeg('10'); ?>},
		{segmento: 'Diversos', segs: <?php QtdeSeg('11'); ?>}
    ];
    Morris.Bar({
        element: 'graph_bar_seg',
        data: seg_data,
        xkey: 'segmento',
        barColors: ['#555555'],
        //barColors: ['#34495E', '#26B99A', '#ACADAC', '#3498DB'],
        ykeys: ['segs'],
        labels: ['Total'],
        hideHover: 'auto',
        xLabelAngle: 45
    });

/*
    var forn_data = [
		{fornecedora: 'Têxtil', forn: <?php QtdeForn('valor_sp'); ?>},
		{fornecedora: 'Agro', forn: <?php QtdeForn('valor_mg'); ?>},
		{fornecedora: 'Nordeste', forn: <?php QtdeForn('valor_ba'); ?>},
		{fornecedora: 'BonPar', forn: <?php QtdeForn('valor_bonpar'); ?>}
    ];
    Morris.Bar({
        element: 'graph_bar_forn',
        data: forn_data,
        xkey: 'fornecedora',
        barColors: ['#26B99A', '#34495E', '#ACADAC', '#3498DB'],
        ykeys: ['forn'],
        labels: ['Total'],
        hideHover: 'auto',
        xLabelAngle: 45
    });
*/

});


</script>


<?php
require("rodape.php");
?>