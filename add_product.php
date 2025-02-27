<?php
session_start();
include 'config.php';

if (isset($_POST['submit'])) {
    // รับข้อมูลจากแบบฟอร์ม
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $description = $_POST['description'];  // รับคำอธิบายสินค้า

    // ตรวจสอบว่าข้อมูลครบถ้วนหรือไม่
    if (!empty($name) && !empty($price) && !empty($image) && !empty($description)) {
        // เตรียมคำสั่ง SQL สำหรับเพิ่มสินค้า
        $stmt = $conn->prepare("INSERT INTO products (name, price, image, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $price, $image, $description);  // แบบพารามิเตอร์คือ String

        // ตรวจสอบผลการทำงานของ SQL
        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มสินค้าสำเร็จ'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มสินค้า');</script>";
        }
    } else {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้า</title>
    <!-- ใช้ Bootstrap สำหรับดีไซน์ที่สวยงาม -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Arial', sans-serif;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
            max-width: 600px;
        }

        h1 {
            color: #4CAF50;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control, .form-select {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 8px;
            padding: 10px 20px;
            width: 100%;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: bold;
            text-align: center;
            width: 100%;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        small {
            font-style: italic;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>เพิ่มสินค้าใหม่</h1>
    <form action="add_product.php" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">ชื่อสินค้า</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">ราคา (บาท)</label>
            <input type="number" class="form-control" id="price" name="price" required min="0" step="0.01">
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">ลิงก์รูปสินค้า</label>
            <input type="text" class="form-control" id="image" name="image" required>
            <small class="form-text text-muted">กรุณาใช้ URL ของรูปภาพที่ต้องการ (สามารถใช้ลิงก์จากเว็บไซต์)</small>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">คำอธิบายสินค้า</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            <small class="form-text text-muted">กรุณากรอกรายละเอียดของสินค้า</small>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">เพิ่มสินค้า</button>
        <a href="index.php" class="btn btn-secondary mt-3">กลับไปหน้าหลัก</a>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>