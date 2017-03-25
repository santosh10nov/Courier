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
<title>rShipper Service- AirwayBill List</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>

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
<li><a href="index.php">Dashboard</a></li>
<li><a href="ServiceTAT.php">Service Availability/TAT</a></li>
<li class="dropdown active">
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


<div class="container" id="container">
<h1 align="center">AirwayBill List</h1>
<!-- form start -->
<form class="form-horizontal" action="airwaybill.php" role="form" method="post" onsubmit="validate_activity()">
<div class="col-md-12 text-right">
<a href="Generate_AirwayBill.php"><button type="button" class="btn btn-sm btn-success text-right">Create New AirwayBill</button></a></br></br>
</div>
<div class="col-md-12 ">
<div class="panel panel-default panel-table">
<div class="panel-heading">
<div class="row">
<div class="col-sm-12">
<table class="table table-bordered">
<thead style="background-color: gray; color:white; ">
<tr>
<th>Courier Vendor</th>
<th>AirwayBill Number</th>
<th>Shipment Date</th>
<th>Receiver Name</th>
<th>Status</th>
<th></th>
</tr>
</thead>
<tbody>
<tr class="filters">
<td>
<select class="form-control selcls" name="couriervendor1" id="couriervendor1">
</td>
<td>
<input type="text" class="form-control" placeholder="Airway Bill Number" id="airwaybill_number">
</td>
<td>
<div class="input-group date form_date col-md-12" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
<input class="form-control" size="16" type="text" value="" name="shipment_date"  id="shipment_date" readonly>
<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
</div>
<input type="hidden" id="dtp_input2" value="" />
</td>
<td>
<input type="text" class="form-control" placeholder="Receiver Name" id="receiver_name">
</td>
<td>
<select class="form-control selcls" name="Status" id="Status">
<option value="">Select Status</option>
<option value="Dispatched">Dispatched</option>
<option value="Success">AWB Generated</option>
</select>
</td>
<td>
<button type="button" class="btn btn-sm btn-primary" onclick="Search()"><span class="glyphicon glyphicon-search"></span></button><button type="button" class="btn btn-sm btn-danger" onclick="ClearFilter()">X</button>
</td>

</tr>
<tr style="background-color: #34495E; color:white;">
<td>
<div class="input-group" id="numberofrows">
<span class="input-group-addon" title="Rows per page"><i class="glyphicon glyphicon-th-list"></i></span>
<select class="form-control selcls" name="rowperpage" id="rowperpage" onchange="list()" >
<option value="5" selected="selected">5</option>
<option value="25">25</option>
<option value="50">50</option>
<option value="100">100</option>
</select>
</div>
</td>
<td colspan="3" align="center">
<input type="hidden" class="form-control" id="page_count">
<ul class="pagination" id="pagination1"></ul>
</td>
<td colspan="2">
<h4 id="displaypagecount"></h4>
</td>
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





<script>

function list(){
    
    
    
    var id="<?php echo $_SESSION['userSession']; ?>";
    
    var row = document.getElementById("rowperpage");
    var row_count = row.options[row.selectedIndex].value;
    
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
            pagecount(row_count);
        
        }
    };
    xmlhttp.open("GET","airwaybill_list_action.php?action=select&row_count="+row_count+"&id="+id,true);
    xmlhttp.send();
    
    
}

function pagecount(i){
    
    var id="<?php echo $_SESSION['userSession']; ?>";
    
    var row_count=i;
    var page_display=0;
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            
            var arr = JSON.parse(xmlhttp.responseText);
            
            var pagecount=arr['pagecount'];
            document.getElementById("page_count").value=pagecount;
            document.getElementById("displaypagecount").innerHTML = "Total Number of Records:"+arr['countnumrows'];
            $("#pagination1").empty();
            var page_size=5;
            var i;
            var j=1;
            var currentpage=0;
            var upperlimit= (pagecount-currentpage)/page_size;
            var lowerlimmit=(pagecount-currentpage)/page_size;
            
            if(pagecount>page_size){
                page_display=page_size;
            }
            else page_display= pagecount;
            
            //$("#pagination1").append('<li class="previous disabled" id="li_prev"><a href="javascript:void(0)"  onclick="changePagination(this.id)"id="prev">&larr; Previous</a></li>');
            
            for(i=0;i<page_display;i++){
                if(j==1){
                    $("#pagination1").append('<li><a class="list active" href="javascript:void(0)"  onclick="changePagination(this.id)" id='+j+' >'+j+'</a></li>');
                    j++;
                }
                else{
                    $("#pagination1").append('<li><a class="list" href="javascript:void(0)"  onclick="changePagination(this.id)" id='+j+'>'+j+'</a></li>');
                    j++;
                }
            }
            
            //$("#pagination1").append('<li class="next" id="li_next"><a href="javascript:void(0)"  onclick="changePagination(this.id)"id="next">Next &rarr;</a></li>');
            
            CourierVendorList();
        }
    };
    xmlhttp.open("GET","airwaybill_list_action.php?action=pagecount&row_count="+row_count+"&id="+id,true);
    xmlhttp.send();
    
    
}


