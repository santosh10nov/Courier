<?php
    
    $userid=$_GET['userid'];
    $couriervendor=$_GET['vendor'];
    $status=$_GET['status'];
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    
    
    
    ?>



<!DOCTYPE html>
<html lang="en">
<head>
<title>rShipper Service- In Progress/Scheduled List</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<style>
ul.pagination {
display: inline-block;
padding: 0;
margin: 0;
    
}

ul.pagination li {display: inline;}

ul.pagination li a {
color: black;
    float: left;
padding: 8px 16px;
    text-decoration: none;
transition: background-color .3s;
border: 1px solid #ddd;
}

.pagination li:first-child a {
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
}

.pagination li:last-child a {
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;
}

ul.pagination li a.active {
    background-color: #4CAF50;
color: white;
border: 1px solid #4CAF50;
}

ul.pagination li a:hover:not(.active) {background-color: #ddd;}
    
    .panel-heading{
        background-color: #c0c0c0;
    position:static;
        z-index:100;
        
    }
    .sidenav {
    height: 100%;
    width: 0;
    position: fixed;
        z-index: 1;
    top: 0;
    left: 0;
        background-color: #111;
        overflow-x: hidden;
    transition: 0.5s;
        padding-top: 60px;
    }
    
    .sidenav a {
    padding: 8px 8px 8px 32px;
        text-decoration: none;
        font-size: 25px;
    color: #818181;
    display: block;
    transition: 0.3s
    }
    
    .sidenav a:hover, .offcanvas a:focus{
    color: #f1f1f1;
    }
    
    .sidenav .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
        font-size: 36px;
        margin-left: 50px;
    }
    
    #container {
transition: margin-left .5s;
padding: 16px;
}


@media screen and (max-height: 450px) {
    .sidenav {padding-top: 15px;}
    .sidenav a {font-size: 18px;}
}
</style>

</head>

<body onload="list()">

<nav class="navbar navbar-default" id="header_nav">
<div class="container-fluid">
<div class="navbar-header">
<a class="navbar-brand" href="index.php">rShipper</a>
</div>
<ul class="nav navbar-nav">
<li><a href="index.php">Home</a></li>
<li><a href="Service_Availabilty.php">Service Availability</a></li>
<li><a href="TAT.php">TAT</a></li>
<li><a href="Generate_AirwayBill.php">Generate AirwayBill</a></li>
<li><a href="schedulepickup.html">Book Pickup</a></li>
<li><a href="#">Track Courier</a></li>
</ul>
</div>
</nav>

