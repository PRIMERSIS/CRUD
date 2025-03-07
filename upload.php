<!-- filepath: /c:/xampp/htdocs/final-ex/upload.php -->
<?php
function uploadImage($inputName)
{
    // Kiểm tra xem có file nào được upload không
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] == UPLOAD_ERR_NO_FILE) {
        die("Error: No image uploaded");
    }

    $name = $_FILES[$inputName]['name'];
    $tmp_name = $_FILES[$inputName]['tmp_name'];
    $fileExt = pathinfo($name, PATHINFO_EXTENSION);
    $fileName = uniqid() . "." . $fileExt;
    $localPath = "uploads/" . $fileName;
    $fileUrl = "http://localhost/final-ex/uploads/" . $fileName; // Đường dẫn URL lưu vào DB

    // Di chuyển file tải lên
    if (move_uploaded_file($tmp_name, $localPath)) {
        return $fileUrl;
    } else {
        die("Error: Failed to upload image");
    }
}
?>