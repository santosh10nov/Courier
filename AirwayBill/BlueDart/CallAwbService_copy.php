<?php 

	/* 
	#echo "Start  of Soap 1.1 version ( BasicHttpBinding) setting"
			$soap = new DebugSoapClient('http://netconnect.bluedart.com/Demo/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl',
			array(
			'trace' 							=> 1,  
			'style'								=> SOAP_DOCUMENT,
			'use'									=> SOAP_LITERAL,
			'soap_version' 				=> SOAP_1_1
			));
			
			$soap->__setLocation("http://netconnect.bluedart.com/Demo/ShippingAPI/Waybill/WayBillGeneration.svc/Basic");
			
			$soap->sendRequest = true;
			$soap->printRequest = false;
			$soap->formatXML = true; 	
	#echo "end of Soap 1.1 version setting" 
	*/
	 
	 #echo "Start  of Soap 1.2 version (ws_http_Binding)  setting";
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
				
				$actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IWayBillGeneration/GenerateWayBill',true);
				$soap->__setSoapHeaders($actionHeader);	
		#echo "end of Soap 1.2 version (WSHttpBinding)  setting";

define('SHIP_LABEL1', 'bluedartshipexpresslabel.pdf');
$params = array(
'Request' => 
	array (
		'Consignee' =>
			array (
				'ConsigneeAddress1' => 'A',
				'ConsigneeAddress2' => 'A',
				'ConsigneeAddress3'=> 'A',
				'ConsigneeAttention'=> 'A',
				'ConsigneeMobile'=> '1234567890',
				'ConsigneeName'=> 'A',
				'ConsigneePincode'=> '400028',
				'ConsigneeTelephone'=> '9833563411',
			)	,
		'Services' => 
			array (
				'ActualWeight' => '1',
				'CollectableAmount' => '0',
				'Commodity' =>
					array (
						'CommodityDetail1' => 'PRETTYSECRET',
						'CommodityDetail2'  => ' Aultra Boos',						
						'CommodityDetail3' => 'Bra'
				),
				'CreditReferenceNo' => '123111989',
				'DeclaredValue' => '1000',
				'Dimensions' =>
					array (
						'Dimension' =>
							array (
								'Breadth' => '1',
								'Count' => '1',
								'Height' => '1',
								'Length' => '1'
							),
                           array (
                                  'Breadth' => '2',
                                  'Count' => '2',
                                  'Height' => '1',
                                  'Length' => '4'
                                  ),
                           ),
					'InvoiceNo' => '',
					'PackType' => '',
					'PickupDate' => '2016-08-05',
					'PickupTime' => '1800',
					'PieceCount' => '1',
					'ProductCode' => 'A',
					'ProductType' => 'Dutiables',
					'SpecialInstruction' => '1',
					'SubProductCode' => ''
			),
			'Shipper' =>
				array(
					'CustomerAddress1' => '1',
					'CustomerAddress2' => '1',
					'CustomerAddress3' => '1',
					'CustomerCode' => '359181',
					'CustomerEmailID' => 'a@b.com',
					'CustomerMobile' => '9833563411',
					'CustomerName' => '1',
					'CustomerPincode' => '400069',
					'CustomerTelephone' => '1234567890',
					'IsToPayCustomer' => '',
					'OriginArea' => 'BOM',
					'Sender' => '1',
					'VendorCode' => ''
				)
	),
	'Profile' => 
		 array(
		 	'Api_type' => 'S',
			'LicenceKey'=>'4ac1e14526f0e9f4a4d91af49404ca2a',
			'LoginID'=>'BOM05559',
			'Version'=>'1.3')
			);

#echo "<br>";
#echo '<h2>Parameters</h2><pre>'; print_r($params); echo '</pre>';

// Here I call my external function
$result = $soap->__soapCall('GenerateWayBill',array($params));
 
    #echo "Generated Waybill number " + $result->GenerateWayBillResult->AWBNo;
//echo $result->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation;
    
    
    $fp = fopen("./AirwayBill/BlueDart/AirwayBill/".SHIP_LABEL1, 'wb');
    fwrite($fp,$result->GenerateWayBillResult->AWBPrintContent); //Create PNG or PDF file
    fclose($fp);
    echo '<a href="./AirwayBill/BlueDart/AirwayBill/'.SHIP_LABEL1.'">'.SHIP_LABEL1.'</a> was generated.';

echo "<br>";
//echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
    
    

 
 
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