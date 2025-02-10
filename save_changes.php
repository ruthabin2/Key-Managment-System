<?php
include 'DbConn.php';
require_once 'config.php';

function encrypATM($atm, $public_key){
    openssl_public_encrypt($atm, $encryptedI, $public_key);
    return $encryptedI;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $originalAtmName = $_POST['atm-name-original'];
    $atmName = $_POST['atm-name'];
    $ipaddress = encrypATM($_POST['ipaddress'], $public_key);
    $hostname = encrypATM($_POST['hostname'],$public_key);
    $key1 = encrypATM($_POST['key1'],$public_key);
    $key2 = encrypATM($_POST['key2'],$public_key);
    $key3 = encrypATM($_POST['key3'],$public_key);

    /////////////////////////////////////////
    // $ipaddress = $_POST['ipaddress'];
    // $hostname = $_POST['hostname'];
    // $key1 = $_POST['key1'];
    // $key2 = $_POST['key2'];
    // $key3 = $_POST['key3'];

    $conn = mysqli_connect("localhost", "root", "", "atm");
    $query = "UPDATE atm_key SET atmname = ?, ipaddress = ?, hostname = ?, key1 = ?, key2 = ?, key3 = ? WHERE atmname = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("sssssss", $atmName, $ipaddress, $hostname, $key1, $key2, $key3, $originalAtmName);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Record updated successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update record"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Failed to prepare statement"]);
    }
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
