<?php

namespace App\Controllers;

use App\Repositories\GameConfigRepository;
use App\Repositories\UserRepository;
use CPE\Framework\AbstractController;

class UserController extends AbstractController
{
    public function login()
    {
        $error = null;
        $repo = new UserRepository("Data/users.json");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $repo->get($username);

            if ($user && password_verify($password, $user->password_hash)) {
                $_SESSION['user'] = $user;

                $saveFile = 'Data/Saves/' . $user->login . '.json';
                if (!file_exists($saveFile)) {
                    copy('Data/Config/save_initial.json', $saveFile);
                }

                header('Location: ' . $this->app->view()->buildRoute('/dashboard'));
                exit;
            } else {
                $error = 'Identifiants incorrects';
            }
        }

        $this->app->view()->setParam('error', $error);
        $this->app->view()->render('login.tpl.php');
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . $this->app->view()->buildRoute('/login'));
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
        $inventory = $saveData['inventory'] ?? [];
        $userBuildings = $saveData['buildings'] ?? [];

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

                header('Location: ' . $this->app->view()->buildRoute('/dashboard'));
                exit;
            }
        }

        $this->app->view()->setParam('products', $products);
        $this->app->view()->setParam('buildings', $buildings);
        $this->app->view()->setParam('inventory', $inventory);
        $this->app->view()->setParam('userBuildings', $userBuildings);
        $this->app->view()->setParam('gameConfigRepository', $gameConfigRepository);
        $this->app->view()->render('dashboard.tpl.php');
    }
}

