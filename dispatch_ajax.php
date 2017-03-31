<?php
    
    $action=$_GET['action'];
    
    
    require_once 'dbconfig.php';

    if($action=="dispatchlist"){
        
        $couriervendor = $_GET['couriervendor'];
        $userid=$_GET['id'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1 = $conn->prepare("SELECT * from AirwayBill LEFT join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=AirwayBill.UID where `AirwayBill`.`CourierVendor`='$couriervendor' and `AirwayBill`.`AWB_Status`='Success' and `AirwayBill`.`CreatedByUserID`='$userid' ORDER BY AWB_Date DESC ");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Select All</th><th>Airway Bill Number</th><th>Shipment Date</th><th>Receiver Name</th><th>Receiver City</th><th>Receiver Pincode</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-md-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Select All &nbsp<INPUT type="checkbox" onchange="checkAll(this)" name="chk[]" /></th><th>Airway Bill Number</th><th>Shipment Date</th><th>Receiver Name</th><th>Receiver City</th><th>Receiver Pincode</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    
                    echo '<tr><td><input type="checkbox" name="checked" value='.$row["UID"].'></td><td>'.$row["Airwaybill_Number"].'</td><td>'.$row["AWB_Date"].'</td><td>'.$row["Receiver_Name"].'</td><td>'.$row["Receiver_City"].'</td><td>'.$row["Receiver_Pincode"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Receiver_Name"].'\',\''.$row["Receiver_Comp"].'\',\''.$row["Receiver_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\',\''.$row["Receiver_City"].'\',\''.$row["Receiver_State"].'\',\''.$row["Receiver_Pincode"].'\',\''.$row["Receiver_Phone"].'\',\''.$row["Receiver_VendorID"].'\')">View</button></a></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
    }
    
    elseif($action=="MarkDispatch"){
        
        $userid=$_GET['id'];
        
        $UID = $_GET['UID'];
        $Name=$_GET['name'];
        $EmplID=$_GET['empid'];
        $DispatchDate=$_GET['DispatchDate'];
        $Val= explode(",",$UID);
        $size=sizeof($Val);
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1= $conn->prepare("UPDATE `AirwayBill` SET `AWB_Status`='Dispatched' where UID in ($UID)");
            
            $stmt1->execute();
            
            for($i=0;$i<$size;$i++){
                
                $stmt2= $conn->prepare("INSERT INTO `DispatchDetails`(`UID`, `Name`, `EmpID`, `DispatchDate`) VALUES ('$Val[$i]','$Name','$EmplID','$DispatchDate')");
                
                $stmt2->execute();
            }
            
            

            
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
    }
?>
