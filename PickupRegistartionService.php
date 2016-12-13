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
	   
        $params = array(
			         'request' => 
										array ( 
									 				  'AreaCode' => 'BOM',
														'ContactPersonName' => 'test1',
														'CustomerAddress1' => 'test2',
														'CustomerAddress2' => 'test3',
														'CustomerAddress3' => 'test4',
														'CustomerCode' => '359181',
														'CustomerName' => 'test',
														'CustomerPincode' => '400069',
														'CustomerTelephoneNumber' => '12345678',
														'DoxNDox' => '1',
														'EmailID' => 'a@b.com',
														'MobileTelNo' => '9967327037',
														'NumberofPieces' => '1',
														'OfficeCloseTime' => '16:00',
														'ProductCode' => 'A',
														'ReferenceNo' => '123456',
														'Remarks' => 'TEST',
														'RouteCode' => '99',
														'ShipmentPickupDate' => '2016-11-27',
														'ShipmentPickupTime' => '1600',
														'VolumeWeight' => '1.2',
														'WeightofShipment' => '1.2',
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
 
echo "<br>";
echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';

 
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
