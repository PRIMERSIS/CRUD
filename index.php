
<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login/login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sapovn";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
} 


$limit = 5;


$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$result = $conn->query("SELECT COUNT(*) AS total FROM DichVu WHERE DaXoa = 0");
$total = $result->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);


$sql = "SELECT * FROM DichVu WHERE DaXoa = 0 ORDER BY IdDichVu DESC LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .modal-content {
            border-radius: 10px;
        }
        .select-container {
            display: flex;
            align-items: center;
            width: 100%;
        }
        .select-container select {
            flex: 1;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Quản lý sản phẩm</h1>
        
        
        <div class="text-right mb-3">
            <a href="login/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        
    
        <form action="index2.php" method="GET" class="mb-3">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
        </form>
        
        <button class="btn btn-primary mb-3" onclick="document.getElementById('addProductModal').style.display='block'">Thêm sản phẩm</button>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Hình ảnh</th>
                    <th>Mã sản phẩm</th>
                    <th>Prefix</th>
                    <th>Danh mục</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $giaFormatted = number_format($row['Gia'], 0, ',', '.') . ' VNĐ';
                        echo "<tr>
                                <td>{$row['IdDichVu']}</td>
                                <td>{$row['Ten']}</td>
                                <td>{$giaFormatted}</td>
                                <td><img src='{$row['HinhAnh']}' alt='{$row['Ten']}' width='100'></td>
                                <td>{$row['MaSP']}</td>
                                <td>{$row['prefix']}</td>
                                <td>{$row['TenDanhMuc']}</td>
                                <td>
                                    <button class='btn btn-warning' onclick=\"editProduct({$row['IdDichVu']}, '{$row['Ten']}', {$row['Gia']}, '{$row['HinhAnh']}', '{$row['prefix']}', '{$row['TenDanhMuc']}')\">Sửa</button>
                                    <button class='btn btn-danger' onclick=\"deleteProduct({$row['IdDichVu']})\">Xóa</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Không có sản phẩm nào</td></tr>";
                }
                ?>
            </tbody>
        </table>

        
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    
    <div id="addProductModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm sản phẩm</h5>
                    <button type="button" class="close" onclick="document.getElementById('addProductModal').style.display='none'" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="add_product.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="tenSanPham">Tên sản phẩm:</label>
                            <input type="text" class="form-control" id="tenSanPham" name="tenSanPham" required>
                        </div>
                        <div class="form-group">
                            <label for="gia">Giá:</label>
                            <input type="text" class="form-control" id="gia" name="gia" required oninput="formatCurrency(this)">
                        </div>
                        <div class="form-group">
                            <label for="hinhAnh">Hình ảnh:</label>
                            <input type="file" class="form-control" id="hinhAnh" name="hinhAnh" required>
                        </div>
                        <div class="form-group">
                            <label for="prefix">Prefix:</label>
                            <div class="select-container">
                                <select class="form-control" id="prefix" name="prefix">
                                    <option value="">Chọn Prefix</option>
                                    <?php
                                    
                                    $conn = new mysqli("localhost", "root", "", "sapovn");
                                    $prefixes = [];
                                    $result = $conn->query("SELECT DISTINCT prefix FROM DichVu");
                                    while ($row = $result->fetch_assoc()) {
                                        $prefixes[] = $row['prefix'];
                                    }
                                    foreach ($prefixes as $prefixOption) {
                                        echo "<option value='$prefixOption'>$prefixOption</option>";
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                                <button type="button" class="btn btn-secondary" onclick="toggleNewPrefix()">Thêm Prefix</button>
                            </div>
                        </div>
                        <div id="newPrefixContainer" class="form-group" style="display: none;">
                            <label for="newPrefix">Nhập Prefix mới:</label>
                            <input type="text" class="form-control" id="newPrefix" name="newPrefix">
                        </div>
                        <div class="form-group">
                            <label for="danhMuc">Danh mục:</label>
                            <input type="text" class="form-control" id="danhMuc" name="danhMuc" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm</button>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('addProductModal').style.display='none'">Hủy</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

   
    <div id="editProductModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa sản phẩm</h5>
                    <button type="button" class="close" onclick="document.getElementById('editProductModal').style.display='none'" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="edit_product.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="editId" name="id">
                        <input type="hidden" id="currentImage" name="currentImage">
                        <div class="form-group">
                            <label for="editTenSanPham">Tên sản phẩm:</label>
                            <input type="text" class="form-control" id="editTenSanPham" name="tenSanPham">
                        </div>
                        <div class="form-group">
                            <label for="editGia">Giá:</label>
                            <input type="text" class="form-control" id="editGia" name="gia" oninput="formatCurrency(this)">
                        </div>
                        <div class="form-group">
                            <label for="editHinhAnh">Hình ảnh:</label>
                            <input type="file" class="form-control" id="editHinhAnh" name="hinhAnh">
                            <img id="currentImageDisplay" src="" alt="" width="100" class="mt-2">
                        </div>
                        <div class="form-group">
                            <label for="editPrefix">Prefix:</label>
                            <div class="select-container">
                                <select class="form-control" id="editPrefix" name="prefix">
                                    <option value="">Chọn Prefix</option>
                                    <?php
                                    
                                    $conn = new mysqli("localhost", "root", "", "sapovn");
                                    $prefixes = [];
                                    $result = $conn->query("SELECT DISTINCT prefix FROM DichVu");
                                    while ($row = $result->fetch_assoc()) {
                                        $prefixes[] = $row['prefix'];
                                    }
                                    foreach ($prefixes as $prefixOption) {
                                        echo "<option value='$prefixOption'>$prefixOption</option>";
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                                <button type="button" class="btn btn-secondary" onclick="toggleNewEditPrefix()">Thêm Prefix</button>
                            </div>
                        </div>
                        <div id="newEditPrefixContainer" class="form-group" style="display: none;">
                            <label for="newEditPrefix">Nhập Prefix mới:</label>
                            <input type="text" class="form-control" id="newEditPrefix" name="newEditPrefix">
                        </div>
                        <div class="form-group">
                            <label for="editDanhMuc">Danh mục:</label>
                            <input type="text" class="form-control" id="editDanhMuc" name="danhMuc">
                        </div>
                        <button type="submit" class="btn btn-primary">Sửa</button>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('editProductModal').style.display='none'">Hủy</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editProduct(id, tenSanPham, gia, hinhAnh, prefix, danhMuc) {
            document.getElementById('editId').value = id;
            document.getElementById('editTenSanPham').value = tenSanPham;
            document.getElementById('editGia').value = gia;
            document.getElementById('currentImage').value = hinhAnh;
            document.getElementById('currentImageDisplay').src = hinhAnh;
            document.getElementById('editPrefix').value = prefix;
            document.getElementById('editDanhMuc').value = danhMuc;
            document.getElementById('editProductModal').style.display = 'block';
        }

        function deleteProduct(id) {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                window.location.href = 'delete_product.php?id=' + id;
            }
        }

        function toggleNewPrefix() {
            var container = document.getElementById('newPrefixContainer');
            if (container.style.display === 'none' || container.style.display === '') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }

        function toggleNewEditPrefix() {
            var container = document.getElementById('newEditPrefixContainer');
            if (container.style.display === 'none' || container.style.display === '') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }

        function formatCurrency(input) {
            
            let value = input.value.replace(/[^0-9]/g, '');
            
            value = new Intl.NumberFormat('vi-VN').format(value);
            
            input.value = value + ' VNĐ';
        }

        
        window.onclick = function(event) {
            var addModal = document.getElementById('addProductModal');
            var editModal = document.getElementById('editProductModal');
            if (event.target == addModal) {
                addModal.style.display = "none";
            }
            if (event.target == editModal) {
                editModal.style.display = "none";
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>