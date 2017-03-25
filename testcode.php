
<?php
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    
    
    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $stmt1 = $conn->prepare("SELECT Tracking.AWB_Number as AWB_Number,Tracking.CourierVendor as CourierVendor, Tracking.UID as UID FROM `Tracking` left join TrackingStatusMapping on Tracking.Code=TrackingStatusMapping.Code WHERE TrackingStatusMapping.`TrackingStatus`!='Delivered' and Tracking.Code='PSD'");
        $stmt1->execute();
        $i=0;
        
        
        foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
            
            //print_r($row);
            
            
            $UID=$row["UID"];
            $AWBNo=$row["AWB_Number"];
            $couriervendor=$row["CourierVendor"];
            
            //echo $AWBNo."<br>";
            
            
            
            if($row["CourierVendor"]=="BlueDart"){
                include("Tracking/BlueDart/BlueDartTracking.php");
                
            }
            if($row["CourierVendor"]=="FedEx"){
                
                include("Tracking/FedEx/TrackWebServiceClient.php");
            }
        }
        
        
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    
    
    ////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////BlueDart API Class ///////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////
    
    
    class DebugSoapClient extends SoapClient {
        public $sendRequest = true;
        public $printRequest = true;
        public $formatXML = true;
        
        public function __doRequest($request, $location, $action, $version, $one_way=0) {
            if ( $this->printRequest ) {
                if ( !$this->formatXML ) {
                    $out = $request;
                }
                else {
                    $doc = new DOMDocument;
                    $doc->preserveWhiteSpace = false;
                    $doc->loadxml($request);
                    $doc->formatOutput = true;
                    $out = $doc->savexml();
                }
                echo $out;
            }
            
            if ( $this->sendRequest ) {
                return parent::__doRequest($request, $location, $action, $version, $one_way);
            }
            else {
                return '';
            }
        }
    }
    
    ?>




<!DOCTYPE html>
<html lang="en">
<head>
<title>rShipper Service</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

<style>
#load{
width:100%;
height:100%;
position:fixed;
z-index:9999;
background:url("https://www.creditmutuel.fr/cmne/fr/banques/webservices/nswr/images/loading.gif") no-repeat center center rgba(0,0,0,0.25)
}

</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="js/validation.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(list);
//google.charts.setOnLoadCallback(drawChart1);

