
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
<li class="nav-item nav-link active"><a href="Service_Availabilty.php">Service Availability</a></li>
<li><a href="TAT.php">TAT</a></li>
<li><a href="Generate_AirwayBill.php">Generate AirwayBill</a></li>
<li><a href="#">Book Pickup</a></li>
<li><a href="#">Track Courier</a></li>
</ul>
</div>
</nav>

<div class="container">
<h1 align="center">Service Availability </h1>
<?php
    

        if (isset($_POST["from_pin"]) && !empty($_POST["from_pin"]) && isset($_POST["to_pin"]) && !empty($_POST["to_pin"])  ) {
         
            
            $from_pin=$_POST["from_pin"];
            $to_pin=$_POST["to_pin"];
            
            $servername = "127.0.0.1";
            $username = "root";
            $pass = "yesbank";
            $dbname = "transporter";
            
            try{
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
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
                
                $service_avi_level1=$row1["p_pickup"]*$row2["p_delivery"]*$row1["p_classification"]*$row2["p_classification"];  // FedEx Priority ///
                
                $service_avi_level2=$row1["s_pickup"]*$row2["s_delivery"]*$row1["s_classification"]*$row2["s_classification"];  ////// FedEx Standard///////
                
                $p_pickup1 = ($row1["p_pickup"]) ? 'Yes' : 'No';
                $p_delivery1= ($row2["p_delivery"]) ? 'Yes' : 'No';
                $p_product1= "Priority";
                
                $s_pickup2= ($row1["s_pickup"]) ? 'Yes' : 'No';
                $s_delivery2= ($row2["s_delivery"]) ? 'Yes' : 'No';
                $s_product2= "Standard";
                
                
                
                
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
                $bd_amount4=$row3["AirValueLimit"];
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
                
                
                ////////////////////////////////////////////////////////
                //////// Location Name ////////////////////////////////
                ///////////////////////////////////////////////////////
                
                $stmt5 = $conn->prepare("Select * from `city_pincode` where `pin`=$from_pin");
                $stmt5->execute();
                $stmt5->setFetchMode(PDO::FETCH_ASSOC);
                $row5 = $stmt5->fetch();
                
                
                $stmt6 = $conn->prepare("Select * from `city_pincode` where `pin`=$to_pin");
                $stmt6->execute();
                $stmt6->setFetchMode(PDO::FETCH_ASSOC);
                $row6 = $stmt6->fetch();
                
                $pickup_location=$row5["location"];
                $destination_location=$row6["location"];
                
                
                
                //////////////////////////////////////////////////////////
                /////////// Service Availability ////////////////////////
                /////////////////////////////////////////////////////////
                
                
                $fedex_service_avi_level= $service_avi_level1 + $service_avi_level2;
                $bluedart_service_avi_level=$service_avi_level4+$service_avi_level5+$service_avi_level6+$service_avi_level7+$service_avi_level8+$service_avi_level9+$service_avi_level10;
                
                echo "Pickup Location: " .$pickup_location."  (".$from_pin.")   ========================>  ";
                echo "Destination Location: " .$destination_location."  (".$to_pin.")<br><br>";
                
                
                
                if($fedex_service_avi_level==0 &&$bluedart_service_avi_level==0 ){
                    echo "Sorry, no service available"."<br>";
                }
                else{
                    echo '<table class="table table-bordered">';
                    echo '<thead><tr><th>Service Provider Name</th><th>Product Name</th><th>Courier Value Limit</th><th>Pickup</th><th>Delivery</th><th>Service Availability</th></tr></thead><tboby>';
                    if($service_avi_level1==1){
                        echo '<tr><td>FedEx</td><td>'.$p_product1.'</td><td></td><td>'.$p_pickup1.'</td><td>'.$p_delivery1.'</td><td>Yes</td></tr>';
                    }
                    if($service_avi_level1==2 ||$service_avi_level1==4 ){
                        echo '<tr><td>FedEx</td><td>'.$p_product1.'</td><td></td><td>'.$p_pickup1.'</td><td>'.$p_delivery1.'</td><td>ODA Location</td></tr>';
                    }
                    if($service_avi_level2==1){
                        echo '<tr><td>FedEx</td><td>'.$s_product2.'</td><td></td><td>'.$s_pickup2.'</td><td>'.$s_delivery2.'</td><td>Yes</td></tr>';
                    }
                    if($service_avi_level2==2 ||$service_avi_level2==4 ){
                        echo '<tr><td>FedEx</td><td>'.$s_product2.'</td><td></td><td>'.$s_pickup2.'</td><td>'.$s_delivery2.'</td><td>ODA Location</td></tr>';
                    }
                    /*if($service_avi_level3==1){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product3.'</td><td>'.$bd_pickup3.'</td><td>'.$bd_delivery3.'</td><td>Yes</td></tr>';
                    }
                    if($service_avi_level3==2 ||$service_avi_level3==4 ){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product3.'</td><td>'.$bd_pickup3.'</td><td>'.$bd_delivery3.'</td><td>ODA Location</td></tr>';
                    }*/
                    if($service_avi_level4==1){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product4.'</td><td>'.$bd_amount4.'</td><td>'.$bd_pickup4.'</td><td>'.$bd_delivery4.'</td><td>Yes</td></tr>';
                    }
                    if($service_avi_level4==2 ||$service_avi_level4==4 ){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product4.'</td><td>'.$bd_amount4.'</td><td>'.$bd_pickup4.'</td><td>'.$bd_delivery4.'</td><td>ODA Location</td></tr>';
                    }
                    if($service_avi_level5==1){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product5.'</td><td></td><td>'.$bd_pickup5.'</td><td>'.$bd_delivery5.'</td><td>Yes</td></tr>';
                    }
                    if($service_avi_level5==2 ||$service_avi_level5==4 ){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product5.'</td><td></td><td>'.$bd_pickup5.'</td><td>'.$bd_delivery5.'</td><td>ODA Location</td></tr>';
                    }
                    
                    if($service_avi_level6==1){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product6.'</td><td>'.$bd_amount6.'</td><td>'.$bd_pickup6.'</td><td>'.$bd_delivery6.'</td><td>Yes</td></tr>';
                    }
                    if($service_avi_level6==2 ||$service_avi_level6==4 ){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product6.'</td><td>'.$bd_amount6.'</td><td>'.$bd_pickup6.'</td><td>'.$bd_delivery6.'</td><td>ODA Location</td></tr>';
                    }
                    if($service_avi_level7==1){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product7.'</td><td>'.$bd_amount7.'</td><td>'.$bd_pickup7.'</td><td>'.$bd_delivery7.'</td><td>Yes</td></tr>';
                    }
                    if($service_avi_level7==2 ||$service_avi_level7==4 ){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product7.'</td><td>'.$bd_amount7.'</td><td>'.$bd_pickup7.'</td><td>'.$bd_delivery7.'</td><td>ODA Location</td></tr>';
                    }
                    if($service_avi_level8==1){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product8.'</td><td>'.$bd_amount8.'</td><td>'.$bd_pickup8.'</td><td>'.$bd_delivery8.'</td><td>Yes</td></tr>';
                    }
                    if($service_avi_level8==2 ||$service_avi_level8==4 ){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product8.'</td><td>'.$bd_amount8.'</td><td>'.$bd_pickup8.'</td><td>'.$bd_delivery8.'</td><td>ODA Location</td></tr>';
                    }
                    if($service_avi_level9==1){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product9.'</td><td>'.$bd_amount9.'</td><td>'.$bd_pickup9.'</td><td>'.$bd_delivery9.'</td><td>Yes</td></tr>';
                    }
                    if($service_avi_level9==2 ||$service_avi_level9==4 ){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product9.'</td><td>'.$bd_amount9.'</td><td>'.$bd_pickup9.'</td><td>'.$bd_delivery9.'</td><td>ODA Location</td></tr>';
                    }
                    if($service_avi_level10==1){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product10.'</td><td>'.$bd_amount10.'</td><td>'.$bd_pickup10.'</td><td>'.$bd_delivery10.'</td><td>Yes</td></tr>';
                    }
                    if($service_avi_level10==2 ||$service_avi_level10==4 ){
                        echo '<tr><td>Bluedart</td><td>'.$bd_product10.'</td><td>'.$bd_amount10.'</td><td>'.$bd_pickup10.'</td><td>'.$bd_delivery10.'</td><td>ODA Location</td></tr>';
                    }
                    
                    echo '</tbody></table><br />';
                    
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
