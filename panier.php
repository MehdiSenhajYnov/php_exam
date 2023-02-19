<?php
include('user.php');
include('NavBar.php');
include('DatabaseClass.php');
session_start();
if (!WebUser::isConnected()) {
    header('Location: login.php');
    exit();
}
$isConnected = true;
$currentUserId = WebUser::getCurrentUserId();

if (isset($_GET['ArticleReduce'])) {
    $cartId = $_GET['ArticleReduce'];
    $CartDb = Database::CartDB();
    $CartDbResult = $CartDb->getWhere(array('id', 'user_id', 'article_id', 'quantity'), array('id' => $cartId));
    if ($CartDbResult[0]['quantity'] - 1 == 0) {
        $CartDb->delete($cartId);
    } else {
        // reduce one to the quantity
        $CartDb->update($cartId, array('quantity' => $CartDbResult[0]['quantity'] - 1));
    }

} else if (isset($_GET['ArticleAdd'])) {
    $cartId = $_GET['ArticleAdd'];
    $CartDb = Database::CartDB();
    $CartDbResult = $CartDb->getWhere(array('id', 'user_id', 'article_id', 'quantity'), array('id' => $cartId));
    $CartDb->update($cartId, array('quantity' => $CartDbResult[0]['quantity'] + 1));
} else if (isset($_GET['ArticleDelete'])) {
    $cartId = $_GET['ArticleDelete'];
    $CartDb = Database::CartDB();
    $CartDbResult = $CartDb->getWhere(array('id', 'user_id', 'article_id', 'quantity'), array('id' => $cartId));
    $CartDb->delete($cartId);


}

$mysqli = new mysqli("localhost", "root", "", "php_exam_db");
$result = $mysqli->query("SELECT cart.id, cart.article_id, taille, quantity, author_id, name, price, image_link
FROM cart 
INNER JOIN article ON cart.article_id = article.id
WHERE user_id = $currentUserId");
$AllArticlesinCart = array();
while ($row = $result->fetch_assoc()) {
    $AllArticlesinCart[] = $row;
}

if (isset($_GET['CartValidate'])) {
    $cartId = $_GET['CartValidate'];
    $AllSellers = array();
    $PrixTotal = 0;
    $AllArticlesNames = array();
    foreach ($AllArticlesinCart as $article) {
        $PrixTotal += $article['price'] * $article['quantity'];
        if (!in_array($article['author_id'], $AllSellers)) {
            $AllSellers[] = $article['author_id'];
        }
        if (isset($AllArticlesNames[$article['name']])) {
            $AllArticlesNames[$article['name']] += $article['quantity'];
        } else {
            $AllArticlesNames[$article['name']] = $article['quantity'];
        }

    }
    $TransactionDate = date('d-m-Y H:i:s');

    $Client = WebUser::getCurrentUser()->username;

    $FactureDb = Database::FactureDB();
    $FactureDb->insert(array('user_id' => $currentUserId, 'transaction_date' => $TransactionDate, '	amount' => $PrixTotal, 'produits' => json_encode($AllArticlesNames)));

    $AllArticlesinCart = array();

    $FactureId = $FactureDb->getWhere(array('id'), array('user_id' => $currentUserId, 'transaction_date' => $TransactionDate))[0]['id'];

    $CartDb = Database::CartDB();
    $CartDb->delete($cartId);

    header('Location: facture.php?factureid=' . $FactureId);
    exit();
}

?>

<head>
    <title>M&M Accueil</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/panier.css">
</head>

<body>
    <?php
    showNavBar();
    ?>
    <div class="paniertitlediv">
        <img class="panierimg" src="images/Panier.png" alt="panierlogo">
        <span class="panierspan">Votre panier</span>

    </div>

    <div class="allarticles">
        <?php
        foreach ($AllArticlesinCart as $key => $value) {
            $artId = $value['article_id'];
            $cartId = $value['id'];
            $artImage = $value['image_link'];
            echo "<div class='horizontalsplit'>";
            echo "<img class='cardimg' src='$artImage' alt='produit'>";
            echo "<div class='verticalsplitter'>";
            echo "<a class='cardtitle' href='detail.php?Articleid=$artId'>$value[name]</a>";
            echo "<div class='cardprice'>$value[price]€</div>";
            echo "<div class='cardprice'>Taille : $value[taille]</div>";

            echo "<div class='quantitychangerparent'>";
            echo "<a class='quantitychanger' href='panier.php?ArticleReduce=$cartId'>";
            echo "-";
            echo "</a>";

            echo "<a class='quantitychanger' href='panier.php?ArticleAdd=$cartId'>";
            echo "+";
            echo "</a>";
            echo "</div>";

            echo "<div class='cardprice'>Nombre : $value[quantity]</div>";
            echo "</div>";
            echo "</div>";
        }
        ?>
        <?php
        if (count($AllArticlesinCart) > 0) {
            $cartId = $AllArticlesinCart[0]['id'];

            echo "<a class='connectbutton' href='panier.php?CartValidate=$cartId'> ACHETER </a>";
        }
        ?>
        <div>
        </div>

    </div>


</body>

</html>