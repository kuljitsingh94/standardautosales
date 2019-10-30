<?php
  require_once('./db.php');
  var_dump($_POST);
  if(isset($_POST) && !empty($_POST)) {
    $id = $_POST['id'] + 0;
    $year= $_POST['year'] + 0;
    $make= $_POST['make'];
    $model= $_POST['model'];
    $color= $_POST['color'];
    $vin= $_POST['vin'];
    $invoice_price= $_POST['invoice_price'] + 0;
    $odo_reading= $_POST['odo_reading'] + 0;
    $date_received= $_POST['date_received'];

    $query = "SELECT code FROM maker WHERE make='$make' AND model='$model'";
    var_dump($query);
    $res = pg_query($db, $query);
    if (pg_num_rows($res) > 0) {
      $row = pg_fetch_assoc($res);
      $maker_code=$row['code'];
      $query = "UPDATE vehicle SET 
        year=$year, 
        maker_code=$maker_code,
        color='$color',
        vin='$vin',
        invoice_price=$invoice_price,
        odo_reading=$odo_reading,
        date_received='$date_received' 
        WHERE id=$id;";
      $res = pg_query($db, $query);
      if (pg_affected_rows($res) > 0) {
        header('Location: ./vehicles.php?action=updat&result=success');
        exit;
      } else {
        header('Location: ./vehicles.php?action=update&result=failed');
        exit;
      }
    } else {
      header('Location: ./vehicles.php?action=update&result=failed&e=Invalid make and model. Could not update.');
      exit;
    }

  } else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
  }
?>