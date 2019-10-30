<?php
  require_once('./db.php');
  var_dump($_POST);
  if(isset($_POST) && !empty($_POST)) {
    $id = $_POST['id'] + 0;

    $query = "DELETE FROM vehicle WHERE id=$id;";
    $res = pg_query($db, $query);
    if (pg_affected_rows($res) > 0) {
      header('Location: ./vehicles.php?action=delet&result=success');
      exit;
    } else {
      header('Location: ./vehicles.php?action=delete&result=failed');
      exit;
    }
  } else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
  }
?>