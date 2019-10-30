<?php function sidebar($active=0) { ?>
  <body id="page-top">
<?php require_once('navbar.php'); ?>
    <div id="wrapper">
      <ul class="sidebar navbar-nav">
        <li class="nav-item <?php echo ($active==1 ? 'active' : '' );?>">
          <a class="nav-link" href="./vehicles.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Vehicles</span>
          </a>
        </li>
        <li class="nav-item <?php echo ($active==2 ? 'active' : '' );?>">
          <a class="nav-link" href="./performance.php">
            <i class="fas fa-fw fa-user-alt"></i>
            <span>Performance</span></a>
        </li>
        <li class="nav-item <?php echo ($active==4 ? 'active' : '' );?>">
          <a class="nav-link" href="./income.php">
            <i class="fas fa-fw fa-money-bill-alt"></i>
            <span>Income</span></a>
        </li>
      </ul>
      <div id="content-wrapper">
<?php } ?>