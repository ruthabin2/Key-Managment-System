<?php
$servername = "127.0.0.1";
$username = "root";
$password ="";
$dbname= "atm";

//connections
$conn= mysqli_connect($servername, $username, $password, $dbname);

//check conn
if(!$conn){
    die("connnection failed" .mysqli_connect_error());
}
?>