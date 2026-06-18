<?php
// solicitar-livro.php
session_start();

// 1. Garantir que o usuário está logado para poder solicitar
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Importar a conexão com o banco de dados
require_once 'conexao.php';

$mensagem_status = "";
$livro = null;

// Pegar o ID do livro enviado pela URL (Ex: solicitar-livro.php?id=5)
$id_livro = $_GET['id'] ?? null;

if (!$id_livro) {
    header("Location: index.php");
    exit();
}

// ==========================================================================
// PROCESSAMENTO DO FORMULÁRIO DE ENVIO DE MENSAGEM
// ==========================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['enviar_solicitacao'])) {
    $mensagem_user = $_POST['mensagem_usuario'] ?? '';
    $solicitante_nome = $_SESSION['usuario_nome'];

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
            $subject = "=?UTF-8?B?".base64_encode("Alguém quer seu livro: ".$dados_info['livro_nome'])."?.=";
            
            // Corpo do e-mail em HTML
            $message = "
            <html>
            <head><title>Interesse no seu livro - RELEIA</title></head>
            <body>
                <h2>Olá, " . htmlspecialchars($dados_info['doador_nome']) . "!</h2>
                <p>O usuário <strong>{$solicitante_nome}</strong> demonstrou interesse em adotar o seu livro: <strong>" . htmlspecialchars($dados_info['livro_nome']) . "</strong>.</p>
                <p><strong>Mensagem enviada por ele:</strong></p>
                <blockquote style='background: #f4f9fc; border-left: 4px solid #8BB0C9; padding: 10px; font-style: italic;'>
                    " . nl2br(htmlspecialchars($mensagem_user)) . "
                </blockquote>
                <p>Combine a entrega respondendo diretamente para o e-mail do interessado ou através da plataforma.</p>
                <br>
                <p>Atenciosamente,<br><strong>Equipe RELEIA</strong></p>
            </body>
            </html>
            ";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: RELEIA <noreply@releia.com.br>" . "\r\n";

            if (mail($to, $subject, $message, $headers)) {
                // Atualiza o status para sumir das listagens gerais
                $sql_update = "UPDATE livros SET status = 'Solicitado' WHERE id_livro = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([$id_livro]);

                $mensagem_status = "sucesso";
            } else {
                $mensagem_status = "erro_envio";
            }
        }
    } catch (PDOException $e) {
        $mensagem_status = "erro_banco";
    }
}

// Buscar dados do livro para exibir na tela do Pop-up fixo
try {
    $sql_livro = "SELECT * FROM livros WHERE id_livro = ? AND status = 'Disponível'";
    $stmt_livro = $pdo->prepare($sql_livro);
    $stmt_livro->execute([$id_livro]);
    $livro = $stmt_livro->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $livro = null;
}

$page_title = "RELEIA | Solicitar Livro";
require_once 'header-nav.php'; 
?>

<link rel="stylesheet" href="oslog.css?v=<?php echo time(); ?>">

<div class="panel-container" style="margin-top: 40px; min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    
    <?php if ($mensagem_status === "sucesso"): ?>
        <div style="text-align: center; max-width: 500px; padding: 30px; background: #FFFFFF; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h2 style="color: #2e7d32;">✓ Solicitação Enviada!</h2>
            <p style="color: #555; margin: 15px 0;">Sua mensagem foi enviada diretamente para o e-mail do doador. Fique atento à sua caixa de entrada para combinarem a entrega!</p>
            <a href="index.php" style="display: inline-block; background: #4A5D6B; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 10px;">Voltar para o Início</a>
        </div>

    <?php elseif (!$livro): ?>
        <div style="text-align: center; max-width: 500px; padding: 30px; background: #FFFFFF; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h2 style="color: #c62828;">Livro Indisponível</h2>
            <p style="color: #555; margin: 15px 0;">Desculpe, este livro já foi solicitado por outra pessoa ou não foi encontrado.</p>
            <a href="index.php" style="display: inline-block; background: #4A5D6B; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;">Ver outros livros</a>
        </div>

    <?php else: ?>
        <div style="background-color: #FFFFFF; padding: 35px 30px; border-radius: 12px; max-width: 500px; width: 100%; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15); text-align: center; box-sizing: border-box;">
            <h3 style="font-size: 1.4rem; margin: 0; color: #333333; text-align: left;">Solicitar Livro: <span style="color: #4A5D6B;"><?php echo htmlspecialchars($livro['nome']); ?></span></h3>
            <hr style="border: 0; height: 2px; background: #8BB0C9; width: 50px; margin: 10px 0 20px 0; text-align: left;">
            
            <form method="POST" action="solicitar-livro.php?id=<?php echo $id_livro; ?>">
                <input type="hidden" name="enviar_solicitacao" value="1">
                
                <div style="text-align: left; margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: #4A5D6B; display: block; margin-bottom: 8px;">Escreva uma mensagem para o doador:</label>
                    <textarea name="mensagem_usuario" rows="5" placeholder="Olá! Gostei muito do seu livro e gostaria de pegá-lo emprestado/doado. Como podemos combinar?" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #D1D5DB; border-radius: 6px; font-family: sans-serif; resize: vertical;" required></textarea>
                </div>
                
                <button type="submit" style="background-color: #4A5D6B; color: #FFFFFF; border: none; padding: 12px 25px; font-size: 0.95rem; font-weight: 700; border-radius: 6px; cursor: pointer; text-transform: uppercase; width: 100%; letter-spacing: 0.5px;">Enviar Mensagem ao Doador</button>
                <a href="index.php" style="display: block; margin-top: 15px; color: #777; font-size: 0.9rem; text-decoration: none;">Cancelar</a>
            </form>
        </div>
    <?php endif; ?>

</div>

<footer class="site-footer" style="margin-top: 40px;">
    <p>© RELEIA | todos os direitos reservados</p>
</footer>
</body>
</html>