<?php

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

if ($_POST["nome_cliente"] == "") {
	header("Location: orcamento.php"); 
	die("Redirecionando para ORÇAMENTO."); 
}

$fornecedora = $_POST["fornecedora"];

//$revisao = $_POST["revisao"];
$id_vend = $_POST["id_vend"];

/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
die();
*/

if ($_POST["novo_cliente"] == "sim") {
	$novo_cliente = mysqli_query($conn,"INSERT INTO `cad_clientes` (`id`, `selecao`, `cnpj_cpf`, `insc_est`, `razao`, `nome`, `rua`, `numero`, `complemento`, `bairro`, `cidade`, `uf`, `cep`, `ddd`, `telefone`, `ramal`, `ddd_cel`, `celular`, `contato_com`, `email_com`, `contato_fin`, `email_fin`, `ramal_fin`, `segmento`, `id_vend`, `status`, `data`, `atualizacao`) VALUES (NULL, '".$_POST["selecao"]."', '".$_POST["cnpj_cpf"]."', '', '', '".$_POST["nome_cliente"]."', '', '', '', '', '".$_POST["cidade_cliente"]."', '".$_POST["uf_cliente"]."', '', '".$_POST["ddd"]."', '".$_POST["telefone"]."', '', '', '', '".$_POST["contato_com"]."', '".$_POST["email_com"]."', '', '', '', '".$_POST["segmento_cliente"]."', '".$_SESSION["user"]["id"]."', '2', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."'); ");
	if(! $novo_cliente ) { die('Não foi possível adicionar informações de cliente: ' . mysqli_error($conn)); }
	$id_cliente = mysqli_insert_id($conn);
} elseif ($_POST["novo_cliente"] == "nao") {
	$atualiza_cliente = mysqli_query( $conn, "UPDATE `cad_clientes` SET `ddd` = '".$_POST["ddd"]."', `telefone` = '".$_POST["telefone"]."', `email_com` = '".$_POST["email_com"]."', `nome` = '".$_POST["nome_cliente"]."', `cidade` = '".$_POST["cidade_cliente"]."', `uf` = '".$_POST["uf_cliente"]."', `selecao` = '".$_POST["selecao"]."', `cnpj_cpf` = '".$_POST["cnpj_cpf"]."', `segmento` = '".$_POST["segmento_cliente"]."', `atualizacao` = '".date('Y-m-d H:i:s')."' WHERE `id` LIKE '".$_POST["id_cliente"]."' LIMIT 1;");
	if(! $atualiza_cliente ) { die('Não foi possível atualizar informações de cliente: ' . mysqli_error($conn)); }
	$id_cliente = $_POST["id_cliente"];
}
$gramat_corpo_grava = $_POST["gramat_corpo"];
$gramat_tampa_grava = $_POST["gramat_tampa"];
$gramat_fundo_grava = $_POST["gramat_fundo_d"];

if($_POST["gramat_tampa"] == 191 || $_POST["gramat_tampa"] == 221 || $_POST["gramat_tampa"] == 211 || $_POST["gramat_tampa"] == 181 || $_POST["gramat_tampa"] == 206 || $_POST["gramat_tampa"] == 156 || $_POST["gramat_tampa"] == 241) {
	$_POST["gramat_tampa"] -= 1;
}
if($_POST["gramat_corpo"] == 191 || $_POST["gramat_corpo"] == 221 || $_POST["gramat_corpo"] == 211 || $_POST["gramat_corpo"] == 181 || $_POST["gramat_corpo"] == 206 || $_POST["gramat_corpo"] == 156 || $_POST["gramat_corpo"] == 241) {
	$_POST["gramat_corpo"] -= 1;
}
if($_POST["gramat_corpo"] == 222) {
	$_POST["gramat_corpo"] -= 2;
}
if($_POST["gramat_fundo_d"] == 191 || $_POST["gramat_fundo_d"] == 221 || $_POST["gramat_fundo_d"] == 211 || $_POST["gramat_fundo_d"] == 181 || $_POST["gramat_fundo_d"] == 206 || $_POST["gramat_fundo_d"] == 156 || $_POST["gramat_fundo_d"] == 241) {
	$_POST["gramat_fundo_d"] -= 1;
}

$class_prod = $_POST["class_prod"];
if($class_prod != "") { $class_produtividade = "_".$class_prod; } else { $class_produtividade = ""; }

$preco_quilo = mysqli_query($conn,"SELECT * FROM preco_kilo");
while($row = mysqli_fetch_array($preco_quilo)) {
	if ($_POST["mercado"] == "int") {
/*
		echo "<pre>";
		echo "FORNEC: ".$fornecedora."\n\r";
		echo $unidade_forn = substr($fornecedora, -2)."\n\r";
		print_r($row);
		echo "<pre>";
		die();
*/
		if ($row['tipo'] == "interno".$class_produtividade) {
			$quilo_int = $row["".$fornecedora.""];
			$quilo_corpo = $row["".$fornecedora.""];
			$quilo_carga = $row["".$fornecedora.""];
			$quilo_descarga = $row["".$fornecedora.""];
		} elseif ($row['tipo'] == "laminado".$class_produtividade) {
			$quilo_lamin = $row["".$fornecedora.""];
		} elseif ($row['tipo'] == $_POST["tampa"]) {
			$gramat_tampa = $_POST["gramat_tampa"]/1000;
		} elseif ($row['tipo'] == $_POST["valvula"]) {
			$gramat_valv = $_POST["gramat_valvula"]/1000;
		} elseif ($row['tipo'] == $_POST["valvula_d"]) {
			$gramat_valv_d = $_POST["gramat_valvula_d"]/1000;
		}
	} elseif ($_POST["mercado"] == "ext") {
		if ($row['tipo'] == "exp_interno") {
			$quilo_int = $row["".$fornecedora.""];
			$quilo_corpo = $row["".$fornecedora.""];
			$quilo_carga = $row["".$fornecedora.""];
			$quilo_descarga = $row["".$fornecedora.""];
		} elseif ($row['tipo'] == "exp_laminado") {
			$quilo_lamin = $row["".$fornecedora.""];
		} elseif ($row['tipo'] == $_POST["tampa"]) {
			$gramat_tampa = $_POST["gramat_tampa"]/1000;
		} elseif ($row['tipo'] == $_POST["valvula"]) {
			$gramat_valv = $_POST["gramat_valvula"]/1000;
		} elseif ($row['tipo'] == $_POST["valvula_d"]) {
			$gramat_valv_d = $_POST["gramat_valvula_d"]/1000;
		}

	}

	if ($row['tipo'] == "fio_costura") {
		$quilo_fio = $row["".$fornecedora.""];
	}

	if ($row['tipo'] == "fio_costura_pp") {
		$quilo_fio_pp = $row["".$fornecedora.""];
	}

	if ($row['tipo'] == "fio_vedante") {
		$quilo_fio_vedante = $row["".$fornecedora.""];
	}

	if ($row['tipo'] == "trava_rede") {
		$quilo_trava_rede = $row["".$fornecedora.""];
	}

	if ($row['tipo'] == "impressao") {
		$preco_impressao = $row["".$fornecedora.""];
	}

	if ($row['tipo'] == "tec_leve") {
		$quilo_leve = $row["".$fornecedora.""];
	}

}


/*
echo "GRAMATURA TAMPA: ".$gramat_tampa."<br><br>";
echo "GRAMATURA VALVULA: ".$gramat_valv."<br><br>";
echo "GRAMATURA SAIA: ".$gramat_saia."<br><br><br>";

echo "GRAMATURA FUNDO: ".$gramat_fundo_d."<br><br>";
echo "GRAMATURA VALVULA D: ".$gramat_valv_d."<br><br>";

echo $_POST["valvula"];
die();
*/


if ($gramat_corpo_grava == "147" || $gramat_corpo_grava == "170" || $gramat_corpo_grava == "181" || $gramat_corpo_grava == "185" || $gramat_corpo_grava == "191" || $gramat_corpo_grava == "205" /* || $gramat_corpo_grava == "210" */ || $gramat_corpo_grava == "211" || $gramat_corpo_grava == "215" || $gramat_corpo_grava == "221" || $gramat_corpo_grava == "225" || $gramat_corpo_grava == "245" || $gramat_corpo_grava == "250" || $gramat_corpo_grava == "265" || $gramat_corpo_grava == "295") {
	$plastificado = "1";
	$quilo_corpo = $quilo_lamin;
}

if ($gramat_corpo_grava <= "100") {
	$quilo_corpo = $quilo_leve;
}

if ($_POST["gramat_tampa"] == "65" || $_POST["gramat_tampa"] == "70" || $_POST["gramat_tampa"] == "147" || $_POST["gramat_saia"] == "65" || $_POST["gramat_saia"] == "70" || $_POST["gramat_saia"] == "147") {
	$quilo_carga = $quilo_lamin;
} else {
	$quilo_carga = $quilo_carga;
}

if ($_POST["gramat_tampa"] <= "100") {
	$quilo_carga = $quilo_leve;
}

if ($_POST["gramat_valvula"] == "65" || $_POST["gramat_valvula"] == "70" || $_POST["gramat_valvula"] == "147" || $_POST["gramat_saia"] == "65" || $_POST["gramat_saia"] == "70" || $_POST["gramat_saia"] == "147") {
	$quilo_v = $quilo_lamin;
} else {
	$quilo_v = $quilo_carga;
}

if ($_POST["gramat_valvula"] <= "100") {
	$quilo_v = $quilo_leve;
}

if ($gramat_fundo_grava == "147" || $gramat_fundo_grava == "170" || $gramat_fundo_grava == "185" || $gramat_fundo_grava == "191" || $gramat_fundo_grava == "215" || $gramat_fundo_grava == "221" || $gramat_fundo_grava == "245" || $gramat_fundo_grava == "250" || $gramat_fundo_grava == "265" || $gramat_fundo_grava == "295") {
	$quilo_descarga = $quilo_lamin;
}

if ($gramat_fundo_grava <= "100") {
	$quilo_descarga = $quilo_leve;
}

if ($_POST["gramat_valvula_d"] == "65" || $_POST["gramat_valvula_d"] == "70" || $_POST["gramat_valvula_d"] == "147") {
	$quilo_v_d = $quilo_lamin;
} else {
	$quilo_v_d = $quilo_descarga;
}

if ($_POST["gramat_valvula_d"] <= "100") {
	$quilo_v_d = $quilo_leve;
}


$quilo = $quilo_int;

if($_POST["gramat_tampa"] != "") {
	$gramat_tampa = $_POST["gramat_tampa"]/1000;
} else {
	$gramat_tampa = 0;
}

if($_POST["gramat_valvula"] != "") {
	$gramat_valv = $_POST["gramat_valvula"]/1000;
} else {
	$gramat_valv = 0;
}

if($_POST["gramat_saia"] != "") {
	$gramat_saia = $_POST["gramat_saia"]/1000;
} else {
	$gramat_saia = 0;
}

if ($_POST["gramat_fundo_d"] != "") {
	$gramat_fundo_d = $_POST["gramat_fundo_d"]/1000;
} else {
	$gramat_fundo_d = 0;
}

if ($_POST["gramat_valvula_d"] != "") {
	$gramat_valv_d = $_POST["gramat_valvula_d"]/1000;
} else {
	$gramat_valv_d = 0;
}

if ($_POST["trava_rede"] == "on") {
	$quilo_travas = $quilo_trava_rede;
} else {
	$quilo_travas = $quilo_lamin;
}

/*
if ($_POST["plastificado"] == "on") {
	$quilo_corpo = $quilo_lamin;
	$quilo_carga = $quilo_lamin;
	$quilo_descarga = $quilo_lamin;
	$quilo_v = $quilo_lamin;
}
*/

$no_pedido = $_POST["no_pedido"]; //sprintf('%05d', $pedido);


/* ============ VARIAVEL - FIOS ============ */

$fio2000 = 2000;
$fio3000 = 3000;
$fio4000 = 4000;

$tipo_cost_corpo = $_POST["tipo_cost_corpo"];
$tipo_cost_enchim = $_POST["tipo_cost_enchim"];
$tipo_cost_esvaz = $_POST["tipo_cost_esvaz"];
$tipo_cost_alca = $_POST["tipo_cost_alca"];

//echo "tipo fio costura<br><br>CORPO: ".$tipo_cost_corpo."<br>CARGA: ".$tipo_cost_enchim."<br>DESCARGA: ".$tipo_cost_esvaz."<br>ALCA: ".$tipo_cost_alca;
//die();

/* ============ FUNCÃO PARA GRAVAR DETALHES ============ */


function GravaDet($f_id,$f_pedido,$f_revisao,$f_nivel,$f_desc,$f_valor_kg,$f_qtde_mat,$f_gramat,$f_valor,$f_m_quadrado,$f_largura,$f_corte,$f_qtde) {

global $conn,$revisao_valor;

$f_desc = $f_desc;

if ($f_valor_kg == "") { $f_valor_kg = "0.00"; }
if ($f_qtde_mat == "") { $f_qtde_mat = "0.00"; }
if ($f_gramat == "") { $f_gramat = "0"; }

$sql_det = "INSERT INTO `pedidos_det` (
    `id`,
    `pedido`,
    `revisao`,
    `nivel`,
    `desc`,
    `valor_kg`,
    `qtde_mat`,
    `gramat`,
    `valor`,
    `m_quadrado`,
    `revisao_valor`,
	`largura`,
	`corte`,
	`qtde`)
VALUES (
    NULL,
    '".$f_pedido."',
    '".$f_revisao."',
    '".$f_nivel."',
    '".$f_desc."',
    '".$f_valor_kg."',
    '".$f_qtde_mat."',
    '".$f_gramat."',
    '".$f_valor."',
    '".$f_m_quadrado."',
    '".$revisao_valor."',
	'".$f_largura."',
	'".$f_corte."',
	'".$f_qtde."')";
$pedido_detalhe = mysqli_query( $conn, $sql_det );

/*
echo "<pre>";
echo $sql_det;
echo "</pre>";
*/

if(! $pedido_detalhe ) { die('Não foi possível gerar detalhes do orçamento: ' . mysqli_error($conn)); }

}


/* ============ CORPO PLANO SIMPLES ============ */

if ($_POST["corpo"] == "qowa" || $_POST["corpo"] == "qowao" || $_POST["corpo"] == "qowam" || $_POST["corpo"] == "qowaa" || $_POST["corpo"] == "qowat" || $_POST["corpo"] == "qhe" || $_POST["corpo"] == "qhe_ref" || $_POST["corpo"] == "qms") {

$base1 = $_POST["base1"]+10;
$base2 = $_POST["base2"]+10;
$altura = $_POST["altura"];
if($_POST["cost_fio_topo"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
//echo "<script>alert('LARGURA: ".$base1." / ALTURA: ".$altura."');</script>";

$valor_corpo1 = $base1 * 2;
$valor_corpo2 = $base2 * 2;
$valor_corpo_larg = $valor_corpo1 + $valor_corpo2;
$valor_corpo_met = $valor_corpo_larg * $altura;
$valor_corpo = $valor_corpo_met / 10000 ;

$gramat_corpo = $_POST["gramat_corpo"] / 1000;
$valor_corpo = $quilo_corpo * $gramat_corpo * $valor_corpo;


if ($tipo_cost_corpo == "simples" || $tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	$peso_fio_ved_corpo = 0.8;
	$peso_fio_corpo = 0.2177;
} elseif ($tipo_cost_corpo == "dupla") {
	$peso_fio_corpo = 0.4354;
} elseif ($tipo_cost_corpo == "overlock") {
	$peso_fio_corpo = 0.204;
}

$costura_corpo = $altura * 4;
$costura_corpo = $costura_corpo + ($valor_corpo1*2) + ($valor_corpo2*2);
$costura_corpo_met = $costura_corpo / 100;
$costura_corpo_gramat = $peso_fio_corpo * 10;
$costura_corpo = $costura_corpo_met * $costura_corpo_gramat / 1000;
$costura_corpo = $costura_corpo * $quilo_fio_pp;

if ($tipo_cost_corpo == "simples1ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_corpo == "simples2ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100 * 2;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Corpo plano simples",
    "",
    number_format((float)$valor_corpo_met / 10000, 2),
    "",
    number_format((float)$valor_corpo+$costura_corpo+$costura_corpo_ved, 2),
	"1",
	0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Corpo",
    number_format((float)$quilo_corpo, 2),
    number_format((float)$valor_corpo_met / 10000, 2),
    number_format((float)$_POST["gramat_corpo"], 0),
    number_format((float)$valor_corpo, 2),
	"1",
	number_format((float)$base1, 2),
	number_format((float)$altura, 2),
	"4");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Lat+Tampa+Fundo)",
    number_format((float)$quilo_fio_pp, 2),
    number_format((float)$costura_corpo_met, 2),
    number_format((float)$costura_corpo_gramat, 2),
    number_format((float)$costura_corpo, 2),
	"0",0,0,0);

if ($tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Lat+Tampa+Fundo)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_corpo_ved_met, 2),
		number_format((float)$costura_corpo_ved_gramat, 2),
		number_format((float)$costura_corpo_ved, 2),
		"0",0,0,0);
}
$valor_corpo = $valor_corpo+$costura_corpo+$costura_corpo_ved;

/* ============ CORPO PLANO DUPLO OU COM FORRO ============ */

} elseif ($_POST["corpo"] == "qowa2" || $_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowafi") {

$base1 = $_POST["base1"]+10;
$base2 = $_POST["base2"]+10;
$altura = $_POST["altura"];
if($_POST["cost_fio_topo"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
//echo "<script>alert('LARGURA: ".$base1." / ALTURA: ".$altura."');</script>";

$valor_corpo1 = $base1 * 2;
$valor_corpo2 = $base2 * 2;
$valor_corpo = $valor_corpo1 + $valor_corpo2;
$valor_corpo_met = $valor_corpo * $altura;
$valor_forro = $valor_corpo_met / 10000 ;
$valor_corpo = $valor_corpo_met / 10000 ;

$gramat_corpo = $_POST["gramat_corpo"] / 1000;
$valor_corpo = $quilo_corpo * $gramat_corpo * $valor_corpo;

if ($_POST["gramat_forro"] == "65" || $_POST["gramat_forro"] == "70" || $_POST["gramat_forro"] == "147") {
	$gramat_forro = $_POST["gramat_forro"] / 1000;
	$quilo_forro = $quilo_lamin;
} else {
	$gramat_forro = $_POST["gramat_forro"] / 1000;
	$quilo_forro = $quilo_corpo;
}

$valor_forro = $quilo_forro * $gramat_forro * $valor_forro;

if ($tipo_cost_corpo == "simples" || $tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	$peso_fio_ved_corpo = 0.8;
	$peso_fio_corpo = 0.2177;
} elseif ($tipo_cost_corpo == "dupla") {
	$peso_fio_corpo = 0.4354;
} elseif ($tipo_cost_corpo == "overlock") {
	$peso_fio_corpo = 0.204;
}

$costura_corpo = $altura * 4;
$costura_corpo = $costura_corpo + ($valor_corpo1*2) + ($valor_corpo2*2);
$costura_corpo_met = $costura_corpo / 100;
$costura_corpo_gramat = $peso_fio_corpo * 10;
$costura_corpo = $costura_corpo_met * $costura_corpo_gramat / 1000;
$costura_corpo = $costura_corpo * $quilo_fio_pp;

if ($tipo_cost_corpo == "simples1ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_corpo == "simples2ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100 * 2;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
}


GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Corpo plano duplo ou com forro",
	"",
	number_format((float)$valor_corpo_met / 10000 *2, 2),
	"",
	number_format((float)$valor_corpo+$valor_forro+$costura_corpo+$costura_corpo_ved, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Corpo",
    number_format((float)$quilo_corpo, 2),
    number_format((float)$valor_corpo_met / 10000, 2),
    number_format((float)$_POST["gramat_corpo"], 0),
    number_format((float)$valor_corpo, 2),
	"1",
	number_format((float)$base1, 2),
	number_format((float)$altura, 2),
	"4");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Forro",
    number_format((float)$quilo_forro, 2),
    number_format((float)$valor_corpo_met / 10000, 2),
    number_format((float)$_POST["gramat_forro"], 0),
    number_format((float)$valor_forro, 2),
	"1",
	number_format((float)$base1, 2),
	number_format((float)$altura, 2),
	"4");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Lat+Tampa+Fundo)",
    number_format((float)$quilo_fio_pp, 2),
    number_format((float)$costura_corpo_met, 2),
    number_format((float)$costura_corpo_gramat, 2),
    number_format((float)$costura_corpo, 2),
	"0",0,0,0);

if ($tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Lat+Tampa+Fundo)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_corpo_ved_met, 2),
		number_format((float)$costura_corpo_ved_gramat, 2),
		number_format((float)$costura_corpo_ved, 2),
		"0",0,0,0);
}
$valor_corpo = $valor_corpo+$costura_corpo+$costura_corpo_ved+$valor_forro;

/* ============ CORPO COM TRAVAS OU GOMOS ============ */

} elseif ($_POST["corpo"] == "qowad4" || $_POST["corpo"] == "qowad8") {

if ($_POST["corpo"] == "qowad4") {

	$base1 = $_POST["base1"] + 10;
	$base2 = $_POST["base2"] + 10;
	$altura = $_POST["altura"];
	if($_POST["cost_fio_topo"] == "on") {
		$altura += 3;
	} else {
		$altura += 5;
	}
	if($_POST["cost_fio_base"] == "on") {
		$altura += 3;
	} else {
		$altura += 5;
	}
	//echo "<script>alert('".$altura."');</script>";

	$valor_corpo1 = $base1 * 2;
	$valor_corpo2 = $base2 * 2;


if ($tipo_cost_corpo == "simples" || $tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	$peso_fio_ved_corpo = 0.8;
	$peso_fio_corpo = 0.2177;
	$peso_fio_trava = 0.1119;
} elseif ($tipo_cost_corpo == "dupla") {
	$peso_fio_corpo = 0.4354;
	$peso_fio_trava = 0.2238;
} elseif ($tipo_cost_corpo == "overlock") {
	$peso_fio_corpo = 0.204;
	$peso_fio_trava = 0.096;
}

$costura_corpo = $altura * 4;
$costura_corpo = $costura_corpo + ($valor_corpo1*2) + ($valor_corpo2*2);
$costura_corpo_met = $costura_corpo / 100;
$costura_corpo_gramat = $peso_fio_corpo * 10;
$costura_corpo = $costura_corpo_met * $costura_corpo_gramat / 1000;
$costura_corpo = $costura_corpo * $quilo_fio_pp;

if ($tipo_cost_corpo == "simples1ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved = $costura_corpo_ved + $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_corpo == "simples2ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved = $costura_corpo_ved + $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100 * 2;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
}


$costura_trava_met = ($altura+10) * 8;
$costura_trava_met = $costura_trava_met / 100;
$costura_trava_gramat = $peso_fio_trava * 10;
$costura_trava = $costura_trava_met * $costura_trava_gramat / 1000;
$costura_trava = $costura_trava * $quilo_fio_pp;



} elseif ($_POST["corpo"] == "qowad8") {

	$base1 = $_POST["base1"] + 10;
	$base2 = $_POST["base2"] + 10;
	$altura = $_POST["altura"];
	if($_POST["cost_fio_topo"] == "on") {
		$altura += 3;
	} else {
		$altura += 5;
	}
	if($_POST["cost_fio_base"] == "on") {
		$altura += 3;
	} else {
		$altura += 5;
	}
	//echo "<script>alert('".$altura."');</script>";

	$valor_corpo1 = $base1 * 2;
	$valor_corpo2 = $base2 * 2;

if ($tipo_cost_corpo == "simples" || $tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	$peso_fio_ved_corpo = 0.8;
	$peso_fio_corpo = 0.2177;
	$peso_fio_trava = 0.1119;
} elseif ($tipo_cost_corpo == "dupla") {
	$peso_fio_corpo = 0.4354;
	$peso_fio_trava = 0.2238;
} elseif ($tipo_cost_corpo == "overlock") {
	$peso_fio_corpo = 0.204;
	$peso_fio_trava = 0.096;
}


$costura_corpo = $altura * 4;
$costura_corpo = $costura_corpo + ($valor_corpo1*2) + ($valor_corpo2*2);
$costura_corpo_met = $costura_corpo / 100;
$costura_corpo_gramat = $peso_fio_corpo * 10;
$costura_corpo = $costura_corpo_met * $costura_corpo_gramat / 1000;
$costura_corpo = $costura_corpo * $quilo_fio_pp;

if ($tipo_cost_corpo == "simples1ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_corpo == "simples2ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved += $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100 * 2;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
}

$costura_trava_met = ($altura+10) * 8;
$costura_trava_met = $costura_trava_met / 100;
$costura_trava_gramat = $peso_fio_trava * 10;
$costura_trava = $costura_trava_met * $costura_trava_gramat / 1000;
$costura_trava = $costura_trava * $quilo_fio_pp;

}

$valor_corpo1 = $base1 * 2;
$valor_corpo2 = $base2 * 2;
$valor_corpo = $valor_corpo1 + $valor_corpo2;
$valor_corpo_met = $valor_corpo * $altura;
$valor_corpo = $valor_corpo_met / 10000 ;

$gramat_corpo = $_POST["gramat_corpo"] / 1000;
$valor_corpo = $quilo_corpo * $gramat_corpo * $valor_corpo;
/*
$b1 = $_POST["base1"];
$b2 = $_POST["base2"];

$b13 = $b1 / 3;
$b23 = $b2 / 3;

$b1 = $b13 * $b13;
$b2 = $b23 * $b23;

$h = $b1 + $b2;
$h = sqrt($h) + 6;
$h = ceil($h);
*/
$h = 60;

if ($_POST["trava_rede"] == "on") {
	$valor_trava_corte = $_POST["altura"] - 10;
	$valor_trava_met = $valor_trava_corte * 4 * 100;
	$valor_trava = $valor_trava_met / 10000;
	$valor_trava = $quilo_travas * 0.060 * $valor_trava;
} else {
	if (strpos(mb_strtoupper($_POST["nome_cliente"]), "BRASKEM") !== false) {
		$valor_trava_gramat = 0.160;
		$valor_trava_corte = $_POST["altura"] - 10;
	} else {
		$valor_trava_gramat = 0.160;
		$valor_trava_corte = $_POST["altura"] - 20;
	}
	$valor_trava_met = $valor_trava_corte * $h * 4;
	$valor_trava = $valor_trava_met / 10000;
	$valor_trava = $quilo_travas * $valor_trava_gramat * $valor_trava;
}

if ($_POST["fio_ved_travas"] == "on") {
	//$valor_fio_ved_travas_met = $_POST["altura"] * 8 * 1.04 / 100; ********* ALTERADO EM 21/02/2018 **********
	$valor_fio_ved_travas_met = ($_POST["altura"]+20) * 8 / 100;
	$valor_fio_ved_travas_met = round($valor_fio_ved_travas_met);
	$valor_fio_ved_travas = $valor_fio_ved_travas_met * 8 * $quilo_fio_vedante;
	$valor_fio_ved_travas = $valor_fio_ved_travas / 1000;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Corpo plano travado",
    "",
    number_format((float)$valor_corpo_met / 10000 + $valor_trava_met / 10000, 2),
    "",
    number_format((float)$valor_corpo+$costura_corpo+$costura_corpo_ved+$valor_trava+$costura_trava+$valor_fio_ved_travas, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Corpo",
    number_format((float)$quilo_corpo, 2),
    number_format((float)$valor_corpo_met / 10000, 2),
    number_format((float)$_POST["gramat_corpo"], 0),
    number_format((float)$valor_corpo, 2),
	"1",
    number_format((float)$base1, 2),
    number_format((float)$altura, 2),
    "4");

if ($_POST["corpo"] == "qowad4") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio de costura (Lat+Tampa+Fundo)",
		number_format((float)$quilo_fio_pp, 2),
		number_format((float)$costura_corpo_met, 2),
		number_format((float)$costura_corpo_gramat, 2),
		number_format((float)$costura_corpo, 2),
		"0",0,0,0);
}

if ($_POST["trava_rede"] == "on") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Travas em rede",
		number_format((float)$quilo_travas, 2),
		number_format((float)$valor_trava_met / 10000, 2),
		number_format((float)52, 0),
		number_format((float)$valor_trava, 2),
		"0",
		number_format((float)$h, 2),
		number_format((float)$valor_trava_corte, 2),
		"4");
} else {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Travas",
		number_format((float)$quilo_travas, 2),
		number_format((float)$valor_trava_met / 10000, 2),
		number_format((float)$valor_trava_gramat*1000, 0),
		number_format((float)$valor_trava, 2),
		"1",
		number_format((float)$h, 2),
		number_format((float)$valor_trava_corte, 2),
		"4");
}

