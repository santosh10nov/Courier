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
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<style>

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
<li><a href="index.php">Dashboard</a></li>
<li><a href="ServiceTAT.php">Service Availability/TAT</a></li>
<li class="dropdown active">
<a class="dropdown-toggle" data-toggle="dropdown" href="#">AirwayBill</a>
<ul class="dropdown-menu">
<li><a href="Generate_AirwayBill.php">Generate AirwayBill</a></li>
<li><a href="AirwayBillList.php">AirwayBill List</a></li>
<li><a href="DispatchList.php">Dispatch List</a></li>
<li><a href="ReceiveCourier.php">Receive Courier</a></li>
<li><a href="ReceiveCourierList.php">Receive Courier List</a></li>
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
<h1 align="center">Receive Courier </h1>
<div id = "alert_placeholder"></div>
<!-- form start -->
<form class="form-horizontal" action="" role="form" method="post" onsubmit="validate_activity()">

<div class="row">
<!-- right column -->
<div class="col-sm-6">
<div class="box box-primary">
<div class="box-header with-border">
<h4 class="box-title">Sender Detail
<button type="button" class="btn btn-default btn-sm"  id="sender_add_fav" onclick="favourite(this)">
<span class="glyphicon glyphicon-heart"></span> Favourite
</button>
<button type="button" class="btn btn-danger btn-sm" id="sender_reset_fav" onclick="Reset(this)" style="display:none">Reset</button>
<button type="button" class="btn btn-info btn-sm" id="sender_update_fav" onclick="favourite(this)" style="display:none">
<span class="glyphicon glyphicon-heart"></span> Update
</button>
<button type="button" class="btn btn-success btn-sm"  id="sender_fav_list" data-toggle="modal" data-target="#SearchModal"  data-parties="sender">
<span class="glyphicon glyphicon-user"></span> List
</button>
</h4>
</div>
<!-- /.box-header -->
<div class="box-body">
<table class="table table-striped table-bordered">
<tr>
<td>Vendor ID</td>
<th><input type="text" class="form-control" name="sender_vendorid" id="sender_vendorid" placeholder=""></th>
</tr>
<tr>
<td class="col-sm-4">Pickup Pincode</td>
<th><input type="text" class="form-control" name="from_pin" id="from_pin" placeholder="" onchange="senderlocation(this.value)"></th>
</tr>
<tr>
<td>Sender Name</td>
<th><input type="text" class="form-control" name="sender_name" id="sender_name" placeholder=""></th>
</tr>
<tr>
<td>Company Name</td>
<th><input type="text" class="form-control" name="sender_com_name" id="sender_com_name" placeholder=""></th>
</tr>
<tr>
<td>Address</td>
<th><textarea class="form-control custom-control" rows="3" style="resize:none" name="sender_address" id="sender_address" ></textarea></th>
</tr>

<tr>
<td>City</td>
<th><input type="text" class="form-control" name="sender_city" placeholder="" id="sender_city" ></th>
</tr>

<tr>
<td>State</td>
<th><input type="text" class="form-control" name="sender_state" placeholder="" id="sender_state"></th>
</tr>

<tr>
<td>Phone Number</td>
<th><input type="text" class="form-control" name="sender_phone" placeholder="" id="sender_phone" ></th>
</tr>
</table>

</div>
<!-- /.box-footer -->
</div>
<!-- /.box -->
</div>



<div class="col-sm-6">
<div class="box box-primary">
<div class="box-header with-border">
<h4 class="box-title">Shipment Details

</h4>
</div>
<!-- /.box-header -->
<div class="box-body">
<table class="table table-striped table-bordered">
<tr>
<td>Airway Bill Number</td>
<td>
<input type="text" id="AWB" name="AWB" class="form-control" placeholder="">
</td>
</tr>

<tr>
<td>Courier Vendor</td>
<td>
<div>
<input type="text" id="OtherCourierVendor" name="OtherCourierVendor" class="form-control" placeholder="">
</div>
</td>
</tr>

<tr>
<td>Courier Service</td>
<td>

<input type="text" id="OtherCourierService" name="OtherCourierService" class="form-control" placeholder="" >
</td>
</tr>

<tr>
<td> COD</td>
<td>
<input type="radio" id="CODYes"  name="COD" value="Yes" onclick="Collectable(this)"> Yes &nbsp;&nbsp;
<input type="radio" id="CODNo" name="COD" value="No"  onclick="Collectable(this)"> No
<input type="text" id="CollectableAmount" name="CollectableAmount" class="form-control" placeholder="Collectable Amount" style="display:none">
</td>

