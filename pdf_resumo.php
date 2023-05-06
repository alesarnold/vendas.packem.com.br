<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 


$id_pedido = $_GET["pedido"];

if ($id_pedido == "") { die(); }

$query_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE `pedido` = '".$id_pedido."' ORDER BY `id` DESC LIMIT 0,1"); // ORDER BY `revisao` DESC");
$pedido = mysqli_fetch_array($query_pedidos);

$i = 0;

$valor_final_venda = $pedido["valor_final"];

$query_detalhes = mysqli_query($conn,"SELECT * FROM `pedidos_det` WHERE `revisao_valor` LIKE (SELECT MAX(`revisao_valor`) FROM `pedidos_det` WHERE `pedido` LIKE '".(float)$id_pedido."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".(float)$id_pedido."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` ASC");



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
		if(substr($row_pedidos_det["desc"],0,8) == "Liner - "){ $comprimento_liner = $row_pedidos_det["qtde_mat"]; }
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
			if ($row_pedidos_det['gramat'] != "0" && substr($row_pedidos_det['desc'], 0, 6) != "Liner ") {
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


$query_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE `pedido` LIKE '".$pedido["pedido"]."' ORDER BY `id` DESC"); //ORDER BY `revisao` DESC");
$row_pedidos = mysqli_fetch_array($query_pedidos);


$desenho .= '<div style="margin:auto;width:357px;">';

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


$resumo = '<!DOCTYPE html>
<html>
<head>
<style>
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

