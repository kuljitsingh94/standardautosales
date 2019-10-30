<?php require_once('db.php'); ?>
<?php require_once('header.php'); ?>
<?php head('Standard Auto Sales'); ?>
<?php require_once('sidebar.php');?>
<?php sidebar(1);?>

<script>var vehicles = [];</script>
<div class="container-fluid">

<?php 
if(isset($_GET['action']) && !empty($_GET['action'])) {
  if($_GET['result'] == "success") {
  ?>
    <div class="alert alert-success" role="alert">
    <i class="fas fa-fw fa-check"></i>
      Vehicle(s) successfully <?php echo $_GET['action'];?>ed.
    </div>
  <?php
  } elseif ($_GET['result'] == "failed") { ?> 
      <div class="alert alert-danger" role="alert">
      <i class="fas fa-fw fa-times"></i>
        <?php echo $_GET['e'] ? $_GET['e'] : "Vehicle ".$_GET['action']." failed.";?>
      </div> <?php
  } elseif ($_GET['result'] == 'partial') { ?>
      <div class="alert alert-warning" role="alert">
      <i class="fas fa-fw fa-times"></i>
        <?php 
          echo $_GET['sCount']." Vehicles added. ".$_GET['fCount']." Vehicles failed to add." 
        ?>
      </div> <?php
  } else {
  }
}
?>
  <div class='row'>  
    <div class='col md-12'>
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-tachometer-alt"></i>
          Vehicles
          <button 
            type="button" 
            class="btn btn-success ml-3"
            data-toggle="modal"
            data-target="#addVehicleModal" 
          >
            <i class="fas fa-fw fa-plus-square" aria-hidden="true"></i>
            Add Vehicle(s)
          </button>
          <button 
            id='generatePDF'
            type="button" 
            class="btn btn-primary ml-3"
          >
            <i class="fas fa-fw fa-file" aria-hidden="true"></i>
            Generate PDF Report 
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table 
              class="table table-bordered" 
              id="dataTable" 
              width="100%" 
              cellspacing="0"
              data-toggle="table"
              data-sort-name="ID"
              data-sort-order="desc"
            >
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Year</th>
                  <th>Make</th>
                  <th>Model</th>
                  <th>Color</th>
                  <th>VIN</th>
                  <th>Invoice Price</th>
                  <th>Odo Reading</th>
                  <th>Date Received</th>
                  <th>Update</th>
                  <!-- <th>Delete</th> -->
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>ID</th>
                  <th>Year</th>
                  <th>Make</th>
                  <th>Model</th>
                  <th>Color</th>
                  <th>VIN</th>
                  <th>Invoice Price</th>
                  <th>Odo Reading</th>
                  <th>Date Received</th>
                  <th>Update</th>
                  <!-- <th>Delete</th> -->
                </tr>
              </tfoot>
              <tbody>
                <?php 
                  $res = pg_query($db, "SELECT * FROM vehicle, maker WHERE maker_code=code");
                  while($row = pg_fetch_assoc($res)) {
                    echo '<script>vehicles['.$row['id'].'] = [';
                    echo ''.$row['year'].',';
                    echo '"'.$row['make'].'",';
                    echo '"'.$row['model'].'",';
                    echo '"'.$row['color'].'",';
                    echo '"'.$row['vin'].'",';
                    echo ''.$row['invoice_price'].',';
                    echo ''.$row['odo_reading'].',';
                    echo '"'.$row['date_received'].'"';
                    echo '];</script>';
//                    var_dump($row);
                    echo '<tr>';
                    echo '<td>'.$row['id'].'</td>';
                    echo '<td>'.$row['year'].'</td>';
                    echo '<td>'.$row['make'].'</td>';
                    echo '<td>'.$row['model'].'</td>';
                    echo '<td>'.$row['color'].'</td>';
                    echo '<td class="text-truncate">'.$row['vin'].'</td>';
                    setlocale(LC_MONETARY, 'en_US.UTF-8');
                    echo '<td>'.money_format("%.0n",$row['invoice_price']).'</td>';
                    echo '<td>'.$row['odo_reading'].'</td>';
                    echo '<td>'.$row['date_received'].'</td>';
                    echo '<td>
                      <button 
                        type="button" 
                        class="btn btn-primary ml-3"
                        data-toggle="modal"
                        data-target="#updateVehicleModal" 
                        data-whatever="'.$row['id'].'"
                      >
                        <i class="fas fa-fw fa-upload" aria-hidden="true"></i>
                      </button>
                    </td>';
                    // echo '<td>
                    //   <button 
                    //     type="button" 
                    //     class="btn btn-danger ml-3"
                    //     data-toggle="modal"
                    //     data-target="#deleteVehicleModal" 
                    //     data-whatever="'.$row['id'].'"
                    //   >
                    //     <i class="fas fa-fw fa-trash" aria-hidden="true"></i>
                    //   </button>
                    // </td>';
                    echo '</tr>';
                  }
                ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer small text-muted">
          <?php 
            echo "Updated on ".date('F jS, o \a\t h:i A');
          ?> 
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addVehicleModal" tabindex="-1" role="dialog" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <ul class="nav nav-tabs card-header-tabs">
          <li class="nav-item">
            <a class="nav-link active" id="addVehicleSingle" href="#">Single</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="addVehicleUpload" href="#">Upload</a>
          </li>
          <!-- <h5 class="modal-title" id="addVehicleModalLabel">Add Vehicle</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button> -->
        </ul>
      </div>
      <div class="modal-body">
        <form 
          id='addVehicleForm' 
          method="POST" 
          action="./addVehicle.php"
        >
          <div class="form-group" id="addVehicleArea">
            <input type='hidden' id='stockNumber' name='id' readonly>
            <label for="addVehicleYear" class="col-form-label">Year:</label>
            <input type="number" class="form-control" id="addVehicleYear" name="year">
            <label for="addVehicleMake" class="col-form-label">Make:</label>
            <input type="text" class="form-control" id="addVehicleMake" name='make'>
            <label for="addVehicleModel" class="col-form-label">Model:</label>
            <input type="text" class="form-control" id="addVehicleModel" name='model'>
            <label for="addVehicleColor" class="col-form-label">Color:</label>
            <input type="text" class="form-control" id="addVehicleColor" name='color'>
            <label for="addVehicleVIN" class="col-form-label">VIN:</label>
            <input type="text" class="form-control" id="addVehicleVIN" name='vin'>
            <label for="addVehiclePrice" class="col-form-label">Invoice Price:</label>
            <input type="number" class="form-control" id="addVehiclePrice" name='invoice_price'>
            <label for="addVehicleOdoReading" class="col-form-label">Odometer Reading:</label>
            <input type="number" class="form-control" id="addVehicleOdoReading" name='odo_reading'>
            <label for="addVehicleDate" class="col-form-label">Date Received:</label>
            <input type="date" class="form-control" id="addVehicleDate" name='date_received'>
          </div>
          <!-- <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div> -->
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="submitAddVehicle">Add</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="updateVehicleModal" tabindex="-1" role="dialog" aria-labelledby="updateVehicleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateVehicleModalLabel">Update Vehicle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id='updateVehicleForm' method="POST" action="./updateVehicle.php">
          <div class="form-group">
            <input type='hidden' id='stockNumber' name='id' readonly>
            <label for="updateVehicleYear" class="col-form-label">Year:</label>
            <input type="number" class="form-control" id="updateVehicleYear" name="year">
            <label for="updateVehicleMake" class="col-form-label">Make:</label>
            <input type="text" class="form-control" id="updateVehicleMake" name='make'>
            <label for="updateVehicleModel" class="col-form-label">Model:</label>
            <input type="text" class="form-control" id="updateVehicleModel" name='model'>
            <label for="updateVehicleColor" class="col-form-label">Color:</label>
            <input type="text" class="form-control" id="updateVehicleColor" name='color'>
            <label for="updateVehicleVIN" class="col-form-label">VIN:</label>
            <input type="text" class="form-control" id="updateVehicleVIN" name='vin'>
            <label for="updateVehiclePrice" class="col-form-label">Invoice Price:</label>
            <input type="number" class="form-control" id="updateVehiclePrice" name='invoice_price'>
            <label for="updateVehicleOdoReading" class="col-form-label">Odometer Reading:</label>
            <input type="number" class="form-control" id="updateVehicleOdoReading" name='odo_reading'>
            <label for="updateVehicleDate" class="col-form-label">Date Received:</label>
            <input type="date" class="form-control" id="updateVehicleDate" name='date_received'>
          </div>
          <!-- <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div> -->
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="submitUpdateVehicle">Update</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteVehicleModal" tabindex="-1" role="dialog" aria-labelledby="deleteVehicleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteVehicleModalLabel">DeleteVehicle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form 
          id='deleteVehicleForm' 
          method="POST" 
          action="./deleteVehicle.php"
          enctype='multipart/form-data'
        >
          <div class="form-group">
            <label for="deleteConfirmation" class="col-form-label">
              <p class="h3 text-center" id='deleteConfirmation'></p>
            </label>
            <input type='hidden' id='stockNumber' name='id' readonly>
            <label for="deleteVehicleYear" class="col-form-label">Year:</label>
            <input type="number" class="form-control" id="deleteVehicleYear" name="year" readonly>
            <label for="deleteVehicleMake" class="col-form-label">Make:</label>
            <input type="text" class="form-control" id="deleteVehicleMake" name='make' readonly>
            <label for="deleteVehicleModel" class="col-form-label">Model:</label>
            <input type="text" class="form-control" id="deleteVehicleModel" name='model' readonly>
            <label for="deleteVehicleColor" class="col-form-label">Color:</label>
            <input type="text" class="form-control" id="deleteVehicleColor" name='color' readonly>
            <label for="deleteVehicleVIN" class="col-form-label">VIN:</label>
            <input type="text" class="form-control" id="deleteVehicleVIN" name='vin' readonly>
            <label for="deleteVehiclePrice" class="col-form-label">Invoice Price:</label>
            <input type="number" class="form-control" id="deleteVehiclePrice" name='invoice_price' readonly>
            <label for="deleteVehicleOdoReading" class="col-form-label">Odometer Reading:</label>
            <input type="number" class="form-control" id="deleteVehicleOdoReading" name='odo_reading' readonly>
            <label for="deleteVehicleDate" class="col-form-label">Date Received:</label>
            <input type="date" class="form-control" id="deleteVehicleDate" name='date_received' readonly>
          </div>
          <!-- <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div> -->
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="submitDeleteVehicle">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- <script>console.log(vehicles);</script> -->
<?php require_once('footer.php'); ?>
<?php footerScripts(); ?>