</tr>

<tr>
<td>Courier Receive Date</td>
<td>
<div class="input-group date form_date " data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
<input class="form-control" size="10" type="text" value="" name="date" readonly>
<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
</div>
<input type="hidden" id="dtp_input2" value="" />
</td>
</tr>
<tr>
<td>Invoice Value</td>
<th><input type="text" class="form-control" name="invoice" placeholder=""></th>
</tr>

<tr>
<td>Purpose</td>
<th><div>
<select class="form-control selcls" name="purpose" id="purpose" >
<option value="" selected="selected">Purpose of Shipment</option>
<option value="GIFT">GIFT</option>
<option value="NOT_SOLD">NOT SOLD</option>
<option value="PERSONAL_EFFECTS">PERSONAL EFFECTS</option>
<option value="REPAIR_AND_RETURN">REPAIR AND RETURN</option>
<option value="SAMPLE">SAMPLE</option>
<option value="SOLD">SOLD</option>
</select></div>

</tr>

<tr>
<td>Package Count</td>
<td>
<select class="form-control selcls" name="packagecount" id="packagecount" >
<option value="" selected="selected">Select Package Count</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
</select>
</td>
</tr>

<tr>
<td>Shipment Content</td>
<td>
<input type="radio" id="Documents" name="shipmentcontent" value="Documents" checked="checked"> Documents &nbsp;&nbsp;
<input type="radio" id="Commodities" name="shipmentcontent" value="Commodities"> Commodities
</td>
</tr>

</table>
<button type="reset" class="btn btn-danger">Reset</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<button type="submit" class="btn btn-success" name="SubmitButton" onclick=" validate_activity()">Receive</button>
</div>
<!-- /.box-footer -->
</div>
<!-- /.box -->
</div>
</div>

<div id="hiddendiv">


</div>


<div id="packageModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h4 class="modal-title">Package Details</h4>
</div>
<div class="modal-body">
<div class="col-md-12 column">
<table class="table table-bordered table-hover" id="tab_logic">
<thead style="background-color: gray; color:white; ">
<tr >
<th class="text-center">
#
</th>
<th class="text-center">
Length
</th>
<th class="text-center">
Width
</th>
<th class="text-center">
Height
</th>
<th class="text-center">
Weight
</th>
</tr>
</thead>
<tbody>
<tr id='package0'>
<td>
1
</td>
<td>
<input type="text" name='length0' id='length0' placeholder='Length in cm' class="form-control"/>
</td>
<td>
<input type="text" name='breath0' id='breath0' placeholder='Breath in cm' class="form-control"/>
</td>
<td>
<input type="text" name='height0' id='height0' placeholder='Height in cm' class="form-control"/>
</td>
<td>
<input type="text" name='weight0' id='weight0' placeholder='Weight in KG' class="form-control"/>
</td>
</tr>
<tr id='package1'></tr>
</tbody>
</table>
<input type="hidden" name="package_count" id="package_count" value=""/>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
<button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
</div>
</div>
</div>
</div>

<div id="ShipmentContentModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h4 class="modal-title">Commodity Details</h4>
</div>
<div class="modal-body">
<div class="row clearfix">
<div class="col-md-12 column">

<table class="table table-bordered table-hover" id="tab_logic1">
<thead style="background-color: gray; color:white; ">
<tr >
<th class="text-center">
#
</th>
<th class="text-center">
Commodity
</th>
<th class="text-center">
Commodity Description
</th>
<th class="text-center">
Quantity
</th>
<th class="text-center">
Unit Weight
</th>
<th class="text-center">
Unit Value
</th>
</tr>
</thead>
<tbody>
<tr id='addr0'>
<td>
1
</td>
<td>
<input type="text" name='Commodity0'  placeholder='' class="form-control"/>
</td>
<td>
<input type="text" name='Commodity_desc0' placeholder='' class="form-control"/>
</td>
<td>
<input type="text" name='Commodity_quan0' placeholder='' class="form-control"/>
</td>
<td>
<input type="text" name='Commodity_weight0' placeholder='' class="form-control"/>
</td>
<td>
<input type="text" name='CommodityValue0' placeholder='' class="form-control"/>
</td>
</tr>
<tr id='addr1'></tr>
</tbody>
</table>
<input type="hidden" name="commodity_count" id="commodity_count" value="1"/>
</div>
<a id="add_row" class="btn btn-success pull-left">Add Row</a><a id='delete_row' class="pull-right btn btn-danger">Delete Row</a>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
<button type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
</div>
</div>
</div>
</div>
</div>


