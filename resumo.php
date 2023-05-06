<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

require("common.php"); 


/*
if(date("N") <= 5) {
	$data_hoje = date("d/m/Y");
	if(date("N") == 1) {
		$data_ontem = date("d/m/Y", time() - 3 * 60 * 60 * 24);
	} else {
		$data_ontem = date("d/m/Y", time() - 60 * 60 * 24);
	}	$row = 1;
	if (($handle = fopen("https://ptax.bcb.gov.br/ptax_internet/consultaBoletim.do?method=gerarCSVFechamentoMoedaNoPeriodo&ChkMoeda=61&DATAINI=".$data_ontem."&DATAFIM=".$data_hoje, "r")) !== FALSE) {
		while (($taxa_usd = fgetcsv($handle, 1000, ";")) !== FALSE) {
			$num = count($taxa_usd);
			$row++;
			$taxa_data[] = $taxa_usd[0];
			$taxa_dolar_compra[] = $taxa_usd[4];
			$taxa_dolar_venda[] = $taxa_usd[5];
		}
		fclose($handle);
	}
	$taxa_query = "INSERT INTO `taxa_dolar` (`id`, `dia`, `compra`, `venda`, `data`) VALUES (NULL, '".$taxa_data[0]."', '".str_replace(",",".",$taxa_dolar_compra[0])."', '".str_replace(",",".",$taxa_dolar_venda[0])."', '".date("Y-m-d H:i:s")."');";
	if(!mysqli_query($conn,$taxa_query)) { die('Não foi possível gravar taxa do dólar: ' . mysqli_error($conn)); }
}
//echo "https://ptax.bcb.gov.br/ptax_internet/consultaBoletim.do?method=gerarCSVFechamentoMoedaNoPeriodo&ChkMoeda=61&DATAINI=".$data_ontem."&DATAFIM=".$data_hoje;
*/


if(empty($_SESSION['user'])) 
{ 
	redirect("login.php"); 
	die("Redirecting to login.php"); 
} 

if ($_SESSION['user']['nivel'] == '6' || $_SESSION['user']['nivel'] == '4') {
	redirect("qualidade.php"); 
	die("Redirecting to qualidade.php"); 
}

require("cabecalho.php"); 

if ($_SESSION['user']['nivel'] == '3' && $_GET["acao"] != "recusa" && $_GET["acao"] != "aprova") {
    echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
	require("rodape.php");
    die(); 
//	die("<br><br><br><center>Você não tem permissão para acessar esse conteúdo.<br><br><br><br><a href=\"pedidos.php\">Voltar</a></center>"); 
}

if (($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') && $_GET["acao"] == "atualiza_fornec") {

	$query_altera_fornec = "UPDATE `pedidos` SET `fornecedora` = '".$_POST["fornecedora"]."' WHERE `pedidos`.`id` = '".$_POST["id_orc"]."' LIMIT 1";
	$altera_fornec = mysqli_query($conn, $query_altera_fornec);
	if (!$altera_fornec) {
		die('Não foi possível alterar fornecedora deste orçamento: ' . mysql_error());
	} else {
		redirect("resumo.php?pedido=".$_POST["id_pedido"]);
	}
	print_r($_POST);
	die();
}

?>
<style>
  .right_col {
    font-size:14px;
  }
</style>
<div class="page-title">
  <div class="title_left">
    <h1>Resumo de orçamento
    </h1>
  </div>
</div>
<div class="clearfix">
</div>
<div class="x_panel">
  <div class="x_title">
    <?php if ($_SESSION['user']['nivel'] == '4') { ?>
    <h2>Ficha técnica
    </h2>
    <?php } elseif ($_SESSION['user']['nivel'] == '3' && $_GET["acao"] == "recusa") { ?>
	<h2>Recusar orçamento?
	</h2>
	<?php } else { ?>
    <h2>Custo
    </h2>
    <?php } ?>
    <ul class="nav navbar-right panel_toolbox">
      <li>
        <a class="collapse-link">
          <i class="fa fa-chevron-up">
          </i>
        </a>
      </li>
    </ul>
    <div class="clearfix">
    </div>
  </div>
  <div class="x_content">
<?php

if ($_SESSION['user']['nivel'] == '3' && $_GET["acao"] == "recusa") {

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$motivos[1] = "O preço estava fora da meta do cliente.";
		$motivos[2] = "O prazo de pagamento não foi aceito pelo cliente.";
		$motivos[3] = "As condições de entrega não foram aceitas pelo cliente.";
		$motivos[4] = "Desistencia da compra.";
		$motivos[5] = "Outro motivo.";

		$sql_repres = "UPDATE `pedidos_repres` SET `id_motivo` = '".$_POST["id_motivo"]."', `det_motivo` = '".$_POST["det_motivo"]."', `status` = '6' WHERE `pedido` = '".$_GET["pedido"]."' LIMIT 1";
		$repres = mysqli_query($conn, $sql_repres);
		if (!$repres) {
			die('Não foi possível recusar orçamento: ' . mysql_error());
		}

		$sql_pedido = "UPDATE `pedidos` SET `status` = '5' WHERE `pedidos`.`id` = '" . $_GET["id"] . "' LIMIT 1";
		$recusa_pedido = mysqli_query($conn, $sql_pedido);
		if (!$recusa_pedido) {
			die('Não foi possível recusar orçamento: ' . mysql_error());
		}

		Log_Sis($pedido, $_SESSION['user']['id'], $_SESSION['user']['nome'], "Recusou o orçamento: " . sprintf('%05d', $pedido) . ".");
		$no_pedido = sprintf('%05d', $pedido);
		$ee_mensagem = '<br /><b>O orçamento ' . $no_pedido . ' foi recusado pelo representante '.$_SESSION['user']['nome'].'.</b><br /><br /><b>Motivo:</b><br />'.$motivos[$_POST["id_motivo"]].'<br /><br /><b>Detalhamento:</b><br />'.$_POST["det_motivo"].'<br /><br />Para acessá-lo clique aqui: <a href="'.$_SERVER['SERVER_NAME'].'/resumo.php?pedido=' . $no_pedido . '">'.$_SERVER['SERVER_NAME'].'/resumo.php?pedido=' . $no_pedido . '</a><br /><br />Orçamento recusado em ' . date("d/m/y") . ' às ' . date("H:i:s");
		//$ee_sql = mysqli_query($conn, "SELECT * FROM `users` WHERE `nivel` LIKE '1' AND `status` LIKE '1'");
		$ee_sql = mysqli_query($conn, "SELECT * FROM `users` WHERE (`id` LIKE '1' OR `id` LIKE '4') AND `status` LIKE '1'");
		while ($ee_envia = mysqli_fetch_array($ee_sql)) {
			Envia_Email($vendedor, 'mellogustavo@gmail.com', $ee_envia["email"], 'Orcamento recusado', $ee_mensagem, $no_pedido);
		}
/*
		echo "<pre>";
		print_r($_POST);
		echo "</pre>";
*/
		redirect("pedidos.php");
		die();
	}
?>
<form method="POST">
<div class="col-md-8 col-md-offset-3 col-sm-10 col-sm-offset-2">
<br><center><h2>Qual o motivo deste orçamento estar sendo recusado?</h2></center><br>
<label><input type="radio" name="id_motivo" value="1" required> O preço estava fora da meta do cliente.</label><br>
<label><input type="radio" name="id_motivo" value="2" required> O prazo de pagamento não foi aceito pelo cliente.</label><br>
<label><input type="radio" name="id_motivo" value="3" required> As condições de entrega não foram aceitas pelo cliente.</label><br>
<label><input type="radio" name="id_motivo" value="4" required> Desistencia da compra.</label><br>
<label><input type="radio" name="id_motivo" value="5" required> Outro motivo.</label><br>
<br><center><h2>Descreva detalhadamente o motivo:</h2></center><br>
<textarea class="form-control" name="det_motivo" rows="5" required></textarea><br>
<center><button type="submit" class="btn  btn-danger tooltips"><i class="fa fa-close"></i> RECUSAR ORÇAMENTO</button></center>
</div>
</form>
<?php
	require("rodape.php");
	die();
}

if ($_GET["acao"] == "envia") {
	$id_pedido = $_POST["id_ped"];
	$pedido = $_POST["pedido"];
	$sql_envia_aprovacao = "UPDATE `pedidos` SET `status` = '2' WHERE `pedidos`.`id` = '" . $id_pedido . "' LIMIT 1";
	$envia_aprovacao = mysqli_query($conn, $sql_envia_aprovacao);
	if (!$envia_aprovacao) {
		die('Não foi possível enviar orçamento para aprovação: ' . mysql_error());
	}

	Log_Sis($pedido, $_SESSION['user']['id'], $_SESSION['user']['nome'], "Enviou para aprovação orçamento: " . sprintf('%05d', $pedido) . ".");
	$no_pedido = sprintf('%05d', $pedido);
	$ee_mensagem = '<br /><b>Solicitação de liberação de orçamento</b><br /><br />Para acessá-lo clique aqui: <a href="'.$_SERVER['SERVER_NAME'].'/resumo.php?pedido=' . $no_pedido . '">'.$_SERVER['SERVER_NAME'].'/resumo.php?pedido=' . $no_pedido . '</a><br /><br />Solicitação enviada em ' . date("d/m/y") . ' às ' . date("H:i:s") . ' por ' . $_SESSION['user']['nome'];
	//$ee_sql = mysqli_query($conn, "SELECT * FROM `users` WHERE `nivel` LIKE '1' AND `status` LIKE '1'"); // `nivel` LIKE '1' OR 
	$ee_sql = mysqli_query($conn,"SELECT * FROM `users` WHERE (`id` LIKE '1' OR `id` LIKE '2' OR `id` LIKE '4') AND `status` LIKE '1'");
	while ($ee_envia = mysqli_fetch_array($ee_sql)) {
		Envia_Email($vendedor, 'mellogustavo@gmail.com', $ee_envia["email"], 'Liberacao de orcamento', $ee_mensagem, $no_pedido);
	}

	redirect("pedidos.php");
	die("Redirecionando...");
}

if ($_GET["acao"] == "correcoes") {

	// print_r($_POST);

	$id_pedido = $_POST["id_ped"];
	$pedido = $_POST["pedido"];
	$correcoes = mysqli_real_escape_string($conn, $_POST["correcoes"]);
	$query_pedidos = mysqli_query($conn, "SELECT * FROM `pedidos` WHERE `id` = '" . $id_pedido . "' ");
	$pedido_corr = mysqli_fetch_array($query_pedidos);
	if ($pedido_corr["correcoes"] != "") {
		$correcoes_bd = mysqli_real_escape_string($conn, $pedido_corr["correcoes"]);
		$correcoes = $correcoes_bd . "\r\n\r\n" . $correcoes;
	}

	// echo $correcoes;

	$sql_envia_correcao = "UPDATE `pedidos` SET `correcoes` = '" . $correcoes . "', `status` = '1' WHERE `pedidos`.`id` = '" . $id_pedido . "' LIMIT 1";
	$envia_correcao = mysqli_query($conn, $sql_envia_correcao);
	if (!$envia_correcao) {
		die('Não foi possível enviar correções do orçamento: ' . mysql_error());
	}

	Log_Sis($pedido, $_SESSION['user']['id'], $_SESSION['user']['nome'], "Solicitou correções no orçamento: " . sprintf('%05d', $pedido) . ".");
	$no_pedido = sprintf('%05d', $pedido);
	$ee_mensagem = '<br /><b>Solicitação de correção de orçamento</b><br /><br />Para acessá-lo clique aqui: <a href="'.$_SERVER['SERVER_NAME'].'/resumo.php?pedido=' . $no_pedido . '">'.$_SERVER['SERVER_NAME'].'/resumo.php?pedido=' . $no_pedido . '</a><br /><br />Solicitação enviada em ' . date("d/m/y") . ' às ' . date("H:i:s");
	//$ee_sql = mysqli_query($conn, "SELECT * FROM `users` WHERE `nivel` LIKE '1' AND `status` LIKE '1'");
	$ee_sql = mysqli_query($conn,"SELECT * FROM `users` WHERE (`id` LIKE '1' OR `id` LIKE '4') AND `status` LIKE '1'");
	while ($ee_envia = mysqli_fetch_array($ee_sql)) {
		Envia_Email($vendedor, 'mellogustavo@gmail.com', $ee_envia["email"], 'Correcao de orcamento', $ee_mensagem, $no_pedido);
	}

	redirect("resumo.php?pedido=" . $pedido);
	die("Redirecionando...");
}

if ($_GET["acao"] == "libera") {
	$id_pedido = $_GET["id"];
	$pedido = $_GET["pedido"];
	$sql_envia_correcao = "UPDATE `pedidos` SET `status` = '3' WHERE `pedidos`.`id` = '" . $id_pedido . "' LIMIT 1";
	$envia_correcao = mysqli_query($conn, $sql_envia_correcao);
	if (!$envia_correcao) {
		die('Não foi possível enviar correções do orçamento: ' . mysql_error());
	}

	$sql_libera_repres = "UPDATE `pedidos_repres` SET `data_liberacao` = '".date('Y-m-d')."', `data_venc1` = '".date('Y-m-d', strtotime("+5 days"))."', `status` = '2' WHERE `pedido` = '" . (float)$pedido . "' LIMIT 1";
	$libera_repres = mysqli_query($conn, $sql_libera_repres);
	if (!$libera_repres) {
		die('Não foi possível liberar repres.: ' . mysql_error());
	}

	Log_Sis($pedido, $_SESSION['user']['id'], $_SESSION['user']['nome'], "Liberou o orçamento: " . sprintf('%05d', $pedido) . ".");

	// INSERT INTO `pedidos_aprova` (`id`, `id_ped`, `revisao`, `id_user`, `nome`, `data`) VALUES (NULL, '56', '2', '1', 'Alexandre', '2015-08-18 11:32:00');
/*
echo "<pre>";
echo "INSERT INTO `pedidos_aprova` (`id`, `id_ped`, `id_user`, `nome`, `data`) VALUES (NULL, '" . (float)$pedido . "', '" . $_SESSION['user']['id'] . "', '" . $_SESSION['user']['nome'] . "', '" . date('Y-m-d H:i:s') . "');";
echo "</pre>";
die();
*/

	$sql_aprova = "INSERT INTO `pedidos_aprova` (`id`, `id_ped`, `id_user`, `nome`, `data`) VALUES (NULL, '" . (float)$pedido . "', '" . $_SESSION['user']['id'] . "', '" . $_SESSION['user']['nome'] . "', '" . date('Y-m-d H:i:s') . "');";
	$pedido_aprova = mysqli_query($conn, $sql_aprova);
	$no_pedido = sprintf('%05d', $pedido);
	$ee_mensagem = '<br /><b>O orçamento ' . $no_pedido . ' foi liberado!</b><br /><br />Para acessá-lo clique aqui: <a href="'.$_SERVER['SERVER_NAME'].'/pedidos.php">'.$_SERVER['SERVER_NAME'].'/pedidos.php</a><br /><br />Orçamento liberado em ' . date("d/m/y") . ' às ' . date("H:i:s");
	$ee_sql_pedido = mysqli_query($conn, "SELECT * FROM `pedidos` WHERE `id` LIKE '" . $id_pedido . "' LIMIT 1");
	$ee_pedido = mysqli_fetch_array($ee_sql_pedido);
	$ee_sql = mysqli_query($conn, "SELECT * FROM `users` WHERE `id` LIKE '" . $ee_pedido["id_vend"] . "'");
	//$ee_sql = mysqli_query($conn, "SELECT * FROM `users` WHERE (`id` LIKE '1' OR `id` LIKE '4') AND `status` LIKE '1'");
	while ($ee_envia = mysqli_fetch_array($ee_sql)) {
		Envia_Email($vendedor, 'mellogustavo@gmail.com', $ee_envia["email"], 'Liberacao de orcamento', $ee_mensagem, $no_pedido);
	}

	//$ee_sql2 = mysqli_query($conn, "SELECT * FROM `users` WHERE `nivel` LIKE '1' AND `status` LIKE '1'");
	$ee_sql2 = mysqli_query($conn, "SELECT * FROM `users` WHERE (`id` LIKE '1' OR `id` LIKE '4') AND `status` LIKE '1'");
	while ($ee_envia2 = mysqli_fetch_array($ee_sql2)) {
		Envia_Email($vendedor, 'mellogustavo@gmail.com', $ee_envia2["email"], 'Liberacao de orcamento', $ee_mensagem, $no_pedido);
	}

	redirect("resumo.php?pedido=" . $pedido);
	die("Redirecionando...");
}

