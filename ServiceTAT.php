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
    
    <body>
        
        <?php echo  $user->Navigation(); ?>
        
        
        
        <div class="container" id="container">
            <h1 align="center"> Service Availabilty & TAT</h1>

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
                                            <th>Pickup Pincode </th>
                                            <th>Destination Pincode</th>
                                            <th><em class="fa fa-cog" align="center"></em></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="filters">
                                            <td>
                                                <input class="form-control" size="16" type="text" value="" name="from_pin"  id="from_pin" maxlength="6" size="6">
                                                    </td>
                                            <td>
                                                <input class="form-control" size="16" type="text" value="" name="to_pin"  id="to_pin" maxlength="6" size="6">
                                                    </td>
                                            <td><input type="button" value="Search" class = "btn btn-success" id="Search" onclick="Service_Availabilty()">&nbsp;<input type="reset" value="Reset" class = "btn btn-info" onclick="Reset()">
                                                </td>
                                        </tr>
                                        <tr style="background-color: #34495E; color:white;">
                                            <td>
                                                <h4 id="from_city"></h4>
                                            </td>
                                            <td>
                                                <h4 id="to_city"></h4>
                                            </td>
                                            <td>
                                                <h4 id="SearchingStatus"></h4>
                                            </td>
                                            
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>
                    <div class="panel-body" id="result" style="overflow: scroll;">
                        
                    </div>
                    
                    <div class="panel-footer">
                    </div>
                </div>
            </div>
            
            <!-- /.form Submit -->
            
            
        </form>
        </div>
        
        
        
        
        <script>
            
            function Service_Availabilty(){
                
                document.getElementById("result").innerHTML ="";
                document.getElementById("from_city").innerHTML ="";
                document.getElementById("to_city").innerHTML ="";
                var from_pin= document.getElementById("from_pin").value;
                var to_pin= document.getElementById("to_pin").value;
                
                if(from_pin==""){
                    alert("Enter Pickup Pincode");
                    return;
                }
                else if (from_pin.length!=6 || isNaN(from_pin)) {
                    if(from_pin!=""){
                        alert("Enter Correct Pickup Pincode");
                        return;
                    }
                }
                else if(to_pin==""){
                    alert("Enter Destination Pincode");
                    return;
                }
                else if (to_pin.length!=6 || isNaN(to_pin)) {
                    if(to_pin!=""){
                        alert("Enter Correct Destination  Pincode");
                        return;
                    }
                }
                else{
                    document.getElementById("SearchingStatus").innerHTML ="Searching....";
                    document.getElementById("Search").disabled = true;
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            document.getElementById("result").innerHTML = xmlhttp.responseText;
                            City(from_pin,to_pin);
                            
                        }
                    };
                    xmlhttp.open("GET", "Service_ajax.php?action=ServiceAvailability&pickup_pin="+from_pin+"&desti_pin="+to_pin, true);
                    xmlhttp.send();
                    
                }
                
                
            }
        
        function City(A,B){
            
            
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    
                    var arr = JSON.parse(xmlhttp.responseText);
                    
                    document.getElementById("from_city").innerHTML =arr[0].from;
                    document.getElementById("to_city").innerHTML =arr[0].to;
                    document.getElementById("SearchingStatus").innerHTML ="";
                    document.getElementById("Search").disabled = false;
                    
                }
            };
            xmlhttp.open("GET", "Service_ajax.php?action=CheckCity&pickup_pin="+A+"&desti_pin="+B, true);
            xmlhttp.send();
        }
        
        function Reset(){
            
            document.getElementById("result").innerHTML ="";
            document.getElementById("from_city").innerHTML ="";
            document.getElementById("to_city").innerHTML ="";
            document.getElementById("Search").disabled = false;
        }
        
        
        
        
        
            </script>
        
        
    </body>
</html>
