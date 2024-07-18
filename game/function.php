<?php
    session_start();

    //registra il punteggio raggiunto nella partita appena conclusa;
    //il punteggio viene registrato solo se migliore del punteggio in classifica dell'utente
    function registraPunteggio($punteggio){
        include("../db/databaseConnection.php");
        $sql = "CALL inserimentoClassifica(?, ?)";
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($sql);
        $stmt->bind_param("si", $_SESSION["username"], $punteggio);
        if(!$stmt->execute()){
            die("Error saving score");
        }
        $stmt->close();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["fname"])){
            switch($_POST["fname"]){
                case "registraPunteggio":
                    if(isset($_POST["punt"])){
                        registraPunteggio((int)$_POST["punt"]);
                    }
                    break;
                default:
                    die("Function name is not valid");
            }
        }
    }
?>