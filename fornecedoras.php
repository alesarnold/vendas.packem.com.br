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
?>
<div class="page-title">
	<div class="title_left">
		<h1>Fornecedoras</h1>
	</div>
</div>
<div class="clearfix"></div>

<div class="x_panel">
<div class="x_content">


<?
if($_GET["acao"] == "update") {
    if (!empty($_POST)) {
        foreach ($_POST as $chave => $valor) {
            foreach ($valor as $k => $v) {
                $query_atualiza = "UPDATE `fornecedora` SET ".$chave." = '".$v."' WHERE id = '".$k."';"."\n\r";
                mysqli_query($conn,$query_atualiza);
                $query_atualiza = "";
            }
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
            <a href="fornecedoras.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> &nbsp;Voltar</a>
            <br>
            <br>
            <br>
            <br>
        </center>
        <?    
    }
} else {
?>

<br>
<form name="fornecedoras" class="form-horizontal form-label-left" action="fornecedoras.php?acao=update" method="post">
<?
$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora`");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
?>
    <input type="text" name="apelido[<? echo $row_fornec['id']; ?>]" value="<? echo $row_fornec['apelido']; ?>" class="form-control"><br>
    <textarea class="form-control" name="texto[<? echo $row_fornec['id']; ?>]" rows="1"><? echo $row_fornec['texto']; ?></textarea><hr>
<?
}
?>

<center>
<br>
<button type="submit" class="btn btn-success"><i class="fa fa-refresh"></i> Atualizar </button>
&nbsp;&nbsp;
<button type="reset" class="btn btn-primary"><i class="fa fa-ban"></i> Redefinir</button>
<br><br>
</center>
</form>


<?
}
?>

</div>
</div>




<?php
require("rodape.php");
?>