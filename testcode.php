<?php
    
    $end=3;
    
    
    //$Commodity_desc=array();
    
    /*for($i=1;$i<($end+1);$i++){
     
     $Commodity_desc["CommodityDetail".$i]=1;
     
     }
     
     print_r($Commodity_desc); */
    
    
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    

    
    try{
        
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt6 = $conn->prepare(" SELECT * FROM  `AirwayBill_Error` WHERE `AWB_UID`=62");
        $stmt6->execute();
        
        $stmt6->setFetchMode(PDO::FETCH_ASSOC);
        $row6 = $stmt6->fetch();
        
        $message=json_decode($row6['Message']);
                           
                           print_r($message);
                           
                           
                           }
                           catch(PDOException $e) {
                           echo "Error: " . $e->getMessage();
                           }
                           
                           
                           
                           ?>