<?php
// header-nav.php - Componente de navegação reutilizável
if (!isset($page_title)) {
    $page_title = "RELEIA";
}

$is_logged_in = isset($_SESSION['usuario_id']);
$user_type = $_SESSION['tipo'] ?? null;

// Define para onde o botão "Início" vai mandar o usuário baseado no tipo dele
$inicio_url = "index.php"; 
if ($is_logged_in) {
    if ($user_type === 'doador') {
        $inicio_url = "doadorlog.php"; 
    } elseif ($user_type === 'organizacao') {
        $inicio_url = "oslog.php";    
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="index.css" />
</head>
<body>
    <header class="site-header">
        <div class="header-content">
            <a href="<?php echo $inicio_url; ?>">
                <img src="gatinho-releia.png" alt="logo do site" class="logo">
            </a>
            <h1 class="brand">R E L E I A</h1>
            <nav class="nav">
                <a href="<?php echo $inicio_url; ?>">Início</a>
                <a href="sobre.php">Sobre</a>
                <a href="contato.php">Contato</a>
                
                <?php if ($is_logged_in): ?>
                    <?php if ($user_type === 'doador'): ?>
                        <a href="perfil-doador.php" class="profile-link">Meu Perfil</a> 
                        
                    <?php elseif ($user_type === 'organizacao'): ?>
                        <a href="perfil-os.php" class="profile-link">Meu Perfil</a> 
                    <?php endif; ?>
                    
                    <a href="logout.php" class="logout-link">Logout</a>
                <?php else: ?>
                    <a href="cadastro.php" class="signup-link">Cadastre-se</a>
                    <a href="login.php" class="login-link">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main></main>