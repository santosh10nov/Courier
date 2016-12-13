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
    'BuildingPartCode' => 'SUITE', // valid values APARTMENT, BUILDING, DEPARTMENT, SUITE, FLOOR and ROOM
    'BuildingPartDescription' => '3B',
    'ReadyTimestamp' => '2016-12-09T09:20:16', // Replace with your ready date time
    'CompanyCloseTime' => '20:00:00'
);
$request['PackageCount'] = $NumberofPieces;
    $request['TotalWeight'] = array(
'Value' => $Weight,
'Units' => 'LB' // valid values LB and KG
);
$request['CarrierCode'] = 'FDXE'; // valid values FDXE-Express, FDXG-Ground, FDXC-Cargo, FXCC-Custom Critical and FXFR-Freight
//$request['OversizePackageCount'] = '1';
$request['CourierRemarks'] = 'This is a test.  Do not pickup';



try {
	if(setEndpoint('changeEndpoint')){
		$newLocation = $client->__setLocation(setEndpoint('endpoint'));
	}
	
	$response = $client ->createPickup($request);

    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
        echo 'Pickup confirmation number is: '.$response -> PickupConfirmationNumber .Newline;
        echo 'Location: '.$response -> Location .Newline;
        $Pickup_Number=$response -> PickupConfirmationNumber;
        $Scheduled_Pickup_Date=$row["pickup_book_date"];
        printSuccess($client, $response);
        $stmt3= $conn->prepare("UPDATE `pickup` SET `Pickup_Number` = '$Pickup_Number', `Scheduled_Pickup_Date`='$Scheduled_Pickup_Date' ,`Pickup_Status`='Success' WHERE `AWB_Number`='$AWBNo'");
        $stmt3->execute();
        
    }else{
        printError($client, $response);
    } 
    
    
    
    writeToLog($client);    // Write to log file   
} catch (SoapFault $exception) {
    printFault($exception, $client);
    printSuccess($client, $response);              
}
?>
