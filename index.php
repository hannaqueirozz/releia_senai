<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>RELEIA | Início - usuário (sem login)</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
	<!-- Reuse base styles from recebedor so layout is consistent across pages -->
	<link rel="stylesheet" href="index.css" />
</head>

<body>
	<!-- Cabeçalho com navegação (usuário - sem login) -->
	<header class="site-header">
		<div class="header-content">
			<a href="index.php">
				<img src="gatinho-releia.png" alt="logo do site"
					class="logo">
			</a>
			<h1 class="brand">R E L E I A</h1>
			<nav class="nav">
				<a href="index.php">Início</a>
				<a href="sobre.php">Sobre</a>
				<a href="contato.php">Contato</a>
				<!-- trocamos 'meu perfil' por 'login' e 'cadastrar' -->
				<a href="cadastro.php" class="signup-link">Cadastre-se</a>
				<a href="login.php" class="login-link">Login</a>
			</nav>
		</div>
	</header>

		<!-- Doador content: convite para doar e instruções -->
		<section class="usuario-content">
			<h2>DOE LIVROS,TRANSFORME VIDAS!</h2>
			<p>Sua doação pode abrir novas portas e inspirar uma paixão pelo aprendizado,
				permitindo que pessoas com menos recursos tenham acesso a materiais educativos e culturais.</p>
			<a href="login.php"><button>Doe aqui!</button></a>
		</section>
		<section class="usuario-content2">
			<h2>A IMPORTANCIA DA DOAÇÃO DE LIVROS PARA CRIANÇAS E ADOLESCENTES</h2>
			<p>Nem todas as pessoas, especialmente em áreas carentes, têm acesso a diversas formas de leitura.
				Por isso, ao doar livros, você está dando a alguém a oportunidade de aprender, crescer e adquirir
				conhecimento.
				A doação de livros para crianças e adolescentes é um ato fundamental de cidadania que democratiza o
				acesso à cultura,
				educa para o consumo consciente e combate a desigualdade no acesso à informação.
				Esse gesto transforma vidas ao estimular a criatividade, ampliar o vocabulário,
				promover o senso crítico e fomentar o desenvolvimento cognitivo e emocional.
				Doar livros é uma forma de garantir que o conhecimento circule e que novos leitores sejam formados,
				garantindo oportunidades de desenvolvimento para todos.</p>
		</section>
		<section class="funcionamento">
    <h2>COMO FUNCIONA O PROJETO?</h2>
    
    <div class="cards-container">
        <div class="card-item">
            <h3>Como doar?</h3>
            <p class="p1">O doador e a organização devem se cadastrar para se encontrarem e se conectarem. </p>
        </div>

        <div class="card-item">
            <h3>Para onde vão os livros?</h3>
            <p class="p2">Os livros serão recebidos por Organizações da Sociedade Civil (ONGs, associações, fundações privadas e organizações religiosas.)</p>
        </div>

        <div class="card-item">
            <h3>Que tipo de livro recebemos?</h3>
            <p class="p3">Todos os gêneros, incluindo didáticos, gibis, mangás.</p>
        </div>
    </div>
</section>
		<section class="Perguntas">
			<h2>PERGUNTAS FREQUENTES</h2>
			<details>
				<summary>Como cadastro a minha instituição?</summary>
				<p>Basta clicar na opção de cadastro no topo da página, e escolher
					"Sou Organização" e digitar suas informações.</p>
			</details>
			<details>
				<summary>Como arrumar os livros para o envio?</summary>
				<p>O livro deve estar em um bom estado e, caso vá fazer o envio via
					correios/transportadora, deve-se embalar muito bem com plástico para
					evitar avarias durante o envio.</p>
			</details>
			<details>
				<summary>Como funciona o recebimento?</summary>
				<p>Você pode escolher entre enviar via Correios/Transportadoras, ou fazer a entrega
					pessoalmente em um local público ou na própria organização.</p>
			</details>
		</section>

		<section class="contato">
			<h2>Entre em contato conosco</h2>

			<form action="#" method="POST" class="form-contato">
				<!-- Campo Nome -->
				<input type="text" name="nome" placeholder="Nome" required>

				<!-- Campo Email -->
				<input type="email" name="email" placeholder="Email" required>

				<!-- Campo Mensagem -->
				<textarea name="mensagem" placeholder="Mensagem:" rows="6" required></textarea>

				<!-- Botão de Enviar -->
				<button type="submit">ENVIAR MENSAGEM</button>
			</form>
		</section>


		<section class="pix">
			<h2>AJUDE NOSSO PROJETO</h2>
			<P>Leia o código abaixo com a câmera do seu celular, ou cópie o código e abra o aplicativo
				do seu banco para fazer uma transferência de qualquer valor que queira.Essa doação irá exclusivamente
                para que o RELEIA continue se mantendo.
			</P>
			<img src="qr-code-plus.png" alt="pix do projeto">

		</section>
	</main>


	<!-- Rodapé -->
	<footer class="site-footer">
		<p>© RELEIA | todos os direitos reservados</p>
	</footer>

	<!-- Dropdown JS for categories (mobile/touch) - copied from recebedor/doador pages -->
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const toggles = document.querySelectorAll('.dropdown-toggle');
			toggles.forEach(toggle => {
				toggle.addEventListener('click', function (e) {
					e.preventDefault();
					const parent = this.closest('.has-dropdown');
					const isOpen = parent.classList.toggle('open');
					this.setAttribute('aria-expanded', isOpen);
				});
			});

			// Close menu when clicking outside
			document.addEventListener('click', function (e) {
				document.querySelectorAll('.has-dropdown.open').forEach(el => {
					if (!el.contains(e.target)) {
						el.classList.remove('open');
						const toggle = el.querySelector('.dropdown-toggle');
						if (toggle) toggle.setAttribute('aria-expanded', 'false');
					}
				});
			});

			// Close with ESC
			document.addEventListener('keydown', function (e) {
				if (e.key === 'Escape' || e.key === 'Esc') {
					document.querySelectorAll('.has-dropdown.open').forEach(el => {
						el.classList.remove('open');
						const toggle = el.querySelector('.dropdown-toggle');
						if (toggle) toggle.setAttribute('aria-expanded', 'false');
					});
				}
			});
		});
	</script>
</body>

</html>