<?php include 'header.php'; ?>

<style>
.carousel-item img {
    height: 400px;
    object-fit: cover;
}

@media (max-width: 768px) {
    .carousel-item img {
        height: 250px;
    }
}

.carousel {
    border-top: 1px solid #ddd;
    padding-top: 10px;
    background-color: #f8f9fa;
}

.carousel-fade .carousel-item {
    transition: opacity 1s ease-in-out;
    opacity: 0;
}

.carousel-fade .carousel-item.active {
    opacity: 1;
}

/* Hiệu ứng khi di chuột qua thẻ sách */
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
}
</style>

<!-- Banner Quảng Cáo Toàn Chiều Rộng -->
<div class="container-fluid p-0 mt-4">
    <div id="adBannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/banner1.jpg" class="d-block w-100" alt="Quảng cáo 1">
            </div>
            <div class="carousel-item">
                <img src="images/banner2.jpg" class="d-block w-100" alt="Quảng cáo 2">
            </div>
            <div class="carousel-item">
                <img src="images/banner3.jpg" class="d-block w-100" alt="Quảng cáo 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#adBannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#adBannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<!-- Form Tìm Kiếm Sách -->
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <?php
                include 'config.php';

                $stmt = $pdo->query("SELECT MaLoai, TenLoai FROM LoaiSach WHERE IsDel = 1");

                while ($row = $stmt->fetch()) {
                    echo '<a href="view_books.php?MaLoai=' . $row['MaLoai'] . '" class="list-group-item list-group-item-action">' . $row['TenLoai'] . '</a>';
                }
                ?>
            </div>
        </div>

        <div class="col-md-9">
            <div>
                <form action="" method="GET">
                    <div class="input-group" style="max-width: 600px;">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sách..." 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                               style="box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); border: none; border-radius: 30px 0 0 30px;">
                        <button type="submit" class="btn btn-primary" 
                                style="box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); border-radius: 0 30px 30px 0;">Tìm kiếm</button>
                    </div>
                </form>
            </div>
            <br>

            <div class="row">
                <?php
                // Số sách trên mỗi trang
                $booksPerPage = 16;

                // Trang hiện tại
                $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                // Tính vị trí bắt đầu của sách trên trang hiện tại
                $offset = ($currentPage - 1) * $booksPerPage;

                // Lấy từ khóa tìm kiếm từ request nếu có
                $search = isset($_GET['search']) ? $_GET['search'] : '';

                // Kiểm tra nếu có bộ lọc theo loại sách
                if (isset($_GET['MaLoai'])) {
                    $maLoai = $_GET['MaLoai'];
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Sach WHERE MaLoai = ? AND IsDel = 1 AND TenSach LIKE ?");
                    $stmt->execute([$maLoai, "%$search%"]);
                    $totalBooks = $stmt->fetchColumn();

                    $stmt = $pdo->prepare("SELECT Sach.MaSach, Sach.TenSach, Sach.TacGia, Sach.MoTa, Sach.HinhAnh, LoaiSach.TenLoai, Sach.NamXuatBan, Sach.TinhTrang 
                                           FROM Sach 
                                           JOIN LoaiSach ON Sach.MaLoai = LoaiSach.MaLoai 
                                           WHERE Sach.MaLoai = ? AND Sach.IsDel = 1 AND Sach.TenSach LIKE ? 
                                           ORDER BY Sach.MaSach OFFSET ? ROWS FETCH NEXT ? ROWS ONLY");
                    $stmt->bindParam(1, $maLoai, PDO::PARAM_INT);
                    $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
                    $stmt->bindParam(3, $offset, PDO::PARAM_INT);
                    $stmt->bindParam(4, $booksPerPage, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Sach WHERE IsDel = 1 AND TenSach LIKE ?");
                    $stmt->execute(["%$search%"]);
                    $totalBooks = $stmt->fetchColumn();

                    $stmt = $pdo->prepare("SELECT Sach.MaSach, Sach.TenSach, Sach.TacGia, Sach.MoTa, Sach.HinhAnh, LoaiSach.TenLoai, Sach.NamXuatBan, Sach.TinhTrang 
                                           FROM Sach 
                                           JOIN LoaiSach ON Sach.MaLoai = LoaiSach.MaLoai 
                                           WHERE Sach.IsDel = 1 AND Sach.TenSach LIKE ? 
                                           ORDER BY Sach.MaSach OFFSET ? ROWS FETCH NEXT ? ROWS ONLY");
                    $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
                    $stmt->bindParam(2, $offset, PDO::PARAM_INT);
                    $stmt->bindParam(3, $booksPerPage, PDO::PARAM_INT);
                    $stmt->execute();
                }

                // Hiển thị sách
                while ($row = $stmt->fetch()) {
                    $imagePath = !empty($row['HinhAnh']) ? $row['HinhAnh'] : 'images/default_book.jpg';

                    echo '
                    <div class="col-md-3 mb-3 d-flex">
                        <div class="card d-flex flex-column" style="width: 100%; height: 100%;">
                            <img src="' . $imagePath . '" class="card-img-top" alt="' . $row['TenSach'] . '" style="height: 400px; object-fit: cover;">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <h5 class="card-title">' . $row['TenSach'] . '</h5>
                                <p class="card-text">Tác giả: ' . $row['TacGia'] . '</p>
                                <p class="card-text">' . substr($row['MoTa'], 0, 10) . '...</p>
                                <button type="button" class="btn btn-primary mt-auto" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="loadBookDetail(' . $row['MaSach'] . ')">Chi Tiết</button>
                            </div>
                        </div>
                    </div>';
                }
                ?>
            </div>

            <!-- Phân trang -->
            <nav>
                <ul class="pagination justify-content-center mt-4">
                    <?php
                    // Tính tổng số trang
                    $totalPages = ceil($totalBooks / $booksPerPage);

                    // Tạo liên kết trang trước
                    $pageUrl = '?page=' . ($currentPage - 1);
                    if ($search) $pageUrl .= '&search=' . urlencode($search);
                    if (isset($maLoai)) $pageUrl .= '&MaLoai=' . $maLoai;

                    if ($currentPage > 1) {
                        echo '<li class="page-item"><a class="page-link" href="' . $pageUrl . '">&laquo; Trang Trước</a></li>';
                    }

                    // Liên kết các trang
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $pageUrl = '?page=' . $i;
                        if ($search) $pageUrl .= '&search=' . urlencode($search);
                        if (isset($maLoai)) $pageUrl .= '&MaLoai=' . $maLoai;
                        
                        echo '<li class="page-item ' . ($i == $currentPage ? 'active' : '') . '"><a class="page-link" href="' . $pageUrl . '">' . $i . '</a></li>';
                    }

                    // Tạo liên kết trang sau
                    $pageUrl = '?page=' . ($currentPage + 1);
                    if ($search) $pageUrl .= '&search=' . urlencode($search);
                    if (isset($maLoai)) $pageUrl .= '&MaLoai=' . $maLoai;

                    if ($currentPage < $totalPages) {
                        echo '<li class="page-item"><a class="page-link" href="' . $pageUrl . '">Trang Sau &raquo;</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<script>
    function loadBookDetail(maSach) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'popupDetailsach.php?MaSach=' + maSach, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.querySelector('#detailModal .modal-content').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }
</script>

<?php include 'footer.php'; ?>
