
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
<li><a href="Service_Availabilty.php">Generate AirwayBill</a></li>
<li><a href="TAT.php">TAT</a></li>
<li class="nav-item nav-link active"><a href="Generate_AirwayBill.php">Generate AirwayBill</a></li>
<li><a href="#">Book shipment</a></li>
<li><a href="#">Track Courier</a></li>
</ul>
</div>
</nav>

<div class="container">
<h1 align="center">Generate AirwayBill</h1>



<?php
    
    
    if (isset($_POST["from_pin"]) && !empty($_POST["from_pin"]) && isset($_POST["to_pin"]) && !empty($_POST["to_pin"])) {
        
        $userid="santy";
        
        $shipment_date=$_POST["date"];
        $nowtime = time();
        $fedex_shipment_date=$shipment_date."T".$nowtime;
        $convert_date = new DateTime($shipment_date);
        $shipment_date1 = date_format($convert_date, 'd-M-Y');

        
        $couriervendor=$_POST["couriervendor"];
        $service=$_POST["services"];
        $uid=$_POST["UID"];
        
        $from_pin=$_POST["from_pin"];
        $to_pin=$_POST["to_pin"];
        $CollectableAmount=$_POST["CollectableAmount"];
        $shipmentcontent=$_POST["shipmentcontent"];
        
        //////////////////////////////////////
        /////// Sender Details///////////////
        /////////////////////////////////////
        
        $sender_name=$_POST["sender_name"];
        $sender_company=$_POST["sender_com_name"];
        $sender_city=$_POST["sender_city"];
        $sender_state=$_POST["sender_state"];
        $sender_address=$_POST["sender_address"];
        $sender_phone=$_POST["sender_phone"];
        $sender_info=array($from_pin,$sender_name,$sender_company,$sender_address,$sender_city,$sender_state,$sender_phone);
        
        
        
        //////////////////////////////////////
        /////// Receiver Details///////////////
        /////////////////////////////////////
        
        $receiver_name=$_POST["receiver_name"];
        $receiver_company=$_POST["receiver_com_name"];
        $receiver_city=$_POST["receiver_city"];
        $receiver_state=$_POST["receiver_state"];
        $receiver_address=$_POST["receiver_address"];
        $receiver_phone=$_POST["receiver_phone"];
        $receiver_info=array($to_pin,$receiver_name,$receiver_company,$receiver_address,$receiver_city,$receiver_state,$receiver_phone);
        
        
        //////////////////////////////////////
        /////// Package Details///////////////
        /////////////////////////////////////
        
        
        $packagecount= $_POST["package_count"];
        $purpose=$_POST["purpose"];
        $cost=$_POST["invoice"];
        
        $TotalWeight=0;
        
        for($i=0;$i<$packagecount;$i++){
            $length=$_POST["length".$i];
            $breath=$_POST["breath".$i];
            $height=$_POST["height".$i];
            $weight=$_POST["weight".$i];
            $TotalWeight=$TotalWeight+$weight;
            
        }
        
        $commodity_count= $_POST["commodity_count"];
        
        
        
        
        
        /*echo $from_pin."<br/>";
        echo $to_pin."<br/>";
        echo $sender_name."<br/>";
        echo $sender_address."<br/>";
        echo $sender_city."<br/>";
        echo $sender_state."<br/>";
        echo $sender_phone."<br/>";
        echo $receiver_name."<br/>";
        echo $receiver_address."<br/>";
        echo $receiver_city."<br/>";
        echo $receiver_state."<br/>";
        echo $receiver_phone."<br/>";
        echo "Courier Vendor=>".$couriervendor."<br/>";
        echo "Service=>".$service."<br/>";
        echo date('c')."<br/>";
        echo "Shipment Date=>".$fedex_shipment_date;*/

        
        
        
        $servername = "127.0.0.1";
        $username = "root";
        $pass = "yesbank";
        $dbname = "transporter";
        
        
        if($couriervendor=="FedEx"){
            include './AirwayBill/FedEx/ShipWebServiceClient.php';
        }
        elseif($couriervendor=="BlueDart"){
            include './AirwayBill/BlueDart/CallAwbService.php';
        }
    }
    else{
        header("Location: index.php");
    }
    
    ?>

</div>

</body>
</html>
