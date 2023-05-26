<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
$servername = "127.0.0.1:3306";
$username = "packemcom_vendas";
$password = "&vQR4h=@?QD-";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}
echo "Conectado com sucesso.";
*/

$host="127.0.0.1";
$port=3306;
$socket="";
$user="packemcom_vendas";
$password="&vQR4h=@?QD-";
$dbname="packemcom_vendasDB";

$con = new mysqli($host, $user, $password, $dbname, $port, $socket)
	or die ('Could not connect to the database server' . mysqli_connect_error());
    echo "Conectado com sucesso.";


//$q_conf = "INSERT INTO `fornecedora` VALUES (1,'F1','CD',NULL),(2,'F2','Uberaba',NULL),(3,'F3','Esteio',NULL),(4,'F4','Petrolândia',NULL),(5,'F5','Agrolândia',NULL),(6,'F6','Aurora',NULL);";
//$rs_conf = mysqli_query($con, $q_conf);
  
//$query = "SELECT TABLE_NAME,TABLE_ROWS,DATA_LENGTH,AUTO_INCREMENT FROM information_schema.tables WHERE table_type='BASE TABLE';";
//$query = "ALTER TABLE `pedidos_extra` ADD COLUMN `ipi` DECIMAL(10,2) NOT NULL AFTER `icms`;";
//$query = "SELECT * FROM `pedidos_extra`;";
//$query = "SHOW TABLES;";
//$query = "SELECT * FROM estados;";
//$query = "SELECT * FROM politica;";
//$query = "TRUNCATE TABLE preco_import;";

//$result = mysqli_query($con, $query);

/*
if(mysqli_num_rows($result) > 0)
    {
       echo '<pre>';
       while($row = mysqli_fetch_assoc($result)){
           print_r($row);
       }
       echo '</pre>';
    }
    else
    {
        echo "0 results";
    }
*/

    


$con->close();

?>