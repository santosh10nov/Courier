function validate_activity() {
    var to_pin=document.getElementsByName('to_pin')[0].value;
    var from_pin=document.getElementsByName('from_pin')[0].value;
    var letters ="^[a-zA-Z0-9_]*$";
    document.getElementById("alert_div").innerHTML = "";
    
    if(to_pin.length != 6 && from_pin.length != 6){
        document.getElementById("alert_box").style.display = "inline";
        document.getElementById("alert_div").innerHTML = "Pickup Pincode and Destination Pincode should be exactly 6 characters";
        return false;
    }
    else if(to_pin.length != 6){
        document.getElementById("alert_box").style.display = "inline";
        document.getElementById("alert_div").innerHTML = "Destination Pincode should be exactly 6 characters";
        return false;
    }
    else if(from_pin.length != 6){
        document.getElementById("alert_box").style.display = "inline";
        document.getElementById("alert_div").innerHTML = "Pickup Pincode should be exactly 6 characters";
        return false;
    }
    
    else if(to_pin == from_pin){
        document.getElementById("alert_box").style.display = "inline";
        document.getElementById("alert_div").innerHTML = "Pickup Pincode and Destination Pincode are same";
        return false;
    }
    
    
    /*else if(isNaN('to_pin')){
        document.getElementById("alert_box").style.display = "inline";
        document.getElementById("alert_div").innerHTML = "Destination Pincode contain character";
        return false;
    }
    else if(isNaN('from_pin')){
        alert("Test")
        document.getElementById("alert_box").style.display = "inline";
        document.getElementById("alert_div").innerHTML = "Pickup Pincode contain Character or Special Charater";
        return false;
    }

    else if(isNaN('to_pin') && isNaN('from_pin')){
        document.getElementById("alert_box").style.display = "inline";
        document.getElementById("alert_div").innerHTML = "Pickup Pincode and Destination Pincode contain Characters or Special Charate";
        return false;
    }*/
  
}