<div id="SearchModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h4 class="modal-title">Contact List</h4>
</div>
<div class="modal-body" id="modalbody1">
<form>
<div class="row">
<div class="panel panel-primary filterable">
<div class="panel-heading">
<h3 class="panel-title">Users</h3>
</div>
<input type="hidden" name="parties" id="parties_id" value=""/>
<table class="table">
<thead style="background-color: gray; color:white; ">
<tr class="filters">
<th><input type="text" class="form-control" placeholder="VendorId" id="search_vendor_id"  onkeyup="search()"></th>
<th><input type="text" class="form-control" placeholder="Name" id="contact_name"  onkeyup="search()"></th>
<th><input type="text" class="form-control" placeholder="Company Name" id="company_name" onkeyup="search()"></th>
<th><input type="text" class="form-control" placeholder="Phone Number" id="phonenumber" onkeyup="search()"></th>
</tr>
</thead>
</table>
<div id="result"></div>
</div>
</div>
</form>
</div>
</div>
</div>
</div>


<!-- /.form Submit -->


</form>

</div>

<script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>

<script type="text/javascript">
$(document).ready(function(){
                  var i=1;
                  $("#packagecount").click(function(){
                                           var j=$("#packagecount").val();
                                           if(i<j){
                                           $('#packageModal').modal();
                                           var diff=j-i;
                                           for( var k = 0; k<diff; k++) {
                                           $('#package'+i).append("<td>"+ (i+1) +"</td><td><input name='length"+i+"' id='length"+i+"' type='text' placeholder='Length in cm' class='form-control input-md'  /> </td><td><input  name='breath"+i+"' id='breath"+i+"' type='text' placeholder='Breath in cm'  class='form-control input-md'></td><td><input  name='height"+i+"' id='height"+i+"' type='text' placeholder='Height in cm'  class='form-control input-md'></td><td><input  name='weight"+i+"' id='weight"+i+"' type='text' placeholder='Weight in KG'  class='form-control input-md'></td>");
                                           
                                           $('#tab_logic').append('<tr id="package'+(i+1)+'"></tr>');
                                           i++;
                                           }
                                           document.getElementById("package_count").value = i;
                                           }
                                           if(i>j){
                                           $('#packageModal').modal();
                                           var diff=i-j;
                                           for( var k = 0; k<diff; k++) {
                                           $("#package"+(i-1)).html('');
                                           i--;
                                           }
                                           document.getElementById("package_count").value = i;
                                           }
                                           if(i==1){
                                           $('#packageModal').modal();
                                           document.getElementById("package_count").value = i;
                                           }
                                           
                                           
                                           });
                  
                  $("#Commodities").click(function () {
                                          if ($("#Commodities").is(":checked")) {
                                          $('#ShipmentContentModal').modal();
                                          var i=1;
                                          $("#add_row").click(function(){
                                                              $('#addr'+i).html("<td>"+ (i+1) +"</td><td><input name='Commodity"+i+"' type='text' placeholder='' class='form-control input-md'  /> </td><td><input  name='Commodity_desc"+i+"' type='text' placeholder=''  class='form-control input-md'></td><td><input  name='Commodity_quan"+i+"' type='text' placeholder=''  class='form-control input-md'></td><td><input  name='Commodity_weight"+i+"' type='text' placeholder=''  class='form-control input-md'></td><td><input  name='CommodityValue"+i+"' type='text' placeholder=''  class='form-control input-md'></td>");
                                                              
                                                              $('#tab_logic1').append('<tr id="addr'+(i+1)+'"></tr>');
                                                              i++;
                                                              document.getElementById("commodity_count").value = i;
                                                              });
                                          $("#delete_row").click(function(){
                                                                 if(i>1){
                                                                 $("#addr"+(i-1)).html('');
                                                                 i--;
                                                                 document.getElementById("commodity_count").value = i;
                                                                 }
                                                                 });
                                          
                                          
                                          }
                                          });
                  
                  });


$('#SearchModal').on('show.bs.modal', function(e) {
                     var parties_name = $(e.relatedTarget).data('parties');
                     $(e.currentTarget).find('input[name="parties"]').val(parties_name);
                     });

