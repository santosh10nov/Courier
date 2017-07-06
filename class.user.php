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
                $stmt = $this->conn->prepare("INSERT INTO users(userName,userEmail,userPass,tokenCode)VALUES(:user_name, :user_mail, :user_pass, :active_code)");
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
                            $_SESSION['userCompID']= $userRow['CompID'];
                            $_SESSION['userRoleID']=$userRow['roleID'];
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
            if(isset($_SESSION['userSession'],$_SESSION['userCompID']))
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
            $_SESSION['userCompID']=false;
        }
        
        ////// Navigation Function///////////
        
        
        public function Navigation(){
            
            // Get current page file name
            $currentpage = basename($_SERVER["PHP_SELF"]);
            
            $parent_menu = array();
            $sub_menu = array();
            
            $stmt = $this->conn->prepare("SELECT * FROM `menu`");
            $stmt->execute();
            
            $numrows = $stmt->rowCount();
            
            $i=1;
            
            while($row = $stmt->fetch()){
                
                
                if($row["ParentID"]==0){
                    
                    
                    $parent_menu[$i]['label'] = $row["Label"];
                    $parent_menu[$i]['link'] = $row["Link"];
                    $parent_menu[$i]['count'] = 0;
                    $parent_menu[$i]['id']=$row["ID"];
                }
                else{
                    
                    $sub_menu[$i]['parent'] = $row["ParentID"];
                    $sub_menu[$i]['label'] = $row["Label"];
                    $sub_menu[$i]['link'] = $row["Link"];
                    $parent_menu[$row["ParentID"]]['count']++;
                }
                
                $i++;
            }
            
            
            $nav='<nav class="navbar navbar-default">
            <div class="container-fluid">
            <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">rShipper</a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">';
            
            
            
            foreach($parent_menu as $pkey =>$pval){
                
                $classval='';
                
                if($pval['count']==0){
                    
                    if($pval['link']==$currentpage){
                        
                        $classval='active';
                        
                    }
                    
                    $nav .= '<li class="'.$classval.'"><a href="'.$pval['link'].'">'.$pval['label'].'</a></li>';
                }
                else{
                    
                    $subnav='';
                    
                    foreach($sub_menu as $skey =>$sval){
                        
                        if($pval['id']==$sval['parent']){
                            
                            if($sval['link']==$currentpage){
                                
                                $classval='active';
                                
                            }
                            
                            $subnav.='<li><a href="'.$sval['link'].'">'.$sval['label'].'</a></li>';;
                        }
                    }
                    
                    $nav.='<li class="dropdown "'.$classval.'">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="'.$pval["link"].'">'.$pval["label"].'</a>
                    <ul class="dropdown-menu">'.$subnav.'</ul></li>';
                }
            }
            
            $nav.='</ul>
            <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-user"></span></a>
            <ul class="dropdown-menu">
            <li><a href="logout.php">Logout</a></li>
            </ul>
            </li>
            </ul>
            </div>
            </div>
            </nav>';
            
            return $nav;
            
            
        }
        
        
        ////// Send Mail Function///////////
        
        function send_mail($email,$message,$subject)
        {
            require_once('PHPMailer/PHPMailerAutoload.php');
            
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
