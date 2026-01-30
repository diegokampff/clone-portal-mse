<?php

$host = getenv("DB_HOST") ?: "localhost";
$banco = getenv("DB_NAME") ?: "portal_mse";
$usuario = getenv("DB_USER") ?: "root";
$senha = getenv("DB_PASS") ?: "";

$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");
