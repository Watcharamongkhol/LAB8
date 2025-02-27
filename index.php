<?php
session_start();
include 'config.php';
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
$conn->set_charset('utf8mb4');
$result = $conn->query("SELECT * FROM products");
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff; /* พื้นหลังสีขาว */
            font-family: 'Sarabun', sans-serif;
        }
        .container {
            max-width: 1200px;
            margin-top: 50px;
        }
        .product-card {
            border: none;
            border-radius: 15px;
            padding: 15px;
            background: #ffffff;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
        }
        .product-card img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            transition: transform 0.3s;
        }
        .product-card img:hover {
            transform: scale(1.05);
        }
        .btn-primary {
            padding: 15px 30px;
            background-color: #ff0000; /* สีแดงของ KFC */
            border: none;
        }
        .btn-primary:hover {
            background-color: #e60000;
        }
        .cart-link {           
            padding: 15px 30px;
            border: 3px solid #ff0000; /* สีแดง */
            border-radius: 15px;
            background: white;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #ff0000;
        }
        .description {
            display: none;
        }
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Kanit:wght@700&display=swap');

        .shop-title {
            display: inline-block;
            padding: 15px 30px;
            border: 3px solid #ff0000; /* สีแดง */
            border-radius: 15px;
            background: white;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            font-size: 40px;
            font-weight: bold;
            color: #ff0000;
            font-family: 'Poppins', 'Kanit', sans-serif;
            position: absolute;
            left: 50%;
            top: 10%;
            transform: translate(-50%, -50%);
        }

        .logo {
            max-width: 100px; /* ขนาดโลโก้ */
            margin-right: 20px;
        }
        .header-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="header-container">
    <img src="https://upload.wikimedia.org/wikipedia/sco/thumb/b/bf/KFC_logo.svg/1200px-KFC_logo.svg.png" alt="KFC Logo" class="logo">
</div>

<div class="container">
    <br>
    <br>
    <div class="d-flex justify-content-between mb-3">
        <a href="cart.php" class="cart-link">ตะกร้าสินค้า (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
        <a href="add_product.php" class="btn btn-primary">+ เพิ่มสินค้า</a>
    </div>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="img-fluid">
                    <h5 class="mt-2"> <?php echo htmlspecialchars($row['name']); ?> </h5>
                    <p class="text-danger"><strong><?php echo number_format($row['price'], 2); ?> บาท</strong></p>
                    <button class="btn btn-info btn-sm toggle-description" data-id="<?php echo $row['id']; ?>">ดูรายละเอียด</button>
                    <p class="description mt-2" id="desc-<?php echo $row['id']; ?>">
                        <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                    </p>
                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">✏️ แก้ไข</a>
                    <form action="delete_product.php" method="POST" onsubmit="return confirm('คุณต้องการลบสินค้านี้ใช่หรือไม่?');">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-danger mt-2">🗑️ ลบสินค้า</button>
                    </form>
                    <form action="cart.php" method="post" class="mt-2">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-success">🛍️ หยิบใส่ตะกร้า</button>
                    </form>
                    <form action="buy_now.php" method="post" class="mt-2">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="buy_now" class="btn btn-primary">🛒 ซื้อเลย</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <div class="d-flex justify-content-center mt-3">
        <a href="checkout.php" class="btn btn-success w-50">ไปที่การชำระเงิน</a>
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-description').forEach(button => {
        button.addEventListener('click', function() {
            const desc = document.getElementById('desc-' + this.getAttribute('data-id'));
            if (desc.style.display === 'none' || desc.style.display === '') {
                desc.style.display = 'block';
                this.textContent = 'ซ่อนรายละเอียด';
            } else {
                desc.style.display = 'none';
                this.textContent = 'ดูรายละเอียด';
            }
        });
    });
</script>
</body>
</html>
