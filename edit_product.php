<?php
session_start();
include 'config.php';

// เปิดโหมดแสดงข้อผิดพลาดของ PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตั้งค่าการเชื่อมต่อให้รองรับ UTF-8
mysqli_set_charset($conn, "utf8");

// ตรวจสอบว่ามีการส่งค่า id มาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ไม่พบรหัสสินค้า");
}

$product_id = intval($_GET['id']);

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$stmt = $conn->prepare("SELECT id, name, price, description, image FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($id, $name, $price, $description, $image);
$stmt->fetch();
$stmt->close();

if (!$id) {
    die("ไม่พบข้อมูลสินค้า");
}

// อัปเดตข้อมูลสินค้าเมื่อมีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $image_path = $image; // ใช้รูปเดิมเป็นค่าเริ่มต้น

    // ตรวจสอบและอัปโหลดรูปภาพใหม่ (ถ้ามี)
    if (!empty($_FILES['image']['name'])) {
        $image_name = basename($_FILES['image']['name']);
        $target_dir = "uploads/";
        $target_path = $target_dir . time() . "_" . $image_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        } else {
            echo "<p style='color:red;'>อัปโหลดรูปภาพล้มเหลว</p>";
        }
    } elseif (!empty($_POST['image_url'])) {
        $image_path = trim($_POST['image_url']); // ใช้ URL รูปภาพที่ป้อน
    }

    // อัปเดตข้อมูลสินค้าในฐานข้อมูล
    $update_stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, image=? WHERE id=?");
    $update_stmt->bind_param("sdssi", $name, $price, $description, $image_path, $product_id);
    
    if ($update_stmt->execute()) {
        echo "<p style='color:green;'>อัปเดตข้อมูลสำเร็จ!</p>";
    } else {
        echo "<p style='color:red;'>เกิดข้อผิดพลาดในการอัปเดต: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า KFC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&display=swap');

        body {
            font-family: 'Kanit', sans-serif;
            background-color: #fff;
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

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
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

        img {
            max-width: 100%;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .logo {
            max-width: 120px;
            display: block;
            margin: auto;
        }

        .back-button {
            background-color: #333;
            color: white;
            padding: 10px;
            display: inline-block;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        .back-button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/bf/KFC_logo.svg/800px-KFC_logo.svg.png" class="logo">
        <h2>✏️ แก้ไขสินค้า</h2>

        <form method="post" enctype="multipart/form-data">
            <label>ชื่อสินค้า:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required>
            
            <label>ราคา (บาท):</label>
            <input type="number" step="0.01" name="price" value="<?= $price ?>" required>
            
            <label>รายละเอียด:</label>
            <textarea name="description" required><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></textarea>
            
            <label>รูปภาพปัจจุบัน:</label>
            <br>
            <img src="<?= $image ?>" alt="รูปสินค้า">
            <br>
            
            <label>อัปโหลดรูปภาพใหม่:</label>
            <input type="file" name="image">
            
            <label>หรือใส่ลิงก์รูปภาพ:</label>
            <input type="text" name="image_url" placeholder="ใส่ URL รูปภาพ" value="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>">
            
            <button type="submit" class="btn-kfc">💾 บันทึกการแก้ไข</button>
        </form>

        <a href="index.php" class="back-button">⬅️ กลับไปหน้าหลัก</a>
    </div>
</body>
</html>
