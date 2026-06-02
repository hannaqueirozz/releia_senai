<?php
// Conexão com o banco
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "sua_base"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$erro = ""; // Inicializa a variável de erro

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // 1. Tenta buscar na tabela de DOADORES
    $sql_doador = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql_doador);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        // Verifica a senha (ajuste se não usar password_hash)
        if (password_verify($senha, $usuario['senha']) || $senha == $usuario['senha']) {
            session_start();
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['tipo'] = 'doador';
            header("Location: doadorlog.php"); // Página do doador
            exit();
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        // 2. Se não achou no doador, tenta buscar na tabela de ORGANIZAÇÕES
        $sql_org = "SELECT * FROM oscs WHERE email = ?";
        $stmt_org = $conn->prepare($sql_org);
        $stmt_org->bind_param("s", $email);
        $stmt_org->execute();
        $result_org = $stmt_org->get_result();

        if ($result_org->num_rows === 1) {
            $usuario = $result_org->fetch_assoc();
            if (password_verify($senha, $usuario['senha']) || $senha == $usuario['senha']) {
                session_start();
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['tipo'] = 'organizacao';
                header("Location: onglog.php"); // Página da organização
                exit();
            } else {
                $erro = "Senha incorreta!";
            }
        } else {
            $erro = "Usuário não encontrado!";
        }
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
                <form class="form" method="POST" action="">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="name@mail.com" required />

                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="••••••••" required />

                    <?php if (!empty($erro)): ?>
                        <p style="color: #ff4d4d; font-size: 0.9rem; margin-bottom: 10px; font-weight: bold;">
                            <?php echo $erro; ?>
                        </p>
                    <?php endif; ?>

                    <div class="buttons">
                        <a href="recuperar-senha.php">Esqueceu a senha?</a>
                        <button type="submit" class="btn login">Entrar</button>
                        <a href="cadastro.php" class="btn back" role="button">Cadastrar-se</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>