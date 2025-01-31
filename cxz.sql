USE [quanlytruonghoc]
GO
/****** Object:  Table [dbo].[LoaiSach]    Script Date: 31/10/2024 8:07:29 CH ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[LoaiSach](
	[MaLoai] [int] IDENTITY(1,1) NOT NULL,
	[TenLoai] [nvarchar](100) NOT NULL,
	[IsDel] [bit] NULL,
PRIMARY KEY CLUSTERED 
(
	[MaLoai] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
UNIQUE NONCLUSTERED 
(
	[TenLoai] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[MuonSach]    Script Date: 31/10/2024 8:07:29 CH ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[MuonSach](
	[MaMuon] [int] IDENTITY(1,1) NOT NULL,
	[MaNguoiDung] [int] NULL,
	[MaSach] [int] NULL,
	[NgayMuon] [datetime] NULL,
	[SoLuong] [int] NOT NULL,
	[TrangThai] [nvarchar](20) NULL,
	[GhiChu] [nvarchar](255) NULL,
	[IsDel] [bit] NULL,
	[TrangThaiDuyet] [nvarchar](20) NULL,
PRIMARY KEY CLUSTERED 
(
	[MaMuon] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[NguoiDung]    Script Date: 31/10/2024 8:07:29 CH ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[NguoiDung](
	[MaNguoiDung] [int] IDENTITY(1,1) NOT NULL,
	[TenDangNhap] [nvarchar](50) NOT NULL,
	[MatKhau] [nvarchar](255) NOT NULL,
	[HoTen] [nvarchar](100) NULL,
	[Email] [nvarchar](100) NULL,
	[SoDienThoai] [nvarchar](15) NULL,
	[VaiTro] [nvarchar](10) NULL,
	[NgayTao] [datetime] NULL,
	[IsDel] [bit] NULL,
PRIMARY KEY CLUSTERED 
(
	[MaNguoiDung] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
UNIQUE NONCLUSTERED 
(
	[TenDangNhap] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY],
UNIQUE NONCLUSTERED 
(
	[Email] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Sach]    Script Date: 31/10/2024 8:07:29 CH ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Sach](
	[MaSach] [int] IDENTITY(1,1) NOT NULL,
	[TenSach] [nvarchar](255) NOT NULL,
	[TacGia] [nvarchar](100) NULL,
	[MaLoai] [int] NULL,
	[NamXuatBan] [int] NULL,
	[MoTa] [nvarchar](max) NULL,
	[SoLuong] [int] NULL,
	[NgayNhap] [datetime] NULL,
	[TinhTrang] [nvarchar](20) NULL,
	[IsDel] [bit] NULL,
	[HinhAnh] [nvarchar](255) NULL,
	[TrangThai] [nvarchar](255) NULL,
PRIMARY KEY CLUSTERED 
(
	[MaSach] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[TraSach]    Script Date: 31/10/2024 8:07:29 CH ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[TraSach](
	[MaTra] [int] IDENTITY(1,1) NOT NULL,
	[MaMuon] [int] NULL,
	[NgayTra] [datetime] NULL,
	[SoLuongTra] [int] NOT NULL,
	[TinhTrangSach] [nvarchar](50) NULL,
	[GhiChu] [nvarchar](255) NULL,
	[IsDel] [bit] NULL,
	[MaNguoiDung] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[MaTra] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
ALTER TABLE [dbo].[LoaiSach] ADD  DEFAULT ((1)) FOR [IsDel]
GO
ALTER TABLE [dbo].[MuonSach] ADD  DEFAULT (getdate()) FOR [NgayMuon]
GO
ALTER TABLE [dbo].[MuonSach] ADD  DEFAULT ((1)) FOR [IsDel]
GO
ALTER TABLE [dbo].[MuonSach] ADD  DEFAULT ('choduyet') FOR [TrangThaiDuyet]
GO
ALTER TABLE [dbo].[NguoiDung] ADD  DEFAULT (getdate()) FOR [NgayTao]
GO
ALTER TABLE [dbo].[NguoiDung] ADD  DEFAULT ((1)) FOR [IsDel]
GO
ALTER TABLE [dbo].[Sach] ADD  DEFAULT ((1)) FOR [SoLuong]
GO
ALTER TABLE [dbo].[Sach] ADD  DEFAULT (getdate()) FOR [NgayNhap]
GO
ALTER TABLE [dbo].[Sach] ADD  DEFAULT ((1)) FOR [IsDel]
GO
ALTER TABLE [dbo].[TraSach] ADD  DEFAULT (getdate()) FOR [NgayTra]
GO
ALTER TABLE [dbo].[TraSach] ADD  DEFAULT ((1)) FOR [IsDel]
GO
ALTER TABLE [dbo].[MuonSach]  WITH CHECK ADD FOREIGN KEY([MaNguoiDung])
REFERENCES [dbo].[NguoiDung] ([MaNguoiDung])
GO
ALTER TABLE [dbo].[MuonSach]  WITH CHECK ADD FOREIGN KEY([MaSach])
REFERENCES [dbo].[Sach] ([MaSach])
GO
ALTER TABLE [dbo].[Sach]  WITH CHECK ADD FOREIGN KEY([MaLoai])
REFERENCES [dbo].[LoaiSach] ([MaLoai])
GO
ALTER TABLE [dbo].[TraSach]  WITH CHECK ADD FOREIGN KEY([MaMuon])
REFERENCES [dbo].[MuonSach] ([MaMuon])
GO
ALTER TABLE [dbo].[TraSach]  WITH CHECK ADD FOREIGN KEY([MaNguoiDung])
REFERENCES [dbo].[NguoiDung] ([MaNguoiDung])
GO
ALTER TABLE [dbo].[MuonSach]  WITH CHECK ADD CHECK  (([TrangThai]='chuatra' OR [TrangThai]='muon'))
GO
ALTER TABLE [dbo].[MuonSach]  WITH CHECK ADD CHECK  (([TrangThaiDuyet]='tuchoi' OR [TrangThaiDuyet]='duyet' OR [TrangThaiDuyet]='choduyet'))
GO
ALTER TABLE [dbo].[NguoiDung]  WITH CHECK ADD CHECK  (([VaiTro]='nguoidung' OR [VaiTro]='admin'))
GO
ALTER TABLE [dbo].[Sach]  WITH CHECK ADD CHECK  (([TinhTrang]='hong' OR [TinhTrang]='mat' OR [TinhTrang]='con'))
GO
ALTER TABLE [dbo].[TraSach]  WITH CHECK ADD CHECK  (([TinhTrangSach]='mat' OR [TinhTrangSach]='hong' OR [TinhTrangSach]='tot'))
GO