if ($_POST["corpo"] == "qowad4") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio de costura (Travas)",
		number_format((float)$quilo_fio_pp, 2),
		number_format((float)$costura_trava_met, 2),
		number_format((float)$costura_trava_gramat, 2),
		number_format((float)$costura_trava, 2),
		"0",0,0,0);
}
if ($_POST["corpo"] == "qowad8") {
		GravaDet (NULL,
			$no_pedido,
			$revisao,
			"2",
			"Fio de costura (Lat+Tampa+Fundo+Travas)",
			number_format((float)$quilo_fio_pp, 2),
			number_format((float)$costura_corpo_met+$costura_trava_met, 2),
			number_format((float)$costura_corpo_gramat, 2),
			number_format((float)$costura_corpo+$costura_trava, 2),
			"0",0,0,0);
}

if ($tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Lat+Tampa+Fundo)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_corpo_ved_met, 2),
		number_format((float)$costura_corpo_ved_gramat, 2),
		number_format((float)$costura_corpo_ved, 2),
		"0",0,0,0);
}

if ($_POST["fio_ved_travas"] == "on") {

	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante nas travas",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$valor_fio_ved_travas_met, 2),
		number_format((float)8, 2),
		number_format((float)$valor_fio_ved_travas, 2),
		"0",0,0,0);
}

$valor_corpo = $valor_corpo+$costura_corpo+$costura_corpo_ved+$valor_trava+$costura_trava+$valor_fio_ved_travas;

/* ============ CORPO PLANO E FORRO TRAVADO ============ */

} elseif ($_POST["corpo"] == "qowadlf") {

$base1 = $_POST["base1"] + 10;
$base2 = $_POST["base2"] + 10;
$altura = $_POST["altura"];
if($_POST["cost_fio_topo"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
//echo "<script>alert('".$altura."');</script>";

$valor_corpo1 = $base1 * 2;
$valor_corpo2 = $base2 * 2;
$valor_corpo = $valor_corpo1 + $valor_corpo2;
$valor_corpo = $valor_corpo * $altura;
$valor_forro_met = $valor_corpo / 10000 ;
$valor_corpo_met = $valor_corpo / 10000 ;

$gramat_corpo = $_POST["gramat_corpo"] / 1000;
$valor_corpo = $quilo_corpo * $gramat_corpo * $valor_corpo_met;


if ($_POST["gramat_forro"] == "65" || $_POST["gramat_forro"] == "70" || $_POST["gramat_forro"] == "147") {
	$gramat_forro = $_POST["gramat_forro"] / 1000;
	$quilo_forro = $quilo_lamin;
} else {
	$gramat_forro = $_POST["gramat_forro"] / 1000;
	$quilo_forro = $quilo_corpo;
}

$valor_forro = $quilo_forro * $gramat_forro * $valor_forro_met;
/*
$b1 = $_POST["base1"];
$b2 = $_POST["base2"];

$b13 = $b1 / 3;
$b23 = $b2 / 3;

$b1 = $b13 * $b13;
$b2 = $b23 * $b23;

$h = $b1 + $b2;
$h = sqrt($h) + 6;
$h = ceil($h);
*/
$h = 60;

if ($_POST["trava_rede"] == "on") {
	$valor_trava_corte = $_POST["altura"] - 10;
	$valor_trava_met = $valor_trava_corte * 4 * 100;
	$valor_trava = $valor_trava_met / 10000;
	$valor_trava = $quilo_travas * 0.060 * $valor_trava;
} else {
	if (strpos(mb_strtoupper($_POST["nome_cliente"]), "BRASKEM") !== false) {
		$valor_trava_gramat = 0.160;
		$valor_trava_corte = $_POST["altura"] - 10;
	} else {
		$valor_trava_gramat = 0.160;
		$valor_trava_corte = $_POST["altura"] - 20;
	}
	$valor_trava_met = $valor_trava_corte * $h * 4;
	$valor_trava = $valor_trava_met / 10000;
	$valor_trava = $quilo_travas * $valor_trava_gramat * $valor_trava;
}

if ($tipo_cost_corpo == "simples" || $tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	$peso_fio_ved_corpo = 0.8;
	$peso_fio_corpo = 0.2177;
	$peso_fio_trava = 0.1119;
} elseif ($tipo_cost_corpo == "dupla") {
	$peso_fio_corpo = 0.4354;
	$peso_fio_trava = 0.2238;
} elseif ($tipo_cost_corpo == "overlock") {
	$peso_fio_corpo = 0.204;
	$peso_fio_trava = 0.096;
}

$costura_corpo = $altura * 4;
$costura_corpo = $costura_corpo + ($valor_corpo1*2) + ($valor_corpo2*2);
$costura_corpo_met = $costura_corpo / 100;
$costura_corpo_gramat = $peso_fio_corpo * 10;
$costura_corpo = $costura_corpo_met * $costura_corpo_gramat / 1000;
$costura_corpo = $costura_corpo * $quilo_fio_pp;

if ($tipo_cost_corpo == "simples1ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved = $costura_corpo_ved + $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_corpo == "simples2ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved = $costura_corpo_ved + $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100 * 2;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
}

$costura_trava_met = ($altura+10) * 8;
$costura_trava_met = $costura_trava_met / 100;
$costura_trava_gramat = $peso_fio_corpo * 10;
$costura_trava = $costura_trava_met * $costura_trava_gramat / 1000;
$costura_trava = $costura_trava * $quilo_fio_pp;

if ($_POST["fio_ved_travas"] == "on") {
	//$valor_fio_ved_travas_met = $_POST["altura"] * 8 * 1.04 / 100; ********* ALTERADO EM 21/02/2018 **********
	$valor_fio_ved_travas_met = ($_POST["altura"]+20) * 8 / 100;
	$valor_fio_ved_travas_met = round($valor_fio_ved_travas_met);
	$valor_fio_ved_travas = $valor_fio_ved_travas_met * 8 * $quilo_fio_vedante;
	$valor_fio_ved_travas = $valor_fio_ved_travas / 1000;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Corpo plano e forro travado",
    "",
    number_format((float)$valor_corpo_met+$valor_forro_met+$valor_trava_met, 2),
    "",
    number_format((float)$valor_corpo+$costura_corpo+$costura_corpo_ved+$valor_forro+$valor_trava+$costura_trava+$valor_fio_ved_travas, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Corpo",
    number_format((float)$quilo_corpo, 2),
    number_format((float)$valor_corpo_met, 2),
    number_format((float)$_POST["gramat_corpo"], 0),
    number_format((float)$valor_corpo, 2),
	"1",
    number_format((float)$base1, 2),
    number_format((float)$altura, 2),
    "4");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Lat+Tampa+Fundo)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_corpo_met, 2),
	number_format((float)$costura_corpo_gramat, 2),
	number_format((float)$costura_corpo, 2),
	"0",0,0,0);

if ($tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Lat+Tampa+Fundo)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_corpo_ved_met, 2),
		number_format((float)$costura_corpo_ved_gramat, 2),
		number_format((float)$costura_corpo_ved, 2),
		"0",0,0,0);
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Forro",
    number_format((float)$quilo_forro, 2),
    number_format((float)$valor_forro_met, 2),
    number_format((float)$_POST["gramat_forro"], 0),
    number_format((float)$valor_forro, 2),
	"1",
    number_format((float)$base1, 2),
    number_format((float)$altura, 2),
    "4");

if ($_POST["trava_rede"] == "on") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Travas em rede",
		number_format((float)$quilo_travas, 2),
		number_format((float)$valor_trava_met / 10000, 2),
		number_format((float)52, 0),
		number_format((float)$valor_trava, 2),
		"0",
		number_format((float)$h, 2),
		number_format((float)$valor_trava_corte, 2),
		"4");
} else {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Travas",
		number_format((float)$quilo_travas, 2),
		number_format((float)$valor_trava_met / 10000, 2),
		number_format((float)$valor_trava_gramat*1000, 0),
		number_format((float)$valor_trava, 2),
		"1",
		number_format((float)$h, 2),
		number_format((float)$valor_trava_corte, 2),
		"4");
}


GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Travas)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_trava_met, 2),
	number_format((float)$costura_trava_gramat, 2),
	number_format((float)$costura_trava, 2),
	"0",0,0,0);

if ($_POST["fio_ved_travas"] == "on") {

	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante nas travas",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$valor_fio_ved_travas_met, 2),
		number_format((float)8, 2),
		number_format((float)$valor_fio_ved_travas, 2),
		"0",0,0,0);
}

$valor_corpo = $valor_corpo+$costura_corpo+$costura_corpo_ved+$valor_forro+$valor_trava+$costura_trava+$valor_fio_ved_travas;

/* ============ CORPO TRAVADO TUBULAR ============ */

} elseif ($_POST["corpo"] == "cowad") {

$base1 = $_POST["base1"];
$base2 = $_POST["base2"];
$altura = $_POST["altura"];
if($_POST["cost_fio_topo"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
//echo "<script>alert('".$altura."');</script>";
	
if($_POST["corpo"] == "gota"){
	$altura = $altura + $_POST["alca_altura"] + 10;
}
$valor_corpo1 = $base1 * 2;
$valor_corpo2 = $base2 * 2;
$valor_corpo = $valor_corpo1 + $valor_corpo2;
$valor_corpo = $valor_corpo * $altura;
$valor_corpo_met = $valor_corpo / 10000 ;

$gramat_corpo = $_POST["gramat_corpo"] / 1000;
$valor_corpo = $quilo_corpo * $gramat_corpo * $valor_corpo_met;

/*
$b1 = $_POST["base1"];
$b2 = $_POST["base2"];

$b13 = $b1 / 3;
$b23 = $b2 / 3;

$b1 = $b13 * $b13;
$b2 = $b23 * $b23;

$h = $b1 + $b2;
$h = sqrt($h) + 6;
$h = ceil($h);
*/
$h = 60;

if ($_POST["trava_rede"] == "on") {
	$valor_trava_corte = $_POST["altura"] - 10;
	$valor_trava_met = $valor_trava_corte * 4 * 100;
	$valor_trava = $valor_trava_met / 10000;
	$valor_trava = $quilo_travas * 0.060 * $valor_trava;
} else {
	if (strpos(mb_strtoupper($_POST["nome_cliente"]), "BRASKEM") !== false) {
		$valor_trava_gramat = 0.160;
		$valor_trava_corte = $_POST["altura"] - 10;
	} else {
		$valor_trava_gramat = 0.160;
		$valor_trava_corte = $_POST["altura"] - 20;
	}
	$valor_trava_met = $valor_trava_corte * $h * 4;
	$valor_trava = $valor_trava_met / 10000;
	$valor_trava = $quilo_travas * $valor_trava_gramat * $valor_trava;
}

if ($tipo_cost_corpo == "simples" || $tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	$peso_fio_ved_corpo = 0.8;
	$peso_fio_corpo = 0.2177;
	$peso_fio_trava = 0.1119;
} elseif ($tipo_cost_corpo == "dupla") {
	$peso_fio_corpo = 0.4354;
	$peso_fio_trava = 0.2238;
} elseif ($tipo_cost_corpo == "overlock") {
	$peso_fio_corpo = 0.204;
	$peso_fio_trava = 0.096;
}

$costura_corpo = ($valor_corpo1*2) + ($valor_corpo2*2) + 25;
$costura_corpo_met = $costura_corpo / 100;
$costura_corpo_gramat = $peso_fio_corpo * 10;
$costura_corpo = $costura_corpo_met * $costura_corpo_gramat / 1000;
$costura_corpo = $costura_corpo * $quilo_fio_pp;

if ($tipo_cost_corpo == "simples1ved") {
	$costura_corpo_ved = $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_corpo == "simples2ved") {
	$costura_corpo_ved = $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100 * 2;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
}

$costura_trava_met = ($altura+10) * 8;
$costura_trava_met = $costura_trava_met / 100;
$costura_trava_gramat = $peso_fio_trava * 10;
$costura_trava = $costura_trava_met * $costura_trava_gramat / 1000;
$costura_trava = $costura_trava * $quilo_fio_pp;

if ($_POST["fio_ved_travas"] == "on") {
	//$valor_fio_ved_travas_met = $_POST["altura"] * 8 * 1.04 / 100; ********* ALTERADO EM 21/02/2018 **********
	$valor_fio_ved_travas_met = ($_POST["altura"]+20) * 8 / 100;
	$valor_fio_ved_travas_met = round($valor_fio_ved_travas_met);
	$valor_fio_ved_travas = $valor_fio_ved_travas_met * 8 * $quilo_fio_vedante;
	$valor_fio_ved_travas = $valor_fio_ved_travas / 1000;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Corpo tubular travado",
	"",
	number_format((float)$valor_corpo_met + ($valor_trava_met / 10000), 2),
	"",
	number_format((float)$valor_corpo+$costura_corpo+$costura_corpo_ved+$valor_trava+$costura_trava+$valor_fio_ved_travas, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Corpo",
	number_format((float)$quilo_corpo, 2),
	number_format((float)$valor_corpo_met, 2),
	number_format((float)$_POST["gramat_corpo"], 0),
	number_format((float)$valor_corpo, 2),
	"1",
    number_format((float)($base1+$base2)*2, 2),
    number_format((float)$altura, 2),
    "1");

if ($_POST["corpo"] == "gota") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio de costura (Fundo)",
		number_format((float)$quilo_fio_pp, 2),
		number_format((float)$costura_corpo_met/2, 2),
		number_format((float)$costura_corpo_gramat, 2),
		number_format((float)$costura_corpo/2, 2),
		"0",0,0,0);
} else {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio de costura (Tampa+Fundo)",
		number_format((float)$quilo_fio_pp, 2),
		number_format((float)$costura_corpo_met, 2),
		number_format((float)$costura_corpo_gramat, 2),
		number_format((float)$costura_corpo, 2),
		"0",0,0,0);
}

if ($_POST["trava_rede"] == "on") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Travas em rede",
		number_format((float)$quilo_travas, 2),
		number_format((float)$valor_trava_met / 10000, 2),
		number_format((float)52, 0),
		number_format((float)$valor_trava, 2),
		"0",
		number_format((float)$h, 2),
		number_format((float)$valor_trava_corte, 2),
		"4");
} else {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Travas",
		number_format((float)$quilo_travas, 2),
		number_format((float)$valor_trava_met / 10000, 2),
		number_format((float)$valor_trava_gramat*1000, 0),
		number_format((float)$valor_trava, 2),
		"1",
		number_format((float)$h, 2),
		number_format((float)$valor_trava_corte, 2),
		"4");
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Travas)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_trava_met, 2),
	number_format((float)$costura_trava_gramat, 2),
	number_format((float)$costura_trava, 2),
	"0",0,0,0);

if ($tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Tampa+Fundo)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_corpo_ved_met, 2),
		number_format((float)$costura_corpo_ved_gramat, 2),
		number_format((float)$costura_corpo_ved, 2),
		"0",0,0,0);
}

if ($_POST["fio_ved_travas"] == "on") {

	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante nas travas",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$valor_fio_ved_travas_met, 2),
		number_format((float)8, 2),
		number_format((float)$valor_fio_ved_travas, 2),
		"0",0,0,0);
}

$valor_corpo = $valor_corpo+$costura_corpo+$costura_corpo_ved;



/* ============ CORPO TUBULAR SIMPLES OU BAG GOTA SINGLE LOOP ============ */

} elseif ($_POST["corpo"] == "gota" || $_POST["corpo"] == "cowa" || $_POST["corpo"] == "cowafi" || $_POST["corpo"] == "cms") {

$base1 = $_POST["base1"];
$base2 = $_POST["base2"];
$altura = $_POST["altura"];
if($_POST["cost_fio_topo"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
//echo "<script>alert('".$altura."');</script>";
if($_POST["corpo"] == "gota"){
	$altura = $altura + $_POST["alca_altura"] + 10;
}
$valor_corpo1 = $base1 * 2;
$valor_corpo2 = $base2 * 2;
$valor_corpo = $valor_corpo1 + $valor_corpo2;
$valor_corpo = $valor_corpo * $altura;
$valor_corpo_met = $valor_corpo / 10000 ;

$gramat_corpo = $_POST["gramat_corpo"] / 1000;
$valor_corpo = $quilo_corpo * $gramat_corpo * $valor_corpo_met;


if ($tipo_cost_corpo == "simples" || $tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	$peso_fio_ved_corpo = 0.8;
	$peso_fio_corpo = 0.2177;
} elseif ($tipo_cost_corpo == "dupla") {
	$peso_fio_corpo = 0.4354;
} elseif ($tipo_cost_corpo == "overlock") {
	$peso_fio_corpo = 0.204;
}


$costura_corpo = ($valor_corpo1*2) + ($valor_corpo2*2) + 25;
$costura_corpo_met = $costura_corpo / 100;
$costura_corpo_gramat = $peso_fio_corpo * 10;
$costura_corpo = $costura_corpo_met * $costura_corpo_gramat / 1000;
$costura_corpo = $costura_corpo * $quilo_fio_pp;

if ($tipo_cost_corpo == "simples1ved") {
	$costura_corpo_ved = $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_corpo == "simples2ved") {
	$costura_corpo_ved = $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100 * 2;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
}



GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Corpo tubular simples",
	"",
	number_format((float)$valor_corpo_met, 2),
	"",
	number_format((float)$valor_corpo+$costura_corpo+$costura_corpo_ved, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Corpo",
	number_format((float)$quilo_corpo, 2),
	number_format((float)$valor_corpo_met, 2),
	number_format((float)$_POST["gramat_corpo"], 0),
	number_format((float)$valor_corpo, 2),
	"1",
    number_format((float)($base1+$base2)*2, 2),
    number_format((float)$altura, 2),
    "1");

if ($_POST["corpo"] == "gota") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio de costura (Fundo)",
		number_format((float)$quilo_fio_pp, 2),
		number_format((float)$costura_corpo_met/2, 2),
		number_format((float)$costura_corpo_gramat, 2),
		number_format((float)$costura_corpo/2, 2),
		"0",0,0,0);
} else {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio de costura (Tampa+Fundo)",
		number_format((float)$quilo_fio_pp, 2),
		number_format((float)$costura_corpo_met, 2),
		number_format((float)$costura_corpo_gramat, 2),
		number_format((float)$costura_corpo, 2),
		"0",0,0,0);
}
	
if ($tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Tampa+Fundo)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_corpo_ved_met, 2),
		number_format((float)$costura_corpo_ved_gramat, 2),
		number_format((float)$costura_corpo_ved, 2),
		"0",0,0,0);
}

$valor_corpo = $valor_corpo+$costura_corpo+$costura_corpo_ved;

/* ============ CORPO TUBULAR COM FORRO ============ */

} elseif ($_POST["corpo"] == "cowaf" || $_POST["corpo"] == "cowa2") {

$base1 = $_POST["base1"];
$base2 = $_POST["base2"];
$altura = $_POST["altura"];
if($_POST["cost_fio_topo"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
//echo "<script>alert('".$altura."');</script>";

$valor_corpo1 = $base1 * 2;
$valor_corpo2 = $base2 * 2;
$valor_corpo = $valor_corpo1 + $valor_corpo2;
$valor_corpo = $valor_corpo * $altura;

$valor_forro_met = $valor_corpo / 10000 ;
$valor_corpo_met = $valor_corpo / 10000 ;

$gramat_corpo = $_POST["gramat_corpo"] / 1000;
$valor_corpo = $quilo_corpo * $gramat_corpo * $valor_corpo_met;

if ($_POST["gramat_forro"] == "65" || $_POST["gramat_forro"] == "70" || $_POST["gramat_forro"] == "147") {
	$gramat_forro = $_POST["gramat_forro"] / 1000;
	$quilo_forro = $quilo_lamin;
} else {
	$gramat_forro = $_POST["gramat_forro"] / 1000;
	$quilo_forro = $quilo_corpo;
}

$valor_forro = $quilo_forro * $gramat_forro * $valor_forro_met;


if ($tipo_cost_corpo == "simples" || $tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	$peso_fio_ved_corpo = 0.8;
	$peso_fio_corpo = 0.2177;
} elseif ($tipo_cost_corpo == "dupla") {
	$peso_fio_corpo = 0.4354;
} elseif ($tipo_cost_corpo == "overlock") {
	$peso_fio_corpo = 0.204;
}


$costura_corpo = ($valor_corpo1*2) + ($valor_corpo2*2) + 25;
$costura_corpo_met = $costura_corpo / 100;
$costura_corpo_gramat = $peso_fio_corpo * 10;
$costura_corpo = $costura_corpo_met * $costura_corpo_gramat / 1000;
$costura_corpo = $costura_corpo * $quilo_fio_pp;

if ($tipo_cost_corpo == "simples1ved") {
	$costura_corpo_ved = $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_corpo == "simples2ved") {
	$costura_corpo_ved = $valor_corpo1 + $valor_corpo2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100 * 2;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
}


GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Corpo tubular com forro",
	"",
	number_format((float)$valor_corpo_met+$valor_forro_met, 2),
	"",
	number_format((float)$valor_corpo+$valor_forro+$costura_corpo+$costura_corpo_ved, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Corpo",
	number_format((float)$quilo_corpo, 2),
	number_format((float)$valor_corpo_met, 2),
	number_format((float)$_POST["gramat_corpo"], 0),
	number_format((float)$valor_corpo, 2),
	"1",
    number_format((float)($base1+$base2)*2, 2),
    number_format((float)$altura, 2),
    "1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Forro",
	number_format((float)$quilo_forro, 2),
	number_format((float)$valor_forro_met, 2),
	number_format((float)$_POST["gramat_forro"], 0),
	number_format((float)$valor_forro, 2),
	"1",
    number_format((float)($base1+$base2)*2, 2),
    number_format((float)$altura, 2),
    "1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Tampa+Fundo)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_corpo_met, 2),
	number_format((float)$costura_corpo_gramat, 2),
	number_format((float)$costura_corpo, 2),
	"0",0,0,0);

if ($tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Tampa+Fundo)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_corpo_ved_met, 2),
		number_format((float)$costura_corpo_ved_gramat, 2),
		number_format((float)$costura_corpo_ved, 2),
		"0",0,0,0);
}

$valor_corpo = $valor_corpo+$valor_forro+$costura_corpo+$costura_corpo_ved;

/* ============ CORPO PAINEL U (OU COM FORRO) ============ */

} elseif ($_POST["corpo"] == "qowac" || $_POST["corpo"] == "qowacf") {

$base1 = $_POST["base1"] + 10;
$base2 = $_POST["base2"] + 10;
$altura = $_POST["altura"];
if($_POST["cost_fio_topo"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
/*
if($_POST["cost_fio_base"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
*/
//echo "<script>alert('".$altura."');</script>";

if ($base1 != $base2) {
	if ($base1 < $base2) {
		$base_maior = $base2;
		$base_menor = $base1;
	} else {
		$base_maior = $base1;
		$base_menor = $base2;
	}
} else {
	$base_maior = $base1;
	$base_menor = $base2;
}

$valor_corpo1 = $altura + ($base_maior-10) + $altura;
$valor_corpo1 = $valor_corpo1 * $base_menor;

$valor_corpo2 = $base_maior * $altura;
$valor_corpo2 = $valor_corpo2 * 2;

$valor_corpo_met1 = $valor_corpo1 / 10000 ;
$valor_corpo_met2 = $valor_corpo2 / 10000 ;

$valor_corpo = $valor_corpo1 + $valor_corpo2;
$valor_corpo_met = $valor_corpo / 10000 ;

$gramat_corpo = $_POST["gramat_corpo"] / 1000;
$valor_corpo1 = $quilo_corpo * $gramat_corpo * $valor_corpo_met1;
$valor_corpo2 = $quilo_corpo * $gramat_corpo * $valor_corpo_met2;
$valor_corpo = $quilo_corpo * $gramat_corpo * $valor_corpo_met;

if ($_POST["corpo"] == "qowacf") {

	$valor_forro1 = $valor_corpo_met1;
	$valor_forro2 = $valor_corpo_met2;

	if ($_POST["gramat_forro"] == "65" || $_POST["gramat_forro"] == "70" || $_POST["gramat_forro"] == "147") {
		$gramat_forro = $_POST["gramat_forro"] / 1000;
		$quilo_forro = $quilo_lamin;
	} else {
		$gramat_forro = $_POST["gramat_forro"] / 1000;
		$quilo_forro = $quilo_corpo;
	}

	$valor_forro1 = $quilo_forro * $gramat_forro * $valor_forro1;
	$valor_forro2 = $quilo_forro * $gramat_forro * $valor_forro2;

}

if ($tipo_cost_corpo == "simples" || $tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	$peso_fio_ved_corpo = 0.8;
	$peso_fio_corpo = 0.2177;
} elseif ($tipo_cost_corpo == "dupla") {
	$peso_fio_corpo = 0.4354;
} elseif ($tipo_cost_corpo == "overlock") {
	$peso_fio_corpo = 0.204;
}

$costura_corpo = $_POST["base1"] + $_POST["base2"];
$costura_corpo = $costura_corpo * 2;
$costura_corpo = $costura_corpo + 25;
$costura_corpo = $costura_corpo + $_POST["altura"] + $_POST["base2"] + $_POST["altura"] + 10;
$costura_corpo = $costura_corpo + $_POST["altura"] + $_POST["base2"] + $_POST["altura"] + 10;

$costura_corpo_met = $costura_corpo / 100;
$costura_corpo_gramat = $peso_fio_corpo * 10;
$costura_corpo = $costura_corpo_met * $costura_corpo_gramat / 1000;
$costura_corpo = $costura_corpo * $quilo_fio_pp;

if ($tipo_cost_corpo == "simples1ved") {
	$costura_corpo_ved = $base_maior * 4;
	$costura_corpo_ved += $base_menor * 2;
	$costura_corpo_ved += $altura * 4;
	$costura_corpo_ved_met = $costura_corpo_ved / 100;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_corpo == "simples2ved") {
	$costura_corpo_ved = $base_maior * 4;
	$costura_corpo_ved += $base_menor * 2;
	$costura_corpo_ved += $altura * 4;
	$costura_corpo_ved_met = $costura_corpo_ved / 100 * 2;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
}


if ($_POST["corpo"] == "qowacf") {
GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Corpo painel U com forro",
	"",
	number_format((float)$valor_corpo_met, 2),
	"",
	number_format((float)$valor_corpo+$costura_corpo+$costura_corpo_ved+$valor_forro1+$valor_forro2, 2),
	"1",0,0,0);
} else {
GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Corpo painel U",
	"",
	number_format((float)$valor_corpo_met, 2),
	"",
	number_format((float)$valor_corpo+$costura_corpo+$costura_corpo_ved, 2),
	"1",0,0,0);
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Corpo",
	number_format((float)$quilo_corpo, 2),
	number_format((float)$valor_corpo_met1, 2),
	number_format((float)$_POST["gramat_corpo"], 0),
	number_format((float)$valor_corpo1, 2),
	"1",
    number_format((float)$base_menor, 2),
    number_format((float)($altura*2)+($base_maior-10), 2),
    "1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Laterais",
	number_format((float)$quilo_corpo, 2),
	number_format((float)$valor_corpo_met2, 2),
	number_format((float)$_POST["gramat_corpo"], 0),
	number_format((float)$valor_corpo2, 2),
	"1",
    number_format((float)($base_maior-10), 2),
    number_format((float)$altura, 2),
    "2");

if ($_POST["corpo"] == "qowacf") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Forro",
		number_format((float)$quilo_forro, 2),
		number_format((float)$valor_corpo_met1, 2),
		number_format((float)$_POST["gramat_forro"], 0),
		number_format((float)$valor_forro1, 2),
/*		number_format((float)$quilo_forro, 2),
		number_format((float)$valor_corpo_met, 2),
		number_format((float)$_POST["gramat_forro"], 0),
		number_format((float)$valor_forro, 2),*/
		"1",
		number_format((float)$base_menor, 2),
		number_format((float)($altura*2)+($base_maior-10), 2),
		"1");
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Forro laterais",
		number_format((float)$quilo_forro, 2),
		number_format((float)$valor_corpo_met2, 2),
		number_format((float)$_POST["gramat_forro"], 0),
		number_format((float)$valor_forro2, 2),
		"1",
		number_format((float)($base_maior-10), 2),
		number_format((float)$altura, 2),
		"2");
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Painel+Lat+Tampa)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_corpo_met, 2),
	number_format((float)$costura_corpo_gramat, 2),
	number_format((float)$costura_corpo, 2),
	"0",0,0,0);

if ($tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Painel+Lat+Tampa)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_corpo_ved_met, 2),
		number_format((float)$costura_corpo_ved_gramat, 2),
		number_format((float)$costura_corpo_ved, 2),
		"0",0,0,0);
}

$valor_corpo = $valor_corpo+$costura_corpo+$costura_corpo_ved+$valor_forro;