<script>
$('#updateVehicleModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var id= button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  modal.find('.modal-title').text('Update Vehicle')
  modal.find('#stockNumber').val(id)
  modal.find('#updateVehicleYear').val(vehicles[id][0])
  modal.find('#updateVehicleMake').val(vehicles[id][1])
  modal.find('#updateVehicleModel').val(vehicles[id][2])
  modal.find('#updateVehicleColor').val(vehicles[id][3])
  modal.find('#updateVehicleVIN').val(vehicles[id][4])
  modal.find('#updateVehiclePrice').val(vehicles[id][5])
  modal.find('#updateVehicleOdoReading').val(vehicles[id][6])
  modal.find('#updateVehicleDate').val(vehicles[id][7])
})

$('#deleteVehicleModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var id= button.data('whatever') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  var modal = $(this)
  modal.find('.modal-title').text('Delete Vehicle')
  modal.find('#stockNumber').val(id)
  modal.find('#deleteConfirmation').text('Are you sure you want to delete Vehicle #'+id+'?')
  modal.find('#deleteVehicleYear').val(vehicles[id][0])
  modal.find('#deleteVehicleMake').val(vehicles[id][1])
  modal.find('#deleteVehicleModel').val(vehicles[id][2])
  modal.find('#deleteVehicleColor').val(vehicles[id][3])
  modal.find('#deleteVehicleVIN').val(vehicles[id][4])
  modal.find('#deleteVehiclePrice').val(vehicles[id][5])
  modal.find('#deleteVehicleOdoReading').val(vehicles[id][6])
  modal.find('#deleteVehicleDate').val(vehicles[id][7])
})