$(".modal").on("hidden.bs.modal", function(){
               $('#modalbody1').find('input,textarea').val('');
               document.getElementById("result").innerHTML=" ";
               
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

<script>

function senderlocation(str) {
    if (str.length!=6 || isNaN(str)) {
        if(str!=""){
            alert("Enter Correct Pincode");
            return;
        }
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var res = xmlhttp.responseText.split("|");
                document.getElementById("sender_city").value = res[0];
                document.getElementById("sender_state").value = res[1];
            }
        };
        xmlhttp.open("GET","location_ajax.php?from_pin="+str+"&location=s",true);
        xmlhttp.send();
        
    }
}

function receiverlocation(str) {
    if (str.length!=6 || isNaN(str)) {
        if(str!=""){
            alert("Enter Correct Pincode");
            return;
            
        }
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var res = xmlhttp.responseText.split("|");
                document.getElementById("receiver_city").value = res[0];
                document.getElementById("receiver_state").value = res[1];
            }
        };
        xmlhttp.open("GET","location_ajax.php?to_pin="+str+"&location=r",true);
        xmlhttp.send();
        
    }
}




function favourite(i){
    
    if(i.id=="sender_add_fav" || i.id=="sender_update_fav"){
        
        var pin= document.getElementById("from_pin").value;
        var name = document.getElementById("sender_name").value;
        var com_name = document.getElementById("sender_com_name").value;
        var address = document.getElementById("sender_address").value;
        var city = document.getElementById("sender_city").value;
        var state = document.getElementById("sender_state").value;
        var phone = document.getElementById("sender_phone").value;
        var vendorid=document.getElementById("sender_vendorid").value;
        
        if(name==""||phone==""||com_name==""){
            alert("Plz. add Name,Company Name, Phone Number");
        }
        else {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    
                    notification(xmlhttp.responseText);
                }
            };
            
            
            if(i.id=="sender_add_fav"){
                
                xmlhttp.open("GET","ContactFav_ajax.php?pin="+pin+"&vendorid="+vendorid+"&name="+name+"&companyname="+com_name+"&address="+address+"&city="+city+"&state="+state+"&phone="+phone+"&fav=1&parties=sender",true);
                xmlhttp.send();
            }
            
            else if(i.id=="sender_update_fav"){
                
                xmlhttp.open("GET","ContactFav_ajax.php?pin="+pin+"&vendorid="+vendorid+"&name="+name+"&companyname="+com_name+"&address="+address+"&city="+city+"&state="+state+"&phone="+phone+"&fav=2&parties=sender",true);
                xmlhttp.send();
                
            }
            
        }
        
        
    }
    
    else if( i.id=="receiver_add_fav" || i.id=="receiver_update_fav" ){
        
        var pin= document.getElementById("to_pin").value;
        var name = document.getElementById("receiver_name").value;
        var com_name = document.getElementById("receiver_com_name").value;
        var address = document.getElementById("receiver_address").value;
        var city = document.getElementById("receiver_city").value;
        var state = document.getElementById("receiver_state").value;
        var phone = document.getElementById("receiver_phone").value;
        var vendorid=document.getElementById("receiver_vendorid").value;
        
        
        if(name==""||phone==""||com_name==""){
            alert("Plz. add Name,Company Name, Phone Number");
        }
        else {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    
                    notification(xmlhttp.responseText);
                }
            };
            
            
            
            if(i.id=="receiver_add_fav"){
                
                xmlhttp.open("GET","ContactFav_ajax.php?pin="+pin+"&vendorid="+vendorid+"&name="+name+"&companyname="+com_name+"&address="+address+"&city="+city+"&state="+state+"&phone="+phone+"&fav=1&parties=receiver",true);
                xmlhttp.send();
            }
            
            else if(i.id=="receiver_update_fav"){
                
                xmlhttp.open("GET","ContactFav_ajax.php?pin="+pin+"&vendorid="+vendorid+"&name="+name+"&companyname="+com_name+"&address="+address+"&city="+city+"&state="+state+"&phone="+phone+"&fav=2&parties=receiver",true);
                xmlhttp.send();
                
            }
            
            
        }
        
        
    }
    
}


