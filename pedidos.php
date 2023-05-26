<?php 
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
//	require("../orcamentos/common.php"); 
	require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        redirect("login.php"); 
        die("Redirecting to login.php"); 
    } 

	if ($_SESSION['user']['nivel'] == '6') {
        redirect("qualidade.php"); 
        die("Redirecting to qualidade.php"); 
	}


    require("cabecalho.php"); 



if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {

	$cont_venc = 0;
	while ($vencendo = mysqli_fetch_array($query_vencendo)) {
		$orc_venc .= '<a href="resumo.php?pedido='.$vencendo["id_ped"].'">'.sprintf('%05d', $vencendo["id_ped"]).'</a> ';
		$cont_venc = $cont_venc+1;
	}

	if ($cont_venc == 1 && empty($_GET)) {
		?>
		<body onload="new TabbedNotification({ title: 'Atenção!', text: 'Existe <?php echo $cont_venc; ?> orçamento vencendo hoje:<br><?php echo str_replace("\"","\'",$orc_venc); ?> ', type: 'error', sound: false })">
		<?php
	} elseif ($cont_venc > 1 && empty($_GET)) {
		?>
		<body onload="new TabbedNotification({ title: 'Atenção!', text: 'Existem <?php echo $cont_venc; ?> orçamentos vencendo hoje:<br><?php echo str_replace("\"","\'",$orc_venc); ?> ', type: 'error', sound: false })">
		<?php
	}
}


function pagination($query,$per_page=10,$page=1,$pesquisa,$ordem,$ascdesc,$url='?'){   
    global $conn;


if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
	$query = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos ".$pesquisa." ORDER BY ".$ordem." ".$ascdesc;
} elseif ($_SESSION['user']['nivel'] == '4') {
	$pesquisa .= " AND `status` LIKE '4'";
	$query = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos ".$pesquisa." ORDER BY ".$ordem." ".$ascdesc;
} else {
	$pesquisa .= " AND `id_vend` LIKE '".$_SESSION['user']['id']."'";
	$query = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos ".$pesquisa." ORDER BY ".$ordem." ".$ascdesc;
}


    $row = mysqli_fetch_array(mysqli_query($conn,$query));
    $total = $row['num'];
    $adjacents = "2"; 
      
    $prevlabel = "<i class=\"fa fa-angle-left\"></i> Anterior";
    $nextlabel = "Próximo <i class=\"fa fa-angle-right\"></i>";
    $lastlabel = "Último <i class=\"fa fa-angle-double-right\"></i>";
      
    $page = ($page == 0 ? 1 : $page);  
    $start = ($page - 1) * $per_page;                               
      
    $prev = $page - 1;                          
    $next = $page + 1;
      
    $lastpage = ceil($total/$per_page);
      
    $lpm1 = $lastpage - 1; // //last page minus 1
      
    $pagination = "";
    if($lastpage > 1){   


    	$pagination .= "<br>";

    	$pagination .= '<center>';
    	$pagination .= '  <div class="row">';
    	$pagination .= '	<div class="btn-group">';
              
        if ($page > 1) $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$prev}'\" type='button'>{$prevlabel}</button>";
              
        if ($lastpage < 7 + ($adjacents * 2)){   
            for ($counter = 1; $counter <= $lastpage; $counter++){
                if ($counter == $page)
                    $pagination .= "	  <button class='btn btn-primary' type='button'>{$counter}</button>";
                else
                    $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$counter}'\" type='button'>{$counter}</button>";
            }
          
        } elseif($lastpage > 5 + ($adjacents * 2)){
              
            if($page < 1 + ($adjacents * 2)) {
                  
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
                    if ($counter == $page)
                        $pagination .= "	  <button class='btn btn-primary' type='button'>{$counter}</button>";
                    else
                        $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$counter}'\" type='button'>{$counter}</button>";
                }
                $pagination .= "	  <button class='btn btn-default' type='button'>...</button>";
                $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$lpm1}'\" type='button'>{$lpm1}</button>";
                $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$lastpage}'\" type='button'>{$lastpage}</button>";
                      
            } elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                  
                $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page=1'\" type='button'>1</button>";
                $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page=2'\" type='button'>2</button>";
                $pagination .= "	  <button class='btn btn-default' type='button'>...</button>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination .= "	  <button class='btn btn-primary' type='button'>{$counter}</button>";
                    else
		                $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$counter}'\" type='button'>{$counter}</button>";
                }
                $pagination .= "	  <button class='btn btn-default' type='button'>...</button>";
                $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$lpm1}'\" type='button'>{$lpm1}</button>";
                $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$lastpage}'\" type='button'>{$lastpage}</button>";
                  
            } else {
                  
                $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page=1'\" type='button'>1</button>";
                $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page=2'\" type='button'>2</button>";
                $pagination .= "	  <button class='btn btn-default' type='button'>...</button>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination .= "	  <button class='btn btn-primary' type='button'>{$counter}</button>";
                    else
		                $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$counter}'\" type='button'>{$counter}</button>";
                }
            }
        }
          
            if ($page < $counter - 1) {
	              $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$next}'\" type='button'>{$nextlabel}</button>";
	              $pagination .= "	  <button class='btn btn-default' onclick=\"window.location='{$url}page={$lastpage}'\" type='button'>{$lastlabel}</button>";
            }
          
    	$pagination .= '	</div>';
    	$pagination .= '  </div>';
    	$pagination .= '</center>';

    }



    return $pagination;
}


?>

<div class="page-title">
	<div class="title_left">
		<h1>Orçamentos</h1>
	</div>
</div>

<?php

if($_GET["ped"] != "") {

if ($_SESSION['user']['nivel'] != '1' and $_SESSION['user']['nivel'] != '2') {
	echo '<center><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="pedidos.php">Voltar</a><br><br><br><br><br></center>';
    require("rodape.php"); 
    die();
}


?>
<form name="pedidos" class="form-horizontal" action="pedidos.php?acao=altera&pedido=<?php echo (float)$_GET["ped"]?>" method="post">

<?php

$pedido = $_GET["ped"];
$num_pedido = $_GET["ped"];

$query_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE `pedido` = '".$pedido."' ORDER BY `id` DESC"); // GROUP BY pedido");
$pedido = mysqli_fetch_array($query_pedidos);

$query_extra = mysqli_query($conn,"SELECT * FROM pedidos_extra WHERE `pedido` = '".$num_pedido."' ORDER BY `id` DESC"); // GROUP BY pedido");
$pedido_extra = mysqli_fetch_array($query_extra);

$id_ped = $pedido["id"];

$query_costura = mysqli_query($conn,"SELECT * FROM pedidos_costura WHERE `no_ped` = '".$num_pedido."' ORDER BY `id` DESC"); // GROUP BY pedido");
$costura = mysqli_fetch_array($query_costura);

/*
echo "<pre>";
print_r($costura);
echo "</pre>";
*/

if ($pedido["status"] == '2') {
	die("<center><br><br><br><br><span style=\"display:block;font-size:1.5em;color:#666;font-weight:bold;\">Orçamento aguardando aprovação!</span><br>Você não tem permissão para alterar o orçamento<br>enquanto estiver aguardando aprovação.</center>");
} elseif ($pedido["status"] == '4') {
	die("<center><br><br><br><br><span style=\"display:block;font-size:1.5em;color:#666;font-weight:bold;\">Orçamento já foi aprovado!</span><br>Você não tem permissão para alterar um orçamento que já foi aprovado pelo cliente.</center>");
}

?>


<?php
/* OCULTAR */
?>

<?php
echo '<input type="hidden" name="no_pedido" value="'.$pedido["pedido"].'">';
echo '<input type="hidden" name="selecao" value="'.$pedido["selecao"].'">';
echo '<input type="hidden" name="cnpj_cpf" value="'.$pedido["cnpj_cpf"].'">';
// echo '<input type="hidden" name="qtde" value="'.$pedido["qtde"].'">';
// echo '<input type="hidden" name="mercado" value="'.$pedido["mercado"].'">';
// echo '<input type="hidden" name="frete" value="'.$pedido["frete"].'">';
// echo '<input type="hidden" name="fornecedora" value="'.$pedido["fornecedora"].'">';
echo '<input type="hidden" name="usocons" value="'.$pedido["usocons"].'">';
echo '<input type="hidden" name="submit" value="'.$pedido["submit"].'">';
echo '<input type="hidden" name="valor_custo" value="'.$pedido["valor_custo"].'">';
echo '<input type="hidden" name="valor_final" value="'.$pedido["valor_final"].'">';
echo '<input type="hidden" name="revisao" value="'.$pedido["revisao"].'">';
echo '<input type="hidden" name="vendedor" value="'.$pedido["vendedor"].'">';
echo '<input type="hidden" name="id_vend" value="'.$pedido["id_vend"].'">';
echo '<input type="hidden" name="data" value="'.$pedido["data"].'">';
echo '<input type="hidden" name="obs_cliente" value="'.$pedido["obs_cliente"].'">';
echo '<input type="hidden" name="obs_comerciais" value="'.$pedido["obs_comerciais"].'">';
echo '<input type="hidden" name="correcoes" value="'.$pedido["correcoes"].'">';
echo '<input type="hidden" name="status" value="'.$pedido["status"].'">';

/*
echo '<input type="hidden" name="valor_mat_auxiliar" value="'.$pedido_extra["mat_auxiliar"].'">';
echo '<input type="hidden" name="valor_cif" value="'.$pedido_extra["cif"].'">';
echo '<input type="hidden" name="valor_mao_obra" value="'.$pedido_extra["mao_obra"].'">';
echo '<input type="hidden" name="valor_custo_bag" value="'.$pedido_extra["custo_bag"].'">';
echo '<input type="hidden" name="valor_icms" value="'.$pedido_extra["icms"].'">';
echo '<input type="hidden" name="valor_pis" value="'.$pedido_extra["pis"].'">';
echo '<input type="hidden" name="valor_cofins" value="'.$pedido_extra["cofins"].'">';
echo '<input type="hidden" name="valor_ir" value="'.$pedido_extra["ir"].'">';
echo '<input type="hidden" name="valor_csll" value="'.$pedido_extra["csll"].'">';
echo '<input type="hidden" name="valor_inss" value="'.$pedido_extra["inss"].'">';
echo '<input type="hidden" name="valor_perda" value="'.$pedido_extra["perda"].'">';
echo '<input type="hidden" name="valor_frete" value="'.$pedido_extra["frete"].'">';
echo '<input type="hidden" name="valor_comissao" value="'.$pedido_extra["comissao"].'">';
echo '<input type="hidden" name="valor_custo_fin" value="'.$pedido_extra["custo_fin"].'">';
echo '<input type="hidden" name="valor_margem" value="'.$pedido_extra["margem"].'">';
echo '<input type="hidden" name="valor_imposto_total" value="'.$pedido_extra["imposto_total"].'">';
echo '<input type="hidden" name="valor_cambio_dolar" value="'.$pedido_extra["cambio_dolar"].'">';
echo '<input type="hidden" name="valor_cambio_data" value="'.$pedido_extra["cambio_data"].'">';
*/
/*
echo "<pre>";
print_r($pedido_extra);
echo "</pre>";
*/

$query_detalhes = mysqli_query($conn,"SELECT * FROM `pedidos_det` WHERE `revisao_valor` LIKE (SELECT MAX(`revisao_valor`) FROM `pedidos_det` WHERE `pedido` LIKE '".(float)$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".(float)$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` ASC");
$row_pedidos_det = mysqli_fetch_array($query_detalhes);

echo '<input type="hidden" name="revisao_valor" value="'.$row_pedidos_det["revisao_valor"].'">';

?>


<div class="clearfix"></div>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Dados do cliente</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label class="control-label" for="nome_cliente">Nome do cliente</label>
					<input type="text" name="nome_cliente" class="form-control col-md-12 col-xs-12" value="<?php echo $pedido["nome_cliente"]; ?>">
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="referencia">Referência</label>
					<input type="text" name="referencia" class="form-control col-md-12 col-xs-12" value="<?php echo $pedido["referencia"]; ?>">
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="referencia">Uso / consumo</label><br>
					<div class="col-md-6 col-sm-6 col-xs-6" style="line-height:35px;"><input type="radio" name="usocons" class="form-control flat" value="sim"<?php if ($pedido["usocons"] == "sim") { echo " checked"; }?>> Sim </div>
					<div class="col-md-6 col-sm-6 col-xs-6" style="line-height:35px;"><input type="radio" name="usocons" class="form-control flat" value="nao"<?php if ($pedido["usocons"] == "nao") { echo " checked"; }?>> Não </div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-5 col-sm-5 col-xs-12">
					<label class="control-label" for="cidade_cliente">Cidade</label>
					<input type="text" name="cidade_cliente" class="form-control col-md-4 col-xs-12" value="<?php echo $pedido["cidade_cliente"]; ?>">
				</div>
				<div class="col-md-1 col-sm-1 col-xs-12">
					<label class="control-label" for="uf_cliente">Estado</label>
					<input type="text" name="uf_cliente" class="form-control col-md-1 col-xs-12" value="<?php echo $pedido["uf_cliente"]; ?>">
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="distancia_aprox">Distância aprox. da fornecedora</label>
					<select name="distancia_aprox" class="form-control col-md-4 col-xs-12">
						<option value="0"<?php if ($pedido["distancia_aprox"] == "0") { echo " selected"; } ?>>Frete FOB</option>
						<option value="1"<?php if ($pedido["distancia_aprox"] == "1") { echo " selected"; } ?>>Menos de 200 km</option>
						<option value="2"<?php if ($pedido["distancia_aprox"] == "2") { echo " selected"; } ?>>De 201 a 500 km</option>
						<option value="3"<?php if ($pedido["distancia_aprox"] == "3") { echo " selected"; } ?>>Acima de 501 km</option>
					</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="prazo">Prazo de pagamento</label> (dias)
					<input type="text" name="prazo" class="form-control" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Utilize 0 para pagamento à vista, -1 para pagamento antecipado ou / para mais de uma data" value="<?php echo $pedido["prazo"]; ?>" min="0">
<?php
/*					<select name="prazo" class="form-control col-md-3 col-xs-12">
						<option value="0"<?php if ($pedido["prazo"] == "0") { echo " selected"; } ?>>À vista</option>
						<option value="28"<?php if ($pedido["prazo"] == "28") { echo " selected"; } ?>>28 dias</option>
						<option value="30"<?php if ($pedido["prazo"] == "30") { echo " selected"; } ?>>30 dias</option>
						<option value="32"<?php if ($pedido["prazo"] == "32") { echo " selected"; } ?>>32 dias</option>
						<option value="35"<?php if ($pedido["prazo"] == "35") { echo " selected"; } ?>>35 dias</option>
						<option value="45"<?php if ($pedido["prazo"] == "45") { echo " selected"; } ?>>45 dias</option>
						<option value="60"<?php if ($pedido["prazo"] == "60") { echo " selected"; } ?>>60 dias</option>
					</select>
*/
?>
				</div>
<?php
$embarques = $pedido["embarques"];
$representante = $pedido["representante"];
$mercado = $pedido["mercado"];
$fornecedora = $pedido["fornecedora"];
?>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="embarques">Embarques</label>
					<input type="text" name="embarques" class="form-control" value="<?php echo $embarques; ?>">
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="representante">Representante</label>
					<input type="text" name="representante" class="form-control" value="<?php echo $representante; ?>">
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="fornecedora">Fornecedora</label>
					<select name="fornecedora" class="form-control">
