<?php
// login.php
session_start();

// Se o usuário JÁ estiver logado, redireciona direto para a página certa dele
if (isset($_SESSION['usuario_id']) && isset($_SESSION['tipo'])) {
    if ($_SESSION['tipo'] === 'doador') {
        header("Location: doadorlog.php");
    } elseif ($_SESSION['tipo'] === 'organizacao') {
        header("Location: oslog.php"); // ALTERADO
    }
    exit();
}

require_once 'conexao.php'; 

$erro = ""; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    try {
        $sql_doador = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql_doador);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            if (password_verify($senha, $usuario['senha']) || $senha == $usuario['senha']) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['tipo'] = 'doador';
                header("Location: doadorlog.php"); 
                exit();
            } else {
                $erro = "Senha incorreta!";
            }
        } else {
            $sql_org = "SELECT * FROM oscs WHERE email = ?";
            $stmt_org = $pdo->prepare($sql_org);
            $stmt_org->execute([$email]);
            $osc = $stmt_org->fetch(PDO::FETCH_ASSOC);

            if ($osc) {
                if (password_verify($senha, $osc['senha']) || $senha == $osc['senha']) {
                    $_SESSION['usuario_id'] = $osc['id'];
                    $_SESSION['usuario_nome'] = $osc['nome'];
                    $_SESSION['tipo'] = 'organizacao';
                    header("Location: oslog.php"); // ALTERADO
                    exit();
                } else {
                    $erro = "Senha incorreta!";
                }
            } else {
                $erro = "Usuário não encontrado!";
            }
        }
    } catch (PDOException $e) {
        $erro = "Erro no banco de dados: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tela de Login - RELEIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="cadastro.css" />
</head>
<body class="login-page">
    <div class="container">
        <div class="form-wrapper">
            <div class="left-panel">
                <div class="logo">RELEIA</div>
                <h2 class="welcome">BEM-VINDO DE VOLTA!</h2>
                <p class="description">
                    "A imaginação é mais importante que o conhecimento." - Albert Einstein
                </p>
            </div>

            <div class="right-panel">
                <h2>Entrar</h2>
                <form class="form" method="POST" action="login.php">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="name@mail.com" required />

                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="••••••••" required />

                    <?php if (!empty($erro)): ?>
                        <p style="color: #ff4d4d; font-size: 0.9rem; margin-bottom: 10px; font-weight: bold;">
                            <?php echo htmlspecialchars($erro); ?>
                        </p>
                    <?php endif; ?>

                    <div class="buttons">
                        <a href="recuperar-senha.php">Esqueceu a senha?</a>
                        <button type="submit" class="btn login">Entrar</button>
                        <a href="cadastro.php" class="btn back">Cadastrar-se</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>