<?php
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    $action=$_GET['action'];
    
    
    require_once 'dbconfig.php';
    
    if($action=="productname"){
        
        $couriervendor=$_GET['couriervendor'];
        
        try{
            
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $stmt1 = $conn->prepare("SELECT * FROM `vendor_services` where `vendor_name`='$couriervendor'");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            
            $response = array();
            while($row = $stmt1->fetch())
            {
                $x['servicevalue']=$row['product_name'];
                $x['service']=$row['display_name'];
                $response[] = $x;
            }
            echo json_encode($response);
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
    }
    
    elseif($action=="ServiceAvailability_AWB"){
    
        $to_pin=$_GET['desti_pin'];
        $from_pin=$_GET['pickup_pin'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            /////////////////////////////////////////////////////////////
            //////////////////Find Distinct Courier Vendor///////////////
            ///////////////////////////////////////////////////////////
            
            $stmt9 = $conn->prepare("SELECT name FROM `courier_vendor_details` ORDER BY `othervendor` DESC");
            $stmt9->execute();
            
            $numrows = $stmt9->rowCount();
            $stmt9->setFetchMode(PDO::FETCH_ASSOC);
            if($numrows!=0){
                
                foreach($stmt9->fetchAll() as $row9){
                    if($row9["name"]=="FedEx"){
                        
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
                        
                        $p_product1= "Priority";
                        
                        $s_product2= "Standard";
                        
                        $stmt9 = $conn->prepare("Select distinct * from `transit_time` where `from_pin`=$from_pin  AND `to_pin` = $to_pin  AND `vendor_name`='FedEx' order by `TAT` asc");
                        $stmt9->execute();
                        
                        $numrows = $stmt9->rowCount();
                        $stmt9->setFetchMode(PDO::FETCH_ASSOC);
                        
                        if($numrows==0){
                            if($service_avi_level1!=0 OR $service_avi_level2!=0)
                            {
                                include 'FedExTATAWB.php';
                            }
                            
                            
                            
                        }
                        
                        
                    }
                    elseif($row9["name"]=="BlueDart"){
                        
                        $stmt3 = $conn->prepare("Select * from `bluedart_service_avii1` where `pincode`=$from_pin");
                        $stmt3->execute();
                        $stmt3->setFetchMode(PDO::FETCH_ASSOC);
                        $row3 = $stmt3->fetch();
                        
                        
                        $stmt4 = $conn->prepare("Select * from `bluedart_service_avii1` where `pincode`=$to_pin");
                        $stmt4->execute();
                        $stmt4->setFetchMode(PDO::FETCH_ASSOC);
                        $row4 = $stmt4->fetch();
                        
                        
                        $service_avi_level4=$row3["ApexInbound"]*$row4["ApexOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product4="Apex";
                        
                        
                        $service_avi_level5=$row3["DomesticPriorityInbound"]*$row4["DomesticPriorityOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product5="Domestic Priority";
                        
                        $service_avi_level6=$row3["GroundInbound"]*$row4["GroundOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product6="Surface";
                        
                        $service_avi_level7=$row3["eTailCODAirInbound"]*$row4["eTailCODAirOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product7 ="Apex COD";
                        
                        $service_avi_level8=$row3["eTailCODGroundInbound"]*$row4["eTailCODGroundOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product8 ="Surface COD";
                        
                        $service_avi_level9=$row3["eTailPrePaidAirInbound"]*$row4["eTailPrePaidAirOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product9 ="Apex Pre-Paid";
                        
                        $service_avi_level10=$row3["eTailPrePaidGroundInbound"]*$row4["eTailPrePaidGroundOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product10 ="Surface Pre-Paid";
                        
                        
                        
                        $stmt9 = $conn->prepare("Select distinct * from `transit_time` where `from_pin`=$from_pin  AND `to_pin` = $to_pin AND `vendor_name`='BlueDart' order by `TAT` asc");
                        $stmt9->execute();
                        
                        $numrows = $stmt9->rowCount();
                        $stmt9->setFetchMode(PDO::FETCH_ASSOC);
                        

                        if($numrows==0){
                            if($service_avi_level4!=0 OR $service_avi_level5!=0 OR $service_avi_level6!=0 OR $service_avi_level7!=0 OR $service_avi_level8!=0 OR $service_avi_level9!=0  OR $service_avi_level10!=0)
                            {
                                include 'BlueDartTATAWB.php';
                            }
                            
                        
                            
                        }
                    }
                    
                    
                }
                
                $stmt9 = $conn->prepare("Select distinct * from `transit_time`left join `vendor_services` on `vendor_services`.display_name=`transit_time`.product_type  where `from_pin`=$from_pin  AND `to_pin` = $to_pin GROUP BY product_type order by `TAT` asc");
                $stmt9->execute();
                
                $numrows = $stmt9->rowCount();
                $stmt9->setFetchMode(PDO::FETCH_ASSOC);
                if($numrows!=0){
                    
                    echo '<table class="table table-bordered">';
                    echo '<thead style="background-color: gray; color:white; "><tr><th>Service Provider Name</th><th>Product Name</th><th>Service Type</th><th>Delivery Date</th><th>TAT</th><th>Action</th></tr></thead><tboby>';
                    while($row9 = $stmt9->fetch()){
                        
                        echo '<tr><td>'.$row9["vendor_name"].'</td><td>'.$row9["display_name"].'</td><td>'.$row9["ServiceType"].'</td><td>'.$row9["ExpectedDateDelivery"].'</td><td>'.$row9["TAT"].'</td><td><button type="button" class="btn btn-success" onclick="santy(\''.$row9["vendor_name"].'\',\''.$row9["product_name"].'\',\''.$row9["display_name"].'\')">></button></td></tr>';
                        
                        
                    }
                    echo '</tbody></table><br />';
                }

            }
            else{
                echo "Plz. add courier vendor to check Service Availability";
            }
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
         $conn=null;
    }
    
    
    
    elseif($action=="ServiceAvailability"){
        
        $to_pin=$_GET['desti_pin'];
        $from_pin=$_GET['pickup_pin'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            /////////////////////////////////////////////////////////////
            //////////////////Find Distinct Courier Vendor///////////////
            ///////////////////////////////////////////////////////////
            
            $stmt9 = $conn->prepare("SELECT name FROM `courier_vendor_details` ORDER BY `othervendor` DESC");
            $stmt9->execute();
            
            $numrows = $stmt9->rowCount();
            $stmt9->setFetchMode(PDO::FETCH_ASSOC);
            if($numrows!=0){
                
                foreach($stmt9->fetchAll() as $row9){
                    if($row9["name"]=="FedEx"){
                        
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
                        
                        $p_product1= "Priority";
                        
                        $s_product2= "Standard";
                        
                        $stmt9 = $conn->prepare("Select distinct * from `transit_time` where `from_pin`=$from_pin  AND `to_pin` = $to_pin  AND `vendor_name`='FedEx' order by `TAT` asc");
                        $stmt9->execute();
                        
                        $numrows = $stmt9->rowCount();
                        $stmt9->setFetchMode(PDO::FETCH_ASSOC);
                        
                        if($numrows==0){
                            if($service_avi_level1!=0 OR $service_avi_level2!=0)
                            {
                                include 'FedExTATAWB.php';
                            }
                            
                            
                            
                        }
                        
                        
                    }
                    elseif($row9["name"]=="BlueDart"){
                        
                        $stmt3 = $conn->prepare("Select * from `bluedart_service_avii1` where `pincode`=$from_pin");
                        $stmt3->execute();
                        $stmt3->setFetchMode(PDO::FETCH_ASSOC);
                        $row3 = $stmt3->fetch();
                        
                        
                        $stmt4 = $conn->prepare("Select * from `bluedart_service_avii1` where `pincode`=$to_pin");
                        $stmt4->execute();
                        $stmt4->setFetchMode(PDO::FETCH_ASSOC);
                        $row4 = $stmt4->fetch();
                        
                        
                        $service_avi_level4=$row3["ApexInbound"]*$row4["ApexOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product4="Apex";
                        
                        
                        $service_avi_level5=$row3["DomesticPriorityInbound"]*$row4["DomesticPriorityOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product5="Domestic Priority";
                        
                        $service_avi_level6=$row3["GroundInbound"]*$row4["GroundOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product6="Surface";
                        
                        $service_avi_level7=$row3["eTailCODAirInbound"]*$row4["eTailCODAirOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product7 ="Apex COD";
                        
                        $service_avi_level8=$row3["eTailCODGroundInbound"]*$row4["eTailCODGroundOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product8 ="Surface COD";
                        
                        $service_avi_level9=$row3["eTailPrePaidAirInbound"]*$row4["eTailPrePaidAirOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product9 ="Apex Pre-Paid";
                        
                        $service_avi_level10=$row3["eTailPrePaidGroundInbound"]*$row4["eTailPrePaidGroundOutbound"]*$row3["classification"]*$row4["classification"];
                        $bd_product10 ="Surface Pre-Paid";
                        
                        
                        
                        $stmt9 = $conn->prepare("Select distinct * from `transit_time` where `from_pin`=$from_pin  AND `to_pin` = $to_pin AND `vendor_name`='BlueDart' order by `TAT` asc");
                        $stmt9->execute();
                        
                        $numrows = $stmt9->rowCount();
                        $stmt9->setFetchMode(PDO::FETCH_ASSOC);
                        
                        
                        if($numrows==0){
                            if($service_avi_level4!=0 OR $service_avi_level5!=0 OR $service_avi_level6!=0 OR $service_avi_level7!=0 OR $service_avi_level8!=0 OR $service_avi_level9!=0  OR $service_avi_level10!=0)
                            {
                                include 'BlueDartTATAWB.php';
                            }
                            
                            
                            
                        }
                    }
                    
                    
                }
                
                $stmt9 = $conn->prepare("Select distinct * from `transit_time`left join `vendor_services` on `vendor_services`.display_name=`transit_time`.product_type  where `from_pin`=$from_pin  AND `to_pin` = $to_pin GROUP BY product_type order by `TAT` asc");
                $stmt9->execute();
                
                $numrows = $stmt9->rowCount();
                $stmt9->setFetchMode(PDO::FETCH_ASSOC);
                if($numrows!=0){
                    
                    echo '<table class="table table-bordered">';
                    echo '<thead style="background-color: gray; color:white; "><tr><th>Courier Vendor Name</th><th>Product Name</th><th>Service Type</th><th>TAT</th><th>Action</th></tr></thead><tboby>';
                    while($row9 = $stmt9->fetch()){
                        
                        echo '<tr><td>'.$row9["vendor_name"].'</td><td>'.$row9["display_name"].'</td><td>'.$row9["ServiceType"].'</td><td>'.$row9["TAT"].'</td><td><button type="button" class="btn btn-success" onclick="santy(\''.$row9["vendor_name"].'\',\''.$row9["product_name"].'\',\''.$row9["display_name"].'\')"> Create AirwayBill</button></td></tr>';
                        
                        
                    }
                    echo '</tbody></table><br />';
                }
            else{
                echo '<table class="table table-bordered">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Courier Vendor Name</th><th>Product Name</th><th>Service Type</th><th>TAT</th><th>Action</th></tr></thead><tboby>';
                echo '<tr><td colspan="5" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                

            }
                 }
                else{
                    echo "Plz. add courier vendor to check Service Availability";
                }

           
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
    }
    
   else if($action=="CheckCity"){
        
        $to_pin=$_GET['desti_pin'];
        $from_pin=$_GET['pickup_pin'];
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1=$conn->prepare("SELECT `city` FROM `city_pincode` where pin=$from_pin");
            $stmt1->execute();
            $result1=$stmt1->fetch();
            
            $stmt2=$conn->prepare("SELECT `city` FROM `city_pincode` where pin=$to_pin");
            $stmt2->execute();
            $result2=$stmt2->fetch();
            $to_city=$result2["city"];
        
            $y['to']=$result2["city"];
            $y['from']=$result1["city"];
            $response[]=$y;
            
            echo json_encode($response);
            
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
    }

    
    
    
    
    ?>
