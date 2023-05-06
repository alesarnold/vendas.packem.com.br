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

//    require("cabecalho.php"); 

$svg_logo = '<svg version="1.0" id="Camada_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
viewBox="0 0 170.1 39" style="enable-background:new 0 0 170.1 39;" xml:space="preserve">
<style type="text/css">
.st0{fill:#F05223;}
</style>
<g>
<path d="M145.7,31.7V15.9h3.3l0.2,1.8c0.9-1.2,2.5-2.2,4.8-2.2c2.2,0,3.9,1,4.8,2.8c1.1-1.6,2.9-2.8,5.5-2.8c3.6,0,5.7,2.7,5.7,6.8
   v9.3h-3.8v-9.1c0-2.1-1.1-3.5-3-3.5c-1.6,0-2.7,1-3.5,2.2c0,0.3,0,0.6,0,0.7v9.7h-3.8v-9.1c0-2.1-1-3.5-3-3.5c-1.6,0-2.6,1-3.5,2.2
   v10.4H145.7z M131,22.2h7.5c-0.1-2.1-1.3-3.7-3.6-3.7C132.7,18.6,131.2,20.1,131,22.2 M127.2,23.8c0-4.8,3.2-8.2,7.8-8.2
   c4.9,0,7.3,3.6,7.3,8.1V25H131c0.1,2.5,1.6,3.9,4.3,3.9c1.9,0,3.2-0.8,3.9-1.8l2.6,2.1c-1.6,1.9-3.6,2.9-6.6,2.9
   C130.3,32,127.2,28.6,127.2,23.8 M111.6,31.7V10l3.8-1.5v16.5l6.1-9.2h4.6l-4.9,6.7l5.5,9.1h-4.4l-3.6-5.9l-4.2,5.9H111.6z
	M79,23.7c0,3.1,1.7,5,4.2,5c1.9,0,3-1,3.9-2.4v-5.1c-0.8-1.4-2-2.4-3.9-2.4C80.8,18.8,79,20.8,79,23.7 M75.2,23.7
   c0-4.8,2.9-8.2,7.3-8.2c2.3,0,4,1,4.8,2.1l0.2-1.8h3.4v15.8h-3.4l-0.2-1.8c-0.9,1.2-2.5,2.1-4.8,2.1C78.1,32,75.2,28.6,75.2,23.7
	M61.2,26.4c0.8,1.4,2,2.4,3.9,2.4c2.5,0,4.3-1.9,4.3-5c0-3-1.8-4.9-4.3-4.9c-1.9,0-3.1,1-3.9,2.4V26.4z M57.4,39V15.9h3.4l0.1,1.8
   c0.9-1.2,2.5-2.2,4.9-2.2c4.3,0,7.3,3.4,7.3,8.2c0,4.8-2.9,8.3-7.3,8.3c-2.4,0-3.9-0.9-4.7-1.9v7.3L57.4,39z M108.6,19l-2.7,2.2
   c-0.7-1.3-1.9-2.3-3.6-2.3c-2.8,0-4.4,2.2-4.4,4.9c0,2.8,1.6,5,4.4,5c1.7,0,2.9-1,3.7-2.3l2.7,2.1c-1.4,2.2-3.6,3.5-6.5,3.5
   c-4.9,0-8.3-3.5-8.3-8.3c0-4.8,3.3-8.2,8.2-8.2C105.2,15.5,107.3,16.8,108.6,19"/>
<polygon class="st0" points="33.2,0 8.6,0 24.6,15.9 49.2,15.9 	"/>
<polygon class="st0" points="0,15.9 16.1,31.7 40.7,31.7 24.6,15.9 	"/>
</g>
</svg>';

$svg_triangulos = '<svg version="1.0" id="Camada_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
viewBox="0 0 255.1 128.1" style="enable-background:new 0 0 255.1 128.1;" xml:space="preserve">
<style type="text/css">
.st0{fill:#F05223;}
</style>
<path class="st0" d="M254.1,1v124.6L130,1H254.1 M255.1,0H127.5l127.6,128.1V0L255.1,0z"/>
<path class="st0" d="M126.6,65v60.6L66.2,65H126.6 M127.6,64H63.8l63.8,64V64L127.6,64z"/>
<path class="st0" d="M62.8,65l0,60.6L2.4,65H62.8 M63.8,64H0l63.8,64h0V64L63.8,64z"/>
<path class="st0" d="M126.6,1v60.6L66.2,1H126.6 M127.6,0H63.8l63.8,64V0L127.6,0z"/>
<path class="st0" d="M62.8,1l0,60.6L2.4,1H62.8 M63.8,0H0l63.8,64h0V0L63.8,0z"/>
</svg>';

$caminho = "https://viajacerto.com.br/orcamentos/";

$pedido = $_GET["ped"];

$query = "SELECT id, username, email, nome, nivel FROM users ORDER BY nome ASC"; 

try { 
	$stmt = $db->prepare($query); 
	$stmt->execute(); 
}
catch(PDOException $ex) { 
	die("Failed to run query: " . $ex->getMessage()); 
} 

$rows = $stmt->fetchAll(); 


$query_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE `pedido` LIKE '$pedido' ORDER BY `id` DESC");

$row_pedidos = mysqli_fetch_array($query_pedidos);

$revisao = $row_pedidos["revisao"];


if ($_SESSION['user']['nivel'] != '1' and $_SESSION['user']['nivel'] != '2') {
	if ($row_pedidos['id_vend'] != $_SESSION['user']['id']) {
		die("<center><br><h1>Alerta!</h1>Você não tem permissão para visualizar esse conteúdo.</center>");
	}
}


$query_segmento = mysqli_query($conn,"SELECT segmento FROM segmentos WHERE id LIKE ".$row_pedidos['segmento_cliente']);
$row_segmento = mysqli_fetch_array($query_segmento);
$segmento_cliente = $row_segmento["segmento"];

if ($row_pedidos["fornecedora"] == "valor_sp") {
	$fornecedora = "Matriz";
} elseif ($row_pedidos["fornecedora"] == "valor_mg") {
	$fornecedora = "Filial 2";
} elseif ($row_pedidos["fornecedora"] == "valor_ba") {
	$fornecedora = "Filial 3";
} elseif ($row_pedidos["fornecedora"] == "valor_f1") {
	$fornecedora = "Filial 1";
} elseif ($row_pedidos["fornecedora"] == "valor_bonpar") {
	$fornecedora = "Filial 4";
}

if ($row_pedidos["usocons"] == "sim") {
	$uso_consumo = "SIM";
} elseif ($row_pedidos["usocons"] == "nao") {
	$uso_consumo = "NÃO";
}

$no_pedido = sprintf('%05d', $row_pedidos['pedido']);

$cliente = $row_pedidos['nome_cliente'];

if ($row_pedidos['referencia'] != "") {
	$referencia = " - ".$row_pedidos['referencia'];
} else {
	$referencia = "";
}

$cidade = $row_pedidos['cidade_cliente'];
$uf = strtoupper($row_pedidos['uf_cliente']);
$qtde = number_format($row_pedidos['qtde'],0,",",".");
$frete = strtoupper($row_pedidos['frete']);
$vendedor = $row_pedidos['vendedor'];

$query_email_vend = mysqli_query($conn,"SELECT * FROM users WHERE `id` LIKE '".$row_pedidos['id_vend']."'");
$row_email_vend = mysqli_fetch_array($query_email_vend);
$email_vend = $row_email_vend["email"];

//$email_vend = $row_pedidos['vendedor'];

if ($_SESSION['user']['nivel'] != '1' and $_SESSION['user']['nivel'] != '2') {
	$telefone_contato = $row_email_vend["celular"];
} else {
	$telefone_contato = $row_email_vend["celular"];
}


$prazo = $row_pedidos['prazo'];

if ($prazo == "0") {
	$prazo = "A VISTA";
} elseif ($prazo == "-1") {
	$prazo = "ANTECIPADO";
} else {
	$prazo = $prazo;
}
/*
if($row_pedidos['corpo'] == "cowad") {
		$row_pedidos['base1'] = $row_pedidos['base1']+2;
		$row_pedidos['base2'] = $row_pedidos['base2']+2;
}
*/

$dimensoes = str_replace(".",",",$row_pedidos['base1'])." x ".str_replace(".",",",$row_pedidos['base2'])." x ".str_replace(".",",",$row_pedidos['altura'])." cm";
$query_dolar = mysqli_query($conn,"SELECT * FROM `pedidos_extra` WHERE `pedido` LIKE '".$row_pedidos["pedido"]."' AND `revisao` LIKE '".$row_pedidos["revisao"]."' ORDER BY id DESC LIMIT 1");
$row_dolar = mysqli_fetch_array($query_dolar);

if($row_pedidos["embarques"] != "") {
	$embarques = $row_pedidos["embarques"];
} else {
	$embarques = "A COMBINAR";
}

if ($row_pedidos['mercado'] == "int") {
	$valor_unitario = "R$ ".number_format((float)$row_pedidos['valor_final'], 2, ',','.');

	$condicoes_venda = '
	<tr><td><b>ICMS</b></td><td><b>IPI</b></td></tr>
	<tr><td style="background-color:#EEE;">'.number_format($row_dolar['icms'], 2, ',','.').'%</td>
		<td style="background-color:#EEE;">Não incluso</td></tr>
	<tr><td><b>Pagamento</b></td><td><b>Frete</b></td></tr>
	<tr><td style="background-color:#EEE;">'.$prazo.'</td>
		<td style="background-color:#EEE;">'.$frete.'</td></tr>
	<tr><td colspan="2"><b>Entrega</b></td></tr>
	<tr><td colspan="2" style="background-color:#EEE;">'.$embarques.'</td></tr>
';

} elseif ($row_pedidos['mercado'] == "ext") {
	$valor_unitario = "US$ ".number_format((float)$row_dolar['valor_dolar'], 2, ',','.');

	$condicoes_venda = '
	<tr><td><b>ICMS</b></td><td><b>IPI</b></td></tr>
	<tr><td style="background-color:#EEE;">'.number_format($row_dolar['icms'], 2, ',','.').'%</td>
		<td style="background-color:#EEE;">Não incluso</td></tr>
	<tr><td><b>Pagamento</b></td><td><b>Frete</b></td></tr>
	<tr><td style="background-color:#EEE;">'.$prazo.'</td>
		<td style="background-color:#EEE;">'.$frete.'</td></tr>
	<tr><td colspan="2"><b>Entrega</b></td></tr>
	<tr><td colspan="2" style="background-color:#EEE;">'.$embarques.'</td></tr>
';

} else {
	$valor_unitario = "R$ ".number_format((float)$row_pedidos['valor_final'], 2, ',','.');

	$condicoes_venda = '
	<tr><td><b>ICMS</b></td><td><b>IPI</b></td></tr>
	<tr><td style="background-color:#EEE;">'.number_format($row_dolar['icms'], 2, ',','.').'%</td>
		<td style="background-color:#EEE;">Não incluso</td></tr>
	<tr><td><b>Pagamento</b></td><td><b>Frete</b></td></tr>
	<tr><td style="background-color:#EEE;">'.$prazo.'</td>
		<td style="background-color:#EEE;">'.$frete.'</td></tr>
	<tr><td colspan="2"><b>Entrega</b></td></tr>
	<tr><td colspan="2" style="background-color:#EEE;">'.$embarques.'</td></tr>
';

}


$valor_total = number_format((float)$row_pedidos['valor_final'] * $row_pedidos['qtde'], 2, ',','.');
$capacidade = number_format((float)$row_pedidos['carga_nominal'], 0, ',','.');

$query_pedidos_det = mysqli_query($conn,"SELECT * FROM `pedidos_det` WHERE `revisao_valor` LIKE (SELECT MAX(`revisao_valor`) FROM `pedidos_det` WHERE `pedido` LIKE '$pedido' AND `revisao` LIKE '$revisao') AND `pedido` LIKE '$pedido' AND `revisao` LIKE '$revisao' AND `nivel` LIKE '1' ORDER BY `id` ASC");
$number = mysqli_num_rows($query_pedidos_det);

$i = 1;

$revisao = sprintf('%02d', $revisao);

while($row_pedidos_det = mysqli_fetch_array($query_pedidos_det)) {

if ($i < $number - 1) {
	$descricao_prod .= $row_pedidos_det['desc'].", ";
} elseif ($i == $number - 1) {
	$descricao_prod .= $row_pedidos_det['desc']." e ";
} else {
	$descricao_prod .= $row_pedidos_det['desc'].".";
}

$i ++;

}

$descricao_prod = $descricao_prod;


$desenho .= '<div style="margin:auto;width:357px;">';

/*
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
*/

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
elseif ($row_pedidos["descarga"] == 'd_simples') { $descarga1_top = "222px"; $descarga1_left =  "251px"; $descarga2_top = "168px"; $descarga2_left =  "196px"; }
elseif ($row_pedidos["descarga"] == 'd_prot_presilha') { $descarga1_top = "222px"; $descarga1_left =  "251px"; $descarga2_top = "168px"; $descarga2_left =  "196px"; }
elseif ($row_pedidos["descarga"] == 'd_prot_mochila') { $descarga1_top = "222px"; $descarga1_left =  "251px"; $descarga2_top = "168px"; $descarga2_left =  "196px"; }
elseif ($row_pedidos["descarga"] == 'd_afunilado') { $descarga1_top = "209px"; $descarga1_left =  "198px"; $descarga2_top = "176px"; $descarga2_left =  "136px"; }
elseif ($row_pedidos["descarga"] == 'd_total') { $descarga1_top = "-35px"; $descarga1_left =  "0px"; $descarga2_top = "-35px"; $descarga2_left =  "0px"; }
elseif ($row_pedidos["descarga"] == 'd_total_presilha') { $descarga1_top = "-35px"; $descarga1_left =  "0px"; $descarga2_top = "-35px"; $descarga2_left =  "0px"; }
elseif ($row_pedidos["descarga"] == 'd_total_blindado') { $descarga1_top = "-35px"; $descarga1_left =  "0px"; $descarga2_top = "-35px"; $descarga2_left =  "0px"; }

$desenho .= '<div id="dim_descarga2" style="z-index: 14; top: '.$descarga2_top.'; left: '.$descarga2_left.';" class="medidas">'.str_replace(".",",",$row_pedidos["descarga2"]).'</div>';
$desenho .= '<div id="dim_descarga1" style="z-index: 13; top: '.$descarga1_top.'; left: '.$descarga1_left.';" class="medidas">'.str_replace(".",",",$row_pedidos["descarga1"]).'</div>';

if ($row_pedidos["carga"] == "vazio"){ $carga1_top = "-35px"; $carga1_left =  "0px"; $carga2_top = "-35px"; $carga2_left =  "0px"; }
elseif ($row_pedidos["carga"] == "c_saia"){ $carga1_top = "-35px"; $carga1_left =  "0px"; $carga2_top = "70px"; $carga2_left =  "180px"; }
elseif ($row_pedidos["carga"] == "c_afunilada"){ $carga1_top = "20px"; $carga1_left =  "140px"; $carga2_top = "70px"; $carga2_left =  "180px"; }
elseif ($row_pedidos["carga"] == "c_simples"){ $carga1_top = "16px"; $carga1_left =  "80px"; $carga2_top = "69px"; $carga2_left =  "137px"; }
elseif ($row_pedidos["carga"] == "c_simples_afunilada"){ $carga1_top = "-2px"; $carga1_left =  "85px"; $carga2_top = "43px"; $carga2_left =  "162px"; }
elseif ($row_pedidos["carga"] == "c_prot_presilha"){ $carga1_top = "61px"; $carga1_left =  "160px"; $carga2_top = "12px"; $carga2_left =  "85px"; }
elseif ($row_pedidos["carga"] == "c_prot_mochila"){ $carga1_top = "61px"; $carga1_left =  "160px"; $carga2_top = "12px"; $carga2_left =  "85px"; }
elseif ($row_pedidos["carga"] == "c_tipo_as"){ $carga1_top = "-35px"; $carga1_left =  "0px"; $carga2_top = "-35px"; $carga2_left =  "0px"; }

$desenho .= '<div id="dim_carga2" style="z-index: 12; top: '.$carga2_top.'; left: '.$carga2_left.';" class="medidas">'.str_replace(".",",",$row_pedidos["carga2"]).'</div>';
$desenho .= '<div id="dim_carga1" style="z-index: 11; top: '.$carga1_top.'; left: '.$carga1_left.';" class="medidas">'.str_replace(".",",",$row_pedidos["carga1"]).'</div>';
$desenho .= '<div id="dim_alca_fix_altura" style="z-index:10; top: 330px; left: 295px;" class="medidas">'.str_replace(".",",",$row_pedidos["alca_fix_altura"]).'</div>';
$desenho .= '<div id="dim_alca_altura" style="z-index:10; top: 298px; left: 295px;" class="medidas">'.str_replace(".",",",$row_pedidos["alca_altura"]).'</div>';
$desenho .= '<div id="dim_altura" style="z-index:9; top: 378px; left: 295px;" class="medidas">'.str_replace(".",",",$row_pedidos["altura"]).'</div>';
$desenho .= '<div id="dim_base2" style="z-index:8; top: 495px; left: 230px;" class="medidas">'.str_replace(".",",",$row_pedidos["base2"]).'</div>';
$desenho .= '<div id="dim_base1" style="z-index:7; top: 495px; left: 70px;" class="medidas">'.str_replace(".",",",$row_pedidos["base1"]).'</div>';

if ($row_pedidos["liner"] != "") {
$desenho .= '<div id="liner" style="z-index:6;" class="desenho"><img src="'.$caminho.'images/desenhos/'.$row_pedidos["liner"].'.png" border="0"></div>';
}
if ($row_pedidos["descarga"] != "") {
$desenho .= '<div id="descarga" style="z-index:5;" class="desenho"><img src="'.$caminho.'images/desenhos/'.$row_pedidos["descarga"].'';
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
$desenho .= '<div id="baseimg" style="z-index:1;" class="desenho"><img src="'.$caminho.'images/desenhos/corpo.png" border="0"></div>';
$desenho .= '<div id="medidas" style="z-index:0;" class="desenho"><img src="'.$caminho.'images/desenhos/medidas.png" border="0"></div>';

$desenho .= '</div>';

$desenho .= '</div>';

$mons = array(1 => "janeiro", 2 => "fevereiro", 3 => "março", 4 => "abril", 5 => "maio", 6 => "junho", 7 => "julho", 8 => "agosto", 9 => "setembro", 10 => "outubro", 11 => "novembro", 12 => "dezembro");
$month = date('n');
$month_name = $mons[$month];
$data_atual = date('j')." de ". $month_name . " de " . date('Y');


/*
$query_aprovacao = mysqli_query($conn,"SELECT * FROM pedidos_aprova WHERE `id_ped` LIKE '$pedido' ORDER BY `id` DESC");
$row_aprovacao = mysqli_fetch_array($query_aprovacao);

if ($row_aprovacao['nome'] === NULL) {
	$txt_aprovacao = '<small><small><font color="#FF0000">Este orçamento / pedido ainda não foi liberado / aprovado.</font></small></small>';
} else {
	$data_aprova = $row_aprovacao['data'];
	$data_aprova = strtotime($data_aprova);
	$data_aprova1 = date("d/m/Y", $data_aprova);
	$data_aprova2 = date("H:i:s", $data_aprova);
	$txt_aprovacao = '<small><small>Aprovado por: '.$row_aprovacao["nome"].' em '.$data_aprova1.' - às '.$data_aprova2.'.</small></small>';
}
*/
$txt_aprovacao = "";

$query_cliente_ped = mysqli_query($conn,"SELECT * FROM `cad_clientes` WHERE `id` LIKE (SELECT `id_cliente` FROM `pedidos_cliente` WHERE `pedido` LIKE '".$pedido."' LIMIT 1)");
$row_cliente_ped = mysqli_fetch_array($query_cliente_ped);

$contato_com = $row_cliente_ped['contato_com'];
$cliente_email = $row_cliente_ped['email_com'];
if ($row_cliente_ped['telefone'] != "") {
	$cliente_telefone = "(".$row_cliente_ped['ddd'].") ".$row_cliente_ped['telefone'];
} else {
	$cliente_telefone = "";
}

$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora` WHERE `sigla` = '".strtoupper(substr($row_pedidos["fornecedora"],6,2))."'");
$row_fornec = mysqli_fetch_array($query_fornec);

$end_fornec = $row_fornec['texto'];

$query_politica = mysqli_query($conn,"SELECT * FROM `politica` WHERE `id` = '1'");
$row_politica = mysqli_fetch_array($query_politica);

$tit_politica = $row_politica['titulo'];
$txt_politica = $row_politica['texto'];

$orcamento = '<!DOCTYPE html>
<html>
<head>
<style>

@page {
	margin: 0px;
}
body {
	font-family:Verdana,Ubuntu,Arial,sans-serif;
	font-size: 9pt;
	color: #333;
}
h1 {
	font-size: 15pt;
	color: #666;
}
table {
	width: 100%;
    border-collapse: collapse;
}
table, td, th {
    border-bottom: 1px solid #ccc;
}
td {
    padding: 5px;
}
.tab_orcamento {
	margin-top: 10px;
	margin-bottom: 10px;
	width: 100%;
}
.tab_orcamento td {
	border: 1px solid #CCC;
	padding: 3px 10px;
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
	text-align: center;
	overflow: hidden;
}

.footer { position: fixed; left: 0px; bottom: -80px; right: 0px; height: 50px; text-align: center; }      

</style>
</head>

<body>

	<div style="position:absolute; top:60px; left:55px;">
		<img src="data:image/svg+xml;base64,'.base64_encode($svg_logo).'" width="170">
	</div>
	<div style="position:absolute; top:0; right:0;">
		<img src="data:image/svg+xml;base64,'.base64_encode($svg_triangulos).'" width="300">
	</div>
	<div style="position:absolute; top:1103px; width: 100%; left: 50%;transform: translate(-50%,0%);">
		<hr style="border: 10px solid #f05223;">
	</div>
	<div style="position:absolute; top:130px; width: 660px; text-align: center; left: 50%;transform: translate(-50%,0%);">
		'.$txt_aprovacao.'
	</div>
	<div style="position:absolute;top:175px;left: 50%;transform: translate(-50%,0%);">
		<h1>PROPOSTA COMERCIAL Nº '.$no_pedido.'/'.$revisao.'</h1>
	</div>
	<div style="position:absolute; top:250px; width: 680px; left: 50%;transform: translate(-50%,0%);">
		<hr style="height:1px;border:none;color:#DDD;background-color:#DDD;">
	</div>
	<div style="position:absolute; top:260px; width: 680px; left: 50%;transform: translate(-50%,0%);">
		<div style="width:490px; float:left; margin-left:10px; margin-right:30px;"><h2>Descrição do produto</h2><p>'.$descricao_prod.'</p></div>
		<div style="width:150px; float:left;"><h2>Dimensões</h2><p>'.$dimensoes.'<br><small>(Tolerância de: +/- 2cm)</small></p></div>
	</div>
	<div style="position:absolute; top:380px; width: 680px; left: 50%;transform: translate(-50%,0%);">
		<hr style="height:1px;border:none;color:#DDD;background-color:#DDD;">
	</div>
	<div style="position:absolute; top:390px; width: 680px; left: 50%;transform: translate(-50%,0%);">
		<div style="width:357px; height: 564px; float:right;">'.$desenho.'</div>
		<div style="width:303px; float:left; margin:10px;">
			<table>
				<tr><td colspan="2"><b>Data</b></td></tr>
				<tr><td colspan="2" style="background-color:#EEE;">'.$data_atual.'</td></tr>
				<tr><td colspan="2"><b>Cliente</b></td></tr>
				<tr><td colspan="2" style="background-color:#EEE;">'.$cliente.'</td></tr>
				<tr><td colspan="2"><b>Validade</b></td></tr>
				<tr><td colspan="2" style="background-color:#EEE;">10 dias a partir da data da proposta</td></tr>
				<tr><td><b>Uso e consumo</b></td><td><b>Capacidade nominal</b></td></tr>
				<tr><td style="background-color:#EEE;">'.$uso_consumo.'</td>
					<td style="background-color:#EEE;">'.$capacidade.' kg</td></tr>
				'.$condicoes_venda.'
				<tr><td><b>Quantidade</b></td><td><b>Valor unitário</b></td></tr>
				<tr><td style="background-color:#EEE;">'.$qtde.'</td>
					<td style="background-color:#EEE; font-size:15pt; text-align:right;"><b>'.$valor_unitario.'</b></td></tr>
			</table>
		</div>
	</div>
	<div style="position:absolute; top:1030px; width: 680px; left: 50%;transform: translate(-50%,0%);">
		<hr style="height:1px;border:none;color:#DDD;background-color:#DDD;">
	</div>
	<div style="position:absolute;top:1050px;left: 50%;transform: translate(-50%,0%);text-align:center;color:#666;font-size:7pt;">
		<p>'.$end_fornec.'</p>
	</div>


	<div style="page-break-before: always;"></div>
	
	<div style="position:absolute; top:60px; left:55px;">
		<img src="data:image/svg+xml;base64,'.base64_encode($svg_logo).'" width="170">
	</div>
	<div style="position:absolute; top:0; right:0;">
		<img src="data:image/svg+xml;base64,'.base64_encode($svg_triangulos).'" width="300">
	</div>
	<div style="position:absolute; top:1103px; width: 100%; left: 50%;transform: translate(-50%,0%);">
		<hr style="border: 10px solid #f05223;">
	</div>
	<div style="position:absolute; top:130px; width: 660px; text-align: center; left: 50%;transform: translate(-50%,0%);">
		'.$txt_aprovacao.'
	</div>
	<div style="position:absolute;top:175px;left: 50%;transform: translate(-50%,0%);">
		<h1>PROPOSTA COMERCIAL Nº '.$no_pedido.'/'.$revisao.'</h1>
	</div>
	<div style="position:absolute; top:250px; width: 680px; left: 50%;transform: translate(-50%,0%);">
		<hr style="height:1px;border:none;color:#DDD;background-color:#DDD;">
	</div>
	
	<div style="position:absolute; top:1030px; width: 680px; left: 50%;transform: translate(-50%,0%);">
		<hr style="height:1px;border:none;color:#DDD;background-color:#DDD;">
	</div>	
	<div style="position:absolute;top:260px;width:660px;left:50%;transform:translate(-50%,0%);text-align:left;color:#BBB;font-size:5.5pt;">
		<p><b>'.$tit_politica.'</b><br>
		'.$txt_politica.'</p>
	</div>
	<div style="position:absolute; top:1030px; width: 680px; left: 50%;transform: translate(-50%,0%);">
		<hr style="height:1px;border:none;color:#DDD;background-color:#DDD;">
	</div>
	<div style="position:absolute;top:1050px;left: 50%;transform: translate(-50%,0%);text-align:center;color:#666;font-size:7pt;">
		<p>'.$end_fornec.'</p>
	</div>


</body>
</html>';

$orcamento = preg_replace('/>\s+</', '><', $orcamento);
//echo $orcamento;
//die();

require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', TRUE);
$dompdf->load_html ($orcamento);
$dompdf->setPaper('A4');
$dompdf->render();
$pdf = $dompdf->output();
file_put_contents("pdf_pedidos/".date("Y-m-d")."_orcamento_".$no_pedido.".pdf", $pdf);
$dompdf->stream(
	"Orçamento ".(float)$no_pedido." - ".$cliente." - ".$dimensoes." - ".$capacidade." kg.pdf",
    array(
        "Attachment" => false // Para download, altere para true
    )
);
?>