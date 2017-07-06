<?php
    
    require_once('PHPMailerAutoload.php');
    
    $servername = "localhost";
    $dbname = "transporter";
    $username = "root";
    $pass = "yesbank";
    
  
    
// Check for empty fields
if(empty($_POST['name'])  		||
   empty($_POST['email']) 		||
   empty($_POST['phone']) 		||
   empty($_POST['message'])	||
   !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
   {
	echo "No arguments Provided!";
	return false;
   }
    
    
    try{
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
        $name = strip_tags(htmlspecialchars($_POST['name']));
        $email_address = strip_tags(htmlspecialchars($_POST['email']));
        $phone = strip_tags(htmlspecialchars($_POST['phone']));
        $message = strip_tags(htmlspecialchars($_POST['message']));
    

        
        $stmt=$conn->prepare("INSERT INTO `ContactMeForm`( `Name`, `EmailID`, `ContactNumber`, `Message`) VALUES ('$name','$email_address','$phone','$message')");
        $stmt->execute();
    
    
        $email_body="Hi,<br/><br/>Thank you for getting in touch!<br/><br/>We have received your message.We will look over your message and get back to you in 2 business days.<br/><br/>Talk to you soon,<br/>The rShipper Team";
    
        $subject="rShipper-Contact Acknowledgement";
        
        
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
        $mail->Username = "rshipper.noreplymails@gmail.com";
        
        //Password to use for SMTP authentication
        $mail->Password = "santosh10nov";
        
        //Set who the message is to be sent from
        $mail->setFrom('santosh10nov@gmail.com', 'rShipper');
        
        //Set an alternative reply-to address
        $mail->addReplyTo('', '');
        
        
        
        //Set who the message is to be sent to
        $mail->addAddress($email_address, '');
        
        //Set the subject line
        $mail->Subject =$subject;

        
        $mail->msgHTML($email_body);
        
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
        
        
        //send the message, check for errors
        if (!$mail->send()) {
            
           
            
            //echo "Mailer Error: " . $mail->ErrorInfo;
            //echo "Email Failed! Try again.";
        } else {
            
            // Send EMail to Admin //////
            $email_to = 'santosh10nov@gmail.com';
            $email_subject = "Website Contact Form:  $name";
            $email_body1= "You have received a new message from your website contact form.</br></br>"."Here are the details:</br></br>Name: $name</br>Email: $email_address</br>Phone: $phone</br>Message:\n$message";
            
            $mail->ClearAddresses();
            $mail->addAddress($email_to, '');
            $mail->Subject=$email_subject;
            $mail->msgHTML($email_body1);
            
            $mail->send();
            
            //echo "Email Sent!";
        }
    
        
        
        $conn=null;
        
        return false;
        
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    
    
?>
