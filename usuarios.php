<?php 

    require("common.php"); 

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
/*        ?><body onload="new TabbedNotification({ title: 'Redirecionando!', text: 'Você está sendo redirecionado para fazer seu login novamente.', type: 'info', sound: false })"><?php */
//        require("rodape.php");
        die(); 
    } 


	$query_niveis = "SELECT nivel FROM niveis"; 
	try { 
		$stmt_niveis = $db->prepare($query_niveis); 
		$stmt_niveis->execute(); 
	}
	catch(PDOException $ex) { 
		die("Failed to run query: " . $ex->getMessage()); 
	} 
	$niveis = $stmt_niveis->fetchAll(); 
	$_SESSION['nivel'] = $niveis;

    require("cabecalho.php"); 


if ($_SESSION['user']['nivel'] != '1' and $_SESSION['user']['nivel'] != '2') {
?>
<div class="clearfix"></div>
<?php 
    echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Você não tem permissão para acessar esse conteúdo.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
	require("rodape.php");
    die(); 
}

// id, username, email, nome, celular, comissao, nivel

$query = "SELECT * FROM `users` WHERE `status` LIKE '1' ORDER BY `nivel` ASC, `nome` ASC"; 

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
		<h1>Usuários</h1>
	</div>
</div>
<div class="clearfix"></div>

<?php 
if ($_GET['acao'] == 'novo') {

  if(!empty($_POST)) 
    { 
        if(empty($_POST['username'])) 
        { 
			echo '<div class="x_panel" style="height:500px;"><div class="x_title"><h2><i class="fa fa-user-plus"></i> Adicionar novo usuário</h2>
			<div class="clearfix"></div></div><center><br><br><br><br><h1><i class="fa fa-user-times"></i></h1><br><h1>Atenção!</h1>
			<h2 style="line-height:30px;">Por favor preencha o usuário.</h2><br><br>
			<a href="#" onclick="window.history.back();return false;" class="btn btn-primary"><i class="fa fa-arrow-left"></i> &nbsp;Voltar</a></center>';
			require("rodape.php");	
			die();
        } 
         
        if(empty($_POST['password'])) 
        { 
			echo '<div class="x_panel" style="height:500px;"><div class="x_title"><h2><i class="fa fa-user-plus"></i> Adicionar novo usuário</h2>
			<div class="clearfix"></div></div><center><br><br><br><br><h1><i class="fa fa-user-times"></i></h1><br><h1>Atenção!</h1>
			<h2 style="line-height:30px;">Por favor preencha a senha.</h2><br><br>
			<a href="#" onclick="window.history.back();return false;" class="btn btn-primary"><i class="fa fa-arrow-left"></i> &nbsp;Voltar</a></center>';
			require("rodape.php");	
			die();
        } 
         
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
			echo '<div class="x_panel" style="height:500px;"><div class="x_title"><h2><i class="fa fa-user-plus"></i> Adicionar novo usuário</h2>
			<div class="clearfix"></div></div><center><br><br><br><br><h1><i class="fa fa-user-times"></i></h1><br><h1>Atenção!</h1>
			<h2 style="line-height:30px;">Endereço de e-mail inválido.</h2><br><br>
			<a href="#" onclick="window.history.back();return false;" class="btn btn-primary"><i class="fa fa-arrow-left"></i> &nbsp;Voltar</a></center>';
			require("rodape.php");	
			die();
        } 
         
        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                username = :username 
        "; 
         
        $query_params = array( 
            ':username' => $_POST['username']
        ); 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        $row = $stmt->fetch(); 
         
        if($row)
        { 
			echo '<div class="x_panel" style="height:500px;"><div class="x_title"><h2><i class="fa fa-user-plus"></i> Adicionar novo usuário</h2>
			<div class="clearfix"></div></div><center><br><br><br><br><h1><i class="fa fa-user-times"></i></h1><br><h1>Atenção!</h1>
			<h2 style="line-height:30px;">Este usuário já existe.</h2><br><br>
			<a href="#" onclick="window.history.back();return false;" class="btn btn-primary"><i class="fa fa-arrow-left"></i> &nbsp;Voltar</a></center>';
			require("rodape.php");	
			die();
        } 
         
        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                email = :email 
        "; 
         
        $query_params = array( 
            ':email' => $_POST['email'] 
        ); 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        $row = $stmt->fetch(); 
         
        if($row) 
        { 
			echo '<div class="x_panel" style="height:500px;"><div class="x_title"><h2><i class="fa fa-user-plus"></i> Adicionar novo usuário</h2>
			<div class="clearfix"></div></div><center><br><br><br><br><h1><i class="fa fa-user-times"></i></h1><br><h1>Atenção!</h1>
			<h2 style="line-height:30px;">Este e-mail já existe.</h2><br><br>
			<a href="#" onclick="window.history.back();return false;" class="btn btn-primary"><i class="fa fa-arrow-left"></i> &nbsp;Voltar</a></center>';
			require("rodape.php");	
			die();
        } 
         
        $query = " 
            INSERT INTO users ( 
                username, 
                password, 
                salt, 
                email,
                nome,
                celular,
                comissao,
                nivel,
                status
            ) VALUES ( 
                :username, 
                :password, 
                :salt, 
                :email,
                :nome,
                :celular,
                :comissao,
                :nivel,
                :status
            ) 
        "; 
         
        $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
         
        $password = hash('sha256', $_POST['password'] . $salt); 
         
        for($round = 0; $round < 65536; $round++) 
        { 
            $password = hash('sha256', $password . $salt); 
        } 
         
        $query_params = array( 
            ':username' => $_POST['username'], 
            ':password' => $password, 
            ':salt' => $salt, 
            ':email' => $_POST['email'],
            ':nome' => $_POST['nome'],
            ':celular' => $_POST['celular'],
            ':comissao' => $_POST['comissao'],
            ':nivel' => $_POST['nivel'],
            ':status' => "1"
        ); 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        redirect("usuarios.php"); 
         
        die("Redirecting to usuarios.php"); 
    }