<?
$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora`");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
?>
						<option value="valor_<? echo strtolower($row_fornec['sigla']); ?>"<? if($fornecedora == "valor_".strtolower($row_fornec['sigla'])){ echo " selected"; } ?>><? echo $row_fornec['apelido']; ?></option>
<?
}
?>
					</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="mercado">Mercado</label>
					<select name="mercado" class="form-control">
						<option value="int"<?php if($mercado == "int"){ echo " selected"; } ?>>Mercado Interno</option>
						<option value="ext"<?php if($mercado == "ext"){ echo " selected"; } ?>>Exportação</option>
					</select>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Dados do produto acondicionado</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label class="control-label" for="nome_prod">Nome do produto</label>
					<input type="text" name="nome_prod" class="form-control" value="<?php echo $pedido["nome_prod"]; ?>">
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="frete">Frete tipo</label>
					<select name="frete" class="form-control">
						<option value="exw"<?php if ($pedido["frete"] == "exw") { echo " selected"; } ?>>EXW (A partir da fábrica)</option>
						<option value="fca"<?php if ($pedido["frete"] == "fca") { echo " selected"; } ?>>FCA (Transportador livre)</option>
						<option value="cpt"<?php if ($pedido["frete"] == "cpt") { echo " selected"; } ?>>CPT (Frete pago até)</option>
						<option value="cip"<?php if ($pedido["frete"] == "cip") { echo " selected"; } ?>>CIP (Frete e seguro pagos até)</option>
						<option value="dat"<?php if ($pedido["frete"] == "dat") { echo " selected"; } ?>>DAT (Entregue no terminal)</option>
						<option value="dap"<?php if ($pedido["frete"] == "dap") { echo " selected"; } ?>>DAP (Entregue no local de destino)</option>
						<option value="ddp"<?php if ($pedido["frete"] == "ddp") { echo " selected"; } ?>>DDP (Entregue com direitos pagos)</option>
						<option value="fas"<?php if ($pedido["frete"] == "fas") { echo " selected"; } ?>>FAS (Livre junto ao costado do navio)</option>
						<option value="fob"<?php if ($pedido["frete"] == "fob") { echo " selected"; } ?>>FOB (Livre a bordo)</option>
						<option value="cfr"<?php if ($pedido["frete"] == "cfr") { echo " selected"; } ?>>CFR (Custo e frete)</option>
						<option value="cif"<?php if ($pedido["frete"] == "cif") { echo " selected"; } ?>>CIF (Custo, seguro e frete)</option>
					</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="nome_prod">Quantidade</label>
					<input type="text" name="qtde" class="form-control" value="<?php echo $pedido["qtde"]; ?>">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label class="control-label" for="segmento_cliente">Segmento</label>
					<select name="segmento_cliente" class="form-control col-md-5 col-xs-12">
					<?php
					$segmento = mysqli_query($conn,"SELECT * FROM segmentos ORDER BY segmento ASC");
					while($row = mysqli_fetch_array($segmento)) {
						if ($row['id'] == $pedido["segmento_cliente"]) {
							echo "    <option SELECTED value=\"" . $row['id'] . "\">" . $row['segmento'] . "</option>\n";
						} else {
							echo "    <option value=\"" . $row['id'] . "\">" . $row['segmento'] . "</option>\n";
						}
					}
					?>
					</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12 has-feedback">
					<label class="control-label" for="dens_aparente">Densidade aparente</label>
					<input type="text" id="dens_aparente" name="dens_aparente" class="form-control col-md-4 col-xs-12" value="<?php echo $pedido["dens_aparente"]; ?>"><span class="form-control-feedback right" style="width:60px;"> gr/cm<sup>3</sup></span>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12 has-feedback">
					<label class="control-label" for="temperatura">Temperatura</label>
					<input type="text" id="temperatura" name="temperatura" class="form-control col-md-3 col-xs-12" value="<?php echo $pedido["temperatura"]; ?>"><span class="form-control-feedback right" style="width:30px;"> °C</span>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Utilização do bag</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-5 col-sm-5 col-xs-12">
					<label class="control-label" for="class_uso">Classificação de uso</label>
					<select id="class_uso" name="class_uso" class="form-control col-md-5 col-xs-12" required>
						<option value="0" selected>Selecione</option>
						<?php
						$classif_uso = mysqli_query($conn,"SELECT * FROM classif_uso");
						while($row = mysqli_fetch_array($classif_uso)) {
						$valor = $row['fator'] . "_" . $row['id'];
						  echo "    <option value=\"" . $valor;
						  if ($valor == $pedido["class_uso"]) {
							echo "\" selected";
						  } else {
							echo "\"";
						  }
						  echo ">" . $row['classif'] . "</option>\n";
						}
						?>
					</select>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-12">
					<label class="control-label" for="transporte">Transporte</label>
					<select name="transporte" class="form-control col-md-5 col-xs-12">
						<option <?php if ($pedido["transporte"] == "Caminhão baú (fechado - abre por trás)") { echo " selected"; } ?>>Caminhão baú (fechado - abre por trás)</option>
						<option <?php if ($pedido["transporte"] == "Caminhão sider (fechado - abre pelos lados)") { echo " selected"; } ?>>Caminhão sider (fechado - abre pelos lados)</option>
						<option <?php if ($pedido["transporte"] == "Caminhão de grade baixa (aberto)") { echo " selected"; } ?>>Caminhão de grade baixa (aberto)</option>
						<option <?php if ($pedido["transporte"] == "Caminhão graneleiro (aberto - grade alta)") { echo " selected"; } ?>>Caminhão graneleiro (aberto - grade alta)</option>
						<option <?php if ($pedido["transporte"] == "Container metálico de 20' (2,35x5,89/2,38m)") { echo " selected"; } ?>>Container metálico de 20' (2,35x5,89/2,38m)</option>
						<option <?php if ($pedido["transporte"] == "Container metálico de 40' (2,35x12,02/2,38m)") { echo " selected"; } ?>>Container metálico de 40' (2,35x12,02/2,38m)</option>
						<option <?php if ($pedido["transporte"] == "Porão de navio") { echo " selected"; } ?>>Porão de navio</option>
						<option <?php if ($pedido["transporte"] == "Vagão de trem com abertura lateral (fechado)") { echo " selected"; } ?>>Vagão de trem com abertura lateral (fechado)</option>
						<option <?php if ($pedido["transporte"] == "Vagão de trem aberto") { echo " selected"; } ?>>Vagão de trem aberto</option>
					</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<label class="control-label" for="dem_mensal">Demanda mensal</label>
					<input type="text" name="dem_mensal" class="form-control col-md-2 col-xs-12" value="<?php echo $pedido["dem_mensal"]; ?>">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-5 col-sm-5 col-xs-12 has-feedback">
					<label class="control-label" for="carga_nominal">Carga nominal</label>
					<input type="text" id="carga_nominal" name="carga_nominal" class="form-control col-md-5 col-xs-12" value="<?php echo $pedido["carga_nominal"]; ?>">
					<span class="form-control-feedback right" style="width:30px;"> kg</span>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-12">
					<label class="control-label" for="armazenagem">Armazenagem</label>
					<select name="armazenagem" class="form-control col-md-5 col-xs-12">
						<option <?php if ($pedido["armazenagem"] == "Local coberto e fechado") { echo " selected"; } ?>>Local coberto e fechado</option>
						<option <?php if ($pedido["armazenagem"] == "Local apenas coberto") { echo " selected"; } ?>>Local apenas coberto</option>
						<option <?php if ($pedido["armazenagem"] == "Local aberto") { echo " selected"; } ?>>Local aberto</option>
					</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<label class="control-label" for="dem_anual">Demanda anual</label>
					<input type="text" name="dem_anual" class="form-control col-md-2 col-xs-12" value="<?php echo $pedido["dem_anual"]; ?>">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-9 col-sm-9 col-xs-12 has-feedback">
					<label class="control-label" for="class_prod">Classificação de produtividade</label>
					<select id="class_prod" name="class_prod" class="form-control col-md-5 col-xs-12" required>
						<option value=""<?php if ($pedido_extra["class_prod"] == "") { echo " selected"; } ?>>Selecione</option>
						<option value="a"<?php if ($pedido_extra["class_prod"] == "a") { echo " selected"; } ?>>TIPO A - Alta produtividade</option>
						<option value="b"<?php if ($pedido_extra["class_prod"] == "b") { echo " selected"; } ?>>TIPO B - Média produtividade</option>
						<option value="c"<?php if ($pedido_extra["class_prod"] == "c") { echo " selected"; } ?>>TIPO C - Baixa produtividade</option>
					</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12 has-feedback">
					<label style="margin-top: 15px;"><input type="checkbox" id="cost_fio_topo" name="cost_fio_topo"<?php if ($pedido["cost_fio_topo"] == "1") { echo " checked"; } ?>> Costura à fio no topo</label><br>
					<label><input type="checkbox" id="cost_fio_base" name="cost_fio_base"<?php if ($pedido["cost_fio_base"] == "1") { echo " checked"; } ?>> Costura à fio na base</label>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12 reticencias">
		<h3>Informações técnicas de projeto</h3>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Corpo</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
				<label class="control-label" for="corpo">Modelo</label>
				<select id="sel_corpo" name="corpo" class="form-control">
					<option value="gota"<?php if ($pedido["corpo"] == "gota") { echo " selected"; } ?>>Gota (Single Loop)</option>
					<option value="qowa"<?php if ($pedido["corpo"] == "qowa") { echo " selected"; } ?>>Plano</option>
					<option value="cowa"<?php if ($pedido["corpo"] == "cowa") { echo " selected"; } ?>>Tubular</option>
					<option value="qowac"<?php if ($pedido["corpo"] == "qowac") { echo " selected"; } ?>>Painel U</option>
					<option value="qowacf"<?php if ($pedido["corpo"] == "qowacf") { echo " selected"; } ?>>Painel U com forro</option>
					<option value="qowad4"<?php if ($pedido["corpo"] == "qowad4") { echo " selected"; } ?>>Travado com costuras nos cantos</option>
					<option value="qowad8"<?php if ($pedido["corpo"] == "qowad8") { echo " selected"; } ?>>Travado em gomos</option>
					<option value="cowad"<?php if ($pedido["corpo"] == "cowad") { echo " selected"; } ?>>Travado tubular</option>
					<option value="qowa2"<?php if ($pedido["corpo"] == "qowa2") { echo " selected"; } ?>>Plano duplo</option>
					<option value="cowa2"<?php if ($pedido["corpo"] == "cowa2") { echo " selected"; } ?>>Tubular duplo</option>
					<option value="qowaf"<?php if ($pedido["corpo"] == "qowaf") { echo " selected"; } ?>>Plano com forro</option>
					<option value="qowadlf"<?php if ($pedido["corpo"] == "qowadlf") { echo " selected"; } ?>>Plano com forro travado</option>
					<option value="cowaf"<?php if ($pedido["corpo"] == "cowaf") { echo " selected"; } ?>>Tubular com forro</option>
					<option value="qowao"<?php if ($pedido["corpo"] == "qowao") { echo " selected"; } ?>>Plano condutivo</option>
					<option value="qowafi"<?php if ($pedido["corpo"] == "qowafi") { echo " selected"; } ?>>Plano com forro VCI</option>
					<option value="cowafi"<?php if ($pedido["corpo"] == "cowafi") { echo " selected"; } ?>>Tubular com forro VCI</option>
					<option value="qowam"<?php if ($pedido["corpo"] == "qowam") { echo " selected"; } ?>>Plano antimicrobiano</option>
					<option value="qowaa"<?php if ($pedido["corpo"] == "qowaa") { echo " selected"; } ?>>Plano arejado</option>
					<option value="qowat"<?php if ($pedido["corpo"] == "qowat") { echo " selected"; } ?>>Plano térmico</option>
					<option value="qhe"<?php if ($pedido["corpo"] == "qhe") { echo " selected"; } ?>>Plano com fechamento especial</option>
					<option value="qhe_ref"<?php if ($pedido["corpo"] == "qhe_ref") { echo " selected"; } ?>>Plano QHE reforçado com fita</option>
					<option value="rof"<?php if ($pedido["corpo"] == "rof") { echo " selected"; } ?>>Porta ensacado simples</option>
					<option value="qms"<?php if ($pedido["corpo"] == "qms") { echo " selected"; } ?>>Plano com duas alças</option>
					<option value="cms"<?php if ($pedido["corpo"] == "cms") { echo " selected"; } ?>>Tubular com duas alças</option>
					<option value="outros"<?php if ($pedido["corpo"] == "outros") { echo " selected"; } ?>>Outro</option>
				</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-4 col-sm-4 col-xs-4 has-feedback" style="padding-left:0;">
					<label class="control-label" for="base1">Base 1</label>
					<input type="text" id="base1" name="base1" class="form-control" value="<?php echo $pedido["base1"]; ?>"><span class="form-control-feedback right" style="width:30px;"> cm</span>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-4 has-feedback" style="padding-left:0;">
					<label class="control-label" for="base">Base 2</label>
					<input type="text" id="base2" name="base2" class="form-control" value="<?php echo $pedido["base2"]; ?>"><span class="form-control-feedback right" style="width:30px;"> cm</span>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-4 has-feedback" style="padding-left:0;">
					<label class="control-label" for="altura">Altura</label>
					<input type="text" id="altura" name="altura" class="form-control" value="<?php echo $pedido["altura"]; ?>"><span class="form-control-feedback right" style="width:30px;"> cm</span>
					</div>
<?php
/*
					<div class="col-md-3 col-sm-3 col-xs-3" style="padding-left:0;padding-top:32px;">
					<label><input type="checkbox" id="plast_check" name="plastificado"<?php if ($pedido["plastificado"] == "1") { echo " CHECKED"; } ?>> Laminado</label>
					</div>
*/
?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-6 col-sm-6 col-xs-12 has-feedback" style="padding-left:0;">
					<label class="control-label" for="gramat_corpo">Gramatura do corpo</label>
					<select id="gramat_corpo" name="gramat_corpo" class="form-control" required>
						<option value="">Não selecionado</option>
<?php
$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;");
while($gramat_row = mysqli_fetch_array($gramaturas_query)) {
  echo "						<option value=\"".$gramat_row['gramat_real']."\"";
  if($pedido["gramat_corpo"] == $gramat_row['gramat_real']) { echo " selected"; }
  echo ">".$gramat_row['gramat_desc']."</option>\n";
}

/*
						<option value="130"<?php if ($pedido["gramat_corpo"] == "130") { echo " selected"; } ?>>130</option>
						<option value="147"<?php if ($pedido["gramat_corpo"] == "147") { echo " selected"; } ?>>130 + 17</option>
						<option value="145"<?php if ($pedido["gramat_corpo"] == "145") { echo " selected"; } ?>>145</option>
						<option value="170"<?php if ($pedido["gramat_corpo"] == "170") { echo " selected"; } ?>>145 + 25</option>
						<option value="160"<?php if ($pedido["gramat_corpo"] == "160") { echo " selected"; } ?>>160</option>
						<option value="185"<?php if ($pedido["gramat_corpo"] == "185") { echo " selected"; } ?>>160 + 25</option>
						<option value="191"<?php if ($pedido["gramat_corpo"] == "191") { echo " selected"; } ?>>160 + 30</option>
						<option value="190"<?php if ($pedido["gramat_corpo"] == "190") { echo " selected"; } ?>>190</option>
						<option value="215"<?php if ($pedido["gramat_corpo"] == "215") { echo " selected"; } ?>>190 + 25</option>
						<option value="221"<?php if ($pedido["gramat_corpo"] == "221") { echo " selected"; } ?>>190 + 30</option>
						<option value="220"<?php if ($pedido["gramat_corpo"] == "220") { echo " selected"; } ?>>220</option>
						<option value="245"<?php if ($pedido["gramat_corpo"] == "245") { echo " selected"; } ?>>220 + 25</option>
						<option value="250"<?php if ($pedido["gramat_corpo"] == "250") { echo " selected"; } ?>>220 + 30</option>
						<option value="240"<?php if ($pedido["gramat_corpo"] == "240") { echo " selected"; } ?>>240</option>
						<option value="265"<?php if ($pedido["gramat_corpo"] == "265") { echo " selected"; } ?>>240 + 25</option>
						<option value="270"<?php if ($pedido["gramat_corpo"] == "270") { echo " selected"; } ?>>270</option>
						<option value="295"<?php if ($pedido["gramat_corpo"] == "295") { echo " selected"; } ?>>270 + 25</option>
*/
?>
					</select><span class="form-control-feedback right" style="width:60px;"> g/m²</span>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 has-feedback" style="padding-left:0;">
					<label class="control-label" for="gramat_forro">Gramatura do forro</label>
					<select id="gramat_forro" name="gramat_forro" class="form-control">
						<option value="">Não selecionado</option>
<?php
$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;");
while($gramat_row = mysqli_fetch_array($gramaturas_query)) {
  echo "						<option value=\"".$gramat_row['gramat_real']."\"";
  if($pedido["gramat_forro"] == $gramat_row['gramat_real']) { echo " selected"; }
  echo ">".$gramat_row['gramat_desc']."</option>\n";
}
/*
?>						<option value="65"<?php if ($pedido["gramat_forro"] == "65") { echo " selected"; } ?>>48 + 17</option>
						<option value="70"<?php if ($pedido["gramat_forro"] == "70") { echo " selected"; } ?>>50 + 20</option>
						<option value="110"<?php if ($pedido["gramat_forro"] == "110") { echo " selected"; } ?>>110</option>
						<option value="125"<?php if ($pedido["gramat_forro"] == "125") { echo " selected"; } ?>>100 + 25</option>
						<option value="147"<?php if ($pedido["gramat_forro"] == "147") { echo " selected"; } ?>>130 + 17</option>
						<option value="160"<?php if ($pedido["gramat_forro"] == "160") { echo " selected"; } ?>>160</option>
						<option value="190"<?php if ($pedido["gramat_forro"] == "190") { echo " selected"; } ?>>190</option>
<?php
*/
?>
					</select><span class="form-control-feedback right" style="width:60px;"> g/m²</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-6 col-sm-6 col-xs-12 has-feedback" style="padding-left:0;">
					<label class="control-label" for="corpo_cor">Cor do tecido</label>
					<select name="corpo_cor" id="corpo_cor" class="form-control">
						<option value="branco"<?php if ($pedido["corpo_cor"] == "branco") { echo " selected"; } ?>>Branco (natural)</option>
						<option value="cinza"<?php if ($pedido["corpo_cor"] == "cinza") { echo " selected"; } ?>>Cinza</option>
						<option value="azul"<?php if ($pedido["corpo_cor"] == "azul") { echo " selected"; } ?>>Azul Carijó</option>
						<option value="marrom"<?php if ($pedido["corpo_cor"] == "marrom") { echo " selected"; } ?>>Marrom Carijó</option>
						<option value="preto"<?php if ($pedido["corpo_cor"] == "preto") { echo " selected"; } ?>>Preto Carijó</option>
						<option value="verde"<?php if ($pedido["corpo_cor"] == "verde") { echo " selected"; } ?>>Verde Carijó</option>
						<option value="outro"<?php if ($pedido["corpo_cor"] == "outro") { echo " selected"; } ?>>Outro</option>
					</select>
					<input type="text" id="corpo_cor_outro" name="corpo_cor_outro" class="form-control" style="margin-top:10px;" value="<?php echo $pedido["corpo_cor_outro"]; ?>">
					</div>

					<div id="plastspan" class="col-md-6 col-sm-6 col-xs-12" style="padding-left: 0;">
					<label class="control-label" for="lamin_cor">Cor da laminação</label>
					<select name="lamin_cor" id="lamin_cor" class="form-control">
						<option value="padrao"<?php if ($pedido["lamin_cor"] == "padrao") { echo " selected"; } ?>>Padrão</option>
						<option value="branco"<?php if ($pedido["lamin_cor"] == "branco") { echo " selected"; } ?>>Branco</option>
						<option value="preto"<?php if ($pedido["lamin_cor"] == "preto") { echo " selected"; } ?>>Preto</option>
						<option value="outro"<?php if ($pedido["lamin_cor"] == "outro") { echo " selected"; } ?>>Outro</option>
					</select>
					<input type="text" id="lamin_cor_outro" name="lamin_cor_outro" class="form-control" style="margin-top:10px;" value="<?php echo $pedido["lamin_cor_outro"]; ?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-12 col-sm-12 col-xs-12" style="padding-left:0;">
					<label class="control-label" for="tipo_cost_corpo">Tipo de costura</label>
					<select id="tipo_cost_corpo" name="tipo_cost_corpo" class="form-control">
						<option value="simples"<?php if ($costura["corpo"] == "simples") { echo " selected"; } ?>>Costura simples</option>
						<option value="simples1ved"<?php if ($costura["corpo"] == "simples1ved") { echo " selected"; } ?>>Costura simples + 1 vedante</option>
						<option value="simples2ved"<?php if ($costura["corpo"] == "simples2ved") { echo " selected"; } ?>>Costura simples + 2 vedante</option>
						<option value="dupla"<?php if ($costura["corpo"] == "dupla") { echo " selected"; } ?>>Costura dupla</option>
						<option value="overlock"<?php if ($costura["corpo"] == "overlock") { echo " selected"; } ?>>Overlock + corrente</option>
					</select>
					</div>
				</div>
			</div>
		</div>
	</div>

		<div class="x_panel">
			<div class="x_title">
				<h2>Sistema de enchimento</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-10 col-sm-10 col-xs-10">
				<label class="control-label" for="sel_carga">Modelo</label>
				<select id="sel_carga" name="carga" class="form-control">
					<option value="vazio"<?php if ($pedido["carga"] == "vazio") { echo " selected"; } ?>>Sem sistema de enchimento</option>
					<option value="c_saia"<?php if ($pedido["carga"] == "c_saia") { echo " selected"; } ?>>Saia</option>
					<option value="c_afunilada"<?php if ($pedido["carga"] == "c_afunilada") { echo " selected"; } ?>>Saia afunilada</option>
					<option value="c_simples"<?php if ($pedido["carga"] == "c_simples") { echo " selected"; } ?>>Válvula simples</option>
					<option value="c_simples_afunilada"<?php if ($pedido["carga"] == "c_simples_afunilada") { echo " selected"; } ?>>Válvula simples com tampa afunilada</option>
					<option value="c_prot_mochila"<?php if ($pedido["carga"] == "c_prot_mochila") { echo " selected"; } ?>>Válvula com proteção tipo flap</option>
					<option value="c_tipo_as"<?php if ($pedido["carga"] == "c_tipo_as") { echo " selected"; } ?>>Tampa tipo porta-ensacado</option>
				</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-2">
				<label><input type="checkbox" id="c_quadrado" name="c_quadrado" value="c_quadrado" style="margin-top:40px;"<?php if ($pedido["c_quadrado"] == "c_quadrado") { echo " CHECKED"; } ?>> Quadrado</label>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
				<label class="control-label reticencias" for="carga1">Dimensão da boca</label>
				<input type="text" id="carga1" name="carga1" class="form-control" value="<?php echo $pedido["carga1"]; ?>">
				<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
				<label class="control-label" for="carga2">Altura</label>
				<input type="text" id="carga2" name="carga2" class="form-control" value="<?php echo $pedido["carga2"]; ?>">
				<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<label class="control-label" for="gramat_tampa">Gramatura da tampa:</label>
					<select id="gramat_tampa" name="gramat_tampa" class="form-control">
						<option value=""<?php if ($pedido["tampa"] == "") { echo " selected"; } ?>>Não se aplica</option>
<?php
$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;");
while($gramat_row = mysqli_fetch_array($gramaturas_query)) {
  echo "						<option value=\"".$gramat_row['gramat_real']."\"";
  if($pedido["tampa"] == $gramat_row['gramat_real']) { echo " selected"; }
  echo ">".$gramat_row['gramat_desc']."</option>\n";
}
/*
						<option value="65"<?php if ($pedido["tampa"] == "65") { echo " selected"; } ?>>48 + 17</option>
						<option value="70"<?php if ($pedido["tampa"] == "70") { echo " selected"; } ?>>50 + 20</option>
						<option value="130"<?php if ($pedido["tampa"] == "130") { echo " selected"; } ?>>130</option>
						<option value="147"<?php if ($pedido["tampa"] == "147") { echo " selected"; } ?>>130 + 17</option>
						<option value="145"<?php if ($pedido["tampa"] == "145") { echo " selected"; } ?>>145</option>
						<option value="170"<?php if ($pedido["tampa"] == "170") { echo " selected"; } ?>>145 + 25</option>
						<option value="160"<?php if ($pedido["tampa"] == "160") { echo " selected"; } ?>>160</option>
						<option value="185"<?php if ($pedido["tampa"] == "185") { echo " selected"; } ?>>160 + 25</option>
						<option value="191"<?php if ($pedido["tampa"] == "191") { echo " selected"; } ?>>160 + 30</option>
						<option value="190"<?php if ($pedido["tampa"] == "190") { echo " selected"; } ?>>190</option>
						<option value="215"<?php if ($pedido["tampa"] == "215") { echo " selected"; } ?>>190 + 25</option>
						<option value="221"<?php if ($pedido["tampa"] == "221") { echo " selected"; } ?>>190 + 30</option>
						<option value="220"<?php if ($pedido["tampa"] == "220") { echo " selected"; } ?>>220</option>
						<option value="245"<?php if ($pedido["tampa"] == "245") { echo " selected"; } ?>>220 + 25</option>
						<option value="250"<?php if ($pedido["tampa"] == "250") { echo " selected"; } ?>>220 + 30</option>
						<option value="240"<?php if ($pedido["tampa"] == "240") { echo " selected"; } ?>>240</option>
						<option value="265"<?php if ($pedido["tampa"] == "265") { echo " selected"; } ?>>240 + 25</option>
						<option value="270"<?php if ($pedido["tampa"] == "270") { echo " selected"; } ?>>270</option>
						<option value="295"<?php if ($pedido["tampa"] == "295") { echo " selected"; } ?>>270 + 25</option>
*/
?>
					</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<label class="control-label" for="gramat_valvula">Gramatura da válvula:</label>
					<select id="gramat_valvula" name="gramat_valvula" class="form-control">
						<option value=""<?php if ($pedido["valvula"] == "") { echo " selected"; } ?>>Não se aplica</option>
<?php
$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;");
while($gramat_row = mysqli_fetch_array($gramaturas_query)) {
  echo "						<option value=\"".$gramat_row['gramat_real']."\"";
  if($pedido["valvula"] == $gramat_row['gramat_real']) { echo " selected"; }
  echo ">".$gramat_row['gramat_desc']."</option>\n";
}
/*
?>						<option value="65"<?php if ($pedido["valvula"] == "65") { echo " selected"; } ?>>48 + 17</option>
						<option value="70"<?php if ($pedido["valvula"] == "70") { echo " selected"; } ?>>50 + 20</option>
						<option value="110"<?php if ($pedido["valvula"] == "110") { echo " selected"; } ?>>110</option>
						<option value="125"<?php if ($pedido["valvula"] == "125") { echo " selected"; } ?>>100 + 25</option>
						<option value="130"<?php if ($pedido["valvula"] == "130") { echo " selected"; } ?>>130</option>
						<option value="147"<?php if ($pedido["valvula"] == "147") { echo " selected"; } ?>>130 + 17</option>
<?php
*/
?>
					</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<label class="control-label" for="gramat_saia">Gramatura da saia:</label>
					<select id="gramat_saia" name="gramat_saia" class="form-control">
						<option value=""<?php if ($pedido["saia"] == "") { echo " selected"; } ?>>Não se aplica</option>
<?php
$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;");
while($gramat_row = mysqli_fetch_array($gramaturas_query)) {
  echo "						<option value=\"".$gramat_row['gramat_real']."\"";
  if($pedido["saia"] == $gramat_row['gramat_real']) { echo " selected"; }
  echo ">".$gramat_row['gramat_desc']."</option>\n";
}
/*
?>
						<option value="65"<?php if ($pedido["saia"] == "65") { echo " selected"; } ?>>48 + 17</option>
						<option value="70"<?php if ($pedido["saia"] == "70") { echo " selected"; } ?>>50 + 20</option>
						<option value="110"<?php if ($pedido["saia"] == "110") { echo " selected"; } ?>>110</option>
						<option value="125"<?php if ($pedido["saia"] == "125") { echo " selected"; } ?>>100 + 25</option>
						<option value="130"<?php if ($pedido["saia"] == "130") { echo " selected"; } ?>>130</option>
						<option value="147"<?php if ($pedido["saia"] == "147") { echo " selected"; } ?>>130 + 17</option>
<?php
*/
?>
					</select>
				</div>
<?php
/*
				<div id="tampa" style="min-width: 180px; text-align:center;"><br>
					Tampa: <label><input type="radio" name="tampa" value="tampa_leve"<?php if ($pedido["tampa"] == "tampa_leve") { echo " CHECKED"; } ?> class="input" style="margin:0 0 0 15px;"> leve</label> <label><input type="radio" name="tampa" value="tampa_pesada"<?php if ($pedido["tampa"] == "tampa_pesada") { echo " CHECKED"; } ?> class="input" style="margin:0 0 0 15px;"> pesada</label><br>
					Válvula: <label><input type="radio" name="valvula" value="valv_leve"<?php if ($pedido["valvula"] == "valv_leve") { echo " CHECKED"; } ?> class="input" style="margin:0 0 0 15px;"> leve</label> <label><input type="radio" name="valvula" value="valv_pesada"<?php if ($pedido["valvula"] == "valv_pesada") { echo " CHECKED"; } ?> class="input" style="margin:0 0 0 15px;"> pesada</label><br>
					Saia: <label><input type="radio" name="saia" value="saia_leve"<?php if ($pedido["saia"] == "saia_leve") { echo " CHECKED"; } ?> class="input" style="margin:0 0 0 15px;"> leve</label> <label><input type="radio" name="saia" value="saia_pesada"<?php if ($pedido["saia"] == "saia_pesada") { echo " CHECKED"; } ?> class="input" style="margin:0 0 0 15px;"> pesada</label>
				</div>
*/
?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-12 col-sm-12 col-xs-12" style="padding-left:0;">
					<label class="control-label" for="tipo_cost_enchim">Tipo de costura</label>
					<select id="tipo_cost_enchim" name="tipo_cost_enchim" class="form-control">
						<option value="simples"<?php if ($costura["enchim"] == "simples") { echo " selected"; } ?>>Costura simples</option>
						<option value="simples1ved"<?php if ($costura["enchim"] == "simples1ved") { echo " selected"; } ?>>Costura simples + 1 vedante</option>
						<option value="simples2ved"<?php if ($costura["enchim"] == "simples2ved") { echo " selected"; } ?>>Costura simples + 2 vedante</option>
						<option value="dupla"<?php if ($costura["enchim"] == "dupla") { echo " selected"; } ?>>Costura dupla</option>
					</select>
					</div>
				</div>
			</div>
		</div>
	</div>

		<div class="x_panel">
			<div class="x_title">
				<h2>Sistema de esvaziamento</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-10 col-sm-10 col-xs-10">
				<label class="control-label" for="sel_descarga">Modelo</label>
				<select id="sel_descarga" name="descarga" class="form-control">
					<option value="vazio"<?php if ($pedido["descarga"] == "vazio") { echo " selected"; } ?>>Sem sistema de esvaziamento</option>
					<option value="d_simples"<?php if ($pedido["descarga"] == "d_simples") { echo " selected"; } ?>>Válvula simples</option>
					<option value="d_prot_presilha"<?php if ($pedido["descarga"] == "d_prot_presilha") { echo " selected"; } ?>>Válvula com proteção tipo "X"</option>
					<option value="d_prot_mochila"<?php if ($pedido["descarga"] == "d_prot_mochila") { echo " selected"; } ?>>Válvula com proteção tipo flap</option>
					<option value="d_afunilado"<?php if ($pedido["descarga"] == "d_afunilado") { echo " selected"; } ?>>Afunilado</option>
					<option value="d_total"<?php if ($pedido["descarga"] == "d_total") { echo " selected"; } ?>>Abertura total simples</option>
				</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-2">
				<label><input type="checkbox" id="d_redondo" name="d_redondo" value="d_redondo" style="margin-top:40px;"<?php if ($pedido["d_redondo"] == "d_redondo") { echo " CHECKED"; } ?>> Redondo</label>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
				<label class="control-label reticencias" for="carga1">Dimensão da boca</label>
				<input type="text" id="descarga1" name="descarga1" class="form-control" value="<?php echo $pedido["descarga1"]; ?>">
				<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
				<label class="control-label" for="carga2">Altura</label>
				<input type="text" id="descarga2" name="descarga2" class="form-control" value="<?php echo $pedido["descarga2"]; ?>">
				<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label" for="gramat_fundo_d">Gramatura do fundo:</label>
					<select id="gramat_fundo_d" name="gramat_fundo_d" class="form-control">
						<option value=""<?php if ($pedido["fundo"] == "") { echo " selected"; } ?>>Não se aplica</option>
<?php
$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;");
while($gramat_row = mysqli_fetch_array($gramaturas_query)) {
  echo "						<option value=\"".$gramat_row['gramat_real']."\"";
  if($pedido["fundo"] == $gramat_row['gramat_real']) { echo " selected"; }
  echo ">".$gramat_row['gramat_desc']."</option>\n";
}
/*
						<option value="130"<?php if ($pedido["fundo"] == "130") { echo " selected"; } ?>>130</option>
						<option value="147"<?php if ($pedido["fundo"] == "147") { echo " selected"; } ?>>130 + 17</option>
						<option value="145"<?php if ($pedido["fundo"] == "145") { echo " selected"; } ?>>145</option>
						<option value="170"<?php if ($pedido["fundo"] == "170") { echo " selected"; } ?>>145 + 25</option>
						<option value="160"<?php if ($pedido["fundo"] == "160") { echo " selected"; } ?>>160</option>
						<option value="185"<?php if ($pedido["fundo"] == "185") { echo " selected"; } ?>>160 + 25</option>
						<option value="191"<?php if ($pedido["fundo"] == "191") { echo " selected"; } ?>>160 + 30</option>
						<option value="190"<?php if ($pedido["fundo"] == "190") { echo " selected"; } ?>>190</option>
						<option value="215"<?php if ($pedido["fundo"] == "215") { echo " selected"; } ?>>190 + 25</option>
						<option value="221"<?php if ($pedido["fundo"] == "221") { echo " selected"; } ?>>190 + 30</option>
						<option value="220"<?php if ($pedido["fundo"] == "220") { echo " selected"; } ?>>220</option>
						<option value="245"<?php if ($pedido["fundo"] == "245") { echo " selected"; } ?>>220 + 25</option>
						<option value="250"<?php if ($pedido["fundo"] == "250") { echo " selected"; } ?>>220 + 30</option>
						<option value="240"<?php if ($pedido["fundo"] == "240") { echo " selected"; } ?>>240</option>
						<option value="265"<?php if ($pedido["fundo"] == "265") { echo " selected"; } ?>>240 + 25</option>
						<option value="270"<?php if ($pedido["fundo"] == "270") { echo " selected"; } ?>>270</option>
						<option value="295"<?php if ($pedido["fundo"] == "295") { echo " selected"; } ?>>270 + 25</option>
*/
?>
					</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label" for="gramat_valvula_d">Gramatura da válvula:</label>
					<select id="gramat_valvula_d" name="gramat_valvula_d" class="form-control">
						<option value=""<?php if ($pedido["valvula_d"] == "") { echo " selected"; } ?>>Não se aplica</option>
<?php
$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;");
while($gramat_row = mysqli_fetch_array($gramaturas_query)) {
  echo "						<option value=\"".$gramat_row['gramat_real']."\"";
  if($pedido["valvula_d"] == $gramat_row['gramat_real']) { echo " selected"; }
  echo ">".$gramat_row['gramat_desc']."</option>\n";
}
/*
?>
						<option value="65"<?php if ($pedido["valvula_d"] == "65") { echo " selected"; } ?>>48 + 17</option>
						<option value="70"<?php if ($pedido["valvula_d"] == "70") { echo " selected"; } ?>>50 + 20</option>
						<option value="110"<?php if ($pedido["valvula_d"] == "110") { echo " selected"; } ?>>110</option>
						<option value="125"<?php if ($pedido["valvula_d"] == "125") { echo " selected"; } ?>>100 + 25</option>
						<option value="130"<?php if ($pedido["valvula_d"] == "130") { echo " selected"; } ?>>130</option>
						<option value="147"<?php if ($pedido["valvula_d"] == "147") { echo " selected"; } ?>>130 + 17</option>
<?php
*/
?>
					</select>
				</div>
<?php
/*
				<div class="col-md-6 col-sm-6 col-xs-12">
				<div id="fundo_d" style="min-width: 180px; text-align:center; margin-top: 18px;">
					Fundo: <label><input type="radio" name="fundo" value="fundo_leve"<?php if ($pedido["fundo"] == "fundo_leve") { echo " CHECKED"; } ?> class="input" style="margin:0 0 0 15px;"> leve</label> <label><input type="radio" name="fundo" value="fundo_pesada"<?php if ($pedido["fundo"] == "fundo_pesada") { echo " CHECKED"; } ?> class="input" style="margin:0 0 0 15px;"> pesado</label><br>
					Válvula: <label><input type="radio" name="valvula_d" value="valv_d_leve"<?php if ($pedido["valvula_d"] == "valv_d_leve") { echo " CHECKED"; } ?> class="input" style="margin:0 0 0 15px;"> leve</label> <label><input type="radio" name="valvula_d" value="valv_d_pesada"<?php if ($pedido["valvula_d"] == "valv_d_pesada") { echo " CHECKED"; } ?> class="input" style="margin:0 0 0 15px;"> pesada</label>				
				</div>
				</div>
*/
?>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-12 col-sm-12 col-xs-12" style="padding-left:0;">
					<label class="control-label" for="tipo_cost_esvaz">Tipo de costura</label>
					<select id="tipo_cost_esvaz" name="tipo_cost_esvaz" class="form-control">
						<option value="simples"<?php if ($costura["esvaz"] == "simples") { echo " selected"; } ?>>Costura simples</option>
						<option value="simples1ved"<?php if ($costura["esvaz"] == "simples1ved") { echo " selected"; } ?>>Costura simples + 1 vedante</option>
						<option value="simples2ved"<?php if ($costura["esvaz"] == "simples2ved") { echo " selected"; } ?>>Costura simples + 2 vedante</option>
						<option value="dupla"<?php if ($costura["esvaz"] == "dupla") { echo " selected"; } ?>>Costura dupla</option>
					</select>
					</div>
				</div>
			</div>
		</div>
	</div>

		<div class="x_panel">
			<div class="x_title">
				<h2>Alças</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
				<label class="control-label" for="alca">Quantidade</label>
				<select id="sel_alca" name="alca" class="form-control">
					<option value="vazio">Sem alças</option>
					<?php
					$qtde_alcas = mysqli_query($conn,"SELECT * FROM qtde_alcas");
					while($row = mysqli_fetch_array($qtde_alcas)) {
					  echo "    <option value=\"" . $row['qtde'] . "_" . $row['cod'] . "\"";
					  if ($pedido["alca"] == $row['qtde'] . "_" . $row['cod']) { echo " selected"; }
					  echo ">" . $row['desc'] . "</option>\n";
					}
					?>
				</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 col-sm-3 col-xs-6">
				<label class="control-label" for="alca_material">Material</label>
				<select name="alca_material" class="form-control">
					<option value="fita"<?php if ($pedido["alca_material"] == "fita") { echo " selected"; } ?>>Fita</option>
					<option value="tecido"<?php if ($pedido["alca_material"] == "tecido") { echo " selected"; } ?>>Tecido</option>
				</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6">
				<label class="control-label" for="alca_cor">Cor</label>
				<select name="alca_cor" class="form-control">
					<option value="branco"<?php if ($pedido["alca_cor"] == "branco") { echo " selected"; } ?>>Branco (natural)</option>
					<option value="amarela"<?php if ($pedido["alca_cor"] == "amarela") { echo " selected"; } ?>>Amarela</option>
					<option value="azul_total"<?php if ($pedido["alca_cor"] == "azul_total") { echo " selected"; } ?>>Azul</option>
					<option value="cinza"<?php if ($pedido["alca_cor"] == "cinza") { echo " selected"; } ?>>Cinza</option>
					<option value="marrom_total"<?php if ($pedido["alca_cor"] == "marrom_total") { echo " selected"; } ?>>Marrom</option>
					<option value="preta_total"<?php if ($pedido["alca_cor"] == "preta_total") { echo " selected"; } ?>>Preta</option>
					<option value="verde_total"<?php if ($pedido["alca_cor"] == "verde_total") { echo " selected"; } ?>>Verde</option>
					<option value="vermelha"<?php if ($pedido["alca_cor"] == "vermelha") { echo " selected"; } ?>>Vermelha</option>
					<option value="amarelo_carijo"<?php if ($pedido["alca_cor"] == "amarelo_carijo") { echo " selected"; } ?>>Amarelo Carijó</option>
					<option value="azul"<?php if ($pedido["alca_cor"] == "azul") { echo " selected"; } ?>>Azul Carijó</option>
					<option value="cinza_carijo"<?php if ($pedido["alca_cor"] == "cinza_carijo") { echo " selected"; } ?>>Cinza Carijó</option>
					<option value="marrom"<?php if ($pedido["alca_cor"] == "marrom") { echo " selected"; } ?>>Marrom Carijó</option>
					<option value="preto"<?php if ($pedido["alca_cor"] == "preto") { echo " selected"; } ?>>Preto Carijó</option>
					<option value="verde"<?php if ($pedido["alca_cor"] == "verde") { echo " selected"; } ?>>Verde Carijó</option>
					<option value="vermelho_carijo"<?php if ($pedido["alca_cor"] == "vermelho_carijo") { echo " selected"; } ?>>Vermelho Carijó</option>
					<option value="outra"<?php if ($pedido["alca_cor"] == "outra") { echo " selected"; } ?>>Outra</option>
				</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
					<label class="control-label reticencias" for="alca_altura">Altura do vão livre</label>
					<input type="text" id="alca_altura" name="alca_altura" class="form-control" value="<?php echo $pedido["alca_altura"]; ?>">
					<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
					<label class="control-label reticencias" for="alca_fix_altura">Altura de fixação</label>
					<input type="text" id="alca_fix_altura" name="alca_fix_altura" class="form-control" value="<?php echo $pedido["alca_fix_altura"]; ?>">
					<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-2 col-xs-6">
					<label style="margin-top:20px;"><input type="checkbox" id="reforco_vao_livre" name="reforco_vao_livre"<?php if ($pedido["reforco_vao_livre"] == "1") { echo " CHECKED"; } ?>> Reforço do vão livre</label>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-6">
					<label style="margin-top:20px;"><input type="checkbox" id="reforco_fixacao" name="reforco_fixacao"<?php if ($pedido["reforco_fixacao"] == "1") { echo " CHECKED"; } ?>> Reforço de fixação</label>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-6">
					<label style="margin-top:20px;"><input type="checkbox" id="alca_dupla" name="alca_dupla"<?php if ($pedido["alca_dupla"] == "1") { echo " CHECKED"; } ?>> Alça dupla</label>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12 has-feedback">
					<label class="control-label reticencias" for="alca_capac">Capacidade individual de cada alça</label>
					<input type="text" id="alca_capac" name="alca_capac" class="form-control" value="<?php echo $pedido["alca_capac"]; ?>">
					<span class="form-control-feedback right" style="width:30px;"> kg</span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label class="control-label" for="tipo_cost_alca">Tipo de costura</label>
					<select id="tipo_cost_alca" name="tipo_cost_alca" class="form-control">
						<option value="ponto_fixo"<?php if ($costura["alca"] == "ponto_fixo") { echo " selected"; } ?>>Ponto fixo</option>
						<option value="dupla"<?php if ($costura["alca"] == "dupla") { echo " selected"; } ?>>Costura dupla</option>
						<option value="dupla2ved"<?php if ($costura["alca"] == "dupla2ved") { echo " selected"; } ?>>Costura dupla + 2 vedante</option>
						<option value="overlock"<?php if ($costura["alca"] == "overlock") { echo " selected"; } ?>>Overlock + corrente</option>
						<option value="overlock2ved"<?php if ($costura["alca"] == "overlock2ved") { echo " selected"; } ?>>Overlock + corrente + 2 vedante</option>
					</select>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label class="control-label" for="alca_fixacao">Tipo de fixação da alça</label>
					<select id="alca_fixacao" name="alca_fixacao" class="form-control">
						<option value="0"<?php if ($pedido["alca_fixacao"] == "0") { echo " SELECTED"; } ?>>Interna</option>
						<option value="1"<?php if ($pedido["alca_fixacao"] == "1") { echo " SELECTED"; } ?>>Externa</option>
					</select>
				</div>
			</div>
		</div>
	</div>

		<div class="x_panel">
			<div class="x_title">
				<h2>Liner</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
				<label class="control-label" for="liner">Modelo</label>
				<select id="sel_liner" name="liner" class="form-control">
					<option value="vazio"<?php if ($pedido["liner"] == "vazio") { echo " selected"; } ?>>Sem liner</option>
					<option value="liner_padrao"<?php if ($pedido["liner"] == "liner_padrao") { echo " selected"; } ?>>Liner padrão</option>
					<option value="liner_gota"<?php if ($pedido["liner"] == "liner_gota") { echo " selected"; } ?>>Liner padrão Bag Gota</option>
					<option value="liner_fertil"<?php if ($pedido["liner"] == "liner_fertil") { echo " selected"; } ?>>Liner padrão Fertilizante</option>
					<option value="liner_afunilado"<?php if ($pedido["liner"] == "liner_afunilado") { echo " selected"; } ?>>Liner afunilado</option>
					<option value="liner_sup_inf"<?php if ($pedido["liner"] == "liner_sup_inf") { echo " selected"; } ?>>Liner com válvula superior e inferior</option>
					<option value="liner_total_inf"<?php if ($pedido["liner"] == "liner_total_inf") { echo " selected"; } ?>>Liner com abertura total e válvula inferior</option>
					<option value="liner_sup_fechado"<?php if ($pedido["liner"] == "liner_sup_fechado") { echo " selected"; } ?>>Liner com válvula superior e fechado no fundo</option>
					<option value="liner_externo"<?php if ($pedido["liner"] == "liner_externo") { echo " selected"; } ?>>Liner externo</option>
				</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 col-sm-3 col-xs-3">
				<label class="control-label" for="tipo_liner">Tipo</label>
				<select name="tipo_liner" class="form-control"<?php if ($pedido["liner"] != "vazio") { echo " required"; } ?>>
					<option value="vazio"<?php if ($pedido["tipo_liner"] == "vazio") { echo " selected"; } ?>>Selecione</option>
					<option value="liner_transp"<?php if ($pedido["tipo_liner"] == "liner_transp") { echo " selected"; } ?>>Virgem</option>
					<option value="liner_canela"<?php if ($pedido["tipo_liner"] == "liner_canela") { echo " selected"; } ?>>Canela</option>
					<option value="liner_cristal"<?php if ($pedido["tipo_liner"] == "liner_cristal") { echo " selected"; } ?>>Cristal</option>
				</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-3 has-feedback">
				<label class="control-label reticencias" for="liner_espessura">Espessura</label>
				<input type="text" id="liner_espessura" name="liner_espessura" class="form-control" value="<?php echo $pedido["liner_espessura"]; ?>"<?php if ($pedido["liner"] != "vazio") { echo " required"; } ?>>
				<span class="form-control-feedback right" style="width:30px;"> µm</span>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-3">
				<label class="control-label" for="fix_liner">Tipo de fixação</label>
				<select id="sel_fix_liner" name="fix_liner" class="form-control"<?php if ($pedido["liner"] != "vazio") { echo " required"; } ?>>
					<option value="sem_fixacao"<?php if ($pedido["fix_liner"] == "sem_fixacao") { echo " selected"; } ?>>Sem fixação</option>
					<option value="colado"<?php if ($pedido["fix_liner"] == "colado") { echo " selected"; } ?>>Colado</option>
					<option value="costurado"<?php if ($pedido["fix_liner"] == "costurado") { echo " selected"; } ?>>Costurado</option>
					<option value="colado_costurado"<?php if ($pedido["fix_liner"] == "colado_costurado") { echo " selected"; } ?>>Colado e costurado</option>
					<option value="liner_externo"<?php if ($pedido["fix_liner"] == "liner_externo") { echo " selected"; } ?>>Liner externo</option>
				</select>
				</div>
<?php /*
				<div class="col-md-3 col-sm-3 col-xs-3">
				<label class="control-label" for="corte_liner">Corte</label>
				<select id="sel_corte_liner" name="corte_liner" class="form-control"<?php if ($pedido["liner"] != "vazio") { echo " required"; } ?>>
					<option value=""<?php if ($pedido["corte_liner"] == "") { echo " selected"; } ?>>Selecione</option>
					<option value="com_solda"<?php if ($pedido["corte_liner"] == "com_solda") { echo " selected"; } ?>>COM SOLDA</option>
					<option value="sem_solda"<?php if ($pedido["corte_liner"] == "sem_solda") { echo " selected"; } ?>>SEM SOLDA</option>
				</select>
				</div>
*/ ?>
			</div>
		</div>
	</div>

		<div class="x_panel">
			<div class="x_title">
				<h2>Impressão</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
				<label class="control-label" for="no_cores">N° cores</label>
				<select name="no_cores" class="form-control">
					<option value="0"<?php if ($pedido["no_cores"] == "0") { echo " selected"; } ?>>Sem impressão</option>
					<option value="1"<?php if ($pedido["no_cores"] == "1") { echo " selected"; } ?>>1 cor</option>
					<option value="2"<?php if ($pedido["no_cores"] == "2") { echo " selected"; } ?>>2 cores</option>
					<option value="3"<?php if ($pedido["no_cores"] == "3") { echo " selected"; } ?>>3 cores (limite ideal)</option>
					<option value="4"<?php if ($pedido["no_cores"] == "4") { echo " selected"; } ?>>4 cores (consultar)</option>
				</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12">
				<label style="margin-top: 15px;"><input type="checkbox" name="imp_controle_viag"<?php if ($pedido["imp_controle_viag"] == "1") { echo " CHECKED"; } ?>> Controle de utilização</label><br>
				<label><input type="checkbox" name="imp_num_seq"<?php if ($pedido["imp_num_seq"] == "1") { echo " CHECKED"; } ?>> Número sequencial</label>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
				<label class="control-label reticencias" for="sel_faces">Faces selecionadas</label>
				<input type="text" id="sel_faces" name="sel_faces" style="text-transform: uppercase;" class="form-control" value="<?php echo $pedido["sel_faces"]; ?>">
				</div>
			</div>
		</div>
	</div>

		<div class="x_panel">
			<div class="x_title">
				<h2>Porta etiqueta</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-2 col-sm-2 col-xs-2" style="text-align:center;">
					<label><input type="checkbox" id="porta_etq1" name="porta_etq1" style="margin-top: 25px;height: 22px;width: 15px;"<?php if ($pedido["porta_etq1"] == "1") { echo " CHECKED"; } ?>> <big><big><big>A</big></big></big></label>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="pos_porta_etq1">Posição</label>
					<select id="pos_porta_etq1" name="pos_porta_etq1" class="form-control">
						<option value=""<?php if ($pedido["pos_porta_etq1"] == "") { echo " selected"; } ?>>Não selecionado</option>
						<option value="topo_meio"<?php if ($pedido["pos_porta_etq1"] == "topo_meio") { echo " selected"; } ?>>Topo centralizado (padrão)</option>
						<option value="topo_direita"<?php if ($pedido["pos_porta_etq1"] == "topo_direita") { echo " selected"; } ?>>Topo na direita</option>
						<option value="topo_esquerda"<?php if ($pedido["pos_porta_etq1"] == "topo_esquerda") { echo " selected"; } ?>>Topo na esquerda (45 cm do topo e 8 cm da lateral)</option>
						<option value="centro"<?php if ($pedido["pos_porta_etq1"] == "centro") { echo " selected"; } ?>>No centro</option>
						<option value="cost_vert"<?php if ($pedido["pos_porta_etq1"] == "cost_vert") { echo " selected"; } ?>>Costurado na vertical</option>
						<option value="personalizado"<?php if ($pedido["pos_porta_etq1"] == "personalizado") { echo " selected"; } ?>>Personalizado (especificar nas obs.)</option>
					</select>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="mod_porta_etq1">Modelo</label>
					<select id="mod_porta_etq1" name="mod_porta_etq1" class="form-control">
						<option value=""<?php if ($pedido["mod_porta_etq1"] == "") { echo " selected"; } ?>>Selecione</option>
						<option value="folha"<?php if ($pedido["mod_porta_etq1"] == "folha") { echo " selected"; } ?>>Folha porta etiqueta (27 x 40 x 0,20)</option>
						<option value="folha2"<?php if ($pedido["mod_porta_etq1"] == "folha2") { echo " selected"; } ?>>Folha porta etiqueta (27 x 20 x 0,20)</option>
						<option value="fronha"<?php if ($pedido["mod_porta_etq1"] == "fronha") { echo " selected"; } ?>>Porta etiqueta tipo fronha (27 x 40 x 0,15)</option>
						<option value="aba_adesiva"<?php if ($pedido["mod_porta_etq1"] == "aba_adesiva") { echo " selected"; } ?>>Porta etiqueta (27 x 45 x 0,12) - com aba adesiva</option>
						<option value="ziplock"<?php if ($pedido["mod_porta_etq1"] == "ziplock") { echo " selected"; } ?>>Porta documento (20 x 25) - ziplock</option>
						<option value="aberto_tras"<?php if ($pedido["mod_porta_etq1"] == "aberto_tras") { echo " selected"; } ?>>Com abertura posicionada para a parte de trás (23x40cm)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-2 col-xs-2" style="text-align:center;">
					<label><input type="checkbox" id="porta_etq2" name="porta_etq2" style="margin-top: 25px;height: 22px;width: 15px;"<?php if ($pedido["porta_etq2"] == "1") { echo " CHECKED"; } ?>> <big><big><big>B</big></big></big></label>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="pos_porta_etq2">Posição</label>
					<select id="pos_porta_etq2" name="pos_porta_etq2" class="form-control">
						<option value=""<?php if ($pedido["pos_porta_etq2"] == "") { echo " selected"; } ?>>Não selecionado</option>
						<option value="topo_meio"<?php if ($pedido["pos_porta_etq2"] == "topo_meio") { echo " selected"; } ?>>Topo centralizado (padrão)</option>
						<option value="topo_direita"<?php if ($pedido["pos_porta_etq2"] == "topo_direita") { echo " selected"; } ?>>Topo na direita</option>
						<option value="topo_esquerda"<?php if ($pedido["pos_porta_etq2"] == "topo_esquerda") { echo " selected"; } ?>>Topo na esquerda (45 cm do topo e 8 cm da lateral)</option>
						<option value="centro"<?php if ($pedido["pos_porta_etq2"] == "centro") { echo " selected"; } ?>>No centro</option>
						<option value="cost_vert"<?php if ($pedido["pos_porta_etq2"] == "cost_vert") { echo " selected"; } ?>>Costurado na vertical</option>
						<option value="personalizado"<?php if ($pedido["pos_porta_etq2"] == "personalizado") { echo " selected"; } ?>>Personalizado (especificar nas obs.)</option>
					</select>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="mod_porta_etq2">Modelo</label>
					<select id="mod_porta_etq2" name="mod_porta_etq2" class="form-control">
						<option value=""<?php if ($pedido["mod_porta_etq2"] == "") { echo " selected"; } ?>>Selecione</option>
						<option value="folha"<?php if ($pedido["mod_porta_etq2"] == "folha") { echo " selected"; } ?>>Folha porta etiqueta (27 x 40 x 0,20)</option>
						<option value="folha2"<?php if ($pedido["mod_porta_etq2"] == "folha2") { echo " selected"; } ?>>Folha porta etiqueta (27 x 20 x 0,20)</option>
						<option value="fronha"<?php if ($pedido["mod_porta_etq2"] == "fronha") { echo " selected"; } ?>>Porta etiqueta tipo fronha (27 x 40 x 0,15)</option>
						<option value="aba_adesiva"<?php if ($pedido["mod_porta_etq2"] == "aba_adesiva") { echo " selected"; } ?>>Porta etiqueta (27 x 45 x 0,12) - com aba adesiva</option>
						<option value="ziplock"<?php if ($pedido["mod_porta_etq2"] == "ziplock") { echo " selected"; } ?>>Porta documento (20 x 25) - ziplock</option>
						<option value="aberto_tras"<?php if ($pedido["mod_porta_etq2"] == "aberto_tras") { echo " selected"; } ?>>Com abertura posicionada para a parte de trás (23x40cm)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-2 col-xs-2" style="text-align:center;">
					<label><input type="checkbox" id="porta_etq3" name="porta_etq3" style="margin-top: 25px;height: 22px;width: 15px;"<?php if ($pedido["porta_etq3"] == "1") { echo " CHECKED"; } ?>> <big><big><big>C</big></big></big></label>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="pos_porta_etq3">Posição</label>
					<select id="pos_porta_etq3" name="pos_porta_etq3" class="form-control">
						<option value=""<?php if ($pedido["pos_porta_etq3"] == "") { echo " selected"; } ?>>Não selecionado</option>
						<option value="topo_meio"<?php if ($pedido["pos_porta_etq3"] == "topo_meio") { echo " selected"; } ?>>Topo centralizado (padrão)</option>
						<option value="topo_direita"<?php if ($pedido["pos_porta_etq3"] == "topo_direita") { echo " selected"; } ?>>Topo na direita</option>
						<option value="topo_esquerda"<?php if ($pedido["pos_porta_etq3"] == "topo_esquerda") { echo " selected"; } ?>>Topo na esquerda (45 cm do topo e 8 cm da lateral)</option>
						<option value="centro"<?php if ($pedido["pos_porta_etq3"] == "centro") { echo " selected"; } ?>>No centro</option>
						<option value="cost_vert"<?php if ($pedido["pos_porta_etq3"] == "cost_vert") { echo " selected"; } ?>>Costurado na vertical</option>
						<option value="personalizado"<?php if ($pedido["pos_porta_etq3"] == "personalizado") { echo " selected"; } ?>>Personalizado (especificar nas obs.)</option>
					</select>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="mod_porta_etq3">Modelo</label>
					<select id="mod_porta_etq3" name="mod_porta_etq3" class="form-control">
						<option value=""<?php if ($pedido["mod_porta_etq3"] == "") { echo " selected"; } ?>>Selecione</option>
						<option value="folha"<?php if ($pedido["mod_porta_etq3"] == "folha") { echo " selected"; } ?>>Folha porta etiqueta (27 x 40 x 0,20)</option>
						<option value="folha2"<?php if ($pedido["mod_porta_etq3"] == "folha2") { echo " selected"; } ?>>Folha porta etiqueta (27 x 20 x 0,20)</option>
						<option value="fronha"<?php if ($pedido["mod_porta_etq3"] == "fronha") { echo " selected"; } ?>>Porta etiqueta tipo fronha (27 x 40 x 0,15)</option>
						<option value="aba_adesiva"<?php if ($pedido["mod_porta_etq3"] == "aba_adesiva") { echo " selected"; } ?>>Porta etiqueta (27 x 45 x 0,12) - com aba adesiva</option>
						<option value="ziplock"<?php if ($pedido["mod_porta_etq3"] == "ziplock") { echo " selected"; } ?>>Porta documento (20 x 25) - ziplock</option>
						<option value="aberto_tras"<?php if ($pedido["mod_porta_etq3"] == "aberto_tras") { echo " selected"; } ?>>Com abertura posicionada para a parte de trás (23x40cm)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-2 col-xs-2" style="text-align:center;">
					<label><input type="checkbox" id="porta_etq4" name="porta_etq4" style="margin-top: 25px;height: 22px;width: 15px;"<?php if ($pedido["porta_etq4"] == "1") { echo " CHECKED"; } ?>> <big><big><big>D</big></big></big></label>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="pos_porta_etq4">Posição</label>
					<select id="pos_porta_etq4" name="pos_porta_etq4" class="form-control">
						<option value=""<?php if ($pedido["pos_porta_etq4"] == "") { echo " selected"; } ?>>Não selecionado</option>
						<option value="topo_meio"<?php if ($pedido["pos_porta_etq4"] == "topo_meio") { echo " selected"; } ?>>Topo centralizado (padrão)</option>
						<option value="topo_direita"<?php if ($pedido["pos_porta_etq4"] == "topo_direita") { echo " selected"; } ?>>Topo na direita</option>
						<option value="topo_esquerda"<?php if ($pedido["pos_porta_etq4"] == "topo_esquerda") { echo " selected"; } ?>>Topo na esquerda (45 cm do topo e 8 cm da lateral)</option>
						<option value="centro"<?php if ($pedido["pos_porta_etq4"] == "centro") { echo " selected"; } ?>>No centro</option>
						<option value="cost_vert"<?php if ($pedido["pos_porta_etq4"] == "cost_vert") { echo " selected"; } ?>>Costurado na vertical</option>
						<option value="personalizado"<?php if ($pedido["pos_porta_etq4"] == "personalizado") { echo " selected"; } ?>>Personalizado (especificar nas obs.)</option>
					</select>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="mod_porta_etq4">Modelo</label>
					<select id="mod_porta_etq4" name="mod_porta_etq4" class="form-control">
						<option value=""<?php if ($pedido["mod_porta_etq4"] == "") { echo " selected"; } ?>>Selecione</option>
						<option value="folha"<?php if ($pedido["mod_porta_etq4"] == "folha") { echo " selected"; } ?>>Folha porta etiqueta (27 x 40 x 0,20)</option>
						<option value="folha2"<?php if ($pedido["mod_porta_etq4"] == "folha2") { echo " selected"; } ?>>Folha porta etiqueta (27 x 20 x 0,20)</option>
						<option value="fronha"<?php if ($pedido["mod_porta_etq4"] == "fronha") { echo " selected"; } ?>>Porta etiqueta tipo fronha (27 x 40 x 0,15)</option>
						<option value="aba_adesiva"<?php if ($pedido["mod_porta_etq4"] == "aba_adesiva") { echo " selected"; } ?>>Porta etiqueta (27 x 45 x 0,12) - com aba adesiva</option>
						<option value="ziplock"<?php if ($pedido["mod_porta_etq4"] == "ziplock") { echo " selected"; } ?>>Porta documento (20 x 25) - ziplock</option>
						<option value="aberto_tras"<?php if ($pedido["mod_porta_etq4"] == "aberto_tras") { echo " selected"; } ?>>Com abertura posicionada para a parte de trás (23x40cm)</option>
					</select>
				</div>
			</div>
		</div>
	</div>

		<div class="x_panel">
			<div class="x_title">
				<h2>Embalagem</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-4 col-sm-4 col-xs-4">
					<label class="control-label" for="fardo">Fardo de</label>
					<select name="fardo" class="form-control">
						<option value="vazio"<?php if ($pedido["fardo"] == "vazio") { echo " selected"; } ?>>Não selecionado</option>
						<option value="5"<?php if ($pedido["fardo"] == "5") { echo " selected"; } ?>>5 peças</option>
						<option value="10"<?php if ($pedido["fardo"] == "10") { echo " selected"; } ?>>10 peças</option>
						<option value="15"<?php if ($pedido["fardo"] == "15") { echo " selected"; } ?>>15 peças</option>
						<option value="20"<?php if ($pedido["fardo"] == "20") { echo " selected"; } ?>>20 peças</option>
						<option value="25"<?php if ($pedido["fardo"] == "25") { echo " selected"; } ?>>25 peças</option>
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-4">
					<label style="margin-top: 30px;"><input type="checkbox" id="fardo_pallet" name="fardo_pallet"<?php if ($pedido["fardo_pallet"] == "1") { echo " CHECKED"; } ?>> Fardo palletizado</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-4" id="tam_fardo_pallet">
					<label class="control-label" for="pos_porta_etq1">Big Bag palletizado</label>
					<select name="palletizado" class="form-control">
						<option value="vazio"<?php if ($pedido["palletizado"] == "vazio") { echo " selected"; } ?>>Selecione</option>
						<option value="80"<?php if ($pedido["palletizado"] == "80") { echo " selected"; } ?>>80</option>
						<option value="100"<?php if ($pedido["palletizado"] == "100") { echo " selected"; } ?>>100</option>
						<option value="125"<?php if ($pedido["palletizado"] == "125") { echo " selected"; } ?>>125</option>
						<option value="130"<?php if ($pedido["palletizado"] == "130") { echo " selected"; } ?>>130</option>
						<option value="150"<?php if ($pedido["palletizado"] == "150") { echo " selected"; } ?>>150</option>
						<option value="175"<?php if ($pedido["palletizado"] == "175") { echo " selected"; } ?>>175</option>
						<option value="200"<?php if ($pedido["palletizado"] == "200") { echo " selected"; } ?>>200</option>
						<option value="250"<?php if ($pedido["palletizado"] == "250") { echo " selected"; } ?>>250</option>
					</select>
				</div>
			</div>
		</div>
	</div>

		<div class="x_panel">
			<div class="x_title">
				<h2>Acessórios</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" name="fio_ved_travas"<?php if ($pedido["fio_ved_travas"] == "1") { echo " CHECKED"; } ?>> Fio vedante nas travas</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" name="cinta_trav"<?php if ($pedido["cinta_trav"] == "1") { echo " CHECKED"; } ?>> Cinta de travamento</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" name="gravata_trav"<?php if ($pedido["gravata"] == "1") { echo " CHECKED"; } ?>> Gravata de travamento</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" onclick="acessorio('sapata');" id="sel_sapata" name="sapata"<?php if ($pedido["sapata"] == "1") { echo " CHECKED"; } ?>> Pack Less</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" onclick="acessorio('velcro');" id="sel_velcro" name="velcro"<?php if ($pedido["velcro"] == "1") { echo " CHECKED"; } ?>> Velcro (Macho/Femea)</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" onclick="acessorio('flap');" id="sel_flap" name="flap"<?php if ($pedido["flap"] == "1") { echo " CHECKED"; } ?>> Tampa para fundo falso</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" name="unitizador"<?php if ($pedido["unit"] != "0") { echo " CHECKED"; } ?>> Unitizador</label><div id="input_unit"><small>Altura sem recorte:</small><input type="text" id="unit" name="unit" style="width: 50px;padding: 0 5px;margin: 0 5px;" value="<?php echo $pedido["unit"]; ?>"></div>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" id="trava_rede" name="trava_rede"<?php if ($pedido["trava_rede"] != "0") { echo " CHECKED"; } ?>> Travas em rede</label>
				</div>
			</div>
		</div>
	</div>

		<div class="x_panel">
			<div class="x_title">
				<h2>Observações</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<label class="control-label" for="obs_cliente">Técnicas</label>
					<textarea name="obs_cliente" rows="3" class="form-control" style="line-height: inherit; resize: vertical;"><?php echo $pedido["obs_cliente"]; ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<label class="control-label" for="obs_comerciais">Comerciais</label>
					<textarea name="obs_comerciais" rows="3" class="form-control" style="line-height: inherit; resize: vertical;"><?php echo $pedido["obs_comerciais"]; ?></textarea>
				</div>
			</div>
		</div>
	</div>

</div>




<center>
Gerar revisão? 
<label><input type="radio" name="gera_revisao" value="sim" class="input" style="margin:0 0 0 15px;"> SIM</label>
<img src="images/alpha_dot.png" width="5" height="1" border="0">
<label><input type="radio" name="gera_revisao" value="nao" CHECKED class="input" style="margin:0 0 0 15px;"> NÃO</label>
<img src="images/alpha_dot.png" width="30" height="1" border="0">

<input type="submit" value="ALTERAR ORÇAMENTO" class="btn btn-success">
<input type="reset" value="RESTAURAR" class="btn btn-danger">
<a class="btn btn-dark" href="pedidos.php">VOLTAR</a>
</center>


<?php
/* OCULTAR */
?>
</form>
<br><br>
<script type="text/javascript">
$(':radio').mousedown(function(e){
  var $self = $(this);
  if( $self.is(':checked') ){
    var uncheck = function(){
      setTimeout(function(){$self.removeAttr('checked');},0);
    };
    var unbind = function(){
      $self.unbind('mouseup',up);
    };
    var up = function(){
      uncheck();
      unbind();
    };
    $self.bind('mouseup',up);
    $self.one('mouseout', unbind);
  }
});
</script>

<?php

} elseif($_GET["acao"] == "analisar") {

if ($_SESSION['user']['nivel'] != '1' and $_SESSION['user']['nivel'] != '2') {
	die("<center><br><br><br><h2>Alerta!</h2><br>Você não tem permissão para visualizar esse conteúdo.</center>");
}

if(!empty($_POST)) {
/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
die();
*/
$query_ped_desc = mysqli_query($conn,"SELECT * FROM `pedidos_desc` WHERE `pedido` LIKE '".ltrim($_GET["pedido"], '0')."' ORDER BY `id` DESC");

if(mysqli_num_rows($query_ped_desc)) {
	$pedidos_desc_query = "UPDATE `pedidos_desc` SET `desc_mat` = '".$desc_mat."', `desc_cif` = '".$desc_cif."', `desc_mo` = '".$desc_mo."' WHERE `pedidos_desc`.`pedido` = '".$pedido."';";
	$pedidos_desc_exec = mysqli_query( $conn, $pedidos_desc_query );
	if(! $pedidos_desc_exec ) { die('Não foi possível atualizar descrições do pedido: ' . mysqli_error()); }
} else {
	if($_POST["desc_mat"] !== "Materiais e insumos diretos / indiretos / auxiliares" || $_POST["desc_cif"] !== "CIF (Custos Indiretos de Fabricação)" || $_POST["desc_mo"] !== "Mão de obra direta") {
		$id_ped = $_POST["id_pedido"];
		$pedido = $_GET["pedido"];
		$desc_mat = $_POST["desc_mat"];
		$desc_cif = $_POST["desc_cif"];
		$desc_mo = $_POST["desc_mo"];
		$pedidos_desc_query = "INSERT INTO `pedidos_desc` (`id`, `id_ped`, `pedido`, `desc_mat`, `desc_cif`, `desc_mo`) VALUES (NULL, '".$id_ped."', '".$pedido."', '".$desc_mat."', '".$desc_cif."', '".$desc_mo."');";
		$pedidos_desc_exec = mysqli_query( $conn, $pedidos_desc_query );
		if(! $pedidos_desc_exec ) { die('Não foi possível gravar descrições do pedido: ' . mysqli_error()); }
	}
}

//die();

$pedido = $_GET["pedido"];

$pedido_original = $pedido;

$no_pedido = (float)$pedido;
$gera_revisao = $_POST["gera_revisao"];

if ($gera_revisao == "nao") {
	$revisao_pedido = $_POST["revisao_pedido"];
	$rev_valor = $_POST["rev_valor"]+1;
} elseif ($gera_revisao == "sim") {
	$revisao_pedido = $_POST["revisao_pedido"] + 1;
	$rev_valor = 0;

/* Especificações do Liner */

//echo "SELECT * FROM `pedidos_liner` WHERE `pedido` = '".$pedido."' AND `revisao` LIKE '".$_POST["revisao_pedido"]."' ORDER BY `id` DESC";
	$query_liner = mysqli_query($conn,"SELECT * FROM `pedidos_liner` WHERE `pedido` = '".$pedido."' AND `revisao` LIKE '".$_POST["revisao_pedido"]."' ORDER BY `id` DESC"); // ORDER BY `revisao` DESC");
	$det_liner = mysqli_fetch_array($query_liner);

	if ($det_liner["comp_liner"] != "") {
		$largura_liner = $det_liner["larg_liner"];
		$comprimento_liner = $det_liner["comp_liner"];
		$espessura_liner = $det_liner["espess_liner"];
		$sql_liner = "INSERT INTO `pedidos_liner` (`id`, `pedido`, `revisao`, `larg_liner`, `comp_liner`, `espess_liner`) VALUES (NULL,'".$pedido."','".$revisao_pedido."','".$largura_liner."','".$comprimento_liner."','".$espessura_liner."')";
		$detalhes_liner = mysqli_query( $conn, $sql_liner );
		if(! $detalhes_liner ) { die('Não foi possível gravar detalhes do liner: ' . mysqli_error()); }
	}

//echo "<br><br>PEDIDO: ".$pedido." - REVISAO: ".$revisao_pedido." - LARGURA: ".$largura_liner." - COMPRIMENTO: ".$comprimento_liner." - ESPESSURA: ".$espessura_liner."<br><br><br>";

//die();
/* Especificações do Liner */

}

$valor_final_pedido = number_format(str_replace(",",".",$_POST["valor_final_pedido"]),2);


$subtotal = number_format(str_replace(",",".",$_POST["subtotal"]),2);

$class_prod = $_POST["class_prod"];
$valor_mat_auxiliar = number_format(str_replace(",",".",$_POST["valor_mat_auxiliar"]),2);
$valor_cif = number_format(str_replace(",",".",$_POST["valor_cif"]),2);
$valor_mao_obra = number_format(str_replace(",",".",$_POST["valor_mao_obra"]),2);
$valor_custo_bag = number_format(str_replace(",",".",$_POST["valor_custo_bag"]),2);
$imposto_icms = number_format(str_replace(",",".",$_POST["imposto_icms"]),2);
$imposto_ipi = number_format(str_replace(",",".",$_POST["imposto_ipi"]),2);
$imposto_pis = number_format(str_replace(",",".",$_POST["imposto_pis"]),2);
$imposto_cofins = number_format(str_replace(",",".",$_POST["imposto_cofins"]),2);
$imposto_ir = number_format(str_replace(",",".",$_POST["imposto_ir"]),2);
$imposto_csll = number_format(str_replace(",",".",$_POST["imposto_csll"]),2);
$imposto_inss = number_format(str_replace(",",".",$_POST["imposto_inss"]),2);
$imposto_perda = number_format(str_replace(",",".",$_POST["imposto_perda"]),2);
$imposto_frete = number_format(str_replace(",",".",$_POST["imposto_frete"]),2);
$imposto_comissao = number_format(str_replace(",",".",$_POST["imposto_comissao"]),2);
$imposto_adm_comercial = number_format(str_replace(",",".",$_POST["imposto_adm_comercial"]),2);
$imposto_custo_fin = number_format(str_replace(",",".",$_POST["imposto_custo_fin"]),2);
$imposto_margem = number_format(str_replace(",",".",$_POST["imposto_margem"]),2);
$imposto_total = number_format(str_replace(",",".",$_POST["imposto_total"]),2);

$valor_final_dolar = number_format(str_replace(",",".",$_POST["valor_final_dolar"]),2);
$cambio_dolar = number_format(str_replace(",",".",$_POST["cambio_dolar"]),4);
$cambio_data = $_POST["cambio_dia"];
//$cambio_data = date("Y-m-d H:i:s");

$obs_comerciais = $_POST["obs_comerciais"];
$obs_cliente = $_POST["obs_cliente"];

$qtde = $_POST["qtde"];
$referencia = $_POST["referencia"];
$frete = $_POST["frete"];
$embarques = $_POST["embarques"];

$representante = $_POST["representante"];

$nome_cliente = $_POST["nome_cliente"];
$cnpj_cpf = $_POST["cnpj_cpf"];


/*
echo $no_pedido."<br>";
echo $revisao_pedido."<br>";
echo $valor_final_pedido."<br>";
*/


$sql_extra = "INSERT INTO `pedidos_extra` (`id`, `pedido`, `revisao`, `class_prod`, `mat_auxiliar`, `cif`, `mao_obra`, `custo_bag`, `icms`, `ipi`, `pis`, `cofins`, `ir`, `csll`, `inss`, `perda`, `frete`, `comissao`, `adm_comercial`, `custo_fin`, `margem`, `imposto_total`, `valor_dolar`, `cambio`, `cambio_data`, `rev_valor`)  VALUES (NULL, '$no_pedido', '$revisao_pedido', '$class_prod', '$valor_mat_auxiliar', '$valor_cif', '$valor_mao_obra', '$valor_custo_bag', '$imposto_icms', '$imposto_ipi', '$imposto_pis', '$imposto_cofins', '$imposto_ir', '$imposto_csll', '$imposto_inss', '$imposto_perda', '$imposto_frete', '$imposto_comissao', '$imposto_adm_comercial', '$imposto_custo_fin', '$imposto_margem', '$imposto_total', '$valor_final_dolar', '$cambio_dolar', '$cambio_data', '$rev_valor');";
$pedido_extra = mysqli_query( $conn, $sql_extra );
if(! $pedido_extra ) { die('Não foi possível atualizar extras do orçamento: ' . mysqli_error()); }

if ($gera_revisao == "nao") {
	$sql_atualiza_ped = "UPDATE `pedidos` SET `valor_final` = '".$valor_final_pedido."', `obs_cliente` = '".$obs_cliente."', `obs_comerciais` = '".$obs_comerciais."', `qtde` = '".$qtde."', `referencia` = '".$referencia."', `frete` = '".$frete."', `embarques` = '".$embarques."', `representante` = '".$representante."', `nome_cliente` = '".$nome_cliente."', `cnpj_cpf` = '".$cnpj_cpf."' WHERE  `pedido` =".$no_pedido." AND `revisao` =".$revisao_pedido.";";
	$atualiza_pedido = mysqli_query( $conn, $sql_atualiza_ped );
	//echo $sql_atualiza_ped;
	if(! $atualiza_pedido ) { die('Não foi possível atualizar valor do orçamento: ' . mysqli_error($conn)); }
} elseif ($gera_revisao == "sim") {
	//$sql_atualiza_ped = "INSERT INTO `pedidos` (`pedido`, `nome_cliente`, `segmento_cliente`, `cidade_cliente`, `uf_cliente`, `selecao`, `cnpj_cpf`, `qtde`, `referencia`, `embarques`, `representante`, `mercado`, `frete`, `fornecedora`, `usocons`, `submit`, `distancia_aprox`, `prazo`, `nome_prod`, `dens_aparente`, `temperatura`, `class_uso`, `transporte`, `dem_mensal`, `dem_anual`, `carga_nominal`, `armazenagem`, `corpo`, `base1`, `base2`, `altura`, `plastificado`, `corpo_cor`, `corpo_cor_outro`, `lamin_cor`, `lamin_cor_outro`, `gramat_corpo`, `gramat_forro`, `carga`, `c_quadrado`, `carga1`, `carga2`, `tampa`, `valvula`, `saia`, `descarga`, `d_redondo`, `descarga1`, `descarga2`, `fundo`, `valvula_d`, `alca`, `alca_material`, `alca_cor`, `alca_altura`, `alca_fix_altura`, `reforco_vao_livre`, `reforco_fixacao`, `alca_dupla`, `alca_capac`, `liner`, `tipo_liner`, `liner_espessura`, `fix_liner`, `no_cores`, `imp_controle_viag`, `imp_num_seq`, `sel_faces`, `porta_etq1`, `pos_porta_etq1`, `mod_porta_etq1`, `porta_etq2`, `pos_porta_etq2`, `mod_porta_etq2`, `porta_etq3`, `pos_porta_etq3`, `mod_porta_etq3`, `porta_etq4`, `pos_porta_etq4`, `mod_porta_etq4`, `fardo`, `fardo_pallet`, `palletizado`, `fio_ved_travas`, `velcro`, `cinta_trav`, `gravata`, `med_gravata`, `sapata`, `flap`, `unit`, `valor_custo`, `valor_final`, `log`, `revisao`, `vendedor`, `id_vend`, `data`, `obs_cliente`, `obs_comerciais`, `correcoes`, `status`) SELECT `pedido`, `nome_cliente`, `segmento_cliente`, `cidade_cliente`, `uf_cliente`, `selecao`, `cnpj_cpf`, `qtde`, `referencia`, `embarques`, `representante`, `mercado`, `frete`, `fornecedora`, `usocons`, `submit`, `distancia_aprox`, `prazo`, `nome_prod`, `dens_aparente`, `temperatura`, `class_uso`, `transporte`, `dem_mensal`, `dem_anual`, `carga_nominal`, `armazenagem`, `corpo`, `base1`, `base2`, `altura`, `plastificado`, `corpo_cor`, `corpo_cor_outro`, `lamin_cor`, `lamin_cor_outro`, `gramat_corpo`, `gramat_forro`, `carga`, `c_quadrado`, `carga1`, `carga2`, `tampa`, `valvula`, `saia`, `descarga`, `d_redondo`, `descarga1`, `descarga2`, `fundo`, `valvula_d`, `alca`, `alca_material`, `alca_cor`, `alca_altura`, `alca_fix_altura`, `reforco_vao_livre`, `reforco_fixacao`, `alca_dupla`, `alca_capac`, `liner`, `tipo_liner`, `liner_espessura`, `fix_liner`, `no_cores`, `imp_controle_viag`, `imp_num_seq`, `sel_faces`, `porta_etq1`, `pos_porta_etq1`, `mod_porta_etq1`, `porta_etq2`, `pos_porta_etq2`, `mod_porta_etq2`, `porta_etq3`, `pos_porta_etq3`, `mod_porta_etq3`, `porta_etq4`, `pos_porta_etq4`, `mod_porta_etq4`, `fardo`, `fardo_pallet`, `palletizado`, `fio_ved_travas`, `velcro`, `cinta_trav`, `gravata`, `med_gravata`, `sapata`, `flap`, `unit`, `valor_custo`, `valor_final`, `log`, `revisao`, `vendedor`, `id_vend`, `data`, `obs_cliente`, `obs_comerciais`, `correcoes`, `status`  FROM `pedidos` WHERE `pedido` = ".$no_pedido." ORDER BY `pedidos`.`id`  DESC LIMIT 0,1";
	  $sql_atualiza_ped = "INSERT INTO `pedidos` (`pedido`, `nome_cliente`, `segmento_cliente`, `cidade_cliente`, `uf_cliente`, `selecao`, `cnpj_cpf`, `qtde`, `referencia`, `embarques`, `representante`, `mercado`, `frete`, `fornecedora`, `usocons`, `submit`, `distancia_aprox`, `prazo`, `nome_prod`, `dens_aparente`, `temperatura`, `class_uso`, `transporte`, `dem_mensal`, `dem_anual`, `carga_nominal`, `armazenagem`, `corpo`, `base1`, `base2`, `altura`, `plastificado`, `corpo_cor`, `corpo_cor_outro`, `lamin_cor`, `lamin_cor_outro`, `gramat_corpo`, `gramat_forro`, `trava_rede`, `carga`, `c_quadrado`, `carga1`, `carga2`, `tampa`, `valvula`, `saia`, `descarga`, `d_redondo`, `descarga1`, `descarga2`, `fundo`, `valvula_d`, `cost_fio_topo`, `cost_fio_base`, `alca`, `alca_material`, `alca_cor`, `alca_altura`, `alca_fix_altura`, `alca_fixacao`, `reforco_vao_livre`, `reforco_fixacao`, `alca_dupla`, `alca_capac`, `liner`, `tipo_liner`, `liner_espessura`, `fix_liner`, `no_cores`, `imp_controle_viag`, `imp_num_seq`, `sel_faces`, `porta_etq1`, `pos_porta_etq1`, `mod_porta_etq1`, `porta_etq2`, `pos_porta_etq2`, `mod_porta_etq2`, `porta_etq3`, `pos_porta_etq3`, `mod_porta_etq3`, `porta_etq4`, `pos_porta_etq4`, `mod_porta_etq4`, `fardo`, `fardo_pallet`, `palletizado`, `fio_ved_travas`, `velcro`, `cinta_trav`, `gravata`, `med_gravata`, `sapata`, `flap`, `unit`, `valor_custo`, `valor_final`, `log`, `revisao`, `vendedor`, `id_vend`, `data`, `obs_cliente`, `obs_comerciais`, `correcoes`, `status`) SELECT `pedido`, `nome_cliente`, `segmento_cliente`, `cidade_cliente`, `uf_cliente`, `selecao`, `cnpj_cpf`, `qtde`, `referencia`, `embarques`, `representante`, `mercado`, `frete`, `fornecedora`, `usocons`, `submit`, `distancia_aprox`, `prazo`, `nome_prod`, `dens_aparente`, `temperatura`, `class_uso`, `transporte`, `dem_mensal`, `dem_anual`, `carga_nominal`, `armazenagem`, `corpo`, `base1`, `base2`, `altura`, `plastificado`, `corpo_cor`, `corpo_cor_outro`, `lamin_cor`, `lamin_cor_outro`, `gramat_corpo`, `gramat_forro`, `trava_rede`, `carga`, `c_quadrado`, `carga1`, `carga2`, `tampa`, `valvula`, `saia`, `descarga`, `d_redondo`, `descarga1`, `descarga2`, `fundo`, `valvula_d`, `cost_fio_topo`, `cost_fio_base`, `alca`, `alca_material`, `alca_cor`, `alca_altura`, `alca_fix_altura`, `alca_fixacao`, `reforco_vao_livre`, `reforco_fixacao`, `alca_dupla`, `alca_capac`, `liner`, `tipo_liner`, `liner_espessura`, `fix_liner`, `no_cores`, `imp_controle_viag`, `imp_num_seq`, `sel_faces`, `porta_etq1`, `pos_porta_etq1`, `mod_porta_etq1`, `porta_etq2`, `pos_porta_etq2`, `mod_porta_etq2`, `porta_etq3`, `pos_porta_etq3`, `mod_porta_etq3`, `porta_etq4`, `pos_porta_etq4`, `mod_porta_etq4`, `fardo`, `fardo_pallet`, `palletizado`, `fio_ved_travas`, `velcro`, `cinta_trav`, `gravata`, `med_gravata`, `sapata`, `flap`, `unit`, `valor_custo`, `valor_final`, `log`, `revisao`, `vendedor`, `id_vend`, `data`, `obs_cliente`, `obs_comerciais`, `correcoes`, `status`  FROM `pedidos` WHERE `pedido` = ".$no_pedido." ORDER BY `pedidos`.`id`  DESC LIMIT 0,1";
	$atualiza_pedido = mysqli_query( $conn, $sql_atualiza_ped );
	if(! $atualiza_pedido ) { die('Não foi possível atualizar valor do orçamento: ' . mysqli_error($conn)); }
	$ultimo_id = mysqli_insert_id($conn);
	$sql_atualiza_ped2 = "UPDATE `pedidos` SET `valor_final` = '".$valor_final_pedido."', `obs_cliente` = '".$obs_cliente."', `obs_comerciais` = '".$obs_comerciais."', `qtde` = '".$qtde."', `referencia` = '".$referencia."', `frete` = '".$frete."', `embarques` = '".$embarques."', `representante` = '".$representante."', `nome_cliente` = '".$nome_cliente."', `revisao` = '".$revisao_pedido."' WHERE  `id` =".$ultimo_id.";";
	$atualiza_pedido2 = mysqli_query( $conn, $sql_atualiza_ped2 );
	if(! $atualiza_pedido2 ) { die('Não foi possível atualizar valor do orçamento: ' . mysqli_error($conn)); }
}


Log_Sis($no_pedido,$_SESSION['user']['id'],$_SESSION['user']['nome'],"Atualizou os valores do orçamento: ".sprintf('%05d', $no_pedido).".");

/*
echo "<pre>";
print_r($_POST);
echo "</pre>";


if (is_array($_POST)) {
	foreach ($_POST["campo"] as $campo) {

		if ($campo["valor_kg"] != "" || $campo["qtde_mat"] != "") {
			echo "SIM <br>";
		} else {
			echo "NAO <br>";
		}
	}
}

die();
*/

if (is_array($_POST)) {
foreach ($_POST["campo"] as $campo) {

		$pedido = $campo["pedido"];

		if ($gera_revisao == "nao") {
			$revisao = $campo["revisao"];
		} elseif ($gera_revisao == "sim") {
			$revisao = $revisao_pedido;
		}

		$nivel = $campo["nivel"];
		$desc = str_replace('\"','"',mysqli_real_escape_string($conn,$campo["desc"])); // mysqli_real_escape_string($conn,str_replace("\\","\"",$campo["desc"]));


if ($campo["valor_kg"] != "" || $campo["qtde_mat"] != "") {

		if($campo["qtde"] != "") { $qtde = number_format(str_replace(",",".",$campo["qtde"]),2,".",""); } else { $qtde = "0.00"; }
		if($campo["largura"] != "") { $largura = number_format(str_replace(",",".",$campo["largura"]),2,".",""); } else { $largura = "0.00"; }
		if($campo["corte"] != "") { $corte = number_format(str_replace(",",".",$campo["corte"]),2,".",""); } else { $corte = "0.00"; }
		if($campo["valor_kg"] != "") { $valor_kg = number_format(str_replace(",",".",$campo["valor_kg"]),2,".",""); } else { $valor_kg = "0.00"; }
		if($campo["qtde_mat"] != "") { $qtde_mat = number_format(str_replace(",",".",$campo["qtde_mat"]),2,".",""); } else { $qtde_mat = "0.00"; }
		if($campo["gramat"] != "") { $gramat = number_format(str_replace(",",".",$campo["gramat"]),2,".",""); } else { $gramat = "0"; }
		if($campo["valor"] != "") { $valor = number_format(str_replace(",",".",$campo["valor"]),2,".",""); } else { $valor = "0.00"; }

		$m_quadrado = $campo["m_quadrado"];
		$novo_revisao_valor = $campo["revisao_valor"]+1;

/*
echo "<pre>";
echo "QTDE: ".$qtde."<br>";
echo "LARGURA: ".$largura."<br>";
echo "CORTE: ".$corte."<br>";
echo "VALOR KG: ".$valor_kg."<br>";
echo "QTDE MAT: ".$qtde_mat."<br>";
echo "GRAMAT: ".$gramat."<br>";
echo "</pre>";
*/
	if ($valor_kg == "0.00" && $qtde_mat == "0.00" && $gramat == "") {
//		echo "SAI";
	} else {
		if ($campo["id_det"] != "") {

			if ($valor_kg == "") { $valor_kg = "0.00"; }
			if ($qtde_mat == "") { $qtde_mat = "0"; }
			if ($gramat == "") { $gramat = "0.00"; }

			$sql_novo_det = "INSERT INTO pedidos_det( `id`, `pedido`, `revisao`, `nivel`, `desc`, `valor_kg`, `qtde_mat`, `gramat`, `valor`, `m_quadrado`, `revisao_valor`, `qtde`, `largura`, `corte` ) VALUES (NULL, '".$pedido."', '".$revisao."', '".$nivel."', '".$desc."', '".$valor_kg."', '".$qtde_mat."', '".$gramat."', '".$valor."', '".$m_quadrado."', '".$novo_revisao_valor."', '".$qtde."', '".$largura."', '".$corte."' )";
			$pedido_detalhe = mysqli_query( $conn, $sql_novo_det );

			$novo_id_detalhe = mysqli_insert_id($conn);

			$sql_atualiza_det = "UPDATE `pedidos_det` SET `revisao_valor` = '".$novo_revisao_valor."' WHERE  `pedidos_det`.`id` =".$novo_id_detalhe.";";
			$atualiza_detalhe = mysqli_query( $conn, $sql_atualiza_det );


//echo $sql_novo_det . "<br>" . $novo_id_detalhe . "<br><br>";


			if(! $pedido_detalhe ) { die('Não foi possível duplicar detalhe do orçamento: ' . mysqli_error($conn)); }
			if(! $atualiza_detalhe ) { die('Não foi possível atualizar detalhe do orçamento: ' . mysqli_error($conn)); }


		} else {

			if ($campo["desc"] != "") {

				$sql_novo_det = "INSERT INTO pedidos_det( `id`, `pedido`, `revisao`, `nivel`, `desc`, `valor_kg`, `qtde_mat`, `gramat`, `valor`, `m_quadrado`, `revisao_valor` ) VALUES (NULL, '".$pedido."', '".$revisao."', '".$nivel."', '".$desc."', '".$valor_kg."', '".$qtde_mat."', '".$gramat."', '".$valor."', '".$m_quadrado."', '".$novo_revisao_valor."' )";
//echo $sql_novo_det;
				$pedido_novo_det = mysqli_query( $conn, $sql_novo_det );
				if(! $pedido_novo_det ) { die('Não foi possível inserir novo detalhe do orçamento: ' . mysqli_error($conn)); }

			}
		}
	}
} else {
//	echo "NAO FOI<br><br>";
}
}
//die();


}

//die("<center><br><br><h2>VALORES ATUALIZADOS COM SUCESSO!</h2></center>");

redirect("resumo.php?pedido=".$pedido_original);
die();
}

echo '<br>';

$id_pedido = $_GET["pedido"];

$query_pedidos = mysqli_query($conn, "SELECT * FROM pedidos WHERE `id` = (SELECT MAX(`id`) FROM pedidos WHERE `pedido` = '".$id_pedido."')"); // "SELECT * FROM pedidos WHERE `pedido` = '".$pedido."' ORDER BY `revisao` DESC");
$pedido = mysqli_fetch_array($query_pedidos);

$i = 0;


if ($pedido["status"] == '2') {
	die("<center><br><br><br><h2>Orçamento aguardando aprovação!</h2><br>Você não tem permissão para alterar o orçamento<br>enquanto estiver aguardando aprovação.</center>");
} elseif ($pedido["status"] == '4') {
	die("<center><br><br><br><h2>Orçamento já foi aprovado!</h2><br>Você não tem permissão para alterar um orçamento que já foi aprovado pelo cliente.</center>");
}

if ($_SESSION['user']['nivel'] == '1') {
	$editar = "";
	//echo "ADMINISTRADOR";
} else {
	$editar = " readonly";
	//echo "GERENTE / VEND";
}


$query_detalhes = mysqli_query($conn,"SELECT * FROM `pedidos_det` WHERE `revisao_valor` LIKE (SELECT MAX(`revisao_valor`) FROM `pedidos_det` WHERE `pedido` LIKE '".(float)$id_pedido."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".(float)$id_pedido."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` ASC");
while ($row_pedidos_det = mysqli_fetch_array($query_detalhes)){
    if ($row_pedidos_det['m_quadrado'] == "1") {
        $detalhes_pedido .= "<tr>\n";
        $detalhes_pedido .= "<td>";
        if ($row_pedidos_det['nivel'] == "2") {
            $detalhes_pedido .= "<p style=\"margin:0 0 0 20px;\">";//"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        if ($row_pedidos_det['nivel'] == "2") {
	        $detalhes_pedido .= " ".$row_pedidos_det['desc']."</td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde]\" value=\"".number_format($row_pedidos_det['qtde'],0,',','')."\"></div></td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][largura]\" value=\"".number_format($row_pedidos_det['largura'],2,',','')."\"></div></td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][corte]\" value=\"".number_format($row_pedidos_det['corte'],2,',','')."\"></div></td>\n";
/*
			if($row_pedidos_det['qtde'] != 0) { $detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde]\" value=\"".number_format($row_pedidos_det['qtde'],0,',','')."\"></div></td>\n"; } else { $detalhes_pedido .= "<td class=\"sumir_coluna\"><input type=\"hidden\" name=\"campo[".$i."][qtde]\" value=\"0\"></td>"; }
			if($row_pedidos_det['largura'] != 0) { $detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][largura]\" value=\"".number_format($row_pedidos_det['largura'],2,',','')."\"></div></td>\n"; } else { $detalhes_pedido .= "<td class=\"sumir_coluna\"><input type=\"hidden\" name=\"campo[".$i."][largura]\" value=\"0\"></td>"; }
			if($row_pedidos_det['corte'] != 0) { $detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][corte]\" value=\"".number_format($row_pedidos_det['corte'],2,',','')."\"></div></td>\n"; } else { $detalhes_pedido .= "<td class=\"sumir_coluna\"><input type=\"hidden\" name=\"campo[".$i."][corte]\" value=\"0\"></td>"; }
*/
	        if ($row_pedidos_det['valor_kg'] != "0.00") {
		       	$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-left\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][valor_kg]\" value=\"".number_format($row_pedidos_det['valor_kg'],2,',','')."\"".$editar."><span class=\"form-control-feedback left\"> R$</span></div></td>\n";
		    } else {
		       	$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";
		    }
	       	$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"submat".$sub." form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde_mat]\" value=\"".tiraZero(number_format($row_pedidos_det['qtde_mat'],2,',',''))."\"".$editar."><span class=\"form-control-feedback right\"> m²</span></div></td>\n";
    
	        $detalhes_pedido .= "<td class=\"sumir_coluna\">";
	        if ($row_pedidos_det['gramat'] != "0") {
    	    	$detalhes_pedido .= "<div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][gramat]\" value=\"".tiraZero(number_format($row_pedidos_det['gramat'], 2, ',', '.'))."\"><span class=\"form-control-feedback right\" style=\"width:48px;\"> g/m²</span></div>";
	        }
		        $detalhes_pedido .= "</td>\n";
				$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][peso]\" value=\"". tiraZero(number_format($row_pedidos_det['qtde_mat']*$row_pedidos_det['gramat'], 2, ',', '.')) . "\"".$editar."><span class=\"form-control-feedback right\"> g</span></div></td>\n";
		       	$detalhes_pedido .= "<td><div class=\"has-feedback\"><input style=\"text-align:right;\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][valor]\" id=\"negativo\" class=\"sub".$sub." form-control has-feedback-left\" value=\"".number_format($row_pedidos_det['valor'],2,',','')."\"".$editar."><span class=\"form-control-feedback left\"> R$</span></div></td>";
		} else {
			$detalhes_pedido .= " ".$row_pedidos_det['desc']."</td>\n";
/*
			$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";
*/
			$detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde]\" value=\"".number_format($row_pedidos_det['qtde'],0,',','')."\"></div></td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][largura]\" value=\"".number_format($row_pedidos_det['largura'],2,',','')."\"></div></td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][corte]\" value=\"".number_format($row_pedidos_det['corte'],2,',','')."\"></div></td>\n";
