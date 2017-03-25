<?php
    session_start();
    require_once 'class.user.php';
    $user_login = new USER();
    
    if($user_login->is_logged_in()!="")
    {
        $user_login->redirect('index.php');
    }
    
    if(isset($_POST['btn-login']))
    {
        $email = trim($_POST['txtemail']);
        $upass = trim($_POST['txtupass']);
        
        if($user_login->login($email,$upass))
        {
            $user_login->redirect('index.php');
        }
    }
    ?>

<!DOCTYPE html>
<html>
<head>
<title>Login |rShiper</title>
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
<div id = "alert_placeholder"></div>
<?php
    if(isset($_GET['inactive']))
    {
        ?>
<div class='alert alert-error'>
<button class='close' data-dismiss='alert'>&times;</button>
<strong>Sorry!</strong> This Account is not Activated Go to your Inbox and Activate it.
</div>
<?php
    }
    ?>
<form class="form-signin" method="post" onsubmit="return Validate();">
<?php
    if(isset($_GET['error']))
    {
        ?>
<div class='alert alert-success'>
<button class='close' data-dismiss='alert'>&times;</button>
<strong>Wrong Details!</strong>
</div>
<?php
    }
    ?>
<h2 class="form-signin-heading">Login</h2><hr />
<input type="email" class="input-block-level" placeholder="Email address" name="txtemail" id="emailid" required />
<input type="password" class="input-block-level" placeholder="Password" name="txtupass" id="pass" required />
<hr />
<button class="btn btn-large btn-success" type="submit" name="btn-login">Login</button>
<a href="signup.php" style="float:right;" class="btn btn-large">Sign Up</a><hr />
<a href="fpass.php">Lost your Password ? </a>
</form>

</div> <!-- /container -->
<script src="bootstrap/js/jquery-1.9.1.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script>

function Validate()
{
    
    var emailid=document.getElementById("emailid").value;
    var pass=document.getElementById("pass").value;
    
    
    if(emailid==""){
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
