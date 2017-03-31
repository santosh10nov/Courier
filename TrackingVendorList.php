<?php
    
    $userid=$_GET['userid'];
    $couriervendor=$_GET['vendor'];
    
    
    require_once 'dbconfig.php';
    
    
    
    ?>






<!DOCTYPE html>
<html lang="en">
<head>
<title>rShipper Service- Tracking List</title>
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
<h1 align="center"> <?php echo $couriervendor; ?> Tracking List</h1>
<!-- form start -->
<form class="form-horizontal"  method="post" >
<div class="col-sm-12 ">
<div class="panel panel-default panel-table">
<div class="panel-heading">
<div class="row">
<div class="col-sm-12">
<table class="table table-bordered">
<thead style="background-color: gray; color:white; ">
<tr>
<td>AWB Number</td>
<td>Shipper City</td>
<td>Receiver City</td>
<td> Tracking Status</td>
<td><em class="fa fa-cog" align="center"></em></td>
</tr>
</thead>
<tbody>
<tr class="filters">
<th>
<input class="form-control" size="16" type="text" value="" name="AWB_Number"  id="AWB_Number">
</th>
<th>
<input class="form-control" size="16" type="text" value="" name="ShipperCity"  id="ShipperCity">
</th>
<th>
<input class="form-control" size="16" type="text" value="" name="ReceiverCity"  id="ReceiverCity">
</th>
<th>
<select class="form-control selcls" name="TrackingStatus" id="TrackingStatus">
<option value="" selected="selected">Select Tracking Status</option>
<option value="Delivered">Delivered</option>
<option value="Out For Delivery">Out For Delivery</option>
<option value="In Transit">In Transit</option>
<option value="Picked Up">Picked Up</option>
<option value="Pickup Scheduled">Pickup Scheduled</option>
<option value="Exception">Exception</option>

</select>

</th>
<th><input type="button" value="Search" class = "btn btn-success" id="Search" onclick="Searchlist()">&nbsp;<input type="reset" value="Reset" class = "btn btn-info" onclick="list()">
</th>

</tr>
<tr style="background-color: #34495E; color:white;">
<td>
<div class="input-group" id="numberofrows">
<span class="input-group-addon" title="Rows per page"><i class="glyphicon glyphicon-th-list"></i></span>
<select class="form-control selcls" name="rowperpage" id="rowperpage" onchange="list()" >
<option value="25" selected="selected">25</option>
<option value="50">50</option>
<option value="100">100</option>
</select>
</div>
</td>
<td colspan="2" align="center">
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

<div id="SendSMS" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h4 class="modal-title">Send SMS</h4>
</div>
<div class="modal-body" id="SMSmodalbody">
<form>
<div class="row">
<table class="table table-striped table-bordered">
<tr>
<td>To:</td>
<th><input type="text" class="form-control" name="toSMS" id="toSMS" placeholder=""></th>
</tr>
<tr>
<td>Body:</td>
<th><textarea class="form-control" style="width:100%;height:200px;min-height:200px;max-height:100%;" name="SMSBody" id="SMSBody"></textarea></th>
</tr>
</table>
<input type="button" value="Send" class = "btn btn-success pull-right" onclick="Send('SMS')">
</div>
</form>
</div>
</div>
</div>
</div>

<div id="SendEmail" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h4 class="modal-title">Send Email</h4>
</div>
<div class="modal-body" id="Emailmodalbody">
<form>
<div class="row">
<input type="text" class="form-control" id="EmailSubject" disabled>
<table class="table table-striped table-bordered">
<tr>
<td>To:</td>
<th><input type="text" class="form-control" name="toemail" id="toemail" placeholder=""></th>
</tr>
<tr>
<td>Body:</td>
<th><textarea class="form-control" style="width:100%;height:200px;min-height:200px;max-height:100%;" name="EmailBody" id="EmailBody"></textarea></th>
</tr>
</table>
<input type="button" value="Send" class = "btn btn-success pull-right" onclick="Send('Mail')">
</div>
</form>
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
    var row = document.getElementById("rowperpage");
    var row_count = row.options[row.selectedIndex].value;
    
    $("#numberofrows").show();
    
    
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
    xmlhttp.open("GET","tracking_ajax.php?action=DisplayVendorList&couriervendor="+couriervendor+"&row_count="+row_count ,true);
    xmlhttp.send();
    
    
}

function pagecount(i){
    
    var couriervendor="<?php echo $couriervendor; ?>";
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
            
            // $("#pagination1").append('<li class="previous disabled" id="li_prev"><a href="javascript:void(0)"  onclick="changePagination(this.id)"id="prev">&larr; Previous</a></li>');
            
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
            
            // $("#pagination1").append('<li class="next" id="li_next"><a href="javascript:void(0)"  onclick="changePagination(this.id)"id="next">Next &rarr;</a></li>');
        }
    };
    xmlhttp.open("GET","tracking_ajax.php?action=pagecount&couriervendor="+couriervendor+"&row_count="+row_count,true);
    xmlhttp.send();
    
}

