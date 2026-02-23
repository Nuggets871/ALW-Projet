<?php

namespace App\Repositories;

use App\Utilities\FileStorage;

class SaveRepository
{
    private FileStorage $storage;
    private string $initialSavePath;
    private array $loadedSaves;

    public function __construct(string $saveDir, string $initialSavePath)
    {
        // En mode procédural, on pourrait avoir besoin de require_once
        // Mais si on est dans le framework, l'autoloader gérera FileStorage.
        $this->storage = new FileStorage($saveDir);
        $this->initialSavePath = $initialSavePath;
        $this->loadedSaves = [];
    }

    public function exists(string $username): bool
    {
        return file_exists($this->storage->getBasePath() . $username . ".json");
    }

    public function initSave(string $username): void
    {
        if (!$this->exists($username)) {
            copy($this->initialSavePath, $this->storage->getBasePath() . $username . ".json");
        }
    }

    public function load(string $username): object
    {
        if (!$this->exists($username)) {
            $this->initSave($username);
        }
        if (!isset($this->loadedSaves[$username])) {
            $this->loadedSaves[$username] = $this->storage->readJson($username . ".json") ?? (object)[];
        }
        return $this->loadedSaves[$username];
    }

    public function save(string $username, object $data): void
    {
        $this->storage->writeJson($username . ".json", $data);
    }

    public function getInventory(string $username): object
    {
        $save = $this->load($username);
        return $save->inventory ?? (object)[];
    }

    public function setInventory(string $username, object $inventory): void
    {
        $save = $this->load($username);
        $save->inventory = $inventory;
        $this->save($username, $save);
    }

    public function getBuildings(string $username): object
    {
        $save = $this->load($username);
        return $save->buildings ?? (object)[];
    }

    public function setBuildings(string $username, object $buildings): void
    {
        $save = $this->load($username);
        $save->buildings = $buildings;
        $this->save($username, $save);
    }
}
