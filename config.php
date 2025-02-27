<?php
$servername = "localhost";
$username = "its66040233128";  // หรือชื่อผู้ใช้ของคุณ
$password = "F4xgN9J5";      // หรือรหัสผ่านของคุณ
$dbname = "its66040233128";   // หรือชื่อฐานข้อมูลของคุณ

header('Content-Type: text/html; charset=utf-8');

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ตั้งค่าการเข้ารหัส UTF-8 เพื่อรองรับภาษาไทย
$conn->set_charset("utf8mb4");
?>