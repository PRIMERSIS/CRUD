<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sapovn";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

   
    $stmt = $conn->prepare("CALL sp_DeleteDichVu(?)");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Xóa sản phẩm thành công!'); window.location.href='index.php';</script>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>