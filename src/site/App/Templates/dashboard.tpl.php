<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord</title>

    <link rel="stylesheet" href="Public/style.css">
</head>
<body>

<h1>Ferme Manager</h1>

<section id="inventory">
    <h2>Inventaire</h2>

    <?php foreach ($products as $product => $data) { ?>
    <article id="product-<?php echo $product ?>">
        <h3><?php echo $data->icon." ".$data->display ?></h3>
        <div>Stock : <output class="stock"><?php echo $inventory[$product] ?? 0 ?></output></div>
    </article>
    <?php } ?>

</section>

<hr>

<section id="buildings">
    <h2>Bâtiments</h2>

    <?php foreach ($buildings as $building => $data) {
        $level = $userBuildings[$building]['level'] ?? 1;
        $nextLevel = $level + 1;
        $upgradeCost = $gameConfigRepository->getUpgradeCost($building, $nextLevel);
        $canUpgrade = ($inventory[$data->cost] ?? 0) >= $upgradeCost;
        ?>
    <article id="buildings-<?php echo $building?>">
        <h3><?php echo $data->display ?> (Niv. <output class="level"><?php echo $level ?></output>)</h3>

        <form method="POST">
            <input type="hidden" name="action" value="harvest">
            <input type="hidden" name="building_id" value="<?php echo $building ?>">
            <button type="submit" class="harvest"><?php echo $data->action ?></button>
        </form>

        <?php if (isset($data->cost, $products->{$data->cost})): ?>
        <form method="POST">
            <input type="hidden" name="action" value="upgrade">
            <input type="hidden" name="building_id" value="<?php echo $building ?>">
            <button type="submit" class="upgrade" <?php echo !$canUpgrade ? 'disabled' : '' ?>>
                Améliorer <br>
                Coût : <output class="cost"><?php echo $upgradeCost ?> <?php echo $products->{$data->cost}->icon ?></output>
            </button>
        </form>
        <?php endif; ?>
    </article>

    <?php } ?>

</section>

</body>
</html>


