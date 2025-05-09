<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_conexao.php';

// Busca e filtros
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT * FROM produtos WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (nome LIKE ? OR descricao LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $query .= " AND descricao LIKE ?";
    $params[] = "%$category%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VGR Business Huambo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Estilos básicos inline para o exemplo - mova para style.css para melhor organização */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f4f0;
            color: #333;
            margin: 0;
            padding-bottom: 50px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header {
            background-color: #000;
            color: #fff;
            padding: 1.5rem 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .logo {
            font-size: 1.75rem;
            font-weight: bold;
        }

        .nav-toggle {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            display: none; /* Oculto por padrão em telas maiores */
        }

        .nav {
            display: flex;
            align-items: center;
        }

        .nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .nav ul li {
            margin-left: 1.2rem;
        }

        .nav ul li:first-child {
            margin-left: 0;
        }

        .nav ul li a {
            color: #fff;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .nav ul li a:hover {
            opacity: 1;
        }

        .follow-btn {
            background-color: #333;
            color: #fff;
            padding: 0.6rem 1.2rem;
            border-radius: 0.25rem;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-left: 1.2rem;
        }

        .follow-btn:hover {
            background-color: #555;
        }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1555041469-dc03117f94eb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            color: #fff;
            text-align: center;
            padding: 5rem 0;
            margin-bottom: 2rem;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 0.75rem;
            font-weight: bold;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero button {
            background-color: #fff;
            color: #000;
            border: none;
            padding: 0.8rem 1.6rem;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .hero button:hover {
            background-color: #eee;
            color: #000;
        }

        .section {
            padding: 3rem 0;
            text-align: center;
        }

        h2 {
            font-size: 2.2rem;
            margin-bottom: 2rem;
            color: #333;
        }

        .search-filter {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .search-filter input[type="text"],
        .search-filter select,
        .search-filter button {
            padding: 0.6rem;
            border-radius: 0.2rem;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        .search-filter button {
            background-color: #333;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-filter button:hover {
            background-color: #555;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            padding: 0 1rem;
        }

        .item {
            background-color: #fff;
            border-radius: 0.2rem;
            box-shadow: 0 0.1rem 0.3rem rgba(0, 0, 0, 0.05);
            padding: 1.2rem;
            text-align: center;
            transition: transform 0.2s ease-in-out;
        }

        .item:hover {
            transform: scale(1.02);
        }

        .item img {
            max-width: 100%;
            height: auto;
            border-radius: 0.2rem;
            margin-bottom: 0.75rem;
        }

        .item h3 {
            font-size: 1.3rem;
            margin-top: 0;
            margin-bottom: 0.6rem;
            color: #333;
        }

        .item .price {
            color: #555;
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
        }

        .item p {
            font-size: 1rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .item button {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 0.2rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .item button:hover {
            background-color: #333;
        }

        .footer {
            background-color: #000;
            color: #f8f4f0;
            text-align: center;
            padding: 1.5rem 0;
            /* position: fixed; */
            bottom: 0;
            width: 100%;
            font-size: 0.9rem;
        }

        .footer a {
            color: #fff;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .footer a:hover {
            opacity: 1;
        }

        /* Estilos para telas menores (menu dropdown) */
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                align-items: flex-start;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: #000;
                z-index: 10;
                display: none; /* Oculto inicialmente */
            }

            .nav.active {
                display: flex;
            }

            .nav ul {
                flex-direction: column;
                width: 100%;
            }

            .nav ul li {
                margin-left: 0;
                width: 100%;
                border-bottom: 1px solid #222;
            }

            .nav ul li:last-child {
                border-bottom: none;
            }

            .nav ul li a, .follow-btn {
                display: block;
                padding: 1rem 1.5rem;
                margin-left: 0;
                width: 100%;
                text-align: left;
            }

            .nav-toggle {
                display: block; /* Visível em telas menores */
            }

            .follow-btn {
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">VGR</div>
        <button class="nav-toggle" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </button>
        <nav class="nav">
            <ul>
                <li><a href="#inicio"><i class="bi bi-house-door-fill"></i> Início</a></li>
                <li><a href="#produtos"><i class="bi bi-box-seam-fill"></i> Produtos</a></li>
                <li><a href="#acessorios"><i class="bi bi-usb-c-fill"></i> Acessórios</a></li>
                <li><a href="#promocoes"><i class="bi bi-percent"></i> Promoções</a></li>
                <li><a href="#contato"><i class="bi bi-envelope-fill"></i> Contato</a></li>
                <?php if (isset($_SESSION['admin'])): ?>
                    <li><a href="admin.php"><i class="bi bi-shield-lock-fill"></i> Admin</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="bi bi-person-fill"></i> Admin Login</a></li>
                <?php endif; ?>
            </ul>
            <a href="https://instagram.com/vgr_business_huambo" class="follow-btn" target="_blank"><i class="bi bi-instagram"></i> Siga-nos</a>
        </nav>
    </header>

    <section class="hero" id="inicio">
        <h1>Bem-vindo à VGR Business Huambo</h1>
        <p>Especialista em produtos Apple e acessórios</p>
        <button onclick="scrollToSection('produtos')"><i class="bi bi-search"></i> Veja Nossos Produtos</button>
    </section>

    <section class="products" id="produtos">
        <h2><i class="bi bi-box-seam"></i> Produtos</h2>
        <div class="search-filter">
            <form method="GET">
                <input type="text" name="search" placeholder="Buscar produtos..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="category">
                    <option value="">Todas as Categorias</option>
                    <option value="iPhone" <?php echo $category == 'iPhone' ? 'selected' : ''; ?>>iPhones</option>
                    <option value="MacBook" <?php echo $category == 'MacBook' ? 'selected' : ''; ?>>MacBooks</option>
                    <option value="iPad" <?php echo $category == 'iPad' ? 'selected' : ''; ?>>iPads</option>
                </select>
                <button type="submit"><i class="bi bi-filter-fill"></i> Filtrar</button>
            </form>
        </div>
        <div class="grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="item">
                        <img src="uploads/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['nome']); ?>">
                        <h3><?php echo htmlspecialchars($product['nome']); ?></h3>
                        <p class="price">AOA <?php echo htmlspecialchars($product['preco']); ?></p>
                        <p><?php echo htmlspecialchars($product['descricao']); ?></p>
                        <button onclick="alert('Produto adicionado ao carrinho! Contato: contato@vgrhuambo.com')"><i class="bi bi-cart-plus-fill"></i> Comprar</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum produto encontrado.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="accessories" id="acessorios">
        <h2><i class="bi bi-usb-c"></i> Acessórios</h2>
        <div class="grid">
            <div class="item">
                <img src="https://via.placeholder.com/200x150?text=AirPods" alt="AirPods">
                <h3>AirPods Pro</h3>
                <p class="price">250.000 AOA</p>
                <p>Áudio de alta qualidade com cancelamento de ruído.</p>
                <button><i class="bi bi-cart-plus-fill"></i> Comprar</button>
            </div>
            <div class="item">
                <img src="https://via.placeholder.com/200x150?text=Capas" alt="Capas">
                <h3>Capas iPhone</h3>
                <p class="price">50.000 AOA</p>
                <p>Proteção elegante para seu iPhone.</p>
                <button><i class="bi bi-cart-plus-fill"></i> Comprar</button>
            </div>
        </div>
    </section>

    <section class="promotions" id="promocoes">
        <h2><i class="bi bi-tag-fill"></i> Promoções</h2>
        <div class="grid">
            <div class="item">
                <img src="https://via.placeholder.com/200x150?text=Combo" alt="Combo">
                <h3>Combo iPhone + AirPods</h3>
                <p class="price"><span style="color: red;">10% OFF</span> - 1.350.000 AOA</p>
                <p>Aproveite essa oferta especial!</p>
                <button><i class="bi bi-cash-coin"></i> Ver Oferta</button>
            </div>
            <script>
    const navToggle = document.querySelector('.nav-toggle');
    const nav = document.querySelector('.nav');

    navToggle.addEventListener('click', () => {
        nav.classList.toggle('active');
    });

    function scrollToSection(sectionId) {
        const element = document.getElementById(sectionId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>
        </div>
        
    </section>

    <footer class="footer" id="contato">
        <p><i class="bi bi-geo-alt-fill"></i> Huambo, Angola | <a href="https://instagram.com/vgr_business_huambo" target="_blank"><i class="bi bi-instagram"></i> @vgr_business_huambo</a> | <a href="mailto:contato@vgrhu