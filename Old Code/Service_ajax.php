<?php
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    $service=$_GET['couriervendor'];
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    
    if($service=="BlueDart"){
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $stmt1 = $conn->prepare("SELECT `product_name` FROM `vendor_services` where `vendor_name`='$service'");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            
            $response = array();
            while($row = $stmt1->fetch())
            {
                $x['servicevalue']=$row['product_name'];
                $x['service']=$row['product_name'];
                $response[] = $x;
            }
            echo json_encode($response);
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    }
    
    elseif($service=="FedEx"){
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $stmt1 = $conn->prepare("SELECT `product_name` FROM `vendor_services` where `vendor_name`='$service'");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            
            $response = array();
            while($row = $stmt1->fetch())
            {
                $x['servicevalue']=$row['product_name'];
                $x['service']=$row['product_name'];
                $response[] = $x;
            }
            echo json_encode($response);
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

    }
    
    
    
    ?>
