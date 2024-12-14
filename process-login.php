<?php
session_start();
include('connect.php'); // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่าฟิลด์ email และ password ถูกส่งมาหรือไม่
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        echo "<script>alert('Email or password is missing. Please try again.'); window.history.back();</script>";
        exit;
    }

    // รับค่าจากฟอร์ม
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ตรวจสอบรูปแบบอีเมล
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format. Please try again.'); window.history.back();</script>";
        exit;
    }

    // ตรวจสอบว่ากรอกข้อมูลครบถ้วน
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in both email and password.'); window.history.back();</script>";
        exit;
    }

    try {
        // ตรวจสอบการเชื่อมต่อฐานข้อมูล
        if (!$pdo) {
            throw new Exception("Database connection failed.");
        }

        // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $stmt = $pdo->prepare("SELECT * FROM customer WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // ตรวจสอบรหัสผ่าน
            if (password_verify($password, $user['password'])) { // ใช้ password_verify สำหรับรหัสผ่านที่เข้ารหัส
                // บันทึกข้อมูลใน session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];

                // เปลี่ยนเส้นทางไปยังหน้า Home
                header("Location: /home.php");
                exit;
            } else {
                echo "<script>alert('Invalid email or password. Please try again.'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Invalid email or password. Please try again.'); window.history.back();</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "'); window.history.back();</script>";
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
        exit;
    }
}
?>