function changePagination(list_id){
    
    
    
    var couriervendor="<?php echo $couriervendor; ?>";
    
    var row = document.getElementById("rowperpage");
    var row_count = row.options[row.selectedIndex].value;
    
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
    xmlhttp.open("GET","tracking_ajax.php?action=changePagination&couriervendor="+couriervendor+"&row_count="+row_count+"&page="+page,true);
    xmlhttp.send();
    
    
}




function Searchlist(){
    
    var userid='<?php echo $userid; ?>';
    
    var couriervendor= '<?php echo $couriervendor; ?>' ;
    
    var ShipperCity=document.getElementById("ShipperCity").value;
    var AWB_Number=document.getElementById("AWB_Number").value;
    var ReceiverCity=document.getElementById("ReceiverCity").value;
    var TrackingStatus=document.getElementById("TrackingStatus").value;
    
    
    if(AWB_Number=="" && ShipperCity=="" && ReceiverCity=="" && TrackingStatus=="" ){
        
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
                
                var arr = JSON.parse(xmlhttp.responseText);
                
                document.getElementById("result").innerHTML = arr['value'];
                
                document.getElementById("displaypagecount").innerHTML = "Found Records: "+arr['numrows'];
                
                $("#pagination1").empty();
                $("#numberofrows").hide();
            }
        };
        xmlhttp.open("GET","tracking_ajax.php?action=Search&couriervendor="+couriervendor+"&ShipperCity="+ShipperCity+"&AWB_Number="+AWB_Number+"&ReceiverCity="+ReceiverCity+"&TrackingStatus="+TrackingStatus,true);
        xmlhttp.send();
    }
    
}

function SendSMS(AWB_Number,couriervendor,status,lastupdatetime,trackinglink){
    
    
    
    var encodelink=encodeURIComponent(trackinglink);
    
    var SMSBody= couriervendor+' Tracking Numer: '+AWB_Number+' is '+status+' on '+lastupdatetime+'. Click below link to know more: '+encodelink;
    
    document.getElementById("SMSBody").value = SMSBody;
    
    $('#SendSMS').modal('show');

}

function SendMail(AWB_Number,couriervendor,status,place,lastupdatetime,contact,trackinglink){

    var encodelink=encodeURIComponent(trackinglink);
    
    var subject='Tracking Ref. Number-'+AWB_Number+' Status-'+status;
    
    var BodyContent = 'Hi,\r\n \r\nPlease find details for the Tracking Reference Number: '+AWB_Number+'\r\n \r\nCourier Vendor: '+couriervendor+' \r\nStatus: '+status+' \r\nPlace: '+place+' \r\nLast Update Time: '+lastupdatetime+'\r\nCourier Contact: '+contact+' \r\nView Tracking Details: '+encodelink+' \r\n \r\nFor any questions, please contact the courier directly. \r\n \r\nThank you very much!';
    
    document.getElementById("EmailBody").value = BodyContent;
    document.getElementById("EmailSubject").value = subject;
    
    

    
    $('#SendEmail').modal('show');
    
}


function Send(x){
    
     var userid='<?php echo $userid; ?>';
    
    
    //////////////////////////////////////////
    ////////////////Mail////////////////////////
    ///////////////////////////////////////////
    
    if(x=="Mail"){
        
        
        var to=document.getElementById("toemail").value;
        var subject=document.getElementById("EmailSubject").value;
        var msg=document.getElementById("EmailBody").value.replace(/\n/g, "<br />");
        
        $('#SendEmail').modal('hide');
        $('#toemail').val('');
        $('#EmailBody').val('');
        
        
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                
                 WebNotification("Email Notification",xmlhttp.responseText);
                
            }
        };
        xmlhttp.open("GET","PHPMailer/examples/gmail.php?to="+to+"&subject="+subject+"&msg="+msg,true);
        xmlhttp.send();
    }
    
    
    ////////////////////////////////////////////////
    /////////////// SMS ///////////////////////////
    ///////////////////////////////////////////////
    
    else if (x=="SMS"){
        
        var to=document.getElementById("toSMS").value;
        var msg=document.getElementById("SMSBody").value;
        
        
        $('#SendSMS').modal('hide');
        $('#toSMS').val('');
        $('#SMSBody').val('');
        

        
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                
               WebNotification("SMS Notification",xmlhttp.responseText);
                
            }
        };
        xmlhttp.open("GET","SMS.php?to="+to+"&userid="+userid+"&msg="+msg,true);
        xmlhttp.send();
    }
        
        
}


function WebNotification(Title,Body){
    
    var notification = window.Notification || window.mozNotification || window.webkitNotification;
    
    // The user needs to allow this
    if ('undefined' === typeof notification)
        alert('Web notification not supported');
    else
        notification.requestPermission(function(permission){});
    
    
    if ('undefined' === typeof notification)
        return false;       //Not supported....
    var noty = new notification(
                                Title, {
                                body:Body,
                                lang: 'EN', //lang used within the notification.
                                tag: 'notificationPopup', //An element ID to get/set the content
                                icon: '' //The URL of an image to be used as an icon
                                }
                                );
    noty.onclick = function () {
        console.log('notification.Click');
    };
    noty.onerror = function () {
        console.log('notification.Error');
    };
    noty.onshow = function () {
        console.log('notification.Show');
    };
    noty.onclose = function () {
        console.log('notification.Close');
    };
    return true;
    
 
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
