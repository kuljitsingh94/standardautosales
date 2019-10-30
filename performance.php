<?php require_once('db.php'); ?>
<?php require_once('timeFunctions.php'); ?>

<!-- <?php 
  $res = pg_query($db, "SELECT * FROM salesperson");
  while($row = pg_fetch_assoc($res)) {
    var_dump($row);
  }
?> -->

<?php require_once('header.php'); ?>
<?php head('Standard Auto Sales'); ?>
<?php require_once('sidebar.php');?>
<?php sidebar(2); ?>

<div class="container-fluid">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          <i class="fas fa-user-alt"></i>
          Salesperson Performance
          <button class="btn btn-secondary dropdown-toggle" style="margin-left: 10px; background-color:white; color:black;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo $_GET['name'] ? $_GET['name'] : "Select Salesperson..."; ?>
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
<?php
  $query = "SELECT * FROM salesperson";
  $res = pg_query($query);
  while($row = pg_fetch_assoc($res)) {
    // var_dump($row);
    $name = $row['first_name'].' '.$row['last_name'];
    $id= $row['id'];
    echo "<a class='dropdown-item";
    if($name == $_GET['name']) echo " active' style='background-color:green;";
    echo "' href='./performance.php?name=$name&id=$id'>$name</a>";
  }
?>
          </div>
