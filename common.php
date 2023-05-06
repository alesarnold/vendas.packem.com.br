<?php 

date_default_timezone_set('America/Sao_Paulo');

    $username = "packemcom_vendas"; 
    $password = "&vQR4h=@?QD-"; 
    $host = "127.0.0.1"; 
    $dbname = "packemcom_vendasDB"; 
/*
if( '20241231' <= date("Ymd") ) {
	die('<center><br><h1>A server error occurred.</h1>Please contact the developer: <a href="mailto:mellogustavo@gmail.com">mellogustavo@gmail.com</a></center>');
}
*/
$conn=mysqli_connect($host,$username,$password,$dbname);
if (mysqli_connect_errno()) {
  die ("Failed to connect to MySQL: " . mysqli_connect_error());
}

    // UTF-8 is a character encoding scheme that allows you to conveniently store 
    // a wide varienty of special characters, like ¢ or €, in your database. 
    // By passing the following $options array to the database connection code we 
    // are telling the MySQL server that we want to communicate with it using UTF-8 
    // See Wikipedia for more information on UTF-8: 
    // http://en.wikipedia.org/wiki/UTF-8 
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
     
    // A try/catch statement is a common method of error handling in object oriented code. 
    // First, PHP executes the code within the try block.  If at any time it encounters an 
    // error while executing that code, it stops immediately and jumps down to the 
    // catch block.  For more detailed information on exceptions and try/catch blocks: 
    // http://us2.php.net/manual/en/language.exceptions.php 
    try 
    { 
        // This statement opens a connection to your database using the PDO library 
        // PDO is designed to provide a flexible interface between PHP and many 
        // different types of database servers.  For more information on PDO: 
        // http://us2.php.net/manual/en/class.pdo.php 
        $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); 
    } 
    catch(PDOException $ex) 
    { 
        // If an error occurs while opening a connection to your database, it will 
        // be trapped here.  The script will output an error and stop executing. 
        // Note: On a production website, you should not output $ex->getMessage(). 
        // It may provide an attacker with helpful information about your code 
        // (like your database username and password). 
        die("Failed to connect to the database: " . $ex->getMessage()); 
    } 
     
    // This statement configures PDO to throw an exception when it encounters 
    // an error.  This allows us to use try/catch blocks to trap database errors. 
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
     
    // This statement configures PDO to return database rows from your database using an associative 
    // array.  This means the array will have string indexes, where the string value 
    // represents the name of the column in your database. 
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
     
    // This block of code is used to undo magic quotes.  Magic quotes are a terrible 
    // feature that was removed from PHP as of PHP 5.4.  However, older installations 
    // of PHP may still have magic quotes enabled and this code is necessary to 
    // prevent them from causing problems.  For more information on magic quotes: 
    // http://php.net/manual/en/security.magicquotes.php 
/*
    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) 
    { 
        function undo_magic_quotes_gpc(&$array) 
        { 
            foreach($array as &$value) 
            { 
                if(is_array($value)) 
                { 
                    undo_magic_quotes_gpc($value); 
                } 
                else 
                { 
                    $value = stripslashes($value); 
                } 
            } 
        } 
     
        undo_magic_quotes_gpc($_POST); 
        undo_magic_quotes_gpc($_GET); 
        undo_magic_quotes_gpc($_COOKIE); 
    } 
*/     
    // This tells the web browser that your content is encoded using UTF-8 
    // and that it should submit content back to you using UTF-8 

     header('Content-Type: text/html; charset=utf-8'); 
     
    // This initializes a session.  Sessions are used to store information about 
    // a visitor from one web page visit to the next.  Unlike a cookie, the information is 
    // stored on the server-side and cannot be modified by the visitor.  However, 
    // note that in most cases sessions do still use cookies and require the visitor 
    // to have cookies enabled.  For more information about sessions: 
    // http://us.php.net/manual/en/book.session.php 
    session_start(); 

    // Note that it is a good practice to NOT end your PHP files with a closing PHP tag. 
    // This prevents trailing newlines on the file from being included in your output, 
    // which can cause problems with redirecting users.


/* ============ FUNCÃO PARA GRAVAR LOG ============ */


