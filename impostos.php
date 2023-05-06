<?php
    require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 

    require("cabecalho.php"); 

if ($_SESSION['user']['nivel'] != '1') {
    echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="pedidos.php">Voltar</a><br><br><br><br><br></center>';
    require("rodape.php"); 
    die();
}

if ($_GET["imposto_tipo"] != "") {
	$imposto_tipo = $_GET["imposto_tipo"];
} else {
	$imposto_tipo = "imposto_icms";
}


?>


<div class="page-title">
	<div class="title_left">
		<h1>Impostos</h1>
	</div>
</div>
<div class="clearfix"></div>

<div class="x_panel">
	<div class="x_title">
		<small>* Sempre utilize VÍRGULA para separar casas decimais.</small>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<br>
<?php
if(!empty($_POST)) {

$tabela_imp = $_POST["tabela_imp"];

foreach($_POST["imposto"] as $key => $value) {
	$sql_atualiza_impostos = "UPDATE `$tabela_imp` SET";
	foreach($value as $key2 => $value2) {
		$sql_atualiza_impostos .= " `$key2` = '".(float)str_replace(",",".",$value2)."',";
	}
	$sql_atualiza_impostos = substr($sql_atualiza_impostos, 0, -1);
	$sql_atualiza_impostos .= " WHERE `$tabela_imp`.`unidade` = '$key' LIMIT 1;";

//echo $sql_atualiza_impostos."<br><br>";

	$atualiza_impostos = mysqli_query( $conn, $sql_atualiza_impostos );
	if(! $atualiza_impostos ) { die('Não foi possível atualizar os valores, tente novamente mais tarde.' . mysql_error()); }
}

Log_Sis("",$_SESSION['user']['id'],$_SESSION['user']['nome'],"Modificou a tabela de imposto: ".strtoupper($tabela_imp).".");

redirect("impostos.php?imposto_tipo=$tabela_imp"); 
die(); 
}
?>
<form action="impostos.php" name="filtro" method="GET" class="form-inline">
<div class="form-group">
<label class="control-label" for="imposto_tipo">Tipo de imposto: </label>
<select style="width: 180px;" class="form-control" name="imposto_tipo" id="imposto_tipo" onchange="form.submit();">
<option value="imposto_icms"<?php if ($imposto_tipo == imposto_icms) { echo " selected"; } ?>>ICMS</option>
<option value="imposto_pis"<?php if ($imposto_tipo == imposto_pis) { echo " selected"; } ?>>PIS</option>
<option value="imposto_cofins"<?php if ($imposto_tipo == imposto_cofins) { echo " selected"; } ?>>COFINS</option>
<option value="imposto_ir"<?php if ($imposto_tipo == imposto_ir) { echo " selected"; } ?>>IR</option>
<option value="imposto_csll"<?php if ($imposto_tipo == imposto_csll) { echo " selected"; } ?>>CSLL</option>
<option value="imposto_inss"<?php if ($imposto_tipo == imposto_inss) { echo " selected"; } ?>>INSS</option>
<option value="imposto_perda"<?php if ($imposto_tipo == imposto_perda) { echo " selected"; } ?>>PERDA</option>
</select>
</div>
</form>
<br>
<form action="impostos.php" name="contabilidade" method="POST">
<table border="0" class="table" width="100%" style="text-align:center;">
<thead>
<tr>
	<th></td>
	<th colspan="4" style="text-align:center;">Unidade fornecedora:</td>
</tr>
<tr>
	<th style="text-align:center;"><b>Estado</b></th>
	<?
$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora`");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
?>
	<th style="text-align: center !important;"><b><? echo $row_fornec['apelido']; ?></b></th>
<?
}
?>
</tr>
</thead>
<?php

$imp = array();

$query_imposto = mysqli_query($conn,"SELECT * FROM `$imposto_tipo` ORDER BY `unidade` DESC");
while ($imposto = mysqli_fetch_assoc($query_imposto)){
	$imp[$imposto['unidade']] = $imposto;
}

$query_estados = mysqli_query($conn,"SELECT * FROM `estados` WHERE `uf` NOT LIKE 'XX' ORDER BY `uf` ASC");
while ($estados = mysqli_fetch_array($query_estados)){

$uf = $estados["uf"];
?>
<tr>
	<td style="vertical-align:middle;"><?php echo $uf; ?></td>
	<td><input type="text" name="imposto[f1][<?php echo $uf; ?>]" value="<?php echo number_format($imp['F1'][$uf], 2, ',', '.'); ?>" class="form-control" style="height:18px; line-height:15px; border:0; box-shadow:none; text-align:center;"></td>
	<td><input type="text" name="imposto[f2][<?php echo $uf; ?>]" value="<?php echo number_format($imp['F2'][$uf], 2, ',', '.'); ?>" class="form-control" style="height:18px; line-height:15px; border:0; box-shadow:none; text-align:center;"></td>
	<td><input type="text" name="imposto[f3][<?php echo $uf; ?>]" value="<?php echo number_format($imp['F3'][$uf], 2, ',', '.'); ?>" class="form-control" style="height:18px; line-height:15px; border:0; box-shadow:none; text-align:center;"></td>
	<td><input type="text" name="imposto[f4][<?php echo $uf; ?>]" value="<?php echo number_format($imp['F4'][$uf], 2, ',', '.'); ?>" class="form-control" style="height:18px; line-height:15px; border:0; box-shadow:none; text-align:center;"></td>
	<td><input type="text" name="imposto[f5][<?php echo $uf; ?>]" value="<?php echo number_format($imp['F5'][$uf], 2, ',', '.'); ?>" class="form-control" style="height:18px; line-height:15px; border:0; box-shadow:none; text-align:center;"></td>
	<td><input type="text" name="imposto[f6][<?php echo $uf; ?>]" value="<?php echo number_format($imp['F6'][$uf], 2, ',', '.'); ?>" class="form-control" style="height:18px; line-height:15px; border:0; box-shadow:none; text-align:center;"></td>
</tr>
<?php
}
?>
</table>
<br>
<center>
<input type="hidden" name="tabela_imp" value="<?php echo $imposto_tipo; ?>">
<button type="submit" class="btn btn-success"><i class="fa fa-refresh"></i> Atualizar </button>
&nbsp;&nbsp;
<button type="reset" class="btn btn-primary"><i class="fa fa-ban"></i> Redefinir</button>
</center>
</form>
<br>
<?php
/*
echo "<pre>";
print_r($imp);
echo "</pre>";
*/
?>
<?php
require("rodape.php");
?>