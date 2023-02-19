<head>
    <title>M&M Favoris</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/accueil.css">
</head>
<?php
include('user.php');
include('NavBar.php');
include('DatabaseClass.php');

session_start();
$currentUser = null;
$isConnected = false;
if (isset($_SESSION['currentuser'])) {
    $currentUser = $_SESSION['currentuser'];
    $isConnected = true;
    $likedarticles = WebUser::getLikedArticles();
    if (!is_countable($likedarticles) || count($likedarticles) == 0) {
        $likedarticles = array();
    }
}
if (isset($_GET['username']) && $_GET['username'] == "DisconnectUser") {
    session_destroy();
    header('Location: Accueil.php');
    exit();
}
if (isset($_GET['likedpost'])) {
    $likedpost = $_GET['likedpost'];
    $likedarticles = WebUser::getLikedArticles();
    if (!is_countable($likedarticles) || count($likedarticles) == 0) {
        $likedarticles = array();
    }
    if ($likedarticles == null) {
        $likedarticles = array();
        array_push($likedarticles, $likedpost);
        Database::UserDB()->update(WebUser::getCurrentUserId(), array('liked_articles' => json_encode($likedarticles)));
        header('Location: favoris.php');
        exit();
    } else {
        if (!in_array($likedpost, $likedarticles)) {
            array_push($likedarticles, $likedpost);
            Database::UserDB()->update(WebUser::getCurrentUserId(), array('liked_articles' => json_encode($likedarticles)));
            header('Location: favoris.php');
            exit();
        }
    }
}
if (isset($_GET['dislikedpost'])) {
    $dislikedpost = $_GET['dislikedpost'];
    $likedarticles = WebUser::getLikedArticles();
    if (!is_countable($likedarticles) || count($likedarticles) == 0) {
        $likedarticles = array();
    }
    if ($likedarticles != null) {
        if (in_array($dislikedpost, $likedarticles)) {
            $key = array_search($dislikedpost, $likedarticles);
            unset($likedarticles[$key]);
            Database::UserDB()->update(WebUser::getCurrentUserId(), array('liked_articles' => json_encode($likedarticles)));
            header('Location: favoris.php');
            exit();
        }
    }
}

if (isset($likedarticles) && is_countable($likedarticles) && count($likedarticles) > 0) {
$likedarticlesstring = "";
    foreach ($likedarticles as $key => $value) {
        $likedarticlesstring .= $value . ",";
    }
    $likedarticlesstring = substr($likedarticlesstring, 0, -1);
    
    $AllArticlesinStock = Database::ArticleDB()->generalQuery("
    SELECT id, name, price, description, genre, image_link FROM article
    WHERE id IN (
        SELECT article_id
        FROM stock
    WHERE quantity > 0
    ) AND id IN ($likedarticlesstring);
    ");
}
    
?>

<body>
    <?php showNavBar(); ?>
    <div class="cardcontainer">
        <?php
        if (isset($AllArticlesinStock)) {

            foreach ($AllArticlesinStock as $key => $value) {
                $artId = $value['id'];
                $imageUrl = $value['image_link'];
                echo "<div class='card'>";
                echo "<a class='cardtitle' href='detail.php?Articleid=$artId'>$value[name]</a>";
                echo "<img class='cardimg' src=$imageUrl alt='produit1'>";
                echo "<div class='cardprice'>";
                echo "PRIX $value[price]€";
                if (isset($likedarticles))
                {
                    if (!in_array($artId, $likedarticles)) {
                        echo "<a class='cardlikeimg' href='favoris.php?likedpost=$artId'><img src='images/likeicon.png' alt='likeblank' /></a>";
                    } else {
                        echo "<a class='cardlikeimg' href='favoris.php?dislikedpost=$artId'><img src='images/likeiconfull.png' alt='likefilled' /></a>";
                    }
                }
                echo "</div>";
                echo "</div>";
            }
        }
        ?>

    </div>
</body>

</html>