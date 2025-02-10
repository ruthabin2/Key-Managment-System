<?php
session_start();
include "config/db2.php";

if (!isset($_POST['uname']) || !isset($_POST['password'])) {
    header("Location: index.php");
    exit();
}

function validate($data) {
    return htmlspecialchars(trim($data));
}

$uname = validate($_POST['uname']);
$pass = validate($_POST['password']);

if (empty($uname)) {
    header("Location: index.php?error=User Name is required");
    exit();
} else if (empty($pass)) {
    header("Location: index.php?error=Password is required");
    exit();
} else {
    $sql = "SELECT * FROM Credential WHERE user_name=?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $uname);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($pass, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_name'] = $row['user_name'];
                $_SESSION['id'] = $row['id'];
                $_SESSION['role'] = $row['role'];

                if ($row['require_password_change'] == 1) {
                    header("Location: change_password.php");
                    exit();
                } else {
                    if ($row['role'] == "admin") {
                        header("Location: admin_home.php");
                    } else {
                        header("Location: user_home.php");
                    }
                    exit();
                }
            } else {
                header("Location: index.php?error=Incorrect User name or password");
                exit();
            }
        } else {
            header("Location: index.php?error=Incorrect User name or password");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}
?>
