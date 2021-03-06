<?php
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    $pincode=$_GET['pin'];
    $name=$_GET['name'];
    $address=$_GET['address'];
    $city=$_GET['city'];
    $state=$_GET['state'];
    $phone=$_GET['phone'];
    $companyname=$_GET['companyname'];
    $fav=$_GET['fav'];
    $parties=$_GET['parties'];
    $name_comp=$name."-".$companyname;
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    
    
    $arr=array($name_comp,$name,$companyname,$address,$city,$state,$pincode,$phone,$fav);
    
    //print_r($arr);
    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt1 = $conn->prepare("Select * from contact_list where phone='$arr[7]' and Pincode=$pincode");
        $stmt1->execute();
        
        $numrows = $stmt1->rowCount();
        
        $output=array($fav,$parties);
        
        if($fav==1){
        
        $sql = "INSERT INTO contact_list (NameCompany,Name,Company,Address,City,State,Pincode,phone,fav) VALUES ('$arr[0]','$arr[1]','$arr[2]','$arr[3]','$arr[4]','$arr[5]','$arr[6]','$arr[7]','$arr[8]')";
        
        // Add new contact-list
        
        $conn->exec($sql);
       
        echo json_encode($output);
            
        }
    
        elseif($fav==0){
            $row = $stmt1->fetch();
            $userid=$row['userid'];
            
            // soft delete contact from contact-list
            $sql = "UPDATE `contact_list` SET `fav`='$fav' WHERE `contact_list`.`userid` = $userid";
            
            $conn->exec($sql);
            
             echo json_encode($output);
        }
        
        elseif($numrows>0 and $fav==2){
            $row = $stmt1->fetch();
            $userid=$row['userid'];
            
            // Update the contact-list
            
            $sql = "UPDATE `contact_list` SET `NameCompany` = '$arr[0]', `Name` = '$arr[1]', `Company` = '$arr[2]', `Address` = '$arr[3]', `City` = '$arr[4]', `State` = '$arr[5]', `Pincode` = '$arr[6]', `phone` = '$arr[7]',`fav`=2 WHERE `contact_list`.`userid` = $userid";
            
            $conn->exec($sql);
            
            echo json_encode($output);
        }

    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    ?>
