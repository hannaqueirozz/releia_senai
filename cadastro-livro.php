<?php
// Verificar se o usuário está logado
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Permitir apenas doadores (não organizações)
if ($_SESSION['tipo'] !== 'doador') {
    header("Location: index.php");
    exit();
}

// 1. Importar a conexão PADRÃO com o banco de dados (PDO)
require_once 'conexao.php';

$mensagem = "";

// PROCESSAMENTO DO FORMULÁRIO
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome_livro = $_POST['book-name'];
    $autor      = $_POST['author-name'];
    $ano        = $_POST['book-year'];
    $tipo       = $_POST['book-type'];
    $genero     = $_POST['book-genre'];
    $estado     = $_POST['book-state'];
    
    // Captura o ID do doador que está logado na sessão atual
    $id_doador  = $_SESSION['usuario_id']; 
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $nome_foto = $_FILES['photo']['name'];
        $caminho_temporario = $_FILES['photo']['tmp_name'];
        
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        $pasta_destino = "uploads/" . time() . "_" . $nome_foto;

        if (move_uploaded_file($caminho_temporario, $pasta_destino)) {
            try {
                // CORREÇÃO: Adicionada a coluna 'usuarios_id' no INSERT para amarrar o livro ao doador
                $sql = "INSERT INTO livros (nome, autor, ano, tipo, genero, estado, foto, usuarios_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                
                // Executa passando todos os parâmetros, incluindo o ID do usuário logado
                if ($stmt->execute([$nome_livro, $autor, $ano, $tipo, $genero, $estado, $pasta_destino, $id_doador])) {
                    $mensagem = "<p style='color: green; font-weight: bold;'>Livro cadastrado com sucesso!</p>";
                } else {
                    $mensagem = "<p style='color: red;'>Erro ao cadastrar no banco de dados.</p>";
                }
            } catch (PDOException $e) {
                $mensagem = "<p style='color: red;'>Erro no banco de dados: " . $e->getMessage() . "</p>";
            }
        } else {
            $mensagem = "<p style='color: red;'>Erro ao mover a foto para a pasta de destino.</p>";
        }
    } else {
        $mensagem = "<p style='color: red;'>Por favor, selecione uma foto.</p>";
    }
}

$page_title = "Cadastro de Livro - RELEIA";
require_once 'header-nav.php';
?>
<section class="book-register-section">
    <div class="form-container">
        <div class="book-wrapper">
            <div class="left-panel">
                <div class="logo">RELEIA</div>
                <h2 class="welcome">OLÁ,<br />DOADOR!</h2>
                <p class="description">"Ainda acabo fazendo livros onde as nossas crianças possam morar." - Monteiro Lobato.</p>
            </div>
            
            <div class="right-panel">
                <h2>Cadastrar Livro</h2>
                
                <div class="status-msg">
                    <?php echo $mensagem; ?>
                </div>

                <form class="book-form" method="POST" action="" enctype="multipart/form-data">
                    <div class="col">
                        <label for="book-name">Nome do livro</label>
                        <input type="text" id="book-name" name="book-name" required />

                        <label for="author-name">Nome do autor</label>
                        <input type="text" id="author-name" name="author-name" required />

                        <label for="book-year">Ano de lançamento</label>
                        <input type="number" id="book-year" name="book-year" required />

                        <label>Tipo do livro</label>
                        <fieldset class="book-type-container">
                            <input type="radio" name="book-type" value="Didático" checked> Didático
                            <input type="radio" name="book-type" value="Literário"> Literário
                        </fieldset>
                    </div>

                    <div class="col">
                        <label for="book-genre">Gênero Literário</label>
                        <input type="text" id="book-genre" name="book-genre" required />

                        <label>Estado do livro</label>
                        <fieldset class="book-state-container">
                            <input type="radio" name="book-state" value="Novo" checked> Novo
                            <input type="radio" name="book-state" value="Seminovo"> Seminovo
                            <input type="radio" name="book-state" value="Usado"> Usado
                        </fieldset>

                        <label for="photo">Foto do livro</label>
                        <input type="file" name="photo" id="photo" required>

                        <div class="buttons">
                            <button type="submit" class="btn book-register">Finalizar Cadastro</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
</main>

<footer class="site-footer">
    <p>© RELEIA | todos os direitos reservados</p>
</footer>
</body>
</html>