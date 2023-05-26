<?php 

    require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 


	$comeco_gramaturas = 26; // EM QUAL REGISTRO COMECAM AS GRAMATURAS.
	
/*
if ($_SESSION['user']['nivel'] != '1' && $_SESSION['user']['nivel'] != '2') {
	echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
	require("rodape.php"); 
	die();
}
*/
if ($_SESSION['user']['id'] != '1' && $_SESSION['user']['id'] != '2' && $_SESSION['user']['id'] != '4' && $_SESSION['user']['id'] != '20') {
	echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
	require("rodape.php"); 
	die();
}


$query = "SELECT id, username, email, nome, nivel FROM users ORDER BY nome ASC"; 

try { 
	$stmt = $db->prepare($query); 
	$stmt->execute(); 
}
catch(PDOException $ex) { 
	die("Failed to run query: " . $ex->getMessage()); 
} 

$rows = $stmt->fetchAll(); 

?>
<!DOCTYPE html>
<html>
<head>
<style>
html {
	margin:0;
}

body {
	font-family:Arial,sans-serif;
	font-size: 8pt;
}
table {
	width: 100%;
    border-collapse: collapse;
}
table, td, th {
    /* border-bottom: 1px solid #888; */
}
th {
	background: #D5D5D5;
}
td {
    padding: 2px;
}

hr {
	border-top: 2px solid #888;
	border-bottom: 0;
	border-left: 0;
	border-right: 0;
}

.tab_orcamento {
	margin-top: 5px;
	margin-bottom: 5px;
	width: 100%;
	font-size: 10px;
	border-collapse: collapse;
	line-height:8.3pt;
}
.tab_orcamento td {
	border: 1px solid #888;
	padding: 2px 5px;
}
.tab_orcamento th {
	border: 1px solid #888;
	padding: 2px 5px;
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

.footer {
    width: 100%;
    text-align: center;
    position: fixed;
}


</style>
</head>

<body onload="window.print()">
<div class="page-title">
	<div class="title_left">
		<h1>Formatação de preços</h1>
	</div>
</div>
<div class="clearfix"></div>

<div class="x_panel">
	<div class="x_title">
		<h3>Preços e gramaturas</h3>
		<div class="clearfix"></div>
	</div>
<div class="x_content">
<table border="1" style="border-collapse:collapse;" cellpadding="3" width="100%" align="center" class="table table-striped">
<thead>
	<tr>
		<th>VALORES</th>
<?
$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora`");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
?>
		<th style="text-align: center !important;" width="12%"><b><? echo $row_fornec['apelido']; ?></b></th>
<?
}
?>
	</tr>
</thead>
<?php
$contabil = mysqli_query($conn,"SELECT * FROM preco_kilo");
while($row = mysqli_fetch_array($contabil)) {

	if ($row["id"] < $comeco_gramaturas) {
		$unidade_med1 = "R$ ";
		$unidade_med2 = "";
?>
		<tr>
			<td style="vertical-align: middle;"><?php echo $row['desc']; ?></td>
			<td style="max-width: 120px; text-align:center;"><?php echo $unidade_med1; ?><?php echo number_format((float)$row['valor_f1'], 2, ',', '.');  echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align:center;"><?php echo $unidade_med1; ?><?php echo number_format((float)$row['valor_f2'], 2, ',', '.');  echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align:center;"><?php echo $unidade_med1; ?><?php echo number_format((float)$row['valor_f3'], 2, ',', '.');  echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align:center;"><?php echo $unidade_med1; ?><?php echo number_format((float)$row['valor_f4'], 2, ',', '.');  echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align:center;"><?php echo $unidade_med1; ?><?php echo number_format((float)$row['valor_f5'], 2, ',', '.');  echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align:center;"><?php echo $unidade_med1; ?><?php echo number_format((float)$row['valor_f6'], 2, ',', '.');  echo $unidade_med2; ?></td>
		</tr>
<?php
	}
}

?>
</table>
<br>
</div>
</div>
<div class="x_panel">
	<div class="x_title">
		<h3>Mão de obra e Custos Indiretos de Fabricação</h3>
		<div class="clearfix"></div>
	</div>
<div class="x_content">
<table border="1" style="border-collapse:collapse;" cellpadding="3" width="100%" align="center" class="table table-striped">
<thead>
	<tr>
		<th>DESCRIÇÃO</th>
<?
$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora`");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
?>
		<th style="text-align:center;" colspan="2"><b><? echo $row_fornec['apelido']; ?></b></th>
<?
}
?>
	</tr>
	<tr>
		<td></td>
		<td align="center"><b>M.O.</b></td>
		<td align="center"><b>CIF</b></td>
		<td align="center"><b>M.O.</b></td>
		<td align="center"><b>CIF</b></td>
		<td align="center"><b>M.O.</b></td>
		<td align="center"><b>CIF</b></td>
		<td align="center"><b>M.O.</b></td>
		<td align="center"><b>CIF</b></td>
		<td align="center"><b>M.O.</b></td>
		<td align="center"><b>CIF</b></td>
		<td align="center"><b>M.O.</b></td>
		<td align="center"><b>CIF</b></td>
	</tr>
</thead>
<?php
$query_cif_mo = mysqli_query($conn,"SELECT * FROM cif_mo");
while($row_cif_mo = mysqli_fetch_array($query_cif_mo)) {
?>
<tr>
	<td style="vertical-align: middle;"><?php echo $row_cif_mo['descricao']; ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['mo_f1'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['cif_f1'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['mo_f2'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['cif_f2'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['mo_f3'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['cif_f3'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['mo_f4'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['cif_f4'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['mo_f5'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['cif_f5'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['mo_f6'], 2, ',', '.'); ?></td>
	<td style="text-align:center;">R$ <?php echo number_format((float)$row_cif_mo['cif_f6'], 2, ',', '.'); ?></td>
</tr>
<?php
}
?>
</table>
<br><br>
</div>
</div>
</body>
</html>