<?php
    
    
    /*
     #echo "Start  of Soap 1.1 version ( BasicHttpBinding) setting"
     $soap = new DebugSoapClient('http://netconnect.bluedart.com/Demo/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl',
     array(
     'trace' 							=> 1,
     'style'								=> SOAP_DOCUMENT,
     'use'									=> SOAP_LITERAL,
     'soap_version' 				=> SOAP_1_1
     ));
     
     $soap->__setLocation("http://netconnect.bluedart.com/Demo/ShippingAPI/Waybill/WayBillGeneration.svc/Basic");
     
     $soap->sendRequest = true;
     $soap->printRequest = false;
     $soap->formatXML = true;
     #echo "end of Soap 1.1 version setting"
     */
    
    #echo "Start  of Soap 1.2 version (ws_http_Binding)  setting";
				$soap = new DebugSoapClient('http://netconnect.bluedart.com/Demo/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl',
                                            array(
                                                  'trace' 							=> 1,
                                                  'style'								=> SOAP_DOCUMENT,
                                                  'use'									=> SOAP_LITERAL,
                                                  'soap_version' 				=> SOAP_1_2
                                                  ));
				
				$soap->__setLocation("http://netconnect.bluedart.com/Demo/ShippingAPI/Waybill/WayBillGeneration.svc");
				
				$soap->sendRequest = true;
				$soap->printRequest = false;
				$soap->formatXML = true;
				
				$actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IWayBillGeneration/GenerateWayBill',true);
				$soap->__setSoapHeaders($actionHeader);
    #echo "end of Soap 1.2 version (WSHttpBinding)  setting";
    
    $filename="";
    $filepath="";
    $status="";
    
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// Product Code /////////////////////////
    ////////////////////////////////////////////////////////////////
    
    if($service=="Domestic Priority"){
        $product='D';
        $subproduct='';
    }
    else if($service=="Ground"){
        $product='E';
        $subproduct='';
    }
    else if($service=="Apex"){
        $product='A';
        $subproduct='';
    }
    else if($service=="eTailCODAir"){
        $product='A';
        $subproduct='C';
    }
    else if($service=="eTailCODGround"){
        $product='E';
        $subproduct='C';
    }
    else if($service=="eTailPrePaidAir"){
        $product='A';
        $subproduct='P';
    }
    else if($service=="eTailPrePaidGround"){
        $product='E';
        $subproduct='P';
    }
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// COD  ///////////////////////////////
    ////////////////////////////////////////////////////////////////
    
    if($subproduct==''){
        $COD=0;
        $CustomerPay='false';
        $DBCOD="No";
    }
    else {
        $CustomerPay='true';
        $COD=$CollectableAmount;
        $DBCOD="Yes";
    }
    
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// Product Type  ////////////////////////
    ///////////////////////////////////////////////////////////////
    
    
    
    if($shipmentcontent=="Documents"){
        $producttype="Docs";
        $Commodity_desc="";
    }
    else if($shipmentcontent=="Commodities"){
        $producttype="Dutiables";
        
        
        
        /*for($i=0;$i<=$commodity_count;$i++){
            
            $Commodity_desc["CommodityDetail".$i]=$_POST["Commodity".$i];
            
        }*/
        
        for($i=0;$i<$commodity_count;$i++){
            
            $Commodity_desc["CommodityDetail".($i+1)]=$_POST["Commodity".$i];
            
        }
        
        
        $COD=0;
    }
    
    ///////////////////////////////////////////////////////////////
    ////////////////////////  Dimension  ///////////////////////////////
    ///////////////////////////////////////////////////////////////
    
    //$bd_dimension=array();
    
    //for($i=0;$i<packagecount;$i++){
    
    //  'Dimension' =>array ('Breadth' =>$breath,'Count' => '1','Height' => $height,'Length' => $length,)
    // }
    
    
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// Database Connection  /////////////////
    ////////////////////////////////////////////////////////////////
    
    
    try{
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt1 = $conn->prepare("SELECT * FROM `courier_vendor_details` where `account_number` ='$account_number'");
        $stmt1->execute();
        $stmt1->setFetchMode(PDO::FETCH_ASSOC);
        $row1 = $stmt1->fetch();
        
        
        $key= $row1['account_key'];
        $LoginID= $row1['login_id'];
        
        $stmt2 = $conn->prepare("SELECT * FROM `bluedart_service_avii1` WHERE `pincode`=$from_pin");
        $stmt2->execute();
        $stmt2->setFetchMode(PDO::FETCH_ASSOC);
        $row2 = $stmt2->fetch();
        
        $AreaCode=$row2['AreaCode'];
        

   
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// BlueDart Parameters  /////////////////
    ////////////////////////////////////////////////////////////////
    
    
    $params = array(
                    'Request' =>
                    array (
                           'Consignee' =>
                           array (
                                  'ConsigneeAddress1' => $receiver_info[3],
                                  'ConsigneeAddress2' => $receiver_info[4],
                                  'ConsigneeAddress3'=> $receiver_info[5],
                                  'ConsigneeAttention'=> $receiver_info[1],
                                  'ConsigneeMobile'=> $receiver_info[6],
                                  'ConsigneeName'=> $receiver_info[2],
                                  'ConsigneePincode'=> $receiver_info[0],
                                  'ConsigneeTelephone'=> $receiver_info[6],
                                  )	,
                           'Services' =>
                           array (
                                  'ActualWeight' => $TotalWeight,
                                  'CollectableAmount' => $COD,
                                  'Commodity' =>$Commodity_desc,
                                  'CreditReferenceNo' => $randUID,
                                  'DeclaredValue' => $InvoiceValue,
                                  'Dimensions' =>
                                  array (
                                         'Dimension' =>
                                         array (
                                                'Breadth' =>$breath,
                                                'Count' => $packagecount,
                                                'Height' => $height,
                                                'Length' => $length
                                                ),
                                         
                                         ),
                                  'InvoiceNo' => '',
                                  'PackType' => '',
                                  'PickupDate' => $shipment_date,
                                  'PickupTime' => '1800',
                                  'PieceCount' => $packagecount,
                                  'ProductCode' => $product,
                                  'ProductType' => $producttype,
                                  'SpecialInstruction' => '1',
                                  'SubProductCode' => $subproduct
                                  ),
                           'Shipper' =>
                           array(
                                 'CustomerAddress1' => $sender_info[3],
                                 'CustomerAddress2' => $sender_info[4],
                                 'CustomerAddress3' => $sender_info[5],
                                 'CustomerCode' => $LoginID,
                                 'CustomerEmailID' => 'a@b.com',
                                 'CustomerMobile' => $sender_info[6],
                                 'CustomerName' => $sender_info[2],
                                 'CustomerPincode' => $sender_info[0],
                                 'CustomerTelephone' => $sender_info[6],
                                 'IsToPayCustomer' => $CustomerPay,
                                 'OriginArea' => 'BOM',
                                 'Sender' => $sender_info[1],
                                 'VendorCode' => ''
                                 )
                           ),
                    'Profile' => 
                    array(
                          'Api_type' => 'S',
                          'LicenceKey'=>$key,
                          'LoginID'=>$account_number,
                          'Version'=>'1.3')
                    );
    
    #echo "<br>";
    #echo '<h2>Parameters</h2><pre>'; print_r($params); echo '</pre>';
    
    // Here I call my external function
    $result = $soap->__soapCall('GenerateWayBill',array($params));
    
    $response_status= $result->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation;
    
    if($response_status=="Waybill Generation Sucessful"){
        
        $token= $result->GenerateWayBillResult->AWBNo;
        
        $PackageDetails="";
        
        include_once("../../TCPDF/tcpdf.php");
        
        if (!class_exists('MYPDF')) {
            class MYPDF extends TCPDF {
                //Page header
                public function Header() {
                    $headerData = $this->getHeaderData();
                    $this->SetFont('helvetica','B', 10);
                    $this->writeHTML($headerData['string']);
                }
            }
        }
        
        

        
        
        
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

        
        if($shipmentcontent=="Commodities"){
            
            include("ProFormaInvoice.php");
        }
        
        
        /////////////////////////////////////////////////
        ///////////////Genrates AWB//////////////////////
        ///////////////////////////////////////////////////
        
        
        // set  header data
        $pdf->setHeaderData($ln='', $lw=0, $ht='', $hs='<table id="head" cellpadding="10" cellspacing="0" style="text-align:center;"><tr><td> Airway Bill</td></tr></table>', $tc=array(0,0,0), $lc=array(0,0,0));
        $pdf->AddPage();
        
        
        $pdf->write1DBarcode($token, 'C128', '', '', '', 18, 0.4, $style, 'N');
        
        
        $html1 ='<style>th{border:0.5px solid #C0C0C0;background-color:rgb(44,126,193); font-size: 9pt;text-align:center;color:#FFFFFF;font-weight:bold;}td{ vertical-align: middle;border:0.5px solid #C0C0C0;padding:65px;color:#000000;background-color:#FFFFFF;font-size: 8pt;text-align: left;cellpadding:10; }tr{cellpadding:"10";}</style>
        <table>
        <tbody>
        <tr nobr="true"><td>Sender Name: <b>'.$sender_name.'</b><br/>Address: '.$sender_address.','.$sender_city.'<br/>'.$sender_state.'-'.$from_pin .'<br/>Contact Number: '.$sender_phone.'</td><td>Receiver Name: <b>'.$receiver_name.'</b><br/>Address: '.$receiver_address.','.$receiver_city.'<br/>'.$receiver_state.'-'.$to_pin .'<br/>Contact Number: '.$receiver_phone.'</td></tr>
        <tr nobr="true"><td>Courier Vendor:'.$couriervendor.' <br/>Service: '.$service.'</td><td>Airway Bill Number: '.$token.'<br/></td></tr>
        <tr ><td>Pickup Date:________________<br/>Time: _____________________<br/> Emp. Code: ________________ <br/> Signature: __________________ <br/></td><td>Unique Number: '.$uid.'<br/>Package Count: '.$packagecount.'<br/> Total Weight: '.$TotalWeight.'<br/> Purpose of Shipment:'.$purpose.'</td></tr>
        <tr ><td colspan="2">Package Details: '.$shipmentcontent.'<br/>'.$PackageDetails.'</td></tr>
        </tbody>
        </table>';
        
        
        
        $pdf417="Sender Address:".$sender_name."\n".$sender_address.','.$sender_city.','.$sender_state.'-'.$from_pin ."\n Contact Number:".$sender_phone."\n Receiver Address:".$receiver_name."\n".$receiver_address.','.$receiver_city.','.$receiver_state.'-'.$to_pin ."\n Contact Number:".$receiver_phone."\nCourier Vendor:".$couriervendor." \n Service:".$service."\n Airway Bill Number:".$token."\n Unique Number:".$uid."\n Package Count:".$packagecount."\n Total Weight:".$TotalWeight."\n Purpose of Shipment:".$purpose."\n Package Details:".$shipmentcontent."\n".$PackageDetails;
        
        
        $pdf->writeHTML($html1, true, false, false, false,'');
        $pdf->Ln(2);
        $style['position'] = 'C';
        $pdf->write2DBarcode($pdf417, 'PDF417', 80, 90, 0, 30, $style, 'N');
        $pdf->lastPage();
        

        $filename=$token.".pdf";
        
        $filepath='AirwayBill/BlueDart/AirwayBill/'.$filename;
        
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/Courier/AirwayBill/BlueDart/AirwayBill/'.$filename, 'F'); // save in folder
        //ob_end_clean();
        
        
        $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`,`Airwaybill_Number`,`AWB_Status`, `AWB_Link`,`CreatedByUserID`,`CreatedByCompID`) VALUES ('$sender_info[1]','$receiver_info[1]','$DBCOD',$packagecount,'$uid','$shipment_date','BlueDart','$service','$token','Success','$filepath','$userid','$CompID')");
        $stmt5->execute();
        
        $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$DBCOD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='BlueDart' AND `AWB_Status` ='Success' order by `API_Hit_Date` DESC");
        $stmt6->execute();
        
        $stmt6->setFetchMode(PDO::FETCH_ASSOC);
        $row6 = $stmt6->fetch();
        
        $AWB_UID=$row6['UID'];
        
        
        
        $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Parties`(`Shipper_VendorID`,`Shipper_Name`, `Shipper_Comp`, `Shipper_Address`,`Shipper_City`,`Shipper_State`,`Shipper_Pincode`,`Shipper_Phone`,`Receiver_VendorID`, `Receiver_Name`, `Receiver_Comp`, `Receiver_Address`,`Receiver_City`,`Receiver_State`,`Receiver_Pincode`,`Receiver_Phone`,`AWB_UID`) VALUES ('$sender_vendorid','$sender_info[1]','$sender_info[2]','$sender_info[3]','$sender_info[4]','$sender_info[5]','$sender_info[0]','$sender_info[6]','$receiver_vendorid','$receiver_info[1]','$receiver_info[2]','$receiver_info[3]','$receiver_info[4]','$receiver_info[5]','$receiver_info[0]','$receiver_info[6]','$AWB_UID') ");
        
        $stmt7->execute();
        
      
            
            $stmt8=$conn->prepare("INSERT INTO `AirwayBill_Packages`(`AWB_UID`, `Package_Count`, `Length`, `Breath`, `Height`, `Weight`) VALUES ('$AWB_UID','1',$length,$breath,$height,$TotalWeight)");
            
            $stmt8->execute();
        
        
                $stmt9=$conn->prepare("INSERT INTO `AirwayBill_Commodites`(`AWB_UID`, `Commodity`, `Commodity_Desc`, `Value`) VALUES ('$AWB_UID','$Commodity','$Commodity_desc','$CommodityValue')");
                $stmt9->execute();
                
        
            
        
        
        $stmt10=$conn->prepare("INSERT INTO `pickup`(`AWB_Number`, `UID`, `Courier_Vendor`,`Pickup_Status`, `UserID`) VALUES ('$token','$AWB_UID','BlueDart','Pending','$userid')");
        $stmt10->execute();
        
        
        $status="Success";
        
        

    }
    else{
        
        $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`,`CreatedByUserID`,`CreatedByCompID`) VALUES ('$sender_info[1]','$receiver_info[1]','$DBCOD',$packagecount,'$uid','$shipment_date','BlueDart','$service','Fail','','$userid','$CompID')");
        $stmt5->execute();
        
        
        
        $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$DBCOD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='BlueDart' AND `AWB_Status` ='Fail' order by `API_Hit_Date` DESC");
        $stmt6->execute();
        
        $stmt6->setFetchMode(PDO::FETCH_ASSOC);
        $row6 = $stmt6->fetch();
        
        $AWB_UID=$row6['UID'];
        
        $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Error`(`AWB_UID`,`Failure_Type`, `Message`) VALUES ('$AWB_UID','Error','$response_status')");
        
        $stmt7->execute();
        
         $status="Error";
    }
    
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    
    
    //echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
    
    
    
