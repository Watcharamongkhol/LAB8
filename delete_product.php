<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);

    // ตรวจสอบว่ามีสินค้านี้ในฐานข้อมูลหรือไม่
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "ลบสินค้าเรียบร้อยแล้ว";
    } else {
        $_SESSION['message'] = "เกิดข้อผิดพลาด ไม่สามารถลบสินค้าได้";
    }
    
    $stmt->close();
    $conn->close();

    header("Location: index.php"); // กลับไปที่หน้ารายการสินค้า
    exit();
}
?>