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
} else {
    header('Location: login.php');
    exit();
}
$UserDB = Database::UserDB();
$AccountId = WebUser::getCurrentUserId();
$Users = $UserDB->getWhere(array('id', 'username', 'email', 'solde', 'profile_picture'), array('id' => $AccountId));
if (!isset($Users) || count($Users) == 0) {
    header('Location: notfound.php');
    exit();
}
$User = $Users[0];
$UserName = $User['username'];
$UserEmail = $User['email'];

if (isset($User['profile_picture'])) {
    $ProfilePicture = $User['profile_picture'];
} else {
    $ProfilePicture = "images/User.png";
}
//$UserPosts = $User['posts'];
//$UserFacture = $User['facture'];
if (isset($User['solde'])) {
    $UserSolde = $User['solde'];
} else {
    $UserSolde = 0;
}

if (isset($_POST['password']) && isset($_POST['password2'])) {
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    if ($password == $password2) {
        
        $UserDB->update($AccountId, array('password' => password_hash($password, PASSWORD_DEFAULT)));
        $strlog = "Password changé avec succès";
    } else {
        $strlog = "Les mots de passe ne correspondent pas";
    }
}

if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
    fwrite($debugfile, $_FILES['file']['name']);
    fclose($debugfile);
    $tmpName = $_FILES['file']['tmp_name'];
    $filename = $_FILES['file']['name'];
    $filesize = $_FILES['file']['size'];
    $fileerror = $_FILES['file']['error'];

    $tabExtension = explode('.', $filename);
    $extension = strtolower(end($tabExtension));

    $extensions = ['jpg', 'png', 'jpeg' /*, 'gif'*/];

    $maxSize = 400000;

    if (in_array($extension, $extensions) && $fileerror == 0) {
        $uniqueName = uniqid('', true);
        $fileURL = 'images/' . $uniqueName . "." . $extension;

        move_uploaded_file($tmpName, $fileURL);
    } else {
        $strlog = "Une erreur est survenue";

    }
    $UserDB->update($AccountId, array('profile_picture' => $fileURL));
    header('Location: myaccount.php?AccountId=' . $AccountId);
    exit();
}

$userarticles = Database::ArticleDB()->getWhere(array('id', 'name', 'description', 'price', 'image_link', 'author_id'), array('author_id' => $AccountId));
$userfactures = Database::FactureDB()->getWhere(array('id', 'user_id', 'transaction_date'), array('user_id' => $AccountId));


?>


<head>
    <title>My Account Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/myaccount.css">
</head>

<body>
    <?php showNavBar(); ?>

    <form id="login-form" method="POST" enctype="multipart/form-data">
        <?php
        echo "<div class='pagetitle'>";
        echo strtoupper($UserName);
        echo "<div class='myunderline'></div>";
        echo "</div>";
        echo "<div class='profilepictureParent'>";
        echo "<label class='formlabel' for='username'>IMAGE :</label>";
        echo "<img class='profilepicture' src='$ProfilePicture' alt='Profile Picture'>";
        echo "</div>";
        echo "<label class='formlabel' for='username'>NOM : $UserName</label>";
        echo "<label class='formlabel' for='username'>EMAIL : $UserEmail</label>";
        echo "<label class='formlabel' for='username'>SOLDE : $UserSolde</label>";
        echo "</div>";

        echo "<div class='labelParent'>";
        echo "<label class='formlabel' for='username'>CHANGER LE MOT DE PASSE</label>";
        if (isset($strlog)) {
            echo "<div class='log'>" . $strlog . "</div>";
        }
        echo "<input class='forminput' type='password' name='password' placeholder='Nouveau mot de passe'>";
        echo "<input class='forminput' type='password' name='password2' placeholder='Confirmer le mot de passe'>";
        echo "</div>";
        echo "<div class='labelParent'>";

        ?>
        <div class="userinput">
            <label class="formlabel" for="username">IMAGE :</label>

            <label for="images" class="dropcontainer">
                <input type="file" id="images" accept="image/*" name="file">
                <div class="inputtext">
                    DROP FILES HERE <br>
                    OR
                </div>
            </label>

        </div>
        <div class='labelParent'>
            <input class='connectbutton' type='submit' name='submit' value='Modifier'>
        </div>

        <?php
        if (isset($userarticles) && count($userarticles) > 0) {
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
        }

        if (isset($userfactures) && count($userfactures) > 0) {
            echo "</div>";
            echo "<div class='pagetitle'>";
            echo "FACTURES";
            echo "<div class='myunderline'></div>";
            echo "</div>";
            echo "<div class='cardcontainer'>";
            foreach ($userfactures as $key => $value) {
                $factId = $value['id'];
                $factDate = $value['transaction_date'];
                echo "<a class='facturetitle' href='facture.php?factureid=$factId'>Facture $factDate</a>";
            }
            echo "</div>";
        }

        ?>
    </form>


</body>