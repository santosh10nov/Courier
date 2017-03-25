
<!DOCTYPE html>
<html lang="en">
<head>
<title>rShipper Service</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-default">
<div class="container-fluid">
<div class="navbar-header">
<a class="navbar-brand" href="index.php">rShipper</a>
</div>
<ul class="nav navbar-nav">
<li><a href="index.php">Home</a></li>
<li><a href="Service_Availabilty.php">Service Availability</a></li>
<li class="nav-item nav-link active"><a href="TAT.php">TAT</a></li>
<li><a href="Generate_AirwayBill.php">Generate AirwayBill</a></li>
<li><a href="#">Book Pickup</a></li>
<li><a href="#">Track Courier</a></li>
</ul>
</div>
</nav>

<div class="container">
<h1 align="center">TAT </h1>
<?php
    
    
    if (isset($_POST["from_pin"]) && !empty($_POST["from_pin"]) && isset($_POST["to_pin"]) && !empty($_POST["to_pin"])) {
        
        $userid="santy";
        $from_pin=$_POST["from_pin"];
        $to_pin=$_POST["to_pin"];
        $pickup_date=$_POST["date"];
        $pickup_time=$_POST["time"];
        $bd_pickup_time=substr($pickup_time,0,5);
        $nowtime = time();
        
        $convert_date = new DateTime($pickup_date);
        $pickup_date1 = date_format($convert_date, 'd-M-Y');
        

        
        $servername = "127.0.0.1";
        $username = "root";
        $pass = "yesbank";
        $dbname = "transporter";
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            ////////////////////////////////////////////////////////
            //////// Location Name ////////////////////////////////
            ///////////////////////////////////////////////////////
            
            $stmt7 = $conn->prepare("Select * from `city_pincode` where `pin`=$from_pin");
            $stmt7->execute();
            $stmt7->setFetchMode(PDO::FETCH_ASSOC);
            $row7 = $stmt7->fetch();
            
            
            $stmt8 = $conn->prepare("Select * from `city_pincode` where `pin`=$to_pin");
            $stmt8->execute();
            $stmt8->setFetchMode(PDO::FETCH_ASSOC);
            $row8 = $stmt8->fetch();
            
            $pickup_location=$row7["location"];
            $destination_location=$row8["location"];
            
            

            
            
            
            ///////////////////////////////////////////////////////////
            /////////////// First Check DB//////////////////////////
            /////////////////////////////////////////////////////////
            
            echo "Pickup Location: " .$pickup_location."  (".$from_pin.")   ========================>  ";
            echo "Destination Location: " .$destination_location."  (".$to_pin.")<br><br>";
            
            $stmt9 = $conn->prepare("Select distinct * from `transit_time` where `from_pin`=$from_pin  AND `to_pin` = $to_pin AND `pickup_date`='$pickup_date1'  AND `pickup_Time`='$pickup_time' order by `TAT` asc");
            $stmt9->execute();
            
            $numrows = $stmt9->rowCount();
            $stmt9->setFetchMode(PDO::FETCH_ASSOC);
            if($numrows!=0){
                
                echo '<table class="table table-bordered">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Service Provider Name</th><th>Product Name</th><th>Pickup Date</th><th>Delivery Date</th><th>TAT</th></tr></thead><tboby>';
                while($row9 = $stmt9->fetch()){
                    echo '<tr><td>'.$row9["vendor_name"].'</td><td>'.$row9["product_type"].'</td><td>'.$row9["pickup_date"].'</td><td>'.$row9["ExpectedDateDelivery"].'</td><td>'.$row9["TAT"].'</td></tr>';
                    
                    
                }
                echo '</tbody></table><br />';
            }
            else{
                
            ///////////////////////////////////////////////////////////////
            ////// FedEx Availabile///////////////////////////////////////
            //////////////////////////////////////////////////////////////
            $stmt1 = $conn->prepare("Select * from fedex_avi where `pincode`=$from_pin");
            $stmt1->execute();
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $row1 = $stmt1->fetch();
            
            $stmt2 = $conn->prepare("Select * from fedex_avi where `pincode`=$to_pin");
            $stmt2->execute();
            $stmt2->setFetchMode(PDO::FETCH_ASSOC);
            $row2 = $stmt2->fetch();
            
            $service_avi_level1=$row1["s_pickup"]*$row2["s_delivery"]*$row1["s_classification"]*$row2["s_classification"];  // FedEx Standard ///
            
            $service_avi_level2=$row1["p_pickup"]*$row2["p_delivery"]*$row1["p_classification"]*$row2["p_classification"];  ////// FedEx Priority///////
            
            $fd_product1= "Priority";
            
            $fd_product2= "Standard";
            
            if($service_avi_level1!=0 OR $service_avi_level2!=0)
            {
                include 'FedExTAT.php';
            }
            
            
            
            
            ///////////////////////////////////////////////////////////////
            ////////////Bluedart Availabile///////////////////////////////
            //////////////////////////////////////////////////////////////
            
            $stmt3 = $conn->prepare("Select * from `bluedart_service_avii1` where `pincode`=$from_pin");
            $stmt3->execute();
            $stmt3->setFetchMode(PDO::FETCH_ASSOC);
            $row3 = $stmt3->fetch();
            
            
            $stmt4 = $conn->prepare("Select * from `bluedart_service_avii1` where `pincode`=$to_pin");
            $stmt4->execute();
            $stmt4->setFetchMode(PDO::FETCH_ASSOC);
            $row4 = $stmt4->fetch();
            
            /*$service_avi_level3=$row3["pickup"]*$row4["delivery"]*$row3["classification"]*$row4["classification"];
             
             $bd_pickup3 = ($row3["pickup"]) ? 'Yes' : 'No';
             $bd_delivery3= ($row4["delivery"]) ? 'Yes' : 'No';
             $bd_product3= "Constant";*/
            
            $service_avi_level4=$row3["ApexInbound"]*$row4["ApexOutbound"]*$row3["classification"]*$row4["classification"];
            $bd_pickup4 = ($row3["ApexInbound"]) ? 'Yes' : 'No';
            $bd_delivery4= ($row4["ApexOutbound"]) ? 'Yes' : 'No';
            $bd_product4="Apex";
           
            
            $service_avi_level5=$row3["DomesticPriorityInbound"]*$row4["DomesticPriorityOutbound"]*$row3["classification"]*$row4["classification"];
            $bd_pickup5 = ($row3["DomesticPriorityInbound"]) ? 'Yes' : 'No';
            $bd_delivery5= ($row4["DomesticPriorityOutbound"]) ? 'Yes' : 'No';
            $bd_product5="Domestic Priority";
            
            $service_avi_level6=$row3["GroundInbound"]*$row4["GroundOutbound"]*$row3["classification"]*$row4["classification"];
            $bd_pickup6 = ($row3["GroundInbound"]) ? 'Yes' : 'No';
            $bd_delivery6= ($row4["GroundOutbound"]) ? 'Yes' : 'No';
            $bd_amount6=$row3["GroundValueLimit"];
            $bd_product6="Surface";
            
            $service_avi_level7=$row3["eTailCODAirInbound"]*$row4["eTailCODAirOutbound"]*$row3["classification"]*$row4["classification"];
            $bd_pickup7 = ($row3["eTailCODAirInbound"]) ? 'Yes' : 'No';
            $bd_delivery7 = ($row4["eTailCODAirOutbound"]) ? 'Yes' : 'No';
            $bd_amount7=$row3["AirValueLimit"];
            $bd_product7 ="Apex COD";
            
            $service_avi_level8=$row3["eTailCODGroundInbound"]*$row4["eTailCODGroundOutbound"]*$row3["classification"]*$row4["classification"];
            $bd_pickup8 = ($row3["eTailCODGroundInbound"]) ? 'Yes' : 'No';
            $bd_delivery8 = ($row4["eTailCODGroundOutbound"]) ? 'Yes' : 'No';
            $bd_amount8=$row3["GroundValueLimit"];
            $bd_product8 ="Surface COD";
            
            $service_avi_level9=$row3["eTailPrePaidAirInbound"]*$row4["eTailPrePaidAirOutbound"]*$row3["classification"]*$row4["classification"];
            $bd_pickup9 = ($row3["eTailPrePaidAirInbound"]) ? 'Yes' : 'No';
            $bd_delivery9 = ($row4["eTailPrePaidAirOutbound"]) ? 'Yes' : 'No';
            $bd_amount9=$row3["AirValueLimiteTailPrePaid"];
            $bd_product9 ="Apex Pre-Paid";
            
            $service_avi_level10=$row3["eTailPrePaidGroundInbound"]*$row4["eTailPrePaidGroundOutbound"]*$row3["classification"]*$row4["classification"];
            $bd_pickup10 = ($row3["eTailPrePaidGroundInbound"]) ? 'Yes' : 'No';
            $bd_delivery10 = ($row4["eTailPrePaidGroundOutbound"]) ? 'Yes' : 'No';
            $bd_amount10=$row3["GroundValueLimiteTailPrePaid"];
            $bd_product10 ="Surface Pre-Paid";

            
            
            if($service_avi_level4!=0 OR $service_avi_level5!=0 OR $service_avi_level6!=0 OR $service_avi_level7!=0 OR $service_avi_level8!=0 OR $service_avi_level9!=0  OR $service_avi_level10!=0)
            {
                include 'BlueDartTAT.php';
            }
            
            
            
                
       
         
            
            
            /////////////////////////////////////////////////////
            /////// TAT OUTPUT///////////////////////////////////////
            /////////////////////////////////////////////////////
            
            echo "Pickup Location: " .$pickup_location."  (".$from_pin.")   ========================>  ";
            echo "Destination Location: " .$destination_location."  (".$to_pin.")<br><br>";
            
            $stmt9 = $conn->prepare("Select distinct * from `transit_time` where `from_pin`=$from_pin  AND `to_pin` = $to_pin AND `pickup_date`='$pickup_date1'  AND `pickup_Time`='$pickup_time' order by `TAT` asc");
            $stmt9->execute();
            
            $numrows = $stmt9->rowCount();
            $stmt9->setFetchMode(PDO::FETCH_ASSOC);
            if($numrows!=0){
            
            echo '<table class="table table-bordered">';
            echo '<thead style="background-color: gray; color:white; "><tr><th>Service Provider Name</th><th>Product Name</th><th>Pickup Date</th><th>Delivery Date</th><th>TAT</th></tr></thead><tboby>';
            while($row9 = $stmt9->fetch()){
                echo '<tr><td>'.$row9["vendor_name"].'</td><td>'.$row9["product_type"].'</td><td>'.$row9["pickup_date"].'</td><td>'.$row9["ExpectedDateDelivery"].'</td><td>'.$row9["TAT"].'</td></tr>';
               

            }
             echo '</tbody></table><br />';
            }
            else{
                echo "No Service Avai.";
            }
            }
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        
    }else{
        header("Location: index.php");
    }
    
    
    
    
    
    $conn = null;
    
    
    
    ?>


</div>

</body>
</html>
