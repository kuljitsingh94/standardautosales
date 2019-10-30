<?php require_once('db.php'); ?>

<!-- <?php 
  $res = pg_query($db, "SELECT * FROM salesperson");
  while($row = pg_fetch_assoc($res)) {
    var_dump($row);
  }
?> -->

<?php require_once('header.php'); ?>
<?php head('Standard Auto Sales'); ?>
<?php require_once('sidebar.php');?>
<?php sidebar(); ?>

<div class="container-fluid">
  <div class='hidden-xs col md-12'>
    <p>BLANK</p>
  </div>
</div>

<?php require_once('footer.php'); ?>
<?php footerScripts(); ?>