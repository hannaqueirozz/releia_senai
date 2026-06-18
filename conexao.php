<?php
// C:\xampp\htdocs\releia-def\conexao.php

// 1. DEFINIÇÃO DAS VARIÁVEIS (Devem vir primeiro!)
$db_host = 'localhost';
$db_user = 'root'; // Usuário padrão do XAMPP
$db_pass = '';     // Senha padrão do XAMPP (vazia, sem espaços)
$db_name = 'sua_base'; // SE SEU BANCO TIVER OUTRO NOME, MUDE AQUI!

try {
    // 2. CONEXÃO (Usando as variáveis configuradas acima)
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    
    // Configura o PDO para alertar sobre erros de SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Se algo der errado, para o código e mostra o erro amigável
    die("Erro crucial: Não foi possível conectar ao banco de dados. Motivo: " . $e->getMessage());
}
?>