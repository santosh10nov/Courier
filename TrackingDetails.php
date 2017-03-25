<?php
    
    $UID=$_GET["UID"];
    $AWBNo=$_GET["AWB_Number"];
    $couriervendor=$_GET["couriervendor"];
    
    $servername = "127.0.0.1";
    $username = "root";
    $pass = "yesbank";
    $dbname = "transporter";
    
    error_reporting(0);
    
    try{
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
            if($couriervendor=="BlueDart"){
                include("Tracking/BlueDart/BlueDartTrackingDetails.php");
                
            }
            else if($couriervendor=="FedEx"){
                
                include("Tracking/FedEx/FedExTrackingDetails.php");
            }
      
        $stmt1 = $conn->prepare("SELECT TrackingHistory.TrackingTime,TrackingStatusMapping.Description,TrackingHistory.Place FROM `TrackingHistory` left join TrackingStatusMapping on TrackingStatusMapping.Code=TrackingHistory.Code where `UID`='$UID'  group by TrackingHistory.TrackingTime order by TrackingHistory.TrackingTime desc");
        $stmt1->execute();
        
        $x="";
        
        
        
        foreach($stmt1->fetchAll(PDO::FETCH_ASSOC) as $key=>$value){
        
           
            $stmt='<li><div><time>Date: '.date('d-M-Y h:i:sa', strtotime($value["TrackingTime"])).'</time>Status: '.$value["Description"].'<br> Place: '.$value["Place"].'</div></li>';
            $x=$x.$stmt;
        }

        
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    

    
    
    ?>





<!DOCTYPE html>
<html lang="en">
<head>
<title>rShipper Service-Tracking Details</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<style>


.timeline ul {
background: gray;
padding: 50px 0;
}

.timeline ul li {
    list-style-type: none;
position: relative;
width: 6px;
margin: 0 auto;
    padding-top: 50px;
background: #fff;
}

.timeline ul li::after {
content: '';
position: absolute;
left: 50%;
bottom: 0;
transform: translateX(-50%);
width: 30px;
height: 30px;
    border-radius: 50%;
background: inherit;
}

.timeline ul li div {
position: relative;
bottom: 0;
width: 400px;
padding: 15px;
background: #F45B69;
}

.timeline ul li div::before {
content: '';
position: absolute;
bottom: 7px;
width: 0;
height: 0;
    border-style: solid;
}

.timeline ul li:nth-child(odd) div {
left: 45px;
}

.timeline ul li:nth-child(odd) div::before {
left: -15px;
    border-width: 8px 16px 8px 0;
    border-color: transparent #F45B69 transparent transparent;
}

.timeline ul li:nth-child(even) div {
left: -439px;
}

.timeline ul li:nth-child(even) div::before {
right: -15px;
    border-width: 8px 0 8px 16px;
    border-color: transparent transparent transparent #F45B69;
}

time {
display: block;
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 8px;
}


/* EFFECTS
 –––––––––––––––––––––––––––––––––––––––––––––––––– */

.timeline ul li::after {
transition: background .5s ease-in-out;
}

.timeline ul li.in-view::after {
background: #F45B69;
}

.timeline ul li div {
visibility: hidden;
opacity: 0;
transition: all .5s ease-in-out;
}

.timeline ul li:nth-child(odd) div {
transform: translate3d(200px, 0, 0);
}

.timeline ul li:nth-child(even) div {
transform: translate3d(-200px, 0, 0);
}

.timeline ul li.in-view div {
transform: none;
visibility: visible;
opacity: 1;
}


/* GENERAL MEDIA QUERIES
 –––––––––––––––––––––––––––––––––––––––––––––––––– */

@media screen and (max-width: 900px) {
    .timeline ul li div {
    width: 250px;
    }
    .timeline ul li:nth-child(even) div {
    left: -289px;
        /*250+45-6*/
    }
}

@media screen and (max-width: 600px) {
    .timeline ul li {
        margin-left: 20px;
    }
    .timeline ul li div {
    width: calc(100vw - 91px);
    }
    .timeline ul li:nth-child(even) div {
    left: 45px;
    }
    .timeline ul li:nth-child(even) div::before {
    left: -15px;
        border-width: 8px 16px 8px 0;
        border-color: transparent #F45B69 transparent transparent;
    }
}
</style>

</head>

<body>


<div class="container" id="container">


<h1 align="center"> Tracking Details</h1>
<h3 align="center"> <?php echo $couriervendor." - ".$AWBNo; ?> </h3>


<!-- timeline start -->

<section class="timeline">
<ul>
<?php echo $x; ?>

</ul>
</section>


</div>


<script>

(function() {
 
 'use strict';
 
 
 var items = document.querySelectorAll(".timeline li");
 
 function isElementInViewport(el) {
 var rect = el.getBoundingClientRect();
 return (
         rect.top >= 0 &&
         rect.left >= 0 &&
         rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
         rect.right <= (window.innerWidth || document.documentElement.clientWidth)
         );
 }
 
 function callbackFunc() {
 for (var i = 0; i < items.length; i++) {
 if (isElementInViewport(items[i])) {
 items[i].classList.add("in-view");
 }
 }
 }
 
 // listen for events
 window.addEventListener("load", callbackFunc);
 window.addEventListener("resize", callbackFunc);
 window.addEventListener("scroll", callbackFunc);
 
 })();
</script>


</body>
</html>
