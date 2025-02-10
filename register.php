
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="register.css">
    <title>Register</title>
</head>
<body>
<?php 
session_start();
include('sidebar.php'); 
include('navbar.php');
// Check if user is authorized
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Unauthorized access'); window.location.href='login.php';</script>";
    exit();
}
?>

<div class="wrapper" style="padding-left:10px; margin-right:70px;" >

    
<form style="width: 70%; margin-top:15px; margin-left:130px; padding-right:150px;" id="registerForm" class="form" method="POST" enctype="multipart/form-data" >

<div class="form_item" >
<label>ATM Name:</label>
<input type="text" id="atmName" placeholder="Enter ATM Name" name="atmName" >

</div><br>  
   
<div class="form_item">
<label>IP Address:</label>
<input type="text" id="ipAddress" placeholder="Enter IP Address"  name="ipAddress" >

</div><br>     
    
<div class="form_item">
<label>Host Name:</label>
<input type="text" id="hostname" placeholder="Enter Host Name" name="hostname" >

</div><br>

    
    <div class="form_item">
<label>Key 1:</label>
<input type="text" id="key1" placeholder="Enter The First Key" name="key1" >
 
</div><br> 

    
    <div class="form_item">
<label>Key 2:</label>
<input type="text" id="key2" placeholder="Enter The Second Key" name="key2" >

</div><br> 

    
    <div class="form_item">
<label>Key 3:</label>
<input type="text" id="key3" placeholder="Enter The Third Key" name="key3" >

</div><br> 




   <div class="btn">
<!-- <input type="submit" value="Submit" name="sbt-btn"> -->

        <input type="file" name="qrImage" accept="image/*" ><br>
        <input type="submit" value="Upload" name="Upload">
        <input type="submit" value="Register" name="Register">
</div>
</form>
     
</div> 
   
</body>
</html>
<?php

require_once 'config.php';
include('DbConn.php');
require __DIR__ . "/vendor/autoload.php";

use Zxing\QrReader;





if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["qrImage"]) && isset($_POST["Upload"])) {
    // Check if there are no upload errors
    if ($_FILES["qrImage"]["error"] == UPLOAD_ERR_OK) {
        // Get the temporary file path
        $tmpFilePath = $_FILES["qrImage"]["tmp_name"];
        try {
            // Decode the QR image
            $qrcode = new QrReader($tmpFilePath);
            $decodedText = $qrcode->text();
            // Extract relevant information from the decoded text (replace with your logic)
            $decodedValues = explode("\n", $decodedText);
            $atmName = $decodedValues[0] ?? '';
            $ipAddress = $decodedValues[1] ?? '';
            $hostname = $decodedValues[2] ?? '';
            $key1 = $decodedValues[3] ?? '';
            $key2 = $decodedValues[4] ?? '';
            $key3 = $decodedValues[5] ?? '';
            // Fill the input fields with the decrypted values
            echo '<script>';
            echo 'document.getElementById("atmName").value = "' . htmlspecialchars(addslashes($atmName), ENT_QUOTES, 'UTF-8') . '";';
            echo 'document.getElementById("ipAddress").value = "' . htmlspecialchars(addslashes($ipAddress), ENT_QUOTES, 'UTF-8') . '";';
            echo 'document.getElementById("hostname").value = "' . htmlspecialchars(addslashes($hostname), ENT_QUOTES, 'UTF-8') . '";';
            echo 'document.getElementById("key1").value = "' . htmlspecialchars(addslashes($key1), ENT_QUOTES, 'UTF-8') . '";';
            echo 'document.getElementById("key2").value = "' . htmlspecialchars(addslashes($key2), ENT_QUOTES, 'UTF-8') . '";';
            echo 'document.getElementById("key3").value = "' . htmlspecialchars(addslashes($key3), ENT_QUOTES, 'UTF-8') . '";';
            echo '</script>';
        } catch (InvalidArgumentException $e) {
            // Handle decoding error
            echo "<script>alert('Error: Invalid image source. Please check the image path.');</script>";
        }
    } else {
        // Handle file upload error
        echo "<script>alert('Error: " . htmlspecialchars($_FILES["qrImage"]["error"], ENT_QUOTES, 'UTF-8') . "');</script>";
    }
}

if (isset($_POST["Register"])) {
    // Create database connection
    $conn = mysqli_connect("localhost", "root", "", "atm");
    if ($conn === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    // Prepare an insert statement
    $sql = "INSERT INTO atm_key (atmname, ipaddress, hostname, key1, key2, key3) VALUES (?, ?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ssssss", $param_atmName, $param_ipAddress, $param_hostname, $param_key1, $param_key2, $param_key3);
        // Set parameters
      //  openssl_public_encrypt($_POST['atmName'], $encryptedA, $public_key);
        openssl_public_encrypt($_POST['ipAddress'], $encryptedI, $public_key);
        openssl_public_encrypt($_POST['hostname'], $encryptedH, $public_key);
        openssl_public_encrypt($_POST['key1'], $encryptedk1, $public_key);
        openssl_public_encrypt($_POST['key2'], $encryptedk2, $public_key);
        openssl_public_encrypt($_POST['key3'], $encryptedk3, $public_key);
        $param_atmName = $_POST['atmName'];
        $param_ipAddress = $encryptedI;
        $param_hostname = $encryptedH;
        $param_key1 = $encryptedk1;
        $param_key2 = $encryptedk2;
        $param_key3 = $encryptedk3;
       
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('ATM registered successfully: ');</script>";
        } else {
            echo "Error:1 " . htmlspecialchars(mysqli_stmt_error($stmt), ENT_QUOTES, 'UTF-8') . "');</script>";
        }
    } else {
        echo "Error:2 " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . "');</script>";
    }
    // Close statement
    mysqli_stmt_close($stmt);
    // Close connection
    mysqli_close($conn);
}
?>
