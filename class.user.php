<?php
    
    require_once 'dbconfig.php';
    
    class USER
    {
        
        private $conn;
        
        public function __construct()
        {
            $database = new Database();
            $db = $database->dbConnection();
            $this->conn = $db;
        }
        
        public function runQuery($sql)
        {
            $stmt = $this->conn->prepare($sql);
            return $stmt;
        }
        
        public function lasdID()
        {
            $stmt = $this->conn->lastInsertId();
            return $stmt;
        }
        
        public function register($uname,$email,$upass,$code)
        {
            try
            {
                $password = md5($upass);
                $stmt = $this->conn->prepare("INSERT INTO users(userName,userEmail,userPass,tokenCode)
                                             VALUES(:user_name, :user_mail, :user_pass, :active_code)");
                                             $stmt->bindparam(":user_name",$uname);
                                             $stmt->bindparam(":user_mail",$email);
                                             $stmt->bindparam(":user_pass",$password);
                                             $stmt->bindparam(":active_code",$code);
                                             $stmt->execute();
                                             return $stmt;
                                             }
                                             catch(PDOException $ex)
                                             {
                                             echo $ex->getMessage();
                                             }
                                             }
                                             
                                             public function login($email,$upass)
                                             {
                                             try
                                             {
                                             $stmt = $this->conn->prepare("SELECT * FROM users WHERE userEmail=:email_id");
                                             $stmt->execute(array(":email_id"=>$email));
                                             $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
                                             
                                             if($stmt->rowCount() == 1)
                                             {
                                             if($userRow['userStatus']=="Y")
                                             {
                                             if($userRow['userPass']==md5($upass))
                                             {
                                             $_SESSION['userSession'] = $userRow['userID'];
                                             return true;
                                             }
                                             else
                                             {
                                             header("Location: login.php?error");
                                             exit;
                                             }
                                             }
                                             else
                                             {
                                             header("Location: login.php?inactive");
                                             exit;
                                             }
                                             }
                                             else
                                             {
                                             header("Location: login.php?error");
                                             exit;
                                             }
                                             }
                                             catch(PDOException $ex)
                                             {
                                             echo $ex->getMessage();
                                             }
                                             }
                                             
                                             
                                             public function is_logged_in()
                                             {
                                             if(isset($_SESSION['userSession']))
                                             {
                                             return true;
                                             }
                                             }
                                             
                                             public function redirect($url)
                                             {
                                             header("Location: $url");
                                             }
                                             
                                             public function logout()
                                             {
                                             session_destroy();
                                             $_SESSION['userSession'] = false;
                                             }
                                             
                                             function send_mail($email,$message,$subject)
                                             {
                                             require_once('PHPMailer/PHPMailerAutoload.php');
                                             
                                             /*$mail = new PHPMailer();
                                              $mail->IsSMTP();
                                              $mail->SMTPDebug  = 0;
                                              $mail->SMTPAuth   = true;
                                              $mail->SMTPSecure = "tls";
                                              $mail->Host       = "smtp.gmail.com";
                                              $mail->Port       = 587;
                                              $mail->AddAddress($email);
                                              $mail->Username="santosh10nov@gmail.co";
                                              $mail->Password="Yesbank@10";
                                              $mail->SetFrom('santosh10nov@gmail.com','rShipper');
                                              $mail->AddReplyTo("you@yourdomain.com","Coding Cage");
                                              $mail->Subject    = $subject;
                                              $mail->MsgHTML($message);
                                              $mail->Send();*/
                                             
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
                                             $mail->setFrom('santosh10nov@gmail.com', 'SantoshYadav');
                                             
                                             //Set an alternative reply-to address
                                             $mail->addReplyTo('riders35@gmail.com', 'Santy');
                                             
                                             
                                             
                                             //Set who the message is to be sent to
                                             $mail->addAddress($email, '');
                                             
                                             //Set the subject line
                                             $mail->Subject =$subject;
                                             
                                             //Read an HTML message body from an external file, convert referenced images to embedded,
                                             //convert HTML into a basic plain-text alternative body
                                             //$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
                                             
                                             $mail->msgHTML($message);
                                             
                                             //Replace the plain text body with one created manually
                                             $mail->AltBody = 'This is a plain-text message body';
                                             
                                             //Attach an image file
                                             //$mail->addAttachment('images/phpmailer_mini.png');
                                             
                                             //send the message, check for errors
                                             if (!$mail->send()) {
                                             //echo "Mailer Error: " . $mail->ErrorInfo;
                                             //echo "Email Failed! Try again.";
                                             } else {
                                             //echo "Email Sent!";
                                             }
                                             
                                             }
                                             
                                             }
    ?>
