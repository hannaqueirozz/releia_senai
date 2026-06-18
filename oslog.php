<?php
// oslog.php
session_start();

// 1. Garantir que APENAS organizações logadas acessem esta página
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'organizacao') {
    header("Location: login.php");
    exit();
}

// 2. Importar a conexão com o banco de dados
require_once 'conexao.php';

$mensagem_status = "";
$livros = []; 

// ==========================================================================
// PROCESSAMENTO DO ENVIO DE SOLICITAÇÃO (E-MAIL E ALTERAÇÃO DE STATUS)
// ==========================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['enviar_solicitacao'])) {
    $id_livro = $_POST['modal_id_livro'];
    $mensagem_org = $_POST['mensagem_organizacao'] ?? '';
    $org_nome = $_SESSION['usuario_nome'];

    try {
        // Buscar dados do livro e o e-mail do doador dono dele
        $sql_busca = "SELECT livros.nome AS livro_nome, usuarios.email AS doador_email, usuarios.nome AS doador_nome 
                      FROM livros 
                      LEFT JOIN usuarios ON livros.usuario_id = usuarios.id 
                      WHERE livros.id_livro = ?";
        $stmt_busca = $pdo->prepare($sql_busca);
        $stmt_busca->execute([$id_livro]);
        $dados_info = $stmt_busca->fetch(PDO::FETCH_ASSOC);

        if ($dados_info && !empty($dados_info['doador_email'])) {
            $to = $dados_info['doador_email'];
            $subject = "=?UTF-8?B?".base64_encode("Interesse no seu livro: ".$dados_info['livro_nome'])."?.=";
            
            // Corpo do e-mail estruturado em HTML
            $message = "
            <html>
            <head>
                <title>Solicitação de Livro - RELEIA</title>
            </head>
            <body>
                <h2>Olá, " . htmlspecialchars($dados_info['doador_nome']) . "!</h2>
                <p>A organização <strong>{$org_nome}</strong> demonstrou interesse em receber a doação do seu livro: <strong>" . htmlspecialchars($dados_info['livro_nome']) . "</strong>.</p>
                <p><strong>Mensagem deixada pela organização:</strong></p>
                <blockquote style='background: #f4f9fc; border-left: 4px solid #8BB0C9; padding: 10px; font-style: italic;'>
                    " . nl2br(htmlspecialchars($mensagem_org)) . "
                </blockquote>
                <p>Por favor, entre em contato respondendo diretamente a este e-mail para combinar a entrega.</p>
                <br>
                <p>Atenciosamente,<br><strong>Equipe RELEIA</strong></p>
            </body>
            </html>
            ";

            // Headers para envio correto de e-mail em formato HTML UTF-8
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: RELEIA <noreply@releia.com.br>" . "\r\n";

            // Envia o e-mail usando a função mail nativa do PHP
            if (mail($to, $subject, $message, $headers)) {
                
                // Atualiza o status do livro para 'Solicitado' para que ele suma desta tela
                $sql_update = "UPDATE livros SET status = 'Solicitado' WHERE id_livro = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([$id_livro]);

                $mensagem_status = "<p class='success-msg' style='color: green; background: #e8f8f0; padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px;'>Solicitação enviada com sucesso ao e-mail do doador! O livro foi reservado.</p>";
            } else {
                $mensagem_status = "<p class='error-msg' style='color: red; background: #fdf2f2; padding: 12px; border-radius: 6px; font-weight: bold; margin-bottom: 20px;'>O status foi alterado, mas o servidor local não pôde disparar o e-mail (necessita de servidor SMTP ativo).</p>";
            }
        } else {
            $mensagem_status = "<p class='error-msg' style='color: red; padding: 12px; font-weight: bold; margin-bottom: 20px;'>Erro: Informações do doador não encontradas.</p>";
        }
    } catch (PDOException $e) {
        $mensagem_status = "<p class='error-msg' style='color: red; padding: 12px; font-weight: bold; margin-bottom: 20px;'>Erro no banco de dados: " . $e->getMessage() . "</p>";
    }
}

// 3. Buscar os livros cadastrados filtrando apenas pelos que estão com status 'Disponível'
try {
    $sql = "SELECT livros.*, usuarios.nome AS doador_nome 
            FROM livros 
            LEFT JOIN usuarios ON livros.usuario_id = usuarios.id 
            WHERE livros.status = 'Disponível'
            ORDER BY livros.nome ASC";
            
    $stmt = $pdo->query($sql);
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro_banco = "Erro ao carregar livros: " . $e->getMessage();
}

