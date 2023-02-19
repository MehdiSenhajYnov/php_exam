<?php
include('DatabaseClass.php');
include('user.php');
include('NavBar.php');

$UserDb = Database::UserDB();
session_start();

if (!isset($_SESSION['currentuser'])) {
    header('Location: login.php');
    exit();
}

$AllUsersResult = $UserDb->getAll();
$AllUsername = array();
foreach ($AllUsersResult as $user) {
    array_push($AllUsername, $user['username']);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['UserSelect'])) {
    $UserId = $UserDb->getWhere(array('id'), array('username' => $_POST['UserSelect']))[0]['id'];
    if (isset($_POST['USERADMIN'])) {
        $UserDb->update($UserId, array('role' => 'admin'));
    } else if (isset($_POST['USERDELETE'])) {
        Database::deleteUserAndReferences($UserId);
    } else if (isset($_POST['USERCHANGE'])) {
        $UserDb->update($UserId, array('username' => $_POST['newusername']));
    } else if (isset($_POST['GETPOSTS'])) {
        $PostDb = Database::ArticleDB();
        $posts = $PostDb->getWhere(array('id', 'name', 'description', 'price', 'publication_date', 'author_id', 'image_link'), array('author_id' => $UserId));
    }
    header('Location: admin.php');
    exit();
}



?>

<head>
    <title>M&M Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/admin.css">
</head>

<body>
    <?php showNavBar(); ?>

    <div class="tableaucontainer">
        <div class='pagetitle'>
            TABLEAU ADMINISTRATEUR
            <div class='myunderline'></div>
        </div>

        <form method="post">
            <div class="infocontainer">
                <div class="infotitle">
                    UTILISATEURS
                </div>
        <?php if (is_countable($AllUsername) && count($AllUsername) > 1) : ?>
                <label class="sizeselectionlabel">
                    <select id="UserSelect" name="UserSelect">
                        <?php

                            foreach ($AllUsername as $username) {
                                if ($username != WebUser::getCurrentUser()->username)
                                {
                                    echo "<option value='$username'>$username</option>";

                                }
                            }
                        
                        ?>
                    </select>
                </label>
                <input class="actionbutton" type="submit" value="Rendre Admin" name="USERADMIN">
                <input class="actionbutton" type="submit" value="Effacer utilisateur" name="USERDELETE">
            </div>
            <div class="infocontainer">
                <input type="text" id="username" name="newusername">
                <input class="actionbutton" type="submit" value="Changer username" name="USERCHANGE">
            </div>
            <div class="infocontainer">
                <input class="actionbutton" type="submit" value="Recuperer tout ses posts" name="GETPOSTS">
            </div>
        <?php else : ?>
            <label class="sizeselectionlabel">
                    <select id="UserSelect" name="UserSelect">
                        <option value='Pas d'utilisateur'>Pas d'utilisateur</option>
                    </select>
                </label>
        <?php endif;?>
        </form>


        <?php
        if (isset($posts) && !empty($posts)) {
            echo "<div class='pagetitle'>";
            echo "POST DE JOHN";
            echo "</div>";

            echo "<div class='cardcontainer'>";
            foreach ($posts as $key => $value) {
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


        ?>

    </div>




</body>