/* ============ CORPO PORTA-ENSACADO ============ */


} elseif ($_POST["corpo"] == "rof") {

$base1 = $_POST["base1"] + 10;
$base2 = $_POST["base2"] + 10;
$altura = $_POST["altura"];
if($_POST["cost_fio_topo"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on") {
	$altura += 3;
} else {
	$altura += 5;
}

/*
$valor_corpo1 = $altura + $_POST["base2"] + $altura + $_POST["base2"];
$valor_corpo1 = $valor_corpo1 * $base1;

$valor_corpo2 = $base2 * $altura;
$valor_corpo2 = $valor_corpo2 * 2;
*/
$valor_corpo1 = ($base1*$altura)*2;
$valor_corpo2 = ($base2*$altura)*2;

$valor_corpo = $valor_corpo1 + $valor_corpo2;
$valor_corpo_met = $valor_corpo / 10000 ;

//echo "<script>alert('".$valor_corpo_met."');</script>";

$gramat_corpo = $_POST["gramat_corpo"] / 1000;
$valor_corpo = $quilo_corpo * $gramat_corpo * $valor_corpo_met;


if ($tipo_cost_corpo == "simples" || $tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	$peso_fio_ved_corpo = 0.8;
	$peso_fio_corpo = 0.2177;
} elseif ($tipo_cost_corpo == "dupla") {
	$peso_fio_corpo = 0.4354;
} elseif ($tipo_cost_corpo == "overlock") {
	$peso_fio_corpo = 0.204;
}

$costura_corpo = $_POST["base1"] + $_POST["base2"];
$costura_corpo = $costura_corpo * 2;
$costura_corpo = $costura_corpo + 25;
$costura_corpo = $costura_corpo + $_POST["altura"] + $_POST["base2"] + $_POST["altura"] + 10;
$costura_corpo = $costura_corpo + $_POST["altura"] + $_POST["base2"] + $_POST["altura"] + 10;

$costura_corpo_met = $costura_corpo / 100;
$costura_corpo_gramat = $peso_fio_corpo * 10;
$costura_corpo = $costura_corpo_met * $costura_corpo_gramat / 1000;
$costura_corpo = $costura_corpo * $quilo_fio_pp;

if ($tipo_cost_corpo == "simples1ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved += $base1 * 2 + $base2 * 2;
	$costura_corpo_ved += $base1 * 2 + $base2 * 2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_corpo == "simples2ved") {
	$costura_corpo_ved = $altura * 4;
	$costura_corpo_ved += $base1 * 2 + $base2 * 2;
	$costura_corpo_ved += $base1 * 2 + $base2 * 2;
	$costura_corpo_ved_met = $costura_corpo_ved / 100 * 2;
	$costura_corpo_ved_gramat = $peso_fio_ved_corpo * 10;
	$costura_corpo_ved = $costura_corpo_ved_met * $costura_corpo_ved_gramat / 1000;
	$costura_corpo_ved = $costura_corpo_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Corpo porta-ensacado",
	"",
	number_format((float)$valor_corpo_met, 2),
	"",
	number_format((float)$valor_corpo+$costura_corpo+$costura_corpo_ved, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Corpo",
	number_format((float)$quilo_corpo, 2),
	number_format((float)$valor_corpo_met, 2),
	number_format((float)$_POST["gramat_corpo"], 0),
	number_format((float)$valor_corpo, 2),
	"1",
    number_format((float)$base1, 2),
    number_format((float)$altura, 2),
    "4");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Painel+Lat+Tampa)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_corpo_met, 2),
	number_format((float)$costura_corpo_gramat, 2),
	number_format((float)$costura_corpo, 2),
	"0",0,0,0);

if ($tipo_cost_corpo == "simples1ved" || $tipo_cost_corpo == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Painel+Lat+Tampa)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_corpo_ved_met, 2),
		number_format((float)$costura_corpo_ved_gramat, 2),
		number_format((float)$costura_corpo_ved, 2),
		"0",0,0,0);
}

$valor_corpo = $valor_corpo+$costura_corpo+$costura_corpo_ved;

}

/* ============ CARGA TIPO SAIA E SAIA AFUNILADA ============ */

if ($_POST["carga"] == "c_saia" || $_POST["carga"] == "c_afunilada") {

$base1 = $_POST["base1"] + 1.5;
$base2 = $_POST["base2"] + 1.5;
$alt_saia = $_POST["carga2"] + 5;

/*
if ($_POST["saia"] == "saia_leve") {
	$gramat_saia = 0.070;
} elseif ($_POST["saia"] == "saia_pesada") {
	$gramat_saia = 0.13;
}
*/

$valor_carga = $base1 + $base2;
$valor_carga = $valor_carga * 2 * $alt_saia;
$valor_carga_met = $valor_carga / 10000 ;

$valor_carga = $quilo_carga * $gramat_saia * $valor_carga_met;

$cadarco_carga = $quilo_int * 0.006 * 1.2;

if ($tipo_cost_enchim == "simples" || $tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
	$peso_fio_ved_carga = 0.8;
	$peso_fio_carga = 0.1119;
} elseif ($tipo_cost_enchim == "dupla") {
	$peso_fio_carga = 0.2238;
}

if ($_POST["carga"] == "c_afunilada") {
	$costura_carga1 = $_POST["base1"] - $_POST["carga1"];
	$costura_carga1 = $costura_carga1 / 2;
	$costura_carga1 = $costura_carga1 * $costura_carga1	;
	$alt_saia1 = $_POST["carga2"] * $_POST["carga2"];
	$costura_carga1 = $alt_saia1 + $costura_carga1;
	$costura_carga1 = sqrt($costura_carga1) + 10;
	$costura_carga1 = $costura_carga1 * 2;

	$costura_carga2 = $_POST["base2"] - $_POST["carga1"];
	$costura_carga2 = $costura_carga2 / 2;
	$costura_carga2 = $costura_carga2 * $costura_carga2	;
	$alt_saia2 = $_POST["carga2"] * $_POST["carga2"];
	$costura_carga2 = $alt_saia2 + $costura_carga2;
	$costura_carga2 = sqrt($costura_carga2) + 10;
	$costura_carga2 = $costura_carga2 * 2;

	$costura_carga = $costura_carga1 + $costura_carga2;
}

if ($_POST["carga"] == "c_saia") {
	$costura_carga = $_POST["base1"] * 2 + $_POST["base2"] * 2 + 25;
	$costura_carga = $alt_saia * 4 + $costura_carga;
}

$costura_carga_met = $costura_carga / 100;
$costura_carga_gramat = $peso_fio_carga * 10;
$costura_carga = $costura_carga_met * $costura_carga_gramat / 1000;
$costura_carga = $costura_carga * $quilo_fio_pp;

if ($tipo_cost_enchim == "simples1ved") {
	if ($_POST["carga"] == "c_saia") {
		$costura_carga_ved = $alt_saia * 4;
	} elseif ($_POST["carga"] == "c_afunilada") {
		$costura_carga_ved = $_POST["base1"] - $_POST["carga1"];
		$costura_carga_ved = $costura_carga_ved / 2;
		$costura_carga_ved = $costura_carga_ved * $costura_carga_ved;
		$costura_carga_ved2 = $_POST["carga2"] * $_POST["carga2"];
		$costura_carga_ved = $costura_carga_ved2 + $costura_carga_ved;
		$costura_carga_ved = sqrt($costura_carga_ved) + 10;
		$costura_carga_ved = $costura_carga_ved * 4;
	}
	$costura_carga_ved_met = $costura_carga_ved / 100;
	$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
	$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
	$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
} elseif ($tipo_cost_enchim == "simples2ved") {
	if ($_POST["carga"] == "c_saia") {
		$costura_carga_ved = $alt_saia * 4;
	} elseif ($_POST["carga"] == "c_afunilada") {
		$costura_carga_ved = $_POST["base1"] - $_POST["carga1"];
		$costura_carga_ved = $costura_carga_ved / 2;
		$costura_carga_ved = $costura_carga_ved * $costura_carga_ved;
		$costura_carga_ved2 = $_POST["carga2"] * $_POST["carga2"];
		$costura_carga_ved = $costura_carga_ved2 + $costura_carga_ved;
		$costura_carga_ved = sqrt($costura_carga_ved) + 10;
		$costura_carga_ved = $costura_carga_ved * 4;
	}
	$costura_carga_ved_met = $costura_carga_ved / 100 * 2;
	$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
	$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
	$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de enchimento",
	"",
	number_format((float)$valor_carga_met, 2),
	"",
	number_format((float)$valor_carga + $cadarco_carga + $costura_carga + $costura_carga_ved, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Saia superior",
	number_format((float)$quilo_carga, 2),
	number_format((float)$valor_carga_met, 2),
	number_format((float)$gramat_saia*1000+$saia_pesada, 0),
	number_format((float)$valor_carga, 2),
	"1",
	number_format((float)$alt_saia, 2),
	number_format((float)($base1+$base2)*2, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da saia",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_carga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Carga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_carga_met, 2),
	number_format((float)$costura_carga_gramat, 2),
	number_format((float)$costura_carga, 2),
	"0",0,0,0);

if ($tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Carga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_carga_ved_met, 2),
		number_format((float)$costura_carga_ved_gramat, 2),
		number_format((float)$costura_carga_ved, 2),
		"0",0,0,0);
}

$valor_carga = $valor_carga + $cadarco_carga + $costura_carga + $costura_carga_ved;


/* ============ SEM SISTEMA DE ENCHIMENTO ============ */

} elseif ($_POST["carga"] == "vazio") {


		if($_POST["gramat_tampa"] != "") {
			$gramat_tampa = $_POST["gramat_tampa"]/1000;
		} else {
			$gramat_tampa = 0;
		}
		//$gramat_tampa = $_POST["gramat_corpo"] / 1000;


		$base1 = $_POST["base1"] + 10;
		$base2 = $_POST["base2"] + 10;

		$area_tampa = $base1 * $base2;
		$area_tampa_met = $area_tampa / 10000;
		$area_tampa = $quilo_carga * $area_tampa_met * $gramat_tampa;

		$valor_carga = $area_tampa;

if ($_POST["corpo"] == "gota") {

	$costura_slit_met = 0.3 + (($base_menor+10)/10);
	$costura_slit_gramat = 0.2177*10;
	$costura_slit = $quilo_fio*$costura_slit_met*$costura_slit_gramat/1000;
	
/*
	echo "<pre>";
	echo "PRECO_QUILO: ".$quilo_fio."\n\n";
	echo "METRAGEM: ".$costura_slit_met."\n\n";
	echo "GRAMAT: ".$costura_slit_gramat."\n\n";
	echo "VALOR: ".$costura_slit."\n\n";
	echo "</pre>";
	die();
*/
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"1",
		"Slit",
		'',
		number_format((float)0.18, 2),
		'',
		number_format((float)($quilo_int*0.18*0.147)+$costura_slit, 2),
		"1",0,0,0);

	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Slit",
		number_format((float)$quilo_int, 2),
		number_format((float)0.18, 2),
		number_format((float)0.147*1000, 0),
		number_format((float)$quilo_int*0.18*0.147, 2),
		"1",0,0,0);

	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio de costura (Alça+Slit)",
		number_format((float)$quilo_fio, 2),
		number_format((float)$costura_slit_met, 2),
		number_format((float)$costura_slit_gramat, 2),
		number_format((float)$costura_slit, 2),
		"0",0,0,0);
	$valor_carga = $quilo_int*0.18*0.147 + $costura_slit;

} else {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"1",
		"Tampa aberta",
		"",
		"",
		"",
		"",
		"1",0,0,0);
}
/* ============ CARGA TIPO VÁLVULA SIMPLES ============ */

} elseif ($_POST["carga"] == "c_simples") {


	if ($tipo_cost_enchim == "simples" || $tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
		$peso_fio_ved_carga = 0.8;
		$peso_fio_carga = 0.1119;
	} elseif ($tipo_cost_enchim == "dupla") {
		$peso_fio_carga = 0.2238;
	}


	if ($_POST["c_quadrado"] == "c_quadrado") {

		$base1 = $_POST["base1"];
		$base2 = $_POST["base2"];
		if($_POST["cost_fio_topo"] == "on") {
			$base1 += 6;
			$base2 += 6;
		} else {
			$base1 += 10;
			$base2 += 10;
		}

		if ($base1 != $base2) {
			if ($base1 < $base2) {
				$base_maior = $base2;
				$base_menor = $base1;
			} else {
				$base_maior = $base1;
				$base_menor = $base2;
			}
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}

		$area_tampa = $base1 * $base2;
		$area_tampa_met = $area_tampa / 10000;
		$area_tampa = $quilo_carga * $area_tampa_met * $gramat_tampa;
		
		$carga1 = $_POST["carga1"] * 4;
		$carga1 = $carga1 + 5;
		$carga2 = $_POST["carga2"] + 5;

		$valvula = $carga1 * $carga2;
		$valvula_met = $valvula / 10000;
		$valvula = $quilo_v * $valvula_met * $gramat_valv;

		$cadarco_carga = $quilo_int * 0.006 * 1.2;


		$costura_carga = $_POST["carga1"] * 4;
		$costura_carga = $costura_carga + 25;
		$costura_carga = $costura_carga + $_POST["carga2"] + 10;
		$costura_carga_met = $costura_carga / 100;
		$costura_carga_gramat = $peso_fio_carga * 10;
		$costura_carga = $costura_carga_met * $costura_carga_gramat / 1000;
		$costura_carga = $costura_carga * $quilo_fio_pp;

		if ($tipo_cost_enchim == "simples1ved") {
			$costura_carga_ved = $_POST["carga1"] * 4;
			$costura_carga_ved += $_POST["carga2"] + 5;
			$costura_carga_ved_met = $costura_carga_ved / 100;
			$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
			$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
			$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
		} elseif ($tipo_cost_enchim == "simples2ved") {
			$costura_carga_ved = $_POST["carga1"] * 4;
			$costura_carga_ved += $_POST["carga2"] + 5;
			$costura_carga_ved_met = $costura_carga_ved / 100 * 2;
			$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
			$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
			$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
		}

		$valor_carga = $area_tampa + $valvula + $cadarco_carga + $costura_carga + $costura_carga_ved;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de enchimento",
	"",
	number_format((float)$valvula_met+$area_tampa_met, 2),
	"",
	number_format((float)$valor_carga, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Tampa",
	number_format((float)$quilo_carga, 2),
	number_format((float)$area_tampa_met, 2),
	number_format((float)$gramat_tampa*1000, 0),
	number_format((float)$area_tampa, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Válvula superior",
	number_format((float)$quilo_v, 2),
	number_format((float)$valvula_met, 2),
	number_format((float)$gramat_valv*1000, 0),
	number_format((float)$valvula, 2),
	"1",
	number_format((float)$carga2, 2),
	number_format((float)$carga1, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da válvula superior",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_carga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Carga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_carga_met, 2),
	number_format((float)$costura_carga_gramat, 2),
	number_format((float)$costura_carga, 2),
	"0",0,0,0);

if ($tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Carga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_carga_ved_met, 2),
		number_format((float)$costura_carga_ved_gramat, 2),
		number_format((float)$costura_carga_ved, 2),
		"0",0,0,0);
}

	} else {

		$base1 = $_POST["base1"];
		$base2 = $_POST["base2"];
		if($_POST["cost_fio_topo"] == "on") {
			$base1 += 6;
			$base2 += 6;
		} else {
			$base1 += 10;
			$base2 += 10;
		}

		if ($base1 != $base2) {
			if ($base1 < $base2) {
				$base_maior = $base2;
				$base_menor = $base1;
			} else {
				$base_maior = $base1;
				$base_menor = $base2;
			}
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}

		$area_tampa = $base1 * $base2;
		$area_tampa_met = $area_tampa / 10000;
		$area_tampa = $quilo_carga * $area_tampa_met * $gramat_tampa;
		
		$carga1 = $_POST["carga1"] * M_PI;
		$carga1 = $carga1 + 16;
		$carga2 = $_POST["carga2"] + 5;

		$valvula = $carga1 * $carga2;
		$valvula_met = $valvula / 10000;
		$valvula = $quilo_v * $valvula_met * $gramat_valv;

		$cadarco_carga = $quilo_int * 0.006 * 1.2;

		$costura_carga = $_POST["carga1"] * M_PI;
		$costura_carga = $costura_carga + 25;
		$costura_carga = $costura_carga + $_POST["carga2"] + 10;
		$costura_carga_met = $costura_carga / 100;
		$costura_carga_gramat = $peso_fio_carga * 10;
		$costura_carga = $costura_carga_met * $costura_carga_gramat / 1000;
		$costura_carga = $costura_carga * $quilo_fio_pp;

		if ($tipo_cost_enchim == "simples1ved") {
			$costura_carga_ved = $_POST["carga1"] * M_PI;
			$costura_carga_ved += $_POST["carga2"] + 5;
			$costura_carga_ved_met = $costura_carga_ved / 100;
			$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
			$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
			$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
		} elseif ($tipo_cost_enchim == "simples2ved") {
			$costura_carga_ved = $_POST["carga1"] * M_PI;
			$costura_carga_ved += $_POST["carga2"] + 5;
			$costura_carga_ved_met = $costura_carga_ved / 100 * 2;
			$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
			$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
			$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
		}

		$valor_carga = $area_tampa + $valvula + $cadarco_carga + $costura_carga + $costura_carga_ved;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de enchimento",
	"",
	number_format((float)$valvula_met+$area_tampa_met, 2),
	"",
	number_format((float)$valor_carga, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Tampa",
	number_format((float)$quilo_carga, 2),
	number_format((float)$area_tampa_met, 2),
	number_format((float)$gramat_tampa*1000, 0),
	number_format((float)$area_tampa, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Válvula superior",
	number_format((float)$quilo_v, 2),
	number_format((float)$valvula_met, 2),
	number_format((float)$gramat_valv*1000, 0),
	number_format((float)$valvula, 2),
	"1",
	number_format((float)$carga2, 2),
	number_format((float)$carga1, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da válvula superior",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_carga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Carga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_carga_met, 2),
	number_format((float)$costura_carga_gramat, 2),
	number_format((float)$costura_carga, 2),
	"0",0,0,0);

if ($tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Carga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_carga_ved_met, 2),
		number_format((float)$costura_carga_ved_gramat, 2),
		number_format((float)$costura_carga_ved, 2),
		"0",0,0,0);
}

	}

/* ============ CARGA TIPO VÁLVULA SIMPLES AFUNILADA ============ */

} elseif ($_POST["carga"] == "c_simples_afunilada") {

	if ($tipo_cost_enchim == "simples" || $tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
		$peso_fio_ved_carga = 0.8;
		$peso_fio_carga = 0.1119;
	} elseif ($tipo_cost_enchim == "dupla") {
		$peso_fio_carga = 0.2238;
	}


	if ($_POST["c_quadrado"] == "c_quadrado") {

		$base1 = $_POST["base1"];
		$base2 = $_POST["base2"];
		if($_POST["cost_fio_topo"] == "on") {
			$base1 += 21;
			$base2 += 21;
		} else {
			$base1 += 25;
			$base2 += 25;
		}

		if ($base1 != $base2) {
			if ($base1 < $base2) {
				$base_maior = $base2;
				$base_menor = $base1;
			} else {
				$base_maior = $base1;
				$base_menor = $base2;
			}
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}

		$area_tampa = $base1 * $base2;
		$area_tampa_met = $area_tampa / 10000;
		$area_tampa = $quilo_carga * $area_tampa_met * $gramat_tampa;
		
		$carga1 = $_POST["carga1"] * 4;
		$carga1 = $carga1 + 5;
		$carga2 = $_POST["carga2"] + 6;

		$valvula = $carga1 * $carga2;
		$valvula_met = $valvula / 10000;
		$valvula = $quilo_v * $valvula_met * $gramat_valv;

		$cadarco_carga = $quilo_int * 0.006 * 1.2;


		$costura_carga1 = $_POST["base1"] - $_POST["carga1"];
		$costura_carga1 = $costura_carga1 / 2;
		$costura_carga1 = $costura_carga1 * $costura_carga1	;
		$alt_saia1 = $_POST["carga2"] * $_POST["carga2"];
		$costura_carga1 = $alt_saia1 + $costura_carga1;
		$costura_carga1 = sqrt($costura_carga1) + 10;
		$costura_carga1 = $costura_carga1 * 2;

		$costura_carga2 = $_POST["base2"] - $_POST["carga1"];
		$costura_carga2 = $costura_carga2 / 2;
		$costura_carga2 = $costura_carga2 * $costura_carga2	;
		$alt_saia2 = $_POST["carga2"] * $_POST["carga2"];
		$costura_carga2 = $alt_saia2 + $costura_carga2;
		$costura_carga2 = sqrt($costura_carga2) + 10;
		$costura_carga2 = $costura_carga2 * 2;

		$costura_carga = $_POST["carga1"] * 4;
		$costura_carga = $costura_carga + 25;
		$costura_carga = $costura_carga + $_POST["carga2"] + 10;

		$costura_carga = $costura_carga + $costura_carga1 + $costura_carga2;
		$costura_carga_met = $costura_carga / 100;
		$costura_carga_gramat = $peso_fio_carga * 10;
		$costura_carga = $costura_carga_met * $costura_carga_gramat / 1000;
		$costura_carga = $costura_carga * $quilo_fio_pp;

		if ($tipo_cost_enchim == "simples1ved") {
			$costura_carga_ved = $_POST["carga1"] * 4;
			$costura_carga_ved += $_POST["carga2"] + 5;
			$costura_carga_ved += $costura_carga1 + $costura_carga2;
			$costura_carga_ved_met = $costura_carga_ved / 100;
			$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
			$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
			$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
		} elseif ($tipo_cost_enchim == "simples2ved") {
			$costura_carga_ved = $_POST["carga1"] * 4;
			$costura_carga_ved += $_POST["carga2"] + 5;
			$costura_carga_ved += $costura_carga1 + $costura_carga2;
			$costura_carga_ved_met = $costura_carga_ved / 100 * 2;
			$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
			$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
			$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
		}

		$valor_carga = $area_tampa + $valvula + $cadarco_carga + $costura_carga + $costura_carga_ved;


GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de enchimento",
	"",
	number_format((float)$valvula_met+$area_tampa_met, 2),
	"",
	number_format((float)$valor_carga, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Tampa",
	number_format((float)$quilo_carga, 2),
	number_format((float)$area_tampa_met, 2),
	number_format((float)$gramat_tampa*1000, 0),
	number_format((float)$area_tampa, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Válvula superior",
	number_format((float)$quilo_v, 2),
	number_format((float)$valvula_met, 2),
	number_format((float)$gramat_valv*1000, 0),
	number_format((float)$valvula, 2),
	"1",
	number_format((float)$carga2, 2),
	number_format((float)$carga1, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da válvula superior",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_carga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Carga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_carga_met, 2),
	number_format((float)$costura_carga_gramat, 2),
	number_format((float)$costura_carga, 2),
	"0",0,0,0);

if ($tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Carga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_carga_ved_met, 2),
		number_format((float)$costura_carga_ved_gramat, 2),
		number_format((float)$costura_carga_ved, 2),
		"0",0,0,0);
}

	} else {

		$base1 = $_POST["base1"];
		$base2 = $_POST["base2"];
		if($_POST["cost_fio_topo"] == "on") {
			$base1 += 21;
			$base2 += 21;
		} else {
			$base1 += 25;
			$base2 += 25;
		}

		if ($base1 != $base2) {
			if ($base1 < $base2) {
				$base_maior = $base2;
				$base_menor = $base1;
			} else {
				$base_maior = $base1;
				$base_menor = $base2;
			}
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}

		$area_tampa = $base1 * $base2;
		$area_tampa_met = $area_tampa / 10000;
		$area_tampa = $quilo_carga * $area_tampa_met * $gramat_tampa;
		
		$carga1 = $_POST["carga1"] * M_PI;
		$carga1 = $carga1 + 16;
		$carga2 = $_POST["carga2"] + 5;

		$valvula = $carga1 * $carga2;
		$valvula_met = $valvula / 10000;
		$valvula = $quilo_v * $valvula_met * $gramat_valv;

		$cadarco_carga = $quilo_int * 0.006 * 1.2;

		$costura_carga1 = $_POST["base1"] - $_POST["carga1"];
		$costura_carga1 = $costura_carga1 / 2;
		$costura_carga1 = $costura_carga1 * $costura_carga1	;
		$alt_saia1 = $_POST["carga2"] * $_POST["carga2"];
		$costura_carga1 = $alt_saia1 + $costura_carga1;
		$costura_carga1 = sqrt($costura_carga1) + 10;
		$costura_carga1 = $costura_carga1 * 2;

		$costura_carga2 = $_POST["base2"] - $_POST["carga1"];
		$costura_carga2 = $costura_carga2 / 2;
		$costura_carga2 = $costura_carga2 * $costura_carga2	;
		$alt_saia2 = $_POST["carga2"] * $_POST["carga2"];
		$costura_carga2 = $alt_saia2 + $costura_carga2;
		$costura_carga2 = sqrt($costura_carga2) + 10;
		$costura_carga2 = $costura_carga2 * 2;

		$costura_carga = $_POST["carga1"] * M_PI;
		$costura_carga = $costura_carga + 25;
		$costura_carga = $costura_carga + $_POST["carga2"] + 10;

		$costura_carga = $costura_carga + $costura_carga1 + $costura_carga2;
		$costura_carga_met = $costura_carga / 100;
		$costura_carga_gramat = $peso_fio_carga * 10;
		$costura_carga = $costura_carga_met * $costura_carga_gramat / 1000;
		$costura_carga = $costura_carga * $quilo_fio_pp;

		if ($tipo_cost_enchim == "simples1ved") {
			$costura_carga_ved = $_POST["carga1"] * M_PI;
			$costura_carga_ved += $_POST["carga2"] + 5;
			$costura_carga_ved += $costura_carga1 + $costura_carga2;
			$costura_carga_ved_met = $costura_carga_ved / 100;
			$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
			$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
			$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
		} elseif ($tipo_cost_enchim == "simples2ved") {
			$costura_carga_ved = $_POST["carga1"] * M_PI;
			$costura_carga_ved += $_POST["carga2"] + 5;
			$costura_carga_ved += $costura_carga1 + $costura_carga2;
			$costura_carga_ved_met = $costura_carga_ved / 100 * 2;
			$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
			$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
			$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
		}

		$valor_carga = $area_tampa + $valvula + $cadarco_carga + $costura_carga + $costura_carga_ved;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de enchimento",
	"",
	number_format((float)$valvula_met+$area_tampa_met, 2),
	"",
	number_format((float)$valor_carga, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Tampa",
	number_format((float)$quilo_carga, 2),
	number_format((float)$area_tampa_met, 2),
	number_format((float)$gramat_tampa*1000, 0),
	number_format((float)$area_tampa, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Válvula superior",
	number_format((float)$quilo_v, 2),
	number_format((float)$valvula_met, 2),
	number_format((float)$gramat_valv*1000, 0),
	number_format((float)$valvula, 2),
	"1",
	number_format((float)$carga2, 2),
	number_format((float)$carga1, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da válvula superior",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_carga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Carga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_carga_met, 2),
	number_format((float)$costura_carga_gramat, 2),
	number_format((float)$costura_carga, 2),
	"0",0,0,0);

if ($tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Carga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_carga_ved_met, 2),
		number_format((float)$costura_carga_ved_gramat, 2),
		number_format((float)$costura_carga_ved, 2),
		"0",0,0,0);
}
	}

/* ============ CARGA TIPO VÁLVULA COM PROTEÇÃO TIPO MOCHILA ============ */

} elseif ($_POST["carga"] == "c_prot_mochila") {

	$base1 = $_POST["base1"];
	$base2 = $_POST["base2"];
	if($_POST["cost_fio_topo"] == "on") {
		$base1 += 6;
		$base2 += 6;
	} else {
		$base1 += 10;
		$base2 += 10;
	}

	if ($base1 != $base2) {
		if ($base1 < $base2) {
			$base_maior = $base2;
			$base_menor = $base1;
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}
	} else {
		$base_maior = $base1;
		$base_menor = $base2;
	}

	$area_tampa = $base1 * $base2;
	$area_tampa_met = $area_tampa / 10000;
	$area_tampa = $quilo_carga * $area_tampa_met * $gramat_tampa;
		
	$carga1 = $_POST["carga1"] * M_PI;
	$carga1 = $carga1 + 16;
	$carga2 = $_POST["carga2"] + 5;

	$valvula = $carga1 * $carga2;
	$valvula_met = $valvula / 10000;
	$valvula = $quilo_v * $valvula_met * $gramat_valv;

	$cadarco_carga = $quilo_int * 0.006 * 1.2;
	$cadarco_mochila = $quilo_int * 0.006 * 1.4;

	$carga1_mo = $_POST["carga1"] * M_PI;
	$carga1_mo = $carga1_mo + 16;
	$carga2_mo = $_POST["carga2"] / 2;
	$carga2_mo = $carga2_mo + 6;

	$valor_mochila = $carga1_mo * $carga2_mo;
	$valor_mochila_met = $valor_mochila / 10000;
	$valor_mochila = $quilo_v * $valor_mochila_met * $gramat_valv;


	if ($tipo_cost_enchim == "simples" || $tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
		$peso_fio_ved_carga = 0.8;
		$peso_fio_carga = 0.1119;
	} elseif ($tipo_cost_enchim == "dupla") {
		$peso_fio_carga = 0.2238;
	}

	$costura_carga = $_POST["carga1"] * M_PI;
	$costura_carga = $costura_carga + 25;
	$costura_carga = $costura_carga + $_POST["carga2"] + 10;
	$costura_carga_flap = $_POST["carga1"] * M_PI;
	$costura_carga_flap = $costura_carga_flap + 25;
	$costura_carga_flap = $costura_carga_flap + $carga2_mo + 19;

	$costura_carga = $costura_carga + $costura_carga_flap;
	$costura_carga_met = $costura_carga / 100;
	$costura_carga_gramat = $peso_fio_carga * 10;
	$costura_carga = $costura_carga_met * $costura_carga_gramat / 1000;
	$costura_carga = $costura_carga * $quilo_fio_pp;

	if ($tipo_cost_enchim == "simples1ved") {
		$costura_carga_ved = $_POST["carga1"] * M_PI;
		$costura_carga_ved += $_POST["carga2"] + 5;
		$costura_carga_ved_met = $costura_carga_ved / 100;
		$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
		$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
		$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
	} elseif ($tipo_cost_enchim == "simples2ved") {
		$costura_carga_ved = $_POST["carga1"] * M_PI;
		$costura_carga_ved += $_POST["carga2"] + 5;
		$costura_carga_ved_met = $costura_carga_ved / 100 * 2;
		$costura_carga_ved_gramat = $peso_fio_ved_carga * 10;
		$costura_carga_ved = $costura_carga_ved_met * $costura_carga_ved_gramat / 1000;
		$costura_carga_ved = $costura_carga_ved * $quilo_fio_vedante;
	}

	$valor_carga = $area_tampa + $valvula + $cadarco_carga + $valor_mochila + $cadarco_mochila + $costura_carga + $costura_carga_ved;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de enchimento",
	"",
	number_format((float)$area_tampa_met+$valvula_met+$valor_mochila_met, 2),
	"",
	number_format((float)$valor_carga, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Tampa",
	number_format((float)$quilo_carga, 2),
	number_format((float)$area_tampa_met, 2),
	number_format((float)$gramat_tampa*1000, 0),
	number_format((float)$area_tampa, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Válvula superior",
	number_format((float)$quilo_v, 2),
	number_format((float)$valvula_met, 2),
	number_format((float)$gramat_valv*1000, 0),
	number_format((float)$valvula, 2),
	"1",
	number_format((float)$carga2, 2),
	number_format((float)$carga1, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da válvula superior",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_carga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Flap da válvula superior",
	number_format((float)$quilo_v, 2),
	number_format((float)$valor_mochila_met, 2),
	number_format((float)$gramat_valv*1000, 0),
	number_format((float)$valor_mochila, 2),
	"1",
	number_format((float)$carga2_mo, 2),
	number_format((float)$carga1_mo, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço do flap",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.4", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_mochila, 2),
	"0",
	1.5,
	140,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Carga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_carga_met, 2),
	number_format((float)$costura_carga_gramat, 2),
	number_format((float)$costura_carga, 2),
	"0",0,0,0);

if ($tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Carga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_carga_ved_met, 2),
		number_format((float)$costura_carga_ved_gramat, 2),
		number_format((float)$costura_carga_ved, 2),
		"0",0,0,0);
}


