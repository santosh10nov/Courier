<!DOCTYPE html>
<html lang="en">
<head>
<title>rShipper Service</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

</head>

<body>

<nav class="navbar navbar-default">
<div class="container-fluid">
<div class="navbar-header">
<a class="navbar-brand" href="index.php">rShipper</a>
</div>
<ul class="nav navbar-nav">
<li><a href="index.php">Home</a></li>
<li><a href="Service_Availabilty.php">Service Availability</a></li>
<li><a href="TAT.php">TAT</a></li>
<li class="nav-item nav-link active"><a href="Generate_AirwayBill.php">Generate AirwayBill</a></li>
<li><a href="#">Book Pickup</a></li>
<li><a href="#">Track Courier</a></li>
</ul>
</div>
</nav>

<div class="container">
<h1 align="center">Generate AirwayBill </h1>
<div class="alerts" id="alert_box" style="padding-top: 0em; padding-bottom: 0em;display: none;">
<div class="alert alert-danger" role="alert">
<p id="alert_div" align="center"></p>
<ul id="list">
</ul>
</div>
</div>

<!-- form start -->
<form class="form-horizontal" action="airwaybill.php" role="form" method="post" onsubmit="return validate_activity(this)">

<div class="row">
<div class="col-xs-12">

<div class="col-xs-4">
<select class="form-control selcls" name="couriervendor" id="couriervendor" onchange="services_list()">
<option value="" selected="selected">Select Courier Vendor</option>
<option value="BlueDart">BlueDart</option>
<option value="FedEx">Fedex</option>
</select>
</div>

<div class="col-xs-4">
<label for="dtp_input2" class="control-label col-xs-5">Shipment Date:  </label>
<div class="input-group date form_date col-xs-7" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
<input class="form-control" size="10" type="text" value="" name="date" readonly>
<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
</div>
<input type="hidden" id="dtp_input2" value="" />
</div>


</div>
</div>


<div class="row">
<!-- right column -->
<div class="col-sm-4">
<div class="box box-primary">
<div class="box-header with-border">
<h3 class="box-title">Sender Detail
<button type="button" class="btn btn-default btn-sm"  id="add_fav" onclick="favourite(this)">
<span class="glyphicon glyphicon-heart"></span> Favourite
</button>
<button type="button" class="btn btn-info btn-sm" id="fav" onclick="favourite(this)" style="display:none">
<span class="glyphicon glyphicon-heart"></span> Favourite
</button>
</h3>
</div>
<!-- /.box-header -->
<div class="box-body">
<table class="table table-striped table-bordered">
<tr>
<td class="col-sm-4">Pickup Pincode:</td>
<th><input type="text" class="form-control" name="from_pin" id="from_pin" placeholder="Enter From Pincode" onchange="senderlocation(this.value)"></th>
</tr>
<tr>
<td>Sender Name:</td>
<th><input type="text" class="form-control" name="sender_name" placeholder="Enter Sender Name"></th>
</tr>
<tr>
<td>Company Name:</td>
<th><input type="text" class="form-control" name="sender_com_name" placeholder="Enter Company Name"></th>
</tr>
<tr>
<td>Address:</td>
<th><textarea class="form-control custom-control" rows="3" style="resize:none" name="sender_address"></textarea></th>
</tr>

<tr>
<td>City:</td>
<th><input type="text" class="form-control" name="sender_city" placeholder="Enter City" id="sender_city" ></th>
</tr>

<tr>
<td>State:</td>
<th><input type="text" class="form-control" name="sender_state" placeholder="Enter State" id="sender_state"></th>
</tr>

<tr>
<td>Phone Number:</td>
<th><input type="text" class="form-control" name="sender_phone" placeholder="Enter Phone Number"></th>
</tr>
</table>

</div>
<!-- /.box-footer -->
</div>
<!-- /.box -->
</div>


<div class="col-sm-4">
<div class="box box-primary" >
<div class="box-header with-border">
<h3 class="box-title">Receiver Details</h3>
</div>
<!-- /.box-header -->
<div class="box-body">
<table class="table table-striped table-bordered">
<tr>
<td class="col-sm-4">Destination Pincode:</td>
<th><input type="text" class="form-control" name="to_pin" id="to_pin" placeholder="Enter To Pincode" onchange="receiverlocation(this.value)"></th>
</tr>
<tr>
<td>Receiver Name</td>
<th><input type="text" class="form-control" name="receiver_name" placeholder="Enter Receiver Name"></th>
</tr>
<tr>
<td>Company Name:</td>
<th><input type="text" class="form-control" name="receiver_com_name" placeholder="Enter Company Name"></th>
</tr>
<tr>
<td>Address:</td>
<th><textarea class="form-control custom-control" rows="3" style="resize:none" name="receiver_address"></textarea></th>
</tr>