?>
<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-user-plus"></i> Adicionar novo usuário</h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">
<br>
<form id="novo_usuario" data-parsley-validate class="form-horizontal form-label-left" action="usuarios.php?acao=novo" method="post">
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Usuário <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="text" name="username" required="required" class="form-control col-md-7 col-xs-12"><span class="fa fa-user form-control-feedback right" aria-hidden="true"></span></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Senha <span class="required">*</span></label>
		<div class="col-md-4 col-sm-4 col-xs-8 has-feedback"><input type="text" id="senha" name="password" required="required" class="form-control col-md-7 col-xs-12"><span class="fa fa-lock form-control-feedback right" aria-hidden="true"></span></div>
		<div class="col-md-2 col-sm-2 col-xs-4">
			<button type="button" class="btn btn-default col-md-12 col-sm-12 col-xs-12" onclick="gerasenha();"><i class="fa fa-refresh"></i> Gerar nova</button>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">E-mail <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="email" name="email" required="required" class="form-control col-md-7 col-xs-12"><span class="fa fa-envelope form-control-feedback right" aria-hidden="true"></span></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome Completo <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="text" name="nome" required="required" class="form-control col-md-7 col-xs-12"><span class="fa fa-user form-control-feedback right" aria-hidden="true"></span></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Celular <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="tel" name="celular" required="required" class="form-control col-md-7 col-xs-12"><span class="glyphicon glyphicon-earphone form-control-feedback right" aria-hidden="true"></span></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Comissão <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="text" name="comissao" required="required" class="form-control col-md-7 col-xs-12"><span class="fa fa-money form-control-feedback right" aria-hidden="true"></span></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12 has-feedback">Nível <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<select name="nivel" class="form-control">
				<?php 
				$sel_niveis = mysqli_query($conn,"SELECT * FROM niveis");
				while($row = mysqli_fetch_array($sel_niveis)) {
					echo "    <option ";
					if ($row['id'] == "1") {
						if ($_SESSION['user']['nivel'] != '1') { echo "DISABLED "; }
					}
					if ($row['id'] == $row_user[0]['nivel']) { echo "SELECTED "; }

					echo "value=\"" . $row['id'] . "\">" . utf8_encode($row['nivel']) . "</option>\n";

				//  echo "    <option value=\"" . $row['id'] . "\">" . utf8_encode($row['nivel']) . "</option>\n";
				}
				?>
			</select>
		</div>
	</div>


	<div class="ln_solid"></div>
		<div class="form-group">
		<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 col-sm-offset-3">
			<button type="submit" class="btn btn-success">Adicionar</button>
			<a href="usuarios.php" class="btn btn-primary">Cancelar</a>
		</div>
	</div>
