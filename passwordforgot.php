<head>
    <title>CONNEXION</title>
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/passwordforgot.css">
</head>

<?php
include('DatabaseClass.php');
include('user.php');
$UserDb = Database::UserDB();
session_start();

if (isset($_SESSION['currentuser'])) {
    header('Location: accueil.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $dbResult = $UserDb->getWhere(array('id', 'username', 'email', 'password', 'role'), array('email' => $email));

    if (!is_null($dbResult) && count($dbResult) > 0) {
        $new_password = rand();

        $UserDb->update($dbResult[0]['id'], array('password' => password_hash($new_password, PASSWORD_DEFAULT)));

        $to = $email;
        $subject = "M&M Mot de passe oublié";
        $txt = "Bonjour, voici votre nouveau mot de passe : " . $new_password;
        $headers = "From: mehdisenhaj03@gmail.com";
        mail($to, $subject, $txt, $headers);
        $error = "Mot de passe : " . $new_password;
    } else {
        $error = "Mauvaise password ou nom utilisateur " . $email;
    }
}
?>


<body>
    <header>
        <div id="logo">
            <img class="logoimg" src="images/logo.png" alt="logo">
        </div>
    </header>

    <form id="login-form" method="post">
        <div class="loginregister">
            <span>
                <a href="register.php" id="comptecreation">CREER UN COMPTE</a>
                <div class="myunderline"></div>
            </span>
            <span>
                <a href="login.php" id="compteconnexion">CONNEXION</a>
                <div class="myunderline"></div>
            </span>
        </div>
        <label class="firstlabel" for="email">EMAIL :</label>
        <input type="text" id="email" name="email">

        <input type="submit" class="connectbutton" value="Recuperer votre mot de passe">
        <?php
        if (isset($error)) {
            echo "<span class='connectgoogletext'>$error</span>";
        }
        ?>

    </form>


</body>