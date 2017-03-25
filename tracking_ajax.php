<?php
    
    $action=$_GET['action'];

    $userid="Santy";
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    
    if($action=="trackingstatus"){
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $result=array();
            
            $stmt1 = $conn->prepare("SELECT CourierVendor,count(*) as AWB_Count FROM `Tracking` group by CourierVendor");
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            $stmt1->setFetchMode(PDO::FETCH_ASSOC);
            
            
            if($numrows!=0){
                $i=0;
                foreach($stmt1->fetchAll() as $row1){
                    
                    $x['Type']="CourierBreakup";
                    $x['CourierVendor']=$row1['CourierVendor'];
                    $x['AWB_Count']=$row1['AWB_Count'];
                    $response[$i] = $x;
                    $i++;
                }
                
                $stmt2 = $conn->prepare("SELECT TrackingStatusMapping.TrackingStatus,count(*) as AWB_Count FROM `Tracking` left join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code group by TrackingStatusMapping.TrackingStatus");
                $stmt2->execute();
                
                $stmt2->setFetchMode(PDO::FETCH_ASSOC);
                
                foreach($stmt2->fetchAll() as $row2){
                    
                    $y['Type']="Status";
                    $y['TrackingStatus']=$row2['TrackingStatus'];
                    $y['AWB_Count']=$row2['AWB_Count'];
                    $response[$i] = $y;
                    $i++;
                    
                }
                
                $stmt3 = $conn->prepare("SELECT `CourierVendor`, sum(if(DATEDIFF(NOW(),`EstimateDeliveryDate`)<0,1,0)) as `On_Time`, sum(if(DATEDIFF(NOW(),`EstimateDeliveryDate`)=0,1,0)) as `Same_Day_Delivery`, sum(if(DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 1 AND 2,1,0)) as `1_2_Day_Delay`, sum(if(DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 3 AND 5,1,0)) as `3_5_Day_Delay`, sum(if(DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 6 AND 10,1,0)) as `6_10_Day_Delay`, sum(if(DATEDIFF(NOW(),`EstimateDeliveryDate`)>10,1,0)) as `More_than_10_Day_Delay` FROM `Tracking` GROUP by `CourierVendor`");
                $stmt3->execute();
                
                $stmt3->setFetchMode(PDO::FETCH_ASSOC);
                
                foreach($stmt3->fetchAll() as $row3){
                    
                    $y['Type']="EstimateTime";
                    $y['CourierVendor']=$row3['CourierVendor'];
                    $y['On_Time']=$row3['On_Time'];
                    $y['Same_Day_Delivery']=$row3['Same_Day_Delivery'];
                     $y['Two_Day_Delay']=$row3['1_2_Day_Delay'];
                     $y['Five_Day_Delay']=$row3['3_5_Day_Delay'];
                     $y['Ten_Day_Delay']=$row3['6_10_Day_Delay'];
                     $y['More_than_10_Day_Delay']=$row3['More_than_10_Day_Delay'];
                    
                    $response[$i] = $y;
                    $i++;
                    
                }
                
                echo json_encode($response);
                
            }
            else{
                echo "Plz. schdeule pickup to start tracking";
            }
            
        }
        
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
    }
    
    
    
    else if($action=="DisplayVendorList"){
        
        $couriervendor=$_GET['couriervendor'];
        $row_count=$_GET['row_count'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1 = $conn->prepare("SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where Tracking.CourierVendor='$couriervendor' limit $row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Status</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                echo '<tr><td colspan="9" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Status</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    ////////////  PickedUpDate ////////////////////////////////
                    
                    $PickedUpDate=$row["PickedUpDate"];
                    
                    if($PickedUpDate=="" || $PickedUpDate=="0000-00-00 00:00:00"){
                        
                        $PickedUpDate="";
                    }
                    else{
                        
                        $PickedUpDate=date("d-M-Y", strtotime($row["PickedUpDate"]));
                    }
                    
                    ////////////  DeliveredDate////////////////////////////////
                    
                    $DeliveredDate=$row["DeliveredDate"];
                    
                    if($DeliveredDate=="" || $DeliveredDate=="0000-00-00 00:00:00" ){
                        
                        $DeliveredDate="";
                        
                    }
                    else{
                        
                        $DeliveredDate=date("d-M-Y", strtotime($row["DeliveredDate"]));
                    }
                    
                    
                    
                    ////////////  EstimateDeliveryDate////////////////////////////////
                    
                    
                    $EstimateDeliveryDate=$row["EstimateDeliveryDate"];
                    
                    
                    if($EstimateDeliveryDate=="" || $EstimateDeliveryDate =="0000-00-00 00:00:00" ){
                        
                        $EstimateDeliveryDate="";
                    }
                    else{
                        
                        $EstimateDeliveryDate=date("d-M-Y",strtotime($row["EstimateDeliveryDate"]));

                    }
                    
                    $trackinglink='http://localhost/Courier/TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"];
                    
                    $StatusChangeTimestamp=date("d-M-Y h:i:s A",strtotime($row["StatusChangeTimestamp"]));
                    
                    if($row["CourierVendor"]=="FedEx"){
                        
                        $contact="1800-419-4343";
                    }
                    else if($row["CourierVendor"]=="BlueDart"){
                        
                       $contact="1860-233-1234";
                    }
                    
                    echo '<tr><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"]."<br>City:".$row["Shipper_City"].'</td><td>'.$row["Receiver_Name"]."<br>City:".$row["Receiver_City"].'</td><td>'.$PickedUpDate.'</td><td>'.$EstimateDeliveryDate.'</td><td>'.$DeliveredDate.'</td><td>'.$row["TrackingStatus"].'</td><td>'.$row["CurrentLocation"].'</td><td>  <span class="glyphicon glyphicon-comment" onclick="SendSMS(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$StatusChangeTimestamp.'\',\''.$trackinglink.'\')"></span> <span class="glyphicon glyphicon-envelope" onclick="SendMail(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$row["CurrentLocation"].'\',\''.$StatusChangeTimestamp.'\',\''.$contact.'\',\''.$trackinglink.'\')"  ></span><a href="TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"].'" target="_blank" ><span class="glyphicon glyphicon-menu-right"></span></a> </td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;

        
       
    }
    
    elseif($action=="pagecount"){
        
        $couriervendor=$_GET['couriervendor'];
        $row_count = $_GET['row_count'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt1= $conn->prepare("SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where Tracking.CourierVendor='$couriervendor'");
        
        $stmt1->execute();
        
        $countnumrows = $stmt1->rowCount();
        
        $pagecount=ceil($countnumrows/$row_count);
            
            $result['pagecount']=$pagecount;
            $result['countnumrows']=$countnumrows;
        
          echo json_encode($result);
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;

        
    }
    
    elseif($action=="changePagination"){
        
        $page=$_GET['page'];
        $row_count = $_GET['row_count'];
        $couriervendor=$_GET['couriervendor'];
        
        $upperlimit=$page*$row_count;
        $lowerlimit=$upperlimit-$row_count;
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1 = $conn->prepare("SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where Tracking.CourierVendor='$couriervendor' limit $lowerlimit, $row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Status</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                echo '<tr><td colspan="9" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Status</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    ////////////  PickedUpDate ////////////////////////////////
                    
                    $PickedUpDate=$row["PickedUpDate"];
                    
                    if($PickedUpDate=="" || $PickedUpDate=="0000-00-00 00:00:00"){
                        
                        $PickedUpDate="";
                    }
                    else{
                        
                        $PickedUpDate=date("d-M-Y", strtotime($row["PickedUpDate"]));
                    }
                    
                    ////////////  DeliveredDate////////////////////////////////
                    
                    $DeliveredDate=$row["DeliveredDate"];
                    
                    if($DeliveredDate=="" || $DeliveredDate=="0000-00-00 00:00:00" ){
                        
                        $DeliveredDate="";
                        
                    }
                    else{
                        
                        $DeliveredDate=date("d-M-Y", strtotime($row["DeliveredDate"]));
                    }
                    
                    
                    
                    ////////////  EstimateDeliveryDate////////////////////////////////
                    
                    
                    $EstimateDeliveryDate=$row["EstimateDeliveryDate"];
                    
                    
                    if($EstimateDeliveryDate=="" || $EstimateDeliveryDate =="0000-00-00 00:00:00" ){
                        
                        $EstimateDeliveryDate="";
                    }
                    else{
                        
                        $EstimateDeliveryDate=date("d-M-Y",strtotime($row["EstimateDeliveryDate"]));
                        
                    }
                    
                    
                    $trackinglink='http://localhost/Courier/TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"];
                    
                    $StatusChangeTimestamp=date("d-M-Y h:i:s A",strtotime($row["StatusChangeTimestamp"]));
                    
                    if($row["CourierVendor"]=="FedEx"){
                        
                        $contact="1800-419-4343";
                    }
                    else if($row["CourierVendor"]=="BlueDart"){
                        
                        $contact="1860-233-1234";
                    }

                    
                    echo '<tr><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"]."<br>City:".$row["Shipper_City"].'</td><td>'.$row["Receiver_Name"]."<br>City:".$row["Receiver_City"].'</td><td>'.$PickedUpDate.'</td><td>'.$EstimateDeliveryDate.'</td><td>'.$DeliveredDate.'</td><td>'.$row["TrackingStatus"].'</td><td>'.$row["CurrentLocation"].'</td><td>  <span class="glyphicon glyphicon-comment" onclick="SendSMS(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$StatusChangeTimestamp.'\',\''.$trackinglink.'\')"></span> <span class="glyphicon glyphicon-envelope" onclick="SendMail(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$row["CurrentLocation"].'\',\''.$StatusChangeTimestamp.'\',\''.$contact.'\',\''.$trackinglink.'\')"  ></span><a href="TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"].'" target="_blank" ><span class="glyphicon glyphicon-menu-right"></span></a></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }


            

        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }
    
    
    else if($action=="Search"){
        
        $couriervendor=$_GET['couriervendor'];
        $ShipperCity=$_GET['ShipperCity'];
        $AWB_Number=$_GET['AWB_Number'];
        $ReceiverCity=$_GET['ReceiverCity'];
        $TrackingStatus=$_GET['TrackingStatus'];
        
        
        $str="SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where Tracking.CourierVendor='$couriervendor' ";
        
        if($ShipperCity!=""){
            
            $str=$str."AND AirwayBill_Parties.`Shipper_City` LIKE '%$ShipperCity%'";
        }
        if($AWB_Number!=""){
            
            $str=$str."AND Tracking.`AWB_Number`='$AWB_Number'";
        }
        if($ReceiverCity!=""){
            
            $str=$str."AND AirwayBill_Parties.`Receiver_City` LIKE '%$ReceiverCity%'";        }
        if($TrackingStatus!=""){
            
            $str=$str."AND TrackingStatusMapping.`TrackingStatus` ='$TrackingStatus'";
        }
        
        
        $str=$str." ORDER BY `Tracking`.`AWB_Number` ASC";
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            
            $stmt1 = $conn->prepare($str);
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $response['numrows']=$numrows;
            
            ///// Creating Table Value/////////////
            
            $table_head='<table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list"> <thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Status</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
            
            $table_body='';

            $table_body_end='</tbody></table><br />';
            
            if($numrows==0){
                
                $table_body='<tr><td colspan="9" align="center">No Record Found</td></tr>';
                
                $response['value']=$table_head.$table_body.$table_body_end;
                
                echo json_encode($response);
                
            }
            else if($numrows>0){
               
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    ////////////  PickedUpDate ////////////////////////////////
                    
                    $PickedUpDate=$row["PickedUpDate"];
                    
                    if($PickedUpDate=="" || $PickedUpDate=="0000-00-00 00:00:00"){
                        
                        $PickedUpDate="";
                    }
                    else{
                        
                        $PickedUpDate=date("d-M-Y", strtotime($row["PickedUpDate"]));
                    }
                    
                    ////////////  DeliveredDate////////////////////////////////
                    
                    $DeliveredDate=$row["DeliveredDate"];
                    
                    if($DeliveredDate=="" || $DeliveredDate=="0000-00-00 00:00:00" ){
                        
                        $DeliveredDate="";
                        
                    }
                    else{
                        
                        $DeliveredDate=date("d-M-Y", strtotime($row["DeliveredDate"]));
                    }
                    
                    
                    
                    ////////////  EstimateDeliveryDate////////////////////////////////
                    
                    
                    $EstimateDeliveryDate=$row["EstimateDeliveryDate"];
                    
                    
                    if($EstimateDeliveryDate=="" || $EstimateDeliveryDate =="0000-00-00 00:00:00" ){
                        
                        $EstimateDeliveryDate="";
                    }
                    else{
                        
                        $EstimateDeliveryDate=date("d-M-Y",strtotime($row["EstimateDeliveryDate"]));
                        
                    }
                    
                    $trackinglink='http://localhost/Courier/TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"];
                    
                    $StatusChangeTimestamp=date("d-M-Y h:i:s A",strtotime($row["StatusChangeTimestamp"]));
                    
                    if($row["CourierVendor"]=="FedEx"){
                        
                        $contact="1800-419-4343";
                    }
                    else if($row["CourierVendor"]=="BlueDart"){
                        
                        $contact="1860-233-1234";
                    }

                    
                    
                    
                    $table_body=$table_body.'<tr><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"]."<br>City:".$row["Shipper_City"].'</td><td>'.$row["Receiver_Name"]."<br>City:".$row["Receiver_City"].'</td><td>'.$PickedUpDate.'</td><td>'.$EstimateDeliveryDate.'</td><td>'.$DeliveredDate.'</td><td>'.$row["TrackingStatus"].'</td><td>'.$row["CurrentLocation"].'</td><td>  <span class="glyphicon glyphicon-comment" onclick="SendSMS(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$StatusChangeTimestamp.'\',\''.$trackinglink.'\')"></span> <span class="glyphicon glyphicon-envelope" onclick="SendMail(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$row["CurrentLocation"].'\',\''.$StatusChangeTimestamp.'\',\''.$contact.'\',\''.$trackinglink.'\')"  ></span><a href="TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"].'" target="_blank" ><span class="glyphicon glyphicon-menu-right"></span></a> </td></tr>';
                    
                    $i++;
                }
                
                $response['value']=$table_head.$table_body.$table_body_end;
                
                echo json_encode($response);
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }
    
    
    
    else if($action=="DisplayEstimateList"){
        
        $couriervendor=$_GET['couriervendor'];
        $row_count=$_GET['row_count'];
        $id=$_GET['id'];
        
        if($id==1){
        
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`)<0";
            
        }
        else if($id==2){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`)=0";
        }
        else if($id==3){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 1 AND 2";
        }
        else if($id==4){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 3 AND 5";
        }
        else if($id==5){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 5 AND 10";
        }
        else if($id==6){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`)>10";
        }


        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1 = $conn->prepare("SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where Tracking.CourierVendor='$couriervendor' AND $str limit $row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Status</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                echo '<tr><td colspan="9" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Status</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    ////////////  PickedUpDate ////////////////////////////////
                    
                    $PickedUpDate=$row["PickedUpDate"];
                    
                    if($PickedUpDate=="" || $PickedUpDate=="0000-00-00 00:00:00"){
                        
                        $PickedUpDate="";
                    }
                    else{
                        
                        $PickedUpDate=date("d-M-Y", strtotime($row["PickedUpDate"]));
                    }
                    
                    ////////////  DeliveredDate////////////////////////////////
                    
                    $DeliveredDate=$row["DeliveredDate"];
                    
                    if($DeliveredDate=="" || $DeliveredDate=="0000-00-00 00:00:00" ){
                        
                        $DeliveredDate="";
                        
                    }
                    else{
                        
                        $DeliveredDate=date("d-M-Y", strtotime($row["DeliveredDate"]));
                    }
                    
                    
                    
                    ////////////  EstimateDeliveryDate////////////////////////////////
                    
                    
                    $EstimateDeliveryDate=$row["EstimateDeliveryDate"];
                    
                    
                    if($EstimateDeliveryDate=="" || $EstimateDeliveryDate =="0000-00-00 00:00:00" ){
                        
                        $EstimateDeliveryDate="";
                    }
                    else{
                        
                        $EstimateDeliveryDate=date("d-M-Y",strtotime($row["EstimateDeliveryDate"]));
                        
                    }
                    
                    $trackinglink='http://localhost/Courier/TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"];
                    
                    $StatusChangeTimestamp=date("d-M-Y h:i:s A",strtotime($row["StatusChangeTimestamp"]));
                    
                    if($row["CourierVendor"]=="FedEx"){
                        
                        $contact="1800-419-4343";
                    }
                    else if($row["CourierVendor"]=="BlueDart"){
                        
                        $contact="1860-233-1234";
                    }

                    
                    
                    echo '<tr><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"]."<br>City:".$row["Shipper_City"].'</td><td>'.$row["Receiver_Name"]."<br>City:".$row["Receiver_City"].'</td><td>'.$PickedUpDate.'</td><td>'.$EstimateDeliveryDate.'</td><td>'.$DeliveredDate.'</td><td>'.$row["TrackingStatus"].'</td><td>'.$row["CurrentLocation"].'</td><td> <span class="glyphicon glyphicon-comment" onclick="SendSMS(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$StatusChangeTimestamp.'\',\''.$trackinglink.'\')"></span> <span class="glyphicon glyphicon-envelope" onclick="SendMail(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$row["CurrentLocation"].'\',\''.$StatusChangeTimestamp.'\',\''.$contact.'\',\''.$trackinglink.'\')"  ></span><a href="TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"].'" target="_blank" ><span class="glyphicon glyphicon-menu-right"></span></a> </td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
    }
    
    elseif($action=="Estimatepagecount"){
        
        $couriervendor=$_GET['couriervendor'];
        $row_count = $_GET['row_count'];
        $id=$_GET['id'];
        
        if($id==1){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`)<0";
            
        }
        else if($id==2){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`)=0";
        }
        else if($id==3){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 1 AND 2";
        }
        else if($id==4){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 3 AND 5";
        }
        else if($id==5){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 5 AND 10";
        }
        else if($id==6){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`)>10";
        }

        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $stmt1= $conn->prepare("SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where Tracking.CourierVendor='$couriervendor' AND $str ");
            
            $stmt1->execute();
            
            $countnumrows = $stmt1->rowCount();
            
            $pagecount=ceil($countnumrows/$row_count);
            
            $result['pagecount']=$pagecount;
            $result['countnumrows']=$countnumrows;
            
            echo json_encode($result);
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }
    
    elseif($action=="EstimatechangePagination"){
        
        $page=$_GET['page'];
        $row_count = $_GET['row_count'];
        $couriervendor=$_GET['couriervendor'];
        $id=$_GET['id'];
        
        if($id==1){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`)<0";
            
        }
        else if($id==2){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`)=0";
        }
        else if($id==3){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 1 AND 2";
        }
        else if($id==4){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 3 AND 5";
        }
        else if($id==5){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 5 AND 10";
        }
        else if($id==6){
            
            $str="DATEDIFF(NOW(),`EstimateDeliveryDate`)>10";
        }

        
        
        $upperlimit=$page*$row_count;
        $lowerlimit=$upperlimit-$row_count;
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1 = $conn->prepare("SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where Tracking.CourierVendor='$couriervendor' AND $str limit $lowerlimit, $row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Status</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                echo '<tr><td colspan="9" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Status</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    ////////////  PickedUpDate ////////////////////////////////
                    
                    $PickedUpDate=$row["PickedUpDate"];
                    
                    if($PickedUpDate=="" || $PickedUpDate=="0000-00-00 00:00:00"){
                        
                        $PickedUpDate="";
                    }
                    else{
                        
                        $PickedUpDate=date("d-M-Y", strtotime($row["PickedUpDate"]));
                    }
                    
                    ////////////  DeliveredDate////////////////////////////////
                    
                    $DeliveredDate=$row["DeliveredDate"];
                    
                    if($DeliveredDate=="" || $DeliveredDate=="0000-00-00 00:00:00" ){
                        
                        $DeliveredDate="";
                        
                    }
                    else{
                        
                        $DeliveredDate=date("d-M-Y", strtotime($row["DeliveredDate"]));
                    }
                    
                    
                    
                    ////////////  EstimateDeliveryDate////////////////////////////////
                    
                    
                    $EstimateDeliveryDate=$row["EstimateDeliveryDate"];
                    
                    
                    if($EstimateDeliveryDate=="" || $EstimateDeliveryDate =="0000-00-00 00:00:00" ){
                        
                        $EstimateDeliveryDate="";
                    }
                    else{
                        
                        $EstimateDeliveryDate=date("d-M-Y",strtotime($row["EstimateDeliveryDate"]));
                        
                    }
                    
                    
                    $trackinglink='http://localhost/Courier/TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"];
                    
                    $StatusChangeTimestamp=date("d-M-Y h:i:s A",strtotime($row["StatusChangeTimestamp"]));
                    
                    if($row["CourierVendor"]=="FedEx"){
                        
                        $contact="1800-419-4343";
                    }
                    else if($row["CourierVendor"]=="BlueDart"){
                        
                        $contact="1860-233-1234";
                    }

                    echo '<tr><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"]."<br>City:".$row["Shipper_City"].'</td><td>'.$row["Receiver_Name"]."<br>City:".$row["Receiver_City"].'</td><td>'.$PickedUpDate.'</td><td>'.$EstimateDeliveryDate.'</td><td>'.$DeliveredDate.'</td><td>'.$row["TrackingStatus"].'</td><td>'.$row["CurrentLocation"].'</td><td> <span class="glyphicon glyphicon-comment" onclick="SendSMS(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$StatusChangeTimestamp.'\',\''.$trackinglink.'\')"></span> <span class="glyphicon glyphicon-envelope" onclick="SendMail(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$row["CurrentLocation"].'\',\''.$StatusChangeTimestamp.'\',\''.$contact.'\',\''.$trackinglink.'\')"  ></span><a href="TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"].'" target="_blank" ><span class="glyphicon glyphicon-menu-right"></span></a> </td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
            
            
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }
    
    
    else if($action=="EstimateSearch"){
        
        $couriervendor=$_GET['couriervendor'];
        $ShipperCity=$_GET['ShipperCity'];
        $AWB_Number=$_GET['AWB_Number'];
        $ReceiverCity=$_GET['ReceiverCity'];
        $TrackingStatus=$_GET['TrackingStatus'];
        $id=$_GET['id'];
        
        
        if($id==1){
            
            $str1="DATEDIFF(NOW(),`EstimateDeliveryDate`)<0";
            
        }
        else if($id==2){
            
            $str1="DATEDIFF(NOW(),`EstimateDeliveryDate`)=0";
        }
        else if($id==3){
            
            $str1="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 1 AND 2";
        }
        else if($id==4){
            
            $str1="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 3 AND 5";
        }
        else if($id==5){
            
            $str1="DATEDIFF(NOW(),`EstimateDeliveryDate`) BETWEEN 5 AND 10";
        }
        else if($id==6){
            
            $str1="DATEDIFF(NOW(),`EstimateDeliveryDate`)>10";
        }

        
        
        $str="SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where Tracking.CourierVendor='$couriervendor' ";
        
        if($ShipperCity!=""){
            
            $str=$str."AND AirwayBill_Parties.`Shipper_City` LIKE '%$ShipperCity%'";
        }
        if($AWB_Number!=""){
            
            $str=$str."AND Tracking.`AWB_Number`='$AWB_Number'";
        }
        if($ReceiverCity!=""){
            
            $str=$str."AND AirwayBill_Parties.`Receiver_City` LIKE '%$ReceiverCity%'";        }
        if($TrackingStatus!=""){
            
            $str=$str."AND TrackingStatusMapping.`TrackingStatus` ='$TrackingStatus'";
        }
        
        $str=$str."AND ".$str1;
        
        $str=$str." ORDER BY `Tracking`.`AWB_Number` ASC";
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            
            $stmt1 = $conn->prepare($str);
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $response['numrows']=$numrows;
            
            ///// Creating Table Value/////////////
            
            $table_head='<table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list"> <thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Status</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
            
            $table_body='';
            
            $table_body_end='</tbody></table><br />';
            
            if($numrows==0){
                
                $table_body='<tr><td colspan="9" align="center">No Record Found</td></tr>';
                
                $response['value']=$table_head.$table_body.$table_body_end;
                
                echo json_encode($response);
                
            }
            else if($numrows>0){
                
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    ////////////  PickedUpDate ////////////////////////////////
                    
                    $PickedUpDate=$row["PickedUpDate"];
                    
                    if($PickedUpDate=="" || $PickedUpDate=="0000-00-00 00:00:00"){
                        
                        $PickedUpDate="";
                    }
                    else{
                        
                        $PickedUpDate=date("d-M-Y", strtotime($row["PickedUpDate"]));
                    }
                    
                    ////////////  DeliveredDate////////////////////////////////
                    
                    $DeliveredDate=$row["DeliveredDate"];
                    
                    if($DeliveredDate=="" || $DeliveredDate=="0000-00-00 00:00:00" ){
                        
                        $DeliveredDate="";
                        
                    }
                    else{
                        
                        $DeliveredDate=date("d-M-Y", strtotime($row["DeliveredDate"]));
                    }
                    
                    
                    
                    ////////////  EstimateDeliveryDate////////////////////////////////
                    
                    
                    $EstimateDeliveryDate=$row["EstimateDeliveryDate"];
                    
                    
                    if($EstimateDeliveryDate=="" || $EstimateDeliveryDate =="0000-00-00 00:00:00" ){
                        
                        $EstimateDeliveryDate="";
                    }
                    else{
                        
                        $EstimateDeliveryDate=date("d-M-Y",strtotime($row["EstimateDeliveryDate"]));
                        
                    }
                    
                    $trackinglink='http://localhost/Courier/TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"];
                    
                    $StatusChangeTimestamp=date("d-M-Y h:i:s A",strtotime($row["StatusChangeTimestamp"]));
                    
                    if($row["CourierVendor"]=="FedEx"){
                        
                        $contact="1800-419-4343";
                    }
                    else if($row["CourierVendor"]=="BlueDart"){
                        
                        $contact="1860-233-1234";
                    }

                    
                    $table_body=$table_body.'<tr><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"]."<br>City:".$row["Shipper_City"].'</td><td>'.$row["Receiver_Name"]."<br>City:".$row["Receiver_City"].'</td><td>'.$PickedUpDate.'</td><td>'.$EstimateDeliveryDate.'</td><td>'.$DeliveredDate.'</td><td>'.$row["TrackingStatus"].'</td><td>'.$row["CurrentLocation"].'</td><td> <span class="glyphicon glyphicon-comment" onclick="SendSMS(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$StatusChangeTimestamp.'\',\''.$trackinglink.'\')"></span> <span class="glyphicon glyphicon-envelope" onclick="SendMail(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$row["CurrentLocation"].'\',\''.$StatusChangeTimestamp.'\',\''.$contact.'\',\''.$trackinglink.'\')"  ></span><a href="TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"].'" target="_blank" ><span class="glyphicon glyphicon-menu-right"></span></a> </td></tr>';
                    
                    $i++;
                }
                
                $response['value']=$table_head.$table_body.$table_body_end;
                
                echo json_encode($response);
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }

    
    else if($action=="DisplayStatusList"){
        
        $status=$_GET['status'];
        $row_count=$_GET['row_count'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1 = $conn->prepare("SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where TrackingStatusMapping.TrackingStatus='$status' limit $row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Courier Vendor</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                echo '<tr><td colspan="9" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Courier Vendor</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    ////////////  PickedUpDate ////////////////////////////////
                    
                    $PickedUpDate=$row["PickedUpDate"];
                    
                    if($PickedUpDate=="" || $PickedUpDate=="0000-00-00 00:00:00"){
                        
                        $PickedUpDate="";
                    }
                    else{
                        
                        $PickedUpDate=date("d-M-Y", strtotime($row["PickedUpDate"]));
                    }
                    
                    ////////////  DeliveredDate////////////////////////////////
                    
                    $DeliveredDate=$row["DeliveredDate"];
                    
                    if($DeliveredDate=="" || $DeliveredDate=="0000-00-00 00:00:00" ){
                        
                        $DeliveredDate="";
                        
                    }
                    else{
                        
                        $DeliveredDate=date("d-M-Y", strtotime($row["DeliveredDate"]));
                    }
                    
                    
                    
                    ////////////  EstimateDeliveryDate////////////////////////////////
                    
                    
                    $EstimateDeliveryDate=$row["EstimateDeliveryDate"];
                    
                    
                    if($EstimateDeliveryDate=="" || $EstimateDeliveryDate =="0000-00-00 00:00:00" ){
                        
                        $EstimateDeliveryDate="";
                    }
                    else{
                        
                        $EstimateDeliveryDate=date("d-M-Y",strtotime($row["EstimateDeliveryDate"]));
                        
                    }
                    
                    $trackinglink='http://localhost/Courier/TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"];
                    
                    $StatusChangeTimestamp=date("d-M-Y h:i:s A",strtotime($row["StatusChangeTimestamp"]));
                    
                    if($row["CourierVendor"]=="FedEx"){
                        
                        $contact="1800-419-4343";
                    }
                    else if($row["CourierVendor"]=="BlueDart"){
                        
                        $contact="1860-233-1234";
                    }

                    
                    
                    echo '<tr><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"]."<br>City:".$row["Shipper_City"].'</td><td>'.$row["Receiver_Name"]."<br>City:".$row["Receiver_City"].'</td><td>'.$PickedUpDate.'</td><td>'.$EstimateDeliveryDate.'</td><td>'.$DeliveredDate.'</td><td>'.$row['CourierVendor'].'</td><td>'.$row["CurrentLocation"].'</td><td>  <span class="glyphicon glyphicon-comment" onclick="SendSMS(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$StatusChangeTimestamp.'\',\''.$trackinglink.'\')"></span> <span class="glyphicon glyphicon-envelope" onclick="SendMail(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$row["CurrentLocation"].'\',\''.$StatusChangeTimestamp.'\',\''.$contact.'\',\''.$trackinglink.'\')"  ></span><a href="TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"].'" target="_blank" ><span class="glyphicon glyphicon-menu-right"></span></a> </td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
        
    }
    
    elseif($action=="Statuspagecount"){
        
        $status=$_GET['status'];
        $row_count = $_GET['row_count'];
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            $stmt1= $conn->prepare("SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where TrackingStatusMapping.TrackingStatus='$status' ");
            
            $stmt1->execute();
            
            $countnumrows = $stmt1->rowCount();
            
            $pagecount=ceil($countnumrows/$row_count);
            
            $result['pagecount']=$pagecount;
            $result['countnumrows']=$countnumrows;
            
            echo json_encode($result);
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }
    
    elseif($action=="StatuschangePagination"){
        
        $page=$_GET['page'];
        $row_count = $_GET['row_count'];
        $status=$_GET['status'];
        
        $upperlimit=$page*$row_count;
        $lowerlimit=$upperlimit-$row_count;
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1 = $conn->prepare("SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where TrackingStatusMapping.TrackingStatus='$status' limit $lowerlimit, $row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Courier Vendor</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                echo '<tr><td colspan="9" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th>Courier Vendor</th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    ////////////  PickedUpDate ////////////////////////////////
                    
                    $PickedUpDate=$row["PickedUpDate"];
                    
                    if($PickedUpDate=="" || $PickedUpDate=="0000-00-00 00:00:00"){
                        
                        $PickedUpDate="";
                    }
                    else{
                        
                        $PickedUpDate=date("d-M-Y", strtotime($row["PickedUpDate"]));
                    }
                    
                    ////////////  DeliveredDate////////////////////////////////
                    
                    $DeliveredDate=$row["DeliveredDate"];
                    
                    if($DeliveredDate=="" || $DeliveredDate=="0000-00-00 00:00:00" ){
                        
                        $DeliveredDate="";
                        
                    }
                    else{
                        
                        $DeliveredDate=date("d-M-Y", strtotime($row["DeliveredDate"]));
                    }
                    
                    
                    
                    ////////////  EstimateDeliveryDate////////////////////////////////
                    
                    
                    $EstimateDeliveryDate=$row["EstimateDeliveryDate"];
                    
                    
                    if($EstimateDeliveryDate=="" || $EstimateDeliveryDate =="0000-00-00 00:00:00" ){
                        
                        $EstimateDeliveryDate="";
                    }
                    else{
                        
                        $EstimateDeliveryDate=date("d-M-Y",strtotime($row["EstimateDeliveryDate"]));
                        
                    }
                    
                    
                    $trackinglink='http://localhost/Courier/TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"];
                    
                    $StatusChangeTimestamp=date("d-M-Y h:i:s A",strtotime($row["StatusChangeTimestamp"]));
                    
                    if($row["CourierVendor"]=="FedEx"){
                        
                        $contact="1800-419-4343";
                    }
                    else if($row["CourierVendor"]=="BlueDart"){
                        
                        $contact="1860-233-1234";
                    }

                    
                    echo '<tr><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"]."<br>City:".$row["Shipper_City"].'</td><td>'.$row["Receiver_Name"]."<br>City:".$row["Receiver_City"].'</td><td>'.$PickedUpDate.'</td><td>'.$EstimateDeliveryDate.'</td><td>'.$DeliveredDate.'</td><td>'.$row["TrackingStatus"].'</td><td>'.$row["CurrentLocation"].'</td><td> <span class="glyphicon glyphicon-comment" onclick="SendSMS(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$StatusChangeTimestamp.'\',\''.$trackinglink.'\')"></span> <span class="glyphicon glyphicon-envelope" onclick="SendMail(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$row["CurrentLocation"].'\',\''.$StatusChangeTimestamp.'\',\''.$contact.'\',\''.$trackinglink.'\')"  ></span><a href="TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"].'" target="_blank" ><span class="glyphicon glyphicon-menu-right"></span></a> </td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
            
            
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }
    
    
    else if($action=="StatusSearch"){
        
        $status=$_GET['status'];
        $ShipperCity=$_GET['ShipperCity'];
        $AWB_Number=$_GET['AWB_Number'];
        $ReceiverCity=$_GET['ReceiverCity'];
        $CourierVendor=$_GET['CourierVendor'];
        
        
        $str="SELECT * FROM `Tracking` INNER join AirwayBill_Parties on AirwayBill_Parties.AWB_UID=Tracking.UID INNER Join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code where TrackingStatusMapping.TrackingStatus='$status' ";
        
        if($ShipperCity!=""){
            
            $str=$str."AND AirwayBill_Parties.`Shipper_City` LIKE '%$ShipperCity%'";
        }
        if($AWB_Number!=""){
            
            $str=$str."AND Tracking.`AWB_Number`='$AWB_Number'";
        }
        if($ReceiverCity!=""){
            
            $str=$str."AND AirwayBill_Parties.`Receiver_City` LIKE '%$ReceiverCity%'";        }
        if($CourierVendor!=""){
            
            $str=$str."AND Tracking.`CourierVendor` ='$CourierVendor'";
        }
        
        
        $str=$str." ORDER BY `Tracking`.`AWB_Number` ASC";
        
        
        try{
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            
            
            $stmt1 = $conn->prepare($str);
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $response['numrows']=$numrows;
            
            ///// Creating Table Value/////////////
            
            $table_head='<table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list"> <thead style="background-color: gray; color:white; "><tr><th>Airway Bill Number</th><th>Shipper Name & City</th><th>Receiver Name & City</th><th>Pickup Date</th><th>Expected Delivery Date</th><th>Delivered Date</th><th> CourierVendor </th><th>Current Package Location</th><th>History</th></tr></thead><tbody>';
            
            $table_body='';
            
            $table_body_end='</tbody></table><br />';
            
            if($numrows==0){
                
                $table_body='<tr><td colspan="9" align="center">No Record Found</td></tr>';
                
                $response['value']=$table_head.$table_body.$table_body_end;
                
                echo json_encode($response);
                
            }
            else if($numrows>0){
                
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    ////////////  PickedUpDate ////////////////////////////////
                    
                    $PickedUpDate=$row["PickedUpDate"];
                    
                    if($PickedUpDate=="" || $PickedUpDate=="0000-00-00 00:00:00"){
                        
                        $PickedUpDate="";
                    }
                    else{
                        
                        $PickedUpDate=date("d-M-Y", strtotime($row["PickedUpDate"]));
                    }
                    
                    ////////////  DeliveredDate////////////////////////////////
                    
                    $DeliveredDate=$row["DeliveredDate"];
                    
                    if($DeliveredDate=="" || $DeliveredDate=="0000-00-00 00:00:00" ){
                        
                        $DeliveredDate="";
                        
                    }
                    else{
                        
                        $DeliveredDate=date("d-M-Y", strtotime($row["DeliveredDate"]));
                    }
                    
                    
                    
                    ////////////  EstimateDeliveryDate////////////////////////////////
                    
                    
                    $EstimateDeliveryDate=$row["EstimateDeliveryDate"];
                    
                    
                    if($EstimateDeliveryDate=="" || $EstimateDeliveryDate =="0000-00-00 00:00:00" ){
                        
                        $EstimateDeliveryDate="";
                    }
                    else{
                        
                        $EstimateDeliveryDate=date("d-M-Y",strtotime($row["EstimateDeliveryDate"]));
                        
                    }
                    
                    $trackinglink='http://localhost/Courier/TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"];
                    
                    $StatusChangeTimestamp=date("d-M-Y h:i:s A",strtotime($row["StatusChangeTimestamp"]));
                    
                    if($row["CourierVendor"]=="FedEx"){
                        
                        $contact="1800-419-4343";
                    }
                    else if($row["CourierVendor"]=="BlueDart"){
                        
                        $contact="1860-233-1234";
                    }

                    
                    
                    $table_body=$table_body.'<tr><td>'.$row["AWB_Number"].'</td><td>'.$row["Shipper_Name"]."<br>City:".$row["Shipper_City"].'</td><td>'.$row["Receiver_Name"]."<br>City:".$row["Receiver_City"].'</td><td>'.$PickedUpDate.'</td><td>'.$EstimateDeliveryDate.'</td><td>'.$DeliveredDate.'</td><td>'.$row["CourierVendor"].'</td><td>'.$row["CurrentLocation"].'</td><td> <span class="glyphicon glyphicon-comment" onclick="SendSMS(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$StatusChangeTimestamp.'\',\''.$trackinglink.'\')"></span> <span class="glyphicon glyphicon-envelope" onclick="SendMail(\''.$row["AWB_Number"].'\',\''.$row["CourierVendor"].'\',\''.$row["TrackingStatus"].'\',\''.$row["CurrentLocation"].'\',\''.$StatusChangeTimestamp.'\',\''.$contact.'\',\''.$trackinglink.'\')"  ></span><a href="TrackingDetails.php?UID='.$row["UID"].'&couriervendor='.$row["CourierVendor"].'&AWB_Number='.$row["AWB_Number"].'" target="_blank" ><span class="glyphicon glyphicon-menu-right"></span></a> </td></tr>';
                    
                    $i++;
                }
                
                $response['value']=$table_head.$table_body.$table_body_end;
                
                echo json_encode($response);
                
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        $conn=null;
        
        
    }
    
?>
