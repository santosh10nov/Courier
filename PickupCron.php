<?php
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";

    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt1 = $conn->prepare("SELECT pickup.Courier_Vendor,pickup.AWB_Number,PickUp_Batch_Details.name,PickUp_Batch_Details.Company_Name,PickUp_Batch_Details.Address,\n"
                                . "PickUp_Batch_Details.City,PickUp_Batch_Details.State,PickUp_Batch_Details.pincode,PickUp_Batch_Details.Phone,\n"
                                . "PickUp_Batch_Details.pickup_book_date,PickUp_Batch_Details.pickup_time,PickUp_Batch_Details.com_closing_time,AirwayBill_Packages.Package_Count,AirwayBill_Packages.Weight\n"
                                . "FROM `pickup`\n"
                                . "LEFT JOIN PickUp_Batch_Details ON PickUp_Batch_Details.Batch_ID=pickup.Batch_ID\n"
                                . "LEFT JOIN AirwayBill_Packages ON pickup.UID=AirwayBill_Packages.AWB_UID\n"
                                . "WHERE pickup.Pickup_Status=\"UNDER PROCESS\"\n"
                                . "AND pickup.is_locked=0");
        $stmt1->execute();
        //$stmt1->setFetchMode(PDO::FETCH_ASSOC);
        //$row1 = $stmt1->fetch();
        
        ///////////////////////////////////////////
        ///////////Lock the Records////////////////
        //////////////////////////////////////////
        
        
        /*$stmt2 = $conn->prepare("Update pickup SET pickup.is_locked=1 \n"
                                . "LEFT JOIN PickUp_Batch_Details ON PickUp_Batch_Details.Batch_ID=pickup.Batch_ID\n"
                                . "LEFT JOIN AirwayBill_Packages ON pickup.UID=AirwayBill_Packages.AWB_UID\n"
                                . "WHERE pickup.Pickup_Status=\"UNDER PROCESS\"\n"
                                . "AND pickup.is_locked=0");
        $stmt2->execute();*/
        
        $i=0;

        
        foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
            
            print_r($row);
            
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
            $NumberofPieces=$row["Package_Count"];
            $Weight=$row["Weight"];
           
            echo $pickupdatetime;

            
            if($row["Courier_Vendor"]=="BlueDart"){
                    include("/Applications/XAMPP/xamppfiles/htdocs/Courier/Pickup/BlueDart/PickupRegistartionService.php");
                
            }
            else if($row["Courier_Vendor"]=="FedEx"){
                
                include("/Applications/XAMPP/xamppfiles/htdocs/Courier/Pickup/FedEx/CreatePickupWebServiceClient.php");
            }
        }
            
        
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
