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
        <title>Contact List | rShipper</title>
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
            <h1 align="center">Contact List </h1>
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
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addcontact" >Add</button>
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
                
                
                <div id="addcontact" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Add Contact Details</h4>
                            </div>
                            <div class="modal-body1">
                                <div class="col-sm-12">
                                    <div class="box box-primary">
                                        <div class="box-body" id="list">
                                            <table class="table table-striped table-bordered" id="addcontacttab">
                                                
                                                <tr>
                                                    <td>Vendor ID:</td>
                                                    <th><input type="text" class="form-control" name="vendorid" id="vendorid" placeholder=""></th>
                                                </tr>
                                                <tr>
                                                    <td class="col-sm-4">Pickup Pincode:</td>
                                                    <th><input type="text" class="form-control" name="pin" id="pin" placeholder="" onchange="senderlocation(this.value)" maxlength="6" size="6" required></th>
                                                </tr>
                                                <tr>
                                                    <td>Name:</td>
                                                    <th><input type="text" class="form-control" name="Cname" id="Cname" placeholder="" required></th>
                                                </tr>
                                                <tr>
                                                    <td>Company Name:</td>
                                                    <th><input type="text" class="form-control" name="com_name" id="com_name" placeholder="" required></th>
                                                </tr>
                                                <tr>
                                                    <td>Address:</td>
                                                    <th><textarea class="form-control custom-control" rows="3" style="resize:none" name="address" id="address" maxlength="70" size="70" required></textarea></th>
                                                </tr>
                                                
                                                <tr>
                                                    <td>City:</td>
                                                    <th><input type="text" class="form-control" name="city" placeholder="" id="city" required></th>
                                                </tr>
                                                
                                                <tr>
                                                    <td>State:</td>
                                                    <th><input type="text" class="form-control" name="state" placeholder="" id="state" required></th>
                                                </tr>
                                                
                                                <tr>
                                                    <td>Phone Number:</td>
                                                    <th><input type="text" class="form-control" name="phone" placeholder="" id="phone" maxlength="10" size="10" required></th>
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
                           $('#addcontacttab').find('input,textarea','select').val('');
                           document.getElementById("save").style.display = 'inline';
                           document.getElementById("update").style.display = 'none';
                           
                           });
                           
                           
            </script>
        
        <script>
            
            

        
        function list(){
            
            var id="<?php echo $userid; ?>";
            
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
            xmlhttp.open("GET","contactajax.php?action=view&id="+id+"&Comp=1",true);
            xmlhttp.send();
        }
        
        
        function DeleteContact(id){
            
            var id="<?php echo $userid; ?>";
            
            if(confirm("Are you sure you want to delete ?") == true){
                
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        
                        var result=xmlhttp.responseText;
                        
                        if(result=="Success"){
                         
                            list();
                        }
                    }
                };
                xmlhttp.open("GET","contactajax.php?action=delete&id="+id,true);
                xmlhttp.send();
                
            }
            
            return;
            
        }
        
        
        function insert(){
            
            var id="<?php echo $userid; ?>";
            
            var vendorid= document.getElementById("vendorid").value;
            var pin= document.getElementById("pin").value;
            var Cname = document.getElementById("Cname").value;
            var com_name= document.getElementById("com_name").value;
            var address =document.getElementById("address").value;
            var city =document.getElementById("city").value;
            var state =document.getElementById("state").value;
            var phone =document.getElementById("phone").value;
            $('#addcontact').modal('hide');
            
            if(Cname==""||phone==""||com_name==""){
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
                        
                        var result=xmlhttp.responseText;
                        
                        if(result=="Success"){
                            
                            list();
                        }
                        
                    }
                };
                xmlhttp.open("GET","contactajax.php?action=insert&id="+id+"&vendorid="+vendorid+"&pin="+pin+"&name="+Cname+"&companyname="+com_name+"&address="+address+"&city="+city+"&state="+state+"&phone="+phone,true);
                xmlhttp.send();
                
                
            }
            
            
        }
        
        
        function EditContact(id,name,companyname,address,city,state,pincode,phone,vendorid){
                
                document.getElementById("Cname").value=name;
                document.getElementById("com_name").value=companyname;
                document.getElementById("address").value=address;
                document.getElementById("city").value=city;
                document.getElementById("state").value=state;
                document.getElementById("pin").value=vendorid;
                document.getElementById("phone").value=phone;
                document.getElementById("vendorid").value=vendorid;
                
                
                document.getElementById("serial_id").value=id;
                
                //Hide Save button and Show Update button///////
                document.getElementById("save").style.display = 'none';
                document.getElementById("update").style.display = 'inline';
                
                $('#addcontact').modal('show');
                
            }
        
        function updatevendor(){
           
            var id="<?php echo $userid; ?>";
            
            var vendorid= document.getElementById("vendorid").value;
            var pin= document.getElementById("pin").value;
            var Cname = document.getElementById("Cname").value;
            var com_name= document.getElementById("com_name").value;
            var address =document.getElementById("address").value;
            var city =document.getElementById("city").value;
            var state =document.getElementById("state").value;
            var phone =document.getElementById("phone").value;
            
            
            var serial_id=document.getElementById("serial_id").value;
         
         $('#addcontact').modal('hide');
         
            if(Cname==""||phone==""||com_name==""){
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
                        
                        var result=xmlhttp.responseText;
                        
                        if(result=="Success"){
                            
                            list();
                        }
                        
                    }
                };
                xmlhttp.open("GET","contactajax.php?action=update&id="+id+"&vendorid="+vendorid+"&pin="+pin+"&name="+Cname+"&companyname="+com_name+"&address="+address+"&city="+city+"&state="+state+"&phone="+phone+"&serial_id="+serial_id,true);
                xmlhttp.send();
                
                
            }

         
            
            
         }
         
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
                            document.getElementById("city").value = res[0];
                            document.getElementById("state").value = res[1];
                        }
                    };
                    xmlhttp.open("GET","location_ajax.php?from_pin="+str+"&location=s",true);
                    xmlhttp.send();
                    
                }
            }
        </script>
        
        
    </body>
</html>
