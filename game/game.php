<?php
    session_start();
    //se l'utente non ha effettuato il login viene reindirizzato alla pagina di login
    if(!isset($_SESSION["username"])){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        header("Location: " . $protocol . $_SERVER["HTTP_HOST"] . "Cosmic-Clash/index.php", true);
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Play Cosmic Clash</title>
    <link rel="stylesheet" href="stylesheet.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
</head>
<body>
    <div id="splash" onclick="start()">
        <p id="play">Click to Play!</p>
        <p id="controls">Controls: A (move Left), D (move Right), Space (Shoot)</p>
        <a href="../guida/guida.html">Regole</a>
    </div>
    <canvas id="canvas1"></canvas>
    <div class="hidden">
        <img src="assets/enemies1.png" class="enemies">
        <img src="assets/enemies2.png" class="enemies">
        <img src="assets/spaceship.png" id="player">
        <img src="assets/laser-2.png" id="laser">
        <img src="assets/explosions.png" id="explosion">
    </div>

    <audio  id="bgm" src="assets/2021-08-30_-_Boss_Time_-_www.FesliyanStudios.com.mp3" loop hidden></audio>

    <script src="script.js"></script>
</body>
</html>