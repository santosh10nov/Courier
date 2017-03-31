<?php
    /*
     #echo Start of Soap1.1 (Basic_Http_Version)
     $soap = new DebugSoapClient('http://netconnect.bluedart.com/Demo/ShippingAPI/Finder/ServiceFinderQuery.svc?wsdl',
     array(
     'trace' 							=> 1,
     'style'								=> SOAP_DOCUMENT,
     'use'									=> SOAP_LITERAL,
     'soap_version' 				=> SOAP_1_1
     ));
     $soap->__setLocation("http://netconnect.bluedart.com/Demo/ShippingAPI/Finder/ServiceFinderQuery.svc/basic");
     
     $soap->sendRequest = true;
     $soap->printRequest = false;
     $soap->formatXML = true;
     #echo End of Soap1.1 (Basic_Http_Version)
     */
    
    
    #echo Start of Soap1.2 (WS_Http_Version)
				$soap = new DebugSoapClient('https://netconnect.bluedart.com/Ver1.7/ShippingAPI/Finder/ServiceFinderQuery.svc?wsdl',
                                            array(
                                                  'trace' 							=> 1,
                                                  'style'								=> SOAP_DOCUMENT,
                                                  'use'									=> SOAP_LITERAL,
                                                  'soap_version' 				=> SOAP_1_2
                                                  ));
				
				$soap->__setLocation("https://netconnect.bluedart.com/Ver1.7/ShippingAPI/Finder/ServiceFinderQuery.svc?wsdl");
				
				$soap->sendRequest = true;
				$soap->printRequest = false;
				$soap->formatXML = true;
				
				
				$actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IServiceFinderQuery/GetDomesticTransitTimeForPinCodeandProduct',true);
				$soap->__setSoapHeaders($actionHeader);
    
    #echo End of Soap1.2 (ws_Http_Version)
    
    // DB ////////
    /////////////
    require_once 'dbconfig.php';
    
  
    //$from_pin=400069;
    //$to_pin=324001;
    //$pickup_date="2016-06-20";
    //$pickup_time="10:00";
    
    if($service_avi_level4!=0){
        $product_type1="Apex";
        $product_code1="A";
        $sub_product1="";
    
    
    $paramsLive = array('pPinCodeFrom' => "$from_pin",
                        'pPinCodeTo' =>"$to_pin",
                        'pProductCode' =>"$product_code1",
                        'pSubProductCode' =>"$sub_product1",
                        'pPudate'=>"$pickup_date",
                        'pPickupTime'=>"$bd_pickup_time",
                        'profile' =>
                        array(
                              'Api_type' => 'S',
                              'LicenceKey'=>'13a81f7d2d83aaf1a417322a27f8e10a',
                              'LoginID'=>'BOM05559',
                              'Version'=>'1.3')
                        );
    
    $params = array('pPinCodeFrom' => '560011',
                    'pPinCodeTo' =>'',
                    'pProductCode' =>'',
                    'pSubProductCode' =>'',
                    'pPudate'=>'',
                    'pPickupTime'=>'',
                    'profile' =>
                    array(
                          'Api_type' => 'S',
                          'Area'=>'BOM',
                          'Customercode'=>'369342',
                          'LicenceKey'=>'13a81f7d2d83aaf1a417322a27f8e10a',
                          'LoginID'=>'BOM05559',
                          'Password'=>'',
                          'Version'=>'1.3')
                    );
    
    /* var_dump($paramsLive);
     echo '<h2>Parameters</h2><pre>'; print_r($paramsLive); echo '</pre>';*/
    // Here I call my external function
    $result = $soap->__soapCall('GetDomesticTransitTimeForPinCodeandProduct',array($paramsLive)); // this line is important 
    //echo "<br>";
    //var_dump($result);
    
    //echo $result->GetServicesforPincodeResult->ErrorMessage ;
    //echo "<br>";
    //echo $result->GetServicesforPincodeResult->PincodeDescription;
    //echo "<br>";

    
    
    //echo "<br>";
    
    
    $AdditionalDays = $result->GetDomesticTransitTimeForPinCodeandProductResult->AdditionalDays;
    $CityDesc_Destination = $result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Destination;
    $CityDesc_Origin = $result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Origin;
    $ExpectedDateDelivery = $result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDateDelivery;
    $ExpectedDatePOD = $result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDatePOD;
    
    
    $diff = abs(strtotime($ExpectedDatePOD) - strtotime($pickup_date));
    
    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    
    
    $convert_date = new DateTime($ExpectedDatePOD);
    $ExpectedDatePOD = date_format($convert_date, 'd-M-Y');
    $convert_date = new DateTime($ExpectedDateDelivery);
    $ExpectedDateDelivery = date_format($convert_date, 'd-M-Y');
    

    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt1 = $conn->prepare("INSERT INTO `transit_time` (`vendor_id`, `vendor_name`, `product_type`, `sub_product`, `pickup_date`, `pickup_Time`, `from_pin`, `to_pin`, `ExpectedDateDelivery`, `ExpectedDatePOD`, `TAT`, `AdditionalDays`,`added_date`,`added_by`) VALUES ('1', 'BlueDart', '$product_type1', '$sub_product1', '$pickup_date1', '$pickup_time', '$from_pin', '$to_pin', '$ExpectedDateDelivery', '$ExpectedDatePOD', '$days', '$AdditionalDays','$nowtime','$userid');");
        $stmt1->execute();
        
    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    }
    
    ///////////////////////////////////////////////
    //////////////Domestic Priority///////////////
    //////////////////////////////////////////////
    
    
    if($service_avi_level5!=0){
        
        $product_type2="Domestic Priority";
        $product_code2="A";
        $sub_product2="";
        
        $paramsLive = array('pPinCodeFrom' => "$from_pin",
                            'pPinCodeTo' =>"$to_pin",
                            'pProductCode' =>"$product_code2",
                            'pSubProductCode' =>"$sub_product2",
                            'pPudate'=>"$pickup_date",
                            'pPickupTime'=>"$bd_pickup_time",
                            'profile' =>
                            array(
                                  'Api_type' => 'S',
                                  'LicenceKey'=>'13a81f7d2d83aaf1a417322a27f8e10a',
                                  'LoginID'=>'BOM05559',
                                  'Version'=>'1.3')
                            );

        
        // Here I call my external function
        $result = $soap->__soapCall('GetDomesticTransitTimeForPinCodeandProduct',array($paramsLive)); // this line is important
        
        $AdditionalDays = $result->GetDomesticTransitTimeForPinCodeandProductResult->AdditionalDays;
        $CityDesc_Destination = $result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Destination;
        $CityDesc_Origin = $result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Origin;
        $ExpectedDateDelivery = $result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDateDelivery;
        $ExpectedDatePOD = $result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDatePOD;
        
        
        $diff = abs(strtotime($ExpectedDatePOD) - strtotime($pickup_date));
        
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        
        
        $convert_date = new DateTime($ExpectedDatePOD);
        $ExpectedDatePOD = date_format($convert_date, 'd-M-Y');
        $convert_date = new DateTime($ExpectedDateDelivery);
        $ExpectedDateDelivery = date_format($convert_date, 'd-M-Y');
        
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt1 = $conn->prepare("INSERT INTO `transit_time` (`vendor_id`, `vendor_name`, `product_type`, `sub_product`, `pickup_date`, `pickup_Time`, `from_pin`, `to_pin`, `ExpectedDateDelivery`, `ExpectedDatePOD`, `TAT`, `AdditionalDays`,`added_date`,`added_by`) VALUES ('1', 'BlueDart', '$product_type2', '$sub_product2', '$pickup_date1', '$pickup_time', '$from_pin', '$to_pin', '$ExpectedDateDelivery', '$ExpectedDatePOD', '$days', '$AdditionalDays','$nowtime','$userid');");
            $stmt1->execute();
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    }
    
    
    ///////////////////////////////////////////////
    //////////////Surface///////////////
    //////////////////////////////////////////////
    
    
    if($service_avi_level6!=0){
        
        $product_type3="Surface";
        $product_code3="E";
        $sub_product3="";
        
        $paramsLive = array('pPinCodeFrom' => "$from_pin",
                            'pPinCodeTo' =>"$to_pin",
                            'pProductCode' =>"$product_code3",
                            'pSubProductCode' =>"$sub_product3",
                            'pPudate'=>"$pickup_date",
                            'pPickupTime'=>"$bd_pickup_time",
                            'profile' =>
                            array(
                                  'Api_type' => 'S',
                                  'LicenceKey'=>'13a81f7d2d83aaf1a417322a27f8e10a',
                                  'LoginID'=>'BOM05559',
                                  'Version'=>'1.3')
                            );
        
        
        // Here I call my external function
        $result = $soap->__soapCall('GetDomesticTransitTimeForPinCodeandProduct',array($paramsLive)); // this line is important
        
        $AdditionalDays = $result->GetDomesticTransitTimeForPinCodeandProductResult->AdditionalDays;
        $CityDesc_Destination = $result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Destination;
        $CityDesc_Origin = $result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Origin;
        $ExpectedDateDelivery = $result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDateDelivery;
        $ExpectedDatePOD = $result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDatePOD;
        
        
        $diff = abs(strtotime($ExpectedDatePOD) - strtotime($pickup_date));
        
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        
        
        $convert_date = new DateTime($ExpectedDatePOD);
        $ExpectedDatePOD = date_format($convert_date, 'd-M-Y');
        $convert_date = new DateTime($ExpectedDateDelivery);
        $ExpectedDateDelivery = date_format($convert_date, 'd-M-Y');
        
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt1 = $conn->prepare("INSERT INTO `transit_time` (`vendor_id`, `vendor_name`, `product_type`, `sub_product`, `pickup_date`, `pickup_Time`, `from_pin`, `to_pin`, `ExpectedDateDelivery`, `ExpectedDatePOD`, `TAT`, `AdditionalDays`,`added_date`,`added_by`) VALUES ('1', 'BlueDart', '$product_type3', '$sub_product3', '$pickup_date1', '$pickup_time', '$from_pin', '$to_pin', '$ExpectedDateDelivery', '$ExpectedDatePOD', '$days', '$AdditionalDays','$nowtime','$userid');");
            $stmt1->execute();
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    

    
    /////////////////////////////////////////////
    //////////////////Apex COD//////////////////
    ////////////////////////////////////////////
    
    if($service_avi_level7!=0){
        
        $product_type4="Apex COD";
        $product_code4="A";
        $sub_product4="C";
        
        $paramsLive = array('pPinCodeFrom' => "$from_pin",
                            'pPinCodeTo' =>"$to_pin",
                            'pProductCode' =>"$product_code4",
                            'pSubProductCode' =>"$sub_product4",
                            'pPudate'=>"$pickup_date",
                            'pPickupTime'=>"$bd_pickup_time",
                            'profile' =>
                            array(
                                  'Api_type' => 'S',
                                  'LicenceKey'=>'13a81f7d2d83aaf1a417322a27f8e10a',
                                  'LoginID'=>'BOM05559',
                                  'Version'=>'1.3')
                            );
        
        
        // Here I call my external function
        $result = $soap->__soapCall('GetDomesticTransitTimeForPinCodeandProduct',array($paramsLive)); // this line is important
        
        $AdditionalDays = $result->GetDomesticTransitTimeForPinCodeandProductResult->AdditionalDays;
        $CityDesc_Destination = $result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Destination;
        $CityDesc_Origin = $result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Origin;
        $ExpectedDateDelivery = $result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDateDelivery;
        $ExpectedDatePOD = $result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDatePOD;
        
        
        $diff = abs(strtotime($ExpectedDatePOD) - strtotime($pickup_date));
        
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        
        
        $convert_date = new DateTime($ExpectedDatePOD);
        $ExpectedDatePOD = date_format($convert_date, 'd-M-Y');
        $convert_date = new DateTime($ExpectedDateDelivery);
        $ExpectedDateDelivery = date_format($convert_date, 'd-M-Y');
        
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt1 = $conn->prepare("INSERT INTO `transit_time` (`vendor_id`, `vendor_name`, `product_type`, `sub_product`, `pickup_date`, `pickup_Time`, `from_pin`, `to_pin`, `ExpectedDateDelivery`, `ExpectedDatePOD`, `TAT`, `AdditionalDays`,`added_date`,`added_by`) VALUES ('1', 'BlueDart', '$product_type4', '$sub_product4', '$pickup_date1', '$pickup_time', '$from_pin', '$to_pin', '$ExpectedDateDelivery', '$ExpectedDatePOD', '$days', '$AdditionalDays','$nowtime','$userid');");
            $stmt1->execute();
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    
    /////////////////////////////////////////////
    /////////////////Apex Pre-Paid /////////////
    ////////////////////////////////////////////
    
    if($service_avi_level9!=0){
        $product_type5="Apex Pre-Paid";
        $product_code5="A";
        $sub_product5="P";
        
        $paramsLive = array('pPinCodeFrom' => "$from_pin",
                            'pPinCodeTo' =>"$to_pin",
                            'pProductCode' =>"$product_code5",
                            'pSubProductCode' =>"$sub_product5",
                            'pPudate'=>"$pickup_date",
                            'pPickupTime'=>"$bd_pickup_time",
                            'profile' =>
                            array(
                                  'Api_type' => 'S',
                                  'LicenceKey'=>'13a81f7d2d83aaf1a417322a27f8e10a',
                                  'LoginID'=>'BOM05559',
                                  'Version'=>'1.3')
                            );
        
        
        // Here I call my external function
        $result = $soap->__soapCall('GetDomesticTransitTimeForPinCodeandProduct',array($paramsLive)); // this line is important
        
        $AdditionalDays = $result->GetDomesticTransitTimeForPinCodeandProductResult->AdditionalDays;
        $CityDesc_Destination = $result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Destination;
        $CityDesc_Origin = $result->GetDomesticTransitTimeForPinCodeandProductResult->CityDesc_Origin;
        $ExpectedDateDelivery = $result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDateDelivery;
        $ExpectedDatePOD = $result->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDatePOD;
        
        
        $diff = abs(strtotime($ExpectedDatePOD) - strtotime($pickup_date));
        
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        
        
        $convert_date = new DateTime($ExpectedDatePOD);
        $ExpectedDatePOD = date_format($convert_date, 'd-M-Y');
        $convert_date = new DateTime($ExpectedDateDelivery);
        $ExpectedDateDelivery = date_format($convert_date, 'd-M-Y');
        
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt1 = $conn->prepare("INSERT INTO `transit_time` (`vendor_id`, `vendor_name`, `product_type`, `sub_product`, `pickup_date`, `pickup_Time`, `from_pin`, `to_pin`, `ExpectedDateDelivery`, `ExpectedDatePOD`, `TAT`, `AdditionalDays`,`added_date`,`added_by`) VALUES ('1', 'BlueDart', '$product_type5', '$sub_product5', '$pickup_date1', '$pickup_time', '$from_pin', '$to_pin', '$ExpectedDateDelivery', '$ExpectedDatePOD', '$days', '$AdditionalDays','$nowtime','$userid');");
            $stmt1->execute();
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    
    
    
    
    class DebugSoapClient extends SoapClient {
        public $sendRequest = true;
        public $printRequest = false;
        public $formatXML = false;
        
        public function __doRequest($request, $location, $action, $version, $one_way=0) {
            if ( $this->printRequest ) {
                if ( !$this->formatXML ) {
                    $out = $request;
                }
                else {
                    $doc = new DOMDocument;
                    $doc->preserveWhiteSpace = false;
                    $doc->loadxml($request);
                    $doc->formatOutput = true;
                    $out = $doc->savexml();
                }
                echo $out;
            }
            
            if ( $this->sendRequest ) {
                return parent::__doRequest($request, $location, $action, $version, $one_way);
            }
            else {
                return '';
            }
        }
    }
