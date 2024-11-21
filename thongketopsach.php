<?php include 'header.php'; ?>

<?php
include 'config.php';
$topBooksQuery = $pdo->query("SELECT TOP 10 s.TenSach, SUM(ms.SoLuong) AS TongSoLuong
                              FROM MuonSach ms
                              JOIN Sach s ON ms.MaSach = s.MaSach
                              GROUP BY s.TenSach
                              ORDER BY TongSoLuong DESC");
$topBooks = $topBooksQuery->fetchAll();

$bookNames = [];
$bookCounts = [];

foreach ($topBooks as $book) {
    $bookNames[] = $book['TenSach'];
    $bookCounts[] = $book['TongSoLuong'];
}

$stmt = $pdo->query("SELECT LoaiSach.TenLoai, COUNT(Sach.MaSach) AS SoLuong
                     FROM Sach
                     JOIN LoaiSach ON Sach.MaLoai = LoaiSach.MaLoai
                     WHERE Sach.IsDel = 1
                     GROUP BY LoaiSach.TenLoai");

$loaiSach = [];
$soLuongSach = [];

while ($row = $stmt->fetch()) {
    $loaiSach[] = $row['TenLoai'];
    $soLuongSach[] = $row['SoLuong'];
}

$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';

$query = "SELECT s.TenSach, ms.SoLuong, CONVERT(DATE, ms.NgayMuon) AS NgayMuon, ms.TrangThaiDuyet 
          FROM MuonSach ms 
          JOIN Sach s ON ms.MaSach = s.MaSach 
          WHERE ms.TrangThaiDuyet = 'datrasach'";

if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND ms.NgayMuon BETWEEN :startDate AND :endDate";
}

if (!empty($startDate) && !empty($endDate) && $startDate > $endDate) {
    echo "<p class='text-danger text-center'>Ngày bắt đầu không được lớn hơn ngày kết thúc.</p>";
}

$query .= " ORDER BY NgayMuon ASC";
$stmt = $pdo->prepare($query);

if (!empty($startDate) && !empty($endDate)) {
    $stmt->bindParam(':startDate', $startDate);
    $stmt->bindParam(':endDate', $endDate);
}

$stmt->execute();
$results = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h1 class="text-center">Thống Kê Top 10 Sách Được Mượn Nhiều Nhất</h1>

    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
            <canvas id="topBooksChart"></canvas>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctxTopBooks = document.getElementById('topBooksChart').getContext('2d');
    const topBooksChart = new Chart(ctxTopBooks, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($bookNames); ?>, // Gán tên sách
            datasets: [{
                label: 'Số lượng mượn',
                data: <?php echo json_encode($bookCounts); ?>, // Gán số lượng mượn
                backgroundColor: 'rgba(75, 192, 192, 0.6)', // Màu nền
                borderColor: 'rgba(75, 192, 192, 1)', // Màu viền
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true // Bắt đầu trục Y từ 0
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        }
    });
</script>
<script>
    const ctxBar = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($loaiSach); ?>,
            datasets: [{
                label: 'Số lượng sách',
                data: <?php echo json_encode($soLuongSach); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Biểu đồ tròn
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($loaiSach); ?>,
            datasets: [{
                data: <?php echo json_encode($soLuongSach); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ]
            }]
        }
    });
</script>

<?php include 'footer.php'; ?>