function notification(response)
{
    
    var arr =  JSON.parse(response)
    
    
    if(arr[0]=="1"){
        alert("Contact Detials Added Successfully");
        if(arr[1]=="sender"){
            document.getElementById("sender_add_fav").style.display = 'none';
            document.getElementById("sender_update_fav").style.display = 'inline';
            document.getElementById("sender_reset_fav").style.display = 'inline';
            document.getElementById("sender_fav_list").innerHTML='<span class="glyphicon glyphicon-user"></span>';
        }
        else if(arr[1]=="receiver"){
            document.getElementById("receiver_add_fav").style.display = 'none';
            document.getElementById("receiver_update_fav").style.display = 'inline';
            document.getElementById("receiver_reset_fav").style.display = 'inline';
            document.getElementById("receiver_fav_list").innerHTML='<span class="glyphicon glyphicon-user"></span>';
        }
        
    }
    else if(arr[0]=="2"){
        alert("Contact Detials Updated Successfully");
        if(arr[1]=="sender"){
            document.getElementById("sender_add_fav").style.display = 'none';
            document.getElementById("sender_update_fav").style.display = 'inline';
            document.getElementById("sender_reset_fav").style.display = 'inline';
            document.getElementById("sender_fav_list").innerHTML='<span class="glyphicon glyphicon-user"></span>';
        }
        else if(arr[1]=="receiver"){
            document.getElementById("receiver_add_fav").style.display = 'none';
            document.getElementById("receiver_update_fav").style.display = 'inline';
            document.getElementById("receiver_reset_fav").style.display = 'inline';
            document.getElementById("receiver_fav_list").innerHTML='<span class="glyphicon glyphicon-user"></span>';
        }
        
    }
    
}


function search(){
    
    
    var searchname= document.getElementById("contact_name").value;
    var searchcomp_name= document.getElementById("company_name").value;
    var searchphone= document.getElementById("phonenumber").value;
    var parties=document.getElementById("parties_id").value;
    var searchvendorid=document.getElementById("search_vendor_id").value;
    
    //alert(searchcomp_name);
    
    if(searchname=="" && searchcomp_name=="" && searchphone=="" && searchvendorid==""){
        
    }else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                
                
                $('#result').html(xmlhttp.responseText);
                //document.getElementById("searchphone").value=xmlhttp.responseText;
                
            }
        };
        xmlhttp.open("GET","SearchContact_ajax.php?searchname="+searchname+"&comp_name="+searchcomp_name+"&phone="+searchphone+"&parties="+parties+"&searchvendorid="+searchvendorid,true);
        xmlhttp.send();
        
        
    }
}

function selectRow(pincode,name,comp_name,address,city,state,phone,parties,vendor_id){
    
    if(parties=="sender"){
        
        document.getElementById("sender_vendorid").value=vendor_id;
        document.getElementById("from_pin").value=pincode;
        document.getElementById("sender_name").value=name;
        document.getElementById("sender_com_name").value=comp_name;
        document.getElementById("sender_address").value= address;
        document.getElementById("sender_city").value= city;
        document.getElementById("sender_state").value= state;
        document.getElementById("sender_phone").value= phone;
        
        
        document.getElementById("sender_add_fav").style.display = 'none';
        document.getElementById("sender_update_fav").style.display = 'inline';
        document.getElementById("sender_reset_fav").style.display = 'inline';
        document.getElementById("sender_fav_list").innerHTML='<span class="glyphicon glyphicon-user"></span>';
        
        
        
    }
    else if(parties=="receiver"){
        document.getElementById("receiver_vendorid").value=vendor_id;
        document.getElementById("to_pin").value=pincode;
        document.getElementById("receiver_name").value=name;
        document.getElementById("receiver_com_name").value=comp_name;
        document.getElementById("receiver_address").value= address;
        document.getElementById("receiver_city").value= city;
        document.getElementById("receiver_state").value= state;
        document.getElementById("receiver_phone").value= phone;
        
        document.getElementById("receiver_add_fav").style.display = 'none';
        document.getElementById("receiver_update_fav").style.display = 'inline';
        document.getElementById("receiver_reset_fav").style.display = 'inline';
        document.getElementById("receiver_fav_list").innerHTML='<span class="glyphicon glyphicon-user"></span>';
        
        
        
    }
    
    
}

function Collectable(i){
    
    if(i.id=="CODYes"){
        document.getElementById("CollectableAmount").style.display='block';
        
    }
    else if (i.id=="CODNo"){
        document.getElementById("CollectableAmount").style.display='none';
        document.getElementById("CollectableAmount").value=0;
        
    }
}


