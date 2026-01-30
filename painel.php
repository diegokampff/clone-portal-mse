<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
?>
<h2>Você está logado</h2>
<a href="logout.php">Sair</a>