/* ============ CARGA COM TAMPA TIPO A.S. ============ */

} elseif ($_POST["carga"] == "c_tipo_as") {

	$base1 = $_POST["base1"];
	$base2 = $_POST["base2"];
	if($_POST["cost_fio_topo"] == "on") {
		$base1 += 6;
		$base2 += 6;
	} else {
		$base1 += 10;
		$base2 += 10;
	}

	if ($base1 != $base2) {
		if ($base1 < $base2) {
			$base_maior = $base2;
			$base_menor = $base1;
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}
	} else {
		$base_maior = $base1;
		$base_menor = $base2;
	}

	$area_tampa = $base1 * $base2;
	$area_tampa_met = $area_tampa / 10000;
	$valor_tampa = $quilo_carga * $area_tampa_met * $gramat_tampa;


	$b1 = $_POST["base1"];
	$b2 = $_POST["base2"];

	$b13 = $b1 / 2;
	$b23 = $b2 / 2;

	$b1 = $b13 * $b13;
	$b2 = $b23 * $b23;

	$h = $b1 + $b2;
	$h = sqrt($h) + 10;

	$valor_cadarco = $h * 4;
	$valor_cadarco_met = $valor_cadarco / 100;
	$valor_cadarco = $quilo_int * 0.006 * $valor_cadarco_met;

	$valor_presilha_met = 0.75;
	$valor_presilha = $quilo_int * 0.006 * $valor_presilha_met;


	if ($tipo_cost_enchim == "simples" || $tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
		$peso_fio_ved_carga = 0.8;
		$peso_fio_carga = 0.1119;
	} elseif ($tipo_cost_enchim == "dupla") {
		$peso_fio_carga = 0.2238;
	}

	$costura_carga = $_POST["base1"];
	$costura_carga = $costura_carga + 25;
	$costura_carga_met = $costura_carga / 100;
	$costura_carga_gramat = $peso_fio_carga * 10;
	$costura_carga = $costura_carga_met * $costura_carga_gramat / 1000;
	$costura_carga = $costura_carga * $quilo_fio_pp;

	$valor_carga = $valor_tampa + $valor_cadarco + $valor_presilha + $costura_carga;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de enchimento",
	"",
	number_format((float)$area_tampa_met, 2),
	"",
	number_format((float)$valor_carga, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Tampa",
	number_format((float)$quilo_carga, 2),
	number_format((float)$area_tampa_met, 2),
	number_format((float)$gramat_tampa*1000, 0),
	number_format((float)$valor_tampa, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da tampa",
	number_format((float)$quilo_int, 2),
	number_format((float)$valor_cadarco_met, 2),
	number_format((float)6, 0),
	number_format((float)$valor_cadarco, 2),
	"0",
	1.5,
	number_format((float)$valor_cadarco_met*100, 2),
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Presilhas de cadarço",
	number_format((float)$quilo_int, 2),
	number_format((float)$valor_presilha_met, 2),
	number_format((float)6, 0),
	number_format((float)$valor_presilha, 2),
	"0",
	1.5,
	number_format((float)$valor_presilha_met*100, 2),
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Carga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_carga_met, 2),
	number_format((float)$costura_carga_gramat, 2),
	number_format((float)$costura_carga, 2),
	"0",0,0,0);

if ($tipo_cost_enchim == "simples1ved" || $tipo_cost_enchim == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Carga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_carga_ved_met, 2),
		number_format((float)$costura_carga_ved_gramat, 2),
		number_format((float)$costura_carga_ved, 2),
		"0",0,0,0);
}

}

/* ============ SEM DESCARGA ============ */

if ($_POST["descarga"] == "vazio") {

if ($_POST["corpo"] != "qowac" && $_POST["corpo"] != "qowacf") {
		//$gramat_fundo_d = $_POST["gramat_corpo"] / 1000;


		$base1 = $_POST["base1"];
		$base2 = $_POST["base2"];
		if($_POST["cost_fio_base"] == "on") {
			$base1 += 6;
			$base2 += 6;
		} else {
			$base1 += 10;
			$base2 += 10;
		}

		if ($base1 != $base2) {
			if ($base1 < $base2) {
				$base_maior = $base2;
				$base_menor = $base1;
			} else {
				$base_maior = $base1;
				$base_menor = $base2;
			}
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}

		$area_fundo_d = $base1 * $base2;
		$area_fundo_d_met = $area_fundo_d / 10000;
		$area_fundo_d = $quilo_descarga * $area_fundo_d_met * $gramat_fundo_d;
		
		$valor_descarga = $area_fundo_d;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Fundo fechado",
	number_format((float)$quilo_descarga, 2),
	number_format((float)$area_fundo_d_met, 2),
	number_format((float)$gramat_fundo_d*1000, 0),
	number_format((float)$valor_descarga, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

if ($_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowadlf" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "cowafi") {

if ($_POST["gramat_forro"] == "65" || $_POST["gramat_forro"] == "70" || $_POST["gramat_forro"] == "147") {
	$gramat_forro = $_POST["gramat_forro"] / 1000;
	$quilo_forro = $quilo_lamin;
} else {
	$gramat_forro = $_POST["gramat_forro"] / 1000;
	$quilo_forro = $quilo_corpo;
}

if ($_POST["gramat_forro"] <= "100") {
	$quilo_forro = $quilo_leve;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Forro do fundo",
	number_format((float)$quilo_forro, 2),
	number_format((float)$area_fundo_d_met / 10000, 2),
	number_format((float)$gramat_forro*1000, 0),
	number_format((float)$gramat_forro*$quilo_forro, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");
}

}

/* ============ DESCARGA TIPO VÁLVULA SIMPLES ============ */

} elseif ($_POST["descarga"] == "d_simples") {


/*
	if ($_POST["fundo"] == "fundo_leve") {
		$gramat_fundo_d = 0.070;
	} elseif ($_POST["fundo"] == "fundo_pesada") {
		$gramat_fundo_d = $_POST["gramat_corpo"] / 1000;
	}
*/

		$gramat_valv_d = $_POST["gramat_valvula_d"]/1000;
		//$gramat_valv = $gramat_valv_d;

	if ($_POST["d_redondo"] == "d_redondo") {

		$base1 = $_POST["base1"];
		$base2 = $_POST["base2"];
		if($_POST["cost_fio_base"] == "on") {
			$base1 += 6;
			$base2 += 6;
		} else {
			$base1 += 10;
			$base2 += 10;
		}

		if ($base1 != $base2) {
			if ($base1 < $base2) {
				$base_maior = $base2;
				$base_menor = $base1;
			} else {
				$base_maior = $base1;
				$base_menor = $base2;
			}
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}

		$area_fundo_d = $base1 * $base2;
		$area_fundo_d_met = $area_fundo_d / 10000;
		$area_fundo_d = $quilo_descarga * $area_fundo_d_met * $gramat_fundo_d;
		
		$descarga1 = $_POST["descarga1"] * M_PI;
		$descarga1 = $descarga1 + 5;
		$descarga2 = $_POST["descarga2"] + 5;

		$valvula_met = $descarga1 * $descarga2;
		$valvula = $valvula_met / 10000;
		$valvula = $quilo_v_d * $valvula * $gramat_valv_d;

		$cadarco_descarga = $quilo_int * 0.006 * 1.2;


		if ($tipo_cost_esvaz == "simples" || $tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
			$peso_fio_ved_descarga = 0.8;
			$peso_fio_descarga = 0.1119;
		} elseif ($tipo_cost_esvaz == "dupla") {
			$peso_fio_descarga = 0.2238;
		}

		$costura_descarga = $_POST["descarga1"] * M_PI;
		$costura_descarga = $costura_descarga + 25;
		$costura_descarga = $costura_descarga + $_POST["descarga2"] + 10;
		$costura_descarga_met = $costura_descarga / 100;
		$costura_descarga_gramat = $peso_fio_descarga * 10;
		$costura_descarga = $costura_descarga_met * $costura_descarga_gramat / 1000;
		$costura_descarga = $costura_descarga * $quilo_fio_pp;

		if ($tipo_cost_esvaz == "simples1ved") {
			$costura_descarga_ved = $_POST["descarga1"] * M_PI;
			$costura_descarga_ved += $_POST["descarga2"] + 5;
			$costura_descarga_ved_met = $costura_descarga_ved / 100;
			$costura_descarga_ved_gramat = $peso_fio_ved_descarga * 10;
			$costura_descarga_ved = $costura_descarga_ved_met * $costura_descarga_ved_gramat / 1000;
			$costura_descarga_ved = $costura_descarga_ved * $quilo_fio_vedante;
		} elseif ($tipo_cost_esvaz == "simples2ved") {
			$costura_descarga_ved = $_POST["descarga1"] * M_PI;
			$costura_descarga_ved += $_POST["descarga2"] + 5;
			$costura_descarga_ved_met = $costura_descarga_ved / 100 * 2;
			$costura_descarga_ved_gramat = $peso_fio_ved_descarga * 10;
			$costura_descarga_ved = $costura_descarga_ved_met * $costura_descarga_ved_gramat / 1000;
			$costura_descarga_ved = $costura_descarga_ved * $quilo_fio_vedante;
		}

if ($_POST["corpo"] == "qowac" || $_POST["corpo"] == "qowacf") {
	$area_fundo_d_met = 0;
}

if ($_POST["corpo"] != "qowac" && $_POST["corpo"] != "qowacf") {
		$valor_descarga = $area_fundo_d + $valvula + $cadarco_descarga + $costura_descarga + $costura_descarga_ved;
} else {
		$valor_descarga = $valvula + $cadarco_descarga + $costura_descarga + $costura_descarga_ved;
}

if ($_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowadlf" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "cowafi") {
	if ($_POST["gramat_forro"] == "65" || $_POST["gramat_forro"] == "70" || $_POST["gramat_forro"] == "147") {
		$gramat_forro = $_POST["gramat_forro"] / 1000;
		$quilo_forro = $quilo_lamin;
	} else {
		$gramat_forro = $_POST["gramat_forro"] / 1000;
		$quilo_forro = $quilo_corpo;
	}
	$valor_descarga += $gramat_forro*$quilo_forro;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de esvaziamento",
	"",
	number_format((float)$area_fundo_d_met+$valvula_met / 10000, 2),
	"",
	number_format((float)$valor_descarga, 2),
	"1",0,0,0);

if ($_POST["corpo"] != "qowac" && $_POST["corpo"] != "qowacf") {
GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fundo",
	number_format((float)$quilo_descarga, 2),
	number_format((float)$area_fundo_d_met, 2),
	number_format((float)$gramat_fundo_d*1000, 0),
	number_format((float)$area_fundo_d, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

if ($_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowadlf" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "cowafi") {
GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Forro do fundo",
	number_format((float)$quilo_forro, 2),
	number_format((float)$area_fundo_d_met, 2),
	number_format((float)$gramat_forro*1000, 0),
	number_format((float)$gramat_forro*$quilo_forro, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");
}

}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Válvula inferior",
	number_format((float)$quilo_v_d, 2),
	number_format((float)$valvula_met/10000, 2),
	number_format((float)$gramat_valv_d*1000, 0),
	number_format((float)$valvula, 2),
	"1",
	number_format((float)$descarga2, 2),
	number_format((float)$descarga1, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da válvula inferior",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_descarga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Descarga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_descarga_met, 2),
	number_format((float)$costura_descarga_gramat, 2),
	number_format((float)$costura_descarga, 2),
	"0",0,0,0);

if ($tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Descarga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_descarga_ved_met, 2),
		number_format((float)$costura_descarga_ved_gramat, 2),
		number_format((float)$costura_descarga_ved, 2),
		"0",0,0,0);
}

	} else {


		$base1 = $_POST["base1"];
		$base2 = $_POST["base2"];
		if($_POST["cost_fio_base"] == "on") {
			$base1 += 6;
			$base2 += 6;
		} else {
			$base1 += 10;
			$base2 += 10;
		}

		if ($base1 != $base2) {
			if ($base1 < $base2) {
				$base_maior = $base2;
				$base_menor = $base1;
			} else {
				$base_maior = $base1;
				$base_menor = $base2;
			}
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}

		$area_fundo_d = $base1 * $base2;
		$area_fundo_d_met = $area_fundo_d / 10000;
		$area_fundo_d = $quilo_descarga * $area_fundo_d_met * $gramat_fundo_d;
		
		$descarga1 = $_POST["descarga1"] * 4;
		$descarga1 = $descarga1 + 5;
		$descarga2 = $_POST["descarga2"] + 5;

		$valvula_met = $descarga1 * $descarga2;
		$valvula = $valvula_met / 10000;
		$valvula = $quilo_v_d * $valvula * $gramat_valv_d;

		$cadarco_descarga = $quilo_int * 0.006 * 1.2;

		if ($tipo_cost_esvaz == "simples" || $tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
			$peso_fio_ved_descarga = 0.8;
			$peso_fio_descarga = 0.1119;
		} elseif ($tipo_cost_esvaz == "dupla") {
			$peso_fio_descarga = 0.2238;
		}

		$costura_descarga = $_POST["descarga1"] * 4;
		$costura_descarga = $costura_descarga + 25;
		$costura_descarga = $costura_descarga + $_POST["descarga2"] + 10;
		$costura_descarga_met = $costura_descarga / 100;
		$costura_descarga_gramat = $peso_fio_descarga * 10;
		$costura_descarga = $costura_descarga_met * $costura_descarga_gramat / 1000;
		$costura_descarga = $costura_descarga * $quilo_fio_pp;

		if ($tipo_cost_esvaz == "simples1ved") {
			$costura_descarga_ved = $_POST["descarga1"] * 4;
			$costura_descarga_ved += $_POST["descarga2"] + 5;
			$costura_descarga_ved_met = $costura_descarga_ved / 100;
			$costura_descarga_ved_gramat = $peso_fio_ved_descarga * 10;
			$costura_descarga_ved = $costura_descarga_ved_met * $costura_descarga_ved_gramat / 1000;
			$costura_descarga_ved = $costura_descarga_ved * $quilo_fio_vedante;
		} elseif ($tipo_cost_esvaz == "simples2ved") {
			$costura_descarga_ved = $_POST["descarga1"] * 4;
			$costura_descarga_ved += $_POST["descarga2"] + 5;
			$costura_descarga_ved_met = $costura_descarga_ved / 100 * 2;
			$costura_descarga_ved_gramat = $peso_fio_ved_descarga * 10;
			$costura_descarga_ved = $costura_descarga_ved_met * $costura_descarga_ved_gramat / 1000;
			$costura_descarga_ved = $costura_descarga_ved * $quilo_fio_vedante;
		}

if ($_POST["corpo"] == "qowac" || $_POST["corpo"] == "qowacf") {
	$area_fundo_d_met = 0;
}

if ($_POST["corpo"] != "qowac" && $_POST["corpo"] != "qowacf") {
		$valor_descarga = $area_fundo_d + $valvula + $cadarco_descarga + $costura_descarga + $costura_descarga_ved;
} else {
		$valor_descarga = $valvula + $cadarco_descarga + $costura_descarga + $costura_descarga_ved;
}

if ($_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowadlf" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "cowafi") {
	if ($_POST["gramat_forro"] == "65" || $_POST["gramat_forro"] == "70" || $_POST["gramat_forro"] == "147") {
		$gramat_forro = $_POST["gramat_forro"] / 1000;
		$quilo_forro = $quilo_lamin;
	} else {
		$gramat_forro = $_POST["gramat_forro"] / 1000;
		$quilo_forro = $quilo_corpo;
	}
	$valor_descarga += $gramat_forro*$quilo_forro;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de esvaziamento",
	"",
	number_format((float)$area_fundo_d_met+$valvula_met / 10000, 2),
	"",
	number_format((float)$valor_descarga, 2),
	"1",0,0,0);

if ($_POST["corpo"] != "qowac" && $_POST["corpo"] != "qowacf") {
GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fundo",
	number_format((float)$quilo_descarga, 2),
	number_format((float)$area_fundo_d_met, 2),
	number_format((float)$gramat_fundo_d*1000, 0),
	number_format((float)$area_fundo_d, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

if ($_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowadlf" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "cowafi") {
GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Forro do fundo",
	number_format((float)$quilo_forro, 2),
	number_format((float)$area_fundo_d_met, 2),
	number_format((float)$gramat_forro*1000, 0),
	number_format((float)$gramat_forro*$quilo_forro, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");
}

}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Válvula inferior",
	number_format((float)$quilo_v_d, 2),
	number_format((float)$valvula_met/10000, 2),
	number_format((float)$gramat_valv_d*1000, 0),
	number_format((float)$valvula, 2),
	"1",
	number_format((float)$descarga2, 2),
	number_format((float)$descarga1, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da válvula inferior",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_descarga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Descarga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_descarga_met, 2),
	number_format((float)$costura_descarga_gramat, 2),
	number_format((float)$costura_descarga, 2),
	"0",0,0,0);

if ($tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Descarga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_descarga_ved_met, 2),
		number_format((float)$costura_descarga_ved_gramat, 2),
		number_format((float)$costura_descarga_ved, 2),
		"0",0,0,0);
}
	}

/* ============ DESCARGA TIPO VÁLVULA COM PROTEÇÃO TIPO PRESILHA ============ */

} elseif ($_POST["descarga"] == "d_prot_presilha") {


/*
	if ($_POST["fundo"] == "fundo_leve") {
		$gramat_fundo_d = 0.070;
	} elseif ($_POST["fundo"] == "fundo_pesada") {
		$gramat_fundo_d = $_POST["gramat_corpo"] / 1000;
	}
*/

	$gramat_valv_d = $_POST["gramat_valvula_d"]/1000;
	//$gramat_valv = $gramat_valv_d;

	$base1 = $_POST["base1"];
	$base2 = $_POST["base2"];
	if($_POST["cost_fio_base"] == "on") {
		$base1 += 6;
		$base2 += 6;
	} else {
		$base1 += 10;
		$base2 += 10;
	}

	if ($base1 != $base2) {
		if ($base1 < $base2) {
			$base_maior = $base2;
			$base_menor = $base1;
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}
	} else {
		$base_maior = $base1;
		$base_menor = $base2;
	}

	$area_fundo_d = $base1 * $base2;
	$area_fundo_d_met = $area_fundo_d / 10000;
	$area_fundo_d = $quilo_descarga * $area_fundo_d_met * $gramat_fundo_d;
		
	$descarga1 = $_POST["descarga1"] * 4;
	$descarga1 = $descarga1 + 5;
	$descarga2 = $_POST["descarga2"] + 6;

	$valvula = $descarga1 * $descarga2;
	$valvula_met = $valvula / 10000;
	$valvula = $quilo_v_d * $valvula_met * $gramat_valv_d;

	$cadarco_descarga = $quilo_int * 0.006 * 1.2;
	$cadarco_flap = $quilo_int * 0.006 * 1.4;

	if ($tipo_cost_esvaz == "simples" || $tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
		$peso_fio_ved_descarga = 0.8;
		$peso_fio_descarga = 0.1119;
	} elseif ($tipo_cost_esvaz == "dupla") {
		$peso_fio_descarga = 0.2238;
	}

	$costura_descarga = $_POST["descarga1"] * 4;
	$costura_descarga = $costura_descarga + 25 + 60;
	$costura_descarga = $costura_descarga + $_POST["descarga2"] + 10;
	$costura_descarga_met = $costura_descarga / 100;
	$costura_descarga_gramat = $peso_fio_descarga * 10;
	$costura_descarga = $costura_descarga_met * $costura_descarga_gramat / 1000;
	$costura_descarga = $costura_descarga * $quilo_fio_pp;

	if ($tipo_cost_esvaz == "simples1ved") {
		$costura_descarga_ved = $_POST["descarga1"] * 4;
		$costura_descarga_ved += $_POST["descarga2"] + 5;
		$costura_descarga_ved_met = $costura_descarga_ved / 100;
		$costura_descarga_ved_gramat = $peso_fio_ved_descarga * 10;
		$costura_descarga_ved = $costura_descarga_ved_met * $costura_descarga_ved_gramat / 1000;
		$costura_descarga_ved = $costura_descarga_ved * $quilo_fio_vedante;
	} elseif ($tipo_cost_esvaz == "simples2ved") {
		$costura_descarga_ved = $_POST["descarga1"] * 4;
		$costura_descarga_ved += $_POST["descarga2"] + 5;
		$costura_descarga_ved_met = $costura_descarga_ved / 100 * 2;
		$costura_descarga_ved_gramat = $peso_fio_ved_descarga * 10;
		$costura_descarga_ved = $costura_descarga_ved_met * $costura_descarga_ved_gramat / 1000;
		$costura_descarga_ved = $costura_descarga_ved * $quilo_fio_vedante;
	}

if ($_POST["corpo"] == "qowac" || $_POST["corpo"] == "qowacf") {
	$area_fundo_d_met = 0;
}

if ($_POST["corpo"] != "qowac" && $_POST["corpo"] != "qowacf") {
	$valor_descarga = $area_fundo_d + $valvula + $cadarco_descarga + $cadarco_flap + $costura_descarga + $costura_descarga_ved;
} else {
	$valor_descarga = $valvula + $cadarco_descarga + $cadarco_flap + $costura_descarga + $costura_descarga_ved;
}

if ($_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowadlf" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "cowafi") {
	if ($_POST["gramat_forro"] == "65" || $_POST["gramat_forro"] == "70" || $_POST["gramat_forro"] == "147") {
		$gramat_forro = $_POST["gramat_forro"] / 1000;
		$quilo_forro = $quilo_lamin;
	} else {
		$gramat_forro = $_POST["gramat_forro"] / 1000;
		$quilo_forro = $quilo_corpo;
	}
	$valor_descarga += $gramat_forro*$quilo_forro;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de esvaziamento",
	"",
	number_format((float)$area_fundo_d_met+$area_tampa_met+$valvula_met, 2),
	"",
	number_format((float)$valor_descarga, 2),
	"1",0,0,0);

if ($_POST["corpo"] != "qowac" && $_POST["corpo"] != "qowacf") {
GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fundo",
	number_format((float)$quilo_descarga, 2),
	number_format((float)$area_fundo_d_met, 2),
	number_format((float)$gramat_fundo_d*1000, 0),
	number_format((float)$area_fundo_d, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

if ($_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowadlf" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "cowafi") {
GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Forro do fundo",
	number_format((float)$quilo_forro, 2),
	number_format((float)$area_fundo_d_met, 2),
	number_format((float)$gramat_forro*1000, 0),
	number_format((float)$gramat_forro*$quilo_forro, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");
}

}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Válvula inferior",
	number_format((float)$quilo_v_d, 2),
	number_format((float)$valvula_met, 2),
	number_format((float)$gramat_valv_d*1000, 0),
	number_format((float)$valvula, 2),
	"1",
	number_format((float)$descarga2, 2),
	number_format((float)$descarga1, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da válvula inferior",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_descarga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço do flap \"X\"",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.4", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_flap, 2),
	"0",
	1.5,
	140,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Descarga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_descarga_met, 2),
	number_format((float)$costura_descarga_gramat, 2),
	number_format((float)$costura_descarga, 2),
	"0",0,0,0);

if ($tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Descarga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_descarga_ved_met, 2),
		number_format((float)$costura_descarga_ved_gramat, 2),
		number_format((float)$costura_descarga_ved, 2),
		"0",0,0,0);
}

/* ============ DESCARGA TIPO VÁLVULA COM PROTEÇÃO TIPO MOCHILA ============ */

} elseif ($_POST["descarga"] == "d_prot_mochila") {


/*
	if ($_POST["fundo"] == "fundo_leve") {
		$gramat_fundo_d = 0.070;
	} elseif ($_POST["fundo"] == "fundo_pesada") {
		$gramat_fundo_d = $_POST["gramat_corpo"] / 1000;
	}
*/

	$gramat_valv_d = $_POST["gramat_valvula_d"]/1000;
	//$gramat_valv = $gramat_valv_d;

	$base1 = $_POST["base1"];
	$base2 = $_POST["base2"];
	if($_POST["cost_fio_base"] == "on") {
		$base1 += 6;
		$base2 += 6;
	} else {
		$base1 += 10;
		$base2 += 10;
	}

	if ($base1 != $base2) {
		if ($base1 < $base2) {
			$base_maior = $base2;
			$base_menor = $base1;
		} else {
			$base_maior = $base1;
			$base_menor = $base2;
		}
	} else {
		$base_maior = $base1;
		$base_menor = $base2;
	}

	$area_fundo_d = $base1 * $base2;
	$area_fundo_d_met = $area_fundo_d / 10000;
	$area_fundo_d = $quilo_descarga * $area_fundo_d_met * $gramat_fundo_d;
		
	$descarga1 = $_POST["descarga1"] * M_PI;
	$descarga1 = $descarga1 + 6;
	$descarga2 = $_POST["descarga2"] + 5;

	$valvula = $descarga1 * $descarga2;
	$valvula_met = $valvula / 10000;
	$valvula = $quilo_v_d * $valvula_met * $gramat_valv_d;

	$cadarco_descarga = $quilo_int * 0.006 * 1.2;
	$cadarco_mochila = $quilo_int * 0.006 * 1.4;

	$descarga1_mo = $_POST["descarga1"] * M_PI;
	$descarga1_mo = $descarga1_mo + 6;

	$descarga2_mo = $_POST["descarga2"] / 2;
	$descarga2_mo = $descarga2_mo + 6;

	$valor_mochila = $descarga1_mo * $descarga2_mo;
	$valor_mochila_met = $valor_mochila / 10000;
	$valor_mochila = $quilo_v_d * $valor_mochila_met * $gramat_valv_d;

	if ($tipo_cost_esvaz == "simples" || $tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
		$peso_fio_ved_descarga = 0.8;
		$peso_fio_descarga = 0.1119;
	} elseif ($tipo_cost_esvaz == "dupla") {
		$peso_fio_descarga = 0.2238;
	}

	$costura_descarga = $_POST["descarga1"] * M_PI;
	$costura_descarga = $costura_descarga + 25;
	$costura_descarga = $costura_descarga + $_POST["descarga2"] + 10;
	$costura_descarga_flap = $_POST["descarga1"] * M_PI;
	$costura_descarga_flap = $costura_descarga_flap + 25;
	$costura_descarga_flap = $costura_descarga_flap + $descarga2_mo + 19;

	$costura_descarga = $costura_descarga + $costura_descarga_flap;
	$costura_descarga_met = $costura_descarga / 100;
	$costura_descarga_gramat = $peso_fio_descarga * 10;
	$costura_descarga = $costura_descarga_met * $costura_descarga_gramat / 1000;
	$costura_descarga = $costura_descarga * $quilo_fio_pp;

	if ($tipo_cost_esvaz == "simples1ved") {
		$costura_descarga_ved = $_POST["descarga1"] * M_PI;
		$costura_descarga_ved += $_POST["descarga2"] + 5;
		$costura_descarga_ved_met = $costura_descarga_ved / 100;
		$costura_descarga_ved_gramat = $peso_fio_ved_descarga * 10;
		$costura_descarga_ved = $costura_descarga_ved_met * $costura_descarga_ved_gramat / 1000;
		$costura_descarga_ved = $costura_descarga_ved * $quilo_fio_vedante;
	} elseif ($tipo_cost_esvaz == "simples2ved") {
		$costura_descarga_ved = $_POST["descarga1"] * M_PI;
		$costura_descarga_ved += $_POST["descarga2"] + 5;
		$costura_descarga_ved_met = $costura_descarga_ved / 100 * 2;
		$costura_descarga_ved_gramat = $peso_fio_ved_descarga * 10;
		$costura_descarga_ved = $costura_descarga_ved_met * $costura_descarga_ved_gramat / 1000;
		$costura_descarga_ved = $costura_descarga_ved * $quilo_fio_vedante;
	}

if ($_POST["corpo"] == "qowac" || $_POST["corpo"] == "qowacf") {
	$area_fundo_d_met = 0;
} elseif ($_POST["corpo"] == "gota") {
	$area_fundo_d_met = ($_POST["base1"]+6) / 100;
	$area_fundo_d_met = $area_fundo_d_met * ($_POST["base2"]+6) / 100;
}

if ($_POST["corpo"] != "qowac" && $_POST["corpo"] != "qowacf") {
	$valor_descarga = $area_fundo_d + $valvula + $cadarco_descarga + $valor_mochila + $cadarco_mochila + $costura_descarga + $costura_descarga_ved;
} else {
	$valor_descarga = $valvula + $cadarco_descarga + $valor_mochila + $cadarco_mochila + $costura_descarga + $costura_descarga_ved;
}

if ($_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowadlf" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "cowafi") {
	if ($_POST["gramat_forro"] == "65" || $_POST["gramat_forro"] == "70" || $_POST["gramat_forro"] == "147") {
		$gramat_forro = $_POST["gramat_forro"] / 1000;
		$quilo_forro = $quilo_lamin;
	} else {
		$gramat_forro = $_POST["gramat_forro"] / 1000;
		$quilo_forro = $quilo_corpo;
	}
	$valor_descarga += $gramat_forro*$quilo_forro;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de esvaziamento",
	"",
	number_format((float)$area_fundo_d_met + $valvula_met + $valor_mochila_met, 2),
	"",
	number_format((float)$valor_descarga, 2),
	"1",0,0,0);

if ($_POST["corpo"] != "qowac" && $_POST["corpo"] != "qowacf") {
GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fundo",
	number_format((float)$quilo_descarga, 2),
	number_format((float)$area_fundo_d_met, 2),
	number_format((float)$gramat_fundo_d*1000, 0),
	number_format((float)$area_fundo_d, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");

if ($_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowadlf" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "cowafi") {
GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Forro do fundo",
	number_format((float)$quilo_forro, 2),
	number_format((float)$area_fundo_d_met, 2),
	number_format((float)$gramat_forro*1000, 0),
	number_format((float)$gramat_forro*$quilo_forro, 2),
	"1",
	number_format((float)$base_menor, 2),
	number_format((float)$base_maior, 2),
	"1");
}

}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Válvula inferior",
	number_format((float)$quilo_v_d, 2),
	number_format((float)$valvula_met, 2),
	number_format((float)$gramat_valv_d*1000, 0),
	number_format((float)$valvula, 2),
	"1",
	number_format((float)$descarga2, 2),
	number_format((float)$descarga1, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da válvula inferior",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_descarga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Flap da válvula inferior",
	number_format((float)$quilo_v_d, 2),
	number_format((float)$valor_mochila_met, 2),
	number_format((float)$gramat_valv_d*1000, 0),
	number_format((float)$valor_mochila, 2),
	"1",
	number_format((float)$descarga2_mo, 2),
	number_format((float)$descarga1_mo, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço do flap",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.4", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_mochila, 2),
	"0",
	1.5,
	140,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Descarga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_descarga_met, 2),
	number_format((float)$costura_descarga_gramat, 2),
	number_format((float)$costura_descarga, 2),
	"0",0,0,0);

if ($tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Descarga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_descarga_ved_met, 2),
		number_format((float)$costura_descarga_ved_gramat, 2),
		number_format((float)$costura_descarga_ved, 2),
		"0",0,0,0);
}

