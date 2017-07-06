<?php
    
    session_start();
    require_once 'class.user.php';
    $user = new USER();
    
    $roleID=$_SESSION['userRoleID'];
    
    if($user->is_logged_in()!=true)
    {
        $user->redirect('login.php');
    }
    if($roleID!=1){
        
        $user->redirect('login.php');
    }
    
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
                            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
                                
                                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
                                <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
                                
                                </head>
    
    <body onload="list()">
        
    <?php echo  $user->Navigation(); ?>



        
        <div class="container">
            <h1 align="center">Courier Vendor Details </h1>
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
                                <div class="col col-xs-6">
                                    <h3 class="panel-title">Details</h3>
                                </div>
                                <div class="col col-xs-6 text-right">
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addvendor" >Add</button>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body" id="result" style="overflow: scroll;">
                            
                            
                        </div>
                        <div class="panel-footer">
                            <div class="row">
                                <div class="col col-xs-4">Page 1 of 1
                                </div>
                                <div class="col col-xs-8">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div id="addvendor" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Courier Vendor</h4>
                            </div>
                            <div class="modal-body1">
                                <div class="col-sm-12">
                                    <div class="box box-primary">
                                        <div class="box-body" id="list">
                                            <table class="table table-striped table-bordered" id="addvendortab">
                                                <tr>
                                                    <td class="col-sm-4">Name:</td>
                                                    <th><select class="form-control selcls" name="vendor_name" id="vendor_name" onclick="otherCourier()">
                                                        <option value="" selected="selected">Select Courier Vendor</option>
                                                        <option value="Other">Other</option>
                                                        <option value="DTDC">DTDC</option>
                                                        <option value="BlueDart">BlueDart</option>
                                                        <option value="FedEx">Fedex</option>
                                                    </select>
                                                    <input type="text" class="form-control" name="other" id="other" placeholder=""  style="display:none">
                                                        </th>
                                                </tr>
                                                <tr>
                                                    <td>Customer-Code/Login ID</td>
                                                    <th><input type="text" class="form-control" name="login_id" id="login_id" placeholder=""></th>
                                                </tr>
                                                <tr>
                                                    <td>Account Number/ Meter</td>
                                                    <th><input type="text" class="form-control" name="account_number" id="account_number" placeholder=""></th>
                                                </tr>
                                                <tr>
                                                    <td>Key</td>
                                                    <th><input type="text" class="form-control" name="account_key" id="account_key" placeholder=""></th>
                                                </tr>
                                                
                                                <tr>
                                                    <td>Password</td>
                                                    <th><input type="text" class="form-control" name="password" placeholder="" id="password" ></th>
                                                </tr>
                                                
                                            </table>
                                            <input type="hidden" class="form-control" name="serial_id" placeholder="" id="serial_id" ></th>
                                                </div>
                                        <!-- /.box-footer -->
                                    </div>
                                    <!-- /.box -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary"  onclick="insert()"id="save">Save</button>
                                <button type="button" class="btn btn-primary"  onclick="updatevendor()" id="update" style="display:none">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                
                <!-- /.form Submit -->
                
                
            </form>
            
        </div>
        
        
        
        <script type="text/javascript">
            
            
            $(".modal").on("hidden.bs.modal", function(){
                           $('#addvendortab').find('input,textarea','select').val('');
                           document.getElementById("save").style.display = 'inline';
                           document.getElementById("update").style.display = 'none';
                           
                           });
                           
                           
            </script>
        
        <script>
            
            
            function editvendor(vendor_name,login_id,account_number,account_key,password,id){
                
                document.getElementById("vendor_name").value=vendor_name;
                document.getElementById("login_id").value=login_id;
                document.getElementById("account_number").value=account_number;
                document.getElementById("account_key").value=account_key;
                document.getElementById("password").value=password;
                document.getElementById("serial_id").value=id;
                document.getElementById("save").style.display = 'none';
                document.getElementById("update").style.display = 'inline';
                
                $('#addvendor').modal('show');
                
            }
        
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
                    
                    document.getElementById("result").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET","vendoraction.php?action=select",true);
            xmlhttp.send();
        }
        
        
        
        function insert(){
            
            var vendor_name= document.getElementById("vendor_name").value;
            var other_vendor= document.getElementById("other").value;
            var login_id = document.getElementById("login_id").value;
            var account_number= document.getElementById("account_number").value;
            var account_key =document.getElementById("account_key").value;
            var password =document.getElementById("password").value;
            
            $('#addvendor').modal('hide');
            
            
            if(vendor_name==""  || account_number=="" ){
                alert("Plz. fill minimum Vendor Name or Account Name");
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
                        document.getElementById("result").innerHTML = xmlhttp.responseText;
                        
                    }
                };
                xmlhttp.open("GET","vendoraction.php?action=insert&vendor_name="+vendor_name+"&login_id="+login_id+"&account_number="+account_number+"&account_key="+account_key+"&password="+password+"&other="+other_vendor,true);
                xmlhttp.send();
                
                
            }
            
            
        }
        
        function deletevendor(vendor_name,login_id,account_number,account_key,password,id){
            
            
            var x;
            if (confirm("Are you sure you want to delete this record?") == true) {
                x =1;
            } else {
                x = 0;
            }
            
            if(x==1){
                
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
                xmlhttp.open("GET","vendoraction.php?action=delete&vendor_name="+vendor_name+"&login_id="+login_id+"&account_number="+account_number+"&account_key="+account_key+"&password="+password+"&id="+id,true);
                xmlhttp.send();
                
            }
            
            
            
        }
        
        
        function updatevendor(){
            var vendor_name= document.getElementById("vendor_name").value;
            var other_vendor= document.getElementById("other").value;
            var login_id = document.getElementById("login_id").value;
            var account_number= document.getElementById("account_number").value;
            var account_key =document.getElementById("account_key").value;
            var password =document.getElementById("password").value;
            var id=document.getElementById("serial_id").value;
            
            $('#addvendor').modal('hide');
            
            if(vendor_name==""  || account_number==""){
                
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
                        document.getElementById("result").innerHTML = xmlhttp.responseText;
                    }
                };
                xmlhttp.open("GET","vendoraction.php?action=update&vendor_name="+vendor_name+"&login_id="+login_id+"&account_number="+account_number+"&account_key="+account_key+"&password="+password+"&id="+id+"&other="+other_vendor,true);
                xmlhttp.send();
                
                
            }
        }
        
        function otherCourier(){
            
            var vendor = document.getElementById("vendor_name");
            var vendor_name = vendor.options[vendor.selectedIndex].value;
            
            var otherCourier=document.getElementById("other").value;
            
            if(vendor_name=="Other"){
                
                //vendor.options[vendor.selectedIndex].value=otherCourier;
                //alert(vendor.options[vendor.selectedIndex].value);
                document.getElementById("other").value="";
                document.getElementById("other").style.display = 'inline';
                
            }
            else if(vendor_name!="Other"){
                document.getElementById("other").style.display = 'none';
                document.getElementById("other").value=vendor.options[vendor.selectedIndex].value;
            }
            
            
        }
        </script>
        
        
    </body>
</html>