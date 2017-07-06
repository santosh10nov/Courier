<?php

     include 'config.php';
	 
	 // Check whether username or password is set from android	
     if(isset($_POST['id']))
     {
		  // Innitialize Variable
		  $result='';
		  //$result="true";
	   	  $id = $_POST['id'];
		  //echo $id;
		  
		  // Query database for row exist or not
          $sql = 'SELECT * FROM AirwayBill WHERE UID = :id';
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(':id', $id, PDO::PARAM_STR);
          $stmt->execute();
		  $result = $stmt -> fetch();

		  // send result back to android
		  echo $result[CourierVendor] ."split" .$result[Airwaybill_Number] ."split" .$result[ReceiverName] ."split" .$result[AWB_Date] ."split" .$result[ReferenceID] ."split" .$result[AWB_Status];
  	}
	
?>