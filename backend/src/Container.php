<?php
declare(strict_types=1);

class Container
{
    private $services = [];

    public function set(string $id, $service): void
    {
        $this->services[$id] = $service;
    }

    public function get(string $id)
    {
        if (!isset($this->services[$id])) {
            throw new \Exception("Service {$id} not found");
        }
        return $this->services[$id];
    }
}
