<link rel="stylesheet" href="style/navbar.css">
<?php
function showNavBar()
{

    $currentUser = null;
    $isConnected = false;
    if (isset($_SESSION['currentuser'])) {
        $currentUser = $_SESSION['currentuser'];
        $isConnected = true;
    }
    if (isset($_GET['username']) && $_GET['username'] == 'DisconnectUser') {
        session_destroy();
        header('Location: Accueil.php');
        exit();
    }

    echo "<header>";
    echo "<div id='logo'>";
    echo "<img class='logoimg' src='images/logo.png' alt='logo'>";
    echo "</div>";
    echo "<div id='menu'>";
    echo "<ul>";
    echo "<a class='menuitem' href='Accueil.php'>Accueil</a>";
    echo "<a class='menuitem' href='homme.php'>Homme</a>";
    echo "<a class='menuitem' href='femme.php'>Femme</a>";
    if ($isConnected) {
        echo "<a class='menuitem' href='favoris.php'>Favoris</a>";
        if ($currentUser->role == 'admin') {
            echo "<a class='menuitem' href='sell.php'>Vendre</a>";
            echo "<a class='menuitem' href='admin.php'>Admin</a>";
        }
        echo "<a class='menuitem' href='myaccount.php'>";
        echo $currentUser->username;
        echo "</a>";
        echo "<a class='menuitem' href='Accueil.php?username=DisconnectUser'>";
        echo "Deconnexion";
        echo "</a>";
        echo "<a class='menuitem' href='Panier.php'><img src='images/Panier.png' alt='Panier' /></a>";
    } else {
        echo "<a class='menuitem' href='login.php'>";
        echo "Connexion";
        echo "</a>";
    }
    echo "</ul>";
    echo "</header>";
}
?>