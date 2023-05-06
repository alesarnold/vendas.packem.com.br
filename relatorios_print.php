<?php 

    require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 
/*
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	//if (substr($ip, 0, 9) != "187.72.86") {
	if (substr($ip, 0, 11) != "189.112.107") {
		Log_Rel($_SESSION['user']['id'],$_SESSION['user']['nome'],"Tentou acessar o relatório de follow up fora da empresa.",$ip);
		$erro = "Erro acessar o conteudo.<br><br>Por gentileza contate o administrador.";
		unset($_SESSION['user']);
		header("Location: mensagem.php?msg=".$erro); 
		die();
	} else {
		Log_Rel($_SESSION['user']['id'],$_SESSION['user']['nome'],"Imprimiu o relatório de follow up.",$ip);
	}
*/
?>
<!DOCTYPE html>
<html>
<head>
<style>

@page {
	size: A4 landscape;
	margin: 10pt;
}

/* body {     width: auto;     border: 0;     margin: 0 5%;     padding: 0;     float: none;     position: static;     overflow: visible; } */


body {
	font-family:Arial,sans-serif;
	font-size: 7pt;
	overflow: visible;
	position: static;
	float: none;
	border: 0;
	margin: 1%;
}

table {
	width: 100%;
    border-collapse: collapse;
}
table, td, th {
    border-bottom: 1px solid #ccc;
    /* padding: 5px 0; */
}

th, tr, td {
	page-break-inside: avoid;
}

th {
	text-align: left;
    padding: 5px 3px;
}

td {
    padding: 3px 5px;
}

hr {
	border-top: 2px solid #888;
	border-bottom: 0;
	border-left: 0;
	border-right: 0;
}

.tab_orcamento {
	margin-top: 5px;
	margin-bottom: 30px;
	width: 100%;
	font-size:8pt;
}
.tab_orcamento td {
	border: 1px solid #CCC;
	padding: 1px 5px;
}

.desenho {
	width: 357px;
	height: 564px;
	position: absolute;
	top: 0;
	left: 0;
}

.medidas {
	position: absolute;
	font-family: Arial, sans-serif;
	color: #444444;
	font-size: 8pt;
	width: 28px;
	height: 20px;
	text-align: right;
	overflow: hidden;
}

.legenda {
	list-style: none;
}

.legenda li {
    list-style: none;
    display: inline;
    padding: 0 10px;
}

.footer {
    width: 100%;
    text-align: center;
    position: fixed;
}
  
  
</style>
</head>

<body onload="window.print()">
<?php /* <body onload="window.print()"> */ ?>

<?php

if ($_SESSION['user']['nivel'] != '1' and $_SESSION['user']['nivel'] != '2') {

    echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
	require("rodape.php");
    die(); 
}


$query_vendedor = mysqli_query($conn,"SELECT * FROM `users` ORDER BY `nome` ASC");

while($vendedores = mysqli_fetch_array($query_vendedor)) {

	$vendedor[$vendedores["id"]]["id"] = $vendedores["id"];
	$vendedor[$vendedores["id"]]["nome"] = $vendedores["nome"];
	$vendedor[$vendedores["id"]]["email"] = $vendedores["email"];
	$vendedor[$vendedores["id"]]["celular"] = $vendedores["celular"];
}

$status[1] = "Em análise";
$status[2] = "Aguardando liberação";
$status[3] = "Liberado";



$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `id_vend` LIKE '%".$_GET["id_vend"]."%' AND `status` LIKE '1' OR `status` LIKE '2'";


$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;


//$per_page = (int)(!isset($_GET["qtde"]) ? 50 : $_GET["qtde"]); // 10; // Set how many records do you want to display per page.
$per_page = 50;

$startpoint = ($page * $per_page) - $per_page;

