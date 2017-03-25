<?php
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";

    //SMTP needs accurate times, and the PHP time zone MUST be set
    //This should be done in your php.ini, but this is how to do it if you don't have access to that
    date_default_timezone_set('Etc/UTC');
    
    require 'PHPMailer/PHPMailerAutoload.php';
    
    

    
    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt1 = $conn->prepare("SELECT Tracking.AWB_Number as AWB_Number,Tracking.CourierVendor as CourierVendor, Tracking.UID as UID FROM `Tracking` left join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code WHERE TrackingStatusMapping.`TrackingStatus`!='Delivered' and Tracking.Code='PSD'");
        $stmt1->execute();
        $i=0;
        
        
        foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
            
            //print_r($row);
            
          
            $UID=$row["UID"];
            $AWBNo=$row["AWB_Number"];
            $couriervendor=$row["CourierVendor"];
            
            //echo $AWBNo."<br>";
            

            
            if($row["CourierVendor"]=="BlueDart"){
                    include("Tracking/BlueDart/BlueDartTracking.php");
                
            }
            if($row["CourierVendor"]=="FedEx"){
                
                include("Tracking/FedEx/TrackWebServiceClient.php");
            }
        }
            
        
}
    catch(PDOException $e) {
        //echo "Error: " . $e->getMessage();
    }
    
    
    ////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////BlueDart API Class ///////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////
    
    
    class DebugSoapClient extends SoapClient {
        public $sendRequest = true;
        public $printRequest = true;
        public $formatXML = true;
        
        public function __doRequest($request, $location, $action, $version, $one_way=0) {
            if ( $this->printRequest ) {
                if ( !$this->formatXML ) {
                    $out = $request;
                }
                else {
                    $doc = new DOMDocument;
                    $doc->preserveWhiteSpace = false;
                    $doc->loadxml($request);
                    $doc->formatOutput = true;
                    $out = $doc->savexml();
                }
                //echo $out;
            }
            
            if ( $this->sendRequest ) {
                return parent::__doRequest($request, $location, $action, $version, $one_way);
            }
            else {
                return '';
            }
        }
    }
    
    try{
        
        $stmt2=$conn->prepare("SELECT DISTINCT `UpdatedBy` as UserID,users.userEmail as UserEmail, userName FROM `Tracking` INNER Join users on users.userID=Tracking.UpdatedBy");
        $stmt2->execute();
        
         foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
          
             $userid=$row['UserID'];
             $username=$row['userName'];
             $to=$row['UserEmail'];
             
             $subject="Daily Report";
             $header="Hi ".$username.",<br/><br/>Please find below snapshot of last 30 days courier details: <br/><br/>";
             $footer="Regards,<br/> Hyzine.com";
             
             
             $stmt3 = $conn->prepare("SELECT CourierVendor,count(*) as AWB_Count FROM `Tracking` where `Tracking`.UpdatedBy=$userid group by CourierVendor");
             $stmt3->execute();
             
             $numrows = $stmt3->rowCount();
             $stmt3->setFetchMode(PDO::FETCH_ASSOC);
             
             $table1=' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-bordered">';
             $tableHeader1='<thead style="background-color: gray; color:white; "><tr><th>Courier Vendor</th><th>Count</th></tr></thead><tbody>';
             $tableBody1='';
             
             
             if($numrows!=0){
                 $i=0;
                 foreach($stmt3->fetchAll() as $row3){
                     
                     
                     $tableBody1=$tableBody1.'<tr><td>'.$row3['CourierVendor'].'</td><td>'.$row3['AWB_Count'].'</td></tr>';
                     
                     $i++;
                 }
                
                $tableEnd1='</tbody></table><br/><br/>';
                 
                 $table1=$table1.$tableHeader1.$tableBody1.$tableEnd1;
                 
                 $stmt4 = $conn->prepare("SELECT TrackingStatusMapping.TrackingStatus,count(*) as AWB_Count FROM `Tracking`left join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code  where `Tracking`.UpdatedBy=$userid  group by TrackingStatusMapping.TrackingStatus");
                 $stmt4->execute();
                 
                 $stmt4->setFetchMode(PDO::FETCH_ASSOC);
                 
                 $table2=' <table class=" col-xs-12 col-sm-12 col-lg-12 table table-bordered">';
                 $tableHeader2='<thead style="background-color: purple; color:white; "><tr><th>Courier Status</th><th>Count</th></tr></thead><tbody>';
                 $tableBody2='';
                 
                 foreach($stmt4->fetchAll() as $row4){
                     
                    
                     
                    $tableBody2=$tableBody2.'<tr><td>'.$row4['TrackingStatus'].'</td><td>'.$row4['AWB_Count'].'</td></tr>';
                     
                     $i++;
                     
                 }
                 
                  $tableEnd2='</tbody></table><br/><br/>';
                 
                 $table2=$table2.$tableHeader2.$tableBody2.$tableEnd2;
                 
                 $msg=$header."Vendor Details <br/>".$table1."Status Details<br/>".$table2.$footer;
                 
             }
             
             //Create a new PHPMailer instance
             $mail = new PHPMailer;
             
             //Tell PHPMailer to use SMTP
             $mail->isSMTP();
             
             //Enable SMTP debugging
             // 0 = off (for production use)
             // 1 = client messages
             // 2 = client and server messages
             $mail->SMTPDebug = 0;
             
             //Ask for HTML-friendly debug output
             $mail->Debugoutput = 'html';
             
             //Set the hostname of the mail server
             $mail->Host = 'smtp.gmail.com';
             // use
             // $mail->Host = gethostbyname('smtp.gmail.com');
             // if your network does not support SMTP over IPv6
             
             //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
             $mail->Port = 587;
             
             //Set the encryption system to use - ssl (deprecated) or tls
             $mail->SMTPSecure = 'tls';
             
             //Whether to use SMTP authentication
             $mail->SMTPAuth = true;
             
             //Username to use for SMTP authentication - use full email address for gmail
             $mail->Username = "santosh10nov@gmail.com";
             
             //Password to use for SMTP authentication
             $mail->Password = "Yesbank@10";
             
             //Set who the message is to be sent from
             $mail->setFrom('santosh10nov@gmail.com', 'SantoshYadav');
             
             //Set an alternative reply-to address
             $mail->addReplyTo('riders35@gmail.com', 'Santy');
             
             
             
             //Set who the message is to be sent to
             $mail->addAddress($to, '');
             
             //Set the subject line
             $mail->Subject =$subject;
             
             //Read an HTML message body from an external file, convert referenced images to embedded,
             //convert HTML into a basic plain-text alternative body
             //$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
             
             $mail->msgHTML($msg);
             
             //Replace the plain text body with one created manually
             $mail->AltBody = 'This is a plain-text message body';
             
             //Attach an image file
             //$mail->addAttachment('images/phpmailer_mini.png');
             
             //send the message, check for errors
             if (!$mail->send()) {
                 //echo "Mailer Error: " . $mail->ErrorInfo;
                 echo "Email Failed! Try again.";
             } else {
                 echo "Email Sent!";
             }
             
         }
        
        
    }catch(PDOException $e){
        
       echo "Error: " . $e->getMessage();
    }
    
    $conn=null;
    
    
    
    
    ?>
