<?php
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

//require("cabecalho.php"); 

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <title>Resumo de Orçamento</title>

	<script src="../fitec/js/jquery.min.js"></script>
	<link href="../quadro/css/lity.css" rel="stylesheet">
	<script src="../quadro/js/lity.js"></script>

<style>

@media print{
	@page {
		size: portrait;
		margin: 5mm 5mm;
	}
}

html {
	//margin:20px 25px;
}

body {
	font-family:Arial,sans-serif;
	font-size: 7pt;
}
table {
	width: 100%;
    border-collapse: collapse;
}
table, td, th {
    /* border-bottom: 1px solid #888; */
	text-align: center;
	padding: 1px;
	border: 1px solid #000;
}
th {
	background: #D5D5D5;
}
td {
    /* padding: 5px; */
}

hr {
	border-top: 2px solid #888;
	border-bottom: 0;
	border-left: 0;
	border-right: 0;
}

a {
	color:#000000;
	text-decoration:none;
}

.tab_orcamento {
	margin-top: 5px;
	margin-bottom: 5px;
	width: 100%;
	font-size: 10px;
	border-collapse: collapse;
	line-height:7.3pt;
}
.tab_orcamento td {
	border: 1px solid #888;
	padding: 2px 5px;
}
.tab_orcamento th {
	border: 1px solid #888;
	padding: 2px 5px;
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
	font-size: 7pt;
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

.text-left {
	text-align: left;
}

.text-center {
	text-align: center;
}

h3 {
	font-size:9pt;
}

</style>
</head>

<body>

<div class="container body">
<?php



?>
<style>
  .right_col {
    font-size:14px;
  }
</style>
<div class="x_panel">
  <div class="x_content">
<?php

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
          <font size="3">
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
      <tr>
        <td>
          <b>Cliente:
          </b> 
          <?php echo $pedido["nome_cliente"]; ?>
        </td>
        <td>
          <b>Referência:
          </b> 
          <?php echo $pedido["referencia"]; ?>
        </td>
      </tr>
      <tr>
<? /*        <td><b>Fornecedora:</b> <?php

if ($pedido["fornecedora"] == "valor_sp") {
echo "BSS/SP";
} elseif ($pedido["fornecedora"] == "valor_mg") {
echo "BSS/MG";
} elseif ($pedido["fornecedora"] == "valor_ba") {
echo "BSS/NE";
} elseif ($pedido["fornecedora"] == "valor_bonpar") {
echo "BonPar (Paraguai)";
}

?>
        </td>
*/ ?>
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
      </tr>
    </table>
      <?php
// $query_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE `pedido` LIKE '".$pedido["pedido"]."' ORDER BY `revisao` DESC");
$row_pedidos = $pedido; // mysqli_fetch_array($query_pedidos);
$desenho .= '<table width="360" border="0" align="center">
<tr>
<td align="right" valign="bottom">Desenho ilustrativo</td>
</tr>
</table>
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
	$desenho .= '<div id="dim_alca_fix_altura" style="z-index:10; top: 330px; left: 307px;" class="medidas">'.str_replace(".",",",$row_pedidos["alca_fix_altura"]).'</div>';
	$desenho .= '<div id="dim_alca_altura" style="z-index:10; top: 298px; left: 307px;" class="medidas">'.str_replace(".",",",$row_pedidos["alca_altura"]).'</div>';
	$desenho .= '<div id="dim_altura" style="z-index:9; top: 378px; left: 307px;" class="medidas">'.str_replace(".",",",$row_pedidos["altura"]).'</div>';
}
$desenho .= '<div id="dim_base2" style="z-index:8; top: 504px; left: 253px;" class="medidas">'.str_replace(".",",",$row_pedidos["base2"]).'</div>';
$desenho .= '<div id="dim_base1" style="z-index:7; top: 497px; left: 36px;" class="medidas">'.str_replace(".",",",$row_pedidos["base1"]).'</div>';
if ($row_pedidos["liner"] != "") {
$desenho .= '<div id="liner" style="z-index:6;" class="desenho"><img src="images/desenhos/'.$row_pedidos["liner"].'.png" border="0"></div>';
}

if ($row_pedidos["descarga"] != "") {
	if($row_pedidos["corpo"] == 'gota') {
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
<table border="0" style="border:0;">
<tr><td style="width:50%; border:0;" valign="top">
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
<?php
/*
                  <tr>
                    <td>Gramatura do corpo:
                    </td>
                    <td>
                      <?php 

$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `gramat_real` LIKE '".$pedido["gramat_corpo"]."';");
$gramat_row = mysqli_fetch_array($gramaturas_query);
	echo $gramat_row['gramat_desc'];

/ * if ($pedido["gramat_corpo"] == "130") { echo "130"; }
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
elseif ($pedido["gramat_corpo"] == "295") { echo "270 + 25"; } * / ?> g/m²
                    </td>
                  </tr>
*/
?>
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
                  <?php } /* ?>
                  <tr>
                    <td>Capacidade individual de cada alça:
                    </td>
                    <td>
                      <?php echo $pedido["alca_capac"]; ?> Kg
                    </td>
                  </tr>
                  <?php */
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
</td><td style="width:50%; border:0;" valign="top">
  <div class="x_panel">
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
echo '</center>';
?>
</div>
</div>
</td></tr></table>

  <?php if ($pedido["obs_cliente"] != "") { ?>
	<br>
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
<br>
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
    <div class="x_content">
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

</div>
<?php }
require("rodape.php");
?>
