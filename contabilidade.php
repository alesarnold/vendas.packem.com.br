<?php 
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
    require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 

    require("cabecalho.php"); 

	$comeco_gramaturas = 26; // EM QUAL REGISTRO COMECAM AS GRAMATURAS.
	
/*
if ($_SESSION['user']['nivel'] != '1' && $_SESSION['user']['nivel'] != '2') {
	echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
	require("rodape.php"); 
	die();
}
*/
if ($_SESSION['user']['id'] != '1' && $_SESSION['user']['id'] != '2' && $_SESSION['user']['id'] != '4' && $_SESSION['user']['id'] != '20' && $_SESSION['user']['id'] != '59') {
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
<div class="page-title">
	<div class="title_left">
		<h1>Formatação de preços</h1>
	</div>
	<span style="margin-top:20px;float:right;"><a href="contabilidade_print.php" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-print"></i> IMPRIMIR</a></span>
</div>
<div class="clearfix"></div>

<div class="x_panel">
	<div class="x_title">
		<h3>Preços e gramaturas</h3><small>* Sempre utilize VÍRGULA para separar casas decimais.</small>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<?php

if($_GET["acao"] == "update") {

if (!empty($_POST)) {
/*
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
*/
	$contabil = mysqli_query($conn,"SELECT * FROM preco_kilo");
	while($row = mysqli_fetch_array($contabil)) {
		$id = $row["id"];


		if(!isset($_POST[$id."_f1"])) { $_POST[$id."_f1"] = 0; }
		if(!isset($_POST[$id."_f2"])) { $_POST[$id."_f2"] = 0; }
		if(!isset($_POST[$id."_f3"])) { $_POST[$id."_f3"] = 0; }
		if(!isset($_POST[$id."_f4"])) { $_POST[$id."_f4"] = 0; }
		if(!isset($_POST[$id."_f5"])) { $_POST[$id."_f5"] = 0; }
		if(!isset($_POST[$id."_f6"])) { $_POST[$id."_f6"] = 0; }

		
		$f1 = str_replace(".","",$_POST[$id.'_f1']);
		$f2 = str_replace(".","",$_POST[$id.'_f2']);
		$f3 = str_replace(".","",$_POST[$id.'_f3']);
		$f4 = str_replace(".","",$_POST[$id.'_f4']);
		$f5 = str_replace(".","",$_POST[$id.'_f5']);
		$f6 = str_replace(".","",$_POST[$id.'_f6']);

		$query_atualizakg = "UPDATE `preco_kilo` SET 
			`valor_f1` = '".str_replace(",",".",$f1)."', 
			`valor_f2` = '".str_replace(",",".",$f2)."', 
			`valor_f3` = '".str_replace(",",".",$f3)."', 
			`valor_f4` = '".str_replace(",",".",$f4)."', 
			`valor_f5` = '".str_replace(",",".",$f5)."', 
			`valor_f6` = '".str_replace(",",".",$f6)."'
		WHERE `preco_kilo`.`id` = '".$id."';";
		//echo "<br>";
/*
		echo "<pre>";
		echo $query_atualizakg;
		echo "</pre>";
		die();
*/
		mysqli_query($conn,$query_atualizakg);
	}

//	die();
	Log_Sis("",$_SESSION['user']['id'],$_SESSION['user']['nome'],"Fez alteracoes na FORMATACAO DE PRECOS.");
}

?>
<center>
	<br>
	<br>
	<br>
	<br>
	<h1><i class="fa fa-refresh"></i></h1>
	<br>
	<h1>Atualizado!</h1>
	<h2 style="line-height:30px;">Valores atualizados com sucesso.</h2>
	<br>
	<br>
	<a href="contabilidade.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> &nbsp;Voltar</a>
	<br>
	<br>
	<br>
	<br>
</center>
<?php
} else {
?>
<br>
<form name="contabilidade" class="form-inline" action="contabilidade.php?acao=update" method="post">
<table border="0" width="100%" align="center" class="table table-striped">
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

	if ($row["id"] == $comeco_gramaturas+1) {
?>
		<tr style="height:51px;">
			<td style="vertical-align: middle;"><?php echo $row['desc']; ?></td>
			<td style="vertical-align: middle;" colspan="6" align="center"><small><i>Será utilizada a mesma gramatura do corpo do bag.</i></small></td>
		</tr>
<?php
	} elseif ($row["id"] == $comeco_gramaturas) {
		if ($row["id"] >= $comeco_gramaturas && $row["id"] <= $comeco_gramaturas+5) { $unidade_med1 = ""; $unidade_med2 = " kg/m² "; }
		else { $unidade_med1 = "R$ "; $unidade_med2 = ""; }
?>
</table>
<b>TIPO A:</b> Alta produtividade<br>
<b>TIPO B:</b> Média produtividade<br> 
<b>TIPO C:</b> Baixa produtividade<br>

<br><br>
<table border="0" width="100%" align="center" class="table table-striped">
<thead>
<tr>
	<th>GRAMATURAS</th>
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
		<tr>
			<td style="vertical-align: middle;"><?php echo $row['desc']; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f1" value="<?php echo number_format((float)$row['valor_f1'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f2" value="<?php echo number_format((float)$row['valor_f2'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f3" value="<?php echo number_format((float)$row['valor_f3'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f4" value="<?php echo number_format((float)$row['valor_f4'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f5" value="<?php echo number_format((float)$row['valor_f5'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f6" value="<?php echo number_format((float)$row['valor_f6'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
		</tr>
<?php
	} elseif ($row["id"] <= $comeco_gramaturas) {
		if ($row["id"] >= $comeco_gramaturas && $row["id"] <= $comeco_gramaturas+5) { $unidade_med1 = ""; $unidade_med2 = " kg/m² "; }
		else { $unidade_med1 = "R$ "; $unidade_med2 = ""; }
?>
		<tr>
			<td style="vertical-align: middle;"><?php echo $row['desc']; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f1" value="<?php echo number_format((float)$row['valor_f1'], 2, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f2" value="<?php echo number_format((float)$row['valor_f2'], 2, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f3" value="<?php echo number_format((float)$row['valor_f3'], 2, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f4" value="<?php echo number_format((float)$row['valor_f4'], 2, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f5" value="<?php echo number_format((float)$row['valor_f5'], 2, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f6" value="<?php echo number_format((float)$row['valor_f6'], 2, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>

		</tr>
<?php
	} elseif ($row["id"] >= $comeco_gramaturas) {

		if ($row["id"] >= $comeco_gramaturas && $row["id"] <= $comeco_gramaturas+5) { $unidade_med1 = ""; $unidade_med2 = " kg/m² "; }
		else { $unidade_med1 = "R$ "; $unidade_med2 = ""; }
?>
		<tr>
			<td style="vertical-align: middle;"><?php echo $row['desc']; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f1" value="<?php echo number_format((float)$row['valor_f1'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f2" value="<?php echo number_format((float)$row['valor_f2'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f3" value="<?php echo number_format((float)$row['valor_f3'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f4" value="<?php echo number_format((float)$row['valor_f4'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f5" value="<?php echo number_format((float)$row['valor_f5'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
			<td style="max-width: 120px; text-align: center;"><?php echo $unidade_med1; ?><input type="text" class="form-control" style="width: 65px;" name="<?php echo $row['id']; ?>_f6" value="<?php echo number_format((float)$row['valor_f6'], 3, ',', '.'); ?>"><?php echo $unidade_med2; ?></td>
		</tr>
<?php
	}
}
?>
</table>

<center>
<br>
<button type="submit" class="btn btn-success"><i class="fa fa-refresh"></i> Atualizar </button>
&nbsp;&nbsp;
<button type="reset" class="btn btn-primary"><i class="fa fa-ban"></i> Redefinir</button>
<?php } ?>
<br><br>
</center>
</form>
</div>
</div>

<?php
//////////////////////////////////////////////// AQUI MESMO ////////////////////////////////
?>

<div class="x_panel">
	<div class="x_title">
		<h3>Mão de obra e Custos Indiretos de Fabricação</h3><small>* Sempre utilize VÍRGULA para separar casas decimais.</small>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<?php

if($_GET["acao"] == "update_cif_mo") {

if (!empty($_POST)) {

	$contabil = mysqli_query($conn,"SELECT * FROM cif_mo");
	while($row = mysqli_fetch_array($contabil)) {
		$id = $row["id"];

		if(!isset($_POST[$id."_mo_f1"])) { $_POST[$id."_mo_f1"] = 0; }
		if(!isset($_POST[$id."_mo_f2"])) { $_POST[$id."_mo_f2"] = 0; }
		if(!isset($_POST[$id."_mo_f3"])) { $_POST[$id."_mo_f3"] = 0; }
		if(!isset($_POST[$id."_mo_f4"])) { $_POST[$id."_mo_f4"] = 0; }
		if(!isset($_POST[$id."_mo_f5"])) { $_POST[$id."_mo_f5"] = 0; }
		if(!isset($_POST[$id."_mo_f6"])) { $_POST[$id."_mo_f6"] = 0; }

		if(!isset($_POST[$id."_cif_f1"])) { $_POST[$id."_cif_f1"] = 0; }
		if(!isset($_POST[$id."_cif_f2"])) { $_POST[$id."_cif_f2"] = 0; }
		if(!isset($_POST[$id."_cif_f3"])) { $_POST[$id."_cif_f3"] = 0; }
		if(!isset($_POST[$id."_cif_f4"])) { $_POST[$id."_cif_f4"] = 0; }
		if(!isset($_POST[$id."_cif_f5"])) { $_POST[$id."_cif_f5"] = 0; }
		if(!isset($_POST[$id."_cif_f6"])) { $_POST[$id."_cif_f6"] = 0; }
		
		$query_atualiza = "UPDATE `cif_mo` SET 
		`mo_f1` = '".number_format(str_replace(",",".",$_POST[$id.'_mo_f1']),2,".","")."', 
		`mo_f2` = '".number_format(str_replace(",",".",$_POST[$id.'_mo_f2']),2,".","")."', 
		`mo_f3` = '".number_format(str_replace(",",".",$_POST[$id.'_mo_f3']),2,".","")."', 
		`mo_f4` = '".number_format(str_replace(",",".",$_POST[$id.'_mo_f4']),2,".","")."', 
		`mo_f5` = '".number_format(str_replace(",",".",$_POST[$id.'_mo_f5']),2,".","")."', 
		`mo_f6` = '".number_format(str_replace(",",".",$_POST[$id.'_mo_f6']),2,".","")."', 

		`cif_f1` = '".number_format(str_replace(",",".",$_POST[$id.'_cif_f1']),2,".","")."', 
		`cif_f2` = '".number_format(str_replace(",",".",$_POST[$id.'_cif_f2']),2,".","")."', 
		`cif_f3` = '".number_format(str_replace(",",".",$_POST[$id.'_cif_f3']),2,".","")."', 
		`cif_f4` = '".number_format(str_replace(",",".",$_POST[$id.'_cif_f4']),2,".","")."',
		`cif_f5` = '".number_format(str_replace(",",".",$_POST[$id.'_cif_f5']),2,".","")."',
		`cif_f6` = '".number_format(str_replace(",",".",$_POST[$id.'_cif_f6']),2,".","")."' 

	WHERE `cif_mo`.`id` = '".$id."';";
/*
		echo "<pre>";
		echo $query_atualiza;
		echo "</pre>";
*/
		mysqli_query($conn,$query_atualiza);

	}

	Log_Sis("",$_SESSION['user']['id'],$_SESSION['user']['nome'],"Fez alteracoes na FORMATACAO DE PRECOS.");

}

?>
<center>
	<br>
	<br>
	<br>
	<br>
	<h1><i class="fa fa-refresh"></i></h1>
	<br>
	<h1>Atualizado!</h1>
	<h2 style="line-height:30px;">Valores atualizados com sucesso.</h2>
	<br>
	<br>
	<a href="contabilidade.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> &nbsp;Voltar</a>
	<br>
	<br>
	<br>
	<br>
</center>
<?php
} else {
?>
<br>

<form name="contabilidade" class="form-inline" action="contabilidade.php?acao=update_cif_mo" method="post">
<table border="0" width="100%" align="center" class="table table-striped">
<thead>
<tr>
	<th>DESCRIÇÃO</th>
<?
$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora`");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
?>
	<th style="text-align: center !important;" colspan="2"><b><? echo $row_fornec['apelido']; ?></b></th>
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
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_mo_f1" value="<?php echo number_format((float)$row_cif_mo['mo_f1'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_cif_f1" value="<?php echo number_format((float)$row_cif_mo['cif_f1'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_mo_f2" value="<?php echo number_format((float)$row_cif_mo['mo_f2'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_cif_f2" value="<?php echo number_format((float)$row_cif_mo['cif_f2'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_mo_f3" value="<?php echo number_format((float)$row_cif_mo['mo_f3'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_cif_f3" value="<?php echo number_format((float)$row_cif_mo['cif_f3'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_mo_f4" value="<?php echo number_format((float)$row_cif_mo['mo_f4'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_cif_f4" value="<?php echo number_format((float)$row_cif_mo['cif_f4'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_mo_f5" value="<?php echo number_format((float)$row_cif_mo['mo_f5'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_cif_f5" value="<?php echo number_format((float)$row_cif_mo['cif_f5'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_mo_f6" value="<?php echo number_format((float)$row_cif_mo['mo_f6'], 2, ',', '.'); ?>"></td>
	<td align="center">R$ <input type="text" class="form-control" style="width: 50px; padding: 6px;" name="<?php echo $row_cif_mo['id']; ?>_cif_f6" value="<?php echo number_format((float)$row_cif_mo['cif_f6'], 2, ',', '.'); ?>"></td>
</tr>
<?php
}
?>
</table>

<center>
<br>
<button type="submit" class="btn btn-success"><i class="fa fa-refresh"></i> Atualizar </button>
&nbsp;&nbsp;
<button type="reset" class="btn btn-primary"><i class="fa fa-ban"></i> Redefinir</button>
<?php } ?>
<br><br>
</center>
</form>
</div>
</div>

<?php
require("rodape.php");
?>