<?php
    
    session_start();
    require_once 'class.user.php';
    $user = new USER();
    
    if($user->is_logged_in()!=true)
    {
        $user->redirect('login.php');
    }
    
    
    $roleID=$_SESSION['userRoleID'];
   
    
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
<style>
table, th, td {
    
padding: 10px;
}
table {
    border-spacing: 15px;
}
.names { font-weight: bold; }
</style>


</head>
<body>


<?php echo  $user->Navigation(); ?>

<div class="container">
<table style="width:100%">
<?php
    
    // Admin Only//////////
    
    if($roleID==1){
        
        echo '<tr>
        <td align="center"><a href="CourierVendorDetails.php"><img src="img/addcouriervendor.png" alt="report" width="65" height="65" border="0"></a></td>
        <td align="left"><a href="CreateUser.php"><img src="img/adduser.png" alt="track" width="65" height="65" border="0"></a></td>
        </tr>
        <tr>
        <td align="center" ><span class="names">Add Courier Vendor</span></td>
        <td align="left"><span class="names">Create New User</span></td>
        </tr>';
        
    }
    
    ?>



<tr>
<td align="center"><a href="report.php"><img src="img/report.png" alt="report" width="65" height="65" border="0"></a></td>
<td align="left"><a href="ContactList.php"><img src="img/contact_list_profile.png" alt="track" width="65" height="65" border="0"></a></td>
</tr>
<tr>
<td align="center"><span class="names">Reports</span></td>
<td align="left"><span class="names">Contact List</span></td>
</tr>






</table>
</div>

</body>
</html>
