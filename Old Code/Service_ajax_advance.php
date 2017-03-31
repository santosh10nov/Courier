<?php
    
    $service=$_GET['couriervendor'];
    
    require_once 'dbconfig.php';
    
    if($service=="BlueDart"){
        $from_pin = $_GET['from_pin'];
        $to_pin= $_GET['to_pin'];
        
        try{
            $arr="Do Nothing";
            echo $arr;
            
            
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    }
    elseif($service=="FedEx"){
        $from_pin = $_GET['from_pin'];
        $to_pin= $_GET['to_pin'];
        
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $stmt1 = $conn->prepare("Select * from fedex_avi where `pincode`=$from_pin");
            $stmt1->execute();
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $row1 = $stmt1->fetch();
            
            $stmt2 = $conn->prepare("Select * from fedex_avi where `pincode`=$to_pin");
            $stmt2->execute();
            $stmt2->setFetchMode(PDO::FETCH_ASSOC);
            $row2 = $stmt2->fetch();
            
            $service_avi_level1=$row1["p_pickup"]*$row2["p_delivery"]*$row1["p_classification"]*$row2["p_classification"];  // FedEx Priority ///
            
            $service_avi_level2=$row1["s_pickup"]*$row2["s_delivery"]*$row1["s_classification"]*$row2["s_classification"];  ////// FedEx Standard///////
            
            
            if($service_avi_level1!=0 && $service_avi_level2!=0){
                echo "Priority and Standard";
            }
            elseif($service_avi_level1!=0){
                echo "Priority";
                
            }
            elseif($service_avi_level2!=0){
                echo "Standard";
            }
            
            
            

            
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    }
    
    
    
    ?>
