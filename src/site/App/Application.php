<?php

namespace App;

use App\Controllers\DefaultController;
use App\Controllers\UserController;
use CPE\Framework\AbstractApplication;
use CPE\Framework\Router;

class Application extends AbstractApplication
{
    public function run()
    {
        // map all routes to corresponding controllers/actions
        $this->router = new Router($this);
        $this->router->mapDefault(DefaultController::class, 'error404');

        $this->router->map('GET', '/', DefaultController::class, 'index');
        // Support old .php URLs during transition
        $this->router->map('GET', '/login.php', UserController::class, 'login');
        $this->router->map('POST', '/login.php', UserController::class, 'login');
        $this->router->map('GET', '/login', UserController::class, 'login');
        $this->router->map('POST', '/login', UserController::class, 'login');
        $this->router->map('GET', '/dashboard.php', UserController::class, 'dashboard');
        $this->router->map('POST', '/dashboard.php', UserController::class, 'dashboard');
        $this->router->map('GET', '/dashboard', UserController::class, 'dashboard');
        $this->router->map('POST', '/dashboard', UserController::class, 'dashboard');
        $this->router->map('GET', '/test/{int:nombre}', DefaultController::class, 'test');

        $route = $this->router->findRoute();
        $controller = $this->router->getController($route->controller);
        $controller->execute($route->action, $route->foundParams);
    }
}