.tab_orcamento {
	margin-top: 5px;
	margin-bottom: 10px;
	width: 100%;
	font-size: 8pt;
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

<body>

<script type="text/php">
if ( isset($pdf) ) {
	$font = Font_Metrics::get_font("Arial", "normal");
	$pdf->page_text(292, 797, "{PAGE_NUM} de {PAGE_COUNT}", $font, 10, array(0,0,0));
}
</script>

<h1>Ficha técnica</h1><hr>

<br>
<table border="0" class="tab_orcamento">
<tr>
<td colspan="2" width="50%" style="border-right:0;">Número do orçamento</td>
<td colspan="2" width="50%" style="border-left:0;" align="right"><font size="5"><b>'.sprintf('%05d', $id_pedido).' / '.sprintf('%02d', $revisao).'</b></font></td>
</tr>
<tr>';

if ($pedido["referencia"] != "") {
	$resumo .= '<td colspan="2"><b>Cliente:</b> '.utf8_encode($pedido["nome_cliente"]).'</td>
	<td colspan="2"><b>Referência:</b> '.utf8_encode($pedido["referencia"]).'</td>';
} else {
	$resumo .= '<td colspan="4"><b>Cliente:</b> '.utf8_encode($pedido["nome_cliente"]).'</td>';
}

$resumo .= '</tr>
<tr>
<td><b>'.strtoupper($pedido["selecao"]).':</b> '.$pedido["cnpj_cpf"].'</td>
<td><b>Cidade:</b> '.utf8_encode($pedido["cidade_cliente"]).'</td>
<td><b>UF:</b> '.$pedido["uf_cliente"].'</td>';

if ($pedido["prazo"] == "0") {
	$resumo .= '<td><b>Prazo de pagamento:</b> A vista</td>';
} elseif ($pedido["prazo"] == "-1") {
	$resumo .= '<td><b>Prazo de pagamento:</b> Antecipado</td>';
} else {
	if ($pedido["mercado"] == "ext") {
		$resumo .= '<td><b>Prazo de pagamento:</b> '.$pedido["prazo"].'</td>';
	} else {
		$resumo .= '<td><b>Prazo de pagamento:</b> '.$pedido["prazo"].' D.D.L.</td>';
	}
}

$resumo .= '</tr>
<tr>
<td><b>Fornecedora:</b> ';

if ($pedido["fornecedora"] == "valor_sp") {
	$resumo .= "BSS/SP";
} elseif ($pedido["fornecedora"] == "valor_mg") {
	$resumo .= "BSS/MG";
} elseif ($pedido["fornecedora"] == "valor_ba") {
	$resumo .= "BSS/NE";
} elseif ($pedido["fornecedora"] == "valor_f1") {
	$resumo .= "BSS/FILIAL BA";
} elseif ($pedido["fornecedora"] == "valor_bonpar") {
	$resumo .= "BSS/PY";
}

$resumo .= '</td>
<td><b>Quantidade:</b> '.$pedido["qtde"].'</td>
<td><b>Frete tipo:</b> '.strtoupper($pedido["frete"]).'</td>
<td><b>Distância aproximada:</b><br>';

if ($pedido["distancia_aprox"] == "0") {
	$resumo .= "Frete FOB";
} elseif ($pedido["distancia_aprox"] == "1") {
	$resumo .= "Menos de 200 km";
} elseif ($pedido["distancia_aprox"] == "2") {
	$resumo .= "De 201 a 500 km";
} elseif ($pedido["distancia_aprox"] == "3") {
	$resumo .= "Acima de 501 km";
}

$resumo .= '</td>
</tr>';

$embarques = $pedido["embarques"];
$representante = $pedido["representante"];
$resumo .= '<tr>
<td colspan="2"><b>Embarques:</b> '.$embarques.'</td>
<td colspan="2"><b>Representante:</b> '.$representante.'</td>
</tr>';

$resumo .= '</table>';

$resumo .= '<table border="0">
	<tr><td valign="top" width="50%" style="padding:0;">

<table border="0" class="tab_orcamento">';

if ($pedido["nome_prod"] != "") {
	$resumo .= '<tr><td><b>Nome do produto acondicionado:</b></td><td>'.utf8_encode($pedido["nome_prod"]).'</td></tr>';
}

$resumo .= '<tr>
<td width="40%">Quantidade:</td><td width="60%">'.$pedido["qtde"].'</td>
</tr><tr>
<td>Segmento:</td><td>';


$segmento = mysqli_query($conn,"SELECT * FROM segmentos");
while($row = mysqli_fetch_array($segmento)) {
	if ($row['id'] == $pedido["segmento_cliente"]) {
		$resumo .= utf8_encode($row['segmento']);
	}
}

$resumo .= '</td>
</tr><tr>';

if ($pedido["dens_aparente"] != "") {
	$resumo .= '<td>Densidade aparente:</td><td>'.$pedido["dens_aparente"].' gr/cm<sup>3</sup></td></tr><tr>';
}

if ($pedido["temperatura"] != "") {
	$resumo .= '<td>Temperatura:</td><td>'.$pedido["temperatura"].' °C</td></tr><tr>';
}
$resumo .= '<td>Classificação de uso:</td><td>';

$classif_uso = mysqli_query($conn,"SELECT * FROM classif_uso");
while($row = mysqli_fetch_array($classif_uso)) {
$valor = $row['fator'] . "_" . $row['id'];
  if ($valor == $pedido["class_uso"]) {
  	$resumo .= utf8_encode($row['classif']);
  }
}

$resumo .= '</td>
</tr>';
if ($pedido["transporte"] != "Selecione") {
$resumo .= '<tr>
<td>Transporte:</td><td>'.utf8_encode($pedido["transporte"]).'</td>
</tr>';
}

$resumo .= '<tr>';

if ($pedido["dem_mensal"] != "") {
	$resumo .= '<td>Demanda mensal:</td><td>'.$pedido["dem_mensal"].'</td></tr><tr>';
}

if ($pedido["dem_anual"] != "") {
	$resumo .= '<td>Demanda anual:</td><td>'.$pedido["dem_anual"].'</td></tr><tr>';
}
$resumo .= '<td>Carga nominal:</td><td>'.$pedido["carga_nominal"].' kg</td>
</tr>';

if ($pedido["armazenagem"] != "Selecione") {
$resumo .= '<tr>
<td>Armazenagem:</td><td>'.utf8_encode($pedido["armazenagem"]).'</td>
</tr>';
}

$resumo .= '<tr>
<td><b>Modelo do corpo:<b></td><td>';

if ($pedido["corpo"] == "gota") { $resumo .= "Gota (Single Loop)"; }
elseif ($pedido["corpo"] == "qowa") { $resumo .= "Plano"; }
elseif ($pedido["corpo"] == "cowa") { $resumo .= "Tubular"; }
elseif ($pedido["corpo"] == "qowac") { $resumo .= "Painel U"; }
elseif ($pedido["corpo"] == "qowacf") { $resumo .= "Painel U com forro"; }
elseif ($pedido["corpo"] == "qowad4") { $resumo .= "Travado com costuras nos cantos"; }
elseif ($pedido["corpo"] == "qowad8") { $resumo .= "Travado em gomos"; }
elseif ($pedido["corpo"] == "cowad") { $resumo .= "Travado tubular"; }
elseif ($pedido["corpo"] == "qowa2") { $resumo .= "Plano duplo"; }
elseif ($pedido["corpo"] == "cowa2") { $resumo .= "Tubular duplo"; }
elseif ($pedido["corpo"] == "qowaf") { $resumo .= "Plano com forro"; }
elseif ($pedido["corpo"] == "qowadlf") { $resumo .= "Plano com forro travado"; }
elseif ($pedido["corpo"] == "cowaf") { $resumo .= "Tubular com forro"; }
elseif ($pedido["corpo"] == "qowao") { $resumo .= "Plano condutivo"; }
elseif ($pedido["corpo"] == "qowafi") { $resumo .= "Plano com forro VCI"; }
elseif ($pedido["corpo"] == "cowafi") { $resumo .= "Tubular com forro VCI"; }
elseif ($pedido["corpo"] == "qowam") { $resumo .= "Plano antimicrobiano"; }
elseif ($pedido["corpo"] == "qowaa") { $resumo .= "Plano arejado"; }
elseif ($pedido["corpo"] == "qowat") { $resumo .= "Plano térmico"; }
elseif ($pedido["corpo"] == "qhe") { $resumo .= "Plano com fechamento especial"; }
elseif ($pedido["corpo"] == "qhe_ref") { $resumo .= "Plano QHE reforçado com fita"; }
elseif ($pedido["corpo"] == "rof") { $resumo .= "Porta ensacado simples"; }
elseif ($pedido["corpo"] == "qms") { $resumo .= "Plano com duas alças"; }
elseif ($pedido["corpo"] == "cms") { $resumo .= "Tubular com duas alças"; }
elseif ($pedido["corpo"] == "outros") { $resumo .= "Outro"; }

$resumo .= '</td>
</tr><tr>
<td>Dimensões (LxCxA):</td><td>'.$pedido["base1"].' x '.$pedido["base2"].' x '.$pedido["altura"].' cm</td>
</tr><tr>
<td>Laminado:</td><td>';

if ($pedido["plastificado"] == "1") { $resumo .= "SIM"; } else { $resumo .= "NÃO"; }

$resumo .= '</td>
</tr><tr>
<td>Cor do tecido:</td><td>';

if ($pedido["corpo_cor"] == "branco") { $resumo .= "Branco (natural)"; }
elseif ($pedido["corpo_cor"] == "cinza") { $resumo .= "Cinza"; }
elseif ($pedido["corpo_cor"] == "azul") { $resumo .= "Azul Carijó"; }
elseif ($pedido["corpo_cor"] == "marrom") { $resumo .= "Marrom Carijó"; }
elseif ($pedido["corpo_cor"] == "preto") { $resumo .= "Preto Carijó"; }
elseif ($pedido["corpo_cor"] == "preto2") { $resumo .= "Preto"; }
elseif ($pedido["corpo_cor"] == "verde") { $resumo .= "Verde Carijó"; }
elseif ($pedido["corpo_cor"] == "outro") { $resumo .= "Outro (".utf8_encode($pedido["corpo_cor_outro"]).")"; }

$resumo .= '</td>
</tr><tr>
<td>Cor da laminação:</td><td>';

if ($pedido["lamin_cor"] == "padrao") { $resumo .= "Padrão"; }
elseif ($pedido["lamin_cor"] == "branco") { $resumo .= "Branco"; }
elseif ($pedido["lamin_cor"] == "preto") { $resumo .= "Preto"; }
elseif ($pedido["lamin_cor"] == "outro") { $resumo .= "Outro (".utf8_encode($pedido["lamin_cor_outro"]).")"; }

$resumo .= '</td>
</tr>';

if ($pedido["fio_ved_travas"] == "1") {
$resumo .= '<tr>
<td>Fio vedante nas travas:</td><td>SIM</td>
</tr>';
}


$resumo .= '<tr>
<td>Gramatura do corpo:
</td>
<td>';

$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `gramat_real` LIKE '".$pedido["gramat_corpo"]."' AND `status` = '1';");
$gramat_row = mysqli_fetch_array($gramaturas_query);
$resumo .= $gramat_row['gramat_desc'];
/*
if ($pedido["gramat_corpo"] == "130") { $resumo .= '130'; }
elseif ($pedido["gramat_corpo"] == "147") { $resumo .= '130 + 17'; }
elseif ($pedido["gramat_corpo"] == "145") { $resumo .= '145'; }
elseif ($pedido["gramat_corpo"] == "170") { $resumo .= '145 + 25'; }
elseif ($pedido["gramat_corpo"] == "160") { $resumo .= '160'; }
elseif ($pedido["gramat_corpo"] == "185") { $resumo .= '160 + 25'; }
elseif ($pedido["gramat_corpo"] == "191") { $resumo .= '160 + 30'; }
elseif ($pedido["gramat_corpo"] == "190") { $resumo .= '190'; }
elseif ($pedido["gramat_corpo"] == "215") { $resumo .= '190 + 25'; }
elseif ($pedido["gramat_corpo"] == "221") { $resumo .= '190 + 30'; }
elseif ($pedido["gramat_corpo"] == "220") { $resumo .= '220'; }
elseif ($pedido["gramat_corpo"] == "245") { $resumo .= '220 + 25'; }
elseif ($pedido["gramat_corpo"] == "250") { $resumo .= '220 + 30'; }
elseif ($pedido["gramat_corpo"] == "240") { $resumo .= '240'; }
elseif ($pedido["gramat_corpo"] == "265") { $resumo .= '240 + 25'; }
elseif ($pedido["gramat_corpo"] == "270") { $resumo .= '270'; }
elseif ($pedido["gramat_corpo"] == "295") { $resumo .= '270 + 25'; }
*/
$resumo .= ' g/m²
</td>
</tr>';


$resumo .= '<tr><td>Costura a fio no topo:</td><td>';
if($pedido["cost_fio_topo"] == "1") { $resumo .= 'SIM'; } else { $resumo .= 'NÃO'; }
$resumo .= '</td></tr>';

$resumo .= '<tr><td>Costura a fio na base:</td><td>';
if($pedido["cost_fio_base"] == "1") { $resumo .= 'SIM'; } else { $resumo .= 'NÃO'; }
$resumo .= '</td></tr>';


$resumo .= '<tr>
<td><b>Sistema de enchimento:</b></td><td>';

if ($pedido["carga"] == "vazio") { $resumo .= "Sem sistema de enchimento"; }
elseif ($pedido["carga"] == "c_saia") { $resumo .= "Saia"; }
elseif ($pedido["carga"] == "c_afunilada") { $resumo .= "Saia afunilada"; }
elseif ($pedido["carga"] == "c_simples") { $resumo .= "Válvula simples"; }
elseif ($pedido["carga"] == "c_simples_afunilada") { $resumo .= "Válvula simples com tampa afunilada"; }
elseif ($pedido["carga"] == "c_prot_mochila") { $resumo .= "Válvula com proteção tipo flap"; }
elseif ($pedido["carga"] == "c_tipo_as") { $resumo .= "Tampa tipo porta-ensacado"; }

$resumo .= '</td>
</tr><tr>';

if ($pedido["carga"] != "vazio") {
	$resumo .= '<td>Quadrado:</td><td>';
	if ($pedido["c_quadrado"] == "c_quadrado") { $resumo .= "SIM"; } else { $resumo .= "NÃO"; }
	$resumo .= '</td></tr><tr>';
	if ($pedido["carga1"] != "") {
	$resumo .= '<td>Diâmetro/lado:</td><td>'.$pedido["carga1"].' cm</td></tr><tr>';
}

if ($pedido["carga2"] != "") {
	$resumo .= '<td>Altura:</td><td>'.$pedido["carga2"].' cm</td></tr><tr>';
}

}

$resumo .= '<td><b>Sistema de esvaziamento:</b></td><td>';

if ($pedido["descarga"] == "vazio") { $resumo .= "Sem sistema de esvaziamento"; }
elseif ($pedido["descarga"] == "d_simples") { $resumo .= "Válvula simples"; }
elseif ($pedido["descarga"] == "d_prot_presilha") { $resumo .= "Válvula com proteção tipo \"X\""; }
elseif ($pedido["descarga"] == "d_prot_mochila") { $resumo .= "Válvula com proteção tipo flap"; }
elseif ($pedido["descarga"] == "d_afunilado") { $resumo .= "Afunilado"; }
elseif ($pedido["descarga"] == "d_total") { $resumo .= "Abertura total simples"; }

$resumo .= '</td></tr>';

if ($pedido["descarga"] != "vazio") {
	$resumo .= '<tr><td>Redondo:</td><td>';
	if ($pedido["d_redondo"] == "d_redondo") { $resumo .= "SIM"; } else { $resumo .= "NÃO"; }
		$resumo .= '</td></tr>';
	if ($pedido["descarga1"] != "") {
		$resumo .= '<tr><td>Diâmetro/lado:</td><td>'.$pedido["descarga1"].' cm</td></tr>';
	}
	if ($pedido["descarga2"] != "") {
		$resumo .= '<tr><td>Altura:</td><td>'.$pedido["descarga2"].' cm</td></tr>';
	}
}

if ($pedido["alca"] == "vazio" && $pedido["corpo"] == "gota") {
$resumo .= '<tr><td><b>Alças:</b></td><td><i>Single Loop</i></td></tr>
	<tr><td>Altura do vão livre:</td><td>'.$pedido["alca_altura"].' cm</td></tr>
	<tr><td>Largura do slit:</td><td>'.$pedido["alca_fix_altura"].' cm</td>';
} elseif ($pedido["alca"] != "vazio") {

$resumo .= '<tr><td><b>Alças:</b></td><td>';

$qtde_alcas = mysqli_query($conn,"SELECT * FROM qtde_alcas");
while($row = mysqli_fetch_array($qtde_alcas)) {
  if ($pedido["alca"] == $row['qtde'] . "_" . $row['cod']) { $resumo .= utf8_encode($row['desc']); }
}

$resumo .= '</td>
</tr><tr>
<td>Cor:</td><td>';

if ($pedido["alca_cor"] == "branco") { $resumo .= "Branco (natural)"; }
elseif ($pedido["alca_cor"] == "amarela") { $resumo .= "Amarela"; }
elseif ($pedido["alca_cor"] == "azul_total") { $resumo .= "Azul"; }
elseif ($pedido["alca_cor"] == "cinza") { $resumo .= "Cinza"; }
elseif ($pedido["alca_cor"] == "marrom_total") { $resumo .= "Marrom"; }
elseif ($pedido["alca_cor"] == "preta_total") { $resumo .= "Preta"; }
elseif ($pedido["alca_cor"] == "verde_total") { $resumo .= "Verde"; }
elseif ($pedido["alca_cor"] == "vermelha") { $resumo .= "Vermelha"; }
elseif ($pedido["alca_cor"] == "amarelo_carijo") { $resumo .= "Amarelo Carijó"; }
elseif ($pedido["alca_cor"] == "azul") { $resumo .= "Azul Carijó"; }
elseif ($pedido["alca_cor"] == "cinza_carijo") { $resumo .= "Cinza Carijó"; }
elseif ($pedido["alca_cor"] == "marrom") { $resumo .= "Marrom Carijó"; }
elseif ($pedido["alca_cor"] == "preto") { $resumo .= "Preto Carijó"; }
elseif ($pedido["alca_cor"] == "preto2") { $resumo .= "Preto"; }
elseif ($pedido["alca_cor"] == "verde") { $resumo .= "Verde Carijó"; }
elseif ($pedido["alca_cor"] == "vermelho_carijo") { $resumo .= "Vermelho Carijó"; }
elseif ($pedido["alca_cor"] == "outra") { $resumo .= "Outra"; }


$resumo .= '</td>
</tr><tr>
<td>Material:</td><td>';

if ($pedido["alca_material"] == "fita") { $resumo .= "Fita"; }
elseif ($pedido["alca_material"] == "tecido") { $resumo .= "Tecido"; }

$resumo .= '</td>
</tr><tr>
<td>Altura do vão livre:</td><td>'.$pedido["alca_altura"].' cm</td>
</tr><tr>
<td>Altura de fixação:</td><td>'.$pedido["alca_fix_altura"].' cm</td>
</tr>';

$resumo .= '<tr>
<td>Tipo de fixação:</td><td>';
if($pedido["alca_fixacao"] == 0) {
	$resumo .= 'INTERNA';
} elseif($pedido["alca_fixacao"] == 1) {
	$resumo .= 'EXTERNA';
}
$resumo .= '</td>
</tr>';

if ($pedido["reforco_vao_livre"] == "1") {
$resumo .= '<tr>
<td>Reforço do vão livre:</td><td>SIM</td>
</tr>';
}

if ($pedido["reforco_fixacao"] == "1") {
$resumo .= '<tr>
<td>Reforço de fixação:</td><td>SIM</td>
</tr>';
}

if ($pedido["alca_dupla"] == "1") {
$resumo .= '<tr>
<td>Alça dupla:</td><td>SIM</td>
</tr>';
}

$resumo .= '<tr>
<td>Capacidade individual de cada alça:</td><td>'.$pedido["alca_capac"].' Kg</td>
</tr>';

} else {

$resumo .= '<tr><td><b>Alças:</b></td><td>Sem alças</td></tr>';

}



if ($pedido["liner"] != "vazio") {

$query_liner = mysqli_query($conn,"SELECT * FROM pedidos_liner WHERE `pedido` = '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` DESC"); // ORDER BY `revisao` DESC");
$det_liner = mysqli_fetch_array($query_liner);

$resumo .= '<tr>
<td><b>Liner:</b></td><td>';
if ($pedido["liner"] == "liner_padrao") { $resumo .= "Liner padrão"; }
elseif ($pedido["liner"] == "liner_gota") { $resumo .= "Liner padrão Bag Gota"; }
elseif ($pedido["liner"] == "liner_fertil") { $resumo .= "Liner padrão Fertilizante"; }
elseif ($pedido["liner"] == "liner_afunilado") { $resumo .= "Liner afunilado"; }
elseif ($pedido["liner"] == "liner_sup_inf") { $resumo .= "Liner com válvula superior e inferior"; }
elseif ($pedido["liner"] == "liner_total_inf") { $resumo .= "Liner com abertura total e válvula inferior"; }
elseif ($pedido["liner"] == "liner_sup_fechado") { $resumo .= "Liner com válvula superior e fechado no fundo"; }
elseif ($pedido["liner"] == "liner_externo") { $resumo .= "Liner externo"; }

$resumo .= '</td>
</tr><tr><td>Tipo:</td><td>';

if ($pedido["tipo_liner"] == "vazio") { $resumo .= "Não especificado"; }
elseif ($pedido["tipo_liner"] == "liner_transp") { $resumo .= "Virgem"; }
elseif ($pedido["tipo_liner"] == "liner_canela") { $resumo .= "Canela"; }
elseif ($pedido["tipo_liner"] == "liner_cristal") { $resumo .= "Cristal"; }

$resumo .= '</td>
</tr>';

if (! $det_liner) {
	$resumo .= '<tr>
	<td>Espessura do liner:</td><td>'. str_replace(".",",",$pedido["liner_espessura"]) .' micra</td>
	</tr>';
} else {
	$resumo .= '<tr>
	<td>Largura do liner:</td><td>'.str_replace(".",",",(float)number_format($det_liner["larg_liner"], 2)).' cm</td>
	</tr><tr>
	<td>Comprimento do liner:</td><td>';
	  if($comprimento_liner != "") { $resumo .= $comprimento_liner*100; }
	  else { $resumo .= str_replace(".",",",(float)number_format($det_liner["comp_liner"], 2)); }
//	.str_replace(".",",",(float)number_format($det_liner["comp_liner"], 2)).
	$resumo .=' cm</td>
	</tr><tr>
	<td>Espessura do liner:</td><td>'.str_replace(".",",",(float)number_format($det_liner["espess_liner"], 2)).' micra</td>
	</tr>';
}

$resumo .= '<tr>
<td>Tipo de fixação:</td><td>';

if ($pedido["fix_liner"] == "sem_fixacao") { $resumo .= "Sem fixação"; }
elseif ($pedido["fix_liner"] == "colado") { $resumo .= "Colado"; }
elseif ($pedido["fix_liner"] == "costurado") { $resumo .= "Costurado"; }
elseif ($pedido["fix_liner"] == "colado_costurado") { $resumo .= "Colado e costurado"; }
elseif ($pedido["fix_liner"] == "liner_externo") { $resumo .= "Liner externo"; }

$resumo .= '</td>';

}

$resumo .= '</tr>';

$resumo .= '<tr>
<td><b>Impressão:</b></td><td>';

if ($pedido["no_cores"] == "0") { $resumo .= "Sem impressão"; }
elseif ($pedido["no_cores"] == "1") { $resumo .= "1 cor"; }
elseif ($pedido["no_cores"] == "2") { $resumo .= "2 cores"; }
elseif ($pedido["no_cores"] == "3") { $resumo .= "3 cores (limite ideal)"; }
elseif ($pedido["no_cores"] == "4") { $resumo .= "4 cores (consultar)"; }

$resumo .= '</td></tr>';

if ($pedido["no_cores"] != "0") {
	$resumo .= '<tr><td>Controle de utilização:</td><td>';
	if ($pedido["imp_controle_viag"] == "1") { $resumo .= "SIM"; } else { $resumo .= "NÃO"; }
		$resumo .= '</td></tr><tr><td>Número sequencial:</td><td>';
	if ($pedido["imp_num_seq"] == "1") { $resumo .= "SIM"; } else { $resumo .= "NÃO"; }
		$resumo .= '</td></tr><tr><td>Faces selecionadas:</td><td>'.strtoupper($pedido["sel_faces"]).'</td></tr>';
}

if ($pedido["porta_etq1"] == "1" || $pedido["porta_etq2"] == "1" || $pedido["porta_etq3"] == "1" || $pedido["porta_etq4"] == "1") {
	$resumo .= '<tr>
	<td colspan="2">Porta etiqueta:</td>
	</td></tr>';
}

if ($pedido["porta_etq1"] == "1") {
$resumo .= '<tr><td><b>Face A</b></td><td>Posição: ';
if ($pedido["pos_porta_etq1"] == "") { $resumo .= "Não selecionado"; }
elseif ($pedido["pos_porta_etq1"] == "topo_meio") { $resumo .= "Topo centralizado (padrão)"; }
elseif ($pedido["pos_porta_etq1"] == "topo_direita") { $resumo .= "Topo na direita"; }
elseif ($pedido["pos_porta_etq1"] == "topo_esquerda") { $resumo .= "Topo na esquerda (45 cm do topo e 8 cm da lateral)"; }
elseif ($pedido["pos_porta_etq1"] == "centro") { $resumo .= "No centro"; }
elseif ($pedido["pos_porta_etq1"] == "cost_vert") { $resumo .= "Costurado na vertical"; }
elseif ($pedido["pos_porta_etq1"] == "personalizado") { $resumo .= "Personalizado (especificar nas obs.)"; }
$resumo .= '</td>
</tr><tr>
<td>Modelo:</td><td>';
if ($pedido["mod_porta_etq1"] == "") { $resumo .= "Não selecionado"; }
elseif ($pedido["mod_porta_etq1"] == "folha") { $resumo .= "Tipo folha (27 x 40 x 0,20)"; }
elseif ($pedido["mod_porta_etq1"] == "folha2") { $resumo .= "Tipo folha (27 x 20 x 0,20)"; }
elseif ($pedido["mod_porta_etq1"] == "fronha") { $resumo .= "Tipo fronha (27 x 40 x 0,15)"; }
elseif ($pedido["mod_porta_etq1"] == "aba_adesiva") { $resumo .= "Com aba adesiva (27 x 45 x 0,12)"; }
elseif ($pedido["mod_porta_etq1"] == "ziplock") { $resumo .= "Porta documento (20 x 25) - ziplock"; }
elseif ($pedido["mod_porta_etq1"] == "aberto_tras") { $resumo .= "Com abertura posicionada para a parte de trás (23x40cm)"; }
$resumo .= '</td>
</tr>';
}
if ($pedido["porta_etq2"] == "1") {
$resumo .= '<tr><td><b>Face B</b></td><td>Posição: ';
if ($pedido["pos_porta_etq2"] == "") { $resumo .= "Não selecionado"; }
elseif ($pedido["pos_porta_etq2"] == "topo_meio") { $resumo .= "Topo centralizado (padrão)"; }
elseif ($pedido["pos_porta_etq2"] == "topo_direita") { $resumo .= "Topo na direita"; }
elseif ($pedido["pos_porta_etq2"] == "topo_esquerda") { $resumo .= "Topo na esquerda (45 cm do topo e 8 cm da lateral)"; }
elseif ($pedido["pos_porta_etq2"] == "centro") { $resumo .= "No centro"; }
elseif ($pedido["pos_porta_etq2"] == "cost_vert") { $resumo .= "Costurado na vertical"; }
elseif ($pedido["pos_porta_etq2"] == "personalizado") { $resumo .= "Personalizado (especificar nas obs.)"; }
$resumo .= '</td>
</tr><tr>
<td>Modelo:</td><td>';
if ($pedido["mod_porta_etq2"] == "") { $resumo .= "Não selecionado"; }
elseif ($pedido["mod_porta_etq2"] == "folha") { $resumo .= "Tipo folha (27 x 40 x 0,20)"; }
elseif ($pedido["mod_porta_etq2"] == "folha2") { $resumo .= "Tipo folha (27 x 20 x 0,20)"; }
elseif ($pedido["mod_porta_etq2"] == "fronha") { $resumo .= "Tipo fronha (27 x 40 x 0,15)"; }
elseif ($pedido["mod_porta_etq2"] == "aba_adesiva") { $resumo .= "Com aba adesiva (27 x 45 x 0,12)"; }
elseif ($pedido["mod_porta_etq2"] == "ziplock") { $resumo .= "Porta documento (20 x 25) - ziplock"; }
elseif ($pedido["mod_porta_etq2"] == "aberto_tras") { $resumo .= "Com abertura posicionada para a parte de trás (23x40cm)"; }
$resumo .= '</td>
</tr>';
}
if ($pedido["porta_etq3"] == "1") {
$resumo .= '<tr><td><b>Face C</b></td><td>Posição: ';
if ($pedido["pos_porta_etq3"] == "") { $resumo .= "Não selecionado"; }
elseif ($pedido["pos_porta_etq3"] == "topo_meio") { $resumo .= "Topo centralizado (padrão)"; }
elseif ($pedido["pos_porta_etq3"] == "topo_direita") { $resumo .= "Topo na direita"; }
elseif ($pedido["pos_porta_etq3"] == "topo_esquerda") { $resumo .= "Topo na esquerda (45 cm do topo e 8 cm da lateral)"; }
elseif ($pedido["pos_porta_etq3"] == "centro") { $resumo .= "No centro"; }
elseif ($pedido["pos_porta_etq3"] == "cost_vert") { $resumo .= "Costurado na vertical"; }
elseif ($pedido["pos_porta_etq3"] == "personalizado") { $resumo .= "Personalizado (especificar nas obs.)"; }
$resumo .= '</td>
</tr><tr>
<td>Modelo:</td><td>';
if ($pedido["mod_porta_etq3"] == "") { $resumo .= "Não selecionado"; }
elseif ($pedido["mod_porta_etq3"] == "folha") { $resumo .= "Tipo folha (27 x 40 x 0,20)"; }
elseif ($pedido["mod_porta_etq3"] == "folha2") { $resumo .= "Tipo folha (27 x 20 x 0,20)"; }
elseif ($pedido["mod_porta_etq3"] == "fronha") { $resumo .= "Tipo fronha (27 x 40 x 0,15)"; }
elseif ($pedido["mod_porta_etq3"] == "aba_adesiva") { $resumo .= "Com aba adesiva (27 x 45 x 0,12)"; }
elseif ($pedido["mod_porta_etq3"] == "ziplock") { $resumo .= "Porta documento (20 x 25) - ziplock"; }
elseif ($pedido["mod_porta_etq3"] == "aberto_tras") { $resumo .= "Com abertura posicionada para a parte de trás (23x40cm)"; }
$resumo .= '</td>
</tr>';
}
if ($pedido["porta_etq4"] == "1") {
$resumo .= '<tr><td><b>Face D</b></td><td>Posição: ';
if ($pedido["pos_porta_etq4"] == "") { $resumo .= "Não selecionado"; }
elseif ($pedido["pos_porta_etq4"] == "topo_meio") { $resumo .= "Topo centralizado (padrão)"; }
elseif ($pedido["pos_porta_etq4"] == "topo_direita") { $resumo .= "Topo na direita"; }
elseif ($pedido["pos_porta_etq4"] == "topo_esquerda") { $resumo .= "Topo na esquerda (45 cm do topo e 8 cm da lateral)"; }
elseif ($pedido["pos_porta_etq4"] == "centro") { $resumo .= "No centro"; }
elseif ($pedido["pos_porta_etq4"] == "cost_vert") { $resumo .= "Costurado na vertical"; }
elseif ($pedido["pos_porta_etq4"] == "personalizado") { $resumo .= "Personalizado (especificar nas obs.)"; }
$resumo .= '</td>
</tr><tr>
<td>Modelo:</td><td>';
if ($pedido["mod_porta_etq4"] == "") { $resumo .= "Não selecionado"; }
elseif ($pedido["mod_porta_etq4"] == "folha") { $resumo .= "Tipo folha (27 x 40 x 0,20)"; }
elseif ($pedido["mod_porta_etq4"] == "folha2") { $resumo .= "Tipo folha (27 x 20 x 0,20)"; }
elseif ($pedido["mod_porta_etq4"] == "fronha") { $resumo .= "Tipo fronha (27 x 40 x 0,15)"; }
elseif ($pedido["mod_porta_etq4"] == "aba_adesiva") { $resumo .= "Com aba adesiva (27 x 45 x 0,12)"; }
elseif ($pedido["mod_porta_etq4"] == "ziplock") { $resumo .= "Porta documento (20 x 25) - ziplock"; }
elseif ($pedido["mod_porta_etq4"] == "aberto_tras") { $resumo .= "Com abertura posicionada para a parte de trás (23x40cm)"; }
$resumo .= '</td>
</tr>';
}
$resumo .= '<tr><td><b>Embalagem:</b></td><td>';
if ($pedido["fardo"] == "vazio") { $resumo .= "Não selecionado"; }
elseif ($pedido["fardo"] == "10") { $resumo .= "Fardo de 10 peças"; }
elseif ($pedido["fardo"] == "15") { $resumo .= "Fardo de 15 peças"; }
elseif ($pedido["fardo"] == "20") { $resumo .= "Fardo de 20 peças"; }
elseif ($pedido["fardo"] == "25") { $resumo .= "Fardo de 25 peças"; }
$resumo .= '</td>
</tr><tr>
<td>Big Bag palletizado:</td><td>';
if ($pedido["palletizado"] == "vazio") { $resumo .= "NÃO"; }
elseif ($pedido["palletizado"] == "80") { $resumo .= "80"; }
elseif ($pedido["palletizado"] == "100") { $resumo .= "100"; }
elseif ($pedido["palletizado"] == "125") { $resumo .= "125"; }
elseif ($pedido["palletizado"] == "150") { $resumo .= "150"; }
elseif ($pedido["palletizado"] == "175") { $resumo .= "175"; }
elseif ($pedido["palletizado"] == "200") { $resumo .= "200"; }
elseif ($pedido["palletizado"] == "250") { $resumo .= "250"; }
$resumo .= '</td>
</tr>';

if ($pedido["velcro"] == "1") {
$resumo .= '<tr>
<td>Velcro (Macho/Femea):</td><td>SIM</td>
</tr>';
}

if ($pedido["cinta_trav"] == "1") {
$resumo .= '<tr>
<td>Cinta de travamento:</td><td>SIM</td>
</tr>';
}

if ($pedido["gravata"] == "1") {
$resumo .= '<tr>
<td>Gravata de travamento:</td><td>'.number_format($pedido["med_gravata"],0,",",".").' cm</td>
</tr>';
}

if ($pedido["sapata"] == "1") {
$resumo .= '<tr>
<td>Pack Less:</td><td>SIM</td>
</tr>';
}

if ($pedido["flap"] == "1") {
$resumo .= '<tr>
<td>Tampa para fundo falso:</td><td>SIM</td>
</tr>';
}

$resumo .= '</table>
</td><td valign="top" width="50%" style="padding-left:10px;">';

$resumo .= '<b>Desenho ilustrativo</b><br>';

//.$desenho;

//if(sprintf('%05d', $row_pedidos["pedido"]) == "01840" || sprintf('%05d', $row_pedidos["pedido"]) == "01831" || sprintf('%05d', $row_pedidos["pedido"]) == "01141" || sprintf('%05d', $row_pedidos["pedido"]) == "00249") {
//if(sprintf('%05d', $row_pedidos["pedido"]) == "02026" || sprintf('%05d', $row_pedidos["pedido"]) == "01840" || sprintf('%05d', $row_pedidos["pedido"]) == "01831" || sprintf('%05d', $row_pedidos["pedido"]) == "01141" || sprintf('%05d', $row_pedidos["pedido"]) == "00249") {
if(in_array(sprintf('%05d', $row_pedidos["pedido"]), $des_especiais, true)) {
	$resumo .= '<center><img src="images/desenhos/'.sprintf('%05d', $row_pedidos["pedido"]).'.png" border="0"></center>';
} else {
	$resumo .= $desenho;
}

//$query_impostos = mysqli_query($conn,"SELECT * FROM `pedidos_extra` WHERE `rev_valor` LIKE (SELECT MAX(`rev_valor`) FROM `pedidos_extra` WHERE `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."' "); //AND `pedido` LIKE '".$pedido["pedido"]."'
$query_impostos = mysqli_query($conn,"SELECT * FROM `pedidos_extra` WHERE `id` LIKE (SELECT MAX(`id`) FROM `pedidos_extra` WHERE `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` DESC"); //AND `pedido` LIKE '".$pedido["pedido"]."'
$row_impostos = mysqli_fetch_array($query_impostos);

$resumo .= '<center>';
if($pedido["cost_fio_topo"] == "1") {
	$resumo .= '<b>COSTURA A FIO NO TOPO</b><br>';
}
if($pedido["cost_fio_base"] == "1") {
	$resumo .= '<b>COSTURA A FIO NA BASE</b><br>';
}
if($row_impostos["class_prod"] == "a") {
	$resumo .= '<b>(A) ALTA PRODUTIVIDADE</b><br>';
} elseif($row_impostos["class_prod"] == "b") {
	$resumo .= '<b>(B) MÉDIA PRODUTIVIDADE</b><br>';
} elseif($row_impostos["class_prod"] == "c") {
	$resumo .= '<b>(C) BAIXA PRODUTIVIDADE</b><br>';
}
if($pedido["liner"] == "liner_fertil") {
	$resumo .= '<br><b>LINER SOLDADO NA BASE, NÃO DEVE<br>ULTRAPASSAR A PARTE INFERIOR DO BIG BAG.</b><br>';
}

$resumo .= '</center><br>';


$resumo .= '</td>
</tr>
</table>';

/*
$resumo .= '<table border="0" class="tab_orcamento"><tr>
<td><b>Descrição</b></td>
<td><b>Quantidade de material</b></td>
<td><b>Gramatura</b></td>
</tr>'.$det_pedido_menor.'</table>';
*/

if ($pedido["obs_cliente"] != "") {
$resumo .= '<table border="0" class="tab_orcamento">
<tr><td><b>Observações técnicas:</b> '.utf8_encode($pedido["obs_cliente"]).'</td></tr>
</table>';
}



$resumo .= '<br>
<div style="page-break-before: always;"></div>

<h1>Custo</h1><hr>

<br>
<table border="0" class="tab_orcamento">
<tr>
<td colspan="2" width="50%" style="border-right:0;">Número do orçamento</td>
<td colspan="2" width="50%" style="border-left:0;" align="right"><font size="5"><b>'.sprintf('%05d', $id_pedido).' / '.sprintf('%02d', $revisao).'</b></font></td>
</tr>
<tr>';

if ($pedido["referencia"] != "") {
	$resumo .= '<td colspan="2"><b>Cliente:</b> '.utf8_encode($pedido["nome_cliente"]).'</td>
	<td colspan="2"><b>Referência:</b> '.utf8_encode($pedido["referencia"]).'</td>';
} else {
	$resumo .= '<td colspan="4"><b>Cliente:</b> '.utf8_encode($pedido["nome_cliente"]).'</td>';
}

$resumo .= '</tr>
<tr>
<td><b>'.strtoupper($pedido["selecao"]).':</b> '.$pedido["cnpj_cpf"].'</td>
<td><b>Cidade:</b> '.utf8_encode($pedido["cidade_cliente"]).'</td>
<td><b>UF:</b> '.$pedido["uf_cliente"].'</td>';

if ($pedido["prazo"] != "0") {
	$resumo .= '<td><b>Prazo de pagamento:</b><br>'.$pedido["prazo"].' dias</td>';
} else {
	$resumo .= '<td><b>Prazo de pagamento:</b><br>À vista</td>';
}

$resumo .= '</tr>
<tr>
<td><b>Fornecedora:</b> ';

if ($pedido["fornecedora"] == "valor_sp") {
	$resumo .= "BSS/SP";
} elseif ($pedido["fornecedora"] == "valor_mg") {
	$resumo .= "BSS/MG";
} elseif ($pedido["fornecedora"] == "valor_ba") {
	$resumo .= "BSS/NE";
} elseif ($pedido["fornecedora"] == "valor_f1") {
	$resumo .= "BSS/FILIAL BA";
} elseif ($pedido["fornecedora"] == "valor_bonpar") {
	$resumo .= "BSS/PY";
}

$resumo .= '</td>
<td><b>Quantidade:</b> '.$pedido["qtde"].'</td>
<td><b>Frete tipo:</b> '.strtoupper($pedido["frete"]).'</td>
<td><b>Distância aproximada:</b><br>';

if ($pedido["distancia_aprox"] == "0") {
	$resumo .= "Frete FOB";
} elseif ($pedido["distancia_aprox"] == "1") {
	$resumo .= "Menos de 200 km";
} elseif ($pedido["distancia_aprox"] == "2") {
	$resumo .= "De 201 a 500 km";
} elseif ($pedido["distancia_aprox"] == "3") {
	$resumo .= "Acima de 501 km";
}

$resumo .= '</td>
</tr>';

$embarques = $pedido["embarques"];
$representante = $pedido["representante"];
$resumo .= '<tr>
<td colspan="2"><b>Embarques:</b> '.$embarques.'</td>
<td colspan="2"><b>Representante:</b> '.$representante.'</td>
</tr>';

$resumo .= '</table>

<table border="0" class="tab_orcamento"><tr>
<td><b>Descrição</b></td>
<td style="width: 80px;"><b>Valor do quilo</b></td>
<td style="width: 90px;"><b>Qde. de material</b></td>
<td style="width: 80px;"><b>Matéria-prima</b></td>
<td style="width: 80px;"><b>Peso</b></td>
<td style="width: 80px;" align="right"><b>Valor</b></td>
</tr>'.$detalhes_pedido.'<tr>
<td colspan="5"><b>SUB-TOTAL</b></td>
<td align="right" class="contabil"><b>R$ '.number_format((float)$subtotal, 2, ',', '.').'</b></td>
</tr>';

/*
if  ($pedido["porta_etq1"] == "1") { $peso_teorico += 0.016; }
if  ($pedido["porta_etq2"] == "1") { $peso_teorico += 0.016; }
if  ($pedido["porta_etq3"] == "1") { $peso_teorico += 0.016; }
if  ($pedido["porta_etq4"] == "1") { $peso_teorico += 0.016; }
*/

$peso_teorico += 0.003 + 0.08;


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


$resumo .= '<tr>
<td colspan="5">'.$desc_mat.'</td>
<td align="right" class="contabil">R$ '.number_format((float)$valor_mat_auxiliar, 2, ',', '.').'</td>
</tr>
<tr>
<td colspan="5">'.$desc_cif.'</td>
<td align="right" class="contabil">R$ '.number_format((float)$valor_cif, 2, ',', '.').'</td>
</tr>
<tr>
<td colspan="5">'.$desc_mo.'</td>
<td align="right" class="contabil">R$ '.number_format((float)$valor_mao_obra, 2, ',', '.').'</td>
</tr>
<tr>
<td colspan="5"><b>CUSTO DO BAG</b></td>
<td align="right" class="contabil"><b>R$ '.number_format((float)$total, 2, ',', '.').'</b></td>
</tr>';

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
$valor_dolar = $row_impostos["valor_dolar"];
$cotacao_dolar = number_format((float)$row_impostos["cambio"], 4, ',', '.');
$cambio_dia = date("d/m/Y", strtotime($row_impostos["cambio_data"]));

$resumo .= '<tr><td>ICMS';
if ($pedido["fornecedora"] == "valor_sp" && $pedido["uf_cliente"] == "SP") { $resumo .= ' - Crédito outorgado'; }
$resumo .= '</td>
<td colspan="2" class="contabil">'.number_format((float)$valor_icms, 2, ',', '.').'%</td>
<td colspan="2">PIS</td>
<td class="contabil">'.number_format((float)$valor_pis, 2, ',', '.').'%</td>
</tr>
<tr>
<td>COFINS</td>
<td colspan="2" class="contabil">'.number_format((float)$valor_cofins, 2, ',', '.').'%</td>
<td colspan="2">IR</td>
<td class="contabil">'.number_format((float)$valor_ir, 2, ',', '.').'%</td>
</tr>
<tr>
<td>CSLL</td>
<td colspan="2" class="contabil">'.number_format((float)$valor_csll, 2, ',', '.').'%</td>
<td colspan="2">INSS (Folha Pagto.)</td>
<td class="contabil">'.number_format((float)$valor_inss, 2, ',', '.').'%</td>
</tr>
<tr>
<td>PERDA</td>
<td colspan="2" class="contabil">'.number_format((float)$valor_perda, 2, ',', '.').'%</td>
<td colspan="2">FRETE</td>
<td class="contabil">'.number_format((float)$valor_frete, 2, ',', '.').'%</td>
</tr>
<tr>
<td>COMISSÃO</td>
<td colspan="2" class="contabil">'.number_format((float)$valor_comissao, 2, ',', '.').'%</td>
<td colspan="2">ADM. COMERCIAL</td>
<td class="contabil">'.number_format((float)$valor_adm_comercial, 2, ',', '.').'%</td>
</tr>
<tr>
<td>MARGEM</td>
<td colspan="2" class="contabil">'.number_format((float)$valor_margem, 2, ',', '.').'%</td>
<td colspan="2">CUSTO FINANCEIRO</td>
<td class="contabil">'.number_format((float)$valor_cfinanceiro, 2, ',', '.').'%</td>
</tr>
<tr>
<td colspan="3"></td>
<td colspan="2"><b>TOTAL DE IMPOSTOS</b></td>
<td align="right" class="contabil"><b>'.number_format((float)$valor_imposto, 2, ',', '.').'%</b></td>
</tr>';

//- IR - CSLL - INSS

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
$resumo .= '<tr>
<td colspan="5"><b>VALOR COM IMPOSTOS</b></td>
<td align="right" class="contabil"><b>R$ '.number_format((float)$valor_final_venda, 2, ',', '.').'</b></td>
</tr>
<tr>
<td colspan="5"><b>VALOR SEM IMPOSTOS</b> (sem ICMS - PIS - COFINS)</td>
<td align="right" class="contabil"><b>R$ '.number_format((float)$valor_semimposto, 2, ',', '.').'</b></td>
</tr>
<tr>
<td colspan="5"><b>VALOR NET</b> (sem ICMS - PIS - COFINS - FRETE)</td>
<td align="right" class="contabil"><b>R$ '.number_format((float)$valor_net, 2, ',', '.').'</b></td>
</tr>
<tr>
<td colspan="5"><b>VALOR SEM FRETE / FOB</b></td>
<td align="right" class="contabil"><b>R$ '.number_format((float)$valor_semfrete, 2, ',', '.').'</b></td>
</tr>';

if ($valor_dolar != "0.00") {
$resumo .= '<tr>
<td colspan="3"><b>VALOR EM DÓLAR</b> (valor com impostos convertido)</td>
<td colspan="2"><small><small>Câmbio: R$ '.$cotacao_dolar.'<br>(Em '.$cambio_dia.'.)</small></small></td>
<td align="right" class="contabil"><b>US$ '.number_format((float)$valor_dolar, 2, ',', '.').'</b></td>
</tr>';
}

if ($pedido["obs_comerciais"] != "") {
	$resumo .= '<tr><td colspan="6"><b>Observações comerciais:</b> '.utf8_encode($pedido["obs_comerciais"]).'</td></tr>';
}

$resumo .= '<tr>
<td><b>PESO TEÓRICO</b></td>
<td colspan="2" align="center">'.number_format($peso_teorico,2,',','.').' Kg</td>
<td colspan="2"><b>FATOR KG</b></td>
<td align="right">R$ '. number_format($valor_final_venda/$peso_teorico,2,',','.').'</td>
</tr>';


$resumo .= '</table>';

/*
$resumo .= '<div style="page-break-before: always;"></div>

<h1>Histórico do orçamento</h1><hr>
<br>
<table border="0" class="tab_orcamento">
<tr>
<td colspan="2" width="50%" style="border-right:0;">Número do orçamento</td>
<td colspan="2" width="50%" style="border-left:0;" align="right"><font size="5"><b>'.sprintf('%05d', $id_pedido).' / '.sprintf('%02d', $revisao).'</b></font></td>
</tr>
<tr>';

if ($pedido["referencia"] != "") {
	$resumo .= '<td colspan="2"><b>Cliente:</b> '.utf8_encode($pedido["nome_cliente"]).'</td>
	<td colspan="2"><b>Referência:</b> '.utf8_encode($pedido["referencia"]).'</td>';
} else {
	$resumo .= '<td colspan="4"><b>Cliente:</b> '.utf8_encode($pedido["nome_cliente"]).'</td>';
}

$resumo .= '</tr>
<tr>
<td><b>'.strtoupper($pedido["selecao"]).':</b> '.$pedido["cnpj_cpf"].'</td>
<td><b>Cidade:</b> '.utf8_encode($pedido["cidade_cliente"]).'</td>
<td><b>UF:</b> '.$pedido["uf_cliente"].'</td>';

if ($pedido["prazo"] != "0") {
	$resumo .= '<td><b>Prazo de pagamento:</b><br>'.$pedido["prazo"].' dias</td>';
} else {
	$resumo .= '<td><b>Prazo de pagamento:</b><br>À vista</td>';
}

$resumo .= '</tr>
<tr>
<td><b>Fornecedora:</b> ';

if ($pedido["fornecedora"] == "valor_sp") {
	$resumo .= "BSS/SP";
} elseif ($pedido["fornecedora"] == "valor_mg") {
	$resumo .= "BSS/MG";
} elseif ($pedido["fornecedora"] == "valor_ba") {
	$resumo .= "BSS/NE";
} elseif ($pedido["fornecedora"] == "valor_bonpar") {
	$resumo .= "BSS/PY";
}

$resumo .= '</td>
<td><b>Quantidade:</b> '.$pedido["qtde"].'</td>
<td><b>Frete tipo:</b> '.strtoupper($pedido["frete"]).'</td>
<td><b>Distância aproximada:</b><br>';

if ($pedido["distancia_aprox"] == "0") {
	$resumo .= "Frete FOB";
} elseif ($pedido["distancia_aprox"] == "1") {
	$resumo .= "Menos de 200 km";
} elseif ($pedido["distancia_aprox"] == "2") {
	$resumo .= "De 201 a 500 km";
} elseif ($pedido["distancia_aprox"] == "3") {
	$resumo .= "Acima de 501 km";
}

$resumo .= '</td>
</tr>';

$embarques = $pedido["embarques"];
$representante = $pedido["representante"];
$resumo .= '<tr>
<td colspan="2"><b>Embarques:</b> '.$embarques.'</td>
<td colspan="2"><b>Representante:</b> '.$representante.'</td>
</tr>';

$resumo .= '</table>
<table border="0" class="tab_orcamento"><tr>
<td><b>Histórico</b></td>
<td><b>Data</b></td>
</tr>';

$query_log_sistema = mysqli_query($conn,"SELECT * FROM `log_sistema` WHERE `pedido` LIKE '".(float)$id_pedido."' ORDER BY `id` DESC LIMIT 35"); // LIMIT 10
while ($log_sistema = mysqli_fetch_array($query_log_sistema)){

$phpdate = strtotime( $log_sistema["data"] );
$data = date( 'd/m/Y', $phpdate );
$hora = date( 'H:i:s', $phpdate );

$resumo .= '<tr>
	<td>'.$log_sistema["nome"].' - '.utf8_encode($log_sistema["desc"]).'</td>
	<td>'.$data.' - '.$hora.'</td>
</tr>';
}

$resumo .= '</table>';

$query_qualidade = mysqli_query($conn,"SELECT * FROM `pedidos_qualidade` WHERE `pedido` LIKE '".(float)$id_pedido."' ORDER BY `id` DESC LIMIT 0,1");
$row_qualidade = mysqli_fetch_array($query_qualidade);

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


$resumo .= '<br>
<h1>Aprovação</h1><hr><br>
'.$txt_aprovacao;
*/





//$resumo .= '<br><br><br>';
//echo $resumo;

// Carrega a classe DOMPdf
require_once("dompdf/dompdf_config.inc.php");


// Cria a instância
$dompdf = new DOMPDF();

// Carrega seu HTML
$dompdf->load_html($resumo);

$dompdf->set_paper("A4", "portrait");

// Renderiza
$dompdf->render();

// Grava
$pdf = $dompdf->output();
file_put_contents("pdf_resumo/".date("Y-m-d")."_resumo_".sprintf('%05d', $id_pedido)."-".sprintf('%02d', $revisao).".pdf", $pdf);


// Exibe
$dompdf->stream(
    "FICHA TÉCNICA ".(float)$id_pedido." - ".utf8_encode($pedido["nome_cliente"])." - ".str_replace(".",",",$row_pedidos['base1'])." x ".str_replace(".",",",$row_pedidos['base2'])." x ".str_replace(".",",",$row_pedidos['altura'])." cm - ".number_format((float)$row_pedidos['carga_nominal'], 0, ',','.')." kg.pdf",
//    "bonsucesso_resumo_".$id_pedido.".pdf", // Nome do arquivo de saída
    array(
        "Attachment" => false // Para download, altere para true
    )
);


?>