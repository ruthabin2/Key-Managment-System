<?php
session_start();
include "config/db2.php";

// Check if the user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password != $confirm_password) {
        header("Location: change_password.php?error=Passwords do not match!");
        exit();
    } else {
 // Implement a basic password policy (minimum 8 characters, at least one number, one uppercase and one lowercase letter)
 if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $new_password)) {
    header("Location: change_password.php?error=Password does not meet the policy requirements!");
    exit();
}

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $user_name = $_SESSION['user_name'];
        $sql = "UPDATE Credential SET password=?, require_password_change=0 WHERE user_name=?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $user_name);
            if (mysqli_stmt_execute($stmt)) {
                $role_query = "SELECT role FROM Credential WHERE user_name=?";
                if ($role_stmt = mysqli_prepare($conn, $role_query)) {
                    mysqli_stmt_bind_param($role_stmt, "s", $user_name);
                    mysqli_stmt_execute($role_stmt);
                    mysqli_stmt_bind_result($role_stmt, $user_role);
                    mysqli_stmt_fetch($role_stmt);
                    mysqli_stmt_close($role_stmt);

                    if ($user_role == "admin") {
                        ob_start(); // Start output buffering
                        header("Location: admin_home.php");
                        ob_end_flush(); // Flush the output buffer
                        exit();
                    } else {
                        ob_start(); // Start output buffering
                        header("Location: user_home.php");
                        ob_end_flush(); // Flush the output buffer
                        exit();
                    }
                } else {
                    $error = "Error fetching user role.";
                }
            } else {
                $error = "Error updating password.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>Change Password</h2>
    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
    <?php if (isset($_GET['error'])) { ?>
     		<p class="error"><?php echo $_GET['error']; ?></p>
     	<?php } ?>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        <input type="submit" value="Change Password">
    </form>
</body>
</html>
