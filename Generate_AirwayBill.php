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
        <?php echo  $user->Navigation(); ?>
        
        
        <div class="container" id="container">
            <h1 align="center">Generate AirwayBill </h1>
            <div class="alerts" id="alert_box" style="padding-top: 0em; padding-bottom: 0em;display: none;">
                <div class="alert alert-danger" role="alert">
                    <p id="alert_div" align="center"></p>
                    <ul id="list">
                    </ul>
                </div>
            </div>
            <!-- form start -->
            <form class="form-horizontal" action="airwaybill.php" role="form" method="post" id="GenerateAWBForm">
                
                <div class="row">
                    <!-- right column -->
                    <div class="col-sm-4">
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
                                        <td>Vendor ID:</td>
                                        <th><input type="text" class="form-control" name="sender_vendorid" id="sender_vendorid" placeholder=""></th>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-4">Pickup Pincode:</td>
                                        <th><input type="text" class="form-control" name="from_pin" id="from_pin" placeholder="" onchange="senderlocation(this.value)" maxlength="6" size="6" required></th>
                                    </tr>
                                    <tr>
                                        <td>Sender Name:</td>
                                        <th><input type="text" class="form-control" name="sender_name" id="sender_name" placeholder="" required></th>
                                    </tr>
                                    <tr>
                                        <td>Company Name:</td>
                                        <th><input type="text" class="form-control" name="sender_com_name" id="sender_com_name" placeholder="" required></th>
                                    </tr>
                                    <tr>
                                        <td>Address:</td>
                                        <th><textarea class="form-control custom-control" rows="3" style="resize:none" name="sender_address" id="sender_address" maxlength="70" size="70" required></textarea></th>
                                    </tr>
                                    
                                    <tr>
                                        <td>City:</td>
                                        <th><input type="text" class="form-control" name="sender_city" placeholder="" id="sender_city" required></th>
                                    </tr>
                                    
                                    <tr>
                                        <td>State:</td>
                                        <th><input type="text" class="form-control" name="sender_state" placeholder="" id="sender_state" required></th>
                                    </tr>
                                    
                                    <tr>
                                        <td>Phone Number:</td>
                                        <th><input type="text" class="form-control" name="sender_phone" placeholder="" id="sender_phone" maxlength="10" size="10" required></th>
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
                                <h4 class="box-title">Receiver Details
                                    <button type="button" class="btn btn-default btn-sm"  id="receiver_add_fav" onclick="favourite(this)">
                                        <span class="glyphicon glyphicon-heart"></span> Favourite
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" id="receiver_reset_fav" onclick="Reset(this)" style="display:none">Reset</button>
                                    <button type="button" class="btn btn-info btn-sm" id="receiver_update_fav" onclick="favourite(this)" style="display:none">
                                        <span class="glyphicon glyphicon-heart"></span> Update
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm"  id="receiver_fav_list" data-toggle="modal" data-target="#SearchModal" data-parties="receiver">
                                        <span class="glyphicon glyphicon-user"></span> List
                                    </button>
                                </h4>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table class="table table-striped table-bordered">
                                    <tr>
                                        <td>Vendor ID:</td>
                                        <th><input type="text" class="form-control" name="receiver_vendorid" id="receiver_vendorid" placeholder=""></th>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-4">Destination Pincode:</td>
                                        <th><input type="text" class="form-control" name="to_pin" id="to_pin" placeholder="" onchange="receiverlocation(this.value)" maxlength="6" size="6" required></th>
                                    </tr>
                                    <tr>
                                        <td>Receiver Name</td>
                                        <th><input type="text" class="form-control" name="receiver_name" id="receiver_name" placeholder=""required></th>
                                    </tr>
                                    <tr>
                                        <td>Company Name:</td>
                                        <th><input type="text" class="form-control" name="receiver_com_name" id="receiver_com_name" placeholder="" required></th>
                                    </tr>
                                    <tr>
                                        <td>Address:</td>
                                        <th><textarea class="form-control custom-control" rows="3" style="resize:none" name="receiver_address"  id="receiver_address" maxlength="70" size="70" required></textarea></th>
                                    </tr>
                                    
                                    <tr>
                                        <td>City:</td>
                                        <th><input type="text" class="form-control" name="receiver_city" placeholder="" id="receiver_city" required></th>
                                    </tr>
                                    
                                    <tr>
                                        <td>State:</td>
                                        <th><input type="text" class="form-control" name="receiver_state" placeholder="" id="receiver_state" required></th>
                                    </tr>
                                    
                                    <tr>
                                        <td>Phone Number:</td>
                                        <th><input type="text" class="form-control" name="receiver_phone" id="receiver_phone" placeholder="" maxlength="10" size="10" required></th>
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
                                <h4 class="box-title">Shipment Details
                                    <button type="button" class="btn btn-info btn-sm"  id="Check_Avi" onclick="Service_Availabilty()" style="display: none;">
                                        <span class="glyphicon glyphicon-search"></span> Check Availability
                                    </button>
                                </h4>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table class="table table-striped table-bordered">
                                    <tr>
                                        <td>Courier Vendor</td>
                                        <td>
                                            <div>
                                                <select class="form-control selcls" name="couriervendor" id="couriervendor" onclick="services_list()">
                                                    <input type="text" id="OtherCourierVendor" name="OtherCourierVendor" class="form-control" placeholder="Courier Vendor Name" style="display:none">
                                                        </div>
                                        </td>
                                    </tr>
                                    
                                    
                                    
                                    <tr>
                                        <td>Shipment Date</td>
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
                                        <td>Packet Number</td>
                                        <th><input type="text" class="form-control" name="UID" id="UIN" placeholder="" maxlength="15" size="15" required></th>
                                    </tr>
                                    
                                  
                                    
                                    <tr><td>Packet Dimension</br>(cm)</td><td>
                                        <div class="form-group row">
                                            <div class="col-xs-4">
                                                <input class="form-control" name="L" id="L" type="text" placeholder="L">
                                                    </div>
                                            <div class="col-xs-4">
                                                <input class="form-control" name="B" id="B" type="text" placeholder="B">
                                                    </div>
                                            <div class="col-xs-4">
                                                <input class="form-control" name="H" id="H" type="text" placeholder="H">
                                                    </div>
                                        </div>
                                        
                                    </td></tr>
                                    
                                    <tr>
                                        <td>Total Weight</td>
                                        <td>
                                            <input class="form-control" name="W" id="W" type="text" placeholder=" Total Weight in KG">
                                                </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Invoice Value:</td>
                                        <th><input type="text" class="form-control" name="invoice" id="invoice" placeholder="" maxlength="8" size="8"  required></th>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <td> COD</td>
                                        <td>
                                            <input type="radio" id="CODYes"  name="COD" value="Yes" onclick="Collectable(this)"> Yes &nbsp;&nbsp;
                                                <input type="radio" id="CODNo" name="COD" value="No"  onclick="Collectable(this)" checked="checked"> No
                                                    <input type="text" id="CollectableAmount" name="CollectableAmount" class="form-control" placeholder="Collectable Amount" style="display:none">
                                                        </td>
                                        
                                    </tr>

                                    
                                    <tr>
                                        <td>Purpose</td>
                                        <td>
                                            <input type="radio" id="Documents" name="shipmentcontent" value="Documents"> Documents &nbsp;&nbsp;
                                                <input type="radio" id="Commodities" name="shipmentcontent" value="Commodities"> Non_Documents
                                                    
                                                    </td>
                                        
                                    </tr>
                                 
                                    
                                </table>
                                
                            </div>
                            <!-- /.box-footer -->
                        </div>
                        <!-- /.box -->
                    </div>
                    
                </div>
                
                <div class="row">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td>
                                <div class="form-group row">
                                    <div class="col-xs-3">
                                        
                                        <select class="form-control selcls" name="services" id="services" style="display:inline">
                                            <option value="" selected="selected">Select Courier Service</option>
                                        </select>
                                        <input type="text" id="OtherCourierService" name="OtherCourierService" class="form-control" placeholder="Courier Vendor Service" style="display:none">
                                            
                                            </div>
                                    
                                    <div class="col-xs-3">
                                        
                                        <select class="form-control selcls" name="accountnumber" id="accountnumber" style="display:inline">
                                            <option value="" selected="selected">Select Account Number</option>
                                        </select>
                                        <input type="text" id="OtherCourierAWB" name="OtherCourierAWB" class="form-control" placeholder="Airway Bill Number" style="display:none">
                                            
                                            </div>
                                    
                                    
                                    <div class="col-xs-3">
                                        <select class="form-control selcls" name="purpose" id="purpose" >
                                            <option value="" selected="selected">Purpose of Shipment</option>
                                            <option value="GIFT">GIFT</option>
                                            <option value="NOT_SOLD">NOT SOLD</option>
                                            <option value="PERSONAL_EFFECTS">PERSONAL EFFECTS</option>
                                            <option value="REPAIR_AND_RETURN">REPAIR AND RETURN</option>
                                            <option value="SAMPLE">SAMPLE</option>
                                            <option value="SOLD">SOLD</option>
                                        </select>
                                        
                                    </div>
                                    
                                    <div class="col-xs-3">
                                        <button type="reset" class="btn btn-danger">Reset</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <button type="button" class="btn btn-success"  onclick="Validate()">Submit</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table >
                    
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
                                                        Unit Weight(KG)
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
                
                
                <div id="CheckAvailability" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Service Availability</h4>
                            </div>
                            <div class="modal-body">
                                <div id="CheckAvailabilityresult"></div>
                            </div>
                            <div class="modal-footer">
                                
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
                              
                              $("#Documents").click(function () {
                                                    if ($("#Documents").is(":checked")) {
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
                              
                              
                              
                              CourierVendorList();
                              
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
                document.getElementById("OtherCourierVendor").style.display='none';
                document.getElementById("OtherCourierVendor").value='';
                document.getElementById("OtherCourierService").style.display='none';
                document.getElementById("OtherCourierService").value='';
                document.getElementById("OtherCourierAWB").style.display='none';
                document.getElementById("OtherCourierAWB").value='';
                
            }
            else if(vendor_name=="Other"){
                //$("#services").empty();
                //document.getElementById("services").options[0]=new Option("Select Courier Service","");
                //$("#accountnumber").empty();
                //document.getElementById("accountnumber").options[0]=new Option("Select Account Number"," ");
                document.getElementById("OtherCourierVendor").style.display='block';
                document.getElementById("OtherCourierService").style.display='block';
                document.getElementById("OtherCourierAWB").style.display='block';
                
                document.getElementById("services").style.display = 'none';
                document.getElementById("accountnumber").style.display = 'none';
                document.getElementById("services").value = "";
                document.getElementById("accountnumber").value = "";
                
                
            }
            else {
                
                document.getElementById("services").style.display = 'inline';
                document.getElementById("accountnumber").style.display = 'inline';
                document.getElementById("OtherCourierVendor").style.display='none';
                document.getElementById("OtherCourierVendor").value='';
                document.getElementById("OtherCourierService").style.display='none';
                document.getElementById("OtherCourierService").value='';
                document.getElementById("OtherCourierAWB").style.display='none';
                document.getElementById("OtherCourierAWB").value='';
                
                
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
        
        
        
        
        function favourite(i){
            
            var userid="<?php echo $userid; ?>";
            
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
                        
                        xmlhttp.open("GET","ContactFav_ajax.php?pin="+pin+"&vendorid="+vendorid+"&name="+name+"&companyname="+com_name+"&address="+address+"&city="+city+"&state="+state+"&phone="+phone+"&fav=1&parties=sender&id="+userid,true);
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
                        
                        xmlhttp.open("GET","ContactFav_ajax.php?pin="+pin+"&vendorid="+vendorid+"&name="+name+"&companyname="+com_name+"&address="+address+"&city="+city+"&state="+state+"&phone="+phone+"&fav=1&parties=receiver&id="+userid,true);
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
            
            var userid="<?php echo $userid; ?>";
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
                xmlhttp.open("GET","SearchContact_ajax.php?searchname="+searchname+"&comp_name="+searchcomp_name+"&phone="+searchphone+"&parties="+parties+"&searchvendorid="+searchvendorid+"&id="+userid,true);
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
                    
                    document.getElementById("couriervendor").options[0]=new Option("Select Courier Vendor","");
                    for(i = 0; i < arr.length; i++) {
                        document.getElementById("couriervendor").options[i+1]=new Option(arr[i].value,arr[i].venndor_name);
                    }
                    document.getElementById("couriervendor").options[i+1]=new Option("Other","Other");
                    
                }
            };
            xmlhttp.open("GET","vendoraction.php?action=CourierVendorList",true);
            xmlhttp.send();
            
            
        }
        
        
        
        function Service_Availabilty(){
            
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
                document.getElementById("Check_Avi").disabled = true;
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        //alert(this.responseText);
                        $("#CheckAvailability").modal();
                        document.getElementById("CheckAvailabilityresult").innerHTML = xmlhttp.responseText;
                        document.getElementById("Check_Avi").disabled = false;
                        
                    }
                };
                xmlhttp.open("GET", "Service_ajax.php?action=ServiceAvailability_AWB&pickup_pin="+from_pin+"&desti_pin="+to_pin, true);
                xmlhttp.send();
                
            }
            
            
        }
        
        
        function Validate(){
            
            var from_pin= document.getElementById("from_pin").value;
            
            
            ///Sender Details////////
            var sname = document.getElementById("sender_name").value;
            var scom_name = document.getElementById("sender_com_name").value;
            var saddress = document.getElementById("sender_address").value;
            var scity = document.getElementById("sender_city").value;
            var sstate = document.getElementById("sender_state").value;
            var sphone = document.getElementById("sender_phone").value;
            
            
            //// Receiver Details//////
            
            var rname = document.getElementById("receiver_name").value;
            var rcom_name = document.getElementById("receiver_com_name").value;
            var raddress = document.getElementById("receiver_address").value;
            var rcity = document.getElementById("receiver_city").value;
            var rstate = document.getElementById("receiver_state").value;
            var rphone = document.getElementById("receiver_phone").value;
            var to_pin= document.getElementById("to_pin").value;
            
            
            
            //// Courier Package and Vendor Details////////
            
            var vendor = document.getElementById("couriervendor");
            var vendor_name = vendor.options[vendor.selectedIndex].value;
            var OtherCourierVendorName = document.getElementById("OtherCourierVendor").value;
            
            
            var OtherCourierService = document.getElementById("OtherCourierService").value;
            
            
            var OtherCourierAWB = document.getElementById("OtherCourierAWB").value;
            
            var COD=document.querySelector('input[name = "COD"]:checked').value;
            var CollectableAmount= document.getElementById("CollectableAmount").value;
            
            var ShipmentDate= document.getElementById("dtp_input2").value;
            var UIN= document.getElementById("UIN").value;
            var invoice= document.getElementById("invoice").value;
            
            var p = document.getElementById("purpose");
            var purposevalue = p.options[p.selectedIndex].value;
            
            //var Content=document.querySelector('input[name = "shipmentcontent"]:checked').value;
            
            var flag=1;
            
            
            if(from_pin==""){
                alert("Enter Pickup Pincode");
                flag=0;
                return;
            }
            else if (from_pin.length!=6 || isNaN(from_pin)) {
                if(from_pin!=""){
                    alert("Enter Correct Pickup Pincode");
                    flag=0;
                    return;
                }
            }
            else if(sname==""){
                
                alert("Enter Sender Name");
                flag=0;
                return false;
            }
            else if (scom_name==""){
                alert("Enter Sender Compnay Name");
                flag=0;
                return false;
                
            }
            else if (saddress==""){
                alert("Enter Sender Address");
                flag=0;
                return false;
                
            }
            else if (scity==""){
                
                alert("Enter Sender City");
                flag=0;
                return false;
            }
            else if (sstate==""){
                
                alert("Enter Sender State ");
                flag=0;
                return false;
            }
            else if (sphone==""){
                
                alert("Enter Sender Phone Number");
                flag=0;
                return false;
            }
            else if (sphone.length!=10|| isNaN(sphone)) {
                if(sphone!=""){
                    alert("Enter Correct Phone Number");
                    flag=0;
                    return;
                }
            }
            else if(to_pin==""){
                alert("Enter Destination Pincode");
                flag=0;
                return;
            }
            else if (to_pin.length!=6 || isNaN(to_pin)) {
                if(to_pin!=""){
                    alert("Enter Correct Destination  Pincode");
                    flag=0;
                    return;
                }
            }
            else if(rname==""){
                
                alert("Enter Receiver Name");
                flag=0;
                return false;
            }
            else if (rcom_name==""){
                alert("Enter Receiver Compnay Name");
                flag=0;
                return false;
                
            }
            else if (raddress==""){
                alert("Enter Receiver Address");
                flag=0;
                return false;
                
            }
            else if (rcity==""){
                
                alert("Enter Receiver City");
                flag=0;
                return false;
            }
            else if (rstate==""){
                
                alert("Enter Receiver State ");
                flag=0;
                return false;
            }
            else if (rphone==""){
                
                alert("Enter Receiver Phone Number");
                flag=0;
                return false;
            }
            else if (rphone.length!=10|| isNaN(rphone)) {
                if(rphone!=""){
                    alert("Enter Correct Phone Number");
                    flag=0;
                    return;
                }
            }
            else if (COD=="Yes"){
                
                if(CollectableAmount==0 || isNaN(CollectableAmount)){
                    
                    alert("Please add valid collectable amount");
                    flag=0;
                    return false;
                }
            }
            else if (ShipmentDate==""){
                
                alert("Please Enter ShipmentDate");
                flag=0;
                return;
            }
            else if(UIN==""){
                
                alert("Please enter Unique Number");
                flag=0;
                return;
            }
            else if(invoice==""){
                
                alert("Please enter invoice value");
                flag=0;
                return;
                if(invoice==0 || isNaN(invoice) ){
                    
                    alert("Please enter valid invoice value");
                    flag=0;
                    return false;
                }
            }
            
            else if(purposevalue==""){
                
                alert("Please Select Purpose of Shipment");
                flag=0;
                return;
            }
            
            if(vendor_name!="Other"){
                
                var CourierService = document.getElementById("services");
                var CourierServiceName = CourierService.options[CourierService.selectedIndex].value;
                var accountnumber = document.getElementById("accountnumber");
                var accountnumbervalue = accountnumber.options[accountnumber.selectedIndex].value;
                
                if(vendor_name==""){
                    
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
                
            }
            
            
            
            
            if(vendor_name=="Other"){
                
                if(OtherCourierVendorName==""){
                    
                    alert("Please Enter Courier Vendor Name");
                    flag=0;
                    return;
                }
                else if(OtherCourierService==""){
                    
                    alert("Please enter Courier Service Name");
                    flag=0;
                    return false;
                }
                else if(OtherCourierAWB==""){
                    
                    alert("Please Enter Airwaybill Number");
                    flag=0;
                    return;
                }
                
                
            }
            
            if (document.querySelector('input[name = "shipmentcontent"]:checked').value === null ){
                
                alert("Please check shipment content");
            }
            
            
            if(flag!=0){
                
                document.getElementById("GenerateAWBForm").submit();
                return true;
            }
            
            
            
            
        }
        
            </script>
        
        
        
    </body>
</html>
