<?php
$pagina_atual = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
?>
          <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
              <ul class="nav side-menu">
<?php 
if ($_SESSION['user']['nivel'] == '5') {
?>
                 <li><a href="<?php echo $menu_location; ?>pedidos.php"><i class="fa fa-list"></i> Lista de orçamentos</a></li>
<?php 
} elseif ($_SESSION['user']['nivel'] == '6') {
} elseif ($_SESSION['user']['nivel'] == '4') {
} else {
?>
                 <li><a href="<?php echo $menu_location; ?>orcamento.php?acao=novo"><i class="fa fa-plus"></i> Novo orçamento</a></li>
                 <li><a href="<?php echo $menu_location; ?>pedidos.php"><i class="fa fa-list"></i> Lista de orçamentos<?php 

if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
  if ($qtde_venc != 0) {
          ?><span class="label label-danger pull-right"><?php echo $qtde_venc; ?></span><?php 
  }
} ?></a></li>
                 <li><a href="<?php echo $menu_location; ?>clientes.php"><i class="fa fa-briefcase"></i> Clientes</a></li>
<?php 
}
if ($_SESSION['user']['nivel'] == '1' || $_SESSION['user']['nivel'] == '2') {
  if ($_SESSION['user']['id'] == '85') {
?>
                 <li><a href="<?php echo $menu_location; ?>relatorios.php"><i class="fa fa-file-text-o"></i> Relatórios</a></li>
<?php
  }
?>
<?php
  if ($_SESSION['user']['nivel'] == '1') {
?>
                 <li><a href="<?php echo $menu_location; ?>validade.php"><i class="fa fa-calendar"></i> Orçamentos a expirar</a></li>
                 <li><a href="<?php echo $menu_location; ?>usuarios.php"><i class="fa fa-users"></i> Usuários</a></li>
                 <li><a href="<?php echo $menu_location; ?>relatorios.php"><i class="fa fa-file-text-o"></i> Relatórios</a></li>
                 <li><a href="<?php echo $menu_location; ?>rel_gerencial.php"><i class="fa fa-file-text"></i> Relatório gerencial</a></li>
                 <li><a href="<?php echo $menu_location; ?>contabilidade.php"><i class="fa fa-money"></i> Formatação de preços</a></li>
<?php
  }
}
if ($_SESSION['user']['id'] == '89') { ?>
                 <li><a href="<?php echo $menu_location; ?>rel_gerencial.php"><i class="fa fa-file-text"></i> Relatório gerencial</a></li>
<?php
}
?>
              </ul>
            </div>
<?php if ($_SESSION['user']['nivel'] == '1') { ?>
            <div class="menu_section">
              <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon-cog"></i> Ajustes <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu" style="display: none">
                    <li><a href="pdfs_gerados.php">PDFs Orçamentos</a></li>
                    <li><a href="impostos.php">Impostos</a></li>
                    <li><a href="fornecedoras.php">Fornecedoras</a></li>
                    <li><a href="politica.php">Política de vendas</a></li>
                    <li><a href="log_acesso.php">Log de acessos</a></li>
                    <li><a href="log_sistema.php">Log de sistema</a></li>
                  </ul>
                </li>
              </ul>
            </div>
<?php } ?>
          </div>
