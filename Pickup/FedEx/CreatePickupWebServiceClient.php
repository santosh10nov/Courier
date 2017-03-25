<?php
// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 5.0.0

require_once('fedex-common.php5');

//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = "/Applications/XAMPP/xamppfiles/htdocs/Courier/Pickup/FedEx/PickupService_v13.wsdl";

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

    
    try{
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt1 = $conn->prepare("Select * from fedex_service_avi where `pincode`=$pincode");
        $stmt1->execute();
        $stmt1->setFetchMode(PDO::FETCH_ASSOC);
        $row1 = $stmt1->fetch();
        
        $ProvinceCode=$row1['p_DestinationAirportId'];
        
        $ReadyTimestamp=$pickupdate."T".$pickuptime;
        
    }
    catch(PDOException $e) {
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
$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Create Pickup Request using PHP ***');
$request['Version'] = array(
	'ServiceId' => 'disp', 
	'Major' => 13, 
	'Intermediate' => 0, 
	'Minor' => 0
);
$request['OriginDetail'] = array(
	'PickupLocation' => array(
		'Contact' => array(
			'PersonName' =>$name ,
          	'CompanyName' =>$address ,
        	'PhoneNumber' => $phone
        ),
      	'Address' => array(
      		'StreetLines' => array($address),
          	'City' => $city,
          	'StateOrProvinceCode' =>$ProvinceCode,
         	'PostalCode' => $pincode,
           	'CountryCode' => 'IN')
       	),
   	'PackageLocation' => 'FRONT', // valid values NONE, FRONT, REAR and SIDE
    'BuildingPartCode' => 'BUILDING', // valid values APARTMENT, BUILDING, DEPARTMENT, SUITE, FLOOR and ROOM
    'BuildingPartDescription' => '',
    'ReadyTimestamp' => $pickupdatetime, // Replace with your ready date time
    'CompanyCloseTime' => $row["com_closing_time"]
);
$request['PackageCount'] = $NumberofPieces;
$request['TotalWeight'] = array(
'Value' => $Weight,
'Units' => 'LB' // valid values LB and KG
);
$request['CarrierCode'] = 'FDXE'; // valid values FDXE-Express, FDXG-Ground, FDXC-Cargo, FXCC-Custom Critical and FXFR-Freight
//$request['OversizePackageCount'] = $couriercount;
$request['CourierRemarks'] = '';



try {
	if(setEndpoint('changeEndpoint')){
		$newLocation = $client->__setLocation(setEndpoint('endpoint'));
	}
	
	$response = $client ->createPickup($request);

    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
        //echo 'Pickup confirmation number is: '.$response -> PickupConfirmationNumber .Newline;
        //echo 'Location: '.$response -> Location .Newline;
        
        $Pickup_Number=$response -> Location.$response -> PickupConfirmationNumber;
        $Scheduled_Pickup_Date=$pickupdate;
        $stmt3= $conn->prepare("UPDATE `pickup` SET `Pickup_Number` = '$Pickup_Number', `Scheduled_Pickup_Date`='$Scheduled_Pickup_Date' ,`Pickup_Status`='Scheduled' WHERE AWB_Number=$AWBNo");
        $stmt3->execute();
        
        
    
        
        
        /////////////////////////////////////////////////////////////////////
        ////////Insert Data in Tracking & Tracking History Table/////////////
        /////////////////////////////////////////////////////////////////////
        
        $stmt4= $conn->prepare(" INSERT INTO `Tracking`(`AWB_Number`, `UID`, `CourierVendor`, `Code`,  `StatusChangeTimestamp`,`UpdatedBy`) VALUES ('$AWBNo','$UID','$couriervendor','PSD',NOW(),'Santy') ");
        $stmt4->execute();
        $stmt5= $conn->prepare(" INSERT INTO `TrackingHistory`(`UID`, `Code`, `TrackingTime`) VALUES ('$UID','PSD',NOW())");
        $stmt5->execute();
        
        $Status="Scheduled";
        
        
        //echo $Pickup_Number;
        
        //printSuccess($client, $response);
        
    }else{
        //printError($client, $response);
        
        $stmt2 = $conn->prepare("UPDATE `pickup` SET pickup.is_locked=0 WHERE AWB_Number=$AWBNo");
        $stmt2->execute();
    } 
    //printFault($client, $exception);
    //printSuccess($client, $response);
    
    
    writeToLog($client);    // Write to log file   
} catch (SoapFault $exception) {
   
}
    


    
?>
