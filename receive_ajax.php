<?php
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    
    $action=$_GET['action'];
    $userid=$_GET['id'];
    $output="";
    
    require_once 'dbconfig.php';
    
    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        
        if($action=="select"){
            
            $row_count = $_GET['row_count'];
            
            $stmt1 = $conn->prepare("SELECT * FROM `receive_details` left join receive_parties on receive_parties.RUID=receive_details.RUID where `AWB_Status`='Received' and `CreatedByUserID`='$userid' GROUP BY receive_details.RUID ORDER BY receive_details.RUID DESC limit $row_count");
            
            
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Courier Vendor</th><th>Airway Bill Number</th><th>Shipment Date</th><th>Sender Name</th><th>Courier Details</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
                
            }
            else if($numrows>0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Courier Vendor</th><th>Airway Bill Number</th><th>Shipment Date</th><th>Sender Name</th><th>Courier Details</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                $i=1;
                while($row = $stmt1->fetch()){
                    
                    $ShippmentDate=date("d-M-Y",strtotime($row["AWB_Date"]));
                    $RUID=$row[0];
                    
                    echo '<tr><td>'.$i.'</td><td>'.$row["CourierVendor"].'</td><td>'.$row["Airwaybill_Number"].'</td><td>'.$ShippmentDate.'</td><td>'.$row["ShipperName"].'</td><td>'.$row["purpose"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\')">View</button>&nbsp&nbsp<button type="button" class="btn btn-sm btn-danger" onclick="CancelAWB(\''.$RUID.'\',\''.$row["AWB_Date"].'\',\''.$row["CourierVendor"].'\',\''.$row["Airwaybill_Number"].'\')">Delete</button></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
 
        }
        
       
        
        elseif($action=="pagecount"){
            
            $row_count = $_GET['row_count'];
            
            $stmt1= $conn->prepare("SELECT * FROM `receive_details` left join receive_parties on receive_parties.RUID=receive_details.RUID where `AWB_Status`='Received' and `CreatedByUserID`='$userid' GROUP BY receive_details.RUID ORDER BY receive_details.RUID DESC");
            
            $stmt1->execute();
            
            $countnumrows = $stmt1->rowCount();
            
            $pagecount=ceil($countnumrows/$row_count);
            
            $result['pagecount']=$pagecount;
            $result['countnumrows']=$countnumrows;
            
            echo json_encode($result);

        }
        
        
        
        elseif($action=="changePagination"){
            
            $page=$_GET['page'];
            $row_count = $_GET['row_count'];
            
            $upperlimit=$page*$row_count;
            $lowerlimit=$upperlimit-$row_count;
            
            
            $stmt1 = $conn->prepare("SELECT * FROM `receive_details` left join receive_parties on receive_parties.RUID=receive_details.RUID where `AWB_Status`='Received' and `CreatedByUserID`='$userid' GROUP BY receive_details.RUID ORDER BY receive_details.RUID DESC limit $lowerlimit,$row_count");
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            
            if($numrows==0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Courier Vendor</th><th>Airway Bill Number</th><th>Shipment Date</th><th>Sender Name</th><th>Courier Details</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                echo '<tr><td colspan="7" align="center">No Record Found</td></tr>';
                echo '</tbody></table><br />';
            }
            else if($numrows>0){
                echo ' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list">';
                echo '<thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Courier Vendor</th><th>Airway Bill Number</th><th>Shipment Date</th><th>Sender Name</th><th>Courier Details</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
                $i=$lowerlimit+1;
                while($row = $stmt1->fetch()){
                    
                    $ShippmentDate=date("d-M-Y",strtotime($row["AWB_Date"]));
                    $RUID=$row[0];
                    
                    echo '<tr><td>'.$i.'</td><td>'.$row["CourierVendor"].'</td><td>'.$row["Airwaybill_Number"].'</td><td>'.$ShippmentDate.'</td><td>'.$row["ShipperName"].'</td><td>'.$row["purpose"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\')">View</button>&nbsp&nbsp<button type="button" class="btn btn-sm btn-danger" onclick="CancelAWB(\''.$RUID.'\',\''.$row["AWB_Date"].'\',\''.$row["CourierVendor"].'\',\''.$row["Airwaybill_Number"].'\')">Delete</button></td></tr>';
                    
                    $i++;
                }
                echo '</tbody></table><br />';
                
            }
            
        }
        
        
        elseif($action=="search"){
            
            $receiver_name=$_GET['receiver_name'];
            $shipment_date=$_GET['shipment_date'];
            $vendor=$_GET['vendor'];
            $airwaybill_number=$_GET['airwaybill_number'];
            
            $stmt1 = $conn->prepare(" SELECT * FROM `receive_details` left join receive_parties on receive_parties.RUID=receive_details.RUID where `AWB_Status`='Received' AND `CreatedByUserID`='$userid' AND `ShipperName` LIKE '%$receiver_name%' AND `AWB_Date` LIKE '%$shipment_date%' AND `CourierVendor` LIKE '%$vendor%' AND `Airwaybill_Number` LIKE '%$airwaybill_number%' GROUP BY receive_details.RUID ORDER BY receive_details.RUID DESC");
            
            
            $stmt1->execute();
            
            $numrows = $stmt1->rowCount();
            
            $response['numrows']=$numrows;
            
            ///// Creating Table Value/////////////
            
            $table_head='<table class=" col-xs-12 col-sm-12 col-lg-12 table table-striped table-bordered table-list"> <thead style="background-color: gray; color:white; "><thead style="background-color: gray; color:white; "><tr><th>ID</th><th>Courier Vendor</th><th>Airway Bill Number</th><th>Shipment Date</th><th>Sender Name</th><th>Courier Details</th><th><em class="fa fa-cog" align="center"></em></th></tr></thead><tbody>';
            $table_body='';
            
            $table_body_end='</tbody></table><br />';
            
            if($numrows==0){
              
                 $table_body='<tr><td colspan="7" align="center">No Record Found</td></tr>';
                
                $response['value']=$table_head.$table_body.$table_body_end;
                
                echo json_encode($response);
            }
            else if($numrows>0){
                $i=1;
                while($row = $stmt1->fetch()){
                    
                     $ShippmentDate=date("d-M-Y",strtotime($row["AWB_Date"]));
                     $RUID=$row[0];
                    
                    $table_body=$table_body.'<tr><td>'.$i.'</td><td>'.$row["CourierVendor"].'</td><td>'.$row["Airwaybill_Number"].'</td><td>'.$ShippmentDate.'</td><td>'.$row["ShipperName"].'</td><td>'.$row["purpose"].'</td><td><button type="button" class="btn btn-sm btn-primary"  onclick="AWB_Details(\''.$row["CourierVendor"].'\',\''.$row["CourierService"].'\',\''.$row["Airwaybill_Number"].'\',\''.$row["AWB_Date"].'\',\''.$row["COD"].'\',\''.$row["PackageCount"].'\',\''.$row["Shipper_Name"].'\',\''.$row["Shipper_Comp"].'\',\''.$row["Shipper_Address"].'\',\''.$row["Shipper_City"].'\',\''.$row["Shipper_State"].'\',\''.$row["Shipper_Pincode"].'\',\''.$row["Shipper_Phone"].'\',\''.$row["Shipper_VendorID"].'\')">View</button>&nbsp&nbsp<button type="button" class="btn btn-sm btn-danger" onclick="CancelAWB(\''.$RUID.'\',\''.$row["AWB_Date"].'\',\''.$row["CourierVendor"].'\',\''.$row["Airwaybill_Number"].'\')">Delete</button></td></tr>';

                    
                    $i++;
                }
                $response['value']=$table_head.$table_body.$table_body_end;
                
                echo json_encode($response);
            }
            
        }
        
        
        
    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    ?>
