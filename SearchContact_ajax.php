<?php
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    $searchname=$_GET['searchname'];
    $searchcom_name=$_GET['comp_name'];
    $searchphone=$_GET['phone'];
    $searchparties=$_GET['parties'];
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
        
        
        //$stmt1 = $conn->prepare("Select * from contact_list where phone LIKE '$searchname' ");
        $stmt1 = $conn->prepare("SELECT * FROM `contact_list` WHERE fav!=0 and `Name` LIKE '%$searchname%' and Company LIKE '%$searchcom_name%' and phone LIKE '%$searchphone%' Limit 5");

        $stmt1->execute();
        
        $numrows = $stmt1->rowCount();
        
        
        if($numrows==0){
        
        //$sql = "INSERT INTO contact_list (NameCompany,Name,Company,Address,City,State,Pincode,phone,fav) VALUES ('$arr[0]','$arr[1]','$arr[2]','$arr[3]','$arr[4]','$arr[5]','$arr[6]','$arr[7]','$arr[8]')";
        
        // Add new contact-list
        
        //$conn->exec($sql);
       
        echo "No Record Found";
            
        }
    
        elseif($numrows>0){
            
        
            echo '<table class="table table-hover" id="try">';
            echo '<thead><tr><th>Name</th><th>Company Name</th><th>Phone Number</th><th>Address</th></tr></thead><tboby>';
            while($row = $stmt1->fetch()){
                
                
                echo '<tr id="try" data-dismiss="modal" onclick="selectRow(\''.$row["Pincode"].'\',\''.$row["Name"].'\',\''.$row["Company"].'\',\''.$row["Address"].'\',\''.$row["City"].'\',\''.$row["State"].'\',\''.$row["phone"].'\',\''.$searchparties.'\')"><td>'.$row["Name"].'</td><td>'.$row["Company"].'</td><td>'.$row["phone"].'</td><td>'.$row["Address"].",".$row["City"].",".$row["State"]."-".$row["Pincode"].'</td></tr>';
                
                
            }
            echo '</tbody></table><br />';
        }
        
        
        

    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    ?>
