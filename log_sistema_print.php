<?php

    require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 



?>
<!DOCTYPE html>
<html>
<head>
<style>

@page {
	size: A4;
	margin: 20pt;
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
    border-bottom: 1px solid #ccc;
    padding: 10px 0;
}
th {
	text-align: left;
}
td {
    padding: 2px 0;
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

.footer {
    width: 100%;
    text-align: center;
    position: fixed;
}
  
  
</style>
</head>

<body onload="window.print()">
<?php

if ($_SESSION['user']['nivel'] != '1' && $_SESSION['user']['nivel'] != '2') {
    echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="pedidos.php">Voltar</a><br><br><br><br><br></center>';
    require("rodape.php"); 
    die();
}

/*
if ($_GET["usuario"] != "" && $_GET["inicio"] != "" && $_GET["termino"] != "") {
	$pesquisa = "WHERE `data` BETWEEN '".$_GET["inicio"]." 00:00:00' AND '".$_GET["termino"]." 23:59:59' AND `id_user` LIKE '".$_GET["usuario"]."'";
	$usuario = $_GET["usuario"];
	$inicio = $_GET["inicio"];
	$termino = $_GET["termino"];
} elseif ($_GET["usuario"] != "") {
	$pesquisa = "WHERE `id_user` LIKE '".$_GET["usuario"]."'";
	$usuario = $_GET["usuario"];
} elseif ($_GET["inicio"] != "" && $_GET["termino"] != "") {
	$pesquisa = "WHERE `data` BETWEEN '".$_GET["inicio"]." 00:00:00' AND '".$_GET["termino"]."  23:59:59'";
	$inicio = $_GET["inicio"];
	$termino = $_GET["termino"];
} 
*/
if ($_GET["usuario"] != "" && $_GET["inicio"] != "" && $_GET["termino"] != "") {
	$pesquisa = "WHERE `data` BETWEEN '".$_GET["inicio"]." 00:00:00' AND '".$_GET["termino"]." 23:59:59' AND `id_user` LIKE '".$_GET["usuario"]."'";
	$usuario = $_GET["usuario"];
	$inicio = $_GET["inicio"];
	$termino = $_GET["termino"];
} elseif ($_GET["usuario"] != "") {
	$pesquisa = "WHERE `id_user` LIKE '".$_GET["usuario"]."'";
	$usuario = $_GET["usuario"];
} elseif ($_GET["inicio"] != "" && $_GET["termino"] != "") {
	$pesquisa = "WHERE `data` BETWEEN '".$_GET["inicio"]." 00:00:00' AND '".$_GET["termino"]."  23:59:59'";
	$inicio = $_GET["inicio"];
	$termino = $_GET["termino"];
} elseif ($_GET["orcamento"] != "") {
	$pesquisa = "WHERE `pedido` LIKE '".$_GET["orcamento"]."'";
	$orcamento = $_GET["orcamento"];
} 


?>
<h1>Log de atividades</h1>



<table border="0" width="100%">
<thead>
<tr>
	<th><b>Usuário</b></td>
	<th><b>Ação</b></td>
	<th><b>Data</b></td>
	<th><b>Hora</b></td>
</tr>
</thead>
<?php


 
$statement = "`log_sistema` ".$pesquisa." ORDER BY `id` DESC";// LIMIT ".$limit; // Change `records` according to your table name.   "SELECT * FROM `log_acesso` ".$pesquisa." ORDER BY `id` DESC LIMIT ".$limit
  
$results = mysqli_query($conn,"SELECT * FROM {$statement}");
 
if (mysqli_num_rows($results) != 0) {
     
    // displaying records.
    while ($log_sistema = mysqli_fetch_array($results)) {

$phpdate = strtotime( $log_sistema["data"] );
$data = date( 'd/m/Y', $phpdate );
$hora = date( 'H:i:s', $phpdate );

?>
<tr>
	<td><?php echo $log_sistema["nome"]; ?></td>
	<td><?php echo $log_sistema["desc"]; ?></td>
	<td><?php echo $data; ?></td>
	<td><?php echo $hora; ?></td>
</tr>
<?php
    }
?>
</table>
<br>
<?php
} else {
?>
	<tr>
	<td colspan="5" height="150" align="center" style="vertical-align: middle;"><h2><i class="fa fa-times"></i> Nenhum registro encontrado.</h2></td>
	</tr>
	</table><br>
<?php
}

?>
<br>


</body>

</html>