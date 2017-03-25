<?php
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    $action=$_GET['action'];
    $output="";
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    
    
    
    //$arr=array($name_comp,$name,$companyname,$address,$city,$state,$pincode,$phone,$fav);
    
    //print_r($arr);
    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        
        if($action=="select"){
            
            $row_count = $_GET['row_count'];
            
            $stmt1 = $conn->prepare("SELECT * FROM `AirwayBill` LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID WHERE AirwayBill.AWB_Status='Success' ORDER BY AirwayBill.UID DESC limit $row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Shipper Details</th><th>Receiver Details</th><th>Shipment Date</th><th>Courier Vendor</th><th>Airway Bill Number</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Select All &nbsp<INPUT type="checkbox" onchange="checkAll(this)" name="chk[]" /></th><th>Courier Vendor</th><th>Airway Bill Number</th><th>Shipment Date</th><th>Pickup Date</th><th>Pickup Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    
                    echo '<tr><td><input type="checkbox" name="" value=""></td><td>'.$row["CourierVendor"].'</td><td>'.$row["Airwaybill_Number"].'</td><td>'.$row["AWB_Date"].'</td><td>XXX</td><td>XXX</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button></a></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }
        
        
        
        elseif($action=="pagecount"){
            
            $row_count = $_GET['row_count'];
            
            $stmt1= $conn->prepare("SELECT * FROM `AirwayBill` LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID WHERE AirwayBill.AWB_Status='Success' ORDER BY AirwayBill.UID DESC");
            
            $stmt1->execute();
            
            $countnumrows = $stmt1->rowCount();
            
            $pagecount=ceil($countnumrows/$row_count);
            
            echo $pagecount;
            
        }
        
        
        
        elseif($action=="changePagination"){
            
            $page=$_GET['page'];
            $row_count = $_GET['row_count'];
            
            $upperlimit=$page*$row_count;
            $lowerlimit=$upperlimit-$row_count;
            
            
            $stmt1 = $conn->prepare("SELECT * FROM `AirwayBill` LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID WHERE AirwayBill.AWB_Status='Success' ORDER BY AirwayBill.UID DESC limit $lowerlimit,$row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Shipper Details</th><th>Receiver Details</th><th>Shipment Date</th><th>Courier Vendor</th><th>Airway Bill Number</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Select All &nbsp<INPUT type="checkbox" onchange="checkAll(this)" name="chk[]" /></th><th>Courier Vendor</th><th>Airway Bill Number</th><th>Shipment Date</th><th>Pickup Date</th><th>Pickup Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                $i=$lowerlimit+1;
                while($row = $stmt1->fetch()){
                    
                    echo '<tr><td><input type="checkbox" name="" value=""></td><td>'.$row["CourierVendor"].'</td><td>'.$row["Airwaybill_Number"].'</td><td>'.$row["AWB_Date"].'</td><td>XXX</td><td>XXX</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button></a></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }
        
        elseif($action=="displayrows"){
            
            $row_count = $_GET['row_count'];
            
            $stmt1 = $conn->prepare("SELECT * FROM `AirwayBill` LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID WHERE AirwayBill.AWB_Status='Success' ORDER BY AirwayBill.UID DESC limit $row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Shipper Details</th><th>Receiver Details</th><th>Shipment Date</th><th>Courier Vendor</th><th>Airway Bill Number</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Select All &nbsp<INPUT type="checkbox" onchange="checkAll(this)" name="chk[]" /></th><th>Courier Vendor</th><th>Airway Bill Number</th><th>Shipment Date</th><th>Pickup Date</th><th>Pickup Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    
                    echo '<tr><td><input type="checkbox" name="" value=""></td><td>'.$row["CourierVendor"].'</td><td>'.$row["Airwaybill_Number"].'</td><td>'.$row["AWB_Date"].'</td><td>XXX</td><td>XXX</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button></a></td></tr>';
                    
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
            
        }
        
        elseif($action=="search"){
            
            $shipper_name=$_GET['shipper_name'];
            $receiver_name=$_GET['receiver_name'];
            $shipment_date=$_GET['shipment_date'];
            $vendor=$_GET['vendor'];
            $airwaybill_number=$_GET['airwaybill_number'];
            $row_count = $_GET['row_count'];
            
            $stmt1 = $conn->prepare("SELECT * FROM `AirwayBill` LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID WHERE AirwayBill.AWB_Status='Success' AND `AirwayBill`.`ShipperName` LIKE '%$shipper_name%' AND `AirwayBill`.`ReceiverName` LIKE '%$receiver_name%' AND `AirwayBill`.`AWB_Date` LIKE '%$shipment_date%' AND `AirwayBill`.`CourierVendor` LIKE '%$vendor%' AND `AirwayBill`.`Airwaybill_Number` LIKE '%$airwaybill_number%' ORDER BY `AirwayBill`.`UID` DESC limit $row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Shipper Details</th><th>Receiver Details</th><th>Shipment Date</th><th>Courier Vendor</th><th>Airway Bill Number</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Select All &nbsp<INPUT type="checkbox" onchange="checkAll(this)" name="chk[]" /></th><th>Courier Vendor</th><th>Airway Bill Number</th><th>Shipment Date</th><th>Pickup Date</th><th>Pickup Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    echo '<tr><td><input type="checkbox" name="" value=""></td><td>'.$row["CourierVendor"].'</td><td>'.$row["Airwaybill_Number"].'</td><td>'.$row["AWB_Date"].'</td><td>XXX</td><td>XXX</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button></a></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }
        
        elseif($action=="select_courier"){
            
       
            $couriervendor = $_GET['couriervendor'];
            $row_count=$_GET['row_count'];
            
        
            
            $stmt1 = $conn->prepare("SELECT * FROM `pickup` left join AirwayBill on AirwayBill.UID=pickup.UID LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID where `Courier_Vendor`='$couriervendor' and `Pickup_Status`='Pending' ORDER BY AirwayBill.UID DESC limit $row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Shipper Details</th><th>Receiver Details</th><th>Shipment Date</th><th>Courier Vendor</th><th>Airway Bill Number</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Select All &nbsp<INPUT type="checkbox" onchange="checkAll(this)" name="chk[]" /></th><th>Airway Bill Number</th><th>Shipment Date</th><th>Batch ID</th><th>Pickup Date</th><th>Pickup Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    
                    echo '<tr><td><input type="checkbox" name="checked" value='.$row["UID"].'></td><td>'.$row["AWB_Number"].'</td><td>'.$row["AWB_Date"].'</td><td>XXX</td><td>XXX</td><td>XXX</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button></a></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
            
            
        }
        
        elseif($action=="Schedule_Pickup"){
            
            $UID = $_GET['UID'];
            $userid=$_GET['userid'];
            
            $vendorid=$_GET['vendorid'];
            $pincode=$_GET['pincode'];
            $name=$_GET['name'];
            $com_name=$_GET['com_name'];
            $address=$_GET['address'];
            $city=$_GET['city'];
            $state=$_GET['state'];
            $phone=$_GET['phone'];
            
            
            $couriervendor=$_GET['couriervendor'];
            $pickupdate=$_GET['pickupdate'];
            //$pickupdate=strtotime($pickupdate);
            $pickuptime=$_GET['pickuptime'];
            $comp_closing_time=$_GET['comp_closing_time'];
            
            
            
            function randbatchid($length){
                
                $chars = "0123456789";
                return substr(str_shuffle($chars),0,$length);
            }
            
            
            do{
                $batchid="#P".randbatchid(10);
                
                
                $stmt1 = $conn->prepare("Select * from pickup where Batch_ID= '$batchid' ");
                $stmt1->execute();
                
                $numrows = $stmt1->rowCount();
            }
            while($numrows>0);
            
                $stmt2 = $conn->prepare("UPDATE `pickup` SET Batch_ID= '$batchid', Pickup_Status='Under Process',USERID='$userid'  WHERE UID IN ($UID) ");
                $stmt2->execute();
                
                $stmt3 = $conn->prepare("INSERT INTO PICKUP_BATCH_DETAILS VALUES('','$batchid','$vendorid','$pincode','$name','$com_name','$address','$city','$state','$phone','$couriervendor','$pickupdate','$pickuptime','$comp_closing_time')");
                $stmt3->execute();
            
            echo $batchid;
        
        }
        
        
        
        
    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    ?>