</form>

</div>
<script>
function randomString(length, chars) {
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
    return result;
}

function gerasenha() {
	$("#senha").val( randomString(5, '0123456789abcdefghijklmnopqrstuvwxyz') );
}
</script>

<?php 
} elseif ($_GET['acao'] == 'edit') {

   if(!empty($_POST)) 
    { 
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
		    echo '<center><br><br><br><br><br><br><h1><i class="fa fa-ban fa-3x"></i></h1><br><h1>Atenção!</h1><h2 style="line-height:30px;">Endereço de e-mail inválido.</h2><br><br><br><br><a href="javascript:history.go(-1)">Voltar</a><br><br><br><br><br></center>';
			require("rodape.php"); 
			die();
        } 

/*
        if($_POST['email'] != $_SESSION['user']['email']) 
        { 
            $query = " 
                SELECT 
                    1 
                FROM users 
                WHERE 
                    email = :email 
            "; 
            $query_params = array( 
                ':email' => $_POST['email'] 
            ); 
             
            try 
            { 
                $stmt = $db->prepare($query); 
                $result = $stmt->execute($query_params); 
            } 
            catch(PDOException $ex) 
            { 
                die("Failed to run query: " . $ex->getMessage()); 
            } 
             
            $row = $stmt->fetch(); 
            if($row) 
            { 
                die("This E-Mail address is already in use"); 
            } 
        } 
*/
         
        if(!empty($_POST['password'])) 
        { 
            $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
            $password = hash('sha256', $_POST['password'] . $salt); 
            for($round = 0; $round < 65536; $round++) 
            { 
                $password = hash('sha256', $password . $salt); 
            } 
        } 
        else 
        { 
            $password = null; 
            $salt = null; 
        } 
         
        $query_params = array( 
            ':nome' => $_POST['nome'], 
            ':celular' => $_POST['celular'], 
            ':username' => $_POST['username'], 
            ':nivel' => $_POST['nivel'], 
            ':email' => $_POST['email'], 
            ':user_id' => $_POST['id'], 
            ':comissao' => $_POST['comissao'], 
        ); 
         
        if($password !== null) 
        { 
            $query_params[':password'] = $password; 
            $query_params[':salt'] = $salt; 
        } 
         
        $query = " 
            UPDATE users 
            SET 
                nome = :nome
                , celular = :celular
                , username = :username
                , nivel = :nivel
                , email = :email 
                , comissao = :comissao
        "; 
         
        if($password !== null) 
        { 
            $query .= " 
                , password = :password 
                , salt = :salt 
            "; 
        } 
         
        $query .= " 
            WHERE 
                id = :user_id 
        "; 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
//        $_SESSION['user']['email'] = $_POST['email']; 
         
        // This redirects the user back to the members-only page after they register 
		redirect("usuarios.php"); 
         
        // Calling die or exit after performing a redirect using the header function 
        // is critical.  The rest of your PHP script will continue to execute and 
        // will be sent to the user if you do not die or exit. 
        die("Redirecting to usuarios.php"); 
    } 


$id_user = $_GET['id'];

	if ($id_user == "1" && $_SESSION['user']['nivel'] != '1') {
		die("<center><br><h2>Alerta!</h2><br>Este usuário não pode ser modificado.</center>");
	}

$query_user = "SELECT * FROM users WHERE id LIKE '".$id_user."'"; 
try { 
	$stmt2 = $db->prepare($query_user); 
	$stmt2->execute(); 
}
catch(PDOException $ex) { 
	die("Failed to run query: " . $ex->getMessage()); 
} 
$row_user = $stmt2->fetchAll(); 

if ($row_user[0]['status'] == "0") {
	die("<center><br><h2>Alerta!</h2><br>Este usuário não pode ser modificado.</center>");
}


?>

<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-pencil"></i> Editar usuário</h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">