/*
			if($row_pedidos_det['qtde'] != 0) { $detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde]\" value=\"".number_format($row_pedidos_det['qtde'],0,',','')."\"></div></td>\n"; } else { $detalhes_pedido .= "<td class=\"sumir_coluna\"><input type=\"hidden\" name=\"campo[".$i."][qtde]\" value=\"0\"></td>"; }
			if($row_pedidos_det['largura'] != 0) { $detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][largura]\" value=\"".number_format($row_pedidos_det['largura'],2,',','')."\"></div></td>\n"; } else { $detalhes_pedido .= "<td class=\"sumir_coluna\"><input type=\"hidden\" name=\"campo[".$i."][largura]\" value=\"0\"></td>"; }
			if($row_pedidos_det['corte'] != 0) { $detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][corte]\" value=\"".number_format($row_pedidos_det['corte'],2,',','')."\"></div></td>\n"; } else { $detalhes_pedido .= "<td class=\"sumir_coluna\"><input type=\"hidden\" name=\"campo[".$i."][corte]\" value=\"0\"></td>"; }
*/
	        if ($row_pedidos_det['valor_kg'] != "0.00") {
		       	$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input type=\"text\" class=\"form-control has-feedback-left\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][valor_kg]\" value=\"".number_format($row_pedidos_det['valor_kg'],2,',','')."\"".$editar."><span class=\"form-control-feedback left\"> R$</span></td>\n";
		    } else {
		       	$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";
		    }
