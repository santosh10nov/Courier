<?php
    
    require_once '../dbconfig.php';

    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt1 = $conn->prepare("SELECT pickup.UID,pickup.Courier_Vendor,pickup.AWB_Number,PickUp_Batch_Details.name,PickUp_Batch_Details. Company_Name,PickUp_Batch_Details.Address,PickUp_Batch_Details.City,PickUp_Batch_Details.State, PickUp_Batch_Details.pincode,PickUp_Batch_Details.Phone,PickUp_Batch_Details.pickup_book_date,PickUp_Batch_Details.pickup_time, PickUp_Batch_Details.com_closing_time,AirwayBill_Packages.Package_Count,AirwayBill_Packages.Weight, AirwayBill.CreatedByCompID FROM `pickup` LEFT JOIN PickUp_Batch_Details ON PickUp_Batch_Details.Batch_ID=pickup.Batch_ID LEFT JOIN AirwayBill_Packages ON pickup.UID=AirwayBill_Packages.AWB_UID LEFT JOIN AirwayBill on AirwayBill.UID= pickup.UID WHERE pickup.Pickup_Status='UNDER PROCESS' AND pickup.is_locked=0 AND AirwayBill.AWB_Status='Success'");
        $stmt1->execute();
        $numrows = $stmt1->rowCount();
        
        $i=1;
        
        $stmt2 = $conn->prepare("UPDATE `pickup` SET pickup.is_locked=1 WHERE pickup.Pickup_Status='UNDER PROCESS' AND pickup.is_locked=0");
        $stmt2->execute();
        
        $fname =time().".txt";
        
        $log=fopen('../Pickup/CronLog/'.$fname,"a");
        
        $val="Total Number of rows=>".$numrows."\n";
        
        

        
        foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
            
            
            $val.= $i."\n".json_encode($row)."\n\n";
            
            $name=$row["name"];
            $com_name=$row["Company_Name"];
            $address=$row["Address"];
            $city=$row["City"];
            $state=$row["State"];
            $pincode=$row["pincode"];
            $phone=$row["Phone"];
            
            $comp_closing_time=substr($row["com_closing_time"],0,5);
            $pickupdate=$row["pickup_book_date"];
            $pickuptime=substr($row["pickup_time"],0,5);
            $pickupdatetime=$row["pickup_book_date"]."T".$row["pickup_time"];
            
            $AWBNo=$row["AWB_Number"];
            $UID=$row["UID"];
            $NumberofPieces=1;      // Scheduling Pickup for Indiv. AWB
            $Weight=$row["Weight"];
            $couriervendor=$row["Courier_Vendor"];
            $CompanyID=$row["CreatedByCompID"];
            
            echo $CompanyID;
           

            
            if($row["Courier_Vendor"]=="BlueDart"){
                    include("../Pickup/BlueDart/PickupRegistartionService.php");
                
            }
            else if($row["Courier_Vendor"]=="FedEx"){
                
                include("../Pickup/FedEx/CreatePickupWebServiceClient.php");
            }
            
            $i++;
        }
       
        fwrite($log,$val);
        
        fclose($log);
        
        
        $Cronlog=fopen('Cronlog.log',"a");
        fwrite($Cronlog,"Successfully runned cron-".time().";\n \n");
        fclose($Cronlog);
        
}
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
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