/* ============ DESCARGA TIPO AFUNILADO OU ABERTURA TOTAL SIMPLES ============ */

} elseif ($_POST["descarga"] == "d_afunilado" || $_POST["descarga"] == "d_total") {

	$base1 = $_POST["base1"] + 1.5;
	$base2 = $_POST["base2"] + 1.5;
	$alt_saia = $_POST["descarga2"] + 5;

	$gramat_saia = $_POST["gramat_fundo_d"];
/*
	if ($_POST["fundo"] == "fundo_leve") {
		$gramat_saia = 0.070;
	} elseif ($_POST["fundo"] == "fundo_pesada") {
		$gramat_saia = 0.13;
	}
*/

	$valor_descarga = $base1 + $base2;
	$valor_descarga = $valor_descarga * 2 * $alt_saia;
	$valor_descarga_met = $valor_descarga / 10000 ;

	$valor_descarga = $quilo_descarga * $gramat_saia * $valor_descarga_met;

	$cadarco_descarga = $quilo_int * 0.006 * 1.2;

	if ($tipo_cost_esvaz == "simples" || $tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
		$peso_fio_ved_descarga = 0.8;
		$peso_fio_descarga = 0.1119;
	} elseif ($tipo_cost_esvaz == "dupla") {
		$peso_fio_descarga = 0.2238;
	}

	if ($_POST["descarga"] == "d_afunilado") {
		$costura_descarga1 = $_POST["base1"] - $_POST["descarga1"];
		$costura_descarga1 = $costura_descarga1 / 2;
		$costura_descarga1 = $costura_descarga1 * $costura_descarga1	;
		$alt_saia1 = $_POST["descarga2"] * $_POST["descarga2"];
		$costura_descarga1 = $alt_saia1 + $costura_descarga1;
		$costura_descarga1 = sqrt($costura_descarga1) + 10;
		$costura_descarga1 = $costura_descarga1 * 2;

		$costura_descarga2 = $_POST["base2"] - $_POST["descarga1"];
		$costura_descarga2 = $costura_descarga2 / 2;
		$costura_descarga2 = $costura_descarga2 * $costura_descarga2	;
		$alt_saia2 = $_POST["descarga2"] * $_POST["descarga2"];
		$costura_descarga2 = $alt_saia2 + $costura_descarga2;
		$costura_descarga2 = sqrt($costura_descarga2) + 10;
		$costura_descarga2 = $costura_descarga2 * 2;

		$costura_descarga = $_POST["descarga1"] * 4;
		$costura_descarga = $costura_descarga + 25;
//		$costura_descarga = $costura_descarga + $_POST["descarga2"] + 10;

		$costura_descarga = $costura_descarga + $costura_descarga1 + $costura_descarga2;

		$costura_descarga_ved = $costura_descarga1 + $costura_descarga2;
	}

	if ($_POST["descarga"] == "d_total") {
		if ($_POST["corpo"] == "qowa" || $_POST["corpo"] == "qowao" || $_POST["corpo"] == "qowam" || $_POST["corpo"] == "qowaa" || $_POST["corpo"] == "qowat" || $_POST["corpo"] == "qhe" || $_POST["corpo"] == "qhe_ref" || $_POST["corpo"] == "qms" || $_POST["corpo"] == "qowa2" || $_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "qowad4" || $_POST["corpo"] == "qowad8" || $_POST["corpo"] == "qowadlf") {
			$costura_descarga1 = $_POST["descarga2"] + 20;
			$costura_descarga1 = $costura_descarga1 * 4;
			$costura_descarga2 = $_POST["base1"] + $_POST["base2"];
			$costura_descarga2 = $costura_descarga2 * 2;
			$costura_descarga2 = $costura_descarga2 + 25;
//			$costura_descarga2 = $costura_descarga2 * 2;

			$costura_descarga = $costura_descarga + $costura_descarga1 + $costura_descarga2;
		} else {
			$costura_descarga = $_POST["base1"] + $_POST["base2"];
			$costura_descarga = $costura_descarga * 2;
			$costura_descarga = $costura_descarga + 25;

//			$costura_descarga = $costura_descarga * 2;
		}
		$costura_descarga_ved = $_POST["descarga2"] + 20;
		$costura_descarga_ved = $$costura_descarga_ved * 4;
	}
	

	$costura_descarga_met = $costura_descarga / 100;
	$costura_descarga_gramat = $peso_fio_descarga * 10;
	$costura_descarga = $costura_descarga_met * $costura_descarga_gramat / 1000;
	$costura_descarga = $costura_descarga * $quilo_fio_pp;

	if ($tipo_cost_esvaz == "simples1ved") {
		$costura_descarga_ved_met = $costura_descarga_ved / 100;
	} elseif ($tipo_cost_esvaz == "simples2ved") {
		$costura_descarga_ved_met = $costura_descarga_ved / 100 * 2;
	}
	$costura_descarga_ved_gramat = $peso_fio_ved_descarga * 10;
	$costura_descarga_ved = $costura_descarga_ved_met * $costura_descarga_ved_gramat / 1000;
	$costura_descarga_ved = $costura_descarga_ved * $quilo_fio_vedante;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Sistema de esvaziamento",
	"",
	number_format((float)$valor_descarga_met, 2),
	"",
	number_format((float)$valor_descarga + $cadarco_descarga + $costura_descarga + $costura_descarga_ved, 2),
	"1",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Saia inferior",
	number_format((float)$quilo_descarga, 2),
	number_format((float)$valor_descarga_met, 2),
	number_format((float)$gramat_saia*1000, 0),
	number_format((float)$valor_descarga, 2),
	"1",
	number_format((float)$alt_saia, 2),
	number_format((float)($base1+$base2)*2, 2),
	"1");

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço da saia",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.2", 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_descarga, 2),
	"0",
	1.5,
	120,
	1);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Descarga)",
	number_format((float)$quilo_fio_pp, 2),
	number_format((float)$costura_descarga_met, 2),
	number_format((float)$costura_descarga_gramat, 2),
	number_format((float)$costura_descarga, 2),
	"0",0,0,0);

if ($tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Descarga)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_descarga_ved_met, 2),
		number_format((float)$costura_descarga_ved_gramat, 2),
		number_format((float)$costura_descarga_ved, 2),
		"0",0,0,0);
}

$valor_descarga = $valor_descarga + $cadarco_descarga + $costura_descarga + $costura_descarga_ved;

}

/* ============ ALÇAS ============ */

$alca_capac = $_POST["alca_capac"];

if ($alca_capac <= 800) {
	$gramat_alca = 0.025;
	$larg_alca = 6;
} elseif ($alca_capac >= 801 && $alca_capac <= 1000) {
	$gramat_alca = 0.028;
	$larg_alca = 7;
} elseif ($alca_capac >= 1001 && $alca_capac <= 1250) {
	$gramat_alca = 0.031;
	$larg_alca = 6.5;
} elseif ($alca_capac >= 1251 && $alca_capac <= 1400) {
	$gramat_alca = 0.036;
	$larg_alca = 7.5;
} elseif ($alca_capac >= 1401 && $alca_capac <= 1700) {
	$gramat_alca = 0.042;
	$larg_alca = 7.5;
} elseif ($alca_capac >= 1701) {
	$gramat_alca = 0.048;
	$larg_alca = 8;
}


/* ============ ALÇAS - 4 ESPALMADAS ============ */

if ($_POST["alca"] == "4_4espalmada") {

	$alca_altura = $_POST["alca_altura"];
	$alca_fix_altura = $_POST["alca_fix_altura"];

	$alt_alca = $alca_altura + $alca_fix_altura;
	$alt_alca = $alt_alca * 2;
	$alt_alca = $alt_alca * 4;
	$alt_alca = $alt_alca / 100;

if ($_POST["alca_material"] == "fita") {
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
} elseif ($_POST["alca_material"] == "tecido") {
	$gramat_alca = 0.095;
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
}


if ($tipo_cost_alca == "ponto_fixo") {
	$peso_fio_alca = 0.0743;
} elseif ($tipo_cost_alca == "dupla" || $tipo_cost_alca == "dupla2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.1486;
} elseif ($tipo_cost_alca == "overlock" || $tipo_cost_alca == "overlock2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.367;
}

if ($_POST["carga_nominal"] >= "1500") {
	$costura_alca = $_POST["alca_fix_altura"] * 8;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
} else {
	$costura_alca = $_POST["alca_fix_altura"] * 6;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
}

$costura_alca_met = $costura_alca / 100;
$costura_alca_gramat = $peso_fio_alca * 10;
$costura_alca = $costura_alca_met * $costura_alca_gramat / 1000;
$costura_alca = $costura_alca * $quilo_fio;

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	$costura_alca_ved_met = $costura_alca_met;
	$costura_alca_ved_gramat = $peso_fio_ved_alca * 10;
	$costura_alca_ved = $costura_alca_ved_met * $costura_alca_ved_gramat / 1000;
	$costura_alca_ved = $costura_alca_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alças espalmadas",
	"",
	number_format((float)$alt_alca, 2),
	"",
	number_format((float)$valor_alca + $costura_alca + $costura_alca_ved, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);

	$valor_alca_unica = $valor_alca;
	$valor_alca = $valor_alca + $costura_alca + $costura_alca_ved;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Alças)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_alca_met, 2),
	number_format((float)$costura_alca_gramat, 2),
	number_format((float)$costura_alca, 2),
	"0",0,0,0);

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Alças)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_alca_ved_met, 2),
		number_format((float)$costura_alca_ved_gramat, 2),
		number_format((float)$costura_alca_ved, 2),
		"0",0,0,0);
}

if ($_POST["reforco_vao_livre"] == "1" || $_POST["reforco_vao_livre"] == true) {
	$valor_reforco_vao_livre = $alca_altura / 100;
	$valor_reforco_vao_livre = $valor_reforco_vao_livre * 2 * $quilo_int * 0.028 * 4;
	$valor_alca = $valor_alca + $valor_reforco_vao_livre;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de vão livre",
	number_format((float)$quilo_int, 2),
	number_format((float)$alca_altura*4/100, 2),
	//number_format((float)$alca_altura*2*4/100, 2),
	number_format((float)28, 0),
	number_format((float)$valor_reforco_vao_livre, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alca_altura, 2),
	4);

}

if ($_POST["reforco_fixacao"] == "1" || $_POST["reforco_fixacao"] == true) {
	$valor_reforco_fixacao = 0.3 * 0.4 * $quilo_lamin * 0.160 * 4;
	$valor_alca = $valor_alca + $valor_reforco_fixacao;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de fixação",
	number_format((float)$quilo_lamin, 2),
	number_format((float)0.3*0.4*4, 2),
	number_format((float)175, 0),
	number_format((float)$valor_reforco_fixacao, 2),
	"0",
	number_format(30, 2),
	number_format(40, 2),
	4);

}

if ($_POST["alca_dupla"] == "1" || $_POST["alca_dupla"] == true) {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alça dupla",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca_unica, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);
}

/* ============ ALÇAS - 4 ESPALMADAS + 2 ESTIVAS ============ */

} elseif ($_POST["alca"] == "6_2estiva") {

	$alca_altura = $_POST["alca_altura"];
	$alca_fix_altura = $_POST["alca_fix_altura"];

	$alt_alca = $alca_altura + $alca_fix_altura;
	$alt_alca = $alt_alca * 2;
	$alt_alca = $alt_alca * 4;
	$alt_alca = $alt_alca / 100;

	$estiva = $_POST["base1"] + 60;
	$estiva = $estiva * 2;
	$estiva = $estiva / 100;

	$valor_estiva = $quilo_int * $gramat_alca * $estiva;

if ($_POST["alca_material"] == "fita") {
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
} elseif ($_POST["alca_material"] == "tecido") {
	$gramat_alca = 0.095;
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
}

	$valor_alca_unica = $valor_alca;

if ($tipo_cost_alca == "ponto_fixo") {
	$peso_fio_alca = 0.0743;
	$peso_fio_estiva = 0.0743;
} elseif ($tipo_cost_alca == "dupla" || $tipo_cost_alca == "dupla2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.1486;
	$peso_fio_estiva = 0.1486;
} elseif ($tipo_cost_alca == "overlock" || $tipo_cost_alca == "overlock2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.367;
	$peso_fio_estiva = 0.367;
}

if ($_POST["carga_nominal"] >= "1500") {
	$costura_alca = $_POST["alca_fix_altura"] * 8;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
} else {
	$costura_alca = $_POST["alca_fix_altura"] * 6;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
}

$costura_alca_met = $costura_alca / 100;
$costura_alca_gramat = $peso_fio_alca * 10;
$costura_alca = $costura_alca_met * $costura_alca_gramat / 1000;
$costura_alca = $costura_alca * $quilo_fio;

$costura_estiva = 25 * 6;
$costura_estiva = $costura_estiva * 4;
$costura_estiva_met = $costura_estiva / 100;
$costura_estiva_gramat = $peso_fio_estiva * 10;
$costura_estiva = $costura_estiva_met * $costura_estiva_gramat / 1000;
$costura_estiva = $costura_estiva * $quilo_fio;

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	$costura_alca_ved_met = $costura_alca_met;
	$costura_alca_ved_gramat = $peso_fio_ved_alca * 10;
	$costura_alca_ved = $costura_alca_ved_met * $costura_alca_ved_gramat / 1000;
	$costura_alca_ved = $costura_alca_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alças espalmadas + estivas",
	"",
	number_format((float)$alt_alca+$estiva, 2),
	"",
	number_format((float)$valor_alca+$costura_alca+$valor_estiva+$costura_estiva, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças espalmadas",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Alças)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_alca_met, 2),
	number_format((float)$costura_alca_gramat, 2),
	number_format((float)$costura_alca, 2),
	"0",0,0,0);

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Alças)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_alca_ved_met, 2),
		number_format((float)$costura_alca_ved_gramat, 2),
		number_format((float)$costura_alca_ved, 2),
		"0",0,0,0);
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças estivas",
	number_format((float)$quilo_int, 2),
	number_format((float)$estiva, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_estiva, 2),
	"0",
	$larg_alca,
	$estiva*100/2,
	2);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Estivas)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_estiva_met, 2),
	number_format((float)$costura_estiva_gramat, 2),
	number_format((float)$costura_estiva, 2),
	"0",0,0,0);

	$valor_alca = $valor_alca + $valor_estiva + $costura_alca + $costura_alca_ved + $costura_estiva;


if ($_POST["reforco_vao_livre"] == "1" || $_POST["reforco_vao_livre"] == true) {
	$valor_reforco_vao_livre = $alca_altura / 100;
	$valor_reforco_vao_livre = $valor_reforco_vao_livre * 2 * $quilo_int * 0.028 * 4;
	$valor_alca = $valor_alca + $valor_reforco_vao_livre;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de vão livre",
	number_format((float)$quilo_int, 2),
	number_format((float)$alca_altura*2*4/100, 2),
	number_format((float)28, 0),
	number_format((float)$valor_reforco_vao_livre, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alca_altura, 2),
	4);

}

if ($_POST["reforco_fixacao"] == "1" || $_POST["reforco_fixacao"] == true) {
	$valor_reforco_fixacao = 0.3 * 0.4 * $quilo_lamin * 0.160 * 4;
	$valor_alca = $valor_alca + $valor_reforco_fixacao;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de fixação",
	number_format((float)$quilo_lamin, 2),
	number_format((float)0.3*0.4*4, 2),
	number_format((float)175, 0),
	number_format((float)$valor_reforco_fixacao, 2),
	"0",
	number_format(30, 2),
	number_format(40, 2),
	4);
}

if ($_POST["alca_dupla"] == "1" || $_POST["alca_dupla"] == true) {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alça dupla",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca_unica, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);
/*
echo "<BR>PRECO QUILO: ".number_format((float)$quilo_int, 2);
echo "<BR>ALT ALCA: ".number_format((float)$alt_alca, 2);
echo "<BR>GRAMAT ALCA: ".number_format((float)$gramat_alca*1000, 0);
echo "<BR>VALOR ALCA DUPLA: ".number_format((float)$valor_alca_unica, 2);
//die();
*/
}


/* ============ ALÇAS - 4 CANTO + 2 ESTIVAS ============ */

} elseif ($_POST["alca"] == "6_2canto") {

	$alca_altura = $_POST["alca_altura"];
	$alca_fix_altura = $_POST["alca_fix_altura"];

	$bag_altura = $_POST["altura"] - 10;
	$bag_altura = $bag_altura - $alca_fix_altura;

	$alt_alca = $alca_altura + $alca_fix_altura;
	$alt_alca = $alt_alca * 2;

	$alt_alca = $alt_alca + $bag_altura;

	$alt_alca = $alt_alca * 4;
	$alt_alca = $alt_alca / 100;


	$estiva = $_POST["base1"] + 60;
	$estiva = $estiva * 2;
	$estiva = $estiva / 100;

	$valor_estiva = $quilo_int * $gramat_alca * $estiva;

if ($_POST["alca_material"] == "fita") {
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
} elseif ($_POST["alca_material"] == "tecido") {
	$gramat_alca = 0.095;
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
}

if ($tipo_cost_esvaz == "simples" || $tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
	$peso_fio_ved_descarga = 0.8;
	$peso_fio_descarga = 0.1119;
} elseif ($tipo_cost_esvaz == "dupla") {
	$peso_fio_descarga = 0.2238;
}

if ($tipo_cost_alca == "ponto_fixo") {
	$peso_fio_alca = 0.0743;
	$peso_fio_estiva = 0.0743;
} elseif ($tipo_cost_alca == "dupla" || $tipo_cost_alca == "dupla2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.1486;
	$peso_fio_estiva = 0.1486;
} elseif ($tipo_cost_alca == "overlock" || $tipo_cost_alca == "overlock2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.367;
	$peso_fio_estiva = 0.367;
}

if ($_POST["carga_nominal"] >= "1500") {
	$costura_alca = $_POST["alca_fix_altura"] * 8;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
} else {
	$costura_alca = $_POST["alca_fix_altura"] * 6;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
}

$costura_alca_met = ($costura_alca + ($_POST["altura"] - 10)*8) / 100;
$costura_alca_gramat = $peso_fio_alca * 10;
$costura_alca = $costura_alca_met * $costura_alca_gramat / 1000;
$costura_alca = $costura_alca * $quilo_fio;

$costura_estiva = 25 * 6;
$costura_estiva = $costura_estiva * 4;
$costura_estiva_met = $costura_estiva / 100;
$costura_estiva_gramat = $peso_fio_estiva * 10;
$costura_estiva = $costura_estiva_met * $costura_estiva_gramat / 1000;
$costura_estiva = $costura_estiva * $quilo_fio;

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	$costura_alca_ved_met = $costura_alca_met;
	$costura_alca_ved_gramat = $peso_fio_ved_alca * 10;
	$costura_alca_ved = $costura_alca_ved_met * $costura_alca_ved_gramat / 1000;
	$costura_alca_ved = $costura_alca_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alças de canto + estivas",
	"",
	number_format((float)$alt_alca, 2),
	"",
	number_format((float)$valor_alca + $valor_estiva + $costura_alca + $costura_alca_ved, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alt_alca*100/4, 2),
	4);

	$valor_alca_unica = $valor_alca;
	$valor_alca = $valor_alca + $costura_alca + $costura_alca_ved;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Alças)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_alca_met, 2),
	number_format((float)$costura_alca_gramat, 2),
	number_format((float)$costura_alca, 2),
	"0",0,0,0);

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Alças)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_alca_ved_met, 2),
		number_format((float)$costura_alca_ved_gramat, 2),
		number_format((float)$costura_alca_ved, 2),
		"0",0,0,0);
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças estivas",
	number_format((float)$quilo_int, 2),
	number_format((float)$estiva, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_estiva, 2),
	"0",
	$larg_alca,
	$estiva*100/2,
	2);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Estivas)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_estiva_met, 2),
	number_format((float)$costura_estiva_gramat, 2),
	number_format((float)$costura_estiva, 2),
	"0",0,0,0);

	$valor_alca_unica = $valor_alca;
	$valor_alca = $valor_alca + $valor_estiva + $costura_alca + $costura_alca_ved + $costura_estiva;


if ($_POST["reforco_vao_livre"] == "1" || $_POST["reforco_vao_livre"] == true) {
	$valor_reforco_vao_livre = $alca_altura / 100;
	$valor_reforco_vao_livre = $valor_reforco_vao_livre * 2 * $quilo_int * 0.028 * 4;
	$valor_alca = $valor_alca + $valor_reforco_vao_livre;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de vão livre",
	number_format((float)$quilo_int, 2),
	number_format((float)$alca_altura*2*4/100, 2),
	number_format((float)28, 0),
	number_format((float)$valor_reforco_vao_livre, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alca_altura, 2),
	4);

}

if ($_POST["reforco_fixacao"] == "1" || $_POST["reforco_fixacao"] == true) {
	$valor_reforco_fixacao = 0.3 * 0.4 * $quilo_lamin * 0.160 * 4;
	$valor_alca = $valor_alca + $valor_reforco_fixacao;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de fixação",
	number_format((float)$quilo_lamin, 2),
	number_format((float)0.3*0.4*4, 2),
	number_format((float)175, 0),
	number_format((float)$valor_reforco_fixacao, 2),
	"0",
	number_format(30, 2),
	number_format(40, 2),
	4);
}

if ($_POST["alca_dupla"] == "1" || $_POST["alca_dupla"] == true) {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alça dupla",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca_unica, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);
}

/* ============ ALÇAS - 4 ESPALMADAS + 4 ARGOLAS ============ */

} elseif ($_POST["alca"] == "8_4argola") {

	$alca_altura = $_POST["alca_altura"];
	$alca_fix_altura = $_POST["alca_fix_altura"];

	$alt_alca = $alca_altura + $alca_fix_altura;
	$alt_alca = $alt_alca * 2;
	$alt_alca = $alt_alca * 4;
	$alt_alca = $alt_alca / 100;

	$valor_argola = $quilo_int * 0.006 * 0.4 * 4;

if ($_POST["alca_material"] == "fita") {
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
} elseif ($_POST["alca_material"] == "tecido") {
	$gramat_alca = 0.095;
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
}

	$valor_alca_unica = $valor_alca;


if ($tipo_cost_alca == "ponto_fixo") {
	$peso_fio_alca = 0.0743;
} elseif ($tipo_cost_alca == "dupla" || $tipo_cost_alca == "dupla2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.1486;
} elseif ($tipo_cost_alca == "overlock" || $tipo_cost_alca == "overlock2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.367;
}

if ($_POST["carga_nominal"] >= "1500") {
	$costura_alca = $_POST["alca_fix_altura"] * 8;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
} else {
	$costura_alca = $_POST["alca_fix_altura"] * 6;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
}

$costura_alca_met = $costura_alca / 100;
$costura_alca_gramat = $peso_fio_alca * 10;
$costura_alca = $costura_alca_met * $costura_alca_gramat / 1000;
$costura_alca = $costura_alca * $quilo_fio;

$costura_argola = 15 * 2;
$costura_argola += 8 * 2;
$costura_argola = $costura_argola * 4;
$costura_argola_met = $costura_argola / 100;
$costura_argola_gramat = $peso_fio_alca * 10;
$costura_argola = $costura_argola_met * $costura_argola_gramat / 1000;
$costura_argola = $costura_argola * $quilo_fio;

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	$costura_alca_ved_met = $costura_alca_met;
	$costura_alca_ved_gramat = $peso_fio_ved_alca * 10;
	$costura_alca_ved = $costura_alca_ved_met * $costura_alca_ved_gramat / 1000;
	$costura_alca_ved = $costura_alca_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alças espalmadas + argolas",
	"",
	number_format((float)$alt_alca+1.6, 2),
	"",
	number_format((float)$valor_alca+$costura_alca+$valor_argola+$costura_argola, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças espalmadas",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Alças)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_alca_met, 2),
	number_format((float)$costura_alca_gramat, 2),
	number_format((float)$costura_alca, 2),
	"0",0,0,0);

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Alças)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_alca_ved_met, 2),
		number_format((float)$costura_alca_ved_gramat, 2),
		number_format((float)$costura_alca_ved, 2),
		"0",0,0,0);
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Argolas",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.6", 2),
	number_format((float)6, 0),
	number_format((float)$valor_argola, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Argolas)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_argola_met, 2),
	number_format((float)$costura_argola_gramat, 2),
	number_format((float)$costura_argola, 2),
	"0",0,0,0);

	$valor_alca = $valor_alca + $valor_argola + $costura_alca + $costura_alca_ved + $costura_argola;

