
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
    $id = $_POST['id'];
    $tenSanPham = !empty($_POST['tenSanPham']) ? $_POST['tenSanPham'] : null;
    $gia = !empty($_POST['gia']) ? str_replace(array('.', 'VNĐ'), '', $_POST['gia']) : null; 
    $prefix = !empty($_POST['prefix']) ? $_POST['prefix'] : null;
    $newPrefix = !empty($_POST['newEditPrefix']) ? $_POST['newEditPrefix'] : null;
    $danhMuc = !empty($_POST['danhMuc']) ? $_POST['danhMuc'] : null;
    $currentImage = $_POST['currentImage'];
    $hinhAnh = $currentImage;

    if (!empty($_FILES['hinhAnh']['name'])) {
        $hinhAnh = uploadImage('hinhAnh');
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
            
            $stmt = $conn->prepare("SELECT TenDanhMuc FROM DichVu WHERE prefix = ?");
            $stmt->bind_param("s", $newPrefix);
            $stmt->execute();
            $stmt->bind_result($danhMuc);
            $stmt->fetch();
            $stmt->close();
        }
    }

    $stmt = $conn->prepare("CALL sp_UpdateDichVu(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdsss", $id, $tenSanPham, $gia, $hinhAnh, $prefix, $danhMuc);
    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật sản phẩm thành công!'); window.location.href='index.php';</script>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>