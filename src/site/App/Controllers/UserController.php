<?php

namespace App\Controllers;

use App\Repositories\GameConfigRepository;
use App\Repositories\SaveRepository;
use App\Repositories\UserRepository;
use CPE\Framework\AbstractController;
use App\Models\User;

class UserController extends AbstractController
{
    private function getSaveRepo(): SaveRepository
    {
        return new SaveRepository('Data/Saves/', 'Data/Config/save_initial.json');
    }

    private function getGameRepo(): GameConfigRepository
    {
        return new GameConfigRepository('Data/Config/game_config.json');
    }

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
                $this->getSaveRepo()->initSave($user->login);

                header('Location: ' . $this->app->view()->buildRoute('/dashboard'));
                exit;
            } else {
                $error = 'Identifiants incorrects';
            }
        }

        $this->app->view()->setParam('error', $error);
        $this->app->view()->render('login.html.twig');
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . $this->app->view()->buildRoute('/login'));
            exit;
        }

        /** @var User $user */
        $user = $_SESSION['user'];
        $saveRepo = $this->getSaveRepo();
        $gameRepo = $this->getGameRepo();

        $products = $gameRepo->getProducts();
        $buildings = $gameRepo->getBuildings();
        $save = $saveRepo->load($user->login);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'], $_POST['building_id'])) {
                $action = $_POST['action'];
                $buildingId = $_POST['building_id'];

                if ($action === 'harvest' && isset($buildings->{$buildingId})) {
                    $resource = $buildings->{$buildingId}->production;
                    $level = $save->buildings->{$buildingId}->level ?? 1;
                    $save->inventory->{$resource} = ($save->inventory->{$resource} ?? 0) + $level;
                } elseif ($action === 'upgrade' && isset($buildings->{$buildingId})) {
                    $currentLevel = $save->buildings->{$buildingId}->level ?? 1;
                    $nextLevel = $currentLevel + 1;
                    $cost = $gameRepo->getUpgradeCost($buildingId, $nextLevel);
                    $costResource = $buildings->{$buildingId}->cost;

                    if (($save->inventory->{$costResource} ?? 0) >= $cost) {
                        $save->inventory->{$costResource} -= $cost;
                        if (!isset($save->buildings->{$buildingId})) {
                            $save->buildings->{$buildingId} = (object)['level' => 1];
                        }
                        $save->buildings->{$buildingId}->level = $nextLevel;
                    }
                }

                $saveRepo->save($user->login, $save);

                header('Location: ' . $this->app->view()->buildRoute('/dashboard'));
                exit;
            }
        }

        $this->app->view()->setParam('products', $products);
        $this->app->view()->setParam('buildings', $buildings);
        $this->app->view()->setParam('inventory', (array)$save->inventory);
        $this->app->view()->setParam('userBuildings', (array)$save->buildings);
        $this->app->view()->setParam('gameConfigRepository', $gameRepo);
        $this->app->view()->render('dashboard.html.twig');
    }
}

