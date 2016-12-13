<?php
    
    $vendor_name=$_GET['vendor_name'];
    $PickupDate=$_GET['PickupDate'];
    $PickupTime=$_GET['PickupTime'];
    $CompanyClosingTime=$_GET['CompanyClosingTime'];
    $pincode=$_GET['pincode'];
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    
    
    if($vendor_name=="BlueDart"){
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $stmt1 = $conn->prepare("SELECT * FROM `PickUp_Cutoff_BlueDart` where `Pincode`= $pincode ");
            $stmt1->execute();
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $row1 = $stmt1->fetch();
            
            
            $CutoffTime=$row1["Apex"];
           
            if(strtotime($PickupTime)<=strtotime($CutoffTime) ){
                echo "Success";
            }
            else{
                echo "Pickup Time is after Cut off Time";
            }
            
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    }
    
    
    elseif($vendor_name=="FedEx"){
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $stmt1 = $conn->prepare("SELECT * FROM `PickUp_Cutoff_FedEx` where `Pincode`= $pincode ");
            $stmt1->execute();
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $row1 = $stmt1->fetch();
            
            
            $CutoffTime=$row1["Apex"];
            
            if(strtotime($PickupTime)<=strtotime($CutoffTime) ){
                echo "Book Pickup";
            }
            else{
                echo "Pickup Time is after Cut off Time";
            }
            
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    
    
    


?>
