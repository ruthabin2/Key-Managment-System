<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['atm'])) {
   // Establish database connection
   $conn = mysqli_connect("localhost", "root", "", "atm");

   if ($conn === false) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}
    // Get ATM name from the query string and sanitize it
    $atmName = mysqli_real_escape_string($conn, $_GET['atm']);


    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM atm_key WHERE atmname = ?");
    $stmt->bind_param("s", $atmName);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        // Fetch the first row (assuming ATM name is unique)
        $row = $result->fetch_assoc();

        // Decrypt ATM information
        $decrypted_atm_ipaddress = decryptATMName($row['ipaddress'], $private_key);
        $decrypted_atm_hostname = decryptATMName($row['hostname'], $private_key);
        $decrypted_atm_key1 = decryptATMName($row['key1'], $private_key);
        $decrypted_atm_key2 = decryptATMName($row['key2'], $private_key);
        $decrypted_atm_key3 = decryptATMName($row['key3'], $private_key);

        // Store decrypted information in an associative array
        $atmInfo = array(
            'atmname' => $row['atmname'],
            'ipaddress' => $decrypted_atm_ipaddress,
            'hostname' => $decrypted_atm_hostname,
            'key1' => $decrypted_atm_key1,
            'key2' => $decrypted_atm_key2,
            'key3' => $decrypted_atm_key3
        );

        // Return ATM information as JSON
        header('Content-Type: application/json');
        echo json_encode($atmInfo);
    } else {
        // No matching ATM found
        http_response_code(404);
        echo json_encode(array("error" => "ATM not found"));
    }

    // Close the database connection
    $stmt->close();
    mysqli_close($conn);
} else {
    // Invalid request
    http_response_code(400);
    echo json_encode(array("error" => "Invalid request"));
}

// Function to decrypt ATM names
function decryptATMName($encrypted_name, $private_key) {
    openssl_private_decrypt($encrypted_name, $decrypted_name, $private_key);
    return $decrypted_name;
}
?>
