<head>
    <title>M&M Facture</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=League Gothic' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="style/facture.css">
</head>
<?php
include('user.php');
include('NavBar.php');
include('DatabaseClass.php');

session_start();

if (!isset($_GET['factureid'])) {
    header('Location: notfound.php');
    exit();
}
$factureid = $_GET['factureid'];

$FactureDb = Database::FactureDB();
$FactureDbResult = $FactureDb->getWhere(array('id', 'user_id', 'transaction_date', 'amount', 'produits'), array('id' => $factureid))[0];
$Client = Database::UserDB()->getWhere(array('username'), array('id' => $FactureDbResult['user_id']))[0]['username'];
$DateFacture = $FactureDbResult['transaction_date'];
$Produits = json_decode($FactureDbResult['produits']);
$ProduitStr = "";
foreach ($Produits as $key => $value) {
    $ProduitStr .= $key . " x" . $value . ", ";
}
$ProduitStr = substr($ProduitStr, 0, -2);
$PrixTotal = $FactureDbResult['amount'];
?>

<body>
    <?php showNavBar(); ?>
    <div class="facturecontainer">
        <div class="facturetitle">
            Facture
            <div class="factureunderline"></div>
        </div>
        <div class="factureinfocontainer">
            <div class="infotitle"> Vendeur </div>
            <div class="info"> M&M </div>
        </div>
        <div class="factureinfocontainer">
            <div class="infotitle"> Client </div>
            <?php echo "<div class='info'> $Client </div>"; ?>
        </div>
        <div class="factureinfocontainer">
            <div class="infotitle"> Date </div>
            <?php echo "<div class='info'> $DateFacture </div>"; ?>
        </div>
        <div class="factureinfocontainer">
            <div class="infotitle"> Produits </div>
            <?php echo "<div class='info'> $ProduitStr </div>"; ?>
        </div>
        <div class="factureinfocontainer">
            <div class="infotitle"> Prix Total </div>
            <?php echo "<div class='info'> $PrixTotal </div>"; ?>
        </div>
    </div>
</body>

</html>