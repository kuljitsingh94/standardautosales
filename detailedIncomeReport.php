<?php
		require_once('./tcpdf/tcpdf.php');
		require_once('db.php'); 

		var_dump($_POST);

		$startingDate = $_POST['start'];
		$endingDate = $_POST['end'];

		echo $startingDate;
		echo $endingDate;

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
    $obj_pdf->Image('assets/images/logo.png','','',50,15,'','','T',false,300,'',false,false,1,false,false,false);  
    $content = '<br><h1 align="left">Standard Auto Sales</h1>';  
		$content.='<br><h2 align="right">Cars In</h2><br><br> ';
		$content.='<h5>Report Starting Date: '.$startingDate.'</h5>';
		$content.='<h5>Report Ending Date: '.$endingDate.'</h5>';
		$content.='<h5>Report Generation: '.date('F jS, o \a\t h:i A').'</h5><br>';
		$content.='<br><br><br>
    <table border="1" cellspacing="0" cellpadding="5">  
    <tr style="border:none; ">  
		<th>ID</th>
		<th>Vehicle</th>
		<th>Color</th>
		<th>VIN</th>
		<th>Invoice Price</th>
		<th>Odo Reading</th>
		<th>Date Received</th>
    </tr>  
		';
		$query = "SELECT * FROM vehicle, maker  WHERE code=maker_code AND date_received>='";
  	$query.=$startingDate."'";
  	$query.= " AND  date_received<'";
  	$query.=$endingDate."'";
  	echo $query;
  	$res = pg_query($db, $query);
		$carsInTotal = 0;	
		while($row = pg_fetch_assoc($res)) {
			$content.='<tr>';
			$content.='<td>'.$row['id'].'</td>';
			$content.='<td>'.$row['year'].' '.$row['make'].' '.$row['model'].'</td>';
			$content.='<td>'.$row['color'].'</td>';
			$content.='<td>'.$row['vin'].'</td>';
			setlocale(LC_MONETARY, 'en_US.UTF-8');
			$content.='<td>'.money_format("%.0n",$row['invoice_price']).'</td>';
			$carsInTotal+=$row['invoice_price'];
			$content.='<td>'.$row['odo_reading'].'</td>';
			$content.='<td>'.$row['date_received'].'</td>';
			$content.='</tr>';
		}
    // $content .= fetch_data();  
		$content.='<tr>';
		$content.='<td>Totals</td>';
		$content.='<td>'.pg_num_rows($res).'</td>';
		$content.='<td colspan="2"></td>';
		$content.='<td>'.money_format("%.0n",$carsInTotal).'</td>';
		$content.='</tr>';
		$content .= '</table>';  
    $obj_pdf->writeHTML($content);  
    $obj_pdf->AddPage();
    $obj_pdf->Image('assets/images/logo.png','','',50,15,'','','T',false,300,'',false,false,1,false,false,false);  
    $content = '<br><h1 align="left">Standard Auto Sales</h1>';  
		$content.='<br><h2 align="right">Income Report</h2><br><br> ';
		$content.='<h5>Report Starting Date: '.$startingDate.'</h5>';
		$content.='<h5>Report Ending Date: '.$endingDate.'</h5>';
		$content.='<h5>Report Generation: '.date('F jS, o \a\t h:i A').'</h5><br>';
		$content.='<br><br><br>
    <table border="1" cellspacing="0" cellpadding="5">  
    <tr style="border:none; ">  
		<th>Date</th>
		<th>Vehicle</th>
		<th>Sale Price</th>
		<th>Invoice Price</th>
		<th>Profit/Loss</th>
    </tr>  
		';
		$query = "SELECT * FROM vehicle v, maker, sale s  WHERE code=maker_code AND v.id=vehicle_id AND s.date>='";
  	$query.=$startingDate."'";
  	$query.= " AND  s.date<'";
  	$query.=$endingDate."' ORDER BY s.date";
  	echo $query;
		$res = pg_query($db, $query);
		$carsOutTotal = 0;
		$salePriceTotal = 0;	
		$invoicePriceTotal = 0;	
		$profitTotal = 0;	
		while($row = pg_fetch_assoc($res)) {
			$carsOutTotal++;
			$content.='<tr>';
			$content.='<td>'.$row['date'].'</td>';
			$content.='<td>'.$row['year'].' '.$row['make'].' '.$row['model'].'</td>';
			setlocale(LC_MONETARY, 'en_US.UTF-8');
			$content.='<td>'.money_format("%.2n",$row['sale_price']).'</td>';
			$salePriceTotal+=$row['sale_price'];
			setlocale(LC_MONETARY, 'en_US.UTF-8');
			$content.='<td>'.money_format("%.2n",$row['invoice_price']).'</td>';
			$invoicePriceTotal+=$row['invoice_price'];
			setlocale(LC_MONETARY, 'en_US.UTF-8');
			if($row['sale_price'] > $row['invoice_price']) {
				$content.='<td color="green">'.money_format("%.2n",$row['sale_price']-$row['invoice_price']).'</td>';
			} else {
				$content.='<td color="red">'.money_format("%.2n",$row['sale_price']-$row['invoice_price']).'</td>';
			}
			$profitTotal+=($row['sale_price'] - $row['invoice_price']);
			$content.='</tr>';
		}
    // $content .= fetch_data();  
		$content.='<tr>';
		$content.='<td>Totals</td>';
		$content.='<td>'.$carsOutTotal.'</td>';
		$content.='<td color="green">'.money_format("%.2n",$salePriceTotal).'</td>';
		$content.='<td color="red">'.money_format("%.2n",$invoicePriceTotal).'</td>';
		if($profitTotal >= 0) {
			$content.='<td color="green">'.money_format("%.2n",$profitTotal).'</td>';
		} else {
			$content.='<td color="red">'.money_format("%.2n",$profitTotal).'</td>';
		}
		$content.='</tr>';
		$content .= '</table>';  
    $obj_pdf->writeHTML($content);  
    ob_clean();
    $obj_pdf->Output('sample.pdf', 'I');  
?>