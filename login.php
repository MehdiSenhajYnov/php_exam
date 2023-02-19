<head>
    <title>CONNEXION</title>
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/login.css">
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
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Validate and check the username and password
    // ...
    $dbResult = $UserDb->getWhere(array('id', 'username', 'email', 'password', 'role'), array('username' => $username))[0];

    if (!is_null($dbResult) && password_verify($password, $dbResult['password'])) {
        $error = "Bon utilisateur";

        $currentUser = new WebUser($dbResult['username'], $dbResult['email'], $dbResult['id'], $dbResult['role']);

        // If the username and password are correct, redirect to the home page
        $_SESSION['currentuser'] = $currentUser;
        header('Location: accueil.php');

        exit();
    } else {
        $error = "Mauvaise password ou nom utilisateur";
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
        <label class="firstlabel" for="username">IDENTIFIANT :</label>
        <input type="text" id="username" name="username">

        <label for="password">MOT DE PASSE : </label>
        <input type="password" id="password" name="password">

        <a href="passwordforgot.php" class="forgot">MOT DE PASSE OUBLIÉ ? </a>
        <input type="submit" class="connectbutton" value="CONNEXION">
        <?php
        if (isset($error)) {
            echo "<span class='connectgoogletext'>$error</span>";
        }
        ?>
        <?php /*
         <span class="connectgoogletext">CONNEXION AVEC GOOGLE</span>
         <input type="image" class="connectgoogle" src="images/GoogleLogo.png" alt="connexion avec google">
         */?>
    </form>


</body>