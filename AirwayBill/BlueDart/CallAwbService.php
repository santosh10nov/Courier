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
				'ConsigneeAddress1' => $sender_info[3],
				'ConsigneeAddress2' => $sender_info[4],
				'ConsigneeAddress3'=> $sender_info[5],
				'ConsigneeAttention'=> $sender_info[2],
				'ConsigneeMobile'=> $sender_info[6],
				'ConsigneeName'=> $sender_info[1],
				'ConsigneePincode'=> $sender_info[0],
				'ConsigneeTelephone'=> $sender_info[6],
			)	,
		'Services' => 
			array (
				'ActualWeight' => $weight,
				'CollectableAmount' => '0',
				'Commodity' =>
					array (
						'CommodityDetail1' => 'PRETTYSECRET',
						'CommodityDetail2'  => ' Aultra Boos',						
						'CommodityDetail3' => 'Bra'
				),
				'CreditReferenceNo' => $uid,
				'DeclaredValue' => $cost,
				'Dimensions' =>
					array (
						'Dimension' =>
							array (
								'Breadth' =>$breath,
								'Count' => '2',
								'Height' => $heigth,
								'Length' => $length
							),
                           
                           ),
					'InvoiceNo' => '',
					'PackType' => '',
					'PickupDate' => $shipment_date,
					'PickupTime' => '1800',
					'PieceCount' => '2',
					'ProductCode' => 'A',
					'ProductType' => 'Dutiables',
					'SpecialInstruction' => '1',
					'SubProductCode' => ''
			),
			'Shipper' =>
				array(
					'CustomerAddress1' => $receiver_info[3],
					'CustomerAddress2' => $receiver_info[4],
					'CustomerAddress3' => $receiver_info[5],
					'CustomerCode' => '359181',
					'CustomerEmailID' => 'a@b.com',
					'CustomerMobile' => $receiver_info[6],
					'CustomerName' => $receiver_info[1],
					'CustomerPincode' => $receiver_info[0],
					'CustomerTelephone' => $receiver_info[6],
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
    //echo '<a href="./AirwayBill/BlueDart/AirwayBill/'.SHIP_LABEL1.'">'.SHIP_LABEL1.'</a> was generated.';
    
    echo '<embed src="./AirwayBill/BlueDart/AirwayBill/'.SHIP_LABEL1.'" width="600px" height="400px"  type="application/pdf"><br>';
    echo'<a href="./AirwayBill/BlueDart/AirwayBill/'.SHIP_LABEL1.'" download><button type="submit" class="btn btn-success">Download</button></a>';
    
  

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