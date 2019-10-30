<?php
		require_once('./tcpdf/tcpdf.php');
		require_once('db.php'); 
		
		
    $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
    $obj_pdf->SetCreator(PDF_CREATOR);  
    $obj_pdf->SetTitle("Employees");  
    $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
    $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
    $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
    $obj_pdf->SetDefaultMonospacedFont('helvetica');  
    $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
    $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
    $obj_pdf->setPrintHeader(false);  
    $obj_pdf->setPrintFooter(false);  
    $obj_pdf->SetAutoPageBreak(TRUE, 10);  
    $obj_pdf->SetFont('helvetica', '', 10);  
    $obj_pdf->AddPage();
    // $obj_pdf->Image('./assets/images/logo.png','','',50,15,'','','T',false,300,'',false,false,1,false,false,false);  
    $content = '';  
    $content .='<br><h1 align="right">Vehicle Inventory</h1><br><br> <p>Report Generation: '.date('F jS, o \a\t h:i A').'</p><br>';
    $content.='<br><br><br>
    <table border="1" cellspacing="0" cellpadding="5">  
    <tr style="border:none; ">  
		<th>ID</th>
		<th>Year</th>
		<th>Make</th>
		<th>Model</th>
		<th>Color</th>
		<th>VIN</th>
		<th>Invoice Price</th>
		<th>Odo Reading</th>
		<th>Date Received</th>
    </tr>  
		';
		if(isset($_GET['searchString']) && !empty($_GET['searchString'])) {
			$searchString = $_GET['searchString'];  
			$res = pg_query($db, 
				"SELECT * FROM vehicle, maker 
				WHERE maker_code=code 
				AND (
					make SIMILAR TO '%($searchString)%'
					OR model SIMILAR TO '%($searchString)%'
				)ORDER BY id asc");
		} else {
			$res = pg_query($db, 
				"SELECT * FROM vehicle, maker 
				WHERE maker_code=code 
				ORDER BY id asc");
		}
		while($row = pg_fetch_assoc($res)) {
			$content.='<tr>';
			$content.='<td>'.$row['id'].'</td>';
			$content.='<td>'.$row['year'].'</td>';
			$content.='<td>'.$row['make'].'</td>';
			$content.='<td>'.$row['model'].'</td>';
			$content.='<td>'.$row['color'].'</td>';
			$content.='<td>'.$row['vin'].'</td>';
			setlocale(LC_MONETARY, 'en_US.UTF-8');
			$content.='<td>'.money_format("%.0n",$row['invoice_price']).'</td>';
			$content.='<td>'.$row['odo_reading'].'</td>';
			$content.='<td>'.$row['date_received'].'</td>';
			$content.='</tr>';
		}
    // $content .= fetch_data();  
    $content .= '</table>';  
    $obj_pdf->writeHTML($content);  
    ob_clean();
    $obj_pdf->Output('sample.pdf', 'I');  
?>