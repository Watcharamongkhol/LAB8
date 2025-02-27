<?php
session_start();
include 'config.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่งค่า id มาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ไม่พบรหัสสินค้า");
}
$product_id = intval($_GET['id']);


// ดึงข้อมูลสินค้าจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("ไม่พบข้อมูลสินค้า");
}

// อัปเดตข้อมูลสินค้าเมื่อมีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    
    // ตรวจสอบการอัปโหลดรูปภาพใหม่ (ถ้ามีการอัปโหลด)
    if (!empty($_FILES['image']['name'])) {
        $image_name = basename($_FILES['image']['name']);
        $target_path = "uploads/" . $image_name;

        // ตรวจสอบประเภทไฟล์
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            echo "<script>alert('ไฟล์ที่อัปโหลดไม่ใช่รูปภาพที่รองรับ');</script>";
            exit();
        }

        // ตรวจสอบขนาดไฟล์
        $max_size = 2 * 1024 * 1024; // 2MB
        if ($_FILES['image']['size'] > $max_size) {
            echo "<script>alert('ขนาดไฟล์รูปภาพใหญ่เกินไป (สูงสุด 2MB)');</script>";
            exit();
        }

        // อัปโหลดไฟล์
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        } else {
            echo "<script>alert('การอัปโหลดไฟล์ล้มเหลว');</script>";
            exit();
        }
    } else {
        $image_path = $product['image']; // ถ้าไม่ได้อัปโหลดให้ใช้รูปเดิม
    }

    // อัปเดตข้อมูลสินค้าในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sdssi", $name, $price, $description, $image_path, $product_id);
    if ($stmt->execute()) {
        echo "<script>alert('อัปเดตสินค้าสำเร็จ!'); window.location.href='admin_dashboard.php';</script>";
        exit();
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตสินค้า');</script>";
    }
    
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .edit-container {
            max-width: 600px;
            margin: auto;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            width: 100%;
        }
        .img-preview {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5 edit-container">
        <div class="card p-4">
            <h2 class="text-center mb-4">แก้ไขสินค้า</h2>

            <!-- แสดงสินค้าที่อยู่ในตะกร้า -->
            <h3>สินค้าที่อยู่ในตะกร้า</h3>
            <?php
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                echo '<ul class="list-group mb-4">';
                foreach ($_SESSION['cart'] as $cart_item) {
                    echo '<li class="list-group-item d-flex justify-content-between">';
                    echo '<span>' . htmlspecialchars($cart_item['name']) . ' (x' . $cart_item['quantity'] . ')</span>';
                    echo '<span>' . number_format($cart_item['price'], 2) . ' บาท</span>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="text-muted">ไม่มีสินค้าในตะกร้า</p>';
            }
            ?>

            <form method="post" enctype="multipart/form-data">
                <!-- ฟอร์มแก้ไขสินค้า -->
                <div class="mb-3">
                    <label class="form-label">ชื่อสินค้า</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ราคา</label>
                    <input type="number" name="price" class="form-control" step="0.01" value="<?php echo $product['price']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รายละเอียดสินค้า</label>
                    <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">อัปโหลดรูปภาพใหม่</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="mb-3 text-center">
                    <p class="text-muted">รูปปัจจุบัน</p>
                    <img src="<?php echo $product['image']; ?>" class="img-preview" width="150">
                </div>
                <button type="submit" class="btn btn-primary btn-custom">บันทึกการเปลี่ยนแปลง</button>
                <a href="admin_dashboard.php" class="btn btn-secondary btn-custom mt-2">ยกเลิก</a>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>