<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
require("common.php");

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');



if(date("N") <= 7) {
	$data_hoje = date("d/m/Y");
	if(date("N") == 1) {
		$data_ontem = date("d/m/Y", time() - 30 * 60 * 60 * 24);
	} else {
		$data_ontem = date("d/m/Y", time() - 30 * 60 * 60 * 24);
	}	$row = 1;
	//echo "https://ptax.bcb.gov.br/ptax_internet/consultaBoletim.do?method=gerarCSVFechamentoMoedaNoPeriodo&ChkMoeda=61&DATAINI=".$data_ontem."&DATAFIM=".$data_hoje;
	//die();
	if (($handle = fopen("https://ptax.bcb.gov.br/ptax_internet/consultaBoletim.do?method=gerarCSVFechamentoMoedaNoPeriodo&ChkMoeda=61&DATAINI=".$data_ontem."&DATAFIM=".$data_hoje, "r")) !== FALSE) {
		while (($taxa_usd = fgetcsv($handle, 1000, ";")) !== FALSE) {
			$num = count($taxa_usd);
			$row++;
			$taxa_data[] = $taxa_usd[0];
			$taxa_dolar_compra[] = $taxa_usd[4];
			$taxa_dolar_venda[] = $taxa_usd[5];

			$taxa_cambio[$row]["dia"] = $taxa_usd[0];
			$taxa_cambio[$row]["compra"] = str_replace(",",".",$taxa_usd[4]);
			$taxa_cambio[$row]["venda"] = str_replace(",",".",$taxa_usd[5]);
		}
		fclose($handle);
	}

/*
	echo "<pre>";
	print_r($taxa_cambio);
	echo "</pre>";
*/
/*
	echo $taxa_query = "INSERT INTO `taxa_dolar` (`id`, `dia`, `compra`, `venda`, `data`) VALUES (NULL, '".$taxa_data[0]."', '".str_replace(",",".",$taxa_dolar_compra[0])."', '".str_replace(",",".",$taxa_dolar_venda[0])."', '".date("Y-m-d H:i:s")."');";
	//if(!mysqli_query($conn,$taxa_query)) { die('Não foi possível gravar taxa do dólar: ' . mysqli_error($conn)); }
	echo "<br><br>";
	echo "https://ptax.bcb.gov.br/ptax_internet/consultaBoletim.do?method=gerarCSVFechamentoMoedaNoPeriodo&ChkMoeda=61&DATAINI=".$data_ontem."&DATAFIM=".$data_hoje;
	echo "<br><br>";
*/
}

?>
<!DOCTYPE html>
<html>
<head>
<style>

@media print{
	@page {
		size: landscape
	}
	html {
		margin:0 !important;
	}
	table, td, th {
		font-size: 7pt !important;
	}
}

html {
	margin:20px 25px;
}

body {
	font-family:Arial,sans-serif;
	font-size: 10pt;
	background-color:#FFF;
}
table {
	width: 100%;
    border-collapse: collapse;
}
table, td, th {
    /* border-bottom: 1px solid #888; */
	text-align: center;
	padding: 4px 5px;
}
th {
	background: #D5D5D5;
}
td {
    /* padding: 5px; */
	border-top: 1px solid #888;
}

hr {
	border-top: 2px solid #888;
	border-bottom: 0;
	border-left: 0;
	border-right: 0;
}

.footer {
    width: 100%;
    text-align: center;
    position: fixed;
}
h1 {
	font-size:18pt !important;
	margin-bottom: 0;
}
h2 {
	font-size:12pt !important;
	margin-top: 5px;
}
.verde {
	background-color:#b6e2b6;
}
.amarelo {
	background-color:#fcf5bb;
}
.azul {
	background-color:#a3ccf5;
}

</style>
<script src="js/jquery.js"></script>
<script src="js/jquery-ui.js"></script>
<title>Taxa câmbio - Dólar americano</title>
</head>

<body>
<?php
/*
$query_cambio = "SELECT DISTINCT `dia`,`compra`,`venda` FROM `taxa_dolar` ORDER BY `taxa_dolar`.`id` DESC LIMIT 0,90";
$result_cambio = mysqli_query($conn,$query_cambio);
*/
?>
<h1>Câmbio - Dólar Americano</h1>
<small>Últimos 30 dias - Fonte: <a style="text-decoration:none; color:#000;" href="http://www.bcb.gov.br" target="_blank">Banco Central do Brasil</a></small><br><br>

<table width="100%">
	<thead>
	<tr>
		<th>Data</th>
		<th>Taxa de compra</th>
		<th>Taxa de venda</th>
	</tr>
	</thead>
<?php
/*
while($cambio = mysqli_fetch_array($result_cambio)) {
*/
/*
echo "<pre>";
print_r($taxa_cambio);
echo "</pre>";
*/

krsort($taxa_cambio);
foreach($taxa_cambio AS $key => $cambio) {
	$cambio_dia = substr($cambio["dia"],-4)."-".substr($cambio["dia"],-6,2)."-".substr($cambio["dia"],-8,2);
?>
	<tr>
		<td><?php echo date("d/m/Y",strtotime($cambio_dia)); echo " - (".utf8_encode(strftime('%A', strtotime($cambio_dia))).")"; ?></td>
		<td>R$ <?php echo $cambio_compra = number_format($cambio["compra"],4,",","."); ?></td>
		<td>R$ <?php echo $cambio_venda = number_format($cambio["venda"],4,",","."); ?></td>
	</tr>
<?php } ?>
</table>
</body>
</html>