if ($_GET["acao"] == "recusa") {
	$pedido = $_GET["pedido"];
	$id_pedido = $_GET["id"];
	$sql_envia_correcao = "UPDATE `pedidos` SET `status` = '5' WHERE `pedidos`.`id` = '" . $id_pedido . "' LIMIT 1";
	$envia_correcao = mysqli_query($conn, $sql_envia_correcao);
	if (!$envia_correcao) {
		die('Não foi possível enviar correções do orçamento: ' . mysql_error());
	}

	Log_Sis($pedido, $_SESSION['user']['id'], $_SESSION['user']['nome'], "Recusou o orçamento: " . sprintf('%05d', $pedido) . ".");
	$no_pedido = sprintf('%05d', $pedido);
	$ee_mensagem = '<br /><b>O orçamento ' . $no_pedido . ' foi recusado.</b><br /><br />Para acessá-lo clique aqui: <a href="'.$_SERVER['SERVER_NAME'].'/resumo.php?pedido=' . $no_pedido . '">'.$_SERVER['SERVER_NAME'].'/resumo.php?pedido=' . $no_pedido . '</a><br /><br />Orçamento recusado em ' . date("d/m/y") . ' às ' . date("H:i:s");
	//$ee_sql = mysqli_query($conn, "SELECT * FROM `users` WHERE `nivel` LIKE '1' AND `status` LIKE '1'");
	$ee_sql = mysqli_query($conn, "SELECT * FROM `users` WHERE (`id` LIKE '1' OR `id` LIKE '4') AND `status` LIKE '1'");
	while ($ee_envia = mysqli_fetch_array($ee_sql)) {
		Envia_Email($vendedor, 'mellogustavo@gmail.com', $ee_envia["email"], 'Orcamento recusado', $ee_mensagem, $no_pedido);
	}

	redirect("resumo.php?pedido=" . $pedido);
	die("Redirecionando...");
}

if ($_GET["acao"] == "reativa") {
	$id_pedido = $_GET["id"];
	$pedido = $_GET["pedido"];
	$sql_envia_correcao = "UPDATE `pedidos` SET `status` = '1' WHERE `pedidos`.`id` = '" . $id_pedido . "' LIMIT 1";
	$envia_correcao = mysqli_query($conn, $sql_envia_correcao);
	if (!$envia_correcao) {
		die('Não foi possível reativar orçamento: ' . mysql_error());
	}

	Log_Sis($pedido, $_SESSION['user']['id'], $_SESSION['user']['nome'], "Reativou o orçamento: " . sprintf('%05d', $pedido) . ".");
	$no_pedido = sprintf('%05d', $pedido);
	$ee_mensagem = '<br /><b>O orçamento ' . $no_pedido . ' foi reativado.</b><br /><br />Para acessá-lo clique aqui: <a href="'.$_SERVER['SERVER_NAME'].'/resumo.php?pedido=' . $no_pedido . '">'.$_SERVER['SERVER_NAME'].'/resumo.php?pedido=' . $no_pedido . '</a><br /><br />Orçamento reativado em ' . date("d/m/y") . ' às ' . date("H:i:s");
	//$ee_sql = mysqli_query($conn, "SELECT * FROM `users` WHERE `nivel` LIKE '1' AND `status` LIKE '1'");
	$ee_sql = mysqli_query($conn, "SELECT * FROM `users` WHERE (`id` LIKE '1' OR `id` LIKE '4') AND `status` LIKE '1'");
	while ($ee_envia = mysqli_fetch_array($ee_sql)) {
		Envia_Email($vendedor, 'mellogustavo@gmail.com', $ee_envia["email"], 'Orcamento reativado', $ee_mensagem, $no_pedido);
	}

	redirect("resumo.php?pedido=" . $pedido);
	die("Redirecionando...");
}

if ($_GET["acao"] == "quali_envia") {
	$id_pedido = $_POST["id_ped"];
	$pedido = $_POST["pedido"];
	$sql_envia_quali_aprovacao = "UPDATE `pedidos` SET `status` = '6' WHERE `pedidos`.`id` = '" . $id_pedido . "' LIMIT 1";
	$envia_quali_aprovacao = mysqli_query($conn, $sql_envia_quali_aprovacao);
	if (!$envia_quali_aprovacao) {
		die('Não foi possível enviar orçamento para qualidade: ' . mysql_error());
	}

	Log_Sis($pedido, $_SESSION['user']['id'], $_SESSION['user']['nome'], "Enviou para aprovação de qualidade: " . sprintf('%05d', $pedido) . ".");
	redirect("pedidos.php");
	die("Redirecionando...");
}

if ($_GET["acao"] == "quali_libera") {
	$id_pedido = $_GET["id_ped"];
	$pedido = $_GET["pedido"];
	$obs_qualidade = mysqli_real_escape_string($conn, $_POST["obs_qualidade"]);
	$sql_envia_qualidade = "UPDATE `pedidos` SET `status` = '7' WHERE `pedidos`.`id` = '" . $id_pedido . "' LIMIT 1";
	$envia_qualidade = mysqli_query($conn, $sql_envia_qualidade);
	if (!$envia_qualidade) {
		die('Não foi possível liberar orçamento: ' . mysql_error());
	}

	Log_Sis($pedido, $_SESSION['user']['id'], $_SESSION['user']['nome'], "Qualidade aprovou o orçamento: " . sprintf('%05d', $pedido) . ".");
	$sql_aprova_quali = "INSERT INTO `pedidos_qualidade` (`id`, `id_ped`, `pedido`, `obs_qualidade`, `aprova`, `id_user`, `nome`, `data`) VALUES (NULL, '" . $id_pedido . "', '" . (float)$pedido . "', '" . $obs_qualidade . "', 'sim', '" . $_SESSION['user']['id'] . "', '" . $_SESSION['user']['nome'] . "', '" . date('Y-m-d H:i:s') . "');";
	$pedido_aprova_quali = mysqli_query($conn, $sql_aprova_quali);
	redirect("pedidos.php");
	die("Redirecionando...");
}

if ($_GET["acao"] == "quali_retorna") {
	$id_pedido = $_GET["id_ped"];
	$pedido = $_GET["pedido"];
	$obs_qualidade = mysqli_real_escape_string($conn, $_POST["obs_qualidade"]);
	$sql_envia_qualidade = "UPDATE `pedidos` SET `status` = '1' WHERE `pedidos`.`id` = '" . $id_pedido . "' LIMIT 1";
	$envia_qualidade = mysqli_query($conn, $sql_envia_qualidade);
	if (!$envia_qualidade) {
		die('Não foi possível retornar para qualidade: ' . mysql_error());
	}

	Log_Sis($pedido, $_SESSION['user']['id'], $_SESSION['user']['nome'], "Qualidade retornou para análise o orçamento: " . sprintf('%05d', $pedido) . ".");
	$sql_aprova_quali = "INSERT INTO `pedidos_qualidade` (`id`, `id_ped`, `pedido`, `obs_qualidade`, `aprova`, `id_user`, `nome`, `data`) VALUES (NULL, '" . $id_pedido . "', '" . (float)$pedido . "', '" . $obs_qualidade . "', 'nao', '" . $_SESSION['user']['id'] . "', '" . $_SESSION['user']['nome'] . "', '" . date('Y-m-d H:i:s') . "');";
	$pedido_aprova_quali = mysqli_query($conn, $sql_aprova_quali);
	redirect("pedidos.php");
	die("Redirecionando...");
}

$id_pedido = $_GET["pedido"];

if ($id_pedido == "") {
	die();
}

$query_pedidos = mysqli_query($conn, "SELECT * FROM `pedidos` WHERE `pedido` = '" . $id_pedido . "' ORDER BY `id` DESC LIMIT 0,1"); // ORDER BY `revisao` DESC");
$pedido = mysqli_fetch_array($query_pedidos);

if ($_SESSION['user']['nivel'] == '4' and $pedido['status'] != '6') {
	die("<center><br /><br /><br /><h2>Alerta!</h2><br />Você não tem permissão para visualizar esse conteúdo.</center>");
}

$i = 0;
$valor_final_venda = $pedido["valor_final"];
$query_detalhes = mysqli_query($conn, "SELECT * FROM `pedidos_det` WHERE `revisao_valor` LIKE (SELECT MAX(`revisao_valor`) FROM `pedidos_det` WHERE `pedido` LIKE '" . (float)$id_pedido . "' AND `revisao` LIKE '" . $pedido["revisao"] . "') AND `pedido` LIKE '" . (float)$id_pedido . "' AND `revisao` LIKE '" . $pedido["revisao"] . "' ORDER BY `id` ASC");