//	       	if ($row_pedidos_det['desc'] == "Corpo plano simples" || $row_pedidos_det['desc'] == "Corpo tubular simples" || $row_pedidos_det['desc'] == "Corpo painel U" || $row_pedidos_det['desc'] == "Corpo porta-ensacado" || $row_pedidos_det['desc'] == "Tampa fechada" || $row_pedidos_det['desc'] == "Fundo fechado") {
//                $detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" name=\"campo[".$i."][qtde_mat]\" value=\"".tiraZero(number_format($row_pedidos_det['qtde_mat'],2,',','.'))."\"".$editar."><span class=\"form-control-feedback right\"> m²</span></div></td>\n";
//            } else {
                $detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde_mat]\" value=\"".tiraZero(number_format($row_pedidos_det['qtde_mat'],2,',',''))."\"".$editar."><span class=\"form-control-feedback right\"> m²</span></div></td>\n";
//            }
    
	        $detalhes_pedido .= "<td class=\"sumir_coluna\">";
    	    if ($row_pedidos_det['gramat'] != "0") {
	        	$detalhes_pedido .= "<div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][gramat]\" value=\"".tiraZero(number_format($row_pedidos_det['gramat'], 2, ',', '.'))."\"><span class=\"form-control-feedback right\" style=\"width:48px;\"> g/m²</span></div>";
    	    }
	        $detalhes_pedido .= "</td>\n";

	        $detalhes_pedido .= "<td class=\"sumir_coluna\">";
			if ($row_pedidos_det['gramat'] != 0) {
				$detalhes_pedido .= "<div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][peso]\" value=\"". tiraZero(number_format($row_pedidos_det['qtde_mat']*$row_pedidos_det['gramat'], 2, ',', '.')) . "\"".$editar."><span class=\"form-control-feedback right\"> g</span></div></td>\n";
			}
	        $detalhes_pedido .= "</td>\n";

			$sub = $i;
            $detalhes_pedido .= "<td><div class=\"has-feedback\"><input class=\"form-control has-feedback-left\" style=\"text-align:right;\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][valor]\" value=\"".number_format($row_pedidos_det['valor'],2,',','')."\"".$editar."><span class=\"form-control-feedback left\"> R$</span></div></td>";
        }
    
        $detalhes_pedido .= "</tr>\n";
    } elseif ($row_pedidos_det['m_quadrado'] == "0") {
        $detalhes_pedido .= "<tr>\n";
        $detalhes_pedido .= "<td>";
        if ($row_pedidos_det['nivel'] == "2") {
            $detalhes_pedido .= "<p style=\"margin:0 0 0 20px;\">";//"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        }

        if ($row_pedidos_det['nivel'] == "2") {

	        $detalhes_pedido .= " ".$row_pedidos_det['desc']."</td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde]\" value=\"".number_format($row_pedidos_det['qtde'],0,',','')."\"></div></td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][largura]\" value=\"".number_format($row_pedidos_det['largura'],2,',','')."\"></div></td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][corte]\" value=\"".number_format($row_pedidos_det['corte'],2,',','')."\"></div></td>\n";
/*
			if($row_pedidos_det['qtde'] != 0) { $detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde]\" value=\"".number_format($row_pedidos_det['qtde'],0,',','')."\"></div></td>\n"; } else { $detalhes_pedido .= "<td class=\"sumir_coluna\"><input type=\"hidden\" name=\"campo[".$i."][qtde]\" value=\"0\"></td>"; }
			if($row_pedidos_det['largura'] != 0) { $detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][largura]\" value=\"".number_format($row_pedidos_det['largura'],2,',','')."\"></div></td>\n"; } else { $detalhes_pedido .= "<td class=\"sumir_coluna\"><input type=\"hidden\" name=\"campo[".$i."][largura]\" value=\"0\"></td>"; }
			if($row_pedidos_det['corte'] != 0) { $detalhes_pedido .= "<td class=\"sumir_coluna\"><div><input class=\"form-control\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][corte]\" value=\"".number_format($row_pedidos_det['corte'],2,',','')."\"></div></td>\n"; } else { $detalhes_pedido .= "<td class=\"sumir_coluna\"><input type=\"hidden\" name=\"campo[".$i."][corte]\" value=\"0\"></td>"; }
*/

	    	    if ($row_pedidos_det['valor_kg'] != "0.00") {
			       	$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input type=\"text\" class=\"form-control has-feedback-left\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][valor_kg]\" value=\"".number_format($row_pedidos_det['valor_kg'],2,',','')."\"".$editar."><span class=\"form-control-feedback left\"> R$</span></td>\n";
			    } else {
			       	$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";
			    }
	        	if (substr($row_pedidos_det['desc'],0,14) == "Fio de costura") {
			        $detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde_mat]\" value=\"".tiraZero(number_format($row_pedidos_det['qtde_mat'],2,',',''))."\"".$editar."><span class=\"form-control-feedback right\"> m</span></div></td>\n";
			    } else {
			        $detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde_mat]\" value=\"".tiraZero(number_format($row_pedidos_det['qtde_mat'],2,',',''))."\"".$editar."><span class=\"form-control-feedback right\"> m</span></div></td>\n";
			    }

	        $detalhes_pedido .= "<td class=\"sumir_coluna\">";
	        if ($row_pedidos_det['gramat'] != "0") {
        		$detalhes_pedido .= "<div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][gramat]\" value=\"".tiraZero(number_format($row_pedidos_det['gramat'], 2, ',', '.'))."\"><span class=\"form-control-feedback right\" style=\"width:48px;\"> g/m</span></div>";
	        }
	        $detalhes_pedido .= "</td>\n";

	        $detalhes_pedido .= "<td class=\"sumir_coluna\">";
			if ($row_pedidos_det['gramat'] != 0) {
				$detalhes_pedido .= "<div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][peso]\" value=\"". tiraZero(number_format($row_pedidos_det['qtde_mat']*$row_pedidos_det['gramat'], 2, ',', '.')) . "\"".$editar."><span class=\"form-control-feedback right\"> g</span></div></td>\n";
			}
	        $detalhes_pedido .= "</td>\n";


	    	$detalhes_pedido .= "<td><div class=\"has-feedback\"><input style=\"padding-right:10px;text-align:right;\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][valor]\" id=\"negativo\" class=\"sub".$sub." form-control has-feedback-left\" value=\"".number_format($row_pedidos_det['valor'],2,',','')."\"".$editar."><span class=\"form-control-feedback left\"> R$</span></div></td>";
        } else {
	        $detalhes_pedido .= " ".$row_pedidos_det['desc']."</td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";
			$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";

	        if (substr($row_pedidos_det['desc'],0,6) == "Liner " || $row_pedidos_det['desc'] == "Cola") {
		       	$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-left\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][valor_kg]\" value=\"".number_format($row_pedidos_det['valor_kg'],2,',','')."\"".$editar."><span class=\"form-control-feedback left\"> R$</span></div></td>\n";
	        	$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde_mat]\" value=\"".tiraZero(number_format($row_pedidos_det['qtde_mat'],2,',',''))."\"".$editar."><span class=\"form-control-feedback right\"> kg</span></div></td>\n";
	        } elseif (substr($row_pedidos_det['desc'],0,12) == "Impressão -" || substr($row_pedidos_det['desc'],0,16) == "Porta etiqueta -") {
				$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-left\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][valor_kg]\" value=\"".number_format($row_pedidos_det['valor_kg'],2,',','')."\"".$editar."><span class=\"form-control-feedback left\"> R$</span></div></td>\n";
				$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde_mat]\" value=\"".tiraZero(number_format($row_pedidos_det['qtde_mat'],2,',',''))."\"".$editar."><span class=\"form-control-feedback right\"> un</span></div></td>\n";
	        } elseif ($row_pedidos_det['desc'] == "Pack Less") {
		       	$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";
	        	$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde_mat]\" value=\"".tiraZero(number_format($row_pedidos_det['qtde_mat'],2,',',''))."\"".$editar."><span class=\"form-control-feedback right\"> par</span></div></td>\n";
	        } else {
	    	    if ($row_pedidos_det['valor_kg'] != "0.00") {
			       	$detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-left\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][valor_kg]\" value=\"".number_format($row_pedidos_det['valor_kg'],2,',','')."\"".$editar."><span class=\"form-control-feedback left\"> R$</span></div></td>\n";
			        $detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde_mat]\" value=\"".tiraZero(number_format($row_pedidos_det['qtde_mat'],2,',',''))."\"".$editar."><span class=\"form-control-feedback right\"> m</span></div></td>\n";
			    } else {
			       	$detalhes_pedido .= "<td class=\"sumir_coluna\"></td>\n";
			        $detalhes_pedido .= "<td class=\"sumir_coluna\"><div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][qtde_mat]\" value=\"".tiraZero(number_format($row_pedidos_det['qtde_mat'],2,',',''))."\"".$editar."><span class=\"form-control-feedback right\"> m</span></div></td>\n";
			    }
		    }

	        $detalhes_pedido .= "<td class=\"sumir_coluna\">";
	        if ($row_pedidos_det['gramat'] != "0" && substr($row_pedidos_det['desc'],0,12) != "Impressão -" && substr($row_pedidos_det['desc'],0,16) != "Porta etiqueta -") {
	        	$detalhes_pedido .= "<div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][gramat]\" value=\"".tiraZero(number_format($row_pedidos_det['gramat'], 2, ',', '.'))."\"><span class=\"form-control-feedback right\"> g/m</span></div>";
	        } else {
	        	$detalhes_pedido .= "<div style=\"display:none;\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][gramat]\" value=\"".tiraZero($row_pedidos_det['gramat'])."\"><span class=\"form-control-feedback right\"> g/m</span></div>";
	        }
	        $detalhes_pedido .= "</td>\n";

	        $detalhes_pedido .= "<td class=\"sumir_coluna\">";
	        if ($row_pedidos_det['gramat'] != "0" && substr($row_pedidos_det['desc'],0,12) != "Impressão -" && substr($row_pedidos_det['desc'],0,16) != "Porta etiqueta -") {
				$detalhes_pedido .= "<div class=\"has-feedback\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][peso]\" value=\"". tiraZero(number_format($row_pedidos_det['qtde_mat']*$row_pedidos_det['gramat'], 2, ',', '.')) . "\"".$editar."><span class=\"form-control-feedback right\"> g</span></div></td>\n";
	        } else {
				$detalhes_pedido .= "<div style=\"display:none;\"><input class=\"form-control has-feedback-right\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][peso]\" value=\"". tiraZero($row_pedidos_det['qtde_mat']*$row_pedidos_det['gramat']) . "\"".$editar."><span class=\"form-control-feedback right\"> g</span></div></td>\n";
			}
	        $detalhes_pedido .= "</td>\n";

			$sub = $i;
            $detalhes_pedido .= "<td><div class=\"has-feedback\"><input class=\"form-control\" style=\"padding-right: 10px; text-align:right;\" type=\"text\" onchange=\"RecalculaLinha('".$i."','".$sub."');\" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name=\"campo[".$i."][valor]\" value=\"".number_format($row_pedidos_det['valor'],2,',','')."\"".$editar."><span class=\"form-control-feedback left\"> R$</span></td>";
        }
        $detalhes_pedido .= "</tr>";
    }
	$detalhes_pedido .= "\n".'<input type="hidden" name="campo['.$i.'][id_det]" value="'.$row_pedidos_det['id'].'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][pedido]" value="'.$row_pedidos_det['pedido'].'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][revisao]" value="'.$row_pedidos_det['revisao'].'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][revisao_valor]" value="'.$row_pedidos_det['revisao_valor'].'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][desc]" value="'.htmlspecialchars($row_pedidos_det['desc']).'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][nivel]" value="'.$row_pedidos_det['nivel'].'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][m_quadrado]" value="'.$row_pedidos_det['m_quadrado'].'">'."\n\n";

	$revisao_valor = $row_pedidos_det['revisao_valor'];
	$revisao = $row_pedidos_det['revisao'];


	if ($row_pedidos_det['nivel'] == "1") {
		$subtotal = $subtotal + $row_pedidos_det['valor'];
	}
	$i = $i + 1;
}
	$detalhes_pedido .= "\n".'<tr><td><input class="form-control" type="text" name="campo['.$i.'][desc]" value=""></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div><input type="text" class="form-control" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][qtde]" value=""></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div><input type="text" class="form-control" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][largura]" value=""></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div><input type="text" class="form-control" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][corte]" value=""></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input type="text" class="form-control has-feedback-left" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][valor_kg]" value=""><span class="form-control-feedback left"> R$</span></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input class="form-control has-feedback-right" type="text" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][qtde_mat]" value=""><span class="form-control-feedback right"> m</span></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input class="form-control has-feedback-right" type="text" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][gramat]" value=""><span class="form-control-feedback right"> g/m</span></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input class="form-control has-feedback-right" type="text" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][peso]" value=""><span class="form-control-feedback right"> g</span></div></td>'."\n";
	$detalhes_pedido .= '<td><div class="has-feedback"><input class="form-control has-feedback-left" type="text" name="campo['.$i.'][valor]" style="text-align:right;" value=""><span class="form-control-feedback left"> R$</span></div></td></tr>'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][pedido]" value="'.(float)$id_pedido.'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][revisao]" value="'.$revisao.'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][revisao_valor]" value="'.$revisao_valor.'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][nivel]" value="1">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][m_quadrado]" value="0">'."\n\n";

	$i = $i + 1;
	$detalhes_pedido .= "\n".'<tr><td><input class="form-control" type="text" name="campo['.$i.'][desc]" value=""></td>';
	$detalhes_pedido .= '<td class="sumir_coluna"><div><input type="text" class="form-control" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][qtde]" value=""></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div><input type="text" class="form-control" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][largura]" value=""></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div><input type="text" class="form-control" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][corte]" value=""></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input type="text" class="form-control has-feedback-left" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][valor_kg]" value=""><span class="form-control-feedback left"> R$</span></div></td>';
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input class="form-control has-feedback-right" type="text" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][qtde_mat]" value=""><span class="form-control-feedback right"> m</span></div></td>';
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input class="form-control has-feedback-right" type="text" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][gramat]" value=""><span class="form-control-feedback right"> g/m</span></div></td>';
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input class="form-control has-feedback-right" type="text" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name="campo['.$i.'][peso]" value=""><span class="form-control-feedback right"> g</span></div></td>';
	$detalhes_pedido .= '<td><div class="has-feedback"><input class="form-control has-feedback-left" type="text" name="campo['.$i.'][valor]" style="text-align:right;" value=""><span class="form-control-feedback left"> R$</span></div></td></tr>'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][pedido]" value="'.(float)$id_pedido.'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][revisao]" value="'.$revisao.'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][revisao_valor]" value="'.$revisao_valor.'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][nivel]" value="1">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][m_quadrado]" value="0">'."\n\n";

	$i = $i + 1;
	$detalhes_pedido .= "\n".'<tr><td><input class="form-control" type="text" name="campo['.$i.'][desc]" value=""></td>';
	$detalhes_pedido .= '<td class="sumir_coluna"><div><input type="text" class="form-control" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][qtde]" value=""></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div><input type="text" class="form-control" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][largura]" value=""></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div><input type="text" class="form-control" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][corte]" value=""></div></td>'."\n";
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input type="text" class="form-control has-feedback-left" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][valor_kg]" value=""><span class="form-control-feedback left"> R$</span></div></td>';
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input class="form-control has-feedback-right" type="text" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][qtde_mat]" value=""><span class="form-control-feedback right"> m</span></div></td>';
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input class="form-control has-feedback-right" type="text" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" name="campo['.$i.'][gramat]" value=""><span class="form-control-feedback right"> g/m</span></div></td>';
	$detalhes_pedido .= '<td class="sumir_coluna"><div class="has-feedback"><input class="form-control has-feedback-right" type="text" onchange="RecalculaLinha('.$i.','.$sub.');" onkeyup=\"this.onchange();\" onpaste=\"this.onchange();\" oninput=\"this.onchange();\" name="campo['.$i.'][peso]" value=""><span class="form-control-feedback right"> g</span></div></td>';
	$detalhes_pedido .= '<td><div class="has-feedback"><input class="form-control has-feedback-left" type="text" name="campo['.$i.'][valor]" style="text-align:right;" value=""><span class="form-control-feedback left"> R$</span></div></td></tr>'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][pedido]" value="'.(float)$id_pedido.'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][revisao]" value="'.$revisao.'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][revisao_valor]" value="'.$revisao_valor.'">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][nivel]" value="1">'."\n";
	$detalhes_pedido .= '<input type="hidden" name="campo['.$i.'][m_quadrado]" value="0">'."\n\n";