if ($_GET["data_inicio"] != "" || $_GET["data_final"] != "" || $_GET["pesq"] != "" || $_GET["num_orc"] != "" || $_GET["tel"] != "" || $_GET["id_vend"] != "" || $_GET["dim"] != "" || $_GET["qtde"] != "" || $_GET["ref"] != "" || $_GET["rep"] != "") {
	if ($_GET["data_inicio"] != "" && $_GET["data_final"] != "") {
		$strpesq1 = "`F`.`data` BETWEEN '".$_GET["data_inicio"]." 00:00:00' AND '".$_GET["data_final"]." 23:59:59' AND ";
	}
	if ($_GET["pesq"] != "") {
		$strpesq2 = "`x`.`nome_cliente` LIKE '%".$_GET["pesq"]."%' AND ";
	}
	if ($_GET["num_orc"] != "") {
		//$strpesq3 = "`x`.`pedido` LIKE '".$_GET["num_orc"]."' AND ";
		$orcamentos = explode(" ",$_GET["num_orc"]);
		$strpesq3 .= "(";
		foreach($orcamentos AS &$numero_orc) {
			$strpesq3 .= "`x`.`pedido` LIKE '".$numero_orc."' OR ";
		}
		$strpesq3 = substr($strpesq3,0,-3).") AND ";
	}
	if ($_GET["id_vend"] != "") {
		$strpesq4 = "`x`.`id_vend` LIKE '".$_GET["id_vend"]."' AND ";
	}
	if ($_GET["dim"] != "") {
		$strpesq5 = "CONCAT(`base1`, 'x', `base2`, 'x', `altura`) LIKE '".$_GET["dim"]."' AND ";
	}
	if ($_GET["qtde"] != "") {
		$strpesq6 = "`x`.`qtde` LIKE '".$_GET["qtde"]."' AND ";
	}
	if ($_GET["ref"] != "") {
		$strpesq7 = "`x`.`referencia` LIKE '%".$_GET["ref"]."%' AND ";
	}
	if ($_GET["rep"] != "") {
		$strpesq8 = "`x`.`representante` LIKE '%".$_GET["rep"]."%' AND ";
	}

	$strpesq = "WHERE ".$strpesq1.$strpesq2.$strpesq3.$strpesq4.$strpesq5.$strpesq6.$strpesq7.$strpesq8;
} else {
	$strpesq = "WHERE ";
}



if ($_GET["status"] != "") {
	$status_ped = $_GET["status"];

	if ($status_ped == "1") { $semstatus = "AND `x`.`status` NOT LIKE '2' AND `x`.`status` NOT LIKE '3' AND `x`.`status` NOT LIKE '4' AND `x`.`status` NOT LIKE '5' AND `x`.`status` NOT LIKE '6' AND `x`.`status` NOT LIKE '7'"; }
	if ($status_ped == "2") { $semstatus = "AND `x`.`status` NOT LIKE '1' AND `x`.`status` NOT LIKE '3' AND `x`.`status` NOT LIKE '4' AND `x`.`status` NOT LIKE '5' AND `x`.`status` NOT LIKE '6' AND `x`.`status` NOT LIKE '7'"; }
	if ($status_ped == "3") { $semstatus = "AND `x`.`status` NOT LIKE '1' AND `x`.`status` NOT LIKE '2' AND `x`.`status` NOT LIKE '4' AND `x`.`status` NOT LIKE '5' AND `x`.`status` NOT LIKE '6' AND `x`.`status` NOT LIKE '7'"; }
} else {
	$status_ped = "1";
}

//`x`.`status` LIKE '".$status_ped."' 

//echo "SELECT * FROM `pedidos` ORDER BY `id` DESC) x {$strpesq} `x`.`status` NOT LIKE '0' {$semstatus} GROUP BY `pedido` ORDER BY `x`.`pedido` DESC";

//$statement = "(SELECT * FROM `pedidos` ORDER BY `id` DESC) x {$strpesq} `x`.`status` NOT LIKE '0' {$semstatus} GROUP BY `pedido` ORDER BY `x`.`pedido` DESC ";//ORDER BY `x`.`pedido` ASC LIMIT 0 , 50
//$statement = "`pedidos` x {$strpesq} `x`.`status` NOT LIKE '0' AND `x`.`id` IN (SELECT MAX(id) FROM `pedidos` GROUP BY `pedido`) {$semstatus} ORDER BY `x`.`pedido` DESC ";//ORDER BY `x`.`pedido` ASC LIMIT 0 , 50
$statement = "`pedidos` x LEFT JOIN `pedidos_followup` F ON (`F`.`pedido` = `x`.`pedido`) {$strpesq} `x`.`status` NOT LIKE '0' AND `x`.`id` IN (SELECT MAX(id) FROM `pedidos` GROUP BY `pedido`) {$semstatus} ORDER BY `F`.`data` DESC, `x`.`pedido` DESC ";//ORDER BY `x`.`pedido` ASC LIMIT 0 , 50


$results = mysqli_query($conn,"SELECT *, `x`.`pedido` FROM {$statement}");

//echo "SELECT * FROM {$statement}";

$query_contagem = "SELECT * FROM {$statement}";
$total_encontrado = mysqli_num_rows(mysqli_query($conn,$query_contagem));;


?>

<span style="float:right;"><small>Total: <?php echo $total_encontrado; ?> - <?php echo date("d/m/Y - H:i:s");?></small></span>
<h2>Relatório de follow up<?php if($_GET["data_inicio"]!="" && $_GET["data_final"]!="") { echo " - ".date("d/m/Y",strtotime($_GET["data_inicio"]))." a ".date("d/m/Y",strtotime($_GET["data_final"])); } ?></h2>

<br>


