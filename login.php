<?php
session_start();
require "conexao.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST["login"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if ($login === "" || $senha === "") {
        $msg = "Preencha login e senha.";
    } else {

        $sql = $conexao->prepare("
            SELECT id, senha 
            FROM fornecedores 
            WHERE email = ? OR cnpj = ?
        ");
        $sql->bind_param("ss", $login, $login);
        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows === 0) {
            $msg = "Usuário não encontrado.";
        } else {
            $usuario = $result->fetch_assoc();

            if (!password_verify($senha, $usuario["senha"])) {
                $msg = "Senha inválida.";
            } else {
                $_SESSION["usuario_id"] = $usuario["id"];
                header("Location: painel.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Portal MSE - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="header-card">
        ⚡ Portal MSE
    </div>

    <div class="card">

        <?php if ($msg): ?>
            <div style="color:#a8071a; text-align:center; margin-bottom:12px; font-size:14px;">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input
                type="text"
                name="login"
                placeholder="E-mail ou CNPJ de cadastro"
                style="margin-bottom:10px;"
            >

            <div class="password-field">
                <input type="password" name="senha" placeholder="Senha">
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Entrar</button>
            </div>

            <p class="login-text">
                <a href="#">Esqueci minha senha</a>
            </p>

        </form>

    </div>

    <div class="info-card">
        <div class="icon">🏭</div>
        <p class="info-title">Para ser um Fornecedor MSE</p>
        <p class="info-subtitle">Cadastre-se aqui</p>
        <a href="index.php" class="submit-btn">Cadastrar</a>
    </div>

</div>

</body>
</html>
