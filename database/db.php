<?php
$con = new mysqli("localhost","root","","ag_store");


//check connection
if($con->connect_error){
    echo "Failed to connect to MySqli: " .$con->connect_error;
    exit();
}
?>
