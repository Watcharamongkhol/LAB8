<?php
session_start();

// ตรวจสอบว่ามีข้อมูลตะกร้าหรือไม่
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}
$conn->set_charset("utf8mb4");
// สมมติว่าการชำระเงินสำเร็จ
unset($_SESSION['cart']); // เคลียร์ตะกร้าหลังจากชำระเงินสำเร็จ

// ใช้ header ก่อนการแสดงผล
header("Location: index.php");
exit();

?>