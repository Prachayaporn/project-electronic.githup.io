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

 if (isset($_POST['reg_user'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password_1 = mysqli_real_escape_string($conn, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($conn, $_POST['password_2']);
    $urole = 'user';
 
    
    if(empty($username)) {
        array_push($errors,"จำเป็นต้องมีชื่อผู้ใช้");
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors,"จำเป็นต้องระบุอีเมล");
    }
    if(empty($password_1)){
        array_push($errors,"จำเป็นต้องมีรหัสผ่าน");
    }
    if($password_1 != $password_2){
        array_push($errors,"รหัสผ่านไม่ตรงกัน");
    }
    $user_check_query = "SELECT * FROM user WHERE username = '$username' OR email = '$email' ";
    $query = mysqli_query($conn, $user_check_query);
    $result = mysqli_fetch_assoc($query); 
if($result){
    if($result['username'] === $username){
        array_push($errors,'ชื่อผู้ใช้มีอยู่แล้ว');
    }
    if($result['email'] === $email){
        array_push($errors,'อีเมล์มีอยู่แล้ว');
    }
    
}
    if(count($errors) == 0){
        $password = md5($password_1);
    

        $sql = "INSERT INTO user (username, email, password) VALUES ('$username','$email','$password')";
        mysqli_query($conn,$sql);

        $_SESSION['username'] = $username;
        $_SESSION['success'];
        header('location: index.php');
        exit();
    }else{
        array_push($errors, "มีชื่อผู้ใช้/อีเมล์อยู่แล้ว");
        $_SESSION['error'] = 'มีชื่อผู้ใช้/อีเมล์อยู่แล้ว';
        header('location: register.php');
        exit();
}
 }  
 
?>