<form id="edita_usuario" data-parsley-validate class="form-horizontal form-label-left" action="usuarios.php?acao=edit" method="post">
<input type="hidden" name="id" value="<?php echo htmlentities($row_user[0]['id'], ENT_QUOTES, 'UTF-8'); ?>"/>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Usuário <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="text" name="username" required="required" readonly="readonly" class="form-control col-md-7 col-xs-12" value="<?php echo htmlentities($row_user[0]['username'], ENT_QUOTES, 'UTF-8'); ?>"><span class="fa fa-user form-control-feedback right" aria-hidden="true"></span></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Senha </label>
		<div class="col-md-4 col-sm-4 col-xs-8 has-feedback"><input type="text" name="password" class="form-control col-md-7 col-xs-12" value="" id="senha" placeholder="(Deixe em branco se não quiser alterar)"><span class="fa fa-lock form-control-feedback right" aria-hidden="true"></span></div>
		<div class="col-md-2 col-sm-2 col-xs-4">
			<button type="button" class="btn btn-default col-md-12 col-sm-12 col-xs-12" onclick="gerasenha();"><i class="fa fa-refresh"></i> Gerar nova</button>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">E-mail <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="email" name="email" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo htmlentities($row_user[0]['email'], ENT_QUOTES, 'UTF-8'); ?>"><span class="fa fa-envelope form-control-feedback right" aria-hidden="true"></span></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Nome Completo <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="text" name="nome" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo htmlentities($row_user[0]['nome'], ENT_QUOTES, 'UTF-8'); ?>"><span class="fa fa-user form-control-feedback right" aria-hidden="true"></span></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Celular <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="tel" name="celular" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo htmlentities($row_user[0]['celular'], ENT_QUOTES, 'UTF-8'); ?>"><span class="glyphicon glyphicon-earphone form-control-feedback right" aria-hidden="true"></span></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Comissão <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12 has-feedback"><input type="text" name="comissao" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo htmlentities($row_user[0]['comissao'], ENT_QUOTES, 'UTF-8'); ?>"><span class="fa fa-money form-control-feedback right" aria-hidden="true"></span></div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12 has-feedback">Nível <span class="required">*</span></label>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<select name="nivel" class="form-control">
				<?php 
				$sel_niveis = mysqli_query($conn,"SELECT * FROM niveis");
				while($row = mysqli_fetch_array($sel_niveis)) {
					echo "    <option ";
					if ($row['id'] == "1") {
						if ($_SESSION['user']['nivel'] != '1') { echo "DISABLED "; }
					}
					if ($row['id'] == $row_user[0]['nivel']) { echo "SELECTED "; }
	
					echo "value=\"" . $row['id'] . "\">" . utf8_encode($row['nivel']) . "</option>\n";
				}
				?>
			</select>
		</div>
	</div>
	<div class="ln_solid"></div>
		<div class="form-group">
		<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
			<button type="submit" class="btn btn-success">Alterar</button>
			<a href="usuarios.php" class="btn btn-primary">Cancelar</a>
		</div>
	</div>
</form>

</div>
</div>

<script>
function randomString(length, chars) {
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
    return result;
}

