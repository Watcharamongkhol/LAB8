<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
include 'config.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการส่งฟอร์ม POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าที่ส่งจากฟอร์ม และป้องกัน XSS
    $name = htmlspecialchars(trim($_POST['name']));
    $address = htmlspecialchars(trim($_POST['address']));
    $payment = htmlspecialchars(trim($_POST['payment']));

    // ตรวจสอบว่า fields ที่จำเป็นไม่ได้ว่างเปล่า
    if (empty($name) || empty($address) || empty($payment)) {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน');</script>";
    } else {
        // บันทึกข้อมูลลงฐานข้อมูล
        $stmt = $conn->prepare("INSERT INTO orders (name, address, payment_method) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $address, $payment);

        if ($stmt->execute()) {
            echo "<script>alert('ซื้อสินค้าสำเร็จ!'); window.location.href = 'index.php';</script>";
            exit();
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูลการซื้อ');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน - KFC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&display=swap');
        body {
            background-color: #fff;
            font-family: 'Kanit', sans-serif;
            text-align: center;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: auto;
            border-top: 5px solid #ff0000;
        }
        h2 {
            color: #ff0000;
            font-weight: bold;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #ff0000;
        }
        .btn-kfc {
            background-color: #ff0000;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 18px;
            border-radius: 5px;
            width: 100%;
        }
        .btn-kfc:hover {
            background-color: #cc0000;
        }
        .logo {
            max-width: 120px;
            display: block;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/bf/KFC_logo.svg/800px-KFC_logo.svg.png" class="logo">
        <h2>🍗 ชำระเงิน</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">ชื่อ</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ที่อยู่</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="payment" class="form-label">วิธีการชำระเงิน</label>
                <select class="form-select" id="payment" name="payment" required>
                    <option value="credit_card">บัตรเครดิต</option>
                    <option value="bank_transfer">โอนผ่านธนาคาร</option>
                    <option value="cash_on_delivery">เก็บเงินปลายทาง</option>
                </select>
            </div>
            <button type="submit" class="btn-kfc">💰 ยืนยันการชำระเงิน</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