function Log_Sis($l_pedido,$l_id_user,$l_nome,$l_desc) {

global $conn;

//$l_desc = utf8_decode($l_desc);

if ($l_pedido == "") { $l_pedido = "0"; }

$sql_log_sis = "INSERT INTO `log_sistema` (`id`, `pedido`, `id_user`, `nome`, `desc`, `data`) VALUES (NULL, '".$l_pedido."', '".$l_id_user."', '".$l_nome."', '".$l_desc."', '".date('Y-m-d H:i:s')."');";
$log_sistema = mysqli_query( $conn, $sql_log_sis );
if(! $log_sistema ) { 

//echo $sql_log_sis."<br><br>";

die('Não foi possível gerar log do sistema. ' . mysqli_error($conn)); }

}

function Log_Rel($l_id_user,$l_nome,$l_desc,$l_ip) {

	global $conn;

	$l_desc = utf8_decode($l_desc);

	$sql_log_sis = "INSERT INTO `log_relatorios` (`id`, `id_user`, `nome`, `desc`, `data`, `ip`) VALUES (NULL, '".$l_id_user."', '".$l_nome."', '".$l_desc."', '".date('Y-m-d H:i:s')."', '".$l_ip."');";
	$log_sistema = mysqli_query( $conn, $sql_log_sis );
	if(! $log_sistema ) { 

	die('Não foi possível gerar log de relatório. ' . mysqli_error()); }

}

function Envia_Email($ee_nome,$ee_de,$ee_para,$ee_assunto,$ee_mensagem,$ee_numero_ped) {

    $emailsender='guilherme.nascimento@packem.com.br';
     
    if(PHP_OS == "Linux") $quebra_linha = "\n"; //Se for Linux
    elseif(PHP_OS == "WINNT") $quebra_linha = "\r\n"; // Se for Windows
    else die("Este script nao esta preparado para funcionar com o sistema operacional de seu servidor");
     
    $nomeremetente     = $ee_nome;
    $emailremetente    = $emailsender; //$ee_de;
    $emaildestinatario = $ee_para;
    $assunto           = $ee_assunto;
    $mensagem          = $ee_mensagem;
     
    $mensagemHTML = $mensagem;

    $headers = "MIME-Version: 1.1".$quebra_linha;
    $headers .= "Content-type: text/html; charset=iso-8859-1".$quebra_linha;
/*
    $headers .= "X-Priority: 1 (Highest)".$quebra_linha;
    $headers .= "X-MSMail-Priority: High".$quebra_linha;
    $headers .= "Importance: High".$quebra_linha;
*/
    $headers .= "From: ".$emailsender.$quebra_linha;
    $headers .= "Return-Path: " . $emailsender . $quebra_linha;
    $headers .= "Reply-To: ".$emailremetente.$quebra_linha;
     
    mail($emaildestinatario, $assunto, utf8_decode($mensagemHTML), $headers, "-r". $emailsender);
};

function redirect($url) {
    if (!headers_sent()) {    
        header('Location: '.$url);
        exit;
	} else {  
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>'; exit;
    }
};

function tiraZero($valor) {
	if (substr($valor,-3) == ",00") {
		$valor = substr($valor,0,-3);
	} elseif (substr($valor,-4) == ",000") {
		$valor = substr($valor,0,-4);
	} elseif (substr($valor,-1) == "0") {
		$valor = substr($valor,0,-1);
	}
	return $valor;
}

function mask($val, $mask) {
	$maskared = '';
	$k = 0;
	for($i = 0; $i<=strlen($mask)-1; $i++) {
		if($mask[$i] == '#') {
			if(isset($val[$k]))
			$maskared .= $val[$k++];
		} else {
			if(isset($mask[$i]))
			$maskared .= $mask[$i];
		}
	}
	return $maskared;
}

$des_especiais = array("00000");


if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

/*
if ($_SESSION['user']['nivel'] != 3) {
	if (substr($ip, 0, 9) != "187.72.86") {
		//$_SESSION['user']['nivel'] = 3;
		/ *
		$erro = "Você não tem permissão para acessar o sistema.<br><br>Por gentileza contate o administrador";
		unset($_SESSION['user']);
		//header("Location: mensagem.php?msg=".$erro); 
		redirect("mensagem.php?msg=".$erro); 
		die();
		* /
	}
}
*/
/*
echo "<pre>";
echo "REQUEST_URI:".$_SERVER['REQUEST_URI']."\n";
echo "SERVER_NAME:".$_SERVER['SERVER_NAME']."\n";
foreach ($_SERVER as $parm => $value)  echo "$parm = '$value'\n";
echo "</pre>";
*/
?>