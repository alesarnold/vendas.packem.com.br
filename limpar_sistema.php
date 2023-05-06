<?php
require("common.php"); 

$query = "DELETE FROM `log_sistema` WHERE `log_sistema`.`id_user` = 2;";
mysqli_query($conn,$query);

redirect("log_sistema.php");

die();
?>