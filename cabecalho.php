<?php 

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Sistema de Orçamentos</title>

  <!-- Bootstrap core CSS -->

  <link href="css/bootstrap.css" rel="stylesheet">

  <link href="fonts/css/font-awesome.min.css" rel="stylesheet">
  <link href="css/animate.min.css" rel="stylesheet">

  <!-- Custom styling plus plugins -->
  <link href="css/custom.css" rel="stylesheet">
  <link href="css/icheck/flat/green.css" rel="stylesheet">

  <!-- editor -->
  <link href="css/editor/external/google-code-prettify/prettify.css" rel="stylesheet">
  <link href="css/editor/index.css" rel="stylesheet">

  <!-- select2 -->
  <link href="css/select/select2.min.css" rel="stylesheet">

  <!-- switchery -->
  <link rel="stylesheet" href="css/switchery/switchery.min.css" />

  <script src="js/jquery.min.js"></script>

<?php 
/*
  <script src="js/jquery.maskedinput-1.1.4.pack.js" /></script>
  <script src="js/jquery.numeric.js"></script>
  <script src="js/jquery.validate.js"></script>
*/
?>

  <!--[if lt IE 9]>
        <script src="../assets/js/ie8-responsive-file-warning.js"></script>
        <![endif]-->

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

</head>


<body class="nav-md">

  <div class="container body">


    <div class="main_container">

      <div class="col-md-3 left_col">
        <div class="left_col">

          <div class="navbar nav_title" style="border: 0;">
            <a href="index.php" class="site_title"><i></i> <span>Packem S.A.</span></a>
          </div>
          <div class="clearfix"></div>

          <!-- menu prile quick info -->
          <div class="profile">
<?php 
$filename = 'images/perfil/user_'.$_SESSION['user']['id'].'.jpg';

if (file_exists($filename)) {
?>
			<div class="profile_pic">
              <img src="<?php  echo $filename; ?>" alt="..." class="img-circle profile_img">
            </div>
<?php 
} else {
?>
			<div class="profile_pic">
              <i class="fa fa-user img-circle profile_img"></i>
            </div>
<?php 
}
?>

            <div class="profile_info">
              <span>Bem-vindo(a),</span>
              <h2><?php echo htmlentities($_SESSION['user']['nome'], ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>
          </div>
          <!-- /menu prile quick info -->

          <br />

<?php 

// $query_vencendo = mysqli_query($conn,"SELECT DISTINCT `id_ped` FROM `pedidos_aprova` WHERE CURDATE() = DATE( DATE_ADD( `data` , INTERVAL 5 DAY ) ) ORDER BY `id_ped` ASC"); // + INTERVAL 1 DAY
$query_vencendo = mysqli_query($conn,"SELECT tt.* FROM `pedidos_aprova` tt INNER JOIN (SELECT `id_ped`, MAX(`id`) AS `id` FROM `pedidos_aprova` GROUP BY `id_ped`) groupedtt ON tt.id_ped = groupedtt.id_ped AND tt.id = groupedtt.id AND CURDATE() = DATE( DATE_ADD( `data` , INTERVAL 5 DAY ) ) ORDER BY `id_ped` ASC"); // + INTERVAL 1 DAY // DATE('2016-04-22')

//echo "SELECT tt.* FROM `pedidos_aprova` tt INNER JOIN (SELECT `id_ped`, MAX(`id`) AS `id` FROM `pedidos_aprova` GROUP BY `id_ped`) groupedtt ON tt.id_ped = groupedtt.id_ped AND tt.id = groupedtt.id AND CURDATE() = DATE( DATE_ADD( `data` , INTERVAL 5 DAY ) ) ORDER BY `id_ped` ASC";

$qtde_venc = mysqli_num_rows($query_vencendo);

?>

          <!-- sidebar menu -->
<?php  require("menu.php"); ?>
          <!-- /sidebar menu -->

<?php 
/*
          <!-- /menu footer buttons -->
          <div class="sidebar-footer hidden-small">
            <a data-toggle="tooltip" data-placement="top" title="Settings">
              <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="FullScreen">
              <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Lock">
              <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Sair" href="logout.php">
              <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
          </div>
          <!-- /menu footer buttons -->
*/
?>
        </div>
      </div>


      <!-- top navigation -->
      <div class="top_nav">

        <div class="nav_menu hidden-print">
          <nav class="" role="navigation">
            <div class="nav toggle">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
              <li class="">
                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-user"></i><?php echo htmlentities($_SESSION['user']['nome'], ENT_QUOTES, 'UTF-8'); ?>
                  <span class=" fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
                  <li><a href="usuarios.php?acao=edit&id=<?php echo htmlentities($_SESSION['user']['id'], ENT_QUOTES, 'UTF-8'); ?>">  Perfil</a>
                  </li>
                  <li>
                    <a href="usuarios.php">
                      <span>Usuários</span>
                    </a>
                  </li>
                  <li><a href="logout.php"><i class="fa fa-sign-out pull-right"></i> Sair</a>
                  </li>
                </ul>
              </li>



            </ul>
          </nav>
        </div>

      </div>
      <!-- /top navigation -->



      <!-- page content -->
      <div class="right_col" role="main">
