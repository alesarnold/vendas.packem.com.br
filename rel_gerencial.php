<?php 
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

require("common.php"); 
//    require("../orcamentos/common.php"); 

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
		Log_Rel($_SESSION['user']['id'],$_SESSION['user']['nome'],"Tentou acessar o relatório gerencial fora da empresa.",$ip);
		$erro = "Erro acessar o conteudo.<br><br>Por gentileza contate o administrador.";
		unset($_SESSION['user']);
		header("Location: mensagem.php?msg=".$erro); 
		die();
	} else {
		Log_Rel($_SESSION['user']['id'],$_SESSION['user']['nome'],"Acessou o relatório gerencial.",$ip);
	}
*/
    require("cabecalho.php"); 


if ($_SESSION['user']['nivel'] != '1' && $_SESSION['user']['id'] != '89') {
?>
<div class="clearfix"></div>
<?php
    echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
	require("rodape.php");
    die(); 
}

$print_link = $_SERVER["REQUEST_URI"];
$print_link = str_replace("rel_gerencial","rel_gerencial_print",$print_link);

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


function pagination($query,$per_page=50,$page=1,$pesquisa,$url='?'){   
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
		<h1>Relatório gerencial</h1>
	</div>
</div>
<div class="clearfix"></div>


<?php

$pesquisa = "WHERE `nome_cliente` LIKE '%".$_GET["pesq"]."%' AND `id_vend` LIKE '%".$_GET["id_vend"]."%' AND `status` LIKE '1' OR `status` LIKE '2'";


$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;

if($_GET["limit"]!="") { $per_page = $_GET["limit"]; } else { $per_page = 10; }

$startpoint = ($page * $per_page) - $per_page;

if ($_GET["data_inicio"] != "" || $_GET["data_final"] != "" || $_GET["pesq"] != "" || $_GET["num_orc"] != "" || $_GET["tel"] != "" || $_GET["id_vend"] != "" || $_GET["dim"] != "" || $_GET["qtde"] != "" || $_GET["ref"] != "" || $_GET["rep"] != "") {
	if ($_GET["data_inicio"] != "" && $_GET["data_final"] != "") {
		$strpesq1 = "`x`.`data` BETWEEN '".$_GET["data_inicio"]." 00:00:00' AND '".$_GET["data_final"]." 23:59:59' AND ";
	}
	if ($_GET["pesq"] != "") {
		$strpesq2 = "`x`.`nome_cliente` LIKE '%".$_GET["pesq"]."%' AND ";
	}
	if ($_GET["num_orc"] != "") {
		$strpesq3 = "`x`.`pedido` LIKE '".$_GET["num_orc"]."' AND ";
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
	if ($status_ped == "4") { $semstatus = "AND `x`.`status` NOT LIKE '1' AND `x`.`status` NOT LIKE '2' AND `x`.`status` NOT LIKE '3' AND `x`.`status` NOT LIKE '5' AND `x`.`status` NOT LIKE '6' AND `x`.`status` NOT LIKE '7'"; }
} else {
	$status_ped = "1";
}

$statement = "(SELECT * FROM `pedidos` ORDER BY `id` DESC) x {$strpesq} `x`.`status` NOT LIKE '0' {$semstatus} GROUP BY `pedido` ORDER BY `x`.`pedido` DESC ";//ORDER BY `x`.`pedido` ASC LIMIT 0 , 50

$results = mysqli_query($conn,"SELECT `pedido`,`nome_cliente`,`qtde`,`referencia`,`representante`,`base1`,`base2`,`altura`,`valor_final`,`data`,`status` FROM {$statement} LIMIT {$startpoint} , {$per_page}");

$query_contagem = "SELECT `id` FROM {$statement}";
$total_encontrado = mysqli_num_rows(mysqli_query($conn,$query_contagem));;

?>


<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-list"></i> Lista de orçamentos<small>Total encontrado: <?php echo $total_encontrado; ?></small></h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<br>

<form>
<table border="0" width="100%" align="center" class="table jambo_table" style="border:0;">
	<thead>
	<tr class="headings">
		<th class="column-title" align="center" style="padding:15px;">
		<div class="row">
			<div class="col-md-2 col-xs-12">
				<label for="data_inicio">Data inicial:</label><input type="date" name="data_inicio" value="<?php echo $_GET["data_inicio"]; ?>" class="form-control">
			</div>
			<div class="col-md-2 col-xs-12">
				<label for="data_final">Data final:</label><input type="date" name="data_final" value="<?php echo $_GET["data_final"]; ?>" class="form-control">
			</div>
			<div class="col-md-3 col-xs-12">
				<label for="pesq">Cliente:</label><input type="text" class="form-control" name="pesq" id="pesq" <?php if ($_GET["pesq"] != "") { ?>value="<?php echo $_GET["pesq"]; ?>"<?php } ?>>
			</div>
			<div class="col-md-2 col-xs-12">
				<label for="status">Status:</label><select class="form-control" name="status" id="status" onchange="form.submit();">
					<option value="">Todos</option>
					<option value="1"<?php if($_GET["status"] == "1") { echo " SELECTED"; } ?>>Em análise</option>
					<option value="2"<?php if($_GET["status"] == "2") { echo " SELECTED"; } ?>>Aguardando liberação</option>
					<option value="3"<?php if($_GET["status"] == "3") { echo " SELECTED"; } ?>>Liberado</option>
					<option value="4"<?php if($_GET["status"] == "4") { echo " SELECTED"; } ?>>Aprovado</option>
				</select>
			</div>
			<div class="col-md-2 col-xs-12">
				<label for="limit">Qtde. por pág.:</label><select class="form-control" name="limit" id="limit" onchange="form.submit();">
					<option value="10"<?php if($_GET["limit"] == "10") { echo " SELECTED"; } ?>>10</option>
					<option value="25"<?php if($_GET["limit"] == "25") { echo " SELECTED"; } ?>>25</option>
					<option value="50"<?php if($_GET["limit"] == "50") { echo " SELECTED"; } ?>>50</option>
					<option value="100"<?php if($_GET["limit"] == "100") { echo " SELECTED"; } ?>>100</option>
					<option value="500"<?php if($_GET["limit"] == "500") { echo " SELECTED"; } ?>>500</option>
				</select>
			</div>
			<div class="col-md-1 col-xs-12 text-center">
				<input type="submit" class="col-md-12 col-xs-12 btn btn-default" style="margin-bottom:0; margin-top: 23px;" value="OK"></th>
			</div>
		</div>
	</tr>
	</thead>
</table>
</form>

<center>
<a href="rel_gerencial.php" class="btn btn-dark"><i class="fa fa-close"></i> LIMPAR FILTROS </a>
<a href="<?php echo $print_link ?>" class="btn btn-info" target="_blank"><i class="fa fa-print"></i> IMPRIMIR</a>
</center>
<br>
<br>

<table border="0" width="100%" align="center" class="table table-striped">
<thead>
<tr>
<th class="text-center"><b>N°</b></td>
<th><b>Cliente</b></td>
<?php /* <th><b>Ref.</b></td> */ ?>
<?php /* <th><b>Dimensões</b></td> */ ?>
<th class="sumir_coluna text-center"><b>Qtde.</b></td>
<?php /* <th class="sumir_coluna"><b>Representante</b></td> */ ?>
<th class="sumir_coluna text-center"><b>Valor unitário</b></td>
<th class="sumir_coluna text-center"><b>Valor</b></td>
<th class="sumir_coluna text-center"><b>Margem</b></td>
<th class="sumir_coluna text-center"><b>Margem real</b></td>
<th class="sumir_coluna text-center"><b>Data</b></td>
<?php /* <th class="sumir_coluna" title="Último PDF gerado"><b>PDF</b></td> */ ?>
<th class="text-center"><b>Status</b></td>
</tr>
</thead>
<?php

if (mysqli_num_rows($results) != 0) {

	while ($row_pedidos = mysqli_fetch_array($results)) {

		$no_pedido = $row_pedidos['pedido'];

		$data_ped0 = $row_pedidos['data'];
		$data_ped0 = strtotime($data_ped0);
		$hora_ped = date("H:i:s", $data_ped0);
		$data_ped = date("d/m/Y", $data_ped0);

		$status_txt = $status[$row_pedidos["status"]];

/*
		$query_cliente_ped = mysqli_query($conn,"SELECT * FROM `cad_clientes` WHERE `id` LIKE (SELECT `id_cliente` FROM `pedidos_cliente` WHERE `pedido` LIKE '".(float)$row_pedidos['pedido']."' LIMIT 1)");
		$row_cliente_ped = mysqli_fetch_array($query_cliente_ped);
		
		$cliente_email = $row_cliente_ped['email_com'];
		if ($row_cliente_ped['telefone'] != "") {
			$cliente_telefone = "(".$row_cliente_ped['ddd'].") ".$row_cliente_ped['telefone'];
		} else {
			$cliente_telefone = "";
		}
*/


		$nome_vend = $vendedor[$row_pedidos['id_vend']]["nome"];
//		$vendedor_email = $vendedor[$row_pedidos['id_vend']]["email"];
//		$vendedor_telefone = $vendedor[$row_pedidos['id_vend']]["celular"];

		$quantidade = number_format($row_pedidos["qtde"], 0, ',', '.');
		$representante = $row_pedidos["representante"];

/*
		$directory = 'pdf_pedidos/';


		$ultimo_pdf = "";
		$scanned_directory = "";
		$result = "";

		$directory = new DirectoryIterator('pdf_pedidos/');

		foreach ($directory as $fileinfo) {
			if (substr($fileinfo->getFilename(),-9,5) == $row_pedidos["pedido"]) {
				$result[] = $fileinfo->getFilename();
			}
		}

		$scanned_directory = $result;
		if (!$scanned_directory) {
			$ultimo_pdf = '<i class="fa fa-ban" title="Nenhum PDF gerado"></i>';
		} else {
			$ultimo_pdf = '<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar último PDF do orçamento" class="btn  btn-danger btn-xs tooltips" href="pdf_pedidos/'.end($scanned_directory).'" download><i class="fa fa-download"></i></a>';
		}
*/
		$query_extra = "SELECT `id`,`pedido`,`margem` FROM `pedidos_extra` WHERE `pedido` LIKE '".$row_pedidos['pedido']."' ORDER BY `id` DESC";
		$result_extra = mysqli_query($conn,$query_extra);
		$extra = mysqli_fetch_array($result_extra);

		$margem = number_format((float)$extra["margem"], 2, ',', '.')."%";

		$valor_unitario = $row_pedidos["valor_final"];
		$valor_pedido = $valor_unitario * $row_pedidos["qtde"];
		
		$margem_real = $valor_pedido*$extra["margem"]/100;

		$total_qtde += $row_pedidos["qtde"];
		$total_valor_unitario += $row_pedidos["valor_final"];
		$total_valor_pedido += $valor_pedido;
		$total_comissao += $extra["comissao"];
		$total_comissao_real += $comissao_real;
		$total_margem += $extra["margem"];
		$total_margem_real += $margem_real;
		?>
			<tr>
				<td class="text-center" style="padding-left:5px; max-width: 50px;"><a href="resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>"><?php echo sprintf('%05d', $row_pedidos['pedido']); ?></a></td>
				<td style="max-width: 200px;"><?php echo $row_pedidos['nome_cliente']; ?></td>
<?php /*				<td><?php echo $row_pedidos['referencia']; ?></td> */ ?>
<?php /* 				<td><?php echo $row_pedidos['base1']."x".$row_pedidos['base2']."x".$row_pedidos['altura']; ?></td> */ ?>
				<td class="sumir_coluna text-center"><?php echo $quantidade; ?></td>
<?php /*				<td class="sumir_coluna"><?php echo $representante; ?></td> */?>
				<td class="sumir_coluna text-center"><?php echo "R$ ".number_format((float)$valor_unitario, 2, ',', '.'); ?></td>
				<td class="sumir_coluna text-center"><?php echo "R$ ".number_format((float)$valor_pedido, 2, ',', '.'); ?></td>
				<td class="sumir_coluna text-center"><?php echo $margem; ?></td>
				<td class="sumir_coluna text-center"><?php echo "R$ ".number_format((float)$margem_real, 2, ',', '.'); ?></td>
				<td class="text-center" title="<?php echo $hora_ped; ?>"><?php echo $data_ped; ?></td>
<?php /*				<td align="center"><?php echo $ultimo_pdf; ?></td> */ ?>
				<td class="text-center"><img src="images/icons/status<?php echo $row_pedidos['status']; ?>.png" alt="<?php echo $status_txt; ?>" title="<?php echo $status_txt; ?>"></td>
			</tr>
		<?php
	}
?>
			<tr>
				<td></td>
				<td class="text-left"><b>Total:</b></td>
				<td class="text-center"><b><?php echo number_format((float)$total_qtde, 0, ',', '.'); ?></b></td>
				<td class="text-center"><b><?php echo "R$ ".number_format((float)$total_valor_unitario, 2, ',', '.'); ?></b></td>
				<td class="text-center"><b><?php echo "R$ ".number_format((float)$total_valor_pedido, 2, ',', '.'); ?></b></td>
				<td class="text-center"><b><?php echo number_format((float)$total_margem, 2, ',', '.')."%"; ?></b></td>
				<td class="text-center"><b><?php echo "R$ ".number_format((float)$total_margem_real, 2, ',', '.'); ?></b></td>
				<td></td>
				<td></td>
			</tr>
<?php
} else {
?>
<tr>
	<td colspan="9" align="center"><br>Nenhum registro encontrado.<br><br></td>
</tr>
<?php
}
?>
</table>

<center>
<?php
echo pagination($query_contagem,$per_page,$page,$pesquisa,$url='?data_inicio='.$_GET["data_inicio"].'&data_final='.$_GET["data_final"].'&pesq='.$_GET["pesq"].'&rep='.$_GET["rep"].'&limit='.$_GET["limit"].'&');
?>
<br><br>
</center>

<?php
require("rodape.php");
?>