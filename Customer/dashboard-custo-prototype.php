<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Toko Elektronik Online</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
        }
        
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }
        
        /* Header */
        header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .dark-mode header {
            background-color: #1a1a1a;
            border-bottom: 1px solid #333;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .logo span {
            color: #3498db;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .search-bar {
            display: flex;
            align-items: center;
            background-color: white;
            padding: 8px 15px;
            border-radius: 30px;
            width: 300px;
        }
        
        .dark-mode .search-bar {
            background-color: #333;
        }
        
        .search-bar input {
            border: none;
            outline: none;
            margin-left: 10px;
            width: 100%;
            background-color: transparent;
        }
        
        .dark-mode .search-bar input {
            color: #e0e0e0;
        }
        
        .cart-icon, .user-icon, .theme-toggle {
            font-size: 1.3rem;
            cursor: pointer;
            position: relative;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Kategori */
        .categories {
            background-color: #34495e;
            padding: 15px 30px;
            display: flex;
            gap: 20px;
            overflow-x: auto;
            white-space: nowrap;
        }
        
        .dark-mode .categories {
            background-color: #252525;
            border-bottom: 1px solid #333;
        }
        
        .category {
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .category:hover, .category.active {
            background-color: #3498db;
        }
        
        /* Hero Banner */
        .hero {
            height: 300px;
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            display: flex;
            align-items: center;
            padding: 0 50px;
            margin-bottom: 30px;
        }
        
        .dark-mode .hero {
            background: linear-gradient(135deg, #1a5276, #1a1a1a);
        }
        
        .hero-content {
            max-width: 500px;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .hero p {
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #c0392b;
        }
        
        /* Produk */
        .container {
            padding: 0 30px 30px;
        }
        
        .section-title {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-title h2 {
            font-size: 1.5rem;
            color: #2c3e50;
        }
        
        .dark-mode .section-title h2 {
            color: #e0e0e0;
        }
        
        .see-all {
            color: #3498db;
            cursor: pointer;
        }
        
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .product-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .dark-mode .product-card {
            background-color: #1e1e1e;
            box-shadow: 0 3px 10px rgba(0,0,0,0.3);
            border: 1px solid #333;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            height: 200px;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .dark-mode .product-image {
            background-color: #252525;
        }
        
        .product-image img {
            max-width: 80%;
            max-height: 80%;
            object-fit: contain;
        }
        
        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-name {
            font-size: 1rem;
            margin-bottom: 8px;
            color: #2c3e50;
        }
        
        .dark-mode .product-name {
            color: #e0e0e0;
        }
        
        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .current-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .original-price {
            font-size: 0.9rem;
            color: #95a5a6;
            text-decoration: line-through;
        }
        
        .product-rating {
            color: #f39c12;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .add-to-cart {
            width: 100%;
            padding: 8px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .add-to-cart:hover {
            background-color: #2980b9;
        }
        
        /* Footer */
        footer {
            background-color: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
            margin-top: 50px;
        }
        
        .dark-mode footer {
            background-color: #1a1a1a;
            border-top: 1px solid #333;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
        }
        
        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .copyright {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">Tech<span>Shop</span></div>
        <div class="header-right">
            <div class="search-bar">
                <span>🔍</span>
                <input type="text" placeholder="Cari produk...">
            </div>
            <div class="theme-toggle" id="themeToggle">🌙</div>
            <div class="cart-icon">
                🛒
                <span class="cart-count">3</span>
            </div>
            <div class="user-icon">
                👤
            </div>
        </div>
    </header>
    
    <!-- Kategori -->
    <div class="categories">
        <div class="category active">Semua Produk</div>
        <div class="category">Smartphone</div>
        <div class="category">Laptop</div>
        <div class="category">Aksesoris</div>
        <div class="category">Audio</div>
        <div class="category">Smartwatch</div>
        <div class="category">Kamera</div>
        <div class="category">Gaming</div>
    </div>
    
    <!-- Hero Banner -->
    <div class="hero">
        <div class="hero-content">
            <h1>Promo Gadget Terbaru</h1>
            <p>Dapatkan diskon hingga 30% untuk produk pilihan. Belanja sekarang sebelum kehabisan!</p>
            <button class="btn btn-primary">Lihat Promo</button>
        </div>
    </div>
    
    <!-- Produk Terbaru -->
    <div class="container">
        <div class="section-title">
            <h2>Produk Terbaru</h2>
            <div class="see-all">Lihat Semua</div>
        </div>
        
        <div class="products">
            <!-- Produk 1 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://placeholder.com/200x200" alt="Smartphone X5 Pro">
                    <div class="discount-badge">-15%</div>
                </div>
                <div class="product-info">
                    <h3 class="product-name">Smartphone X5 Pro</h3>
                    <div class="product-price">
                        <span class="current-price">Rp8.499.000</span>
                        <span class="original-price">Rp9.999.000</span>
                    </div>
                    <div class="product-rating">★★★★☆ (4.2)</div>
                    <button class="add-to-cart">+ Keranjang</button>
                </div>
            </div>
            
            <!-- Produk 2 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://placeholder.com/200x200" alt="Wireless Earbuds Pro">
                </div>
                <div class="product-info">
                    <h3 class="product-name">Wireless Earbuds Pro</h3>
                    <div class="product-price">
                        <span class="current-price">Rp1.299.000</span>
                    </div>
                    <div class="product-rating">★★★★★ (4.8)</div>
                    <button class="add-to-cart">+ Keranjang</button>
                </div>
            </div>
            
            <!-- Produk 3 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://placeholder.com/200x200" alt="Smart Watch 3">
                    <div class="discount-badge">-20%</div>
                </div>
                <div class="product-info">
                    <h3 class="product-name">Smart Watch 3</h3>
                    <div class="product-price">
                        <span class="current-price">Rp3.199.000</span>
                        <span class="original-price">Rp3.999.000</span>
                    </div>
                    <div class="product-rating">★★★★☆ (4.3)</div>
                    <button class="add-to-cart">+ Keranjang</button>
                </div>
            </div>
            
            <!-- Produk 4 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://placeholder.com/200x200" alt="Laptop Ultra Slim">
                </div>
                <div class="product-info">
                    <h3 class="product-name">Laptop Ultra Slim</h3>
                    <div class="product-price">
                        <span class="current-price">Rp12.750.000</span>
                    </div>
                    <div class="product-rating">★★★★☆ (4.1)</div>
                    <button class="add-to-cart">+ Keranjang</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Produk Terlaris -->
    <div class="container">
        <div class="section-title">
            <h2>Produk Terlaris</h2>
            <div class="see-all">Lihat Semua</div>
        </div>
        
        <div class="products">
            <!-- Produk 5 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://placeholder.com/200x200" alt="Power Bank 20.000mAh">
                </div>
                <div class="product-info">
                    <h3 class="product-name">Power Bank 20.000mAh</h3>
                    <div class="product-price">
                        <span class="current-price">Rp599.000</span>
                    </div>
                    <div class="product-rating">★★★★★ (4.9)</div>
                    <button class="add-to-cart">+ Keranjang</button>
                </div>
            </div>
            
            <!-- Produk 6 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://placeholder.com/200x200" alt="Bluetooth Speaker">
                    <div class="discount-badge">-10%</div>
                </div>
                <div class="product-info">
                    <h3 class="product-name">Bluetooth Speaker</h3>
                    <div class="product-price">
                        <span class="current-price">Rp1.079.000</span>
                        <span class="original-price">Rp1.199.000</span>
                    </div>
                    <div class="product-rating">★★★★☆ (4.5)</div>
                    <button class="add-to-cart">+ Keranjang</button>
                </div>
            </div>
            
            <!-- Produk 7 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://placeholder.com/200x200" alt="Kamera Mirrorless">
                </div>
                <div class="product-info">
                    <h3 class="product-name">Kamera Mirrorless</h3>
                    <div class="product-price">
                        <span class="current-price">Rp7.850.000</span>
                    </div>
                    <div class="product-rating">★★★★★ (4.7)</div>
                    <button class="add-to-cart">+ Keranjang</button>
                </div>
            </div>
            
            <!-- Produk 8 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://placeholder.com/200x200" alt="Keyboard Mechanical">
                    <div class="discount-badge">-25%</div>
                </div>
                <div class="product-info">
                    <h3 class="product-name">Keyboard Mechanical</h3>
                    <div class="product-price">
                        <span class="current-price">Rp1.499.000</span>
                        <span class="original-price">Rp1.999.000</span>
                    </div>
                    <div class="product-rating">★★★★☆ (4.4)</div>
                    <button class="add-to-cart">+ Keranjang</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="footer-links">
            <a href="#">Tentang Kami</a>
            <a href="#">Kebijakan Privasi</a>
            <a href="#">Syarat & Ketentuan</a>
            <a href="#">Bantuan</a>
            <a href="#">Kontak</a>
        </div>
        <div class="copyright">
            &copy; 2023 TechShop. All Rights Reserved.
        </div>
    </footer>

    <script>
        // Toggle Dark Mode
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        
        // Cek preferensi dark mode dari local storage
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            themeToggle.textContent = '☀️';
        }
        
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                themeToggle.textContent = '☀️';
            } else {
                localStorage.setItem('darkMode', 'disabled');
                themeToggle.textContent = '🌙';
            }
        });
    </script>
</body>
</html>