while ($row_pedidos_det = mysqli_fetch_array($query_detalhes)) {
	if ($row_pedidos_det['m_quadrado'] == "1") {
		$detalhes_pedido.= "<tr>\n";
		$detalhes_pedido.= "<td style=\"max-width: 150px;\">";
		if ($row_pedidos_det['nivel'] == "2") {
			$detalhes_pedido.= "<p style=\"margin:0 0 0 20px;\">";//"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}

		if ($row_pedidos_det['nivel'] == "2") {
			$detalhes_pedido.= " " . $row_pedidos_det['desc'] . "</p></td>\n";
			if ($row_pedidos_det['valor_kg'] != "0.00") {
				$detalhes_pedido.= "<td class=\"contabil\">R$ " . number_format($row_pedidos_det['valor_kg'], 2, ',', '.') . "</td>\n";
			}
			else {
				$detalhes_pedido.= "<td></td>\n";
			}

			$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " m²</td>\n";
			$detalhes_pedido.= "<td class=\"contabil\">";
			if ($row_pedidos_det['gramat'] != "0") {
				$detalhes_pedido.= "" . tiraZero(number_format($row_pedidos_det['gramat'], 2, ',', '.')). " g/m²";
			}

			$detalhes_pedido.= "</td>\n";
			$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat']*$row_pedidos_det['gramat'], 2, ',', '.')) . " g</td>\n";
			$detalhes_pedido.= '<td class="contabil" align="right">(R$ ' . number_format($row_pedidos_det['valor'], 2, ',', '.') . ')</td>';
		}
		else {
			$detalhes_pedido.= " " . $row_pedidos_det['desc'] . "</td>\n";
			if ($row_pedidos_det['valor_kg'] != "0.00") {
				$detalhes_pedido.= "<td class=\"contabil\">R$ " . number_format($row_pedidos_det['valor_kg'], 2, ',', '.') . "</td>\n";
			}
			else {
				$detalhes_pedido.= "<td></td>\n";
			}

			$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " m²</td>\n";
			$detalhes_pedido.= "<td class=\"contabil\">";
			if ($row_pedidos_det['gramat'] != "0") {
				$detalhes_pedido.= tiraZero(number_format($row_pedidos_det['gramat'], 2, ',', '.')). " g/m²";
			}

			$detalhes_pedido.= "</td>\n";
			if ($row_pedidos_det['gramat'] != 0) {
				$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat']*$row_pedidos_det['gramat'], 2, ',', '.')) . " g</td>\n";
			} else {
				$detalhes_pedido.= "<td class=\"contabil\"></td>\n";
			}
			$sub = $i;
			$detalhes_pedido.= '<td class="contabil" align="right">R$ ' . number_format($row_pedidos_det['valor'], 2, ',', '.') . '</td>';
		}

		$detalhes_pedido.= "</tr>\n";
	}
	elseif ($row_pedidos_det['m_quadrado'] == "0") {
		$detalhes_pedido.= "<tr>\n";
		$detalhes_pedido.= "<td>";
		if ($row_pedidos_det['nivel'] == "2") {
			$detalhes_pedido.= "<p style=\"margin:0 0 0 20px;\">";//"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}

		if ($row_pedidos_det['nivel'] == "2") {
			$detalhes_pedido.= " " . $row_pedidos_det['desc'] . "</p></td>\n";
			if (substr($row_pedidos_det['desc'], 0, 6) == "Liner " || $row_pedidos_det['desc'] == "Cola") {
				$detalhes_pedido.= "<td class=\"contabil\">R$ " . number_format($row_pedidos_det['valor_kg'], 2, ',', '.') . "</td>\n";
				$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " m</td>\n";
			}
			elseif (substr($row_pedidos_det['desc'], 0, 14) == "Fio de costura") {
				$detalhes_pedido.= "<td class=\"contabil\">R$ " . number_format($row_pedidos_det['valor_kg'], 2, ',', '.') . "</td>\n";
				$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " m</td>\n";
			}
			elseif ($row_pedidos_det['desc'] == "Pack Less") {
				$detalhes_pedido.= "<td></td>\n";
				$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " par</td>\n";
			}
			else {
				if ($row_pedidos_det['valor_kg'] != "0.00") {
					$detalhes_pedido.= "<td class=\"contabil\">R$ " . number_format($row_pedidos_det['valor_kg'], 2, ',', '.') . "</td>\n";
				}
				else {
					$detalhes_pedido.= "<td></td>\n";
				}

				$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " m</td>\n";
			}

			$detalhes_pedido.= "<td class=\"contabil\">";
			if ($row_pedidos_det['gramat'] != "0") {
				if (substr($row_pedidos_det['desc'], 0, 6) == "Liner ") {
					$detalhes_pedido.= tiraZero(number_format($row_pedidos_det['gramat'], 2, ',', '.')) . " g/m²";
				} else {
					$detalhes_pedido.= tiraZero(number_format($row_pedidos_det['gramat'], 2, ',', '.')) . " g/m lin.";
				}
			}


			$detalhes_pedido.= "</td>\n";
			$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat']*$row_pedidos_det['gramat'], 2, ',', '.')) . " g</td>\n";
			$detalhes_pedido.= '<td class="contabil" align="right">(R$ ' . number_format($row_pedidos_det['valor'], 2, ',', '.') . ')</td>';
		}
		else {
			$detalhes_pedido.= " " . $row_pedidos_det['desc'] . "</td>\n";
			if (substr($row_pedidos_det['desc'], 0, 6) == "Liner " || $row_pedidos_det['desc'] == "Cola") {
				$detalhes_pedido.= "<td class=\"contabil\">R$ " . number_format($row_pedidos_det['valor_kg'], 2, ',', '.') . "</td>\n";

				$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " m</td>\n";
			}
			elseif (substr($row_pedidos_det['desc'],0,12) == "Impressão -" || substr($row_pedidos_det['desc'],0,16) == "Porta etiqueta -") {
				$detalhes_pedido.= "<td class=\"contabil\">R$ " . number_format($row_pedidos_det['valor_kg'], 2, ',', '.') . "</td>\n";
				$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " un</td>\n";
			}
			elseif ($row_pedidos_det['desc'] == "Pack Less") {
				$detalhes_pedido.= "<td></td>\n";
				$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " par</td>\n";
			}
			else {
				if ($row_pedidos_det['valor_kg'] != "0.00") {
					$detalhes_pedido.= "<td class=\"contabil\">R$ " . number_format($row_pedidos_det['valor_kg'], 2, ',', '.') . "</td>\n";
					$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " m</td>\n";
				}
				else {
					$detalhes_pedido.= "<td></td>\n";
					$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat'], 2, ',', '.')) . " m</td>\n";
				}
			}

			$detalhes_pedido.= "<td class=\"contabil\">";
			if ($row_pedidos_det['gramat'] != "0") {
				if (substr($row_pedidos_det['desc'],0,12) == "Impressão -" || substr($row_pedidos_det['desc'],0,16) == "Porta etiqueta -") {
//					$detalhes_pedido.= "";
				} else {
					$detalhes_pedido.= tiraZero(number_format($row_pedidos_det['gramat'], 2, ',', '.')). " g/m lin.";
				}
			}

			$detalhes_pedido.= "</td>\n";
			if ($row_pedidos_det['gramat'] != 0) {
				if (substr($row_pedidos_det['desc'],0,12) == "Impressão -" || substr($row_pedidos_det['desc'],0,16) == "Porta etiqueta -") {
					$detalhes_pedido.= "<td class=\"contabil\"></td>\n";
				} else {
					$detalhes_pedido.= "<td class=\"contabil\">" . tiraZero(number_format($row_pedidos_det['qtde_mat']*$row_pedidos_det['gramat'], 2, ',', '.')) . " g</td>\n";
				}
			} else {
				$detalhes_pedido.= "<td class=\"contabil\"></td>\n";
			}
			$sub = $i;
			$detalhes_pedido.= '<td class="contabil" align="right">R$ ' . number_format($row_pedidos_det['valor'], 2, ',', '.') . '</td>';
		}

		$detalhes_pedido.= "</tr>";
	}

	/* ************ CALCULO DO PESO DO BAG ************ */
	if (substr($row_pedidos_det['desc'],0,12) == "Impressão -" || substr($row_pedidos_det['desc'],0,16) == "Porta etiqueta -") {
	} else {
		$peso_teorico+= $row_pedidos_det['gramat'] * $row_pedidos_det['qtde_mat'] / 1000;
	}
	/* ************ CALCULO DO PESO DO BAG ************ */
	$revisao_valor = $row_pedidos_det['revisao_valor'];
	$revisao = $row_pedidos_det['revisao'];
	if ($row_pedidos_det['nivel'] == "1") {
		$subtotal = $subtotal + $row_pedidos_det['valor'];
	}

	$i = $i + 1;
}

$query_detalhes2 = mysqli_query($conn, "SELECT * FROM `pedidos_det` WHERE `revisao_valor` LIKE (SELECT MAX(`revisao_valor`) FROM `pedidos_det` WHERE `pedido` LIKE '" . (float)$id_pedido . "' AND `revisao` LIKE '" . $pedido["revisao"] . "') AND `pedido` LIKE '" . (float)$id_pedido . "' AND `revisao` LIKE '" . $pedido["revisao"] . "' ORDER BY `id` ASC");
/*
echo "<pre>";
echo "SELECT * FROM `pedidos_det` WHERE `revisao_valor` LIKE (SELECT MAX(`revisao_valor`) FROM `pedidos_det` WHERE `pedido` LIKE '" . (float)$id_pedido . "' AND `revisao` LIKE '" . $pedido["revisao"] . "') AND `pedido` LIKE '" . (float)$id_pedido . "' AND `revisao` LIKE '" . $pedido["revisao"] . "' ORDER BY `id` ASC";
echo "</pre>";
*/
while ($row_ped_det2 = mysqli_fetch_array($query_detalhes2)) {
	//$rev_det = $row_ped_det2['revisao'];
	//$rev_valor = $row_ped_det2['revisao_valor'];
	if ($row_ped_det2['m_quadrado'] == "1") {
		$det_pedido_menor.= "<tr>\n";
		$det_pedido_menor.= "<td>";
		if ($row_ped_det2['nivel'] == "2") {
			$det_pedido_menor.= "<p style=\"margin:0 0 0 20px;\">";//"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}

		if ($row_ped_det2['nivel'] == "2") {
			$det_pedido_menor.= " " . $row_ped_det2['desc'] . "</p></td>\n";
			if($row_ped_det2['qtde'] != 0) {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde'], 0, ',', '.')) . "</td>\n";
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['largura'], 2, ',', '.')) . " cm</td>\n";
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['corte'], 2, ',', '.')) . " cm</td>\n";
			} else {
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
			}
			$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " m²</td>\n";
			$det_pedido_menor.= "<td class=\"contabil\">";
			if ($row_ped_det2['gramat'] != "0") {
				$det_pedido_menor.= "" . tiraZero(number_format($row_ped_det2['gramat'], 2, ',', '.')) . " g/m²";
			}

			$det_pedido_menor.= "</td>\n";
		}
		else {
			$det_pedido_menor.= " " . $row_ped_det2['desc'] . "</td>\n";
			if($row_ped_det2['qtde'] != 0) {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde'], 0, ',', '.')) . "</td>\n";
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['largura'], 2, ',', '.')) . " cm</td>\n";
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['corte'], 2, ',', '.')) . " cm</td>\n";
			} else {
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
			}
			$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " m²</td>\n";
			$det_pedido_menor.= "<td class=\"contabil\">";
			if ($row_ped_det2['gramat'] != "0") {
				$det_pedido_menor.= tiraZero(number_format($row_ped_det2['gramat'], 2, ',', '.')) . " g/m²";
			}

			$det_pedido_menor.= "</td>\n";
		}

		if ($row_ped_det2['gramat'] != 0) {
			$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat']*$row_ped_det2['gramat'], 2, ',', '.')) . " g</td>\n";
		} else {
			$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
		}

		$det_pedido_menor.= "</tr>\n";
	}
	elseif ($row_ped_det2['m_quadrado'] == "0") {
		if(substr($row_ped_det2["desc"],0,8) == "Liner - "){ $comprimento_liner = $row_ped_det2["qtde_mat"]; }
		$det_pedido_menor.= "<tr>\n";
		$det_pedido_menor.= "<td>";
		if ($row_ped_det2['nivel'] == "2") {
			$det_pedido_menor.= "<p style=\"margin:0 0 0 20px;\">";//"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}

		if ($row_ped_det2['nivel'] == "2") {
			$det_pedido_menor.= " " . $row_ped_det2['desc'] . "</p></td>\n";

			if($row_ped_det2['qtde'] != 0) {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde'], 0, ',', '.')) . "</td>\n";
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['largura'], 2, ',', '.')) . " cm</td>\n";
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['corte'], 2, ',', '.')) . " cm</td>\n";
			} else {
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
			}

			if (substr($row_ped_det2['desc'], 0, 5) == "Liner") {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " m</td>\n";
			}
			elseif ($row_ped_det2['desc'] == "Cola") {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " kg</td>\n";
			}
			elseif (substr($row_ped_det2['desc'], 0, 14) == "Fio de costura") {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " m</td>\n";
			}
			elseif ($row_ped_det2['desc'] == "Pack Less") {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " par</td>\n";
			}
			else {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " m</td>\n";
			}

			$det_pedido_menor.= "<td class=\"contabil\">";
			if ($row_ped_det2['gramat'] != "0") {
				if (substr($row_ped_det2['desc'], 0, 6) == "Liner ") {
					$det_pedido_menor.= tiraZero(number_format($row_ped_det2['gramat'], 2, ',', '.')) . " g/m²";
				} else {
					$det_pedido_menor.= tiraZero(number_format($row_ped_det2['gramat'], 2, ',', '.')) . " g/m lin.";
				}
				//$det_pedido_menor.= tiraZero(number_format($row_ped_det2['gramat'], 2, ',', '.')) . " g/m lin.";
			}

			$det_pedido_menor.= "</td>\n";

			if ($row_ped_det2['gramat'] != 0) {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat']*$row_ped_det2['gramat'], 2, ',', '.')) . " g</td>\n";
			} else {
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
			}

		}
		else {
			$det_pedido_menor.= " " . $row_ped_det2['desc'] . "</td>\n";
			if($row_ped_det2['qtde'] != 0) {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde'], 0, ',', '.')) . "</td>\n";
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['largura'], 2, ',', '.')) . " cm</td>\n";
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['corte'], 2, ',', '.')) . " cm</td>\n";
			} else {
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
			}
			if (substr($row_ped_det2['desc'], 0, 5) == "Liner") {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " m</td>\n";
			}
			elseif ($row_ped_det2['desc'] == "Cola") {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " kg</td>\n";
			}
			elseif (substr($row_ped_det2['desc'],0,12) == "Impressão -" || substr($row_ped_det2['desc'],0,16) == "Porta etiqueta -") {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " un</td>\n";
			}
			elseif ($row_ped_det2['desc'] == "Pack Less") {
				//$det_pedido_menor.= "<td></td>\n";
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " par</td>\n";
			}
			else {
				$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat'], 2, ',', '.')) . " m</td>\n";
			}

			$det_pedido_menor.= "<td class=\"contabil\">";
			if ($row_ped_det2['gramat'] != "0") {
				if (substr($row_ped_det2['desc'],0,12) == "Impressão -" || substr($row_ped_det2['desc'],0,16) == "Porta etiqueta -") {
					$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
				} else {
					$det_pedido_menor.= tiraZero(number_format($row_ped_det2['gramat'], 2, ',', '.')) . " g/m lin.";
				}
			}

			$det_pedido_menor.= "</td>\n";


			if ($row_ped_det2['gramat'] != 0) {
				if (substr($row_ped_det2['desc'],0,12) == "Impressão -" || substr($row_ped_det2['desc'],0,16) == "Porta etiqueta -") {
//					$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
				} else {
					$det_pedido_menor.= "<td class=\"contabil\">" . tiraZero(number_format($row_ped_det2['qtde_mat']*$row_ped_det2['gramat'], 2, ',', '.')) . " g</td>\n";
				}
			} else {
				$det_pedido_menor.= "<td class=\"contabil\"></td>\n";
			}

		}

		$det_pedido_menor.= "</tr>";
	}

	$revisao_valor = $row_ped_det2['revisao_valor'];
	$revisao = $row_ped_det2['revisao'];
}
?>
    <br>
    <table border="0" class="table">
      <thead>
      <tr>
        <th>Número do orçamento / revisão
        </td>
        <th style="width: 140px;" align="right">
          <font size="5">
            <b>
              <?php echo sprintf('%05d', $id_pedido); ?> / 
              <?php echo sprintf('%02d', $revisao); ?>
            </b>
          </font>
        </td>
      </tr>
      </thead>
    </table>
    <br>
    <table border="0" class="table table-bordered lessover">
<?php

if ($pedido["referencia"] != "") {
?>
      <tr>
        <td colspan="2">
          <b>Cliente:
          </b> 
          <?php echo $pedido["nome_cliente"]; ?>
        </td>
        <td>
          <b>Referência:
          </b> 
          <?php echo $pedido["referencia"]; ?>
        </td>
<?php
} else {
?>
      <tr>
        <td colspan="3">
          <b>Cliente:</b> 
          <?php echo $pedido["nome_cliente"]; ?>
        </td>
<?php
}

$query_ped_cliente = mysqli_query($conn, "SELECT * FROM `pedidos_cliente` WHERE `pedido` = ".$pedido["pedido"]." ORDER BY `id` ASC");
$rel_cliente = mysqli_fetch_array($query_ped_cliente);

if (!empty($rel_cliente)) {
	$query_cliente = mysqli_query($conn, "SELECT * FROM `cad_clientes` WHERE `id` = ".$rel_cliente["id_cliente"]);
	$cliente = mysqli_fetch_array($query_cliente);
}
?>
        <td>
          <b>Tel.:</b> 
          <?php echo "(".$cliente["ddd"].") ".$cliente["telefone"]; ?>
        </td>
      </tr>
      <tr>
        <td>
          <b>
            <?php echo strtoupper($pedido["selecao"]); ?>:
          </b> 
          <?php echo $pedido["cnpj_cpf"]; ?>
        </td>
        <td>
          <b>Cidade:
          </b> 
          <?php echo $pedido["cidade_cliente"]; ?>
        </td>
        <td>
          <b>UF:
          </b> 
          <?php echo $pedido["uf_cliente"]; ?>
        </td>
<?php
if ($pedido["prazo"] == "0") {
	echo '<td><b>Prazo de pagamento:</b> A vista</td>';
} elseif ($pedido["prazo"] == "-1") {
	echo '<td><b>Prazo de pagamento:</b> Antecipado</td>';
} else {
	echo '<td><b>Prazo de pagamento:</b> '.$pedido["prazo"].' D.D.L.</td>';
}
?>
      </tr>
      <tr>
        <td><form method="POST" action="resumo.php?acao=atualiza_fornec"><b>Fornecedora:</b> <select name="fornecedora" style="border:0;">
