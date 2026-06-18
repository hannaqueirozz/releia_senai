<?php
// doadorlog.php
session_start();

// 1. Garantir que APENAS doadores logados acessem esta página
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'doador') {
    header("Location: login.php");
    exit();
}

// 2. Importar a conexão com o banco de dados
require_once 'conexao.php';

// 3. Buscar todas as Organizações (OSCs) cadastradas no sistema
try {
    $sql = "SELECT id, nome, email, telefone, cidade, estado, sobre, status_livros, status_outros, status_entregas 
            FROM oscs 
            ORDER BY nome ASC";
    $stmt = $pdo->query($sql);
    $organizacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro_banco = "Erro ao carregar as organizações: " . $e->getMessage();
}

// 4. Configurar o título antes de chamar o menu dinâmico
$page_title = "RELEIA | Unidades Disponíveis";

require_once 'header-nav.php'; 
?>

<link rel="stylesheet" href="doadorlog.css?v=<?php echo time(); ?>">

<section class="hero-banner">
    <div class="banner-content">
        <h2>DOE LIVROS, TRANSFORME VIDAS!</h2>
    </div>
</section>

<div class="container-unidades">
    
    <div class="unidades-header">
        <h2>Organizações Parceiras</h2>
        <p>Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>! Conheça as instituições que precisam de doações de livros:</p>
    </div>

    <?php if (isset($erro_banco)): ?>
        <p class="error-msg"><?php echo $erro_banco; ?></p>
    <?php endif; ?>

    <div class="container-perfil">
        <?php if (count($organizacoes) > 0): ?>
            <?php foreach ($organizacoes as $os): ?>
                
                <article class="card-perfil">
                    <div class="avatar-placeholder"></div>
                    
                    <div class="perfil-direita">
                        <h3 class="nome-resumido"><?php echo htmlspecialchars($os['nome']); ?></h3>
                        <p class="os-badge-text"><strong>Localização:</strong> <?php echo htmlspecialchars($os['cidade']); ?> - <?php echo htmlspecialchars($os['estado']); ?></p>
                        
                        <p class="os-about">
                            <strong>Sobre o trabalho:</strong><br>
                            <?php echo !empty($os['sobre']) ? nl2br(htmlspecialchars($os['sobre'])) : 'Esta instituição ainda não adicionou uma descrição.'; ?>
                        </p>
                        
                        <div class="status-list-block">
                            <p><strong>Livros:</strong> <?php echo htmlspecialchars($os['status_livros'] ?? 'Não informado'); ?></p>
                            <p><strong>Outros itens:</strong> <?php echo htmlspecialchars($os['status_outros'] ?? 'Não informado'); ?></p>
                            <p><strong>Logística:</strong> <?php echo htmlspecialchars($os['status_entregas'] ?? 'Não informado'); ?></p>
                        </div>
                        
                        <p class="os-contact-text"><strong>Contato:</strong> <?php echo htmlspecialchars($os['telefone']); ?> | <?php echo htmlspecialchars($os['email']); ?></p>
                    </div>

                    <div class="os-card-footer">
                        <a href="perfil-os.php?id=<?php echo $os['id']; ?>" class="btn-ver-perfil">VISITAR PERFIL</a>
                    </div>
                </article>

            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-msg">Nenhuma organização foi cadastrada no sistema até o momento.</p>
        <?php endif; ?>
    </div>

</div>

</main> 

<footer class="site-footer">
    <p>© RELEIA | todos os direitos reservados</p>
</footer>

</body>
</html>