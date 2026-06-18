<?php
// perfil-doador.php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Permitir apenas doadores
if ($_SESSION['tipo'] !== 'doador') {
    header("Location: index.php");
    exit();
}

// Importa a conexão PDO
require_once 'conexao.php';

$mensagem = "";
$usuario_id = $_SESSION['usuario_id'];

// Inicializa a variável com valores padrão
$dadosUsuario = [
    'nome' => $_SESSION['usuario_nome'] ?? 'Doador Anônimo',
    'email' => 'email@naoencontrado.com',
    'telefone' => '(00) 00000-0000',
    'cidade' => 'Não informada',
    'estado' => '--'
];

// PROCESSAMENTO DOS FORMULÁRIOS
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // A. Atualizar perfil (Sem JS)
    if (isset($_POST['atualizar_perfil'])) {
        $nome     = $_POST['perfil_nome'] ?? '';
        $telefone = $_POST['perfil_telefone'] ?? '';
        $cidade   = $_POST['perfil_cidade'] ?? '';
        $estado   = $_POST['perfil_estado'] ?? '';

        if (empty($nome) || empty($telefone)) {
            $mensagem = "<p class='error-msg'>Nome e Telefone são obrigatórios para o perfil.</p>";
        } else {
            try {
                $sql_perfil = "UPDATE usuarios SET nome = ?, telefone = ?, cidade = ?, estado = ? WHERE id = ?";
                $stmt_perfil = $pdo->prepare($sql_perfil);
                
                if ($stmt_perfil->execute([$nome, $telefone, $cidade, $estado, $usuario_id])) {
                    $mensagem = "<p class='success-msg'>Seus dados foram atualizados com sucesso!</p>";
                    $_SESSION['usuario_nome'] = $nome;
                } else {
                    $mensagem = "<p class='error-msg'>Erro ao atualizar os dados do perfil.</p>";
                }
            } catch (PDOException $e) {
                $mensagem = "<p class='error-msg'>Erro no banco de dados: " . $e->getMessage() . "</p>";
            }
        }
    }

    // B. Cadastrar Livro
    if (isset($_POST['cadastrar_livro'])) {
        $nome_livro = $_POST['book-name'];
        $autor      = $_POST['author-name'];
        $ano        = $_POST['book-year'];
        $tipo       = $_POST['book-type'];
        $genero     = $_POST['book-genre'];
        $estado_livro = $_POST['book-state'];
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $nome_foto = $_FILES['photo']['name'];
            $caminho_temporario = $_FILES['photo']['tmp_name'];
            
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            $pasta_destino = "uploads/" . time() . "_" . $nome_foto;

            if (move_uploaded_file($caminho_temporario, $pasta_destino)) {
                try {
                    // Adicionado 'Disponível' explicitamente no INSERT
                    $sql = "INSERT INTO livros (nome, autor, ano, tipo, genero, estado, foto, usuario_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Disponível')";
                    $stmt = $pdo->prepare($sql);
                    
                    if ($stmt->execute([$nome_livro, $autor, $ano, $tipo, $genero, $estado_livro, $pasta_destino, $usuario_id])) {
                        $mensagem = "<p class='success-msg'>Livro cadastrado com sucesso!</p>";
                    } else {
                        $mensagem = "<p class='error-msg'>Erro ao cadastrar o livro.</p>";
                    }
                } catch (PDOException $e) {
                    $mensagem = "<p class='error-msg'>Erro no banco de dados: " . $e->getMessage() . "</p>";
                }
            } else {
                $mensagem = "<p class='error-msg'>Erro ao mover a foto para a pasta de destino.</p>";
            }
        } else {
            $mensagem = "<p class='error-msg'>Por favor, selecione uma foto para a capa.</p>";
        }
    }

    // C. NOVA FUNCIONALIDADE: Marcar Livro como Doado
    if (isset($_POST['marcar_doado'])) {
        $id_livro_doado = $_POST['id_livro_doado'];
        
        try {
            // Garante que o doador só altere um livro que realmente pertence a ele
            $sql_doado = "UPDATE livros SET status = 'Doado' WHERE id_livro = ? AND usuario_id = ?";
            $stmt_doado = $pdo->prepare($sql_doado);
            
            if ($stmt_doado->execute([$id_livro_doado, $usuario_id])) {
                $mensagem = "<p class='success-msg'>Parabéns! O status do livro foi alterado para doado.</p>";
            } else {
                $mensagem = "<p class='error-msg'>Erro ao atualizar o status do livro.</p>";
            }
        } catch (PDOException $e) {
            $mensagem = "<p class='error-msg'>Erro no banco de dados: " . $e->getMessage() . "</p>";
        }
    }
}