<?
$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora`");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
?>
          <option value="valor_<? echo strtolower($row_fornec['sigla']); ?>"<? if($pedido["fornecedora"] == "valor_".strtolower($row_fornec['sigla'])){ echo " selected"; } ?>><? echo $row_fornec['apelido']; ?></option>
<?
}
?>
		</select>
		<input type="hidden" name="id_orc" value="<?php echo $pedido["id"]; ?>">
		<input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">
		<button style="border:0; background-color:transparent;"><i class="fa fa-refresh"></i></button></form>
        </td>
        <td>
          <b>Quantidade:
          </b> 
          <?php echo $pedido["qtde"]; ?>
        </td>
        <td>
          <b>Frete tipo:
          </b> 
          <?php echo strtoupper($pedido["frete"]); ?>
        </td>
        <td>
          <b>Distância aproximada:
          </b> 
          <?php
if ($pedido["distancia_aprox"] == "0") {
echo "Frete FOB";
} elseif ($pedido["distancia_aprox"] == "1") {
echo "Menos de 200 km";
} elseif ($pedido["distancia_aprox"] == "2") {
echo "De 201 a 500 km";
} elseif ($pedido["distancia_aprox"] == "3") {
echo "Acima de 501 km";
}
?>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <b>Embarques:
          </b> 
          <?php echo $pedido["embarques"]; ?>
        </td>
        <td colspan="2">
          <b>Representante:
          </b> 
          <?php echo $pedido["representante"]; ?>
        </td>
		</tr>
    </table>
    <br>
    <?php if ($_SESSION['user']['nivel'] != '4') { ?>
    <table border="0" class="table table-bordered lessover">
      <thead>
      <tr>
        <th>
          <b>Descrição
          </b>
        </th>
        <th>
          <b>Valor do quilo
          </b>
        </th>
        <th>
          <b>Qtde. de material
          </b>
        </th>
        <th>
          <b>Matéria-prima
          </b>
        </th>
        <th>
          <b>Peso
          </b>
        </th>
        <th style="width: 140px; text-align: right;">
          <b>Valor
          </b>
        </th>
      </tr>
      </thead>
      <?php
echo $detalhes_pedido;
?>
      <tr>
        <td colspan="5">
          <b>
            <?php echo "SUB-TOTAL"; ?>
          </b>
        </td>
        <td align="right" class="contabil">
          <b>R$ 
            <?php echo number_format((float)$subtotal, 2, ',', '.'); ?>
          </b>
        </td>
      </tr>
    </table>
    <br>
    <?php } ?>
    <table border="0" class="table table-bordered lessover">
      <tr>
        <td>
          <b>PESO TEÓRICO
          </b>
        </td>
        <td style="width: 140px;" align="right">
          <?php
/*
if  ($pedido["porta_etq1"] == "1") { $peso_teorico += 0.016; }
if  ($pedido["porta_etq2"] == "1") { $peso_teorico += 0.016; }
if  ($pedido["porta_etq3"] == "1") { $peso_teorico += 0.016; }
if  ($pedido["porta_etq4"] == "1") { $peso_teorico += 0.016; }
*/
$peso_teorico += 0.003 + 0.08;
echo number_format($peso_teorico,2,',','.'); ?> Kg
        </td>
        <td style="width: 200px; border-bottom: 0; border-top: 0;">
        </td>
        <td>
          <b>FATOR KG
          </b>
        </td>
        <td style="width: 140px;" align="right">R$ 
          <?php echo number_format($valor_final_venda/$peso_teorico,2,',','.'); ?>
        </td>
      </tr>
    </table>
    <?php if ($_SESSION['user']['nivel'] != '4') { ?>
    <?php
//$query_impostos = mysqli_query($conn,"SELECT * FROM `pedidos_extra` WHERE `rev_valor` LIKE (SELECT MAX(`rev_valor`) FROM `pedidos_extra` WHERE `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` DESC"); //AND `pedido` LIKE '".$pedido["pedido"]."'
$query_impostos = mysqli_query($conn,"SELECT * FROM `pedidos_extra` WHERE `id` LIKE (SELECT MAX(`id`) FROM `pedidos_extra` WHERE `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` DESC"); //AND `pedido` LIKE '".$pedido["pedido"]."'
$row_impostos = mysqli_fetch_array($query_impostos);
//echo $row_impostos["id"];
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
    <br>
    <table border="0" class="table table-bordered lessover">
      <tr>
        <td>
          <?php echo $desc_mat; ?>
        </td>
        <td style="width: 140px;" align="right" class="contabil">R$ 
          <?php echo number_format((float)$valor_mat_auxiliar, 2, ',', '.'); ?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo $desc_cif; ?>
        </td>
        <td align="right" class="contabil">R$ 
          <?php echo number_format((float)$valor_cif, 2, ',', '.'); ?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo $desc_mo; ?>
        </td>
        <td align="right" class="contabil">R$ 
          <?php echo number_format((float)$valor_mao_obra, 2, ',', '.'); ?>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo "<b>CUSTO DO BAG</b>"; ?>
        </td>
        <td align="right" class="contabil">
          <b>R$ 
            <?php echo number_format((float)$total, 2, ',', '.'); ?>
          </b>
        </td>
      </tr>
    </table>
    <?php
$valor_icms = $row_impostos["icms"];
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
?>
    <br>
    <table border="0" class="table table-bordered lessover">
      <tr>
        <td style="width: 20%;">
          <?php echo "ICMS"; ?>
        </td>
        <td style="width: 30%;" class="contabil">
          <?php echo number_format((float)$valor_icms, 2, ',', '.'); ?>%
        </td>
        <td style="width: 33%;">
          <?php echo "PIS"; ?>
        </td>
        <td style="width: 17%;" class="contabil">
          <?php echo number_format((float)$valor_pis, 2, ',', '.'); ?>%
        </td>
      </tr>
      <tr>
        <td>
          <?php echo "COFINS"; ?>
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_cofins, 2, ',', '.'); ?>%
        </td>
        <td>
          <?php echo "IR"; ?>
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_ir, 2, ',', '.'); ?>%
        </td>
      </tr>
      <tr>
        <td>
          <?php echo "CSLL"; ?>
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_csll, 2, ',', '.'); ?>%
        </td>
        <td>
          <?php echo "INSS (Folha Pagto.)"; ?>
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_inss, 2, ',', '.'); ?>%
        </td>
      </tr>
      <tr>
        <td>
          <?php echo "PERDA"; ?>
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_perda, 2, ',', '.'); ?>%
        </td>
        <td>
          <?php echo "FRETE"; ?>
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_frete, 2, ',', '.'); ?>%
        </td>
      </tr>
      <tr>
        <td>
          <?php echo "COMISSÃO"; ?>
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_comissao, 2, ',', '.'); ?>%
        </td>
        <td>
          <?php echo "ADM. COMERCIAL"; ?>
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_adm_comercial, 2, ',', '.'); ?>%
        </td>
      </tr>
      <tr>
        <td>
          <?php echo "MARGEM"; ?>
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_margem, 2, ',', '.'); ?>%
        </td>
        <td>
          <?php echo "CUSTO FINANCEIRO"; ?>
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_cfinanceiro, 2, ',', '.'); ?>%
        </td>
      </tr>
      <tr>
<?php
/*
if($valor_inss > 0) {
?>
        <td>
          <?php echo "INSS"; ?> (Folha Pagto.)
        </td>
        <td style="width: 140px;" class="contabil">
          <?php echo number_format((float)$valor_inss, 2, ',', '.'); ?>%
        </td>
<?php
} else {
?>
        <td colspan="2"></td>
<?php
}*/
?>
        <td colspan="2"></td>
        <td>
          <?php echo "<b>TOTAL DE IMPOSTOS</b>"; ?>
        </td>
        <td style="width: 140px;" align="right" class="contabil">
          <b>
            <?php echo number_format((float)$valor_imposto, 2, ',', '.'); ?>%
          </b>
        </td>
      </tr>
    </table>
    <br>
<?php
$valor_semimposto = $valor_icms + $valor_pis + $valor_cofins;
$valor_semimposto = 100 - $valor_semimposto;
$valor_semimposto /= 100;
$valor_semimposto = $valor_final_venda * $valor_semimposto;

$valor_net = $valor_icms + $valor_pis + $valor_cofins + $valor_frete;
$valor_net = 100 - $valor_net;
$valor_net /= 100;
$valor_net = $valor_final_venda * $valor_net;

$valor_semfrete = $valor_frete;
$valor_semfrete = 100 - ($valor_imposto-$valor_frete);
$valor_semfrete /= 100;
$valor_semfrete = ($valor_final_venda*((100-$valor_imposto)/100)) / $valor_semfrete;
/*
$valor_semimposto = $valor_ir + $valor_csll + $valor_inss + $valor_perda + $valor_comissao + $valor_cfinanceiro + $valor_margem + $valor_frete;
$valor_semimposto = 100 - $valor_semimposto;
$valor_semimposto /= 100;
$valor_semimposto = $total / $valor_semimposto;

$valor_net = $valor_ir + $valor_csll + $valor_inss + $valor_perda + $valor_comissao + $valor_cfinanceiro + $valor_margem;
$valor_net = 100 - $valor_net;
$valor_net /= 100;
$valor_net = $total / $valor_net;

$valor_semfrete = $valor_icms + $valor_pis + $valor_cofins + $valor_ir + $valor_csll + $valor_inss + $valor_perda + $valor_comissao + $valor_cfinanceiro + $valor_margem;
$valor_semfrete = 100 - $valor_semfrete;
$valor_semfrete /= 100;
$valor_semfrete = $total / $valor_semfrete;
*/
?>
    <table border="0" class="table table-bordered lessover">
      <tr>
        <td>
          <?php echo "<b>VALOR COM IMPOSTOS</b>"; ?>
        </td>
        <td style="width: 140px;" align="right" class="contabil">
          <b>R$ 
            <?php echo number_format((float)$valor_final_venda, 2, ',', '.'); ?>
          </b>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo "<b>VALOR SEM IMPOSTOS</b> (sem ICMS - PIS - COFINS)"; ?>
        </td>
        <td style="width: 140px;" align="right" class="contabil">
          <b>R$ 
            <?php echo number_format((float)$valor_semimposto, 2, ',', '.'); ?>
          </b>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo "<b>VALOR NET</b> (sem ICMS - PIS - COFINS - FRETE)"; ?>
        </td>
        <td style="width: 140px;" align="right" class="contabil">
          <b>R$ 
            <?php echo number_format((float)$valor_net, 2, ',', '.'); ?>
          </b>
        </td>
      </tr>
      <tr>
        <td>
          <?php echo "<b>VALOR SEM FRETE / FOB"; ?>
        </td>
        <td style="width: 140px;" align="right" class="contabil">
          <b>R$ 
            <?php echo number_format((float)$valor_semfrete, 2, ',', '.'); ?>
          </b>
        </td>
      </tr>
      <?php
$bd_valor_dolar = $row_impostos["valor_dolar"];
$bd_cambio = $row_impostos["cambio"];
$bd_cambio_data = $row_impostos["cambio_data"];
$bd_cambio_dia = strtotime($bd_cambio_data);
$bd_cambio_hora = date("H:i:s", $bd_cambio_dia);
$bd_cambio_dia = date("d/m/Y", $bd_cambio_dia);
/*
echo "Valor em dolar: ".$bd_valor_dolar."<br>";
echo "Cambio: ".$bd_cambio."<br>";
echo "Cambio dia: ".$bd_cambio_dia."<br>";
echo "Cambio hora: ".$bd_cambio_hora."<br>";
echo "Cambio data: ".$bd_cambio_data."<br>";
*/
if ($bd_valor_dolar != "0.00") {
	$cambio_dia = $bd_cambio_dia;
	$cotacao_dolar = str_replace( '.', ',', $bd_cambio);
	$valor_dolar = $bd_valor_dolar;
} else {
/*
	require("dolar/class.UOLCotacoes.php");
	$uol = new UOLCotacoes();
	list($dolarComercialCompra, $dolarComercialVenda, $dolarTurismoCompra, $dolarTurismoVenda, $euroCompra, $euroVenda, $libraCompra, $libraVenda, $pesosCompra, $pesosVenda) = $uol->pegaValores();
*/
	if ($dolarComercialCompra != "") {
	$cotacao_dolar = str_replace( ',', '.', $dolarComercialCompra);
	$cambio_dia = date("d/m/Y");
	$valor_dolar = $valor_final_venda/$cotacao_dolar;
	}
}
/*
echo "VALOR SEM IMPOSTO: ".$valor_final_venda;
echo "<br>";
echo "COTACAO: ".$cotacao_dolar;
echo "<br>";
echo "VALOR DOLAR: ".$valor_dolar;
*/
?>
      <tr>
        <td>
          <?php echo "<b>VALOR EM DÓLAR</b> (valor com impostos convertido) <div style=\"float:right;\"><a href=\"cambio.php\" data-lity>Câmbio</a>: R$ ".$cotacao_dolar." <small><small>(Em ".$cambio_dia.")</small></small></div>"; ?>
        </td>
        <td style="width: 140px;" align="right" class="contabil">
          <b>US$ 
            <?php echo number_format((float)$valor_dolar, 2, ',', '.'); ?>
          </b>
        </td>
      </tr>
    </table>
    <br>
    <?php // if ($pedido["obs_comerciais"] != "") { ?>
    <table border="0" class="table table-bordered lessover">
      <tr>
        <td>
          <b>Observações comerciais:
          </b> 
          <?php echo $pedido["obs_comerciais"]; ?>
        </td>
      </tr>
    </table>
    <?php // } ?>
  </div>
</div>
<div class="col-md-7 col-xs-12" style="padding-left:0;">
  <div class="x_panel">
    <div class="x_title">
      <h2>Ficha técnica
      </h2>
      <ul class="nav navbar-right panel_toolbox">
        <li>
          <a class="collapse-link">
            <i class="fa fa-chevron-up">
            </i>
          </a>
        </li>
      </ul>
      <div class="clearfix">
      </div>
    </div>
    <div class="x_content">
      <?php } ?>
      <?php
// $query_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE `pedido` LIKE '".$pedido["pedido"]."' ORDER BY `revisao` DESC");
$row_pedidos = $pedido; // mysqli_fetch_array($query_pedidos);
$desenho .= '<table width="360" border="0" align="center">
<tr>
<td align="right" valign="bottom">Desenho ilustrativo</td>
</tr>
</table>
<hr><br>
<div class="clearfix"></div>
<div style="margin:auto;width:357px;">';
if ($row_pedidos["segmento_cliente"] == "1") {
$desenho .= '<br>
<p align="center"><font color="#FF0000"><b>ALIMENTÍCIO - PADRÃO EXPORTAÇÃO</b></font></p>';
} elseif ($row_pedidos["segmento_cliente"] == "10") {
$desenho .= '<br>
<p align="center"><font color="#FF0000"><b>ALIMENTÍCIO</b></font></p>';
}
if ($row_pedidos["mercado"] == "ext") {
$desenho .= '<br>
<p align="center"><font color="#FF0000"><b>BIG BAG PARA EXPORTAÇÃO</b></font></p>';
}
$desenho .= '<div id="box_desenho" style="position:relative; overflow: hidden; width:357px; height:564px;">';
$sel_faces = $row_pedidos["sel_faces"];
if (strpos($sel_faces,"d") !== false) {
$desenho .= '<div id="face_d" style="z-index:24;" class="desenho"><img src="images/desenhos/face_d.png" border="0"></div>';
}
if (strpos($sel_faces,"c") !== false) {
$desenho .= '<div id="face_c" style="z-index:23;" class="desenho"><img src="images/desenhos/face_c.png" border="0"></div>';
}
if (strpos($sel_faces,"b") !== false) {
$desenho .= '<div id="face_b" style="z-index:22;" class="desenho"><img src="images/desenhos/face_b.png" border="0"></div>';
}
if (strpos($sel_faces,"a") !== false) {
$desenho .= '<div id="face_a" style="z-index:21;" class="desenho"><img src="images/desenhos/face_a.png" border="0"></div>';
}
if ($row_pedidos["porta_etq4"] != false) {
$desenho .= '<div id="des_porta_etq4" style="z-index:20;" class="desenho"><img src="images/desenhos/4_'.$row_pedidos["pos_porta_etq4"].'.png" border="0"></div>';
}
if ($row_pedidos["porta_etq3"] != false) {
$desenho .= '<div id="des_porta_etq3" style="z-index:19;" class="desenho"><img src="images/desenhos/3_'.$row_pedidos["pos_porta_etq3"].'.png" border="0"></div>';
}
if ($row_pedidos["porta_etq2"] != false) {
$desenho .= '<div id="des_porta_etq2" style="z-index:18;" class="desenho"><img src="images/desenhos/2_'.$row_pedidos["pos_porta_etq2"].'.png" border="0"></div>';
}
if ($row_pedidos["porta_etq1"] != false) {
$desenho .= '<div id="des_porta_etq1" style="z-index:17;" class="desenho"><img src="images/desenhos/1_'.$row_pedidos["pos_porta_etq1"].'.png" border="0"></div>';
}
if ($row_pedidos["flap"] != false) {
$desenho .= '<div id="flap" style="z-index:4;" class="desenho"><img src="images/desenhos/flap.png" border="0"></div>';
}
if ($row_pedidos["unit"] != "0") {
$desenho .= '<div id="dim_unit" style="z-index: 15; top: 453px; left: 106px; display: block;" class="medidas">'.$row_pedidos["unit"].'</div>';
$desenho .= '<div id="unitizador" style="z-index:10;" class="desenho"><img src="images/desenhos/unitizador.png" border="0"></div>';
}
if ($row_pedidos["sapata"] != false) {
$desenho .= '<div id="sapata" style="z-index:15;" class="desenho"><img src="images/desenhos/sapata.png" border="0"></div>';
}
if ($row_pedidos["velcro"] != false) {
$desenho .= '<div id="velcro" style="z-index:16;" class="desenho"><img src="images/desenhos/velcro.png" border="0"></div>';
}
if ($row_pedidos["cinta_trav"] != false) {
$desenho .= '<div id="cinta" style="z-index:16;" class="desenho"><img src="images/desenhos/cinta.png" border="0"></div>';
}
if ($row_pedidos["gravata"] != false) {
$desenho .= '<div id="gravata" style="z-index:16;" class="desenho"><img src="images/desenhos/gravata.png" border="0"></div>';
}
if ($row_pedidos["descarga"] == 'vazio') { $descarga1_top = "-35px"; $descarga1_left =  "0px"; $descarga2_top = "-35px"; $descarga2_left =  "0px"; }
elseif ($row_pedidos["descarga"] == 'd_simples') { $descarga1_top = "218px"; $descarga1_left =  "256px"; $descarga2_top = "165px"; $descarga2_left =  "184px"; }
elseif ($row_pedidos["descarga"] == 'd_prot_presilha') { $descarga1_top = "217px"; $descarga1_left =  "257px"; $descarga2_top = "167px"; $descarga2_left =  "179px"; }
elseif ($row_pedidos["descarga"] == 'd_prot_mochila' && $row_pedidos["corpo"] == 'gota') { $descarga1_top = "177px"; $descarga1_left =  "234px"; $descarga2_top = "129px"; $descarga2_left =  "301px"; }
elseif ($row_pedidos["descarga"] == 'd_prot_mochila') { $descarga1_top = "217px"; $descarga1_left =  "257px"; $descarga2_top = "167px"; $descarga2_left =  "179px"; }
elseif ($row_pedidos["descarga"] == 'd_afunilado') { $descarga1_top = "209px"; $descarga1_left =  "198px"; $descarga2_top = "176px"; $descarga2_left =  "136px"; }
elseif ($row_pedidos["descarga"] == 'd_total') { $descarga1_top = "-35px"; $descarga1_left =  "0px"; $descarga2_top = "-35px"; $descarga2_left =  "0px"; }
elseif ($row_pedidos["descarga"] == 'd_total_presilha') { $descarga1_top = "-35px"; $descarga1_left =  "0px"; $descarga2_top = "-35px"; $descarga2_left =  "0px"; }
elseif ($row_pedidos["descarga"] == 'd_total_blindado') { $descarga1_top = "-35px"; $descarga1_left =  "0px"; $descarga2_top = "-35px"; $descarga2_left =  "0px"; }
$desenho .= '<div id="dim_descarga2" style="z-index: 14; top: '.$descarga2_top.'; left: '.$descarga2_left.';" class="medidas">'.str_replace(".",",",$row_pedidos["descarga2"]).'</div>';
$desenho .= '<div id="dim_descarga1" style="z-index: 13; top: '.$descarga1_top.'; left: '.$descarga1_left.';" class="medidas">'.str_replace(".",",",$row_pedidos["descarga1"]).'</div>';
if ($row_pedidos["carga"] == "vazio"){ $carga1_top = "-35px"; $carga1_left =  "0px"; $carga2_top = "-35px"; $carga2_left =  "0px"; }
elseif ($row_pedidos["carga"] == "c_saia"){ $carga1_top = "-35px"; $carga1_left =  "0px"; $carga2_top = "52px"; $carga2_left =  "205px"; }
elseif ($row_pedidos["carga"] == "c_afunilada"){ $carga1_top = "20px"; $carga1_left =  "140px"; $carga2_top = "52px"; $carga2_left =  "205px"; }
elseif ($row_pedidos["carga"] == "c_simples"){ $carga1_top = "10px"; $carga1_left =  "84px"; $carga2_top = "64px"; $carga2_left =  "153px"; }
elseif ($row_pedidos["carga"] == "c_simples_afunilada"){ $carga1_top = "-2px"; $carga1_left =  "85px"; $carga2_top = "43px"; $carga2_left =  "162px"; }
elseif ($row_pedidos["carga"] == "c_prot_presilha"){ $carga1_top = "12px"; $carga1_left =  "85px"; $carga2_top = "61px"; $carga2_left =  "160px"; }
elseif ($row_pedidos["carga"] == "c_prot_mochila"){ $carga1_top = "12px"; $carga1_left =  "85px"; $carga2_top = "61px"; $carga2_left =  "160px"; }
//elseif ($row_pedidos["carga"] == "c_simples_afunilada"){ $carga1_top = "43px"; $carga1_left =  "162px"; $carga2_top = "-2px"; $carga2_left =  "85px"; }
//elseif ($row_pedidos["carga"] == "c_prot_presilha"){ $carga1_top = "61px"; $carga1_left =  "160px"; $carga2_top = "12px"; $carga2_left =  "85px"; }
//elseif ($row_pedidos["carga"] == "c_prot_mochila"){ $carga1_top = "61px"; $carga1_left =  "160px"; $carga2_top = "12px"; $carga2_left =  "85px"; }
elseif ($row_pedidos["carga"] == "c_tipo_as"){ $carga1_top = "-35px"; $carga1_left =  "0px"; $carga2_top = "-35px"; $carga2_left =  "0px"; }
$desenho .= '<div id="dim_carga2" style="z-index: 12; top: '.$carga2_top.'; left: '.$carga2_left.';" class="medidas">'.str_replace(".",",",$row_pedidos["carga2"]).'</div>';
$desenho .= '<div id="dim_carga1" style="z-index: 11; top: '.$carga1_top.'; left: '.$carga1_left.';" class="medidas">'.str_replace(".",",",$row_pedidos["carga1"]).'</div>';
if ($row_pedidos["corpo"] == "gota") {
	$desenho .= '<div id="dim_alca_fix_altura" style="z-index:10; top: 142px; left: 132px;" class="medidas">'.str_replace(".",",",$row_pedidos["alca_fix_altura"]).'</div>';
	$desenho .= '<div id="dim_alca_altura" style="z-index:10; top: 213px; left: 44px;" class="medidas">'.str_replace(".",",",$row_pedidos["alca_altura"]).'</div>';
	$desenho .= '<div id="dim_altura" style="z-index:9; top: 355px; left: 285px;" class="medidas">'.str_replace(".",",",$row_pedidos["altura"]).'</div>';
} else {
	$desenho .= '<div id="dim_alca_fix_altura" style="z-index:10; top: 330px; left: 295px;" class="medidas">'.str_replace(".",",",$row_pedidos["alca_fix_altura"]).'</div>';
	$desenho .= '<div id="dim_alca_altura" style="z-index:10; top: 294px; left: 295px;" class="medidas">'.str_replace(".",",",$row_pedidos["alca_altura"]).'</div>';
	$desenho .= '<div id="dim_altura" style="z-index:9; top: 370px; left: 295px;" class="medidas">'.str_replace(".",",",$row_pedidos["altura"]).'</div>';
}
$desenho .= '<div id="dim_base2" style="z-index:8; top: 490px; left: 230px;" class="medidas">'.str_replace(".",",",$row_pedidos["base2"]).'</div>';
$desenho .= '<div id="dim_base1" style="z-index:7; top: 490px; left: 65px;" class="medidas">'.str_replace(".",",",$row_pedidos["base1"]).'</div>';
if ($row_pedidos["liner"] != "") {
$desenho .= '<div id="liner" style="z-index:6;" class="desenho"><img src="images/desenhos/'.$row_pedidos["liner"].'.png" border="0"></div>';
}

if ($row_pedidos["descarga"] != "") {
	if($row_pedidos["corpo"] == 'gota') {
//		$desenho .= '<div id="descarga" style="z-index:5;" class="desenho"><img src="images/desenhos/gota_valvula';
//echo "DESC_GOTA".$row_pedidos["descarga"];
		if($row_pedidos["descarga"] == 'vazio') {
			$desenho .= '<div id="descarga" style="z-index:5;" class="desenho"><img src="images/desenhos/vazio';
		} else {
			$desenho .= '<div id="descarga" style="z-index:5;" class="desenho"><img src="images/desenhos/tampa_gota.png" border="0"></div>';
			$desenho .= '<div id="descarga" style="z-index:5;" class="desenho"><img src="images/desenhos/gota_valvula';
		}
	} else {
		$desenho .= '<div id="descarga" style="z-index:5;" class="desenho"><img src="images/desenhos/'.$row_pedidos["descarga"].'';
	}
if ($row_pedidos["d_redondo"] != false) { 
$desenho .= '_r';
}
$desenho .= '.png" border="0"></div>';
}
if ($row_pedidos["carga"] != "") {
$desenho .= '<div id="carga" style="z-index:4;" class="desenho"><img src="images/desenhos/'.$row_pedidos["carga"].'';
if ($row_pedidos["c_quadrado"] != false) {
$desenho .= '_q';
}
if ($row_pedidos["carga"] == "c_simples" && $row_pedidos["carga2"] == "") {
$desenho .= '2';
}
$desenho .= '.png" border="0"></div>';
}
if ($row_pedidos["alca"] != "") {
$desenho .= '<div id="alca" style="z-index:3;" class="desenho"><img src="images/desenhos/'.$row_pedidos["alca"].'.png" border="0"></div>';
}
if ($row_pedidos["corpo"] != "") {
$desenho .= '<div id="corpo" style="z-index:2;" class="desenho"><img src="images/desenhos/'.$row_pedidos["corpo"].'.png" border="0"></div>';
}
if ($row_pedidos["corpo"] == "gota") {
	$desenho .= '<div id="medidas" style="z-index:0;" class="desenho"><img src="images/desenhos/gota_medidas.png" border="0"></div>';
} else {
	$desenho .= '<div id="baseimg" style="z-index:1;" class="desenho"><img src="images/desenhos/corpo.png" border="0"></div>';
	$desenho .= '<div id="medidas" style="z-index:0;" class="desenho"><img src="images/desenhos/medidas.png" border="0"></div>';
}
$desenho .= '</div>';
$desenho .= '</div>';
?>
      <br>

            <table border="0" class="table table-bordered lessover">
              <tr>
                <td>
                  <b>Nome do produto acondicionado:
                  </b>
                </td>
                <td>
                  <?php echo $pedido["nome_prod"]; ?>
                </td>
              </tr>
              <tr>
                <td>Quantidade:
                </td>
                <td>
                  <?php echo $pedido["qtde"]; ?>
                </td>
              </tr>
              <tr>
                <td>Segmento:
                </td>
                <td>
                  <?php
$segmento = mysqli_query($conn,"SELECT * FROM `segmentos`");
while($row = mysqli_fetch_array($segmento)) {
if ($row['id'] == $pedido["segmento_cliente"]) {
echo $row['segmento'];
}
}
?>
                </td>
              </tr>
              <tr>
                <?php if ($pedido["dens_aparente"] != "") { ?>
                <td>Densidade aparente:
                </td>
                <td>
                  <?php echo $pedido["dens_aparente"]; ?> gr/cm
                  <sup>3
                  </sup>
                </td>
              </tr>
              <tr>
                <?php } ?>
                <?php if ($pedido["temperatura"] != "") { ?>
                <td>Temperatura:
                </td>
                <td>
                  <?php echo $pedido["temperatura"]; ?> °C
                </td>
              </tr>
              <tr>
                <?php } ?>
                <td>Classificação de uso:
                </td>
                <td>
                  <?php
$classif_uso = mysqli_query($conn,"SELECT * FROM `classif_uso`");
while($row = mysqli_fetch_array($classif_uso)) {
$valor = $row['fator'] . "_" . $row['id'];
if ($valor == $pedido["class_uso"]) {
echo "FS ".$row['fator'].":1";
}
}
?>
                </td>
              </tr>
              <?php if ($pedido["transporte"] != "Selecione") { ?>
              <tr>
                <td>Transporte:
                </td>
                <td>
                  <?php echo $pedido["transporte"]; ?>
                </td>
              </tr>
              <?php } ?>
              <tr>
                <?php if ($pedido["dem_mensal"] != "") { ?>
                <td>Demanda mensal:
                </td>
                <td>
                  <?php echo $pedido["dem_mensal"]; ?>
                </td>
              </tr>
              <tr>
                <?php } ?>
                <?php if ($pedido["dem_anual"] != "") { ?>
                <td>Demanda anual:
                </td>
                <td>
                  <?php echo $pedido["dem_anual"]; ?>
                </td>
              </tr>
              <tr>
                <?php } ?>
                <td>Carga nominal:
                </td>
                <td>
                  <?php echo $pedido["carga_nominal"]; ?> kg
                </td>
              </tr>
              <?php if ($pedido["armazenagem"] != "Selecione") { ?>
              <tr>
                <td>Armazenagem:
                </td>
                <td>
                  <?php echo $pedido["armazenagem"]; ?>
                </td>
              </tr>
              <?php } ?>
              <tr>
                <td>
                  <b>Modelo do corpo:
                    <b>
                      </td>
                    <td>
                      <?php
if ($pedido["corpo"] == "gota") { echo "Gota (Single Loop)"; }
elseif ($pedido["corpo"] == "qowa") { echo "Plano"; }
elseif ($pedido["corpo"] == "cowa") { echo "Tubular"; }
elseif ($pedido["corpo"] == "qowac") { echo "Painel U"; }
elseif ($pedido["corpo"] == "qowacf") { echo "Painel U com forro"; }
elseif ($pedido["corpo"] == "qowad4") { echo "Travado com costuras nos cantos"; }
elseif ($pedido["corpo"] == "qowad8") { echo "Travado em gomos"; }
elseif ($pedido["corpo"] == "cowad") { echo "Travado tubular"; }
elseif ($pedido["corpo"] == "qowa2") { echo "Plano duplo"; }
elseif ($pedido["corpo"] == "cowa2") { echo "Tubular duplo"; }
elseif ($pedido["corpo"] == "qowaf") { echo "Plano com forro"; }
elseif ($pedido["corpo"] == "qowadlf") { echo "Plano com forro travado"; }
elseif ($pedido["corpo"] == "cowaf") { echo "Tubular com forro"; }
elseif ($pedido["corpo"] == "qowao") { echo "Plano condutivo"; }
elseif ($pedido["corpo"] == "qowafi") { echo "Plano com forro VCI"; }
elseif ($pedido["corpo"] == "cowafi") { echo "Tubular com forro VCI"; }
elseif ($pedido["corpo"] == "qowam") { echo "Plano antimicrobiano"; }
elseif ($pedido["corpo"] == "qowaa") { echo "Plano arejado"; }
elseif ($pedido["corpo"] == "qowat") { echo "Plano térmico"; }
elseif ($pedido["corpo"] == "qhe") { echo "Plano com fechamento especial"; }
elseif ($pedido["corpo"] == "qhe_ref") { echo "Plano QHE reforçado com fita"; }
elseif ($pedido["corpo"] == "rof") { echo "Porta ensacado simples"; }
elseif ($pedido["corpo"] == "qms") { echo "Plano com duas alças"; }
elseif ($pedido["corpo"] == "cms") { echo "Tubular com duas alças"; }
elseif ($pedido["corpo"] == "outros") { echo "Outro"; }
?>
                    </td>
                    </tr>
                  <tr>
                    <td>Dimensões: (L x C x A )
                    </td>
                    <td>
                      <?php echo $pedido["base1"]; ?> x 
                      <?php echo $pedido["base2"]; ?> x 
                      <?php echo $pedido["altura"]; ?> cm
                    </td>
                  </tr>
                  <tr>
                    <td>Laminado:
                    </td>
                    <td>
                      <?php if ($pedido["plastificado"] == "1") { echo "SIM"; } else { echo "NÃO"; } ?>
                    </td>
                  </tr>
                  <tr>
                    <td>Cor do tecido:
                    </td>
                    <td>
                      <?php if ($pedido["corpo_cor"] == "branco") { echo "Branco (natural)"; }
elseif ($pedido["corpo_cor"] == "cinza") { echo "Cinza"; }
elseif ($pedido["corpo_cor"] == "azul") { echo "Azul Carijó"; }
elseif ($pedido["corpo_cor"] == "marrom") { echo "Marrom Carijó"; }
elseif ($pedido["corpo_cor"] == "preto") { echo "Preto Carijó"; }
elseif ($pedido["corpo_cor"] == "preto2") { echo "Preto"; }
elseif ($pedido["corpo_cor"] == "verde") { echo "Verde Carijó"; }
elseif ($pedido["corpo_cor"] == "outro") { echo "Outro (".$pedido["corpo_cor_outro"].")"; }
?>
                    </td>
                  </tr>
                  <tr>
                    <td>Cor da laminação:
                    </td>
                    <td>
                      <?php if ($pedido["lamin_cor"] == "padrao") { echo "Padrão"; }
elseif ($pedido["lamin_cor"] == "branco") { echo "Branco"; }
elseif ($pedido["lamin_cor"] == "preto") { echo "Preto"; }
elseif ($pedido["lamin_cor"] == "outro") { echo "Outro (".$pedido["lamin_cor_outro"].")"; }
?>
                    </td>
                  </tr>
                  <?php if ($pedido["fio_ved_travas"] == "1") { ?>
                  <tr>
                    <td>Fio vedante nas travas:
                    </td>
                    <td>SIM
                    </td>
                  </tr>
                  <?php } ?>
                  <tr>
                    <td>Gramatura do corpo:
                    </td>
                    <td>
                      <?php 

$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `gramat_real` LIKE '".$pedido["gramat_corpo"]."' AND `status` = '1';");
$gramat_row = mysqli_fetch_array($gramaturas_query);
	echo $gramat_row['gramat_desc'];

/* if ($pedido["gramat_corpo"] == "130") { echo "130"; }
elseif ($pedido["gramat_corpo"] == "147") { echo "130 + 17"; }
elseif ($pedido["gramat_corpo"] == "145") { echo "145"; }
elseif ($pedido["gramat_corpo"] == "170") { echo "145 + 25"; }
elseif ($pedido["gramat_corpo"] == "160") { echo "160"; }
elseif ($pedido["gramat_corpo"] == "185") { echo "160 + 25"; }
elseif ($pedido["gramat_corpo"] == "190") { echo "190"; }
elseif ($pedido["gramat_corpo"] == "215") { echo "190 + 25"; }
elseif ($pedido["gramat_corpo"] == "220") { echo "220"; }
elseif ($pedido["gramat_corpo"] == "245") { echo "220 + 25"; }
elseif ($pedido["gramat_corpo"] == "240") { echo "240"; }
elseif ($pedido["gramat_corpo"] == "265") { echo "240 + 25"; }
elseif ($pedido["gramat_corpo"] == "270") { echo "270"; }
elseif ($pedido["gramat_corpo"] == "295") { echo "270 + 25"; } */ ?> g/m²
                    </td>
                  </tr>
                  <tr>
                    <td>Costura a fio no topo:</td>
                    <td><?php  if($pedido["cost_fio_topo"] == "1") { echo "SIM"; } else { echo "NÃO"; } ?>
                    </td>
                  </tr>
                  <tr>
                    <td>Costura a fio na base:</td>
                    <td><?php  if($pedido["cost_fio_base"] == "1") { echo "SIM"; } else { echo "NÃO"; } ?>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <b>Sistema de enchimento:
                      </b>
                    </td>
                    <td>
                      <?php if ($pedido["carga"] == "vazio") { echo "Sem sistema de enchimento"; }
elseif ($pedido["carga"] == "c_saia") { echo "Saia"; }
elseif ($pedido["carga"] == "c_afunilada") { echo "Saia afunilada"; }
elseif ($pedido["carga"] == "c_simples") { echo "Válvula simples"; }
elseif ($pedido["carga"] == "c_simples_afunilada") { echo "Válvula simples com tampa afunilada"; }
elseif ($pedido["carga"] == "c_prot_mochila") { echo "Válvula com proteção tipo flap"; }
elseif ($pedido["carga"] == "c_tipo_as") { echo "Tampa tipo porta-ensacado"; } ?>
                    </td>
                  </tr>
                  <tr>
                    <?php if ($pedido["carga"] != "vazio") { ?>
                    <td>Quadrado:
                    </td>
                    <td>
                      <?php if ($pedido["c_quadrado"] == "c_quadrado") { echo "SIM"; } else { echo "NÃO"; } ?>
                    </td>
                  </tr>
                  <tr>
                    <?php if ($pedido["carga1"] != "") { ?>
                    <td>Diâmetro/lado:
                    </td>
                    <td>
                      <?php echo $pedido["carga1"]; ?> cm
                    </td>
                  </tr>
                  <tr>
                    <?php } ?>
                    <?php if ($pedido["carga2"] != "") { ?>
                    <td>Altura:
                    </td>
                    <td>
                      <?php echo $pedido["carga2"]; ?> cm
                    </td>
                  </tr>
                  <tr>
                    <?php } ?>
                    <?php } ?>
                    <td>
                      <b>Sistema de esvaziamento:
                      </b>
                    </td>
                    <td>
                      <?php if ($pedido["descarga"] == "vazio") { echo "Sem sistema de esvaziamento"; }
elseif ($pedido["descarga"] == "d_simples") { echo "Válvula simples"; }
elseif ($pedido["descarga"] == "d_prot_presilha") { echo "Válvula com proteção tipo \"X\""; }
elseif ($pedido["descarga"] == "d_prot_mochila") { echo "Válvula com proteção tipo flap"; }
elseif ($pedido["descarga"] == "d_afunilado") { echo "Afunilado"; }
elseif ($pedido["descarga"] == "d_total") { echo "Abertura total simples"; } ?>
                    </td>
                  </tr>
                  <tr>
                    <?php if ($pedido["descarga"] != "vazio") { ?>
                    <td>Redondo:
                    </td>
                    <td>
                      <?php if ($pedido["d_redondo"] == "d_redondo") { echo "SIM"; } else { echo "NÃO"; } ?>
                    </td>
                  </tr>
                  <tr>
                    <?php if ($pedido["descarga1"] != "") { ?>
                    <td>Diâmetro/lado:
                    </td>
                    <td>
                      <?php echo $pedido["descarga1"]; ?> cm
                    </td>
                  </tr>
                  <tr>
                    <?php } ?>
                    <?php if ($pedido["descarga2"] != "") { ?>
                    <td>Altura:
                    </td>
                    <td>
                      <?php echo $pedido["descarga2"]; ?> cm
                    </td>
                  </tr>
                  <tr>
                    <?php } ?>
                    <?php } ?>
                    <?php if ($pedido["alca"] == "vazio" && $pedido["corpo"] == "gota") { ?>
                    <td><b>Alças:</b>
                    </td>
					<td><i>Single Loop</i>
					</td>
                  </tr>
                  <tr>
                    <td>Altura do vão livre:</td>
                    <td><?php echo $pedido["alca_altura"]; ?> cm</td>
                  </tr>
                  <tr>
                    <td>Largura do slit:</td>
                    <td><?php echo $pedido["alca_fix_altura"]; ?> cm</td>
                    <?php } elseif ($pedido["alca"] != "vazio") { ?>
                    <td>
                      <b>Alças:
                      </b>
                    </td>
                    <td>
                      <?php
$qtde_alcas = mysqli_query($conn,"SELECT * FROM `qtde_alcas`");
while($row = mysqli_fetch_array($qtde_alcas)) {
if ($pedido["alca"] == $row['qtde'] . "_" . $row['cod']) { echo $row['desc']; }
}
?>
                    </td>
                  </tr>
                  <tr>
                    <td>Cor:
                    </td>
                    <td>
                      <?php if ($pedido["alca_cor"] == "branco") { echo "Branco (natural)"; }
						elseif ($pedido["alca_cor"] == "amarela") { echo "Amarela"; }
						elseif ($pedido["alca_cor"] == "azul_total") { echo "Azul"; }
						elseif ($pedido["alca_cor"] == "cinza") { echo "Cinza"; }
						elseif ($pedido["alca_cor"] == "marrom_total") { echo "Marrom"; }
						elseif ($pedido["alca_cor"] == "preta_total") { echo "Preta"; }
						elseif ($pedido["alca_cor"] == "verde_total") { echo "Verde"; }
						elseif ($pedido["alca_cor"] == "vermelha") { echo "Vermelha"; }
						elseif ($pedido["alca_cor"] == "amarelo_carijo") { echo "Amarelo Carijó"; }
						elseif ($pedido["alca_cor"] == "azul") { echo "Azul Carijó"; }
						elseif ($pedido["alca_cor"] == "cinza_carijo") { echo "Cinza Carijó"; }
						elseif ($pedido["alca_cor"] == "marrom") { echo "Marrom Carijó"; }
						elseif ($pedido["alca_cor"] == "preto") { echo "Preto Carijó"; }
						elseif ($pedido["alca_cor"] == "preto2") { echo "Preto"; }
						elseif ($pedido["alca_cor"] == "verde") { echo "Verde Carijó"; }
						elseif ($pedido["alca_cor"] == "vermelho_carijo") { echo "Vermelho Carijó"; }
						elseif ($pedido["alca_cor"] == "outra") { echo "Outra"; }
?>
                    </td>
                  </tr>
                  <tr>
                    <td>Material:
                    </td>
                    <td>
                      <?php if ($pedido["alca_material"] == "fita") { echo "Fita"; }
elseif ($pedido["alca_material"] == "tecido") { echo "Tecido"; } ?>
                    </td>
                  </tr>
                  <tr>
                    <td>Altura do vão livre:
                    </td>
                    <td>
                      <?php echo $pedido["alca_altura"]; ?> cm
                    </td>
                  </tr>
                  <tr>
                    <td>Altura de fixação:
                    </td>
                    <td>
                      <?php echo $pedido["alca_fix_altura"]; ?> cm
                    </td>
                  </tr>
                  <tr>
                    <td>Tipo de fixação:
                    </td>
                    <td>
                      <?php if($pedido["alca_fixacao"] == 0) { echo "INTERNA"; } elseif($pedido["alca_fixacao"] == 1) { echo "EXTERNA"; } ?>
                    </td>
                  </tr>
                  <?php if ($pedido["reforco_vao_livre"] == "1") { ?>
                  <tr>
                    <td>Reforço do vão livre:
                    </td>
                    <td>SIM
                    </td>
                  </tr>
                  <?php } ?>
                  <?php if ($pedido["reforco_fixacao"] == "1") { ?>
                  <tr>
                    <td>Reforço de fixação:
                    </td>
                    <td>SIM
                    </td>
                  </tr>
                  <?php } ?>
                  <?php if ($pedido["alca_dupla"] == "1") { ?>
                  <tr>
                    <td>Alça dupla:
                    </td>
                    <td>SIM
                    </td>
                  </tr>
                  <?php } ?>
                  <tr>
                    <td>Capacidade individual de cada alça:
                    </td>
                    <td>
                      <?php echo $pedido["alca_capac"]; ?> Kg
                    </td>
                  </tr>
                  <?php
} else {
?>
                <td>
                  <b>Alças:
                  </b>
                </td>
                <td>Sem alças
                </td>
              </tr>
              <?php }  ?>
              <?php if ($pedido["liner"] != "vazio") {
$query_liner = mysqli_query($conn,"SELECT * FROM `pedidos_liner` WHERE `pedido` = '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` DESC"); // ORDER BY `revisao` DESC");
$det_liner = mysqli_fetch_array($query_liner);
?>
              <tr>
                <td>
                  <b>Liner:
                  </b>
                </td>
                <td>
                  <?php
