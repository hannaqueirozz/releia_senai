<?php
// Conexão com o banco
$servername = "localhost";
$username = "root";      // seu usuário do MySQL
$password = "";          // sua senha do MySQL
$dbname = "sua_base";  // nome do banco

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar'];

    // Verifica se as senhas conferem
    if ($senha !== $confirmar) {
        die("As senhas não conferem!");
    }

    // Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere no banco
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nome, $email, $senhaHash);

    if ($stmt->execute()) {
        echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tela de Cadastro</title>
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
                <p class="description">
                    "A verdadeira coragem está em enfrentar o perigo quando você está com medo." - L. Frank Baum
                </p>
                <h2 class="welcome">FAÇA SEU <br />CADASTRO!</h2>
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