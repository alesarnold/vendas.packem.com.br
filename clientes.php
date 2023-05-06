<?php 

    require("common.php"); 

/*
$query = mysqli_query($conn,"SELECT `cnpj_cpf`,`nome_cliente`,`segmento_cliente`,`cidade_cliente`,`uf_cliente`,`selecao` FROM (SELECT * FROM `pedidos` ORDER BY `id` DESC) x WHERE `x`.`status` NOT LIKE '0' GROUP BY `cnpj_cpf` ORDER BY `x`.`pedido` DESC");

echo "INSERT INTO `cad_clientes` (`id`, `selecao`, `cnpj_cpf`, `insc_est`, `razao`, `nome`, `rua`, `numero`, `complemento`, `bairro`, `cidade`, `uf`, `cep`, `ddd`, `telefone`, `ramal`, `ddd_cel`, `celular`, `contato_com`, `email_com`, `contato_fin`, `email_fin`, `ramal_fin`, `segmento`, `id_vend`, `status`, `data`, `atualizacao`) VALUES ";
while($cliente = mysqli_fetch_array($query)) {
	echo "(NULL, '".$cliente["selecao"]."', '".utf8_encode($cliente["cnpj_cpf"])."', '', '', '".utf8_encode($cliente["nome_cliente"])."', '', '', '', '', '".utf8_encode($cliente["cidade_cliente"])."', '".utf8_encode($cliente["uf_cliente"])."', '000', '', '', '', '', '', '', '', '', '', '', '".$cliente["segmento_cliente"]."', NULL, '2', '2016-09-01 00:00:00', '2016-09-01 00:00:00'), "."\n";
}

die();
*/

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 

    require("cabecalho.php"); 

if ($_SESSION['user']['nivel'] == '3') {
	if ($_GET["id"] != "") {

		$results = "SELECT * FROM `cad_clientes` WHERE `id` LIKE '".$_GET["id"]."'";
		$cliente = mysqli_fetch_array(mysqli_query($conn,$results));

		if ($_SESSION['user']['id'] != $cliente["id_vend"]) { ?>
		<div class="clearfix"></div>
		<?php
			echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
			require("rodape.php");
			die(); 
		}
	}
}


$print_link = $_SERVER["REQUEST_URI"];
$print_link = str_replace("relatorios","relatorios_print",$print_link);


$query_vendedor = mysqli_query($conn,"SELECT * FROM `users` ORDER BY `nome` ASC");

while($vendedores = mysqli_fetch_array($query_vendedor)) {

	$vendedor_info[$vendedores["id"]]["id"] = $vendedores["id"];
	$vendedor_info[$vendedores["id"]]["nome"] = $vendedores["nome"];
	$vendedor_info[$vendedores["id"]]["email"] = $vendedores["email"];
	$vendedor_info[$vendedores["id"]]["celular"] = $vendedores["celular"];
}

/*
echo "<pre>";
echo "Vendedores: ";
print_r($vendedor_info);
echo "</pre>";
*/

$status[1] = "close";
$status[2] = "check";


