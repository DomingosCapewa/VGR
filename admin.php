<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_conexao.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Garantir que a pasta de uploads existe
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

// Adicionar produto
if (isset($_POST['add_product'])) {
    $name = htmlspecialchars(trim($_POST['nome']));
    $price = htmlspecialchars(trim($_POST['preco']));
    $description = htmlspecialchars(trim($_POST['descricao']));

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;

        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($image_type, ['jpg', 'jpeg', 'png', 'gif']) && $_FILES['image']['size'] <= 5000000) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, descricao, image_url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $price, $description, $image_name]);
                $success = "Produto adicionado com sucesso!";
            } else {
                $error = "Erro ao fazer upload da imagem.";
            }
        } else {
            $error = "Apenas imagens JPG, JPEG, PNG ou GIF são permitidas, com tamanho máximo de 5MB.";
        }
    }
}

// Deletar produto
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare("SELECT image_url FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product && file_exists($target_dir . $product['image_url'])) {
            unlink($target_dir . $product['image_url']);
        }

        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Produto deletado com sucesso!";
    }
}

// Buscar produtos
$products = $pdo->query("SELECT * FROM produtos")->fetchAll(PDO::FETCH_ASSOC);

// Editar produto
if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = htmlspecialchars(trim($_POST['nome']));
    $price = htmlspecialchars(trim($_POST['preco']));
    $description = htmlspecialchars(trim($_POST['descricao']));

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $stmt = $pdo->prepare("SELECT image_url FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product && file_exists($target_dir . $product['image_url'])) {
            unlink($target_dir . $product['image_url']);
        }

        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($image_type, ['jpg', 'jpeg', 'png', 'gif']) && $_FILES['image']['size'] <= 5000000) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, descricao = ?, image_url = ? WHERE id = ?");
                $stmt->execute([$name, $price, $description, $image_name, $id]);
                $success = "Produto editado com sucesso!";
            }
        }
    } else {
        $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, descricao = ? WHERE id = ?");
        $stmt->execute([$name, $price, $description, $id]);
        $success = "Produto editado com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - VGR Business Huambo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">VGR</div>
            <button class="nav-toggle" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <nav class="nav">
                <ul>
                    <li><a href="index.php"><i class="bi bi-arrow-left-circle-fill"></i> Voltar à Vitrine</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-page section">
        <div class="container">
            <h2>Adicionar Novo Produto</h2>
            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success-message"><?php echo $success; ?></p>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <input type="text" name="nome" placeholder="Nome do Produto" required>
                <input type="text" name="preco" placeholder="Preço (ex.: 1.200.000 AOA)" required>
                <textarea name="descricao" placeholder="Descrição do Produto" required></textarea>
                <label for="image_upload">Imagem do Produto:</label>
                <input type="file" id="image_upload" name="image" accept="image/*" required>
                <button type="submit" name="add_product" class="button primary"><i class="bi bi-plus-circle-fill"></i> Adicionar Produto</button>
            </form>

            <h2>Gerenciar Produtos</h2>
            <div class="grid">
                <?php foreach ($products as $product): ?>
                    <div class="item">
                        <img src="uploads/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['nome']); ?>">
                        <h3><?php echo htmlspecialchars($product['nome']); ?></h3>
                        <p class="price">AOA <?php echo htmlspecialchars($product['preco']); ?></p>
                        <p class="description"><?php echo htmlspecialchars($product['descricao']); ?></p>
                        <form method="POST" enctype="multipart/form-data" class="edit-form">
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            <label>Nome:</label>
                            <input type="text" name="nome" value="<?php echo htmlspecialchars($product['nome']); ?>" required>
                            <label>Preço:</label>
                            <input type="text" name="preco" value="<?php echo htmlspecialchars($product['preco']); ?>" required>
                            <label>Descrição:</label>
                            <textarea name="descricao" required><?php echo htmlspecialchars($product['descricao']); ?></textarea>
                            <label for="edit_image_<?php echo $product['id']; ?>">Nova Imagem (opcional):</label>
                            <input type="file" id="edit_image_<?php echo $product['id']; ?>" name="image" accept="image/*">
                            <button type="submit" name="edit_product" class="button secondary"><i class="bi bi-pencil-square"></i> Editar</button>
                        </form>
                        <a href="admin.php?delete=<?php echo $product['id']; ?>" onclick="return confirm('Tem certeza que deseja deletar?')" class="button danger delete-btn"><i class="bi bi-trash-fill"></i> Deletar</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> VGR Business Huambo - Admin</p>
        </div>
    </footer>

    <script>
        const navToggle = document.querySelector('.nav-toggle');
        const nav = document.querySelector('.nav');

        navToggle.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
    </script>
</body>
</html>
