<?php
    
    $key='4ac1e14526f0e9f4a4d91af49404ca2a';
    $loginid='BOM05559';
    
    $scan=1;
    
    require_once 'dbconfig.php';
    
    //$AWBNo="69534598276";
    //$UID="9";
    
    
    
    
    
    
    $URL="http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=$loginid&awb=awb&numbers=$AWBNo&format=xml&lickey=$key&verno=1.3f&scan=$scan";
    
    
    //$context = stream_context_create($opts);
    
    //$oXML = new SimpleXMLElement(file_get_contents($URL,false,$context));*/
    
    
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
        
        print_r($oXML);
        
        $TrackingCode= $oXML->Shipment->Scans->ScanDetail->ScanCode[0];
        
        if($oXML->Shipment->StatusType!="NF"){
            
            $ExpectedDeliveryDate = date('Y-m-d',strtotime($oXML->Shipment->ExpectedDeliveryDate));
            
            $PickupDate= date('Y-m-d',strtotime($oXML->Shipment->PickUpDate));
            $Pickuphour=substr($oXML->Shipment->PickUpTime,0,2);
            $Pickupmin=substr($oXML->Shipment->PickUpTime,2);
            $PickupTimestamp=$PickupDate."T".$Pickuphour.":".$Pickupmin;
            
            $DeliveredDate="";
            
            $StatusDate= date('Y-m-d',strtotime($oXML->Shipment->Scans->ScanDetail->ScanDate[0]));
            
            $StatusChangeTimestamp=$StatusDate."T".$oXML->Shipment->Scans->ScanDetail->ScanTime[0];
            
            $ExpectedDeliveryDate=date('Y-m-d H:i:s',strtotime($oXML->Shipment->ExpectedDeliveryDate));
            
            if($oXML->Shipment->Scans->ScanDetail->Scan[0]=="SHIPMENT DELIVERED"){
                
                $DeliveredDate=$StatusChangeTimestamp;
                
            }
            
            
            $Location=$oXML->Shipment->Scans->ScanDetail->ScannedLocation[0];
                 
            
            try{
                
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt3= $conn->prepare("UPDATE `Tracking` SET `PickedUpDate` = '$PickupTimestamp', `DeliveredDate`='$DeliveredDate',`EstimateDeliveryDate`='$ExpectedDeliveryDate' ,`Code`='$TrackingCode',`CurrentLocation`='$Location',`StatusChangeTimestamp`='$StatusChangeTimestamp' WHERE AWB_Number=$AWBNo");
                $stmt3->execute();
                
                /*$stmt4= $conn->prepare("SELECT * FROM TrackingHistory  WHERE TrackingHistory.Code = '$TrackingCode' and TrackingHistory.UID='$UID'");
                 $stmt4->execute();
                 
                 $numrows = $stmt4->rowCount();
                 
                 if($numrows==0){
                 
                 $stmt5= $conn->prepare("INSERT INTO `TrackingHistory`(`UID`, `Code`, `TrackingTime`) VALUES ('$UID','$TrackingCode','$StatusChangeTimestamp')");
                 $stmt5->execute();
                 
                 }*/
                
                
            }
            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            
        }
        
        $conn=null;
        
        
    }
    
    
    
    
    curl_close($ch);
    
    
    if (!$logfile = fopen(__DIR__.'/BlueDarttransactions.log', "a"))
    {
        print_r("Cannot open " . __DIR__.'/BlueDarttransactions.log' . " file.\n", 0);
        exit(1);
    }
    fwrite($logfile, sprintf("\r%s:- %s",date_default_timezone_set('America/Los_Angeles'), $URL."\r\n\r\n".$response."\r\n\r\n"));
    
    
    
    
    
    
    
    
    
    
    
    
    
    ?>
