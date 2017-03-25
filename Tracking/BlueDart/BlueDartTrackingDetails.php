<?php
    
    $key='4ac1e14526f0e9f4a4d91af49404ca2a';
    $loginid='BOM05559';
    
    $scan=1;
    
    
    //$AWBNo="69534367302";
    //$UID="115782";
    
    $URL="http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=$loginid&awb=awb&numbers=$AWBNo&format=xml&lickey=$key&verno=1.3f&scan=$scan";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10);
    
    
    
    if(curl_exec($ch) === false)
    {
        $response= curl_error($ch);
        
    }
    else
    {
        $response=curl_exec($ch);
        
        
        $oXML = new SimpleXMLElement($response);
        
        $TrackingCode= $oXML->Shipment->Scans->ScanDetail->ScanCode[0];
        
        if($oXML->Shipment->StatusType!="NF"){
            
            $ExpectedDeliveryDate = date('Y-m-d',strtotime($oXML->Shipment->ExpectedDeliveryDate));
            
            $PickupDate= date('Y-m-d',strtotime($oXML->Shipment->PickUpDate));
            $Pickuphour=substr($oXML->Shipment->PickUpTime,0,2);
            $Pickupmin=substr($oXML->Shipment->PickUpTime,2);
            $PickupTimestamp=$PickupDate."T".$Pickuphour.":".$Pickupmin;
            
            $StatusDate= date('Y-m-d',strtotime($oXML->Shipment->Scans->ScanDetail->ScanDate[0]));
            
            $StatusChangeTimestamp=$StatusDate."T".$oXML->Shipment->Scans->ScanDetail->ScanTime[0];
            
            
            $Location=$oXML->Shipment->Scans->ScanDetail->ScannedLocation[0];
            
            
            try{
                
                
                
                $stmt3= $conn->prepare("UPDATE `Tracking` SET `PickedUpDate` = '$PickupTimestamp', `DeliveredDate`='' ,`Code`='$TrackingCode',`CurrentLocation`='$Location' WHERE AWB_Number=$AWBNo");
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
                    
                    foreach($oXML->Shipment->Scans->ScanDetail as $key=>$value){
                        
                        
                        $TimeStamp=$value->ScanDate."T".$value->ScanTime.":00";
                        
                        $TrackingCreationTime=strtotime($TimeStamp);
                        
                        
                        if($TrackingCreationTime>$LastTrackingTime){
                            
                            $TrackingCreationTime=date("Y-m-d H:i:s", strtotime($TimeStamp));
                            
                            $TrackingCode=$value->ScanCode;
                            $City=$value->ScannedLocation;
                            
                            $stmt5= $conn->prepare("INSERT INTO `TrackingHistory`(`UID`, `Code`,`TrackingTime`,`Place`) VALUES ('$UID','$TrackingCode','$TrackingCreationTime','$City')");
                            $stmt5->execute();
                            
                        }
                        
                        //break;
                    }
                    
                }
                
                elseif($numrows==0){
                    
                    foreach($oXML->Shipment->Scans->ScanDetail as $key=>$value){
                        
                        $TimeStamp=$value->ScanDate."T".$value->ScanTime.":00";
                        
                        $TrackingCreationTime=date("Y-m-d H:i:s", strtotime($TimeStamp));
                        
                        $TrackingCode=$value->ScanCode;
                        $City=$value->ScannedLocation;
                        
                        //echo $TrackingCreationTime."=======>".$TimeStamp."------>". strtotime($TrackingCreationTime)."<br>";
                        
                        $stmt5= $conn->prepare("INSERT INTO `TrackingHistory`(`UID`, `Code`,`TrackingTime`,`Place`) VALUES ('$UID','$TrackingCode','$TrackingCreationTime','$City')");
                        $stmt5->execute();
                        
                    }
                    
                    
                }
                
                
            }
            catch(PDOException $e) {
              //  echo "Error: " . $e->getMessage();
            }
            
        }
        
       
        
        
    }
    
    
    
    
    curl_close($ch);
    
    
    if (!$logfile = fopen(__DIR__.'/BlueDarttransactions.log', "a"))
    {
        print_r("Cannot open " . __DIR__.'/BlueDarttransactions.log' . " file.\n", 0);
        exit(1);
    }
    fwrite($logfile, sprintf("\r%s:- %s",date_default_timezone_set('America/Los_Angeles'), $URL."\r\n\r\n".$response."\r\n\r\n"));
    
    
    
    
    
    
    
    
    
    
    
    
    
    ?>