function Reset(i){
    
    if(i.id=="sender_reset_fav"){
        document.getElementById("sender_add_fav").style.display = 'inline';
        document.getElementById("sender_update_fav").style.display = 'none';
        document.getElementById("sender_reset_fav").style.display = 'none';
        document.getElementById("sender_fav_list").innerHTML='<span class="glyphicon glyphicon-user"></span>List';
        
        document.getElementById("sender_vendorid").value="";
        document.getElementById("from_pin").value="";
        document.getElementById("sender_name").value="";
        document.getElementById("sender_com_name").value="";
        document.getElementById("sender_address").value="";
        document.getElementById("sender_city").value="";
        document.getElementById("sender_state").value="";
        document.getElementById("sender_phone").value="";
        
        
    }
    else if(i.id=="receiver_reset_fav"){
        
        document.getElementById("receiver_add_fav").style.display = 'inline';
        document.getElementById("receiver_update_fav").style.display = 'none';
        document.getElementById("receiver_reset_fav").style.display = 'none';
        document.getElementById("receiver_fav_list").innerHTML='<span class="glyphicon glyphicon-user"></span>List';
        
        document.getElementById("receiver_vendorid").value="";
        document.getElementById("to_pin").value="";
        document.getElementById("receiver_name").value="";
        document.getElementById("receiver_com_name").value="";
        document.getElementById("receiver_address").value= "";
        document.getElementById("receiver_city").value= "";
        document.getElementById("receiver_state").value= "";
        document.getElementById("receiver_phone").value= "";
        
        
    }
}



function showalert(message,alerttype) {
    
    $('#alert_placeholder').append('<div id="alertdiv" class="alert ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
    
    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
               
               window.location = "ReceiveCourier.php";
               $("#alertdiv").remove();
               
               }, 1500);
    
    
}


