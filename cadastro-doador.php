<?php
// cadastro-doador.php
session_start();

if (isset($_SESSION['usuario_id']) && isset($_SESSION['tipo'])) {
    if ($_SESSION['tipo'] === 'doador') {
        header("Location: doadorlog.php");
    } elseif ($_SESSION['tipo'] === 'organizacao') {
        header("Location: oslog.php"); 
    }
    exit();
}

require_once 'conexao.php';

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome      = $_POST['nome'];
    $email     = $_POST['email'];
    $senha     = $_POST['senha'];
    $confirmar = $_POST['confirmar'];
    $telefone  = $_POST['telefone'];
    $cidade    = $_POST['cidade'];
    $cpf       = $_POST['cpfCnpj'];
    $estado    = $_POST['estado'];
    $tipo      = "doador"; // Define explicitamente o tipo de conta que está sendo criada

    if ($senha !== $confirmar) {
        $erro = "As senhas não conferem!";
    } else {
        try {
            // Utilizando corretamente a nova coluna 'id'
            $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmtCheck->execute([$email]);
            
            if ($stmtCheck->fetch()) {
                $erro = "Este e-mail já está sendo utilizado!";
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                
                // Adicionado 'tipo' à lista de colunas para inserção no banco de dados
                $sql = "INSERT INTO usuarios (nome, email, senha, telefone, cidade, estado, cpf, tipo) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$nome, $email, $senhaHash, $telefone, $cidade, $estado, $cpf, $tipo])) {
                    echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href='login.php';</script>";
                    exit();
                } else {
                    $erro = "Erro ao realizar o cadastro.";
                }
            }
        } catch (PDOException $e) {
            $erro = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cadastro Doador - RELEIA</title>
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
                    "Apenas porque não posso vê-lo, não significa que eu não posso acreditar." - O Estranho Mundo de Jack
                </p>
            </div>
            <div class="right-panel">
                <form class="form" method="POST" action="cadastro-doador.php">
                    <label for="nome">Nome completo</label>
                    <input type="text" id="nome" name="nome" placeholder="Nome completo" required />

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="name@mail.com" required />

                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="••••••••" required />

                    <label for="confirmar">Confirme sua senha</label>
                    <input type="password" id="confirmar" name="confirmar" placeholder="••••••••" required />

                    <label for="telefone">Insira seu número</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(11) 99999-9999" required />

                    <label for="cidade">Insira sua cidade</label>
                    <input type="text" id="cidade" name="cidade" placeholder="Ex: Itabuna" required />

                    <label for="cidade">Insira seu estado</label>
                    <input type="text" id="estado" name="estado" placeholder="Ex: BA" required />

                    <label for="cpfCnpj">CPF</label>
                    <input type="text" id="cpfCnpj" name="cpfCnpj" placeholder="000.000.000-00" required>  

                    <?php if (!empty($erro)): ?>
                        <p style="color: #ff4d4d; font-size: 0.9rem; margin-top: 10px; font-weight: bold;">
                            <?php echo htmlspecialchars($erro); ?>
                        </p>
                    <?php endif; ?>

                    <div class="buttons">
                        <button type="submit" class="btn register">Registrar</button>
                        <a href="login.php" class="btn back">Voltar ao login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>