$page_title = "RELEIA | Painel da Organização";
require_once 'header-nav.php'; 
?>

<link rel="stylesheet" href="oslog.css?v=<?php echo time(); ?>">

<section class="hero-banner">
    <div class="banner-content">
        <h2>DOE LIVROS, TRANSFORME VIDAS!</h2>
    </div>
</section>

<section class="panel-container">
    <div class="panel-header">
        <p>Bem-vindo de volta, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>
    </div>

    <div class="panel-content">
        <h3>Livros Cadastrados para Doação</h3>
        
        <div class="status-container-global">
            <?php echo $mensagem_status; ?>
        </div>

        <?php if (isset($erro_banco)): ?>
            <p class="error-msg" style="color: red; font-weight: bold;"><?php echo $erro_banco; ?></p>
        <?php endif; ?>

        <div class="books-grid">
            <?php if (is_array($livros) && count($livros) > 0): ?>
                <?php foreach ($livros as $livro): ?>
                    <article class="book-card">
                        <div class="book-cover">
                            <?php if (!empty($livro['foto'])): ?>
                                <img src="<?php echo htmlspecialchars($livro['foto']); ?>" alt="Capa do livro <?php echo htmlspecialchars($livro['nome']); ?>">
                            <?php else: ?>
                                <img src="uploads/capa-padrao.png" alt="Capa indisponível">
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h4><?php echo htmlspecialchars($livro['nome']); ?></h4>
                            <p class="book-author"><strong>Autor:</strong> <?php echo htmlspecialchars($livro['autor']); ?></p>
                            <p class="book-genre"><strong>Gênero:</strong> <?php echo htmlspecialchars($livro['genero']); ?></p>
                            <p class="book-donor"><strong>Cadastrado por:</strong> <?php echo htmlspecialchars($livro['doador_nome'] ?? 'Doador Perceiro'); ?></p>
                            
                            <button type="button" class="btn-solicitar" onclick="abrirModalSolicitacao('<?php echo $livro['id_livro']; ?>', '<?php echo htmlspecialchars($livro['nome'], ENT_QUOTES); ?>')">Solicitar Livro</button>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-msg">Nenhum livro disponível para doação no momento.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<div id="modalSolicitacao" class="modal-overlay">
    <div class="modal-box">
        <span class="modal-fechar" onclick="fecharModalSolicitacao()">&times;</span>
        <h3 class="modal-titulo">Solicitar Livro: <span id="modal-livro-nome" style="color: #4A5D6B;"></span></h3>
        <hr style="border: 0; height: 2px; background: #8BB0C9; width: 50px; margin: 10px 0 20px 0;">
        
        <form method="POST" action="oslog.php">
            <input type="hidden" name="enviar_solicitacao" value="1">
            <input type="hidden" name="modal_id_livro" id="modal-livro-id">
            
            <div style="text-align: left; margin-bottom: 15px;">
                <label style="font-weight: 600; font-size: 0.9rem; color: #4A5D6B; display: block; margin-bottom: 8px;">Escreva uma mensagem para o doador:</label>
                <textarea name="mensagem_organizacao" rows="5" placeholder="Olá! Somos da organização X e gostaríamos de receber este livro para nossos projetos de leitura..." style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #D1D5DB; border-radius: 6px; font-family: sans-serif; resize: vertical;" required></textarea>
            </div>
            
            <button type="submit" style="background-color: #4A5D6B; color: #FFFFFF; border: none; padding: 12px 25px; font-size: 0.95rem; font-weight: 700; border-radius: 6px; cursor: pointer; text-transform: uppercase; width: 100%; letter-spacing: 0.5px;">Enviar E-mail de Solicitação</button>
        </form>
    </div>
</div>

<script>
function abrirModalSolicitacao(id, nome) {
    document.getElementById('modal-livro-id').value = id;
    document.getElementById('modal-livro-nome').innerText = nome;
    document.getElementById('modalSolicitacao').style.display = 'flex';
}

function fecharModalSolicitacao() {
    document.getElementById('modalSolicitacao').style.display = 'none';
}

window.onclick = function(event) {
    var modal = document.getElementById('modalSolicitacao');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<footer class="site-footer">
    <p>© RELEIA | todos os direitos reservados</p>
</footer>

</body>
</html>