if ($pedido["liner"] == "liner_padrao") { echo "Liner padrão"; }
elseif ($pedido["liner"] == "liner_gota") { echo "Liner padrão Bag Gota"; }
elseif ($pedido["liner"] == "liner_fertil") { echo "Liner padrão Fertilizante"; }
elseif ($pedido["liner"] == "liner_afunilado") { echo "Liner afunilado"; }
elseif ($pedido["liner"] == "liner_sup_inf") { echo "Liner com válvula superior e inferior"; }
elseif ($pedido["liner"] == "liner_total_inf") { echo "Liner com abertura total e válvula inferior"; }
elseif ($pedido["liner"] == "liner_sup_fechado") { echo "Liner com válvula superior e fechado no fundo"; }
elseif ($pedido["liner"] == "liner_externo") { echo "Liner externo"; } ?>
                </td>
              </tr>
              <tr>
                <td>Tipo do liner:
                </td>
                <td>
                  <?php if ($pedido["tipo_liner"] == "vazio") { echo "Não especificado"; }
elseif ($pedido["tipo_liner"] == "liner_transp") { echo "Virgem"; }
elseif ($pedido["tipo_liner"] == "liner_canela") { echo "Canela"; }
elseif ($pedido["tipo_liner"] == "liner_cristal") { echo "Cristal"; } ?>
                </td>
              </tr>
              <?php
