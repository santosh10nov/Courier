<?php
    // Copyright 2009, FedEx Corporation. All rights reserved.
    // Version 12.0.0
    
    require_once('/Applications/XAMPP/xamppfiles/htdocs/Courier/fedex-common.php5');
    
    
    //The WSDL is not included with the sample code.
    //Please include and reference in $path_to_wsdl variable.
    $path_to_wsdl = "/Applications/XAMPP/xamppfiles/htdocs/Courier/AirwayBill/FedEx/ShipService_v17.wsdl";
    
    
    ini_set("soap.wsdl_cache_enabled", "0");
    
    $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
    
    if($shipmentcontent=="Documents"){
        
        $DocumentContent='DOCUMENTS_ONLY';
    }
    else{
        $DocumentContent='NON_DOCUMENTS';
    }
    
    $filename="";
    $filepath="";
    
    try{
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt=$conn->prepare("Select * from courier_vendor_details where account_number=$account_number and name='FedEx'");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();
        
        $Key=$row['account_key'];
        $Password=$row['password'];
        $Meter=$row['login_id'];
        
        
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
    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    
    
    $request['WebAuthenticationDetail'] = array(
                                                'ParentCredential' => array(
                                                                            'Key' =>$Key,
                                                                            'Password' =>$Password
                                                                            ),
                                                'UserCredential' => array(
                                                                          'Key' =>$Key,
                                                                          'Password' =>$Password
                                                                          )
                                                );
    
    $request['ClientDetail'] = array(
                                     'AccountNumber' =>$account_number,
                                     'MeterNumber' =>$Meter
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
                                                                 'Value' => $TotalWeight,
                                                                 'Units' => 'KG' // valid values LB and KG
                                                                 ),
                                          'Shipper' => array(
                                                             'Contact' => array(
                                                                                'PersonName' => $sender_name,
                                                                                'CompanyName' =>$sender_company,
                                                                                'PhoneNumber' =>$sender_phone
                                                                                ),
                                                             'Address' => array(
                                                                                'StreetLines' => array($sender_address),
                                                                                'City' => $sender_city,
                                                                                'StateOrProvinceCode' => $ShipperStateOrProvinceCode,
                                                                                'PostalCode' =>$from_pin ,
                                                                                'CountryCode' => 'IN'
                                                                                )
                                                             ),
                                          'Recipient' =>array(
                                                              'Contact' => array(
                                                                                 'PersonName' =>$receiver_name ,
                                                                                 'CompanyName' => $receiver_company ,
                                                                                 'PhoneNumber' =>$receiver_phone
                                                                                 ),
                                                              'Address' => array(
                                                                                 'StreetLines' => array($receiver_address),
                                                                                 'City' => $receiver_city,
                                                                                 'StateOrProvinceCode' =>$ReceiverStateOrProvinceCode,
                                                                                 'PostalCode' => $to_pin,
                                                                                 'CountryCode' => 'IN',
                                                                                 'Residential' => true
                                                                                 )
                                                              ) ,
                                          'ShippingChargesPayment' =>array('PaymentType' => 'SENDER',
                                                                           'Payor' => array(
                                                                                            'ResponsibleParty' => array(
                                                                                                                        'AccountNumber' =>$account_number,
                                                                                                                        'Contact' => null,
                                                                                                                        'Address' => array(
                                                                                                                                           'CountryCode' => 'IN')
                                                                                                                        )
                                                                                            )
                                                                           ),
                                          //'SpecialServicesRequested' => addSpecialServices(),
                                          'CustomsClearanceDetail'=>array(
                                                                          'DocumentContent'=>$DocumentContent,
                                                                          'CommercialInvoice'=>array(
                                                                                                     'Purpose'=>$purpose
                                                                                                     ),
                                                                          'CustomsValue'=>array(
                                                                                                'Currency'=>'INR',
                                                                                                'Amount'=>$InvoiceValue
                                                                                                ),
                                                                          'Commodities'=> array(
                                                                                                'Name'=>$Commodity,
                                                                                                'NumberOfPieces'=>$packagecount,
                                                                                                'Description'=>$Commodity_desc,
                                                                                                'CountryOfManufacture'=>'IN',
                                                                                                'Weight'=>array(
                                                                                                                'Units'=>'KG',
                                                                                                                'Value'=>$CommodityWeight
                                                                                                                ),
                                                                                                'Quantity'=>$CommodityQuantity,
                                                                                                'QuantityUnits'=>'EA',
                                                                                                'UnitPrice'=>array(
                                                                                                                   'Currency'=>'INR',
                                                                                                                   'Amount'=>$CommodityValue
                                                                                                                   ),
                                                                                                'CustomsValue'=>array(
                                                                                                                      'Currency'=>'INR',
                                                                                                                      'Amount'=>$CommodityValue
                                                                                                                      )
                                                                                                
                                                                                                )
                                                                          
                                                                          
                                                                          ),
                                          'LabelSpecification' => array(
                                                                        'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
                                                                        'ImageType' => 'PDF',  // valid values DPL, EPL2, PDF, ZPLII and PNG
                                                                        'LabelStockType' => 'PAPER_7X4.75'
                                                                        ),
                                          'ShippingDocumentSpecification'=>array(
                                                                                 
                                                                                 
                                                                                 
                                                                                 'CommercialInvoiceDetail'=>array(
                                                                                                                  'Format'=>array(
                                                                                                                                  'ImageType'=>'PDF',
                                                                                                                                  'StockType'=>'PAPER_LETTER'
                                                                                                                                  )
                                                                                                                  
                                                                                                                  ),
                                                                                 'ShippingDocumentTypes'=>array('PRO_FORMA_INVOICE')
                                                                                 
                                                                                 
                                                                                 
                                                                                 
                                                                                 
                                                                                 ),
                                          'PackageCount' => 1,
                                          'RequestedPackageLineItems' => array(
                                                                               '0' => array(
                                                                                            'SequenceNumber'=>1,
                                                                                            'GroupPackageCount'=>1,
                                                                                            'Weight' => array(
                                                                                                              'Value' =>$TotalWeight ,
                                                                                                              'Units' => 'KG'
                                                                                                              ),
                                                                                            'Dimensions' => array(
                                                                                                                  'Length' => $length,
                                                                                                                  'Width' => $breath,
                                                                                                                  'Height' =>$height,
                                                                                                                  'Units' => 'CM'
                                                                                                                  )
                                                                                            )
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
            
            $filepath='AirwayBill/FedEx/AirwayBill/'.$filename;
            
            
            
            $fp = fopen("../../AirwayBill/FedEx/AirwayBill/".$filename, 'wb');
            fwrite($fp, $response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image); //Create PNG or PDF file
            fclose($fp);
            
            
            ///////////////////////////////////////////////////
            /////////////// Merge AWB and ProForma Invoice/////
            ///////////////////////////////////////////////////
            
            if($DocumentContent=="NON_DOCUMENTS"){
                
                $fp = fopen("../../AirwayBill/FedEx/ProForma/".$filename, 'wb');
                fwrite($fp, $response->CompletedShipmentDetail->ShipmentDocuments->Parts->Image); //Create PNG or PDF file
                fclose($fp);
                
                
                include_once('/Applications/XAMPP/xamppfiles/htdocs/Courier/Merger/PDFMerger.php');  // Change Path in Live Server
                
                
                $pdf = new PDFMerger;
                
                $pdf->addPDF("../../AirwayBill/FedEx/ProForma/".$filename, 'all')
                ->addPDF("../../AirwayBill/FedEx/AirwayBill/".$filename, 'all')
                ->merge('file', "/Applications/XAMPP/xamppfiles/htdocs/Courier/AirwayBill/FedEx/AWB_ProForma/".$filename); // Change Path in Live Server
                
                
                $filepath='AirwayBill/FedEx/AWB_ProForma/'.$filename;
            }
            
            
            
            
            $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`,`Airwaybill_Number`, `AWB_Status`, `AWB_Link`,`CreatedByUserID`,`CreatedByCompID`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','FedEx','$service','$token','Success','$filepath','$userid','$CompID')");
            $stmt5->execute();
            
            $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='FedEx' AND `AWB_Status` ='Success' order by `API_Hit_Date` DESC");
            $stmt6->execute();
            
            $stmt6->setFetchMode(PDO::FETCH_ASSOC);
            $row6 = $stmt6->fetch();
            
            $AWB_UID=$row6['UID'];
            
            
            $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Parties`(`Shipper_VendorID`,`Shipper_Name`, `Shipper_Comp`, `Shipper_Address`,`Shipper_City`,`Shipper_State`,`Shipper_Pincode`,`Shipper_Phone`,`Receiver_VendorID`, `Receiver_Name`, `Receiver_Comp`, `Receiver_Address`,`Receiver_City`,`Receiver_State`,`Receiver_Pincode`,`Receiver_Phone`,`AWB_UID`) VALUES ('$sender_vendorid','$sender_info[1]','$sender_info[2]','$sender_info[3]','$sender_info[4]','$sender_info[5]','$sender_info[0]','$sender_info[6]','$receiver_vendorid','$receiver_info[1]','$receiver_info[2]','$receiver_info[3]','$receiver_info[4]','$receiver_info[5]','$receiver_info[0]','$receiver_info[6]','$AWB_UID') ");
            
            $stmt7->execute();
            
            
            
            $stmt8=$conn->prepare("INSERT INTO `AirwayBill_Packages`(`AWB_UID`, `Package_Count`, `Length`, `Breath`, `Height`, `Weight`) VALUES ('$AWB_UID','$packagecount',$length,$breath,$height,$TotalWeight)");
            
            $stmt8->execute();
            
            
            
            if($shipmentcontent=="Commodities"){
                
                
                
                
                $stmt9=$conn->prepare("INSERT INTO `AirwayBill_Commodites`(`AWB_UID`, `Commodity`, `Commodity_Desc`, `Value`) VALUES ('$AWB_UID','$Commodity','$Commodity_desc','$CommodityValue')");
                $stmt9->execute();
                
                
                
            }
            
            $stmt10=$conn->prepare("INSERT INTO `pickup`(`AWB_Number`, `UID`, `Courier_Vendor`,`Pickup_Status`, `UserID`) VALUES ('$token','$AWB_UID','FedEx','Pending','$userid')");
            $stmt10->execute();
            
            $status="Success";
            
            
        }else{
            //printError($client, $response);
            
            $APIRequest=$client->__getLastRequest();
            $APIResponse=$client->__getLastResponse();
            
            //$APIRequest=str_replace("'", ' ',$APIRequest);
            $message=json_encode($response->Notifications);
            
            
            $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`,`CreatedByCompID`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','FedEx','$service','Fail','','$CompID')");
            $stmt5->execute();
            
            
            
            $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='FedEx' AND `AWB_Status` ='Fail' order by `API_Hit_Date` DESC");
            $stmt6->execute();
            
            $stmt6->setFetchMode(PDO::FETCH_ASSOC);
            $row6 = $stmt6->fetch();
            
            $AWB_UID=$row6['UID'];
            
            $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Error`(`AWB_UID`,`Failure_Type`,`Request`,`Response` ,`Message`) VALUES ('$AWB_UID','Error','$APIRequest','$APIResponse','$message')");
            
            $stmt7->execute();
            
            $stmt9=$conn->prepare("INSERT INTO `AirwayBill_Parties`(`Shipper_VendorID`,`Shipper_Name`, `Shipper_Comp`, `Shipper_Address`,`Shipper_City`,`Shipper_State`,`Shipper_Pincode`,`Shipper_Phone`,`Receiver_VendorID`, `Receiver_Name`, `Receiver_Comp`, `Receiver_Address`,`Receiver_City`,`Receiver_State`,`Receiver_Pincode`,`Receiver_Phone`,`AWB_UID`) VALUES ('$sender_vendorid','$sender_info[1]','$sender_info[2]','$sender_info[3]','$sender_info[4]','$sender_info[5]','$sender_info[0]','$sender_info[6]','$receiver_vendorid','$receiver_info[1]','$receiver_info[2]','$receiver_info[3]','$receiver_info[4]','$receiver_info[5]','$receiver_info[0]','$receiver_info[6]','$AWB_UID') ");
            
            $stmt9->execute();
            
            
            
            $stmt8=$conn->prepare("INSERT INTO `AirwayBill_Packages`(`AWB_UID`, `Package_Count`, `Length`, `Breath`, `Height`, `Weight`) VALUES ('$AWB_UID','$packagecount',$length,$breath,$height,$TotalWeight)");
            
            $stmt8->execute();
            
            $status="Error";
            
        }
        
        writeToLog($client);    // Write to log file
    } catch (SoapFault $exception) {
        
        writeToLog($client);    // Write to log file
        
        $APIRequest=$client->__getLastRequest();
        $APIResponse=$client->__getLastResponse();
        
        
        $message=json_encode($exception);
        $message = str_replace("'", ' ', $message);
        
        $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`,`CreatedByCompID`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','FedEx','$service','Fail','','$CompID')");
        $stmt5->execute();
        
        
        
        $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='FedEx' AND `AWB_Status` ='Fail' order by `API_Hit_Date` DESC");
        $stmt6->execute();
        
        $stmt6->setFetchMode(PDO::FETCH_ASSOC);
        $row6 = $stmt6->fetch();
        
        $AWB_UID=$row6['UID'];
        
        
        $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Error`(`AWB_UID`,`Failure_Type`,`Request`,`Response` ,`Message`) VALUES ('$AWB_UID','Error','$APIRequest','$APIResponse','$message')");
        
        $stmt7->execute();
        
        
        $stmt9=$conn->prepare("INSERT INTO `AirwayBill_Parties`(`Shipper_VendorID`,`Shipper_Name`, `Shipper_Comp`, `Shipper_Address`,`Shipper_City`,`Shipper_State`,`Shipper_Pincode`,`Shipper_Phone`,`Receiver_VendorID`, `Receiver_Name`, `Receiver_Comp`, `Receiver_Address`,`Receiver_City`,`Receiver_State`,`Receiver_Pincode`,`Receiver_Phone`,`AWB_UID`) VALUES ('$sender_vendorid','$sender_info[1]','$sender_info[2]','$sender_info[3]','$sender_info[4]','$sender_info[5]','$sender_info[0]','$sender_info[6]','$receiver_vendorid','$receiver_info[1]','$receiver_info[2]','$receiver_info[3]','$receiver_info[4]','$receiver_info[5]','$receiver_info[0]','$receiver_info[6]','$AWB_UID') ");
        
        $stmt9->execute();
        
        
        
        $stmt8=$conn->prepare("INSERT INTO `AirwayBill_Packages`(`AWB_UID`, `Package_Count`, `Length`, `Breath`, `Height`, `Weight`) VALUES ('$AWB_UID','$packagecount',$length,$breath,$height,$TotalWeight)");
        
        $stmt8->execute();
        
        $status="Error";
    }
    
    
    $conn=null;
    

    ?>
