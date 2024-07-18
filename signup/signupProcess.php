<?php
    //funzione per il controllo della sicurezza della password; nel caso in cui la password non soddisfi i requisiti 
    //viene mostrato un messaggio di errore
    function passwordStrenght($password){
        $minLength = 8;
        $upperCase = preg_match('/[A-Z]/', $password);
        $lowerCase = preg_match('/[a-z]/', $password);
        $number = preg_match('/[0-9]/', $password);
        $specialChar = preg_match('/[\W]/', $password);

        if(strlen($password) < $minLength){
            header("Location: signup.php?error=Password must be at least 8 characters");
            exit();
        }
        if(!$upperCase){
            header("Location: signup.php?error=Password must contain at least one uppercase letter");
            exit();
        }
        if(!$lowerCase){
            header("Location: signup.php?error=Password must contain at least one lowercase letter");
            exit();
        }
        if(!$number){
            header("Location: signup.php?error=Password must contain at least one number");
            exit();
        }
        if(!$specialChar){
            header("Location: signup.php?error=Password must contain at least one non-alphanumeric character");
            exit();
        }
        return;
    }


    include("../db/databaseConnection.php");
    session_start();
    //salvo i dati inseriti nel form in modo da poter ripopolare il form in caso di errore
    $_SESSION["form_data"] = $_POST;

    //controllo che i campi del form siano tutti riempiti correttamente
    if(!isset($_POST["FirstName"]) || $_POST["FirstName"] == ""){
        header("Location: signup.php?error=First Name is required");
        exit();
    }
    if(!isset($_POST["LastName"]) || $_POST["LastName"] == ""){
        header("Location: signup.php?error=Last Name is required");
        exit();
    }
    if(!isset($_POST["mail"]) || $_POST["mail"] == ""){
        header("Location: signup.php?error=Email address is required");
        exit();
    }
    if(!isset($_POST["uname"]) || $_POST["uname"] == ""){
        header("Location: signup.php?error=Username is required");
        exit();
    }
    if(!isset($_POST["psw"]) || $_POST["psw"] == ""){
        header("Location: signup.php?error=Password is required");
        exit();
    }

    passwordStrenght($_POST["psw"]);

    //controllo che le password inserite siano uguali
    if($_POST["psw"] != $_POST["repeatPsw"]){
        header("Location: signup.php?error=Passwords do not match");
        exit();
    }

    //controllo che il nome utente non sia già associato ad un utente
    $sql = "SELECT * FROM Utente WHERE Username = ?";

    $stmt = $mysqli->stmt_init();
    if(!$mysqli->prepare($sql)){
        die("SQL error: " . $mysqli->error);
    }
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $_POST["uname"]);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($row){
        header("Location: signup.php?error=This username is already associated with an account");
        exit();
    }

    //controllo che la mail non sia già associata ad un utente
    $sql = "SELECT * FROM Utente WHERE Email = ?";

    if(!$mysqli->prepare($sql)){
        die("SQL error: " . $mysqli->error);
    }
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $_POST["mail"]);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($row){
        header("Location: signup.php?error=This email is already associated with an account");
        exit();
    }

    //posso effettuare la crezione di un nuovo account
    $password_hash = password_hash($_POST["psw"], PASSWORD_DEFAULT);
    $sql = "INSERT INTO Utente (Username, Password, Nome, Cognome, Email) Values (?, ?, ?, ?, ?)";

    if(!$mysqli->prepare($sql)){
        die("SQL error: " . $mysqli->error);
    }
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssss", $_POST["uname"], $password_hash, $_POST["FirstName"], $_POST["LastName"], $_POST["mail"]);
    $result = $stmt->execute();

    if($result){
        //creazione account completata con successo
        //posso elimiare i dati del form salvati in $_SESSION
        unset($_SESSION["form_data"]);
        $_SESSION["username"] = $_POST["uname"];
        $_SESSION["nome"] = $_POST["FirstName"];
        $_SESSION["cognome"] = $_POST["LastName"];
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        header("Location: " . $protocol . $_SERVER["HTTP_HOST"] . "/Cosmic-Clash/home/home.php", true);
        exit();
    }
    else{
        //errore nella creazione dell'account
        die("Error creating account. Please try again later.");
    }
?>