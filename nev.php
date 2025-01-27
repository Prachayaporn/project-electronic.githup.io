<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบยืมคืนอุปกรณ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- ใช้ไอคอนจาก Bootstrap -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin-left: 250px; /* เว้นที่สำหรับแถบด้านข้าง */
            background-color: #73dcdf; /* ตั้งค่าพื้นหลังเป็นสีฟ้าอ่อน */
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #343a40;
            padding: 20px;
            color: white;
            overflow-y: auto;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar h2 {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px;
            background-color: #ffffff; /* เพิ่มพื้นหลังสีขาวให้กับเนื้อหา */
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>ระบบยืมคืนอุปกรณ์</h2>
        <a href="index.php">
            <i class="bi bi-house-door"></i> หน้าแรก
        </a>
        <a href="borrow.php">
            <i class="bi bi-archive"></i> ยืมอุปกรณ์
        </a>
        <a href="return.php">
            <i class="bi bi-arrow-return-left"></i> คืนอุปกรณ์
        </a>
        <a href="add.php" >
        <i class="bi bi-plus-circle">เพิ่มอุปกรณ์</i> <!-- ใช้ไอคอนปุ่มเพิ่ม -->
        </a>
        <a href="admin.php">
            <i class="bi bi-person-circle"></i> Admin
        </a>
        <a href="login.php">
            <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
        </a>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>
