<?php
    
    include("TCPDF/tcpdf.php");
    
    class MYPDF extends TCPDF {
        //Page header
        public function Header() {
            $headerData = $this->getHeaderData();
            $this->SetFont('helvetica','B', 10);
            $this->writeHTML($headerData['string']);
        }
    }
    
    
    $PackageDetails="";
    
    $sender_name="Santosh Yadav";
    $sender_address="edwsfwds fdwsfwdss";
    $sender_city="Mumbai";
    $sender_state="Maharastra";
    $from_pin=400069;
    $sender_phone="9833563411";
    $receiver_name="P.H Yadav";
    $receiver_address="dasdad ddfsd  dwfdfsd dfsdfds ";
    $receiver_city="Kota";
    $receiver_state="Rajasthan";
    $to_pin="324001";
    $receiver_phone="9928061807";
    $OtherCourierVendor="TrackOn";
    $OtherCourierService="TrackOn";
    $OtherCourierAWB="3421321312312";
    $uid="198";
    $packagecount=2;
    $TotalWeight=10;
    $purpose="Gift";
    $shipmentcontent="Commodity";
    
    
    $style = array(
                   'position' => '',
                   'align' => 'C',
                   'stretch' => false,
                   'fitwidth' => true,
                   'cellfitalign' => '',
                   'border' => true,
                   'hpadding' => 'auto',
                   'vpadding' => 'auto',
                   'fgcolor' => array(0,0,0),
                   'bgcolor' => false, //array(255,255,255),
                   'text' => true,
                   'font' => 'helvetica',
                   'fontsize' => 8,
                   'stretchtext' => 4
                   );
    
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8", false); //Default for UTF-8 unicode
    //$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, ‘ISO-8859-1′, false); // set unicode to ISO-8859-1 so special chars like æ, ø, å will work.
    $pdf->SetCreator(PDF_CREATOR);
    
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA,'', PDF_FONT_SIZE_DATA));
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    // set document information
    
    /////////////////////////////////////////////////
    ///////////////Pro-Forma Invoice/////////////////
    ///////////////////////////////////////////////////
    
    $PackageDetails="";
    $Pro_formaPackageDetails="";
    $total_units=0;
    $total_weight=0;
    $invoice_value=0;
    
    if($shipmentcontent=="Commodities"){
        
        for($i=0;$i<$commodity_count;$i++){
            
            $total_value=$Commodity_desc["Com".$i]["CommodityValue".$i]*$Commodity_desc["Com".$i]["Commodity_quan".$i];
            
            $PackageDetails=$PackageDetails.$Commodity_desc["Com".$i]["Commodity".$i]."<br/>";
            $Pro_formaPackageDetails=$Pro_formaPackageDetails.'<tr><td>'.$i.'</td><td>'.$Commodity_desc["Com".$i]["Commodity_quan".$i].'</td><td>'.$Commodity_desc["Com".$i]["Commodity_weight".$i].'</td><td>KG</td><td colspan="3">'.$Commodity_desc["Com".$i]["Commodity".$i].": ".$Commodity_desc["Com".$i]["Commodity_desc".$i].'</td><td>IN</td><td>'.$Commodity_desc["Com".$i]["CommodityValue".$i].'</td><td>'.$total_value.'</td></tr>';
            $total_units= $total_units+$Commodity_desc["Com".$i]["Commodity_quan".$i];
            $total_weight=$total_weight+$Commodity_desc["Com".$i]["Commodity_weight".$i];
            $invoice_value=$invoice_value+$total_value;
        }
        for($j=$commodity_count;$j<=10;j++){
            
            $Pro_formaPackageDetails=$Pro_formaPackageDetails.'<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
        }
        
        
        // set  header data
        $pdf->setHeaderData($ln='', $lw=0, $ht='', $hs='<table id="head" cellpadding="10" cellspacing="0" style="text-align:center;"><tr><td>Pro-Forma Invoice</td></tr></table>', $tc=array(0,0,0), $lc=array(0,0,0));
        
        $pdf->AddPage();
        
        
        $html ='<style>th{border:0.5px solid #C0C0C0;background-color:rgb(44,126,193); font-size: 9pt;text-align:center;color:#FFFFFF;font-weight:bold;}td{ vertical-align: middle;border:0.5px solid #C0C0C0;padding:65px;color:#000000;background-color:#FFFFFF;font-size: 8pt;text-align: left;cellpadding:10; }tr{cellpadding:"10";}</style>
        <table>
        <tbody>
        <tr nobr="true"><td colspan="5">Sender Name: <b>'.$sender_name.'</b><br/>Address: '.$sender_address.','.$sender_city.'<br/>'.$sender_state.'-'.$from_pin .'<br/>Contact Number: '.$sender_phone.'</td><td colspan="5">Courier Vendor:'.$OtherCourierVendor.' <br/>Service: '.$OtherCourierService.'<br/>Airway Bill Number: '.$OtherCourierAWB.'<br/></td></tr>
        <tr ><td colspan="5">Receiver Name: <b>'.$receiver_name.'</b><br/>Address: '.$receiver_address.','.$receiver_city.'<br/>'.$receiver_state.'-'.$to_pin .'<br/>Contact Number: '.$receiver_phone.'</td><td colspan="5">Unique Number: '.$uid.'<br/>Package Count: '.$packagecount.'<br/> Total Weight: '.$TotalWeight.'<br/> Purpose of Shipment:'.$purpose.'</td></tr>
        <tr ><td colspan="10">Duties and Taxes Payable by: []Sender []Receiver  []Other<br/>If Other, please specify</td></tr>
        <tr><td>No. of Package</td><td>No.of Units</td><td>Net Weight</td><td>Unit of Measure</td><td colspan="3">Description of Goods</td><td>Country of Manufacture</td><td>Unit Value</td><td>Total Value</td></tr>
        <tr><td>1</td><td>3</td><td>0.50</td><td>KG</td><td colspan="3">Customer Handsets</td><td>IN</td><td>4,9000</td><td>4,9000</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>Total Package</td><td>Total Units</td><td>Total Weight</td><td>Total Gross Weight</td><td colspan="4" rowspan="2">Terms of Sale:</td><td>Invoice Total</td><td>'.$invoice_value.'</td></tr>
        <tr><td>1</td><td>'.$total_units.'</td><td>'.$total_weight.' KG</td><td>'.$total_weight*$total_units.' KG</td><td>Currency </td><td>Indian Rupee</td></tr>
        <tr><td colspan="10"><b>Special Instructions:<br/><br/></b></td></tr>
        <tr><td colspan="10"><b>Decalartion Statement(s):<br/><br/></b></td></tr>
        <tr><td colspan="10"><b>I decalare that all the information contain in this invoice to be true and correct.<br/></b></td></tr>
        <tr><td colspan="10"><br/><br/><b>Signature_______________________ &nbsp;Date_________________</b></td></tr>
        </tbody>
        </table>';
        
        $pdf->writeHTML($html, true, false, false, false,'');
        $pdf->Ln(2);
    }

    
    
    
    /////////////////////////////////////////////////
    ///////////////Genrates AWB//////////////////////
    ///////////////////////////////////////////////////
    
    
    // set  header data
    $pdf->setHeaderData($ln='', $lw=0, $ht='', $hs='<table id="head" cellpadding="10" cellspacing="0" style="text-align:center;"><tr><td> Airway Bill</td></tr></table>', $tc=array(0,0,0), $lc=array(0,0,0));
    $pdf->AddPage();
    
    
    $pdf->write1DBarcode($OtherCourierAWB, 'C128', '', '', '', 18, 0.4, $style, 'N');
    
    
    $html1 ='<style>th{border:0.5px solid #C0C0C0;background-color:rgb(44,126,193); font-size: 9pt;text-align:center;color:#FFFFFF;font-weight:bold;}td{ vertical-align: middle;border:0.5px solid #C0C0C0;padding:65px;color:#000000;background-color:#FFFFFF;font-size: 8pt;text-align: left;cellpadding:10; }tr{cellpadding:"10";}</style>
    <table>
    <tbody>
    <tr nobr="true"><td>Sender Name: <b>'.$sender_name.'</b><br/>Address: '.$sender_address.','.$sender_city.'<br/>'.$sender_state.'-'.$from_pin .'<br/>Contact Number: '.$sender_phone.'</td><td>Receiver Name: <b>'.$receiver_name.'</b><br/>Address: '.$receiver_address.','.$receiver_city.'<br/>'.$receiver_state.'-'.$to_pin .'<br/>Contact Number: '.$receiver_phone.'</td></tr>
    <tr nobr="true"><td>Courier Vendor:'.$OtherCourierVendor.' <br/>Service: '.$OtherCourierService.'</td><td>Airway Bill Number: '.$OtherCourierAWB.'<br/></td></tr>
    <tr ><td>Pickup Date:________________<br/>Time: _____________________<br/> Emp. Code: ________________ <br/> Signature: __________________ <br/></td><td>Unique Number: '.$uid.'<br/>Package Count: '.$packagecount.'<br/> Total Weight: '.$TotalWeight.'<br/> Purpose of Shipment:'.$purpose.'</td></tr>
    <tr ><td colspan="2">Package Details: '.$shipmentcontent.'<br/>'.$PackageDetails.'</td></tr>
    </tbody>
    </table>';
    
    
    
    $pdf417="Sender Address:".$sender_name."\n".$sender_address.','.$sender_city.','.$sender_state.'-'.$from_pin ."\n Contact Number:".$sender_phone."\n Receiver Address:".$receiver_name."\n".$receiver_address.','.$receiver_city.','.$receiver_state.'-'.$to_pin ."\n Contact Number:".$receiver_phone."\nCourier Vendor:".$OtherCourierVendor." \n Service:".$OtherCourierService."\n Airway Bill Number:".$OtherCourierAWB."\n Unique Number:".$uid."\n Package Count:".$packagecount."\n Total Weight:".$TotalWeight."\n Purpose of Shipment:".$purpose."\n Package Details:".$shipmentcontent."\n".$PackageDetails;
    
    
    $pdf->writeHTML($html1, true, false, false, false,'');
    $pdf->Ln(2);
    $style['position'] = 'C';
    $pdf->write2DBarcode($pdf417, 'PDF417', 80, 90, 0, 30, $style, 'N');
    $pdf->lastPage();
    
    $fname = $OtherCourierAWB.'_AWB.pdf'; // filename test
    $pdf->output('Other.pdf', 'I'); // pdf view
    //$pdf->Output($_SERVER['DOCUMENT_ROOT'].'/Courier/AirwayBill/NonAPI/'.$fname, 'F'); // save in pdf folder
    
    
    
    ?>
