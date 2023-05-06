<?php

    //require("../orcamentos/common.php"); 
    require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 


$id_pedido = $_GET["pedido"];

if ($id_pedido == "") { die(); }

$caminho = "https://viajacerto.com.br/orcamentos/";

$query_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE `pedido` = '".$id_pedido."' ORDER BY `id` DESC"); // ORDER BY `revisao` DESC");
$pedido = mysqli_fetch_array($query_pedidos);

$i = 0;

$valor_final_venda = $pedido["valor_final"];

$query_detalhes = mysqli_query($conn,"SELECT * FROM `pedidos_det` WHERE `revisao_valor` LIKE (SELECT MAX(`revisao_valor`) FROM `pedidos_det` WHERE `pedido` LIKE '".(float)$id_pedido."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".(float)$id_pedido."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` ASC");


$query_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE `pedido` LIKE '".$pedido["pedido"]."' ORDER BY `id` DESC"); //ORDER BY `revisao` DESC");
$row_pedidos = mysqli_fetch_array($query_pedidos);


$desenho .= '<div id="desenho_bag" style="width:357px; height:564px; background:#FFFFFF;">'; //margin:auto;

if ($row_pedidos["segmento_cliente"] == "1") {
$desenho .= '<div style="width:180px; position:absolute; top:-5px; left:170px;">
<p align="center"><font color="#FF0000"><b>ALIMENTÍCIO<br>PADRÃO EXPORTAÇÃO</b></font></p></div>';
} elseif ($row_pedidos["segmento_cliente"] == "10") {
$desenho .= '<div style="width:180px; position:absolute; top:-5px; left:170px;">
<p align="center"><font color="#FF0000"><b>ALIMENTÍCIO</b></font></p></div>';
}
if ($row_pedidos["mercado"] == "ext") {
$desenho .= '<div style="width:250px; position:absolute; top:535px; left:50px;">
<p align="center"><font color="#FF0000"><b>BIG BAG PARA EXPORTAÇÃO</b></font></p></div>';
}

$desenho .= '<div id="box_desenho" style="position:relative; overflow: hidden; width:357px; height:564px;">';

$sel_faces = $row_pedidos["sel_faces"];

if (strpos($sel_faces,"d") !== false) {
$desenho .= '<div id="face_d" style="z-index:24;" class="desenho"><img src="'.$caminho.'images/desenhos/face_d.png" border="0"></div>';
}
if (strpos($sel_faces,"c") !== false) {
$desenho .= '<div id="face_c" style="z-index:23;" class="desenho"><img src="'.$caminho.'images/desenhos/face_c.png" border="0"></div>';
}
if (strpos($sel_faces,"b") !== false) {
$desenho .= '<div id="face_b" style="z-index:22;" class="desenho"><img src="'.$caminho.'images/desenhos/face_b.png" border="0"></div>';
}
if (strpos($sel_faces,"a") !== false) {
$desenho .= '<div id="face_a" style="z-index:21;" class="desenho"><img src="'.$caminho.'images/desenhos/face_a.png" border="0"></div>';
}
if ($row_pedidos["porta_etq4"] != false) {
$desenho .= '<div id="des_porta_etq4" style="z-index:20;" class="desenho"><img src="'.$caminho.'images/desenhos/4_'.$row_pedidos["pos_porta_etq4"].'.png" border="0"></div>';
}
if ($row_pedidos["porta_etq3"] != false) {
$desenho .= '<div id="des_porta_etq3" style="z-index:19;" class="desenho"><img src="'.$caminho.'images/desenhos/3_'.$row_pedidos["pos_porta_etq3"].'.png" border="0"></div>';
}
if ($row_pedidos["porta_etq2"] != false) {
$desenho .= '<div id="des_porta_etq2" style="z-index:18;" class="desenho"><img src="'.$caminho.'images/desenhos/2_'.$row_pedidos["pos_porta_etq2"].'.png" border="0"></div>';
}
if ($row_pedidos["porta_etq1"] != false) {
$desenho .= '<div id="des_porta_etq1" style="z-index:17;" class="desenho"><img src="'.$caminho.'images/desenhos/1_'.$row_pedidos["pos_porta_etq1"].'.png" border="0"></div>';
}
if ($row_pedidos["flap"] != false) {
$desenho .= '<div id="flap" style="z-index:4;" class="desenho"><img src="'.$caminho.'images/desenhos/flap.png" border="0"></div>';
}
if ($row_pedidos["unit"] != "0") {
$desenho .= '<div id="dim_unit" style="z-index: 15; top: 453px; left: 106px; display: block;" class="medidas">'.$row_pedidos["unit"].'</div>';
$desenho .= '<div id="unitizador" style="z-index:10;" class="desenho"><img src="'.$caminho.'images/desenhos/unitizador.png" border="0"></div>';
}
if ($row_pedidos["sapata"] != false) {
$desenho .= '<div id="sapata" style="z-index:15;" class="desenho"><img src="'.$caminho.'images/desenhos/sapata.png" border="0"></div>';
}
if ($row_pedidos["velcro"] != false) {
$desenho .= '<div id="velcro" style="z-index:16;" class="desenho"><img src="'.$caminho.'images/desenhos/velcro.png" border="0"></div>';
}
if ($row_pedidos["cinta_trav"] != false) {
$desenho .= '<div id="cinta" style="z-index:16;" class="desenho"><img src="'.$caminho.'images/desenhos/cinta.png" border="0"></div>';
}
if ($row_pedidos["gravata"] != false) {
$desenho .= '<div id="gravata" style="z-index:16;" class="desenho"><img src="'.$caminho.'images/desenhos/gravata.png" border="0"></div>';
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
	$desenho .= '<div id="dim_altura" style="z-index:9; top: 370px; left: 295px;" class="medidas">'.str_replace(".",",",$row_pedidos["altura"]).'</div>';
}
$desenho .= '<div id="dim_base2" style="z-index:8; top: 490px; left: 230px;" class="medidas">'.str_replace(".",",",$row_pedidos["base2"]).'</div>';
$desenho .= '<div id="dim_base1" style="z-index:7; top: 490px; left: 65px;" class="medidas">'.str_replace(".",",",$row_pedidos["base1"]).'</div>';

