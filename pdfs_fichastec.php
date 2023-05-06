<?php 

    require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
/*        ?><body onload="new TabbedNotification({ title: 'Redirecionando!', text: 'Você está sendo redirecionado para fazer seu login novamente.', type: 'info', sound: false })"><?php */
//        require("rodape.php");
        die(); 
    } 


	$query_niveis = "SELECT nivel FROM niveis"; 
	try { 
		$stmt_niveis = $db->prepare($query_niveis); 
		$stmt_niveis->execute(); 
	}
	catch(PDOException $ex) { 
		die("Failed to run query: " . $ex->getMessage()); 
	} 
	$niveis = $stmt_niveis->fetchAll(); 
	$_SESSION['nivel'] = $niveis;

    require("cabecalho.php"); 


if ($_SESSION['user']['nivel'] != '1' and $_SESSION['user']['nivel'] != '2') {
?>
<div class="clearfix"></div>
<?php
    echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
	require("rodape.php");
    die(); 
}

require("pdf_qualidade/index.php");

require("rodape.php");
?>