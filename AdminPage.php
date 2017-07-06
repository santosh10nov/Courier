<?php
    
    session_start();
    require_once 'class.user.php';
    $user = new USER();
    
    if($user->is_logged_in()!=true)
    {
        $user->redirect('login.php');
    }
    
    ?>



<!DOCTYPE html>
<html lang="en">
<head>
<title> Dashboard |rShipper</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

<?php echo  $user->Navigation(); ?>


<div class="container">
<table style="width:100%">
<tr>
<td align="center"><a href="CourierVendorDetails.php"><img src="img/addcouriervendor.png" alt="report" width="42" height="42" border="0"></a></td>
<td align="left"><img src="img/report.png" alt="report" width="42" height="42" border="0"></td>
</tr>
<tr>
<td align="center">Add Courier Vendor</td>
<td align="left">Reports </td>
</tr>

<tr>
<td align="center"><img src="img/contact_list_profile.png" alt="track" width="42" height="42" border="0"></td>

</tr>
<tr>
<td align="center">Contact List</td>
</tr>






</table>
</div>

</body>
</html>
