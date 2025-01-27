<?php
session_start();
include("server.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .card {
            margin: 50px auto;
            padding: 30px;
            max-width: 500px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-outline-danger {
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            color: #dc3545;
            font-weight: bold;
        }
        .form-label {
            font-weight: bold;
            color: #495057;
        }
        .error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card bg-white">
        <div class="header">
            <h2>สมัครสมาชิก</h2>
        </div>
        <form action="register_db.php" method="post">
            <?php include("errors.php"); ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger text-center">
                    <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif ?>

            <!-- ชื่อ - นามสกุล -->
            <div class="mb-3">
                <label for="username" class="form-label">ชื่อ - นามสกุล</label>
                <input type="text" name="username" class="form-control" placeholder="กรอกชื่อ-นามสกุล" required>
            </div>

            <!-- อีเมล์ -->
            <div class="mb-3">
                <label for="email" class="form-label">อีเมล์</label>
                <input type="email" name="email" class="form-control" placeholder="กรอกอีเมล์" required>
            </div>

            <!-- รหัสผ่าน -->
            <div class="mb-3">
                <label for="password_1" class="form-label">รหัสผ่าน</label>
                <input type="password" name="password_1" class="form-control" placeholder="กรอกรหัสผ่าน" required>
            </div>

            <!-- ยืนยันรหัสผ่าน -->
            <div class="mb-3">
                <label for="password_2" class="form-label">ยืนยันรหัสผ่าน</label>
                <input type="password" name="password_2" class="form-control" placeholder="ยืนยันรหัสผ่าน" required>
            </div>

            <!-- ปุ่มสมัครสมาชิก -->
            <button type="submit" name="reg_user" class="btn btn-outline-danger">สมัครสมาชิก</button>

            <!-- ลิงก์เข้าสู่ระบบ -->
            <p class="text-center mt-3">
                คุณเป็นสมาชิกอยู่แล้ว? <a href="login.php" class="text-danger">เข้าสู่ระบบ</a>
            </p>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
