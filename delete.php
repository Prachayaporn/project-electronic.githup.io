<?php
session_start();
include('server.php');

// ตรวจสอบว่าได้รับ id_em หรือไม่
if (isset($_GET['id'])) {
    $id_em = $_GET['id'];

    // ดึงข้อมูลอุปกรณ์เพื่อลบรูปภาพ
    $query = mysqli_query($conn, "SELECT * FROM equipment WHERE equipment_id = '$equipment_id'");
    $equipment = mysqli_fetch_assoc($query);

    // ลบไฟล์รูปภาพถ้ามี
    if ($equipment['imeg_em']) {
        $file_path = 'em_photo/' . $equipment['imeg_em'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // ลบข้อมูลในฐานข้อมูล
    $delete_query = mysqli_query($conn, "DELETE FROM equipment WHERE equipment_id = '$equipment_id'");
    
    if ($delete_query) {
        $_SESSION['message'] = 'อุปกรณ์ถูกลบเรียบร้อยแล้ว';
        header('location: add.php');
        exit();
    } else {
        $_SESSION['message'] = 'ไม่สามารถลบอุปกรณ์ได้';
        header('location: add.php');
        exit();
    }
} else {
    $_SESSION['message'] = 'ไม่พบข้อมูลอุปกรณ์';
    header('location: add.php');
    exit();
}
?>
