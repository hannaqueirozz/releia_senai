# RELEIA - Plataforma de Doação de Livros

> O **RELEIA** é uma plataforma web desenvolvida para conectar pessoas que possuem livros disponíveis para doação a Organizações da Sociedade Civil (OSCs)...

<img src="logo.png" alt="Logo do Sistema RELEIA" width="100" align="left"> ![Status do Projeto](https://img.shields.io/badge/Status-Em%20Desenvolvimento-yellow) ![PHP](https://img.shields.io/badge/PHP-7.4%20%7C%208.x-777BB4?logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)

<br clear="left">

---

## Funcionalidades Principais (Features)

* **Perfil do Doador:** Permite o cadastro e login de usuários físicos, visualização de suas informações cadastrais e a opção de cadastrar e gerenciar livros que não utilizam mais.
* **Perfil da Organização (OSC):** Permite o cadastro e login de instituições sociais, visualização de informações sobre a organização e a opção de adicionar uma descrição detalhada mostrando o projeto social que realizam.
* **Mural de Anúncios:** Espaço onde todos os livros cadastrados pelos doadores ficam visíveis para a plataforma.
* **Manifestação de Interesse:** Sistema onde as organizações sociais cadastradas navegam pelos anúncios e podem clicar em um livro de interesse para sinalizar que desejam recebê-lo.
* **Integração com API ISBN (Em Planejamento):** Sistema de busca para preenchimento automático das informações do livro (título, autor, capa) a partir do código do livro (ISBN).

---

## Tecnologias, Linguagens e Ferramentas Utilizadas

O ecossistema do projeto **RELEIA** foi planejado para ser leve, funcional e de fácil hospedagem, utilizando as seguintes tecnologias:

### Back-end & Banco de Dados
* **PHP (Linguagem Principal):** Responsável por toda a lógica de negócios, controle de sessões de usuários (doadores e OSCs), processamento de formulários e comunicação com o banco de dados.
* **MySQL (Banco de Dados):** Utilizado para o armazenamento seguro e relacional dos dados do sistema através da base `sua_base`, estruturada nas tabelas `usuarios`, `oscs` e `livros`.

### Front-end (Interface do Usuário)
* **HTML5:** Estruturação semântica de todas as páginas da plataforma (telas de cadastro, mural de anúncios e perfis).
* **CSS3:** Estilização customizada, garantindo uma identidade visual agradável, limpa e uma interface responsiva para doadores e instituições.
* **JavaScript (Vanilla):** Utilizado para validações de formulários no lado do cliente e interações dinâmicas na interface.

### Integrações & Bibliotecas Chave
* **API Pública de Consulta ISBN (Em Planejamento):** Biblioteca/Componente para consumo de API externa, permitindo a busca automatizada de metadados dos livros (título, autor e capa) a fim de otimizar o cadastro por parte do doador.
* **PDO (PHP Data Objects):** Driver do PHP utilizado para garantir uma conexão segura e protegida contra ataques de *SQL Injection* no banco de dados.

---

## Pré-requisitos e Instalação

Antes de começar, você vai precisar ter instalado em sua máquina as seguintes ferramentas:
* Um servidor local Apache com suporte a PHP e MySQL (Recomendamos o **XAMPP**).

### Passo a Passo

1. Baixe o código do projeto em formato `.zip` no GitHub e extraia o conteúdo diretamente no diretório de arquivos do seu servidor local (no Windows com XAMPP, o caminho padrão é `C:\xampp\htdocs\RELEIA`).

### Configuração do Banco de Dados (MySQL)

1. Abra o painel de controle do XAMPP e inicialize os módulos **Apache** e **MySQL**.
2. Acesse o painel do banco de dados no seu navegador através do endereço: `http://localhost/phpmyadmin`.
3. Crie um novo banco de dados com o nome exato de **`sua_base`**.
4. Importe o arquivo de script SQL do projeto (localizado na raiz da pasta do projeto) para criar automaticamente as 3 tabelas necessárias:
   * **`usuarios`**: Para o cadastro e login dos doadores.
   * **`oscs`**: Para as informações e descrições dos projetos das organizações sociais.
   * **`livros`**: Para o armazenamento dos livros cadastrados e disponíveis para doação.

---

---

## Como Executar a Aplicação

Certifique-se de que a pasta do projeto esteja no diretório do servidor local (`htdocs`) e que o Apache e MySQL estejam rodando no XAMPP.

Abra o seu navegador e acesse a seguinte URL:

http://localhost/RELEIA

A partir desse endereço, o sistema estará pronto para que avaliadores e desenvolvedores possam testar o fluxo de cadastro, inserção de livros e manifestação de interesse por parte das OSCs.

---

## Autores e Contribuidores

Abaixo estão os membros da equipe que desenvolveram o projeto RELEIA:

* **Hanna Queiroz** - [GitHub](https://github.com/hannaqueirozz) | [LinkedIn](https://www.linkedin.com/in/hanna-queiroz-7bb9802b2/)
