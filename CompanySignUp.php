<?php
    session_start();
    require_once 'class.user.php';
    
    $reg_user = new USER();
    
    if($reg_user->is_logged_in()!="")
    {
        $reg_user->redirect('index.php');
    }
    
    
    if(isset($_POST['btn-signup']))
    {
        $uname = trim($_POST['txtuname']);
        $email = trim($_POST['txtemail']);
        $upass = trim($_POST['txtpass']);
        $phone = trim($_POST['txtphone']);
        $refcode = trim($_POST['txtrefcode']);
        $code = md5(uniqid(rand()));
        
        $stmt = $reg_user->runQuery("SELECT * FROM users WHERE userEmail=:email_id");
        $stmt->execute(array(":email_id"=>$email));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($stmt->rowCount() > 0)
        {
            $msg = "'Sorry, Email allready exists.Please try forgot password to reset your password on login page'";
            $alerttype="'alert-danger'";
        }
        else
        {
            $max=2;
            
            $stmt = $reg_user->runQuery("SELECT * FROM `MarketingCode` where `Code`='$refcode' and `Status`='Active' ");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($stmt->rowCount()!=1){
                
                $msg = "'Referal Code already expired or invalid code. Please <a href=index.html#contact> Contact Us</a>.'";
                $alerttype="'alert-danger'";
                
            }
            else{
                
                if($reg_user->register($uname,$email,$upass,$code))
                {
                    $id = $reg_user->lasdID();
                    $key = base64_encode($id);
                    $id = $key;
                    
                    
                    ///////////////Code Generation//////////////////
                    
                    $characters = 'QWERTYUIOPASDFGHJKLZXCVBNM123456789';
                    $CodeLen=6;
                    
                    $string = '';
                    $max = strlen($characters) - 1;
                    for ($i=0; $i<$CodeLen; $i++) {
                        $string .= $characters[mt_rand(0, $max)];
                    }

                    
                    
                    $newcode = $string;
                    
                    $stmt1 = $reg_user->runQuery("INSERT INTO `CompanyDetails`( `Name`, `EmailID`, `PhoneNumber`,`RefCode`) VALUES ('$uname','$email','$phone','$newcode')");
                    $stmt1->execute();
                    
                    $CompCode= $reg_user->lasdID();
                    
                    $stmt3 = $reg_user->runQuery("INSERT INTO `UsedMarketingCode`(`Code`, `UsedBy`) VALUES ('$refcode','$CompCode')");
                    $stmt3->execute();
                    
                    $stmt4 = $reg_user->runQuery("SELECT * FROM `UsedMarketingCode` where 'Code'='$refcode'");
                    $stmt4->execute();
                    $row = $stmt4->fetch(PDO::FETCH_ASSOC);
                    
                    if($stmt4->rowCount() >=$max){
                        
                        $stmt5 = $reg_user->runQuery("UPDATE `MarketingCode` SET `Status`='Expired' WHERE `Code`='$refcode'");
                        $stmt5->execute();
                        
                    }
                    
                    $stmt6 = $reg_user->runQuery(" INSERT INTO `MarketingCode`( `Code`, `Type`, `GeneratedBy`, `Status`) VALUES ('$newcode','ReferalCode','$CompCode','Active');");
                    $stmt6->execute();
                    
                    
                    // Add Company User as ADMIN///////////
                    
                    $stmt7= $reg_user->runQuery("UPDATE `users` SET `CompID`='$CompCode', roleID=1 WHERE `userEmail`='$email'; ");
                    $stmt7->execute();
                    
                    
                    
                    $message = "
                    Hi,
                    <br /><br />
                    Congrats on being a part of rShipper community.Follow the below link to activate your account:<br/>
                    <br /><br />
                    <a href='http://rshipper.com/verify.php?id=$id&code=$code'>Click HERE to Activate :)</a>
                    <br /><br />
                    In case of link is not clickable, please copy paste below URL into browser:<br/>
                    http://rshipper.com/verify.php?id=$id&code=$code
                    <br /><br />
                    
                    Thanks";
                    
                    $subject = "rShipper- Account Activation";
                    
                    $reg_user->send_mail($email,$message,$subject);
                    
                    $msg = "'Success, We have sent an email to $email. Please click on the confirmation link in the email to create your account.'";
                    $alerttype="'alert-success'";
                    
            
                    $message2="#rShippered<br/><br/>As you've succesfully become a part of our community, we've decided to double your stakes.<br/><br/><b>Get 2 months of free subscription</b> when 2 of your friends join our community using your referral code. Now use our services in style free of cost for a hasssle-free work environment.<br/><br/>Your Referral Code:$newcode";
                    
                    $subject2="rShipper- Referal Code";
                    
                    $reg_user->send_mail($email,$message2,$subject2);
                }
                else
                {
                    
                    $msg = "'Sorry! Some error occured.Please <a href=index.html#contact> Contact Us</a>.Please click on the confirmation link in the email to create your account.'";
                    $alerttype="'alert-danger'";
                }
                
            }
            
        
           
        }
    }
    ?>