function pagination($query,$per_page=50,$page=1,$url='?'){   
    global $conn;

	$total = mysqli_num_rows(mysqli_query($conn,$query));
    $adjacents = "2"; 
      
    $prevlabel = "&lsaquo; Anterior";
    $nextlabel = "Próximo &rsaquo;";
    $lastlabel = "Último &rsaquo;&rsaquo;";
      
    $page = ($page == 0 ? 1 : $page);  
    $start = ($page - 1) * $per_page;                               
      
    $prev = $page - 1;                          
    $next = $page + 1;
      
    $lastpage = ceil($total/$per_page);
      
    $lpm1 = $lastpage - 1;
      
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
		<h1>Clientes</h1>
	</div>
</div>
<div class="clearfix"></div>


<?php

if($_GET['acao'] == 'desativar') {

	$atualiza_cliente = mysqli_query( $conn, "UPDATE `cad_clientes` SET `status` = '1', `atualizacao` = '".date('Y-m-d H:i:s')."' WHERE `id` LIKE '".$_GET["id"]."';");
	if(! $atualiza_cliente ) { die('Não foi possível atualizar informações de cliente: ' . mysqli_error($conn)); } else { redirect("clientes.php?codigo=".$_GET["id"]); die(); }

} elseif($_GET['acao'] == 'ativar') {

	$atualiza_cliente = mysqli_query( $conn, "UPDATE `cad_clientes` SET `status` = '2', `atualizacao` = '".date('Y-m-d H:i:s')."' WHERE `id` LIKE '".$_GET["id"]."';");
	if(! $atualiza_cliente ) { die('Não foi possível atualizar informações de cliente: ' . mysqli_error($conn)); } else { redirect("clientes.php?codigo=".$_GET["id"]); die(); }

} elseif($_GET['acao'] == 'remover') {

	$atualiza_cliente = mysqli_query( $conn, "UPDATE `cad_clientes` SET `status` = '0', `atualizacao` = '".date('Y-m-d H:i:s')."' WHERE `id` LIKE '".$_GET["id"]."';");
	if(! $atualiza_cliente ) { die('Não foi possível atualizar informações de cliente: ' . mysqli_error($conn)); } else { redirect("clientes.php"); die(); }

} elseif($_GET['acao'] == 'adicionar') {

if( isset($_POST["nome"]) ) {

	$_POST["atualizacao"] = date('Y-m-d H:i:s');

	$query = 'INSERT INTO `cad_clientes` (';
	$query .= 'id, ';
	foreach($_POST as $chave => $valor) {
	  $query .= '`'.$chave.'`, ';
	}

	$query .= "status, data";
	$query .= ") VALUES (NULL, ";

	foreach($_POST as $chave => $valor) {
	  $query .= '\''.mysqli_real_escape_string($conn,$valor).'\', ';
	}

	$query .= "'2', '".date("Y-m-d H:i:s")."'";
	$query .= ");";


//echo $query;
//die();

	$adiciona_cliente = mysqli_query( $conn, $query );
	if(! $adiciona_cliente ) {
		die('Não foi possível adicionar novo cliente: ' . mysqli_error($conn));
	} else {
		redirect("clientes.php");
		die();
	}

} else {
?>
<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-plus"></i> Adicionar cliente</h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<br>

<form class="form-horizontal form-label-left" action="clientes.php?acao=adicionar" method="post">
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="nome">Cliente <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="nome" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["nome"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="razao">Razão Social <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="razao" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["razao"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="cnpj_cpf"><select name="selecao" class="selecao" style="height: 29px; width: 60px; border:0; margin-top:-5px;">
    	<option value="cnpj">CNPJ</option>
    	<option value="cpf">CPF</option>
    	<option value="rut">RUT</option>
    	<option value="cuit">CUIT</option>
    	<option value="ruc">RUC</option>
    	<option value="rif">RIF</option>
    	<option value="outro">Outro</option>
	</select> <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="cnpj_cpf" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["cnpj_cpf"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="insc_est">Insc. Estadual</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="insc_est" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["insc_est"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="rua">Rua</label>
	<div class="col-md-4 col-sm-4 col-xs-12"><input type="text" name="rua" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["rua"]; ?>"></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="numero">Número</label>
	<div class="col-md-1 col-sm-1 col-xs-12"><input type="number" name="numero" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["numero"]; ?>" min="0"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="complemento">Complemento</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="complemento" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["complemento"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="bairro">Bairro</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="bairro" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["bairro"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="cidade">Cidade <span class="required">*</span></label>
	<div class="col-md-4 col-sm-4 col-xs-12"><input type="text" name="cidade" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["cidade"]; ?>"></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="uf">Estado <span class="required">*</span></label>
	<div class="col-md-1 col-sm-1 col-xs-12"><select name="uf" required="required" class="form-control"><?php

	$estado = mysqli_query($conn,"SELECT * FROM estados");
	while($row = mysqli_fetch_array($estado)) {
		if ($row['status'] == 1) {
			echo "    <option ";
			if ($row['uf'] == $cliente["uf"]) { echo "SELECTED "; }
			echo "value=\"" . $row['uf'] . "\">" . $row['uf'] . "</option>\n";
		}
	}
?></select></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="cep">CEP</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="number" name="cep" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["cep"]; ?>" min="0"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="ddd">DDD <span class="required">*</span></label>
	<div class="col-md-1 col-sm-1 col-xs-12"><input type="number" name="ddd" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["ddd"]; ?>" min="0"></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="telefone">Telefone <span class="required">*</span></label>
	<div class="col-md-2 col-sm-2 col-xs-12"><input type="number" name="telefone" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["telefone"]; ?>" min="0"></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="ramal">Ramal</label>
	<div class="col-md-1 col-sm-1 col-xs-12"><input type="number" name="ramal" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["ramal"]; ?>" min="0"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="ddd_cel">DDD</label>
	<div class="col-md-1 col-sm-1 col-xs-12"><input type="number" name="ddd_cel" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["ddd_cel"]; ?>" min="0"></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="celular">Celular</label>
	<div class="col-md-4 col-sm-4 col-xs-12"><input type="number" name="celular" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["celular"]; ?>" min="0"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="contato_com">Nome do comprador <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="contato_com" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["contato_com"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="email_com">E-mail do comprador <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="email" name="email_com" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["email_com"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="contato_fin">Nome do financeiro</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="contato_fin" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["contato_fin"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="email_fin">E-mail do financeiro</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="email" name="email_fin" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["email_fin"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="ramal_fin">Ramal do financeiro</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="number" name="ramal_fin" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["ramal_fin"]; ?>" min="0"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="segmento">Segmento <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><select name="segmento" class="form-control">
	<?php
	$segmento = mysqli_query($conn,"SELECT * FROM segmentos");
	while($row = mysqli_fetch_array($segmento)) {
	  echo "    <option value=\"" . $row['id'] . "\">" . $row['segmento'] . "</option>\n";
	}
	?>
	</select></div>
</div>
<?php
if ($_SESSION['user']['nivel'] != '3') {
?><div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_vend">Vendedor responsável</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><select class="form-control" name="id_vend" id="id_vend">
				<option value="0">Selecione</option>
<?php
foreach ($vendedor_info as &$vend) {
	if($cliente["id_vend"] == $vend["id"]) { $vend_selecionado = " SELECTED"; } else { $vend_selecionado = ""; }
	echo '				<option value="'.$vend["id"].'"'.$vend_selecionado.'>'.$vend["nome"].'</option>'."\n";
}
?>
	</select></div>
</div>
<?php
} else {
?>
<input type="hidden" name="id_vend" value="<?php echo $_SESSION['user']['id']; ?>">
<?php
}
?>
<br>
<div class="ln_solid"></div>
	<div class="form-group">
	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
		<button type="submit" class="btn btn-success">Adicionar</button>
		<a href="clientes.php" class="btn btn-primary">Cancelar</a>
	</div>
</div>
</form>
<?php
}

} elseif($_GET['acao'] == 'editar') {

	$results = "SELECT * FROM `cad_clientes` WHERE `id` LIKE '".$_GET["id"]."'";
	$cliente = mysqli_fetch_array(mysqli_query($conn,$results));

if( isset($_POST["nome"]) ) {

	$_POST["atualizacao"] = date('Y-m-d H:i:s');
	$query = 'UPDATE `cad_clientes` SET ';
	foreach($_POST as $chave => $valor) {
	  $query .= '`'.$chave.'` = \''.mysqli_real_escape_string($conn,$valor).'\', ';
	}
	$query = rtrim($query, ', ');
	$query .= " WHERE `id` LIKE '".$_GET["id"]."';";

	$atualiza_cliente = mysqli_query( $conn, $query );
	if(! $atualiza_cliente ) {
		die('Não foi possível atualizar informações de cliente: ' . mysqli_error($conn));
	} else {
		redirect("clientes.php?codigo=".$_GET["id"]);
		die();
	}

} else {

//	$cliente = utf8_encode($cliente);
?>

<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-pencil"></i> Editar cliente</h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<br>

<form class="form-horizontal form-label-left" action="clientes.php?acao=editar&id=<?php echo $cliente["id"]; ?>" method="post">
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="nome">Cliente <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="nome" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["nome"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="razao">Razão Social <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="razao" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["razao"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="cnpj_cpf"><select name="selecao" class="selecao" style="height: 29px; width: 60px; border:0; margin-top:-5px;">
    	<option value="cnpj"<?php if($cliente["selecao"] == "cnpj") { echo " SELECTED"; } ?>>CNPJ</option>
    	<option value="cpf"<?php if($cliente["selecao"] == "cpf") { echo " SELECTED"; } ?>>CPF</option>
    	<option value="rut"<?php if($cliente["selecao"] == "rut") { echo " SELECTED"; } ?>>RUT</option>
    	<option value="cuit"<?php if($cliente["selecao"] == "cuit") { echo " SELECTED"; } ?>>CUIT</option>
    	<option value="ruc"<?php if($cliente["selecao"] == "ruc") { echo " SELECTED"; } ?>>RUC</option>
    	<option value="rif"<?php if($cliente["selecao"] == "rif") { echo " SELECTED"; } ?>>RIF</option>
    	<option value="outro"<?php if($cliente["selecao"] == "outro") { echo " SELECTED"; } ?>>Outro</option>
	</select> <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="cnpj_cpf" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["cnpj_cpf"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="insc_est">Insc. Estadual</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="insc_est" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["insc_est"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="rua">Rua</label>
	<div class="col-md-4 col-sm-4 col-xs-12"><input type="text" name="rua" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["rua"]; ?>"></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="numero">Número</label>
	<div class="col-md-1 col-sm-1 col-xs-12"><input type="number" name="numero" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["numero"]; ?>" min="0"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="complemento">Complemento</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="complemento" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["complemento"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="bairro">Bairro</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="bairro" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["bairro"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="cidade">Cidade <span class="required">*</span></label>
	<div class="col-md-4 col-sm-4 col-xs-12"><input type="text" name="cidade" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["cidade"]; ?>"></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="uf">Estado <span class="required">*</span></label>
	<div class="col-md-1 col-sm-1 col-xs-12"><select name="uf" required="required" class="form-control"><?php

	$estado = mysqli_query($conn,"SELECT * FROM estados");
	while($row = mysqli_fetch_array($estado)) {
		if ($row['status'] == 1) {
			echo "    <option ";
			if ($row['uf'] == $cliente["uf"]) { echo "SELECTED "; }
			echo "value=\"" . $row['uf'] . "\">" . $row['uf'] . "</option>\n";
		}
	}
?></select></div>
<?php /* 	<input type="text" name="uf" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["uf"]; ?>"></div> */ ?>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="cep">CEP</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="number" name="cep" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["cep"]; ?>" min="0"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="ddd">DDD <span class="required">*</span></label>
	<div class="col-md-1 col-sm-1 col-xs-12"><input type="number" name="ddd" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["ddd"]; ?>" min="0"></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="telefone">Telefone <span class="required">*</span></label>
	<div class="col-md-2 col-sm-2 col-xs-12"><input type="number" name="telefone" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["telefone"]; ?>" min="0"></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="ramal">Ramal</label>
	<div class="col-md-1 col-sm-1 col-xs-12"><input type="number" name="ramal" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["ramal"]; ?>" min="0"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="ddd_cel">DDD</label>
	<div class="col-md-1 col-sm-1 col-xs-12"><input type="number" name="ddd_cel" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["ddd_cel"]; ?>" min="0"></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="celular">Celular</label>
	<div class="col-md-4 col-sm-4 col-xs-12"><input type="number" name="celular" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["celular"]; ?>" min="0"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="contato_com">Nome do comprador <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="contato_com" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["contato_com"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="email_com">E-mail do comprador <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="email" name="email_com" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["email_com"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="contato_fin">Nome do financeiro</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="contato_fin" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["contato_fin"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="email_fin">E-mail do financeiro</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="email" name="email_fin" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["email_fin"]; ?>"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="ramal_fin">Ramal do financeiro</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="number" name="ramal_fin" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["ramal_fin"]; ?>" min="0"></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="segmento">Segmento <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><select name="segmento" class="form-control">
	<?php
	$segmento = mysqli_query($conn,"SELECT * FROM segmentos");
	while($row = mysqli_fetch_array($segmento)) {
		if($row['id'] == $cliente["segmento"]) { $txt_selected = " SELECTED"; } else { $txt_selected = ""; }
		  echo "    <option value=\"" . $row['id'] . "\"".$txt_selected.">" . $row['segmento'] . "</option>\n";
	}
	?>
	</select></div>
</div>
<?php
if ($_SESSION['user']['nivel'] != '3') {
?><div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_vend">Vendedor responsável</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><select class="form-control" name="id_vend" id="id_vend">
<?php
$vendedor_info[0][id] = "0";
$vendedor_info[0][nome] = "Não selecionado";
foreach ($vendedor_info as &$vend) {
	if($cliente["id_vend"] == $vend["id"]) { $vend_selecionado = " SELECTED"; } else { $vend_selecionado = ""; }
	echo '				<option value="'.$vend["id"].'"'.$vend_selecionado.'>'.$vend["nome"].'</option>'."\n";
}
?>
	</select></div>
</div>
<?php
} else {
?>
<input type="hidden" name="id_vend" value="<?php echo $_SESSION['user']['id']; ?>">
<?php
}
?>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="data">Data de criação</label>
	<div class="col-md-2 col-sm-2 col-xs-12"><input type="text" readonly class="form-control col-md-7 col-xs-12" value="<?php echo date_format(date_create($cliente["data"]),"d/m/Y - H:i"); ?>"></div>
	<label class="control-label col-md-2 col-sm-2 col-xs-12" for="atualizacao">Última atualização</label>
	<div class="col-md-2 col-sm-2 col-xs-12"><input type="text" readonly class="form-control col-md-7 col-xs-12" value="<?php echo date_format(date_create($cliente["atualizacao"]),"d/m/Y - H:i"); ?>"></div>
</div>

<div class="ln_solid"></div>
	<div class="form-group">
	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
		<button type="submit" class="btn btn-success">Alterar</button>
		<a href="clientes.php?codigo=<?php echo $cliente["id"]; ?>" class="btn btn-primary">Cancelar</a>
	</div>
</div>
</form>

<?php
}

} else {

$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;


$per_page = (int)(!isset($_GET["qtde"]) ? 20 : $_GET["qtde"]); // 10; // Set how many records do you want to display per page.
 
$startpoint = ($page * $per_page) - $per_page;

if ($_GET["pesq"] != "" || $_GET["codigo"] != "" || $_GET["tel"] != "" || $_GET["vendedor"] != "") {
	if ($_GET["pesq"] != "") {
		$strpesq1 = "`nome` LIKE '%".$_GET["pesq"]."%' AND ";
	}
	if ($_GET["codigo"] != "") {
		$strpesq2 = "`id` LIKE '".$_GET["codigo"]."' AND ";
	}
	if ($_GET["tel"] != "") {
		$strpesq1 = "`telefone` LIKE '%".$_GET["tel"]."%' AND ";
	}
	if ($_GET["vendedor"] != "") {
		$strpesq3 = "`id_vend` LIKE '".$_GET["vendedor"]."' AND ";
	}
	$strpesq = "WHERE ".$strpesq1.$strpesq2.$strpesq3;
} else {
	$strpesq = "WHERE ";
}

if($_GET["ordem"]!="") {
	$ordem = $_GET["ordem"];
} else {
	$ordem = "nome";
}

if($_GET["ascdesc"]!="") {
	$ascdesc = $_GET["ascdesc"];
} else {
	$ascdesc = "asc";
}


if ($_SESSION['user']['nivel'] != '3') {
	if ($_GET["acao"] == "reativar") {
		if ($_GET["id"] != "") {
			$atualiza_cliente = mysqli_query( $conn, "UPDATE `cad_clientes` SET `status` = '1' WHERE `id` LIKE '".$_GET["id"]."';");
			if(! $atualiza_cliente ) { die('Não foi possível atualizar informações de cliente: ' . mysqli_error($conn)); } else { redirect("clientes.php?codigo=".$_GET["id"]); die(); }
		} else {
			$statement = "`cad_clientes` {$strpesq} `status` LIKE '0' ORDER BY `".$ordem."` ".$ascdesc;
		}
	} else {
		$statement = "`cad_clientes` {$strpesq} `status` NOT LIKE '0' ORDER BY `".$ordem."` ".$ascdesc;
	}
} else {
	$statement = "`cad_clientes` {$strpesq} `status` NOT LIKE '0' AND `status` NOT LIKE '1' AND `id_vend` LIKE '".$_SESSION['user']["id"]."' ORDER BY `".$ordem."` ".$ascdesc;
}


$results = mysqli_query($conn,"SELECT * FROM {$statement} LIMIT {$startpoint} , {$per_page}");

$query_contagem = "SELECT * FROM {$statement}";
$total_encontrado = mysqli_num_rows(mysqli_query($conn,$query_contagem));;

?>


<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-list"></i> Lista geral de clientes<small>Total encontrado: <?php echo $total_encontrado; ?></small></h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<br>

<?php
if ($_GET["acao"] != "reativar") {
?>
<form>
<table border="0" width="100%" align="center" class="table jambo_table" style="border:0;">
	<thead>
	<tr class="headings">
		<th class="column-title" align="center">
		<div class="col-md-<?php if ($_SESSION['user']['nivel'] != '3') { echo "4"; } else { echo "7"; } ?> col-xs-12">
			<label for="pesq">Cliente:</label><input type="text" class="form-control" name="pesq" id="pesq" <?php if ($_GET["pesq"] != "") { ?>value="<?php echo $_GET["pesq"]; ?>"<?php } ?>>
		</div>
		<div class="col-md-1 col-xs-12">
			<label for="codigo">Cód.:</label><input type="text" class="form-control" name="codigo" id="codigo" <?php if ($_GET["codigo"] != "") { ?>value="<?php echo $_GET["codigo"]; ?>"<?php } ?>>
		</div>
		<div class="col-md-2 col-xs-12">
			<label for="tel">Telefone:</label><input type="text" class="form-control" name="tel" id="tel" <?php if ($_GET["tel"] != "") { ?>value="<?php echo $_GET["tel"]; ?>"<?php } ?>>
		</div>
<?php
if ($_SESSION['user']['nivel'] != '3') {
?>		<div class="col-md-3 col-xs-12">
			<label for="vendedor">Vendedor:</label><select class="form-control" name="vendedor" id="vendedor" onchange="form.submit();">
				<option<?php if ($_GET["vendedor"] == "") { echo " selected"; } ?> value="">Todos</option>
<?php
foreach ($vendedor_info as &$vend) {
	if($_GET["vendedor"] == $vend["id"]) { $vend_selecionado = " SELECTED"; } else { $vend_selecionado = ""; }
	echo '				<option value="'.$vend["id"].'"'.$vend_selecionado.'>'.$vend["nome"].'</option>'."\n";
}
?>
			</select>
		</div>
<?php
}
?>
		<input type="hidden" name="ordem" value="<?php echo $_GET["ordem"]; ?>">
		<input type="hidden" name="ascdesc" value="<?php echo $_GET["ascdesc"]; ?>">
		<div class="col-md-1 col-xs-12">
			<input type="submit" class="btn btn-default" style="margin-bottom:0; margin-top: 23px;" value="FILTRAR">
		</div>
		<div class="col-md-1 col-xs-12">
			<input type="button" class="btn btn-default" style="margin-bottom:0; margin-top: 23px;" value="LIMPAR" onclick="window.location='clientes.php'">
		</div>
		</th>
	</tr>
	</thead>
</table>
</form>
<?php
}

if($ascdesc == "asc") {
	$ascdesc_x = "desc";
} else {
	$ascdesc_x = "asc";
}

?>
<br>

<table border="0" width="100%" align="center" class="table table-striped">
<thead>
<tr>
<th align="center"><b><a href="clientes.php?pesq=<?php echo $_GET["pesq"]; ?>&codigo=<?php echo $_GET["codigo"]; ?>&tel=<?php echo $_GET["tel"]; ?>&vendedor=<?php echo $_GET["vendedor"]; ?>&ascdesc=<?php echo $ascdesc_x; ?>&ordem=id&page=<?php echo $_GET["page"]; ?>"><?php if($ordem=="id") { ?><i class="fa fa-sort-numeric-<?php echo $ascdesc; ?>"></i> <?php } ?>Cód.</a></b></th>
<th><b><a href="clientes.php?pesq=<?php echo $_GET["pesq"]; ?>&codigo=<?php echo $_GET["codigo"]; ?>&tel=<?php echo $_GET["tel"]; ?>&vendedor=<?php echo $_GET["vendedor"]; ?>&ascdesc=<?php echo $ascdesc_x; ?>&ordem=nome&page=<?php echo $_GET["page"]; ?>"><?php if($ordem=="nome") { ?><i class="fa fa-sort-alpha-<?php echo $ascdesc; ?>"></i> <?php } ?>Cliente</a></b></th>
<th class="sumir_coluna"><b>E-mail</b></th>
<th class="sumir_coluna"><b>Telefone</b></th>
<th class="sumir_coluna"><b>Vendedor</b></th>
<th class="sumir_coluna"><b>Tel. vendedor</b></th>
<th class="sumir_coluna"><b>Ações</b></th>
<th><b>Status</b></th>
<?php if ($_SESSION['user']['nivel'] != '3') {
?><th></th>
<?php } ?></tr>
</thead>
<?php

if (mysqli_num_rows($results) != 0) {

	while ($row_clientes = mysqli_fetch_array($results)) {

		$nome_vend = $vendedor_info[$row_clientes['id_vend']]["nome"];
		$vendedor_email = $vendedor_info[$row_clientes['id_vend']]["email"];
		$vendedor_telefone = $vendedor_info[$row_clientes['id_vend']]["celular"];

		?>
			<tr>
				<td align="center" style="padding-left:5px; max-width: 50px;"><?php echo sprintf('%03d', $row_clientes['id']); ?></td>
				<td style="max-width: 200px;"><?php echo $row_clientes['nome']; ?></td>
				<td class="sumir_coluna"><a href="mailto:<?php echo $row_clientes['email_com']; ?>"><?php echo $row_clientes['email_com']; ?></a></td>
				<td class="sumir_coluna"><?php echo "(".$row_clientes['ddd'].") ".$row_clientes['telefone']; ?></td>
				<td class="sumir_coluna"><a href="mailto:<?php echo $vendedor_email; ?>"><?php echo $nome_vend; ?></a></td>
				<td class="sumir_coluna"><?php echo $vendedor_telefone; ?></td>
				<td class="sumir_coluna" align="center">
					<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Editar" class="btn  btn-info btn-xs tooltips" onclick="window.location='clientes.php?acao=editar&id=<?php echo $row_clientes["id"]; ?>'"><i class="fa fa-pencil"></i> </button>
<?php if ($row_clientes["status"] == "1") { ?>
					<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Ativar" class="btn  btn-success btn-xs tooltips" onclick="window.location='clientes.php?acao=ativar&id=<?php echo $row_clientes["id"]; ?>'"><i class="fa fa-check"></i> </button>
<?php } elseif ($row_clientes["status"] == "2") { ?>
					<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Desativar" class="btn  btn-danger btn-xs tooltips" onclick="window.location='clientes.php?acao=desativar&id=<?php echo $row_clientes["id"]; ?>'"><i class="fa fa-close"></i> </button>
<?php } ?>
				</td>
<?php
if ($_GET["acao"] == "reativar") {
?>
				<td align="center" colspan="2"><button title="" data-placement="top" data-toggle="tooltip" type="button" class="btn  btn-default btn-xs tooltips" onclick="window.location='clientes.php?acao=reativar&id=<?php echo $row_clientes["id"]; ?>'"><i class="fa fa-reply"></i> Reativar cliente</button>
<?php
} else {
?>
				<td align="center"><i class="fa fa-<?php echo $status[$row_clientes["status"]]; ?>" style="color:<?php if ($row_clientes["status"] == "1") { echo "#b30000"; } elseif ($row_clientes["status"] == "2") { echo "#00b300"; } ?>;"></i></td>
<?php if ($_SESSION['user']['nivel'] != '3') { ?>
				<td align="center"><button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Remover" class="btn  btn-default btn-xs tooltips" style="opacity:0.3;" onclick="window.location='clientes.php?acao=remover&id=<?php echo $row_clientes["id"]; ?>'"><i class="fa fa-trash"></i></button>
<?php
 }
}
?>
				</td>
			</tr>
		<?php
	}
  
} else {
?>
<tr>
	<td colspan="9" align="center"><br>Nenhum cliente encontrado.<br><br></td>
</tr>
<?php
}
?>
</table>

<center>

<a href="clientes.php?acao=adicionar" class="btn btn-default source"><i class="fa fa-plus"></i> Adicionar novo cliente</a>
<?php
if ($_SESSION['user']['nivel'] != '3') {
	if ($_GET["acao"] == "reativar") {
		?>
		<a href="clientes.php" class="btn btn-default source"><i class="fa fa-arrow-left"></i> Voltar</a>
		<?php
	} else {
		?>
		<a href="clientes.php?acao=reativar" class="btn btn-default source"><i class="fa fa-reply"></i> Reativar cliente removido</a>
		<?php
	}
}
?>
<br>

<?php
echo pagination($query_contagem,$per_page,$page,$url='?pesq='.$_GET["pesq"].'&codigo='.$_GET["codigo"].'&tel='.$_GET["tel"].'&vendedor='.$_GET["vendedor"].'&ascdesc='.$_GET["ascdesc"].'&ordem='.$_GET["ordem"].'&');
?>
<br><br>
</center>

<?php

}

require("rodape.php");
?>