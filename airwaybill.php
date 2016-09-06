
<!DOCTYPE html>
<html lang="en">
<head>
<title>rShipper Service</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="./js/pdfobject.js"></script>
<style>
.pdfobject-container { height: 500px;}
.pdfobject { border: 1px solid #666; }
    </style>
    
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
    
    
    <form class="form-horizontal" action="airwaybill.php" role="form">
    
    <div class="row">
    <div class="col-sm-4" id="info">
    <button type="button" class="btn btn-danger btn-sm" href="Generate_AirwayBill.php" >Cancel AirwayBill</button>
    <button type="button" class="btn btn-info btn-sm">Modify AirwayBill</button>
    <button type="button" class="btn btn-success btn-sm">Generate New AirwayBill</button>
    </div>
    
    <div class="col-sm-8" id="pdf">
    
    </div>
    </div>
    
    </form>
    </div>
    
    </body>
    </html>
    
    
    
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
        $account_number=$_POST["accountnumber"];
        $uid=$_POST["UID"];
        
        $from_pin=$_POST["from_pin"];
        $to_pin=$_POST["to_pin"];
        $COD=$_POST["COD"];
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
        
        $package_details=array($TotalWeight,$length,$breath,$height,$purpose,$cost);
        
        $status="";
        
        $servername = "127.0.0.1";
        $username = "root";
        $pass = "yesbank";
        $dbname = "transporter";
        
        try{
            
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            if($couriervendor=="FedEx"){
                
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
                
                
                if($service=="PRIORITY_OVERNIGHT" &&  $service_avi_level1!=0 ){
                    
                    include './AirwayBill/FedEx/ShipWebServiceClient.php';
                    
                }
                
                elseif($service=="STANDARD_OVERNIGHT" &&  $service_avi_level2!=0){
                    
                    include './AirwayBill/FedEx/ShipWebServiceClient.php';
                }
                
                else{
                    
                    $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','FedEx','$service','No Service','')");
                    $stmt5->execute();
                    
                    $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='FedEx' AND `AWB_Status` ='No Service' order by `API_Hit_Date` DESC");
                    $stmt6->execute();
                    
                    $stmt6->setFetchMode(PDO::FETCH_ASSOC);
                    $row6 = $stmt6->fetch();
                    
                    $AWB_UID=$row6['UID'];
                    
                    
                    $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Parties`(`Shipper_Name`, `Shipper_Comp`, `Shipper_Address`, `Receiver_Name`, `Receiver_Comp`, `Receiver_Address`,`AWB_UID`) VALUES ('$sender_info[1]','$sender_info[2]','$sender_info[3].$sender_info[4].$sender_info[5].$sender_info[0]','$receiver_info[1]','$receiver_info[2]','$receiver_info[3].$receiver_info[4].$receiver_info[5].$receiver_info[0]','$AWB_UID') ");
                    
                    $stmt7->execute();
                    $status="No Service";
                    
                }
                
                if($status=="Success"){
                    ?>
                    
                    <script>PDFObject.embed('<?php echo "./AirwayBill/FedEx/AirwayBill/$filename";?>', "#pdf");</script>
                    
                    <?php
                }
                elseif($status=="Error"){
                    echo "Sorry, Some Error Occurred";
                }
                elseif($status=="No Service"){
                    
                    echo "Sorry, No Service";
                }

            }
            elseif($couriervendor=="BlueDart"){
                
                
                $stmt3 = $conn->prepare("Select * from `bluedart_service_avii1` where `pincode`=$from_pin");
                $stmt3->execute();
                $stmt3->setFetchMode(PDO::FETCH_ASSOC);
                $row3 = $stmt3->fetch();
                
                
                $stmt4 = $conn->prepare("Select * from `bluedart_service_avii1` where `pincode`=$to_pin");
                $stmt4->execute();
                $stmt4->setFetchMode(PDO::FETCH_ASSOC);
                $row4 = $stmt4->fetch();
                
                $service_avi_level4=$row3["ApexInbound"]*$row4["ApexOutbound"]*$row3["classification"]*$row4["classification"];
                
                
                $service_avi_level5=$row3["DomesticPriorityInbound"]*$row4["DomesticPriorityOutbound"]*$row3["classification"]*$row4["classification"];
                
                
                $service_avi_level6=$row3["GroundInbound"]*$row4["GroundOutbound"]*$row3["classification"]*$row4["classification"];
                
                
                $service_avi_level7=$row3["eTailCODAirInbound"]*$row4["eTailCODAirOutbound"]*$row3["classification"]*$row4["classification"];
                
                
                $service_avi_level8=$row3["eTailCODGroundInbound"]*$row4["eTailCODGroundOutbound"]*$row3["classification"]*$row4["classification"];
                
                
                $service_avi_level9=$row3["eTailPrePaidAirInbound"]*$row4["eTailPrePaidAirOutbound"]*$row3["classification"]*$row4["classification"];
                
                
                $service_avi_level10=$row3["eTailPrePaidGroundInbound"]*$row4["eTailPrePaidGroundOutbound"]*$row3["classification"]*$row4["classification"];
                
                
                if($service=="Apex" && $service_avi_level4!=0){
                    
                    include './AirwayBill/BlueDart/CallAwbService.php';
                    
                }
                elseif($service=="Domestic Priority" && $service_avi_level5!=0){
                    
                    include './AirwayBill/BlueDart/CallAwbService.php';
                }
                elseif($service=="Ground" && $service_avi_level6!=0){
                    
                    include './AirwayBill/BlueDart/CallAwbService.php';
                }
                elseif($service=="eTailCODAir" && $service_avi_level7!=0){
                    
                    include './AirwayBill/BlueDart/CallAwbService.php';
                }
                elseif($service=="eTailCODGround" && $service_avi_level8!=0){
                    
                    include './AirwayBill/BlueDart/CallAwbService.php';
                }
                elseif($service=="eTailPrePaidAir" && $service_avi_level9!=0){
                    
                    include './AirwayBill/BlueDart/CallAwbService.php';
                }
                elseif($service=="eTailPrePaidGround" && $service_avi_level10!=0){
                    
                    include './AirwayBill/BlueDart/CallAwbService.php';
                }
                else{
                    
                    $stmt5 = $conn->prepare("INSERT INTO `AirwayBill`(`ShipperName`, `ReceiverName`, `COD`, `PackageCount`, `ReferenceID`, `AWB_Date`, `CourierVendor`, `CourierService`, `AWB_Status`, `AWB_Link`) VALUES ('$sender_info[1]','$receiver_info[1]','$COD',$packagecount,'$uid','$shipment_date','BlueDart','$service','No Service','')");
                    $stmt5->execute();
                    
                    $stmt6 = $conn->prepare(" SELECT * FROM AirwayBill WHERE `ShipperName`='$sender_info[1]' AND `ReceiverName`= '$receiver_info[1]' AND `COD`= '$COD' AND `ReferenceID`='$uid' AND `AWB_Date`='$shipment_date' AND `CourierVendor`='BlueDart' AND `AWB_Status` ='No Service' order by `API_Hit_Date` DESC");
                    $stmt6->execute();
                    
                    $stmt6->setFetchMode(PDO::FETCH_ASSOC);
                    $row6 = $stmt6->fetch();
                    
                    $AWB_UID=$row6['UID'];
                    
                    
                    $stmt7=$conn->prepare("INSERT INTO `AirwayBill_Parties`(`Shipper_Name`, `Shipper_Comp`, `Shipper_Address`, `Receiver_Name`, `Receiver_Comp`, `Receiver_Address`,`AWB_UID`) VALUES ('$sender_info[1]','$sender_info[2]','$sender_info[3].$sender_info[4].$sender_info[5].$sender_info[0]','$receiver_info[1]','$receiver_info[2]','$receiver_info[3].$receiver_info[4].$receiver_info[5].$receiver_info[0]','$AWB_UID') ");
                    
                    $stmt7->execute();
                    
                    $status="No Service";
                }
                
                
                if($status=="Success"){
                    ?>
                    <script>PDFObject.embed('<?php echo "./AirwayBill/BlueDart/AirwayBill/$filename";?>', "#pdf");</script>
                    
                    <?php
                }
                elseif($status=="Error"){
                    echo "Sorry, Some Error Occurred";
                }
                elseif($status=="No Service"){
                    
                    echo "Sorry, No Service";
                }
            }
            
            
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    }
    else{
        header("Location: index.php");
    }
    
    ?>
