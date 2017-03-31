<?php
    
    require_once 'dbconfig.php';
    ignore_user_abort(true);
    set_time_limit(0); // disable the time limit for this script
    
    $UID=$_GET['UID'];
    $userid=$_GET['userid'];
    try{
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt=$conn->prepare("Select * from AirwayBill where UID='$UID' and CreatedByUserID='$userid'");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $row= $stmt->fetch();
    $numrows = $stmt->rowCount();
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    if($numrows==1){
        
        $path = $row["AWB_Link"];// change the path to fit your websites document structure
        $filename=$row["Airwaybill_Number"];
        
    }
    else{
        
        $path = 'SomethingWentWrong.pdf';// change the path to fit your websites document structure
        $filename='SomethingWentWrong.pdf';
        
    }
    
        $dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).]|[\.]{2,})", '',$filename); // simple file name validation
        $dl_file = filter_var($dl_file, FILTER_SANITIZE_URL); // Remove (more) invalid characters
        $fullPath = $path;
        
        if ($fd = fopen ($fullPath, "r")) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case "pdf":
                    header("Content-type: application/pdf");
                    header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
                    break;
                    // add more headers for other content types here
                default;
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
                    break;
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while(!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose ($fd);
    
        
    
    
    ?>
