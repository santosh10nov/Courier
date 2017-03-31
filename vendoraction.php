<?php
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    $action=$_GET['action'];
    $output="";
    
    require_once 'dbconfig.php';
    
    
    
    //$arr=array($name_comp,$name,$companyname,$address,$city,$state,$pincode,$phone,$fav);
    
    //print_r($arr);
    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if($action=="select"){
            $stmt1 = $conn->prepare("SELECT * FROM `courier_vendor_details` ");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-xl-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Name</th><th>Customer-Code/Login ID</th><th>Account Number/ Meter</th><th>Key</th><th>Password</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tboby>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-xl-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Name</th><th>Customer-Code/Login ID</th><th>Account Number/ Meter</th><th>Key</th><th>Password</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tboby>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    
                    echo '<tr><td>'.$i.'</td><td>'.$row["othervendor"].'</td><td>'.$row["login_id"].'</td><td>'.$row["account_number"].'</td><td>'.$row["account_key"].'</td><td>'.$row["password"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="editvendor(\''.$row["name"].'\',\''.$row["login_id"].'\',\''.$row["account_number"].'\',\''.$row["account_key"].'\',\''.$row["password"].'\',\''.$row["serial_id"].'\')">Edit</button>&nbsp&nbsp<button type="button" class="btn btn-sm btn-danger" onclick="deletevendor(\''.$row["name"].'\',\''.$row["login_id"].'\',\''.$row["account_number"].'\',\''.$row["account_key"].'\',\''.$row["password"].'\',\''.$row["serial_id"].'\')">Delete</button></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
 
        }
        
        elseif($action=="insert"){
            
            $name=$_GET['vendor_name'];
            $login_id=$_GET['login_id'];
            $account_number=$_GET['account_number'];
            $account_key=$_GET['account_key'];
            $password=$_GET['password'];
            $other=$_GET['other'];
            
            
            
            
            $stmt1 = $conn->prepare("INSERT INTO `courier_vendor_details` (`name`, `login_id`, `account_number`, `account_key`, `password`,`othervendor`) VALUES ('$name','$login_id','$account_number','$account_key','$password','$other')");
            
            $stmt1->execute();
            
            
            $stmt2 = $conn->prepare("SELECT * FROM `courier_vendor_details` ");
            
            $stmt2->execute();
            
            $numrows = $stmt2->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-xl-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Name</th><th>Customer-Code/Login ID</th><th>Account Number/ Meter</th><th>Key</th><th>Password</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tboby>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class="col-xl-12  table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Name</th><th>Customer-Code/Login ID</th><th>Account Number/ Meter</th><th>Key</th><th>Password</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tboby>';
                $i=1;
                while($row = $stmt2->fetch()){
                    
                    
                    echo '<tr><td>'.$i.'</td><td>'.$row["othervendor"].'</td><td>'.$row["login_id"].'</td><td>'.$row["account_number"].'</td><td>'.$row["account_key"].'</td><td>'.$row["password"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="editvendor(\''.$row["name"].'\',\''.$row["login_id"].'\',\''.$row["account_number"].'\',\''.$row["account_key"].'\',\''.$row["password"].'\',\''.$row["serial_id"].'\')">Edit</button>&nbsp&nbsp<button type="button" class="btn btn-sm btn-danger" onclick="deletevendor(\''.$row["name"].'\',\''.$row["login_id"].'\',\''.$row["account_number"].'\',\''.$row["account_key"].'\',\''.$row["password"].'\',\''.$row["serial_id"].'\')">Delete</button></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
            
            
        }
        
        elseif($action=="delete"){
            $name=$_GET['vendor_name'];
            $login_id=$_GET['login_id'];
            $account_number=$_GET['account_number'];
            $account_key=$_GET['account_key'];
            $password=$_GET['password'];
            $id=$_GET['id'];
            
            
            
            $stmt1 = $conn->prepare("DELETE FROM `courier_vendor_details` WHERE `serial_id`=$id");
            
            $stmt1->execute();
            
            
            $stmt2 = $conn->prepare("SELECT * FROM `courier_vendor_details` ");
            
            $stmt2->execute();
            
            $numrows = $stmt2->rowCount();
            
            
            if($numrows==0){
                echo ' <table class="table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Name</th><th>Customer-Code/Login ID</th><th>Account Number/ Meter</th><th>Key</th><th>Password</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tboby>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class="table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Name</th><th>Customer-Code/Login ID</th><th>Account Number/ Meter</th><th>Key</th><th>Password</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tboby>';
                $i=1;
                while($row = $stmt2->fetch()){
                    
                    
                    echo '<tr><td>'.$i.'</td><td>'.$row["othervendor"].'</td><td>'.$row["login_id"].'</td><td>'.$row["account_number"].'</td><td>'.$row["account_key"].'</td><td>'.$row["password"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="editvendor(\''.$row["name"].'\',\''.$row["login_id"].'\',\''.$row["account_number"].'\',\''.$row["account_key"].'\',\''.$row["password"].'\',\''.$row["serial_id"].'\')">Edit</button>&nbsp&nbsp<button type="button" class="btn btn-sm btn-danger" onclick="deletevendor(\''.$row["name"].'\',\''.$row["login_id"].'\',\''.$row["account_number"].'\',\''.$row["account_key"].'\',\''.$row["password"].'\',\''.$row["serial_id"].'\')">Delete</button></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }

            
            
        }
        
        
        elseif($action=="update"){
            
            $name=$_GET['vendor_name'];
            $login_id=$_GET['login_id'];
            $account_number=$_GET['account_number'];
            $account_key=$_GET['account_key'];
            $password=$_GET['password'];
            $id=$_GET['id'];
            $other=$_GET['other'];
            
            
            $stmt1 = $conn->prepare("Update `courier_vendor_details` SET `name`='$name',`login_id`='$login_id',`account_number`='$account_number',`account_key`='$account_key',`password`='$password', `othervendor`='$other' WHERE `serial_id`=$id");
            
            $stmt1->execute();
            
            
            $stmt2 = $conn->prepare("SELECT * FROM `courier_vendor_details` ");
            
            $stmt2->execute();
            
            $numrows = $stmt2->rowCount();
            
            
            if($numrows==0){
                echo ' <table class="table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Name</th><th>Customer-Code/Login ID</th><th>Account Number/ Meter</th><th>Key</th><th>Password</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tboby>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class="table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Name</th><th>Customer-Code/Login ID</th><th>Account Number/ Meter</th><th>Key</th><th>Password</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tboby>';
                $i=1;
                while($row = $stmt2->fetch()){
                    
                    
                    echo '<tr><td>'.$i.'</td><td>'.$row["othervendor"].'</td><td>'.$row["login_id"].'</td><td>'.$row["account_number"].'</td><td>'.$row["account_key"].'</td><td>'.$row["password"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="editvendor(\''.$row["name"].'\',\''.$row["login_id"].'\',\''.$row["account_number"].'\',\''.$row["account_key"].'\',\''.$row["password"].'\',\''.$row["serial_id"].'\')">Edit</button>&nbsp&nbsp<button type="button" class="btn btn-sm btn-danger" onclick="deletevendor(\''.$row["name"].'\',\''.$row["login_id"].'\',\''.$row["account_number"].'\',\''.$row["account_key"].'\',\''.$row["password"].'\',\''.$row["serial_id"].'\')">Delete</button></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }

            
            
        }
        
        elseif($action=="vendornameList"){
            
            $vendorname=$_GET['vendor_name'];
            
            $stmt1 = $conn->prepare("SELECT * FROM `courier_vendor_details` WHERE `name` ='$vendorname'");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            
            $response = array();
            while($row = $stmt1->fetch())
            {
                $x['accountvalue']=$row['account_number'];
                $x['account_number']=$row['account_number'];
                $response[] = $x;
            }
            echo json_encode($response);
            
        }
        
        elseif($action=="CourierVendorList"){
            
            $stmt1 = $conn->prepare("SELECT * FROM `courier_vendor_details` ");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            
            $response = array();
            while($row = $stmt1->fetch())
            {
                $x['value']=$row['othervendor'];
                $x['venndor_name']=$row['othervendor'];
                $response[] = $x;
            }
            echo json_encode($response);
        }
        
        elseif($action=="DispatchCourierVendorList"){
            
            $id=$_GET['id'];
            
            $stmt1 = $conn->prepare("SELECT DISTINCT `CourierVendor` as `Vendor` FROM `AirwayBill` where AWB_Status='Success' and CreatedByUserID='$id'");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            
            $response = array();
            while($row = $stmt1->fetch())
            {
                $x['value']=$row['Vendor'];
                $x['venndor_name']=$row['Vendor'];
                $response[] = $x;
            }
            echo json_encode($response);
        }
        
        elseif($action=="AirwayBillCourierVendorList"){
            
            
            $id=$_GET['id'];
            
            $stmt1 = $conn->prepare("SELECT DISTINCT `CourierVendor` as `Vendor` FROM `AirwayBill` where AWB_Status in ('Success','Dispatched') and CreatedByUserID='$id'");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            
            $response = array();
            while($row = $stmt1->fetch())
            {
                $x['value']=$row['Vendor'];
                $x['venndor_name']=$row['Vendor'];
                $response[] = $x;
            }
            echo json_encode($response);
            
        }
        
        elseif($action=="ReceiveCourierList"){
            
            
            $id=$_GET['id'];
            
            $stmt1 = $conn->prepare("SELECT DISTINCT `CourierVendor` as `Vendor` FROM `receive_details` where AWB_Status in ('Received') and CreatedByUserID='$id'");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            
            $response = array();
            while($row = $stmt1->fetch())
            {
                $x['value']=$row['Vendor'];
                $x['venndor_name']=$row['Vendor'];
                $response[] = $x;
            }
            echo json_encode($response);
            
        }
  
    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    ?>
