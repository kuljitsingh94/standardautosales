<?php function mapCSV($file) {
    $csv = array_map('str_getcsv', file($file));
    array_walk($csv, function(&$a) use ($csv) {
      $a = array_combine($csv[0], $a);
    });
    array_shift($csv); # remove column header
    return $csv;
  }
  
  require_once('./db.php');
  // echo "<pre>";
  var_dump($_POST);
  var_dump($_FILES);
  if(isset($_POST) && !empty($_POST) && !isset($FILES['addVehicleCSV']) ){
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
    } else {
      header('Location: ./vehicles.php?action=add&result=failed&e=Invalid make and model. Could not add.');
      exit;
    }

    $query = "SELECT id FROM vehicle WHERE vin='$vin';";
    $res = pg_query($db, $query);
    if (pg_num_rows($res) > 0) {
      header('Location: ./vehicles.php?action=add&result=failed&e=Vehicle with entered VIN already exists. Could not add.');
      exit;
    }

    $query = "INSERT INTO vehicle (id, year, maker_code, color, vin, invoice_price, odo_reading, date_received) VALUES (
      default,
      $year, 
      $maker_code,
      '$color',
      '$vin',
      $invoice_price,
      $odo_reading,
      '$date_received' 
      );";
    var_dump($query);
    $res = pg_query($db, $query);
    if (pg_affected_rows($res) > 0) {
      header('Location: ./vehicles.php?action=add&result=success');
      exit;
    } else {
      header('Location: ./vehicles.php?action=add&result=failed');
      exit;
    }

  } elseif (isset($_FILES['addVehicleCSV'])) {
    //add from CSV
    // var_dump($_POST);
    // var_dump($_FILES);
    $file = mapCSV($_FILES['addVehicleCSV']['tmp_name']);
    // var_dump($file);
    

    $success=0;
    $fail=0;

    foreach($file as $row) {
      // var_dump($row);
      $maker_code = $row['maker_code'];
      $year = $row['year'];
      $color = $row['color'];
      $vin = $row['vin'];
      $invoice_price = $row['invoice_price'];
      $date_received = $row['date_received'];
      $odo_reading = $row['odo_reading'];
   
      

      // echo("ROW MAKER CODE = ".$row['maker_code']);

      $query = "SELECT id FROM vehicle WHERE vin='$vin';";
      $res = pg_query($db, $query);
      if (pg_num_rows($res) > 0) {
        $fail++;
      } else {
        $query = "INSERT INTO vehicle (id, year, maker_code, color, vin, invoice_price, odo_reading, date_received) VALUES (
          default,
          $year, 
          $maker_code,
          '$color',
          '$vin',
          $invoice_price,
          $odo_reading,
          '$date_received' 
          );";
        var_dump($query);
        $res = pg_query($db, $query);
        if (pg_affected_rows($res) > 0) {
          $success++;
        } else {
          $fail++;
        }
      }
    }

    // echo "\nSTART\n";
    // echo "SUCCESS: $success\n";
    // echo "FAIL: $fail\n";
    // echo "COUNT: ".count($file)."\n";

    if($success == count($file)) {
      // echo 1;
      header('Location: ./vehicles.php?action=add&result=success');
      exit;
    } elseif($success > 0) {
      // echo 2;
      header("Location: ./vehicles.php?action=add&result=partial&sCount=$success&fCount=$fail");
      exit;
    } elseif($fail == count($file)) {
      // echo 3;
      header('Location: ./vehicles.php?action=add&result=failed&e=No vehicles added.');
      exit;
    } else {
      // echo 4;
      header('Location: ./vehicles.php?action=add&result=failed');
      exit;
    }
    // echo "WHY ARE WE HERE";
    // echo "</pre>";
  } else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
  }
?>