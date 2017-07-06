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
        <title>Multiplex AirwayBill |rShipper</title>
        <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
                
                <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
                    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
                        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
                        <script type="text/javascript" src="jquery/jquery.blockUI.js"></script>
                        <script type="text/javascript" src="jquery/jquery.cookie.js"></script>


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
    
    <body onload="CourierVendorList()">
        
        <?php echo  $user->Navigation(); ?>
        
        
        <div class="container" id="container">
            
            <h1 align="center">Multiplex Generate AirwayBill- Upload Excel</h1>
            <!-- form start -->
            <form class="form-horizontal"  method="post" id="GenerateAWBForm" action="PHPExcel/uploader/AWBExcel.php" enctype="multipart/form-data">
                <div class="col-md-12 text-right">
                    <a href="download.php?action=ExcelTemplate" download><button type="button" class="btn btn-sm btn-success text-right"> Downlaod Excel Template</button></a></br></br>
                </div>
                <div class="col-md-12 ">
                    <div class="panel panel-default panel-table">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead style="background-color: gray; color:white; ">
                                            <tr>
                                                <td>Courier Vendor</td>
                                                <td>Service</td>
                                                <td>Account Number</td>
                                                <td>Shipment Date</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="filters">
                                                <td>
                                                    <div>
                                                        <select class="form-control selcls" name="couriervendor" id="couriervendor" onclick="services_list()">
                                                            </div>
                                                </td>
                                                <td>
                                                    <select class="form-control selcls" name="services" id="services" style="display:inline">
                                                        <option value="" selected="selected">Select Courier Service</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control selcls" name="accountnumber" id="accountnumber" style="display:inline">
                                                        <option value="" selected="selected">Select Account Number</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group date form_date col-md-12" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                                        <input class="form-control" size="16" type="text" value="" name="dispatchDate"  id="dispatchDate" readonly>
                                                            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                            </div>
                                                    <input type="hidden" id="dtp_input2" name="date" value="" />
                                                </td>
                                                
                                            </tr>
                                            
                                            <tr>
                                                <td>
                                                    <select class="form-control selcls" name="purpose" id="purpose" >
                                                        <option value=" " selected="selected">Purpose of Shipment</option>
                                                        <option value="GIFT">GIFT</option>
                                                        <option value="NOT_SOLD">NOT SOLD</option>
                                                        <option value="PERSONAL_EFFECTS">PERSONAL EFFECTS</option>
                                                        <option value="REPAIR_AND_RETURN">REPAIR AND RETURN</option>
                                                        <option value="SAMPLE">SAMPLE</option>
                                                        <option value="SOLD">SOLD</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="radio" id="Documents" name="shipmentcontent" value="Documents" checked="checked"> Documents &nbsp;&nbsp;
                                                        <input type="radio" id="Commodities" name="shipmentcontent" value="Commodities"> Non Documents
                                                            </td>
                                                
                                                <td>
                                                    <div class="input-group">
                                                        <label class="input-group-btn">
                                                            <span class="btn btn-primary">
                                                                Browse&hellip; <input type="file" style="display: none;" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="file" id="file">
                                                                    </span>
                                                        </label>
                                                        <input type="text" class="form-control" readonly>
                                                            </div>
                                                    
                                                </td>
                                                <td>
                                                    
                                                    <button type="button" class="btn btn-success"  onclick="ProcessAWB()" id="GenAWB">Generate AWB</button>
                                                </td>
                                            </tr>
                                            
                                            
                                        </tbody>
                                    </table>
                                </div>
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
                                                                                                             
                                                                                                             $(function() {
                                                                                                               
                                                                                                               // We can attach the `fileselect` event to all file inputs on the page
                                                                                                               $(document).on('change', ':file', function() {
                                                                                                                              var input = $(this),
                                                                                                                              numFiles = input.get(0).files ? input.get(0).files.length : 1,
                                                                                                                              label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                                                                                                                                                                              input.trigger('fileselect', [numFiles, label]);
                                                                                                                                                                              });
                                                                                                                              
                                                                                                                              // We can watch for our custom `fileselect` event like this
                                                                                                                              $(document).ready( function() {
                                                                                                                                                $(':file').on('fileselect', function(event, numFiles, label) {
                                                                                                                                                              
                                                                                                                                                              var input = $(this).parents('.input-group').find(':text'),
                                                                                                                                                              log = numFiles > 1 ? numFiles + ' files selected' : label;
                                                                                                                                                              
                                                                                                                                                              if( input.length ) {
                                                                                                                                                              input.val(log);
                                                                                                                                                              } else {
                                                                                                                                                              if( log ) alert(log);
                                                                                                                                                              }
                                                                                                                                                              
                                                                                                                                                              });
                                                                                                                                                });
                                                                                                                              
                                                                                                                              });
                                                                                                               
                                                                                                               </script>
        
        <script>
            
            
            function CourierVendorList(){
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        
                        $("#couriervendor").empty();
                        var arr = JSON.parse(xmlhttp.responseText);
                        var i;
                        
                        document.getElementById("couriervendor").options[0]=new Option("Select Courier Vendor"," ");
                        for(i = 0; i < arr.length; i++) {
                            document.getElementById("couriervendor").options[i+1]=new Option(arr[i].value,arr[i].venndor_name);
                        }
                        
                        
                    }
                };
                xmlhttp.open("GET","vendoraction.php?action=CourierVendorList",true);
                xmlhttp.send();
                
                
            }
        
        
        
        function services_list(){
            
            var vendor = document.getElementById("couriervendor");
            var vendor_name = vendor.options[vendor.selectedIndex].value;
            
            //var from_pin= document.getElementById("from_pin").value;
            //var to_pin= document.getElementById("to_pin").value;
            
            
            if(vendor_name==""){
                $("#services").empty();
                document.getElementById("services").options[0]=new Option("Select Courier Service"," ");
                $("#accountnumber").empty();
                document.getElementById("accountnumber").options[0]=new Option("Select Account Number"," ");
            }
            
            else {
                
                document.getElementById("services").style.display = 'inline';
                document.getElementById("accountnumber").style.display = 'inline';
                
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        
                        courierservice(xmlhttp.responseText,vendor_name);
                    }
                };
                xmlhttp.open("GET","Service_ajax.php?action=productname&couriervendor="+vendor_name,true);
                xmlhttp.send();
                
                
            }
        }
        
        
        function courierservice(response,vendor) {
            $("#services").empty();
            var arr = JSON.parse(response);
            var i;
            document.getElementById("services").options[0]=new Option("Select Courier Service"," ");
            for(i = 0; i < arr.length; i++) {
                document.getElementById("services").options[i+1]=new Option(arr[i].service,arr[i].servicevalue);
                
            }
            
            accountnumber(vendor);
            
        }
        
        function accountnumber(vendor_name){
            
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    
                    accountnumberlist(xmlhttp.responseText);
                }
            };
            xmlhttp.open("GET","vendoraction.php?action=vendornameList&vendor_name="+vendor_name,true);
            xmlhttp.send();
            
        }
        
        function accountnumberlist(response){
            
            $("#accountnumber").empty();
            var arr = JSON.parse(response);
            var i;
            
            document.getElementById("accountnumber").options[0]=new Option("Select Account Number"," ");
            for(i = 0; i < arr.length; i++) {
                document.getElementById("accountnumber").options[i+1]=new Option(arr[i].accountvalue,arr[i].account_number);
            }
            
            
        }
        
        function ProcessAWB() {
            
            var flag=1;
            
            var vendor = document.getElementById("couriervendor");
            var vendor_name = vendor.options[vendor.selectedIndex].value;
            
            var CourierService = document.getElementById("services");
            var CourierServiceName = CourierService.options[CourierService.selectedIndex].value;
            
            var accountnumber = document.getElementById("accountnumber");
            var accountnumbervalue = accountnumber.options[accountnumber.selectedIndex].value;
            
            var ShipmentDate= document.getElementById("dtp_input2").value;
            
            var p = document.getElementById("purpose");
            var purposevalue = p.options[p.selectedIndex].value;
            var Content=document.querySelector('input[name = "shipmentcontent"]:checked').value;
            
            var attachment=document.getElementById('file').value;
            
            if(vendor_name==" "){
                
                alert("Please Select Courier Vendor");
                flag=0;
                return false;
            }
            else if(CourierServiceName==" "){
                
                alert("Please Select Courier Service");
                flag=0;
                return false;
            }
            else if (accountnumbervalue==" "){
                
                alert("Please select account number");
                flag=0;
                return false;
            }
            else if (ShipmentDate==""){
                
                alert("Please Enter ShipmentDate");
                flag=0;
                return;
            }
            else if(purposevalue==" "){
                alert("Please select purpose");
                flag=0;
                return;
                
            }
            else if(attachment=='') {
                alert('No File Attached');
                flag=0;
                return;
            }
            
            
            
            if(flag!=0){
             
                document.getElementById("GenerateAWBForm").submit();
                
                $(document).ready(function(){
                $.blockUI(
                        {
                        message: '<h1><img src="http://s13.postimg.org/80vkv0coz/image.gif" /> Please Wait Custom Message...</h1>'
                        });
                fileDownloadCheckTimer = window.setInterval(function () {
                                                            var cookieValue = $.cookie('DownloadFlag');
                                                            if (cookieValue == 1)
                                                            finishDownload();
                                                            }, 1000);
                                  
                
                                  });
                                                                                                               
                return true;
            }
        }
        
                                                                                                               
        
        function finishDownload() {
                    window.clearInterval(fileDownloadCheckTimer);
                    $.removeCookie('DownloadFlag'); //clears this cookie value
                    $.unblockUI();
                    location.reload();
                }
        
        
        
        
            </script>
        
    </body>
</html>


