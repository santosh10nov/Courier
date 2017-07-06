<?php
    
    require_once '/Applications/XAMPP/xamppfiles/htdocs/Courier/dbconfig.php';

try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    $stmt1 = $conn->prepare("SELECT Tracking.AWB_Number as AWB_Number,Tracking.CourierVendor as CourierVendor, Tracking.UID as UID FROM `Tracking` left join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code WHERE TrackingStatusMapping.`TrackingStatus`!='Delivered' AND TrackingStatusMapping.`TrackingStatus`!='Pickup Canceled'");
    $stmt1->execute();
    $i=0;
    
    
    foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
        
        //print_r($row);
        
        
        $UID=$row["UID"];
        $AWBNo=$row["AWB_Number"];
        $couriervendor=$row["CourierVendor"];
        
        echo "<br>".$AWBNo."<br>";
        
        
        
        if($row["CourierVendor"]=="BlueDart"){
            include("/Applications/XAMPP/xamppfiles/htdocs/Courier/Tracking/BlueDart/BlueDartTracking.php");
            
        }
        if($row["CourierVendor"]=="FedEx"){
            
            include("/Applications/XAMPP/xamppfiles/htdocs/Courier/Tracking/FedEx/TrackWebServiceClient.php");
        }
    }
    
    
    
    
}
catch(PDOException $e) {
    //echo "Error: " . $e->getMessage();
}


////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////BlueDart API Class ///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////


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

?>