<!DOCTYPE html>
<html>
    <head>
        <title>Company Signup | rShipper</title>
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
            <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
                <link href="assets/styles.css" rel="stylesheet" media="screen">
                    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
                    <!--[if lt IE 9]>
                     <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
                     <![endif]-->
                    <script src="js/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    <body id="login" style="background:#778899;">
        <div class="container">
            <h1 class="form-signin-heading" align="center">rShipper</h1><hr />
            <div id = "alert_placeholder"></div>
            <form class="form-signin" method="post" id="SignupForm"  onsubmit="return Validate();" >
                <h3 class="form-signin-heading"> Company Sign Up</h3><hr />
                <input type="text" class="input-block-level" placeholder="Company Name" name="txtuname" id="username"  required/>
                <input type="text" class="input-block-level" placeholder="Contact Number" name="txtphone" id="phone"  required/>
                <input type="email" class="input-block-level" placeholder="Email address" name="txtemail" id="emailid" required/>
                <input type="password" class="input-block-level" placeholder="Password" name="txtpass" id="pass" required/ >
                    <input type="text" class="input-block-level" placeholder="Referal Code" name="txtrefcode" id="refcode"  required/>
                    *Need Referal Code,<a href="index.html#contact"> Contact Us!</a>
                    <hr />
                    <button class="btn btn-large btn-success"  type="submit" name="btn-signup" >Sign Up</button>
                    <a href="login.php" style="float:right;" class="btn btn-large">Login</a>
            </form>
            
        </div> <!-- /container -->
        <script src="jquery/jquery-1.9.1.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script>
            
            function Validate()
            {
                var username=document.getElementById("username").value;
                var emailid=document.getElementById("emailid").value;
                var pass=document.getElementById("pass").value;
                var phone=document.getElementById("phone").value;
                var refcode=document.getElementById("refcode").value;
                
                
                if(username==""){
                    alertmsg="You forgot to entered Company Name!";
                    alerttype="alert-danger";
                    showalert(alertmsg,alerttype);
                    return(false);
                }
                else if(phone==""){
                    alertmsg="You forgot to entered contact number!";
                    alerttype="alert-danger";
                    showalert(alertmsg,alerttype);
                    return(false);
                    
                }
                else if(emailid==""){
                    alertmsg="You forgot to entered email address!";
                    alerttype="alert-danger";
                    showalert(alertmsg,alerttype);
                    return(false);
                    
                }
                else if(pass==""){
                    alertmsg="You forgot to entered password!";
                    alerttype="alert-danger";
                    showalert(alertmsg,alerttype);
                    return(false);
                    
                }
                else if(refcode==""){
                    alertmsg="You forgot to entered referal code!";
                    alerttype="alert-danger";
                    showalert(alertmsg,alerttype);
                    return(false);
                    
                }
                else if(isNaN(phone)){
                    alertmsg="You have entered an invalid phone number!";
                    alerttype="alert-danger";
                    showalert(alertmsg,alerttype);
                    return(false);
                    
                }
                
                if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(emailid))
                {
                    return (true);
                }
                else{
                    alertmsg="You have entered an invalid email address!";
                    alerttype="alert-danger";
                    showalert(alertmsg,alerttype);
                    return(false);
                }
                
                
                
                
            }
        
        function showalert(message,alerttype) {
            
            $('#alert_placeholder').append('<div id="alertdiv" class="alert ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
            
            setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
                       
                       
                       $("#alertdiv").remove();
                       
                       }, 3000);
        }
        
        
        <?php if(isset($msg)) echo "showalert($msg,$alerttype)"  ?>
        
            </script>
    </body>
</html>