$('#generatePDF').on('click', function (event) {
  searchString=$('#dataTable_filter label input').val();
  console.log(searchString);
  window.location.href='./pdf.php?searchString='+searchString;
})
// onclick="location.href='./pdf.php'"

$('#submitUpdateVehicle').on('click', function (event) {
  $('#updateVehicleForm').submit();
})
$('#submitDeleteVehicle').on('click', function (event) {
  $('#deleteVehicleForm').submit();
})
$('#submitAddVehicle').on('click', function (event) {
  $('#addVehicleForm').submit();
})
$('#addVehicleSingle').on('click', function (event) {
  $('#addVehicleSingle').attr('class','nav-link active');
  $('#addVehicleUpload').attr('class','nav-link');
  $('#addVehicleForm').attr('enctype','');
  $('#addVehicleArea').html("\
<label for='addVehicleYear' class='col-form-label'>Year:</label>\
<input type='number' class='form-control' id='addVehicleYear' name='year'>\
<label for='addVehicleMake' class='col-form-label'>Make:</label>\
<input type='text' class='form-control' id='addVehicleMake' name='make'>\
<label for='addVehicleModel' class='col-form-label'>Model:</label>\
<input type='text' class='form-control' id='addVehicleModel' name='model'>\
<label for='addVehicleColor' class='col-form-label'>Color:</label>\
<input type='text' class='form-control' id='addVehicleColor' name='color'>\
<label for='addVehicleVIN' class='col-form-label'>VIN:</label>\
<input type='text' class='form-control' id='addVehicleVIN' name='vin'>\
<label for='addVehiclePrice' class='col-form-label'>Invoice Price:</label>\
<input type='number' class='form-control' id='addVehiclePrice' name='invoice_price'>\
<label for='addVehicleOdoReading' class='col-form-label'>Odometer Reading:</label>\
<input type='number' class='form-control' id='addVehicleOdoReading' name='odo_reading'>\
<label for='addVehicleDate' class='col-form-label'>Date Received:</label>\
<input type='date' class='form-control' id='addVehicleDate' name='date_received'>\
");
})
$('#addVehicleUpload').on('click', function (event) {
  $('#addVehicleSingle').attr('class','nav-link');
  $('#addVehicleMultiple').attr('class','nav-link');
  $('#addVehicleUpload').attr('class','nav-link active');
  $('#addVehicleForm').attr('enctype','multipart/form-data');
  $('#addVehicleArea').html("\
  <label for='addVehicleCSV' class='col-form-label'>Upload CSV file</label>\
  <input type='file' class='form-control-file' id='addVehicleCSV' name='addVehicleCSV'>\
  ");
})

</script>