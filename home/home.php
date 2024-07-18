<?php
    session_start();
    include("../db/databaseConnection.php");

    //nel caso in cui non è stato effettuato il login l'utente viene reindirizzato alla pagina di login
    if (!isset($_SESSION["username"])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        header("Location: " . $protocol . $_SERVER["HTTP_HOST"] . "/Cosmic-Clash/index.php", replace: true);
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Home Cosmic Clash</title>
        <link rel="stylesheet" href="stylesheet.css">
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
        <script>
            //lista dei nomi delle immagini profilo disponibili
            var files = <?php
                $out = array();
                foreach(glob("assets/profileImages/*.png") as $filename){
                    $p = pathinfo($filename);
                    $out[] = $p["filename"];
                }
                echo json_encode($out);
            ?>

            //nascondi il contenitore delle immagini profilo quando l'utente effettua un click al di fuori di questo
            window.addEventListener("click", event => {
                if(event.target.id != "imagesContainer" && event.target.tagName != "IMG"){
                    document.getElementById("imagesContainer").style.display = "none";
                }
            });

            //mostra contenitore per le immagini profilo (inizialmente nascosto)
            function changeProfileImage(){
                const container = document.getElementById("imagesContainer");
                if(container.style.display === "none"){
                    container.style.display = "grid";
                }else{
                    container.style.display = "none";
                }
            }

            //porta alla pagina di gioco
            function playGame(){
                window.location.replace("/Cosmic-Clash/game/game.php");
            }

            //effettua il logout
            function logout(){
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200)
                        window.location.replace("/Cosmic-Clash/index.php");
                }
                xhttp.open("POST", "functions.php", true);
                xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhttp.send("fname=logout");
            }

            //popola il contenitore delle immagini profilo
            function initialize(){
                var container = document.getElementById("imagesContainer");
                for(let i = 0; i < files.length; i++){
                    var elem = document.createElement("img");
                    elem.id = files[i];
                    elem.classList.add("grid-item");
                    elem.src = "assets/profileImages/" + files[i] + ".png";
                    elem.setAttribute('onclick', "changeTo(" + elem.id + ")");
                    container.appendChild(elem);
                }
            }

            //effettua il cambio di immagine profilo con quella selezionata
            function changeTo(id){
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var profileImage = document.getElementById("profileImage");
                        profileImage.src = "assets/profileImages/" + id + ".png";
                    }
                }

                xhttp.open("POST", "functions.php", true);
                xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhttp.send("fname=changeProfileImage&imageId=" + id);
            }
        </script>
    </head>
    <body onload="initialize()">
        <div id="imagesContainer">
        </div>
        <header>
            <div id="header_element_left">
                <abbr title="Click to change profile image"><img id="profileImage" alt="Profile image" onclick="changeProfileImage()" src="assets/profileImages/<?php 
                    //inserimento dell'immagine profilo scelta dall'utente
                    $sql = "SELECT ImmagineProfilo FROM Utente WHERE Username = ?;";
                    $stmt = $mysqli->stmt_init();
                    $stmt->prepare($sql);
                    $stmt->bind_param("s", $_SESSION["username"]);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $row = $result->fetch_assoc();
                    echo htmlspecialchars($row["ImmagineProfilo"]);
                ?>.png"></abbr>
            </div>
            <div id="header_element_middle">
                <button id="play" onclick="playGame()">Play</button>
            </div>
            <div id="header_element_right">
                <abbr title="Logout"><button id="logout" onclick="logout()"></button></abbr>
            </div>
        </header>
        <div id="classificaContainer">
            <p>Top 10 Cosmic Clash players</p>
            <table id="classifica">
                <tr>
                    <th>Position</th>
                    <th>Username</th>
                    <th>Points</th>
                    <th>Date</th>
                </tr>
                <?php
                    //popolamento della classifica
                    $sql = "SELECT * FROM Classifica ORDER BY Punteggio DESC, DataRecord ASC LIMIT 10;";
                    $stmt = $mysqli->stmt_init();
                    $stmt = $mysqli->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $i = 1;

                    while($row = $result->fetch_assoc()){
                        echo "<tr>";
                        echo "<td>" . $i . "</td>";
                        echo "<td>" . htmlspecialchars($row["Username"]) . "</td>";
                        echo "<td>" . $row["Punteggio"] . "</td>";
                        echo "<td>" . $row["DataRecord"] . "</td>";
                        echo "</tr>";
                        $i++;
                    }

                    $stmt->close();
                ?>
            </table>
        </div>
        <div id="userStats">
            <p id="stats">
                <?php
                    //posizione in classifica dell'utente e relativo punteggio
                    //se non ha ancora giocato una partita viene mostrato un messaggio che lo comunica
                    $sql = "SELECT Punteggio, DataRecord FROM Classifica WHERE Username = ?;";
                    $stmt = $mysqli->stmt_init();
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("s", $_SESSION["username"]);
                    $stmt->execute();
                    $result1 = $stmt->get_result();

                    if($row1 = $result1->fetch_assoc()){
                        $sql = "SELECT COUNT(*) AS pos FROM Classifica WHERE Punteggio > ? OR (Punteggio = ? AND DataRecord <= ?) ORDER BY Punteggio DESC;";
                        $stmt = $mysqli->stmt_init();
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param("iis", $row1["Punteggio"], $row1["Punteggio"], $row1["DataRecord"]);
                        $stmt->execute();
                        $result2 = $stmt->get_result();

                        $row2 = $result2->fetch_assoc();
                        echo "Your position is: " . $row2["pos"] . " with a score of " . $row1["Punteggio"] . ".";
                    }else{
                        echo "Play at least one match to get into the rankings.";
                    }
                    $stmt->close();
                ?>
            </p>
        </div>
    </body>
</html>