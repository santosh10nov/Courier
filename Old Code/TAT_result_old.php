
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
<li><a href="#">Generate AirwayBill</a></li>
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
        
        include 'Calltransittime.php';
        include 'FedExTAT.php';

        
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
            $stmt1 = $conn->prepare("Select * from `transit_time` where `vendor_name`='FedEx' AND `from_pin`=$from_pin  AND `to_pin` = $to_pin AND `pickup_date`='$pickup_date'  AND `pickup_Time`='$pickup_time' order by `added_date` desc limit 1");
            $stmt1->execute();
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            $row1= $stmt1->fetch();
            
            $fd_pickupdate=$row1["pickup_date"];
            $fd_deliverydate=$row1["ExpectedDateDelivery"];
            $fd_tat=$row1["TAT"];
            $fd_product= "Constant";
            
            
            ///////////////////////////////////////////////////////////////
            ////////////Bluedart Availabile///////////////////////////////
            //////////////////////////////////////////////////////////////
    
            $stmt2 = $conn->prepare("Select * from `transit_time` where `vendor_name`='BlueDart' AND `from_pin`=$from_pin  AND `to_pin` = $to_pin AND `pickup_date`='$pickup_date'  AND `pickup_Time`='$pickup_time' order by `added_date` desc limit 1");
            $stmt2->execute();
            $stmt2->setFetchMode(PDO::FETCH_ASSOC);
            $row2 = $stmt2->fetch();
            
            $bd_pickupdate=$row2["pickup_date"];
            $bd_deliverydate=$row2["ExpectedDateDelivery"];
            $bd_tat=$row2["TAT"];
            $bd_product= "Constant";
            
            
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
            
            
            echo "Pickup Location: " .$pickup_location."  (".$from_pin.")   ========================>  ";
            echo "Destination Location: " .$destination_location."  (".$to_pin.")<br><br>";
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>Service Provider Name</th><th>Product Name</th><th>Pickup Date</th><th>Delivery Date</th><th>TAT</th></tr></thead><tboby>';
            echo '<tr><td>BlueDart</td><td>'.$bd_product.'</td><td>'.$bd_pickupdate.'</td><td>'.$bd_deliverydate.'</td><td>'.$bd_tat.'</td></tr>';
            echo '<tr><td>FedEx</td><td>'.$fd_product.'</td><td>'.$fd_pickupdate.'</td><td>'.$fd_deliverydate.'</td><td>'.$fd_tat.'</td></tr>';
            echo '</tbody></table><br />';
         
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
