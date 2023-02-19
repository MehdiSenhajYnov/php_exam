<head>
    <title>Register Page</title>

    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/register.css">
</head>
<?php
function IsNullOrEmptyString($str)
{
    return ($str === null || trim($str) === '');
}

include('DatabaseClass.php');
include('user.php');

session_start();
if (isset($_SESSION['currentuser'])) {
    header('Location: accueil.php');
    exit();
}



$UserDb = Database::UserDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (IsNullOrEmptyString($username) || IsNullOrEmptyString($password) || IsNullOrEmptyString($email)) {
        echo "Complete all info";
        return;
    }


    $dbResult = $UserDb->getWhere(array('username', 'email', 'password'), array('username' => $username));

    if (!is_null($dbResult)) {
        $erreur = "User already exist!";

    } else {

        $UserDb->insert(array('username' => $username, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT), 'role' => 'user'));

        $dbResult = $UserDb->getWhere(array('id', 'username', 'email', 'password', 'role'), array('username' => $username))[0];

        $currentUser = new WebUser($username, $email, $dbResult['id'], $dbResult['role']);

        // If the username and password are correct, redirect to the home page
        $_SESSION['currentuser'] = $currentUser;

        // If the username and password are correct, redirect to the home page
        header('Location: accueil.php');
        exit();
    }

    // Redirect to the login page
    //header('Location: index.php');
    //exit;
}
?>

<body>
    <header>
        <div id="logo">
            <img class="logoimg" src="images/logo.png" alt="logo">
        </div>
    </header>

    <form id="register-form" method="post">
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
        <?php /*
         <span class="connectgoogletext">INSCRIVEZ VOUS AVEC GOOGLE</span>
         <input type="image" class="connectgoogle" src="images/GoogleLogo.png" alt="connexion avec google">
         <span class="connectgoogletext">OU UTILISEZ VOTRE <br> ADDRESSE E-MAIL :</span>
         */?>

        <label class="firstlabel" for="username">ADRESSE E-MAIL :</label>
        <input type="email" id="email" name="email" required>

        <label class="firstlabel" for="username">IDENTIFIANT :</label>
        <input type="text" id="username" name="username" required>

        <label for="password">MOT DE PASSE : </label>
        <input type="password" id="password" name="password" required>
        <?php
        if (isset($erreur)) {
            echo "<span class='connectgoogletext'>$erreur</span>";
        }
        ?>

        <input type="submit" class="comptecreationbutton" value="CREER VOTRE COMPTE">
    </form>


</body>