<div class="container" id="container">
<span style="font-size:30px;cursor:pointer;float: left;" onclick="openNav()">&#9776;</span>
<h1 align="center">In Progress/Scheduled List</h1>
<div class="alerts" id="alert_box" style="padding-top: 0em; padding-bottom: 0em;display: none;">
<div class="alert alert-danger" role="alert">
<p id="alert_div" align="center"></p>
<ul id="list">
</ul>
</div>
</div>
<div id="mySidenav" class="sidenav">
<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
<a href="Generate_AirwayBill.php" >Generate Way Bill</a>
<a href="airwaybill_list.html">Way Bill List</a>
</div>
<!-- form start -->
<form class="form-horizontal"  method="post" >
<div class="col-md-12 ">
<div class="panel panel-default panel-table">
<div class="panel-heading">
<div class="row">
<div class="col-md-12">
<table class="table table-bordered">
<thead style="background-color: gray; color:white; ">
<tr>
<td>Pickup Date</td>
<td>AWB Number</td>
<td>Pickup Number</td>
<td> Pickup Status</td>
<td><em class="fa fa-cog" align="center"></em></td>
</tr>
</thead>
<tbody>
<tr class="filters">
<th>
<div class="input-group date form_date col-md-12" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
<input class="form-control" size="16" type="text" value="" name="pickupdate"  id="pickupdate" readonly>
<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
</div>
<input type="hidden" id="dtp_input1" value="" />
</th>
<th>
<input class="form-control" size="16" type="text" value="" name="AWB_Number"  id="AWB_Number">
</th>`
<th>
<input class="form-control" size="16" type="text" value="" name="PickupNumber"  id="PickupNumber">
</th>
<th>
<select class="form-control selcls" name="PickupStatus" id="PickupStatus">
<option value="" selected="selected">Select Pickup Status</option>
<option value="Under Process">In Process</option>
<option value="Scheduled">Scheduled</option>
</select>

</th>
<th><input type="button" value="Search" class = "btn btn-success" id="Search" onclick="Searchlist()">&nbsp;<input type="reset" value="Reset" class = "btn btn-info" onclick="list()"></th>
</tr>

</tbody>
</table>
</div>
</div>
</div>
<div class="panel-body" id="result">


</div>
<div class="panel-footer">

</div>
</div>
</div>




<!-- /.form Submit -->


</form>

<div id="details" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="list()">×</button>
<h4 class="modal-title">Pickup Confirmation Details</h4>
</div>
<div class="modal-body">
<p id="response"></p>
</div>
<div class="modal-footer">
</div>
</div>
</div>
</div>







<div id="viewdetails" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h4 class="modal-title">AirwayBill Details</h4>
</div>
<div class="modal-body">
<div class= "row">
<div class="col-sm-12">
<table class="table table-bordered"  id="AirwayBillInfo">
<thead style="background-color: gray; color:white; ">
<tr>
<th>Vendor</th>
<th>Serivice</th>
<th>AirwayBill No.</th>
<th>Shipment Date</th>
<th>COD</th>
<th>Packet Count</th>
</tr>
</thead>
<tbody>
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>

</tr>
</tbody>
</table>

</div>

</div>

<div class="row">
<!-- right column -->
<div class="col-sm-6">
<div class="box box-primary">
<!-- /.box-header -->
<div class="box-body">
<table class="table table-striped table-bordered">
<tr>
<td>Vendor ID:</td>
<th><input type="text" class="form-control" name="sender_vendorid" id="sender_vendorid" disabled></th>
</tr>
<tr>
<td class="col-sm-4">Pickup Pincode:</td>
<th><input type="text" class="form-control" name="from_pin" id="from_pin" disabled></th>
</tr>
<tr>
<td>Sender Name:</td>
<th><input type="text" class="form-control" name="sender_name" id="sender_name" disabled></th>
</tr>
<tr>
<td>Company Name:</td>
<th><input type="text" class="form-control" name="sender_com_name" id="sender_com_name" disabled></th>
</tr>
<tr>
<td>Address:</td>
<th><textarea class="form-control custom-control" rows="3" style="resize:none" name="sender_address" id="sender_address"disabled ></textarea></th>
</tr>

<tr>
<td>City:</td>
<th><input type="text" class="form-control" name="sender_city"  id="sender_city" disabled></th>
</tr>

<tr>
<td>State:</td>
<th><input type="text" class="form-control" name="sender_state"  id="sender_state" disabled></th>
</tr>

<tr>
<td>Phone Number:</td>
<th><input type="text" class="form-control" name="sender_phone" id="sender_phone" disabled></th>
</tr>
</table>

</div>
<!-- /.box-footer -->
</div>
<!-- /.box -->
</div>


<div class="col-sm-6">
<div class="box box-primary" >
<!-- /.box-header -->
<div class="box-body">
<table class="table table-striped table-bordered">
<tr>
<td>Vendor ID:</td>
<th><input type="text" class="form-control" name="receiver_vendorid" id="receiver_vendorid" disabled></th>
</tr>
<tr>
<td class="col-sm-4">Destination Pincode:</td>
<th><input type="text" class="form-control" name="to_pin" id="to_pin" disabled></th>
</tr>
<tr>
<td>Receiver Name</td>
<th><input type="text" class="form-control" name="receiver_name" id="receiver_name1" disabled></th>
</tr>
<tr>
<td>Company Name:</td>
<th><input type="text" class="form-control" name="receiver_com_name" id="receiver_com_name" disabled></th>
</tr>
<tr>
<td>Address:</td>
<th><textarea class="form-control custom-control" rows="3" style="resize:none" name="receiver_address"  id="receiver_address" disabled></textarea></th>
</tr>

<tr>
<td>City:</td>
<th><input type="text" class="form-control" name="receiver_city"  id="receiver_city" disabled></th>
</tr>

<tr>
<td>State:</td>
<th><input type="text" class="form-control" name="receiver_state"  id="receiver_state" disabled></th>
</tr>

<tr>
<td>Phone Number:</td>
<th><input type="text" class="form-control" name="receiver_phone" id="receiver_phone" disabled></th>
</tr>

</table>

</div>
<!-- /.box-footer -->
</div>
<!-- /.box -->
</div>

</div>

</div>
</div>
</div>
</div>

</div>

</div>


<script type="text/javascript" src="jquery/jquery-1.8.3.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>


<script>


function list(){
    
    var couriervendor="<?php echo $couriervendor; ?>";
    
    
    
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            
            document.getElementById("result").innerHTML = xmlhttp.responseText;
        }
    };
    xmlhttp.open("GET","pickup_ajax.php?action=DisplayScheduling&couriervendor="+couriervendor,true);
    xmlhttp.send();
    
    
}




function AWB_Details(CourierVendor,CourierService,Airwaybill_Number,AWB_Date,COD,PackageCount,Shipper_Name,Shipper_Comp,Shipper_Address,Receiver_Name,Receiver_Comp,Receiver_Address,sender_city,sender_state,sender_pincode,sender_phone,sender_vendorid,receiver_city,receiver_state,receiver_pincode,receiver_phone,receiver_vendorid){
    
    
    var myTable=document.getElementById("AirwayBillInfo");
    
    myTable.rows[1].cells[0].innerHTML = CourierVendor;
    
    myTable.rows[1].cells[1].innerHTML = CourierService;
    
    myTable.rows[1].cells[2].innerHTML = Airwaybill_Number;
    
    myTable.rows[1].cells[3].innerHTML = AWB_Date;
    
    myTable.rows[1].cells[4].innerHTML = COD;
    
    myTable.rows[1].cells[5].innerHTML = PackageCount;
    
    document.getElementById("sender_vendorid").value=sender_vendorid;
    document.getElementById("sender_name").value=Shipper_Name;
    document.getElementById("sender_com_name").value=Shipper_Comp;
    document.getElementById("sender_address").value=Shipper_Address;
    document.getElementById("sender_city").value=sender_city;
    document.getElementById("sender_state").value=sender_state;
    document.getElementById("from_pin").value=sender_pincode;
    document.getElementById("sender_phone").value=sender_phone;
    
    document.getElementById("receiver_vendorid").value=receiver_vendorid;
    document.getElementById("receiver_name1").value=Receiver_Name;
    document.getElementById("receiver_com_name").value=Receiver_Comp;
    document.getElementById("receiver_address").value=Receiver_Address;
    document.getElementById("receiver_city").value=receiver_city;
    document.getElementById("receiver_state").value=receiver_state;
    document.getElementById("to_pin").value=receiver_pincode;
    document.getElementById("receiver_phone").value=receiver_phone;
    
    
    $('#viewdetails').modal('show');
}




function Assign_Batch(){
    
    document.getElementById("Schedule").disabled = true;
    
    var userid='<?php echo $userid; ?>';
    
    
    
    var couriervendor= '<?php echo $couriervendor; ?>' ;
    var pickupdate= document.getElementById("pickupdate").value ;
    var pickuptime= document.getElementById("pickuptime").value;
    var comp_closing_time= document.getElementById("comp_closing_time").value;
    
    var couriercount=$(":checkbox:checked").length;
    
    var checkboxes = document.getElementsByName('checked');
    var vals = "";
    for (var i=0, n=checkboxes.length;i<n;i++)
    {
        if (checkboxes[i].checked)
        {
            vals += ","+checkboxes[i].value;
        }
    }
    
    if (vals) vals = vals.substring(1);
    
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            
            //document.getElementById("result").innerHTML = xmlhttp.responseText;
            //pagecount(row_count);
            xmlhttp.responseText;
            
            document.getElementById("response").innerHTML = "Pickup Number:"+xmlhttp.responseText+".";
            
            $('#details').modal('show');
            document.getElementById("Schedule").disabled = false;
        }
    };
    xmlhttp.open("GET","pickup_ajax.php?action=Assign_Batch&UID="+vals+"&userid="+userid+"&pincode="+pickuppincode+"&name="+name+"&com_name="+CompanyName+"&address="+address+"&city="+city+"&state="+state+"&phone="+phone+"&couriervendor="+couriervendor+"&pickupdate="+pickupdate+"&pickuptime="+pickuptime+"&comp_closing_time="+comp_closing_time+"&couriercount="+couriercount,true);
    xmlhttp.send();
    
    
}


function Searchlist(){
    
    var userid='<?php echo $userid; ?>';
    
    var couriervendor= '<?php echo $couriervendor; ?>' ;
    
    var pickupdate=document.getElementById("pickupdate").value;
    var AWB_Number=document.getElementById("AWB_Number").value;
    var PickupNumber=document.getElementById("PickupNumber").value;
    var PickupStatus=document.getElementById("PickupStatus").value;
    
    //alert("pickupdate:"+pickupdate+"&AWB_Number:"+AWB_Number+"&PickupNumber:"+PickupNumber+"&PickupStatus:"+PickupStatus);
    
    if(pickupdate=="" && AWB_Number=="" && PickupNumber=="" && PickupStatus=="" ){
        
        alert("Plz. fill atleast one condition!");
    }
    else{
        
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                
                document.getElementById("result").innerHTML = xmlhttp.responseText;
            }
        };
        xmlhttp.open("GET","pickup_ajax.php?action=Search&couriervendor="+couriervendor+"&pickupdate="+pickupdate+"&AWB_Number="+AWB_Number+"&PickupNumber="+PickupNumber+"&PickupStatus="+PickupStatus,true);
        xmlhttp.send();
    }
  
}

function Cancel(AWB_Number){
    
    if (confirm("Are you sure you want to delete pickup!") == true) {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                
                list();
            }
        };
        xmlhttp.open("GET","pickup_ajax.php?action=Cancel&AWB_Number="+AWB_Number,true);
        xmlhttp.send();
    }
    
    
}





function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("container").style.marginLeft = "250px";
    document.getElementById("header_nav").style.marginLeft = "250px";
    //document.body.style.backgroundColor = "rgba(0,0,0,0.7)";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("container").style.marginLeft= "0";
    document.getElementById("header_nav").style.marginLeft = "0";
    document.body.style.backgroundColor = "white";
}






$(document).ready(function() {
                  var elementPosition = $('.panel-heading').offset();
                  $(window).scroll(function() {
                                   if($(window).scrollTop() > elementPosition.top){
                                   $('.panel-heading').css('position','fixed').css('top','0');
                                   } else {
                                   $('.panel-heading').css('position','static');
                                   }
                                   });
                  });



</script>

<script type="text/javascript">
$('.form_datetime').datetimepicker({
                                   language:  'en',
                                   weekStart: 1,
                                   todayBtn:  1,
                                   autoclose: 1,
                                   todayHighlight: 1,
                                   startView: 2,
                                   forceParse: 0,
                                   showMeridian: 1
                                   });
$('.form_date').datetimepicker({
                               //language:  'fr',
                               weekStart: 1,
                               todayBtn:  1,
                               autoclose: 1,
                               todayHighlight: 1,
                               startView: 2,
                               minView: 2,
                               forceParse: 0
                               });
$('.form_time').datetimepicker({
                               //language:  'fr',
                               weekStart: 1,
                               todayBtn:  1,
                               autoclose: 1,
                               todayHighlight: 1,
                               startView: 1,
                               minView: 0,
                               maxView: 1,
                               forceParse: 0
                               });
</script>


</body>
</html>