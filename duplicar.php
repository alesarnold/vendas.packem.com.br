<?php 

	require("common.php"); 
//	require("../orcamentos/common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 

    require("cabecalho.php"); 


$id = $_GET["id"];
$query_duplica1 = mysqli_query($conn,"SELECT * FROM `pedidos` where `id` = $id;");

$orcam_dup = mysqli_fetch_array($query_duplica1);

//print_r($orcam_dup);

$id_vend = $orcam_dup["id_vend"];

/*
if ($_SESSION['user']['id'] != $id_vend) {
?>
<div class="clearfix"></div>
<?php
    echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
	require("rodape.php");
    die(); 
}
*/


?>
<div class="page-title">
	<div class="title_left">
		<h1>Duplicar orçamento</h1>
	</div>
</div>
<div class="clearfix"></div>

<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-copy"></i> Cópia de orçamento existente</h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<form name="orcamento" class="form-horizontal form-label-left" action="orcamento.php?acao=gravar" method="post">
<br><br>
<?php

$query_duplica = mysqli_query($conn,"SELECT * FROM `pedidos` where `id` = $id;");

while ($duplica = mysqli_fetch_assoc($query_duplica)) {
?>
<center><h2>Deseja realmente duplicar o orçamento: <b><?php echo sprintf('%05d', $duplica["pedido"])." / ".sprintf('%02d', $duplica["revisao"]); ?></b> ?</h2></center>
<br><br>
<?php

$query_cliente_ped = mysqli_query($conn,"SELECT * FROM `cad_clientes` WHERE `id` LIKE (SELECT `id_cliente` FROM `pedidos_cliente` WHERE `pedido` LIKE '".(float)$duplica["pedido"]."' LIMIT 1)");
$row_cliente_ped = mysqli_fetch_array($query_cliente_ped);

if ($row_cliente_ped != "") {
	echo '<input type="hidden" name="novo_cliente" value="nao">'."\n\n";
	echo '<input type="hidden" name="ddd" value="'.$row_cliente_ped["ddd"].'">'."\n";
	echo '<input type="hidden" name="telefone" value="'.$row_cliente_ped["telefone"].'">'."\n";
	echo '<input type="hidden" name="email_com" value="'.$row_cliente_ped["email_com"].'">'."\n";
	echo '<input type="hidden" name="id_cliente" value="'.$row_cliente_ped["id"].'">'."\n";
	echo '<input type="hidden" name="nome_cliente" value="'.$row_cliente_ped["nome"].'">'."\n";
	echo '<input type="hidden" name="cidade_cliente" value="'.$row_cliente_ped["cidade"].'">'."\n";
	echo '<input type="hidden" name="uf_cliente" value="'.$row_cliente_ped["uf"].'">'."\n";
	echo '<input type="hidden" name="selecao" value="'.$row_cliente_ped["selecao"].'">'."\n";
	echo '<input type="hidden" name="cnpj_cpf" value="'.$row_cliente_ped["cnpj_cpf"].'">'."\n";
	echo '<input type="hidden" name="segmento_cliente" value="'.$row_cliente_ped["segmento"].'">'."\n\n";
} else {
	echo '<input type="hidden" name="novo_cliente" value="sim">'."\n";
}






    foreach($duplica as $key => $value) {
        if ($key != "id" && $key != "pedido" && $key != "log" && $key != "revisao" && $key != "vendedor" && $key != "mercado" && $key != "id_vend" && $key != "data" && $key != "status" && $key != "valor_custo" && $key != "valor_final" && $key != "correcoes" && $key != "nome_cliente" && $key != "cnpj_cpf" && $key != "cidade_cliente" && $key != "uf_cliente" && $key != "fornecedora" && $key != "qtde" && $key != "frete") {
        	if ($key == "valvula") { echo "<input type=\"hidden\" name=\"gramat_valvula\" value=\"".utf8_encode($value)."\">\n"; }
        	elseif ($key == "saia") { echo "<input type=\"hidden\" name=\"gramat_saia\" value=\"".utf8_encode($value)."\">\n"; }
        	elseif ($key == "tampa") { echo "<input type=\"hidden\" name=\"gramat_tampa\" value=\"".utf8_encode($value)."\">\n"; }
        	elseif ($key == "fundo") { echo "<input type=\"hidden\" name=\"gramat_fundo_d\" value=\"".utf8_encode($value)."\">\n"; }
        	elseif ($key == "valvula_d") { echo "<input type=\"hidden\" name=\"gramat_valvula_d\" value=\"".utf8_encode($value)."\">\n"; }
        	elseif ($key == "obs_cliente") { echo "<input type=\"hidden\" name=\"obs_cliente\" value=\"".utf8_encode(mysqli_real_escape_string($conn,$value))."\">\n"; }
			else { echo "<input type=\"hidden\" name=\"".$key."\" value=\"".utf8_encode($value)."\">\n"; }
        }
    }


$query_duplica_costura = mysqli_query($conn,"SELECT * FROM `pedidos_costura` WHERE `no_ped` = ".$duplica["pedido"]." ORDER BY `id` DESC;");
$duplica_costura = mysqli_fetch_array($query_duplica_costura);

if ($duplica_costura["corpo"] == "") { $duplica_costura["corpo"] = "simples"; }
if ($duplica_costura["enchim"] == "") { $duplica_costura["enchim"] = "simples"; }
if ($duplica_costura["esvaz"] == "") { $duplica_costura["esvaz"] = "simples"; }
if ($duplica_costura["alca"] == "") { $duplica_costura["alca"] = "ponto_fixo"; }

echo "<input type=\"hidden\" name=\"tipo_cost_corpo\" value=\"".utf8_encode($duplica_costura["corpo"])."\">\n";
echo "<input type=\"hidden\" name=\"tipo_cost_enchim\" value=\"".utf8_encode($duplica_costura["enchim"])."\">\n";
echo "<input type=\"hidden\" name=\"tipo_cost_esvaz\" value=\"".utf8_encode($duplica_costura["esvaz"])."\">\n";
echo "<input type=\"hidden\" name=\"tipo_cost_alca\" value=\"".utf8_encode($duplica_costura["alca"])."\">\n";


/*
	unset($key);
	$query_duplica2 = mysqli_query($conn,"SELECT *  FROM `pedidos_extra` WHERE `pedido` = '".$duplica['pedido']."'  ORDER BY `pedidos_extra`.`id`  DESC LIMIT 0,1");
	while ($duplica2 = mysqli_fetch_assoc($query_duplica2)) {
		foreach($duplica2 as $key => $value) {
			if ($key != "id" && $key != "pedido" && $key != "log" && $key != "revisao" && $key != "vendedor" && $key != "id_vend" && $key != "data" && $key != "status" && $key != "valor_custo" && $key != "valor_final" && $key != "correcoes" && $key != "nome_cliente" && $key != "cnpj_cpf" && $key != "cidade_cliente" && $key != "uf_cliente" && $key != "fornecedora" && $key != "qtde" && $key != "frete") {
				echo "<input type=\"hidden\" name=\"".$key."\" value=\"".utf8_encode($value)."\">\n";
			}
		}
		unset($key);
	}
*/
?>

<input type="hidden" name="pedido_duplicado" value="<?php echo $duplica["pedido"]; ?>">

<?php
$query_extra = mysqli_query($conn,"SELECT * FROM `pedidos_extra` WHERE `pedido` = ".$duplica["pedido"]." ORDER BY `id` DESC;");
$duplica_extra = mysqli_fetch_array($query_extra);
?>

<input type="hidden" name="class_prod" value="<?php echo $duplica_extra["class_prod"]; ?>">

<?php

?>

	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="nome_cliente">Cliente</label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="text" name="nome_cliente" value="<?php echo utf8_encode($duplica["nome_cliente"]); ?>" required="required" class="form-control col-md-7 col-xs-12" readonly></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" style="padding-top: 2px;" for="cnpj_cpf"><select name="selecao" style="height: 29px; width: 60px; border:0;">
    <option value="cnpj"<?php if ($duplica["selecao"] == "cnpj") { echo " selected"; } ?>>CNPJ</option>
    <option value="cpf"<?php if ($duplica["selecao"] == "cpf") { echo " selected"; } ?>>CPF</option>
    <option value="rut"<?php if ($duplica["selecao"] == "rut") { echo " selected"; } ?>>RUT</option>
    <option value="cuit"<?php if ($duplica["selecao"] == "cuit") { echo " selected"; } ?>>CUIT</option>
    <option value="ruc"<?php if ($duplica["selecao"] == "ruc") { echo " selected"; } ?>>RUC</option>
    <option value="rif"<?php if ($duplica["selecao"] == "rif") { echo " selected"; } ?>>RIF</option>
    <option value="outro"<?php if ($duplica["selecao"] == "outro") { echo " selected"; } ?>>Outro</option>
	</select></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="text" name="cnpj_cpf" value="<?php echo $duplica["cnpj_cpf"]; ?>" required="required" class="form-control col-md-7 col-xs-12" readonly></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="telefone">Telefone <span class="required">*</span></label>
		<div class="col-md-1 col-sm-1 col-xs-12"><input type="number" name="ddd" required="required" placeholder="DDD" class="form-control col-md-7 col-xs-12" value="<?php echo $row_cliente_ped["ddd"]; ?>" readonly></div>
		<div class="col-md-5 col-sm-5 col-xs-12"><input type="number" name="telefone" required="required" placeholder="Número" class="form-control col-md-7 col-xs-12" value="<?php echo $row_cliente_ped["telefone"]; ?>" readonly></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="email_com">E-mail comercial <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12"><input type="email" name="email_com" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $row_cliente_ped["email_com"]; ?>" readonly></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="cidade_cliente">Cidade</label>
		<div class="col-md-3 col-sm-3 col-xs-12 has-feedback"><input type="text" name="cidade_cliente" value="<?php echo utf8_encode($duplica["cidade_cliente"]); ?>" required="required" class="form-control col-md-4 col-xs-12" readonly></div>
		<label class="control-label col-md-1 col-sm-1 col-xs-12" for="uf_cliente">UF</label>
		<div class="col-md-2 col-sm-2 col-xs-12 has-feedback"><input type="text" name="uf_cliente" value="<?php echo $duplica["uf_cliente"]; ?>" required="required" class="form-control col-md-7 col-xs-12" readonly></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="fornecedora">Fornecedora</label>
		<div class="col-md-3 col-sm-3 col-xs-12 has-feedback">
			<select name="fornecedora" class="form-control col-md-12 col-xs-12">
<?
$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora`");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
?>
				<option value="valor_<? echo strtolower($row_fornec['sigla']); ?>"<? if($duplica["fornecedora"] == "valor_".strtolower($row_fornec['sigla'])){ echo " selected"; } ?>><? echo $row_fornec['apelido']; ?></option>
<?
}
?>
			</select>
		</div>
		<label class="control-label col-md-1 col-sm-1 col-xs-12" for="mercado">Mercado</label>
		<div class="col-md-2 col-sm-2 col-xs-12 has-feedback"><select name="mercado" class="form-control col-md-7 col-xs-12">
    <option value="int"<?php if ($duplica["mercado"] == "int") { echo " selected"; } ?>>Mercado interno</option>
    <option value="ext"<?php if ($duplica["mercado"] == "ext") { echo " selected"; } ?>>Exportação</option>
	</select>
	</div>

	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="qtde">Quantidade</label>
		<div class="col-md-3 col-sm-3 col-xs-12 has-feedback"><input type="text" name="qtde" value="<?php echo $duplica["qtde"]; ?>" required="required" class="form-control col-md-4 col-xs-12"></div>
		<label class="control-label col-md-1 col-sm-1 col-xs-12" for="uf_cliente">Frete tipo</label>
		<div class="col-md-2 col-sm-2 col-xs-12 has-feedback"><select name="frete" class="form-control col-md-7 col-xs-12">
    <option value="cif"<?php if ($duplica["frete"] == "cif") { echo " selected"; } ?>>CIF</option>
    <option value="fob"<?php if ($duplica["frete"] == "fob") { echo " selected"; } ?>>FOB</option>
	</select>
	</div>
	</div>

<?php
}
?>
	<div class="ln_solid"></div>
		<div class="form-group">
		<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 col-sm-offset-3">
			<button type="submit" class="btn btn-success"><i class="fa fa-copy"></i> Duplicar</button>
			<a href="pedidos.php" class="btn btn-primary"><i class="fa fa-ban"></i> Cancelar</a>
		</div>
	</div>

</form>

</div>

<?php
require("rodape.php");
?>