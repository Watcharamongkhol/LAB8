<?php
session_start();
include 'config.php';

// ตรวจสอบว่ามี order_id ใน URL หรือไม่
if (!isset($_GET['order_id'])) {
    echo "<script>alert('ไม่มีหมายเลขคำสั่งซื้อ'); window.location.href='index.php';</script>";
    exit();
}

$order_id = (int)$_GET['order_id'];

// ดึงข้อมูลคำสั่งซื้อจากตาราง orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    echo "<script>alert('ไม่พบข้อมูลคำสั่งซื้อ'); window.location.href='index.php';</script>";
    exit();
}

// ดึงรายการสินค้าของคำสั่งซื้อนี้จากตาราง order_details ร่วมกับตาราง products
$stmt_items = $conn->prepare("SELECT od.quantity, od.price, p.name FROM order_details od JOIN products p ON od.product_id = p.id WHERE od.order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ขอบคุณสำหรับการสั่งซื้อ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h1>ขอบคุณสำหรับการสั่งซื้อ!</h1>
  <h3>หมายเลขคำสั่งซื้อ: <?php echo $order['id']; ?></h3>
  <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
  <p><strong>ที่อยู่:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
  <p><strong>โทรศัพท์:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
  <p><strong>ยอดรวม:</strong> <?php echo number_format($order['total'], 2); ?> บาท</p>
  
  <h4>รายการสินค้าที่สั่งซื้อ</h4>
  <table class="table">
    <thead>
      <tr>
        <th>ชื่อสินค้า</th>
        <th>ราคา</th>
        <th>จำนวน</th>
        <th>รวม</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      while ($item = $items_result->fetch_assoc()) {
          $subtotal = $item['price'] * $item['quantity'];
          echo "<tr>
                  <td>" . htmlspecialchars($item['name']) . "</td>
                  <td>" . number_format($item['price'], 2) . " บาท</td>
                  <td>" . $item['quantity'] . "</td>
                  <td>" . number_format($subtotal, 2) . " บาท</td>
                </tr>";
      }
      ?>
    </tbody>
  </table>
  <a href="index.php" class="btn btn-primary">กลับไปหน้าหลัก</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>