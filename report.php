<?php
    
    session_start();
    
    require_once 'class.user.php';
    $user = new USER();
    
    if($user->is_logged_in()!=true)
    {
        $user->redirect('login.php');
    }
    
    $userid=$_SESSION['userSession'];
    $CompID=$_SESSION['userCompID'];

    
    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Report | rShipper</title>
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
                            
                            
                            
                                </style>
                            
                            </head>
    
    <body>
        
        <?php echo  $user->Navigation(); ?>
        
        <div class="container" id="container">
            <h1 align="center">Reports</h1>
            <div id = "alert_placeholder"></div>
            <!-- form start -->
            <form class="form-horizontal"  method="post" >
                <div class="col-md-12 ">
                    <div class="panel panel-default panel-table">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead style="background-color: gray; color:white; ">
                                            <tr>
                                                <td>From Date</td>
                                                <td>To Date</td>
                                                <td>Report Type</td>
                                                <td><em class="fa fa-cog" align="center"></em></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="filters">
                                                <th>
                                                    <div class="input-group date form_date col-md-12" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
                                                        <input class="form-control" size="16" type="text" value="" name="fromdate"  id="fromdate" readonly>
                                                            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                            </div>
                                                    <input type="hidden" id="dtp_input1" value="" />
                                                </th>
                                                <th>
                                                    <div class="input-group date form_date col-md-12" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                                        <input class="form-control" size="16" type="text" value="" name="todate"  id="todate" readonly>
                                                            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                            </div>
                                                    <input type="hidden" id="dtp_input2" value="" />
                                                </th>
                                                <th>
                                                    <select class="form-control selcls" name="ReportType" id="ReportType">
                                                        <option value="" selected="selected">Select Report Type</option>
                                                        <option value="AirwayBill">AirwayBill List</option>
                                                        <option value="Pickup">Pickup Report</option>
                                                        <option value="Dispatch">Dispatch Packages Report</option>
                                                        <option value="Tracking">Tracking Report</option>
                                                        <option value="Receive">Receive Courier Report</option>
                                                    </select>
                                                </th>
                                                
                                                <th><input type="button" value="Download Report" class = "btn btn-success" id="Download"onclick="DownloadReport()">&nbsp;<input type="reset" value="Reset" class = "btn btn-info"></th>
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
        
        </div>
        
        
        <script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
        <script type="text/javascript" src="js/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
        
        
        <script>
            
            
            function DownloadReport(){
                
                
                var id="<?php echo $userid; ?>";
                var Comp="<?php echo $CompID; ?>";
                var from_date=document.getElementById("dtp_input1").value;
                var to_date=document.getElementById("dtp_input2").value;
                var reporttype= document.getElementById("ReportType");
                var type = reporttype.options[reporttype.selectedIndex].value;
                
                
                if(from_date=="" || to_date=="" || type=="" ){
                    
                    alert("Plz. fill all details to download report");
                }
                else{
                    
                window.location="DownloadReport.php?id="+id+"&Comp="+Comp+"&fromdate="+from_date+"&todate="+to_date+"&type="+type;
                
                }
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