?>
<form name="det_pedido" class="orcamento" action="pedidos.php?acao=analisar&pedido=<?php echo $_GET["pedido"]; ?>" method="post">

<div class="clearfix"></div>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Análise de orçamento</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">

<table class="table table-bordered">
<tr>
<td>Número do orçamento</td>
<td style="width: 150px;" align="right"><font size="5"><b><?php echo $id_pedido; ?> / <?php echo sprintf('%02d', $revisao); ?></b></font></td>
</tr>
</table>

<br>
<table class="table table-bordered">
<tr>
<td colspan="2"><label class="control-label" for="nome_cliente">Nome do cliente:</label><input type="text" name="nome_cliente" class="form-control" value="<?php echo $pedido["nome_cliente"]; ?>"></td>
<td><label class="control-label" for="referencia">Referência:</label><input type="text" name="referencia" class="form-control" value="<?php echo $pedido["referencia"]; ?>"></td>
</tr>
<tr>
<td><label class="control-label"><?php echo strtoupper($pedido["selecao"]); ?>:</label><input type="text" class="form-control" name="cnpj_cpf" value="<?php echo $pedido["cnpj_cpf"]; ?>"></td>
<td><label class="control-label">Cidade:</label><input type="text" class="form-control" value="<?php echo $pedido["cidade_cliente"]; ?>" readonly></td>
<td><label class="control-label">UF:</label><input type="text" class="form-control" value="<?php echo $pedido["uf_cliente"]; ?>" readonly></td>
</tr>
<tr>
<td><label class="control-label">Fornecedora:</label><input type="text" class="form-control" value="<?php