<table border="0" width="100%" align="center" class="table table-striped">
<thead>
<tr>
<th align="center"><b>N°</b></td>
<th><b>Cliente</b></td>
<?php /* <th><b>Ref.</b></td> */ ?>
<th><b>Dimensões</b></td>
<th><b>Qtde.</b></td>
<th><b>Preço</b></td>
<?php /* <th><b>Telefone</b></td> */ ?>
<th><b>Contato</b></td>
<?php /* <th><b>E-mail</b></td> */ ?>
<th><b>Representante</b></td>
<?php /* <th><b>Vendedor</b></td> */ ?>
<th><b>Data</b></td>
<th><b>Follow Up</b></td>
<?php /* <th><b>Status</b></td> */ ?>
</tr>
</thead>
<?php

if (mysqli_num_rows($results) != 0) {

	while ($row_pedidos = mysqli_fetch_array($results)) {

		$no_pedido = $row_pedidos['pedido'];

		if(isset($row_pedidos['data'])) {
			$data_ped0 = $row_pedidos['data'];
			$data_ped0 = strtotime($data_ped0);
			$hora_ped = date("H:i:s", $data_ped0);
			$data_ped = date("d/m/Y", $data_ped0);
		} else {
			$data_ped = "-";
		}

		$status_txt = $status[$row_pedidos["status"]];


		$query_cliente_ped = mysqli_query($conn,"SELECT * FROM `cad_clientes` WHERE `id` LIKE (SELECT `id_cliente` FROM `pedidos_cliente` WHERE `pedido` LIKE '".(float)$row_pedidos['pedido']."' LIMIT 1)");
		$row_cliente_ped = mysqli_fetch_array($query_cliente_ped);

		$query_followup = mysqli_query($conn,"SELECT * FROM `pedidos_followup` WHERE `pedido` LIKE '".(float)$row_pedidos['pedido']."' LIMIT 1;");
		$row_followup = mysqli_fetch_array($query_followup);
		$followup = $row_followup["followup"];


		$contato_com = $row_cliente_ped['contato_com'];
		$cliente_email = $row_cliente_ped['email_com'];
		if ($row_cliente_ped['telefone'] != "") {
			$cliente_telefone = "(".$row_cliente_ped['ddd'].") ".$row_cliente_ped['telefone'];
		} else {
			$cliente_telefone = "";
		}


		$quantidade = number_format($row_pedidos["qtde"], 0, ',', '.');
		$preco = number_format($row_pedidos["valor_final"], 2, ',', '.');
		$obs_comerciais = $row_pedidos["obs_comerciais"];
		$representante = $row_pedidos["representante"];


		$nome_vend = $vendedor[$row_pedidos['id_vend']]["nome"];
		$vendedor_email = $vendedor[$row_pedidos['id_vend']]["email"];
		$vendedor_telefone = $vendedor[$row_pedidos['id_vend']]["celular"];
		$ultimo_pdf = "ultimo_pdf.pdf";

		?>
			<tr>
				<td align="center" style="padding-left:5px; max-width: 50px;"><?php echo sprintf('%05d', $row_pedidos['pedido']); ?></td>
				<td style="max-width: 200px;"><?php echo $row_pedidos['nome_cliente']; ?></td>
<?php /*				<td><?php echo $row_pedidos['referencia']; ?></td> */ ?>
				<td><?php echo $row_pedidos['base1']."x".$row_pedidos['base2']."x".$row_pedidos['altura']; ?></td>
				<td><?php echo $quantidade; ?></td>
				<td>R$ <?php echo $preco; ?></td>
				<td><?php echo $contato_com; ?><br><?php echo $cliente_telefone; ?><br><a href="mailto:<?php echo strtolower($cliente_email); ?>"><?php echo strtolower($cliente_email); ?></a></td>
				<td><?php echo $representante; ?></td>
<?php /*				<td><?php echo $nome_vend; ?></td> */ ?>
				<td><?php echo $data_ped; ?></td>
<?php /*				<td align="center"><img src="images/icons/status<?php echo $row_pedidos['status']; ?>.png" width="12" alt="<?php echo $status_txt; ?>" title="<?php echo $status_txt; ?>"></td> */ ?>
				<td><?php echo $followup; ?></td>
			</tr>
		<?php
	}
  
} else {
?>
<tr>
	<td colspan="11" align="center"><br>Nenhum registro encontrado.<br><br></td>
</tr>
<?php
}
?>
</table>

<?php
/*
<center>
<br>
<ul class="legenda">
<li><img style="vertical-align: middle;" src="images/icons/status1.png" border="0"> Em análise</li>
<li><img style="vertical-align: middle;" src="images/icons/status2.png" border="0"> Aguardando liberação</li>
<li><img style="vertical-align: middle;" src="images/icons/status3.png" border="0"> Liberado</li>
<li><img style="vertical-align: middle;" src="images/icons/status4.png" border="0"> Aprovado</li>
</ul>
<br><br>
</center>
*/
?>

</body>
</html>