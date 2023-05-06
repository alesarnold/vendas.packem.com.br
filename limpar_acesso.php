<?php
require("common.php"); 

$query = "DELETE FROM `log_acesso` WHERE `log_acesso`.`id_user` = 2;";
mysqli_query($conn,$query);

redirect("log_acesso.php");

die();
?>