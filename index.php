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

<nav class="navbar navbar-default">
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
<ul class="nav navbar-nav">
<li class="active"><a href="index.php">Dashboard</a></li>
<li><a href="ServiceTAT.php">Service Availability/TAT</a></li>
<li class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" href="#">AirwayBill</a>
<ul class="dropdown-menu">
<li><a href="Generate_AirwayBill.php">Generate AirwayBill</a></li>
<li><a href="AirwayBillList.php">AirwayBill List</a></li>
<li><a href="DispatchList.php">Dispatch List</a></li>
</ul>
</li>
<li><a href="schedulepickup.html">Pickup</a></li>
<li><a href="trackcourier.php">Tracking</a></li>
<li><a href="index.php">Extra</a></li>
</ul>
<ul class="nav navbar-nav navbar-right">
<li class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-user"></span></a>
<ul class="dropdown-menu">
<li><a href="#">Profile</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</li>
</ul>
</div>
</div>
</nav>


<div class="container">
<table style="width:100%">
<tr>
<td align="center"><a href="Service_Availabilty.php"><img src="img/Search.png" alt="Service Availability" width="42" height="42" border="0"></a></td>
<td align="center"><a href="TAT.php"><img src="img/TAT.png" alt="TAT" width="42" height="42" border="0"></a></td>
</tr>
<tr>
<td align="center">Service Availability</td>
<td align="center">TAT</td>
</tr>
<tr>
<td align="center"><a href="Generate_AirwayBill.php"><img src="img/airwaybill.png" alt="airwaybill" width="42" height="42" border="0"></td>
<td align="center"><a href="pickup_3.html"><img src="img/pickup.png" alt="pickup" width="42" height="42" border="0"></td>
</tr>
<tr>
<td align="center">Generate AirwayBill</td>
<td align="center">Book Pickup  </td>
</tr>
<tr>
<td align="center"><img src="img/track.png" alt="track" width="42" height="42" border="0"></td>
<td align="center"><img src="img/report.png" alt="report" width="42" height="42" border="0"></td>
</tr>
<tr>
<td align="center">Track Courier</td>
<td align="center">Reports </td>
</tr>

<tr>
<td align="center"><img src="img/contact_list_profile.png" alt="track" width="42" height="42" border="0"></td>
<td align="center"><a href="airwaybill_list.html"><img src="img/AirwayBillList.png" alt="report" width="42" height="42" border="0"></a></td>
</tr>
<tr>
<td align="center">Contact List</td>
<td align="center">AirwayBill List </td>
</tr>

<tr>
<td align="center"><a href="couriervendordetails.html"><img src="img/addcouriervendor.png" alt="report" width="42" height="42" border="0"></a></td>
<td align="center"><img src="img/contact_list_profile.png" alt="track" width="42" height="42" border="0"></td>
</tr>
<tr>
<td align="center">Add Courier Vendor</td>
<td align="center">try </td>
</tr>




</table>
</div>

</body>
</html>
