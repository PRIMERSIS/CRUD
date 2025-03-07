<?php
include 'upload.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sapovn";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenSanPham = $_POST['tenSanPham'];
    $gia = str_replace(['.', ' VNĐ'], '', $_POST['gia']); 
    $prefix = $_POST['prefix'];
    $newPrefix = $_POST['newPrefix'];
    $danhMuc = $_POST['danhMuc'];

    
    $stmt = $conn->prepare("SELECT COUNT(*) FROM DichVu WHERE Ten = ?");
    $stmt->bind_param("s", $tenSanPham);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "<script>alert('Tên sản phẩm đã tồn tại!'); window.location.href='index.php';</script>";
        exit;
    }

    if (!empty($newPrefix)) {
        
        $stmt = $conn->prepare("SELECT COUNT(*) FROM DichVu WHERE prefix = ?");
        $stmt->bind_param("s", $newPrefix);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            $prefix = $newPrefix;
        } else {
            echo "<script>alert('Mã Prefix đã tồn tại!'); window.location.href='index.php';</script>";
            exit;
        }
    }

    
    $hinhAnh = '';
    if (!empty($_FILES['hinhAnh']['name'])) {
        $hinhAnh = uploadImage('hinhAnh');
    }

    $stmt = $conn->prepare("CALL sp_InsertDichVu(?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsss", $tenSanPham, $gia, $hinhAnh, $prefix, $danhMuc);
    if ($stmt->execute()) {
        echo "<script>alert('Thêm sản phẩm thành công!'); window.location.href='index.php';</script>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>