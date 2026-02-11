<?php
header("Content-Type: application/json; charset=utf-8");

$cnpj = $_GET["cnpj"] ?? "";
$cnpj = preg_replace('/\D+/', '', $cnpj);

if (strlen($cnpj) !== 14) {
  echo json_encode(["ok" => false, "msg" => "CNPJ inválido"]);
  exit;
}

$url = "https://brasilapi.com.br/api/cnpj/v1/{$cnpj}";
$resp = @file_get_contents($url);

if (!$resp) {
  echo json_encode(["ok" => false, "msg" => "Falha ao consultar BrasilAPI"]);
  exit;
}

$data = json_decode($resp, true);

if (!$data || isset($data["errors"]) || isset($data["message"])) {
  echo json_encode(["ok" => false, "msg" => "CNPJ não encontrado"]);
  exit;
}

echo json_encode([
  "ok" => true,
  "razao_social" => $data["razao_social"] ?? "",
  "nome_fantasia" => $data["nome_fantasia"] ?? "",
  "descricao_situacao_cadastral" => $data["descricao_situacao_cadastral"] ?? "",

  "cep" => $data["cep"] ?? "",
  "logradouro" => $data["logradouro"] ?? "",
  "numero" => $data["numero"] ?? "",
  "bairro" => $data["bairro"] ?? "",
  "complemento" => $data["complemento"] ?? "",
  "municipio" => $data["municipio"] ?? "",
  "uf" => $data["uf"] ?? ""
]);
