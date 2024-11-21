<?php
include 'header.php';
include 'config.php';

// Khởi tạo giá trị mặc định cho ngày bắt đầu và ngày kết thúc
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';

// Kiểm tra nếu người dùng đã chọn khoảng thời gian
$query = "SELECT s.TenSach, ms.SoLuong, CONVERT(DATE, ms.NgayMuon) AS NgayMuon, ms.TrangThaiDuyet 
          FROM MuonSach ms 
          JOIN Sach s ON ms.MaSach = s.MaSach 
          WHERE ms.TrangThaiDuyet = 'datrasach'";

if (!empty($startDate) && !empty($endDate)) {
    // Thêm điều kiện lọc theo khoảng thời gian vào truy vấn
    $query .= " AND ms.NgayMuon BETWEEN :startDate AND :endDate";
}

$query .= " ORDER BY NgayMuon ASC";
$stmt = $pdo->prepare($query);

// Gán giá trị tham số cho khoảng thời gian
if (!empty($startDate) && !empty($endDate)) {
    $stmt->bindParam(':startDate', $startDate);
    $stmt->bindParam(':endDate', $endDate);
}

$stmt->execute();
$results = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h1 class="text-center">Thống Kê Số Lượng Sách Mượn</h1>

    <!-- Form lọc theo khoảng thời gian -->
    <form method="POST" class="row g-3 mb-5">
        <div class="col-md-4">
            <label for="startDate" class="form-label">Ngày bắt đầu</label>
            <input type="date" class="form-control" id="startDate" name="startDate" value="<?php echo $startDate; ?>" required>
        </div>
        <div class="col-md-4">
            <label for="endDate" class="form-label">Ngày kết thúc</label>
            <input type="date" class="form-control" id="endDate" name="endDate" value="<?php echo $endDate; ?>" required>
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
    </form>

    <!-- Bảng kết quả -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên Sách</th>
                <th>Số Lượng</th>
                <th>Ngày Mượn</th>
                <th>Trạng Thái Duyệt</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['TenSach']); ?></td>
                        <td><?php echo htmlspecialchars($row['SoLuong']); ?></td>
                        <td><?php echo htmlspecialchars($row['NgayMuon']); ?></td>
                        <td><?php echo htmlspecialchars($row['TrangThaiDuyet']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