if ($_POST["reforco_vao_livre"] == "1" || $_POST["reforco_vao_livre"] == true) {
	$valor_reforco_vao_livre = $alca_altura / 100;
	$valor_reforco_vao_livre = $valor_reforco_vao_livre * 2 * $quilo_int * 0.028 * 4;
	$valor_alca = $valor_alca + $valor_reforco_vao_livre;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de vão livre",
	number_format((float)$quilo_int, 2),
	number_format((float)$alca_altura*2*4/100, 2),
	number_format((float)28, 0),
	number_format((float)$valor_reforco_vao_livre, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alca_altura, 2),
	4);

}

if ($_POST["reforco_fixacao"] == "1" || $_POST["reforco_fixacao"] == true) {
	$valor_reforco_fixacao = 0.3 * 0.4 * $quilo_lamin * 0.160 * 4;
	$valor_alca = $valor_alca + $valor_reforco_fixacao;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de fixação",
	number_format((float)$quilo_lamin, 2),
	number_format((float)0.3*0.4*4, 2),
	number_format((float)175, 0),
	number_format((float)$valor_reforco_fixacao, 2),
	"0",
	number_format(30, 2),
	number_format(40, 2),
	4);
}

if ($_POST["alca_dupla"] == "1" || $_POST["alca_dupla"] == true) {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alça dupla",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca_unica, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);
}


/* ============ ALÇAS - 4 CANTO + 4 ARGOLAS ============ */

} elseif ($_POST["alca"] == "8_4canto") {

	$alca_altura = $_POST["alca_altura"];
	$alca_fix_altura = $_POST["alca_fix_altura"];

	$bag_altura = $_POST["altura"] - 10;
	$bag_altura = $bag_altura - $alca_fix_altura;

	$alt_alca = $alca_altura + $alca_fix_altura;
	$alt_alca = $alt_alca * 2;

	$alt_alca = $alt_alca + $bag_altura;

	$alt_alca = $alt_alca * 4;
	$alt_alca = $alt_alca / 100;

	$valor_argola = $quilo_int * 0.006 * 0.4 * 4;

if ($_POST["alca_material"] == "fita") {
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
} elseif ($_POST["alca_material"] == "tecido") {
	$gramat_alca = 0.095;
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
}

	$valor_alca_unica = $valor_alca;

if ($tipo_cost_alca == "ponto_fixo") {
	$peso_fio_alca = 0.0743;
} elseif ($tipo_cost_alca == "dupla" || $tipo_cost_alca == "dupla2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.1486;
} elseif ($tipo_cost_alca == "overlock" || $tipo_cost_alca == "overlock2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.367;
}

if ($_POST["carga_nominal"] >= "1500") {
	$costura_alca = $_POST["alca_fix_altura"] * 8;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
} else {
	$costura_alca = $_POST["alca_fix_altura"] * 6;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
}

$costura_alca_met = ($costura_alca + ($_POST["altura"] - 10)*8) / 100;
$costura_alca_gramat = $peso_fio_alca * 10;
$costura_alca = $costura_alca_met * $costura_alca_gramat / 1000;
$costura_alca = $costura_alca * $quilo_fio;

$costura_argola = 15 * 2;
$costura_argola += 8 * 2;
$costura_argola = $costura_argola * 4;
$costura_argola_met = $costura_argola / 100;
$costura_argola_gramat = $peso_fio_alca * 10;
$costura_argola = $costura_argola_met * $costura_argola_gramat / 1000;
$costura_argola = $costura_argola * $quilo_fio;

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	$costura_alca_ved_met = $costura_alca_met;
	$costura_alca_ved_gramat = $peso_fio_ved_alca * 10;
	$costura_alca_ved = $costura_alca_ved_met * $costura_alca_ved_gramat / 1000;
	$costura_alca_ved = $costura_alca_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alças de canto + argolas",
	"",
	number_format((float)$alt_alca+1.6, 2),
	"",
	number_format((float)$valor_alca+$costura_alca+$valor_argola+$costura_argola, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças de canto",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Alças)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_alca_met, 2),
	number_format((float)$costura_alca_gramat, 2),
	number_format((float)$costura_alca, 2),
	"0",0,0,0);

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Alças)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_alca_ved_met, 2),
		number_format((float)$costura_alca_ved_gramat, 2),
		number_format((float)$costura_alca_ved, 2),
		"0",0,0,0);
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Argolas",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.6", 2),
	number_format((float)6, 0),
	number_format((float)$valor_argola, 2),
	"0",1.5,160,4);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Argolas)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_argola_met, 2),
	number_format((float)$costura_argola_gramat, 2),
	number_format((float)$costura_argola, 2),
	"0",0,0,0);

	$valor_alca = $valor_alca + $valor_argola + $costura_alca + $costura_alca_ved + $costura_argola;

if ($_POST["reforco_vao_livre"] == "1" || $_POST["reforco_vao_livre"] == true) {
	$valor_reforco_vao_livre = $alca_altura / 100;
	$valor_reforco_vao_livre = $valor_reforco_vao_livre * 2 * $quilo_int * 0.028 * 4;
	$valor_alca = $valor_alca + $valor_reforco_vao_livre;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de vão livre",
	number_format((float)$quilo_int, 2),
	number_format((float)$alca_altura*2*4/100, 2),
	number_format((float)28, 0),
	number_format((float)$valor_reforco_vao_livre, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alca_altura, 2),
	4);

}

if ($_POST["reforco_fixacao"] == "1" || $_POST["reforco_fixacao"] == true) {
	$valor_reforco_fixacao = 0.3 * 0.4 * $quilo_lamin * 0.160 * 4;
	$valor_alca = $valor_alca + $valor_reforco_fixacao;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de fixação",
	number_format((float)$quilo_lamin, 2),
	number_format((float)0.3*0.4*4, 2),
	number_format((float)175, 0),
	number_format((float)$valor_reforco_fixacao, 2),
	"0",
	number_format(30, 2),
	number_format(40, 2),
	4);
}

if ($_POST["alca_dupla"] == "1" || $_POST["alca_dupla"] == true) {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alça dupla",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca_unica, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);
}

/* ============ ALÇAS - 4 ESPALMADAS + 4 ARGOLAS + 2 ESTIVAS ============ */

} elseif ($_POST["alca"] == "10_4argola2estiva") {

	$alca_altura = $_POST["alca_altura"];
	$alca_fix_altura = $_POST["alca_fix_altura"];

	$alt_alca = $alca_altura + $alca_fix_altura;
	$alt_alca = $alt_alca * 2;
	$alt_alca = $alt_alca * 4;
	$alt_alca = $alt_alca / 100;

	$estiva = $_POST["base1"] + 60;
	$estiva = $estiva * 2;
	$estiva = $estiva / 100;

	$valor_estiva = $quilo_int * $gramat_alca * $estiva;

	$valor_argola = $quilo_int * 0.006 * 0.4 * 4;

if ($_POST["alca_material"] == "fita") {
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
} elseif ($_POST["alca_material"] == "tecido") {
	$gramat_alca = 0.095;
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
}

	$valor_alca_unica = $valor_alca;

if ($tipo_cost_alca == "ponto_fixo") {
	$peso_fio_alca = 0.0743;
	$peso_fio_estiva = 0.0743;
} elseif ($tipo_cost_alca == "dupla" || $tipo_cost_alca == "dupla2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.1486;
	$peso_fio_estiva = 0.1486;
} elseif ($tipo_cost_alca == "overlock" || $tipo_cost_alca == "overlock2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.367;
	$peso_fio_estiva = 0.367;
}


if ($_POST["carga_nominal"] >= "1500") {
	$costura_alca = $_POST["alca_fix_altura"] * 8;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
} else {
	$costura_alca = $_POST["alca_fix_altura"] * 6;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
}

$costura_alca_met = $costura_alca / 100;
$costura_alca_gramat = $peso_fio_alca * 10;
$costura_alca = $costura_alca_met * $costura_alca_gramat / 1000;
$costura_alca = $costura_alca * $quilo_fio;

$costura_estiva = 25 * 6;
$costura_estiva = $costura_estiva * 4;
$costura_estiva_met = $costura_estiva / 100;
$costura_estiva_gramat = $peso_fio_estiva * 10;
$costura_estiva = $costura_estiva_met * $costura_estiva_gramat / 1000;
$costura_estiva = $costura_estiva * $quilo_fio;

$costura_argola = 15 * 2;
$costura_argola += 8 * 2;
$costura_argola = $costura_argola * 4;
$costura_argola_met = $costura_argola / 100;
$costura_argola_gramat = $peso_fio_alca * 10;
$costura_argola = $costura_argola_met * $costura_argola_gramat / 1000;
$costura_argola = $costura_argola * $quilo_fio;

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	$costura_alca_ved_met = $costura_alca_met;
	$costura_alca_ved_gramat = $peso_fio_ved_alca * 10;
	$costura_alca_ved = $costura_alca_ved_met * $costura_alca_ved_gramat / 1000;
	$costura_alca_ved = $costura_alca_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alças espalmadas + estivas + argolas",
	"",
	number_format((float)$alt_alca+$estiva+1.6, 2),
	"",
	number_format((float)$valor_alca+$costura_alca+$valor_estiva+$costura_estiva+$valor_argola+$costura_argola, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças espalmadas",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Alças)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_alca_met, 2),
	number_format((float)$costura_alca_gramat, 2),
	number_format((float)$costura_alca, 2),
	"0",0,0,0);

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Alças)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_alca_ved_met, 2),
		number_format((float)$costura_alca_ved_gramat, 2),
		number_format((float)$costura_alca_ved, 2),
		"0",0,0,0);
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças estivas",
	number_format((float)$quilo_int, 2),
	number_format((float)$estiva, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_estiva, 2),
	"0",
	$larg_alca,
	$estiva*100/2,
	2);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Estivas)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_estiva_met, 2),
	number_format((float)$costura_estiva_gramat, 2),
	number_format((float)$costura_estiva, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Argolas",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.6", 2),
	number_format((float)6, 0),
	number_format((float)$valor_argola, 2),
	"0",
	1.5,
	160,
	4);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Argolas)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_argola_met, 2),
	number_format((float)$costura_argola_gramat, 2),
	number_format((float)$costura_argola, 2),
	"0",0,0,0);

	$valor_alca = $valor_alca + $costura_alca + $costura_alca_ved + $valor_estiva + $costura_estiva + $valor_argola + $costura_argola;


if ($_POST["reforco_vao_livre"] == "1" || $_POST["reforco_vao_livre"] == true) {
	$valor_reforco_vao_livre = $alca_altura / 100;
	$valor_reforco_vao_livre = $valor_reforco_vao_livre * 2 * $quilo_int * 0.028 * 4;
	$valor_alca = $valor_alca + $valor_reforco_vao_livre;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de vão livre",
	number_format((float)$quilo_int, 2),
	number_format((float)$alca_altura*2*4/100, 2),
	number_format((float)28, 0),
	number_format((float)$valor_reforco_vao_livre, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alca_altura, 2),
	4);

}

if ($_POST["reforco_fixacao"] == "1" || $_POST["reforco_fixacao"] == true) {
	$valor_reforco_fixacao = 0.3 * 0.4 * $quilo_lamin * 0.160 * 4;
	$valor_alca = $valor_alca + $valor_reforco_fixacao;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de fixação",
	number_format((float)$quilo_lamin, 2),
	number_format((float)0.3*0.4*4, 2),
	number_format((float)175, 0),
	number_format((float)$valor_reforco_fixacao, 2),
	"0",
	number_format(30, 2),
	number_format(40, 2),
	4);
}

if ($_POST["alca_dupla"] == "1" || $_POST["alca_dupla"] == true) {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alça dupla",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca_unica, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);
}

/* ============ ALÇAS - 4 CANTO + 4 ARGOLAS + 2 ESTIVAS ============ */

} elseif ($_POST["alca"] == "10_4canto2estiva") {

	$alca_altura = $_POST["alca_altura"];
	$alca_fix_altura = $_POST["alca_fix_altura"];

	$bag_altura = $_POST["altura"] - 10;
	$bag_altura = $bag_altura - $alca_fix_altura;

	$alt_alca = $alca_altura + $alca_fix_altura;
	$alt_alca = $alt_alca * 2;

	$alt_alca = $alt_alca + $bag_altura;

	$alt_alca = $alt_alca * 4;
	$alt_alca = $alt_alca / 100;

	$estiva = $_POST["base1"] + 60;
	$estiva = $estiva * 2;
	$estiva = $estiva / 100;

	$valor_estiva = $quilo_int * $gramat_alca * $estiva;

	$valor_argola = $quilo_int * 0.006 * 0.4 * 4;

if ($_POST["alca_material"] == "fita") {
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
} elseif ($_POST["alca_material"] == "tecido") {
	$gramat_alca = 0.095;
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
}

	$valor_alca_unica = $valor_alca;

if ($tipo_cost_alca == "ponto_fixo") {
	$peso_fio_alca = 0.0743;
	$peso_fio_estiva = 0.0743;
} elseif ($tipo_cost_alca == "dupla" || $tipo_cost_alca == "dupla2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.1486;
	$peso_fio_estiva = 0.1486;
} elseif ($tipo_cost_alca == "overlock" || $tipo_cost_alca == "overlock2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.367;
	$peso_fio_estiva = 0.367;
}


if ($_POST["carga_nominal"] >= "1500") {
	$costura_alca = $_POST["alca_fix_altura"] * 8;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
} else {
	$costura_alca = $_POST["alca_fix_altura"] * 6;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
}

$costura_alca_met = ($costura_alca + ($_POST["altura"] - 10)*8) / 100;
$costura_alca_gramat = $peso_fio_alca * 10;
$costura_alca = $costura_alca_met * $costura_alca_gramat / 1000;
$costura_alca = $costura_alca * $quilo_fio;

$costura_estiva = 25 * 6;
$costura_estiva = $costura_estiva * 4;
$costura_estiva_met = $costura_estiva / 100;
$costura_estiva_gramat = $peso_fio_estiva * 10;
$costura_estiva = $costura_estiva_met * $costura_estiva_gramat / 1000;
$costura_estiva = $costura_estiva * $quilo_fio;

$costura_argola = 15 * 2;
$costura_argola += 8 * 2;
$costura_argola = $costura_argola * 4;
$costura_argola_met = $costura_argola / 100;
$costura_argola_gramat = $peso_fio_alca * 10;
$costura_argola = $costura_argola_met * $costura_argola_gramat / 1000;
$costura_argola = $costura_argola * $quilo_fio;

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	$costura_alca_ved_met = $costura_alca_met;
	$costura_alca_ved_gramat = $peso_fio_ved_alca * 10;
	$costura_alca_ved = $costura_alca_ved_met * $costura_alca_ved_gramat / 1000;
	$costura_alca_ved = $costura_alca_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alças de canto + estivas + argolas",
	"",
	number_format((float)$alt_alca+$estiva+1.6, 2),
	"",
	number_format((float)$valor_alca+$costura_alca+$valor_estiva+$costura_estiva+$valor_argola+$costura_argola, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças de canto",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Alças)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_alca_met, 2),
	number_format((float)$costura_alca_gramat, 2),
	number_format((float)$costura_alca, 2),
	"0",0,0,0);

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Alças)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_alca_ved_met, 2),
		number_format((float)$costura_alca_ved_gramat, 2),
		number_format((float)$costura_alca_ved, 2),
		"0",0,0,0);
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças estivas",
	number_format((float)$quilo_int, 2),
	number_format((float)$estiva, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_estiva, 2),
	"0",
	$larg_alca,
	$estiva*100/2,
	2);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Estivas)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_estiva_met, 2),
	number_format((float)$costura_estiva_gramat, 2),
	number_format((float)$costura_estiva, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Argolas",
	number_format((float)$quilo_int, 2),
	number_format((float)"1.6", 2),
	number_format((float)6, 0),
	number_format((float)$valor_argola, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Argolas)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_argola_met, 2),
	number_format((float)$costura_argola_gramat, 2),
	number_format((float)$costura_argola, 2),
	"0",0,0,0);

	$valor_alca = $valor_alca + $costura_alca + $costura_alca_ved + $valor_estiva + $costura_estiva + $valor_argola + $costura_argola;


if ($_POST["reforco_vao_livre"] == "1" || $_POST["reforco_vao_livre"] == true) {
	$valor_reforco_vao_livre = $alca_altura / 100;
	$valor_reforco_vao_livre = $valor_reforco_vao_livre * 2 * $quilo_int * 0.028 * 4;
	$valor_alca = $valor_alca + $valor_reforco_vao_livre;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de vão livre",
	number_format((float)$quilo_int, 2),
	number_format((float)$alca_altura*2*4/100, 2),
	number_format((float)28, 0),
	number_format((float)$valor_reforco_vao_livre, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alca_altura, 2),
	4);

}

if ($_POST["reforco_fixacao"] == "1" || $_POST["reforco_fixacao"] == true) {
	$valor_reforco_fixacao = 0.3 * 0.4 * $quilo_lamin * 0.160 * 4;
	$valor_alca = $valor_alca + $valor_reforco_fixacao;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de fixação",
	number_format((float)$quilo_lamin, 2),
	number_format((float)0.3*0.4*4, 2),
	number_format((float)175, 0),
	number_format((float)$valor_reforco_fixacao, 2),
	"0",
	number_format(30, 2),
	number_format(40, 2),
	4);
}

if ($_POST["alca_dupla"] == "1" || $_POST["alca_dupla"] == true) {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alça dupla",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca_unica, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);
}

/* ============ ALÇAS - MODELO QMS / COLHEITA / ESTIVAS ============ */

} elseif ($_POST["alca"] == "2_mod_qms") {

	$alca_altura = $_POST["alca_altura"] * 2;
	$alca_fix_altura = $_POST["alca_fix_altura"] * 4;

	$alt_alca = $alca_altura + $alca_fix_altura;
	$alt_alca = $alt_alca / 100;

if ($_POST["alca_material"] == "fita") {
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
} elseif ($_POST["alca_material"] == "tecido") {
	$gramat_alca = 0.095;
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
}

if ($tipo_cost_alca == "ponto_fixo") {
	$peso_fio_alca = 0.0743;
} elseif ($tipo_cost_alca == "dupla" || $tipo_cost_alca == "dupla2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.1486;
} elseif ($tipo_cost_alca == "overlock" || $tipo_cost_alca == "overlock2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.367;
}

if ($_POST["carga_nominal"] >= "1500") {
	$costura_alca = $_POST["alca_fix_altura"] * 8;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
} else {
	$costura_alca = $_POST["alca_fix_altura"] * 6;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
}

$costura_alca_met = $costura_alca / 100;
$costura_alca_gramat = $peso_fio_alca * 10;
$costura_alca = $costura_alca_met * $costura_alca_gramat / 1000;
$costura_alca = $costura_alca * $quilo_fio;

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	$costura_alca_ved_met = $costura_alca_met;
	$costura_alca_ved_gramat = $peso_fio_ved_alca * 10;
	$costura_alca_ved = $costura_alca_ved_met * $costura_alca_ved_gramat / 1000;
	$costura_alca_ved = $costura_alca_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alças de estivas",
	"",
	number_format((float)$alt_alca, 2),
	"",
	number_format((float)$valor_alca+ $costura_alca + $costura_alca_ved, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Alças)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_alca_met, 2),
	number_format((float)$costura_alca_gramat, 2),
	number_format((float)$costura_alca, 2),
	"0",0,0,0);

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Alças)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_alca_ved_met, 2),
		number_format((float)$costura_alca_ved_gramat, 2),
		number_format((float)$costura_alca_ved, 2),
		"0",0,0,0);
}

	$valor_alca_unica = $valor_alca;
	$valor_alca = $valor_alca + $costura_alca + $costura_alca_ved;


if ($_POST["reforco_vao_livre"] == "1" || $_POST["reforco_vao_livre"] == true) {
	$valor_reforco_vao_livre = $alca_altura / 100;
	$valor_reforco_vao_livre = $valor_reforco_vao_livre * 2 * $quilo_int * 0.028 * 4;
	$valor_alca = $valor_alca + $valor_reforco_vao_livre;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de vão livre",
	number_format((float)$quilo_int, 2),
	number_format((float)$alca_altura*2*4/100, 2),
	number_format((float)28, 0),
	number_format((float)$valor_reforco_vao_livre, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alca_altura, 2),
	4);

}

if ($_POST["reforco_fixacao"] == "1" || $_POST["reforco_fixacao"] == true) {
	$valor_reforco_fixacao = 0.3 * 0.4 * $quilo_lamin * 0.160 * 4;
	$valor_alca = $valor_alca + $valor_reforco_fixacao;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de fixação",
	number_format((float)$quilo_lamin, 2),
	number_format((float)0.3*0.4*4, 2),
	number_format((float)175, 0),
	number_format((float)$valor_reforco_fixacao, 2),
	"0",
	number_format(30, 2),
	number_format(40, 2),
	4);
}

if ($_POST["alca_dupla"] == "1" || $_POST["alca_dupla"] == true) {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alça dupla",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca_unica, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);
}

/* ============ ALÇAS - 4 ALÇAS DE CANTO ============ */

} elseif ($_POST["alca"] == "4_alcacanto") {

	$alca_altura = $_POST["alca_altura"];
	$alca_fix_altura = $_POST["alca_fix_altura"];

	$bag_altura = $_POST["altura"] - 10;
	$bag_altura = $bag_altura - $alca_fix_altura;

	$alt_alca = $alca_altura + $alca_fix_altura;
	$alt_alca = $alt_alca * 2;

	$alt_alca = $alt_alca + $bag_altura;

	$alt_alca = $alt_alca * 4;
	$alt_alca = $alt_alca / 100;

if ($_POST["alca_material"] == "fita") {
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
} elseif ($_POST["alca_material"] == "tecido") {
	$gramat_alca = 0.095;
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
}

if ($tipo_cost_esvaz == "simples" || $tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
	$peso_fio_ved_descarga = 0.8;
	$peso_fio_descarga = 0.1119;
} elseif ($tipo_cost_esvaz == "dupla") {
	$peso_fio_descarga = 0.2238;
}

if ($tipo_cost_alca == "ponto_fixo") {
	$peso_fio_alca = 0.0743;
} elseif ($tipo_cost_alca == "dupla" || $tipo_cost_alca == "dupla2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.1486;
} elseif ($tipo_cost_alca == "overlock" || $tipo_cost_alca == "overlock2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.367;
}

if ($_POST["carga_nominal"] >= "1500") {
	$costura_alca = $_POST["alca_fix_altura"] * 8;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
} else {
	$costura_alca = $_POST["alca_fix_altura"] * 6;
	$costura_alca = $costura_alca + 20;
	$costura_alca = $costura_alca * 8;
}

$costura_alca_met = ($costura_alca + ($_POST["altura"] - 10)*8) / 100;
$costura_alca_gramat = $peso_fio_alca * 10;
$costura_alca = $costura_alca_met * $costura_alca_gramat / 1000;
$costura_alca = $costura_alca * $quilo_fio;

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	$costura_alca_ved_met = $costura_alca_met;
	$costura_alca_ved_gramat = $peso_fio_ved_alca * 10;
	$costura_alca_ved = $costura_alca_ved_met * $costura_alca_ved_gramat / 1000;
	$costura_alca_ved = $costura_alca_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alças de canto",
	"",
	number_format((float)$alt_alca, 2),
	"",
	number_format((float)$valor_alca + $costura_alca + $costura_alca_ved, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);

	$valor_alca_unica = $valor_alca;
	$valor_alca = $valor_alca + $costura_alca + $costura_alca_ved;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Alças)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_alca_met, 2),
	number_format((float)$costura_alca_gramat, 2),
	number_format((float)$costura_alca, 2),
	"0",0,0,0);

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Alças)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_alca_ved_met, 2),
		number_format((float)$costura_alca_ved_gramat, 2),
		number_format((float)$costura_alca_ved, 2),
		"0",0,0,0);
}

if ($_POST["reforco_vao_livre"] == "1" || $_POST["reforco_vao_livre"] == true) {
	$valor_reforco_vao_livre = $alca_altura / 100;
	$valor_reforco_vao_livre = $valor_reforco_vao_livre * 2 * $quilo_int * 0.028 * 4;
	$valor_alca = $valor_alca + $valor_reforco_vao_livre;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de vão livre",
	number_format((float)$quilo_int, 2),
	number_format((float)$alca_altura*2*4/100, 2),
	number_format((float)28, 0),
	number_format((float)$valor_reforco_vao_livre, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alca_altura, 2),
	4);

}

if ($_POST["reforco_fixacao"] == "1" || $_POST["reforco_fixacao"] == true) {
	$valor_reforco_fixacao = 0.3 * 0.4 * $quilo_lamin * 0.160 * 4;
	$valor_alca = $valor_alca + $valor_reforco_fixacao;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de fixação",
	number_format((float)$quilo_lamin, 2),
	number_format((float)0.3*0.4*4, 2),
	number_format((float)175, 0),
	number_format((float)$valor_reforco_fixacao, 2),
	"0",
	number_format(30, 2),
	number_format(40, 2),
	4);
}

if ($_POST["alca_dupla"] == "1" || $_POST["alca_dupla"] == true) {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alça dupla",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca_unica, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);
}


/* ============ ALÇAS - 4 ALÇAS DE CANTO ESPALMADAS ============ */

} elseif ($_POST["alca"] == "4_4_canto_espalmada") {

	$alca_altura = $_POST["alca_altura"];
	$alca_fix_altura = $_POST["alca_fix_altura"];

	$bag_altura = $_POST["altura"] - 10;
	$bag_altura = $bag_altura - $alca_fix_altura;

	$alt_alca = $alca_altura + $alca_fix_altura;
	$alt_alca = $alt_alca * 2;

	$alt_alca = $alt_alca + $bag_altura;

	$alt_alca = $alt_alca * 4;
	$alt_alca = $alt_alca / 100;

if ($_POST["alca_material"] == "fita") {
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
} elseif ($_POST["alca_material"] == "tecido") {
	$gramat_alca = 0.095;
	$valor_alca = $quilo_int * $gramat_alca * $alt_alca;
}

if ($tipo_cost_esvaz == "simples" || $tipo_cost_esvaz == "simples1ved" || $tipo_cost_esvaz == "simples2ved") {
	$peso_fio_descarga = 0.1119;
} elseif ($tipo_cost_esvaz == "dupla") {
	$peso_fio_descarga = 0.2238;
}

if ($tipo_cost_alca == "ponto_fixo") {
	$peso_fio_alca = 0.0743;
} elseif ($tipo_cost_alca == "dupla" || $tipo_cost_alca == "dupla2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.1486;
} elseif ($tipo_cost_alca == "overlock" || $tipo_cost_alca == "overlock2ved") {
	$peso_fio_ved_alca = 0.8;
	$peso_fio_alca = 0.367;
}

$costura_alca = $_POST["altura"] - 10;
$costura_alca = $costura_alca * 4;
$costura_alca_met = $costura_alca / 100;
$costura_alca_gramat = $peso_fio_alca * 10;
$costura_alca = $costura_alca_met * $costura_alca_gramat / 1000;
$costura_alca = $costura_alca * $quilo_fio;

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	$costura_alca_ved_met = $costura_alca_met;
	$costura_alca_ved_gramat = $peso_fio_ved_alca * 10;
	$costura_alca_ved = $costura_alca_ved_met * $costura_alca_ved_gramat / 1000;
	$costura_alca_ved = $costura_alca_ved * $quilo_fio_vedante;
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alças de canto espalmadas",
	"",
	number_format((float)$alt_alca, 2),
	"",
	number_format((float)$valor_alca + $costura_alca + $costura_alca_ved, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Alças",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);

	$valor_alca_unica = $valor_alca;
	$valor_alca = $valor_alca + $costura_alca + $costura_alca_ved;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Fio de costura (Alças)",
	number_format((float)$quilo_fio, 2),
	number_format((float)$costura_alca_met, 2),
	number_format((float)$costura_alca_gramat, 2),
	number_format((float)$costura_alca, 2),
	"0",0,0,0);

if ($tipo_cost_alca == "dupla2ved" || $tipo_cost_alca == "overlock2ved") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio vedante (Alças)",
		number_format((float)$quilo_fio_vedante, 2),
		number_format((float)$costura_alca_ved_met, 2),
		number_format((float)$costura_alca_ved_gramat, 2),
		number_format((float)$costura_alca_ved, 2),
		"0",0,0,0);
}