if (! $det_liner) {
?>
              <tr>
                <td>Espessura do liner:
                </td>
                <td>
                  <?php echo str_replace(".",",",$pedido["liner_espessura"]); ?> micra
                </td>
              </tr>
              <?php
} else {
?>
              <tr>
                <td>Largura do liner:
                </td>
                <td>
                  <?php echo str_replace(".",",",(float)number_format($det_liner["larg_liner"], 2)); ?> cm
                </td>
              </tr>
              <tr>
                <td>Comprimento do liner:
                </td>
                <td>
                  <?php
				  if($comprimento_liner != "") { echo $comprimento_liner*100; }
				  else { echo str_replace(".",",",(float)number_format($det_liner["comp_liner"], 2)); }
/*				  if($det_liner["comp_liner"] != substr($dados_tec["desc"],0,17))
				  echo str_replace(".",",",(float)number_format($det_liner["comp_liner"], 2)); */?> cm
                </td>
              </tr>
              <tr>
                <td>Espessura do liner:
                </td>
                <td>
                  <?php echo str_replace(".",",",(float)number_format($det_liner["espess_liner"], 2)); ?> micra
                </td>
              </tr>
              <?php } ?>
              <tr>
                <td>Tipo de fixação do liner:
                </td>
                <td>
                  <?php if ($pedido["fix_liner"] == "sem_fixacao") { echo "Sem fixação"; }
