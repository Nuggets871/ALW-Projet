<?php

require_once "Utils/User.php";
require_once "Utils/FileStorage.php";
require_once "Utils/GameConfigRepository.php";

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

$gameConfigRepository = new GameConfigRepository('Data/Config/game_config.json');

$products = $gameConfigRepository->getProducts();
$buildings = $gameConfigRepository->getBuildings();

$saveFile = 'Data/Saves/' . $user->login . '.json';
if (!file_exists($saveFile)) {
    copy('Data/Config/save_initial.json', $saveFile);
}

$saveData = json_decode(file_get_contents($saveFile), true);
$inventory = $saveData['inventory'];
$userBuildings = $saveData['buildings'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'], $_POST['building_id'])) {
        $action = $_POST['action'];
        $buildingId = $_POST['building_id'];

        if ($action === 'harvest' && isset($buildings->{$buildingId})) {
            $resource = $buildings->{$buildingId}->production;
            $level = $userBuildings[$buildingId]['level'] ?? 1;
            $inventory[$resource] = ($inventory[$resource] ?? 0) + $level;
        } elseif ($action === 'upgrade' && isset($buildings->{$buildingId})) {
            $costResource = $buildings->{$buildingId}->cost;
            $currentLevel = $userBuildings[$buildingId]['level'] ?? 1;
            $nextLevel = $currentLevel + 1;
            $cost = $gameConfigRepository->getUpgradeCost($buildingId, $nextLevel);

            if (($inventory[$costResource] ?? 0) >= $cost) {
                $inventory[$costResource] -= $cost;
                $userBuildings[$buildingId]['level'] = $nextLevel;
            }
        }

        $saveData['inventory'] = $inventory;
        $saveData['buildings'] = $userBuildings;
        file_put_contents($saveFile, json_encode($saveData, JSON_PRETTY_PRINT));

        header('Location: dashboard.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Maquette Ferme Manager</title>
    <link rel="stylesheet" href="Public/style.css">

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