$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora` WHERE sigla = '".strtoupper(substr($pedido["fornecedora"],6,2))."'");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
	echo $row_fornec['apelido'];
}

?>" readonly></td>
<td><label class="control-label" for="nome_prod">Quantidade:</label>
<input type="text" name="qtde" class="form-control" value="<?php echo $pedido["qtde"]; ?>"></td>
<td><label class="control-label" for="frete">Frete tipo:</label>
	<select name="frete" class="form-control">
		<option value="exw"<?php if ($pedido["frete"] == "exw") { echo " selected"; } ?>>EXW (A partir da fábrica)</option>
		<option value="fca"<?php if ($pedido["frete"] == "fca") { echo " selected"; } ?>>FCA (Transportador livre)</option>
		<option value="cpt"<?php if ($pedido["frete"] == "cpt") { echo " selected"; } ?>>CPT (Frete pago até)</option>
		<option value="cip"<?php if ($pedido["frete"] == "cip") { echo " selected"; } ?>>CIP (Frete e seguro pagos até)</option>
		<option value="dat"<?php if ($pedido["frete"] == "dat") { echo " selected"; } ?>>DAT (Entregue no terminal)</option>
		<option value="dap"<?php if ($pedido["frete"] == "dap") { echo " selected"; } ?>>DAP (Entregue no local de destino)</option>
		<option value="ddp"<?php if ($pedido["frete"] == "ddp") { echo " selected"; } ?>>DDP (Entregue com direitos pagos)</option>
		<option value="fas"<?php if ($pedido["frete"] == "fas") { echo " selected"; } ?>>FAS (Livre junto ao costado do navio)</option>
		<option value="fob"<?php if ($pedido["frete"] == "fob") { echo " selected"; } ?>>FOB (Livre a bordo)</option>
		<option value="cfr"<?php if ($pedido["frete"] == "cfr") { echo " selected"; } ?>>CFR (Custo e frete)</option>
		<option value="cif"<?php if ($pedido["frete"] == "cif") { echo " selected"; } ?>>CIF (Custo, seguro e frete)</option>
	</select></td>
	</tr>
	<input type="hidden" name="id_pedido" value="<?php echo $pedido["id"]; ?>">
	<tr>
	<td colspan="2"><label class="control-label" for="embarques">Embarques:</label><input type="text" name="embarques" class="form-control" value="<?php echo $pedido["embarques"]; ?>"></td>
	<td><label class="control-label" for="representante">Representante:</label><input type="text" name="representante" class="form-control" value="<?php echo $pedido["representante"]; ?>"></td>
	</tr>
</table>
<br>
<table class="table table-bordered"><tr>
<td><b>Descrição</b></td>
<td style="width: 75px;" class="sumir_coluna"><b>Qtde.<br>(un.)</b></td>
<td style="width: 88px;" class="sumir_coluna"><b>Largura<br>(cm)</b></td>
<td style="width: 88px;" class="sumir_coluna"><b>Corte<br>(cm)</b></td>
<td class="sumir_coluna"><b>Valor do quilo</b></td>
<td class="sumir_coluna"><b>Quantidade de material</b></td>
<td class="sumir_coluna"><b>Matéria-prima</b></td>
<td class="sumir_coluna"><b>Peso</b></td>
<td style="width: 120px;" align="right"><b>Valor</b></td>
</tr>
<?php
echo $detalhes_pedido;
?>
<tr>
<td><b><?php echo "SUB-TOTAL"; ?></b></td>
<td colspan="7" class="sumir_coluna"></td>
<td class="contabil"><div class="has-feedback"><input class="form-control has-feedback-left" style="font-weight: bold; text-align: right;" type="text" id="subtotal" name="subtotal" value="" readonly><?php // echo "R$ ".number_format((float)$subtotal, 2, ',', '.'); ?><span class="form-control-feedback left"> <b>R$</b></span></div></td>
</tr>
<?php
echo '</table>';


/* CÁLCULO DE CUSTOS ADICIONAIS */

/*
$fornecedora = $pedido["fornecedora"];

$query_mat_aux = mysqli_query($conn,"SELECT * FROM `preco_kilo` WHERE `tipo` LIKE 'mat_auxiliar'");
$row_mat_aux = mysqli_fetch_array($query_mat_aux);
$valor_mat_auxiliar = $row_mat_aux["".$fornecedora.""];

$query_cif = mysqli_query($conn,"SELECT * FROM `preco_kilo` WHERE `tipo` LIKE 'cif'");
$row_cif = mysqli_fetch_array($query_cif);
$valor_cif = $row_cif["".$fornecedora.""];

$query_mao_obra = mysqli_query($conn,"SELECT * FROM `preco_kilo` WHERE `tipo` LIKE 'mao_obra'");
$row_mao_obra = mysqli_fetch_array($query_mao_obra);
$valor_mao_obra = $row_mao_obra["".$fornecedora.""];

$total = $subtotal + $valor_mat_auxiliar + $valor_cif + $valor_mao_obra;

$valor_margem = 15.00;
*/

//$query_impostos = mysqli_query($conn,"SELECT * FROM `pedidos_extra` WHERE `rev_valor` LIKE (SELECT MAX(`rev_valor`) FROM `pedidos_extra` WHERE `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` DESC"); //AND `pedido` LIKE '".$pedido["pedido"]."'
$query_impostos = mysqli_query($conn,"SELECT * FROM `pedidos_extra` WHERE `id` LIKE (SELECT MAX(`id`) FROM `pedidos_extra` WHERE `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` DESC"); //AND `pedido` LIKE '".$pedido["pedido"]."'
$row_impostos = mysqli_fetch_array($query_impostos);



$valor_mat_auxiliar = $row_impostos["mat_auxiliar"];
$valor_cif = $row_impostos["cif"];
$valor_mao_obra = $row_impostos["mao_obra"];
$total = $row_impostos["custo_bag"];

$query_ped_desc = mysqli_query($conn,"SELECT * FROM `pedidos_desc` WHERE `pedido` LIKE '".ltrim($_GET["pedido"], '0')."' ORDER BY `id` DESC");

if(mysqli_num_rows($query_ped_desc)) {
	$row_ped_desc = mysqli_fetch_array($query_ped_desc);
	$desc_mat = $row_ped_desc["desc_mat"];
	$desc_cif = $row_ped_desc["desc_cif"];
	$desc_mo = $row_ped_desc["desc_mo"];
} else {
	$desc_mat = "Materiais e insumos diretos / indiretos / auxiliares";
	$desc_cif = "CIF (Custos Indiretos de Fabricação)";
	$desc_mo = "Mão de obra direta";
}

?>
<input type="hidden" name="class_prod" value="<?php echo $row_impostos["class_prod"]; ?>">
<br>
<table class="table table-bordered">
<tr>
<td><input type="text" class="form-control" name="desc_mat" value="<?php echo $desc_mat; ?>"<?php echo $editar; ?>></td>
<td style="width: 120px;"><div class="has-feedback"><input onchange="CalculaCusto();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-left" style="text-align: right;" type="text" id="valor_mat_auxiliar" name="valor_mat_auxiliar" value="<?php echo number_format((float)$valor_mat_auxiliar, 2, ',', '.'); ?>"<?php echo $editar; ?>><span class="form-control-feedback left"> R$</span></div></td>
</tr>
<tr>
<td><input type="text" class="form-control" name="desc_cif" value="<?php echo $desc_cif; ?>"<?php echo $editar; ?>></td>
<td><div class="has-feedback"><input onchange="CalculaCusto();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-left" style="text-align: right;" type="text" id="valor_cif" name="valor_cif" value="<?php echo number_format((float)$valor_cif, 2, ',', '.'); ?>"<?php echo $editar; ?>><span class="form-control-feedback left"> R$</span></div></td>
</tr>
<tr>
<td><input type="text" class="form-control" name="desc_mo" value="<?php echo $desc_mo; ?>"<?php echo $editar; ?>></td>
<td><div class="has-feedback"><input onchange="CalculaCusto();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-left" style="text-align: right;" type="text" id="valor_mao_obra" name="valor_mao_obra" value="<?php echo number_format((float)$valor_mao_obra, 2, ',', '.'); ?>"<?php echo $editar; ?>><span class="form-control-feedback left"> R$</span></div></td>
</tr>
<tr>
<td><?php echo "<b>CUSTO DO BAG</b>"; ?></td>
<td><div class="has-feedback"><input class="form-control has-feedback-left" style="text-align: right; font-weight: bold;" type="text" id="valor_custo_bag" name="valor_custo_bag" value="NAO<?php //echo number_format((float)$total, 2, ',', '.'); ?>" readonly><span class="form-control-feedback left"> <b>R$</b></span></div></td>
</tr>
</table>

<br>

<?php
/* CÁLCULO DE IMPOSTOS */

/*

$unidade_forn = substr($fornecedora, -2);
$uf_cliente = $pedido["uf_cliente"];

if ($unidade_forn == "sp" && $pedido["usocons"] == "sim") {
	$valor_icms = "18";
} else {
	$query_icms = mysqli_query($conn,"SELECT * FROM `imposto_icms` WHERE `unidade` LIKE '".$unidade_forn."'");
	$row_icms = mysqli_fetch_array($query_icms);
	$valor_icms = $row_icms["".$uf_cliente.""];
}

$query_pis = mysqli_query($conn,"SELECT * FROM `imposto_pis` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_pis = mysqli_fetch_array($query_pis);
$valor_pis = $row_pis["".$uf_cliente.""];

$query_cofins = mysqli_query($conn,"SELECT * FROM `imposto_cofins` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_cofins = mysqli_fetch_array($query_cofins);
$valor_cofins = $row_cofins["".$uf_cliente.""];

$query_ir = mysqli_query($conn,"SELECT * FROM `imposto_ir` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_ir = mysqli_fetch_array($query_ir);
$valor_ir = $row_ir["".$uf_cliente.""];

$query_csll = mysqli_query($conn,"SELECT * FROM `imposto_csll` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_csll = mysqli_fetch_array($query_csll);
$valor_csll = $row_csll["".$uf_cliente.""];

$query_inss = mysqli_query($conn,"SELECT * FROM `imposto_inss` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_inss = mysqli_fetch_array($query_inss);
$valor_inss = $row_inss["".$uf_cliente.""];

$query_perda = mysqli_query($conn,"SELECT * FROM `imposto_perda` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_perda = mysqli_fetch_array($query_perda);
$valor_perda = $row_perda["".$uf_cliente.""];

$distancia_aprox = $pedido["distancia_aprox"];

if ($distancia_aprox == 0) {
	$valor_frete = 0;
} elseif ($distancia_aprox == 1) {
	$valor_frete = 2.00;
} elseif ($distancia_aprox == 2) {
	$valor_frete = 4.00;
} elseif ($distancia_aprox == 3) {
	$valor_frete = 6.00;
}

$id_user = $pedido["id_vend"];
$query_comissao = mysqli_query($conn,"SELECT * FROM `users` WHERE `id` LIKE '".$id_user."'");
$row_comissao = mysqli_fetch_array($query_comissao);
$valor_comissao = $row_comissao["comissao"];

$query_cfin = mysqli_query($conn,"SELECT * FROM preco_kilo");
while($row_cfin = mysqli_fetch_array($query_cfin)) {
	if ($row_cfin['tipo'] == "c_financeiro") {
		$c_financeiro = $row_cfin["".$fornecedora.""];
	}
}

$prazo = $pedido["prazo"];
$valor_cfinanceiro = $c_financeiro * $prazo / 28;

$valor_imposto = $valor_icms + $valor_pis + $valor_cofins + $valor_ir + $valor_csll + $valor_inss + $valor_perda + $valor_frete + $valor_comissao + $valor_cfinanceiro + $valor_margem;

$valor_final_venda = 100-$valor_imposto;
$valor_final_venda = $valor_final_venda/100;
$valor_final_venda = $total/$valor_final_venda;





/*
echo $subtotal ." + ". $valor_mat_auxiliar ." + ". $valor_cif ." + ". $valor_mao_obra;
echo " = ".$total."<br><br>";

echo $valor_icms ." + ". $valor_pis ." + ". $valor_cofins ." + ". $valor_ir ." + ". $valor_csll ." + ". $valor_inss ." + ". $valor_perda ." + ". $valor_frete ." + ". $valor_comissao ." + ". $valor_cfinanceiro ." + ". $valor_margem;
echo " = ".$valor_imposto."<br><br>";
*/

$valor_icms = $row_impostos["icms"];
$valor_ipi = $row_impostos["ipi"];
$valor_pis = $row_impostos["pis"];
$valor_cofins = $row_impostos["cofins"];
$valor_ir = $row_impostos["ir"];
$valor_csll = $row_impostos["csll"];
$valor_inss = $row_impostos["inss"];
$valor_perda = $row_impostos["perda"];
$valor_frete = $row_impostos["frete"];
$valor_comissao = $row_impostos["comissao"];
$valor_adm_comercial = $row_impostos["adm_comercial"];
$valor_cfinanceiro = $row_impostos["custo_fin"];
$valor_margem = $row_impostos["margem"];
$valor_imposto = $row_impostos["imposto_total"];

//echo $row_impostos["id"];

if ($pedido["mercado"] == "ext") {
	$desab_imp = " readonly";
} else {
	$desab_imp = "";
}

?>

<table class="table table-bordered" style="border:0;">
<tr>
<td style="min-width:300px;"><?php echo "ICMS"; ?></td>
<td style="width: 120px;"><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_icms" name="imposto_icms" value="<?php echo number_format((float)$valor_icms, 2, ',', '.'); ?>"<?php echo $desab_imp; ?><?php echo $editar; ?>><span class="form-control-feedback right"> %</span></div></td>
<td style="width: 50px; border-top:none; border-bottom:0;"></td>
<td style="min-width:300px;"><?php echo "PIS"; ?></td>
<td style="width: 120px;"><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_pis" name="imposto_pis" value="<?php echo number_format((float)$valor_pis, 2, ',', '.'); ?>"<?php echo $desab_imp; ?><?php echo $editar; ?>><span class="form-control-feedback right"> %</span></div></td>
</tr>
<tr>
<td><?php echo "COFINS"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_cofins" name="imposto_cofins" value="<?php echo number_format((float)$valor_cofins, 2, ',', '.'); ?>"<?php echo $desab_imp; ?><?php echo $editar; ?>><span class="form-control-feedback right"> %</span></div></td>
<td style="border-top:none; border-bottom:0;"></td>
<td><?php echo "IR"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_ir" name="imposto_ir" value="<?php echo number_format((float)$valor_ir, 2, ',', '.'); ?>"<?php echo $editar; ?>><span class="form-control-feedback right"> %</span></div></td>
</tr>
<tr>
<td><?php echo "CSLL"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_csll" name="imposto_csll" value="<?php echo number_format((float)$valor_csll, 2, ',', '.'); ?>"<?php echo $editar; ?>><span class="form-control-feedback right"> %</span></div></td>
<td style="border-top:none; border-bottom:0;"></td>
<td><?php echo "INSS"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_inss" name="imposto_inss" value="<?php echo number_format((float)$valor_inss, 2, ',', '.'); ?>"<?php echo $editar; ?>><span class="form-control-feedback right"> %</span></div></td>
</tr>
<tr>
<td><?php echo "PERDA"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_perda" name="imposto_perda" value="<?php echo number_format((float)$valor_perda, 2, ',', '.'); ?>"><span class="form-control-feedback right"> %</span></div></td>
<td style="border-top:none; border-bottom:0;"></td>
<td><?php echo "FRETE"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_frete" name="imposto_frete" value="<?php echo number_format((float)$valor_frete, 2, ',', '.'); ?>"><span class="form-control-feedback right"> %</span></div></td>
</tr>
<tr>
<td><?php echo "COMISSÃO"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_comissao" name="imposto_comissao" value="<?php echo number_format((float)$valor_comissao, 2, ',', '.'); ?>"><span class="form-control-feedback right"> %</span></div></td>
<td style="border-top:none; border-bottom:0;"></td>
<td><?php echo "ADMINISTRAÇÃO COMERCIAL"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_adm_comercial" name="imposto_adm_comercial" value="<?php echo number_format((float)$valor_adm_comercial, 2, ',', '.'); ?>"><span class="form-control-feedback right"> %</span></div></td>
</tr>
<tr>
<td><?php echo "MARGEM"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_margem" name="imposto_margem" value="<?php echo number_format((float)$valor_margem, 2, ',', '.'); ?>"><span class="form-control-feedback right"> %</span></div></td>
<td style="border-top:none; border-bottom:0;"></td>
<td><?php echo "CUSTO FINANCEIRO"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" type="text" id="imposto_custo_fin" name="imposto_custo_fin" value="<?php echo number_format((float)$valor_cfinanceiro, 2, ',', '.'); ?>"<?php echo $editar; ?>><span class="form-control-feedback right"> %</span></div></td>
</tr>
<tr>
<td><?php echo "IPI (não incluso)"; ?></td>
<td><div class="has-feedback"><input class="form-control has-feedback-right" type="text" id="imposto_ipi" name="imposto_ipi" value="<?php echo number_format((float)$valor_ipi, 2, ',', '.'); ?>"><span class="form-control-feedback right"> %</span></div></td>
<td style="border-top:none; border-bottom:0;"></td>
<td><?php echo "<b>TOTAL DE IMPOSTOS</b>"; ?></td>
<td><div class="has-feedback"><input onchange="CalculaImpostos();" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" class="form-control has-feedback-right" style="font-weight: bold;" type="text" id="imposto_total" name="imposto_total" value="<?php echo number_format((float)$valor_imposto, 3, ',', '.'); ?>"<?php echo $editar; ?>><span class="form-control-feedback right"> <b>%</b></span></div></td>
</tr>
</table>
<br>
<?php

$id_extra = $row_impostos["id"];

$bd_valor_dolar = $row_impostos["valor_dolar"];
$bd_cambio = $row_impostos["cambio"];
$bd_cambio_data = $row_impostos["cambio_data"];

$bd_cambio_dia = strtotime($bd_cambio_data);
$bd_cambio_hora = date("H:i:s", $bd_cambio_dia);
$bd_cambio_dia = date("d/m/Y", $bd_cambio_dia);

/*
echo "ID Extra: ".$id_extra."<br>";
echo "Valor em dolar: ".$bd_valor_dolar."<br>";
echo "Cambio: ".$bd_cambio."<br>";
echo "Cambio dia: ".$bd_cambio_dia."<br>";
echo "Cambio hora: ".$bd_cambio_hora."<br>";
echo "Cambio data: ".$bd_cambio_data."<br>";
*/

$valor_semimposto = $valor_ir + $valor_csll + $valor_inss + $valor_perda + $valor_comissao + $valor_adm_comercial + $valor_cfinanceiro + $valor_margem;
?>
<input type="hidden" id="sem_imposto_total" name="sem_imposto_total" value="<?php echo number_format((float)$valor_semimposto, 2, ',', '.'); ?>">
<?php
$valor_semimposto = 100 - $valor_semimposto;
$valor_semimposto /= 100;
$valor_semimposto = $total / $valor_semimposto;

/*
require("dolar/class.UOLCotacoes.php");
$uol = new UOLCotacoes();
list($dolarComercialCompra, $dolarComercialVenda, $dolarTurismoCompra, $dolarTurismoVenda, $euroCompra, $euroVenda, $libraCompra, $libraVenda, $pesosCompra, $pesosVenda) = $uol->pegaValores();
*/
$query_dolar = mysqli_query($conn,"SELECT * FROM `taxa_dolar` ORDER BY `id` DESC LIMIT 1");
$taxa_dolar = mysqli_fetch_array($query_dolar);

/*
echo "<pre>";
print_r($taxa_dolar);
echo "</pre>";

echo "<pre>";
echo DateTime::createFromFormat("d/m/Y",mask($taxa_dolar["dia"],'##/##/####'))->format('Y-m-d')."\n\n";
echo "</pre>";
*/

if ($bd_valor_dolar != "0.00") {
	$cambio_dia = date("Y-m-d",strtotime($bd_cambio_data));
	$cotacao_dolar = $bd_cambio;
} else {
	//$cotacao_dolar = str_replace( '.', ',', $dolarComercialCompra);
	$cotacao_dolar = $taxa_dolar["venda"];
	//$cambio_dia = DateTime::createFromFormat("d/m/Y",mask($taxa_dolar["dia"],'##/##/####'))->format('Y-m-d');
	//echo $taxa_dolar["dia"]."<br>";
	$cambio_dia = substr($taxa_dolar["dia"],-4)."-".substr($taxa_dolar["dia"],-6,2)."-".substr($taxa_dolar["dia"],-8,2);
}

?>


<table class="table table-bordered">
<tr>
<td><?php echo "<b>VALOR COM IMPOSTOS</b>"; ?></td>
<td style="width: 120px;"><div class="has-feedback"><input type="text" class="form-control has-feedback-left" style="text-align:right;font-weight: bold;" id="valor_final_pedido" name="valor_final_pedido" value="<?php echo number_format((float)$valor_final_venda, 2, ',', '.'); ?>" readonly><span class="form-control-feedback left"> <b>R$</b></span></div></td>
</tr>
<tr>
<td><?php echo "<b>VALOR SEM IMPOSTOS</b> (sem ICMS - PIS - COFINS - IR - CSLL - INSS)"; ?></td>
<td><div class="has-feedback"><input type="text" class="form-control has-feedback-left" style="text-align:right;font-weight: bold;" id="valor_semimposto" name="valor_semimposto" value="<?php echo number_format((float)$valor_semimposto, 2, ',', '.'); ?>" readonly><span class="form-control-feedback left"> <b>R$</b></span></div></td>
</tr>
<tr>
<td class="contabil"><b>VALOR EM DÓLAR</b> (valor com impostos convertido) <div style="float:right;"><a href="cambio.php" data-lity>Câmbio</a>: R$ <input style="width: 75px; text-align:right;" type="number" step="0.0001" id="cambio_dolar" name="cambio_dolar" value="<?php echo $cotacao_dolar; ?>"> em <input style="width:125px; text-align:right;" type="date" id="cambio_dia" name="cambio_dia" value="<?php echo $cambio_dia; ?>"></div></td>
<td><div class="has-feedback"><input type="text" class="form-control has-feedback-left" style="text-align:right;font-weight: bold;" id="valor_final_dolar" name="valor_final_dolar" readonly><span class="form-control-feedback left" style="width:40px;"> <b>US$</b></span></div></td>
</tr>
</table>
<input type="hidden" name="revisao_pedido" value="<?php echo $pedido["revisao"]; ?>">
<input type="hidden" name="rev_valor" value="<?php echo $row_impostos["rev_valor"]; ?>">
<br>

<table class="table table-bordered">

<tr>
<td><b>Observações técnicas:</b>
<textarea name="obs_cliente" style="height: 100px; line-height: inherit; resize: none; margin: 10px 0 0 0;" class="form-control"><?php echo $pedido["obs_cliente"]; ?></textarea></td>
</tr>

<tr>
<td><b>Observações comerciais:</b>
<textarea name="obs_comerciais" style="height: 100px; line-height: inherit; resize: none; margin: 10px 0 0 0;" class="form-control"><?php echo $pedido["obs_comerciais"]; ?></textarea></td>
</tr>
</table>

</div>
</div>

<br>
<br>
<br>

<center>
Gerar revisão? 
<label><input type="radio" name="gera_revisao" value="sim" class="input" style="margin:0 0 0 15px;"> SIM</label>
<img src="images/alpha_dot.png" width="5" height="1" border="0">
<label><input type="radio" name="gera_revisao" value="nao" CHECKED class="input" style="margin:0 0 0 15px;"> NÃO</label>

<img src="images/alpha_dot.png" width="30" height="1" border="0">

<input type="submit" value="ATUALIZAR VALORES" class="btn btn-success">
<input type="button" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $pedido['pedido']); ?>'" value="RESUMO DO ORÇAMENTO" class="btn btn-primary">
<input type="button" onclick="window.location='?ped=<?php echo sprintf('%05d', $pedido['pedido']); ?>'" value="EDITAR ORÇAMENTO" class="btn btn-warning">
<a class="btn btn-dark" href="pedidos.php">VOLTAR</a>

</center>
</form>

<br>

<?php    


} elseif($_GET["acao"] == "altera") {

/*
if (!mysql_ping($conn)) {

	mysqli_close($conn);
	$conn = mysqli_connect($host,$username,$password,$dbname);
	mysqli_select_db('bonsuces',$conn);
}
*/

//echo "ALTERAR ORÇAMENTO<br><br><br>";

if ($plastificado == "on") { $plastificado = "1"; }
if ($reforco_vao_livre == "on") { $reforco_vao_livre = "1"; }
if ($reforco_fixacao == "on") { $reforco_fixacao = "1"; }
if ($alca_dupla == "on") { $alca_dupla = "1"; }


$revisao = $_POST["revisao"];
$revisao_valor = $_POST["revisao_valor"];


/*
if ($_POST["gera_revisao"] == "sim") {
    $rev_old = $revisao;
    $revisao = $revisao + 1;
	$revisao_valor = $revisao_valor + 1;
} elseif ($_POST["gera_revisao"] == "nao") {
	$revisao_valor = $revisao_valor + 1;
}


echo $revisao_valor;
echo "<pre>";
print_r($_POST);
echo "</pre>";
die();
*/

if ($_POST["gera_revisao"] == "sim") {
    $rev_old = $revisao;
    $revisao = $revisao + 1;
	$revisao_valor = $revisao_valor + 1;
	include 'gera_valores.php';
} elseif ($_POST["gera_revisao"] == "nao") {
	$revisao_valor = $revisao_valor + 1;
	include 'gera_valores.php';
}

//echo $sql_revisao;

/*
    $query_detalhes = mysqli_query($conn,"SELECT * FROM `pedidos_det` WHERE `revisao_valor` LIKE (SELECT MAX(`revisao_valor`) FROM `pedidos_det` WHERE `pedido` LIKE '".$pedido."') AND `pedido` LIKE '".$pedido."' AND `revisao` LIKE '".$rev_old."'");
    while ($row_pedidos_det = mysqli_fetch_array($query_detalhes)){
    	$sql_revisao_det = "INSERT INTO `pedidos_det` (`id`, `pedido`, `revisao`, `nivel`, `desc`, `valor_kg`, `qtde_mat`, `gramat`, `valor`, `m_quadrado`, `revisao_valor`) VALUES (NULL, '".$row_pedidos_det["pedido"]."', '".$revisao."', '".$row_pedidos_det["nivel"]."', '".$row_pedidos_det["desc"]."', '".$row_pedidos_det["valor_kg"]."', '".$row_pedidos_det["qtde_mat"]."', '".$row_pedidos_det["gramat"]."', '".$row_pedidos_det["valor"]."', '".$row_pedidos_det["m_quadrado"]."', '".$row_pedidos_det["revisao_valor"]."');";
        $gera_revisao_det = mysqli_query( $conn, $sql_revisao_det );
        if(! $gera_revisao_det ) { die('Não foi possível atualizar informações do orçamento: ' . mysqli_error()); }
    }
*/


/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
*/

} elseif($_GET["acao"] == "deletar") {

	$id_pedido = $_GET["id"];
	$pedido = $_GET["pedido"];

	$sql_envia_correcao = "UPDATE `pedidos` SET `status` = '0' WHERE `pedidos`.`pedido` = '".$pedido."'";

	$envia_correcao = mysqli_query( $conn, $sql_envia_correcao );
	if(! $envia_correcao ) { die('Não foi possível remover orçamento: ' . mysqli_error()); }

Log_Sis($pedido,$_SESSION['user']['id'],$_SESSION['user']['nome'],"Removeu o orcamento: ".sprintf('%05d', $pedido).".");

    redirect("pedidos.php"); 
    die("Redirecionando..."); 

} elseif($_GET["acao"] == "aprova") {

	$id_pedido = $_GET["id"];
	$pedido = $_GET["pedido"];

	$sql_envia_correcao = "UPDATE `pedidos` SET `status` = '4' WHERE `pedidos`.`id` = '".$id_pedido."' LIMIT 1";

	$envia_correcao = mysqli_query( $conn, $sql_envia_correcao );
	if(! $envia_correcao ) { die('Não foi possível enviar correções do orçamento: ' . mysqli_error()); }

Log_Sis($pedido,$_SESSION['user']['id'],$_SESSION['user']['nome'],"Aprovou o orcamento: ".sprintf('%05d', $pedido).".");

    redirect("pedidos.php"); 
    die("Redirecionando..."); 

} else {

if ($_GET["ordem"] != "") {
	$ordem = $_GET["ordem"];
} else {
	$ordem = "pedido";
}

if ($_GET["ascdesc"] != "") {
	$ascdesc = $_GET["ascdesc"];
} else {
	$ascdesc = "desc";
}

if ($_GET["qtde"] != "") {
	$limit = $_GET["qtde"];
} else {
	$limit = 15;
}

if ($_GET["num_orc"] != "") {

	$nums_orcs = explode(" ", $_GET["num_orc"]);

	$count_orc = 1;
	$quant_orc = count($nums_orcs);
	foreach ($nums_orcs as $value) {
		if ($count_orc != $quant_orc) {
			$numero_orc .= "`pedido` LIKE '".$value."' OR ";
		} else {
			$numero_orc .= "`pedido` LIKE '".$value."'";
		}
		$count_orc += 1;
	}
	unset($value);


	if ($_GET["fornec"] != "" AND $_GET["seg"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND ".$numero_orc." AND `segmento_cliente` LIKE '".$_GET["seg"]."' AND `fornecedora` LIKE '".$_GET["fornec"]."' AND `status` NOT LIKE '0'";
	} elseif ($_GET["seg"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND ".$numero_orc." AND `segmento_cliente` LIKE '".$_GET["seg"]."' AND `status` NOT LIKE '0'";
	} elseif ($_GET["fornec"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND ".$numero_orc." AND `fornecedora` LIKE '".$_GET["fornec"]."' AND `status` NOT LIKE '0'";
	} elseif ($_GET["ref"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND ".$numero_orc." AND `referencia` LIKE '%".$_GET["ref"]."%' AND `status` NOT LIKE '0'";
	} else {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND ".$numero_orc." AND `status` NOT LIKE '0'";
	}

/*
	if ($_GET["fornec"] != "" AND $_GET["seg"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `pedido` LIKE '".$_GET["num_orc"]."' AND `segmento_cliente` LIKE '".$_GET["seg"]."' AND `fornecedora` LIKE '".$_GET["fornec"]."' AND `status` NOT LIKE '0'";
	} elseif ($_GET["seg"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `pedido` LIKE '".$_GET["num_orc"]."' AND `segmento_cliente` LIKE '".$_GET["seg"]."' AND `status` NOT LIKE '0'";
	} elseif ($_GET["fornec"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `pedido` LIKE '".$_GET["num_orc"]."' AND `fornecedora` LIKE '".$_GET["fornec"]."' AND `status` NOT LIKE '0'";
	} else {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `pedido` LIKE '".$_GET["num_orc"]."' AND `status` NOT LIKE '0'";
	}
*/

} else {
	if ($_GET["fornec"] != "" AND $_GET["seg"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `pedido` LIKE '%".$_GET["num_orc"]."%' AND `segmento_cliente` LIKE '".$_GET["seg"]."' AND `fornecedora` LIKE '".$_GET["fornec"]."' AND `status` NOT LIKE '0'";
	} elseif ($_GET["seg"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `pedido` LIKE '%".$_GET["num_orc"]."%' AND `segmento_cliente` LIKE '".$_GET["seg"]."' AND `status` NOT LIKE '0'";
	} elseif ($_GET["fornec"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `pedido` LIKE '%".$_GET["num_orc"]."%' AND `fornecedora` LIKE '".$_GET["fornec"]."' AND `status` NOT LIKE '0'";
	} elseif ($_GET["ref"] != "") {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `pedido` LIKE '%".$_GET["num_orc"]."%' AND `referencia` LIKE '%".$_GET["ref"]."%' AND `status` NOT LIKE '0'";
	} else {
		$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `pedido` LIKE '%".$_GET["num_orc"]."%' AND `status` NOT LIKE '0'";
	}
}

?>
<style>
.venc_prox {
	color:#eaae21 !important;
}
.vencido {
	color:#d9534f !important;
}
</style>
<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-list"></i> Lista de orçamentos <small>Total encontrado: <?php

if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
	$query5 = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos ".$pesquisa." ORDER BY ".$ordem." ".$ascdesc;
} elseif ($_SESSION['user']['nivel'] == '4') {
	$query5 = "SELECT COUNT(pp.`id`) as `num` FROM `pedidos` pp INNER JOIN (SELECT MAX(`id`) AS `id`,`pedido` FROM `pedidos` ".$pesquisa." GROUP BY `pedido`) groupedpp ON pp.pedido = groupedpp.pedido AND pp.id = groupedpp.id AND `status` LIKE '4'";
} elseif ($_SESSION['user']['nivel'] == '5') {
	$query5 = "SELECT COUNT(pp.`id`) as `num` FROM `pedidos` pp INNER JOIN (SELECT MAX(`id`) AS `id`,`pedido` FROM `pedidos` ".$pesquisa." GROUP BY `pedido`) groupedpp ON pp.pedido = groupedpp.pedido AND pp.id = groupedpp.id AND `status` LIKE '2'";
} else {
	$pesquisa .= " AND `id_vend` LIKE '".$_SESSION['user']['id']."'";
	$query5 = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos ".$pesquisa." ORDER BY ".$ordem." ".$ascdesc;
}
    $row5 = mysqli_fetch_array(mysqli_query($conn,$query5));
    $total5 = $row5['num'];

 echo $total5; ?></small></h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<form action="pedidos.php" name="pedidos_filtro" method="GET">
<table border="0" width="100%" align="center" class="table jambo_table" style="border:0;">
	<thead>
	<tr class="headings">
		<th class="column-title" align="center">Cliente: <input type="text" class="form-control" name="pesq" id="pesq" <?php if ($_GET["pesq"] != "") { ?>value="<?php echo $_GET["pesq"]; ?>"<?php } ?>>
		</th>
		<th class="column-title" align="center">Referência: <input type="text" class="form-control" name="ref" id="ref" <?php if ($_GET["ref"] != "") { ?>value="<?php echo $_GET["ref"]; ?>"<?php } ?>>
		</th>
		<th class="column-title" align="center">Orçamento: <input type="text" class="form-control" name="num_orc" id="num_orc" <?php if ($_GET["num_orc"] != "") { ?>value="<?php echo $_GET["num_orc"]; ?>"<?php } ?>>
		</th>
		<th class="column-title sumir_coluna" align="center">Segmento: <select class="form-control" name="seg" id="seg" onchange="form.submit();">
		    <option<?php if ($_GET["seg"] == "") { echo " selected"; } ?> value="">Todos</option>
		    <option<?php if ($_GET["seg"] == 1) { echo " selected"; } ?> value="1">Alimentício (padrão exportação)</option>
		    <option<?php if ($_GET["seg"] == 2) { echo " selected"; } ?> value="2">Café</option>
		    <option<?php if ($_GET["seg"] == 3) { echo " selected"; } ?> value="3">Carga perigosa</option>
		    <option<?php if ($_GET["seg"] == 4) { echo " selected"; } ?> value="4">Exportação</option>
			<option<?php if ($_GET["seg"] == 5) { echo " selected"; } ?> value="5">Fertilizante</option>
			<option<?php if ($_GET["seg"] == 6) { echo " selected"; } ?> value="6">Grãos / sementes</option>
			<option<?php if ($_GET["seg"] == 7) { echo " selected"; } ?> value="7">Minérios</option>
			<option<?php if ($_GET["seg"] == 8) { echo " selected"; } ?> value="8">Petroquímico</option>
			<option<?php if ($_GET["seg"] == 9) { echo " selected"; } ?> value="9">Químico</option>
			<option<?php if ($_GET["seg"] == 10) { echo " selected"; } ?> value="10">Usinas</option>
			<option<?php if ($_GET["seg"] == 11) { echo " selected"; } ?> value="11">Diversos</option>
			</select>
		</th>
		<th class="column-title sumir_coluna" align="center">Fornecedora: <select class="form-control" name="fornec" id="fornec" onchange="form.submit();">
		    <option<?php if ($_GET["fornec"] == "") { echo " selected"; } ?> value="">Todas</option>
<?php
$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora`");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
?>
			<option<?php if ($_GET["fornec"] == "valor_".strtolower($row_fornec['sigla'])) { echo " selected"; } ?> value="valor_<? echo strtolower($row_fornec['sigla']);?>"><? echo $row_fornec['apelido']; ?></option>
