<?php
session_start();
include("server.php");

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION["username"])) {
    $_SESSION['msg'] = "คุณต้องเข้าสู่ระบบก่อน";
    header('location: login.php');
    exit();
}

// ฟังก์ชันออกจากระบบ
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header('location: login.php');
    exit();
}

// โหลดข้อมูลอุปกรณ์และแผนก
$equipment_query = $conn->prepare("SELECT * FROM equipment");
$equipment_query->execute();
$equipment_result = $equipment_query->get_result();

$department_query = $conn->prepare("SELECT * FROM department");
$department_query->execute();
$department_result = $department_query->get_result();

// การจัดการการยืมอุปกรณ์
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['borrow'])) {
    $borrow_id = uniqid();
    $borrower_name = trim($_POST['borrower_name']);
    $borrow_date = $_POST['borrow_date'] . " " . $_POST['borrow_time'];
    $return_date = $_POST['return_date'] . " " . $_POST['return_time'];
    $phone = trim($_POST['phone']);
    $equipment_id = $_POST['equipment_id'] ?? null;
    $quantity = (int) ($_POST['quantity'] ?? 1);
    $department = $_POST['new_department'];
   

    // ตรวจสอบว่าผู้ใช้เลือกแผนกใหม่
    $department = $_POST['department'];
$new_department = trim($_POST['new_department'] ?? '');

if ($department === 'new' && !empty($new_department)) {
    // ตรวจสอบว่ามีแผนกนี้อยู่แล้วหรือไม่
    $check_department = $conn->prepare("SELECT * FROM department WHERE name = ?");
    $check_department->bind_param("s", $new_department);
    $check_department->execute();
    $result = $check_department->get_result();

    if ($result->num_rows === 0) {
        // เพิ่มแผนกใหม่ถ้ายังไม่มี
        $insert_department = $conn->prepare("INSERT INTO department (name) VALUES (?)");
        $insert_department->bind_param("s", $new_department);
        $insert_department->execute();
    }
    $department = $new_department;
}

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($borrower_name) || empty($department) || empty($borrow_date) || empty($return_date) || empty($phone) ||  empty($quantity) || $quantity < 1) {
        $_SESSION['message'] = 'ข้อมูลไม่ครบถ้วน กรุณาลองใหม่!';
        header('location: borrow.php');
        exit();
    }

    if (strtotime($borrow_date) >= strtotime($return_date)) {
        $_SESSION['message'] = 'เวลาคืนต้องมากกว่าเวลาที่ยืม!';
        header('location: borrow.php');
        exit();
    }

    $equipment_id = $_POST['equipment_name'] ?? null;

    // ตรวจสอบจำนวนอุปกรณ์
    $query = $conn->prepare("SELECT available, name_em FROM equipment WHERE num_em = ?");
    $query->bind_param("s", $equipment_id);
    $query->execute();
    $result = $query->get_result();
    $equipment = $result->fetch_assoc();
    
    if (!$equipment || $equipment['available'] < $quantity) {
        $_SESSION['message'] = 'จำนวนอุปกรณ์ไม่เพียงพอ!';
        header('location: borrow.php');
        exit();
    }
    

    // บันทึกข้อมูลการยืม
    $insert_stmt = $conn->prepare("INSERT INTO borrow (borrower_name, department, equipment_id, equipment_name, borrow_date, return_date, phone, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("sssssssi", $borrower_name, $department, $equipment_id, $equipment['name_em'], $borrow_date, $return_date, $phone, $quantity);
    if (!$insert_stmt->execute()) {
        $_SESSION['message'] = 'เกิดข้อผิดพลาดในการบันทึกการยืม!';
        header('location: borrow.php');
        exit();
    }

    // อัปเดตจำนวนอุปกรณ์
    $update_stmt = $conn->prepare("UPDATE equipment SET available = available - ? WHERE num_em = ?");
$update_stmt->bind_param("is", $quantity, $equipment_id);
if (!$update_stmt->execute()) {
    $_SESSION['message'] = 'เกิดข้อผิดพลาดในการอัปเดตจำนวนอุปกรณ์!';
    header('location: borrow.php');
    exit();
}

    $_SESSION['receipt'] = [
        'borrower_name' => $borrower_name,
        'name_em' => $equipment['name_em'],
        'borrow_date' => $borrow_date,
        'return_date' => $return_date,
        'quantity' => $quantity
    ];

    header('location: borrow.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืมอุปกรณ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-content {
            border-radius: 8px;
        }
    </style>
</head>
<body>

<?php include("nev.php"); ?>

<div class="container mt-4">
    <form method="POST" class="form-container" onsubmit="return confirmBorrow();">
        <h2 class="text-center mb-4">ฟอร์มการยืมอุปกรณ์</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-warning"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="borrower_name">ชื่อผู้ยืม</label>
            <input type="text" name="borrower_name" class="form-control" required>
        </div>


        <div class="mb-3">
            <label for="department">แผนก</label>
            <select name="department" class="form-select" id="department">
                <option value="">-- กรุณาเลือกแผนก --</option>
                <?php while ($row = $department_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['new_department']); ?>"><?= htmlspecialchars($row['new_department']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="equipment_name">เลือกอุปกรณ์</label>
            <select name="equipment_name" class="form-select" required>
                <option value="">-- กรุณาเลือกอุปกรณ์ --</option>
                <?php while ($row = $equipment_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['num_em']); ?>" data-available="<?= htmlspecialchars($row['available']); ?>">
                        <?= htmlspecialchars($row['name_em']); ?> (คงเหลือ: <?= htmlspecialchars($row['available']); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity">จำนวน</label>
            <input type="number" name="quantity" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label for="borrow_date">วันที่และเวลายืม</label>
            <div class="input-group">
                <input type="date" name="borrow_date" class="form-control" required>
                <input type="time" name="borrow_time" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="return_date">วันที่และเวลาคืน</label>
            <div class="input-group">
                <input type="date" name="return_date" class="form-control" required>
                <input type="time" name="return_time" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="phone">หมายเลขโทรศัพท์</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <button type="submit" name="borrow" class="btn btn-primary" onclick="return confirmBorrow()">ยืมอุปกรณ์</button>
    </form>
    <script>
    function confirmBorrow()  {
        return confirm("คุณแน่ใจหรือไม่ว่าต้องการยืมอุปกรณ์นี้?");
    }
</script>
</div>

<!-- ใบเสร็จ -->
<?php if (isset($_SESSION['receipt'])): ?>
<div class="modal fade show" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel">ใบเสร็จการยืมอุปกรณ์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">รายการ</th>
                            <th scope="col">รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ชื่อผู้ยืม</td>
                            <td><?= htmlspecialchars($_SESSION['receipt']['borrower_name']); ?></td>
                        </tr>
                        <tr>
                            <td>อุปกรณ์</td>
                            <td><?= htmlspecialchars($_SESSION['receipt']['name_em']); ?></td>
                        </tr>
                        <tr>
                            <td>วันที่และเวลายืม</td>
                            <td><?= htmlspecialchars($_SESSION['receipt']['borrow_date']); ?></td>
                        </tr>
                        <tr>
                            <td>วันที่และเวลาคืน</td>
                            <td><?= htmlspecialchars($_SESSION['receipt']['return_date']); ?></td>
                        </tr>
                        <tr>
                            <td>จำนวน</td>
                            <td><?= htmlspecialchars($_SESSION['receipt']['quantity']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
<script>
    var myModal = new bootstrap.Modal(document.getElementById('receiptModal'));
    myModal.show();
</script>
<?php unset($_SESSION['receipt']); endif; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
