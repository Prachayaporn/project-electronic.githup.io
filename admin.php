
<?php 
session_start();
include("server.php");

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION["username"])) {
    header('location: login.php');
    exit();
}
// ดึงข้อมูลการยืมอุปกรณ์จากฐานข้อมูล (แสดงประวัติทั้งหมด)
$query = "SELECT * FROM borrow"; // ดึงข้อมูลทั้งหมดจากตาราง borrow
$result = mysqli_query($conn, $query);
$borrow_list = mysqli_fetch_all($result, MYSQLI_ASSOC);

// ค้นหาข้อมูลตามชื่อผู้ยืม (กรณีต้องการค้นหาตามชื่อ)
if (isset($_POST['search'])) {
    $borrower_name = $_POST['borrower_name'];

    // ฟิลเตอร์ข้อมูลตามชื่อผู้ยืม
    $borrow_list = array_filter($borrow_list, function ($borrow) use ($borrower_name) {
        return stripos($borrow['borrower_name'], $borrower_name) !== false;
    });
}
// เชื่อมต่อฐานข้อมูลและตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}
// โหลดข้อมูลอุปกรณ์และแผนก
$equipment_query = $conn->prepare("SELECT * FROM equipment");
$equipment_query->execute();
$equipment_result = $equipment_query->get_result();

$department_query = $conn->prepare("SELECT * FROM department");
$department_query->execute();
$department_result = $department_query->get_result();
// ดึงรายการการยืมทั้งหมด (ทั้งยืมและคืนแล้ว)


$result = $conn->query($query);

// ตรวจสอบว่าการ query สำเร็จหรือไม่
if (!$result) {
    die("การดึงข้อมูลไม่สำเร็จ: " . $conn->error);
}

$borrow_list = $result->fetch_all(MYSQLI_ASSOC);

if (isset($_POST['add_department'])) {
    $new_department = mysqli_real_escape_string($conn, $_POST['new_department']);
    
    if (empty($errors)) {
        // ถ้าเป็นการเพิ่มข้อมูลใหม่
        if (!empty($imeg_name)) {
            move_uploaded_file($imeg_tmp, $imeg_location);
        }

        $query = mysqli_query($conn, "INSERT INTO department (new_department) 
        VALUES ('$new_department')");
        
        if ($query) {
            $_SESSION['message'] = 'เพิ่มแผนกสำเร็จ';
            header('location: admin.php');
            exit();
        } else {
            $_SESSION['message'] = 'เพิ่มแผนกไม่สำเร็จ';
            header('location: add.php');
            exit();
        }
    }

    
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการยืมอุปกรณ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .badge {
            font-size: 1rem;
            padding: 0.5em 1em;
        }
        .modal-header {
            background-color: #007bff;
            color: white;
        }
        .modal-footer {
            background-color: #f1f1f1;
        }
        .add-department-icon {
        width: 100px; /* ขนาดของรูปภาพ */
        height: auto; /* ปรับอัตราส่วนของภาพให้คงที่ */
        cursor: pointer; /* แสดงว่าเป็นภาพที่สามารถคลิกได้ */
        align-items: center;
    }
    </style>
</head>
<body>

    <?php include('nev.php'); ?>
    <div class="container">
        <!-- ไอคอนเพิ่มแผนก -->
        <div class="position-relative">
        <img src="img/add-department.jpg" alt="เพิ่มแผนก" class="add-department-icon" data-bs-toggle="modal" data-bs-target="#addDepartmentModal" title="คลิกเพื่อเพิ่มแผนกใหม่">
        <!-- คำอธิบายใต้ภาพ -->
            
                <p>คลิกที่ภาพเพื่อเพิ่มแผนกใหม่ในระบบ</p>
            </div>

            <h4 class="text-center">ประวัติการยืมอุปกรณ์</h4>
        <!-- ฟอร์มค้นหา -->
        <form method="POST" action="" class="mb-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <input type="text" name="borrower_name" class="form-control" placeholder="ค้นหาด้วยชื่อผู้ยืม" value="<?= htmlspecialchars($_POST['borrower_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="search" class="btn btn-primary w-100">ค้นหา</button>
                </div>
            </div>
        </form>

        <!-- ตารางแสดงข้อมูล -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ชื่ออุปกรณ์</th>
                        <th>ชื่อผู้ยืม</th>
                        <th>แผนก</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>วันที่ยืม</th>
                        <th>วันที่คืน</th>
                        <th>จำนวนที่ยืม</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($borrow_list)): ?>
                        <?php foreach ($borrow_list as $borrow): ?>
                            <tr>
                                <td><?= htmlspecialchars($borrow['equipment_name']); ?></td>
                                <td><?= htmlspecialchars($borrow['borrower_name']); ?></td>
                                <td><?= htmlspecialchars($borrow['department']); ?></td>
                                <td><?= htmlspecialchars($borrow['phone']); ?></td>
                                <td><?= htmlspecialchars($borrow['borrow_date']); ?></td>
                                <td><?= $borrow['return_date'] ? htmlspecialchars($borrow['return_date']) : 'ยังไม่คืน'; ?></td>
                                <td><?= htmlspecialchars($borrow['quantity']); ?></td>
                                <td>
                                    <?php if ($borrow['return_date']): ?>
                                        <span class="badge bg-success">คืนแล้ว</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">ยังไม่คืน</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">ไม่มีข้อมูลการยืม</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ฟอร์มเพิ่มแผนก (Modal) -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDepartmentLabel">เพิ่มแผนกใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_department" class="form-label">ชื่อแผนกใหม่:</label>
                            <input type="text" name="new_department" id="new_department" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" name="add_department" class="btn btn-primary">เพิ่มแผนก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