<?
}

?>
			</select>
		</th>
		<th class="column-title sumir_coluna" align="center">Qtde.: <select class="form-control" style="min-width: 50px;" name="qtde" id="qtde" onchange="form.submit();">
			<option<?php if ($limit == 1) { echo " selected"; } ?>>1</option>
			<option<?php if ($limit == 15) { echo " selected"; } ?>>15</option>
			<option<?php if ($limit == 30) { echo " selected"; } ?>>30</option>
			<option<?php if ($limit == 50) { echo " selected"; } ?>>50</option>
			<option<?php if ($limit == 100) { echo " selected"; } ?>>100</option>
			<option<?php if ($limit == 200) { echo " selected"; } ?>>200</option>
			</select>
		</th>
		<th class="column-title" align="center" valign="bottom"><input type="submit" class="btn btn-default" style="margin-bottom:0;" value="OK"></th>
	</tr>
	</thead>
</table>

<table border="0" width="100%" align="center" class="table table-striped">
<tr>
<?php if ($_SESSION['user']['nivel'] != "3") { ?>
<td style="width: 28px;"><input type="checkbox" id="selectall" style="width:14px;"></td>
<?php } ?>
<td style="width: 28px;"><b><a href="#" onclick="document.pedidos_filtro.ordem.value='mercado'; document.pedidos_filtro.ascdesc.value='<?php if ($_GET["ascdesc"] == "asc") { echo "desc"; } else { echo "asc"; } ?>'; document.pedidos_filtro.submit();" title="Exportação"><i class="fa fa-star"></i></a></b></td>
<td style="width: 55px;" align="center"><b><a href="#" onclick="document.pedidos_filtro.ordem.value='pedido'; document.pedidos_filtro.ascdesc.value='<?php if ($_GET["ascdesc"] == "asc") { echo "desc"; } else { echo "asc"; } ?>'; document.pedidos_filtro.submit();" title="Número do orçamento">N°</a></b></td>
<td><b><a href="#" onclick="document.pedidos_filtro.ordem.value='nome_cliente'; document.pedidos_filtro.ascdesc.value='<?php if ($_GET["ascdesc"] == "asc") { echo "desc"; } else { echo "asc"; } ?>'; document.pedidos_filtro.submit();" title="Cliente">Cliente</a></b></td>
<td class="sumir_coluna"><b><a href="#" onclick="document.pedidos_filtro.ordem.value='referencia'; document.pedidos_filtro.ascdesc.value='<?php if ($_GET["ascdesc"] == "asc") { echo "desc"; } else { echo "asc"; } ?>'; document.pedidos_filtro.submit();" title="Referência">Ref.</a></b></td>
<td class="sumir_coluna sumir_coluna1"><b><a href="#" onclick="document.pedidos_filtro.ordem.value='segmento_cliente'; document.pedidos_filtro.ascdesc.value='<?php if ($_GET["ascdesc"] == "asc") { echo "desc"; } else { echo "asc"; } ?>'; document.pedidos_filtro.submit();" title="Segmento">Segmento</a></b></td>
<td class="sumir_coluna"><b><a href="#" onclick="document.pedidos_filtro.ordem.value='qtde'; document.pedidos_filtro.ascdesc.value='<?php if ($_GET["ascdesc"] == "asc") { echo "desc"; } else { echo "asc"; } ?>'; document.pedidos_filtro.submit();" title="Quantidade">Qtde.</a></b></td>
<td class="sumir_coluna sumir_coluna1"><b><a href="#" onclick="document.pedidos_filtro.ordem.value='fornecedora'; document.pedidos_filtro.ascdesc.value='<?php if ($_GET["ascdesc"] == "asc") { echo "desc"; } else { echo "asc"; } ?>'; document.pedidos_filtro.submit();" title="Fornecedora">Fornecedora</a></b></td>
<td class="sumir_coluna"><b><a href="#" onclick="document.pedidos_filtro.ordem.value='representante'; document.pedidos_filtro.ascdesc.value='<?php if ($_GET["ascdesc"] == "asc") { echo "desc"; } else { echo "asc"; } ?>'; document.pedidos_filtro.submit();" title="Representante">Representante</a></b></td>
<td class="sumir_coluna"><b><a href="#" onclick="document.pedidos_filtro.ordem.value='data'; document.pedidos_filtro.ascdesc.value='<?php if ($_GET["ascdesc"] == "asc") { echo "desc"; } else { echo "asc"; } ?>'; document.pedidos_filtro.submit();" title="Data">Data</a></b></td>
<?php if ($_SESSION['user']['nivel'] == '3') { ?>
<td class="sumir_coluna" title="Vencimento"><b>Venc.</b></td>
<?php } ?>
<td style="width: 170px;" class="sumir_coluna" align="center" title="Ações"><b>Ações</b></td>
<td style="width: 58px;"><b><a href="#" onclick="document.pedidos_filtro.ordem.value='status'; document.pedidos_filtro.ascdesc.value='<?php if ($_GET["ascdesc"] == "asc") { echo "desc"; } else { echo "asc"; } ?>'; document.pedidos_filtro.submit();" title="Status">Status</a></b></td>
<?php
/*  ******** REMOVER PEDIDO *********** */
if ($_SESSION['user']['nivel'] == '1') {
?>
<td class="sumir_coluna" style="width: 37px;" align="center"><b></b></td>
<?php
}
/* ********* REMOVER PEDIDO *********** */
?>
</tr>
<input name="ordem" type="hidden" value="<?php if ($_GET["ordem"] != "") { echo $_GET["ordem"]; } ?>">
<input name="ascdesc" type="hidden" value="<?php if ($_GET["ascdesc"] != "") { echo $_GET["ascdesc"]; } ?>">

</form>

<form name="imprimir" action="imprimir.php" method="post" target="_blank">

<?php

// $$$$$$$$$$$$$$$$$$$$$$$ ROTATIVO $$$$$$$$$$$$$$$$$$$$$$$$$$$$$



if ($_GET["ordem"] != "") {
	$ordem = $_GET["ordem"];
} else {
	$ordem = "pedido";
}

if ($_GET["ascdesc"] != "") {
	$ascdesc = $_GET["ascdesc"];
} else {
	$ascdesc = "desc";
}

if ($_GET["qtde"] != "") {
	$limit = $_GET["qtde"];
} else {
	$limit = 15;
}


$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;


$per_page = (int)(!isset($_GET["qtde"]) ? 15 : $_GET["qtde"]); // 10; // Set how many records do you want to display per page.
 
$startpoint = ($page * $per_page) - $per_page;
 
/*
$statement = "`pedidos` p ".$pesquisa." AND id = (SELECT max(id) FROM pedidos p2 WHERE p2.pedido = p.pedido) ORDER BY `{$ordem}` {$ascdesc}"; // Change `records` according to your table name.   "SELECT * FROM `log_acesso` ".$pesquisa." ORDER BY `id` DESC LIMIT ".$limit
$results = mysqli_query($conn,"SELECT * FROM {$statement} LIMIT {$startpoint} , {$per_page}");
*/

if ($_SESSION['user']['nivel'] == '4') {
	$query6 = "SELECT pp.`id`,pp.`pedido`,`nome_cliente`,`segmento_cliente`,`qtde`,`referencia`,`mercado`,`fornecedora`,`representante`,`data`,`status` FROM `pedidos` pp INNER JOIN (SELECT MAX(`id`) AS `id`,`pedido` FROM `pedidos` ".$pesquisa." GROUP BY `pedido`) groupedpp ON pp.pedido = groupedpp.pedido AND pp.id = groupedpp.id AND `status` LIKE '4' ORDER BY ".$ordem." ".$ascdesc." LIMIT {$startpoint} , {$per_page}";
} elseif ($_SESSION['user']['nivel'] == '5') {
	$query6 = "SELECT pp.`id`,pp.`pedido`,`nome_cliente`,`segmento_cliente`,`qtde`,`referencia`,`mercado`,`fornecedora`,`representante`,`data`,`status` FROM `pedidos` pp INNER JOIN (SELECT MAX(`id`) AS `id`,`pedido` FROM `pedidos` ".$pesquisa." GROUP BY `pedido`) groupedpp ON pp.pedido = groupedpp.pedido AND pp.id = groupedpp.id AND `status` LIKE '2' ORDER BY ".$ordem." ".$ascdesc." LIMIT {$startpoint} , {$per_page}";
} else {
	$query6 = "SELECT pp.`id`,pp.`pedido`,`nome_cliente`,`segmento_cliente`,`qtde`,`referencia`,`mercado`,`fornecedora`,`representante`,`data`,`status` FROM `pedidos` pp INNER JOIN (SELECT MAX(`id`) AS `id`,`pedido` FROM `pedidos` ".$pesquisa." GROUP BY `pedido`) groupedpp ON pp.pedido = groupedpp.pedido AND pp.id = groupedpp.id ORDER BY ".$ordem." ".$ascdesc." LIMIT {$startpoint} , {$per_page}";
}

//echo $query6;

$results = mysqli_query($conn,$query6);

/*
//echo $query6;

$count_ped_sel = 1;
$quant_ped_sel = mysqli_num_rows($results6);

while ($ids_orcs = mysqli_fetch_array($results6)) {
	if ($count_ped_sel != $quant_ped_sel) {
		$pedidos_sel .= "`id` LIKE '".$ids_orcs["id"]."' OR";
	} else {
		$pedidos_sel .= "`id` LIKE '".$ids_orcs["id"]."'";
	}
	$count_ped_sel += 1;
}

if ($pedidos_sel == "") {
	$pedidos_sel = "`id` LIKE '0'";
}

$results = mysqli_query($conn,"SELECT * FROM `pedidos` WHERE {$pedidos_sel} ORDER BY ".$ordem." ".$ascdesc);
*/

