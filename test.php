<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="register.css">
    <title>Register</title>
</head>
<body>
<?php include('sidebar.php'); ?>
<?php include('navbar.php'); ?>
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
<input type="text" id="key3" placeholder="Enter The Third Key" name="key3">

</div><br> 




   <div class="btn">
<!-- <input type="submit" value="Submit" name="sbt-btn"> -->

<input type="file" name="qrImage" accept="image/*"><br>
        <input type="submit" value="Upload" name="Upload">
        <input type="submit" value="Register" name="Register">
</div>
</form>
     
</div> 
   
</body>
</html>
<?php
$key_size = 32; // 256 bits
$encryption_key = openssl_random_pseudo_bytes($key_size, $strong);
// $strong will be true if the key is crypto safe

function encryptData($data, $key) {
    $iv = openssl_random_pseudo_bytes(16); // Initialization vector
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decryptData($encryptedData, $key) {
    $data = base64_decode($encryptedData);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
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
        
        $param_atmName = encryptData($_POST["atmName"], $encryption_key);
        $param_ipAddress = encryptData($_POST["ipAddress"], $encryption_key);
        $param_hostname = encryptData($_POST["hostname"], $encryption_key);
        $param_key1 = encryptData($_POST["key1"], $encryption_key);
        $param_key2 = encryptData($_POST["key2"], $encryption_key);
        $param_key3 = encryptData($_POST["key3"], $encryption_key);
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
           // echo "<script>alert('ATM registered successfully');</script>";
        } else {
            echo "Error:1 " . mysqli_stmt_error($stmt);
        }
    } else {
        echo "Error:2 " . mysqli_error($conn);
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($conn);
}
//Key
$key = 'SuperSecretKey';

//To Encrypt:
$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, 'I want to encrypt this', MCRYPT_MODE_ECB);

//To Decrypt:
$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $encrypted, MCRYPT_MODE_ECB);

echo "<script>alert('follow:  ".$decrypted ."".$encrypted ." ');</script>";
?>