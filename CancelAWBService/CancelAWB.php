<?php
    
    
    
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    
    class DebugSoapClient extends SoapClient {
        public $sendRequest = true;
        public $printRequest = true;
        public $formatXML = true;
        
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
    
    
    try{
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt1 = $conn->prepare("SELECT * FROM `AirwayBill` WHERE `AWB_Status`='Cancellation Pending' ");
        $stmt1->execute();
        
        $numrows = $stmt1->rowCount();
        
        if($numrows>0){
            while($row = $stmt1->fetch()){
                if($row["CourierVendor"]=="BlueDart"){
                    $cancel_status=CancelAWBBlueDart($row["Airwaybill_Number"]);
                    $Airwaybill_Number=$row["Airwaybill_Number"];
                    if($cancel_status=="Valid"){
                        $stmt1 = $conn->prepare("UPDATE AirwayBill SET `AWB_Status` = 'Cancelled' where `Airwaybill_Number`= '$Airwaybill_Number' ");
                        $stmt1->execute();
                    }
                    
                }
                
            }
            
        }
        
        
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////BlueDart Function//////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    function CancelAWBBlueDart($Airwaybill_Number){
        
        
        $soap = new DebugSoapClient('http://netconnect.bluedart.com/Demo/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl',
                                    array(
                                          'trace' 							=> 1,
                                          'style'								=> SOAP_DOCUMENT,
                                          'use'									=> SOAP_LITERAL,
                                          'soap_version' 				=> SOAP_1_2
                                          ));
        
        $soap->__setLocation("http://netconnect.bluedart.com/Demo/ShippingAPI/Waybill/WayBillGeneration.svc");
        
        $soap->sendRequest = true;
        $soap->printRequest = false;
        $soap->formatXML = true;
        
        $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IWayBillGeneration/CancelWaybill',true);
        $soap->__setSoapHeaders($actionHeader);
        
        $params = array(
                        'Request' =>
                        array (
                               'AWBNo'=>$Airwaybill_Number,
                               ),
                        'Profile' =>
                        array(
                              'Api_type' => 'S',
                              'LicenceKey'=>'4ac1e14526f0e9f4a4d91af49404ca2a',
                              'LoginID'=>'BOM05559',
                              'Version'=>'1.3')
                        );
        //echo "<br>";
        //echo '<h2>Parameters</h2><pre>'; print_r($params); echo '</pre>';
        
        // Here I call my external function
        $result = $soap->__soapCall('CancelWaybill',array($params));
        
        $response_status= $result->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusCode;
        
        echo "<br>";
        echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
        
    }

    
    ?>
