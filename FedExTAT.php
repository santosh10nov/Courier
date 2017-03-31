<?php
    // Copyright 2009, FedEx Corporation. All rights reserved.
    // Version 12.0.0
    
    include('fedex-common.php5');
    
    $newline = "<br/>";
    //The WSDL is not included with the sample code.
    //Please include and reference in $path_to_wsdl variable.
    $path_to_wsdl = "RateService_v18.wsdl";
    
    ini_set("soap.wsdl_cache_enabled", "0");
    
    $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
    
    
    $product_type1="STANDARD_OVERNIGHT";
    $sub_product="";
    $pickup_datetime=$pickup_date."T".$pickup_time;
    
    //$pickup_datetime="";
    
    $weight=50;
    $length=3;
    $breath=4;
    $height=5;
    
    
    
    $dimension=array("we"=>"$weight","l"=>"$length","b"=>"$breath","h"=>"$height");
    
    //////////////////////////////////
    /// MySql Connection//////////////
    //////////////////////////////////
    
    
   require_once 'dbconfig.php';
    
    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->prepare("SELECT * FROM `city_pincode` where `pin`=$from_pin");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();
        $PID1=$row['ProvinceCode'];
        $fromcity=$row['city'];
        
        $stmt = $conn->prepare("SELECT * FROM `city_pincode` where `pin`=$to_pin");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();
        $PID2=$row['ProvinceCode'];
        $tocity=$row['city'];
    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    
    
    
    
    
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
    $request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request using PHP ***');
    $request['Version'] = array(
                                'ServiceId' => 'crs',
                                'Major' => '18',
                                'Intermediate' => '0',
                                'Minor' => '0'
                                );
    $request['ReturnTransitAndCommit'] = true;
    $request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
    $request['RequestedShipment']['ShipTimestamp'] =$pickup_datetime;
    $request['RequestedShipment']['ServiceType'] = $product_type1; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
    $request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
    $request['RequestedShipment']['TotalInsuredValue']=array(
                                                             'Ammount'=>100,
                                                             'Currency'=>'INR'
                                                             );
    $request['RequestedShipment']['Shipper'] = addShipper($from_pin,$PID1,$fromcity);
    $request['RequestedShipment']['Recipient'] = addRecipient($to_pin,$PID2,$tocity);
    $request['RequestedShipment']['ShippingChargesPayment'] = addShippingChargesPayment();
    $request['RequestedShipment']['CustomsClearanceDetail'] = addCustomsClearanceDetail();
    $request['RequestedShipment']['PackageCount'] = '1';
    $request['RequestedShipment']['RequestedPackageLineItems'] = addPackageLineItem1($dimension);
    
    
    
    if($service_avi_level1!=0){
        
        try {
            if(setEndpoint('changeEndpoint')){
                $newLocation = $client->__setLocation(setEndpoint('endpoint'));
            }
            
            $response = $client -> getRates($request);
            
            if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
                $rateReply = $response -> RateReplyDetails;
                $serviceType = '<td>'.$rateReply -> ServiceType . '</td>';
                $service=$rateReply -> ServiceType;
                
                if($rateReply->RatedShipmentDetails && is_array($rateReply->RatedShipmentDetails)){
                    $amount = '<td>' . number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
                    $amt= number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",");
                }elseif($rateReply->RatedShipmentDetails && ! is_array($rateReply->RatedShipmentDetails)){
                    $amount = '<td>' . number_format($rateReply->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
                    $amt= number_format($rateReply->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",");
                }
                if(array_key_exists('DeliveryTimestamp',$rateReply)){
                    $deliveryDate= '<td>' . $rateReply->DeliveryTimestamp . '</td>';
                    $tat=$rateReply->DeliveryTimestamp;
                }else if(array_key_exists('TransitTime',$rateReply)){
                    $deliveryDate= '<td>' . $rateReply->TransitTime . '</td>';
                    $tat=$rateReply->TransitTime;
                }else {
                    $deliveryDate='<td>&nbsp;</td>';
                }
                
                ////////////////////////////////////////////
                ///////// Expected Date////////////////////
                //////////////////////////////////////////
                
                $ExpectedDateDelivery=substr($deliveryDate, 4, 10);
                $ExpectedDatePOD=substr($deliveryDate,4, 10);
                $AdditionalDays="";
                
                
                $convert_date = new DateTime($ExpectedDateDelivery);
                $ExpectedDateDelivery = date_format($convert_date, 'd-M-Y');
                $convert_date = new DateTime($ExpectedDatePOD);
                $ExpectedDatePOD = date_format($convert_date, 'd-M-Y');
                
                $diff = abs(strtotime($ExpectedDatePOD) - strtotime($pickup_date1));
                
                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                
                
                
                try{
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt1 = $conn->prepare("INSERT INTO `transit_time` (`vendor_id`, `vendor_name`, `product_type`, `sub_product`, `pickup_date`, `pickup_Time`, `from_pin`, `to_pin`, `ExpectedDateDelivery`, `ExpectedDatePOD`, `TAT`, `AdditionalDays`,`added_date`,`added_by`) VALUES ('2', 'FedEx', '$product_type1', '$sub_product', '$pickup_date1', '$pickup_time', '$from_pin', '$to_pin', '$ExpectedDateDelivery', '$ExpectedDatePOD', '$days', '$AdditionalDays','$nowtime','$userid');");
                    $stmt1->execute();
                    
                }catch(PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                
                
                //printSuccess($client, $response);
            }else{
                //printError($client, $response);
            }
            writeToLog($client);    // Write to log file
            
        } catch (SoapFault $exception) {
            printFault($exception, $client);
        }
        
    }
    
    /////////////////////////////////////////////////////////////////////////////////////
    ////////////////////// PRIORITY_OVERNIGHT///////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////
    
    
    if($service_avi_level2!=0){
        
        $product_type2='PRIORITY_OVERNIGHT';
        
        $request['RequestedShipment']['ServiceType'] = $product_type1;
        
        
        try {
            if(setEndpoint('changeEndpoint')){
                $newLocation = $client->__setLocation(setEndpoint('endpoint'));
            }
            
            $response = $client -> getRates($request);
            
            if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
                $rateReply = $response -> RateReplyDetails;
                $serviceType = '<td>'.$rateReply -> ServiceType . '</td>';
                $service=$rateReply -> ServiceType;
                
                if($rateReply->RatedShipmentDetails && is_array($rateReply->RatedShipmentDetails)){
                    $amount = '<td>' . number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
                    $amt= number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",");
                }elseif($rateReply->RatedShipmentDetails && ! is_array($rateReply->RatedShipmentDetails)){
                    $amount = '<td>' . number_format($rateReply->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
                    $amt= number_format($rateReply->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",");
                }
                if(array_key_exists('DeliveryTimestamp',$rateReply)){
                    $deliveryDate= '<td>' . $rateReply->DeliveryTimestamp . '</td>';
                    $tat=$rateReply->DeliveryTimestamp;
                }else if(array_key_exists('TransitTime',$rateReply)){
                    $deliveryDate= '<td>' . $rateReply->TransitTime . '</td>';
                    $tat=$rateReply->TransitTime;
                }else {
                    $deliveryDate='<td>&nbsp;</td>';
                }
                
                ////////////////////////////////////////////
                ///////// Expected Date////////////////////
                //////////////////////////////////////////
                
                $ExpectedDateDelivery=substr($deliveryDate, 4, 10);
                $ExpectedDatePOD=substr($deliveryDate,4, 10);
                $AdditionalDays="";
                
                $convert_date = new DateTime($ExpectedDateDelivery);
                $ExpectedDateDelivery = date_format($convert_date, 'd-M-Y');
                $convert_date = new DateTime($ExpectedDatePOD);
                $ExpectedDatePOD = date_format($convert_date, 'd-M-Y');
                
                $diff = abs(strtotime($ExpectedDatePOD) - strtotime($pickup_date1));
                
                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                
                
                
                
                try{
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $product_type2="PRIORITY_OVERNIGHT";
                    $stmt1 = $conn->prepare("INSERT INTO `transit_time` (`vendor_id`, `vendor_name`, `product_type`, `sub_product`, `pickup_date`, `pickup_Time`, `from_pin`, `to_pin`, `ExpectedDateDelivery`, `ExpectedDatePOD`, `TAT`, `AdditionalDays`,`added_date`,`added_by`) VALUES ('2', 'FedEx', '$product_type2', '$sub_product', '$pickup_date1', '$pickup_time', '$from_pin', '$to_pin', '$ExpectedDateDelivery', '$ExpectedDatePOD', '$days', '$AdditionalDays','$nowtime','$userid');");
                    $stmt1->execute();
                    
                }catch(PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                
                
                //printSuccess($client, $response);
            }else{
                //printError($client, $response);
            }
            writeToLog($client);    // Write to log file
            
        } catch (SoapFault $exception) {
            printFault($exception, $client);
        }
        
    }
    
    
    
    
    /////////////////////////////////////////
    // Sender Details////////////////////////
    /////////////////////////////////////////
    
    function addShipper($pin,$pid1,$city){
        $shipper = array(
                         'Contact' => array(
                                            'PersonName' => 'Sender Name',
                                            'CompanyName' => 'Sender Company Name',
                                            'PhoneNumber' => '9012638716'
                                            ),
                         'Address' => array(
                                            'StreetLines' => array('Address Line 1'),
                                            'City' => "$city",
                                            'StateOrProvinceCode' => "$pid1",
                                            'PostalCode' => "$pin",
                                            'CountryCode' => 'IN'
                                            )
                         );
        return $shipper;
    }
    
    /////////////////////////////////////////
    // Recipient Details/////////////////////
    /////////////////////////////////////////
    
    function addRecipient($pin,$pid2,$city){
        $recipient = array(
                           'Contact' => array(
                                              'PersonName' => 'Recipient Name',
                                              'CompanyName' => 'Company Name',
                                              'PhoneNumber' => '9012637906'
                                              ),
                           'Address' => array(
                                              'StreetLines' => array('Address Line 1'),
                                              'City' => '$city',
                                              'StateOrProvinceCode' => "$pid2",
                                              'PostalCode' => "$pin",
                                              'CountryCode' => 'IN',
                                              'Residential' => false
                                              )
                           );
        return $recipient;
    }
    
    /////////////////////////////////////////
    // Weight and Dimension of Packet////////
    /////////////////////////////////////////
    
    function addShippingChargesPayment(){
        $shippingChargesPayment = array(
                                        'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
                                        'Payor' => array(
                                                         'ResponsibleParty' => array(
                                                                                     'AccountNumber' => getProperty('billaccount'),
                                                                                     'CountryCode' => 'IN'
                                                                                     )
                                                         )
                                        );
        return $shippingChargesPayment;
    }
    
    /////////////////////////////////////////
    // Airway Generation/////////////////////
    /////////////////////////////////////////
    
    function addLabelSpecification(){
        $labelSpecification = array(
                                    'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
                                    'ImageType' => 'PDF',  // valid values DPL, EPL2, PDF, ZPLII and PNG
                                    'LabelStockType' => 'PAPER_7X4.75'
                                    );
        return $labelSpecification;
    }
    
    /////////////////////////////////////////
    // Any Special Service COD//////////////
    /////////////////////////////////////////
    
    function addSpecialServices(){
        $specialServices = array(
                                 'SpecialServiceTypes' => array('COD'),
                                 'CodDetail' => array(
                                                      'CodCollectionAmount' => array(
                                                                                     'Currency' => 'INR',
                                                                                     'Amount' => 150
                                                                                     ),
                                                      'CollectionType' => 'ANY' // ANY, GUARANTEED_FUNDS
                                                      )
                                 );
        return $specialServices;
    }
    /////////////////////////////////////////
    // Weight and Dimension of Packet////////
    /////////////////////////////////////////
    
    function addPackageLineItem1($di){
        $packageLineItem = array(
                                 'SequenceNumber'=>1,
                                 'GroupPackageCount'=>1,
                                 'Weight' => array(
                                                   'Value' => "$di[we]",
                                                   'Units' => 'LB'
                                                   ),
                                 'Dimensions' => array(
                                                       'Length' => "$di[l]",
                                                       'Width' => "$di[b]",
                                                       'Height' => "$di[h]",
                                                       'Units' => 'IN'
                                                       )
                                 );
        return $packageLineItem;
    }
    
    
    
    function addCustomsClearanceDetail(){
        $customsClearance = array(
                                  'CustomsValue' => array(
                                                          'Currency'=>'INR',
                                                          'Amount'=>100
                                                          ),
                                  'CommercialInvoice' => array(
                                                               'Purpose' => "SOLD"
                                                               )
                                  );
        
        return $customsClearance;
    }
    ?>