if (mysqli_num_rows($results) != 0) {

    while ($row_pedidos = mysqli_fetch_array($results)) {


$query_segmento = mysqli_query($conn,"SELECT segmento FROM segmentos WHERE id LIKE ".$row_pedidos['segmento_cliente']);
$row_segmento = mysqli_fetch_array($query_segmento);
$segmento_cliente = $row_segmento["segmento"];

$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora` WHERE sigla = '".strtoupper(substr($row_pedidos["fornecedora"],6,2))."'");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
	$fornecedora = $row_fornec['apelido'];
}

$segmento_cliente = $row_segmento["segmento"];

$representante = $row_pedidos['representante'];

$data_ped = $row_pedidos['data'];
$data_ped = strtotime($data_ped);
//$data_ped = DateTime::createFromFormat('Y-m-d', $data_ped);
//strtotime($row_pedidos['data']);
$hora_ped = date("H:i:s", $data_ped);
$data_ped = date("d/m/Y", $data_ped);

if ($row_pedidos["status"] == "1") { $status_txt = "Em análise";
} elseif ($row_pedidos["status"] == "2") { $status_txt = "Aguardando liberação";
} elseif ($row_pedidos["status"] == "3") { $status_txt = "Liberado";
} elseif ($row_pedidos["status"] == "4") { $status_txt = "Aprovado";
} elseif ($row_pedidos["status"] == "5") { $status_txt = "Recusado";
} elseif ($row_pedidos["status"] == "6") { $status_txt = "Aguardando qualidade";
} elseif ($row_pedidos["status"] == "7") { $status_txt = "Aprovado pela qualidade";
}

//  onmouseover="ChangeBackgroundColor(this)" onmouseout="RestoreBackgroundColor(this)"
?>
	<tr>
<?php if ($_SESSION['user']['nivel'] != "3") { ?>
		<td><input type="checkbox" class="selectPrint" name="pedido[]" value="<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>" onclick="resetSelectAll();" style="width:14px;"></td>
<?php } ?>
		<td><?php if($row_pedidos['mercado'] == "ext") { echo "<i class=\"fa fa-star\"></i>"; } ?></td>
		<?php if ($_SESSION['user']['nivel'] == "3") { ?>
			<td style="padding-left:5px;"><?php echo sprintf('%05d', $row_pedidos['pedido']); ?></td>
		<?php } else { ?>
			<td style="padding-left:5px;"><a href="resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>"><?php echo sprintf('%05d', $row_pedidos['pedido']); ?></a></td>
		<?php } ?>
		<td><?php echo $row_pedidos['nome_cliente']; ?></td>
		<td class="sumir_coluna"><?php echo $row_pedidos['referencia']; ?></td>
		<td class="sumir_coluna sumir_coluna1"><?php echo $segmento_cliente; ?></td>
		<td class="sumir_coluna"><?php echo number_format((float)$row_pedidos['qtde'], 0, ',', '.'); ?></td>
		<td class="sumir_coluna sumir_coluna1"><?php echo $fornecedora; ?></td>
		<td class="sumir_coluna"><?php echo $representante; ?></td>
<?php
if ($_SESSION['user']['nivel'] == "3") {
	$query_repres = mysqli_query($conn,"SELECT * FROM `pedidos_repres` WHERE `pedido` = '".$row_pedidos['pedido']."'");
	$repres = mysqli_fetch_array($query_repres);
	if ($repres["status"] == 1) {
?>
		<td class="sumir_coluna"><?php echo date('d/m/Y', strtotime($repres["data_criacao"])); ?></td>
		<td></td>
<?php
/* <td class="sumir_coluna"><?php echo date('d/m/Y', strtotime("+5 days",strtotime($row_pedidos['data']))); ?></td> */
	} elseif ($repres["status"] == 2 || $repres["status"] == 3 || $repres["status"] == 5 || $repres["status"] == 6 || $repres["status"] == 7) {
?>
		<td class="sumir_coluna"><?php echo date('d/m/Y', strtotime($repres["data_liberacao"])); ?></td>
<?php		if ($repres["data_venc2"] != "" && $row_pedidos["status"] != "4") { ?>
		<td class="sumir_coluna<?php if(strtotime($repres["data_venc2"]) == strtotime(date("Y-m-d")."+1 day")) { echo " venc_prox"; } elseif(strtotime($repres["data_venc2"]) <= strtotime(date("Y-m-d"))) { echo " vencido"; } ?>"><?php echo date('d/m/Y', strtotime($repres["data_venc2"])); ?></td>
<?php		} elseif ($repres["data_venc1"] != "" && $row_pedidos["status"] != "4") { ?>
		<td class="sumir_coluna<?php if(strtotime($repres["data_venc1"]) == strtotime(date("Y-m-d")."+1 day")) { echo " venc_prox"; } elseif(strtotime($repres["data_venc1"]) <= strtotime(date("Y-m-d"))) { echo " vencido"; } ?>"><?php echo date('d/m/Y', strtotime($repres["data_venc1"])); ?></td>
<?php		} else { ?>
		<td></td>
<?php
		}
/* <td class="sumir_coluna"><?php echo date('d/m/Y', strtotime("+5 days",strtotime($row_pedidos['data']))); ?></td> */
	} else {
?>
		<td></td>
		<td></td>
<?php
	}
} else {

?>
		<td class="sumir_coluna" title="<?php echo $hora_ped; ?>"><?php echo $data_ped; ?></td>
<?php
}
?>
		<td class="sumir_coluna" align="center"><?php
if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
	if ($row_pedidos['status'] == "1") {
		?><button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Analisar" class="btn  btn-warning btn-xs tooltips" onclick="window.location='pedidos.php?acao=analisar&pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-search"></i> </button>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Editar" class="btn  btn-primary btn-xs tooltips" onclick="window.location='pedidos.php?ped=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-pencil"></i> </button>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Resumo" class="btn  btn-info btn-xs tooltips" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Duplicar" class="btn  btn-dark btn-xs tooltips" onclick="window.location='duplicar.php?id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-copy"></i> </button>
<?php
	} elseif ($row_pedidos['status'] == "2") {
?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Resumo" class="btn  btn-info btn-xs tooltips" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
<?php
if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Liberar" class="btn  btn-success btn-xs tooltips" onclick="window.location='resumo.php?acao=libera&pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>&id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-arrow-up"></i> </button>
<?php
}
?>
<? /*		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar PDF do resumo" class="btn  btn-danger btn-xs tooltips" href="pdf_resumo.php?pedido=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a> */ ?>
<?php
	} elseif ($row_pedidos['status'] == "3") {
?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Resumo" class="btn  btn-info btn-xs tooltips" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar PDF do orçamento" class="btn  btn-danger btn-xs tooltips" href="gera_pdf.php?ped=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a>
<?php /*		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Orçamento - Simples Nacional" class="btn  btn-warning btn-xs tooltips" href="gera_pdf_simples.php?ped=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a> */ ?>
<?php /*		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Aprovar orçamento" class="btn  btn-success btn-xs tooltips" onclick="window.location='pedidos.php?acao=aprova&pedido=<?php echo $row_pedidos['pedido']; ?>&id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-check"></i> </button> */ ?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Retornar análise" class="btn  btn-warning btn-xs tooltips" onclick="window.location='resumo.php?acao=reativa&pedido=<?php echo $row_pedidos['pedido']; ?>&id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-refresh"></i> </button>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Recusar orçamento" class="btn  btn-danger btn-xs tooltips" onclick="window.location='resumo.php?acao=recusa&pedido=<?php echo $row_pedidos['pedido']; ?>&id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-close"></i> </button>
<?php
	} elseif ($row_pedidos['status'] == "4") {
?>
<? /*		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar PDF do resumo" class="btn  btn-danger btn-xs tooltips" href="pdf_resumo.php?pedido=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a> */ ?>
		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar PDF do orçamento" class="btn  btn-danger btn-xs tooltips" href="gera_pdf.php?ped=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Resumo" class="btn  btn-info btn-xs tooltips" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
<?php
	} elseif ($row_pedidos['status'] == "5") {
?>
		<a href="resumo.php?acao=reativa&pedido=<?php echo $row_pedidos['pedido']; ?>&id=<?php echo $row_pedidos['id']; ?>">reativar</a>
<?php
	} elseif ($row_pedidos['status'] == "6") {
?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Resumo" class="btn  btn-info btn-xs tooltips" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
<?php
	} elseif ($row_pedidos['status'] == "7") {
?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Resumo" class="btn  btn-info btn-xs tooltips" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
<?php
	} 
} elseif ($_SESSION['user']['nivel'] == '3') {
	if($repres["data_venc2"]!="") { $vencimento = $repres["data_venc2"]; } elseif($repres["data_venc1"]!="") { $vencimento = $repres["data_venc1"]; } else { $vencimento = "2999-12-30"; }
	if(strtotime($vencimento) == strtotime(date("Y-m-d"))) {
		if($repres["status"] == 5) {
?>
		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Prazo limite vencido" class="btn  btn-danger btn-xs tooltips"><i class="fa fa-calendar-times-o"></i> Vencido</a>
<?php
		} elseif($repres["status"] == 6) {
?>
		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Orçamento recusado" class="btn  btn-danger btn-xs tooltips"><i class="fa fa-times"></i> Recusado</a>
<?php
		} else {
?>
		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Solicite reativamento" class="btn  btn-warning btn-xs tooltips" href="repres.php?acao=reatv&ped=<?php echo $row_pedidos['pedido']; ?>"><i class="fa fa-calendar-minus-o"></i> Vencido</a>
<?php
		}
	} elseif(strtotime($vencimento) < strtotime(date("Y-m-d"))) {
?>
		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Prazo limite vencido" class="btn  btn-danger btn-xs tooltips"><i class="fa fa-calendar-times-o"></i> Vencido</a>
<?php
	} else {
		if($repres["status"] == 6) {
?>
		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Orçamento recusado" class="btn  btn-danger btn-xs tooltips"><i class="fa fa-times"></i> Recusado</a>
<?php
		} else {

	if ($row_pedidos['status'] == "1"|| $row_pedidos['status'] == "2") {
?>
		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Aguarde a liberação do orçamento" class="btn  btn-info btn-xs tooltips"><i class="fa fa-hourglass-half"></i> Aguarde</a>
<?php
		/*?><img src="images/aguarde.png" border="0" width="25" height="25" style="opacity: 0.6; filter: alpha(opacity=60); padding:0;" alt="Aguarde a liberação do orçamento" title="Aguarde a liberação do orçamento"><?php*/
	} elseif ($row_pedidos['status'] == "3" || $row_pedidos['status'] == "4" || $row_pedidos['status'] == "5") {
		if ($row_pedidos['status'] == "4") {
		?>
<? /*		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar PDF do orçamento" class="btn  btn-danger btn-xs tooltips" href="gera_pdf.php?ped=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a> */ ?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Duplicar orçamento" class="btn  btn-dark btn-xs tooltips" onclick="window.location='duplicar.php?id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-copy"></i> </button>
        <?php } elseif ($row_pedidos['status'] == "3") { ?>
<? /*		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar PDF do orçamento" class="btn  btn-danger btn-xs tooltips" href="gera_pdf.php?ped=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a> */ ?>
		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar orçamento em ESPANHOL" class="btn  btn-primary btn-xs tooltips" href="gera_pdf_es.php?ped=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Aprovar orçamento" class="btn  btn-success btn-xs tooltips" onclick="window.location='pedidos.php?acao=aprova&pedido=<?php echo $row_pedidos['pedido']; ?>&id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-check"></i> </button>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Recusar orçamento" class="btn  btn-danger btn-xs tooltips" onclick="window.location='resumo.php?acao=recusa&pedido=<?php echo $row_pedidos['pedido']; ?>&id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-close"></i> </button>
        <?php } elseif ($row_pedidos['status'] == "5") { ?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Duplicar orçamento" class="btn  btn-dark btn-xs tooltips" onclick="window.location='duplicar.php?id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-copy"></i> </button>
        <?php
        }
	}
	}
	}
} elseif ($_SESSION['user']['nivel'] == '4') {
	?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Gerar FT" class="btn  btn-dark btn-xs tooltips" onclick="window.location='qualidade.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
	<?php
} elseif ($_SESSION['user']['nivel'] == '5') {
	?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Resumo" class="btn  btn-info btn-xs tooltips" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
	<?php
}
		?></td>
		<td align="center"><img src="images/icons/status<?php echo $row_pedidos['status']; ?>.png" alt="<?php echo $status_txt; ?>" title="<?php echo $status_txt; ?>"></td>
<?php
if ($_SESSION['user']['nivel'] == '1') {
?>
		<td class="sumir_coluna" align="center">
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Remover orçamento" class="btn  btn-default btn-xs tooltips" style="opacity:0.3;" onclick="window.location='pedidos.php?acao=deletar&pedido=<?php echo $row_pedidos['pedido']; ?>&id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-trash"></i> </button></td>
<?php
}
?>
	</tr>
<?php
}
  
} else {
?>
<tr>
	<td colspan="13" align="center"><br>Nenhum registro encontrado.<br><br></td>
</tr>
<?php
}
?>
</table>

<center>

<?php
if ($_SESSION['user']['nivel'] != '4' && $_SESSION['user']['nivel'] != '3') {
?>
<div class="orcamento">
<? /* <input type="submit" value="&#xf02f; IMPRIMIR SELECIONADOS " class="btn btn-round btn-default source" style="font-family: 'FontAwesome', Arial;"> */ ?>
</div>
<?php
}
?>
</form>


<?php
$seg = $_GET["seg"];
$pesq = $_GET["pesq"];
$fornec = $_GET["fornec"];

echo pagination($query,$per_page,$page,$pesquisa,$ordem,$ascdesc,$url='?pesq='.$pesq.'&num_orc='.$_GET["num_orc"].'&seg='.$seg.'&fornec='.$fornec.'&qtde='.$limit.'&ordem='.$ordem.'&ascdesc='.$ascdesc.'&');
?>
<br>
<div class="ln_solid"></div><center>
<br>
<a class="btn btn-app nolink"><img style="vertical-align: middle;" src="images/icons/status1.png" border="0"><br>Em análise</a>
<a class="btn btn-app nolink"><img style="vertical-align: middle;" src="images/icons/status2.png" border="0"><br>Aguardando liberação</a>
<a class="btn btn-app nolink"><img style="vertical-align: middle;" src="images/icons/status3.png" border="0"><br>Liberado</a>
<a class="btn btn-app nolink"><img style="vertical-align: middle;" src="images/icons/status4.png" border="0"><br>Aprovado</a>
<a class="btn btn-app nolink"><img style="vertical-align: middle;" src="images/icons/status5.png" border="0"><br>Recusado</a>
<a class="btn btn-app nolink"><img style="vertical-align: middle;" src="images/icons/status6.png" border="0"><br>Aguardando qualidade</a>
<a class="btn btn-app nolink"><img style="vertical-align: middle;" src="images/icons/status7.png" border="0"><br>Aprovado pela qualidade</a>

<?php } ?>
<br><br>
</center>


<script type="text/javascript">

<?php
if($_GET["acao"] == "analisar") {
?>

var lastValue = '';

function CalculaSubtotal() {
	var total = 0;
	$("input[name*='[valor]']").each(function(){
	
		var valor = Number($(this).val().toString().replace(/\,/g, '.'));

		if ($(this).is("#negativo")) {
			if (!isNaN(valor)) total - valor;
		} else {
			if (!isNaN(valor)) total += valor;
		}
	});

	$("#subtotal").val(total.toFixed(2).replace(/\./g, ','));
};

/*
function CalculaValor(reg) {
    $('input[name="campo[' + reg + '][valor_kg]"],input[name="campo[' + reg + '][qtde_mat]"],input[name="campo[' + reg + '][gramat]"]').on('change focus blur keyup keydown keypress paste mouseup', function() {
    if ($(this).val() != lastValue) {
    	lastValue = $(this).val();
        var str = 0;
        var valorkg = $('input[name="campo[' + reg + '][valor_kg]"]').val().toString().replace(/\,/g, '.');
        var qtdemat = $('input[name="campo[' + reg + '][qtde_mat]"]').val().toString().replace(/\,/g, '.');
        var gramat = $('input[name="campo[' + reg + '][gramat]"]').val().toString().replace(/\,/g, '.');
		var str = valorkg * qtdemat * gramat / 1000;
		var str = isNaN(str) ? 0 : str;
		$('input[name="campo[' + reg + '][valor]"]').val( parseFloat(str).toFixed(2).toString().replace(/\./g, ',') ); //.toFixed(2) parseFloat(str) || 0 ); // parseInt(str,10) || 0 );
    CalculaSubtotal();
    CalculaCusto();
    CalculaImpostos();
    CalculaValorFinal();
    CalculaValorDolar();
	}
    });
};
*/

function CalculaValor(reg) {

	var str = 0;
    $('input[class*="sub' + reg + ' form-control has-feedback-left"]').each(function() {
		var valor = Number($(this).val().toString().replace(/\,/g, '.'));
		if (!isNaN(valor)) str += valor;
		$('input[name="campo[' + reg + '][valor]"]').val( parseFloat(str).toFixed(2).toString().replace(/\./g, ',') );
    });
};

function CalculaMat(reg) {

	var str = 0;
    $('input[class*="submat' + reg + ' form-control has-feedback-right"]').each(function() {
		var valor = Number($(this).val().toString().replace(/\,/g, '.'));
		if (!isNaN(valor)) str += valor;
		$('input[name="campo[' + reg + '][qtde_mat]"]').val( parseFloat(str).toFixed(2).toString().replace(/\./g, ',') );
    });
};

function RecalculaLinha(reg,sub) {
    //$('input[name="campo[' + reg + '][qtde_mat]"],input[name="campo[' + reg + '][gramat]"]').on('change focus blur keyup keydown keypress paste mouseup', function() {
    //if ($(this).val() != lastValue) {
    	//lastValue = $(this).val();
        var str = 0;
        var gramat = $('input[name="campo[' + reg + '][gramat]"]').val().replace(/\,/g, '.');
		if ($('input[name="campo[' + reg + '][qtde]"]').val() != 0 && $('input[name="campo[' + reg + '][m_quadrado]"]').val() == 1) {
			var qtde = $('input[name="campo[' + reg + '][qtde]"]').val().replace(/\,/g, '.');
			var largura = $('input[name="campo[' + reg + '][largura]"]').val().replace(/\,/g, '.');
			var corte = $('input[name="campo[' + reg + '][corte]"]').val().replace(/\,/g, '.');
			var qtdemat = qtde*(largura/100)*(corte/100);
		} else if ($('input[name="campo[' + reg + '][qtde]"]').val() != 0 && $('input[name="campo[' + reg + '][m_quadrado]"]').val() == 0) {
			var qtde = $('input[name="campo[' + reg + '][qtde]"]').val().replace(/\,/g, '.');
			var largura = $('input[name="campo[' + reg + '][largura]"]').val().replace(/\,/g, '.');
			var corte = $('input[name="campo[' + reg + '][corte]"]').val().replace(/\,/g, '.');
			var qtdemat = qtde*(corte/100);
		} else {
			var qtdemat = $('input[name="campo[' + reg + '][qtde_mat]"]').val().replace(/\,/g, '.');
		}
		var str = qtdemat * gramat;
		var str = isNaN(str) ? 0 : str;
		$('input[name="campo[' + reg + '][qtde_mat]"]').val( parseFloat(qtdemat).toFixed(2).replace(/\./g, ',') ); //.toFixed(2) parseFloat(str) || 0 ); // parseInt(str,10) || 0 );
		$('input[name="campo[' + reg + '][peso]"]').val( parseFloat(str).toFixed(2).replace(/\./g, ',') ); //.toFixed(2) parseFloat(str) || 0 ); // parseInt(str,10) || 0 );
    CalculaLinha(reg);
    CalculaValor(sub);
    CalculaMat(sub);
    CalculaSubtotal();
    CalculaCusto();
    CalculaImpostos();
    CalculaValorFinal();
    CalculaValorDolar();
	//}
    //});
};

/*
function CalculaPeso(reg,sub) {
    //$('input[name="campo[' + reg + '][qtde_mat]"],input[name="campo[' + reg + '][gramat]"]').on('change focus blur keyup keydown keypress paste mouseup', function() {
    //if ($(this).val() != lastValue) {
    	//lastValue = $(this).val();
        var str = 0;
        var qtdemat = $('input[name="campo[' + reg + '][qtde_mat]"]').val().toString().replace(/\,/g, '.');
        var gramat = $('input[name="campo[' + reg + '][gramat]"]').val().toString().replace(/\,/g, '.');
		var str = qtdemat * gramat;
		var str = isNaN(str) ? 0 : str;
		$('input[name="campo[' + reg + '][peso]"]').val( parseFloat(str).toFixed(2).toString().replace(/\./g, ',') ); //.toFixed(2) parseFloat(str) || 0 ); // parseInt(str,10) || 0 );
    CalculaLinha(reg);
    CalculaValor(sub);
    CalculaSubtotal();
    CalculaCusto();
    CalculaImpostos();
    CalculaValorFinal();
    CalculaValorDolar();
	//}
    //});
};
*/

function CalculaLinha(reg) {
    //$('input[name="campo[' + reg + '][valor_kg]"],input[name="campo[' + reg + '][peso]"]').on('change focus blur keyup keydown keypress paste mouseup', function() {
    //if ($(this).val() != lastValue) {
    	//lastValue = $(this).val();
        var str = 0;
        var valorkg = $('input[name="campo[' + reg + '][valor_kg]"]').val().toString().replace(/\,/g, '.');
        var peso = $('input[name="campo[' + reg + '][peso]"]').val().toString().replace(/\,/g, '.');
		var str = valorkg * peso / 1000;
		var str = isNaN(str) ? 0 : str;
		$('input[name="campo[' + reg + '][valor]"]').val( parseFloat(str).toFixed(2).toString().replace(/\./g, ',') ); //.toFixed(2) parseFloat(str) || 0 ); // parseInt(str,10) || 0 );
    CalculaSubtotal();
    CalculaCusto();
    CalculaImpostos();
    CalculaValorFinal();
    CalculaValorDolar();
	//}
    //});
};

function CalculaCusto(){
	var custo = 0;
	var subtotal = Number($("#subtotal").val().toString().replace(/\,/g, '.'));
	var valor_mat_auxiliar = Number($("#valor_mat_auxiliar").val().toString().replace(/\,/g, '.'));
	var valor_cif = Number($("#valor_cif").val().toString().replace(/\,/g, '.'));
	var valor_mao_obra = Number($("#valor_mao_obra").val().toString().replace(/\,/g, '.'));

	var custo = (subtotal) + (valor_mat_auxiliar) + (valor_cif) + (valor_mao_obra);
	var custo = isNaN(custo) ? 0 : custo;
	$("#valor_custo_bag").val(custo.toFixed(2).replace(/\./g, ','));

    CalculaImpostos();
    CalculaValorFinal();
    CalculaValorDolar();

};


function CalculaImpostos(){
	var imposto_icms = Number($("#imposto_icms").val().toString().replace(/\,/g, '.'));
	var imposto_pis = Number($("#imposto_pis").val().toString().replace(/\,/g, '.'));
	var imposto_cofins = Number($("#imposto_cofins").val().toString().replace(/\,/g, '.'));
	var imposto_ir = Number($("#imposto_ir").val().toString().replace(/\,/g, '.'));
	var imposto_csll = Number($("#imposto_csll").val().toString().replace(/\,/g, '.'));
	var imposto_inss = Number($("#imposto_inss").val().toString().replace(/\,/g, '.'));
	var imposto_perda = Number($("#imposto_perda").val().toString().replace(/\,/g, '.'));
	var imposto_frete = Number($("#imposto_frete").val().toString().replace(/\,/g, '.'));
	var imposto_comissao = Number($("#imposto_comissao").val().toString().replace(/\,/g, '.'));
	var imposto_adm_comercial = Number($("#imposto_adm_comercial").val().toString().replace(/\,/g, '.'));
	var imposto_custo_fin = Number($("#imposto_custo_fin").val().toString().replace(/\,/g, '.'));
	var imposto_margem = Number($("#imposto_margem").val().toString().replace(/\,/g, '.'));

	var imposto = (imposto_icms) + (imposto_pis) + (imposto_cofins) + (imposto_ir) + (imposto_csll) + (imposto_inss) + (imposto_perda) + (imposto_frete) + (imposto_comissao) + (imposto_adm_comercial) + (imposto_custo_fin) + (imposto_margem);

	var imposto = isNaN(imposto) ? 0 : imposto;
	$("#imposto_total").val(imposto.toFixed(3).replace(/\./g, ','));

	var sem_imposto = (imposto_perda) + (imposto_frete) + (imposto_comissao) + (imposto_adm_comercial) + (imposto_custo_fin) + (imposto_margem);

	var sem_imposto = isNaN(sem_imposto) ? 0 : sem_imposto;
	$("#sem_imposto_total").val(sem_imposto.toFixed(3).replace(/\./g, ','));

    CalculaValorFinal();
    CalculaValorDolar();

};


function CalculaValorFinal(){
	var valor_custo = Number($("#valor_custo_bag").val().toString().replace(/\,/g, '.'));;
	var valor_imposto = Number($("#imposto_total").val().toString().replace(/\,/g, '.'));;
	var valor_sem_imposto = Number($("#sem_imposto_total").val().toString().replace(/\,/g, '.'));;

	var valor_final = 100 - (valor_imposto);
	var valor_final = (valor_final) / 100;
	var valor_final = (valor_custo) / (valor_final);

	var valor_final = isNaN(valor_final) ? 0 : valor_final;
	$("#valor_final_pedido").val(valor_final.toFixed(2).replace(/\./g, ','));

	var valor_final_sem = 100 - (valor_sem_imposto);
	var valor_final_sem = (valor_final_sem) / 100;
	var valor_final_sem = (valor_custo) / (valor_final_sem);

	var valor_final_sem = isNaN(valor_final_sem) ? 0 : valor_final_sem;
	$("#valor_semimposto").val(valor_final_sem.toFixed(2).replace(/\./g, ','));

    CalculaValorDolar();

};

function CalculaValorDolar(){
	var valor_final = Number($("#valor_final_pedido").val().toString().replace(/\,/g, '.'));;
	var cambio_dolar = Number($("#cambio_dolar").val().toString().replace(/\,/g, '.'));;
	var valor_dolar = (valor_final) / (cambio_dolar);
	var valor_dolar = isNaN(valor_dolar) ? 0 : valor_dolar;
	$("#valor_final_dolar").val(valor_dolar.toFixed(2).replace(/\./g, ','));
};


$("input").change(function() {
	CalculaSubtotal();
	CalculaCusto();
    CalculaImpostos();
    CalculaValorFinal();
    CalculaValorDolar();
});

$(document).ready(function() {
	CalculaSubtotal();
	CalculaCusto();
    CalculaImpostos();
    CalculaValorFinal();
    CalculaValorDolar();
});

<?php
}
?>

$(document).ready(function () {
    $('#selectall').click(function () {
        $('.selectPrint').attr('checked', isChecked('selectall'));
    });
});
function isChecked(checkboxId) {
    var id = '#' + checkboxId;
    return $(id).is(":checked");
};
function resetSelectAll() {
    if ($(".selectPrint").length == $(".selectPrint:checked").length) {
        $("#selectall").attr("checked", "checked");
    } else {
        $("#selectall").removeAttr("checked");
    }

    if ($(".selectPrint:checked").length > 0) {
        $('#edit').attr("disabled", false);
    } else {
        $('#edit').attr("disabled", true);
    }
};

</script>


<?php
require("rodape.php");
?>