elseif ($pedido["fix_liner"] == "colado") { echo "Colado"; }
elseif ($pedido["fix_liner"] == "costurado") { echo "Costurado"; } 
elseif ($pedido["fix_liner"] == "colado_costurado") { echo "Colado e costurado"; }
elseif ($pedido["fix_liner"] == "liner_externo") { echo "Liner externo"; } ?>
                </td>
              </tr>
              <?php } ?>
              <tr>
                <td>
                  <b>Impressão:
                  </b>
                </td>
                <td>
                  <?php if ($pedido["no_cores"] == "0") { echo "Sem impressão"; }
elseif ($pedido["no_cores"] == "1") { echo "1 cor"; }
elseif ($pedido["no_cores"] == "2") { echo "2 cores"; }
elseif ($pedido["no_cores"] == "3") { echo "3 cores (limite ideal)"; }
elseif ($pedido["no_cores"] == "4") { echo "4 cores (consultar)"; } ?>
                </td>
              </tr>
              <?php if ($pedido["no_cores"] != "0") { ?>
              <tr>
                <td>Controle de utilização:
                </td>
                <td>
                  <?php if ($pedido["imp_controle_viag"] == "1") { echo "SIM"; } else { echo "NÃO"; } ?>
                </td>
              </tr>
              <tr>
                <td>Número sequencial:
                </td>
                <td>
                  <?php if ($pedido["imp_num_seq"] == "1") { echo "SIM"; } else { echo "NÃO"; } ?>
                </td>
              </tr>
              <tr>
                <td>Faces selecionadas:
                </td>
                <td>
                  <?php echo strtoupper($pedido["sel_faces"]); ?>
                </td>
              </tr>
              <?php } ?>
              <?php if ($pedido["porta_etq1"] == "1" || $pedido["porta_etq2"] == "1" || $pedido["porta_etq3"] == "1" || $pedido["porta_etq4"] == "1") { ?>
              <tr>
                <td colspan="2">
                  <b>Porta etiqueta:
                  </b>
                </td>
              </td>
        </tr>
        <?php } ?>
        <?php if ($pedido["porta_etq1"] == "1") { ?>
        <tr>
          <td>
            <b>Face A
            </b>
          </td>
          <td>Posição: 
            <?php if ($pedido["pos_porta_etq1"] == "") { echo "Não selecionado"; }
elseif ($pedido["pos_porta_etq1"] == "topo_meio") { echo "Topo centralizado (padrão)"; }
elseif ($pedido["pos_porta_etq1"] == "topo_direita") { echo "Topo na direita"; }
elseif ($pedido["pos_porta_etq1"] == "topo_esquerda") { echo "Topo na esquerda (45 cm do topo e 8 cm da lateral)"; }
elseif ($pedido["pos_porta_etq1"] == "centro") { echo "No centro"; }
elseif ($pedido["pos_porta_etq1"] == "cost_vert") { echo "Costurado na vertical"; }
elseif ($pedido["pos_porta_etq1"] == "personalizado") { echo "Personalizado (especificar nas obs.)"; } ?>
          </td>
        </tr>
        <tr>
          <td>Modelo:
          </td>
          <td>
            <?php if ($pedido["mod_porta_etq1"] == "") { echo "Não selecionado"; }
elseif ($pedido["mod_porta_etq1"] == "folha") { echo "Tipo folha (27 x 40 x 0,20)"; }
elseif ($pedido["mod_porta_etq1"] == "folha2") { echo "Tipo folha (27 x 20 x 0,20)"; }
elseif ($pedido["mod_porta_etq1"] == "fronha") { echo "Tipo fronha (27 x 40 x 0,15)"; }
elseif ($pedido["mod_porta_etq1"] == "aba_adesiva") { echo "Com aba adesiva (27 x 45 x 0,12)"; }
elseif ($pedido["mod_porta_etq1"] == "ziplock") { echo "Porta documento (20 x 25) - ziplock"; }
elseif ($pedido["mod_porta_etq1"] == "aberto_tras") { echo "Com abertura posicionada para a parte de trás (23x40cm)"; } ?>
          </td>
        </tr>
        <?php } ?>
        <?php if ($pedido["porta_etq2"] == "1") { ?>
        <tr>
          <td>
            <b>Face B
            </b>
          </td>
          <td>Posição: 
            <?php if ($pedido["pos_porta_etq2"] == "") { echo "Não selecionado"; }
elseif ($pedido["pos_porta_etq2"] == "topo_meio") { echo "Topo centralizado (padrão)"; }
elseif ($pedido["pos_porta_etq2"] == "topo_direita") { echo "Topo na direita"; }
elseif ($pedido["pos_porta_etq2"] == "topo_esquerda") { echo "Topo na esquerda (45 cm do topo e 8 cm da lateral)"; }
elseif ($pedido["pos_porta_etq2"] == "centro") { echo "No centro"; }
elseif ($pedido["pos_porta_etq2"] == "cost_vert") { echo "Costurado na vertical"; }
elseif ($pedido["pos_porta_etq2"] == "personalizado") { echo "Personalizado (especificar nas obs.)"; } ?>
          </td>
        </tr>
        <tr>
          <td>Modelo:
          </td>
          <td>
            <?php if ($pedido["mod_porta_etq2"] == "") { echo "Não selecionado"; }
elseif ($pedido["mod_porta_etq2"] == "folha") { echo "Tipo folha (27 x 40 x 0,20)"; }
elseif ($pedido["mod_porta_etq2"] == "folha2") { echo "Tipo folha (27 x 20 x 0,20)"; }
elseif ($pedido["mod_porta_etq2"] == "fronha") { echo "Tipo fronha (27 x 40 x 0,15)"; }
elseif ($pedido["mod_porta_etq2"] == "aba_adesiva") { echo "Com aba adesiva (27 x 45 x 0,12)"; }
elseif ($pedido["mod_porta_etq2"] == "ziplock") { echo "Porta documento (20 x 25) - ziplock"; }
elseif ($pedido["mod_porta_etq2"] == "aberto_tras") { echo "Com abertura posicionada para a parte de trás (23x40cm)"; } ?>
          </td>
        </tr>
        <?php } ?>
        <?php if ($pedido["porta_etq3"] == "1") { ?>
        <tr>
          <td>
            <b>Face C
            </b>
          </td>
          <td>Posição: 
            <?php if ($pedido["pos_porta_etq3"] == "") { echo "Não selecionado"; }
elseif ($pedido["pos_porta_etq3"] == "topo_meio") { echo "Topo centralizado (padrão)"; }
elseif ($pedido["pos_porta_etq3"] == "topo_direita") { echo "Topo na direita"; }
elseif ($pedido["pos_porta_etq3"] == "topo_esquerda") { echo "Topo na esquerda (45 cm do topo e 8 cm da lateral)"; }
elseif ($pedido["pos_porta_etq3"] == "centro") { echo "No centro"; }
elseif ($pedido["pos_porta_etq3"] == "cost_vert") { echo "Costurado na vertical"; }
elseif ($pedido["pos_porta_etq3"] == "personalizado") { echo "Personalizado (especificar nas obs.)"; } ?>
          </td>
        </tr>
        <tr>
          <td>Modelo:
          </td>
          <td>
            <?php if ($pedido["mod_porta_etq3"] == "") { echo "Não selecionado"; }
elseif ($pedido["mod_porta_etq3"] == "folha") { echo "Tipo folha (27 x 40 x 0,20)"; }
elseif ($pedido["mod_porta_etq3"] == "folha2") { echo "Tipo folha (27 x 20 x 0,20)"; }
elseif ($pedido["mod_porta_etq3"] == "fronha") { echo "Tipo fronha (27 x 40 x 0,15)"; }
elseif ($pedido["mod_porta_etq3"] == "aba_adesiva") { echo "Com aba adesiva (27 x 45 x 0,12)"; }
elseif ($pedido["mod_porta_etq3"] == "ziplock") { echo "Porta documento (20 x 25) - ziplock"; }
elseif ($pedido["mod_porta_etq3"] == "aberto_tras") { echo "Com abertura posicionada para a parte de trás (23x40cm)"; } ?>
          </td>
        </tr>
        <?php } ?>
        <?php if ($pedido["porta_etq4"] == "1") { ?>
        <tr>
          <td>
            <b>Face D
            </b>
          </td>
          <td>Posição: 
            <?php if ($pedido["pos_porta_etq4"] == "") { echo "Não selecionado"; }
elseif ($pedido["pos_porta_etq4"] == "topo_meio") { echo "Topo centralizado (padrão)"; }
elseif ($pedido["pos_porta_etq4"] == "topo_direita") { echo "Topo na direita"; }
elseif ($pedido["pos_porta_etq4"] == "topo_esquerda") { echo "Topo na esquerda (45 cm do topo e 8 cm da lateral)"; }
elseif ($pedido["pos_porta_etq4"] == "centro") { echo "No centro"; }
elseif ($pedido["pos_porta_etq4"] == "cost_vert") { echo "Costurado na vertical"; }
elseif ($pedido["pos_porta_etq4"] == "personalizado") { echo "Personalizado (especificar nas obs.)"; } ?>
          </td>
        </tr>
        <tr>
          <td>Modelo:
          </td>
          <td>
            <?php if ($pedido["mod_porta_etq4"] == "") { echo "Não selecionado"; }
elseif ($pedido["mod_porta_etq4"] == "folha") { echo "Tipo folha (27 x 40 x 0,20)"; }
elseif ($pedido["mod_porta_etq4"] == "folha2") { echo "Tipo folha (27 x 20 x 0,20)"; }
elseif ($pedido["mod_porta_etq4"] == "fronha") { echo "Tipo fronha (27 x 40 x 0,15)"; }
elseif ($pedido["mod_porta_etq4"] == "aba_adesiva") { echo "Com aba adesiva (27 x 45 x 0,12)"; }
elseif ($pedido["mod_porta_etq4"] == "ziplock") { echo "Porta documento (20 x 25) - ziplock"; }
elseif ($pedido["mod_porta_etq4"] == "aberto_tras") { echo "Com abertura posicionada para a parte de trás (23x40cm)"; } ?>
          </td>
        </tr>
        <?php } ?>
        <tr>
          <td>
            <b>Embalagem:
            </b>
          </td>
          <td>
            <?php if ($pedido["fardo"] == "vazio") { echo "Não selecionado"; }
elseif ($pedido["fardo"] == "10") { echo "Fardo de 10 peças"; }
elseif ($pedido["fardo"] == "15") { echo "Fardo de 15 peças"; }
elseif ($pedido["fardo"] == "20") { echo "Fardo de 20 peças"; }
elseif ($pedido["fardo"] == "25") { echo "Fardo de 25 peças"; } ?>
          </td>
        </tr>
        <tr>
          <td>Big Bag palletizado:
          </td>
          <td>
            <?php if ($pedido["palletizado"] == "vazio") { echo "NÃO"; }
elseif ($pedido["palletizado"] == "80") { echo "80"; }
elseif ($pedido["palletizado"] == "100") { echo "100"; }
elseif ($pedido["palletizado"] == "125") { echo "125"; }
elseif ($pedido["palletizado"] == "150") { echo "150"; }
elseif ($pedido["palletizado"] == "175") { echo "175"; }
elseif ($pedido["palletizado"] == "200") { echo "200"; }
elseif ($pedido["palletizado"] == "250") { echo "250"; } ?>
          </td>
        </tr>
        <?php if ($pedido["velcro"] == "1") { ?>
        <tr>
          <td>Velcro (Macho/Femea):
          </td>
          <td>SIM
          </td>
        </tr>
        <?php } ?>
        <?php if ($pedido["cinta_trav"] == "1") { ?>
        <tr>
          <td>Cinta de travamento:
          </td>
          <td>SIM
          </td>
        </tr>
        <?php } ?>
        <?php if ($pedido["gravata"] == "1") { ?>
        <tr>
          <td>Gravata de travamento:
          </td>
          <td><?php echo number_format($pedido["med_gravata"],0,",","."); ?> cm
          </td>
        </tr>
        <?php } ?>
        <?php if ($pedido["sapata"] == "1") { ?>
        <tr>
          <td>Pack Less:
          </td>
          <td>SIM
          </td>
        </tr>
        <?php } ?>
        <?php if ($pedido["flap"] == "1") { ?>
        <tr>
          <td>Tampa para fundo falso:
          </td>
          <td>SIM
          </td>
        </tr>
        <?php } ?>
      </table>


  </div>
</div>

<?php
if($_SESSION['user']['nivel'] != '4') {
	?>
	</div>
	<div class="col-md-5 col-xs-12" style="padding-right:0;">
	<?php
} else {
	?>
	<div class="col-md-5 col-xs-12" style="padding-left:0;">
	<?php
}
?>
  <div class="x_panel">
    <div class="x_title">
      <h2>Desenho
      </h2>
      <ul class="nav navbar-right panel_toolbox">
        <li>
          <a class="collapse-link">
            <i class="fa fa-chevron-up">
            </i>
          </a>
        </li>
      </ul>
      <div class="clearfix">
      </div>
    </div>
    <div class="x_content">
<?php
//if(sprintf('%05d', $row_pedidos["pedido"]) == "02026" || sprintf('%05d', $row_pedidos["pedido"]) == "01840" || sprintf('%05d', $row_pedidos["pedido"]) == "01831" || sprintf('%05d', $row_pedidos["pedido"]) == "01141" || sprintf('%05d', $row_pedidos["pedido"]) == "00249") {
if(in_array(sprintf('%05d', $row_pedidos["pedido"]), $des_especiais, true)) {
	echo '<center><img src="images/desenhos/'.sprintf('%05d', $row_pedidos["pedido"]).'.png" border="0"></center>';
} else {
	echo $desenho;
}
echo '<center>';
if($pedido["cost_fio_topo"] == "1") {
	echo '<b>COSTURA A FIO NO TOPO</b><br>';
}
if($pedido["cost_fio_base"] == "1") {
	echo '<b>COSTURA A FIO NA BASE</b><br>';
}
if($row_impostos["class_prod"] == "a") {
	echo '<b>(A) ALTA PRODUTIVIDADE</b><br>';
} elseif($row_impostos["class_prod"] == "b") {
	echo '<b>(B) MÉDIA PRODUTIVIDADE</b><br>';
} elseif($row_impostos["class_prod"] == "c") {
	echo '<b>(C) BAIXA PRODUTIVIDADE</b><br>';
}
if($pedido["liner"] == "liner_fertil") {
	echo '<br><b>LINER SOLDADO NA BASE, NÃO DEVE<br>ULTRAPASSAR A PARTE INFERIOR DO BIG BAG.</b><br>';
}
echo '</center><br>';
?>

  <?php if ($pedido["obs_cliente"] != "") { ?>
	<table border="0" class="table table-bordered lessover">
	  <tr>
		<td>
		  <b>Observações técnicas:
		  </b> 
		  <?php echo $pedido["obs_cliente"]; ?>
		</td>
	  </tr>
	</table>
	<?php } ?>
</div>
</div>

</div>

