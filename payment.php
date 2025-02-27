<?php
if ($stmt->execute()) {
    echo "<script>alert('อัปเดตสินค้าสำเร็จ!'); window.location.href='payment.php?id=$product_id';</script>";
    exit();
} else {
    echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตสินค้า');</script>";
}
$conn->set_charset("utf8mb4");
?>