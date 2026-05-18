<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sobre - RELEIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Caveat:wght@700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="sobre.css" />
</head>
<body>

    <header class="site-header">
        <div class="header-container">
            <div class="logo-area">
                <a href="index.php">
                    <img src="gatinho-releia.png" alt="logo do site" class="logo">
                </a>
                <h1 class="brand">RELEIA</h1>
            </div>
            
            <nav class="nav">
                <a href="index.php">Início</a>
                <a href="sobre.php" class="active">Sobre</a>
                <a href="contato.php">Serviços</a> <a href="contato.php">Contato</a>
            </nav>

            <div class="user-area">
                <span class="user-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </span>
                <button class="btn-client">Olá, Cliente</button>
            </div>
        </div>
    </header>

    <main class="main-content">
        <section class="sobre-section">
            <div class="container">
                <h2>SOBRE O PROJETO</h2>
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
            </div>
        </section>

        <section class="pix-section">
            <div class="container">
                <h2>Ajude nosso projeto</h2>
                <p>Leia o código abaixo com a câmera do celular, ou copie o código e abra o aplicativo do seu banco para fazer uma transferência de qualquer valor que queira.</p>
                
                <div class="qr-container">
                    <img src="qr-code-plus.png" alt="QR Code Pix" class="qr-code">
                </div>

                <button class="btn-copy-pix">COPIAR O CÓDIGO PIX</button>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <p>© RELEIA 2026. Todos os direitos reservados.</p>
    </footer>

</body>
</html>