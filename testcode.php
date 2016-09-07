<?php
    
    $end=3;
    
    
    $Commodity_desc=array();
    
    for($i=1;$i<($end+1);$i++){
     
        $Commodity_desc["Dim".$i]["length".$i]=1;
        $Commodity_desc["Dim".$i]["breath".$i]=1;
        $Commodity_desc["Dim".$i]["height".$i]=1;
        $Commodity_desc["Dim".$i]["weight".$i]=1;
     
     }
     
     print_r($Commodity_desc);
    
?>