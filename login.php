<?php 

    require("common.php"); 

    $submitted_username = ''; 

    if(!empty($_POST)) 
    { 
/*
        $query = " 
            SELECT 
                id, 
                username, 
                password, 
                salt, 
                email,
                nome,
                nivel,
                status
            FROM bonsuces.users 
            WHERE 
                username = :username 
        "; 
         
        $query_params = array(':username' => $_POST['username']); 

        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) { 
            // Note: On a production website, you should not output $ex->getMessage(). 
            // It may provide an attacker with helpful information about your code.  
            die("Failed to run query: " . $ex->getMessage()); 
            die("Problemas ao logar, consulte o admministrador."); 
        } 

        $login_ok = false; 
         
        $row = $stmt->fetch(); 
*/
		$query_login = "SELECT * FROM users WHERE `username` = '".$_POST['username']."' ";
		$result_login = mysqli_query($conn, $query_login);
		$login_ok = false; 
		$row = mysqli_fetch_array($result_login);

        if($row) { 
            $check_password = hash('sha256', $_POST['password'] . $row['salt']); 
            for($round = 0; $round < 65536; $round++) { 
                $check_password = hash('sha256', $check_password . $row['salt']); 
            } 
            if($check_password === $row['password']) { 
                $login_ok = true; 
            } 
        } 
        if($login_ok) 
        { 
            unset($row['salt']); 
            unset($row['password']); 
            $_SESSION['user'] = $row; 


$dia = date("d/m/Y");
$hora = date("H:i:s");


if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}


if ($_SESSION['user']['nivel'] != 1 && $_SESSION['user']['nivel'] != 2 && $_SESSION['user']['nivel'] != 3 && $_SESSION['user']['nivel'] != 4) {
	$erro = utf8_encode("Voc� n�o tem permiss�o para acessar o sistema.<br><br>Por gentileza contate o administrador.");
	unset($_SESSION['user']);
	//header("Location: mensagem.php?msg=".$erro); 
	redirect("mensagem.php?msg=".$erro); 
	die();
}


if ($_SESSION['user']['status'] == 0) {
//print_r($_SESSION['user']);
	$erro = utf8_encode("Voc� n�o tem permiss�o para efetuar o login.<br><br>Por gentileza contate o administrador.");
	unset($_SESSION['user']);
	//header("Location: mensagem.php?msg=".$erro); 
	redirect("mensagem.php?msg=".$erro); 
	die();
}


mysqli_query($conn,"INSERT INTO `log_acesso` (`id`, `id_user`, `nome`, `email`, `data`, `hora`, `ip`) VALUES (NULL, '".$row['id']."', '".$row['nome']."', '".$row['email']."', '".$dia."', '".$hora."', '".$ip."');");

            // Redirect the user to the private members-only page. 
			//header("Location: index.php");
			redirect("index.php");
            die("Redirecting to: index.php"); 
        } else { 
            // Tell the user they failed 
            header("Location: mensagem.php?msg=<b>Ops, desculpe!</b> Problemas ao efetuar o login.<br>Por gentileza contate o administrador."); 
             
            // Show them their username again so all they have to do is enter a new 
            // password.  The use of htmlentities prevents XSS attacks.  You should 
            // always use htmlentities on user submitted values before displaying them 
            // to any users (including the user that submitted them).  For more information: 
            // http://en.wikipedia.org/wiki/XSS_attack 
            $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8'); 
        } 
    } else {
		//header("Location: login.html"); 
		redirect("login.html"); 
		die("Redirecting to: login.html"); 
    }
     
?> 
