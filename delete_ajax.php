<?php
include 'DbConn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['atmname'])) {
        $atmname = $_POST['atmname'];
        $conn = mysqli_connect("localhost", "root", "", "atm");
        $query = "DELETE FROM atm_key WHERE atmname = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("s", $atmname);
            if ($stmt->execute()) {
                echo "Record deleted successfully";
            } else {
                echo "Failed to delete row";
            }
            $stmt->close();
        } else {
            echo "Failed to prepare statement";
        }
    } else {
        echo "No ATM Name provided";
    }
    $conn->close();
} else {
    echo "Invalid request";
}
?>
