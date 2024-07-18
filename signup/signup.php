<?php
    session_start();
    //se l'utente ha già effettuato il login viene reindirizzato alla home
    if(isset($_SESSION["username"])){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        header("Location: " . $protocol . $_SERVER["HTTP_HOST"] . "/Cosmic-Clash/home/home.php", replace: true);
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Sign Up to Cosmic Clash</title>
        <link rel="stylesheet" href="../stylesheet.css">
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    </head>
    <body>
        <canvas id="canvas1"></canvas>
        <div id="form" class="area">

            <script src="../drawText/script.js"></script>
            <script>
                drawText('COSMIC CLASH tap anywhere');

                const form = document.getElementById('form');
                const canvas = document.getElementById('canvas1');
                
                canvas.addEventListener("click", changeFormVisibility);

                function changeFormVisibility(){
                    if(form.style.display == 'none' || form.style.display == '')
                        form.style.display='flex';
                    else
                        form.style.display='none';
                }
            </script>

            <form class="area-content animate" id="signup" action="signupProcess.php" method="post">
                        <p id="info">Sign Up to Cosmic Clash</p>
                        <?php if(isset($_GET["error"])) { ?>
                            <p class="error"><?php echo htmlspecialchars($_GET["error"]);?></p>
                            <?php echo '<script type="text/javascript">' . 'changeFormVisibility();' . '</script>';?>
                        <?php } ?>
                        <label for="FirstName"><b>First Name</b></label>
                        <input id="FirstName" type="text" name="FirstName" placeholder="First Name" value="<?php echo isset($_SESSION["form_data"]["FirstName"]) ? htmlspecialchars($_SESSION["form_data"]["FirstName"]) : '';?>" required>

                        <label for="LastName"><b>Last Name</b></label>
                        <input id="LastName" type="text" name="LastName" placeholder="Last Name" value="<?php echo isset($_SESSION["form_data"]["LastName"]) ? htmlspecialchars($_SESSION["form_data"]["LastName"]) : '';?>" required>

                        <label for="mail"><b>Email address</b></label>
                        <input id="mail" type="email" name="mail" placeholder="Enter your email" value="<?php echo isset($_SESSION["form_data"]["mail"]) ? htmlspecialchars($_SESSION["form_data"]["mail"]) : '';?>" required>

                        <label for="uname"><b>Username</b></label>
                        <input id="uname" type="text" name="uname" placeholder="Enter Username" value="<?php echo isset($_SESSION["form_data"]["uname"]) ? htmlspecialchars($_SESSION["form_data"]["uname"]) : '';?>" required>

                        <label for="psw"><b>Password</b></label>
                        <input id="psw" type="password" name="psw" placeholder="Enter Password" value="<?php echo isset($_SESSION["form_data"]["psw"]) ? htmlspecialchars($_SESSION["form_data"]["psw"]) : '';?>" required>

                        <label for="repeatPsw"><b>Repeat Password</b></label>
                        <input id="repeatPsw" type="password" name="repeatPsw" placeholder="Repeat Password" value="<?php echo isset($_SESSION["form_data"]["repeatPsw"]) ? htmlspecialchars($_SESSION["form_data"]["repeatPsw"]) : '';?>" required>

                        <button type="submit">Sign Up</button>

                        <p>Already have an account: <a href="../index.php">Login</a></p>
            </form><!--area-content-->
        </div><!--area-->
    </body>
</html>