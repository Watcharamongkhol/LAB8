<?php
session_start();
include 'config.php';

// ตรวจสอบและกำหนดค่าเริ่มต้นให้กับตะกร้า หากยังไม่มีการตั้งค่าไว้
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// เพิ่มสินค้าในตะกร้า
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
}

// อัพเดทตะกร้า
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
}

// ลบสินค้าทั้งหมด
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
}

// ลบสินค้าออกจากตะกร้า (เฉพาะตัวเดียว)
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header("Location: cart.php");
    exit();
}

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$product_ids = array_keys($_SESSION['cart']);
if (!empty($product_ids)) {
    $product_ids_str = implode(',', array_map('intval', $product_ids)); // ป้องกัน SQL Injection
    $result = $conn->query("SELECT * FROM products WHERE id IN ($product_ids_str)");
} else {
    $result = false;
}

// จำนวนสินค้าทั้งหมด
$total_items = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$total_price = 0; // ตัวแปรสำหรับราคารวม
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fff; font-family: 'Arial', sans-serif; }
        .cart-item {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            background: #fff;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .cart-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }
        .cart-link {
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
        }
        .btn-secondary {
            border-radius: 8px;
        }
        .btn-success, .btn-warning, .btn-danger {
            border-radius: 8px;
        }
        .cart-total {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .cart-total span {
            font-size: 1.4rem;
            color: #e30000; /* ใช้สีแดง KFC */
        }
        .cart-item .row {
            display: flex;
            align-items: center;
        }
        .cart-item h5 {
            font-size: 1.1rem;
        }
        .cart-item p {
            font-size: 1rem;
        }

        /* เปลี่ยนสีพื้นหลังและข้อความให้เป็นสีแดง KFC */
        h1 {
            color: #e30000;
        }
        .btn-success {
            background-color: #e30000; /* สีแดง KFC */
            border-color: #e30000;
        }
        .btn-success:hover {
            background-color: #d20000;
            border-color: #d20000;
        }
        .btn-warning {
            background-color: #ffcc00; /* สีเหลืองที่ใช้ในโลโก้ KFC */
            border-color: #ffcc00;
        }
        .btn-warning:hover {
            background-color: #ffb300;
            border-color: #ffb300;
        }
        .btn-danger {
            background-color: #f44336; /* สีแดงแบบทั่วไป */
            border-color: #f44336;
        }
        .btn-danger:hover {
            background-color: #d32f2f;
            border-color: #d32f2f;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">🛒 ตะกร้าสินค้า</h1>
    <div class="d-flex justify-content-between mb-3">
        <a href="index.php" class="btn btn-secondary">← กลับไปหน้าหลัก</a>
    </div>
    <?php if ($total_items == 0): ?>
        <p class="text-center text-muted">ตะกร้าสินค้าของคุณว่างเปล่า</p>
    <?php else: ?>
        <form action="cart.php" method="post">
            <div class="row">
                <?php if ($result): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="cart-item">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <h5><?php echo htmlspecialchars($row['name']); ?></h5>
                                        <p><strong><?php echo number_format($row['price'], 2); ?> บาท</strong></p>
                                        <input type="number" name="quantity[<?php echo $row['id']; ?>]" value="<?php echo $_SESSION['cart'][$row['id']]; ?>" class="form-control" min="1">
                                        <a href="cart.php?remove=<?php echo $row['id']; ?>" class="btn btn-danger mt-2">ลบออกจากตะกร้า</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            // คำนวณราคารวม
                            $total_price += $row['price'] * $_SESSION['cart'][$row['id']];
                        ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <button type="submit" name="update_cart" class="btn btn-warning">อัพเดทตะกร้า</button>
                <button type="submit" name="clear_cart" class="btn btn-danger">ลบทั้งหมด</button>
            </div>
        </form>
        <div class="d-flex justify-content-between mt-4">
            <a href="checkout.php" class="btn btn-success">ไปที่การชำระเงิน</a>
            <div class="cart-total">
                จำนวนสินค้าทั้งหมด: <?php echo $total_items; ?> ชิ้น <br>
                <span>ราคารวม: <?php echo number_format($total_price, 2); ?> บาท</span>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