if ($_POST["reforco_vao_livre"] == "1" || $_POST["reforco_vao_livre"] == true) {
	$valor_reforco_vao_livre = $alca_altura / 100;
	$valor_reforco_vao_livre = $valor_reforco_vao_livre * 2 * $quilo_int * 0.028 * 4;
	$valor_alca = $valor_alca + $valor_reforco_vao_livre;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de vão livre",
	number_format((float)$quilo_int, 2),
	number_format((float)$alca_altura*2*4/100, 2),
	number_format((float)28, 0),
	number_format((float)$valor_reforco_vao_livre, 2),
	"0",
	number_format($larg_alca, 2),
	number_format($alca_altura, 2),
	4);

}

if ($_POST["reforco_fixacao"] == "1" || $_POST["reforco_fixacao"] == true) {
	$valor_reforco_fixacao = 0.3 * 0.4 * $quilo_lamin * 0.160 * 4;
	$valor_alca = $valor_alca + $valor_reforco_fixacao;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Reforço de fixação",
	number_format((float)$quilo_lamin, 2),
	number_format((float)0.3*0.4*4, 2),
	number_format((float)175, 0),
	number_format((float)$valor_reforco_fixacao, 2),
	"0",
	number_format(30, 2),
	number_format(40, 2),
	4);
}

if ($_POST["alca_dupla"] == "1" || $_POST["alca_dupla"] == true) {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Alça dupla",
	number_format((float)$quilo_int, 2),
	number_format((float)$alt_alca, 2),
	number_format((float)$gramat_alca*1000, 0),
	number_format((float)$valor_alca_unica, 2),
	"0",
	number_format($larg_alca, 2),
	number_format(($alca_altura+$alca_fix_altura)*2, 2),
	4);
}

}

/* ============ LINER ============ */

if ($_POST["liner"] == "liner_fertil"){

$bag_altura = $_POST["altura"];
$base1 = $_POST["base1"];
$base2 = $_POST["base2"];

$espessura_liner = $_POST["liner_espessura"];
$tipo_liner = $_POST["tipo_liner"];

if ($tipo_liner == "liner_transp") {
	$desc_tipo_liner = "Liner - Fertilizante - Virgem";
} elseif ($tipo_liner == "liner_canela") {
	$desc_tipo_liner = "Liner - Fertilizante - Canela";
} elseif ($tipo_liner == "liner_cristal") {
	$desc_tipo_liner = "Liner - Fertilizante - Cristal";
} else {
	$desc_tipo_liner = "Liner - Fertilizante";
}

if ($_POST["liner"] == "liner_externo") {
	$desc_tipo_liner .= " - (externo)";
}

if ($base1 != $base2) {
	if ($base1 < $base2) {
		$base_maior = $base2;
	} else {
		$base_maior = $base1;
	}
} else {
	$base_maior = $base1;
}

	$diam_carga = $_POST["carga1"];
	$alt_carga = $_POST["carga2"];
	$diam_descarga = $_POST["descarga1"];
	$alt_descarga = $_POST["descarga2"];

	$base1 = $base1 * 2;
	$base2 = $base2 * 2;

	$perimetro_liner = $base1 + $base2;
	$largura_liner = $perimetro_liner / 2;

if ($_POST["carga"] != "vazio") {
	if ($diam_carga != "") {
		if ($_POST["carga"] == "c_afunilada") {
			$comp_liner_carga = $alt_carga;
		} else {
			$comp_liner_carga = $base_maior - $diam_carga;
			$comp_liner_carga = $comp_liner_carga / 2;
			$comp_liner_carga = $comp_liner_carga + $alt_carga;
		}
	} else {
		$comp_liner_carga = $alt_carga;
		if ($alt_carga == "") {
			$comp_liner_carga = $base_maior + $comp_liner_carga ;
		}
	}
}
	$comp_liner_descarga = $base_maior / 2;
	$comprimento_liner = $comp_liner_carga + $bag_altura + $comp_liner_descarga + 15;

$tipo_liner = "terceiro_".$tipo_liner;

$preco_quilo_liner = mysqli_query($conn,"SELECT * FROM preco_kilo");
while($row_liner = mysqli_fetch_array($preco_quilo_liner)) {
	if ($row_liner['tipo'] == $tipo_liner) {
		$quilo_liner = $row_liner["".$fornecedora.""];
	}
}

	$valor_liner = $largura_liner * $comprimento_liner * $espessura_liner; // * 0.922; = 496,8
	$valor_liner_kg = $valor_liner / 1000000;
	$valor_liner = $valor_liner_kg * $quilo_liner;

$sql_liner = "INSERT INTO `pedidos_liner` (`id`, `pedido`, `revisao`, `larg_liner`, `comp_liner`, `espess_liner`) VALUES (NULL,'".$no_pedido."','".$revisao."','".$largura_liner."','".$comprimento_liner."','".$espessura_liner."')";
$detalhes_liner = mysqli_query( $conn, $sql_liner );
if(! $detalhes_liner ) { die('Não foi possível gravar detalhes do liner: ' . mysql_error()); }

$peso_fio_liner = 0.059;

if ($_POST["fix_liner"] == "colado" || $_POST["fix_liner"] == "colado_costurado") {
	if ($_POST["segmento_cliente"] == 1) {
		$preco_cola_liner = mysqli_query($conn,"SELECT * FROM preco_kilo");
		while($row_cola = mysqli_fetch_array($preco_cola_liner)) {
			if ($row_cola['tipo'] == "cola_incolor") {
				$quilo_cola = $row_cola["".$fornecedora.""];
				$desc_cola = "Cola Incolor";
			}
		}
	} else {
		$preco_cola_liner = mysqli_query($conn,"SELECT * FROM preco_kilo");
		while($row_cola = mysqli_fetch_array($preco_cola_liner)) {
			if ($row_cola['tipo'] == "cola_marrom") {
				$quilo_cola = $row_cola["".$fornecedora.""];
				$desc_cola = "Cola Amarela";
			}
		}
	}

	$valor_cola_liner = $quilo_cola * 0.04;
}

if ($_POST["fix_liner"] == "costurado" || $_POST["fix_liner"] == "colado_costurado") {
	if ($_POST["carga"] == "c_saia" || $_POST["carga"] == "c_tipo_as") {
		$costura_liner = $_POST["base1"] * 2;
		$costura_liner += $_POST["base2"] * 2;
		$costura_liner += 25;
		$costura_liner_met = $costura_liner / 100;
		$costura_liner_gramat = $peso_fio_liner * 10;
		$costura_liner = $costura_liner_met * $costura_liner_gramat / 1000;
		$costura_liner = $costura_liner * $quilo_fio_pp;
	} elseif ($_POST["carga"] == "c_afunilada") {
		$costura_liner = $_POST["carga1"] * 4;
		$costura_liner += 25;
		$costura_liner_met = $costura_liner / 100;
		$costura_liner_gramat = $peso_fio_liner * 10;
		$costura_liner = $costura_liner_met * $costura_liner_gramat / 1000;
		$costura_liner = $costura_liner * $quilo_fio_pp;
	} else {
		if ($_POST["c_quadrado"] == "c_quadrado") {
			$costura_liner = $_POST["carga1"]*4;
			$costura_liner += 25;
			$costura_liner_met = $costura_liner / 100;
			$costura_liner_gramat = $peso_fio_liner * 10;
			$costura_liner = $costura_liner_met * $costura_liner_gramat / 1000;
			$costura_liner = $costura_liner * $quilo_fio_pp;
		} else {
			$costura_liner = $_POST["carga1"] * M_PI;
			$costura_liner += 25;
			$costura_liner_met = $costura_liner / 100;
			$costura_liner_gramat = $peso_fio_liner * 10;
			$costura_liner = $costura_liner_met * $costura_liner_gramat / 1000;
			$costura_liner = $costura_liner * $quilo_fio_pp;
		}
	}
}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Liner",
	"",
	number_format((float)$comprimento_liner/100, 2),
	"",
	number_format((float)$valor_liner+$valor_cola_liner+$costura_liner, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	$desc_tipo_liner,
	number_format((float)$quilo_liner, 2),
	number_format((float)$comprimento_liner/100, 2),
	number_format((float)$valor_liner_kg*100000/$comprimento_liner, 2),
	number_format((float)$valor_liner, 2),
	"0",
	number_format((float)$largura_liner, 2),
	number_format((float)$comprimento_liner, 2),
	1);

if ($_POST["fix_liner"] == "costurado" || $_POST["fix_liner"] == "colado_costurado") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio de costura (Liner)",
		number_format((float)$quilo_fio_pp, 2),
		number_format((float)$costura_liner_met, 2),
		number_format((float)$costura_liner_gramat, 2),
		number_format((float)$costura_liner, 2),
		"0",0,0,0);
}

if ($_POST["fix_liner"] == "colado" || $_POST["fix_liner"] == "colado_costurado") {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	$desc_cola,
	number_format((float)$quilo_cola, 2),
	number_format((float)"0.4", 2),
	100,
	number_format((float)$valor_cola_liner, 2),
	"0",0,0,0);

}

$valor_liner = $valor_liner+$valor_cola_liner+$costura_liner;

} elseif ($_POST["liner"] == "liner_gota"){

	$base1 = $_POST["base1"]+2.5;
	$base2 = $_POST["base2"]+2.5;

	$espessura_liner = $_POST["liner_espessura"];
	$tipo_liner = $_POST["tipo_liner"];

if ($tipo_liner == "liner_transp") {
	$desc_tipo_liner = "Liner - Virgem";
} elseif ($tipo_liner == "liner_canela") {
	$desc_tipo_liner = "Liner - Canela";
} elseif ($tipo_liner == "liner_cristal") {
	$desc_tipo_liner = "Liner - Cristal";
} else {
	$desc_tipo_liner = "Liner";
}

if ($_POST["liner"] == "liner_externo") {
	$desc_tipo_liner .= " - (externo)";
}

if ($base1 != $base2) {
	if ($base1 < $base2) {
		$base_maior = $base2;
	} else {
		$base_maior = $base1;
	}
} else {
	$base_maior = $base1;
}

$largura_liner = $base1 + $base2;

$tipo_liner = "terceiro_".$tipo_liner;

$preco_quilo_liner = mysqli_query($conn,"SELECT * FROM preco_kilo");
while($row_liner = mysqli_fetch_array($preco_quilo_liner)) {
	if ($row_liner['tipo'] == $tipo_liner) {
		$quilo_liner = $row_liner["".$fornecedora.""];
	}
}

$comprimento_liner = $_POST["altura"] + $_POST["alca_altura"] + (($base_maior-2.5)/2) + 35;
$valor_liner = $largura_liner * $comprimento_liner * $espessura_liner; // * 0.922; = 496,8
$valor_liner_kg = $valor_liner / 1000000;
$valor_liner = $valor_liner_kg * $quilo_liner;


$sql_liner = "INSERT INTO `pedidos_liner` (`id`, `pedido`, `revisao`, `larg_liner`, `comp_liner`, `espess_liner`) VALUES (NULL,'".$no_pedido."','".$revisao."','".$largura_liner."','".$comprimento_liner."','".$espessura_liner."')";
$detalhes_liner = mysqli_query( $conn, $sql_liner );
if(! $detalhes_liner ) { die('Não foi possível gravar detalhes do liner: ' . mysql_error()); }


$preco_cola_liner = mysqli_query($conn,"SELECT * FROM preco_kilo");
while($row_cola = mysqli_fetch_array($preco_cola_liner)) {
	if ($row_cola['tipo'] == "cola_marrom") {
		$quilo_cola = $row_cola["".$fornecedora.""];
		$desc_cola = "Cola Amarela";
	} elseif ($row_cola['tipo'] == "cola_incolor") {
		$quilo_cola = $row_cola["".$fornecedora.""];
		$desc_cola = "Cola Incolor";
	}
}

$valor_cola_liner = $quilo_cola * 0.025;
$cadarco_liner = $quilo_int * 0.006 * 1.4;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Liner",
	"",
	number_format((float)$comprimento_liner/100, 2),
	"",
	number_format((float)$valor_liner+$valor_cola_liner+$cadarco_liner, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	$desc_tipo_liner,
	number_format((float)$quilo_liner, 2),
	number_format((float)$comprimento_liner/100, 2),
	number_format((float)$valor_liner_kg*100000/$comprimento_liner, 2),
	number_format((float)$valor_liner, 2),
	"0",
	number_format((float)$largura_liner, 2),
	number_format((float)$comprimento_liner, 2),
	1);

if ($_POST["fix_liner"] == "colado") {

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	$desc_cola,
	number_format((float)$quilo_cola, 2),
	number_format((float)0.25, 2),
	100,
	number_format((float)$valor_cola_liner, 2),
	"0",0,0,0);

}

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	"Cadarço do liner",
	number_format((float)$quilo_int, 2),
	number_format((float)1.4, 2),
	number_format((float)6, 0),
	number_format((float)$cadarco_liner, 2),
	"0",0,0,0);


$valor_liner = $valor_liner + $valor_cola_liner + $cadarco_liner;

/*
echo "<pre>";
echo "VALOR LINER:".$valor_liner."\n";
echo "VALOR KG LINER:".$quilo_liner."\n";
echo "VALOR COLA:".$valor_cola_liner."\n";
echo "VALOR KG COLA:".$quilo_cola."\n";
echo "DESCRICAO:".$desc_tipo_liner."\n";
echo "ESPESSURA:".$espessura_liner."\n";
echo "TIPO LINER:".$tipo_liner."\n";
echo "BASE MAIOR:".$base_maior."\n";
echo "COMPRIMENTO LINER:".$comprimento_liner."\n\n";
echo $_POST["altura"]." + ".$_POST["alca_altura"]." + ".(($base_maior-2.5)/2)." + 35"."\n\n";
echo "LARGURA LINER:".$largura_liner."\n";
echo "FORNECEDORA:".$fornecedora."\n";
echo "</pre>";
die();
*/

} elseif ($_POST["liner"] == "liner_padrao" || $_POST["liner"] == "liner_afunilado" || $_POST["liner"] == "liner_sup_inf" || $_POST["liner"] == "liner_total_inf" || $_POST["liner"] == "liner_sup_fechado" || $_POST["liner"] == "liner_externo") {

if ($_POST["liner"] == "liner_externo") {
	$bag_altura = $_POST["altura"];
	$base1 = $_POST["base1"]+2.5;
	$base2 = $_POST["base2"]+2.5;
} else {
	$bag_altura = $_POST["altura"];
	$base1 = $_POST["base1"];
	$base2 = $_POST["base2"];
}

	$espessura_liner = $_POST["liner_espessura"];
	$tipo_liner = $_POST["tipo_liner"];

if ($tipo_liner == "liner_transp") {
	$desc_tipo_liner = "Liner - Virgem";
} elseif ($tipo_liner == "liner_canela") {
	$desc_tipo_liner = "Liner - Canela";
} elseif ($tipo_liner == "liner_cristal") {
	$desc_tipo_liner = "Liner - Cristal";
} else {
	$desc_tipo_liner = "Liner";
}

if ($_POST["liner"] == "liner_externo") {
	$desc_tipo_liner .= " - (externo)";
}

if ($base1 != $base2) {
	if ($base1 < $base2) {
		$base_maior = $base2;
	} else {
		$base_maior = $base1;
	}
} else {
	$base_maior = $base1;
}

	$diam_carga = $_POST["carga1"];
	$alt_carga = $_POST["carga2"];
	$diam_descarga = $_POST["descarga1"];
	$alt_descarga = $_POST["descarga2"];

	$base1 = $base1 * 2;
	$base2 = $base2 * 2;

	$perimetro_liner = $base1 + $base2;
	//if($perimetro_liner <= 360) { $perimetro_liner = 364; }
	//elseif($perimetro_liner <= 376) { $perimetro_liner = 380; }
	$largura_liner = $perimetro_liner / 2;

if ($_POST["carga"] != "vazio") {
	if ($diam_carga != "") {
		if ($_POST["carga"] == "c_afunilada") {
			$comp_liner_carga = $alt_carga;
		} else {
			$comp_liner_carga = $base_maior - $diam_carga;
			$comp_liner_carga = $comp_liner_carga / 2;
			$comp_liner_carga = $comp_liner_carga + $alt_carga;
		}
	} else {
		$comp_liner_carga = $alt_carga;
		if ($alt_carga == "") {
			$comp_liner_carga = $base_maior + $comp_liner_carga ;
		}
	}
}

if ($diam_descarga != "") {
	$comp_liner_descarga = $base_maior - $diam_descarga;
	$comp_liner_descarga = $comp_liner_descarga / 2;
	$comp_liner_descarga = $comp_liner_descarga + $alt_descarga;
} elseif ($_POST["descarga"] == "vazio") {
	$comp_liner_descarga = $base_maior - $diam_descarga;
	$comp_liner_descarga = $comp_liner_descarga / 2;
} else {
	$comp_liner_descarga = $alt_descarga;
}
	$comprimento_liner = $comp_liner_carga + $bag_altura + $comp_liner_descarga + 15;

$tipo_liner = "terceiro_".$tipo_liner;

$preco_quilo_liner = mysqli_query($conn,"SELECT * FROM preco_kilo");
while($row_liner = mysqli_fetch_array($preco_quilo_liner)) {
	if ($row_liner['tipo'] == $tipo_liner) {
		$quilo_liner = $row_liner["".$fornecedora.""];
	}
}

	$valor_liner = $largura_liner * $comprimento_liner * $espessura_liner; // * 0.922; = 496,8
	$valor_liner_kg = $valor_liner / 1000000;
	$valor_liner = $valor_liner_kg * $quilo_liner;

$sql_liner = "INSERT INTO `pedidos_liner` (`id`, `pedido`, `revisao`, `larg_liner`, `comp_liner`, `espess_liner`) VALUES (NULL,'".$no_pedido."','".$revisao."','".$largura_liner."','".$comprimento_liner."','".$espessura_liner."')";
$detalhes_liner = mysqli_query( $conn, $sql_liner );
if(! $detalhes_liner ) { die('Não foi possível gravar detalhes do liner: ' . mysql_error()); }

$peso_fio_liner = 0.059;

if ($_POST["fix_liner"] == "colado" || $_POST["fix_liner"] == "colado_costurado") {
	if ($_POST["segmento_cliente"] == 1) {
		$preco_cola_liner = mysqli_query($conn,"SELECT * FROM preco_kilo");
		while($row_cola = mysqli_fetch_array($preco_cola_liner)) {
			if ($row_cola['tipo'] == "cola_incolor") {
				$quilo_cola = $row_cola["".$fornecedora.""];
				$desc_cola = "Cola Incolor";
			}
		}
	} else {
		$preco_cola_liner = mysqli_query($conn,"SELECT * FROM preco_kilo");
		while($row_cola = mysqli_fetch_array($preco_cola_liner)) {
			if ($row_cola['tipo'] == "cola_marrom") {
				$quilo_cola = $row_cola["".$fornecedora.""];
				$desc_cola = "Cola Amarela";
			}
		}
	}

	$valor_cola_liner = $quilo_cola * 0.04;
}

if ($_POST["fix_liner"] == "costurado" || $_POST["fix_liner"] == "colado_costurado") {
	if ($_POST["carga"] == "c_saia" || $_POST["carga"] == "c_tipo_as") {
		$costura_liner = $_POST["base1"] * 2;
		$costura_liner += $_POST["base2"] * 2;
		$costura_liner += 25;
		$costura_liner_met = $costura_liner / 100;
		$costura_liner_gramat = $peso_fio_liner * 10;
		$costura_liner = $costura_liner_met * $costura_liner_gramat / 1000;
		$costura_liner = $costura_liner * $quilo_fio_pp;
	} elseif ($_POST["carga"] == "c_afunilada") {
		$costura_liner = $_POST["carga1"] * 4;
		$costura_liner += 25;
		$costura_liner_met = $costura_liner / 100;
		$costura_liner_gramat = $peso_fio_liner * 10;
		$costura_liner = $costura_liner_met * $costura_liner_gramat / 1000;
		$costura_liner = $costura_liner * $quilo_fio_pp;
	} else {
		if ($_POST["c_quadrado"] == "c_quadrado") {
			$costura_liner = $_POST["carga1"]*4;
			$costura_liner += 25;
			$costura_liner_met = $costura_liner / 100;
			$costura_liner_gramat = $peso_fio_liner * 10;
			$costura_liner = $costura_liner_met * $costura_liner_gramat / 1000;
			$costura_liner = $costura_liner * $quilo_fio_pp;
		} else {
			$costura_liner = $_POST["carga1"] * M_PI;
			$costura_liner += 25;
			$costura_liner_met = $costura_liner / 100;
			$costura_liner_gramat = $peso_fio_liner * 10;
			$costura_liner = $costura_liner_met * $costura_liner_gramat / 1000;
			$costura_liner = $costura_liner * $quilo_fio_pp;
		}
	}
}



GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Liner",
	"",
	number_format((float)$comprimento_liner/100, 2),
	"",
	number_format((float)$valor_liner+$valor_cola_liner+$costura_liner, 2),
	"0",0,0,0);

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	$desc_tipo_liner,
	number_format((float)$quilo_liner, 2),
	number_format((float)$comprimento_liner/100, 2),
	number_format((float)$valor_liner_kg*100000/$comprimento_liner, 2),
	number_format((float)$valor_liner, 2),
	"0",
	number_format((float)$largura_liner, 2),
	number_format((float)$comprimento_liner, 2),
	1);

if ($_POST["fix_liner"] == "costurado" || $_POST["fix_liner"] == "colado_costurado") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Fio de costura (Liner)",
		number_format((float)$quilo_fio_pp, 2),
		number_format((float)$costura_liner_met, 2),
		number_format((float)$costura_liner_gramat, 2),
		number_format((float)$costura_liner, 2),
		"0",0,0,0);
}

if ($_POST["fix_liner"] == "colado" || $_POST["fix_liner"] == "colado_costurado") {


GravaDet (NULL,
	$no_pedido,
	$revisao,
	"2",
	$desc_cola,
	number_format((float)$quilo_cola, 2),
	number_format((float)"0.4", 2),
	100,
	number_format((float)$valor_cola_liner, 2),
	"0",0,0,0);


}


$valor_liner = $valor_liner+$valor_cola_liner+$costura_liner;


}

/* ============ IMPRESSÃO ============ */

if ($_POST["sel_faces"] != "") {

	$qtde_lados = 0;

	$sel_faces = $_POST["sel_faces"];
	$sel_faces = strtolower($sel_faces);

	if (strpos($sel_faces,"d") !== false) {
		$qtde_lados = $qtde_lados + 1;
	}
	if (strpos($sel_faces,"c") !== false) {
		$qtde_lados = $qtde_lados + 1;
	}
	if (strpos($sel_faces,"b") !== false) {
		$qtde_lados = $qtde_lados + 1;
	}
	if (strpos($sel_faces,"a") !== false) {
		$qtde_lados = $qtde_lados + 1;
	}

$valor_impressao = $qtde_lados*$preco_impressao;

	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"1",
		"Impressão - ".$qtde_lados." lado(s)",
		number_format((float)$preco_impressao, 2),
		number_format((float)$qtde_lados, 2),
		1000,
		number_format((float)$valor_impressao, 2),
		"0",0,0,0);
}

/* ============ PORTA-ETIQUETA ============ */

if ($_POST["porta_etq1"] == true || $_POST["porta_etq2"] == true || $_POST["porta_etq3"] == true || $_POST["porta_etq4"] == true) {

$qtde_ptetq = 0;

if ($_POST["porta_etq1"] == true) {
	$qtde_ptetq += 1;
}
if ($_POST["porta_etq2"] == true) {
	$qtde_ptetq += 1;
}
if ($_POST["porta_etq3"] == true) {
	$qtde_ptetq += 1;
}
if ($_POST["porta_etq4"] == true) {
	$qtde_ptetq += 1;
}

$valor_portaetq = $qtde_ptetq*0.25;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Porta etiqueta - ".$qtde_ptetq." unidade(s)",
	number_format((float)0.25, 2),
	number_format((float)$qtde_ptetq, 2),
	1000,
	number_format((float)$valor_portaetq, 2),
	"0",0,0,0);
}

/* ============ VELCRO ============ */

if ($_POST["velcro"] == true) {

	$query_velcro = mysqli_query($conn,"SELECT * FROM `preco_kilo` WHERE `tipo` LIKE 'velcro'");
	$row_velcro = mysqli_fetch_array($query_velcro);

	$valor_velcro = $row_velcro["".$fornecedora.""];

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Velcro (Macho/Femea)",
	number_format((float)$valor_velcro, 2),
	number_format((float)0.05, 2),
	"1",
	number_format((float)$valor_velcro*0.05, 2),
	"0",0,0,0);

}

/* ============ CINTA DE TRAVAMENTO ============ */

if ($_POST["cinta_trav"] == true) {

	$valor_cinta_trav_met = $_POST["base1"]*2;
	$valor_cinta_trav_met += $_POST["base2"]*2;
	$valor_cinta_trav_met += 10;
	$valor_cinta_trav = $valor_cinta_trav_met * 0.28;
	$valor_cinta_trav = $valor_cinta_trav * $quilo_int / 1000;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Cinta de travamento",
	number_format((float)$quilo_int, 2),
	number_format((float)$valor_cinta_trav_met/100, 2),
	"28",
	number_format((float)$valor_cinta_trav, 2),
	"0",0,0,0);

}

/* ============ GRAVATA DE TRAVAMENTO ============ */

if ($_POST["gravata_trav"] == true) {

	$gravata_med = 22;
	$valor_gravata_trav_met = $gravata_med*4;
	$valor_gravata_trav = $valor_gravata_trav_met * 0.28;
	$valor_gravata_trav = $valor_gravata_trav * $quilo_int / 1000;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Gravata de travamento",
	number_format((float)$quilo_int, 2),
	number_format((float)$valor_gravata_trav_met/100, 2),
	"28",
	number_format((float)$valor_gravata_trav, 2),
	"0",0,0,0);

}

/* ============ SAPATA - PACKLESS ============ */

if ($_POST["sapata"] == true) {

	$query_pl = mysqli_query($conn,"SELECT * FROM `preco_kilo` WHERE `tipo` LIKE 'packless'");
	$row_pl = mysqli_fetch_array($query_pl);

	$valor_sapata = $row_pl["".$fornecedora.""];


GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Pack Less",
	"",
	"1",
	"",
	number_format((float)$valor_sapata, 2),
	"0",0,0,0);

}

/* ============ TAMPA FUNDO FALSO ============ */

if ($_POST["flap"] == true) {
	$base1 = $_POST["base1"] + 10;
	$base2 = $_POST["base2"] + 10;
	$gramat_corpo = $_POST["gramat_corpo"] / 1000;

	$valor_flap_met = $base1 * $base2;
	$valor_flap = $valor_flap_met * $gramat_corpo * $quilo_descarga / 10000;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Tampa para fundo falso",
	number_format((float)$quilo_descarga, 2),
	number_format((float)$valor_flap_met/10000, 2),
	number_format((float)$gramat_corpo*1000, 0),
	number_format((float)$valor_flap, 2),
	"0",0,0,0);

}

/* ============ SUBTOTAL ============ */

$subtotal = $valor_corpo + $valor_carga + $valor_descarga + $valor_alca + $valor_liner + $valor_sapata + $valor_flap + $valor_impressao + $valor_portaetq; // + $costura_corpo + $costura_trava; // + $costura_alca + $costura_liner;

/*
echo "Corpo: ".$valor_corpo."<br>Carga: ".$valor_carga."<br>Descarga: ".$valor_descarga."<br>Alça: ".$valor_alca."<br>Liner: ".$valor_liner."<br>Sapata: ".$valor_sapata."<br>Flap: ".$valor_flap."<br>Impressao: ".$valor_impressao."<br>Porta etq: ".$valor_portaetq;//."<br>".$costura_corpo."<br>".$costura_trava."<br>".$costura_alca."<br>".$costura_liner;
echo "<br><br>SUBTOTAL: ".$subtotal;
echo "<br><br><br><br>------------";
*/

/* ============ MATERIAIS E INSUMOS DIRETOS / INDIRETOS / AUXILIARES ============ */

	$query_mat_aux = mysqli_query($conn,"SELECT * FROM `preco_kilo` WHERE `tipo` LIKE 'mat_auxiliar'");
	$row_mat_aux = mysqli_fetch_array($query_mat_aux);

	$valor_mat_auxiliar = $row_mat_aux["".$fornecedora.""];


/* ============ CIF (CUSTOS INDIRETOS DE FABRICAÇÃO) ============ */
/* ============ MAO DE OBRA DIRETA ============ */

$fornec_cif_mo = str_replace("valor_","",$fornecedora);

