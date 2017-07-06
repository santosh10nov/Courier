
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
                                    
                                    var today= new Date();
                                    var dd = ("0" + today.getDate()).slice(-2);
                                    var mm = ("0" + (today.getMonth() + 1)).slice(-2);
                                    var yyyy = today.getFullYear();
                                    var todayDate=dd+'-'+mm+'-'+yyyy;
                                    
                                    var k=1;
                                    
                                    
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
                                            else if(arr[i].TrackingStatus=="Pickup Canceled"){
                                                
                                                var PickupCanceled=Number(arr[i].AWB_Count);
                                            }
                                            else{
                                                
                                                var Others=Number(arr[i].AWB_Count);
                                                //alert("Add new status and color code on this page:"+arr[i].TrackingStatus);
                                            }
                                            
                                        }
                                        
                                        if(arr[i].Type=="EstimateTime"){
                                            
                                            /////// 1. Within Timelimit ///////
                                            /////// 2. On Same Day ////////////////
                                            /////// 3. 1-2 Days ///////////////
                                            /////// 4. 3-5 Days ///////////////
                                            /////// 5. 5-10 Days //////////////
                                            /////// 6. 10 Days above //////////
                                            
                                            
                                            var markup = '<tr><td>'+arr[i].CourierVendor+'</td><td style="background-color: green; color:white;" onclick="EstimatePage(1,\''+arr[i].On_Time+'\',\''+arr[i].CourierVendor+'\')" >'+arr[i].On_Time+'</td><td style="background-color: #ffcc66; color:white;" onclick="EstimatePage(2,\''+arr[i].Same_Day_Delivery+'\',\''+arr[i].CourierVendor+'\')" >'+arr[i].Same_Day_Delivery+'</td><td style="background-color: #ff9980; color:white;" onclick="EstimatePage(3,\''+arr[i].Two_Day_Delay+'\',\''+arr[i].CourierVendor+'\')" >'+arr[i].Two_Day_Delay+'</td><td style="background-color: #ff704d; color:white;" onclick="EstimatePage(4,\''+arr[i].Five_Day_Delay+'\',\''+arr[i].CourierVendor+'\')" >'+arr[i].Five_Day_Delay+'</td><td style="background-color: #ff3333; color:white;" onclick="EstimatePage(5,\''+arr[i].Ten_Day_Delay+'\',\''+arr[i].CourierVendor+'\')" >'+arr[i].Ten_Day_Delay+'</td><td style="background-color:#ff0000; color:white;" onclick="EstimatePage(6,\''+arr[i].More_than_10_Day_Delay+'\',\''+arr[i].CourierVendor+'\')" >'+arr[i].More_than_10_Day_Delay+'</td></tr>';
                                            $('#EstimateTime > tbody').append(markup);
                                            
                                            /*var parts = arr[i].EstimateDeliveryDate.split("-");
                                             var EstimateDeliveryDate1=new Date(parts[2], parts[1] - 1, parts[0]);
                                             
                                             var parts1=todayDate.split("-");
                                             var todayDate1= new Date(parts1[2], parts1[1] - 1, parts1[0]);
                                             
                                             var timeDiff = Math.abs(todayDate1.getTime() - EstimateDeliveryDate1.getTime());
                                             var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
                                             
                                             if(diffDays<0){
                                             
                                             
                                             }*/
                                            
                                            
                                        }
                                        
                                    }
                                    
                                    
                                    var data = google.visualization.arrayToDataTable(temparr);
                                    
                                    
                                    var options = { title: '', pieHole: 0.3,};
                                    
                                    var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
                                    
                                    chart.draw(data, options);
                                    
                                    
                                    var data1 = google.visualization.arrayToDataTable([["Status", "Count", { role: "style" } ],["Delivered",Delivered, "green"],["Out for Delivery", OutForDelivery, "orange"],["In Transit", InTransit, "yellow"],["Picked Up", PickedUp, "silver"],["Pickup Scheduled",Scheduled,"gray"],["Pickup Canceled",PickupCanceled,"blue" ],["Exception", Exception, "red"],["Others", Others, "black"]]);
                                    
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
        
        
        <?php echo  $user->Navigation(); ?>
        
        <div id="load"></div>
        
        <div class="container" id="container">
            <h1 align="center">Track Courier </h1>
            
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
            
            <div class="row">
                <!-- right column -->
                <div class="col-sm-12">
                    <h3>Estimate Delivery Time Analysis</h3>
                    <div class="table-responsive" style="align:center;" >
                        <table class="table table-bordered" id="EstimateTime">
                            <thead style="background-color: gray; color:white;">
                                <tr>
                                    <th>Courier Vendor</th>
                                    <th>On Time</th>
                                    <th> Today Delivery</th>
                                    <th> 1-2 Days Delivery Delay</th>
                                    <th>3-5 Days Delivery Delay</th>
                                    <th>5-10 Days Delivery Delay</th>
                                    <th>More than 10 Days Delivery Delay</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <p>*Delivered Packetes are not included<p>
                    </div>
                </div>
                
            </div>
            
            
            
            <script>
                
                function Attra(r,y){
                    
                    if(r=="CourierBreakup"){
                        
                        window.location.href = "TrackingVendorList.php?vendor="+y;
                    }
                    
                    else if(r=="Status"){
                        
                        window.location.href = "TrackingStatusList.php?status="+y;
                    }
                    
                }
            
            function EstimatePage(x,y,z){
                
                if(y!=0){
                    
                    window.location.href = "TrackingEstimateTimeList.php?id="+x+"&vendor="+z;
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
            
        </div>
    </body>
</html>