<?php
    
    if(isset($_POST['SubmitButton'])){
        
        $receive_date=$_POST["date"];
        $nowtime = time();
        $fedex_shipment_date=$receive_date."T".$nowtime;
        $convert_date = new DateTime($receive_date);
        $receive_date1 = date_format($convert_date, 'd-M-Y');
        
        /////////////////////////////////////////////////
        ///////////// Courier Vendor Details ////////////
        /////////////////////////////////////////////////
        
        $OtherCourierVendor=$_POST["OtherCourierVendor"];
        $OtherCourierService=$_POST["OtherCourierService"];
        $OtherCourierAWB=$_POST["AWB"];
        
        
        $from_pin=$_POST["from_pin"];
        $COD=$_POST["COD"];
        $CollectableAmount=$_POST["CollectableAmount"];
        $shipmentcontent=$_POST["shipmentcontent"];
        
        //////////////////////////////////////
        /////// Sender Details///////////////
        /////////////////////////////////////
        $sender_vendorid=$_POST["sender_vendorid"];
        $sender_name=$_POST["sender_name"];
        $sender_company=$_POST["sender_com_name"];
        $sender_city=$_POST["sender_city"];
        $sender_state=$_POST["sender_state"];
        $sender_address=$_POST["sender_address"];
        $sender_phone=$_POST["sender_phone"];
        $sender_info=array($from_pin,$sender_name,$sender_company,$sender_address,$sender_city,$sender_state,$sender_phone);
        
        
        //////////////////////////////////////
        /////// Package Details///////////////
        /////////////////////////////////////
        
        
        $packagecount= $_POST["package_count"];
        $purpose=$_POST["purpose"];
        $cost=$_POST["invoice"];
        
        $TotalWeight=0;
        $Package=array();
        
        for($i=0;$i<$packagecount;$i++){
            $length=$_POST["length".$i];
            $breath=$_POST["breath".$i];
            $height=$_POST["height".$i];
            $weight=$_POST["weight".$i];
            $TotalWeight=$TotalWeight+$weight;
            
            $Package["Dim".$i]["length".$i]=$_POST["length".$i];
            $Package["Dim".$i]["breath".$i]=$_POST["breath".$i];
            $Package["Dim".$i]["height".$i]=$_POST["height".$i];
            $Package["Dim".$i]["weight".$i]=$_POST["weight".$i];
            
        }
        
        $package_details=array($TotalWeight,$length,$breath,$height,$purpose,$cost);  ///////////// We have to remove this line//////////////////////////
        
        if($shipmentcontent=="Commodities"){
            
            $commodity_count= $_POST["commodity_count"];
            $Commodity_desc=array();
            
            for($i=0;$i<$commodity_count;$i++){
                
                $Commodity_desc["Com".$i]["Commodity".$i]=$_POST["Commodity".$i];
                $Commodity_desc["Com".$i]["Commodity_desc".$i]=$_POST["Commodity_desc".$i];
                $Commodity_desc["Com".$i]["Commodity_quan".$i]=$_POST["Commodity_quan".$i];
                $Commodity_desc["Com".$i]["Commodity_weight".$i]=$_POST["Commodity_weight".$i];
                $Commodity_desc["Com".$i]["CommodityValue".$i]=$_POST["CommodityValue".$i];
                
            }
            
        }
        
        
        
        $status="";
        
        require_once 'dbconfig.php';
        try{
            
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt1=$conn->prepare("INSERT INTO `receive_details` (`RUID`, `ShipperName`, `COD`, `PackageCount`, `AWB_Date`, `CourierVendor`, `CourierService`, `Airwaybill_Number`, `AWB_Status`, `AWB_UID`, `CreatedByUserID`, `CreatedDate`,`purpose`) VALUES (NULL, '$sender_name', '$COD', '$packagecount', '$receive_date', '$OtherCourierVendor', '$OtherCourierService', '$OtherCourierAWB', 'Received', '', '$userid', CURRENT_TIMESTAMP,'$purpose');");
            $stmt1->execute();
            
            $stmt5=$conn->prepare("SELECT * FROM `receive_details` where `Airwaybill_Number`='$OtherCourierAWB' and ShipperName='$sender_name' and AWB_Date='$receive_date' limit 1 ;");
            $stmt5->execute();
            $stmt5->setFetchMode(PDO::FETCH_ASSOC);
            $row5 = $stmt5->fetch();
            $RUID=$row5['RUID'];
            
            
            $stmt2=$conn->prepare("INSERT INTO `receive_parties` (`SNO`, `RUID`, `Shipper_VendorID`, `Shipper_Name`, `Shipper_Comp`, `Shipper_Address`, `Shipper_City`, `Shipper_State`, `Shipper_Pincode`, `Shipper_Phone`, `CreatedDate`) VALUES (NULL,'$RUID', '$sender_vendorid', '$sender_name', '$sender_company', '$sender_address', '$sender_city', '$sender_state', '$from_pin', '$sender_phone', CURRENT_TIMESTAMP);");
            $stmt2->execute();
            
            
            for($i=0;$i<$packagecount;$i++){
                
                
                $L=$Package["Dim".$i]["length".$i];
                $B=$Package["Dim".$i]["breath".$i];
                $H=$Package["Dim".$i]["height".$i];
                $W=$Package["Dim".$i]["weight".$i];
                
                $count=$i+1;
                
                $stmt3=$conn->prepare("INSERT INTO `Receive_Packages` (`SNO`, `RUID`, `Package_Count`, `Length`, `Breath`, `Height`, `Weight`, `Update_time`) VALUES (NULL, '$RUID', '$count', '$L', '$B', '$H', '$W', CURRENT_TIMESTAMP);");
                $stmt3->execute();
                
                
            }
            
            if($shipmentcontent=="Commodities"){
                
                $commodity_count= $_POST["commodity_count"];
                $Commodity_desc=array();
                
                for($i=0;$i<$commodity_count;$i++){
                    
                    $Commodity=$_POST["Commodity".$i];
                    $Commodity_Des=$_POST["Commodity_desc".$i];
                    $Commodity_Quant=$_POST["Commodity_quan".$i];
                    $Commodity_Weight=$_POST["Commodity_weight".$i];
                    $Commodity_Value=$_POST["CommodityValue".$i];
                    
                    
                    $stmt4=$conn->prepare("INSERT INTO `Receive_Commodites` (`SNO`, `RUID`, `Commodity`, `Commodity_Desc`, `Value`, `Quantity`, `Weight`, `Update_Timestamp`) VALUES (NULL, '', '$Commodity', '$Commodity_Des', '$Commodity_Value', '$Commodity_Quant', '$Commodity_Weight', CURRENT_TIMESTAMP);");
                    $stmt4->execute();
                }
                
            }
            
            
            echo "showalert('Courier received successfuly!','alert-success');";
            
        }
        catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    }

    
    
    
    ?>




</script>




</body>
</html>