<tr>
<td>City:</td>
<th><input type="text" class="form-control" name="receiver_city" placeholder="Enter City" id="receiver_city"></th>
</tr>

<tr>
<td>State:</td>
<th><input type="text" class="form-control" name="receiver_state" placeholder="Enter State" id="receiver_state" ></th>
</tr>

<tr>
<td>Phone Number:</td>
<th><input type="text" class="form-control" name="receiver_phone" placeholder="Enter Phone Number"></th>
</tr>
</table>

</div>
<!-- /.box-footer -->
</div>
<!-- /.box -->
</div>


<div class="col-sm-4">
<div class="box box-primary">
<div class="box-header with-border">
<h3 class="box-title">Shipment Details</h3>
</div>
<!-- /.box-header -->
<div class="box-body">
<table class="table table-striped table-bordered">
<tr>
<td>Length</td>
<td><input type="text" class="form-control" name="length" placeholder="Length in cm"></td>
</tr>
<tr>
<td>Breath</td>
<td><input type="text" class="form-control" name="breath" placeholder="Breath in cm"></td>
</tr>
<tr>
<td>Height</td>
<td><input type="text" class="form-control" name="height" placeholder="Height in cm"></td>
</tr>
<tr>
<td>Weight</td>
<th><input type="text" class="form-control" name="weight" placeholder="Enter Weight in KG"></th>
</tr>


<tr>
<td>Unique Number</td>
<th><input type="text" class="form-control" name="UID" placeholder="Enter Unique Refer. Number"></th>
</tr>

<tr>
<td>Invoice Value:</td>
<th><input type="text" class="form-control" name="invoice" placeholder="Enter Shipment InvoiceValue"></th>
</tr>

<tr>
<td>Commodities:</td>
<th><input type="text" class="form-control" name="purpose" placeholder="Enter "></th>
</tr>

<tr>
<td colspan="2">
<div class="col-xs-8">
<select class="form-control selcls" name="services" id="services">
<option value="" selected="selected">Select Courier Service</option>
</select>
</div>
<button type="submit" class="btn btn-success"  onClick="return validate_activity()">Submit</button>
</td>
</tr>


</table>

</div>
<!-- /.box-footer -->
</div>
<!-- /.box -->
</div>
</div>
<!-- /.row -->



<!-- /.form Submit -->


</form>
</div>


<script type="text/javascript" src="jquery/jquery-1.8.3.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
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

<script>
function senderlocation(str) {
    if (str == "") {
        document.getElementByName("from_pin").innerHTML = "";
        return;
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
    if (str == "") {
        document.getElementByName("to_pin").innerHTML = "";
        return;
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

function services_list(){
    
    var vendor = document.getElementById("couriervendor");
    var vendor_name = vendor.options[vendor.selectedIndex].value;
    
    var from_pin= document.getElementById("from_pin").value;
    var to_pin= document.getElementById("to_pin").value;
    
    
    if(vendor_name==""){
        $("#services").empty();
        document.getElementById("services").options[0]=new Option("Select Courier Service","");
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
              
                courierservice(xmlhttp.responseText);
            }
        };
        xmlhttp.open("GET","Service_ajax.php?couriervendor="+vendor_name,true);
        xmlhttp.send();
        
        
        function courierservice(response) {
        $("#services").empty();
            var arr = JSON.parse(response);
            var i;
            document.getElementById("services").options[0]=new Option("Select Courier Service","");
            for(i = 0; i < arr.length; i++) {
                document.getElementById("services").options[i+1]=new Option(arr[i].servicevalue,arr[i].service);
            }
        
        }
        
        
   
        
    }
    
}

function favourite(i){
    
    if(i.id=="add_fav"){
        document.getElementById(i.id).style.display = 'none';
        document.getElementById("fav").style.display = 'inline';
    }
    
    else if(i.id=="fav")
    {
        document.getElementById(i.id).style.display = 'none';
        document.getElementById("add_fav").style.display = 'inline';
    }
    
}

</script>



</body>
</html>