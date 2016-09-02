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
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// Product Code /////////////////////////
    ////////////////////////////////////////////////////////////////
    
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

    ///////////////////////////////////////////////////////////////
    //////////////////////// COD  ///////////////////////////////
    ////////////////////////////////////////////////////////////////
    
    if($subproduct==''){
        $COD=0;
        $CustomerPay='false';
    }
    else {
        $CustomerPay='true';
        $COD=$CollectableAmount;
    }
    
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// Product Type  ///////////////////////////////
    ///////////////////////////////////////////////////////////////
    
    
    
    if($shipmentcontent=="Documents"){
        $producttype="Docs";
    }
    else if($shipmentcontent=="Commodities"){
       $producttype="Dutiables";
        
        
        
        for($i=1;$i<($commodity_count+1);$i++){
            
            $Commodity_desc["CommodityDetail".$i]=$_POST["Commodity".$i];
            
        }

        
       $COD=0;
    }
    
    ///////////////////////////////////////////////////////////////
    ////////////////////////  Dimension  ///////////////////////////////
    ///////////////////////////////////////////////////////////////

    //$bd_dimension=array();
    
    //for($i=0;$i<packagecount;$i++){
    
      //  'Dimension' =>array ('Breadth' =>$breath,'Count' => '1','Height' => $height,'Length' => $length,)
   // }
    
    
    
    ///////////////////////////////////////////////////////////////
    //////////////////////// BlueDart Parameters  /////////////////
    ////////////////////////////////////////////////////////////////

    
$params = array(
'Request' => 
	array (
		'Consignee' =>
			array (
				'ConsigneeAddress1' => $receiver_info[3],
				'ConsigneeAddress2' => $receiver_info[4],
				'ConsigneeAddress3'=> $receiver_info[5],
				'ConsigneeAttention'=> $receiver_info[2],
				'ConsigneeMobile'=> $receiver_info[6],
				'ConsigneeName'=> $receiver_info[1],
				'ConsigneePincode'=> $receiver_info[0],
				'ConsigneeTelephone'=> $receiver_info[6],
			)	,
		'Services' => 
			array (
				'ActualWeight' => $weight,
				'CollectableAmount' => $COD,
				'Commodity' =>$Commodity_desc,
				'CreditReferenceNo' => $uid,
				'DeclaredValue' => $cost,
				'Dimensions' =>
					array (
						'Dimension' =>
							array (
								'Breadth' =>$breath,
								'Count' => $packagecount,
								'Height' => $height,
								'Length' => $length
							),
                        
                           ),
					'InvoiceNo' => '',
					'PackType' => '',
					'PickupDate' => $shipment_date,
					'PickupTime' => '1800',
					'PieceCount' => $packagecount,
					'ProductCode' => $product,
					'ProductType' => $producttype,
					'SpecialInstruction' => '1',
					'SubProductCode' => $subproduct
			),
			'Shipper' =>
				array(
					'CustomerAddress1' => $sender_info[3],
					'CustomerAddress2' => $sender_info[4],
					'CustomerAddress3' => $sender_info[5],
					'CustomerCode' => '359181',
					'CustomerEmailID' => 'a@b.com',
					'CustomerMobile' => $sender_info[6],
					'CustomerName' => $sender_info[1],
					'CustomerPincode' => $sender_info[0],
					'CustomerTelephone' => $sender_info[6],
					'IsToPayCustomer' => $CustomerPay,
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