// BUSCAR DADOS DO BANCO (Perfil do usuário)
try {
    $stmtUser = $pdo->prepare("SELECT nome, email, telefone, cidade, estado FROM usuarios WHERE id = ?");
    $stmtUser->execute([$usuario_id]);
    $resultadoBanco = $stmtUser->fetch(PDO::FETCH_ASSOC);
    
    if ($resultadoBanco) {
        $dadosUsuario = $resultadoBanco;
    }
} catch (PDOException $e) {
    $mensagem = "<p class='error-msg'>Erro ao carregar perfil: " . $e->getMessage() . "</p>";
}

// BUSCAR HISTÓRICO DE LIVROS (Exibe apenas os que ainda estão 'Disponível')
$livrosCadastrados = [];
try {
    $stmtLivros = $pdo->prepare("SELECT id_livro, nome, autor, genero, ano, tipo, estado, foto, status FROM livros WHERE usuario_id = ? AND status = 'Disponível' ORDER BY id_livro DESC");
    $stmtLivros->execute([$usuario_id]);
    $livrosCadastrados = $stmtLivros->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensagem .= "<p class='error-msg'>Erro ao carregar histórico de livros: " . $e->getMessage() . "</p>";
}

$page_title = "Meu Perfil e Cadastro - RELEIA";
require_once 'header-nav.php';
?>

<body class="profile-page">

<link rel="stylesheet" href="perfil-doador.css?v=<?php echo time(); ?>">

