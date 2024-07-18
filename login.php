<?php
    include("db/databaseConnection.php");
    session_start();
    $_SESSION["form_data"] = $_POST;

    if(!isset($_POST["username"])){
        header("Location: index.php?error=Username is required");
        exit();
    }
    if(!isset($_POST["password"])){
        header("Location: index.php?error=Password is required");
        exit();
    }

    $uname = $_POST["username"];
    $pass = $_POST["password"];

    $sql = "SELECT * FROM Utente WHERE Username = ?";

    $stmt = $mysqli->stmt_init();
    if(!$mysqli->prepare($sql)){
        die("SQL error: " . $mysqli->error);
    }
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $uname);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($row && password_verify($pass, $row["Password"])){
        unset($_SESSION["form_data"]);
        $_SESSION["username"] = $row["Username"];
        $_SESSION["nome"] = $row["Nome"];
        $_SESSION["cognome"] = $row["Cognome"];
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        header("Location: " . $protocol . $_SERVER["HTTP_HOST"] . "/Cosmic-Clash/home/home.php", true);
        exit();
    }else{
        header("Location: index.php?error=Incorrect username or password");
        exit();
    }
?>