function gerasenha() {
	$("#senha").val( randomString(5, '0123456789abcdefghijklmnopqrstuvwxyz') );
}
</script>
<?php 

} elseif ($_GET['acao'] == 'remov') {

if ($_GET['id'] == "1") {
?>

<div class="x_panel" style="height:500px;">
<div class="x_title">
  <h2><i class="fa fa-user-times"></i> Desativar usuário</h2>
  <div class="clearfix"></div>
</div>

<center>

<br><br><br><br>
<h1><i class="fa fa-user-times"></i></h1>
<br>
<h1>Atenção!</h1>
<h2 style="line-height:30px;">Este usuário não pode ser removido.</h2>
<br><br>

<form name="new-user" class="new-user" action="usuarios.php?acao=remov" method="post">
<input type="hidden" name="id" value="<?php echo htmlentities($row_user[0]['id'], ENT_QUOTES, 'UTF-8'); ?>" />
<a href="usuarios.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> &nbsp;Voltar</a>
</form>
</center>
<?php 
	require("rodape.php");	
//	die("<center><br><h2>Alerta!</h2><br>Este usuário não pode ser removido.</center>");
	die();
}


  if(!empty($_POST)) 
    {
	$id_user = $_POST['id'];

	$query_user = "UPDATE `users` SET `status` = '0' WHERE `id` LIKE '".$id_user."'"; 
//	$query_user = "DELETE FROM `users` WHERE `id` LIKE '".$id_user."'"; 
	try { 
		$stmt2 = $db->prepare($query_user); 
		$stmt2->execute(); 
	}
	catch(PDOException $ex) { 
		die("Failed to run query: " . $ex->getMessage()); 
	} 
	redirect("usuarios.php");
	die("Redirecting to usuarios.php");
	}

$id_user = $_GET['id'];
$query_user = "SELECT id, nome FROM users WHERE id LIKE '".$id_user."'"; 
try { 
	$stmt2 = $db->prepare($query_user); 
	$stmt2->execute(); 
}
catch(PDOException $ex) { 
	die("Failed to run query: " . $ex->getMessage()); 
} 
$row_user = $stmt2->fetchAll(); 


?>
<!-- page content -->



<div class="x_panel" style="height:500px;">
<div class="x_title">
  <h2><i class="fa fa-user-times"></i> Desativar usuário</h2>
  <div class="clearfix"></div>
</div>

<center>

<br><br><br><br>
<h1><i class="fa fa-user-times"></i></h1>
<br>
<h2 style="line-height:30px;">Você tem certeza que deseja desativar o usuário "<?php echo htmlentities($row_user[0]['nome'], ENT_QUOTES, 'UTF-8'); ?>"?</h2>
<br><br>

<form name="new-user" class="new-user" action="usuarios.php?acao=remov" method="post">
<input type="hidden" name="id" value="<?php echo htmlentities($row_user[0]['id'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit" name="submit" class="btn btn-danger" /><i class="fa fa-trash"></i> &nbsp;Desativar usuário</button>
<a href="usuarios.php" class="btn btn-primary"><i class="fa fa-times"></i> &nbsp;Cancelar</a>
</form>
</center>

</div>

<?php 

} elseif ($_GET['acao'] == 'reativar') {

?>
<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-reply"></i> Reativar usuário existente</h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">
	<table border="0" width="100%" id="usuarios" class="table table-striped">
	<thead>
		<tr>
			<th>Nome</th>
			<th>Login</th>
			<th>Celular</th>
			<th class="sumir_coluna">E-mail</th>
			<th class="sumir_coluna">Nível</th>
			<th width="85">Reativar</th>
		</tr>
	</thead>
<?php 

$sql_desativados = mysqli_query($conn,"SELECT * FROM `users` WHERE `status` LIKE '0'");
while($users_desativados = mysqli_fetch_array($sql_desativados)) {
?>
<tr>
	<td bgcolor="#FFFFEE"><?php echo htmlentities(utf8_encode($users_desativados['nome']), ENT_QUOTES, 'UTF-8'); ?></td> 
	<td bgcolor="#FFFFEE"><?php echo htmlentities(utf8_encode($users_desativados['username']), ENT_QUOTES, 'UTF-8'); ?></td> 
	<td bgcolor="#FFFFEE"><?php echo htmlentities($users_desativados['celular'], ENT_QUOTES, 'UTF-8'); ?></td> 
	<td class="sumir_coluna" bgcolor="#FFFFEE"><a href="mailto:<?php echo htmlentities($users_desativados['email'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlentities($users_desativados['email'], ENT_QUOTES, 'UTF-8'); ?></a></td> 
	<td class="sumir_coluna" bgcolor="#FFFFEE"><?php echo ($_SESSION['nivel'][$users_desativados['nivel']-1]['nivel']); ?></td>
	<td bgcolor="#FFFFEE" align="center"><a href="usuarios.php?acao=reativ&id=<?php echo $users_desativados['id'];?>" class="btn btn-success btn-xs"><i class="fa fa-reply"></i> Reativar</a></td>
</tr> 
<?php 
}
?>

</table>
<div class="ln_solid"></div>
<center>
<a href="usuarios.php" class="btn btn-primary"><i class="fa fa-times"></i> Cancelar</a>
</center>
</div>
<?php 

} elseif ($_GET['acao'] == 'reativ') {

  if(!empty($_POST)) 
    {
	$id_user = $_POST['id'];

	$query_user = "UPDATE `users` SET `status` = '1' WHERE `id` LIKE '".$id_user."'"; 
//	$query_user = "DELETE FROM `users` WHERE `id` LIKE '".$id_user."'"; 
	try { 
		$stmt2 = $db->prepare($query_user); 
		$stmt2->execute(); 
	}
	catch(PDOException $ex) { 
		die("Failed to run query: " . $ex->getMessage()); 
	} 
	redirect("usuarios.php");
	die("Redirecting to usuarios.php");
	}

$id_user = $_GET['id'];
$query_user = "SELECT id, nome FROM users WHERE id LIKE '".$id_user."'"; 
try { 
	$stmt2 = $db->prepare($query_user); 
	$stmt2->execute(); 
}
catch(PDOException $ex) { 
	die("Failed to run query: " . $ex->getMessage()); 
} 
$row_user = $stmt2->fetchAll(); 


?>
<div class="x_panel" style="height:500px;">
<div class="x_title">
  <h2><i class="fa fa-reply"></i> Reativar usuário</h2>
  <div class="clearfix"></div>
</div>

<center>

<br><br><br><br>
<h1><i class="fa fa-reply"></i></h1>
<br>
<h2 style="line-height:30px;">Você tem certeza que deseja reativar o usuário "<?php echo htmlentities($row_user[0]['nome'], ENT_QUOTES, 'UTF-8'); ?>"?</h2>
<br><br>

<form name="reativa-user" action="usuarios.php?acao=reativ" method="post">
<input type="hidden" name="id" value="<?php echo htmlentities($row_user[0]['id'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit" name="submit" class="btn btn-success" /><i class="fa fa-reply"></i> &nbsp;Reativar usuário</button>
<a href="usuarios.php" class="btn btn-primary"><i class="fa fa-times"></i> &nbsp;Cancelar</a>
</form>
</center>

</div>

<?php 
} else {
?>

<div class="x_panel">
	<div class="x_title">
		<h2><i class="fa fa-users"></i> Lista de usuários</h2>
		<ul class="nav navbar-right panel_toolbox">
			<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			</li>
		</ul>
		<div class="clearfix"></div>
	</div>
<div class="x_content">
	<table border="0" width="100%" id="usuarios" class="table table-striped">
		<thead>
			<tr>
				<th>Nome</th>
				<th>Login</th>
				<th class="sumir_coluna">Celular</th>
				<th class="sumir_coluna">E-mail</th>
			<?php //	<th>Usuário</th> ?>
				<th class="sumir_coluna">Nível</th>
			<?php //	<th>Comissão</th> ?>
				<th style="width: 165px;">Ações</th>
			</tr>
		</thead>
<?php foreach($rows as $row): ?> 
<tr>
	<td><?php echo htmlentities($row['nome'], ENT_QUOTES, 'UTF-8'); ?></td> 
	<td><?php echo htmlentities($row['username'], ENT_QUOTES, 'UTF-8'); ?></td> 
	<td class="sumir_coluna"><a href="tel:<?php echo preg_replace("/[^0-9]/","",$row['celular']); ?>"><?php echo htmlentities($row['celular'], ENT_QUOTES, 'UTF-8'); ?></a></td> 
	<td class="sumir_coluna"><a href="mailto:<?php echo htmlentities($row['email'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo substr(htmlentities($row['email'], ENT_QUOTES, 'UTF-8'), 0, 25) . '...'; ?></a></td> 
<?php //	<td><?php echo htmlentities($row['username'], ENT_QUOTES, 'UTF-8'); ? ></td> ?>
	<td class="sumir_coluna"><?php echo ($_SESSION['nivel'][$row['nivel']-1]['nivel']); ?></td>
<?php //	<td><?php echo htmlentities($row['comissao'], ENT_QUOTES, 'UTF-8'); ? ></td> ?>
	<td style="width: 155px;"><a href="usuarios.php?acao=edit&id=<?php echo $row['id'];?>" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Editar </a>
	<a href="usuarios.php?acao=remov&id=<?php echo $row['id'];?>" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Desativar </a></td>
</tr> 
<?php endforeach; ?> 
</table>
<div class="ln_solid"></div><center>
<a href="usuarios.php?acao=novo" class="btn btn-default source"><i class="fa fa-user-plus"></i> Adicionar novo usuário</a>
<a href="usuarios.php?acao=reativar" class="btn btn-default source"><i class="fa fa-reply"></i> Reativar usuário existente</a>
</center>
</div>
<?php 
}

require("rodape.php");
?>