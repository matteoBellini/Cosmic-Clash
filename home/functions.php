<?php
    session_start();

    //effettua il logout
    function logout(){
        session_unset();
        session_destroy();
        exit();
    }

    //cambia l'immagine di profilo con quella scelta dall'utente così al prossimo caricamento della pagina verrà visualizzata quella corretta
    function changeProfileImage($imageId){
        include("../db/databaseConnection.php");
        $sql = "UPDATE Utente SET ImmagineProfilo = ? WHERE Username = ?";
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($sql);
        $stmt->bind_param("ss", $imageId, $_SESSION["username"]);
        if(!$stmt->execute()){
            die("Error updating profile image");
        }
        $stmt->close();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST)){
            switch($_POST["fname"]){
                case "logout":
                    logout();
                    break;
                case "changeProfileImage":
                    if(isset($_POST["imageId"])){
                        changeProfileImage($_POST["imageId"]);
                    }
                    break;
                default:
                    die("Function name is not valid");
            }
        }
    }
?>