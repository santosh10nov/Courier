<?php 

require('fpdf/fpdf.php');
include('fpdf/php-barcode.php');
    //require('fpdf/code128.php');

class PDF_reciept extends FPDF {
	function __construct ($orientation = 'P', $unit = 'pt', $format = 'Letter', $margin = 40) {
		$this->FPDF($orientation, $unit, $format);
		$this->SetTopMargin($margin);
		$this->SetLeftMargin($margin);
		$this->SetRightMargin($margin);
		
		$this->SetAutoPageBreak(true, $margin);
	}
	
	function Header () {
		$this->SetFont('Arial', 'B', 20);
		$this->SetFillColor(36, 96, 84);
		$this->SetTextColor(225);
		$this->Cell(0, 30, "AirWayBill", 0, 1, 'C', true);
	}
	
	function Footer () {
		$this->SetFont('Arial', '', 12);
		$this->SetTextColor(0);
		$this->SetXY(40, -60);
		$this->Cell(0, 20, "Thank you for shopping at Nettuts+", 'T', 0, 'C');
	}
	
    function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle=0)
    {
        $font_angle+=90+$txt_angle;
        $txt_angle*=M_PI/180;
        $font_angle*=M_PI/180;
        
        $txt_dx=cos($txt_angle);
        $txt_dy=sin($txt_angle);
        $font_dx=cos($font_angle);
        $font_dy=sin($font_angle);
        
        $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',$txt_dx,$txt_dy,$font_dx,$font_dy,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
        if ($this->ColorFlag)
            $s='q '.$this->TextColor.' '.$s.' Q';
        $this->_out($s);
    }
    
    
    
}
 
    
    $Shippername="Santy";
    $Shipperaddress="The text isn't too important, but it's too long too go on one line; plus, it has a few line breaks in it. ";
    $Shipperphone="9833563411";

    $pdf = new PDF_reciept();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetY(100);

    $pdf->Cell(250,50,"Courier Vendor:".$couriervendor,1,0);  // Shipper Details
    $pdf->Cell(280,50,"Courier Service:".$service,1,1);  // Shipper Details
    
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->SetFont('Times');
    $pdf->MultiCell(250, 24, "From:".$sender_name."\nAddress:".$sender_address.",".$sender_city.",".$sender_state."-".$from_pin."\n Contact Number:".$sender_phone, 1, 1);
    $y1 = $pdf->GetY();
    $height=$y1-$y;
    $pdf->SetXY($x + 250, $y);
    $pdf->MultiCell(280, 30, "To:".$receiver_name."\nAddress:".$receiver_address."\n Contact Number:".$receiver_phone, 1,1);
   
    
    
    
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    
    
    
    // -------------------------------------------------- //
    //                  PROPERTIES
    // -------------------------------------------------- //
    
    $fontSize = 20;
    $marge    = 5;   // between barcode and hri in pixel
    $x        = $x+250;  // barcode center
    $y        = $y+40;  // barcode center
    $height   = 30;   // barcode height in 1D ; module size in 2D
    $width    = 2;    // barcode height in 1D ; not use in 2D
    $angle    = 0;   // rotation in degrees
    
    $code     = 'ABC456789012'; // barcode, of course ;)
    $type     = 'code128';
    $black    = '000000'; // color in hexa
    
    
    // -------------------------------------------------- //
    //                      BARCODE
    // -------------------------------------------------- //
    
    $data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$code), $width, $height);
    
    // -------------------------------------------------- //
    //                      HRI
    // -------------------------------------------------- //
    
    $pdf->SetFont('Arial','B',$fontSize);
    $pdf->SetTextColor(0, 0, 0);
    $len = $pdf->GetStringWidth($data['hri']);
    Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
    $pdf->Cell(530,100,$pdf->TextWithRotation($x + $xt, $y + $yt, $data['hri'], $angle),1,2);
    
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->SetFont('Times','B',10);
    $pdf->MultiCell(200,20,"Pickup Date:\nTime:\n Emp. Code: \n Sign:",1,1);
    $pdf->SetXY($x + 200, $y);
    $pdf->MultiCell(150, 27, "Unique Number: ".$y."\n Package Count: ".$y."\n Total Weight: ".$y."KG", 1,1);
    $pdf->SetXY($x + 350, $y);
    $pdf->MultiCell(180, 27, "To- Pay Account: ".$y."\n Invoice Value: Rs".$y."\n Collectable Amount: Rs".$y, 1,1);
    
    $pdf->Cell(530,40,"Commodity",1,2);
    $pdf->Cell(530,40,"Purpose of Shipment",1,2);
    $pdf->Cell(530,60,"Note",1,2);
    
    
    $pdf->Output('reciept.pdf', 'F');
    
    
    
    
    ?>




