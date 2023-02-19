<?php

include('DatabaseClass.php');
include('user.php');
include('NavBar.php');

session_start();

if (!isset($_GET['Articleid'])) {
    header('Location: notfound.php');
    exit();
}

$currentUser = null;
$isConnected = false;
if (isset($_SESSION['currentuser'])) {
    $currentUser = $_SESSION['currentuser'];
    $isConnected = true;
} else {
    header('Location: login.php');
    exit();
}
$ArticleId = $_GET['Articleid'];
$Articles = Database::ArticleDB();
$Articlesresult = $Articles->getWhere(array('id', 'name', 'description', 'price', 'genre', 'image_link', 'publication_date', 'author_id'), array('id' => $ArticleId));
if (!is_countable($Articlesresult) || count($Articlesresult) == 0) {
    header('Location: notfound.php');
    exit();
}
$Article = $Articlesresult[0];


if (isset($_FILES['file']) && isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])) {
    $tmpName = $_FILES['file']['tmp_name'];
    $filename = $_FILES['file']['name'];
    $filesize = $_FILES['file']['size'];
    $fileerror = $_FILES['file']['error'];

    $tabExtension = explode('.', $filename);
    $extension = strtolower(end($tabExtension));
    //Tableau des extensions que l'on accepte
    $extensions = ['jpg', 'png', 'jpeg' /*, 'gif'*/];

    $maxSize = 400000;

    if (in_array($extension, $extensions) && $fileerror == 0) {
        $uniqueName = uniqid('', true);
        $fileURL = 'images/' . $uniqueName . "." . $extension;
        //echo "NEW FILE : " . $file;
        move_uploaded_file($tmpName, $fileURL);
    } else {
        echo "Une erreur est survenue";
    }
}

$Articles = Database::ArticleDB();
$UpdateArray = array();

if (isset($_POST['name']) && !empty($_POST['name'])) {
    $UpdateArray['name'] = $_POST['name'];
}
if (isset($_POST['prix']) && !empty($_POST['prix'])) {
    $UpdateArray['price'] = floatval($_POST['prix']);
}
if (isset($_POST['genre']) && $_POST['genre'] != "nochange") {
    $UpdateArray['genre'] = $_POST['genre'];
}
if (isset($_POST['description']) && !empty($_POST['description'])) {
    $UpdateArray['description'] = $_POST['description'];
}
if (isset($fileURL)) {
    $UpdateArray['image_link'] = $fileURL;
}

if (count($UpdateArray) > 0) {
    $Articles->update($ArticleId, $UpdateArray);
        
    header("Location: detail.php?Articleid=$ArticleId");
    exit();
}


?>


<head>
    <title>M&M Changement D'Article</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/articlechange.css">
</head>

<body>
    <?php showNavBar(); ?>

    <form id="login-form" method="POST" enctype="multipart/form-data">
        <div class="pagetitle">
            Changer un article
            <div class="myunderline"></div>
        </div>
        <div class="userinput">
            <label class="formlabel" for="username">NOM :</label>
            <input type="text" id="namelabel" name="name">
        </div>
        <div class="userinput">
            <label class="formlabel" for="username">PRIX :</label>
            <input type="text" id="prixlabel" name="prix">
        </div>

        <div class="userinput">
            <label class="formlabel" for="genre">GENRE :</label>
            <label class="sizeselectionlabel">
                <select id="genre" name="genre">
                    <option value="nochange">DEFAULT</option>
                    <option value="homme">HOMME</option>
                    <option value="femme">FEMME</option>
                    <option value="unisex">UNISEX</option>
                </select>
            </label>
        </div>


        <div class="userinput">
            <label class="formlabel" for="username">IMAGE :</label>

            <label for="images" class="dropcontainer">
                <input type="file" id="images" accept="image/*" name="file">
            </label>

            <div class="inputtext">
                DROP FILES HERE
                OR
            </div>
        </div>


        <div class="userinput">
            <label class="formlabel descriptionlabel" for="password">DESCRIPTION :</label>
            <textarea class="textarea" name="description"></textarea>
        </div>

        <input type="submit" class="connectbutton" value="CHANGER">
    </form>


</body>