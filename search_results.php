<?php
include 'config.php';
include 'header.php';

// Kiểm tra và lấy giá trị tìm kiếm
$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';

// Truy vấn sách theo tên
$stmt = $pdo->prepare("SELECT MaSach, TenSach, TacGia, MoTa, HinhAnh FROM Sach WHERE TenSach LIKE ? AND IsDel = 1");
$searchTerm = '%' . $searchQuery . '%';
$stmt->execute([$searchTerm]);

echo '<div class="container mt-4">';
echo '<h4>Kết quả tìm kiếm cho: ' . htmlspecialchars($searchQuery) . '</h4>';
echo '<div class="row">';

while ($row = $stmt->fetch()) {
    $imagePath = !empty($row['HinhAnh']) ? $row['HinhAnh'] : 'images/default_book.jpg';
    echo '
        <div class="col-md-3 mb-3">
            <div class="card">
                <img src="' . $imagePath . '" class="card-img-top" alt="' . htmlspecialchars($row['TenSach']) . '" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($row['TenSach']) . '</h5>
                    <p class="card-text">Tác giả: ' . htmlspecialchars($row['TacGia']) . '</p>
                    <p class="card-text">' . substr(htmlspecialchars($row['MoTa']), 0, 50) . '...</p>
                    <a href="#" class="btn btn-primary">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
    ';
}

echo '</div></div>';

include 'footer.php';
?>
