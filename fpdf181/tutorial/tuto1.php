<?php
require('../fpdf.php');

    $Shippername="Santy\n";
    $Shipperaddress="The text isn't too important, but it's too long too go on one line; plus, it has a few line breaks in it. ";
    $Shipperphone="9833563411";
    
    $pdf = new FPDF('L','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->MultiCell(140,15,$Shippername,1,2);  // Shipper Details
    $pdf->Cell(140,15,$Shippername,1,1);  // Shipper Details
    $pdf->Cell(280,30,$Shipperaddress,1,1);  // Shipper Details
    $pdf->Cell(280,30,$Shippername,1,1);  // Shipper Details
    $pdf->Cell(280,30,$Shippername,1,1);  // Shipper Details
    $pdf->Cell(280,30,$Shippername,1,1);  // Shipper Details
    
    
    $pdf->Output();
?>
