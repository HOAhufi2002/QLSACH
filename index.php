<?php include 'header.php'; ?>


<?php
// Kết nối cơ sở dữ liệu
include 'config.php';

// Truy vấn dữ liệu số lượng sách theo loại sách
$stmt = $pdo->query("SELECT LoaiSach.TenLoai, COUNT(Sach.MaSach) AS SoLuong
                     FROM Sach
                     JOIN LoaiSach ON Sach.MaLoai = LoaiSach.MaLoai
                     WHERE Sach.IsDel = 1
                     GROUP BY LoaiSach.TenLoai");

$loaiSach = [];
$soLuongSach = [];

// Lưu kết quả truy vấn vào mảng
while ($row = $stmt->fetch()) {
    $loaiSach[] = $row['TenLoai'];
    $soLuongSach[] = $row['SoLuong'];
}
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
<div class="container mt-5">
    <h1 class="text-center">Thống Kê Số Lượng Sách Theo Loại</h1>

    <!-- Biểu đồ cột -->
    <div class="row mt-5">
        <div class="col-md-6">
            <canvas id="barChart"></canvas>
        </div>

        <!-- Biểu đồ tròn -->
        <!-- Biểu đồ tròn nhỏ hơn -->
        <div class="col-md-5">
            <canvas id="pieChart" style="width: 3px; height: 1030px;"></canvas> <!-- Điều chỉnh kích thước ở đây -->
        </div>
    </div>
    <br>
</div>
<!-- Include thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript để vẽ biểu đồ -->
<script>
    // Biểu đồ cột (Bar Chart)
    const ctxBar = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(ctxBar, {
        type: 'bar', // Loại biểu đồ cột
        data: {
            labels: <?php echo json_encode($loaiSach); ?>, // Gán tên loại sách
            datasets: [{
                label: 'Số lượng sách',
                data: <?php echo json_encode($soLuongSach); ?>, // Gán số lượng sách tương ứng
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true // Bắt đầu từ 0 trên trục Y
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
            }
        }
    });

    // Biểu đồ tròn (Pie Chart)
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctxPie, {
        type: 'pie', // Loại biểu đồ tròn
        data: {
            labels: <?php echo json_encode($loaiSach); ?>, // Gán tên loại sách
            datasets: [{
                label: 'Số lượng sách',
                data: <?php echo json_encode($soLuongSach); ?>, // Gán số lượng sách tương ứng
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
            }
        }
    });
</script>

<?php include 'footer.php'; ?>