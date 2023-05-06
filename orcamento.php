<?php 
//	require("../orcamentos/common.php"); 
	require("common.php"); 

	if(empty($_SESSION['user'])) 
    { 
        redirect("login.php"); 
        die("Redirecting to login.php"); 
    } 

	if ($_SESSION['user']['nivel'] == '6') {
        redirect("qualidade.php"); 
        die("Redirecting to qualidade.php"); 
	}

    require("cabecalho.php"); 
/*
	echo "<pre>";
	print_r($_SESSION);
	echo "</pre>";
*/

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

$query = "SELECT id, username, email, nome, nivel FROM users ORDER BY nome ASC"; 

try { 
	$stmt = $db->prepare($query); 
	$stmt->execute(); 
}
catch(PDOException $ex) { 
	die("Failed to run query: " . $ex->getMessage()); 
} 

$rows = $stmt->fetchAll(); 

?>

<div class="page-title">
	<div class="title_left">
		<h1>Orçamento</h1>
	</div>
</div>

<?php

if($_GET['acao'] == 'gravar') {

/*
echo "<pre>";
print_r($_POST);
echo "<pre>";
die();
*/

if ($_POST["nome_cliente"] == "") {
	header("Location: orcamento.php"); 
	die("Redirecionando para ORÇAMENTO."); 
}

if ($_POST["novo_cliente"] == "sim") {

	$novo_cliente = mysqli_query($conn,"INSERT INTO `cad_clientes` (`id`, `selecao`, `cnpj_cpf`, `insc_est`, `razao`, `nome`, `rua`, `numero`, `complemento`, `bairro`, `cidade`, `uf`, `cep`, `ddd`, `telefone`, `ramal`, `ddd_cel`, `celular`, `contato_com`, `email_com`, `contato_fin`, `email_fin`, `ramal_fin`, `segmento`, `id_vend`, `status`, `data`, `atualizacao`) VALUES (NULL, '".$_POST["selecao"]."', '".$_POST["cnpj_cpf"]."', '', '', '".$_POST["nome_cliente"]."', '', '', '', '', '".$_POST["cidade_cliente"]."', '".$_POST["uf_cliente"]."', '', '".$_POST["ddd"]."', '".$_POST["telefone"]."', '', '', '', '".$_POST["contato_com"]."', '".$_POST["email_com"]."', '', '', '', '".$_POST["segmento_cliente"]."', '".$_SESSION["user"]["id"]."', '2', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."'); ");
	if(! $novo_cliente ) { die('Não foi possível adicionar informações de cliente: ' . mysqli_error($conn)); }
	$id_cliente = mysqli_insert_id($conn);

} elseif ($_POST["novo_cliente"] == "nao") {

	$atualiza_cliente = mysqli_query( $conn, "UPDATE `cad_clientes` SET `ddd` = '".$_POST["ddd"]."', `telefone` = '".$_POST["telefone"]."', `email_com` = '".$_POST["email_com"]."', `nome` = '".$_POST["nome_cliente"]."', `cidade` = '".$_POST["cidade_cliente"]."', `uf` = '".$_POST["uf_cliente"]."', `selecao` = '".$_POST["selecao"]."', `cnpj_cpf` = '".$_POST["cnpj_cpf"]."', `segmento` = '".$_POST["segmento_cliente"]."', `atualizacao` = '".date('Y-m-d H:i:s')."' WHERE `id` LIKE '".$_POST["id_cliente"]."' LIMIT 1;");
	if(! $atualiza_cliente ) { die('Não foi possível atualizar informações de cliente: ' . mysqli_error($conn)); }
	$id_cliente = $_POST["id_cliente"];

}


/*
echo $id_cliente;

echo "<pre>";
print_r($_POST);
echo "</pre>";
die();
*/

if ($_POST["pedido_duplicado"] != "") {
	Log_Sis($_POST["pedido_duplicado"],$_SESSION['user']['id'],$_SESSION['user']['nome'],"Duplicou o orcamento: ".sprintf('%05d', $_POST["pedido_duplicado"]).".");
}


$fornecedora = $_POST["fornecedora"];
$uf_cliente = $_POST["uf_cliente"];

if($_POST["gramat_fundo_d"] == "") {
	$_POST["gramat_fundo_d"] = $_POST["gramat_corpo"];
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
		if ($row['tipo'] == "interno".$class_produtividade) {
			$quilo_int = $row["".$fornecedora.""];
			$quilo_corpo = $row["".$fornecedora.""];
			$quilo_carga = $row["".$fornecedora.""];
			$quilo_descarga = $row["".$fornecedora.""];
		} elseif ($row['tipo'] == "laminado".$class_produtividade) {
			$quilo_lamin = $row["".$fornecedora.""];
		} elseif ($row['tipo'] == $_POST["tampa"]) {
			$gramat_tampa = $_POST["gramat_tampa"]/1000;
/*
			if ($_POST["tampa"] == "tampa_pesada") {
				$gramat_tampa = $_POST["gramat_corpo"]/1000;
			} else {
				$gramat_tampa = $row["".$fornecedora.""];
			}
*/
		} elseif ($row['tipo'] == $_POST["valvula"]) {
			$gramat_valv = $_POST["gramat_valvula"]/1000;
			//$gramat_valv = $row["".$fornecedora.""];
		} elseif ($row['tipo'] == $_POST["valvula_d"]) {
			$gramat_valv_d = $_POST["gramat_valvula_d"]/1000;
			//$gramat_valv_d = $row["".$fornecedora.""];
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
/*
			if ($_POST["tampa"] == "tampa_pesada") {
				$gramat_tampa = $_POST["gramat_corpo"]/1000;
			} else {
				$gramat_tampa = $row["".$fornecedora.""];
			}
*/
		} elseif ($row['tipo'] == $_POST["valvula"]) {
			$gramat_valv = $_POST["gramat_valvula"]/1000;
			//$gramat_valv = $row["".$fornecedora.""];
		} elseif ($row['tipo'] == $_POST["valvula_d"]) {
			$gramat_valv_d = $_POST["gramat_valvula_d"]/1000;
			//$gramat_valv_d = $row["".$fornecedora.""];
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

$plastificado = 0;

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

if ($_POST["trava_rede"] == "on" || $_POST["trava_rede"] == "1") {
	$quilo_travas = $quilo_trava_rede;
} else {
	$quilo_travas = $quilo_lamin;
}

/*
if ($_POST["plastificado"] == "on" || $_POST["plastificado"] == "1") {
	$quilo_corpo = $quilo_lamin;
	$quilo_carga = $quilo_lamin;
	$quilo_descarga = $quilo_lamin;
	$quilo_v = $quilo_lamin;
}
*/

$query_pedido = mysqli_query($conn,"SELECT `pedido` FROM `pedidos` GROUP BY `pedido`");
while($row_pedido = mysqli_fetch_array($query_pedido)) {
	$pedido = $row_pedido["pedido"];
}

$pedido = $pedido + 1;
$revisao = "0";


$no_pedido = sprintf('%05d', $pedido);

//echo "NUMERO DO PEDIDO NOVO: ".$no_pedido."<BR><BR>";

/* ============ VARIAVEL - FIOS ============ */

$fio2000 = 2000;
$fio3000 = 3000;
$fio4000 = 4000;

$tipo_cost_corpo = $_POST["tipo_cost_corpo"];
$tipo_cost_enchim = $_POST["tipo_cost_enchim"];
$tipo_cost_esvaz = $_POST["tipo_cost_esvaz"];
$tipo_cost_alca = $_POST["tipo_cost_alca"];


/* ============ FUNCÃO PARA GRAVAR DETALHES ============ */


function GravaDet($f_id,$f_pedido,$f_revisao,$f_nivel,$f_desc,$f_valor_kg,$f_qtde_mat,$f_gramat,$f_valor,$f_m_quadrado,$f_largura,$f_corte,$f_qtde) {

global $conn;

if ($f_valor_kg == "") { $f_valor_kg = 0; }
if ($f_gramat == "") { $f_gramat = 0; }


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
    '0',
	'".$f_largura."',
	'".$f_corte."',
	'".$f_qtde."')";
$pedido_detalhe = mysqli_query( $conn, $sql_det );
if(! $pedido_detalhe ) { echo $sql_det; die('Não foi possível gerar detalhes do orçamento: / Desc:'. $f_desc . mysqli_error()); }

}


/* ============ CORPO PLANO SIMPLES ============ */

if ($_POST["corpo"] == "qowa" || $_POST["corpo"] == "qowao" || $_POST["corpo"] == "qowam" || $_POST["corpo"] == "qowaa" || $_POST["corpo"] == "qowat" || $_POST["corpo"] == "qhe" || $_POST["corpo"] == "qhe_ref" || $_POST["corpo"] == "qms") {

$base1 = $_POST["base1"]+10;
$base2 = $_POST["base2"]+10;
$altura = $_POST["altura"];
if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
	if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
		$altura += 3;
	} else {
		$altura += 5;
	}
	if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
	if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
		$altura += 3;
	} else {
		$altura += 5;
	}
	if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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

if ($_POST["trava_rede"] == "on" || $_POST["trava_rede"] == "1") {
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

if ($_POST["trava_rede"] == "on" || $_POST["trava_rede"] == "1") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Travas em rede",
		number_format((float)$quilo_travas, 2),
		number_format((float)$valor_trava_met / 10000, 2),
		number_format((float)53, 0),
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
if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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

if ($_POST["trava_rede"] == "on" || $_POST["trava_rede"] == "1") {
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

if ($_POST["trava_rede"] == "on" || $_POST["trava_rede"] == "1") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Travas em rede",
		number_format((float)$quilo_travas, 2),
		number_format((float)$valor_trava_met / 10000, 2),
		number_format((float)53, 0),
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
if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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

if ($_POST["trava_rede"] == "on" || $_POST["trava_rede"] == "1") {
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

if ($_POST["trava_rede"] == "on" || $_POST["trava_rede"] == "1") {
	GravaDet (NULL,
		$no_pedido,
		$revisao,
		"2",
		"Travas em rede",
		number_format((float)$quilo_travas, 2),
		number_format((float)$valor_trava_met / 10000, 2),
		number_format((float)53, 0),
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
if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
	$altura += 3;
} else {
	$altura += 5;
}
/*
if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
	$altura += 3;
} else {
	$altura += 5;
}
if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
		if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
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
		if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
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
		if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
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
		if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
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
	if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
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
	if($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") {
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
		if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
		if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
	if ($_POST["gramat_forro"] <= "100") {
		$quilo_forro = $quilo_leve;
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
		if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
	if ($_POST["gramat_forro"] <= "100") {
		$quilo_forro = $quilo_leve;
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
	if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
	if ($_POST["gramat_forro"] <= "100") {
		$quilo_forro = $quilo_leve;
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
	if($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") {
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
	if ($_POST["gramat_forro"] <= "100") {
		$quilo_forro = $quilo_leve;
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

$valor_portaetq = $qtde_ptetq*0.23;

GravaDet (NULL,
	$no_pedido,
	$revisao,
	"1",
	"Porta etiqueta - ".$qtde_ptetq." unidade(s)",
	number_format((float)0.23, 2),
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
echo "<br><br><br><br>";
echo "QUERY: ".$query_cif_mo_txt."<br><br>";
echo "NOME PRODUTO: ".$nome_prod."<br><br>";
echo "CORPO: ".$_POST["corpo"]."<br><br>";
echo "ALTURA: ".$_POST["altura"]."<br><br>";
echo "CLASSIFICACAO USO: ".$_POST["class_uso"]."<br><br>";
echo "FORNECEDORA: ".$fornec_cif_mo."<br><br>";
echo "SUBTOTAL: ".$subtotal."<br><br>";
echo "TOTAL: ".$total."<br><br>";
echo "CIF: ".$valor_cif."<br><br>";
echo "MAO OBRA: ".$valor_mao_obra;
echo "<br><br><br><br>";
echo "<pre>";
print_r($_POST);
echo "</pre>";
die();
*/

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
$valor_adm_comercial = 1;



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

$valor_imposto = $valor_icms + $valor_pis + $valor_cofins + $valor_ir + $valor_csll + $valor_inss + $valor_perda + $valor_frete + $valor_comissao + $valor_adm_comercial + $valor_cfinanceiro + $valor_margem;

$valor_final_venda = 100-$valor_imposto;
$valor_final_venda = $valor_final_venda/100;
$valor_final_venda = $total/$valor_final_venda;

$valor_semimposto = $valor_perda + $valor_frete + $valor_comissao + $valor_adm_comercial + $valor_cfinanceiro + $valor_margem;
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
// die();
*/

$nome_cliente = $_POST["nome_cliente"];
$segmento_cliente = $_POST["segmento_cliente"];
$cidade_cliente = $_POST["cidade_cliente"];
$uf_cliente = $_POST["uf_cliente"];
$selecao = $_POST["selecao"];
$cnpj_cpf = $_POST["cnpj_cpf"];
$qtde = $_POST["qtde"];
$referencia = $_POST["referencia"];
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
//if ($_POST["plastificado"] == "on" || $_POST["plastificado"] == "1") { $plastificado = "1"; } else { $plastificado = "0"; }
$corpo_cor = $_POST["corpo_cor"];
$corpo_cor_outro = $_POST["corpo_cor_outro"];
$lamin_cor = $_POST["lamin_cor"];
$lamin_cor_outro = $_POST["lamin_cor_outro"];
//$gramat_corpo = $_POST["gramat_corpo"]);
$gramat_corpo = $gramat_corpo_grava;
$gramat_forro = $_POST["gramat_forro"];
if ($_POST["cost_fio_topo"] == "on" || $_POST["cost_fio_topo"] == "1") { $cost_fio_topo = "1"; } else { $cost_fio_topo = "0"; }
if ($_POST["cost_fio_base"] == "on" || $_POST["cost_fio_base"] == "1") { $cost_fio_base = "1"; } else { $cost_fio_base = "0"; }
if ($_POST["trava_rede"] == "on" || $_POST["trava_rede"] == "1") { $trava_rede = "1"; } else { $trava_rede = "0"; }
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
if ($_POST["reforco_vao_livre"] == "on" || $_POST["reforco_vao_livre"] == "1") { $reforco_vao_livre = "1"; } else { $reforco_vao_livre = "0"; }
if ($_POST["reforco_fixacao"] == "on" || $_POST["reforco_fixacao"] == "1") { $reforco_fixacao = "1"; } else { $reforco_fixacao = "0"; }
if ($_POST["alca_dupla"] == "on" || $_POST["alca_dupla"] == "1") { $alca_dupla = "1"; } else { $alca_dupla = "0"; }
$alca_capac = $_POST["alca_capac"];
$liner = $_POST["liner"];
$tipo_liner = $_POST["tipo_liner"];
$liner_espessura = $_POST["liner_espessura"];
$fix_liner = $_POST["fix_liner"];
$no_cores = $_POST["no_cores"];
if ($_POST["imp_controle_viag"] == "on") { $imp_controle_viag = "1"; } else { $imp_controle_viag = "0"; }
if ($_POST["imp_num_seq"] == "on") { $imp_num_seq = "1"; } else { $imp_num_seq = "0"; }
$sel_faces = $_POST["sel_faces"];
if ($_POST["porta_etq1"] == "on" || $_POST["porta_etq1"] == "1") { $porta_etq1 = "1"; } else { $porta_etq1 = "0"; }
$pos_porta_etq1 = $_POST["pos_porta_etq1"];
$mod_porta_etq1 = $_POST["mod_porta_etq1"];
if ($_POST["porta_etq2"] == "on" || $_POST["porta_etq2"] == "1") { $porta_etq2 = "1"; } else { $porta_etq2 = "0"; }
$pos_porta_etq2 = $_POST["pos_porta_etq2"];
$mod_porta_etq2 = $_POST["mod_porta_etq2"];
if ($_POST["porta_etq3"] == "on" || $_POST["porta_etq3"] == "1") { $porta_etq3 = "1"; } else { $porta_etq3 = "0"; }
$pos_porta_etq3 = $_POST["pos_porta_etq3"];
$mod_porta_etq3 = $_POST["mod_porta_etq3"];
if ($_POST["porta_etq4"] == "on" || $_POST["porta_etq4"] == "1") { $porta_etq4 = "1"; } else { $porta_etq4 = "0"; }
$pos_porta_etq4 = $_POST["pos_porta_etq4"];
$mod_porta_etq4 = $_POST["mod_porta_etq4"];
$fardo = $_POST["fardo"];
if ($_POST["fardo_pallet"] == "on" || $_POST["fardo_pallet"] == "1") { $fardo_pallet = "1"; } else { $fardo_pallet = "0"; }
$palletizado = $_POST["palletizado"];
if ($_POST["fio_ved_travas"] == "on" || $_POST["fio_ved_travas"] == "1") { $fio_ved_travas = "1"; } else { $fio_ved_travas = "0"; }
if ($_POST["velcro"] == "on" || $_POST["velcro"] == "1") { $velcro = "1"; } else { $velcro = "0"; }
if ($_POST["cinta_trav"] == "on" || $_POST["cinta_trav"] == "1") { $cinta_trav = "1"; } else { $cinta_trav = "0"; }
if ($_POST["gravata_trav"] == "on" || $_POST["gravata_trav"] == "1") { $gravata_trav = "1"; $gravata_med = 22; } else { $gravata_trav = "0"; $gravata_med = 0; }
if ($_POST["sapata"] == "on" || $_POST["sapata"] == "1") { $sapata = "1"; } else { $sapata = "0"; }
if ($_POST["flap"] == "on" || $_POST["flap"] == "1") { $flap = "1"; } else { $flap = "0"; }
$unit = $_POST["flap"];

$obs_cliente = addslashes($_POST["obs_cliente"]);
$obs_comerciais = addslashes($_POST["obs_comerciais"]);
$correcoes = "";

$log = "Orçamento gerado por: ".$_SESSION['user']['nome']." no dia ".date("d/m/Y - H:i:s").".";
$vendedor = $_SESSION['user']['nome'];
$id_vend = $_SESSION['user']['id'];

$data_ped = date("Y-m-d H:i:s");


$valor_final = number_format((float)$valor_final_venda, 2);


/*
require("dolar/class.UOLCotacoes.php");

$uol = new UOLCotacoes();
list($dolarComercialCompra, $dolarComercialVenda, $dolarTurismoCompra, $dolarTurismoVenda, $euroCompra, $euroVenda, $libraCompra, $libraVenda, $pesosCompra, $pesosVenda) = $uol->pegaValores();

$cambio_dolar = str_replace( ',', '.', $dolarComercialCompra);
*/
$query_cambio = "SELECT DISTINCT `dia`,`compra`,`venda` FROM `taxa_dolar` ORDER BY `taxa_dolar`.`id` DESC LIMIT 0,1";
$result_cambio = mysqli_query($conn,$query_cambio);
$cambio = mysqli_fetch_array($result_cambio);

if ($cambio_dolar != "") {
	$valor_final_dolar = $valor_final_venda/$cambio_dolar;
} else {
	$valor_final_dolar = 0;
}

if ($no_pedido == "") { $no_pedido = 0; }
if ($valor_mat_auxiliar == "") { $valor_mat_auxiliar = 0; }
if ($valor_cif == "") { $valor_cif = 0; }
if ($valor_mao_obra == "") { $valor_mao_obra = 0; }
if ($total == "") { $total = 0; }
if ($valor_icms == "") { $valor_icms = 0; }
if ($valor_pis == "") { $valor_pis = 0; }
if ($valor_cofins == "") { $valor_cofins = 0; }
if ($valor_ir == "") { $valor_ir = 0; }
if ($valor_csll == "") { $valor_csll = 0; }
if ($valor_inss == "") { $valor_inss = 0; }
if ($valor_perda == "") { $valor_perda = 0; }
if ($valor_frete == "") { $valor_frete = 0; }
if ($valor_comissao == "") { $valor_comissao = 0; }
if ($valor_adm_comercial == "") { $valor_adm_comercial = 0; }
if ($valor_cfinanceiro == "") { $valor_cfinanceiro = 0; }
if ($valor_margem == "") { $valor_margem = 0; }
if ($valor_imposto == "") { $valor_imposto = 0; }
if ($valor_final_dolar == "") { $valor_final_dolar = 0; }
if ($cambio_dolar == "") { $cambio_dolar = 0; }
if ($data_ped == "") { $data_ped = 0; }


$sql = "INSERT INTO `pedidos` (`id`, `pedido`, `nome_cliente`, `segmento_cliente`, `cidade_cliente`, `uf_cliente`, `selecao`, `cnpj_cpf`, `qtde`, `referencia`, `embarques`, `representante`, `mercado`, `frete`, `fornecedora`, `usocons`, `submit`, `distancia_aprox`, `prazo`, `nome_prod`, `dens_aparente`, `temperatura`, `class_uso`, `transporte`, `dem_mensal`, `dem_anual`, `carga_nominal`, `armazenagem`, `corpo`, `base1`, `base2`, `altura`, `plastificado`, `corpo_cor`, `corpo_cor_outro`, `lamin_cor`, `lamin_cor_outro`, `gramat_corpo`, `gramat_forro`, `trava_rede`, `carga`, `c_quadrado`, `carga1`, `carga2`, `tampa`, `valvula`, `saia`, `descarga`, `d_redondo`, `descarga1`, `descarga2`, `fundo`, `valvula_d`, `cost_fio_topo`, `cost_fio_base`, `alca`, `alca_material`, `alca_cor`, `alca_altura`, `alca_fix_altura`, `alca_fixacao`, `reforco_vao_livre`, `reforco_fixacao`, `alca_dupla`, `alca_capac`, `liner`, `tipo_liner`, `liner_espessura`, `fix_liner`, `no_cores`, `imp_controle_viag`, `imp_num_seq`, `sel_faces`, `porta_etq1`, `pos_porta_etq1`, `mod_porta_etq1`, `porta_etq2`, `pos_porta_etq2`, `mod_porta_etq2`, `porta_etq3`, `pos_porta_etq3`, `mod_porta_etq3`, `porta_etq4`, `pos_porta_etq4`, `mod_porta_etq4`, `fardo`, `fardo_pallet`, `palletizado`, `fio_ved_travas`, `velcro`, `cinta_trav`, `gravata`, `med_gravata`, `sapata`, `flap`, `unit`, `valor_custo`, `valor_final`, `log`, `revisao`, `vendedor`, `id_vend`, `data`, `obs_cliente`, `obs_comerciais`, `correcoes`, `status`) VALUES (NULL, '".$no_pedido."', '".$nome_cliente."', '".$segmento_cliente."', '".$cidade_cliente."', '".$uf_cliente."', '".$selecao."', '".$cnpj_cpf."', '".$qtde."', '".$referencia."', '".$embarques."', '".$representante."', '".$mercado."', '".$frete."', '".$fornecedora."', '".$usocons."', '".$submit."', '".$distancia_aprox."', '".$prazo."', '".$nome_prod."', '".$dens_aparente."', '".$temperatura."', '".$class_uso."', '".$transporte."', '".$dem_mensal."', '".$dem_anual."', '".$carga_nominal."', '".$armazenagem."', '".$corpo."', '".$base1."', '".$base2."', '".$altura."', '".$plastificado."', '".$corpo_cor."', '".$corpo_cor_outro."', '".$lamin_cor."', '".$lamin_cor_outro."', '".$gramat_corpo."', '".$gramat_forro."', '".$trava_rede."', '".$carga."', '".$c_quadrado."', '".$carga1."', '".$carga2."', '".$tampa."', '".$valvula."', '".$saia."', '".$descarga."', '".$d_redondo."', '".$descarga1."', '".$descarga2."', '".$fundo."', '".$valvula_d."', '".$cost_fio_topo."', '".$cost_fio_base."', '".$alca."', '".$alca_material."', '".$alca_cor."', '".$alca_altura."', '".$alca_fix_altura."', '".$alca_fixacao."', '".$reforco_vao_livre."', '".$reforco_fixacao."', '".$alca_dupla."', '".$alca_capac."', '".$liner."', '".$tipo_liner."', '".$liner_espessura."', '".$fix_liner."', '".$no_cores."', '".$imp_controle_viag."', '".$imp_num_seq."', '".$sel_faces."', '".$porta_etq1."', '".$pos_porta_etq1."', '".$mod_porta_etq1."', '".$porta_etq2."', '".$pos_porta_etq2."', '".$mod_porta_etq2."', '".$porta_etq3."', '".$pos_porta_etq3."', '".$mod_porta_etq3."', '".$porta_etq4."', '".$pos_porta_etq4."', '".$mod_porta_etq4."', '".$fardo."', '".$fardo_pallet."', '".$palletizado."', '".$fio_ved_travas."', '".$velcro."', '".$cinta_trav."', '".$gravata_trav."', '".$gravata_med."', '".$sapata."', '".$flap."', '".$unit."', '".$total."', '".$valor_final."', '".$log."', '0', '".$vendedor."', '".$id_vend."', '".$data_ped."', '".$obs_cliente."', '".$obs_comerciais."', '".$correcoes."', '1')";

//die(); 

$sql_extra = "INSERT INTO `pedidos_extra` (`id`, `pedido`, `revisao`, `class_prod`, `mat_auxiliar`, `cif`, `mao_obra`, `custo_bag`, `icms`, `pis`, `cofins`, `ir`, `csll`, `inss`, `perda`, `frete`, `comissao`, `adm_comercial`, `custo_fin`, `margem`, `imposto_total`, `valor_dolar`, `cambio`, `cambio_data`, `rev_valor`) VALUES (NULL, '".$no_pedido."', '0', '".$class_prod."', '".$valor_mat_auxiliar."', '".$valor_cif."', '".$valor_mao_obra."', '".$total."', '".$valor_icms."', '".$valor_pis."', '".$valor_cofins."', '".$valor_ir."', '".$valor_csll."', '".$valor_inss."', '".$valor_perda."', '".$valor_frete."', '".$valor_comissao."', '".$valor_adm_comercial."', '".$valor_cfinanceiro."', '".$valor_margem."', '".$valor_imposto."',  '".$valor_final_dolar."', '".$cambio_dolar."', '".$data_ped."', '0')";

$sql_repres = "INSERT INTO `pedidos_repres` (`id`, `pedido`, `id_vend`, `data_criacao`, `data_liberacao`, `data_reativado`, `data_venc1`, `data_venc2`, `id_motivo`, `det_motivo`, `status`) VALUES (NULL, '".(float)$no_pedido."', '".$_SESSION['user']['id']."', '".date("Y-m-d")."', NULL, NULL, NULL, NULL, NULL, NULL, '1');";


$retval = mysqli_query( $conn, $sql );
$id_pedido = mysqli_insert_id($conn);
if(! $retval )
{
  die('Não foi possível gerar o orcamento: ' . mysqli_error($conn));
}


$retval_extra = mysqli_query( $conn, $sql_extra );
if(! $retval_extra )
{
  echo $sql_extra;
  die('Não foi possível gerar extras do orçamento: ' . mysqli_error($conn));
}

$retval_repres = mysqli_query( $conn, $sql_repres );
if(! $retval_repres )
{
  echo $sql_repres;
  die(mysqli_error($conn));
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

$sql_cliente = "INSERT INTO `pedidos_cliente` (`id`, `pedido`, `id_cliente`, `id_vend`, `data`) VALUES (NULL, '".(float)$no_pedido."', '".$id_cliente."', '".$_SESSION["user"]["id"]."', '".date('Y-m-d H:i:s')."')";

$retval_cliente = mysqli_query( $conn, $sql_cliente );
if(! $retval_cliente )
{
  echo $sql_cliente;
  die('Não foi possível relacionar o cliente: ' . mysqli_error($conn));
}


Log_Sis($no_pedido,$_SESSION['user']['id'],$_SESSION['user']['nome'],"Gerou um novo orcamento: ".$no_pedido.".");

$ee_mensagem = '<br><b>Novo orçamento!</b><br><br>Para acessá-lo clique aqui: <a href="/orcamentos/resumo.php?pedido='.$no_pedido.'">/orcamentos/resumo.php?pedido='.$no_pedido.'</a><br><br>Orçamento gerado por '.$vendedor.' em '.date("d/m/y").' às '.date("H:i:s");
//$ee_sql = mysqli_query($conn,"SELECT * FROM `users` WHERE `nivel` LIKE '1' AND `status` LIKE '1'"); // SELECT * FROM `users` WHERE `nivel` LIKE '2' AND `status` LIKE '1' OR `nivel` LIKE '1' AND `status` LIKE '1'
$ee_sql = mysqli_query($conn,"SELECT * FROM `users` WHERE (`id` LIKE '1' OR `id` LIKE '4') AND `status` LIKE '1'");
while($ee_envia = mysqli_fetch_array($ee_sql)) {
	Envia_Email($vendedor,'enviar@email.com.br',$ee_envia["email"],'Novo orcamento',$ee_mensagem,$no_pedido);
}

?>
<script type="text/javascript">
alert("Orçamento enviado com sucesso!");
window.location.href = "pedidos.php";
</script>
<?php

die("");

}


if(!empty($_POST))
	{
        if(empty($_POST['nome_cliente'])) 
        { 
            die("<br><br><br><center>Por favor preencha o nome do cliente.<br><br><br><br><a href=\"javascript:history.go(-1)\">Voltar</a></center>"); 
        } 

        if(empty($_POST['cidade_cliente'])) 
        { 
            die("<br><br><br><center>Por favor preencha a cidade do cliente.<br><br><br><br><a href=\"javascript:history.go(-1)\">Voltar</a></center>"); 
        } 

        if(empty($_POST['cnpj_cpf'])) 
        { 
            die("<br><br><br><center>Por favor preencha o documento do cliente.<br><br><br><br><a href=\"javascript:history.go(-1)\">Voltar</a></center>"); 
        } 

        if(empty($_POST['qtde'])) 
        { 
            die("<br><br><br><center>Por favor preencha a quantidade desejada.<br><br><br><br><a href=\"javascript:history.go(-1)\">Voltar</a></center>"); 
        }

?>
<form name="orcamento" class="form-horizontal" action="orcamento.php?acao=gravar" method="post">
<?php
foreach($_POST as $key => $value) {
  echo "<input type=\"hidden\" name=\"$key\" value=\"$value\">\n";
}
?>

<div class="clearfix"></div>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Dados do cliente</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<label class="control-label" for="nome_cliente">Nome</label>
					<input type="text" name="nome_cliente" class="form-control col-md-12 col-xs-12" value="<?php echo $_POST["nome_cliente"]; ?>" readonly="readonly">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label class="control-label" for="cidade_cliente">Cidade</label>
					<input type="text" name="cidade_cliente" class="form-control col-md-4 col-xs-12" value="<?php echo $_POST["cidade_cliente"]; ?>" readonly="readonly">
				</div>
				<div class="col-md-1 col-sm-1 col-xs-12">
					<label class="control-label" for="uf_cliente">Estado</label>
					<input type="text" name="uf_cliente" class="form-control col-md-1 col-xs-12" value="<?php echo $_POST["uf_cliente"]; ?>" readonly="readonly">
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label class="control-label" for="distancia_aprox">Distância aprox. da fornecedora <span class="required">*</span></label>
					<select name="distancia_aprox" class="form-control col-md-4 col-xs-12" required>
						<option value="">Selecione</option>
						<?php
						if ($_POST["frete"] == "fob") {
						?>
						<option value="0">Frete FOB</option>
						<?php
						} else {
						?>
						<option value="1">Menos de 200 km</option>
						<option value="2">De 201 a 500 km</option>
						<option value="3">Acima de 501 km</option>
						<?php
						}
						?>
					</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<label class="control-label" for="prazo">Prazo de pagamento</label> (dias) <span class="required">*</span>

					<input type="text" name="prazo" class="form-control" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Utilize 0 para pagamento à vista, -1 para pagamento antecipado ou / para mais de uma data" value="" min="0" required>
					
<?php
/*
					<select name="prazo" class="form-control col-md-3 col-xs-12">
						<option value="0">À vista</option>
						<option value="28">28 dias</option>
						<option value="30">30 dias</option>
						<option value="35">35 dias</option>
						<option value="45">45 dias</option>
						<option value="60">60 dias</option>
					</select>
*/
?>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Dados do produto acondicionado</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<label class="control-label" for="nome_prod">Nome do produto</label>
					<input type="text" name="nome_prod" class="form-control col-md-12 col-xs-12">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-5 col-sm-5 col-xs-12">
					<label class="control-label" for="segmento_cliente">Segmento</label>
					<select name="segmento_cliente" class="form-control col-md-5 col-xs-12">
					<?php
					$segmento = mysqli_query($conn,"SELECT * FROM segmentos ORDER BY segmento ASC");
					while($row = mysqli_fetch_array($segmento)) {
						if ($row['id'] == $_POST["segmento_cliente"]) {
							echo "    <option selected=\"selected\" value=\"" . $row['id'] . "\">" . $row['segmento'] . "</option>\n";
						} else {
							echo "    <option disabled=\"disabled\" value=\"" . $row['id'] . "\">" . $row['segmento'] . "</option>\n";
						}
					}
					?>
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12 has-feedback">
					<label class="control-label" for="dens_aparente">Densidade aparente</label>
					<input type="text" oninput="numero('dens_aparente');" id="dens_aparente" name="dens_aparente" class="form-control col-md-4 col-xs-12"><span class="form-control-feedback right" style="width:60px;"> gr/cm<sup>3</sup></span>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12 has-feedback">
					<label class="control-label" for="temperatura">Temperatura</label>
					<input type="text" oninput="numero('temperatura');" id="temperatura" name="temperatura" class="form-control col-md-3 col-xs-12"><span class="form-control-feedback right" style="width:30px;"> °C</span>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Utilização do bag</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
			<div class="form-group">
				<div class="col-md-5 col-sm-5 col-xs-12">
					<label class="control-label" for="class_uso">Classificação de uso</label>
					<select id="class_uso" name="class_uso" class="form-control col-md-5 col-xs-12" required>
						<option value="" selected>Selecione</option>
						<?php
						$classif_uso = mysqli_query($conn,"SELECT * FROM classif_uso");
						while($row = mysqli_fetch_array($classif_uso)) {
						  echo "    <option value=\"" . $row['fator'] . "_" . $row['id'] . "\">" . $row['classif'] . "</option>\n";
						}
						?>
					</select>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-12">
					<label class="control-label" for="transporte">Transporte</label>
					<select name="transporte" class="form-control col-md-5 col-xs-12">
						<option selected>Selecione</option>
						<option>Caminhão baú (fechado - abre por trás)</option>
						<option>Caminhão sider (fechado - abre pelos lados)</option>
						<option>Caminhão de grade baixa (aberto)</option>
						<option>Caminhão graneleiro (aberto - grade alta)</option>
						<option>Container metálico de 20' (2,35x5,89/2,38m)</option>
						<option>Container metálico de 40' (2,35x12,02/2,38m)</option>
						<option>Porão de navio</option>
						<option>Vagão de trem com abertura lateral (fechado)</option>
						<option>Vagão de trem aberto</option>
					</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<label class="control-label" for="dem_mensal">Demanda mensal</label>
					<input type="text" name="dem_mensal" class="form-control col-md-2 col-xs-12">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-5 col-sm-5 col-xs-12 has-feedback">
					<label class="control-label" for="carga_nominal">Carga nominal</label>
					<input type="text" id="carga_nominal" name="carga_nominal" class="form-control col-md-5 col-xs-12" required>
					<span class="form-control-feedback right" style="width:30px;"> kg</span>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-12">
					<label class="control-label" for="armazenagem">Armazenagem</label>
					<select name="armazenagem" class="form-control col-md-5 col-xs-12">
						<option selected>Selecione</option>
						<option>Local coberto e fechado</option>
						<option>Local apenas coberto</option>
						<option>Local aberto</option>
					</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<label class="control-label" for="dem_anual">Demanda anual</label>
					<input type="text" name="dem_anual" class="form-control col-md-2 col-xs-12">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-9 col-sm-9 col-xs-12 has-feedback">
					<label class="control-label" for="class_prod">Classificação de produtividade</label>
					<select id="class_prod" name="class_prod" onclick="fclassprod();" class="form-control col-md-5 col-xs-12" required>
						<option value="" selected>Selecione</option>
						<option value="a">TIPO A - Alta produtividade</option>
						<option value="b">TIPO B - Média produtividade</option>
						<option value="c">TIPO C - Baixa produtividade</option>
					</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12 has-feedback">
					<label style="margin-top: 15px;"><input type="checkbox" id="cost_fio_topo" name="cost_fio_topo" disabled> Costura a fio no topo</label><br>
					<label><input type="checkbox" id="cost_fio_base" name="cost_fio_base" disabled> Costura a fio na base</label>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12 reticencias">
		<h3>Informações técnicas de projeto</h3>
	</div>
</div>

<div class="row">
	<div class="col-md-7 col-sm-7 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Corpo</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="display: none;">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="col-md-12 col-sm-12 col-xs-12" style="padding-left:0;">
				<label class="control-label" for="corpo">Modelo</label>
				<select onclick="mudar_desenho('corpo');" id="sel_corpo" name="corpo" class="form-control" required>
					<option value="" selected>Selecione</option>
					<option value="gota">Gota (Single Loop)</option>
					<option value="qowa">Plano</option>
					<option value="cowa">Tubular</option>
					<option value="qowac">Painel U</option>
					<option value="qowacf">Painel U com forro</option>
					<option value="qowad4">Travado com costuras nos cantos</option>
					<option value="qowad8">Travado em gomos</option>
					<option value="cowad">Travado tubular</option>
					<option value="qowa2">Plano duplo</option>
					<option value="cowa2">Tubular duplo</option>
					<option value="qowaf">Plano com forro</option>
					<option value="qowadlf">Plano com forro travado</option>
					<option value="cowaf">Tubular com forro</option>
				<?php /*	<option value="qowar">Plano reforçado com faixa</option>
					<option value="cowarf">Tubular com forro e reforçado com faixa</option>
					<option value="qowalf">Plano com liner e forro</option> */ ?>
					<option value="qowao">Plano condutivo</option>
					<option value="qowafi">Plano com forro VCI</option>
					<option value="cowafi">Tubular com forro VCI</option>
					<option value="qowam">Plano antimicrobiano</option>
					<option value="qowaa">Plano arejado</option>
					<option value="qowat">Plano térmico</option>
					<option value="qhe">Plano com fechamento especial</option>
					<option value="qhe_ref">Plano QHE reforçado com fita</option>
					<option value="rof">Porta ensacado simples</option>
					<option value="qms">Plano com duas alças</option>
					<option value="cms">Tubular com duas alças</option>
				<?php //	<option value="mg">Magicont</option> ?>
					<option value="outros">Outro</option>
				</select>
				</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-4 col-sm-4 col-xs-4 has-feedback" style="padding-left:0;">
					<label class="control-label" for="base1">Base 1</label>
					<input type="text" oninput="dimensoes('base1');numero('base1');" id="base1" name="base1" class="form-control" required><span class="form-control-feedback right" style="width:30px;"> cm</span>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-4 has-feedback" style="padding-left:0;">
					<label class="control-label" for="base">Base 2</label>
					<input type="text" oninput="dimensoes('base2');numero('base2');" id="base2" name="base2" class="form-control" required><span class="form-control-feedback right" style="width:30px;"> cm</span>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-4 has-feedback" style="padding-left:0;">
					<label class="control-label" for="altura">Altura</label>
					<input type="text" oninput="dimensoes('altura');numero('altura');" id="altura" name="altura" class="form-control" required><span class="form-control-feedback right" style="width:30px;"> cm</span>
					</div>
					<?php
					/*
					<div class="col-md-3 col-sm-3 col-xs-3" style="padding-left:0;padding-top:32px;">
					<label><input type="checkbox" onchange="plastif('plast_check');" id="plast_check" name="plastificado"> Laminado</label>
					</div>
					*/
					?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-6 col-sm-6 col-xs-12 has-feedback" style="padding-left:0;">
					<label class="control-label" for="gramat_corpo">Gramatura do corpo</label>
					<select id="gramat_corpo" name="gramat_corpo" onclick="plastif('gramat_corpo');" class="form-control" required>
						<option value="" selected>Selecione</option>
<?php
$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;");
while($gramat_row = mysqli_fetch_array($gramaturas_query)) {
  echo "						<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>\n";
}

/*						<option value="130">130</option>
						<option value="147">130 + 17</option>
						<option value="145">145</option>
						<option value="170">145 + 25</option>
						<option value="160">160</option>
						<option value="185">160 + 25</option>
						<option value="191">160 + 30</option>
						<option value="190">190</option>
						<option value="215">190 + 25</option>
						<option value="221">190 + 30</option>
						<option value="220">220</option>
						<option value="245">220 + 25</option>
						<option value="250">220 + 30</option>
						<option value="240">240</option>
						<option value="265">240 + 25</option>
						<option value="270">270</option>
						<option value="295">270 + 25</option>
*/
?>
					</select><span class="form-control-feedback right" style="width:60px;"> g/m²</span>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 has-feedback" style="padding-left:0;">
					<label class="control-label" for="gramat_forro">Gramatura do forro</label>
					<select id="gramat_forro" name="gramat_forro" class="form-control">
						<option value="" selected>Selecione</option>
<?php
$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;");
while($gramat_row = mysqli_fetch_array($gramaturas_query)) {
  echo "						<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>\n";
}
/*
						<option value="" selected>Selecione</option>
						<option value="65">48 + 17</option>
						<option value="70">50 + 20</option>
						<option value="110">110</option>
						<option value="125">100 + 25</option>
						<option value="147">130 + 17</option>
						<option value="160">160</option>
						<option value="190">190</option>
*/
?>
					</select><span class="form-control-feedback right" style="width:60px;"> g/m²</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-6 col-sm-6 col-xs-12 has-feedback" style="padding-left:0;">
					<label class="control-label" for="corpo_cor">Cor do tecido</label>
					<select onclick="outro('corpo_cor');" name="corpo_cor" id="corpo_cor" class="form-control">
						<option value="branco">Branco (natural)</option>
						<option value="cinza">Cinza</option>
						<option value="azul">Azul Carijó</option>
						<option value="marrom">Marrom Carijó</option>
						<option value="preto">Preto Carijó</option>
						<option value="preto2">Preto</option>
						<option value="verde">Verde Carijó</option>
						<option value="outro">Outro</option>
					</select>
					<input type="text" id="corpo_cor_outro" name="corpo_cor_outro" class="form-control" style="display:none;margin-top:10px;" placeholder="Especifique">
					</div>

					<div id="plastspan" class="col-md-6 col-sm-6 col-xs-12" style="padding-left: 0; display: none;">
					<label class="control-label" for="lamin_cor">Cor da laminação</label>
					<select onclick="outro('lamin_cor');" name="lamin_cor" id="lamin_cor" class="form-control">
					<option value="padrao">Padrão</option>
					<option value="branco">Branco</option>
					<option value="preto">Preto</option>
					<option value="outro">Outro</option>
					</select>
					<input type="text" id="lamin_cor_outro" name="lamin_cor_outro" class="form-control" style="display:none;margin-top:10px;" placeholder="Especifique">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-12 col-sm-12 col-xs-12" style="padding-left:0;">
					<label class="control-label" for="tipo_cost_corpo">Tipo de costura</label>
					<select id="tipo_cost_corpo" name="tipo_cost_corpo" class="form-control">
						<option value="simples" selected>Costura simples</option>
						<option value="simples1ved">Costura simples + 1 vedante</option>
						<option value="simples2ved">Costura simples + 2 vedante</option>
						<option value="dupla">Costura dupla</option>
						<option value="overlock">Overlock + corrente</option>
					</select>
					</div>
				</div>
			</div>
		</div>
	</div>
		<div class="x_panel">
			<div class="x_title">
				<h2>Sistema de enchimento</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="display: none;">
			<div class="form-group">
				<div class="col-md-10 col-sm-10 col-xs-10">
				<label class="control-label" for="sel_carga">Modelo</label>
				<select onclick="fcarga('carga');" id="sel_carga" name="carga" class="form-control">
					<option value="vazio" selected>Sem sistema de enchimento</option>
					<option value="c_saia">Saia</option>
					<option value="c_afunilada">Saia afunilada</option>
					<option value="c_simples">Válvula simples</option>
					<option value="c_simples_afunilada">Válvula simples com tampa afunilada</option>
					<?php //	<option value="c_prot_presilha">Válvula com proteção tipo presilha</option> ?>
					<option value="c_prot_mochila">Válvula com proteção tipo flap</option>
					<option value="c_tipo_as">Tampa tipo porta-ensacado</option>
				</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-2">
				<label><input type="checkbox" id="c_quadrado" name="c_quadrado" value="c_quadrado" style="margin-top:40px;"> Quadrado</label>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
				<label class="control-label reticencias" for="carga1">Dimensão da boca</label>
				<input type="text" oninput="dimensoes('carga1');numero('carga1');" id="carga1" name="carga1" class="form-control">
				<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
				<label class="control-label" for="carga2">Altura</label>
				<input type="text" oninput="dimensoes('carga2');numero('carga2');" id="carga2" name="carga2" class="form-control">
				<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
				<div id="tampa"></div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-12 col-sm-12 col-xs-12" style="padding-left:0;">
					<label class="control-label" for="tipo_cost_enchim">Tipo de costura</label>
					<select id="tipo_cost_enchim" name="tipo_cost_enchim" class="form-control">
						<option value="simples" selected>Costura simples</option>
						<option value="simples1ved">Costura simples + 1 vedante</option>
						<option value="simples2ved">Costura simples + 2 vedante</option>
						<option value="dupla">Costura dupla</option>
					</select>
					</div>
				</div>
			</div>
		</div>
	</div>
		<div class="x_panel">
			<div class="x_title">
				<h2>Sistema de esvaziamento</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="display: none;">
			<div class="form-group">
				<div class="col-md-10 col-sm-10 col-xs-10">
				<label class="control-label" for="sel_descarga">Modelo</label>
				<select onclick="fdescarga('descarga');" id="sel_descarga" name="descarga" class="form-control">
					<option value="vazio" selected>Sem sistema de esvaziamento</option>
					<option value="d_simples">Válvula simples</option>
					<option value="d_prot_presilha">Válvula com proteção tipo "X"</option>
					<option value="d_prot_mochila">Válvula com proteção tipo flap</option>
					<option value="d_afunilado">Afunilado</option>
					<option value="d_total">Abertura total simples</option>
					<?php /*	<option value="d_total_presilha">Abertura total com presilhas</option>
					<option value="d_total_blindado">Abertura total blindado</option> */ ?>
				</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-2">
				<label><input type="checkbox" id="d_redondo" name="d_redondo" value="d_redondo" style="margin-top:40px;" checked> Redondo</label>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
				<label class="control-label reticencias" for="carga1">Dimensão da boca</label>
				<input type="text" oninput="dimensoes('descarga1');numero('descarga1');" id="descarga1" name="descarga1" class="form-control">
				<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
				<label class="control-label" for="carga2">Altura</label>
				<input type="text" oninput="dimensoes('descarga2');numero('descarga2');" id="descarga2" name="descarga2" class="form-control">
				<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
				<div id="fundo_d"><label class="control-label" for="gramat_fundo_d">Gramatura do fundo:</label>
					<select id="gramat_fundo_d" name="gramat_fundo_d" class="form-control">
						<option value="">Selecione</option>
<?php
$gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;");
while($gramat_row = mysqli_fetch_array($gramaturas_query)) {
  echo "						<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>\n";
}
/*						<option value="130">130</option>
						<option value="147">130 + 17</option>
						<option value="145">145</option>
						<option value="170">145 + 25</option>
						<option value="160">160</option>
						<option value="185">160 + 25</option>
						<option value="191">160 + 30</option>
						<option value="190">190</option>
						<option value="215">190 + 25</option>
						<option value="221">190 + 30</option>
						<option value="220">220</option>
						<option value="245">220 + 25</option>
						<option value="250">220 + 30</option>
						<option value="240">240</option>
						<option value="265">240 + 25</option>
						<option value="270">270</option>
						<option value="295">270 + 25</option>
*/ ?>
					</select></div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="col-md-12 col-sm-12 col-xs-12" style="padding-left:0;">
					<label class="control-label" for="tipo_cost_esvaz">Tipo de costura</label>
					<select id="tipo_cost_esvaz" name="tipo_cost_esvaz" class="form-control">
						<option value="simples" selected>Costura simples</option>
						<option value="simples1ved">Costura simples + 1 vedante</option>
						<option value="simples2ved">Costura simples + 2 vedante</option>
						<option value="dupla">Costura dupla</option>
					</select>
					</div>
				</div>
			</div>
		</div>
	</div>
		<div class="x_panel">
			<div class="x_title">
				<h2>Alças</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="display: none;">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
				<label class="control-label" for="alca">Quantidade</label>
				<select onclick="mudar_desenho('alca');qtde_alcas();" id="sel_alca" name="alca" class="form-control">
					<option value="vazio" selected>Sem alças</option>
					<?php
					$qtde_alcas = mysqli_query($conn,"SELECT * FROM qtde_alcas");
					while($row = mysqli_fetch_array($qtde_alcas)) {
					echo "    <option value=\"" . $row['qtde'] . "_" . $row['cod'] . "\">" . $row['desc'] . "</option>\n";
					}
					?>
				</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 col-sm-3 col-xs-6">
				<label class="control-label" for="alca_material">Material</label>
				<select name="alca_material" class="form-control">
					<option value="fita" selected>Fita</option>
					<option value="tecido">Tecido</option>
				</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6">
				<label class="control-label" for="alca_cor">Cor</label>
				<select name="alca_cor" class="form-control">
					<option value="branco">Branco (natural)</option>
					<option value="amarela">Amarela</option>
					<option value="azul_total">Azul</option>
					<option value="cinza">Cinza</option>
					<option value="marrom_total">Marrom</option>
					<option value="preta_total">Preta</option>
					<option value="verde_total">Verde</option>
					<option value="vermelha">Vermelha</option>
					<option value="amarelo_carijo">Amarelo Carijó</option>
					<option value="azul">Azul Carijó</option>
					<option value="cinza_carijo">Cinza Carijó</option>
					<option value="marrom">Marrom Carijó</option>
					<option value="preto">Preto Carijó</option>
					<option value="preto2">Preto</option>
					<option value="verde">Verde Carijó</option>
					<option value="vermelho_carijo">Vermelho Carijó</option>
					<option value="outra">Outra</option>
				</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
					<label class="control-label reticencias" for="alca_altura">Altura do vão livre</label>
					<input type="text" oninput="dimensoes('alca_altura');numero('alca_altura');" id="alca_altura" name="alca_altura" class="form-control" required>
					<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6 has-feedback">
					<label class="control-label reticencias" for="alca_fix_altura">Altura de fixação</label>
					<input type="text" oninput="dimensoes('alca_fix_altura');numero('alca_fix_altura');" id="alca_fix_altura" name="alca_fix_altura" class="form-control" required>
					<span class="form-control-feedback right" style="width:30px;"> cm</span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-2 col-xs-6">
					<label style="margin-top:20px;"><input type="checkbox" id="reforco_vao_livre" name="reforco_vao_livre"> Reforço do vão livre</label>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-6">
					<label style="margin-top:20px;"><input type="checkbox" id="reforco_fixacao" name="reforco_fixacao"> Reforço de fixação</label>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-6">
					<label style="margin-top:20px;"><input type="checkbox" id="alca_dupla" name="alca_dupla"> Alça dupla</label>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12 has-feedback">
					<label class="control-label reticencias" for="alca_capac">Capacidade individual de cada alça</label>
					<input type="text" id="alca_capac" name="alca_capac" class="form-control" readonly>
					<span class="form-control-feedback right" style="width:30px;"> kg</span>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label class="control-label" for="tipo_cost_alca">Tipo de costura</label>
					<select id="tipo_cost_alca" name="tipo_cost_alca" class="form-control">
						<option value="ponto_fixo" selected>Ponto fixo</option>
						<option value="dupla">Costura dupla</option>
						<option value="dupla2ved">Costura dupla + 2 vedante</option>
						<option value="overlock">Overlock + corrente</option>
						<option value="overlock2ved">Overlock + corrente + 2 vedante</option>
					</select>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label class="control-label" for="alca_fixacao">Tipo de fixação da alça</label>
					<select id="alca_fixacao" name="alca_fixacao" class="form-control">
						<option value="0" selected>Interna</option>
						<option value="1">Externa</option>
					</select>
				</div>
			</div>
		</div>
	</div>
		<div class="x_panel">
			<div class="x_title">
				<h2>Liner</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="display: none;">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
				<label class="control-label" for="liner">Modelo</label>
				<select onclick="mudar_liner('liner');" id="sel_liner" name="liner" class="form-control">
					<option value="vazio" selected>Selecione</option>
					<option value="liner_padrao">Liner padrão</option>
					<option value="liner_gota">Liner padrão Bag Gota</option>
					<option value="liner_fertil">Liner padrão Fertilizante</option>
					<option value="liner_afunilado">Liner afunilado</option>
					<option value="liner_sup_inf">Liner com válvula superior e inferior</option>
					<option value="liner_total_inf">Liner com abertura total e válvula inferior</option>
					<option value="liner_sup_fechado">Liner com válvula superior e fechado no fundo</option>
					<option value="liner_externo">Liner externo</option>
				</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 col-sm-4 col-xs-4">
				<label class="control-label" for="tipo_liner">Tipo</label>
				<select name="tipo_liner" class="form-control">
					<option value="vazio" selected>Selecione</option>
					<option value="liner_transp">Virgem</option>
					<option value="liner_canela">Canela</option>
					<option value="liner_cristal">Cristal</option>
				</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-4 has-feedback">
				<label class="control-label reticencias" for="liner_espessura">Espessura</label>
				<input type="text" oninput="numero('liner_espessura');" id="liner_espessura" name="liner_espessura" class="form-control">
				<span class="form-control-feedback right" style="width:30px;"> µm</span>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-4">
				<label class="control-label" for="fix_liner">Tipo de fixação</label>
				<select onclick="mudar_desenho('fix_liner');" id="sel_fix_liner" name="fix_liner" class="form-control">
					<option value="sem_fixacao" selected>Sem fixação</option>
					<option value="colado">Colado</option>
					<option value="costurado">Costurado</option>
					<option value="colado_costurado">Colado e costurado</option>
					<option value="soldado_fundo">Soldado fundo</option>
					<option value="liner_externo">Liner externo</option>
				</select>
				</div>
			</div>
		</div>
	</div>
		<div class="x_panel">
			<div class="x_title">
				<h2>Impressão</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="display: none;">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
				<label class="control-label" for="no_cores">N° cores</label>
				<select name="no_cores" class="form-control">
					<option value="0" selected>Sem impressão</option>
					<option value="1">1 cor</option>
					<option value="2">2 cores</option>
					<option value="3">3 cores (limite ideal)</option>
					<option value="4">4 cores (consultar)</option>
				</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4 col-sm-4 col-xs-12">
				<label style="margin-top: 15px;"><input type="checkbox" name="imp_controle_viag"> Controle de utilização</label><br>
				<label><input type="checkbox" name="imp_num_seq"> Número sequencial</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-6">
				<label class="control-label reticencias" for="sel_faces">Faces selecionadas</label>
				<input type="text" id="sel_faces" name="sel_faces" style="text-transform: uppercase;" class="form-control" readonly>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-6">
				<img id="faces" src="images/faces.jpg" border="0" width="140" height="60" orgWidth="140" orgHeight="60" usemap="#bag_faces" alt="" />
				<map name="bag_faces" id="bag_faces">
				<area  alt="" title="" href="#" shape="poly" coords="53,0,86,0,80,16,58,16" style="outline:none;" target="_self" onClick="face_add('c'); return false;"/>
				<area  alt="" title="" href="#" shape="poly" coords="99,13,99,49,84,41,84,21" style="outline:none;" target="_self" onClick="face_add('b'); return false;"/>
				<area  alt="" title="" href="#" shape="poly" coords="58,46,80,46,85,59,53,59" style="outline:none;" target="_self" onClick="face_add('a'); return false;"/>
				<area  alt="" title="" href="#" shape="poly" coords="40,13,54,20,54,42,40,49" style="outline:none;" target="_self" onClick="face_add('d'); return false;"/>
				</map>
				</div>
			</div>
		</div>
	</div>
		<div class="x_panel">
			<div class="x_title">
				<h2>Porta etiqueta</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="display: none;">
			<div class="form-group">
				<div class="col-md-2 col-sm-2 col-xs-2" style="text-align:center;">
					<label><input type="checkbox" onclick="hab_portaetq(1);" id="porta_etq1" name="porta_etq1" style="margin-top: 25px;height: 22px;width: 15px;"> <big><big><big>A</big></big></big></label>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="pos_porta_etq1">Posição</label>
					<select onclick="portaetq('1');" id="pos_porta_etq1" name="pos_porta_etq1" class="form-control" disabled="disabled">
						<option value="vazio" selected>Selecione</option>
						<option value="topo_meio">Topo centralizado (padrão)</option>
						<option value="topo_direita">Topo na direita</option>
						<option value="topo_esquerda">Topo na esquerda (45 cm do topo e 8 cm da lateral)</option>
						<option value="centro">No centro</option>
						<option value="cost_vert">Costurado na vertical</option>
						<option value="personalizado">Personalizado (especificar nas obs.)</option>
					</select>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="mod_porta_etq1">Modelo</label>
					<select id="mod_porta_etq1" name="mod_porta_etq1" class="form-control" disabled="disabled">
						<option value="" selected>Selecione</option>
						<option value="folha">Folha porta etiqueta (27 x 40 x 0,20)</option>
						<option value="folha2">Folha porta etiqueta (27 x 20 x 0,20)</option>
						<option value="fronha">Porta etiqueta tipo fronha (27 x 40 x 0,15)</option>
						<option value="aba_adesiva">Porta etiqueta (27 x 45 x 0,12) - com aba adesiva</option>
						<option value="ziplock">Porta documento (20 x 25) - ziplock</option>
						<option value="aberto_tras">Com abertura posicionada para a parte de trás (23x40cm)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-2 col-xs-2" style="text-align:center;">
					<label><input type="checkbox" onclick="hab_portaetq(2);" id="porta_etq2" name="porta_etq2" style="margin-top: 25px;height: 22px;width: 15px;"> <big><big><big>B</big></big></big></label>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="pos_porta_etq2">Posição</label>
					<select onclick="portaetq('2');" id="pos_porta_etq2" name="pos_porta_etq2" class="form-control" disabled="disabled">
						<option value="vazio" selected>Selecione</option>
						<option value="topo_meio">Topo centralizado (padrão)</option>
						<option value="topo_direita">Topo na direita</option>
						<option value="topo_esquerda">Topo na esquerda (45 cm do topo e 8 cm da lateral)</option>
						<option value="centro">No centro</option>
						<option value="cost_vert">Costurado na vertical</option>
						<option value="personalizado">Personalizado (especificar nas obs.)</option>
					</select>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="mod_porta_etq2">Modelo</label>
					<select id="mod_porta_etq2" name="mod_porta_etq2" class="form-control" disabled="disabled">
						<option value="" selected>Selecione</option>
						<option value="folha">Folha porta etiqueta (27 x 40 x 0,20)</option>
						<option value="folha2">Folha porta etiqueta (27 x 20 x 0,20)</option>
						<option value="fronha">Porta etiqueta tipo fronha (27 x 40 x 0,15)</option>
						<option value="aba_adesiva">Porta etiqueta (27 x 45 x 0,12) - com aba adesiva</option>
						<option value="ziplock">Porta documento (20 x 25) - ziplock</option>
						<option value="aberto_tras">Com abertura posicionada para a parte de trás (23x40cm)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-2 col-xs-2" style="text-align:center;">
					<label><input type="checkbox" onclick="hab_portaetq(3);" id="porta_etq3" name="porta_etq3" style="margin-top: 25px;height: 22px;width: 15px;"> <big><big><big>C</big></big></big></label>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="pos_porta_etq3">Posição</label>
					<select onclick="portaetq('3');" id="pos_porta_etq3" name="pos_porta_etq3" class="form-control" disabled="disabled">
						<option value="vazio" selected>Selecione</option>
						<option value="topo_meio">Topo centralizado (padrão)</option>
						<option value="topo_direita">Topo na direita</option>
						<option value="topo_esquerda">Topo na esquerda (45 cm do topo e 8 cm da lateral)</option>
						<option value="centro">No centro</option>
						<option value="cost_vert">Costurado na vertical</option>
						<option value="personalizado">Personalizado (especificar nas obs.)</option>
					</select>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="mod_porta_etq3">Modelo</label>
					<select id="mod_porta_etq3" name="mod_porta_etq3" class="form-control" disabled="disabled">
						<option value="" selected>Selecione</option>
						<option value="folha">Folha porta etiqueta (27 x 40 x 0,20)</option>
						<option value="folha2">Folha porta etiqueta (27 x 20 x 0,20)</option>
						<option value="fronha">Porta etiqueta tipo fronha (27 x 40 x 0,15)</option>
						<option value="aba_adesiva">Porta etiqueta (27 x 45 x 0,12) - com aba adesiva</option>
						<option value="ziplock">Porta documento (20 x 25) - ziplock</option>
						<option value="aberto_tras">Com abertura posicionada para a parte de trás (23x40cm)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-2 col-xs-2" style="text-align:center;">
					<label><input type="checkbox" onclick="hab_portaetq(4);" id="porta_etq4" name="porta_etq4" style="margin-top: 25px;height: 22px;width: 15px;"> <big><big><big>D</big></big></big></label>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="pos_porta_etq4">Posição</label>
					<select onclick="portaetq('4');" id="pos_porta_etq4" name="pos_porta_etq4" class="form-control" disabled="disabled">
						<option value="vazio" selected>Selecione</option>
						<option value="topo_meio">Topo centralizado (padrão)</option>
						<option value="topo_direita">Topo na direita</option>
						<option value="topo_esquerda">Topo na esquerda (45 cm do topo e 8 cm da lateral)</option>
						<option value="centro">No centro</option>
						<option value="cost_vert">Costurado na vertical</option>
						<option value="personalizado">Personalizado (especificar nas obs.)</option>
					</select>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<label class="control-label" for="mod_porta_etq4">Modelo</label>
					<select id="mod_porta_etq4" name="mod_porta_etq4" class="form-control" disabled="disabled">
						<option value="" selected>Selecione</option>
						<option value="folha">Folha porta etiqueta (27 x 40 x 0,20)</option>
						<option value="folha2">Folha porta etiqueta (27 x 20 x 0,20)</option>
						<option value="fronha">Porta etiqueta tipo fronha (27 x 40 x 0,15)</option>
						<option value="aba_adesiva">Porta etiqueta (27 x 45 x 0,12) - com aba adesiva</option>
						<option value="ziplock">Porta documento (20 x 25) - ziplock</option>
						<option value="aberto_tras">Com abertura posicionada para a parte de trás (23x40cm)</option>
					</select>
				</div>
			</div>
		</div>
	</div>
		<div class="x_panel">
			<div class="x_title">
				<h2>Embalagem</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="display: none;">
			<div class="form-group">
				<div class="col-md-4 col-sm-4 col-xs-4">
					<label class="control-label" for="fardo">Fardo de</label>
					<select name="fardo" class="form-control">
						<option value="vazio" selected>Selecione</option>
						<option value="5">5 peças</option>
						<option value="10">10 peças</option>
						<option value="15">15 peças</option>
						<option value="20">20 peças</option>
						<option value="25">25 peças</option>
					</select>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-4">
					<label style="margin-top: 30px;"><input type="checkbox" onclick="reforco('fardo_pallet');" id="fardo_pallet" name="fardo_pallet"> Fardo palletizado</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-4" id="tam_fardo_pallet" style="display:none;">
					<label class="control-label" for="palletizado">Big Bag palletizado</label>
					<select name="palletizado" class="form-control">
						<option value="vazio" selected>Selecione</option>
						<option value="80">80</option>
						<option value="100">100</option>
						<option value="125">125</option>
						<option value="130">130</option>
						<option value="150">150</option>
						<option value="175">175</option>
						<option value="200">200</option>
						<option value="250">250</option>
					</select>
				</div>
			</div>
		</div>
	</div>
		<div class="x_panel">
			<div class="x_title">
				<h2>Acessórios</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="display: none;">
			<div class="form-group">
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" name="fio_ved_travas"> Fio vedante nas travas</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" onclick="acessorio('cinta');" id="sel_cinta" name="cinta_trav"> Cinta de travamento</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" onclick="acessorio('gravata');" id="sel_gravata" name="gravata_trav"> Gravata de travamento</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" onclick="acessorio('sapata');" id="sel_sapata" name="sapata"> Pack Less</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" onclick="acessorio('velcro');" id="sel_velcro" name="velcro"> Velcro (Macho/Femea)</label>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" onclick="acessorio('flap');" id="sel_flap" name="flap"> Tampa para fundo falso</label>
				</div>
<?
/*
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" onclick="laranja();" id="sel_laranja" name=""> Desenho mod. laranja</label>
					<div id="mod_laranja"></div>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" onclick="unitizador();" id="sel_unitizador" name=""> Unitizador</label>
					<div id="input_unit" style="display:none;"><small>Altura sem recorte:</small><input type="text" id="unit" name="unit" style="width: 50px;padding: 0 5px;margin: 0 5px;" oninput="dimensoes('unit');"></div>
				</div>
*/
?>
				<div class="col-md-4 col-sm-4 col-xs-12">
					<label><input type="checkbox" id="trava_rede" name="trava_rede"> Travas em rede</label>
				</div>
			</div>
		</div>
	</div>
		<div class="x_panel">
			<div class="x_title">
				<h2>Observações</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
					</li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="display: none;">
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<label class="control-label" for="obs_cliente">Técnicas</label>
					<textarea name="obs_cliente" rows="3" class="form-control" style="resize: none;"></textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<label class="control-label" for="obs_comerciais">Comerciais</label>
					<textarea name="obs_comerciais" rows="3" class="form-control" style="resize: vertical;"></textarea>
				</div>
			</div>
		</div>
	</div>

<div class="ln_solid"></div>
<div class="form-group">
	<div class="col-md-12 col-sm-12 col-xs-12 text-center">
		<button type="submit" class="btn btn-success"><i class="fa fa-check"></i> ENVIAR ORÇAMENTO </button>
		<a href="pedidos.php" class="btn btn-primary"><i class="fa fa-close"></i> CANCELAR </a>
	</div>
</div>
<br>



	</div>
	<div id="ilustracao" class="col-md-5 col-sm-5 col-xs-12">
		<div class="x_panel">
		<p align="right">Desenho ilustrativo</p>
		<?php
		if ($_POST["segmento_cliente"] == "1") {
		?>
		<br>
		<p align="center"><font color="#FF0000"><b>ALIMENTÍCIO - PADRÃO EXPORTAÇÃO</b></font></p>
		<?php
		}
		if ($_POST["mercado"] == "ext") {
		?>
		<br>
		<p align="center"><font color="#FF0000"><b>BIG BAG PARA EXPORTAÇÃO</b></font></p>
		<?php
		}
		?>

		<div id="box_desenho" style="position:relative; overflow: hidden; width:357px; height:564px; margin:auto;">

		<div id="face_d" style="z-index:24;" class="desenho"></div>
		<div id="face_c" style="z-index:23;" class="desenho"></div>
		<div id="face_b" style="z-index:22;" class="desenho"></div>
		<div id="face_a" style="z-index:21;" class="desenho"></div>
		<div id="des_porta_etq4" style="z-index:20;" class="desenho"></div>
		<div id="des_porta_etq3" style="z-index:19;" class="desenho"></div>
		<div id="des_porta_etq2" style="z-index:18;" class="desenho"></div>
		<div id="des_porta_etq1" style="z-index:17;" class="desenho"></div>
		<div id="flap" style="z-index:4;" class="desenho"></div>
		<div id="cinta" style="z-index:17;" class="desenho"></div>
		<div id="gravata" style="z-index:17;" class="desenho"></div>
		<div id="velcro" style="z-index:16;" class="desenho"></div>
		<div id="sapata" style="z-index:15;" class="desenho"></div>
		<div id="dim_unit" style="z-index:15; top: 453px; left: 106px; display:none;" class="medidas"></div>
		<div id="dim_descarga2" style="z-index:14;" class="medidas"></div>
		<div id="dim_descarga1" style="z-index:13;" class="medidas"></div>
		<div id="dim_carga2" style="z-index:12; top: -35px; left: 0px;" class="medidas"></div>
		<div id="dim_carga1" style="z-index:11; top: -35px; left: 0px;" class="medidas"></div>
		<div id="dim_alca_fix_altura" style="z-index:10; top: 330px; left: 307px;" class="medidas"></div>
		<div id="dim_alca_altura" style="z-index:10; top: 298px; left: 307px;" class="medidas"></div>
		<div id="dim_altura" style="z-index:9; top: 370px; left: 295px;" class="medidas"></div>
		<div id="dim_base2" style="z-index:8; top: 490px; left: 230px;" class="medidas"></div>
		<div id="dim_base1" style="z-index:7; top: 490px; left: 65px;" class="medidas"></div>
		<div id="unitizador" style="z-index:10;" class="desenho"></div>
		<div id="liner" style="z-index:6;" class="desenho"></div>
		<div id="descarga" style="z-index:5;" class="desenho"></div>
		<div id="carga" style="z-index:4;" class="desenho"></div>
		<div id="alca" style="z-index:3;" class="desenho"><img src="images/desenhos/vazio.png" border="0"></div>
		<div id="corpo" style="z-index:2;" class="desenho"></div>
		<div id="baseimg" style="z-index:1;" class="desenho"><img src="images/desenhos/corpo.png" border="0"></div>
		<div id="medidas" style="z-index:0;" class="desenho"><img src="images/desenhos/medidas.png" border="0"></div>
		</div>
	</div>
	</div>
</div>



<script>


function portaetq(id) {
	$( "#pos_porta_etq"+ id )
	.change(function () {
		if ($(this).val() != "vazio") {
			$( "#des_porta_etq"+id ).html( "<img src='images/desenhos/"+ id + "_" + $( this ).val() +".png' border='0'>" );
		} else {
			$( "#des_porta_etq"+id ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
		}
	});
}

function hab_portaetq(id) {
	if ($('#porta_etq'+id).is (':checked')) {
	    $('#pos_porta_etq'+id).val("vazio");
	    $('#pos_porta_etq'+id).removeAttr("disabled");
	    $('#mod_porta_etq'+id).val("");
	    $('#mod_porta_etq'+id).removeAttr("disabled");
	} else {
	    $('#pos_porta_etq'+id).val("vazio");
	    $('#pos_porta_etq'+id).attr('disabled', 'disabled');
	    $('#mod_porta_etq'+id).val("");
	    $('#mod_porta_etq'+id).attr('disabled', 'disabled');
	    $('#des_porta_etq'+id).html("<img src='images/desenhos/vazio.png' border='0'>");
	}
/*	$('#porta_etq'+id+':checkbox').click(function() {
	    $('#pos_porta_etq'+id).val("vazio",! this.checked)
	    $('#pos_porta_etq'+id).attr('disabled',! this.checked)
	    $('#mod_porta_etq'+id).val("",! this.checked)
	    $('#mod_porta_etq'+id).attr('disabled',! this.checked)
	    $('#des_porta_etq'+id).html("<img src='images/desenhos/vazio.png' border='0'>",! this.checked)
	});*/
}


function qtde_alcas() {

	$("#sel_alca")
	.change(function () {

		var classificacao = parseInt($("#class_uso option:selected").val(),10);
		var quantidade = parseInt($("#sel_alca option:selected").val(),10);
		var carga = parseInt($("#carga_nominal").val(),10);


		str = carga * classificacao / 4 ; // quantidade ;
			$("#alca_capac").val( parseInt(str,10) || 0 );
//			$("#alca_capac").val( carga + "*" + classificacao + "/" + quantidade );
	});
}


function plastif(id) {
$("#"+id).change(function () {
	if ($("#"+id).val() == '147' || $("#"+id).val() == '170' || $("#"+id).val() == '185' || $("#"+id).val() == '215' || $("#"+id).val() == '245' || $("#"+id).val() == '265' || $("#"+id).val() == '295') {
		$("#plastspan").css('display','inline');
	} else {
		$("#plastspan").css('display','none');
	}
});
}


function reforco(id) {
/*$('#'+id).click(function () {*/
	if ($('#'+id).is (':checked')) {
		$("#tam_"+id).css('display','inline');
	} else {
		$("#tam_"+id).css('display','none');
	}
/*});*/
}



function outro(id) {
$('#'+id).change(function () {
	if ($("#"+id).val() == 'outro') {
		$("#"+id+"_outro").css('display','inline');
	} else {
		$("#"+id+"_outro").css('display','none');
	}
});
}




function expandir(id) {

$( "tr" )
	.find("."+id).each(function() {
		$(this).toggle();
	});

if($("."+id).is(':visible')){
	$("#"+id).css('background-image','url(images/menos.jpg)');
} else {
	$("#"+id).css('background-image','url(images/mais.jpg)');
}


}


function mudar_liner(id) {
	$( "#sel_"+ id )
	.change(function () {
			$( "#liner" ).html( "<img src='images/desenhos/"+ $( this ).val() +".png' border='0'>" );
	});
}



function mudar_desenho(id) {
	$( "#sel_"+ id )
	.change(function () {
		if ($( this ).val() == '') { $( this ).val('vazio'); }
		$( "#medidas" ).html( "<img src='images/desenhos/medidas.png' border='0'>" );
		var str = "";
		$( "#sel_" + id + " option:selected" ).each(function() {
		str += "<img src='images/desenhos/" + $( this ).val() + ".png' border='0'>";
		if ($(this).val() == "qms" || $(this).val() == "cms"  || $(this).val() == "gota" || $(this).val() == "mg") {
			$( "#alca" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
		}
		});
		$( "#"+id ).html( str );

		$("#sel_liner").change(function () {
			$( "#liner_topo_afunilado" ).removeAttr('checked');
		});

		if ($(this).val() == 'qowa2' || $(this).val() == 'cowa2' || $(this).val() == 'qowaf' || $(this).val() == 'cowaf' || $(this).val() == 'cowarf' || $(this).val() == 'qowalf' || $(this).val() == 'qowadlf' || $(this).val() == 'qowafi' || $(this).val() == 'cowafi') {
			$("#gramatura_forro").css('display','inline');
		} else {
			$("#gramatura_forro").css('display','none');
		}

		if ($(this).val() == 'cowad') {
			$('#trava_rede').attr('disabled',false);
		} else {
			$('#trava_rede').attr('disabled',true);
			$('#trava_rede').prop('checked', false);
		}

		if($(this).val() == 'mg'){
			$( "#flap" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
			$( "#baseimg" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
			$( "#carga" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
			$( "#descarga" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
			$( "#liner" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
			$( "#dim_carga1" ).css( "top","-35px" );
			$( "#dim_carga1" ).css( "left", "0px" );
			$( "#dim_carga2" ).css( "top","-35px" );
			$( "#dim_carga2" ).css( "left", "0px" );
			$( "#dim_carga3" ).css( "top","-35px" );
			$( "#dim_carga3" ).css( "left", "0px" );
			$( "#dim_carga4" ).css( "top","-35px" );
			$( "#dim_carga4" ).css( "left", "0px" );
		} else {
			$( "#baseimg" ).html( "<img src='images/desenhos/corpo.png' border='0'>" );
		}

		if($("#sel_corpo").val() == 'gota'){
			$( "#baseimg" ).html( "" );
			$( "#medidas" ).html( "<img src='images/desenhos/gota_medidas.png' border='0'>" );
			$( "#dim_altura" ).css( "top", "355px" );
			$( "#dim_altura" ).css( "left", "285px" );
			$( "#dim_alca_altura" ).css( "top", "213px" );
			$( "#dim_alca_altura" ).css( "left", "44px" );
			$( "#dim_alca_fix_altura" ).css( "top", "142px" );
			$( "#dim_alca_fix_altura" ).css( "left", "132px" );
		} else {
			$( "#dim_altura" ).css( "top", "378px" );
			$( "#dim_altura" ).css( "left", "307px" );
			$( "#dim_alca_altura" ).css( "top", "298px" );
			$( "#dim_alca_altura" ).css( "left", "307px" );
			$( "#dim_alca_fix_altura" ).css( "top", "330px" );
			$( "#dim_alca_fix_altura" ).css( "left", "307px" );
		}

		if($(this).val() == 'outros'){
			$( "#liner" ).html( "" );
			$( "#descarga" ).html( "" );
			$( "#carga" ).html( "" );
			$( "#alca" ).html( "" );
			$( "#corpo" ).html( "" );
			$( "#baseimg" ).html( "" );
			$( "#medidas" ).html( "" );
		}

		if($(this).val() == 'qms'){
			$( "#carga" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
			$( "#decarga" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
			$( "#dim_carga1" ).css( "top","-35px" );
			$( "#dim_carga1" ).css( "left", "0px" );
			$( "#dim_carga2" ).css( "top","-35px" );
			$( "#dim_carga2" ).css( "left", "0px" );
			$( "#dim_carga3" ).css( "top","-35px" );
			$( "#dim_carga3" ).css( "left", "0px" );
			$( "#dim_carga4" ).css( "top","-35px" );
			$( "#dim_carga4" ).css( "left", "0px" );
		}

		if($(this).val() == 'cms'){
			$( "#carga" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
			$( "#decarga" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
			$( "#dim_carga1" ).css( "top","-35px" );
			$( "#dim_carga1" ).css( "left", "0px" );
			$( "#dim_carga2" ).css( "top","-35px" );
			$( "#dim_carga2" ).css( "left", "0px" );
			$( "#dim_carga3" ).css( "top","-35px" );
			$( "#dim_carga3" ).css( "left", "0px" );
			$( "#dim_carga4" ).css( "top","-35px" );
			$( "#dim_carga4" ).css( "left", "0px" );
		}

	})
	.change();
}


function fclassprod() {
	if ( $("#class_prod").val() == "a" ) {
		$('#cost_fio_topo').attr('disabled',false);
		$('#cost_fio_base').attr('disabled',false);
		
	} else if ( $("#class_prod").val() == "b" ) {
		$('#cost_fio_topo').attr('disabled',false);
		$('#cost_fio_base').attr('disabled',true);
		$('#cost_fio_base').prop('checked', false );
	} else if ( $("#class_prod").val() == "c" ) {
		$('#cost_fio_topo').attr('disabled',true);
		$('#cost_fio_base').attr('disabled',true);
		$('#cost_fio_topo').prop('checked', false );
		$('#cost_fio_base').prop('checked', false );
	} else {
		$('#cost_fio_topo').attr('disabled',true);
		$('#cost_fio_base').attr('disabled',true);
		$('#cost_fio_topo').prop('checked', false );
		$('#cost_fio_base').prop('checked', false );
	}
}
function fcarga(id) {
	$( "#sel_"+ id )
	.change(function () {
		var str = "";

		if ( $("#sel_carga").val() == "c_simples" || $("#sel_carga").val() == "c_simples_afunilada" || $("#sel_carga").val() == "c_prot_presilha") {
			str_tampa = '<label class="control-label" for="gramat_tampa">Gramatura da tampa:</label><select id="gramat_tampa" name="gramat_tampa" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select><label class="control-label" for="gramat_valvula">Gramatura da válvula:</label><select id="gramat_valvula" name="gramat_valvula" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat`WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select>';
			$("#tampa").html( str_tampa );

			$('#carga1').attr('disabled',false);
			$('#carga2').attr('disabled',false);
			$('#c_quadrado').attr('disabled',false);

		} else if ( $("#sel_carga").val() == "c_prot_mochila" ) {

			str_tampa = '<label class="control-label" for="gramat_tampa">Gramatura da tampa:</label><select id="gramat_tampa" name="gramat_tampa" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select><label class="control-label" for="gramat_valvula">Gramatura da válvula:</label><select id="gramat_valvula" name="gramat_valvula" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select>';
			$("#tampa").html( str_tampa );

			$('#carga1').attr('disabled',false);
			$('#carga2').attr('disabled',false);
			$('#c_quadrado').attr('disabled',true);

		} else if ( $("#sel_carga").val() == "c_saia" ) {

			str_tampa = '<label class="control-label" for="gramat_saia">Gramatura da saia:</label><select id="gramat_saia" name="gramat_saia" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select>';
			$("#tampa").html( str_tampa );

			$('#carga1').attr('disabled',true);
			$('#carga1').attr('value','');
			$('#carga2').attr('disabled',false);
			$('#c_quadrado').attr('disabled',true);

		} else if ( $("#sel_carga").val() == "c_afunilada" ) {

			str_tampa = '<label class="control-label" for="gramat_saia">Gramatura da saia:</label><select id="gramat_saia" name="gramat_saia" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select>';
			$("#tampa").html( str_tampa );

			$('#carga1').attr('disabled',false);
			$('#carga2').attr('disabled',false);
			$('#c_quadrado').attr('disabled',true);

		} else if ( $("#sel_carga").val() == "c_tipo_as" ) {
			str_tampa = '<label class="control-label" for="gramat_tampa">Gramatura da tampa:</label><select id="gramat_tampa" name="gramat_tampa" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select><label class="control-label" for="gramat_valvula">Gramatura da válvula:</label><select id="gramat_valvula" name="gramat_valvula" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select>';
			$("#tampa").html( str_tampa );

			$('#carga1').attr('disabled',true);
			$('#carga1').attr('value','');
			$('#carga2').attr('disabled',true);
			$('#carga2').attr('value','');
			$('#c_quadrado').attr('disabled',true);

		} else {
			$("#tampa").html("");

			$('#carga1').attr('disabled',false);
			$('#carga2').attr('disabled',false);
			$('#c_quadrado').attr('disabled',false);

		}

		if ( $("#sel_carga").val() != "vazio" ) {
			if($("#c_quadrado").is(':checked')) {
				var q = "_q";
		    } else {
				var q = "";
		    }
		} else {
			var q = "";
		}

		$( "#sel_" + id + " option:selected" ).each(function() {
		str += "<img src='images/desenhos/" + $( this ).val() + q + ".png' border='0'>";
		});
		$( "#"+id ).html( str );


		if($("#sel_carga").val() == 'vazio'){
			$( "#c_quadrado" ).removeAttr('checked');
			$( "#dim_carga1" ).css( "top","-35px" );
			$( "#dim_carga1" ).css( "left", "0px" );
			$( "#dim_carga2" ).css( "top","-35px" );
			$( "#dim_carga2" ).css( "left", "0px" );
		}


		if($(this).val() == 'c_saia'){
			$( "#dim_carga1" ).css( "top","-35px" );
			$( "#dim_carga1" ).css( "left", "0px" );
			$( "#dim_carga2" ).css( "top","52px" );
			$( "#dim_carga2" ).css( "left", "205px" );
		}

		if($(this).val() == 'c_afunilada'){
			$( "#dim_carga1" ).css( "top","20px" );
			$( "#dim_carga1" ).css( "left", "140px" );
			$( "#dim_carga2" ).css( "top","52px" );
			$( "#dim_carga2" ).css( "left", "205px" );
		}

		if($(this).val() == 'c_simples'){
			$( "#dim_carga1" ).css( "top","10px" );
			$( "#dim_carga1" ).css( "left", "84px" );
			$( "#dim_carga2" ).css( "top","64px" );
			$( "#dim_carga2" ).css( "left", "153px" );
		}

		if($(this).val() == 'c_simples_afunilada'){
			$( "#dim_carga1" ).css( "top","-2px" );
			$( "#dim_carga1" ).css( "left", "85px" );
			$( "#dim_carga2" ).css( "top","43px" );
			$( "#dim_carga2" ).css( "left", "162px" );
		}

		if($(this).val() == 'c_prot_presilha'){
			$( "#dim_carga1" ).css( "top","61px" );
			$( "#dim_carga1" ).css( "left", "160px" );
			$( "#dim_carga2" ).css( "top","12px" );
			$( "#dim_carga2" ).css( "left", "85px" );
		}

		if($(this).val() == 'c_prot_mochila'){
			$( "#dim_carga1" ).css( "top","12px" );
			$( "#dim_carga1" ).css( "left", "85px" );
			$( "#dim_carga2" ).css( "top","61px" );
			$( "#dim_carga2" ).css( "left", "160px" );
/*
			$( "#dim_carga1" ).css( "top","61px" );
			$( "#dim_carga1" ).css( "left", "160px" );
			$( "#dim_carga2" ).css( "top","12px" );
			$( "#dim_carga2" ).css( "left", "85px" );
*/
		}

		if($(this).val() == 'c_tipo_as'){
			$( "#dim_carga1" ).css( "top","-35px" );
			$( "#dim_carga1" ).css( "left", "0px" );
			$( "#dim_carga2" ).css( "top","-35px" );
			$( "#dim_carga2" ).css( "left", "0px" );
		}


	})
	.change();
}

function fdescarga(id) {
	$( "#sel_"+ id )
	.change(function () {
		var str = "";

//		str_fundo_d = "Fundo: <label><input type='radio' name='fundo' value='fundo_leve' class='input' style='margin:0 0 0 15px;'> leve</label> <label><input type='radio' name='fundo' value='fundo_pesada' class='input' style='margin:0 0 0 15px;'> pesado</label>";

		if ( $("#sel_descarga").val() == "d_simples" || $("#sel_descarga").val() == "d_prot_presilha" || $("#sel_descarga").val() == "d_prot_mochila" ) {
			str_fundo_d = '<label class="control-label" for="gramat_fundo_d">Gramatura do fundo:</label><select id="gramat_fundo_d" name="gramat_fundo_d" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select><label class="control-label" for="gramat_valvula_d">Gramatura da válvula:</label><select id="gramat_valvula_d" name="gramat_valvula_d" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select>';
			$("#fundo_d").html( str_fundo_d );
		} else if ( $("#sel_descarga").val() == "d_afunilado" || $("#sel_descarga").val() == "d_total" || $("#sel_descarga").val() == "d_total_presilha" || $("#sel_descarga").val() == "d_total_blindado" || $("#sel_descarga").val() == "vazio" ) {
			str_fundo_d = '<label class="control-label" for="gramat_fundo_d">Gramatura do fundo:</label><select id="gramat_fundo_d" name="gramat_fundo_d" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select>';
			$("#fundo_d").html( str_fundo_d );
		} else {
			str_fundo_d = '<label class="control-label" for="gramat_fundo_d">Gramatura do fundo:</label><select id="gramat_fundo_d" name="gramat_fundo_d" class="form-control"><option value="">Selecione</option><?php $gramaturas_query = mysqli_query($conn,"SELECT * FROM `pedidos_gramat` WHERE `status` = '1' ORDER BY `gramat_real` ASC;"); while($gramat_row = mysqli_fetch_array($gramaturas_query)) { echo "<option value=\"".$gramat_row['gramat_real']."\">".$gramat_row['gramat_desc']."</option>"; } ?></select>';
			$("#fundo_d").html("");
		}

		if ( $("#sel_descarga").val() == "d_simples" ) {
			$('#d_redondo').attr('disabled',false);
			$('#d_redondo').prop('checked',true);
		} else if ( $("#sel_descarga").val() == "d_prot_mochila" ) {
			$('#d_redondo').attr('disabled',false);
			$('#d_redondo').prop('checked',true);
		} else {
			$('#d_redondo').attr('disabled',true);
			$('#d_redondo').prop('checked',false);
		}

		if ( $("#sel_descarga").val() == "d_total" ) {
			$('#descarga1').attr('disabled',true);
		} else {
			$('#descarga1').attr('disabled',false);
		}



		if ( $( "#sel_descarga" ).val() != "vazio" ) {
			if($("#d_redondo").is(':checked')) {
				var r = "_r";
		    } else {
				var r = "";
		    }
		} else {
			var r = "";
		}

		if($("#sel_corpo").val() == 'gota' && $("#sel_descarga").val() == "d_prot_mochila"){
			$( "#sel_" + id + " option:selected" ).each(function() {
			str += "<img src='images/desenhos/gota_valvula.png' border='0'>";
			});
			$( "#"+id ).html( str );
		} else {
			$( "#sel_" + id + " option:selected" ).each(function() {
			str += "<img src='images/desenhos/" + $( this ).val() + r + ".png' border='0'>";
			});
			$( "#"+id ).html( str );
		}

		if($("#sel_descarga").val() == 'vazio'){
			$( "#d_redondo" ).removeAttr('checked');
			$( "#dim_descarga1" ).css( "top","-35px" );
			$( "#dim_descarga1" ).css( "left", "0px" );
			$( "#dim_descarga2" ).css( "top","-35px" );
			$( "#dim_descarga2" ).css( "left", "0px" );
		}

		if($(this).val() == 'd_simples'){
			$( "#dim_descarga1" ).css( "top","218px" );
			$( "#dim_descarga1" ).css( "left", "256px" );
			$( "#dim_descarga2" ).css( "top","165px" );
			$( "#dim_descarga2" ).css( "left", "184px" );
		}

		if($(this).val() == 'd_prot_presilha'){
			$( "#dim_descarga1" ).css( "top","217px" );
			$( "#dim_descarga1" ).css( "left", "257px" );
			$( "#dim_descarga2" ).css( "top","167px" );
			$( "#dim_descarga2" ).css( "left", "179px" );
		}

		if($(this).val() == 'd_prot_mochila' && $("#sel_corpo").val() == 'gota'){
			$( "#dim_descarga1" ).css( "top","177px" );
			$( "#dim_descarga1" ).css( "left", "234px" );
			$( "#dim_descarga2" ).css( "top","129px" );
			$( "#dim_descarga2" ).css( "left", "301px" );
		} else 
		if($(this).val() == 'd_prot_mochila'){
			$( "#dim_descarga1" ).css( "top","217px" );
			$( "#dim_descarga1" ).css( "left", "257px" );
			$( "#dim_descarga2" ).css( "top","167px" );
			$( "#dim_descarga2" ).css( "left", "179px" );
		}

		if($(this).val() == 'd_afunilado'){
			$( "#dim_descarga1" ).css( "top","209px" );
			$( "#dim_descarga1" ).css( "left", "198px" );
			$( "#dim_descarga2" ).css( "top","176px" );
			$( "#dim_descarga2" ).css( "left", "136px" );
		}

		if($(this).val() == 'd_total'){
			$( "#dim_descarga1" ).css( "top","-35px" );
			$( "#dim_descarga1" ).css( "left", "0px" );
			$( "#dim_descarga2" ).css( "top","-35px" );
			$( "#dim_descarga2" ).css( "left", "0px" );
		}

		if($(this).val() == 'd_total_presilha'){
			$( "#dim_descarga1" ).css( "top","-35px" );
			$( "#dim_descarga1" ).css( "left", "0px" );
			$( "#dim_descarga2" ).css( "top","-35px" );
			$( "#dim_descarga2" ).css( "left", "0px" );
		}

		if($(this).val() == 'd_total_blindado'){
			$( "#dim_descarga1" ).css( "top","-35px" );
			$( "#dim_descarga1" ).css( "left", "0px" );
			$( "#dim_descarga2" ).css( "top","-35px" );
			$( "#dim_descarga2" ).css( "left", "0px" );
		}


	})
	.change();
}


function acessorio(id) {
/*	$('#sel_'+id).click(function () {*/
		if ($("#sel_"+id).is (':checked')) {
			$( "#"+id ).html( "<img src='images/desenhos/" + id + ".png' border='0'>" );
		} else {
			$( "#"+id ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
		}
/*	});*/
}


$("#c_quadrado").change(function() {
    modelo = $( "#sel_carga" ).val();
	if ( modelo != "vazio" ) {
	    if(this.checked) {
			$( "#carga" ).html( "<img src='images/desenhos/" + modelo + "_q.png' border='0'>" );
	    } else {
			$( "#carga" ).html( "<img src='images/desenhos/" + modelo + ".png' border='0'>" );
	    }
	}
});

$("#d_redondo").change(function() {
    modelo = $( "#sel_descarga" ).val();
	if ( modelo != "vazio" ) {
	    if(this.checked) {
			$( "#descarga" ).html( "<img src='images/desenhos/" + modelo + "_r.png' border='0'>" );
	    } else {
			$( "#descarga" ).html( "<img src='images/desenhos/" + modelo + ".png' border='0'>" );
	    }
	}
});


function dimensoes(id2) {

	$("#"+id2).keyup(function() { 
		var str = "";
		str += $( this ).val();
		$( "#dim_"+id2 ).html( str );
	}); 
}

$(function() {

if ($(window).width() > 765) {
		var offset = $("#ilustracao").offset();
		var topPadding = 15;
		$(window).scroll(function() {
			if ($(window).scrollTop() > offset.top) {
				$("#ilustracao").stop().animate({
					marginTop: $(window).scrollTop() - offset.top + topPadding - 50
				});
				} else {
					$("#ilustracao").stop().animate({
					marginTop: 0
				});
			};
		});
	}
});



$('#sel_corpo').change(function(){
  if($(this).val() == 'mg' || $(this).val() == 'gota'){ // or this.value == 'volvo'
	$( "#baseimg" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
  } else {
	$( "#baseimg" ).html( "<img src='images/desenhos/corpo.png' border='0'>" );
  }
});
/*
function laranja() {
		if ($("#sel_laranja").is (':checked')) {
			$( "#liner" ).html( "<img src='images/desenhos/bag_laranja.png' border='0'>" );
			$( "#mod_laranja" ).html( "<input type='hidden' name='liner' value='bag_laranja'>" );//setAttribute('value', 'bag_laranja');//value( "bag_laranaja" );
		} else {
			$( "#liner" ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
			$( "#mod_laranja" ).html( "" );
		}
}

function unitizador() {
		if ($("#sel_unitizador").is (':checked')) {
			$( "#unitizador" ).html( "<img src='images/desenhos/unitizador.png' border='0'>" );
			$( "#input_unit" ).css( "display","block" );
			$( "#dim_unit" ).css( "display","block" );
		} else {
			$( "#unitizador" ).html( "" );
			$( "#input_unit" ).css( "display","none" );
			$( "#unit" ).val( "" );
			$( "#dim_unit" ).css( "display","none" );
			$( "#dim_unit" ).html( "" );
		}
}
*/

function face_on(f) {
	$( "#face_"+f ).html( "<img src='images/desenhos/face_"+ f +".png' border='0'>" );
}
function face_off(f) {
	$( "#face_"+f ).html( "<img src='images/desenhos/vazio.png' border='0'>" );
}

function face_add(f) {
	var fa = $( "#sel_faces" ).val() + f || [];
	var faces = $( "#sel_faces" ).val();
	if ( faces.indexOf(f) >= 0 ) {
		var arr = $.makeArray( faces.split(" ") );
		y = jQuery.grep(arr, function(value) {
			return value != f;
		});
		$("#sel_faces").val( y.join(" ") );
		var faces1 = $( "#sel_faces" ).val().replace(/ /g,'');
		var ordem1 = $.makeArray(faces1.split(""));
		var ordem2 = ordem1.sort();
		var ordem3 = ordem2.join(" ");
		$("#sel_faces").val( ordem3 );

		face_off(f);
	} else {
		$("#sel_faces").val( fa );
		var faces2 = $( "#sel_faces" ).val().replace(/ /g,'');
		var ordem1 = $.makeArray(faces2.split(""));
		var ordem2 = ordem1.sort();
		var ordem3 = ordem2.join(" ");
		$("#sel_faces").val( ordem3 );

		face_on(f);
	};
}

</script>




</form>
<?php
    } else {
?>
<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-plus"></i> Novo orçamento</h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<br>
<?php
if ($_GET["acao"] == "novo") {
?>
<form name="orcamento_form" data-parsley-validate class="form-horizontal form-label-left" action="orcamento.php" method="get">
<br><br><br>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_cliente">Cliente</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><select name="id_cliente" id="id_cliente" class="form-control">
	    <option value="">Selecione</option>
<?php
if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
	$query_clientes = "SELECT * FROM `cad_clientes` WHERE `status` NOT LIKE '0' AND `status` NOT LIKE '1' ORDER BY `nome` ASC";
} else {
	$query_clientes = "SELECT * FROM `cad_clientes` WHERE `status` NOT LIKE '0' AND `status` NOT LIKE '1' AND `id_vend` LIKE '".$_SESSION["user"]["id"]."' ORDER BY `nome` ASC";
}

$results_clientes = mysqli_query($conn,$query_clientes);
while ($clientes = mysqli_fetch_array($results_clientes)) {
	echo "		<option value=\"".$clientes["id"]."\">".$clientes["nome"]." (".$clientes["cnpj_cpf"].") - (".$clientes["ddd"].") ".$clientes["telefone"]."</option>\n";
}

?>
	</select></div>
</div>
<br>
<div class="ln_solid"></div>
<br>
<?php
/*
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="telefone">Telefone <span class="required">*</span></label>
	<div class="col-md-1 col-sm-1 col-xs-12"><input type="number" name="ddd" id="ddd" required="required" placeholder="DDD" class="form-control col-md-7 col-xs-12" /></div>
	<div class="col-md-5 col-sm-5 col-xs-12"><input type="number" name="telefone" id="telefone" required="required" placeholder="Número" class="form-control col-md-7 col-xs-12" /></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="email_com">E-mail comercial</label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="email" name="email_com" id="email_com" class="form-control col-md-7 col-xs-12" /></div>
</div>
*/
?>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="selecao">
	<select name="selecao" id="selecao" class="selecao" style="height: 29px; width: 60px; border:0; margin-top:-5px;">
    	<option value="cnpj"<?php if ($cliente["selecao"] == "cnpj") { echo " SELECTED"; } ?>>CNPJ</option>
    	<option value="cpf"<?php if ($cliente["selecao"] == "cpf") { echo " SELECTED"; } ?>>CPF</option>
    	<option value="rut"<?php if ($cliente["selecao"] == "rut") { echo " SELECTED"; } ?>>RUT</option>
    	<option value="cuit"<?php if ($cliente["selecao"] == "cuit") { echo " SELECTED"; } ?>>CUIT</option>
    	<option value="ruc"<?php if ($cliente["selecao"] == "ruc") { echo " SELECTED"; } ?>>RUC</option>
    	<option value="rif"<?php if ($cliente["selecao"] == "rif") { echo " SELECTED"; } ?>>RIF</option>
    	<option value="outro"<?php if ($cliente["selecao"] == "outro") { echo " SELECTED"; } ?>>Outro</option>
	</select></label>
	<div class="col-md-6 col-sm-6 col-xs-12" id="localCampo"><input type="text" id="cnpj" name="cnpj_cpf" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["cnpj_cpf"]; ?>" /></div>
</div>
<br><br><br>

<div class="ln_solid"></div>
	<div class="form-group">
	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 col-sm-offset-3">
		<button type="submit" class="btn btn-success">Próximo</button>
		<a href="orcamento.php" class="btn btn-primary">Pular</a>
	</div>
</div>


</form>

<script>
$('#id_cliente').change(function() {
    if($("#id_cliente").val()===""){ 
        $('#ddd').prop('disabled', false);
        $('#telefone').prop('disabled', false);
        $('#email_com').prop('disabled', false);
        $('#selecao').prop('disabled', false);
        $('#cnpj').prop('disabled', false);
    } else {
    	$('#ddd').prop('disabled', 'disabled');
    	$('#telefone').prop('disabled', 'disabled');
    	$('#email_com').prop('disabled', 'disabled');
    	$('#selecao').prop('disabled', 'disabled');
    	$('#cnpj').prop('disabled', 'disabled');
    }
});
</script>

<?php
} else {
?>
<form name="orcamento_form" data-parsley-validate class="form-horizontal form-label-left" action="orcamento.php" method="post">
<?php
if (!$_GET) {
} else {

	if ($_GET["id_cliente"] != "") {
		if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
			$results = "SELECT * FROM `cad_clientes` WHERE `id` LIKE '".$_GET["id_cliente"]."' AND `status` LIKE '2' ORDER BY `id` DESC";
		} else {
			$results = "SELECT * FROM `cad_clientes` WHERE `id` LIKE '".$_GET["id_cliente"]."' AND `id_vend` LIKE '".$_SESSION["user"]["id"]."' AND `status` LIKE '2' ORDER BY `id` DESC";
		}
		$cliente = mysqli_fetch_array(mysqli_query($conn,$results));
		if($cliente != "") {
			echo '<input type="hidden" name="novo_cliente" value="nao">'."\n";
		} else {
			echo '<input type="hidden" name="novo_cliente" value="sim">'."\n";
		}
		?>
		<input type="hidden" name="ddd" value="<?php echo $cliente["ddd"]; ?>">
		<input type="hidden" name="telefone" value="<?php echo $cliente["telefone"]; ?>">
		<input type="hidden" name="email_com" value="<?php echo $cliente["email_com"]; ?>">
		<input type="hidden" name="id_cliente" value="<?php echo $cliente["id"]; ?>">
		<?php
	} elseif ($_GET["cnpj_cpf"] != "") {
		if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
			$results = "SELECT * FROM `cad_clientes` WHERE `cnpj_cpf` LIKE '".$_GET["cnpj_cpf"]."' AND `status` LIKE '2' ORDER BY `id` DESC";
		} else {
			$results = "SELECT * FROM `cad_clientes` WHERE `cnpj_cpf` LIKE '".$_GET["cnpj_cpf"]."' AND `id_vend` LIKE '".$_SESSION["user"]["id"]."' AND `status` LIKE '2' ORDER BY `id` DESC";
		}
		$cliente = mysqli_fetch_array(mysqli_query($conn,$results));
		if($cliente != "") {
			echo '<input type="hidden" name="novo_cliente" value="nao">'."\n";
		} else {
			echo '<input type="hidden" name="novo_cliente" value="sim">'."\n";
		}
		?>
		<input type="hidden" name="ddd" value="<?php echo $_GET["ddd"]; ?>">
		<input type="hidden" name="telefone" value="<?php echo $_GET["telefone"]; ?>">
		<input type="hidden" name="email_com" value="<?php echo $_GET["email_com"]; ?>">
		<input type="hidden" name="id_cliente" value="<?php echo $cliente["id"]; ?>">
		<?php
	}
}
?>

<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="nome_cliente">Nome do cliente <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="nome_cliente" required="required" id="autocomplete-custom-append" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["nome"]; ?>" />
	<div id="autocomplete-container" style="position: relative; float: left; width: 400px;"></div></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="cidade_cliente">Cidade <span class="required">*</span></label>
	<div class="col-md-3 col-sm-3 col-xs-12"><input type="text" name="cidade_cliente" required="required" id="autocomplete-custom-append-city" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["cidade"]; ?>" />
	<div id="autocomplete-container-city" style="position: relative; float: left; width: 400px;"></div></div>
	<label class="control-label col-md-1 col-sm-1 col-xs-12" for="cidade_cliente">Estado <span class="required">*</span></label>
	<div class="col-md-2 col-sm-2 col-xs-12"><select name="uf_cliente" class="form-control">
	<?php
	$estado = mysqli_query($conn,"SELECT * FROM estados");
	while($row = mysqli_fetch_array($estado)) {
		if ($row['status'] == 1) {
			echo "    <option ";
			if ($row['uf'] == $cliente["uf"]) { echo "SELECTED "; }
			echo "value=\"" . $row['uf'] . "\">" . $row['estado'] . "</option>\n";
		}
	}
	?>
	</select></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-3" for="selecao">
	<select name="selecao" class="selecao" style="height: 29px; width: 65px; border:0; margin-top:-5px;">
    	<option value="cnpj"<?php if ($cliente["selecao"] == "cnpj") { echo " SELECTED"; } elseif ($_GET["selecao"] == "cnpj") { echo "SELECTED"; } ?>>CNPJ</option>
    	<option value="cpf"<?php if ($cliente["selecao"] == "cpf") { echo " SELECTED"; } elseif ($_GET["selecao"] == "cpf") { echo "SELECTED"; } ?>>CPF</option>
    	<option value="rut"<?php if ($cliente["selecao"] == "rut") { echo " SELECTED"; } elseif ($_GET["selecao"] == "rut") { echo "SELECTED"; } ?>>RUT</option>
    	<option value="cuit"<?php if ($cliente["selecao"] == "cuit") { echo " SELECTED"; } elseif ($_GET["selecao"] == "cuit") { echo "SELECTED"; } ?>>CUIT</option>
    	<option value="ruc"<?php if ($cliente["selecao"] == "ruc") { echo " SELECTED"; } elseif ($_GET["selecao"] == "ruc") { echo "SELECTED"; } ?>>RUC</option>
    	<option value="rif"<?php if ($cliente["selecao"] == "rif") { echo " SELECTED"; } elseif ($_GET["selecao"] == "rif") { echo "SELECTED"; } ?>>RIF</option>
    	<option value="outro"<?php if ($cliente["selecao"] == "outro") { echo " SELECTED"; } elseif ($_GET["selecao"] == "outro") { echo "SELECTED"; } ?>>Outro</option>
	</select></label>
	<div class="col-md-6 col-sm-6 col-xs-9" id="localCampo"><input type="text" id="<?php if ($cliente["selecao"] != "") { echo $cliente["selecao"]; } else { echo "cnpj"; } ?>" name="cnpj_cpf" required="required" class="form-control col-md-7 col-xs-12" value="<?php if ($cliente["cnpj_cpf"] != "") { echo $cliente["cnpj_cpf"]; } elseif ($_GET["cnpj_cpf"] != "") { echo $_GET["cnpj_cpf"]; } ?>" /></div>
</div>
<?php
$doc_cliente = $cliente["cnpj_cpf"];
//if (!$_GET || $cliente["telefone"] == "" || $cliente["email_com"] == "") {
?>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="telefone">Telefone <span class="required">*</span></label>
	<div class="col-md-1 col-sm-1 col-xs-3"><input type="number" name="ddd" required="required" placeholder="DDD" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["ddd"]; ?>" /></div>
	<div class="col-md-5 col-sm-5 col-xs-9"><input type="number" name="telefone" required="required" placeholder="Número" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["telefone"]; ?>" /></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="contato_com">Nome do comprador <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" name="contato_com" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["contato_com"]; ?>" /></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="email_com">E-mail do comprador <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><input type="email" name="email_com" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $cliente["email_com"]; ?>" /></div>
</div>
<?php
//}
?>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Segmento <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><select name="segmento_cliente" class="form-control">
	<?php
	$segmento = mysqli_query($conn,"SELECT * FROM segmentos ORDER BY segmento ASC");
	while($row = mysqli_fetch_array($segmento)) {
		if($row['id'] == $cliente["segmento"]) { $txt_selected = " SELECTED"; } else { $txt_selected = ""; }
		echo "    <option value=\"" . $row['id'] . "\"".$txt_selected.">" . $row['segmento'] . "</option>\n";
	}
	?>
	</select></div>
</div>
<div class="ln_solid"></div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="qtde">Quantidade <span class="required">*</span></label>
	<div class="col-md-2 col-sm-2 col-xs-12"><input type="text" oninput="numero('qtde');" name="qtde" id="qtde" required="required" class="form-control col-md-7 col-xs-12" /></div>
	<label class="control-label col-md-2 col-sm-2 col-xs-12" for="referencia">Referência </label>
	<div class="col-md-2 col-sm-2 col-xs-12"><input type="text" name="referencia" class="form-control col-md-7 col-xs-12" /></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="embarques">Embarques </label>
	<div class="col-md-2 col-sm-2 col-xs-12"><input type="text" name="embarques" id="embarques" class="form-control col-md-7 col-xs-12" /></div>
	<label class="control-label col-md-2 col-sm-2 col-xs-12" for="representante">Representante </label>
<?php if ($_SESSION['user']['nivel'] == '3') { ?>
	<div class="col-md-2 col-sm-2 col-xs-12"><input type="text" name="representante" id="representante" class="form-control col-md-7 col-xs-12" value="<?php echo $_SESSION['user']['nome']; ?>" readonly /></div>
<?php } else { ?>
	<div class="col-md-2 col-sm-2 col-xs-12"><input type="text" name="representante" id="representante" class="form-control col-md-7 col-xs-12" /></div>
<?php } ?>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mercado">Mercado </label>
	<div class="col-md-6 col-sm-6 col-xs-12"><select name="mercado" class="form-control">
	<option value="int">Mercado Interno</option><option value="ext">Exportação</option></select></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="frete">Frete <span class="required">*</span></label>
	<div class="col-md-6 col-sm-6 col-xs-12"><select name="frete" class="form-control" required>
		<option value="" selected>Selecione</option>
		<option value="exw">EXW (A partir da fábrica)</option>
		<option value="fca">FCA (Transportador livre)</option>
		<option value="cpt">CPT (Frete pago até)</option>
		<option value="cip">CIP (Frete e seguro pagos até)</option>
		<option value="dat">DAT (Entregue no terminal)</option>
		<option value="dap">DAP (Entregue no local de destino)</option>
		<option value="ddp">DDP (Entregue com direitos pagos)</option>
		<option value="fas">FAS (Livre junto ao costado do navio)</option>
		<option value="fob">FOB (Livre a bordo)</option>
		<option value="cfr">CFR (Custo e frete)</option>
		<option value="cif">CIF (Custo, seguro e frete)</option>
	</select></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="fornecedora">Fornecedora </label>
	<div class="col-md-6 col-sm-6 col-xs-12"><select name="fornecedora" class="form-control">
<?
$query_fornec = mysqli_query($conn,"SELECT * FROM `fornecedora`");
while($row_fornec = mysqli_fetch_array($query_fornec)) {
?>
		<option value="valor_<? echo strtolower($row_fornec['sigla']); ?>"><? echo $row_fornec['apelido']; ?></option>
<?
}
?>
	</select></div>
</div>
<div class="form-group">
	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="usocons">Uso / consumo </label>
	<div class="col-md-6 col-sm-6 col-xs-12">
	<div class="col-md-3 col-sm-3 col-xs-6" style="line-height:35px;"><input type="radio" name="usocons" class="form-control flat" value="sim"> Sim </div>
	<div class="col-md-3 col-sm-3 col-xs-6" style="line-height:35px;"><input type="radio" name="usocons" class="form-control flat" value="nao" checked> Não </div>
    </div>
</div>


	<div class="ln_solid"></div>
		<div class="form-group">
		<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 col-sm-offset-3">
			<button type="submit" class="btn btn-success">Adicionar</button>
			<a href="orcamento.php?acao=novo" class="btn btn-primary">Cancelar</a>
		</div>
	</div>

</form>

<?php
}
?>

</div>

<?php
    }
?>


<script type="text/javascript" src="js/input_mask/jquery.maskedinput.js" /></script>
<script type="text/javascript" src="js/jquery.numeric.js"></script>
<script type="text/javascript" src="js/jquery.validate.js"></script>


<script type="text/javascript">

$("#qtde").numeric({ decimal: false, negative: false }, function() { alert("Utilize apenas números inteiros"); this.value = ""; this.focus(); });

function numero(id3) {
	$("#"+id3).numeric({ negative: false }, function() { alert("Utilize apenas números inteiros"); this.value = ""; this.focus(); });
}

$(document).ready(function(){
<?php
//echo $doc_cliente;
if($_GET["cnpj_cpf"] == "" && $doc_cliente == "") {
?>
               $("#cnpj").mask("99.999.999/9999-99");
<?php
}
?>

               $(".selecao").change(function(){
                   var Campo= $(this).val();
                   var inserirCampo= '<input type="text" id="'+Campo+'" name= "cnpj_cpf" required="required" class="form-control col-md-7 col-xs-12">';
                   $("#localCampo").html(inserirCampo);
                   $("#cnpj").mask("99.999.999/9999-99");
                   $("#cpf").mask("999.999.999-99");
                   $("#rut").mask("99.999.999-9");
                   $("#cuit").mask("99-99999999-9");
                   $("#ruc").mask("99999999-9");
                   $("#rif").mask("9-99999999-9");
               })
});


</script>


<!-- Autocomplete -->
<script type="text/javascript" src="js/autocomplete/clientes.js.php<?php echo '?id='.$_SESSION['user']['id'];?>"></script>
<script type="text/javascript" src="js/autocomplete/cidades.js.php"></script>
<script src="js/autocomplete/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(function() {
  'use strict';
  var countriesArray = $.map(countries, function(value, key) {
	return {
	  value: value,
	  data: key
	};
  });
  var cidadesArray = $.map(cidades, function(value, key) {
	return {
	  value: value,
	  data: key
	};
  });

  // Initialize autocomplete with custom appendTo:
  $('#autocomplete-custom-append').autocomplete({
	lookup: countriesArray,
	appendTo: '#autocomplete-container'
  });
  $('#autocomplete-custom-append-city').autocomplete({
	lookup: cidadesArray,
	appendTo: '#autocomplete-container-city'
  });

});
</script>


<?php
require("rodape.php");
?>