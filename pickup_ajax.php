<?php
    
    require_once 'dbconfig.php';
    
    $action=$_GET['action'];
    
    //session_start();
    //$userid=$_SESSION['userSession'];
    
    
    if($action=="pickupstatus"){
        
       $userid=$_GET['id'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $result=array();
            
            $stmt1 = $conn->prepare("SELECT name FROM `courier_vendor_details` ORDER BY `othervendor` DESC");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            
            
            /////////////// Pickup Status Flow ////////////////////////////
            /////// Pending-> Under Process-> Scheduled -> Canceled///////
            //////////////////////////////////////////////////////////////
            
            
            
            
            if($numrows!=0){
                $i=0;
                foreach($stmt1->fetchAll() as $row1){
                    $result[$i]["CourierVendor"]=$row1["name"];
                    $CourierVendor=$row1["name"];
                    
                    
                    $stmt2 = $conn->prepare("SELECT * FROM `pickup` inner join AirwayBill on AirwayBill.UID=pickup.UID and AirwayBill.CreatedByUserID=$userid where AirwayBill.AWB_Status='Success' and `Courier_Vendor`='$CourierVendor' and `Pickup_Status`='Pending'  ");
                    $stmt2->execute();
                    $numrows2 = $stmt2->rowCount();
                    $result[$i]["Pending"]=$numrows2;
                    
                    
                    
                    $stmt3 = $conn->prepare("SELECT * FROM `pickup` inner join AirwayBill on AirwayBill.UID=pickup.UID and AirwayBill.CreatedByUserID=$userid  where AirwayBill.AWB_Status='Success' and `Courier_Vendor`='$CourierVendor' and `Pickup_Status` in ('Scheduled','Under Process')  ");
                    $stmt3->execute();
                    $numrows3 = $stmt3->rowCount();
                    $result[$i]["Scheduled"]=$numrows3;
                    
                    
                    $stmt4 = $conn->prepare("SELECT * FROM `pickup` inner join AirwayBill on AirwayBill.UID=pickup.UID and AirwayBill.CreatedByUserID=$userid  where AirwayBill.AWB_Status='Success' and `Courier_Vendor`='$CourierVendor' and `Pickup_Status`='Canceled' ");
                    $stmt4->execute();
                    $numrows4 = $stmt4->rowCount();
                    $result[$i]["Canceled"]=$numrows4;
                    
                    $result[$i]["total"]=$numrows2+$numrows3+$numrows4;
                    
                    
                    $i++;
                }
                echo '<table class="table table-bordered">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Courier Vendor</th><th>Pending Pickup</th><th>Scheduled Pickup</th><th>Canceled Pickup</th><th>Total</th></tr></thead><tboby>';
                for($j=0;$j<$i;$j++){
                    
                    if($result[$j]["Pending"]!=0){
                        
                        $pending='<a href="pending_pickup_list.php?userid='.$userid.'&vendor='.$result[$j]["CourierVendor"].'&status=Scheduled">'.$result[$j]["Pending"].'</a>';
                    }
                    else{
                        $pending=$result[$j]["Pending"];
                    }
                    
                    if($result[$j]["Scheduled"]!=0){
                        
                        $scheduled='<a href="scheduling_pickup_list.php?userid='.$userid.'&vendor='.$result[$j]["CourierVendor"].'&status=Scheduled">'.$result[$j]["Scheduled"].'</a>';
                    }
                    else{
                        
                        $scheduled=$result[$j]["Scheduled"];
                    }
                    if($result[$j]["Canceled"]!=0){
                        
                        $canceled='<a href="canceled_pickup_list.php?userid='.$userid.'&vendor='.$result[$j]["CourierVendor"].'&status=Canceled">'.$result[$j]["Canceled"].'</a>';
                    }
                    else{
                        
                        $canceled=$result[$j]["Canceled"];
                    }
                    
                    
                    echo '<tr><td>'.$result[$j]["CourierVendor"].'</td><td>'.$pending.'</td><td>'.$scheduled.'</td><td>'.$canceled.'</td><td>'.$result[$j]["total"].'</td></tr>';
                    
                    
                }
                echo '</tbody></table><br />';
                
            }
            else{
                echo "Plz. generate airwaywaybill to schdeule pickup";
            }
            
        }
        
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
    }
    
    
    
    else if($action=="CheckCutoff"){
        $vendor_name=$_GET['vendor_name'];
        $PickupDate=$_GET['PickupDate'];
        $PickupTime=$_GET['PickupTime'];
        $CompanyClosingTime=$_GET['CompanyClosingTime'];
        $pincode=$_GET['pincode'];
        
        if($vendor_name=="BlueDart"){
            
            try{
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                
                $stmt1 = $conn->prepare("SELECT * FROM `PickUp_Cutoff_BlueDart` where `Pincode`= $pincode ");
                $stmt1->execute();
                $stmt1->setFetchMode(PDO::FETCH_ASSOC);
                $row1 = $stmt1->fetch();
                
                
                $CutoffTime=$row1["Apex"];
                
                if(strtotime($PickupTime)<=strtotime($CutoffTime) ){
                    echo "Success";
                }
                else{
                    echo "Pickup Time is after Cut off Time";
                }
                
            }
            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            
            $conn=null;
            
        }
        
        
        elseif($vendor_name=="FedEx"){
            try{
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                
                $stmt1 = $conn->prepare("SELECT * FROM `PickUp_Cutoff_FedEx` where `Pincode`= $pincode ");
                $stmt1->execute();
                $stmt1->setFetchMode(PDO::FETCH_ASSOC);
                $row1 = $stmt1->fetch();
                
                
                $CutoffTime=$row1["PRIORITY_OVERNIGHT_CutoffTime"];
                $AccessTime=$row1["PRIORITY_OVERNIGHT_AccessTime"];
                
                if(strtotime($PickupTime)<=strtotime($CutoffTime) ){
                    $time=floor((strtotime($CompanyClosingTime)-strtotime($PickupTime))/3600);
                    
                    if($time>$AccessTime){
                        echo "Success";
                    }
                    else{
                        echo "Access Time Isssue";
                    }
                    
                }
                else{
                    echo "Pickup Time is after Cut off Time";
                }
                
            }
            catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            $conn=null;
        }
        
        
    }
    
    else if($action=="DisplayPending"){
        
        $couriervendor = $_GET['couriervendor'];
        $userid=$_GET['id'];
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1 = $conn->prepare("SELECT * FROM `pickup` inner join AirwayBill on AirwayBill.UID=pickup.UID and AirwayBill.CreatedByUserID=$userid  LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID where `Courier_Vendor`='$couriervendor' and `Pickup_Status`='Pending' and AirwayBill.AWB_Status='Success' ORDER BY AWB_Date DESC ");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Select All</th><th>Airway Bill Number</th><th>Shipper Name</th><th>Receiver Name</th><th>Receiver City</th><th>Receiver Pincode</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Select All &nbsp<INPUT type="checkbox" onchange="checkAll(this)" name="chk[]" /></th><th>Airway Bill Number</th><th>Shipper Name</th><th>Receiver Name</th><th>Receiver City</th><th>Receiver Pincode</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    
                    echo '<tr><td><input type="checkbox" name="checked" value='.$row["UID"].'></td><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"].'</td><td>'.$row["Receiver_Name"].'</td><td>'.$row["Receiver_City"].'</td><td>'.$row["Receiver_Pincode"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button></a></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
    }
    
    else if($action=="DisplayScheduling"){
        
        $couriervendor = $_GET['couriervendor'];
        $userid=$_GET['id'];
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1 = $conn->prepare("SELECT * FROM `pickup` inner join AirwayBill on AirwayBill.UID=pickup.UID and AirwayBill.CreatedByUserID=$userid  LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID left join PickUp_Batch_Details on PickUp_Batch_Details.Batch_ID = pickup.Batch_ID where pickup.Courier_Vendor = '$couriervendor' and `Pickup_Status` in ('Scheduled','Under Process') and AirwayBill.AWB_Status='Success' ORDER BY PickUp_Batch_Details.pickup_book_date DESC");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Pickup Date</th><th>Airway Bill Number</th><th>Shipper Name</th><th>Pickup Number</th><th>Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>' ;
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo  '<thead style="background-color: gray; color:white; "><tr><th>Pickup Date</th><th>Airway Bill Number</th><th>Shipper Name</th><th>Pickup Number</th><th>Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>' ;
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    if($row["Pickup_Status"]=="Scheduled"){
                        
                        $CancelButton='&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-sm btn-danger" onclick="Cancel(\''.$row["AWB_Number"].'\',\''.$row["UID"].'\')">Cancel Pickup</button>';
                        
                         echo '<tr><td>'.$row["pickup_book_date"].'</td><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"].'</td><td>'.$row["Pickup_Number"].'</td><td>'.$row["Pickup_Status"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button>'.$CancelButton.'</a></td></tr>';
                        
                    }
                    else{
                        echo '<tr><td>'.$row["pickup_book_date"].'</td><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"].'</td><td>'.$row["Pickup_Number"].'</td><td>'.$row["Pickup_Status"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button></a></td></tr>';

                    }
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
    }
    
    else if($action=="DisplayCanceled"){
        
        $couriervendor = $_GET['couriervendor'];
        $userid=$_GET['id'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1 = $conn->prepare("SELECT * FROM `pickup` inner join AirwayBill on AirwayBill.UID=pickup.UID and AirwayBill.CreatedByUserID=$userid LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID left join PickUp_Batch_Details on PickUp_Batch_Details.Batch_ID = pickup.Batch_ID where pickup.Courier_Vendor = '$couriervendor' and `Pickup_Status` in ('Canceled') and AirwayBill.AWB_Status='Success' ORDER BY PickUp_Batch_Details.pickup_book_date DESC");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Pickup Date</th><th>Airway Bill Number</th><th>Shipper Name</th><th>Pickup Number</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>' ;
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo  '<thead style="background-color: gray; color:white; "><tr><th>Pickup Date</th><th>Airway Bill Number</th><th>Shipper Name</th><th>Pickup Number</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>' ;
                $i=1;
                while($row = $stmt1->fetch()){
                  
                        echo '<tr><td>'.$row["pickup_book_date"].'</td><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"].'</td><td>'.$row["Pickup_Number"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button>&nbsp;&nbsp;<button type="button"  class="btn btn-sm btn-success" onclick="MovetoPending('.$row["AWB_Number"].')">Move to Pending</button></td></tr>';
                        
                   
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
    }
    
    else if($action=="EditPickupAddress"){
        
        $userid=$_GET['userid'];
        $name=$_GET['name'];
        $pincode=$_GET['pincode'];
        $CompanyName=$_GET['CompanyName'];
        $address=$_GET['address'];
        $city=$_GET['city'];
        $state=$_GET['state'];
        $phone=$_GET['phone'];
        
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt=$conn->prepare("SELECT * from `PreferPickupAddress` where `UserID`='$userid';");
            $stmt->execute();
            
            $numrows = $stmt->rowCount();
            
            if($numrows==0){
                
                $stmt1=$conn->prepare("INSERT INTO `PreferPickupAddress`(`UserID`, `Name`, `Pincode`, `CompanyName`, `Address`, `City`, `State`,`phone`) VALUES ('$userid','$name','$pincode','$CompanyName','$address','$city','$state','$phone');");
                $stmt1->execute();
            }
            else{
                
                $stmt1 = $conn->prepare("UPDATE `PreferPickupAddress` SET `Name`='$name',`Pincode`='$pincode',`CompanyName`='$CompanyName',`Address`='$address',`City`='$city',`State`='$state',`phone`='$phone' WHERE UserID='$userid'");
                
                $stmt1->execute();
                
            }
           
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
        
    }
    elseif($action=="Assign_Batch"){
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            
            $UID = $_GET['UID'];
            $userid=$_GET['userid'];
            
            
            $pincode=$_GET['pincode'];
            $name=$_GET['name'];
            $com_name=$_GET['com_name'];
            $address=$_GET['address'];
            $city=$_GET['city'];
            $state=$_GET['state'];
            $phone=$_GET['phone'];
            
            $couriercount=$_GET['couriercount'];
            $couriervendor=$_GET['couriervendor'];
            $pickupdate=$_GET['pickupdate'];
            //$pickupdate=strtotime($pickupdate);
            $pickuptime=$_GET['pickuptime'];
            $comp_closing_time=$_GET['comp_closing_time'];
            $Status="";
            
            
            
            
            function randbatchid($length){
                
                $chars = "0123456789";
                return substr(str_shuffle($chars),0,$length);
            }
            
            
            do{
                $batchid="#P".randbatchid(10);
                
                
                $stmt1 = $conn->prepare("Select * from pickup where Batch_ID= '$batchid' ");
                $stmt1->execute();
                
                $numrows = $stmt1->rowCount();
            }while($numrows>0);
            
            $stmt2 = $conn->prepare("UPDATE `pickup` SET Batch_ID= '$batchid', Pickup_Status='Under Process', USERID='$userid'  WHERE UID IN ($UID) ");
            $stmt2->execute();
            
            $stmt3=$conn->prepare("INSERT INTO `PickUp_Batch_Details`( `Batch_ID`, `pincode`, `name`, `Company_Name`, `Address`, `City`, `State`, `Phone`, `pickup_book_date`, `pickup_time`, `com_closing_time`) VALUES ('$batchid','$pincode','$name','$com_name','$address','$city','$state','$phone','$pickupdate','$pickuptime','$comp_closing_time')");
            
             $stmt3->execute();
            
         
            echo 'Wait for 10 minute while we get schedule pickup.';
                        
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }
    
    else if ($action=="Search"){
        
        $couriervendor=$_GET['couriervendor'];
        $pickupdate=$_GET['pickupdate'];
        $AWB_Number=$_GET['AWB_Number'];
        $PickupNumber=$_GET['PickupNumber'];
        $PickupStatus=$_GET['PickupStatus'];
        $userid=$_GET['id'];
        
        $str="SELECT * FROM `pickup` inner join AirwayBill on AirwayBill.UID=pickup.UID and AirwayBill.CreatedByUserID=$userid LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID left join PickUp_Batch_Details on PickUp_Batch_Details.Batch_ID = pickup.Batch_ID where pickup.Courier_Vendor = '$couriervendor'";
        
        if($pickupdate!=""){
            
            $str=$str."AND `pickup_book_date` ='$pickupdate'";
        }
        if($AWB_Number!=""){
            
             $str=$str."AND pickup.`AWB_Number`='$AWB_Number'";
        }
        if($PickupNumber!=""){
            
             $str=$str."AND pickup.`Pickup_Number`='$PickupNumber'";        }
        if($PickupStatus!=""){
            
             $str=$str."AND pickup.`Pickup_Status`='$PickupStatus'";
        }
        else{
            
            $str=$str."AND pickup.`Pickup_Status`in ('Under Process','Scheduled')";
        }
        
            
        $str=$str." ORDER BY PickUp_Batch_Details.pickup_book_date DESC";
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            
            
            $stmt1 = $conn->prepare($str);
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Pickup Date</th><th>Airway Bill Number</th><th>Shipper Name</th><th>Pickup Number</th><th>Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>' ;
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo  '<thead style="background-color: gray; color:white; "><tr><th>Pickup Date</th><th>Airway Bill Number</th><th>Shipper Name</th><th>Pickup Number</th><th>Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>' ;
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    
                    echo '<tr><td>'.$row["pickup_book_date"].'</td><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"].'</td><td>'.$row["Pickup_Number"].'</td><td>'.$row["Pickup_Status"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button></a></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
            
        }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;

        
    }
    
    else if ($action=="CancelSearch"){
        
        $couriervendor=$_GET['couriervendor'];
        $pickupdate=$_GET['pickupdate'];
        $AWB_Number=$_GET['AWB_Number'];
        $PickupNumber=$_GET['PickupNumber'];
        $PickupStatus=$_GET['PickupStatus'];
        $userid=1;
        
        $str="SELECT * FROM `pickup` inner join AirwayBill on AirwayBill.UID=pickup.UID and AirwayBill.CreatedByUserID=$userid  LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID left join PickUp_Batch_Details on PickUp_Batch_Details.Batch_ID = pickup.Batch_ID where pickup.Courier_Vendor = '$couriervendor'";
        
        if($pickupdate!=""){
            
            $str=$str."AND `pickup_book_date` ='$pickupdate'";
        }
        if($AWB_Number!=""){
            
            $str=$str."AND pickup.`AWB_Number`='$AWB_Number'";
        }
        if($PickupNumber!=""){
            
            $str=$str."AND pickup.`Pickup_Number`='$PickupNumber'";        }
        if($PickupStatus!=""){
            
            $str=$str."AND pickup.`Pickup_Status`='$PickupStatus'";
        }
        else{
            
            $str=$str."AND pickup.`Pickup_Status`in ('Under Process','Scheduled')";
        }
        
        
        $str=$str." ORDER BY PickUp_Batch_Details.pickup_book_date DESC";
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            
            $stmt1 = $conn->prepare($str);
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Pickup Date</th><th>Airway Bill Number</th><th>Shipper Name</th><th>Pickup Number</th><th>Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>' ;
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo  '<thead style="background-color: gray; color:white; "><tr><th>Pickup Date</th><th>Airway Bill Number</th><th>Shipper Name</th><th>Pickup Number</th><th>Status</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>' ;
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    
                    echo '<tr><td>'.$row["pickup_book_date"].'</td><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"].'</td><td>'.$row["Pickup_Number"].'</td><td>'.$row["Pickup_Status"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button>&nbsp;&nbsp;<button type="button"  class="btn btn-sm btn-success" onclick="MovetoPending('.$row["AWB_Number"].')">Move to Pending</button></a></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }
    
    else if($action=="Cancel"){
        
        $AWB_Number=$_GET['AWB_Number'];
        $UID=$_GET['UID'];
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            // Updating Pickup Cancelation details in Pickuo  Table
            $stmt=$conn->prepare("Update pickup SET `Pickup_Status`='Canceled',`Pickup_Number`='',`Scheduled_Pickup_Date`='' where AWB_Number='$AWB_Number'");
            $stmt->execute();
            
                        
            // Delete from tracking table////
            $stmt1=$conn->prepare("DELETE FROM `Tracking`  WHERE `AWB_Number`='$AWB_Number'");
            $stmt1->execute();
            
            
            //// DELETE from tracking history/////
            $stmt2=$conn->prepare(" DELETE FROM `TrackingHistory` WHERE `UID`='$UID' ");
            $stmt2->execute();
            
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;

        
    }
    
    else if($action=="MovetoPending"){
        
        $AWB_Number=$_GET['AWB_Number'];
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt=$conn->prepare("Update pickup SET `Pickup_Status`='Pending', is_locked=0 where AWB_Number='$AWB_Number'");
            
            $stmt->execute();
            
            
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }
    
    
    
    
    
    
    
    
    ?>
