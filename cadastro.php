<?php
// cadastro.php
session_start();

if (isset($_SESSION['usuario_id']) && isset($_SESSION['tipo'])) {
    if ($_SESSION['tipo'] === 'doador') {
        header("Location: doadorlog.php");
    } elseif ($_SESSION['tipo'] === 'organizacao') {
        header("Location: oslog.php"); // ALTERADO
    }
    exit();
}

require_once 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tela de Cadastro - RELEIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="cadastro.css" />
</head>
<body class="login-page">
    <div class="container">
        <div class="form-wrapper">
            <div class="left-panel">
                <a href="index.php">
                    <div class="logo">RELEIA</div>
                </a>
                <h2 class="welcome">FAÇA SEU <br />CADASTRO!</h2>
                <p class="description">
                    "A verdadeira coragem está em enfrentar o perigo quando você está com medo." - L. Frank Baum
                </p>
            </div>

            <div class="right-panel">
                <div class="selection-container">
                    <h3 class="selection-title">Como você deseja se juntar a nós?</h3>
                    <div class="selection-options">
                        <a href="cadastro-doador.php" class="btn-option">
                            <div class="text-group">
                                <strong>Cadastrar como doador</strong>
                            </div>
                        </a>
                        <a href="cadastro-os.php" class="btn-option">
                            <div class="text-group">
                                <strong>Cadastrar minha organização</strong>
                            </div>
                        </a>
                    </div>
                    <div class="footer-links">
                        <a href="login.php" class="back-link">Voltar ao login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>