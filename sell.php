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

if (isset($_FILES['file'])) {
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

    $Articles = Database::ArticleDB();

    $articlename = $_POST['name'];
    $articleprice = $_POST['prix'];
    $articlegenre = $_POST['genre'];
    $articledescription = $_POST['description'];
    $articledate = date('Y-m-d');
    $articleauthorId = WebUser::alternatifGetCurrentUserId();
    //$Articles->update($allArticles["id"], array("image_link" => $fileURL));
    $Articles->insert(array('name' => $articlename, 'description' => $articledescription, 'price' => $articleprice, 'genre' => $articlegenre, 'image_link' => $fileURL, 'publication_date' => $articledate, 'author_id' => $articleauthorId));

    $lastId = $Articles->getWhere(array('id'), array('image_link' => $fileURL))[0]['id'];

    $stocknb = $_POST['stocknb'];
    $stock = Database::StockDB();
    $stock->insert(array('article_id' => $lastId, 'quantity' => $stocknb));

    header("Location: detail.php?Articleid=$lastId");

}


?>


<head>
    <title>Sell Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/sell.css">
</head>

<body>
    <?php showNavBar(); ?>

    <form id="login-form" method="POST" enctype="multipart/form-data">
        <div class="pagetitle">
            Creer un nouvel article
            <div class="myunderline"></div>
        </div>
        <div class="userinput">
            <label class="formlabel" for="username">NOM :</label>
            <input type="text" id="namelabel" name="name" required>
        </div>
        <div class="userinput">
            <label class="formlabel" for="username">PRIX :</label>
            <input type="text" id="prixlabel" name="prix" required>
        </div>

        <div class="userinput">
            <label class="formlabel" for="genre">GENRE :</label>
            <label class="sizeselectionlabel">
                <select id="genre" name="genre">
                    <option value="homme">HOMME</option>
                    <option value="femme">FEMME</option>
                    <option value="unisex">UNISEX</option>
                </select>
            </label>
        </div>


        <div class="userinput">
            <label class="formlabel" for="username">IMAGE :</label>

            <label for="images" class="dropcontainer">
                <input type="file" id="images" accept="image/*" name="file" required>
            </label>

            <div class="inputtext">
                DROP FILES HERE
                OR
            </div>
        </div>


        <div class="userinput">
            <label class="formlabel descriptionlabel" for="password">DESCRIPTION :</label>
            <textarea class="textarea" name="description" required></textarea>
        </div>
        <div class="userinput">
            <label class="formlabel" for="username">STOCK :</label>
            <input type="text" id="prixlabel" name="stocknb" required>
        </div>
        <input type="submit" class="connectbutton" value="METTRE EN VENTE">
    </form>


</body>