/* ===================== TRAVADO - 4 PAINEIS ===================== */
if ($_POST["altura"] <= "150") {
	if ($_POST["corpo"] == "qowad4" || $_POST["corpo"] == "qowadlf") {
		$query_cif_mo_txt = "SELECT * FROM `cif_mo` WHERE `tipo` LIKE '4_menor'";
	}
} else {
	if ($_POST["corpo"] == "qowad4" || $_POST["corpo"] == "qowadlf" || $_POST["corpo"] == "outros" && $_POST["altura"] > "150") {
		$query_cif_mo_txt = "SELECT * FROM `cif_mo` WHERE `tipo` LIKE '4_maior'";
	}
}
	/* ===================== TRAVADO - 8 PAINEIS ===================== */
if ($_POST["altura"] <= "150") {
	if ($_POST["corpo"] == "qowad8") {
		$query_cif_mo_txt = "SELECT * FROM `cif_mo` WHERE `tipo` LIKE '8_menor'";
	}
} else {
	if ($_POST["corpo"] == "qowad8") {
		$query_cif_mo_txt = "SELECT * FROM `cif_mo` WHERE `tipo` LIKE '8_maior'";
	}
}
if($_POST["class_uso"] == "6_6" || $_POST["class_uso"] == "6_7") {
	/* ===================== TUBULAR HOMOLOGADO ===================== */
	if ($_POST["corpo"] == "gota" || $_POST["corpo"] == "cowa" || $_POST["corpo"] == "cowa2" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "cowafi" || $_POST["corpo"] == "cms") {
		$query_cif_mo_txt = "SELECT * FROM `cif_mo` WHERE `tipo` LIKE 'tub_hom'";
	}
} else {
	if ($_POST["liner"] == "vazio") {
		/* ===================== TUBULAR SEM LINER ===================== */
		if ($_POST["corpo"] == "gota" || $_POST["corpo"] == "cowa" || $_POST["corpo"] == "cowa2" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "cowafi" || $_POST["corpo"] == "cms") {
			$query_cif_mo_txt = "SELECT * FROM `cif_mo` WHERE `tipo` LIKE 'tub_sem".$class_produtividade."'";
		}
	} else {
		/* ===================== TUBULAR COM LINER ===================== */
		if ($_POST["corpo"] == "gota" || $_POST["corpo"] == "cowa" || $_POST["corpo"] == "cowa2" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "cowafi" || $_POST["corpo"] == "cms") {
			$query_cif_mo_txt = "SELECT * FROM `cif_mo` WHERE `tipo` LIKE 'tub_com'";
		}
	}
}

/* ===================== CORPO U ===================== */
if ($_POST["corpo"] == "qowac" || $_POST["corpo"] == "qowacf" || $_POST["corpo"] == "rof") {
	$query_cif_mo_txt = "SELECT * FROM `cif_mo` WHERE `tipo` LIKE 'corpo_u'";
}
/* ===================== CORPO - 4 PAINEIS ===================== */
if ($_POST["corpo"] == "qowa" || $_POST["corpo"] == "qowa2" || $_POST["corpo"] == "qowaf" || $_POST["corpo"] == "qowao" || $_POST["corpo"] == "qowafi" || $_POST["corpo"] == "qowam" || $_POST["corpo"] == "qowaa" || $_POST["corpo"] == "qowat" || $_POST["corpo"] == "qhe" || $_POST["corpo"] == "qhe_ref" || $_POST["corpo"] == "qms") {
	$query_cif_mo_txt = "SELECT * FROM `cif_mo` WHERE `tipo` LIKE 'corpo_4'";
}

/* ===================== TRAVADO TUBULAR ===================== */
if ($_POST["corpo"] == "cowad" ) {
	$query_cif_mo_txt = "SELECT * FROM `cif_mo` WHERE `tipo` LIKE 'trav_tub'";
}

$query_cif_mo = mysqli_query($conn,$query_cif_mo_txt);
$row_cif_mo = mysqli_fetch_array($query_cif_mo);

$valor_cif = $row_cif_mo["cif_".$fornec_cif_mo.""];
$valor_mao_obra = $row_cif_mo["mo_".$fornec_cif_mo.""];

$total = $subtotal + $valor_mat_auxiliar + $valor_cif + $valor_mao_obra;

/*
echo $subtotal."<br><br>";
echo $total." - ".$subtotal." - ".$valor_mat_auxiliar." - ".$valor_cif." - ".$valor_mao_obra;;
die();
*/

/* ============ CALCULO DE IMPOSTOS ============ */

$uf_cliente = $_POST["uf_cliente"];

/* ============ MARGEM ============ */

// CORPO - TUBULAR
if($_POST["corpo"] == "cowa" || $_POST["corpo"] == "cowa2" || $_POST["corpo"] == "cowaf" || $_POST["corpo"] == "cowafi" || $_POST["corpo"] == "cms") {
	if($_POST["qtde"] <= 1000){
		$valor_margem = 25.00;
	} elseif($_POST["qtde"] <= 5000){
		$valor_margem = 15.00;
	} elseif($_POST["qtde"] <= 7500){
		$valor_margem = 12.00;
	} elseif($_POST["qtde"] <= 10000){
		$valor_margem = 10.00;
	} elseif($_POST["qtde"] >= 10001){
		$valor_margem = 7.50;
	}
// CORPO - TRAVADO
} elseif($_POST["corpo"] == "qowad4" || $_POST["corpo"] == "qowad8" || $_POST["corpo"] == "qowadlf") {
	if($_POST["qtde"] <= 1000){
		$valor_margem = 30.00;
	} elseif($_POST["qtde"] <= 5000){
		$valor_margem = 20.00;
	} elseif($_POST["qtde"] >= 5001){
		$valor_margem = 15.00;
	}
// CORPO - OUTROS
} else {
	$valor_margem = 20.00;
}

/*
echo $valor_margem;
echo "<pre>";
print_r($_POST);
echo "</pre>";
die();
*/

/* ============ ICMS ============ */

$unidade_forn = substr($fornecedora, -2);

if ($unidade_forn == "sp" && $_POST["usocons"] == "sim") {
	$valor_icms = "18";
} else {
	$query_icms = mysqli_query($conn,"SELECT * FROM `imposto_icms` WHERE `unidade` LIKE '".$unidade_forn."'");
	$row_icms = mysqli_fetch_array($query_icms);
	$valor_icms = $row_icms["".$uf_cliente.""];
}

/* ============ PIS ============ */

$query_pis = mysqli_query($conn,"SELECT * FROM `imposto_pis` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_pis = mysqli_fetch_array($query_pis);

$valor_pis = $row_pis["".$uf_cliente.""];


/* ============ COFINS ============ */

$query_cofins = mysqli_query($conn,"SELECT * FROM `imposto_cofins` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_cofins = mysqli_fetch_array($query_cofins);

$valor_cofins = $row_cofins["".$uf_cliente.""];


/* ============ IR ============ */

$query_ir = mysqli_query($conn,"SELECT * FROM `imposto_ir` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_ir = mysqli_fetch_array($query_ir);

$valor_ir = $row_ir["".$uf_cliente.""];


/* ============ CSLL ============ */

$query_csll = mysqli_query($conn,"SELECT * FROM `imposto_csll` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_csll = mysqli_fetch_array($query_csll);

$valor_csll = $row_csll["".$uf_cliente.""];


/* ============ INSS ============ */

$query_inss = mysqli_query($conn,"SELECT * FROM `imposto_inss` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_inss = mysqli_fetch_array($query_inss);

$valor_inss = $row_inss["".$uf_cliente.""];


/* ============ PERDA ============ */

$query_perda = mysqli_query($conn,"SELECT * FROM `imposto_perda` WHERE `unidade` LIKE '".$unidade_forn."'");
$row_perda = mysqli_fetch_array($query_perda);

$valor_perda = $row_perda["".$uf_cliente.""];


/* ============ FRETE ============ */

$distancia_aprox = $_POST["distancia_aprox"];

if ($distancia_aprox == 0) {
	$valor_frete = 0;
} elseif ($distancia_aprox == 1) {
	$valor_frete = 2.00;
} elseif ($distancia_aprox == 2) {
	$valor_frete = 4.00;
} elseif ($distancia_aprox == 3) {
	$valor_frete = 6.00;
}


/* ============ COMISSÃO ============ */

$id_user = $_SESSION['user']['id'];

$query_comissao = mysqli_query($conn,"SELECT * FROM `users` WHERE `id` LIKE '".$id_user."'");
$row_comissao = mysqli_fetch_array($query_comissao);

$valor_comissao = $row_comissao["comissao"];



/* ============ CUSTO FINANCEIRO ============ */


$query_cfin = mysqli_query($conn,"SELECT * FROM preco_kilo");
while($row_cfin = mysqli_fetch_array($query_cfin)) {
	if ($row_cfin['tipo'] == "c_financeiro") {
		$c_financeiro = $row_cfin["".$fornecedora.""];
	}
}

$prazo = $_POST["prazo"];
$prazo = explode("/",$prazo);
$prazo_qtde = count($prazo);
$prazo = array_sum($prazo);
$prazo = $prazo / $prazo_qtde;
$prazo = ceil($prazo);

if ($prazo == "0" || $prazo == "-1") {
	$valor_cfinanceiro = 0;
} else {
	$valor_cfinanceiro = $c_financeiro * $prazo / 28;
}


/* ============ MARGEM ============ */


if($_POST["mercado"] == "ext") {
	$valor_icms = 0;
	$valor_pis = 0;
	$valor_cofins = 0;
}

$valor_imposto = $valor_icms + $valor_pis + $valor_cofins + $valor_ir + $valor_csll + $valor_inss + $valor_perda + $valor_frete + $valor_comissao + $valor_cfinanceiro + $valor_margem;

$valor_final_venda = 100-$valor_imposto;
$valor_final_venda = $valor_final_venda/100;
$valor_final_venda = $total/$valor_final_venda;

$valor_semimposto = $valor_perda + $valor_frete + $valor_comissao + $valor_cfinanceiro + $valor_margem;
//echo "<br><br>IMPOSTOS: ".$valor_semimposto."%<br><br>";
$valor_semimposto = 100 - $valor_semimposto;
$valor_semimposto /= 100;
$valor_semimposto = $total / $valor_semimposto;
//echo "VALOR SEM IMPOSTOS: R$ ".$valor_semimposto."<br><br>";
//die();

/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
die();
*/

$nome_cliente = $_POST["nome_cliente"];
$segmento_cliente = $_POST["segmento_cliente"];
$cidade_cliente = $_POST["cidade_cliente"];
$uf_cliente = $_POST["uf_cliente"];
$selecao = $_POST["selecao"];
$cnpj_cpf = $_POST["cnpj_cpf"];
$qtde = $_POST["qtde"];
$referencia = $_POST["referencia"];
//$embarques = addslashes(htmlentities($_POST["embarques"]));
//$representante = addslashes(htmlentities($_POST["representante"]));
$embarques = addslashes($_POST["embarques"]);
$representante = addslashes($_POST["representante"]);
$mercado = $_POST["mercado"];
$frete = $_POST["frete"];
$fornecedora = $_POST["fornecedora"];
$usocons = $_POST["usocons"];
$submit = $_POST["submit"];
$distancia_aprox = $_POST["distancia_aprox"];
$prazo = $_POST["prazo"];
$nome_prod = $_POST["nome_prod"];
$dens_aparente = $_POST["dens_aparente"];
$temperatura = $_POST["temperatura"];
$class_uso = $_POST["class_uso"];
$transporte = addslashes($_POST["transporte"]);
$dem_mensal = $_POST["dem_mensal"];
$dem_anual = $_POST["dem_anual"];
$carga_nominal = $_POST["carga_nominal"];
$armazenagem = $_POST["armazenagem"];
$corpo = $_POST["corpo"];
$base1 = $_POST["base1"];
$base2 = $_POST["base2"];
$altura = $_POST["altura"];
//if ($_POST["plastificado"] == "on") { $plastificado = "1"; } else { $plastificado = "0"; }
$corpo_cor = $_POST["corpo_cor"];
$corpo_cor_outro = $_POST["corpo_cor_outro"];
$lamin_cor = $_POST["lamin_cor"];
$lamin_cor_outro = $_POST["lamin_cor_outro"];
//$gramat_corpo = $_POST["gramat_corpo"];
$gramat_corpo = $gramat_corpo_grava;
$gramat_forro = $_POST["gramat_forro"];
if ($_POST["cost_fio_topo"] == "on") { $cost_fio_topo = "1"; } else { $cost_fio_topo = "0"; }
if ($_POST["cost_fio_base"] == "on") { $cost_fio_base = "1"; } else { $cost_fio_base = "0"; }
if ($_POST["trava_rede"] == "on") { $trava_rede = "1"; } else { $trava_rede = "0"; }
$carga = $_POST["carga"];
$c_quadrado = $_POST["c_quadrado"];
$carga1 = $_POST["carga1"];
$carga2 = $_POST["carga2"];
$tampa = $gramat_tampa_grava;//$_POST["gramat_tampa"];
$valvula = $_POST["gramat_valvula"];
$saia = $_POST["gramat_saia"];
$descarga = $_POST["descarga"];
$d_redondo = $_POST["d_redondo"];
$descarga1 = $_POST["descarga1"];
$descarga2 = $_POST["descarga2"];
$fundo = $gramat_fundo_grava;//$_POST["gramat_fundo_d"];
$valvula_d = $_POST["gramat_valvula_d"];
$alca = $_POST["alca"];
$alca_material = $_POST["alca_material"];
$alca_cor = $_POST["alca_cor"];
$alca_altura = $_POST["alca_altura"];
$alca_fix_altura = $_POST["alca_fix_altura"];
$alca_fixacao = $_POST["alca_fixacao"];
if ($_POST["reforco_vao_livre"] == "on") { $reforco_vao_livre = "1"; } else { $reforco_vao_livre = "0"; }
if ($_POST["reforco_fixacao"] == "on") { $reforco_fixacao = "1"; } else { $reforco_fixacao = "0"; }
if ($_POST["alca_dupla"] == "on") { $alca_dupla = "1"; } else { $alca_dupla = "0"; }
$alca_capac = $_POST["alca_capac"];
$liner = $_POST["liner"];
$tipo_liner = $_POST["tipo_liner"];
$liner_espessura = $_POST["liner_espessura"];
$fix_liner = $_POST["fix_liner"];
$no_cores = $_POST["no_cores"];
if ($_POST["imp_controle_viag"] == "on") { $imp_controle_viag = "1"; } else { $imp_controle_viag = "0"; }
if ($_POST["imp_num_seq"] == "on") { $imp_num_seq = "1"; } else { $imp_num_seq = "0"; }
$sel_faces = strtolower($_POST["sel_faces"]);
if ($_POST["porta_etq1"] == "on") { $porta_etq1 = "1"; } else { $porta_etq1 = "0"; }
$pos_porta_etq1 = $_POST["pos_porta_etq1"];
$mod_porta_etq1 = $_POST["mod_porta_etq1"];
if ($_POST["porta_etq2"] == "on") { $porta_etq2 = "1"; } else { $porta_etq2 = "0"; }
$pos_porta_etq2 = $_POST["pos_porta_etq2"];
$mod_porta_etq2 = $_POST["mod_porta_etq2"];
if ($_POST["porta_etq3"] == "on") { $porta_etq3 = "1"; } else { $porta_etq3 = "0"; }
$pos_porta_etq3 = $_POST["pos_porta_etq3"];
$mod_porta_etq3 = $_POST["mod_porta_etq3"];
if ($_POST["porta_etq4"] == "on") { $porta_etq4 = "1"; } else { $porta_etq4 = "0"; }
$pos_porta_etq4 = $_POST["pos_porta_etq4"];
$mod_porta_etq4 = $_POST["mod_porta_etq4"];
$fardo = $_POST["fardo"];
if ($_POST["fardo_pallet"] == "on") { $fardo_pallet = "1"; } else { $fardo_pallet = "0"; }
$palletizado = $_POST["palletizado"];
if ($_POST["fio_ved_travas"] == "on" || $_POST["fio_ved_travas"] == "1") { $fio_ved_travas = "1"; } else { $fio_ved_travas = "0"; }
if ($_POST["velcro"] == "on" || $_POST["velcro"] == "1") { $velcro = "1"; } else { $velcro = "0"; }
if ($_POST["cinta_trav"] == "on" || $_POST["cinta_trav"] == "1") { $cinta_trav = "1"; } else { $cinta_trav = "0"; }
if ($_POST["gravata_trav"] == "on" || $_POST["gravata_trav"] == "1") { $gravata_trav = "1"; $gravata_med = 22; } else { $gravata_trav = "0"; $gravata_med = 0; }
if ($_POST["sapata"] == "on" || $_POST["sapata"] == "1") { $sapata = "1"; } else { $sapata = "0"; }
if ($_POST["flap"] == "on" || $_POST["flap"] == "1") { $flap = "1"; } else { $flap = "0"; }
$unit = $_POST["unit"];

$vendedor = $_POST["vendedor"];

$obs_cliente = addslashes($_POST["obs_cliente"]);
$obs_comerciais = addslashes($_POST["obs_comerciais"]);

$class_prod = $_POST["class_prod"];


$log = "Orçamento modificado por: ".htmlentities($_SESSION['user']['nome'], ENT_QUOTES, 'UTF-8')." no dia ".date("d/m/Y - H:i:s").".";

/*
$vendedor = htmlentities($_SESSION['user']['nome'], ENT_QUOTES, 'UTF-8');
$id_vend = htmlentities($_SESSION['user']['id'], ENT_QUOTES, 'UTF-8');
*/

//print_r($_POST);

$data_ped = $_POST['data']; //date("Y-m-d H:i:s");

$valor_final = number_format((float)$valor_final_venda, 2);


$pedido = $no_pedido;


$query_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE `pedido` = '".$pedido."' ORDER BY `id` DESC"); // GROUP BY pedido");
$pedido = mysqli_fetch_array($query_pedidos);

$query_impostos = mysqli_query($conn,"SELECT * FROM `pedidos_extra` WHERE `id` LIKE (SELECT MAX(`id`) FROM `pedidos_extra` WHERE `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."') AND `pedido` LIKE '".$pedido["pedido"]."' AND `revisao` LIKE '".$pedido["revisao"]."' ORDER BY `id` DESC"); //AND `pedido` LIKE '".$pedido["pedido"]."'
$row_impostos = mysqli_fetch_array($query_impostos);

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

/*
if ($bd_valor_dolar != "0.00") {
	$cambio_dia = $bd_cambio_dia;
	$cambio_dolar = str_replace( ',', '.', $bd_cambio);
	$valor_final_dolar = $bd_valor_dolar;
	$cambio_data = $bd_cambio_data;
	echo "1111111";
} else {
*/

/*
require("dolar/class.UOLCotacoes.php");

$uol = new UOLCotacoes();
list($dolarComercialCompra, $dolarComercialVenda, $dolarTurismoCompra, $dolarTurismoVenda, $euroCompra, $euroVenda, $libraCompra, $libraVenda, $pesosCompra, $pesosVenda) = $uol->pegaValores();
*/
	//$cambio_dolar = str_replace( ',', '.', $dolarComercialCompra);
	
$query_cambio = "SELECT DISTINCT `dia`,`compra`,`venda` FROM `taxa_dolar` ORDER BY `taxa_dolar`.`id` DESC LIMIT 0,1";
$result_cambio = mysqli_query($conn,$query_cambio);
$cambio = mysqli_fetch_array($result_cambio);

	$cambio_dolar = $cambio["venda"];
	//$cambio_dolar = 3.8661;
	$cambio_dia = date("d/m/Y");
	if($cambio_dolar != "") {
		$valor_final_dolar = $valor_final_venda/$cambio_dolar;
	} else {
		$valor_final_dolar = 0;
	}
	$cambio_data = date("Y-m-d H:i:s");



if ($no_pedido == "") { $no_pedido = "NULL"; }
if ($revisao == "") { $revisao = "NULL"; }
if ($valor_mat_auxiliar == "") { $valor_mat_auxiliar = "0.00"; }
if ($valor_cif == "") { $valor_cif = "0.00"; }
if ($valor_mao_obra == "") { $valor_mao_obra = "0.00"; }
if ($total == "") { $total = "0.00"; }
if ($valor_icms == "") { $valor_icms = "0.00"; }
if ($valor_pis == "") { $valor_pis = "0.00"; }
if ($valor_cofins == "") { $valor_cofins = "0.00"; }
if ($valor_ir == "") { $valor_ir = "0.00"; }
if ($valor_csll == "") { $valor_csll = "0.00"; }
if ($valor_inss == "") { $valor_inss = "0.00"; }
if ($valor_perda == "") { $valor_perda = "0.00"; }
if ($valor_frete == "") { $valor_frete = "0.00"; }
if ($valor_comissao == "") { $valor_comissao = "0.00"; }
if ($valor_cfinanceiro == "") { $valor_cfinanceiro = "0.00"; }
if ($valor_margem == "") { $valor_margem = "0.00"; }
if ($valor_imposto == "") { $valor_imposto = "0.00"; }
if ($valor_final_dolar == "") { $valor_final_dolar = "0.00"; }
if ($cambio_dolar == "") { $cambio_dolar = "0.00"; }
if ($cambio_data == "") { $cambio_data = "00-00-0000"; }


$sql = "INSERT INTO `pedidos` (`id`, `pedido`, `nome_cliente`, `segmento_cliente`, `cidade_cliente`, `uf_cliente`, `selecao`, `cnpj_cpf`, `qtde`, `referencia`, `embarques`, `representante`, `mercado`, `frete`, `fornecedora`, `usocons`, `submit`, `distancia_aprox`, `prazo`, `nome_prod`, `dens_aparente`, `temperatura`, `class_uso`, `transporte`, `dem_mensal`, `dem_anual`, `carga_nominal`, `armazenagem`, `corpo`, `base1`, `base2`, `altura`, `plastificado`, `corpo_cor`, `corpo_cor_outro`, `lamin_cor`, `lamin_cor_outro`, `gramat_corpo`, `gramat_forro`, `trava_rede`, `carga`, `c_quadrado`, `carga1`, `carga2`, `tampa`, `valvula`, `saia`, `descarga`, `d_redondo`, `descarga1`, `descarga2`, `fundo`, `valvula_d`, `cost_fio_topo`, `cost_fio_base`, `alca`, `alca_material`, `alca_cor`, `alca_altura`, `alca_fix_altura`, `alca_fixacao`, `reforco_vao_livre`, `reforco_fixacao`, `alca_dupla`, `alca_capac`, `liner`, `tipo_liner`, `liner_espessura`, `fix_liner`, `no_cores`, `imp_controle_viag`, `imp_num_seq`, `sel_faces`, `porta_etq1`, `pos_porta_etq1`, `mod_porta_etq1`, `porta_etq2`, `pos_porta_etq2`, `mod_porta_etq2`, `porta_etq3`, `pos_porta_etq3`, `mod_porta_etq3`, `porta_etq4`, `pos_porta_etq4`, `mod_porta_etq4`, `fardo`, `fardo_pallet`, `palletizado`, `fio_ved_travas`, `velcro`, `cinta_trav`, `gravata`, `med_gravata`, `sapata`, `flap`, `unit`, `valor_custo`, `valor_final`, `log`, `revisao`, `vendedor`, `id_vend`, `data`, `obs_cliente`, `obs_comerciais`, `correcoes`, `status`) VALUES (NULL, '".$no_pedido."', '".$nome_cliente."', '".$segmento_cliente."', '".$cidade_cliente."', '".$uf_cliente."', '".$selecao."', '".$cnpj_cpf."', '".$qtde."', '".$referencia."', '".$embarques."', '".$representante."', '".$mercado."', '".$frete."', '".$fornecedora."', '".$usocons."', '".$submit."', '".$distancia_aprox."', '".$prazo."', '".$nome_prod."', '".$dens_aparente."', '".$temperatura."', '".$class_uso."', '".$transporte."', '".$dem_mensal."', '".$dem_anual."', '".$carga_nominal."', '".$armazenagem."', '".$corpo."', '".$base1."', '".$base2."', '".$altura."', '".$plastificado."', '".$corpo_cor."', '".$corpo_cor_outro."', '".$lamin_cor."', '".$lamin_cor_outro."', '".$gramat_corpo."', '".$gramat_forro."', '".$trava_rede."', '".$carga."', '".$c_quadrado."', '".$carga1."', '".$carga2."', '".$tampa."', '".$valvula."', '".$saia."', '".$descarga."', '".$d_redondo."', '".$descarga1."', '".$descarga2."', '".$fundo."', '".$valvula_d."', '".$cost_fio_topo."', '".$cost_fio_base."', '".$alca."', '".$alca_material."', '".$alca_cor."', '".$alca_altura."', '".$alca_fix_altura."', '".$alca_fixacao."', '".$reforco_vao_livre."', '".$reforco_fixacao."', '".$alca_dupla."', '".$alca_capac."', '".$liner."', '".$tipo_liner."', '".$liner_espessura."', '".$fix_liner."', '".$no_cores."', '".$imp_controle_viag."', '".$imp_num_seq."', '".$sel_faces."', '".$porta_etq1."', '".$pos_porta_etq1."', '".$mod_porta_etq1."', '".$porta_etq2."', '".$pos_porta_etq2."', '".$mod_porta_etq2."', '".$porta_etq3."', '".$pos_porta_etq3."', '".$mod_porta_etq3."', '".$porta_etq4."', '".$pos_porta_etq4."', '".$mod_porta_etq4."', '".$fardo."', '".$fardo_pallet."', '".$palletizado."', '".$fio_ved_travas."', '".$velcro."', '".$cinta_trav."', '".$gravata_trav."', '".$gravata_med."', '".$sapata."', '".$flap."', '".$unit."', '".$total."', '".$valor_final."', '".$log."', '".$revisao."', '".$vendedor."', '".$id_vend."', '".$data_ped."', '".$obs_cliente."', '".$obs_comerciais."', '".$correcoes."', '1')";
$sql_extra = "INSERT INTO `pedidos_extra` (`id`, `pedido`, `revisao`, `class_prod`, `mat_auxiliar`, `cif`, `mao_obra`, `custo_bag`, `icms`, `pis`, `cofins`, `ir`, `csll`, `inss`, `perda`, `frete`, `comissao`, `custo_fin`, `margem`, `imposto_total`, `valor_dolar`, `cambio`, `cambio_data`, `rev_valor`) VALUES (NULL, '".$no_pedido."', '".$revisao."', '".$class_prod."', '".$valor_mat_auxiliar."', '".$valor_cif."', '".$valor_mao_obra."', '".$total."', '".$valor_icms."', '".$valor_pis."', '".$valor_cofins."', '".$valor_ir."', '".$valor_csll."', '".$valor_inss."', '".$valor_perda."', '".$valor_frete."', '".$valor_comissao."', '".$valor_cfinanceiro."', '".$valor_margem."', '".$valor_imposto."', '".$valor_final_dolar."', '".$cambio_dolar."', '".$cambio_data."', '0')";

/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
echo "<br><br><br><pre>".$sql."</pre><br><br><br>";
echo "<br><br><br><pre>".$sql_extra."</pre><br><br><br>";
printf("Errormessage: %s\n", mysqli_error($conn));
die();
*/


$retval = mysqli_query( $conn, $sql );
$id_pedido = mysqli_insert_id($conn);
if(! $retval )
{
  die('Não foi possível gerar o orçamento: ' . mysqli_error($conn));
}

$retval_extra = mysqli_query( $conn, $sql_extra );
if(! $retval_extra )
{
  die('Não foi possível gerar extras do orçamento: ' . mysqli_error($conn));
}

/* ---- COSTURA ----- */

$sql_costura = "INSERT INTO `pedidos_costura` (`id`, `id_ped`, `no_ped`, `corpo`, `enchim`, `esvaz`, `alca`) VALUES (NULL, '".$id_pedido."', '".$no_pedido."', '".$tipo_cost_corpo."', '".$tipo_cost_enchim."', '".$tipo_cost_esvaz."', '".$tipo_cost_alca."')";

$retval_costura = mysqli_query( $conn, $sql_costura );
if(! $retval_costura )
{
  echo $sql_costura;
  die('Não foi possível gerar costura do orçamento: ' . mysqli_error($conn));
}

/* ---- CLIENTE ---- */

if ($id_cliente!="") {
	$sql_cliente = "INSERT INTO `pedidos_cliente` (`id`, `pedido`, `id_cliente`, `id_vend`, `data`) VALUES (NULL, '".(float)$no_pedido."', '".$id_cliente."', '".$_SESSION["user"]["id"]."', '".date('Y-m-d H:i:s')."')";

	$retval_cliente = mysqli_query( $conn, $sql_cliente );
	if(! $retval_cliente )
	{
	  echo $sql_cliente;
	  die('Não foi possível relacionar o cliente: ' . mysqli_error($conn));
	}
}

Log_Sis($no_pedido,$_SESSION['user']['id'],$_SESSION['user']['nome'],"Fez alteracoes no orcamento: ".sprintf('%05d', $no_pedido).".");


//header("Location: resumo.php?pedido=$no_pedido"); 
redirect("resumo.php?pedido=$no_pedido");
die("");

?>