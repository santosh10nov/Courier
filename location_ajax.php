<?php
    
    $location=$_GET['location'];
    
    require_once 'dbconfig.php';
    
    if($location=="s"){
        $from_pin = $_GET['from_pin'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $stmt1 = $conn->prepare("Select * from `city_pincode` where `pin`=$from_pin");
            $stmt1->execute();
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $row1 = $stmt1->fetch();
            
            
            $city=$row1["location"];
            $state=$row1["state"];
            //echo $pickup_location;
            //echo $state;
            $arr=$city."|".$state;
            echo $arr;
            
            
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    }
    elseif($location=="r"){
        $to_pin = $_GET['to_pin'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $stmt1 = $conn->prepare("Select * from `city_pincode` where `pin`=$to_pin");
            $stmt1->execute();
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $row1 = $stmt1->fetch();
            
            
            $city=$row1["location"];
            $state=$row1["state"];
            //echo $pickup_location;
            //echo $state;
            $arr=$city."|".$state;
            echo $arr;
            
            
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

    }
    


    ?>
