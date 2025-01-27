<?php
session_start();
include("server.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrow_id = intval($_POST['borrow_id']);
    $equipment_id = intval($_POST['equipment_id']);
    $quantity = intval($_POST['quantity']);

    // Validate inputs
    if (empty($borrow_id) || empty($equipment_id) || empty($quantity)) {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit();
    }

    // Set return date
    $return_date = date('Y-m-d H:i:s');

    // Update borrow table
    $update_borrow_query = $conn->prepare("UPDATE borrow SET return_date = ?, status = 'returned' WHERE borrow_id = ?");
    $update_borrow_query->bind_param("si", $return_date, $borrow_id);
    $update_borrow_query->execute();

    if ($update_borrow_query->affected_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตข้อมูลการยืม']);
        exit();
    }

    // Update equipment table
    $update_equipment_query = $conn->prepare("UPDATE equipment SET available = available + ? WHERE num_em = ?");
    $update_equipment_query->bind_param("ii", $quantity, $equipment_id);
    $update_equipment_query->execute();

    if ($update_equipment_query->affected_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตข้อมูลอุปกรณ์']);
        exit();
    }

    // Send success response
    echo json_encode(['success' => true, 'message' => 'คืนอุปกรณ์สำเร็จ!']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คืนอุปกรณ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <?php include("nev.php"); ?>

    <div class="container mt-4">
        <h2 class="mb-4">รายการการยืมที่ยังไม่ได้คืน</h2>
        <div id="borrow-list">
            <?php
            // Fetch borrow list excluding returned items
            $query = "
                SELECT 
                    borrow.borrow_id, 
                    borrow.borrower_name, 
                    borrow.department, 
                    borrow.phone, 
                    borrow.borrow_date, 
                    borrow.quantity, 
                    borrow.equipment_id,
                    borrow.status,
                    equipment.name_em AS equipment_name
                FROM 
                    borrow
                INNER JOIN 
                    equipment 
            "; 

            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result === false) {
                die("SQL Error: " . $conn->error);
            }

            $borrow_list = $result->fetch_all(MYSQLI_ASSOC);
            ?>

            <?php if (!empty($borrow_list)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ชื่ออุปกรณ์</th>
                            <th>ชื่อผู้ยืม</th>
                            <th>แผนก</th>
                            <th>เบอร์โทรศัพท์</th>
                            <th>วันที่ยืม</th>
                            <th>จำนวนที่ยืม</th>
                            <th>การคืน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($borrow_list as $borrow): ?>
                            <tr id="row-<?= $borrow['borrow_id']; ?>">
                                <td><?= htmlspecialchars($borrow['equipment_name']); ?></td>
                                <td><?= htmlspecialchars($borrow['borrower_name']); ?></td>
                                <td><?= htmlspecialchars($borrow['department']); ?></td>
                                <td><?= htmlspecialchars($borrow['phone']); ?></td>
                                <td><?= htmlspecialchars($borrow['borrow_date']); ?></td>
                                <td><?= htmlspecialchars($borrow['quantity']); ?></td>
                                <td>
                                    <button 
                                        class="btn btn-success return-btn" 
                                        data-borrow-id="<?= $borrow['borrow_id']; ?>"
                                        data-equipment-id="<?= $borrow['equipment_id']; ?>"
                                        data-quantity="<?= $borrow['quantity']; ?>"
                                    >
                                        คืน
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">ไม่มีรายการการยืมที่ยังไม่ได้คืน</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        $(document).on('click', '.return-btn', function() {
            const borrowId = $(this).data('borrow-id');
            const equipmentId = $(this).data('equipment-id');
            const quantity = $(this).data('quantity');

            if (confirm('คุณต้องการคืนอุปกรณ์นี้ใช่หรือไม่?')) {
                $.ajax({
                    url: 'process_return.php',
                    method: 'POST',
                    data: {
                        borrow_id: borrow_id,
                        equipment_id: equipment_id,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reload the page to reflect the changes
                            location.reload();
                        } else {
                            // Display error message
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('เกิดข้อผิดพลาดในการคืนอุปกรณ์');
                    }
                });
            }
        });
    </script>
</body>
</html>