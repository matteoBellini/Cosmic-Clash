<?php
    session_start();
    //se è già stato effettuato il login l'utente viene reindirizzato alla home
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
        <title>Login to Cosmic Clash</title>
        <link rel="stylesheet" href="stylesheet.css">
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    </head>
    <body>
        <canvas id="canvas1"></canvas>
        <div id="form" class="area">

            <script src="drawText/script.js"></script>
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

            <form class="area-content animate" id="signup" action="login.php" method="post">
                <p id="info">Login to Cosmic Clash</p>
                <?php if(isset($_GET["error"])) { ?>
                    <p class="error"><?php echo htmlspecialchars($_GET["error"]);?></p>
                    <?php echo '<script type="text/javascript">' . 'changeFormVisibility();' . '</script>'?>
                <?php } ?>
                <label for="username"><b>Username</b></label>
                <input id="username" type="text" name="username" placeholder="Enter Username" value="<?php echo isset($_SESSION["form_data"]["username"]) ? htmlspecialchars($_SESSION["form_data"]["username"]) : '';?>" required>

                <label for="password"><b>Password</b></label>
                <input id="password" type="password" name="password" placeholder="Enter Password" value="<?php echo isset($_SESSION["form_data"]["password"]) ? htmlspecialchars($_SESSION["form_data"]["password"]) : '';?>" required>
                <button type="submit"><b>Login</b></button>
                <p>If you don't have an account: <a href="signup/signup.php">Register now!</a></p>
            </form>
        </div><!--area-->
    </body>
</html>