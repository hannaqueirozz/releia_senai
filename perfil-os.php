<?php
// perfil-os.php
session_start();

// 1. Garantir que o usuário esteja pelo menos logado no sistema
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexao.php';

$mensagem_sucesso = "";
$mensagem_erro = "";
$eh_proprio_perfil = false;

// 2. Determinar qual OSC estamos visualizando
// Se houver um ID na URL (ex: perfil-os.php?id=5), o doador está visitando a OSC
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_os = intval($_GET['id']);
    
    // Se o ID da URL for igual ao ID logado e o tipo for organização, é o dono do perfil
    if ($_SESSION['tipo'] === 'organizacao' && $_SESSION['usuario_id'] == $id_os) {
        $eh_proprio_perfil = true;
    }
} else {
    // Se não há ID na URL, assume que a própria OSC quer ver seu painel
    if ($_SESSION['tipo'] === 'organizacao') {
        $id_os = $_SESSION['usuario_id'];
        $eh_proprio_perfil = true;
    } else {
        // Se um doador tentar entrar sem ID, manda de volta para o painel dele
        header("Location: doadorlog.php");
        exit();
    }
}

// 3. Processar envio do formulário de atualização (APENAS se for o dono do perfil)
if ($_SERVER["REQUEST_METHOD"] === "POST" && $eh_proprio_perfil) {
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? 'SP';
    $sobre = $_POST['sobre'] ?? '';
    $status_livros = $_POST['status_livros'] ?? '';
    $status_outros = $_POST['status_outros'] ?? '';
    $status_entregas = $_POST['status_entregas'] ?? '';

    if (empty($nome) || empty($telefone)) {
        $mensagem_erro = "O nome e o telefone são campos obrigatórios.";
    } else {
        try {
            $sql_update = "UPDATE oscs SET nome = ?, telefone = ?, cidade = ?, estado = ?, sobre = ?, status_livros = ?, status_outros = ?, status_entregas = ? WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            
            if ($stmt_update->execute([$nome, $telefone, $cidade, $estado, $sobre, $status_livros, $status_outros, $status_entregas, $id_os])) {
                $mensagem_sucesso = "Perfil e informações atualizados com sucesso!";
            } else {
                $mensagem_erro = "Erro ao atualizar os dados.";
            }
        } catch (PDOException $e) {
            $mensagem_erro = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}

// 4. Buscar os dados da OSC no banco
try {
    $sql = "SELECT * FROM oscs WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_os]);
    $os = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$os) {
        die("Organização não encontrada.");
    }
} catch (PDOException $e) {
    die("Erro ao carregar dados do perfil: " . $e->getMessage());
}

$page_title = "Perfil - " . htmlspecialchars($os['nome']);
require_once 'header-nav.php';
?>

<body class="profile-page">

<link rel="stylesheet" href="perfil-os.css?v=<?php echo time(); ?>">

<div class="container-perfil">

    <?php if (!empty($mensagem_sucesso)): ?>
        <p class="success-alert"><?php echo htmlspecialchars($mensagem_sucesso); ?></p>
    <?php endif; ?>
    <?php if (!empty($mensagem_erro)): ?>
        <p class="error-alert"><?php echo htmlspecialchars($mensagem_erro); ?></p>
    <?php endif; ?>

    <?php if ($eh_proprio_perfil): ?>
        <input type="checkbox" id="alternar-edicao-os" class="checkbox-toggle-perfil">
    <?php endif; ?>

    <section class="profile-card">
        <div class="profile-left">
            <div class="profile-avatar-placeholder">
                <div class="avatar-circle"></div>
            </div>
            <h3 class="profile-role">OSCs</h3>
            
            <?php if ($eh_proprio_perfil): ?>
                <label for="alternar-edicao-os" class="btn-edit-profile btn-label-trigger">EDITAR PERFIL</label>
            <?php endif; ?>
        </div>

        <div class="profile-divider"></div>

        <div class="profile-right">
            <p class="info-item"><strong>Nome:</strong> <?php echo htmlspecialchars($os['nome']); ?></p>
            <p class="info-item"><strong>Email:</strong> <?php echo htmlspecialchars($os['email']); ?></p>
            <p class="info-item"><strong>Telefone:</strong> <?php echo htmlspecialchars($os['telefone'] ?? 'Não informado'); ?></p>
            
            <div class="info-location-group">
                <p class="info-item"><strong>Cidade:</strong> <?php echo htmlspecialchars($os['cidade'] ?? 'Não informada'); ?></p>
                <p class="info-item"><strong>Estado:</strong> <?php echo htmlspecialchars($os['estado'] ?? 'SP'); ?></p>
            </div>
            
            <p class="info-item"><strong>CNPJ:</strong> <?php echo htmlspecialchars($os['cnpj'] ?? 'Não informado'); ?></p>
        </div>
    </section>

    <form action="perfil-os.php<?php echo !$eh_proprio_perfil ? '?id='.$id_os : ''; ?>" method="POST" class="form-profile-details">
        
        <?php if ($eh_proprio_perfil): ?>
            <section class="about-section secao-editar-perfil" id="formulario-editar-os">
                <h3>Alterar Dados Cadastrais</h3>
                
                <div class="form-grid-inputs">
                    <div class="status-field">
                        <label for="nome">Nome da Organização:</label>
                        <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($os['nome']); ?>" required>
                    </div>

                    <div class="status-field">
                        <label for="telefone">Telefone:</label>
                        <input type="text" name="telefone" id="telefone" value="<?php echo htmlspecialchars($os['telefone'] ?? ''); ?>" required>
                    </div>

                    <div class="status-field">
                        <label for="cidade">Cidade:</label>
                        <input type="text" name="cidade" id="cidade" value="<?php echo htmlspecialchars($os['cidade'] ?? ''); ?>">
                    </div>

                    <div class="status-field">
                        <label for="estado">Estado (UF):</label>
                        <input type="text" name="estado" id="estado" maxlength="2" value="<?php echo htmlspecialchars($os['estado'] ?? 'SP'); ?>">
                    </div>
                </div>
            </section>
        <?php endif; ?>
        
        <section class="about-section">
            <h3>Sobre o Nosso Trabalho</h3>
            <div class="textarea-wrapper">
                <textarea name="sobre" id="sobre" placeholder="Escreva aqui sobre a história da sua organização, projetos activos e como as doações de livros ajudam a comunidade..." rows="5" <?php echo !$eh_proprio_perfil ? 'disabled' : ''; ?>><?php echo htmlspecialchars($os['sobre'] ?? ''); ?></textarea>
            </div>
        </section>

        <section class="status-section-box">
            <h3>Status da Oscs</h3>
            
            <div class="status-field">
                <label for="status_livros">Doações de Livros:</label>
                <select name="status_livros" id="status_livros" <?php echo !$eh_proprio_perfil ? 'disabled' : ''; ?>>
                    <option value="Está recebendo livros" <?php echo ($os['status_livros'] ?? '') === 'Está recebendo livros' ? 'selected' : ''; ?>>Está recebendo livros</option>
                    <option value="Doações de livros suspensas temporariamente" <?php echo ($os['status_livros'] ?? '') === 'Doações de livros suspensas temporariamente' ? 'selected' : ''; ?>>Doações de livros suspensas temporariamente</option>
                </select>
            </div>

            <div class="status-field">
                <label for="status_outros">Outros Tipos de Doações:</label>
                <select name="status_outros" id="status_outros" <?php echo !$eh_proprio_perfil ? 'disabled' : ''; ?>>
                    <option value="Está recebendo outras doações" <?php echo ($os['status_outros'] ?? '') === 'Está recebendo outras doações' ? 'selected' : ''; ?>>Está recebendo outras doações</option>
                    <option value="Não está recebendo outros itens no momento" <?php echo ($os['status_outros'] ?? '') === 'Não está recebendo outros itens no momento' ? 'selected' : ''; ?>>Não está recebendo outros itens no momento</option>
                </select>
            </div>

            <div class="status-field">
                <label for="status_entregas">Logística de Recebimento:</label>
                <select name="status_entregas" id="status_entregas" <?php echo !$eh_proprio_perfil ? 'disabled' : ''; ?>>
                    <option value="Entregas pessoais" <?php echo ($os['status_entregas'] ?? '') === 'Entregas pessoais' ? 'selected' : ''; ?>>Entregas pessoais</option>
                    <option value="Retiramos a domicílio" <?php echo ($os['status_entregas'] ?? '') === 'Retiramos a domicílio' ? 'selected' : ''; ?>>Retiramos a domicílio</option>
                    <option value="Recebimento apenas via Correios" <?php echo ($os['status_entregas'] ?? '') === 'Recebimento apenas via Correios' ? 'selected' : ''; ?>>Recebimento apenas via Correios</option>
                </select>
            </div>

            <?php if ($eh_proprio_perfil): ?>
                <div class="status-actions">
                    <button type="submit" class="btn-save-status">Salvar Alterações</button>
                </div>
            <?php endif; ?>
        </section>

    </form>
</div>

<footer class="site-footer">
    <p>© RELEIA | todos os direitos reservados</p>
</footer>

</body>
</html>