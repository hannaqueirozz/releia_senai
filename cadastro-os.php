<?php
// cadastro-osc.php
session_start();

// Redireciona caso o usuário já esteja logado
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
    $estado    = $_POST['estado'];   // Capturando o estado dinamicamente do formulário
    $cnpj = $_POST['cpfCnpj'] ?? $_POST['cnpj'] ?? '';

    if ($senha !== $confirmar) {
        $erro = "As senhas não conferem!";
    } else {
        try {
            // Verifica se o e-mail já existe na tabela de OSCs
            $stmtCheck = $pdo->prepare("SELECT id FROM oscs WHERE email = ?");
            $stmtCheck->execute([$email]);
            
            if ($stmtCheck->fetch()) {
                $erro = "Este e-mail já está sendo utilizado!";
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                
                // CORREÇÃO AQUI: Removido a coluna 'tipo' que causava o erro 1054
                // Inserindo exatamente as 7 colunas correspondentes à tabela 'oscs'
                $sql = "INSERT INTO oscs (nome, email, senha, telefone, cidade, estado, cnpj) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                // Enviando os 7 parâmetros corretos no execute
                if ($stmt->execute([$nome, $email, $senhaHash, $telefone, $cidade, $estado, $cnpj])) {
                    echo "<script>alert('Cadastro de Organização realizado com sucesso!'); window.location.href='login.php';</script>";
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
    <title>Cadastro Organização - RELEIA</title>
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
                <h2 class="welcome">CADASTRE SUA <br />ORGANIZAÇÃO!</h2>
                <p class="description">
                    "Apenas porque não posso vê-lo, não significa que eu não posso acreditar." - O Estranho Mundo de Jack
                </p>
            </div>
            <div class="right-panel">
                <form class="form" method="POST" action="cadastro-os.php">
                    <label for="nome">Nome da Instituição</label>
                    <input type="text" id="nome" name="nome" placeholder="Nome completo da OSC" required />

                    <label for="email">Email Corporativo</label>
                    <input type="email" id="email" name="email" placeholder="instituicao@mail.com" required />

                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="••••••••" required />

                    <label for="confirmar">Confirme a senha</label>
                    <input type="password" id="confirmar" name="confirmar" placeholder="••••••••" required />

                    <label for="telefone">Telefone de Contato</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(11) 99999-9999" required />

                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" name="cidade" placeholder="Ex: Itabuna" required />

                    <label for="estado">Estado</label>
                    <input type="text" id="estado" name="estado" placeholder="Ex: BA" required />

                    <label for="cpfCnpj">CNPJ</label>
                    <input type="text" id="cpfCnpj" name="cpfCnpj" placeholder="00.000.000/0001-00" required>  

                    <?php if (!empty($erro)): ?>
                        <p style="color: #ff4d4d; font-size: 0.9rem; margin-top: 10px; font-weight: bold;">
                            <?php echo htmlspecialchars($erro); ?>
                        </p>
                    <?php endif; ?>

                    <div class="buttons">
                        <button type="submit" class="btn register">Registrar Unidade</button>
                        <a href="login.php" class="btn back">Voltar ao login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>