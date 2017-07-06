<?php
    
    session_start();
    require_once 'class.user.php';
    $user = new USER();
    
    if($user->is_logged_in()!=true)
    {
        $user->redirect('login.php');
    }
    
    $userid=$_SESSION['userSession'];
    
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>rShipper Service</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="js/validation.js"></script>
</head>
<body>

<?php echo  $user->Navigation(); ?>

<div class="container">
<h1 align="center">Service Availability </h1>

<div class="alerts" id="alert_box" style="padding-top: 0em; padding-bottom: 0em;display: none;">
<div class="alert alert-danger" role="alert">
<p id="alert_div" align="center"></p>
<ul id="list">
</ul>
</div>
</div>

<form class="form-horizontal" action="Service_Avi.php" role="form" method="post" onsubmit="return validate_activity(this)">
<div class="form-group">
<label class="control-label col-sm-2" for="email">Pickup Pincode:</label>
<div class="col-sm-10">
<input type="text" class="form-control" name="from_pin" placeholder="Enter From Pincode" required="" maxlength="6" pattern="(.){6,6}">
</div>
</div>
<div class="form-group">
<label class="control-label col-sm-2" for="pwd">Destination Pincode:</label>
<div class="col-sm-10">
<input type="text" class="form-control" name="to_pin" placeholder="Enter To Pincode" required="" maxlength="6" pattern="(.){6,6}">
</div>
</div>
<div class="form-group">
<div class="col-sm-offset-2 col-sm-10">
<button type="submit" class="btn btn-success" onClick="return validate_activity()">Submit</button>
</div>
</div>
</form>

</div>

</body>
</html>
