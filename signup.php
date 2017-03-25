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
        $code = md5(uniqid(rand()));
        
        $stmt = $reg_user->runQuery("SELECT * FROM users WHERE userEmail=:email_id");
        $stmt->execute(array(":email_id"=>$email));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($stmt->rowCount() > 0)
        {
            $msg = "
            <div class='alert alert-danger'>
            <button class='close' data-dismiss='alert'>&times;</button>
            <strong>Sorry !</strong>  Email allready exists , Please try forgot password to reset your password on login page
            </div>
            ";
        }
        else
        {
            if($reg_user->register($uname,$email,$upass,$code))
            {
                $id = $reg_user->lasdID();
                $key = base64_encode($id);
                $id = $key;
                
                $message = "
                Hello $uname,
                <br /><br />
                Welcome to rShipper!<br/>
                To complete your registration  please , just click following link<br/>
                <br /><br />
                <a href='http://rshipper.com/verify.php?id=$id&code=$code'>Click HERE to Activate :)</a>
                <br /><br />
                Thanks,";
                
                $subject = "Confirm Registration";
                
                $reg_user->send_mail($email,$message,$subject);
                $msg = "
                <div class='alert alert-success'>
                <button class='close' data-dismiss='alert'>&times;</button>
                <strong>Success!</strong>  We've sent an email to $email.
                Please click on the confirmation link in the email to create your account.
                </div>
                ";
            }
            else
            {
                echo "sorry , Query could no execute...";
            }
        }
    }
    ?>




<!DOCTYPE html>
<html>
<head>
<title>Signup | rShipper</title>
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
<body id="login">
<div class="container">
<h1 class="form-signin-heading" align="center">rShipper</h1><hr />
<?php if(isset($msg)) echo $msg;  ?>
<div id = "alert_placeholder"></div>
<form class="form-signin" method="post" onsubmit="return Validate();">
<h2 class="form-signin-heading">Sign Up</h2><hr />
<input type="text" class="input-block-level" placeholder="Username" name="txtuname" id="username"  required/>
<input type="email" class="input-block-level" placeholder="Email address" name="txtemail" id="emailid" required/>
<input type="password" class="input-block-level" placeholder="Password" name="txtpass" id="pass" required/ >
<hr />
<button class="btn btn-large btn-success"  type="submit" name="btn-signup">Sign Up</button>
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
    
    
    if(username==""){
        alertmsg="You forgot to entered username!";
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


</script>
</body>
</html>
