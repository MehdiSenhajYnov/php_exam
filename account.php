<?php

include('DatabaseClass.php');
include('user.php');
include('NavBar.php');

session_start();
$currentUser = null;
$isConnected = false;
if (isset($_SESSION['currentuser'])) {
    $currentUser = $_SESSION['currentuser'];
    $isConnected = true;
}

if (!isset($_GET['AccountId'])) {
    header('Location: notfound.php');
    exit();
}
$AccountId = intval($_GET['AccountId']);
$UserDB = Database::UserDB();
if ($AccountId == WebUser::getCurrentUserId()) {
    header('Location: myaccount.php');
    exit();
}
$Users = $UserDB->getWhere(array('id', 'username', 'email', 'solde', 'profile_picture'), array('id' => $AccountId));
if (count($Users) == 0) {
    header('Location: notfound.php');
    exit();
}
$User = $Users[0];
$UserName = $User['username'];
$UserEmail = $User['email'];
//$UserPosts = $User['posts'];
//$UserFacture = $User['facture'];
if (isset($User['solde'])) {
    $UserSolde = $User['solde'];
} else {
    $UserSolde = 0;
}
if (isset($User['profile_picture'])) {
    $ProfilePicture = $User['profile_picture'];
} else {
    $ProfilePicture = "images/User.png";
}

$userarticles = Database::ArticleDB()->getWhere(array('id', 'name', 'description', 'price', 'image_link', 'author_id'), array('author_id' => $AccountId));



?>


<head>
    <title>M&M Compte</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/account.css">
</head>

<body>
    <?php showNavBar(); ?>

    <div class="accountcontainer">
        <?php
        echo "<div class='pagetitle'>";
        echo strtoupper($UserName);
        echo "<div class='myunderline'></div>";
        echo "</div>";

        echo "<div class='infoparent'>";
        echo "<div class='ImageParent'>";
        echo "<label class='infotitle' for='username'>IMAGE :</label>";
        echo "<img class='profilepicture' src='$ProfilePicture' alt='Profile Picture'>";
        echo "</div>";

        echo "<label class='infotitle' for='username'>NOM : $UserName</label>";
        echo "<label class='infotitle' for='username'>EMAIL : $UserEmail</label>";
        echo "<label class='infotitle' for='username'>SOLDE : $UserSolde</label>";
        echo "</div>";
        echo "<div class='pagetitle'>";
        echo "POSTS";
        echo "<div class='myunderline'></div>";
        echo "</div>";
        echo "<div class='cardcontainer'>";
        foreach ($userarticles as $key => $value) {
            $artId = $value['id'];
            $imageUrl = $value['image_link'];
            echo "<div class='card'>";
            echo "<a class='cardtitle' href='detail.php?Articleid=$artId'>$value[name]</a>";
            echo "<img class='cardimg' src=$imageUrl alt='produit1'>";
            echo "<div class='cardprice'>";
            echo "PRIX $value[price]€";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
        ?>

    </div>




</body>