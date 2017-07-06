<?php
    
    $userid="Santy";
    
    $vendorid=$_POST['sender_vendorid'];
    $pincode=$_POST['from_pin'];
    $name=$_POST['sender_name'];
    $com_name=$_POST['sender_com_name'];
    $address=$_POST['sender_address'];
    $city=$_POST['sender_city'];
    $state=$_POST['sender_state'];
    $phone=$_POST['sender_phone'];
    
    
    $couriervendor=$_POST['couriervendor'];
    $pickupdate=$_POST['pickupdate'];
    $pickuptime=$_POST['pickuptime'];
    $comp_closing_time=$_POST['comp_closing_time'];
    
    
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
<li><a href="#">Book Pickup</a></li>
<li><a href="#">Track Courier</a></li>
</ul>
</div>
</nav>

<div class="container" id="container">
<h1 align="center"><?php echo $couriervendor;?> Pickup</h1>
<div class="alerts" id="alert_box" style="padding-top: 0em; padding-bottom: 0em;display: none;">
<div class="alert alert-danger" role="alert">
<p id="alert_div" align="center"></p>
<ul id="list">
</ul>
</div>
</div>

<!-- form start -->
<form class="form-horizontal" action="airwaybill.php" role="form" method="post" onsubmit="validate_activity()">
<div class="col-md-12 ">
<div class="panel panel-default panel-table">
<div class="panel-heading">
<div class="row">
<div class="col-md-12">
<h3 class="panel-title">Filters</h3>
<table class="table table-bordered">
<thead style="background-color: gray; color:white; ">
<tr>
<td>Courier Vendor</td>
<td>Shipment Date</td>
<td>Pickup Date</td>
<td>Pickup Status</td>
<td><em class="fa fa-cog" align="center"></em></td>
</tr>
</thead>
<tbody>
<tr class="filters">
<th><input type="text" class="form-control" placeholder="Courier Vendor" id="vendor"></th>
<th><input type="text" class="form-control" placeholder="Shipment Date" id="shipment_date"></th>
<th><input type="text" class="form-control" placeholder="Pickup Date" id="pickup_date"></th>
<th><input type="text" class="form-control" placeholder="Pickup Status" id="pickup_status"></th>
<th><button type="button" class="btn btn-sm btn-danger" onclick="ClearFilter()">X</button> <button type="button" class="btn btn-sm btn-primary" onclick="Search()"><span class="glyphicon glyphicon-search"></span></button> </th>
</tr>
</tbody>
</table>
<button type="button" class="btn btn-sm btn-danger" onclick="Cancel_Pickup()">Cancel Pickup</button>
<button type="button" class="btn btn-sm btn-success" onclick="Schedule_Pickup()">Schedule Pickup</span></button> </th>
</div>
</div>


</div>
<div class="panel-body" id="result" style="overflow: scroll;">


</div>
<div class="panel-footer">
<div class="row">
<div class="col col-md-4">
<div class="input-group">
<span class="input-group-addon" title="Rows per page"><i class="glyphicon glyphicon-th-list"></i></span>
<select class="form-control selcls" name="rowperpage" id="rowperpage" onchange="PageDisplay()" >
<option value="5" selected="selected">5</option>
<option value="15">15</option>
<option value="25">25</option>
<option value="50">50</option>
<option value="100">100</option>
</select>
</div>
</div>
<div class="col col-md-8">
<input type="hidden" class="form-control" id="page_count">
<ul class="pagination" id="pagination1">

</ul>
</div>
</div>
</div>
</div>
</div>

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
<button type="button" class="btn btn-sm btn-primary">Check Current Batch Status</button>
<button type="button" class="btn btn-sm btn-success">Schedule Next Pickup</button>
</div>
</div>
</div>
</div>


<!-- /.form Submit -->


</form>
</div>


<script>

function list(){
    
    var couriervendor="<?php echo $couriervendor; ?>";
    
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
    xmlhttp.open("GET","pickup_list_action.php?action=select_courier&couriervendor="+couriervendor+"&row_count="+row_count,true);
    xmlhttp.send();
    
    
}

function pagecount(i){
    
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
            
            //document.getElementById("pagecount").value = xmlhttp.responseText;
            var pagecount=xmlhttp.responseText;
            document.getElementById("page_count").value=pagecount;
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
            
            $("#pagination1").append('<li class="previous disabled" id="li_prev"><a href="javascript:void(0)"  onclick="changePagination(this.id)"id="prev">&larr; Previous</a></li>');
            
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
            
            $("#pagination1").append('<li class="next" id="li_next"><a href="javascript:void(0)"  onclick="changePagination(this.id)"id="next">Next &rarr;</a></li>');
        }
    };
    xmlhttp.open("GET","pickup_list_action.php?action=pagecount&row_count="+row_count,true);
    xmlhttp.send();
    
}


