<?php
include('user.php');
include('NavBar.php');
include('DatabaseClass.php');

session_start();
if (isset($_GET['DeleteArticleid']))
{
    $Articleid = $_GET['DeleteArticleid'];
    Database::deleteArticleAndReferences($Articleid);
    header('Location: deletedarticle.php');
    exit();
}
if (!isset($_GET['Articleid'])) {
    header('Location: notfound.php');
    exit();
}


$ArticleDb = Database::ArticleDB();
$Articleid = $_GET['Articleid'];
$ArticleDbResult = $ArticleDb->getWhere(array('id', 'name', 'price', 'description', 'image_link', 'author_id'), array('id' => $Articleid));
if (!isset($ArticleDbResult) || count($ArticleDbResult) == 0) {
    header('Location: notfound.php');
    exit();
}
$Article = $ArticleDbResult[0];
$UserCreator = Database::UserDB()->getWhere(array('username', 'profile_picture'), array('id' => $Article['author_id']))[0];
$UserCreatorName = $UserCreator['username'];
if (isset($UserCreator['profile_picture'])) {
    $UserCreatorProfilePicture = $UserCreator['profile_picture'];
} else {
    $UserCreatorProfilePicture = "images/User.png";
}

if (isset($_POST['size']) && isset($_POST['quantity']) && isset($_POST['AddToCart'])) {
    if (WebUser::isConnected() == false) {
        header('Location: login.php');
        exit();
    }
    $size = $_POST['size'];
    $boughtquantity = $_POST['quantity'];
    // Check if the article is already in the cart
    $CartDb = Database::CartDB();
    $CartDbResult = $CartDb->getWhere(array('id', 'user_id', 'article_id', 'quantity'), array('user_id' => WebUser::getCurrentUserId(), 'article_id' => $Articleid, 'taille' => $size));
    if (is_countable($CartDbResult) && count($CartDbResult) > 0) {
        // Add one to the quantity
        $CartDb->update($CartDbResult[0]['id'], array('quantity' => $CartDbResult[0]['quantity'] + intval($boughtquantity)));
    } else {
        // Insert a new row
        $CartDb->insert(array('user_id' => WebUser::getCurrentUserId(), 'article_id' => $Articleid, 'quantity' => $boughtquantity, 'taille' => $size));
    }

    header('Location: Panier.php');
    exit();
    //$currentUser = $_SESSION['currentuser'];
    //$currentUser->addArticleToCart($Articleid, $size);
    //header('Location: panier.php');
}
$artImage = $Article['image_link'];

if (WebUser::getCurrentUserId() == $Article['author_id']) {
    $StockDB = Database::StockDB();
    $StockDBResult = $StockDB->getWhere(array('id', 'article_id', 'quantity'), array('article_id' => $Articleid))[0];
    $Stock = $StockDBResult['quantity'];

    if (isset($_POST['quantity']) && isset($_POST['AddToStock'])) {
        $boughtquantity = $_POST['quantity'];
        $StockDB->update($StockDBResult['id'], array('quantity' => $StockDBResult['quantity'] + intval($boughtquantity)));
        header('Location: detail.php?Articleid=' . $Articleid);
        exit();
    }
}

?>

<head>
    <title>M&M Article</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/article.css">
</head>

<body>
    <?php showNavBar(); ?>

    <div class="articlecreator">
        <img class="articlecreatorprofilepicture" src="<?php echo $UserCreatorProfilePicture; ?>" alt="profilepicture">
        <?php echo "<a class='articlecreatorname' href='account.php?AccountId=$Article[author_id]'>$UserCreatorName</a>"; ?>
        <?php
            if (WebUser::isConnected() && (WebUser::getCurrentUserId() == $Article['author_id'] || WebUser::isAdmin())) {
                echo "<a class='adminbutton' href='articlechange.php?Articleid=$Articleid'><div class='modifierbutton'>Modifier</div></a>";
            }
        ?>
    </div>
    <div class="articlecontainer">
        <?php echo "<img class='cardimg' src='$artImage' alt='produit1'>"; ?>
        <div class="articleinfo">
            <div class="inlineparent">
                <?php echo "<div class='articletitle'>$Article[name]</div>";
                if (WebUser::isConnected() && (WebUser::getCurrentUserId() == $Article['author_id'] || WebUser::isAdmin())) {
                echo "<a class='adminbutton' href='detail.php?DeleteArticleid=$Articleid'><div class='modifierbutton'>Effacer le post</div></a>";
                }
                ?>
            </div>

            <?php echo "<div class='articleprice'>PRICE : $Article[price]€</div>"; ?>
            <form method="post">

                <label class="sizelabel" for="size">TAILLE</label>
                <label class="sizeselectionlabel">

                    <select id="size" name="size">
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                </label>
                <br>
                <div class="inlineparent">
                    <label class="sizelabel" for="size">QUANTITÉ</label>
                    <label class="inlinesizeselectionlabel">

                        <select id="size" name="quantity">
                            <?php
                            for ($i = 1; $i <= 10; $i++) {
                                echo "<option value='$i'>$i</option>";
                            }
                            ?>
                        </select>
                    </label>
                    <?php
                    if (isset($Stock)) {
                        echo "<label class='sizelabel' for='size'> STOCK&nbsp;:&nbsp;$Stock</label>";
                    }
                    ?>
                </div>
                <div class="inlineparent">
                    <input class="panierbutton" type="submit" value="Ajouter au panier" name="AddToCart">
                    <?php
                    if (WebUser::isConnected() && (WebUser::getCurrentUserId() == $Article['author_id'])) {
                        echo "<input class='panierbutton' type='submit' value='Ajouter au Stock' name='AddToStock'>";
                    }
                    ?>

                </div>
            </form>
        </div>
    </div>
    <div class="articledescription">
        <div class="descriptionTitle">DESCRIPTION : </div>
        <?php echo "<div class='articledescriptiontext'>$Article[description]</div>"; ?>

    </div>
</body>

</html>