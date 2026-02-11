<?php
header("Content-Type: application/json; charset=utf-8");

$cep = $_GET["cep"] ?? "";
$cep = preg_replace('/\D+/', '', $cep);

if (strlen($cep) !== 8) {
  echo json_encode(["ok" => false, "msg" => "CEP inválido"]);
  exit;
}

$url = "https://viacep.com.br/ws/{$cep}/json/";
$resp = @file_get_contents($url);

if (!$resp) {
  echo json_encode(["ok" => false, "msg" => "Falha ao consultar ViaCEP"]);
  exit;
}

$data = json_decode($resp, true);

if (!$data || isset($data["erro"])) {
  echo json_encode(["ok" => false, "msg" => "CEP não encontrado"]);
  exit;
}

$data["ok"] = true;
echo json_encode($data);