<?php if($_GET['name'] == '') { ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } else { ?>

          <button 
            id='generatePDF'
            type="button" 
            class="btn btn-primary ml-3"
            data-toggle="modal"
            data-target="#customDateModal"
          >
            <i class="fas fa-fw fa-file" aria-hidden="true"></i>
            Generate Custom Date PDF Report 
          </button>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Today</h5>
                  <p class="card-text">
                    <ul>
                      <li>
                        Customer Visits: 
<?php
  $name = $_GET['name'];
  $sid = $_GET['id'];
  $query = "SELECT * FROM visit WHERE salesperson_id=$sid AND date='";
  $query.=date('o-m-d');
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Test Drives: 
<?php
  $query = "SELECT * FROM testdrive t, visit v WHERE salesperson_id=$sid AND visit_id=v.id AND date='";
  $query.=date('o-m-d');
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Sales: 
<?php
  $query = "SELECT * FROM sale s, vehicle v WHERE salesperson_id=$sid AND vehicle_id=v.id AND date='";
  $query.= date('o-m-d');
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  $todayRevenue = 0;
  $todayCOGS = 0;
  while($row = pg_fetch_assoc($res)) {
    // var_dump($row);
    $todayRevenue+= $row['sale_price'];
    $todayCOGS+= $row['invoice_price'];
  }
  setlocale(LC_MONETARY, 'en_US.UTF-8');
  echo pg_num_rows($res);
  // echo money_format("%.2n",$todayRevenue);
?>
                      </li>
                      <li>
                        Revenue Generated:
<?php 
  echo money_format("%.2n", $todayRevenue); 
?> 
                      </li>
                      <li>
<?php
  if($todayRevenue >= $todayCOGS) {
    echo "Profit Generated: <span style='color:green;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  } else {
    echo "Loss Generated: <span style='color:red;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  }
?> 
                      </li>
                    </ul>
                  </p>
                  <form
                    method='POST'
                    action='./detailedPerformanceReport.php'  
                  >
                    <input type='text' name='start' hidden
                     value='<?php echo date("o-m-d"); ?>'
                    > 
                    <input type='text' name='end' hidden
                     value='<?php echo date("o-m-d", strtotime("+1 day")); ?>'
                    >
                    <input type='text' name='name' hidden
                      value='<?php echo $name; ?>'
                    > 
                    <input type='text' name='sid' hidden
                      value='<?php echo $sid; ?>'
                    > 
                    <button 
                      id='today'
                      type="submit" 
                      class="btn btn-primary ml-3"
                    >
                    <i class="fas fa-fw fa-file" aria-hidden="true"></i>
                    Generate Detailed PDF Report 
                  </button>
                </form>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Week</h5>
                  <p class="card-text">
                    <ul>
                      <li>
                        Customer Visits:  
<?php
  $sid =$_GET['id'];
  $query = "SELECT * FROM visit WHERE salesperson_id=$sid AND date>='";
  $query.=date('o-m-d', strtotime("last sunday"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("next monday"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Test Drives: 
<?php
  $query = "SELECT * FROM testdrive t, visit v WHERE salesperson_id=$sid AND visit_id=v.id AND date>'";
  $query.=date('o-m-d', strtotime("last sunday"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("next monday"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Sales: 
<?php
  $query = "SELECT * FROM sale s, vehicle v WHERE salesperson_id=$sid AND vehicle_id=v.id AND date>='";
  $query.=date('o-m-d', strtotime("last sunday"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("next monday"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  $todayRevenue = 0;
  $todayCOGS = 0;
  while($row = pg_fetch_assoc($res)) {
    // var_dump($row);
    $todayRevenue+= $row['sale_price'];
    $todayCOGS+= $row['invoice_price'];
  }
  setlocale(LC_MONETARY, 'en_US.UTF-8');
  echo pg_num_rows($res);
  // echo money_format("%.2n",$todayRevenue);
?>
                      </li>
                      <li>
                        Revenue Generated:
<?php 
  echo money_format("%.2n", $todayRevenue); 
?> 
                      </li>
                      <li>
<?php
  if($todayRevenue >= $todayCOGS) {
    echo "Profit Generated: <span style='color:green;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  } else {
    echo "Loss Generated: <span style='color:red;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  }
?> 
                      </li>
                    </ul>
                  </p>
                  <form
                    method='POST'
                    action='./detailedPerformanceReport.php'  
                  >
                    <input type='text' name='start' hidden
                     value='<?php echo date('o-m-d', strtotime("last sunday")); ?>'
                    > 
                    <input type='text' name='end' hidden
                     value='<?php echo date('o-m-d', strtotime("next monday")); ?>'
                    > 
                    <input type='text' name='name' hidden
                      value='<?php echo $name; ?>'
                    > 
                    <input type='text' name='sid' hidden
                      value='<?php echo $sid; ?>'
                    > 
                    <button 
                      id='today'
                      type="submit" 
                      class="btn btn-primary ml-3"
                    >
                      <i class="fas fa-fw fa-file" aria-hidden="true"></i>
                      Generate Detailed PDF Report 
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Month</h5>
                  <p class="card-text">
                    <ul>
                      <li>
                        Customer Visits: 
<?php
  $sid = $_GET['id'];
  $query = "SELECT * FROM visit WHERE salesperson_id=$sid AND date>='";
  $query.=date('o-m-d', strtotime("first day of this month"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("first day of next month"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Test Drives: 
<?php
  $query = "SELECT * FROM testdrive t, visit v WHERE salesperson_id=$sid AND visit_id=v.id AND date>'";
  $query.=date('o-m-d', strtotime("first day of this month"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("first day of next month"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Sales: 
<?php
  $query = "SELECT * FROM sale s, vehicle v WHERE salesperson_id=$sid AND vehicle_id=v.id AND date>='";
  $query.=date('o-m-d', strtotime("first day of this month"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("first day of next month"));
  $query.= "'";
  $res = pg_query($db, $query);
  $todayRevenue = 0;
  $todayCOGS = 0;
  while($row = pg_fetch_assoc($res)) {
    // var_dump($row);
    $todayRevenue+= $row['sale_price'];
    $todayCOGS+= $row['invoice_price'];
  }
  setlocale(LC_MONETARY, 'en_US.UTF-8');
  echo pg_num_rows($res);
  // echo money_format("%.2n",$todayRevenue);
?>
                      </li>
                      <li>
                        Revenue Generated:
<?php 
  echo money_format("%.2n", $todayRevenue); 
?> 
                      </li>
                      <li>
<?php
  if($todayRevenue >= $todayCOGS) {
    echo "Profit Generated: <span style='color:green;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  } else {
    echo "Loss Generated: <span style='color:red;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  }
?> 
                      </li>
                    </ul>
                  </p>
                  <form
                    method='POST'
                    action='./detailedPerformanceReport.php'  
                  >
                    <input type='text' name='start' hidden
                     value='<?php echo date('o-m-d', strtotime("first day of this month")); ?>'
                    > 
                    <input type='text' name='end' hidden
                     value='<?php echo date('o-m-d', strtotime("first day of next month")); ?>'
                    > 
                    <input type='text' name='name' hidden
                      value='<?php echo $name; ?>'
                    > 
                    <input type='text' name='sid' hidden
                      value='<?php echo $sid; ?>'
                    > 
                    <button 
                      id='today'
                      type="submit" 
                      class="btn btn-primary ml-3"
                    >
                      <i class="fas fa-fw fa-file" aria-hidden="true"></i>
                      Generate Detailed PDF Report 
                    </button>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Quarter</h5>
                  <p class="card-text">
                    <ul>
                      <li>
                        Customer Visits: 
<?php
  $thisQuarter = get_dates_of_quarter();
  // var_dump($thisQuarter['start']);
  $query = "SELECT * FROM visit WHERE salesperson_id=$sid AND date>='";
  $query.= $thisQuarter['start']->format("Y-m-d");
  $query.= "' AND date<'";
  $query.= $thisQuarter['end']->format("Y-m-d");
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Test Drives: 
<?php
  $query = "SELECT * FROM testdrive t, visit v WHERE salesperson_id=$sid AND visit_id=v.id AND date>'";
  $query.= $thisQuarter['start']->format("Y-m-d");
  $query.= "' AND date<='";
  $query.= $thisQuarter['end']->format("Y-m-d");
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Sales: 
<?php
  $query = "SELECT * FROM sale s, vehicle v WHERE salesperson_id=$sid AND vehicle_id=v.id AND date>='";
  $query.= $thisQuarter['start']->format("Y-m-d");
  $query.= "' AND date<='";
  $query.= $thisQuarter['end']->format("Y-m-d");
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  $todayRevenue = 0;
  $todayCOGS = 0;
  while($row = pg_fetch_assoc($res)) {
    // var_dump($row);
    $todayRevenue+= $row['sale_price'];
    $todayCOGS+= $row['invoice_price'];
  }
  setlocale(LC_MONETARY, 'en_US.UTF-8');
  echo pg_num_rows($res);
  // echo money_format("%.2n",$todayRevenue);
?>
                      </li>
                      <li>
                        Revenue Generated:
<?php 
  echo money_format("%.2n", $todayRevenue); 
?> 
                      </li>
                      <li>
<?php
  if($todayRevenue >= $todayCOGS) {
    echo "Profit Generated: <span style='color:green;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  } else {
    echo "Loss Generated: <span style='color:red;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  }
?> 
                      </li>
                    </ul>
                  </p>
                  <form
                    method='POST'
                    action='./detailedPerformanceReport.php'  
                  >
                    <input type='text' name='start' hidden
                     value='<?php echo $thisQuarter['start']->format("Y-m-d"); ?>'
                    > 
                    <input type='text' name='end' hidden
                     value='<?php echo $thisQuarter['end']->format("Y-m-d"); ?>'
                    > 
                    <input type='text' name='name' hidden
                      value='<?php echo $name; ?>'
                    > 
                    <input type='text' name='sid' hidden
                      value='<?php echo $sid; ?>'
                    > 
                    <button 
                      id='today'
                      type="submit" 
                      class="btn btn-primary ml-3"
                    >
                      <i class="fas fa-fw fa-file" aria-hidden="true"></i>
                      Generate Detailed PDF Report 
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Year to Date</h5>
                  <p class="card-text">
                    <ul>
                      <li>
                        Customer Visits: 
<?php
  $query = "SELECT * FROM visit WHERE salesperson_id=$sid AND date>='";
  $query.=date('o-m-d', strtotime("first day of this year"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("first day of next year"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Test Drives: 
<?php
  $query = "SELECT * FROM testdrive t, visit v WHERE salesperson_id=$sid AND visit_id=v.id AND date>'";
  $query.=date('o-m-d', strtotime("first day of this year"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("first day of next year"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Sales: 
<?php
  $query = "SELECT * FROM sale s, vehicle v WHERE salesperson_id=$sid AND vehicle_id=v.id AND date>='";
  $query.=date('o-m-d', strtotime("first day of this year"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("first day of next year"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  $todayRevenue = 0;
  $todayCOGS = 0;
  while($row = pg_fetch_assoc($res)) {
    // var_dump($row);
    $todayRevenue+= $row['sale_price'];
    $todayCOGS+= $row['invoice_price'];
  }
  setlocale(LC_MONETARY, 'en_US.UTF-8');
  echo pg_num_rows($res);
  // echo money_format("%.2n",$todayRevenue);
?>
                      </li>
                      <li>
                        Revenue Generated:
<?php 
  echo money_format("%.2n", $todayRevenue); 
?> 
                      </li>
                      <li>
<?php
  if($todayRevenue >= $todayCOGS) {
    echo "Profit Generated: <span style='color:green;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  } else {
    echo "Loss Generated: <span style='color:red;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  }
?> 
                      </li>
                    </ul>
                  </p>
                  <form
                    method='POST'
                    action='./detailedPerformanceReport.php'  
                  >
                    <input type='text' name='start' hidden
                     value='<?php echo date('o-m-d', strtotime("first day of this year")); ?>'
                    > 
                    <input type='text' name='end' hidden
                     value='<?php echo date('o-m-d', strtotime("first day of next year")); ?>'
                    > 
                    <input type='text' name='name' hidden
                      value='<?php echo $name; ?>'
                    > 
                    <input type='text' name='sid' hidden
                      value='<?php echo $sid; ?>'
                    > 
                    <button 
                      id='today'
                      type="submit" 
                      class="btn btn-primary ml-3"
                    >
                      <i class="fas fa-fw fa-file" aria-hidden="true"></i>
                      Generate Detailed PDF Report 
                    </button>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Last Year</h5>
                  <p class="card-text">
                    <ul>
                      <li>
                        Customer Visits: 
<?php
  $query = "SELECT * FROM visit WHERE salesperson_id=$sid AND date>='";
  $query.=date('o-m-d', strtotime("first day of last year"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("first day of this year"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Test Drives: 
<?php
  $query = "SELECT * FROM testdrive t, visit v WHERE salesperson_id=$sid AND visit_id=v.id AND date>'";
  $query.=date('o-m-d', strtotime("first day of last year"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("first day of this year"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  echo pg_num_rows($res);
?>
                      </li>
                      <li>
                        Sales: 
<?php
  $query = "SELECT * FROM sale s, vehicle v WHERE salesperson_id=$sid AND vehicle_id=v.id AND date>='";
  $query.=date('o-m-d', strtotime("first day of last year"));
  $query.= "' AND date<'";
  $query.=date('o-m-d', strtotime("first day of this year"));
  $query.= "'";
  // echo $query;
  $res = pg_query($db, $query);
  $todayRevenue = 0;
  $todayCOGS = 0;
  while($row = pg_fetch_assoc($res)) {
    // var_dump($row);
    $todayRevenue+= $row['sale_price'];
    $todayCOGS+= $row['invoice_price'];
  }
  setlocale(LC_MONETARY, 'en_US.UTF-8');
  echo pg_num_rows($res);
  // echo money_format("%.2n",$todayRevenue);
?>
                      </li>
                      <li>
                        Revenue Generated:
<?php 
  echo money_format("%.2n", $todayRevenue); 
?> 
                      </li>
                      <li>
<?php
  if($todayRevenue >= $todayCOGS) {
    echo "Profit Generated: <span style='color:green;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  } else {
    echo "Loss Generated: <span style='color:red;'>".money_format("%.2n", $todayRevenue-$todayCOGS)."</span>";
  }
?> 
                      </li>
                    </ul>
                  </p>
                  <form
                    method='POST'
                    action='./detailedPerformanceReport.php'  
                  >
                    <input type='text' name='start' hidden
                     value='<?php echo date('o-m-d', strtotime("first day of last year")); ?>'
                    > 
                    <input type='text' name='end' hidden
                     value='<?php echo date('o-m-d', strtotime("first day of this year")); ?>'
                    > 
                    <input type='text' name='name' hidden
                      value='<?php echo $name; ?>'
                    > 
                    <input type='text' name='sid' hidden
                      value='<?php echo $sid; ?>'
                    > 
                    <button 
                      id='today'
                      type="submit" 
                      class="btn btn-primary ml-3"
                    >
                      <i class="fas fa-fw fa-file" aria-hidden="true"></i>
                      Generate Detailed PDF Report 
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="customDateModal" tabindex="-1" role="dialog" aria-labelledby="customDateModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="customDateModalLabel">Select your custom date range...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id='customDateForm' method="POST" action="./detailedPerformanceReport.php">
          <div class="form-group">
            <label for="customStartDate" class="col-form-label">Starting Date:</label>
            <input type="date" class="form-control" id="customeStartDate" name='start'>
            <label for="customEndDate" class="col-form-label">Ending Date:</label>
            <input type="date" class="form-control" id="customeEndDate" name='end'>
            <input type='text' name='name' hidden
              value='<?php echo $name; ?>'
            > 
            <input type='text' name='sid' hidden
              value='<?php echo $sid; ?>'
            > 
          </div>
          <!-- <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div> -->
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="submitCustomDate">Generate Report</button>
      </div>
    </div>
  </div>
</div>

<?php } ?>
<?php require_once('footer.php'); ?>
<?php footerScripts(); ?>

<script>

$('#submitCustomDate').on('click', function (event) {
  $('#customDateForm').submit();
});

</script>