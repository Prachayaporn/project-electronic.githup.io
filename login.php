<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- ใช้ไอคอนจาก Bootstrap -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .card {
            margin: 50px auto;
            padding: 30px;
            max-width: 400px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header h2 {
            text-align: center;
            color: #dc3545;
            font-weight: bold;
        }
        .form-label {
            font-weight: bold;
            color: #495057;
        }
        .btn-danger {
            width: 100%;
        }
        .error {
            color: #dc3545;
            font-size: 0.9rem;
            text-align: center;
        }
        .text-center a {
            text-decoration: none;
            color: #dc3545;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card bg-white">
            <!-- Header -->
            <div class="header">
                <h2>เข้าสู่ระบบ</h2>
            </div>
            <!-- Login Form -->
            <form action="login_db.php" method="post">
                <!-- Error Message -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger text-center">
                        <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif ?>

                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label">ชื่อ - นามสกุล</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="กรอกชื่อผู้ใช้งาน" required>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="กรอกรหัสผ่าน" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="login_user" class="btn btn-danger">เข้าสู่ระบบ</button>

                <!-- Registration Link -->
                <p class="text-center mt-3">คุณยังไม่ได้เป็นสมาชิก? <a href="register.php">สมัครสมาชิก</a></p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
