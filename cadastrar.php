<?php
require "conexao.php";
header("Content-Type: application/json; charset=utf-8");

function only_digits($v) {
  return preg_replace('/\D+/', '', (string)$v);
}

function validar_cnpj($cnpj) {
  $cnpj = only_digits($cnpj);
  if (strlen($cnpj) !== 14) return false;
  if (preg_match('/^(\d)\1+$/', $cnpj)) return false;

  $calc = function($base) {
    $tamanho = strlen($base);
    $pos = $tamanho - 7;
    $soma = 0;
    for ($i = $tamanho; $i >= 1; $i--) {
      $soma += intval($base[$tamanho - $i]) * $pos--;
      if ($pos < 2) $pos = 9;
    }
    $resto = $soma % 11;
    return ($resto < 2) ? 0 : (11 - $resto);
  };

  $base = substr($cnpj, 0, 12);
  $d1 = $calc($base);
  $d2 = $calc($base . $d1);

  return $cnpj[12] == (string)$d1 && $cnpj[13] == (string)$d2;
}

$cnpj               = only_digits($_POST["cnpj"] ?? "");
$razao_social        = trim($_POST["razao_social"] ?? "");
$nome_fantasia       = trim($_POST["nome_fantasia"] ?? "");
$inscricao_estadual  = trim($_POST["inscricao_estadual"] ?? "");

$regime_icms         = trim($_POST["regime_icms"] ?? "");
$situacao            = trim($_POST["situacao"] ?? "");
$telefone            = trim($_POST["telefone"] ?? "");
$email               = trim($_POST["email"] ?? "");

$rua                 = trim($_POST["rua"] ?? "");
$numero              = trim($_POST["numero"] ?? "");
$bairro              = trim($_POST["bairro"] ?? "");
$complemento         = trim($_POST["complemento"] ?? "");
$cep                 = only_digits($_POST["cep"] ?? "");
$pais                = trim($_POST["pais"] ?? "");
$estado              = trim($_POST["estado"] ?? "");
$municipio           = trim($_POST["municipio"] ?? "");

$fornecedor_de_arr   = $_POST["fornecedor_de"] ?? [];
$fornecedor_de       = is_array($fornecedor_de_arr) ? implode(" | ", $fornecedor_de_arr) : "";
$ramos               = trim($_POST["ramos"] ?? "");

$cnpj_responsavel    = only_digits($_POST["cnpj_responsavel"] ?? "");
$nome_responsavel    = trim($_POST["nome_responsavel"] ?? "");

$senha               = (string)($_POST["senha"] ?? "");
$senha2              = (string)($_POST["senha_confirmacao"] ?? "");

if (!$cnpj || !$razao_social || !$nome_fantasia || !$inscricao_estadual || !$regime_icms || !$situacao || !$telefone || !$email || !$cnpj_responsavel || !$nome_responsavel || !$senha) {
  echo json_encode(["ok" => false, "msg" => "Preencha todos os campos obrigatórios."]);
  exit;
}

if (!validar_cnpj($cnpj)) {
  echo json_encode(["ok" => false, "msg" => "CNPJ inválido."]);
  exit;
}

if ($senha !== $senha2) {
  echo json_encode(["ok" => false, "msg" => "As senhas não conferem."]);
  exit;
}

$check = $conexao->prepare("SELECT id FROM fornecedores WHERE cnpj = ? OR email = ? LIMIT 1");
$check->bind_param("ss", $cnpj, $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  echo json_encode(["ok" => false, "msg" => "CNPJ ou E-mail já cadastrado."]);
  exit;
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

$ins = $conexao->prepare(
  "INSERT INTO fornecedores (
    cnpj, razao_social, nome_fantasia, inscricao_estadual,
    regime_icms, situacao, telefone, email,
    rua, numero, bairro, complemento, cep, pais, estado, municipio,
    fornecedor_de, ramos,
    cnpj_responsavel, nome_responsavel,
    senha_hash
  ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
);

$ins->bind_param(
  "sssssssssssssssssssss",
  $cnpj, $razao_social, $nome_fantasia, $inscricao_estadual,
  $regime_icms, $situacao, $telefone, $email,
  $rua, $numero, $bairro, $complemento, $cep, $pais, $estado, $municipio,
  $fornecedor_de, $ramos,
  $cnpj_responsavel, $nome_responsavel,
  $hash
);

if (!$ins->execute()) {
  echo json_encode(["ok" => false, "msg" => "Erro ao salvar no banco de dados."]);
  exit;
}

echo json_encode(["ok" => true, "msg" => "Cadastro realizado com sucesso."]);
