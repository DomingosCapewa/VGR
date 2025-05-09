<?php
session_start();
include 'db_conexao.php';

if (isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

$error = '';

if (isset($_POST['login'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Validar se os campos não estão vazios
    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        // Verificar se o usuário existe e a senha está correta
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['id'];
            header('Location: admin.php');
            exit();
        } else {
            $error = 'Nome de usuário ou senha incorretos.';
        }
    } else {
        $error = 'Por favor, preencha todos os campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VGR Business Huambo</title>
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
                </ul>
            </nav>
        </div>
    </header>

    <div class="login-page section">
        <div class="container">
            <h2>Login</h2>

            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label for="username">Nome de Usuário:</label>
                    <input type="text" id="username" name="username" placeholder="Nome de Usuário" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" placeholder="Senha" required>
                </div>
                <button type="submit" name="login" class="button primary"><i class="bi bi-box-arrow-in-right"></i> Entrar</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> VGR Business Huambo</p>
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