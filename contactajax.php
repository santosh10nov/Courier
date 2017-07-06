<?php
    
    require_once 'dbconfig.php';
    
    $action=$_GET['action'];
    $userid=$_GET['id'];
    //$CompID=$_GET['Comp'];
    
    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        if($action=="view"){
            
            $stmt1 = $conn->prepare("Select * from contact_list where `UpdatedBy`='$userid' AND fav!=0 order by `Name`");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-sm-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Vendor ID</th><th>Name</th><th>Address</th><th>Company Name</th><th>Phone Number</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tboby>';
                echo '<tr><td colspan="6" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-sm-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Vendor ID</th><th>Name</th><th>Address</th><th>Company Name</th><th>Phone Number</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tboby>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    
                    echo '<tr><td>'.$row["vendor_id"].'</td><td>'.$row["Name"].'</td><td>'.$row["Address"]." ".$row["City"]." ".$row["State"]." ".$row["Pincode"].'</td><td>'.$row["Company"].'</td><td>'.$row["phone"].'</td><td><button type="button" class="btn btn-sm btn-primary" onclick="EditContact(\''.$row["id"].'\',\''.$row["Name"].'\',\''.$row["Company"].'\',\''.$row["Address"].'\',\''.$row["City"].'\',\''.$row["State"].'\',\''.$row["Pincode"].'\',\''.$row["phone"].'\',\''.$row["vendor_id"].'\')">Edit</button>&nbsp&nbsp<button type="button" class="btn btn-sm btn-danger" onclick="DeleteContact(\''.$row["id"].'\')">Delete</button></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }
        elseif($action=="delete"){
            
            $id=$_GET['id'];
            
            $stmt1 = $conn->prepare("UPDATE `contact_list` SET fav=0 WHERE `id`='$id'");
            $stmt1->execute();
            
            echo "Success";
            
        }
        elseif($action=="insert"){
            
            $pincode=$_GET['pin'];
            $name=$_GET['name'];
            $address=$_GET['address'];
            $city=$_GET['city'];
            $state=$_GET['state'];
            $phone=$_GET['phone'];
            $companyname=$_GET['companyname'];
            $fav=1;
            $vendorid=$_GET['vendorid'];
            $name_comp=$name."-".$companyname;
            
            $arr=array($name_comp,$name,$companyname,$address,$city,$state,$pincode,$phone,$fav,$vendorid);
            
            $stmt1 = $conn->prepare("INSERT INTO contact_list (NameCompany,Name,Company,Address,City,State,Pincode,phone,fav,vendor_id,`UpdatedBy`) VALUES ('$arr[0]','$arr[1]','$arr[2]','$arr[3]','$arr[4]','$arr[5]','$arr[6]','$arr[7]','$arr[8]','$arr[9]','$userid') ");
            $stmt1->execute();
            
            echo "Success";
            
            
        }
        
        elseif($action=="update"){
            
            $pincode=$_GET['pin'];
            $name=$_GET['name'];
            $address=$_GET['address'];
            $city=$_GET['city'];
            $state=$_GET['state'];
            $phone=$_GET['phone'];
            $companyname=$_GET['companyname'];
            $fav=2;
            $vendorid=$_GET['vendorid'];
            $name_comp=$name."-".$companyname;
            $id=$_GET['serial_id'];
            
            $arr=array($name_comp,$name,$companyname,$address,$city,$state,$pincode,$phone,$fav,$vendorid);
            
            $stmt1 = $conn->prepare("UPDATE `contact_list` SET `NameCompany` = '$arr[0]', `Name` = '$arr[1]', `Company` = '$arr[2]', `Address` = '$arr[3]', `City` = '$arr[4]', `State` = '$arr[5]', `Pincode` = '$arr[6]', `phone` = '$arr[7]',`fav`=2,`vendor_id`='$arr[9]' WHERE `contact_list`.`id` = $id");
            $stmt1->execute();
            
            echo "Success";
            
            
        }
        
        
    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    
    ?>
