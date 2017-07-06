<?php

     include 'config.php';
	 
	 // Check whether id is set from android	
     if(isset($_POST['id']))
     {
		  // Innitialize Variable
		  $result="test";
		  //$result="true";
	   	  $id = $_POST['id'];
		  //echo $id;
		  
		  $updateterm = "dispatched"; 
		  
		  // Query database for row exist or not
          $sql = 'UPDATE AirwayBill SET AWB_Status = :updateterm WHERE UID = :id';
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(':id', $id, PDO::PARAM_STR);
		  $stmt->bindParam(':updateterm', $updateterm, PDO::PARAM_STR);
          $stmt->execute();
		  
          if($stmt->rowCount())
          {
			 $result = "true";
          }  
          else if(!$stmt->rowCount())
          {
			 $result = "false";
          }
		
		  // send result back to android
   		  echo $result;
  	}
	
?>