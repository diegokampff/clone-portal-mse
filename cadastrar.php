<?php
require "conexao.php";
header("Content-Type: application/json");

$cnpj         = $_POST["cnpj"] ?? "";
$razao        = $_POST["razao_social"] ?? "";
$email        = $_POST["email"] ?? "";
$senha        = $_POST["senha"] ?? "";
$senha_conf   = $_POST["senha_confirmacao"] ?? "";

if (!$cnpj || !$razao || !$email || !$senha) {
    echo json_encode(["ok" => false, "msg" => "Preencha todos os campos obrigatórios."]);
    exit;
}

if ($senha !== $senha_conf) {
    echo json_encode(["ok" => false, "msg" => "As senhas não conferem."]);
    exit;
}

$check = $conexao->prepare("SELECT id FROM fornecedores WHERE cnpj = ?");
$check->bind_param("s", $cnpj);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["ok" => false, "msg" => "CNPJ já cadastrado."]);
    exit;
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

$ins = $conexao->prepare(
    "INSERT INTO fornecedores (cnpj, razao_social, email, senha)
     VALUES (?, ?, ?, ?)"
);

$ins->bind_param("ssss", $cnpj, $razao, $email, $hash);
$ins->execute();

echo json_encode(["ok" => true, "msg" => "Cadastro realizado com sucesso."]);
exit;
