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

if ($_SESSION['user']['id'] != '1' && $_SESSION['user']['id'] != '2') {
	echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
	require("rodape.php"); 
	die();
}


?>
<div class="page-title">
	<div class="title_left">
		<h1>Política de vendas</h1>
	</div>
</div>
<div class="clearfix"></div>

<div class="x_panel">
<div class="x_content">

<?php

if($_GET["acao"] == "update") {

if (!empty($_POST)) {
/*
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
    die();
*/
    $query_atualiza = "UPDATE `politica` SET `titulo` = '".$_POST['titulo']."', `texto` = '".$_POST['texto']."' WHERE id = 1";
    mysqli_query($conn,$query_atualiza);

	Log_Sis("",$_SESSION['user']['id'],$_SESSION['user']['nome'],"Fez alteracoes na POLITICA DE VENDAS.");
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
	<h2 style="line-height:30px;">Dados atualizados com sucesso.</h2>
	<br>
	<br>
	<a href="politica.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> &nbsp;Voltar</a>
	<br>
	<br>
	<br>
	<br>
</center>
<?php
} else {
    $query_politica = mysqli_query($conn,"SELECT * FROM `politica` WHERE `id` = '1'");
    $row_politica = mysqli_fetch_array($query_politica);

    $tit_politica = $row_politica['titulo'];
    $txt_politica = $row_politica['texto'];
?>
<br>
<form name="contabilidade" class="form-horizontal form-label-left" action="politica.php?acao=update" method="post">
<input type="text" name="titulo" value="<? echo $tit_politica; ?>" class="form-control"><br>
<textarea class="form-control" name="texto" rows="8"><? echo $txt_politica; ?></textarea>
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