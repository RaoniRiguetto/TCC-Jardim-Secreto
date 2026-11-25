<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// só libera acesso para cliente logado
if (!isset($_SESSION["id"]) || !isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "usuario") {
    $mensagem = "Sessão expirada, faça login novamente.";
    header("location: login.php?mensagem=" . urlencode($mensagem));
    exit();
}
?>
