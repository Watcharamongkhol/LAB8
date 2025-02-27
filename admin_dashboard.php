<?php
if ($stmt->execute()) {
    // แสดงข้อความแจ้งเตือนเมื่ออัปเดตสินค้าเสร็จสมบูรณ์
    echo "<script>alert('อัปเดตสินค้าสำเร็จ!'); window.location.href='index.php';</script>";
} else {
    // แสดงข้อความแจ้งเตือนเมื่อเกิดข้อผิดพลาดในการอัปเดต
    echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตสินค้า');</script>";
    $conn->set_charset("utf8mb4");
}
?>
