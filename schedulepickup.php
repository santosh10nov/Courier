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
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
                    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
                    <script src="js/validation.js"></script>
                    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    </head>
    <body onload="pickupstatus()">
        

        <?php echo  $user->Navigation(); ?>
        <div class="container">
            <h1 align="center">Schedule Pickup </h1>
            
            <div id="result" style="overflow: scroll;">
            </div>
            <div>
                <button type="button" class="btn btn-success" onClick="pickupstatus()">Refresh</button>
            </div>
            
            <script>



                function pickupstatus(){
                    
                
                    
                    var id="<?php echo $_SESSION['userSession']; ?>";
                    
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
                            
                            
                        }
                    };
                    xmlhttp.open("GET","pickup_ajax.php?action=pickupstatus&id="+id,true);
                    xmlhttp.send();
                    
                    
                    
                }

            </script>
            
            
    </body>
</html>
