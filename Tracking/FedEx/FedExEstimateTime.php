<?php
    // Copyright 2009, FedEx Corporation. All rights reserved.
    // Version 12.0.0
    
    //require_once('fedex-common.php5');
    
    $newline = "<br />";
    //The WSDL is not included with the sample code.
    //Please include and reference in $path_to_wsdl variable.
    $path_to_wsdl1 = "/Applications/XAMPP/xamppfiles/htdocs/Courier/wsdl/RateService_v18.wsdl";
    
    ini_set("soap.wsdl_cache_enabled", "0");
    
    $client1 = new SoapClient($path_to_wsdl1, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
    
    
    $weight=50;
    $length=3;
    $breath=4;
    $height=5;
    
    
    $dimension=array("we"=>"$weight","l"=>"$length","b"=>"$breath","h"=>"$height");
    
    
    //////////////////////////////////
    /// MySql Connection//////////////
    //////////////////////////////////
    
    
   
    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        
        $stmt1=$conn->prepare("SELECT CourierService,Shipper_Pincode,Receiver_Pincode FROM `AirwayBill` INNER JOIN AirwayBill_Parties on AirwayBill.UID=AirwayBill_Parties.AWB_UID where AirwayBill.Airwaybill_Number='$AWBNo'");
        
        $stmt1->execute();
        $stmt1->setFetchMode(PDO::FETCH_ASSOC);
        $row1 = $stmt1->fetch();
        
        
        $from_pin=$row1['Shipper_Pincode'];
        $to_pin=$row1['Receiver_Pincode'];
        $product_type1=$row1['CourierService'];
        
       
        
        $product_type1="STANDARD_OVERNIGHT";
        
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
    
    
    
    
    
    
    $request1['WebAuthenticationDetail'] = array(
                                                'ParentCredential' => array(
                                                                            'Key' => getProperty('parentkey'),
                                                                            'Password' => getProperty('parentpassword')
                                                                            ),
                                                'UserCredential' => array(
                                                                          'Key' => getProperty('key'),
                                                                          'Password' => getProperty('password')
                                                                          )
                                                );
    $request1['ClientDetail'] = array(
                                     'AccountNumber' => getProperty('shipaccount'),
                                     'MeterNumber' => getProperty('meter')
                                     );
    $request1['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request using PHP ***');
    $request1['Version'] = array(
                                'ServiceId' => 'crs',
                                'Major' => '18',
                                'Intermediate' => '0',
                                'Minor' => '0'
                                );
    $request1['ReturnTransitAndCommit'] = true;
    $request1['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
    $request1['RequestedShipment']['ShipTimestamp'] =$PickupDate;
    $request1['RequestedShipment']['ServiceType'] = $product_type1; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
    $request1['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
    $request1['RequestedShipment']['TotalInsuredValue']=array(
                                                             'Ammount'=>100,
                                                             'Currency'=>'INR'
                                                             );
    $request1['RequestedShipment']['Shipper'] = array(
                                                      'Contact' => array(
                                                                         'PersonName' => 'Sender Name',
                                                                         'CompanyName' => 'Sender Company Name',
                                                                         'PhoneNumber' => '9012638716'
                                                                         ),
                                                      'Address' => array(
                                                                         'StreetLines' => array('Address Line 1'),
                                                                         'City' => "$fromcity",
                                                                         'StateOrProvinceCode' => "$PID1",
                                                                         'PostalCode' => "$from_pin",
                                                                         'CountryCode' => 'IN'
                                                                         )
                                                      );
    $request1['RequestedShipment']['Recipient'] = array(
                                                        'Contact' => array(
                                                                           'PersonName' => 'Recipient Name',
                                                                           'CompanyName' => 'Company Name',
                                                                           'PhoneNumber' => '9012637906'
                                                                           ),
                                                        'Address' => array(
                                                                           'StreetLines' => array('Address Line 1'),
                                                                           'City' => "$tocity",
                                                                           'StateOrProvinceCode' => "$PID2",
                                                                           'PostalCode' => "$to_pin",
                                                                           'CountryCode' => 'IN',
                                                                           'Residential' => false
                                                                           )
                                                        );
    $request1['RequestedShipment']['ShippingChargesPayment'] = array(
                                                                     'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
                                                                     'Payor' => array(
                                                                                      'ResponsibleParty' => array(
                                                                                                                  'AccountNumber' => getProperty('billaccount'),
                                                                                                                  'CountryCode' => 'IN'
                                                                                                                  )
                                                                                      )
                                                                     );
    $request1['RequestedShipment']['CustomsClearanceDetail'] = array(
                                                                     'CustomsValue' => array(
                                                                                             'Currency'=>'INR',
                                                                                             'Amount'=>100
                                                                                             ),
                                                                     'CommercialInvoice' => array(
                                                                                                  'Purpose' => "SOLD"
                                                                                                  )
                                                                     );
    $request1['RequestedShipment']['PackageCount'] = '1';
    $request1['RequestedShipment']['RequestedPackageLineItems'] = array(
                                                                        'SequenceNumber'=>1,
                                                                        'GroupPackageCount'=>1,
                                                                        'Weight' => array(
                                                                                          'Value' => "$dimension[we]",
                                                                                          'Units' => 'LB'
                                                                                          ),
                                                                        'Dimensions' => array(
                                                                                              'Length' => "$dimension[l]",
                                                                                              'Width' => "$dimension[b]",
                                                                                              'Height' => "$dimension[h]",
                                                                                              'Units' => 'IN'
                                                                                              )
                                                                        );
    
    
    
    
       
        try {
            
            if(setEndpoint('changeEndpoint')){
                
                $newLocation = $client1->__setLocation(setEndpoint('endpoint'));
                
            }
            
           
            
            $response1 = $client1 -> getRates($request1);
            
            if ($response1 -> HighestSeverity != 'FAILURE' && $response1 -> HighestSeverity != 'ERROR'){
                $rateReply = $response1 -> RateReplyDetails;
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
                
                /*$convert_date = new DateTime($ExpectedDatePOD);
                $ExpectedDatePOD = date_format($convert_date, 'd-M-Y');
                
                $diff = abs(strtotime($ExpectedDatePOD) - strtotime($pickup_date1));
                
                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));*/
                
               
                $EstimateDeliveryDate=$tat;
                
                //printSuccess($client1, $response1);
            }else{
                //printError($client1, $response1);
            }
            writeToLog($client1);    // Write to log file
            
        } catch (SoapFault $exception) {
            printFault($exception, $client1);
        }
    
   
    ?>
