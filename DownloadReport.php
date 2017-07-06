<?php
    
    $userid=$_GET["id"];
    $CompID=$_GET["Comp"];
    $reporttype=$_GET["type"];
    $fromdate=$_GET["fromdate"];
    $todate=$_GET["todate"];
    
    if(isset($userid,$CompID,$reporttype,$fromdate,$todate)){
    
    /*******EDIT LINES 3-8*******/
    $DB_Server = "localhost"; //MySQL Server
    $DB_Username = "root"; //MySQL Username
    $DB_Password = "yesbank";             //MySQL Password
    $DB_DBName = "transporter";         //MySQL Database Name
        
        
        if($reporttype=="AirwayBill"){
            
            $filename = "AirwayBill Report";
            
             $sql = "SELECT `ShipperName` as `Shipper Name`, `ReceiverName` as `Receiver Name`, `COD`, `PackageCount` as `PackageCount`, `ReferenceID`, `AWB_Date` as `AirwayBill Date`, `CourierVendor` as `Courier`,`Airwaybill_Number`, if(`AWB_Status`='Success','Generated',`AWB_Status`) as `Status` FROM `AirwayBill`  WHERE `AWB_Status`in ('Success','Dispatched','Cancelled') and `CreatedByUserID` ='$userid' and `CreatedByCompID`='$CompID' and `API_Hit_Date` >='$fromdate' AND `API_Hit_Date` <='$todate' ";
            
            
        }
        elseif($reporttype=="Pickup"){
            
            $filename = "Pickup Report";
            
            $sql = " SELECT `AWB_Number` as `AirwayBill Number`, `Courier_Vendor` as `Courier`, `Pickup_Status` as `Pickup Status`, `Pickup_Number` as `Pickup Number`, `Scheduled_Pickup_Date` FROM `pickup` INNER JOIN AirwayBill on AirwayBill.UID=pickup.UID  WHERE `CreatedByUserID` ='$userid' and `CreatedByCompID`='$CompID' and `API_Hit_Date` >='$fromdate' AND `API_Hit_Date` <='$todate' ";
        }
        elseif($reporttype=="Dispatch"){
            
            $filename = "Dispatch Package Report";
            
            $sql = "SELECT AirwayBill.`ShipperName` as `Shipper Name`, AirwayBill.`ReceiverName` as `Receiver Name`,  AirwayBill.`COD`,  AirwayBill.`PackageCount` as `PackageCount`,  AirwayBill.`ReferenceID`,  AirwayBill.`AWB_Date` as `AirwayBill Date`,  AirwayBill.`CourierVendor` as `Courier`, AirwayBill.`Airwaybill_Number`, DispatchDetails.`DispatchDate` FROM `DispatchDetails` INNER JOIN AirwayBill on AirwayBill.UID=DispatchDetails.UID  WHERE `CreatedByUserID` ='$userid' and `CreatedByCompID`='$CompID' and `API_Hit_Date` >='$fromdate' AND `API_Hit_Date` <='$todate' ";
        }

        elseif($reporttype=="Tracking"){
            
            $filename = "Tracking Report";
            
            $sql = "SELECT `AWB_Number`, Tracking.`CourierVendor`, `TrackingStatus`, `CurrentLocation`, `StatusChangeTimestamp` FROM `Tracking` INNER JOIN TrackingStatusMapping on TrackingStatusMapping.Code=Tracking.Code   INNER JOIN AirwayBill on AirwayBill.UID=Tracking.UID  WHERE `CreatedByUserID` ='$userid' and `CreatedByCompID`='$CompID' and `API_Hit_Date` >='$fromdate' AND `API_Hit_Date` <='$todate' ";
        }

        elseif($reporttype=="Receive"){
            
            $filename = "Receive Courier Report";
            
            $sql = " SELECT  `ShipperName` as `Shipper Name`,  `AWB_Date` as `Package Received Date`, `CourierVendor` as `Courier`, `Airwaybill_Number` FROM `receive_details` WHERE `CreatedByUserID`='$userid' and `CreatedDate`>='$fromdate' and `CreatedDate`<= '$todate'";
            
        }

        
        
       
        
        
    /*******YOU DO NOT NEED TO EDIT ANYTHING BELOW THIS LINE*******/
    //create MySQL connection
    
    $Connect = @mysql_connect($DB_Server, $DB_Username, $DB_Password) or die("Couldn't connect to MySQL:<br>" . mysql_error() . "<br>" . mysql_errno());
    //select database
    $Db = @mysql_select_db($DB_DBName, $Connect) or die("Couldn't select database:<br>" . mysql_error(). "<br>" . mysql_errno());
    //execute query
    $result = @mysql_query($sql,$Connect) or die("Couldn't execute query:<br>" . mysql_error(). "<br>" . mysql_errno());
    $file_ending = "xls";
    //header info for browser
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=$filename.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    /*******Start of Formatting for Excel*******/
    //define separator (defines columns in excel & tabs in word)
    $sep = "\t"; //tabbed character
    //start of printing column names as names of MySQL fields
    for ($i = 0; $i < mysql_num_fields($result); $i++) {
        echo mysql_field_name($result,$i) . "\t";
    }
    print("\n");
    //end of printing column names
    //start while loop to get data
    while($row = mysql_fetch_row($result))
    {
        $schema_insert = "";
        for($j=0; $j<mysql_num_fields($result);$j++)
        {
            if(!isset($row[$j]))
                $schema_insert .= "NULL".$sep;
            elseif ($row[$j] != "")
            $schema_insert .= "$row[$j]".$sep;
            else
                $schema_insert .= "".$sep;
        }
        $schema_insert = str_replace($sep."$", "", $schema_insert);
        $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
        $schema_insert .= "\t";
        print($schema_insert);
        print "\n";
    }
        
    }
    else{
        
        echo "Something Went Wrong";
    }
    ?>
