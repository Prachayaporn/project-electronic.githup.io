<?php
session_start();
include('server.php');

if (!isset($_SESSION["username"])) {
    $_SESSION['msg'] = "คุณต้องเข้าสู่ระบอบก่อน";
    header('location: login.php');
 }
 if (isset($_GET['logout'])){
    session_destroy();
    unset($_SESSION['username']);
    header('location: login.php');
 }
$errors = array();

if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $urole = 'user';
    if (empty($username)) {
        array_push($errors, "Username is required");
    }

    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $password = md5($password);
        $query = "SELECT * FROM user WHERE username = '$username' AND password = '$password'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION["user_id"] = $row["id_user"];
        $_SESSION["username"] = $row["username"];
        $_SESSION["urole"] = $row["urole"];

            if($_SESSION["urole"] == 'admin') {
                header('location: admin.php');
        }
        if (mysqli_num_rows($result) == 1) {
            $_SESSION['username'] = $username;
            $_SESSION['success'];
            header('location: index.php');
      }else{
            array_push($errors, "ชื่อผู้ใช้/รหัสผ่านไม่ถูกต้อง");
            $_SESSION['error'] = 'ชื่อผู้ใช้/รหัสผ่านไม่ถูกต้อง';
            header('location: login.php');
        }
    }
}
}
?>
        