if ($row_pedidos["liner"] != "") {
$desenho .= '<div id="liner" style="z-index:6;" class="desenho"><img src="'.$caminho.'images/desenhos/'.$row_pedidos["liner"].'.png" border="0"></div>';
}
if ($row_pedidos["descarga"] != "") {
	if($row_pedidos["corpo"] == 'gota') {
		if($row_pedidos["descarga"] == 'vazio') {
			$desenho .= '<div id="descarga" style="z-index:5;" class="desenho"><img src="'.$caminho.'images/desenhos/vazio';
		} else {
			$desenho .= '<div id="descarga" style="z-index:5;" class="desenho"><img src="'.$caminho.'images/desenhos/tampa_gota.png" border="0"></div>';
			$desenho .= '<div id="descarga" style="z-index:5;" class="desenho"><img src="'.$caminho.'images/desenhos/gota_valvula';
		}
	} else {
		$desenho .= '<div id="descarga" style="z-index:5;" class="desenho"><img src="'.$caminho.'images/desenhos/'.$row_pedidos["descarga"].'';
	}
if ($row_pedidos["d_redondo"] != false) { 
$desenho .= '_r';
}
$desenho .= '.png" border="0"></div>';
}
if ($row_pedidos["carga"] != "") {
$desenho .= '<div id="carga" style="z-index:4;" class="desenho"><img src="'.$caminho.'images/desenhos/'.$row_pedidos["carga"].'';
if ($row_pedidos["c_quadrado"] != false) {
$desenho .= '_q';
}
if ($row_pedidos["carga"] == "c_simples" && $row_pedidos["carga2"] == "") {
$desenho .= '2';
}
$desenho .= '.png" border="0"></div>';
}
if ($row_pedidos["alca"] != "") {
$desenho .= '<div id="alca" style="z-index:3;" class="desenho"><img src="'.$caminho.'images/desenhos/'.$row_pedidos["alca"].'.png" border="0"></div>';
}
if ($row_pedidos["corpo"] != "") {
$desenho .= '<div id="corpo" style="z-index:2;" class="desenho"><img src="'.$caminho.'images/desenhos/'.$row_pedidos["corpo"].'.png" border="0"></div>';
}
if ($row_pedidos["corpo"] == "gota") {
	$desenho .= '<div id="medidas" style="z-index:0;" class="desenho"><img src="'.$caminho.'images/desenhos/gota_medidas.png" border="0"></div>';
} else {
	$desenho .= '<div id="baseimg" style="z-index:1;" class="desenho"><img src="'.$caminho.'images/desenhos/corpo.png" border="0"></div>';
	$desenho .= '<div id="medidas" style="z-index:0;" class="desenho"><img src="'.$caminho.'images/desenhos/medidas.png" border="0"></div>';
}
	
$desenho .= '</div>';

$desenho .= '</div>';

echo $desenho;
?>