<div class="container-perfil">

    <div class="status-container-global">
        <?php echo $mensagem; ?>
    </div>

    <input type="checkbox" id="alternar-edicao-doador" class="checkbox-toggle-perfil">

    <section class="card-perfil">
        <div class="perfil-esquerda">
            <div class="avatar-placeholder"></div>
            <div class="nome-resumido">
                <?php 
                    $partes = explode(" ", $dadosUsuario['nome']);
                    echo htmlspecialchars($partes[0] . (isset($partes[1]) ? " " . substr($partes[1], 0, 1) . "." : ""));
                ?>
            </div>
            <h3 class="profile-role">Doador</h3>
            
            <label for="alternar-edicao-doador" class="btn-editar btn-label-trigger">EDITAR PERFIL</label>
        </div>
        
        <div class="divisor-vertical"></div>
        
        <div class="perfil-direita">
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($dadosUsuario['nome']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($dadosUsuario['email']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($dadosUsuario['telefone'] ?? 'Não informado'); ?></p>
            
            <div class="info-location-group">
                <p><strong>Cidade:</strong> <?php echo htmlspecialchars($dadosUsuario['cidade'] ?? 'Não informada'); ?></p> 
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($dadosUsuario['estado'] ?? '--'); ?></p>
            </div>
        </div>
    </section>

    <section class="card-formulario secao-editar-perfil" id="formulario-editar-perfil">
        <h3 class="titulo-bloco">Meus Dados Pessoais</h3>
        <hr class="divisor-horizontal">

        <form method="POST" action="perfil-doador.php" class="form-grid-perfil">
            <input type="hidden" name="atualizar_perfil" value="1">
            
            <div class="input-container">
                <label>Seu Nome Completo:</label>
                <input type="text" name="perfil_nome" value="<?php echo htmlspecialchars($dadosUsuario['nome']); ?>" required />
            </div>

            <div class="input-container">
                <label>Telefone de Contato:</label>
                <input type="text" name="perfil_telefone" value="<?php echo htmlspecialchars($dadosUsuario['telefone'] ?? ''); ?>" required />
            </div>

            <div class="input-container">
                <label>Cidade:</label>
                <input type="text" name="perfil_cidade" value="<?php echo htmlspecialchars($dadosUsuario['cidade'] ?? ''); ?>" />
            </div>

            <div class="input-container">
                <label>Estado (UF):</label>
                <input type="text" name="perfil_estado" maxlength="2" value="<?php echo htmlspecialchars($dadosUsuario['estado'] ?? ''); ?>" />
            </div>

            <button type="submit" class="btn-submeter">SALVAR MEUS DADOS</button>
        </form>
    </section>

    <section class="card-formulario">
        <h3 class="titulo-bloco">Cadastrar livro</h3>
        <hr class="divisor-horizontal">

        <form method="POST" action="perfil-doador.php" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="cadastrar_livro" value="1">
            
            <input type="text" name="book-name" placeholder="Nome:" required />
            <input type="text" name="author-name" placeholder="Autor:" required />
            <input type="text" name="book-genre" placeholder="Gênero:" required />
            <input type="number" name="book-year" placeholder="Ano de Lançamento:" required />
            
            <select name="book-type" required>
                <option value="Literário">Literário</option>
                <option value="Didático">Didático</option>
            </select>

            <select name="book-state" required>
                <option value="" disabled selected>Estado do livro:</option>
                <option value="Novo">Novo</option>
                <option value="Seminovo">Seminovo</option>
                <option value="Usado">Usado</option>
            </select>

            <div class="upload-container" onclick="document.getElementById('photo').click();">
                <span id="file-label">upload da capa do livro</span>
                <input type="file" name="photo" id="photo" required onchange="document.getElementById('file-label').innerText = this.files[0].name;">
            </div>

            <button type="submit" class="btn-submeter">CADASTRAR LIVRO</button>
        </form>
    </section>

    <section class="card-formulario secao-historico">
        <h3 class="titulo-bloco">Meus Livros Disponíveis</h3>
        <hr class="divisor-horizontal">

        <?php if (empty($livrosCadastrados)): ?>
            <p class="sem-livros">Você não possui livros ativos para doação no momento.</p>
        <?php else: ?>
            <div class="grid-historico-livros">
                <?php foreach ($livrosCadastrados as $livro): ?>
                    <div class="card-livro-historico">
                        <div class="capa-livro-historico">
                            <img src="<?php echo htmlspecialchars($livro['foto']); ?>" alt="Capa de <?php echo htmlspecialchars($livro['nome']); ?>">
                        </div>
                        <div class="info-livro-historico">
                            <h4><?php echo htmlspecialchars($livro['nome']); ?></h4>
                            <p><strong>Autor:</strong> <?php echo htmlspecialchars($livro['autor']); ?></p>
                            <p><strong>Gênero:</strong> <?php echo htmlspecialchars($livro['genero']); ?> (<?php echo htmlspecialchars($livro['ano']); ?>)</p>
                            
                            <div class="tags-livro">
                                <span class="tag-tipo"><?php echo htmlspecialchars($livro['tipo']); ?></span>
                                <span class="tag-estado <?php echo strtolower($livro['estado']); ?>"><?php echo htmlspecialchars($livro['estado']); ?></span>
                            </div>

                            <form method="POST" action="perfil-doador.php" style="margin-top: 12px;">
                                <input type="hidden" name="marcar_doado" value="1">
                                <input type="hidden" name="id_livro_doado" value="<?php echo $livro['id_livro']; ?>">
                                <button type="submit" class="btn-marcar-doado" onclick="return confirm('Confirmar que este livro já foi doado? Ele sairá da listagem pública.');">Já doei</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</div>

<footer class="footer-copy">
    © 2026 RELEIA. Todos os direitos reservados.
</footer>
</body>
</html>