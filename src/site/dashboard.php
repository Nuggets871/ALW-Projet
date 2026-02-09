<?php

require_once "Utils/FileStorage.php";
require_once "Utils/GameConfigRepository.php";

$gameConfigRepository = new GameConfigRepository('Data/Config/game_config.json');

$products = $gameConfigRepository->getProducts();
$buildings = $gameConfigRepository->getBuildings();

foreach ($products as $product) {
//    var_dump($product);
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Maquette Ferme Manager</title>
    <style>
        /* Styles indicatifs (non imposés) */
        article {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 5px;
            display: inline-block;
            width: 200px;
            vertical-align: top;
        }

        .icon {
            font-size: 2em;
        }
    </style>

    <!-- Intégration du JS (Partie 2.1) -->
    <!-- <script src="Public/JS/FermeEngine.js" defer></script> -->
    <!-- <script src="Public/JS/main.js" defer></script> -->
</head>

<body>
<h1>Ferme Manager</h1>

<section id="inventory">
    <h2>Inventaire</h2>

    <?php foreach ($products as $product => $data) { ?>
    <article id="product-<?php echo $product ?>">
        <h3><?php echo $data->icon." ".$data->display ?></h3>
        <div>Stock : <output class="stock">0</output></div>
    </article>
    <?php } ?>

</section>

<hr>

<section id="buildings">
    <h2>Bâtiments</h2>

    <?php foreach ($buildings as $building => $data) { ?>
    <article id="buildings-<?php echo $building?>">
        <h3><?php echo $data->display ?> (Niv. <output class="level">1</output></h3>

        <button class="harvest"><?php echo $data->production ?></button>

        <?php if (isset($data->cost, $products->{$data->cost})): ?>
        <button class="upgrade">
            Améliorer <br>
            Coût : <output class="cost">10 <?php echo $products->{$data->cost}->icon ?></output>
        </button>
        <?php endif; ?>
    </article>

    <?php } ?>

</section>
</body>

</html>
