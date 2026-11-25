<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION["id"])) {
    $mensagem = "Você já está logado.";
    header("location: index.php?mensagem=" . urlencode($mensagem));
    exit();
}
?>