function changePagination(list_id){
    
    var id="<?php echo $_SESSION['userSession']; ?>";
    
    var row = document.getElementById("rowperpage");
    var row_count = row.options[row.selectedIndex].value;
    
    var page_count=document.getElementById("page_count").value;
    
    var display=0;
    var page_size=5;
    var page_count=document.getElementById("page_count").value;
    
    var page=list_id;
    var current_page=$("#pagination1 li").find("a.active").attr('id');
    
    
    
    if(page_count>page_size){
        
        
        $('a').removeClass('active');
        $('#'+list_id).addClass('active');
        
        var lowerlimit=parseInt(page)-2;
        var upperlimit=parseInt(page)+2;
        
        if(lowerlimit>0 && page<=(parseInt(page_count)-2)){
            
            $("#pagination1").empty();
            
            
            for(i=lowerlimit;i<=upperlimit;i++){
                $("#pagination1").append('<li><a class="list" href="javascript:void(0)"  onclick="changePagination(this.id)" id='+i+'>'+i+'</a></li>');
            }
            
            $('#'+list_id).addClass('active');
        }
        else if(page>(parseInt(page_count)-2)){
            
            $("#pagination1").empty();
            
            
            lowerlimit=page_count- page_size;
            
            //alert(display);
            
            
            for(i=lowerlimit;i<=page_count;i++){
                $("#pagination1").append('<li><a class="list" href="javascript:void(0)"  onclick="changePagination(this.id)" id='+i+'>'+i+'</a></li>');
            }
            
            $('#'+list_id).addClass('active');
            
            
        }
        else{
            
            
            
            $("#pagination1").empty();
            
            for(i=1;i<=page_size;i++){
                $("#pagination1").append('<li><a class="list" href="javascript:void(0)"  onclick="changePagination(this.id)" id='+i+'>'+i+'</a></li>');
            }
            
            $('#'+list_id).addClass('active');
            
        }
        
    }
    else {
        
        
        
        $('a').removeClass('active');
        $('#'+list_id).addClass('active');
        $("#pagination1").empty();
        
        for(i=1;i<=page_count;i++){
            $("#pagination1").append('<li><a class="list" href="javascript:void(0)"  onclick="changePagination(this.id)" id='+i+'>'+i+'</a></li>');
        }
        
        $('#'+list_id).addClass('active');
        
    }

    
    
    document.getElementById("result").innerHTML ="";
    
    
    
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
    xmlhttp.open("GET","airwaybill_list_action.php?action=changePagination&page="+page+"&row_count="+row_count+"&id="+id,true);
    xmlhttp.send();
    
    
}



function Search(){
    
    var id="<?php echo $_SESSION['userSession']; ?>";
    
    var vendor = document.getElementById("couriervendor1");
    var vendor = vendor.options[vendor.selectedIndex].value;
    
    var status = document.getElementById("Status");
    var statusvalue = status.options[status.selectedIndex].value;
    
    var receiver_name = document.getElementById("receiver_name").value;
    var shipment_date = document.getElementById("shipment_date").value;
    var airwaybill_number = document.getElementById("airwaybill_number").value;
    var row = document.getElementById("rowperpage");
    var row_count = row.options[row.selectedIndex].value;
    
    if(statusvalue==""&& receiver_name=="" && shipment_date=="" && vendor=="" && airwaybill_number=="" ){
        alert("Atleast one field");
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
                
                var arr = JSON.parse(xmlhttp.responseText);

                
                document.getElementById("result").innerHTML = arr['value'];
                
                document.getElementById("displaypagecount").innerHTML = "Found Records: "+arr['numrows'];
                
                $("#pagination1").empty();
                $("#numberofrows").hide();
            }
        };
        xmlhttp.open("GET","airwaybill_list_action.php?action=search&StatusVal="+statusvalue+"&receiver_name="+receiver_name+"&shipment_date="+shipment_date+"&vendor="+vendor+"&airwaybill_number="+airwaybill_number+"&id="+id,true);
        xmlhttp.send();
        
    }
    
}

function ClearFilter(){
    
   
    document.getElementById("receiver_name").value="";
    document.getElementById("shipment_date").value="";
    document.getElementById("airwaybill_number").value="";
    
     $("#numberofrows").show();
    
    list();
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


function CancelAWB(UID,AWB_Date,CourierVendor,Airwaybill_Number){
    
    if (confirm("Are you sure you want to cancel AWB?") == true) {
        
        
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
        xmlhttp.open("GET","CancelAWB.php?CourierVendor="+CourierVendor+"&Airwaybill_Number="+Airwaybill_Number+"&UID="+UID+"&AWB_Date="+AWB_Date+"&cancel_type=AirwayBill",true);
        xmlhttp.send();
        
    }
    return;
    
}

function CourierVendorList(){
    
    var id="<?php echo $_SESSION['userSession']; ?>";
    
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            
            $("#couriervendor1").empty();
            var arr = JSON.parse(xmlhttp.responseText);
            var i;
            
            document.getElementById("couriervendor1").options[0]=new Option("Select Courier Vendor","");
            for(i = 0; i < arr.length; i++) {
                document.getElementById("couriervendor1").options[i+1]=new Option(arr[i].value,arr[i].venndor_name);
            }
            
            
        }
    };
    xmlhttp.open("GET","vendoraction.php?action=AirwayBillCourierVendorList&id="+id,true);
    xmlhttp.send();
    
    
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

<script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
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