<?php
if($_SESSION['user']['nivel'] != '4') {
?>
<div class="col-md-12 col-xs-12" style="padding:0;">
<?php
} else {
?>
<div class="col-md-7 col-xs-12" style="padding:0;">
<?php
}
?>
  <div class="x_panel">
    <div class="x_title">
      <h2>Quantidades
      </h2>
      <ul class="nav navbar-right panel_toolbox">
        <li>
          <a class="collapse-link">
            <i class="fa fa-chevron-up">
            </i>
          </a>
        </li>
      </ul>
      <div class="clearfix">
      </div>
    </div>
    <div class="x_content">

	<br>
<?php
/*
echo "<pre>";
echo "ID: ".$pedido["id"]." - REV DET: ".$rev_det." - REV VALOR: ".$rev_valor;
echo "</pre>";
*/
?>
      <table border="0" class="table table-bordered lessover">
        <thead>
        <tr>
          <th><b>Descrição</b></th>
          <th><b>Qtde.</b></th>
          <th><b>Largura</b></th>
          <th><b>Corte</b></th>
          <th><b>Quantidade de material</b></th>
          <th><b>Matéria-prima</b></th>
          <th><b>Peso</b></th>
        </tr>
        </thead>
        <?php
echo $det_pedido_menor;
?>
      </table>

      <br>
      <?php
$query_qualidade = mysqli_query($conn,"SELECT * FROM `pedidos_qualidade` WHERE `pedido` LIKE '".(float)$id_pedido."' ORDER BY `id` DESC LIMIT 0,1");
$row_qualidade = mysqli_fetch_array($query_qualidade);
?>
      <?php if ($_SESSION['user']['nivel'] != '4') { ?>
      <?php
if ($row_qualidade != "") {
?>
      <table border="0" class="table table-bordered lessover">
        <tr>
          <td>
            <b>Observações da qualidade
            </b>
          </td>
        </tr>
        <tr>
          <td>
            <?php echo nl2br($row_qualidade["obs_qualidade"]); ?>
          </td>
        </tr>
      </table>
      <?php } ?>
      <?php } ?>
      <?php if ($_SESSION['user']['nivel'] != '4') { ?>

</div>
</div>

  <div class="x_panel">
    <div class="x_title">
      <h2>Histórico
      </h2>
      <ul class="nav navbar-right panel_toolbox">
        <li>
          <a class="collapse-link">
            <i class="fa fa-chevron-up">
            </i>
          </a>
        </li>
      </ul>
      <div class="clearfix">
      </div>
    </div>
    <div class="x_content">
	<br>
      <table border="0" class="table table-bordered lessover">
        <thead>
        <tr>
          <th>
            <b>Histórico do orçamento
            </b>
          </th>
          <th>
            <b>Data
            </b>
          </th>
        </tr>
        </thead>
        <?php
$query_log_sistema = mysqli_query($conn,"SELECT * FROM `log_sistema` WHERE `pedido` LIKE '".(float)$id_pedido."' ORDER BY `id` DESC"); // LIMIT 10
while ($log_sistema = mysqli_fetch_array($query_log_sistema)){
$phpdate = strtotime( $log_sistema["data"] );
$data = date( 'd/m/Y', $phpdate );
$hora = date( 'H:i:s', $phpdate );
?>
        <tr>
          <td>
            <?php echo $log_sistema["nome"]." - ".$log_sistema["desc"]; ?>
          </td>
          <td>
            <?php echo $data." - ".$hora; ?>
          </td>
        </tr>
        <?php
}
?>
      </table>
      <?php } ?>
      <?php
$query_aprovacao = mysqli_query($conn,"SELECT * FROM `pedidos_aprova` WHERE `id_ped` LIKE '".(float)$id_pedido."' ORDER BY `id` DESC");
$row_aprovacao = mysqli_fetch_array($query_aprovacao);
if ($row_qualidade["aprova"] == "sim" AND $row_qualidade["id_ped"] == $pedido["id"]) {
$data_quali_aprova = $row_qualidade['data'];
$data_quali_aprova = strtotime($data_quali_aprova);
$data_quali_aprova1 = date("d/m/Y", $data_quali_aprova);
$data_quali_aprova2 = date("H:i:s", $data_quali_aprova);
$txt_aprovacao .= 'Aprovado pela qualidade por '.$row_qualidade["nome"].' em '.$data_quali_aprova1.' às '.$data_quali_aprova2.'.<br>';
}
if ($row_aprovacao['nome'] === NULL) {
$txt_aprovacao .= '<font color="#FF0000">Este orçamento ainda não foi liberado ou aprovado.</font>';
} else {
$data_aprova = $row_aprovacao['data'];
$data_aprova = strtotime($data_aprova);
$data_aprova1 = date("d/m/Y", $data_aprova);
$data_aprova2 = date("H:i:s", $data_aprova);
$txt_aprovacao .= 'Orçamento aprovado por '.$row_aprovacao["nome"].' em '.$data_aprova1.' às '.$data_aprova2.'.';
}
?>
      <?php if ($pedido["status"] == "6" and $_SESSION['user']['nivel'] == '4') { ?>
      <form method="POST" class="orcamento">
        <table border="0" class="table table-bordered lessover">
          <tr>
            <td>
              <b>Observações da qualidade:
              </b>
              <textarea name="obs_qualidade" style="height: 120px; border: 0; box-shadow: none; resize: none;" class="form-control"><?php echo $row_qualidade["obs_qualidade"]; ?></textarea>
            </td>
          </tr>
        </table>
        <?php } else { ?>
        <table border="0" class="table table-bordered lessover">
          <tr>
            <td style="vertical-align: middle;">
              <b><i class="fa fa-check"></i> Aprovação
              </b>
            </td>
            <td>
              <?php echo $txt_aprovacao; ?>
            </td>
          </tr>
        </table>
        <?php } ?>
        </td>
  </tr>
  <tr>
    <td colspan="2">
      <?php if ($pedido["status"] == "1" || $pedido["status"] == "2") { ?>
      <table border="0" class="table table-bordered lessover">
        <tr>
          <td>
            <b>Correções:
            </b> 
            <pre><?php
// style="white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;"

$str_correcoes = $pedido["correcoes"];
$str_search   = array("<", ">", "\r\n", "\n", "\r");
$str_replace = array("&#60;", "&#62;", "<br />", "<br />", "<br />");
// Processes \r\n's first so they aren't converted twice.
$new_str_correcoes = str_replace($str_search, $str_replace, $str_correcoes);
echo $new_str_correcoes; ?></pre>
          </td>
        </tr>
      </table>
      <?php } ?>
      <?php if ($pedido["status"] == "2") { ?>
      <?php if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '5') { ?>
      <form name="correcoes" class="orcamento" action="resumo.php?acao=correcoes" method="post">
        <table border="0" class="table table-bordered lessover">
          <tr>
            <td>
              <b>Adicionar correções:
              </b> 
              <textarea name="correcoes" class="form-control" style="height:90px; background-color:#fefad2; border: 1px solid #ded300;">
              </textarea>
            </td>
          </tr>
        </table>
        <input type="hidden" name="pedido" value="<?php echo $id_pedido; ?>">
        <input type="hidden" name="id_ped" value="<?php echo $pedido["id"]; ?>">
        <center>
          <a href="resumo.php?acao=libera&pedido=<?php echo sprintf('%05d', $id_pedido); ?>&id=<?php echo $pedido["id"]; ?>" class="btn btn-success"> LIBERAR ORÇAMENTO </a>
          <button type="submit" class="btn btn-warning"> RETORNAR PARA ANÁLISE </button>
          <a href="resumo.php?acao=recusa&pedido=<?php echo sprintf('%05d', $id_pedido); ?>&id=<?php echo $pedido["id"]; ?>" class="btn btn-danger"> RECUSAR ORÇAMENTO </a>
          <?php /* <a href="pdf_resumo.php?pedido=<?php echo sprintf('%05d', $id_pedido); ?>" download class="btn btn-danger"><i class="fa fa-download"></i> GERAR PDF DO RESUMO </a>
          <input type="button" value=" GERAR PDF DO RESUMO " onclick="window.open('pdf_resumo.php?pedido=<?php echo sprintf('%05d', $id_pedido); ?>','_blank')" class="input"> */ ?>
        </center>
      </form>
      <?php } } ?>
    </td>
  </tr>
  </table>
</div>
</div>
</div>
<div class="clearfix"></div>
<br>
<br>
<?php if ($pedido["status"] == "1") { ?>
<center>
  <?php
/*
  if ($row_qualidade["aprova"] == "sim" AND $row_qualidade["id_ped"] == $pedido["id"]) { ?>
  <form name="aprovacao" class="orcamento" action="resumo.php?acao=envia" method="post">
    <input type="hidden" name="pedido" value="<?php echo $id_pedido; ?>">
    <input type="hidden" name="id_ped" value="<?php echo $pedido["id"]; ?>">
    <input type="submit" value=" ENVIAR PARA APROVAÇÃO " class="btn btn-warning">
    <?php } else { ?>
    <form name="aprovacao" class="orcamento" action="resumo.php?acao=quali_envia" method="post">
      <input type="hidden" name="pedido" value="<?php echo $id_pedido; ?>">
      <input type="hidden" name="id_ped" value="<?php echo $pedido["id"]; ?>">
      <button type="submit" class="btn btn-warning"> ENVIAR PARA QUALIDADE </button>
      <?php } ?>
*/ ?>
	<form name="aprovacao" class="orcamento" action="resumo.php?acao=envia" method="post">
	<input type="hidden" name="pedido" value="<?php echo $id_pedido; ?>">
	<input type="hidden" name="id_ped" value="<?php echo $pedido["id"]; ?>">
	<input type="submit" value=" ENVIAR PARA APROVAÇÃO " class="btn btn-warning">

	<a href="pedidos.php?acao=analisar&pedido=<?php echo sprintf('%05d', $id_pedido); ?>" class="btn btn-info"> ANALISAR VALORES </a>
	<a href="pedidos.php?ped=<?php echo sprintf('%05d', $id_pedido); ?>" class="btn btn-primary"> EDITAR ORÇAMENTO </a>
	<?php /* <a href="pdf_resumo.php?pedido=<?php echo sprintf('%05d', $id_pedido); ?>" download class="btn btn-danger"><i class="fa fa-download"></i> GERAR PDF DO RESUMO </a> */ ?>
	<?php /* <input type="button" value=" GERAR PDF DO RESUMO " onclick="window.open('pdf_resumo.php?pedido=<?php echo sprintf('%05d', $id_pedido); ?>','_blank')" class="input"> */ ?>
	<a href="duplicar.php?id=<?php echo $pedido["id"]; ?>" class="btn btn-dark"> DUPLICAR ORÇAMENTO </a>
	<a href="resumo_print.php?pedido=<?php echo sprintf('%05d', $id_pedido); ?>" target="_blank" class="btn btn-default"> <i class="fa fa-print"></i> </a>
    </form>
    </center>
  <?php } elseif ($pedido["status"] == "2" && $_SESSION['user']['nivel'] != '1') { ?>
  <center>
    <span class="btn btn-default nolink"><img style="vertical-align: middle;" src="images/icons/status2.png" border="0"> Aguardando liberação</span>
  </center>
  <?php } elseif ($pedido["status"] == "3") { ?>
  <center>
    <span class="btn btn-default nolink"><img style="vertical-align: middle;" src="images/icons/status3.png" border="0"> Orçamento liberado</span>
    <a href="resumo.php?acao=reativa&pedido=<?php echo sprintf('%05d', $id_pedido); ?>&id=<?php echo $pedido["id"]; ?>" class="btn btn-warning"> RETORNAR PARA ANÁLISE </a>
    <?php /* <a href="pdf_resumo.php?pedido=<?php echo sprintf('%05d', $id_pedido); ?>" download class="btn btn-danger"><i class="fa fa-download"></i> GERAR PDF DO RESUMO </a> */ ?>
    <?php /* <span class="orcamento"><input type="button" value=" GERAR PDF DO RESUMO " onclick="window.open('pdf_resumo.php?pedido=<?php echo sprintf('%05d', $id_pedido); ?>','_blank')" class="input"></span> */ ?>
    <a href="duplicar.php?id=<?php echo $pedido["id"]; ?>" class="btn btn-dark"> DUPLICAR ORÇAMENTO </a>
	<a href="resumo_print.php?pedido=<?php echo sprintf('%05d', $id_pedido); ?>" class="btn btn-default"> <i class="fa fa-print"></i> </a>
</center>
<?php } elseif ($pedido["status"] == "4") { ?>
<center>
  <span class="btn btn-default nolink"><img style="vertical-align: middle;" src="images/icons/status4.png" border="0"> Orçamento aprovado</span>
  <a href="duplicar.php?id=<?php echo $pedido["id"]; ?>" class="btn btn-dark"> DUPLICAR ORÇAMENTO </a>
</center>
<?php } elseif ($pedido["status"] == "5") { ?>
<center>
  <span class="btn btn-default nolink"><img style="vertical-align: middle;" src="images/icons/status5.png" border="0"> Orçamento recusado</span>
</center>
<?php } elseif ($pedido["status"] == "6" and $_SESSION['user']['nivel'] == '4') { ?>
<center class="orcamento">
  <button type="submit" formaction="resumo.php?acao=quali_libera&id_ped=<?php echo $pedido["id"]; ?>&pedido=<?php echo (float)$id_pedido; ?>" class="btn btn-success"> LIBERAR ORÇAMENTO </button>
  <button type="submit" formaction="resumo.php?acao=quali_retorna&id_ped=<?php echo $pedido["id"]; ?>&pedido=<?php echo (float)$id_pedido; ?>" class="btn btn-warning"> RETORNAR PARA ANÁLISE </button>
</center>
</form>
<?php } elseif ($pedido["status"] == "6") { ?>
<center>
  <span class="btn btn-default nolink"><img style="vertical-align: middle;" src="images/icons/status6.png" border="0"> Orçamento aguardando aprovação da qualidade</span>
	<?php if($_SESSION['user']['nivel'] == '1') { ?>
	<a href="resumo.php?acao=reativa&pedido=<?php echo sprintf('%05d', $id_pedido); ?>&id=<?php echo $pedido["id"]; ?>" class="btn btn-warning"> RETORNAR PARA ANÁLISE </a>
	<?php } ?>
</center>
<?php } elseif ($pedido["status"] == "7") { ?>
<center>
  <form name="aprovacao" class="orcamento" action="resumo.php?acao=envia" method="post">
    <input type="hidden" name="pedido" value="<?php echo $id_pedido; ?>">
    <input type="hidden" name="id_ped" value="<?php echo $pedido["id"]; ?>">
    <span class="btn btn-default nolink"><img style="vertical-align: middle;" src="images/icons/status7.png" border="0"> Aprovado pela qualidade</span>
    <button type="submit" class="btn btn-info"> ENVIAR PARA APROVAÇÃO </button>
    <a href="resumo.php?acao=reativa&pedido=<?php echo sprintf('%05d', $id_pedido); ?>&id=<?php echo $pedido["id"]; ?>" class="btn btn-warning"> RETORNAR PARA ANÁLISE </a>
<? /*    <a href="pdf_resumo.php?pedido=<?php echo sprintf('%05d', $id_pedido); ?>" download class="btn btn-danger"><i class="fa fa-download"></i> GERAR PDF DO RESUMO </a> */ ?>
  </form>  
</center>
  <?php } ?>
  <br>
  <br>
  <br>
  <?php
require("rodape.php");
?>
