<?php

// namespace App\Repositories;

// use App\Utilities\FileStorage;
// use Exception;

class GameConfigRepository
{
    private FileStorage $storage;
    private object $config;

    public function __construct(string $filePath)
    {
        // En mode procédural, filePath est le chemin complet vers le JSON.
        // FileStorage prend un dossier de base.
        $dir = dirname($filePath);
        $file = basename($filePath);

        $this->storage = new FileStorage($dir);
        $this->config = $this->storage->readJson($file) ?? [];

        if (empty($this->config)) {
            throw new Exception("Fichier de configuration introuvable ou vide : " . $filePath);
        }
    }

    public function getProducts(): object
    {
        return $this->config->products ?? [];
    }

    public function getBuildings(): object
    {
        return $this->config->buildings ?? [];
    }

    public function getBuilding(string $name): ?object
    {
        return $this->config->buildings->{$name} ?? null;
    }

    public function getUpgradeCost(string $buildingName, int $nextLevel): int
    {
        $building = $this->getBuilding($buildingName);
        if (!$building) return 0;

        // Calcul simple : cout de base * multiplicateur * niveau
        $multiplier = $building->upgrade_cost_multiplier ?? 10;
        return $multiplier * $nextLevel;
    }
}
