<?php
// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 12.0.0

require_once('fedex-common.php5');

//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = "/Applications/XAMPP/xamppfiles/htdocs/Courier/AirwayBill/FedEx/ShipService_v17.wsdl";
    

define('SHIP_LABEL', 'fedexshipexpresslabel.pdf');  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. shiplabel.pdf)
define('SHIP_CODLABEL', 'CODexpressreturnlabel.pdf');  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. CODexpressreturnlabel.pdf)

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

 
    
    try{
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt1 = $conn->prepare("Select * from fedex_service_avi where `pincode`=$from_pin");
        $stmt1->execute();
        $stmt1->setFetchMode(PDO::FETCH_ASSOC);
        $row1 = $stmt1->fetch();
        
        $stmt2 = $conn->prepare("Select * from fedex_service_avi where `pincode`=$to_pin");
        $stmt2->execute();
        $stmt2->setFetchMode(PDO::FETCH_ASSOC);
        $row2 = $stmt2->fetch();
        
        $ShipperStateOrProvinceCode=$row1['p_DestinationAirportId'];
        $ReceiverStateOrProvinceCode= $row2['p_DestinationAirportId'];

        
            $request['WebAuthenticationDetail'] = array(
                                                        'ParentCredential' => array(
                                                                                    'Key' => getProperty('parentkey'),
                                                                                    'Password' => getProperty('parentpassword')
                                                                                    ),
                                                        'UserCredential' => array(
                                                                                  'Key' => getProperty('key'),
                                                                                  'Password' => getProperty('password')
                                                                                  )
                                                        );
            
            $request['ClientDetail'] = array(
                                             'AccountNumber' => getProperty('shipaccount'),
                                             'MeterNumber' => getProperty('meter')
                                             );
            $request['TransactionDetail'] = array('CustomerTransactionId' => '*** Express Domestic Shipping Request using PHP ***');
            $request['Version'] = array(
                                        'ServiceId' => 'ship',
                                        'Major' => '17',
                                        'Intermediate' => '0',
                                        'Minor' => '0'
                                        );
            
            
            $request['RequestedShipment'] = array(
                                                  'ShipTimestamp' => date('c'),
                                                  'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
                                                  'ServiceType' => $service, // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
                                                  'PackagingType' => 'YOUR_PACKAGING', // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
                                                  'TotalWeight' => array(
                                                                         'Value' => $weight,
                                                                         'Units' => 'LB' // valid values LB and KG
                                                                         ), 
                                                  'Shipper' => addShipper($sender_info,$ShipperStateOrProvinceCode),
                                                  'Recipient' => addRecipient($receiver_info,$ReceiverStateOrProvinceCode),
                                                  'ShippingChargesPayment' => addShippingChargesPayment(),
                                                  //'SpecialServicesRequested' => addSpecialServices(),
                                                  'CustomsClearanceDetail'=>addCustomsClearanceDetail($package_details),
                                                  'LabelSpecification' => addLabelSpecification(), 
                                                  'PackageCount' => 1,
                                                  'RequestedPackageLineItems' => array(
                                                                                       '0' => addPackageLineItem1($package_details)
                                                                                       )
                                                  );
            
            try {
                if(setEndpoint('changeEndpoint')){
                    $newLocation = $client->__setLocation(setEndpoint('endpoint'));
                }
                
                $response = $client->processShipment($request);  // FedEx web service invocation
                
                if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR'){
                    //printSuccess($client, $response);
                    
                    //$fp = fopen(SHIP_CODLABEL, 'wb');
                    //fwrite($fp, $response->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image); //Create COD Return PNG or PDF file
                    //fclose($fp);
                    //echo '<a href="./'.SHIP_CODLABEL.'">'.SHIP_CODLABEL.'</a> was generated.'.Newline;
                    
                    // Create PNG or PDF label
                    // Set LabelSpecification.ImageType to 'PDF' or 'PNG for generating a PDF or a PNG label
                    
                    $token= $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
                    
                    $filename=$token.".pdf";

                    
                    
                    $fp = fopen("./AirwayBill/FedEx/AirwayBill/".$filename, 'wb');
                    fwrite($fp, $response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image); //Create PNG or PDF file
                    fclose($fp);
                   
                    $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','FedEx','$service','Success','$filename')");
                    $stmt5->execute();
                    
                    $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='FedEx' AND `AWB_Status` ='Success' order by `API_Hit_Date` DESC");
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
                    
                    
                }else{
                    //printError($client, $response);
                    
                    $message=json_encode($response->Notifications);
                    
                    
                    $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','FedEx','$service','Fail','')");
                    $stmt5->execute();
                    
                    
                    
                    $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='FedEx' AND `AWB_Status` ='Fail' order by `API_Hit_Date` DESC");
                    $stmt6->execute();
                    
                    $stmt6->setFetchMode(PDO::FETCH_ASSOC);
                    $row6 = $stmt6->fetch();
                    
                    $AWB_UID=$row6['UID'];
                    
                    $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Error`(`AWB_UID`,`Failure_Type`, `Message`) VALUES ('$AWB_UID','Error','$message')");
                    
                    $stmt7->execute();
                    
                     $status="Error";
                    
                }
                
                writeToLog($client);    // Write to log file
            } catch (SoapFault $exception) {
                
                
                $message=json_encode($exception->detail);
                $message = str_replace("'", ' ', $message);
                
                $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','FedEx','$service','Fail','')");
                $stmt5->execute();
                
                
                
                $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='FedEx' AND `AWB_Status` ='Fail' order by `API_Hit_Date` DESC");
                $stmt6->execute();
                
                $stmt6->setFetchMode(PDO::FETCH_ASSOC);
                $row6 = $stmt6->fetch();
                
                $AWB_UID=$row6['UID'];
        
                $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Error`(`AWB_UID`,`Failure_Type`, `Message`) VALUES ('$AWB_UID','FAULT','$message')");
                
                $stmt7->execute();
                
                 $status="Error";
            }
            
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
 

   
function addShipper($arr,$code){
	$shipper = array(
		'Contact' => array(
			'PersonName' => $arr[1] ,
			'CompanyName' => $arr[2],
			'PhoneNumber' => $arr[6]
		),
		'Address' => array(
			'StreetLines' => array($arr[3]),
			'City' => $arr[4],
			'StateOrProvinceCode' => $code,
			'PostalCode' =>$arr[0] ,
			'CountryCode' => 'IN'
		)
	);
	return $shipper;
}
function addRecipient($arr,$code){
	$recipient = array(
		'Contact' => array(
			'PersonName' =>$arr[1] ,
			'CompanyName' => $arr[2] ,
			'PhoneNumber' =>$arr[6]
		),
		'Address' => array(
			'StreetLines' => array($arr[3]),
			'City' => $arr[4],
			'StateOrProvinceCode' =>$code ,
			'PostalCode' => $arr[0],
			'CountryCode' => 'IN',
			'Residential' => true
		)
	);
	return $recipient;	                                    
}
    
function addShippingChargesPayment(){
	$shippingChargesPayment = array('PaymentType' => 'SENDER',
        'Payor' => array(
		'ResponsibleParty' => array(
			'AccountNumber' => getProperty('billaccount'),
			'Contact' => null,
			'Address' => array(
				'CountryCode' => 'IN')
			)
		)
	);
	return $shippingChargesPayment;
}
    
function addLabelSpecification(){
	$labelSpecification = array(
		'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
		'ImageType' => 'PDF',  // valid values DPL, EPL2, PDF, ZPLII and PNG
		'LabelStockType' => 'PAPER_7X4.75'
	);
	return $labelSpecification;
}
    
function addSpecialServices(){
	$specialServices = array(
		'SpecialServiceTypes' => array('COD'),
		'CodDetail' => array(
			'CodCollectionAmount' => array(
				'Currency' => 'INR',
				'Amount' => 150
			),
			'CollectionType' => 'GUARANTEED_FUNDS' // ANY, GUARANTEED_FUNDS
		)
	);
	return $specialServices; 
}
    
function addPackageLineItem1($arr){
	$packageLineItem = array(
		'SequenceNumber'=>1,
		'GroupPackageCount'=>1,
		'Weight' => array(
			'Value' => $arr[0],
			'Units' => 'LB'
		),
		'Dimensions' => array(
			'Length' => $arr[1],
			'Width' => $arr[2],
			'Height' => $arr[3],
			'Units' => 'IN'
		)
	);
	return $packageLineItem;
}
    
    function addCustomsClearanceDetail($arr){
        $try=array(
                   'DocumentContent'=>'DOCUMENTS_ONLY',
                   'CommercialInvoice'=>array(
                                            'Purpose'=>$arr[4]
                   ),
                   'CustomsValue'=>array(
                                        'Currency'=>'INR',
                                         'Amount'=>$arr[5]
                   ),
                   'Commodities'=> array(
                                         'Name'=>'BOOKS',
                                         'NumberOfPieces'=>1,
                                         'Description'=>'Books',
                                         'CountryOfManufacture'=>'IN',
                                         'Weight'=>array(
                                                        'Units'=>'LB',
                                                         'Value'=>$arr[0]
                                         ),
                                         'Quantity'=>1,
                                         'QuantityUnits'=>'EA',
                                         'UnitPrice'=>array(
                                                            'Currency'=>'INR',
                                                            'Amount'=>$arr[5]
                                         ),
                                         'CustomsValue'=>array(
                                                               'Currency'=>'INR',
                                                              'Amount'=>$arr[5]
                                         )
                                         
                   )
                  
        
        );
        return $try;
    }
?>