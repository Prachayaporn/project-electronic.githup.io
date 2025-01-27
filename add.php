<?php
session_start();
include('server.php');

// ตรวจสอบการเข้าสู่ระบบและการเป็น admin
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header('location: login.php');
    exit();
}
$query = mysqli_query($conn, "SELECT * FROM equipment");
$rows = mysqli_num_rows($query);
$errors = array();

if (isset($_POST['add_em'])) {
    $name_em = mysqli_real_escape_string($conn, $_POST['name_em']);
    $num_em = mysqli_real_escape_string($conn, $_POST['num_em']);
    $available = mysqli_real_escape_string($conn, $_POST['available']);
    
    $imeg_name = $_FILES['imeg_em']['name'];
    $imeg_tmp = $_FILES['imeg_em']['tmp_name'];
    $folder = 'em_photo/';
    $imeg_location = $folder . $imeg_name;

    // ตรวจสอบข้อมูลที่ต้องการ
    if (empty($name_em)) {
        array_push($errors, "กรุณาใส่ชื่ออุปกรณ์");
    }

    if (empty($num_em)) {
        array_push($errors, "กรุณาใส่หมายเลขอุปกรณ์");
    }

    if (empty($errors)) {
        // ถ้าเป็นการเพิ่มข้อมูลใหม่
        if (!empty($imeg_name)) {
            move_uploaded_file($imeg_tmp, $imeg_location);
        }

        $query = mysqli_query($conn, "INSERT INTO equipment (name_em, num_em, available, imeg_em) 
        VALUES ('$name_em', '$num_em', '$available', '$imeg_name')");
        
        if ($query) {
            $_SESSION['message'] = 'เพิ่มอุปกรณ์สำเร็จ';
            header('location: add.php');
            exit();
        } else {
            $_SESSION['message'] = 'เพิ่มอุปกรณ์ไม่สำเร็จ';
            header('location: add.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มอุปกรณ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f8;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #007bff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }
        .form-group input[type="file"] {
            padding: 5px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 1.1rem;
            border: none;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        table td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .btn-edit, .btn-delete {
            padding: 5px 10px;
            font-size: 0.9rem;
            border-radius: 5px;
        }
        .btn-edit {
            background-color: #ffc107;
            color: white;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-edit:hover, .btn-delete:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

<?php include("nev.php"); ?>

<div class="container">
    <div class="header">
        <h2>เพิ่มอุปกรณ์</h2>
    </div>

    <!-- ฟอร์มเพิ่มอุปกรณ์ -->
    <form action="add.php" method="post" enctype="multipart/form-data">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif ?>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-warning">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="name_em">ชื่ออุปกรณ์</label>
            <input type="text" name="name_em" id="name_em" placeholder="กรอกชื่ออุปกรณ์" required>
        </div>

        <div class="form-group">
            <label for="num_em">เลขอุปกรณ์</label>
            <input type="text" name="num_em" id="num_em" placeholder="กรอกเลขอุปกรณ์" required>
        </div>

        <div class="form-group">
            <label for="available">จำนวน</label>
            <input type="number" name="available" id="available" placeholder="กรอกจำนวน" required>
        </div>

        <div class="form-group">
            <label for="imeg_em">รูปภาพอุปกรณ์</label>
            <input type="file" name="imeg_em" id="imeg_em" accept="image/png, image/jpeg, image/jpg">
        </div>

        <button type="submit" name="add_em" class="btn-custom "onclick="return confirmBorrow()">เพิ่มอุปกรณ์</button>
    </form>
    <script>
    function confirmBorrow() {
        return confirm("คุณแน่ใจหรือไม่ว่าต้องการเพิ่มอุปกรณ์นี้?");
    }
</script>
    <!-- ตารางอุปกรณ์ -->
    <table>
        <thead>
            <tr>
                <th>รูปภาพ</th>
                <th>ชื่ออุปกรณ์</th>
                <th>เลขอุปกรณ์</th>
                <th>จำนวน</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($rows > 0): ?>
                <?php while ($equipment = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td>
                        <?php if (!empty($equipment['imeg_em'])): ?>
                                <img src="em_photo/<?php echo htmlspecialchars($equipment['imeg_em']); ?>" width="50" alt="รูปอุปกรณ์">
                            <?php else: ?>
                                <img src="img/no image.png" width="50" alt="ไม่มีรูปภาพ">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $equipment['name_em']; ?></td>
                        <td><?php echo $equipment['num_em']; ?></td>
                        <td><?php echo $equipment['available']; ?></td>
                        <td>
                        <a href="edit.php?equipment_id=<?php echo $equipment['equipment_id']; ?>" class="btn-edit" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการแก้ไขอุปกรณ์นี้?');">แก้ไข</a>
                        <a href="delete.php?id=<?php echo $equipment['equipment_id']; ?>" class="btn-delete" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบอุปกรณ์นี้?');">ลบ</a>

                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-danger">ไม่มีอุปกรณ์</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
