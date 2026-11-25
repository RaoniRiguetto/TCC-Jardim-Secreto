<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// impede acesso de quem não está logado ou é cliente comum
if (!isset($_SESSION["id"]) || !isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "funcionario") {
    $mensagem = "Sessão expirada, faça login novamente.";
    header("location: login.php?mensagem=" . urlencode($mensagem));
    exit();
}
?>
