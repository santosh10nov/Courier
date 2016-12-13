<?php 
      /*
       session_start();
       echo ($session = session_name() . '=' . session_id());
      */
      /*
		   #echo Start of Soap1.1 (Basic_Http_Version)
	 		$soap = new DebugSoapClient('http://netconnect.bluedart.com/Demo/ShippingAPI/Pickup/PickupRegistrationService.svc?wsdl',
				array(
				'trace' 							=> 1,  
				'style'								=> SOAP_DOCUMENT,
				'use'									=> SOAP_LITERAL,
				'soap_version' 				=> SOAP_1_1
				));
				
				$soap->__setLocation("http://netconnect.bluedart.com/Demo/ShippingAPI/Pickup/PickupRegistrationService.svc/Basic");
				
				$soap->sendRequest = true;
				$soap->printRequest = false;
				$soap->formatXML = true; 
				 
			#echo "end of Soap 1.1 version setting" 
	   */
	   
	   
		  #echo "Start  of Soap 1.2 version (ws_http_Binding)  setting";
    
    
            $servername = "127.0.0.1";
            $username = "root";
            $pass = "yesbank";
            $dbname = "transporter";
    
    
	 		$soap = new DebugSoapClient('http://netconnect.bluedart.com/Demo/ShippingAPI/Pickup/PickupRegistrationService.svc?wsdl',
				array(
				'trace' 							=> 1,  
				'style'								=> SOAP_DOCUMENT,
				'use'									=> SOAP_LITERAL,
				'soap_version' 				=> SOAP_1_2
				));
				
				$soap->__setLocation("http://netconnect.bluedart.com/Demo/ShippingAPI/Pickup/PickupRegistrationService.svc");
				
				$soap->sendRequest = true;
				$soap->printRequest = false;
				$soap->formatXML = true; 
				
				$actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IPickupRegistration/RegisterPickup',true);
				$soap->__setSoapHeaders($actionHeader);
			#echo "end of Soap 1.2 version (ws_http_Binding)  setting"
    
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// Area Code /////////////////////////
    ////////////////////////////////////////////////////////////////

    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt1 = $conn->prepare("SELECT * FROM `bluedart_service_avi_main` WHERE pincode= $pincode ");
        $stmt1->execute();
        $stmt1->setFetchMode(PDO::FETCH_ASSOC);
        $row1 = $stmt1->fetch();
        
        
        $AreaCode=$row1["AreaCode"];
        
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// Product Code /////////////////////////
    ////////////////////////////////////////////////////////////////
   
        $stmt2 = $conn->prepare(" SELECT * FROM `AirwayBill` WHERE `Airwaybill_Number`= $AWBNo");
        $stmt2->execute();
        $stmt2->setFetchMode(PDO::FETCH_ASSOC);
        $row2 = $stmt2->fetch();
        
        $service=$row2["CourierService"];
    
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    
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
    
    
    
    $params = array(
			         'request' => 
										array ( 
									 				  'AreaCode' =>'BOM',
														'ContactPersonName' =>$name,
														'CustomerAddress1' => $address,
														'CustomerAddress2' => $city.",".$state,
														'CustomerName' => $com_name,
														'CustomerPincode' => $pincode,
														'CustomerTelephoneNumber' =>$phone,
														'EmailID' => 'a@b.com',
														'MobileTelNo' => $phone,
                                               
                                                        'CustomerCode' => '359181',
                                                        'ProductCode' => $product,
                                                        'ReferenceNo' => $AWBNo,
                                                        'Remarks' => $AWBNo,
                                             
                                                        'OfficeCloseTime' => $comp_closing_time,
														'ShipmentPickupDate' => $pickupdate,
														'ShipmentPickupTime' => $pickuptime,
                                                        'NumberofPieces' =>$NumberofPieces ,
                                                        'VolumeWeight' =>$Weight ,
                                                        'WeightofShipment' =>$Weight ,
                                                        'isToPayShipper' => 'false'),
								'profile' => 
										 array(
                                               'Api_type' => 'S',
                                               'LicenceKey'=>'4ac1e14526f0e9f4a4d91af49404ca2a',
                                               'LoginID'=>'BOM05559',
                                               'Version'=>'1.3')
											);
						
$result = $soap->__soapCall('RegisterPickup',array($params));

/*
echo '<h5> TokenNo : ' ;
echo $result->RegisterPickupResult->TokenNumber;
echo ' </h5> <h5> Error Message : ' ;
echo $result->RegisterPickupResult->IsError;
echo '</h5>' ;
 */
 
//echo "<br>";
echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';

    
    
    $Pickup_Number=$result->RegisterPickupResult->TokenNumber;
    $Scheduled_Pickup_Date=substr($result->RegisterPickupResult->ShipmentPickupDate,0,10);
    
    //echo $Scheduled_Pickup_Date;
    
    
    if($result->RegisterPickupResult->IsError!=1){
        
        $stmt3= $conn->prepare("UPDATE `pickup` SET `Pickup_Number` = '$Pickup_Number', `Scheduled_Pickup_Date`='$Scheduled_Pickup_Date' ,`Pickup_Status`='Success' WHERE `AWB_Number`='$AWBNo'");
        $stmt3->execute();
        
    }
  

 

