<?php
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    
    
   
    $cancel_type=$_GET["cancel_type"];
    
    
    try{
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        if($cancel_type=="AirwayBill"){
            $CourierVendor=$_GET["CourierVendor"];
            $Airwaybill_Number=$_GET["Airwaybill_Number"];
            $AWB_Date=$_GET["AWB_Date"];
            $UID=$_GET["UID"];
            
            $stmt1 = $conn->prepare("UPDATE `AirwayBill` SET `AWB_Status`='Cancellation Pending' where `UID`=$UID AND `Airwaybill_Number`=$Airwaybill_Number ");
            $stmt1->execute();
        }
        elseif($cancel_type=="Pickup"){
            
            $UID=$_GET["UID"];
            $Airwaybill_Number=$_GET["Airwaybill_Number"];
            $stmt1 = $conn->prepare("UPDATE `pickup` SET `Pickup_Status`='Cancel Pickup' WHERE `UID`= $UID and AWB_Number= $Airwaybill_Number");
            $stmt1->execute();
            
            echo "Pickup Canceled";
        }
        
        
        
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    ?>
