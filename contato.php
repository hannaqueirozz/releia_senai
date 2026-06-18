<?php
session_start();
$page_title = "Contato - RELEIA";
require_once 'header-nav.php';
?>

<link rel="stylesheet" href="contato.css?v=<?php echo time(); ?>">

<main class="contato-container">

    <section class="contato">
        <h2>Entre em contato conosco</h2>

        <form action="#" method="POST" class="form-contato">
            <input type="text" name="nome" placeholder="Nome" required>

            <input type="email" name="email" placeholder="Email" required>

            <textarea name="mensagem" placeholder="Mensagem:" rows="6" required></textarea>

            <button type="submit">ENVIAR MENSAGEM</button>
        </form>
    </section>

    <section class="Perguntas">
        <h2>PERGUNTAS FREQUENTES</h2>
        
        <div class="faq-wrapper">
            <details>
                <summary>Como cadastro a minha instituição?</summary>
                <p>Basta clicar na opção de cadastro no topo da página, e escolher "Sou Organização" e digitar suas informações.</p>
            </details>
            
            <details>
                <summary>Como arrumar os livros para o envio?</summary>
                <p>O livro deve estar em um bom estado e, caso vá fazer o envio via correios/transportadora, deve-se embalar muito bem com plástico para evitar avarias durante o envio.</p>
            </details>
            
            <details>
                <summary>Como funciona o recebimento?</summary>
                <p>Você pode escolher entre enviar via Correios/Transportadoras, ou fazer a entrega pessoalmente em um local público ou na própria organização.</p>
            </details>
        </div>
    </section>

</main>

<footer class="site-footer">
    <p>© RELEIA | todos os direitos reservados</p>
</footer>

</body>
</html>