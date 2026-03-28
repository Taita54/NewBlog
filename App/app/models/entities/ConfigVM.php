<?php
namespace app\models\entities;

class ConfigVM
{

    protected array $configData = []; // Changed to an array to hold data

    public function __construct(array $configData = [])
    {
        $this->configData = $configData;
    }

    public function getConfVal(string $key, $default = null)
    {
        return $this->configData[$key] ?? $default;
    }

    public function setConfVal(string $key, $value): void
    {
        $this->configData[$key] = $value;
    }

    public function __get($key)
    {
        return $this->configData[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->configData[$key] = $value;
    }
}
