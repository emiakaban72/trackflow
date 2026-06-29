<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SupplySync - Global Risk Intelligence</title>
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --matcha-50: #f4f9f4;
            --matcha-100: #e3efe3;
            --matcha-500: #81A263; 
            --matcha-700: #365E32; 
            --corporate-dark: #0f172a;
            --corporate-gray: #64748b;
        }
        
        /* Mengunci seluruh halaman agar tidak bisa di-scroll */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; 
            color: var(--corporate-dark);
            height: 100vh;
            overflow: hidden; 
        }

        /* Pengaturan tinggi dan scroll independen untuk Sidebar */
        .sidebar {
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            height: 100vh;
            overflow-y: auto; 
            padding-bottom: 2rem; /* Ruang napas di bagian paling bawah */
        }

        /* Pengaturan tinggi dan scroll independen untuk Konten Utama */
        .main-content {
            background-color: #f8fafc;
            height: 100vh;
            overflow-y: auto;
        }

        /* Custom Scrollbar Elegan untuk Sidebar & Konten */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent; 
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }

        .brand-logo {
            color: var(--matcha-700);
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .menu-label {
            font-size: 0.70rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            padding-left: 1rem;
        }
        .nav-link {
            color: var(--corporate-gray);
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            margin-bottom: 0.2rem;
            transition: all 0.2s ease-in-out;
        }
        .nav-link i {
            width: 24px;
            text-align: center;
        }
        .nav-link:hover, .nav-link.active {
            background-color: var(--matcha-50);
            color: var(--matcha-700);
        }
        .card-corporate {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
        }
        .text-matcha { color: var(--matcha-700); }
        .bg-matcha { background-color: var(--matcha-500); color: #ffffff; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR -->
            <div class="col-md-2 sidebar p-4">
                <h5 class="brand-logo mb-4">
                    <i class="fa-solid fa-link me-2"></i>TrackFlow
                </h5>
                
                <ul class="nav flex-column">
                    <div class="menu-label">Main Dashboard</div>
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fa-solid fa-chart-line me-2"></i> Global Overview</a>
                    </li>

                    <div class="menu-label">Analytics & Maps</div>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-earth-americas me-2"></i> Weather & Ports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-chart-pie me-2"></i> Data Visualization</a>
                    </li>

                    <div class="menu-label">Market Intelligence</div>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-money-bill-trend-up me-2"></i> Currency Impact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-newspaper me-2"></i> News Intelligence</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-scale-balanced me-2"></i> Country Comparison</a>
                    </li>

                    <div class="menu-label">My Workspace</div>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-bookmark me-2"></i> My Watchlist</a>
                    </li>

                    <div class="menu-label">System Admin</div>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-sliders me-2"></i> Admin Panel</a>
                    </li>
                    
                    <!-- Tambahan spasi kosong agar menu bawah bisa terlihat jelas saat di-scroll -->
                    <li class="nav-item mt-5 mb-5">
                        <a class="nav-link text-danger" href="#"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
                    </li>
                </ul>
            </div>

            <!-- AREA KONTEN (Independent Scroll) -->
            <div class="col-md-10 p-4 main-content">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>