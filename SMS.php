<?php

    $to=$_GET['to'];
    $msg=$_GET['msg'];
    $userid=$_GET['userid'];
    
    
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";

    
// Replace with your username
$user = "santy10nov";

// Replace with your API KEY (We have sent API KEY on activation email, also available on panel)
$apikey = "2gRqOjbAlhg3WHpCUC4J";

// Replace if you have your own Sender ID, else donot change
$senderid  =  "MYTEXT"; 

// Replace with the destination mobile Number to which you want to send sms
$mobile  = $to;

// Replace with your Message content
$message   = $msg;
$message = urlencode($message);

// For Plain Text, use "txt" ; for Unicode symbols or regional Languages like hindi/tamil/kannada use "uni"
$type   =  "txt";
    
// Currently Using SMS Horizon  Gateway////////////
    
    $Gateway='SMSHorizon';
    
    

$ch = curl_init("http://smshorizon.co.in/api/sendsms.php?user=".$user."&apikey=".$apikey."&mobile=".$mobile."&senderid=".$senderid."&message=".$message."&type=".$type."");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);      
    curl_close($ch); 

    // Display MSGID of the successful sms push
    //echo $output;
    
    $ch1 = curl_init("http://smshorizon.co.in/api/status.php?user=".$user."&apikey=".$apikey."&msgid=".$output."");
    curl_setopt($ch1, CURLOPT_HEADER, 0);
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
    $status = curl_exec($ch1);
    curl_close($ch1);
   
    // Display Status sms push
    echo $status;
    
    
    //////////////////////SMS_Details///////////
    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt1=$conn->prepare(" INSERT INTO `SMS_Details`( `Mobile`, `Message`, `SenderID`, `Gateway`,`Msg_Status`) VALUES ('$to','$msg','$userid','$Gateway','$status');");
        $stmt1->execute();
        
        
        
    }catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    $conn=null;



?>

