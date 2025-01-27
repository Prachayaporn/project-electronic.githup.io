<?php
session_start();
include('server.php');

// ตรวจสอบว่าได้รับ id_em หรือไม่
if (isset($_GET['equipment_id'])) {
    $equipment_id = $_GET['equipment_id'];

    // ดึงข้อมูลจากฐานข้อมูล
    $query = mysqli_query($conn, "SELECT * FROM equipment WHERE equipment_id = '$equipment_id'");
    $equipment = mysqli_fetch_assoc($query);

    if (!$equipment) {
        $_SESSION['message'] = 'ไม่พบข้อมูลอุปกรณ์';
        header('location: add.php');
        exit();
    }
}

// ตรวจสอบการอัปเดตข้อมูล
if (isset($_POST['update_em'])) {
    $name_em = mysqli_real_escape_string($conn, $_POST['name_em']);
    $num_em = mysqli_real_escape_string($conn, $_POST['num_em']);
    $available = mysqli_real_escape_string($conn, $_POST['available']);
    
    $imeg_name = $_FILES['imeg_em']['name'];
    $imeg_tmp = $_FILES['imeg_em']['tmp_name'];
    $folder = 'em_photo/';
    $imeg_location = $folder . $imeg_name;

    // ตรวจสอบข้อมูลที่ต้องการ
    if (empty($name_em)) {
        $_SESSION['message'] = "กรุณาใส่ชื่ออุปกรณ์";
    }

    if (empty($num_em)) {
        $_SESSION['message'] = "กรุณาใส่หมายเลขอุปกรณ์";
    }

    if (empty($_SESSION['message'])) {
        // ถ้ามีการอัปโหลดรูปภาพใหม่
        if (!empty($imeg_name)) {
            // ลบรูปภาพเก่าหากมี
            $old_imeg = $equipment['imeg_em'];
            if (!empty($old_imeg)) {
                unlink($folder . $old_imeg);
            }
            move_uploaded_file($imeg_tmp, $imeg_location);
        } else {
            // หากไม่มีการอัปโหลดรูปภาพใหม่
            $imeg_name = $equipment['imeg_em'];
        }

        // อัปเดตข้อมูลในฐานข้อมูล
        $update_query = mysqli_query($conn, "UPDATE equipment SET name_em = '$name_em', num_em = '$num_em', available = '$available', imeg_em = '$imeg_name' WHERE equipment_id = '$equipment_id'");

        if ($update_query) {
            $_SESSION['message'] = 'อุปกรณ์ถูกอัปเดตเรียบร้อยแล้ว';
            header('location: add.php');
            exit();
        } else {
            $_SESSION['message'] = 'ไม่สามารถอัปเดตอุปกรณ์ได้';
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
    <title>แก้ไขอุปกรณ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>
<body>
<?php include("nev.php"); ?>
<div class="container">
    <h2 class="text-center">แก้ไขอุปกรณ์</h2>

    <!-- ฟอร์มแก้ไขอุปกรณ์ -->
    <form action="edit.php?equipment_id=<?php echo $equipment['equipment_id']; ?>" method="post" enctype="multipart/form-data">
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-warning">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="name_em">ชื่ออุปกรณ์</label>
            <input type="text" name="name_em" id="name_em" value="<?php echo $equipment['name_em']; ?>" required>
        </div>

        <div class="form-group">
            <label for="num_em">เลขอุปกรณ์</label>
            <input type="text" name="num_em" id="num_em" value="<?php echo $equipment['num_em']; ?>" required>
        </div>

        <div class="form-group">
            <label for="available">จำนวน</label>
            <input type="number" name="available" id="available" value="<?php echo $equipment['available']; ?>" required>
        </div>

        <div class="form-group">
            <label for="imeg_em">รูปภาพอุปกรณ์ (ถ้ามี)</label>
            <input type="file" name="imeg_em" id="imeg_em" accept="image/png, image/jpeg, image/jpg">
            <img src="em_photo/<?php echo $equipment['imeg_em']; ?>" alt="รูปภาพอุปกรณ์" width="100" height="100">
        </div>

        <button type="submit" name="update_em" class="btn-custom" onclick="return confirmBorrow()">อัปเดตอุปกรณ์</button>
    </form>
    <script>
    function confirmBorrow() {
        return confirm("คุณแน่ใจหรือไม่ว่าต้องการอัปเดตอุปกรณ์นี้?");
    }
</script>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