function list(){
    
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
            
            var temparr = [];
            
            var str=['Courier Vendor', 'Count'];
            
            temparr[0] = str;
            
            var j=1;
            
            var sum=0;
            
            for(i = 0; i < arr.length; i++) {
                
                if(arr[i].Type=="CourierBreakup"){
                    
                    
                    var markup = '<tr onclick="Attra(\''+arr[i].Type+'\',\''+arr[i].CourierVendor+'\')"><td>'+arr[i].CourierVendor+'</td><td>'+arr[i].AWB_Count+'</td></tr>';
                    $('#CourierBreakup > tbody').append(markup);
                    
                    temparr[j]=[arr[i].CourierVendor,Number(arr[i].AWB_Count)];
                    j++;
                    
                    sum=sum+Number(arr[i].AWB_Count);
                    
                }
                
                if(arr[i].Type=="Status"){
                    
                    var markup = '<tr onclick="Attra(\''+arr[i].Type+'\',\''+arr[i].TrackingStatus+'\')" ><td>'+arr[i].TrackingStatus+'</td><td>'+arr[i].AWB_Count+'</td><td>'+((Number(arr[i].AWB_Count)/sum)*100).toFixed(2)+'%</td></tr>';
                    $('#TrackingStatus > tbody').append(markup);
                    
                    
                    if(arr[i].TrackingStatus=="Delivered"){
                        
                        var Delivered=Number(arr[i].AWB_Count);
                    }
                    else if(arr[i].TrackingStatus=="Out For Delivery"){
                        
                        var OutForDelivery=Number(arr[i].AWB_Count);
                        
                    }
                    else if(arr[i].TrackingStatus=="In Transit"){
                        
                        var InTransit =Number(arr[i].AWB_Count);
                        
                    }
                    
                    else if(arr[i].TrackingStatus=="Picked Up"){
                        
                        var PickedUp=Number(arr[i].AWB_Count);
                    }
                    else if(arr[i].TrackingStatus=="Exception"){
                        
                        var Exception=Number(arr[i].AWB_Count);
                    }
                    else if(arr[i].TrackingStatus=="Pickup Scheduled"){
                        
                        var Scheduled=Number(arr[i].AWB_Count);
                    }
                    else{
                        
                        alert("Add new status and color code on this page:"+arr[i].TrackingStatus);
                    }
                    
                }
                
            }
            
            
            
            //var data = google.visualization.arrayToDataTable([['Courier Vendor', 'Airway Bill'],['FedEx',59],['BlueDart',17]]);
            
            var data = google.visualization.arrayToDataTable(temparr);
            
            
            var options = { title: '', pieHole: 0.3,};
            
            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            
            chart.draw(data, options);
            
            
            var data1 = google.visualization.arrayToDataTable([["Status", "Count", { role: "style" } ],["Delivered",Delivered, "green"],["Out for Delivery", OutForDelivery, "orange"],["In Transit", InTransit, "yellow"],["Picked Up", PickedUp, "silver"],["Pickup Scheduled",Scheduled,"gray"],["Exception", Exception, "red"]]);
            
            var view = new google.visualization.DataView(data1);
            view.setColumns([0, 1,
                            { calc: "stringify",
                            sourceColumn: 1,
                            type: "string",
                            role: "annotation" },
                            2]);
            
            var options = {
            title: "",
            width: 600,
            height: 400,
            bar: {groupWidth: "65%"},
            legend: { position: "none" },
            };
            var chart = new google.visualization.BarChart(document.getElementById("barchart_values"));
            chart.draw(view, options);
            
        }
    };
    xmlhttp.open("GET","tracking_ajax.php?action=trackingstatus",true);
    xmlhttp.send();
    
}
</script>

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
<li><a href="Generate_AirwayBill.php">Generate AirwayBill</a></li>
<li ><a href="schedulepickup.html">Book Pickup</a></li>
<li class="nav-item nav-link active" ><a href="trackcourier.php">Track Courier</a></li>
</ul>
</div>
</nav>

<div id="load"></div>

<div class="container" id="container">
<h1 align="center">Track Courier </h1>

<div class="alerts" id="alert_box" style="padding-top: 0em; padding-bottom: 0em;display: none;">
<div class="alert alert-danger" role="alert">
<p id="alert_div" align="center"></p>
<ul id="list">
</ul>
</div>
</div>



<div class="row">
<!-- right column -->
<div class="col-sm-7">
<h3>Courier Vendor Split</h3>
<div class="table-responsive table-bordered" style="align:center;" >
<table class="table table-hover" id="CourierBreakup">
<thead style="background-color: gray;  color:white;">
<tr>
<th>Courier Name</th>
<th>Count</th>
</tr>
</thead>
<tbody>

</tbody>
</table>
</div>


</div>
<div class="col-sm-5">
<div id="piechart_3d" style="text-align: center;"></div>
</div>
</div>

<div class="row">
<!-- right column -->
<div class="col-sm-7">
<h3>Status Split</h3>
<div class="table-responsive table-bordered" style="align:center;" >
<table class="table table-hover" id="TrackingStatus">
<thead style="background-color: purple; color:white;">
<tr>
<th>Status</th>
<th>Count</th>
<th>%</th>
</tr>
</thead>
<tbody>
</tbody>
</table>
</div>
</div>

<div class="col-sm-5">
<div id="barchart_values" style="text-align: center;"></div>
</div>
</div>


<script>

function Attra(r,y){
    
    if(r=="CourierBreakup"){
        
        window.location.href = "TrackingVendorList.php?userid=Santy&vendor="+y;
    }
    
    else if(r=="Status"){
        
        window.location.href = "TrackingStatusList.php?userid=Santy&status="+y;
    }
    
}




document.onreadystatechange = function () {
    var state = document.readyState
    if (state == 'interactive') {
        document.getElementById('container').style.visibility="hidden";
    } else if (state == 'complete') {
        setTimeout(function(){
                   document.getElementById('interactive');
                   document.getElementById('load').style.visibility="hidden";
                   document.getElementById('container').style.visibility="visible";
                   },1000);
    }
}



</script>


</body>
</html>
