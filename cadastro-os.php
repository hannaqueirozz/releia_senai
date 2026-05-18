<?php
session_start();

// Se o usuário já estiver logado, redireciona direto para o painel correto
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['usuario_tipo'] === 'osc') {
        header("Location: painel-osc.php");
    } else {
        header("Location: painel-doador.php");
    }
    exit();
}

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
    $telefone = $_POST['telefone'];
    $cidade = $_POST['cidade'];
    $cnpj = $_POST['cnpj'];

    // Verifica se as senhas conferem
    if ($senha !== $confirmar) {
        die("As senhas não conferem!");
    }

    // Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Ajustado para inserir os 6 campos necessários na tabela oscs
    $sql = "INSERT INTO oscs (nome, email, senha, telefone, cidade, cnpj) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // "ssssss" indica que são 6 parâmetros do tipo string
    $stmt->bind_param("ssssss", $nome, $email, $senhaHash, $telefone, $cidade, $cnpj);

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
            <!-- Painel esquerdo -->
            <div class="left-panel">
                <p class="description">
                    "Apenas porque não posso vê-lo, não significa que eu não
                    posso creditar." - O Estranho Mundo de Jack
                </p>
                <h2 class="welcome">FAÇA SEU
                    <br />CADASTRO!
                </h2>
                <a href="index.php">
                    <div class="logo">RELEIA</div>
                </a>
            </div>

            <!-- Painel direito -->
            <div class="right-panel">
                <form class="form" method="POST" action="">
                    <label for="nome">Nome da Organização</label>
                    <input type="text" id="nome" name="nome" placeholder="Nome completo" required />

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="name@mail.com" required />

                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="••••••••" required />

                    <label for="confirmar">Confirme sua senha</label>
                    <input type="password" id="confirmar" name="confirmar" placeholder="••••••••" required />

                    <label for="telefone">Insira seu número</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(11)999999999" required maxlength="11"/>

                    <label for="cidade">Insira sua cidade</label>
                    <input type="text" id="cidade" name="cidade" placeholder="Insira sua cidade" required>

                    <label for="Cnpj">CNPJ</label>
                    <input type="text" id="cnpj" name="cnpj" placeholder="00.000.000/0000-00"
                        maxlength="18" pattern="(\d{3}\.?\d{3}\.?\d{3}-?\d{2})|(\d{2}\.?\d{3}\.?\d{3}/?\d{4}-?\d{2})"
                        title="Digite um CNPJ válido" required>  


                    <div class="buttons">
                        <button type="submit" class="btn register">Registrar</button>
                        <a href="login.php" class="btn back" role="button">Voltar ao login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
