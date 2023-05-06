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

if ($_GET["qtde"] != "") {
	$limit = $_GET["qtde"];
} else {
	$limit = 15;
}

if ($_GET["usuario"] != "") {
	$pesquisa = "WHERE `id_user` LIKE '".$_GET["usuario"]."'";
	$usuario = $_GET["usuario"];
}


function pagination($query,$per_page=10,$page=1,$pesquisa,$url='?'){   
    global $conn;
    $query = "SELECT COUNT(*) as `num` FROM `log_acesso` ".$pesquisa." ORDER BY `id` DESC";
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
		<h1>Log de acessos</h1>
	</div>
</div>
<div class="clearfix"></div>

<div class="x_panel">
	<div class="x_title">
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<br>
<form action="log_acesso.php" name="filtro" method="GET" class="form-inline">
<div class="form-group">
<label class="control-label" for="usuario">Usuário: </label>
<select style="height: 34px; width: 180px;" class="btn btn-default" name="usuario" id="usuario" onchange="form.submit();">
<option value="">Todos</option>
<?php
$query_usuarios = mysqli_query($conn,"SELECT * FROM `users` WHERE `status` LIKE '1' ORDER BY `username` ASC");
while ($usuarios = mysqli_fetch_array($query_usuarios)){
?>
<option value="<?php echo $usuarios["id"]; ?>"<?php if ($usuario == $usuarios["id"]) { echo " selected"; } ?>><?php echo utf8_encode($usuarios["nome"]); ?></option>
<?php
}
?>
</select>
</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<div class="form-group">
<label class="control-label" for="qtde">Qtde.: </label>
<select style="height: 34px; width: 70px;" class="btn btn-default" name="qtde" id="qtde" onchange="form.submit();">
<option<?php if ($limit == 15) { echo " selected"; } ?>>15</option>
<option<?php if ($limit == 30) { echo " selected"; } ?>>30</option>
<option<?php if ($limit == 50) { echo " selected"; } ?>>50</option>
<option<?php if ($limit == 100) { echo " selected"; } ?>>100</option>
<option<?php if ($limit == 200) { echo " selected"; } ?>>200</option>
<option<?php if ($limit == 500) { echo " selected"; } ?>>500</option>
<option<?php if ($limit == 1000) { echo " selected"; } ?>>1000</option>
</select>
</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<div class="form-group">
<a href="log_acesso.php" class="btn btn-dark"> LIMPAR FILTROS </a>
</div>
</form>
<br><br>
<table border="0" class="table table-striped">
<thead>
<tr>
	<th><b>Usuário</b></td>
	<th class="sumir_coluna"><b>E-mail</b></td>
	<th><b>Data</b></td>
	<th><b>Hora</b></td>
	<th class="sumir_coluna"><b>IP</b></td>
</tr>
</thead>
<?php
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
if ($page <= 0) $page = 1;


$per_page = (int)(!isset($_GET["qtde"]) ? 15 : $_GET["qtde"]); // 10; // Set how many records do you want to display per page.
 
$startpoint = ($page * $per_page) - $per_page;
 
$statement = "`log_acesso` ".$pesquisa." ORDER BY `id` DESC"; // Change `records` according to your table name.   "SELECT * FROM `log_acesso` ".$pesquisa." ORDER BY `id` DESC LIMIT ".$limit
  
$results = mysqli_query($conn,"SELECT * FROM {$statement} LIMIT {$startpoint} , {$per_page}");
 
if (mysqli_num_rows($results) != 0) {
     
    // displaying records.
    while ($log_acesso = mysqli_fetch_array($results)) {
?>
<tr>
	<td><?php echo $log_acesso["nome"]; ?></td>
	<td class="sumir_coluna"><a href="mailto:<?php echo $log_acesso["email"]; ?>"><?php echo $log_acesso["email"]; ?></a></td>
	<td><?php echo $log_acesso["data"]; ?></td>
	<td><?php echo $log_acesso["hora"]; ?></td>
	<td class="sumir_coluna"><a href="http://ipinfo.io/<?php echo $log_acesso["ip"]; ?>" target="_blank"><?php echo $log_acesso["ip"]; ?></a></td>
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
	<center>
		<button onclick="window.location='log_acesso.php'" class="btn btn-info"><i class="fa fa-undo"></i> Mostrar todos</button>
	</center>
<?php
}

 // displaying paginaiton.
echo pagination($statement,$per_page,$page,$pesquisa,$url='?usuario='.$usuario.'&qtde='.$per_page.'&');

?>
<br>
<?php
/*
if ($_SESSION['user']['id'] == '2') {
	?>
	<center><a href="limpar_acesso.php">LIMPAR</a></center>
	<?php
}
*/
?>
</div>
</div>

<?php
require("rodape.php");
?>