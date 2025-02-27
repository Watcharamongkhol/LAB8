<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
include 'config.php';

if (isset($_POST['buy_now'])) {
    // รับค่า product_id จากฟอร์ม
    $product_id = $_POST['product_id'];

    // ตรวจสอบว่า product_id มีค่าหรือไม่
    if (empty($product_id)) {
        echo "<script>alert('ไม่พบรหัสสินค้า'); window.location.href = 'index.php';</script>";
        exit();
    }

    // ตรวจสอบว่ามีตะกร้าหรือยัง
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // เพิ่มสินค้าลงในตะกร้า
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 1; // เริ่มต้นจำนวน 1 ชิ้น
    } else {
        // หากมีสินค้าในตะกร้าแล้ว เพิ่มจำนวนขึ้น
        $_SESSION['cart'][$product_id]++;
    }

    // แสดง Alert แล้ว Redirect ไป checkout.php
    echo "<script>
        alert('🎉 ซื้อสำเร็จ! กำลังไปหน้าชำระเงิน...');
        window.location.href = 'checkout.php';
    </script>";
    exit();
}
$conn->set_charset("utf8mb4");
?>