function changePagination(list_id){
    
    var row = document.getElementById("rowperpage");
    var row_count = row.options[row.selectedIndex].value;
    
    var page_count=document.getElementById("page_count").value;
    
    var page=list_id;
    var current_page=$("#pagination1 li").find("a.active").attr('id');
    
    
    if(isNaN(list_id)){
        if(list_id=="next"){
            
            var nextpage= parseInt(current_page)+1;
            page=nextpage;
            $('a').removeClass('active');
            $('#'+nextpage).addClass('active');
            
        }
        else if(list_id=="prev"){
            var previous_page=parseInt(current_page)-1;
            page=previous_page;
            $('a').removeClass('active');
            $('#'+previous_page).addClass('active');
            
            
        }
    }
    else{
        
        $('a').removeClass('active');
        $('#'+list_id).addClass('active');
        
        var lowerlimit=parseInt(page)-2;
        var upperlimit=parseInt(page)+3;
        
        if(lowerlimit>0 && upperlimit<(parseInt(page_count)-2)){
            
            $("#pagination1").empty();
            
            $("#pagination1").append('<li class="previous disabled" id="li_prev"><a href="javascript:void(0)"  onclick="changePagination(this.id)"id="prev">&larr; Previous</a></li>');
            
            
            for(i=lowerlimit;i<upperlimit;i++){
                $("#pagination1").append('<li><a class="list" href="javascript:void(0)"  onclick="changePagination(this.id)" id='+i+'>'+i+'</a></li>');
            }
            
            $("#pagination1").append('<li class="next" id="li_next"><a href="javascript:void(0)"  onclick="changePagination(this.id)"id="next">Next &rarr;</a></li>');
            
            $('#'+list_id).addClass('active');
        }
        
    }
    
    /////////////////////////////////////////
    //////////// Disable and Enable /////////////////
    ////////////////////////////////////////
    
    
    
    
    
    
    
    
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
    xmlhttp.open("GET","pickup_list_action.php?action=changePagination&page="+page+"&row_count="+row_count,true);
    xmlhttp.send();
    
    
}


function PageDisplay(){
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
    xmlhttp.open("GET","pickup_list_action.php?action=displayrows&row_count="+row_count,true);
    xmlhttp.send();
    
}

function Search(){
    
    var shipper_name = document.getElementById("shipper_name").value;
    var receiver_name = document.getElementById("receiver_name").value;
    var shipment_date = document.getElementById("shipment_date").value;
    var vendor = document.getElementById("vendor").value;
    var airwaybill_number = document.getElementById("airwaybill_number").value;
    var row = document.getElementById("rowperpage");
    var row_count = row.options[row.selectedIndex].value;
    
    if(shipper_name==""&& receiver_name=="" && shipment_date=="" && vendor=="" && airwaybill_number=="" ){
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
                
                document.getElementById("result").innerHTML = xmlhttp.responseText;
                pagecount(row_count);
            }
        };
        xmlhttp.open("GET","pickup_list_action.php?action=search&shipper_name="+shipper_name+"&receiver_name="+receiver_name+"&shipment_date="+shipment_date+"&vendor="+vendor+"&airwaybill_number="+airwaybill_number+"&row_count="+row_count,true);
        xmlhttp.send();
        
    }
    
}

function ClearFilter(){
    
    document.getElementById("shipper_name").value="";
    document.getElementById("receiver_name").value="";
    document.getElementById("shipment_date").value="";
    document.getElementById("vendor").value="";
    document.getElementById("airwaybill_number").value="";
    
    list();
}



function checkAll(ele) {
    var checkboxes = document.getElementsByTagName('input');
    if (ele.checked) {
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == 'checkbox') {
                checkboxes[i].checked = true;
            }
        }
    } else {
        for (var i = 0; i < checkboxes.length; i++) {
            console.log(i)
            if (checkboxes[i].type == 'checkbox') {
                checkboxes[i].checked = false;
            }
        }
    }
}


function Schedule_Pickup(){
    
    var userid='<?php echo $userid; ?>';
    
    var vendorid= '<?php echo $vendorid; ?>' ;
    var pincode='<?php echo $pincode;?>';
    var name='<?php echo $name;?>';
    var com_name= '<?php echo $com_name;?>';
    var address= '<?php echo $address; ?>' ;
    var city= '<?php echo $city; ?>' ;
    var state='<?php echo $state; ?>' ;
    var phone='<?php echo $phone;?>';
    
    
    var couriervendor= '<?php echo $couriervendor; ?>' ;
    var pickupdate='<?php echo $pickupdate; ?>';
    var pickuptime='<?php echo $pickuptime;?>';
    var comp_closing_time= '<?php echo $comp_closing_time; ?>';

    
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
    
    //alert(vals);
    
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
            
            document.getElementById("response").innerHTML = "This will take some moment to Schedule Pickup. To know the current Status of Pickup kindly refer Batch ID:"+xmlhttp.responseText+".";
            
            $('#details').modal('show');
        }
    };
    xmlhttp.open("GET","pickup_list_action.php?action=Schedule_Pickup&UID="+vals+"&userid="+userid+"&vendorid="+vendorid+"&pincode="+pincode+"&name="+name+"&com_name="+com_name+"&address="+address+"&city="+city+"&state="+state+"&phone="+phone+"&couriervendor="+couriervendor+"&pickupdate="+pickupdate+"&pickuptime="+pickuptime+"&comp_closing_time="+comp_closing_time,true);
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



</body>
</html>
