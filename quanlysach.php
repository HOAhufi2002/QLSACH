<?php
session_start(); // Bắt đầu session

// Kiểm tra nếu người dùng không phải là admin thì chuyển hướng về trang đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit;
}

include 'config.php';

// Lấy danh sách loại sách
$stmt = $pdo->query("SELECT MaLoai, TenLoai FROM LoaiSach WHERE IsDel = 1");
$loaiSachList = $stmt->fetchAll();

// Xử lý tìm kiếm
$searchTerm = isset($_POST['search_term']) ? $_POST['search_term'] : '';
$searchCategory = isset($_POST['search_category']) ? $_POST['search_category'] : '';
$searchYear = isset($_POST['search_year']) ? $_POST['search_year'] : '';

// Prepare SQL query with additional filters for category and year
$query = "SELECT * FROM Sach WHERE TenSach LIKE ? AND IsDel = 1";
$params = ['%' . $searchTerm . '%'];

if ($searchCategory) {
    $query .= " AND MaLoai = ?";
    $params[] = $searchCategory;
}

if ($searchYear) {
    $query .= " AND NamXuatBan = ?";
    $params[] = $searchYear;
}

// Execute the query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sachList = $stmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Xử lý thêm sách
    if (isset($_POST['add'])) {
        // Thông tin sách
        $tenSach = $_POST['TenSach'];
        $tacGia = $_POST['TacGia'];
        $maLoai = $_POST['MaLoai'];
        $namXuatBan = $_POST['NamXuatBan'];
        $moTa = $_POST['MoTa'];
        $soLuong = $_POST['SoLuong'];

        // Upload hình ảnh
        if (isset($_FILES['HinhAnh']) && $_FILES['HinhAnh']['error'] == 0) {
            // Đường dẫn lưu ảnh trong hệ thống file (vật lý)
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/a/images/";
            $fileName = basename($_FILES["HinhAnh"]["name"]);
            $targetFile = $targetDir . $fileName;

            // Kiểm tra và di chuyển file ảnh
            if (move_uploaded_file($_FILES["HinhAnh"]["tmp_name"], $targetFile)) {
                // Đường dẫn URL tương đối, lưu vào database
                $hinhAnh = "/a/images/" . $fileName;
            } else {
                // Đường dẫn URL cho ảnh mặc định
                $hinhAnh = "/a/images/default_book.jpg";
            }
        } else {
            $hinhAnh = '/a/images/default_book.jpg'; // Đường dẫn mặc định
        }

        // Lưu thông tin sách
        $stmt = $pdo->prepare("INSERT INTO Sach (TenSach, TacGia, MaLoai, NamXuatBan, MoTa, SoLuong, HinhAnh, TinhTrang, IsDel) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, 'con', 1)");
        $stmt->execute([$tenSach, $tacGia, $maLoai, $namXuatBan, $moTa, $soLuong, $hinhAnh]);

        header("Location: quanlysach.php");
        exit;
    }

    // Xử lý xóa sách
    if (isset($_POST['delete'])) {
        $maSach = $_POST['MaSach'];

        // Cập nhật trạng thái IsDel thành 0 để xóa logic
        $stmt = $pdo->prepare("UPDATE Sach SET IsDel = 0 WHERE MaSach = ?");
        $stmt->execute([$maSach]);

        header("Location: quanlysach.php");
        exit;
    }

    // Xử lý sửa sách
    if (isset($_POST['edit'])) {
        $maSach = $_POST['MaSach'];
        $tenSach = $_POST['TenSach'];
        $tacGia = $_POST['TacGia'];
        $maLoai = $_POST['MaLoai'];
        $namXuatBan = $_POST['NamXuatBan'];
        $moTa = $_POST['MoTa'];
        $soLuong = $_POST['SoLuong'];

        // Upload hình ảnh
        if (isset($_FILES['HinhAnh']) && $_FILES['HinhAnh']['error'] == 0) {
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/a/images/";
            $fileName = basename($_FILES["HinhAnh"]["name"]);
            $targetFile = $targetDir . $fileName;

            // Kiểm tra và di chuyển file ảnh
            if (move_uploaded_file($_FILES["HinhAnh"]["tmp_name"], $targetFile)) {
                // Đường dẫn URL tương đối, lưu vào database
                $hinhAnh = "/a/images/" . $fileName;
            } else {
                // Đường dẫn URL cho ảnh mặc định
                $hinhAnh = "/a/images/default_book.jpg";
            }
        } else {
            // Giữ nguyên ảnh cũ nếu không upload ảnh mới
            $hinhAnh = $_POST['HinhAnhHienTai'];
        }

        // Cập nhật thông tin sách
        $stmt = $pdo->prepare("UPDATE Sach SET TenSach = ?, TacGia = ?, MaLoai = ?, NamXuatBan = ?, MoTa = ?, SoLuong = ?, HinhAnh = ? WHERE MaSach = ?");
        $stmt->execute([$tenSach, $tacGia, $maLoai, $namXuatBan, $moTa, $soLuong, $hinhAnh, $maSach]);

        header("Location: quanlysach.php");
        exit;
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Quản Lý Sách</h2>

    <!-- Form tìm kiếm sách -->
    <div class="row">
    <form style="border-radius: 50px;" method="POST" class="col-md-9">
        <div class="input-group mb-3">
            <input type="text" name="search_term" class="form-control" placeholder="Nhập tên sách cần tìm..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <select name="search_category" class="form-select" style="border-radius: 5px; margin-left: 5px;">
                <option value="">Chọn loại sách</option>
                <?php foreach ($loaiSachList as $loai): ?>
                    <option value="<?php echo $loai['MaLoai']; ?>" <?php echo isset($_POST['search_category']) && $_POST['search_category'] == $loai['MaLoai'] ? 'selected' : ''; ?>>
                        <?php echo $loai['TenLoai']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="search_year" class="form-control" placeholder="Năm xuất bản" style="border-radius: 5px; margin-left: 5px;" value="<?php echo isset($_POST['search_year']) ? $_POST['search_year'] : ''; ?>">
            <button class="btn btn-primary" type="submit" name="search" style="border-radius: 50px; margin-left: 5px;">Tìm kiếm</button>
        </div>
    </form>
    <div class="col-md-3 text-end">
        <button style="width: 150px;" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Thêm Sách Mới</button>
    </div>
</div>

    
    <br>
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table">
                <tr>
                    <th>Mã Sách</th>
                    <th>Tên Sách</th>
                    <th>Tác Giả</th>
                    <th>Thể Loại</th>
                    <th>Năm Xuất Bản</th>
                    <th>Số Lượng</th>
                    <th>Hình Ảnh</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sachList as $sach): ?>
                    <tr>
                        <td><?php echo $sach['MaSach']; ?></td>
                        <td><?php echo $sach['TenSach']; ?></td>
                        <td><?php echo $sach['TacGia']; ?></td>
                        <td><?php echo $sach['MaLoai']; ?></td>
                        <td><?php echo $sach['NamXuatBan']; ?></td>
                        <td><?php echo $sach['SoLuong']; ?></td>
                        <td>
                            <?php
                            $imagePath = !empty($sach['HinhAnh']) ? $sach['HinhAnh'] : '/a/images/default_book.jpg';
                            ?>
                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($sach['TenSach']); ?>" class="img-thumbnail" style="width: 100px; height: auto;">
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="MaSach" value="<?php echo $sach['MaSach']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" name="delete">Xóa</button>
                            </form>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $sach['MaSach']; ?>">Sửa</button>
                        </td>
                    </tr>

                    <!-- Modal Sửa Sách -->
                    <div class="modal fade" id="editModal<?php echo $sach['MaSach']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Sửa Thông Tin Sách</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="MaSach" value="<?php echo $sach['MaSach']; ?>">
                                        <input type="hidden" name="HinhAnhHienTai" value="<?php echo $sach['HinhAnh']; ?>">
                                        <div class="mb-3">
                                            <label for="TenSach" class="form-label">Tên Sách</label>
                                            <input type="text" class="form-control" name="TenSach" value="<?php echo $sach['TenSach']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="TacGia" class="form-label">Tác Giả</label>
                                            <input type="text" class="form-control" name="TacGia" value="<?php echo $sach['TacGia']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="MaLoai" class="form-label">Thể Loại</label>
                                            <select class="form-select" name="MaLoai" required>
                                                <?php foreach ($loaiSachList as $loai): ?>
                                                    <option value="<?php echo $loai['MaLoai']; ?>" <?php echo $loai['MaLoai'] == $sach['MaLoai'] ? 'selected' : ''; ?>>
                                                        <?php echo $loai['TenLoai']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="NamXuatBan" class="form-label">Năm Xuất Bản</label>
                                            <input type="number" class="form-control" name="NamXuatBan" value="<?php echo $sach['NamXuatBan']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="MoTa" class="form-label">Mô Tả</label>
                                            <textarea class="form-control" name="MoTa" required><?php echo $sach['MoTa']; ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="SoLuong" class="form-label">Số Lượng</label>
                                            <input type="number" class="form-control" name="SoLuong" value="<?php echo $sach['SoLuong']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="HinhAnh" class="form-label">Hình Ảnh</label>
                                            <input type="file" class="form-control" name="HinhAnh" accept="image/*">
                                            <img src="<?php echo htmlspecialchars($sach['HinhAnh']); ?>" alt="<?php echo htmlspecialchars($sach['TenSach']); ?>" class="img-thumbnail mt-2" style="width: 50px; height: auto;">
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="edit">Cập Nhật</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Button để mở modal thêm sách -->

    <!-- Modal Thêm Sách -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Thêm Sách Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="TenSach" class="form-label">Tên Sách</label>
                            <input type="text" class="form-control" name="TenSach" required>
                        </div>
                        <div class="mb-3">
                            <label for="TacGia" class="form-label">Tác Giả</label>
                            <input type="text" class="form-control" name="TacGia" required>
                        </div>
                        <div class="mb-3">
                            <label for="MaLoai" class="form-label">Thể Loại</label>
                            <select class="form-select" name="MaLoai" required>
                                <?php foreach ($loaiSachList as $loai): ?>
                                    <option value="<?php echo $loai['MaLoai']; ?>"><?php echo $loai['TenLoai']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="NamXuatBan" class="form-label">Năm Xuất Bản</label>
                            <input type="number" class="form-control" name="NamXuatBan" required>
                        </div>
                        <div class="mb-3">
                            <label for="MoTa" class="form-label">Mô Tả</label>
                            <textarea class="form-control" name="MoTa" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="SoLuong" class="form-label">Số Lượng</label>
                            <input type="number" class="form-control" name="SoLuong" required>
                        </div>
                        <div class="mb-3">
                            <label for="HinhAnh" class="form-label">Hình Ảnh</label>
                            <input type="file" class="form-control" name="HinhAnh" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-success" name="add">Thêm Sách</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include 'footer.php'; ?>