<?php
    
    require_once 'dbconfig.php';
    
    
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $batch_id=$_GET['batchid'];
    
    ?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <title>rShipper Service- AirwayBill List</title>
        <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
                    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
                        <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
                            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
                                
                                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
                                <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    <body onload="list()">
        
        <nav class="navbar navbar-default" id="header_nav">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">rShipper</a>
                </div>
                <ul class="nav navbar-nav">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="Service_Availabilty.php">Service Availability</a></li>
                    <li><a href="TAT.php">TAT</a></li>
                    <li><a href="Generate_AirwayBill.php">Generate AirwayBill</a></li>
                    <li><a href="#">Book Pickup</a></li>
                    <li><a href="#">Track Courier</a></li>
                </ul>
            </div>
        </nav>
        
        <div class="container" id="container">
<h1 align="center">Pickup Batch ID <?php echo $batch_id; ?> </h1>
            <div class="panel-body" id="result" style="overflow: scroll;">
            <?php
                
                // Batch ID -> Vendor-> Pickup Time -> Pickup Date -> Sucess -> In Process -> Fail -> Total
                
                $stmt1 = $conn->prepare("SELECT distinct `AWB_Number`,`Courier_Vendor`,AirwayBill_Parties.Shipper_City,AirwayBill_Parties.Receiver_City,`Pickup_Status`,Pickup_Number,AirwayBill_Parties.AWB_UID FROM `pickup` INNER JOIN AirwayBill_Parties on AirwayBill_Parties.AWB_UID=pickup.UID INNER JOIN PickUp_Batch_Details on PickUp_Batch_Details.Batch_ID=pickup.Batch_ID WHERE pickup.`Batch_ID`='$batch_id'");
                
                $stmt1->execute();
                
                $numrows = $stmt1->rowCount();
                
                
                if($numrows==0){
                    echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                    echo '<thead style="background-color: gray; color:white; "><tr><th>Way Bill Number</th><th>Vendor</th><th>From</th><th>To</th><th>Pickup Status</th><th>Pickup Number</th><th>Action</th></tr></thead><tbody>';
                    echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                    echo '</tbody></table><br />';
                    
                }
                else if($numrows>0){
                    echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                    echo '<thead style="background-color: gray; color:white; "><tr><th>Way Bill Number</th><th>Vendor</th><th>From</th><th>To</th><th>Pickup Status</th><th>Pickup Number</th><th>Pickup Date</th><th>Action</th></tr></thead><tbody>';
                    $i=1;
                    while($row = $stmt1->fetch()){
                        
                        if($row[4]=="Success" || $row[4]=="Under Process"){
                            echo '<tr><td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td><td>'.$row[3].'</td><td>'.$row[4].'</td><td>'.$row[5].'</td><td>XXXX</td><td><button type="button" class="btn btn-sm btn-danger" onclick="cancelpickup('.$row[6].",".$row[0].')">Cancel Pickup</button></td></tr>';
                        }
                        elseif($row[4]=="Fail"|| $row[4]=="Cancel"){
                            echo '<tr><td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td><td>'.$row[3].'</td><td>'.$row[4].'</td><td>'.$row[5].'</td><td>XXXX</td><td><button type="button" class="btn btn-sm btn-success" onclick="cancelpickup('.$row[6].",".$row[0].')" >Retry Pickup</button></td></tr>';
                        }
                        
                        
                        $i++;
                    }
                    echo '</tbody></table><br />';
                    
                }

                
                ?>
            </div>

        </div>

<script>

function cancelpickup(UID,Airwaybill_Number){
    
    if (confirm("Are you sure you want to cancel pickup for AWB:"+Airwaybill_Number+"?") == true) {
        
        
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                
                alert(xmlhttp.responseText);
            }
        };
        xmlhttp.open("GET","CancelAWB.php?Airwaybill_Number="+Airwaybill_Number+"&UID="+UID+"&cancel_type=Pickup",true);
        xmlhttp.send();
        
    }
    return;
}

</script>
    </body>
</html>
