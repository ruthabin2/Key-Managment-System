<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
if (isset($_POST["Register"])) {
    $conn = mysqli_connect("localhost", "root", "", "atm");
    if ($conn === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
   
    $uname = htmlspecialchars($_POST['uname']);
    $pass = htmlspecialchars($_POST['pass']);
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);
    $require_password_change = isset($_POST['require_password_change']) ? 1 : 0;

    $sql = "INSERT INTO Credential (user_name, password, role, require_password_change) VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssi", $uname, $hashed_password, $role, $require_password_change);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('User Registered Successfully');</script>";
        } else {
            echo "Error:1 " . mysqli_stmt_error($stmt);
        }
    } else {
        echo "Error:2 " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="register.css">
<title>user manage</title>
</head>
<body>
<?php include('sidebar.php'); ?>
<?php include('navbar.php'); ?>
<div class='card'>
    <div class='data-container'>
        <form style="width: 70%; margin-top:15px; margin-left:130px; padding-right:150px;" id="registerForm" class="form" method="POST" enctype="multipart/form-data">
            <div class="form_item">
                <label>User Name:</label>
                <select id="uname" name="uname" required>
                    <option value="">Select User Name</option>
                    <?php
                    // Populate dropdown with usernames from the domain table
                    require_once 'config.php';
                    include('DbConn.php');
                    $conn = mysqli_connect("localhost", "root", "", "atm");
                    $result = mysqli_query($conn, "SELECT user_name FROM domain");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . htmlspecialchars($row['user_name']) . "'>" . htmlspecialchars($row['user_name']) . "</option>";
                    }
                    mysqli_close($conn);
                    ?>
                </select>
            </div><br>  

            <div class="form_item">
                <label>Default Password:</label>
                <input type="text" id="pass" name="pass" value="P@ssw0rd" style="width:40%" readonly>
            </div><br>     

            <div class="form_item">
                <label>Role:</label>
                <select id="role" name="role" Required >
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div><br>

            <div class="form_item">
                <label>Require password change on first login:</label>
                <input type="checkbox" id="require_password_change" name="require_password_change">
            </div><br>

            <input type="submit" value="Register" name="Register" style="width:25%; background-color: #3d2f2f; color:white; cursor:pointer; font-size: 16px; ">
        </form>
    </div>
</div>
</body>
</html>

