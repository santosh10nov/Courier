<?php
    // Copyright 2009, FedEx Corporation. All rights reserved.
    // Version 12.0.0
    
    require_once('/Applications/XAMPP/xamppfiles/htdocs/Courier/fedex-common.php5');
    
    
    //The WSDL is not included with the sample code.
    //Please include and reference in $path_to_wsdl variable.
    $path_to_wsdl = "/Applications/XAMPP/xamppfiles/htdocs/Courier/AirwayBill/FedEx/ShipService_v17.wsdl";
    
    
    define('SHIP_LABEL', 'fedexshipexpresslabel.pdf');  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. shiplabel.pdf)
    define('SHIP_CODLABEL', 'CODexpressreturnlabel.pdf');  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. CODexpressreturnlabel.pdf)
    
    ini_set("soap.wsdl_cache_enabled", "0");
    
    $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
    
    
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
                                                                 'Value' => $weight,
                                                                 'Units' => 'KG' // valid values LB and KG
                                                                 ),
                                          'Shipper' => addShipper($sender_info,$ShipperStateOrProvinceCode),
                                          'Recipient' => addRecipient($receiver_info,$ReceiverStateOrProvinceCode),
                                          'ShippingChargesPayment' => addShippingChargesPayment(),
                                          //'SpecialServicesRequested' => addSpecialServices(),
                                          'CustomsClearanceDetail'=>addCustomsClearanceDetail($account_number,$DocumentContent,$cost,$commodity_count,$Commodity_desc,$purpose),
                                          'LabelSpecification' => addLabelSpecification(),
                                          'PackageCount' => 1,
                                          'ShippingDocumentSpecification'=>addshippingDocumentSpecification(),
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
            
            $filepath='AirwayBill/FedEx/AirwayBill/'.$filename;
            
            
            
            $fp = fopen("./AirwayBill/FedEx/AirwayBill/".$filename, 'wb');
            fwrite($fp, $response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image); //Create PNG or PDF file
            fclose($fp);
            
            
            
            ///////////////////////////////////////////////////
            /////////////// Merge AWB and ProForma Invoice/////
            ///////////////////////////////////////////////////
            
            if($DocumentContent=="NON_DOCUMENTS"){
                
                $fp = fopen("./AirwayBill/FedEx/ProForma/".$filename, 'wb');
                fwrite($fp, $response->CompletedShipmentDetail->ShipmentDocuments->Parts->Image); //Create PNG or PDF file
                fclose($fp);
                
                
                include('/Applications/XAMPP/xamppfiles/htdocs/Courier/Merger/PDFMerger.php');  // Change Path in Live Server
                
                
                $pdf = new PDFMerger;
                
                $pdf->addPDF("./AirwayBill/FedEx/ProForma/".$filename, 'all')
                ->addPDF("./AirwayBill/FedEx/AirwayBill/".$filename, 'all')
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
            
            
            
            $stmt8=$conn->prepare("INSERT INTO `AirwayBill_Packages`(`AWB_UID`, `Package_Count`, `Length`, `Breath`, `Height`, `Weight`) VALUES ('$AWB_UID','1',$length,$breath,$height,$weight)");
            
            $stmt8->execute();
            
            
            
            if($shipmentcontent=="Commodities"){
                for($i=0;$i<$commodity_count;$i++){
                    
                    $Commodity=$_POST["Commodity".$i];
                    $Commodity_desc=$_POST["Commodity_desc".$i];
                    $CommodityValue=$_POST["CommodityValue".$i];
                    
                    $stmt9=$conn->prepare("INSERT INTO `AirwayBill_Commodites`(`AWB_UID`, `Commodity`, `Commodity_Desc`, `Value`) VALUES ('$AWB_UID','$Commodity','$Commodity_desc','$CommodityValue')");
                    $stmt9->execute();
                    
                }
                
            }
            
            $stmt10=$conn->prepare("INSERT INTO `pickup`(`AWB_Number`, `UID`, `Courier_Vendor`,`Pickup_Status`, `UserID`) VALUES ('$token','$AWB_UID','FedEx','Pending','$userid')");
            $stmt10->execute();
            
            $status="Success";
            
            writeToLog($client);
            
            
        }else{
            //printError($client, $response);
            
            $APIRequest=$client->__getLastRequest();
            $APIResponse=$client->__getLastResponse();
            
            //$APIRequest=str_replace("'", ' ',$APIRequest);
            $message=json_encode($response->Notifications);
            
            writeToLog($client);    // Write to log file
            
            
            $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`,`CreatedByCompID`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','FedEx','$service','Fail','','$CompID')");
            $stmt5->execute();
            
            
            
            $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='FedEx' AND `AWB_Status` ='Fail' order by `API_Hit_Date` DESC");
            $stmt6->execute();
            
            $stmt6->setFetchMode(PDO::FETCH_ASSOC);
            $row6 = $stmt6->fetch();
            
            $AWB_UID=$row6['UID'];
            
            $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Error`(`AWB_UID`,`Failure_Type`,`Request`,`Response` ,`Message`) VALUES ('$AWB_UID','Error','$APIRequest','$APIResponse','$message')");
            
            $stmt7->execute();
            
            $status="Error";
            
        }
        
    } catch (SoapFault $exception) {
        
        $APIRequest=$client->__getLastRequest();
        $APIResponse=$client->__getLastResponse();
        
        
        $message=json_encode($exception);
        $message = str_replace("'", ' ', $message);
        
        
        echo $APIResponse."</br>";
        
        $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`,`CreatedByCompID`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','FedEx','$service','Fail','','$CompID')");
        $stmt5->execute();
        
        
        
        $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='FedEx' AND `AWB_Status` ='Fail' order by `API_Hit_Date` DESC");
        $stmt6->execute();
        
        $stmt6->setFetchMode(PDO::FETCH_ASSOC);
        $row6 = $stmt6->fetch();
        
        $AWB_UID=$row6['UID'];
        
        
        $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Error`(`AWB_UID`,`Failure_Type`,`Request`,`Response` ,`Message`) VALUES ('$AWB_UID','Error','$APIRequest','$APIResponse','$message')");
        
        $stmt7->execute();
        
        $status="Error";
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
                                    'LabelStockType' => 'PAPER_8.5X11_BOTTOM_HALF_LABEL',
                                    'LabelPrintingOrientation'=>'BOTTOM_EDGE_OF_TEXT_FIRST',
                                    'CustomerSpecifiedDetail'=>array(
                                                                     'DocTabContent'=>array('DocTabContentType'=>'STANDARD')
                                                                     
                                                                     
                                                                     
                                                                     )
                                    );
        return $labelSpecification;
    }
    
    function addSpecialServices(){
        $specialServices = array(
                                 'SpecialServiceTypes' => array('ELECTRONIC_TRADE_DOCUMENTS'),
                                 'EtdDetail' => array(
                                                      
                                                      'RequestedDocumentCopies' => 'PRO_FORMA_INVOICE' // ANY, GUARANTEED_FUNDS
                                                      )
                                 );
        return $specialServices;
    }
    
    function addPackageLineItem1($arr){
        $packageLineItem = array(
                                 'SequenceNumber'=>1,
                                 'GroupPackageCount'=>1,
                                 'Weight' => array(
                                                   'Value' =>$arr[0],
                                                   'Units' => 'KG'
                                                   ),
                                 'Dimensions' => array(
                                                       'Length' => $arr[1],
                                                       'Width' => $arr[2],
                                                       'Height' => $arr[3],
                                                       'Units' => 'CM'
                                                       )
                                 );
        return $packageLineItem;
    }
    
    
    
    function addCustomsClearanceDetail($AccountNumber,$DocumentType,$CustomValue,$CommoditiesCount,$Commodities,$purpose){
        
        $Comm=array();
        
        
        for($i=0;$i<$CommoditiesCount;$i++){
            
            $CommValues=
            array(
                  'NumberOfPieces'=>1,
                  'Description'=>$Commodities["Com".$i]["Commodity_desc".$i],
                  'CountryOfManufacture'=>'IN',
                  'Weight'=>array('Units'=>'KG','Value'=>$Commodities["Com".$i]["Commodity_weight".$i]),
                  'Quantity'=>$Commodities["Com".$i]["Commodity_quan".$i],
                  'QuantityUnits'=>'EA',
                  'UnitPrice'=>array('Currency'=>'INR','Amount'=>$Commodities["Com".$i]["CommodityValue".$i]),
                  'CustomsValue'=>array('Currency'=>'INR','Amount'=>$Commodities["Com".$i]["CommodityValue".$i])
                  );
            array_push($Comm,$CommValues);
            
            
        }
        
        $customerClearanceDetail=array(
                                       
                                       'DutiesPayment'=>array(
                                                              'PaymentType'=>'SENDER',
                                                              'Payor'=>array(
                                                                             'ResponsibleParty'=>array(
                                                                                                       'AccountNumber'=>$AccountNumber,
                                                                                                       'Contact'=>null,
                                                                                                       'Address'=>array('CountryCode'=>'IN')
                                                                                                       )
                                                                             )
                                                              ),
                                       'DocumentContent'=>$DocumentType,
                                       'CommercialInvoice'=>array(
                                                                  'Purpose'=>$purpose
                                                                  ),
                                       'CustomsValue'=>array(
                                                             'Currency'=>'INR',
                                                             'Amount'=>$CustomValue
                                                             ),
                                       'Commodities'=>$Comm
                                       );
        
        return $customerClearanceDetail;
    }
    
    
    function addshippingDocumentSpecification(){
        
        
        $shippingDocumentSpecification=array(
                                             
                                             
                                             
                                             'CommercialInvoiceDetail'=>array(
                                                                              'Format'=>array(
                                                                                              'ImageType'=>'PDF',
                                                                                              'StockType'=>'PAPER_LETTER'
                                                                                              )
                                                                              
                                                                              ),
                                             'ShippingDocumentTypes'=>array('PRO_FORMA_INVOICE')
                                             
                                             
                                             
                                             
                                             
                                             );
        
        
        return $shippingDocumentSpecification;
    }
    
    ?>
