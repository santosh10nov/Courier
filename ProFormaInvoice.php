<?php

$PackageDetails="";
$Pro_formaPackageDetails="";
$total_units=0;
$total_weight=0;
$invoice_value=0;
$gross_weight=0;
$NetGross_Weight=0;

for($i=0;$i<$commodity_count;$i++){
    
    $total_value=$Commodity_desc["Com".$i]["CommodityValue".$i]*$Commodity_desc["Com".$i]["Commodity_quan".$i];
    $gross_weight=$Commodity_desc["Com".$i]["Commodity_weight".$i]*$Commodity_desc["Com".$i]["Commodity_quan".$i];
    $count=$i+1;
    
    $PackageDetails=$PackageDetails.$Commodity_desc["Com".$i]["Commodity".$i]."<br/>";
    $Pro_formaPackageDetails=$Pro_formaPackageDetails.'<tr><td>'.$count.'</td><td>'.$Commodity_desc["Com".$i]["Commodity_quan".$i].'</td><td>'.$Commodity_desc["Com".$i]["Commodity_weight".$i].'</td><td>KG</td><td colspan="3">'.$Commodity_desc["Com".$i]["Commodity".$i].": ".$Commodity_desc["Com".$i]["Commodity_desc".$i].'</td><td>IN</td><td>'.$Commodity_desc["Com".$i]["CommodityValue".$i].'</td><td>'.$total_value.'</td></tr>';
    $total_units= $total_units+$Commodity_desc["Com".$i]["Commodity_quan".$i];
    $total_weight=$total_weight+$Commodity_desc["Com".$i]["Commodity_weight".$i];
    $NetGross_Weight=$NetGross_Weight+$gross_weight;
    $invoice_value=$invoice_value+$total_value;
}
for($j=$commodity_count;$j<=15;$j++){
    
    $Pro_formaPackageDetails=$Pro_formaPackageDetails.'<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td colspan="3">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
}


// set  header data
$pdf->setHeaderData($ln='', $lw=0, $ht='', $hs='<table id="head" cellpadding="10" cellspacing="0" style="text-align:center;"><tr><td>Pro-Forma Invoice</td></tr></table>', $tc=array(0,0,0), $lc=array(0,0,0));

$pdf->AddPage();


$html ='<style>th{border:0.5px solid #C0C0C0;background-color:rgb(44,126,193); font-size: 9pt;text-align:center;color:#FFFFFF;font-weight:bold;}td{ vertical-align: middle;border:0.5px solid #C0C0C0;padding:65px;color:#000000;background-color:#FFFFFF;font-size: 8pt;text-align: left;cellpadding:10; }tr{cellpadding:"10";}</style>
<table>
<tbody>
<tr nobr="true"><td colspan="5">Sender Name: <b>'.$sender_name.'</b><br/>Address: '.$sender_address.','.$sender_city.'<br/>'.$sender_state.'-'.$from_pin .'<br/>Contact Number: '.$sender_phone.'</td><td colspan="5">Courier Vendor:'.$OtherCourierVendor.' <br/>Service: '.$OtherCourierService.'<br/>Airway Bill Number: '.$token.'<br/></td></tr>
<tr ><td colspan="5">Receiver Name: <b>'.$receiver_name.'</b><br/>Address: '.$receiver_address.','.$receiver_city.'<br/>'.$receiver_state.'-'.$to_pin .'<br/>Contact Number: '.$receiver_phone.'</td><td colspan="5">Unique Number: '.$uid.'<br/>Package Count: '.$packagecount.'<br/> Total Weight: '.$TotalWeight.'<br/> Purpose of Shipment:'.$purpose.'</td></tr>
<tr ><td colspan="10">Duties and Taxes Payable by: []Sender []Receiver  []Other<br/>If Other, please specify</td></tr>
<tr><td>No. of Package</td><td>No.of Units</td><td>Net Weight</td><td>Unit of Measure</td><td colspan="3">Description of Goods</td><td>Country of Manufacture</td><td>Unit Value</td><td>Total Value</td></tr>'.$Pro_formaPackageDetails.'
<tr><td>Total Package</td><td>Total Units</td><td>Total Weight</td><td>Total Gross Weight</td><td colspan="4" rowspan="2">Terms of Sale:</td><td>Invoice Total</td><td>'.$invoice_value.'</td></tr>
<tr><td>'.$count.'</td><td>'.$total_units.'</td><td>'.$total_weight.' KG</td><td>'.$NetGross_Weight.' KG</td><td>Currency </td><td>Indian Rupee</td></tr>
<tr><td colspan="10"><b>Special Instructions:<br/><br/></b></td></tr>
<tr><td colspan="10"><b>Decalartion Statement(s):<br/><br/></b></td></tr>
<tr><td colspan="10"><b>I decalare that all the information contain in this invoice to be true and correct.<br/></b></td></tr>
<tr><td colspan="10"><br/><br/><b>Signature_______________________ &nbsp;Date_________________</b></td></tr>
</tbody>
</table>';

$pdf->writeHTML($html, true, false, false, false,'');
$pdf->Ln(2);
    
    ?>

