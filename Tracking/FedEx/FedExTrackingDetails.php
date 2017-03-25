<?php
    // Copyright 2009, FedEx Corporation. All rights reserved.
    // Version 6.0.0
    
    require_once('/Applications/XAMPP/xamppfiles/htdocs/Courier/Tracking/FedEx/fedex-common.php5');
    
    //The WSDL is not included with the sample code.
    //Please include and reference in $path_to_wsdl variable.
    $path_to_wsdl = "/Applications/XAMPP/xamppfiles/htdocs/Courier/Tracking/FedEx/TrackService_v12.wsdl";
    
    ini_set("soap.wsdl_cache_enabled", "0");
    
    $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
    
    
    
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
    $request['TransactionDetail'] = array('CustomerTransactionId' => '*** Track Request using PHP ***');
    $request['Version'] = array(
                                'ServiceId' => 'trck',
                                'Major' => '12',
                                'Intermediate' => '0',
                                'Minor' => '0'
                                );
    $request['SelectionDetails'] = array(
                                         'PackageIdentifier' => array(
                                                                      'Type' => 'TRACKING_NUMBER_OR_DOORTAG',
                                                                      'Value' => $AWBNo // Replace 'XXX' with a valid tracking identifier
                                                                      )
                                         );
    
    $request['ProcessingOptions'] ='INCLUDE_DETAILED_SCANS';
    
    try {
        if(setEndpoint('changeEndpoint')){
            $newLocation = $client->__setLocation(setEndpoint('endpoint'));
        }
        
        $response = $client ->track($request);
        
        if ($response->CompletedTrackDetails->TrackDetails->Notification->Code==0){
            
            $TrackingCode= $response->CompletedTrackDetails->TrackDetails->StatusDetail->Code;
            
            
            
            if($TrackingCode!=""){
                
                $DeliveryDate=NULL;
                $PickupDate=NULL;
                $EstimateDelivery=NULL;
                $TrackingCreationTime= $response->CompletedTrackDetails->TrackDetails->StatusDetail->CreationTime;
                $Location="";
                
                if($TrackingCode=="DL"){
                    
                    foreach($response->CompletedTrackDetails->TrackDetails->DatesOrTimes as $key=>$value){
                        
                        if($value->Type=="ACTUAL_DELIVERY"){
                            
                            $DeliveryDate=$value->DateOrTimestamp;
                        }
                        elseif($value->Type=="ACTUAL_PICKUP"){
                            
                            $PickupDate=$value->DateOrTimestamp;
                            
                        }
                        
                    }
                    
                    $TrackingStatus="Delivered";
                    
                    $Location=$response->CompletedTrackDetails->TrackDetails->StatusDetail->Location->City;
                    
                    
                    
                    
                }
                
                elseif(in_array($TrackingCode,["AA","AC","AF","AR","AX","DS","EA","ED","EO","HL","IT","IX","OF","OX","PL","PM","DP"])){
                    
                    
                    foreach($response->CompletedTrackDetails->TrackDetails->DatesOrTimes as $key=>$value){
                        
                        if($value->Type=="ACTUAL_PICKUP"){
                            
                            $PickupDate=$value->DateOrTimestamp;
                            
                        }
                        elseif($value->Type=="ESTIMATED_DELIVERY"){
                            
                            $EstimateDelivery=$value->DateOrTimestamp;
                        }
                        
                    }
                    
                    $TrackingStatus="In Transit";
                    
                    $Location=$response->CompletedTrackDetails->TrackDetails->StatusDetail->Location->City;
                    
                    $Message=$response->CompletedTrackDetails->TrackDetails->StatusDetail->Description;
                    
                }
                
                
                elseif(in_array($TrackingCode,["OD","AD"])){
                    
                    
                    foreach($response->CompletedTrackDetails->TrackDetails->DatesOrTimes as $key=>$value){
                        
                        if($value->Type=="ACTUAL_PICKUP"){
                            
                            $PickupDate=$value->DateOrTimestamp;
                            
                        }
                        elseif($value->Type=="ESTIMATED_DELIVERY"){
                            
                            $EstimateDelivery=$value->DateOrTimestamp;
                        }
                        
                    }
                    
                    $TrackingStatus="Out For Delivery";
                    
                    $Location=$response->CompletedTrackDetails->TrackDetails->StatusDetail->Location->City;
                    
                    $Message=$response->CompletedTrackDetails->TrackDetails->StatusDetail->Description;
                    
                }
                
                
                elseif($TrackingCode=="OC"){
                    
                    $TrackingStatus="AWB Created";
                    
                    
                }
                
                elseif(in_array($TrackingCode,["AP","LO","PU","PX"])){
                    
                    $TrackingStatus="Picked Up";
                    
                    
                }
                
                elseif(in_array($TrackingCode,["DD","DE","DR","DY","PD","SE","CD","CC","CP"])){
                    
                    
                    foreach($response->CompletedTrackDetails->TrackDetails->DatesOrTimes as $key=>$value){
                        
                        if($value->Type=="ACTUAL_PICKUP"){
                            
                            $PickupDate=$value->DateOrTimestamp;
                            
                        }
                        elseif($value->Type=="ESTIMATED_DELIVERY"){
                            
                            $EstimateDelivery=$value->DateOrTimestamp;
                        }
                        
                    }
                    
                    $TrackingStatus="Exception";
                    
                    $Location=$response->CompletedTrackDetails->TrackDetails->StatusDetail->Location->City;
                    
                    $Message=$response->CompletedTrackDetails->TrackDetails->StatusDetail->Description;
                    
                    
                }
                
                
                
                
                
                try{
                    
                 
                    $stmt3= $conn->prepare("UPDATE `Tracking` SET `PickedUpDate` = '$PickupDate', `DeliveredDate`='$DeliveryDate' ,`Code`='$TrackingCode',`CurrentLocation`='$Location' WHERE AWB_Number=$AWBNo");
                    $stmt3->execute();
                    
                    
                    ///////////////////////////////////////////////////
                    ///////////////////Adding Track Details/////////////
                    ////////////////////////////////////////////////////
                    
                    
                    $stmt4= $conn->prepare("SELECT TrackingTime FROM TrackingHistory WHERE TrackingHistory.UID='$UID' order by `TrackingTime` desc limit 1 ");
                    $stmt4->execute();
                    
                    $row=$stmt4->fetch();
                    
                    $LastTrackingTime=strtotime($row['TrackingTime']);
                    
                    
                    $numrows = $stmt4->rowCount();
                    
                    
                    if($numrows>0){
                        
                        foreach($response->CompletedTrackDetails->TrackDetails->Events as $key=>$value){
                            
                            
                            $TrackingCreationTime=strtotime(substr($value->Timestamp,0,19));
                            
                            if($TrackingCreationTime>$LastTrackingTime){
                                $TrackingCreationTime=$value->Timestamp;
                                $TrackingCode=$value->EventType;
                                $City=$value->Address->City;
                                
                                $stmt5= $conn->prepare("INSERT INTO `TrackingHistory`(`UID`, `Code`,`TrackingTime`,`Place`) VALUES ('$UID','$TrackingCode','$TrackingCreationTime','$City')");
                                $stmt5->execute();
                                
                            }
                            
                            //break;
                        }
                        
                    }
                    
                    elseif($numrows==0){
                        
                        foreach($response->CompletedTrackDetails->TrackDetails->Events as $key=>$value){
                            
                            
                            $TrackingCreationTime=$value->Timestamp;
                            $TrackingCode=$value->EventType;
                            if($TrackingCode=="OC"){
                                $City="";
                            }
                            else{
                                $City=$value->Address->City;
                            }
                            
                            $stmt5= $conn->prepare("INSERT INTO `TrackingHistory`(`UID`, `Code`,`TrackingTime`,`Place`) VALUES ('$UID','$TrackingCode','$TrackingCreationTime','$City')");
                            $stmt5->execute();
                            
                        }
                        
                        
                    }
                    
                    
                    
                }
                catch(PDOException $e) {
                    //echo "Error: " . $e->getMessage();
                }
                
                
            }
            
            
            
            //printSuccess($client, $response);
        }
        else{
            //printError($client, $response);
        }
        
        writeToLog($client);    // Write to log file
    } catch (SoapFault $exception) {
        //printFault($exception, $client);
    }
    ?>



