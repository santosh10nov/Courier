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
    
    
    
    
    
    
    
    define('SHIP_LABEL1', 'bluedartshipexpresslabel.pdf');
    
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
    }
    else {
        $CustomerPay='true';
        $COD=$CollectableAmount;
    }
    
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// Product Type  ///////////////////////////////
    ///////////////////////////////////////////////////////////////
    
    
    
    if($shipmentcontent=="Documents"){
        $producttype="Docs";
        $Commodity_desc="";
    }
    else if($shipmentcontent=="Commodities"){
        $producttype="Dutiables";
        
        
        
        for($i=1;$i<($commodity_count+1);$i++){
            
            $Commodity_desc["CommodityDetail".$i]=$_POST["Commodity".$i];
            
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
        
        $stmt2 = $conn->prepare("SELECT * FROM `bluedart_service_avi` WHERE `pincode`=$from_pin");
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
                                  'ActualWeight' => $weight,
                                  'CollectableAmount' => $COD,
                                  'Commodity' =>$Commodity_desc,
                                  'CreditReferenceNo' => $uid,
                                  'DeclaredValue' => $cost,
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
        
        $filename=$token.".pdf";
        
        $fp = fopen("./AirwayBill/BlueDart/AirwayBill/".$filename, 'wb');
        fwrite($fp,$result->GenerateWayBillResult->AWBPrintContent); //Create PNG or PDF file
        fclose($fp);
    
        $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','BlueDart','$service','Success','$filename')");
        $stmt5->execute();
    
        $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='BlueDart' AND `AWB_Status` ='Success' order by `API_Hit_Date` DESC");
        $stmt6->execute();
    
        $stmt6->setFetchMode(PDO::FETCH_ASSOC);
        $row6 = $stmt6->fetch();
    
        $AWB_UID=$row6['UID'];
    
    
        $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Parties`(`Shipper_Name`, `Shipper_Comp`, `Shipper_Address`, `Receiver_Name`, `Receiver_Comp`, `Receiver_Address`,`AWB_UID`) VALUES ('$sender_info[1]','$sender_info[2]','$sender_info[3].$sender_info[4].$sender_info[5].$sender_info[0]','$receiver_info[1]','$receiver_info[2]','$receiver_info[3].$receiver_info[4].$receiver_info[5].$receiver_info[0]','$AWB_UID') ");
    
        $stmt7->execute();
        
        
        for($i=0;$i<$packagecount;$i++){
            
            $length=$Package["Dim".$i]["length".$i];
            $breath=$Package["Dim".$i]["breath".$i];
            $height=$Package["Dim".$i]["height".$i];
            $weight=$Package["Dim".$i]["weight".$i];
            
            
            $stmt8=$conn->prepare("INSERT INTO `AirwayBill_Packages`(`AWB_UID`, `Package_Count`, `Length`, `Breath`, `Height`, `Weight`) VALUES ('$AWB_UID','$i+1',$length,$breath,$height,$weight)");
            
            $stmt8->execute();
            
        }
        
        if($shipmentcontent=="Commodities"){
            for($i=0;$i<$commodity_count;$i++){
                
                $Commodity=$Package["Com".$i]["Commodity".$i];
                $Commodity_desc=$Package["Com".$i]["Commodity_desc".$i];
                $CommodityValue=$Package["Com".$i]["CommodityValue".$i];
                
                $stmt9=$conn->prepare("INSERT INTO `AirwayBill_Commodites`(`AWB_UID`, `Commodity`, `Commodity_Desc`, `Value`) VALUES ('$AWB_UID','$Commodity','$Commodity_desc','$CommodityValue')");
                $stmt9->execute();
                
            }
            
        }
        
        
         $status="Success";

    }
    else{
        
        $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','BlueDart','$service','Fail','')");
        $stmt5->execute();
        
        
        
        $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='BlueDart' AND `AWB_Status` ='Fail' order by `API_Hit_Date` DESC");
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
    
    
    
    
    
    class DebugSoapClient extends SoapClient {
        public $sendRequest = true;
        public $printRequest = true;
        public $formatXML = true;
        
        public function __doRequest($request, $location, $action, $version, $one_way=0) {
            if ( $this->printRequest ) {
                if ( !$this->formatXML ) {
                    $out = $request;
                }
                else {
                    $doc = new DOMDocument;
                    $doc->preserveWhiteSpace = false;
                    $doc->loadxml($request);
                    $doc->formatOutput = true;
                    $out = $doc->savexml();
                }
                echo $out;
            }
            
            if ( $this->sendRequest ) {
                return parent::__doRequest($request, $location, $action, $version, $one_way);
            }
            else {
                return '';
            }
        }
    }