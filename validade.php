<?php 

    require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 

    //require("dolar/class.UOLCotacoes.php");

	//$uol = new UOLCotacoes();
	//list($dolarComercialCompra, $dolarComercialVenda, $dolarTurismoCompra, $dolarTurismoVenda, $euroCompra, $euroVenda, $libraCompra, $libraVenda, $pesosCompra, $pesosVenda) = $uol->pegaValores();


    require("cabecalho.php"); 




function pagination($query,$per_page=50,$page=1,$pesquisa,$ordem,$ascdesc,$url='?'){   
    global $conn;


//if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
//	$query = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos ".$pesquisa." AND `status` LIKE '3' ORDER BY ".$ordem." ".$ascdesc;
//} else {
//	$pesquisa .= " AND `id_vend` LIKE '%".$_SESSION['user']['id']."%'";
	$query = "SELECT COUNT(DISTINCT pedido) as `num` FROM pedidos p ".$pesquisa." AND `status` LIKE '3' ORDER BY ".$ordem." ".$ascdesc;
//}


    $row = mysqli_fetch_array(mysqli_query($conn,$query));
    $total = $row['num'];
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
		<h1>Orçamentos a expirar</h1>
	</div>
</div>
<div class="clearfix"></div>


<?php

$ordem = "p.pedido";
$ascdesc = "desc";
$limit = 50;
$pesquisa = "WHERE p.nome_cliente LIKE '%".utf8_decode($_GET["pesq"])."%' AND p.vendedor LIKE '%".utf8_decode($_GET["vendedor"])."%' AND p.status LIKE '3'";

?>


<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-list"></i> Lista de orçamentos vencendo<small>Total encontrado: <?php
/*
if ($_GET["data_inicio"] != "" && $_GET["data_venc"] != "") {
	$query5 = "SELECT `id` FROM `pedidos` p ".$pesquisa." AND pedido = ANY (SELECT tt.id_ped FROM `pedidos_aprova` tt INNER JOIN (SELECT `id_ped`, MAX(`id`) AS `id` FROM `pedidos_aprova` GROUP BY `id_ped`) groupedtt ON tt.id_ped = groupedtt.id_ped AND tt.id = groupedtt.id AND DATE( DATE_ADD( `data` , INTERVAL 5 DAY ) ) BETWEEN '".$_GET["data_inicio"]." 00:00:00' AND '".$_GET["data_venc"]." 23:59:59' )";
	$row5 = mysqli_num_rows(mysqli_query($conn,$query5));
    $total5 = $row5;
} elseif ($_GET["data_venc"] != "") {
	$query5 = "SELECT `id` FROM `pedidos` p ".$pesquisa." AND pedido = ANY (SELECT tt.id_ped FROM `pedidos_aprova` tt INNER JOIN (SELECT `id_ped`, MAX(`id`) AS `id` FROM `pedidos_aprova` GROUP BY `id_ped`) groupedtt ON tt.id_ped = groupedtt.id_ped AND tt.id = groupedtt.id AND DATE('".$_GET["data_venc"]."') = DATE( DATE_ADD( `data` , INTERVAL 5 DAY ) ) )";
	$row5 = mysqli_num_rows(mysqli_query($conn,$query5));
    $total5 = $row5;
} else {
	$query5 = "SELECT DISTINCT`pedido`, `id` FROM `pedidos` p WHERE `status` LIKE '3' AND id = (SELECT max(id) FROM pedidos p2 WHERE p2.pedido = p.pedido)";
	$row5 = mysqli_num_rows(mysqli_query($conn,$query5));
    $total5 = $row5;
}

 echo $total5; */?></small></h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<br>

<form class="form-inline">
	<div class="form-group">
		<label class="control-label" for="data_inicio">Data Inicial: </label>
		<input type="date" name="data_inicio" value="<?php echo $_GET["data_inicio"]; ?>" class="btn btn-default">
		<label class="control-label" for="data_venc">Data Final: </label>
		<input type="date" name="data_venc" value="<?php echo $_GET["data_venc"]; ?>" class="btn btn-default">
		<input type="submit" class="btn btn-default" value=" OK ">
	</div>
</form>
<br>

<table border="0" width="100%" align="center" class="table table-striped">
<thead>
<tr>
<th align="center"><b>N°</b></td>
<th><b>Cliente</b></td>
<th class="sumir_coluna"><b>Data de criação</b></td>
<th class="sumir_coluna"><b>Data de liberação</b></td>
<th><b>Data de validade</b></td>
<th class="sumir_coluna" style="width:110px;" align="center"><b>Ações</b></td>
<th><b>Status</b></td>
<?php
/*  ******** REMOVER PEDIDO *********** */
if ($_SESSION['user']['nivel'] == '1') {
?>
<th class="sumir_coluna" style="width: 37px;" align="center"><b></b></td>
<?php
}
/* ********* REMOVER PEDIDO *********** */
?>
</tr>
</thead>
<?php



$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;


$per_page = (int)(!isset($_GET["qtde"]) ? 50 : $_GET["qtde"]); // 10; // Set how many records do you want to display per page.
 
$startpoint = ($page * $per_page) - $per_page;

if ($_GET["data_inicio"] != "" && $_GET["data_venc"] != "") {
	$statement = "`pedidos` p INNER JOIN (SELECT pedido, MAX(id) AS maxid FROM `pedidos` GROUP BY pedido) MXID ON p.pedido = MXID.pedido AND p.id = MXID.maxid ".$pesquisa." AND p.pedido = ANY (SELECT tt.id_ped FROM `pedidos_aprova` tt INNER JOIN (SELECT `id_ped`, MAX(`id`) AS `id` FROM `pedidos_aprova` GROUP BY `id_ped`) groupedtt ON tt.id_ped = groupedtt.id_ped AND tt.id = groupedtt.id AND DATE( DATE_ADD( `data` , INTERVAL 10 DAY ) ) BETWEEN '".$_GET["data_inicio"]." 00:00:00' AND '".$_GET["data_venc"]." 23:59:59' )";
} elseif ($_GET["data_venc"] != "") {
	$statement = "`pedidos` p INNER JOIN (SELECT pedido, MAX(id) AS maxid FROM `pedidos` GROUP BY pedido) MXID ON p.pedido = MXID.pedido AND p.id = MXID.maxid ".$pesquisa." AND p.pedido = ANY (SELECT tt.id_ped FROM `pedidos_aprova` tt INNER JOIN (SELECT `id_ped`, MAX(`id`) AS `id` FROM `pedidos_aprova` GROUP BY `id_ped`) groupedtt ON tt.id_ped = groupedtt.id_ped AND tt.id = groupedtt.id AND DATE('".$_GET["data_venc"]."') = DATE( DATE_ADD( `data` , INTERVAL 10 DAY ) ) ) ORDER BY {$ordem} {$ascdesc}"; // Change `records` according to your table name.   "SELECT * FROM `log_acesso` ".$pesquisa." ORDER BY `id` DESC LIMIT ".$limit
} else {
	$statement = "`pedidos` p INNER JOIN (SELECT pedido, MAX(id) AS maxid FROM `pedidos` GROUP BY pedido) MXID ON p.pedido = MXID.pedido AND p.id = MXID.maxid ".$pesquisa." ORDER BY {$ordem} {$ascdesc}"; // Change `records` according to your table name.   "SELECT * FROM `log_acesso` ".$pesquisa." ORDER BY `id` DESC LIMIT ".$limit
}
/*
echo "<pre>";
echo "SELECT * FROM {$statement} LIMIT {$startpoint} , {$per_page}";
echo "</pre>";
die();
*/
$results = mysqli_query($conn,"SELECT * FROM {$statement} LIMIT {$startpoint} , {$per_page}");

//echo $statement;

if (mysqli_num_rows($results) != 0) {

//echo $statement;
     
    // displaying records.
    while ($row_pedidos = mysqli_fetch_array($results)) {

$no_pedido = $row_pedidos['pedido'];

$data_ped0 = $row_pedidos['data'];
$data_ped0 = strtotime($data_ped0);
$hora_ped = date("H:i:s", $data_ped0);
$data_ped = date("d/m/Y", $data_ped0);

$liberacao = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM  `pedidos_aprova` WHERE  `id_ped` = '{$no_pedido}' ORDER BY  `id` DESC LIMIT 0 , 1"));

/*
echo "<pre>";
echo $liberacao["data"];
echo "SELECT * FROM  `pedidos_aprova` WHERE  `id_ped` = '{$no_pedido}' ORDER BY  `id` DESC LIMIT 0 , 1";
echo "</pre>";
*/

$data_lib0 = $liberacao["data"];
$data_lib0 = strtotime($data_lib0);
$hora_liberacao = date("H:i:s", $data_lib0);
$data_liberacao = date("d/m/Y", $data_lib0);

/*
SELECT * FROM  `pedidos_aprova` WHERE  `id_ped` LIKE  '555' ORDER BY  `id` DESC LIMIT 0 , 1
*/

$data_validade = date('Y-m-d', strtotime("+10 days", $data_lib0));
if (strtotime($data_validade) > strtotime('now') ) {
	$cor_validade = "0000FF";;
} else {
	$cor_validade = "FF0000";;
}

$data_validade = date('d/m/Y', strtotime($data_validade));

if ($row_pedidos["status"] == "1") { $status_txt = "Em análise";
} elseif ($row_pedidos["status"] == "2") { $status_txt = "Aguardando liberação";
} elseif ($row_pedidos["status"] == "3") { $status_txt = "Liberado";
} elseif ($row_pedidos["status"] == "4") { $status_txt = "Aprovado";
} elseif ($row_pedidos["status"] == "5") { $status_txt = "Recusado";
}


?>
	<tr>
		<td align="center" style="padding-left:5px; max-width: 50px;"><?php echo sprintf('%05d', $row_pedidos['pedido']); ?></td>
		<td style="max-width: 200px;"><?php echo utf8_encode($row_pedidos['nome_cliente']); ?></td>
		<td class="sumir_coluna" title="<?php echo $hora_ped; ?>"><?php echo $data_ped; ?></td>
		<td class="sumir_coluna" title="<?php echo $hora_liberacao; ?>"><?php echo $data_liberacao; ?></td>
		<td><font color="#<?php echo $cor_validade; ?>"><?php echo $data_validade; ?></font></td>
		<td class="sumir_coluna" style="max-width: 65px;" align="center"><?php
if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
	if ($row_pedidos['status'] == "1") {
		?><a href="pedidos.php?acao=analisar&pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>"><img src="images/icons/analisar.png" title="Analisar" border="0" width="30" height="30" style="opacity: 0.6; filter: alpha(opacity=60);"></a>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Resumo" class="btn  btn-info btn-xs tooltips" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
<?php
	} elseif ($row_pedidos['status'] == "2") {
?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Resumo" class="btn  btn-info btn-xs tooltips" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
<?php
	} elseif ($row_pedidos['status'] == "3") {
?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Retornar para análise" class="btn  btn-warning btn-xs tooltips" onclick="window.location='resumo.php?acao=reativa&pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>&id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-reply"></i> </button>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Resumo" class="btn  btn-info btn-xs tooltips" onclick="window.location='resumo.php?pedido=<?php echo sprintf('%05d', $row_pedidos['pedido']); ?>'"><i class="fa fa-file-text"></i> </button>
<?php /*		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar PDF do orçamento" class="btn  btn-danger btn-xs tooltips" href="gera_pdf.php?ped=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a> */ ?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Aprovar orçamento" class="btn  btn-success btn-xs tooltips" onclick="window.location='pedidos.php?acao=aprova&pedido=<?php echo $row_pedidos['pedido']; ?>&id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-check"></i> </button>
<?php
	} elseif ($row_pedidos['status'] == "4") {
?>
<?php /*		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar PDF do orçamento" class="btn  btn-danger btn-xs tooltips" href="gera_pdf.php?ped=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a> */ ?>
		<a href="pdf_resumo.php?pedido=<?php echo $row_pedidos['pedido']; ?>" target="_blank"><img src="images/icon.pdf.png" border="0" style="padding:6px 0;" alt="Gerar PDF do resumo" title="Gerar PDF do resumo"></a>
<?php
	} elseif ($row_pedidos['status'] == "5") {
?>
		<a href="resumo.php?acao=reativa&pedido=<?php echo $row_pedidos['pedido']; ?>&id=<?php echo $row_pedidos['id']; ?>">reativar</a>
<?php
	}
} elseif ($_SESSION['user']['nivel'] == '3') {
	if ($row_pedidos['status'] == "1"|| $row_pedidos['status'] == "2") {
		?><img src="images/aguarde.png" border="0" width="30" height="30" style="opacity: 0.6; filter: alpha(opacity=60); padding:6px 0;" alt="Aguarde a liberação do orçamento" title="Aguarde a liberação do orçamento"><?php
	} elseif ($row_pedidos['status'] == "3" || $row_pedidos['status'] == "4") {
		?>
<?php /*		<a data-placement="top" data-toggle="tooltip" type="button" data-original-title="Baixar PDF do orçamento" class="btn  btn-danger btn-xs tooltips" href="gera_pdf.php?ped=<?php echo $row_pedidos['pedido']; ?>" download><i class="fa fa-download"></i></a> */ ?>
        <?php if ($row_pedidos['status'] == "3") { ?>
		<button title="" data-placement="top" data-toggle="tooltip" type="button" data-original-title="Aprovar orçamento" class="btn  btn-success btn-xs tooltips" onclick="window.location='pedidos.php?acao=aprova&pedido=<?php echo $row_pedidos['pedido']; ?>&id=<?php echo $row_pedidos['id']; ?>'"><i class="fa fa-check"></i> </button>
        <?php
        }
	}
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
	<td colspan="9" align="center"><br>Nenhum registro encontrado.<br><br></td>
</tr>
<?php
}
?>
</table>

<center>
<?php
if ($_GET["data_venc"] == "") {
	echo pagination($query,$per_page,$page,$pesquisa,$ordem,$ascdesc,$url='?qtde='.$limit.'&');
}
?>
<br><br>
</center>

<?php
require("rodape.php");
?>